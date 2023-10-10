<?php

namespace Tests\Unit;

use App\Models\ChartOfAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AdminBaseTest;
use App\Models\Contact;
use App\Models\ContactPerson;
use App\Models\Order;
use App\Models\TaxRate;

class ContactTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testContactTaxTypeIsHappy()
    {
        $response = $this->get('/api/contacts/tax-types');

        $response->assertStatus(200);
    }

    public function testContactDueDateTypesIsHappy()
    {
        $response = $this->get('/api/contacts/due-date-types');

        $response->assertStatus(200);
    }

    public function testContactIndexIsHappy()
    {
        Contact::factory()->withTaxAccountIds()->create();
        
        $response = $this->get('/api/contacts');

        $response->assertStatus(200);

        $response->assertSee("name");
    }

    public function testContactCreateIsHappy()
    {
        $contact = Contact::factory()->withTaxAccountIds()->make()->toArray();

        $response = $this->post('/api/contacts', $contact);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");
    }

    public function testContactCreateWithContactPersonIsHappy()
    {
        $contact = Contact::factory()->contactPerson()->make()->toArray();

        $response = $this->post('/api/contacts', $contact);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");

        $response->assertSee('contact_persons');

        $response->assertSee($contact['contact_persons'][0]['first_name']);
    }

    public function testContactCreateWithContactPersonFieldsAreRequired()
    {
        $contact = Contact::factory()->contactPerson()->make()->toArray();

        $contact['name'] = '';

        $contact['contact_persons'][0]['first_name'] = '';

        $contact['contact_persons'][0]['last_name'] = '';

        $contact['contact_persons'][0]['email'] = '';

        $response = $this->post('/api/contacts', $contact);

        $response->assertStatus(422);

        $response->assertSee('NAME_REQUIRED');

        $response->assertSee('FIRST_NAME_REQUIRED');

        $response->assertSee('LAST_NAME_REQUIRED');

        $response->assertSee('EMAIL_REQUIRED');
    }

    public function testContactCreateWithContactPersonEmailMustBeValidEmail()
    {
        $contact = Contact::factory()->contactPerson()->make()->toArray();

        $contact['contact_persons'][0]['email'] = 'test@';

        $response = $this->post('/api/contacts', $contact);

        $response->assertStatus(422);

        $response->assertSee('EMAIL_MUST_BE_A_VALID_EMAIL');
    }

    public function testContactCreateWithContactPersonMobileNumberOnlyAcceptsNumbers()
    {
        $contact = Contact::factory()->contactPerson()->make()->toArray();

        $contact['mobile_number'] = 'asd';

        $response = $this->post('/api/contacts', $contact);

        $response->assertStatus(422);

        $response->assertSee('MOBILE_NUMBER_ONLY_ACCEPT_NUMBERS');
    }

    public function testContactCreateOrUpdateWithContactPersonIsHappy()
    {
        $contact = Contact::factory()->withTaxAccountIds()->create();

        $contact_person = ContactPerson::factory()->create(['contact_id' => $contact->id]);

        $contact = $contact->load('contactPersons');

        $response = $this->put('/api/contacts/' . $contact->id, $contact->toArray());

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_UPDATED");

        $response->assertSee('contact_persons');

        $response->assertSee($contact_person['first_name']);
    }

    public function testContactCreateOrUpdateWithNewContactPersonIsHappy()
    {
        Contact::factory()->withTaxAccountIds()->create()->each(function(Contact $contact){
            ContactPerson::factory()->count(3)->create(['contact_id' => $contact->id]);
        });

        $contact = Contact::with('contactPersons')->first()->toArray();

        $response = $this->put("/api/contacts/{$contact['id']}", $contact);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_UPDATED");

        $response->assertSee('contact_persons');
    }

    public function testContactShowIsHappy()
    {
        $contact = Contact::factory()->withTaxAccountIds()->create();

        $response = $this->get("/api/contacts/{$contact['id']}");

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_FOUND");
    }

    public function testContactUpdateIsHappy()
    {
        $contact = Contact::factory()->withTaxAccountIds()->create();

        $response = $this->put("/api/contacts/{$contact['id']}", [
            'name' => 'vat 12'
        ]);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_UPDATED");
    }

    public function testContactUpdateFieldsAreRequired()
    {
        $contact = Contact::factory()->withTaxAccountIds()->create()->toArray();

        $contact['name'] = '';

        $contact['contact_persons'][0]['first_name'] = '';

        $contact['contact_persons'][0]['last_name'] = '';

        $contact['contact_persons'][0]['email'] = '';

        $response = $this->put("/api/contacts/{$contact['id']}", $contact);

        $response->assertStatus(422);

        $response->assertSee('NAME_REQUIRED');

        $response->assertSee('FIRST_NAME_REQUIRED');

        $response->assertSee('LAST_NAME_REQUIRED');

        $response->assertSee('EMAIL_REQUIRED');
    }

    public function testContactUpdateMobileNumberOnlyAcceptsNumber()
    {
        $contact = Contact::factory()->withTaxAccountIds()->create()->toArray();

        $contact['mobile_number'] = 'zxc';

        $response = $this->put("/api/contacts/{$contact['id']}", $contact);

        $response->assertStatus(422);

        $response->assertSee('MOBILE_NUMBER_ONLY_ACCEPT_NUMBERS');
    }

    public function testContactUpdateWithContactPersonEmailMustBeAValidEmail()
    {
        $contact = Contact::factory()->withTaxAccountIds()->create()->toArray();

        $contact['contact_persons'][0]['email'] = 'zxc';

        $response = $this->put("/api/contacts/{$contact['id']}", $contact);

        $response->assertStatus(422);

        $response->assertSee('EMAIL_MUST_BE_A_VALID_EMAIL');
    }

    public function testContactTrashIsHappy()
    {
        $contact = Contact::factory()->withTaxAccountIds()->create();

        $response = $this->delete("/api/contacts/{$contact['id']}");

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_TRASHED");
    }

    public function testContactWithoutPermissionCanIndex()
    {
        $this->actingAs($this->userWithoutPermissions());

        Contact::factory()->count(3)->create();

        $response = $this->get('/api/contacts');

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }

    public function testContactIndexSortAscIsHappy()
    {
        Contact::factory()->create(['name' => 'Apple Inc']);

        Contact::factory()->create(['name' => 'Tesla Inc']);

        Contact::factory()->create(['name' => 'Microsoft Inc.']);

        $response = $this->get('/api/contacts?sortKey=name&sortOrder=asc');

        $response->assertStatus(200);

        $response->assertSeeInOrder(["Apple Inc", "Microsoft Inc.", "Tesla Inc"]);
    }

    public function testContactIndexSortDescIsHappy()
    {
        Contact::factory()->create(['name' => 'Apple Inc']);

        Contact::factory()->create(['name' => 'Tesla Inc']);

        Contact::factory()->create(['name' => 'Microsoft Inc.']);

        $response = $this->get('/api/contacts?sortKey=name&sortOrder=desc');

        $response->assertStatus(200);

        $response->assertSeeInOrder(["Tesla Inc", "Microsoft Inc.", "Apple Inc"]);
    }

    public function testContactIndexWithPrimaryPersonSortDescIsHappy()
    {
        Contact::factory()->create(['name' => 'Apple Inc']);

        Contact::factory()->create(['name' => 'Tesla Inc']);

        Contact::factory()->create(['name' => 'Microsoft Inc.']);

        $response = $this->get('/api/contacts?sortKey=primaryPerson.email&sortOrder=desc');
        
        $response->assertStatus(200);

        $response->assertSee("primary_person_email");
    }

    public function testContactIndexWithPrimaryPersonSortAscIsHappy()
    {
        Contact::factory()->create(['name' => 'Apple Inc']);

        Contact::factory()->create(['name' => 'Tesla Inc']);

        Contact::factory()->create(['name' => 'Microsoft Inc.']);

        $response = $this->get('/api/contacts?sortKey=primaryPerson.email&sortOrder=asc');

        $response->assertStatus(200);

        $response->assertSee("primary_person_email");
    }

    public function testContactCreateBillDueMustBeInteger()
    {
        $contact = Contact::factory()->withTaxAccountIds()->make()->toArray();

        $contact['bill_due'] = "asdasd";

        $contact['bill_due_type'] = "of the current month";

        $response = $this->post('/api/contacts', $contact);

        $response->assertStatus(422);

        $response->assertSee("BILL_DUE_MUST_BE_AN_INTEGER");
    }

    public function testContactCreateBillDueMin()
    {
        $contact = Contact::factory()->withTaxAccountIds()->make()->toArray();

        $contact['bill_due'] = "-1";

        $contact['bill_due_type'] = "of the current month";

        $response = $this->post('/api/contacts', $contact);

        $response->assertStatus(422);

        $response->assertSee("BILL_DUE_MIN_VALUE_IS_1");
    }

    public function testContactCreateBillDueMax()
    {
        $contact = Contact::factory()->withTaxAccountIds()->make()->toArray();

        $contact['bill_due'] = "32";

        $contact['bill_due_type'] = "of the current month";

        $response = $this->post('/api/contacts', $contact);

        $response->assertStatus(422);

        $response->assertSee("BILL_DUE_MAX_VALUE_IS_31");
    }

    public function testContactCreateBillDueTypeDaysAfterNoMax()
    {
        $contact = Contact::factory()->withTaxAccountIds()->make()->toArray();

        $contact['bill_due'] = "32";

        $contact['bill_due_type'] = "day(s) after the end of the order month";

        $response = $this->post('/api/contacts', $contact);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");
    }

    public function testContactRelationships()
    {
        $sale_tax_rate = TaxRate::factory()->create();

        $purchase_tax_rate = TaxRate::factory()->create();

        $sale_account = ChartOfAccount::factory()->withTaxRate()->create();

        $purchase_account = ChartOfAccount::factory()->withTaxRate()->create();

        $contact = Contact::factory()->create([
            'sale_tax_rate_id' => $sale_tax_rate['id'],
            'purchase_tax_rate_id' => $purchase_tax_rate['id'],
            'sale_account_id' => $sale_account['id'],
            'purchase_account_id' => $purchase_account['id']
        ]);

        $primary = ContactPerson::factory()->create([
            'contact_id' => $contact['id'],
            'is_primary' => true
        ]);

        $secondary = ContactPerson::factory()->create([
            'contact_id' => $contact['id'],
            'is_primary' => false
        ]);

        $bill = Order::factory()->bill()->approved()->create([
            'contact_id' => $contact['id']
        ]);

        $bill_cn = Order::factory()->billCn()->approved()->create([
            'contact_id' => $contact['id']
        ]);

        $invoice = Order::factory()->invoice()->approved()->create([
            'contact_id' => $contact['id']
        ]);

        $invoice_cn = Order::factory()->invoiceCn()->approved()->create([
            'contact_id' => $contact['id']
        ]);

        $quote = Order::factory()->quote()->sent()->create([
            'contact_id' => $contact['id']
        ]);

        $sales = Order::factory()->sales()->approved()->create([
            'contact_id' => $contact['id']
        ]);

        $purchase = Order::factory()->purchase()->approved()->create([
            'contact_id' => $contact['id']
        ]);

        $response = $this->json('GET', '/api/contacts/' . $contact['id'] . '?with=primaryPerson,contactPersons,saleAccount,saleTaxRate,purchaseAccount,purchaseTaxRate,sales,purchases,quotes,bills,invoices,spendMoney,receiveMoney,orders');

        $response->assertJsonStructure([
            "data" => [
                "bills", "invoices", "sales", "purchases", "orders", "primary_person", "contact_persons", "sale_tax_rate", "purchase_tax_rate", "purchase_account", "sale_account"
            ]
        ]);
    }


}
