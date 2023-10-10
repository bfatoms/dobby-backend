<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AdminBaseTest;

class TimeEntryTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testTimeEntryIndexIsHappy()
    {
        TimeEntry::factory()->create();

        $response = $this->get('/api/time-entries');

        $response->assertStatus(200);

        $response->assertSee("type");
    }

    public function testTimeEntryCreateIsHappy()
    {
        $timeEntries = TimeEntry::factory()->make()->toArray();

        $response = $this->post('/api/time-entries', $timeEntries);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");
    }

    public function testTimeEntryShowIsHappy()
    {
        $timeEntries = TimeEntry::factory()->create();

        $response = $this->get("/api/time-entries/{$timeEntries['id']}");

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_FOUND");
    }

    public function testTimeEntryUpdateIsHappy()
    {
        $timeEntries = TimeEntry::factory()->create();

        $response = $this->put("/api/time-entries/{$timeEntries['id']}", array_merge($timeEntries->toArray(), [
            'duration' => 123
        ]));

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_UPDATED");
    }

    public function testTimeEntryTrashIsHappy()
    {
        $timeEntries = TimeEntry::factory()->create();

        $response = $this->delete("/api/time-entries/{$timeEntries['id']}");

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_TRASHED");
    }
}
