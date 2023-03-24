<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => ['required', 'unique:users', 'string', 'email'],
            'username' => ['max:25', 'string'],
            'password' => ['required', 'string']
        ]);
        $newUser = new User;
        $newUser->email = $request->email;
        $newUser->password = Hash::make($request->password);
        if (!$request->username) {
            $newUser->username = 'Anonymous';
        } else {
            $newUser->username = $request->username;
        }
        $newUser->save();
        $response = [
            'user' => $newUser
        ];
        return response($response, 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Invalid credentials'
            ], 401);
        } else {
            $accessToken = $user->createToken('authToken')->accessToken;
            $response = [
                'user' => $user,
                'token' => $accessToken
            ];
            return response($response, 201);
        }
    }

    public function test() {
        $response = [
            'test' => 'test successful'
        ];
        return response($response, 201);
    }


}
