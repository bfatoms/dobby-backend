<?php

namespace Tests\Unit;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AdminBaseTest;

class ProjectTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testProjectIndexIsHappy()
    {
        Project::factory()->create();

        $response = $this->get('/api/projects');

        $response->assertStatus(200);

        $response->assertSee("name");
    }

    public function testProjectCreateWithStatusInProgressIsHappy()
    {
        $project = Project::factory()->inProgress()->make()->toArray();

        $response = $this->post('/api/projects', $project);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");

        $response->assertSee("IN_PROGRESS");
    }

    public function testProjectCreateWithStatusDraftIsHappy()
    {
        $project = Project::factory()->draft()->make()->toArray();

        $response = $this->post('/api/projects', $project);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");

        $response->assertSee("DRAFT");
    }

    public function testProjectCreateWithStatusClosedIsHappy()
    {
        $project = Project::factory()->closed()->make()->toArray();

        $response = $this->post('/api/projects', $project);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");

        $response->assertSee("CLOSED");
    }

    public function testProjectCreateIsHappy()
    {
        $project = Project::factory()->make()->toArray();

        $response = $this->post('/api/projects', $project);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");
    }

    public function testProjectShowIsHappy()
    {
        $project = Project::factory()->create();

        $response = $this->get("/api/projects/{$project['id']}");

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_FOUND");
    }

    public function testProjectUpdateIsHappy()
    {
        $project = Project::factory()->create();

        $response = $this->put("/api/projects/{$project['id']}", array_merge($project->toArray(), [
            'name' => 'project updated name'
        ]));

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_UPDATED");
    }

    public function testProjectTrashIsHappy()
    {
        $project = Project::factory()->create();

        $response = $this->delete("/api/projects/{$project['id']}");

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_TRASHED");
    }
}
