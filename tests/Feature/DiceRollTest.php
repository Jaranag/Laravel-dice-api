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
        // $this->artisan('migrate');
        $this->artisan('db:seed');
        // $this->artisan('passport:install');

    }

    public function test_user_can_roll() {
        $user = User::factory()->create();
        $user->assignRole('user');
        $response = $this->actingAs($user, 'api')->postJson(route('diceroll.roll', $user->id));
        $response->assertStatus(201);
        $response->assertJsonStructure(['dice1']);
    }

    public function test_user_cant_roll_for_other_users() {
        $user = User::factory()->create();
        $user->assignRole('user');
        $response = $this->actingAs($user, 'api')->postJson(route('diceroll.roll', 3));
        $response->assertStatus(403);
        $response->assertJsonStructure(['error message']);
    }

    public function test_user_can_see_rolls() {
        $user = User::factory()->create();
        $user->assignRole('user');
        $this->actingAs($user, 'api')->postJson(route('diceroll.roll', $user->id));
        $response = $this->actingAs($user, 'api')->get(route('diceroll.index', $user->id));
        $response->assertStatus(201);
        $response->assertJsonStructure([['dice1']]);
    }

    public function test_user_can_delete_rolls() {
        $user = User::factory()->create();
        $user->assignRole('user');
        $this->actingAs($user, 'api')->postJson(route('diceroll.roll', $user->id));
        $response = $this->actingAs($user, 'api')->delete(route('diceroll.delete', $user->id));
        $response->assertStatus(201);
        $response->assertJsonStructure(['message']);
    }

    public function test_user_cant_delete_other_users_rolls() {
        $user = User::factory()->create();
        $user->assignRole('user');
        $response = $this->actingAs($user, 'api')->delete(route('diceroll.delete', 3));
        $response->assertStatus(403);
        $response->assertJsonStructure(['error message']);
    }
}
