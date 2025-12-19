<?php

namespace Tests\Unit;

use App\Models\Player;
use App\Models\Result;
use App\Models\ResultDescription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResultModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create result descriptions
        ResultDescription::factory()->create(['id' => 1, 'description' => 'beat']);
        ResultDescription::factory()->create(['id' => 2, 'description' => 'drew with']);
    }

    /** @test */
    public function it_can_determine_if_player1_won()
    {
        $result = Result::factory()->create(['result_description_id' => 1]);

        $this->assertTrue($result->player1Won());
    }

    /** @test */
    public function it_can_determine_if_match_was_a_draw()
    {
        $result = Result::factory()->create(['result_description_id' => 2]);

        $this->assertTrue($result->isDraw());
    }

    /** @test */
    public function it_can_get_the_winner()
    {
        $player1 = Player::factory()->create();
        $player2 = Player::factory()->create();

        $result = Result::factory()
            ->between($player1, $player2)
            ->create(['result_description_id' => 1]);

        $winner = $result->getWinner();

        $this->assertEquals($player1->id, $winner->id);
    }

    /** @test */
    public function it_returns_null_winner_for_draw()
    {
        $result = Result::factory()->create(['result_description_id' => 2]);

        $this->assertNull($result->getWinner());
    }

    /** @test */
    public function it_can_get_the_loser()
    {
        $player1 = Player::factory()->create();
        $player2 = Player::factory()->create();

        $result = Result::factory()
            ->between($player1, $player2)
            ->create(['result_description_id' => 1]);

        $loser = $result->getLoser();

        $this->assertEquals($player2->id, $loser->id);
    }

    /** @test */
    public function it_processes_rankings_correctly_when_lower_rank_wins()
    {
        $higherPlayer = Player::factory()->rank(1)->create();
        $lowerPlayer = Player::factory()->rank(5)->create();

        $result = Result::factory()
            ->between($lowerPlayer, $higherPlayer)
            ->create(['result_description_id' => 1]); // lowerPlayer beat higherPlayer

        $result->processRankings();

        $this->assertEquals(5, $higherPlayer->fresh()->rank);
        $this->assertEquals(1, $lowerPlayer->fresh()->rank);
    }

    /** @test */
    public function factory_can_create_recent_result()
    {
        $result = Result::factory()->recent()->create();

        $this->assertTrue($result->match_date->isAfter(now()->subDays(8)));
    }

    /** @test */
    public function recent_scope_filters_results()
    {
        // Old result
        Result::factory()->create(['match_date' => now()->subDays(10)]);

        // Recent result
        Result::factory()->create(['match_date' => now()->subDays(3)]);

        $recentResults = Result::recent(7)->get();

        $this->assertCount(1, $recentResults);
    }
}
