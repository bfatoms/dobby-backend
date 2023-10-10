<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
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
            'charge_type' => Arr::random(config('company.expense_charge_types')),
            'unit_price' => rand(1000, 10000),
            'quantity' => rand(1, 10),
            'mark_up' => rand(1, 100),
            'custom_price' => rand(1000, 10000),
            'is_invoiced' => $this->faker->boolean(),
        ];
    }

    public function withProjectTaskAndTimeEntry()
    {
        return $this->state(function (array $attributes) {
            $project = Project::factory()->withTaskAndTimeEntries()->create();

            // Task::factory()->withTimeEntries()->create(['project_id' => $project->id], 5);

            return [
                'project_id' => $project->id,
            ];
        });
    }
}


// $factory->state(Expense::class, 'with_task', function (Faker $faker) {
//     $project_id = factory(Project::class)->create()->id;

//     $task = factory(Task::class)->create(['project_id' => $project_id]);

//     factory(TimeEntry::class, 5)->create(['task_id' => $task->id]);

//     return [
//         'project_id' => $project_id,
//     ];
// });

