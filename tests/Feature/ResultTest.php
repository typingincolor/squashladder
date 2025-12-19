<?php

namespace Tests\Feature;

use App\Models\Player;
use App\Models\Result;
use App\Models\ResultDescription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResultTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create result descriptions that are needed
        ResultDescription::factory()->create(['id' => 1, 'description' => 'beat']);
        ResultDescription::factory()->create(['id' => 2, 'description' => 'drew with']);
    }

    /** @test */
    public function it_can_create_a_result()
    {
        $player1 = Player::factory()->rank(1)->create();
        $player2 = Player::factory()->rank(2)->create();

        $response = $this->post('/results', [
            'match_date' => '2024-01-15',
            'player1_id' => $player1->id,
            'player2_id' => $player2->id,
            'result_description_id' => 1,
        ]);

        $response->assertRedirect(route('ladder.index'));
        $this->assertDatabaseHas('results', [
            'player1_id' => $player1->id,
            'player2_id' => $player2->id,
        ]);
    }

    /** @test */
    public function it_swaps_ranks_when_lower_ranked_player_wins()
    {
        // Create players with specific ranks
        $higherRankedPlayer = Player::factory()->rank(1)->create(); // Rank 1 (higher)
        $lowerRankedPlayer = Player::factory()->rank(5)->create();  // Rank 5 (lower)

        // Lower ranked player beats higher ranked player
        $response = $this->post('/results', [
            'match_date' => now()->format('Y-m-d'),
            'player1_id' => $lowerRankedPlayer->id,
            'player2_id' => $higherRankedPlayer->id,
            'result_description_id' => 1, // beat
        ]);

        // Refresh the models from database
        $higherRankedPlayer->refresh();
        $lowerRankedPlayer->refresh();

        // Ranks should be swapped
        $this->assertEquals(5, $higherRankedPlayer->rank);
        $this->assertEquals(1, $lowerRankedPlayer->rank);
    }

    /** @test */
    public function it_does_not_swap_ranks_when_higher_ranked_player_wins()
    {
        $higherRankedPlayer = Player::factory()->rank(1)->create();
        $lowerRankedPlayer = Player::factory()->rank(5)->create();

        // Higher ranked player wins - no rank change expected
        $this->post('/results', [
            'match_date' => now()->format('Y-m-d'),
            'player1_id' => $higherRankedPlayer->id,
            'player2_id' => $lowerRankedPlayer->id,
            'result_description_id' => 1, // beat
        ]);

        $higherRankedPlayer->refresh();
        $lowerRankedPlayer->refresh();

        // Ranks should remain the same
        $this->assertEquals(1, $higherRankedPlayer->rank);
        $this->assertEquals(5, $lowerRankedPlayer->rank);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->post('/results', []);

        $response->assertSessionHasErrors(['match_date', 'player1_id', 'player2_id', 'result_description_id']);
    }

    /** @test */
    public function it_validates_players_are_different()
    {
        $player = Player::factory()->create();

        $response = $this->post('/results', [
            'match_date' => now()->format('Y-m-d'),
            'player1_id' => $player->id,
            'player2_id' => $player->id, // Same player
            'result_description_id' => 1,
        ]);

        $response->assertSessionHasErrors('player2_id');
    }

    /** @test */
    public function it_validates_match_date_is_not_in_future()
    {
        $player1 = Player::factory()->create();
        $player2 = Player::factory()->create();

        $response = $this->post('/results', [
            'match_date' => now()->addDays(1)->format('Y-m-d'), // Tomorrow
            'player1_id' => $player1->id,
            'player2_id' => $player2->id,
            'result_description_id' => 1,
        ]);

        $response->assertSessionHasErrors('match_date');
    }
}
