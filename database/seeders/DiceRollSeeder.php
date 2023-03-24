<?php

namespace Database\Seeders;

use App\Models\DiceRoll;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiceRollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DiceRoll::factory()
            ->count(200)
            ->create();
    }
}
