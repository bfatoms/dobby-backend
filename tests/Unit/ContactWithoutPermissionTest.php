<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\UserBaseTest;
use App\Models\Contact;

class ContactWithoutPermissionTest extends UserBaseTest
{
    use RefreshDatabase;

    public function testContactWithoutPermissionCannotCreate()
    {
        $contact = Contact::factory()->withTaxAccountIds()->make()->toArray();

        $contact['password'] = 'password';

        $response = $this->post('/api/contacts', $contact);

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }

    public function testContactWithoutPermissionCannotShow()
    {
        $contact = Contact::factory()->withTaxAccountIds()->create();

        $response = $this->get("/api/contacts/{$contact['id']}");

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }

    public function testContactWithoutPermissionCannotUpdate()
    {
        $contact = Contact::factory()->withTaxAccountIds()->create();

        $response = $this->put("/api/contacts/{$contact['id']}", [
            'first_name' => 'charlene'
        ]);

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }

    public function testContactWithoutPermissionCannotTrash()
    {
        $contact = Contact::factory()->withTaxAccountIds()->create();

        $response = $this->delete("/api/contacts/{$contact['id']}");

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }
}
