<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


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
}
