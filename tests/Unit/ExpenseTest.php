<?php

namespace Tests\Unit;

use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AdminBaseTest;

class ExpenseTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testExpenseIndexIsHappy()
    {
        Expense::factory()->create();

        $response = $this->get('/api/expenses');

        $response->assertStatus(200);

        $response->assertSee("name");
    }

    public function testExpenseCreateIsHappy()
    {
        $expense = Expense::factory()->make()->toArray();

        $response = $this->post('/api/expenses', $expense);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");
    }

    public function testExpenseShowIsHappy()
    {
        $expense = Expense::factory()->create();

        $response = $this->get("/api/expenses/{$expense['id']}");

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_FOUND");
    }

    public function testExpenseUpdateIsHappy()
    {
        $expense = Expense::factory()->create();

        $response = $this->put("/api/expenses/{$expense['id']}", array_merge($expense->toArray(), [
            'name' => 'project updated name'
        ]));

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_UPDATED");
    }

    public function testExpenseTrashIsHappy()
    {
        $expense = Expense::factory()->create();

        $response = $this->delete("/api/expenses/{$expense['id']}");

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_TRASHED");
    }
}
