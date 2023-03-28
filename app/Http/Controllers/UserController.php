<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $response = User::all();
        return response($response, 201);
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
        $request->admin_password == "Admin1234" ? $newUser->assignRole('admin') : $newUser->assignRole('user');
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

    public function update(Request $request, $id)
    {
        $user = User::find(auth()->user()->id);
        if($user->hasRole('admin')){
            $userToUpdate = User::find($id);
            $userToUpdate->username = $request->username;
            $userToUpdate->save();
            $response = [
                'User' => $userToUpdate,
                'Message' => 'Username updated'
            ];
            return response($response, 201);
        }
        if ($user->id == $id) {
            $user->username = $request->username;
            $user->save();
            $response =  [
                'User' => $user,
                'Message' => 'Username updated'
            ];
            return response($response, 201);
        } else {
            $response =  [
                'Error message' => 'Only able to update own username, input your id in URL'
            ];
            return response($response, 403);

        }
    }

    public function ranking()
    {
        $authUser = User::find(auth()->user()->id);
        $users = User::orderBy('winning_percentage', 'desc')->get();
        $usersRanked = array();

        if ($authUser->hasRole('admin')) {
            return response($users, 201);
        } else {
            foreach ($users as $user) {
                $userClean = [
                    'username' => $user->username,
                    'winning percentage' => $user->winning_percentage,
                    'total rolls' => $user->total_rolls,
                    'successful rolls' => $user->successful_rolls,
                ];
                array_push($usersRanked, $userClean);
            }
            return response($usersRanked, 201);

        }
    }

    public function winner()
    {
        $authUser = User::find(auth()->user()->id);
        $winnerA = User::orderBy('winning_percentage', 'desc')->limit(1)->get();
        $winner = $winnerA[0];
        if ($authUser->hasRole('admin')) {
            return response($winner, 201);
        } else {
            $winnerClean = [
                'username' => $winner->username,
                'winning percentage' => $winner->winning_percentage,
                'total rolls' => $winner->total_rolls,
                'successful rolls' => $winner->successful_rolls,
            ];
            return response($winnerClean, 201);

        }
        
    }

    public function loser()
    {
        $authUser = User::find(auth()->user()->id);
        $loserA = User::orderBy('winning_percentage', 'asc')->limit(1)->get();
        $loser = $loserA[0];
        if ($authUser->hasRole('admin')) {
            return response($loser, 201);
        } else {
            $loserClean = [
                'username' => $loser->username,
                'winning percentage' => $loser->winning_percentage,
                'total rolls' => $loser->total_rolls,
                'successful rolls' => $loser->successful_rolls,
            ];
            return response($loserClean, 201);
        }
    }

}
