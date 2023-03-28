<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\DiceRoll;

class DiceRollTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function test_can_see_rolls() {
        $user = User::factory()->create();
        $user->assignRole('user');
        $response = $this->actingAs($user, 'api')->get(route('diceroll.roll', $user->id));
        $response->assertStatus(200);
        $response->assertJsonFragment(['user']);
    }

    // public function test_can_see_rolls() {
    //     $user = User::factory()->create();
    //     $user->assignRole('user');
    //     $diceRoll = DiceRoll::factory()->make([
    //         'id_user' => $user->id
    //     ]);
    //     $response = $this->actingAs($user, 'api')->get(route('diceroll.index', $diceRoll->id_user));
    //     $response->assertStatus(200);
    //     $response->assertJsonFragment([]);
    // }
}
