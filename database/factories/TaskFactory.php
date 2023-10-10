<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\TimeEntry;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'project_id' => Project::factory()->create()->id,
            'name' => $this->faker->name,
            'charge_type' => Arr::random(config('company.task_charge_types')),
            'status' => Arr::random(config('company.task_statuses')),
            'rate' => rand(1000, 10000),    
        ];
    }

    public function withTimeEntries()
    {
        return $this->state(function (array $attributes) {
            TimeEntry::factory()->count(5)->create([
                'task_id' => $attributes['task_id'],
            ]);
        });
    }
}
