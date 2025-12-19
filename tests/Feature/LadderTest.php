<?php

namespace Tests\Feature;

use App\Models\Player;
use App\Models\Result;
use App\Models\ResultDescription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LadderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_the_ladder_standings()
    {
        // Create test players using factories
        Player::factory()->rank(1)->create(['forename' => 'John', 'surname' => 'Doe']);
        Player::factory()->rank(2)->create(['forename' => 'Jane', 'surname' => 'Smith']);
        Player::factory()->rank(3)->create(['forename' => 'Bob', 'surname' => 'Wilson']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('Jane Smith');
        $response->assertSee('Bob Wilson');
    }

    /** @test */
    public function it_displays_recent_results()
    {
        // Create result descriptions first
        $beatDescription = ResultDescription::factory()->create(['id' => 1, 'description' => 'beat']);

        // Create players
        $player1 = Player::factory()->rank(1)->create(['forename' => 'Alice']);
        $player2 = Player::factory()->rank(2)->create(['forename' => 'Bob']);

        // Create a recent result
        Result::factory()
            ->recent()
            ->between($player1, $player2)
            ->create(['result_description_id' => $beatDescription->id]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Recent Results');
        $response->assertSee('Alice');
        $response->assertSee('beat');
        $response->assertSee('Bob');
    }

    /** @test */
    public function it_orders_players_by_rank()
    {
        // Create players in random order
        Player::factory()->rank(3)->create(['surname' => 'Third']);
        Player::factory()->rank(1)->create(['surname' => 'First']);
        Player::factory()->rank(2)->create(['surname' => 'Second']);

        $response = $this->get('/');
        $content = $response->getContent();

        // Assert that "First" appears before "Second" and "Third" in the HTML
        $posFirst = strpos($content, 'First');
        $posSecond = strpos($content, 'Second');
        $posThird = strpos($content, 'Third');

        $this->assertTrue($posFirst < $posSecond);
        $this->assertTrue($posSecond < $posThird);
    }
}
