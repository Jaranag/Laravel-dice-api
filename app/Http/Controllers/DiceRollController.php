<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiceRoll;
use App\Models\User;
use GuzzleHttp\Psr7\Message;

class DiceRollController extends Controller
{
    public function index($id)
    {
        $user = User::find($id);
        $userRolls = $user->diceRolls;
        return $userRolls;
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
        return $newRoll;
        } else {
            return [
                'error message' => 'can only roll for yourself',
                'your id' => auth()->user()->id
            ];
        }
    }

}
