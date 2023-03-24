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
}
