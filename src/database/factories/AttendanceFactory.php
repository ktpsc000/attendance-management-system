<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
        'user_id' => User::factory(),
        'work_date' => today(),
        'clock_in_at' => null,
        'clock_out_at' => null,
    ];
    }

    public function working()
    {
        return $this->state([
            'clock_in_at' => today()->setTime(9, 0),
        ]);
    }

    public function finished()
    {
        return $this->state([
            'clock_in_at' => today()->setTime(9, 0),
            'clock_out_at' => today()->setTime(18, 0),
        ]);
    }
}
