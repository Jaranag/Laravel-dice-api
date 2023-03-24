<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DiceRoll;



class UserUpdate extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        foreach ($users as $user) {
            $user->total_rolls = 0;
            $user->successful_rolls = 0;
            $user->winning_percentage = 0;
            $this->updateUser($user);
            if ($user->total_rolls > 0) {
                $user->winning_percentage = $user->successful_rolls / $user->total_rolls * 100;
            }
            $user->save();
        }
    }

    protected function updateUser($user) {
        $diceRolls = DiceRoll::all();
        foreach($diceRolls as $diceRoll) {
            $this->checkRoll($diceRoll, $user);
        }
    }

    protected function checkRoll($diceRoll, $user) {
        if ($diceRoll->id_user == $user->id) {
            $user->total_rolls += 1;
            if ($diceRoll->was_successful == 'Yes') {
                $user->successful_rolls += 1;
            }
        }
    }
}
