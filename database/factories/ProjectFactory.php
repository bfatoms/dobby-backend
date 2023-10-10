<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Task;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'status' => Arr::random(config('company.project_statuses')),
            'estimate' => rand(1000000, 9999999),
            'contact_id' => Contact::factory()->create()->id,
            'deadline' => $this->faker->iso8601(),
        ];
    }

    public function inProgress()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'IN_PROGRESS',
            ];
        });
    }

    public function closed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'CLOSED',
            ];
        });
    }

    public function draft()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'DRAFT',
            ];
        });
    }

    public function withTaskAndTimeEntries()
    {
        return $this->state(function (array $attributes) {
            Task::factory()->withTimeEntries()->create(['project_id' => $attributes['project_id']]);

            // return [
            //     'task_id' => Task::factory()->withTimeEntry()->create()->id,
            // ];
        });
    }
}
