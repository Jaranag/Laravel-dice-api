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

    public function test_user_can_roll() {
        $user = User::factory()->create();
        $user->assignRole('user');
        $response = $this->actingAs($user, 'api')->postJson(route('diceroll.roll', $user->id));
        $response->assertStatus(201);
        $response->assertJsonStructure(['dice1']);
    }

    public function test_user_can_see_rolls() {
        $user = User::factory()->create();
        $user->assignRole('user');
        $this->actingAs($user, 'api')->postJson(route('diceroll.roll', $user->id));
        $response = $this->actingAs($user, 'api')->get(route('diceroll.index', $user->id));
        $response->assertStatus(200);
        $response->assertJsonStructure([['dice1']]);
    }
}
