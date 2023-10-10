<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeEntry>
 */
class TimeEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'task_id' => Task::factory()->create()->id,
            'user_id' => User::factory()->create()->id,
            'description' => $this->faker->paragraph,
            'type' => Arr::random(config('company.time_entry_types')),
            'start_at' => now()->toDateTimeString(),
            'end_at' => now()->addDays(7)->toDateTimeString(),
            'time_entry_date' => now()->toDateTimeString(),
            'duration' => rand(1, 100),
            'is_invoiced' => false
        ];
    }
}
