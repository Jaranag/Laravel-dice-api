<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;


class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */

     public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function login() {
        $user = [
            'email' => fake()->safeEmail(),
            'password' => 'test',
        ];
        $response = $this->postJson(route('players.register'), $user);
        $response = $this->postJson(route('login'), [
            'email' => $user['email'],
            'password' => 'test',
        ]);

        return $user;
    }

    public function test_createUser(): void
    {
        $user = [
            'username' => fake()->name(),
            'email' => fake()->safeEmail(),
            'password' => 'test',
        ];
        $response = $this->postJson(route('players.register'), $user)->assertCreated();
        $response->assertStatus(201);
        $response->assertJson(['user' => [
            'username' => $user['username'],
        ]]);
    }

    public function test_createUserError(): void
    {
        $user = [
            'username' => '',
            'email' => '',
            'password' => 'test',
        ];
        $response = $this->postJson(route('players.register'), $user);
        $response->assertStatus(422);
    }

    public function test_createUserAnonymous(): void
    {
        $user = [
            'email' => fake()->safeEmail(),
            'password' => 'test',
        ];
        $response = $this->postJson(route('players.register'), $user);
        $response->assertStatus(201);
        $response->assertJson(['user' => [
            'username' => 'Anonymous',
        ]]);
    }

    public function test_login() {
        $user = [
            'email' => fake()->safeEmail(),
            'password' => 'test',
        ];
        $response = $this->postJson(route('players.register'), $user);
        $response->assertJson(['user' => [
            'email' => $user['email'],
        ]]);
        $response = $this->postJson(route('login'), [
            'email' => $user['email'],
            'password' => 'test',
        ]);
        $response->assertStatus(201);
        $response->assertJsonfragment([
            'email' => $user['email'],
        ]);
    }

    public function test_login_not_successful() {
        $user = [
            'email' => fake()->safeEmail(),
            'password' => 'test',
        ];
        $response = $this->postJson(route('players.register'), $user);
        $response->assertJson(['user' => [
            'email' => $user['email'],
        ]]);
        $response = $this->postJson(route('login'), [
            'email' => 'fake@email.com',
            'password' => 'nose',
        ]);
        $response->assertStatus(401);
        $response->assertJsonfragment([
            'message' => 'Invalid credentials',
        ]);
    }

    public function test_admin_can_see_users() {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'password'
        ]);
        $response = $this->actingAs($user, 'api')->get(route('players.showAll'));
        $response->assertJsonStructure([['username']]);
    }

    public function test_user_cant_see_users() {
        $user = User::factory()->create();
        $user->assignRole('user');
        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'password'
        ]);
        $response = $this->actingAs($user, 'api')->get(route('players.showAll'));
        $response->assertStatus(403);
    }

    public function test_admin_can_see_ranking() {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $response = $this->actingAs($user, 'api')->get(route('players.ranking'));
        $response->assertStatus(200);
        $response->assertJsonStructure([['created_at']]);
    }

    public function test_user_can_see_ranking_and_user_clean() {
        $user = User::factory()->create();
        $user->assignRole('user');
        $response = $this->actingAs($user, 'api')->get(route('players.ranking'));
        $response->assertStatus(200);
        $response->assertJsonStructure([['username']]);
    }

    public function test_user_can_see_winner_clean() {
        $user = User::factory()->create();
        $user->assignRole('user');
        $response = $this->actingAs($user, 'api')->get(route('players.winner'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['username']);
    }

    public function test_admin_can_see_winner_complete() {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $response = $this->actingAs($user, 'api')->get(route('players.winner'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['created_at']);
    }


    public function test_user_can_see_loser_clean() {
        $user = User::factory()->create();
        $user->assignRole('user');
        $response = $this->actingAs($user, 'api')->get(route('players.loser'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['username']);
    }

    public function test_admin_can_see_loser_complete() {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $response = $this->actingAs($user, 'api')->get(route('players.loser'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['created_at']);
    }
    // public function test_can_update_username(){
    //     $user = User::factory()->create();
    //     $user->assignRole('admin');
    //     $response = $this->postJson(route('login'), [
    //         'email' => $user->email,
    //         'password' => 'password'
    //     ]);
    //     $response = $this->actingAs($user, 'api')->postJson(route('players.update', $user->id), ['username' => 'newUsername']);
    //     $response->assertJsonFragment(['User']);
    //     $response->assertStatus(200);
    // }

    // public function test_can_update_username(){
    //     $user = User::factory()->create();
    //     $user->assignRole('admin');
    //     $response = $this->actingAs($user)->postJson("players/$user->id", ['username' => 'newUsername']);
    //     $response->assertJsonFragment(['User']);
    //     $response->assertStatus(200);
    // }
}
