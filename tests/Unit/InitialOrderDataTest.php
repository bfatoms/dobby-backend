<?php

namespace Tests\Unit;

use App\Models\Contact;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AdminBaseTest;
use App\Models\Setting;

class InitialOrderDataTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testInitialOrderDataOrderTypeRequired()
    {
        $response = $this->get('/api/orders/initial-data');

        $response->assertStatus(422);

        $response->assertSee('ORDER_TYPE_REQUIRED');
    }

    public function testInitialOrderDataForPurchaseOrders()
    {
        $contact = Contact::factory()->withTaxAccountIds()->create();

        $response = $this->get('/api/orders/initial-data?order_type=PO&contact_id=' . $contact['id']);

        $response->assertStatus(200);

        $response->assertJsonFragment(['contact_name' => $contact['name']]);

        $response->assertJsonFragment(['order_number_prefix' => $this->setting['purchase_order_prefix']]);

        $response->assertJsonFragment(['next_order_number' => $this->setting['purchase_order_next_number']]);

        $response->assertJsonFragment(['tax_settings' => $contact['purchase_tax_type']]);

        $response->assertJsonFragment(['discount_from_contact' => (float)0]);

        $response->assertJsonFragment(['default_tax_rate' => $contact['purchase_tax_rate_id']]);

        $response->assertJsonFragment(['default_account' => $contact['purchase_account_id']]);

        $response->assertJsonFragment(['end_date' => null]);
    }

    public function testInitialOrderDataForBills()
    {
        $contact = Contact::factory()->withTaxAccountIds()->create();

        $response = $this->get('/api/orders/initial-data?order_type=BILL&contact_id=' . $contact['id']);

        $response->assertStatus(200);

        $response->assertJsonFragment(['contact_name' => $contact['name']]);

        $response->assertJsonFragment(['order_number_prefix' => null]);

        $response->assertJsonFragment(['next_order_number' => null]);

        $response->assertJsonFragment(['tax_settings' => $contact['purchase_tax_type']]);

        $response->assertJsonFragment(['discount_from_contact' => (float)0]);

        $response->assertJsonFragment(['default_tax_rate' => $contact['purchase_tax_rate_id']]);

        $response->assertJsonFragment(['default_account' => $contact['purchase_account_id']]);
    }

    public function testInitialOrderDataForBillsWithoutContactId()
    {
        Setting::factory()->create();


        $response = $this->get('/api/orders/initial-data?order_type=BILL');

        $response->assertStatus(200);
    }

    public function testInitialOrderDataForSalesOrders()
    {

        $contact = Contact::factory()->withTaxAccountIds()->create();

        $response = $this->get('/api/orders/initial-data?order_type=SO&contact_id=' . $contact['id']);

        $response->assertStatus(200);

        $response->assertJsonFragment(['contact_name' => $contact['name']]);

        $response->assertJsonFragment(['order_number_prefix' => $this->setting['sales_order_prefix']]);

        $response->assertJsonFragment(['next_order_number' => $this->setting['sales_order_next_number']]);

        $response->assertJsonFragment(['tax_settings' => $contact['sale_tax_type']]);

        $response->assertJsonFragment(['discount_from_contact' => $contact['sale_discount']]);

        $response->assertJsonFragment(['default_tax_rate' => $contact['sale_tax_rate_id']]);

        $response->assertJsonFragment(['default_account' => $contact['sale_account_id']]);

        $response->assertJsonFragment(['end_date' => null]);
    }

    public function testInitialOrderDataForSalesOrderWithoutContactId()
    {
        Setting::factory()->create();


        $response = $this->get('/api/orders/initial-data?order_type=SO');

        $response->assertStatus(200);
    }

    public function testInitialOrderDataForQuotes()
    {
        $contact = Contact::factory()->withTaxAccountIds()->create();

        $response = $this->get('/api/orders/initial-data?order_type=QU&contact_id=' . $contact['id']);

        $response->assertStatus(200);

        $response->assertJsonFragment(['contact_name' => $contact['name']]);

        $response->assertJsonFragment(['order_number_prefix' => $this->setting['quote_prefix']]);

        $response->assertJsonFragment(['next_order_number' => $this->setting['quote_next_number']]);

        $response->assertJsonFragment(['tax_settings' => $contact['sale_tax_type']]);

        $response->assertJsonFragment(['discount_from_contact' => $contact['sale_discount']]);

        $response->assertJsonFragment(['default_tax_rate' => $contact['sale_tax_rate_id']]);

        $response->assertJsonFragment(['default_account' => $contact['sale_account_id']]);
    }

    public function testInitialOrderDataForInvoices()
    {
        $contact = Contact::factory()->withTaxAccountIds()->create();

        $response = $this->get('/api/orders/initial-data?order_type=INV&contact_id=' . $contact['id']);

        $response->assertStatus(200);

        $response->assertJsonFragment(['contact_name' => $contact['name']]);

        $response->assertJsonFragment(['order_number_prefix' => $this->setting['invoice_prefix']]);

        $response->assertJsonFragment(['next_order_number' => $this->setting['invoice_next_number']]);

        $response->assertJsonFragment(['tax_settings' => $contact['sale_tax_type']]);

        $response->assertJsonFragment(['discount_from_contact' => $contact['sale_discount']]);

        $response->assertJsonFragment(['default_tax_rate' => $contact['sale_tax_rate_id']]);

        $response->assertJsonFragment(['default_account' => $contact['sale_account_id']]);
    }

    public function testInitialOrderDataForInvoiceCreditNote()
    {
        $contact = Contact::factory()->withTaxAccountIds()->create();

        $response = $this->get("/api/orders/initial-data?order_type=INV-CN&contact_id={$contact['id']}");

        $response->assertStatus(200);

        $response->assertJsonFragment(['contact_name' => $contact['name']]);

        $response->assertJsonFragment(['order_number_prefix' => $this->setting['credit_note_prefix']]);

        $response->assertJsonFragment(['next_order_number' => $this->setting['invoice_next_number']]);

        $response->assertJsonFragment(['tax_settings' => $contact['sale_tax_type']]);

        $response->assertJsonFragment(['discount_from_contact' => $contact['sale_discount']]);

        $response->assertJsonFragment(['default_tax_rate' => $contact['sale_tax_rate_id']]);

        $response->assertJsonFragment(['default_account' => $contact['sale_account_id']]);
    }

    public function testInitialOrderDataForReceiveMoneyDirectPaymentWithoutContact()
    {

        $response = $this->get('/api/orders/initial-data?order_type=RMD');

        $response->assertStatus(200);

        $response->assertJsonFragment(['contact_name' => null]);

        $response->assertJsonFragment(['order_number_prefix' => null]);

        $response->assertJsonFragment(['next_order_number' => null]);

        $response->assertJsonFragment(['tax_settings' => "no tax"]);
    }

    public function testInitialOrderDataForReceiveMoneyDirectPaymentWithContact()
    {

        $contact = Contact::factory()->withTaxAccountIds()->create();

        $response = $this->get("/api/orders/initial-data?order_type=RMD&contact_id={$contact['id']}");

        $response->assertStatus(200);

        $response->assertJsonFragment(['contact_name' => $contact['name']]);

        $response->assertJsonFragment(['order_number_prefix' => null]);

        $response->assertJsonFragment(['next_order_number' => null]);

        $response->assertJsonFragment(['tax_settings' => "no tax"]);
    }

    public function testInitialOrderDataForReceiveMoneyPrePaymentWithoutContact()
    {

        $response = $this->get('/api/orders/initial-data?order_type=RMP&is_prepayment=true');

        $response->assertStatus(200);

        $response->assertJsonFragment(['contact_name' => null]);

        $response->assertJsonFragment(['order_number_prefix' => $this->setting['invoice_prefix']]);

        $response->assertJsonFragment(['next_order_number' => $this->setting['invoice_next_number']]);

        $response->assertJsonFragment(['tax_settings' => "no tax"]);
    }

    public function testInitialOrderDataForReceiveMoneyPrePaymentWithContact()
    {

        $contact = Contact::factory()->withTaxAccountIds()->create();

        $response = $this->get("/api/orders/initial-data?order_type=RMP&contact_id={$contact['id']}&is_prepayment=true");

        $response->assertStatus(200);

        $response->assertJsonFragment(['contact_name' => $contact['name']]);

        $response->assertJsonFragment(['order_number_prefix' => $this->setting['invoice_prefix']]);

        $response->assertJsonFragment(['next_order_number' => $this->setting['invoice_next_number']]);

        $response->assertJsonFragment(['tax_settings' => "no tax"]);
    }

    public function testInitialOrderDataForReceiveMoneyOverPaymentWithoutContact()
    {

        $response = $this->get('/api/orders/initial-data?order_type=RMO&is_overpayment=true');

        $response->assertStatus(200);

        $response->assertJsonFragment(['contact_name' => null]);

        $response->assertJsonFragment(['order_number_prefix' => null]);

        $response->assertJsonFragment(['next_order_number' => null]);

        $response->assertJsonFragment(['tax_settings' => "no tax"]);
    }

    public function testInitialOrderDataForReceiveMoneyOverPaymentWithContact()
    {

        $contact = Contact::factory()->withTaxAccountIds()->create();

        $response = $this->get("/api/orders/initial-data?order_type=RMO&contact_id={$contact['id']}&is_overpayment=true");

        $response->assertStatus(200);

        $response->assertJsonFragment(['contact_name' => $contact['name']]);

        $response->assertJsonFragment(['order_number_prefix' => null]);

        $response->assertJsonFragment(['next_order_number' => null]);

        $response->assertJsonFragment(['tax_settings' => "no tax"]);
    }

        public function testInitialOrderDataForSpendMoneyDirectPaymentWithoutContact()
    {

        $response = $this->get('/api/orders/initial-data?order_type=SMD');

        $response->assertStatus(200);

        $response->assertJsonFragment(['contact_name' => null]);

        $response->assertJsonFragment(['order_number_prefix' => null]);

        $response->assertJsonFragment(['next_order_number' => null]);

        $response->assertJsonFragment(['tax_settings' => "no tax"]);
    }

    public function testInitialOrderDataForSpendMoneyDirectPaymentWithContact()
    {

        $contact = Contact::factory()->withTaxAccountIds()->create();

        $response = $this->get("/api/orders/initial-data?order_type=SMD&contact_id={$contact['id']}");

        $response->assertStatus(200);

        $response->assertJsonFragment(['contact_name' => $contact['name']]);

        $response->assertJsonFragment(['order_number_prefix' => null]);

        $response->assertJsonFragment(['next_order_number' => null]);

        $response->assertJsonFragment(['tax_settings' => "no tax"]);
    }

    public function testInitialOrderDataForSpendMoneyPrePaymentWithoutContact()
    {

        $response = $this->get('/api/orders/initial-data?order_type=SMP&is_prepayment=true');

        $response->assertStatus(200);

        $response->assertJsonFragment(['contact_name' => null]);

        $response->assertJsonFragment(['order_number_prefix' => null]);

        $response->assertJsonFragment(['next_order_number' => null]);

        $response->assertJsonFragment(['tax_settings' => "no tax"]);
    }

    public function testInitialOrderDataForSpendMoneyPrePaymentWithContact()
    {

        $contact = Contact::factory()->withTaxAccountIds()->create();

        $response = $this->get("/api/orders/initial-data?order_type=SMP&contact_id={$contact['id']}&is_prepayment=true");

        $response->assertStatus(200);

        $response->assertJsonFragment(['contact_name' => $contact['name']]);

        $response->assertJsonFragment(['order_number_prefix' => null]);

        $response->assertJsonFragment(['next_order_number' => null]);

        $response->assertJsonFragment(['tax_settings' => "no tax"]);
    }

    public function testInitialOrderDataForSpendMoneyOverPaymentWithoutContact()
    {

        $response = $this->get('/api/orders/initial-data?order_type=SMO&is_overpayment=true');

        $response->assertStatus(200);

        $response->assertJsonFragment(['contact_name' => null]);

        $response->assertJsonFragment(['order_number_prefix' => null]);

        $response->assertJsonFragment(['next_order_number' => null]);

        $response->assertJsonFragment(['tax_settings' => "no tax"]);
    }

    public function testInitialOrderDataForSpendMoneyOverPaymentWithContact()
    {

        $contact = Contact::factory()->withTaxAccountIds()->create();

        $response = $this->get("/api/orders/initial-data?order_type=SMO&contact_id={$contact['id']}&is_overpayment=true");

        $response->assertStatus(200);

        $response->assertJsonFragment(['contact_name' => $contact['name']]);

        $response->assertJsonFragment(['order_number_prefix' => null]);

        $response->assertJsonFragment(['next_order_number' => null]);

        $response->assertJsonFragment(['tax_settings' => "no tax"]);
    }

    public function testContactWithBillCredits()
    {
        $contact = Contact::factory()->withTaxAccountIds()->create();

        Order::factory()->billCn()->create([
            'total_amount' => 1000,
            'contact_id' => $contact['id']
        ]);

        Order::factory()->spendMoneyOverpayment()->create([
            'total_amount' => 2000,
            'contact_id' => $contact['id']
        ]);

        Order::factory()->spendMoneyPrepayment()->create([
            'total_amount' => 3000,
            'contact_id' => $contact['id']
        ]);

        $response = $this->get("/api/orders/initial-data?order_type=BILL-CN&contact_id={$contact['id']}");

        $response->assertJsonFragment([
            'total_amount' => 1000,
            'order_type' => 'BILL-CN'
        ]);

        $response->assertJsonFragment([
            'total_amount' => 2000,
            'order_type' => 'SMO'
        ]);
        
        $response->assertJsonFragment([
            'total_amount' => 3000,
            'order_type' => 'SMP'
        ]);
    }

    public function testContactWithInvoiceCredits()
    {
        $contact = Contact::factory()->withTaxAccountIds()->create();

        Order::factory()->invoiceCn()->create([
            'total_amount' => 1000,
            'contact_id' => $contact['id']
        ]);

        Order::factory()->receiveMoneyOverpayment()->create([
            'total_amount' => 2000,
            'contact_id' => $contact['id']
        ]);

        Order::factory()->receiveMoneyPrepayment()->create([
            'total_amount' => 3000,
            'contact_id' => $contact['id']
        ]);

        $response = $this->get("/api/orders/initial-data?order_type=INV-CN&contact_id={$contact['id']}");

        $response->assertJsonFragment([
            'total_amount' => 1000,
            'order_type' => 'INV-CN'
        ]);

        $response->assertJsonFragment([
            'total_amount' => 2000,
            'order_type' => 'RMO'
        ]);
        
        $response->assertJsonFragment([
            'total_amount' => 3000,
            'order_type' => 'RMP'
        ]);
    }
}
