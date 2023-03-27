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
    public function test_createUser(): void
    {
        $user = [
            'username' => 'testUser',
            'email' => 'test@gmail.com',
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
            'email' => 'tested@gmail.com',
            'password' => 'test',
        ];
        $response = $this->postJson(route('players.register'), $user);
        $response->assertStatus(201);
        $response->assertJson(['user' => [
            'username' => 'Anonymous',
        ]]);
    }
}
