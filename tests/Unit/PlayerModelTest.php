<?php

namespace Tests\Unit;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_full_name()
    {
        $player = Player::factory()->create([
            'forename' => 'John',
            'surname' => 'Doe',
        ]);

        $this->assertEquals('John Doe', $player->full_name);
    }

    /** @test */
    public function it_can_swap_ranks_with_another_player()
    {
        $player1 = Player::factory()->rank(1)->create();
        $player2 = Player::factory()->rank(5)->create();

        $player1->swapRankWith($player2);

        $this->assertEquals(5, $player1->fresh()->rank);
        $this->assertEquals(1, $player2->fresh()->rank);
    }

    /** @test */
    public function it_can_get_ladder_ordered_by_rank()
    {
        Player::factory()->rank(3)->create();
        Player::factory()->rank(1)->create();
        Player::factory()->rank(2)->create();

        $ladder = Player::ladder();

        $this->assertEquals(1, $ladder[0]->rank);
        $this->assertEquals(2, $ladder[1]->rank);
        $this->assertEquals(3, $ladder[2]->rank);
    }

    /** @test */
    public function factory_can_create_player_with_specific_rank()
    {
        $player = Player::factory()->rank(42)->create();

        $this->assertEquals(42, $player->rank);
    }

    /** @test */
    public function factory_can_create_player_with_specific_name()
    {
        $player = Player::factory()->named('Alice', 'Johnson')->create();

        $this->assertEquals('Alice', $player->forename);
        $this->assertEquals('Johnson', $player->surname);
    }
}
