<?php

namespace Tests\Feature;

use App\Models\Player;
use App\Models\Result;
use App\Models\ResultDescription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChallengerLosesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create all result descriptions
        ResultDescription::factory()->create(['id' => 1, 'description' => 'beat']);
        ResultDescription::factory()->create(['id' => 2, 'description' => 'drew with']);
        ResultDescription::factory()->create(['id' => 3, 'description' => 'lost to']);
    }

    /** @test */
    public function challenger_loses_and_was_higher_ranked_swaps_ranks()
    {
        // Create user and link to player
        $user = User::factory()->create();
        $challengerPlayer = Player::factory()->rank(1)->create(); // Rank 1 (better)
        $challengerPlayer->user_id = $user->id;
        $challengerPlayer->save();

        $opponentPlayer = Player::factory()->rank(5)->create(); // Rank 5 (worse)

        // Challenger (rank 1) lost to opponent (rank 5)
        $this->actingAs($user)->post('/results', [
            'match_date' => now()->format('Y-m-d'),
            'player1_id' => $challengerPlayer->id,
            'player2_id' => $opponentPlayer->id,
            'result_description_id' => 3, // lost to
        ]);

        // Refresh models
        $challengerPlayer->refresh();
        $opponentPlayer->refresh();

        // Since higher-ranked player (rank 1) lost, they should swap
        $this->assertEquals(5, $challengerPlayer->rank);
        $this->assertEquals(1, $opponentPlayer->rank);
    }

    /** @test */
    public function challenger_loses_and_was_lower_ranked_no_swap()
    {
        // Create user and link to player
        $user = User::factory()->create();
        $challengerPlayer = Player::factory()->rank(5)->create(); // Rank 5 (worse)
        $challengerPlayer->user_id = $user->id;
        $challengerPlayer->save();

        $opponentPlayer = Player::factory()->rank(1)->create(); // Rank 1 (better)

        // Challenger (rank 5) lost to opponent (rank 1) - expected result
        $this->actingAs($user)->post('/results', [
            'match_date' => now()->format('Y-m-d'),
            'player1_id' => $challengerPlayer->id,
            'player2_id' => $opponentPlayer->id,
            'result_description_id' => 3, // lost to
        ]);

        // Refresh models
        $challengerPlayer->refresh();
        $opponentPlayer->refresh();

        // No rank change - lower ranked player lost as expected
        $this->assertEquals(5, $challengerPlayer->rank);
        $this->assertEquals(1, $opponentPlayer->rank);
    }

    /** @test */
    public function challenger_wins_and_was_lower_ranked_swaps_ranks()
    {
        // Create user and link to player
        $user = User::factory()->create();
        $challengerPlayer = Player::factory()->rank(5)->create(); // Rank 5 (worse)
        $challengerPlayer->user_id = $user->id;
        $challengerPlayer->save();

        $opponentPlayer = Player::factory()->rank(1)->create(); // Rank 1 (better)

        // Challenger (rank 5) beat opponent (rank 1) - upset!
        $this->actingAs($user)->post('/results', [
            'match_date' => now()->format('Y-m-d'),
            'player1_id' => $challengerPlayer->id,
            'player2_id' => $opponentPlayer->id,
            'result_description_id' => 1, // beat
        ]);

        // Refresh models
        $challengerPlayer->refresh();
        $opponentPlayer->refresh();

        // Ranks should swap - lower ranked player won
        $this->assertEquals(1, $challengerPlayer->rank);
        $this->assertEquals(5, $opponentPlayer->rank);
    }

    /** @test */
    public function result_model_correctly_identifies_lost_to()
    {
        $player1 = Player::factory()->rank(1)->create();
        $player2 = Player::factory()->rank(2)->create();

        $result = Result::create([
            'match_date' => now(),
            'player1_id' => $player1->id,
            'player2_id' => $player2->id,
            'result_description_id' => 3, // lost to
        ]);

        $this->assertTrue($result->player1Lost());
        $this->assertFalse($result->player1Won());
        $this->assertFalse($result->isDraw());
        $this->assertEquals($player2->id, $result->getWinner()->id);
        $this->assertEquals($player1->id, $result->getLoser()->id);
    }
}
