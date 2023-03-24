<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiceRoll>
 */
class DiceRollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dice1 = rand(1, 6);
        $dice2 = rand(1, 6);
        $result = $dice1 + $dice2;
        if ($result == 7) {
            $response = 'Yes';
        } else {
            $response = 'No';
        }
        return [
            'id_user' => rand(1, 21),
            'dice1' => $dice1,
            'dice2' => $dice2,
            'result' => $result,
            'was_successful' => $response
        ];
    }
}
