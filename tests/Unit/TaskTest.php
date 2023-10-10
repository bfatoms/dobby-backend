<?php

namespace Tests\Unit;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AdminBaseTest;

class TaskTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testTaskIndexIsHappy()
    {
        Task::factory()->create();

        $response = $this->get('/api/tasks');

        $response->assertStatus(200);

        $response->assertSee("name");
    }

    public function testTaskCreateIsHappy()
    {
        $task = Task::factory()->make();

        $task->makeVisible($task->getHidden());

        $response = $this->post('/api/tasks', $task->toArray());

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");
    }

    public function testTaskShowIsHappy()
    {
        $task = Task::factory()->create();

        $response = $this->get("/api/tasks/{$task['id']}");

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_FOUND");
    }

    public function testTaskUpdateIsHappy()
    {
        $task = Task::factory()->create();
        $task->makeVisible($task->getHidden());

        $response = $this->put("/api/tasks/{$task['id']}", array_merge($task->toArray(), [
            'name' => 'project updated name'
        ]));

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_UPDATED");
    }

    public function testTaskTrashIsHappy()
    {
        $task = Task::factory()->create();

        $response = $this->delete("/api/tasks/{$task['id']}");

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_TRASHED");
    }
}
