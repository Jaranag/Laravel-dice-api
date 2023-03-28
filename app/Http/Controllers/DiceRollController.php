<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiceRoll;
use App\Models\User;
use GuzzleHttp\Psr7\Message;
use Illuminate\Support\Facades\Auth;


class DiceRollController extends Controller
{
    public function index($id)
    {
        $user = User::find($id);
        $userRolls = $user->diceRolls;
        return response($userRolls, 201);
    }

    public function roll($id)
    {

        if (auth()->user()->id == $id) {
            $newRoll = new DiceRoll;
            $newRoll->id_user = auth()->user()->id;
            $newRoll->dice1 = rand(1, 6);
            $newRoll->dice2 = rand(1, 6);
            $newRoll->result = $newRoll->dice1 +  $newRoll->dice2;
            if ($newRoll->result == 7) {
                $newRoll->was_successful = 'Yes';
            } else {
                $newRoll->was_successful = 'No';
            }
            $this->updateUser($newRoll);
            $newRoll->save();
            return response($newRoll, 201);
        } else {
            $response =  [
                'error message' => 'can only roll for yourself',
                'your id' => auth()->user()->id
            ];
            return response($response, 403);

        }
    }

    public function delete($id)
    {
        $user = User::find(auth()->user()->id);
        if (auth()->user()->id == $id) {
            foreach ($user->diceRolls as $diceRoll) {
                $diceRoll->delete();
            }
            $user->total_rolls = 0;
            $user->successful_rolls = 0;
            $user->winning_percentage = 0;
            $user->save();
            $response = [
                'message' => 'User games deleted'
            ];
            return response($response, 201);
        } else {
            $response =  [
                'error message' => 'Can only delete your dice rolls',
                'your id' => auth()->user()->id
            ];
            return response($response, 403);
        }
    }

    protected function updateUser(DiceRoll $roll)
    {
        $user = User::find(Auth::user()->id);
        $user->total_rolls += 1;
        if ($roll->was_successful == 'Yes') {
            $user->successful_rolls += 1;
        }
        $totalRolls = $user->total_rolls;
        $successfulRolls = $user->successful_rolls;
        $user->winning_percentage = $successfulRolls / $totalRolls * 100;
        $user->save();
    }
}
