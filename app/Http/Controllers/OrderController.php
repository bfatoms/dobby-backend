<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleCreateRequest;
use App\Http\Requests\SaleUpdateRequest;
use App\Http\Requests\PurchaseCreateRequest;
use App\Http\Requests\PurchaseUpdateRequest;
use App\Http\Requests\QuoteCreateRequest;
use App\Http\Requests\QuoteUpdateRequest;
use App\Http\Requests\BillCreateRequest;
use App\Http\Requests\BillUpdateRequest;
use App\Http\Requests\InitialOrderDataRequest;
use App\Http\Requests\InvoiceCreateRequest;
use App\Http\Requests\InvoiceUpdateRequest;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Requests\ReceiveMoneyCreateRequest;
use App\Http\Requests\ReceiveMoneyUpdateRequest;
use App\Http\Requests\SpendMoneyCreateRequest;
use App\Http\Requests\SpendMoneyUpdateRequest;

use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Bill;
use App\Models\ChartOfAccount;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\Order;
use App\Models\Currency;
use App\Models\Expense;
use App\Models\OrderLine;
use App\Models\ReceiveMoney;
use App\Models\SpendMoney;
use App\Models\Setting;
use App\Models\TaxRate;
use App\Models\OrderActivityView;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderController extends BaseController
{
    protected $model = Order::class;

    protected $view_table = 'orders_view';

    protected $create_request = OrderCreateRequest::class;

    protected $update_request = OrderUpdateRequest::class;

    protected $types = [
        'so' => [
            'model' => Sale::class,
            'create_request' => SaleCreateRequest::class,
            'update_request' => SaleUpdateRequest::class
        ],
        'po' => [
            'model' => Purchase::class,
            'create_request' => PurchaseCreateRequest::class,
            'update_request' => PurchaseUpdateRequest::class
        ],
        'bill' => [
            'model' => Bill::class,
            'create_request' => BillCreateRequest::class,
            'update_request' => BillUpdateRequest::class
        ],
        'bill-cn' => [
            'model' => Bill::class,
            'create_request' => BillCreateRequest::class,
            'update_request' => BillUpdateRequest::class
        ],
        'inv' => [
            'model' => Invoice::class,
            'create_request' => InvoiceCreateRequest::class,
            'update_request' => InvoiceUpdateRequest::class
        ],
        'inv-cn' => [
            'model' => Invoice::class,
            'create_request' => InvoiceCreateRequest::class,
            'update_request' => InvoiceUpdateRequest::class
        ],
        'qu' => [
            'model' => Quote::class,
            'create_request' => QuoteCreateRequest::class,
            'update_request' => QuoteUpdateRequest::class
        ],
        'rmd' => [
            'model' => ReceiveMoney::class,
            'create_request' => ReceiveMoneyCreateRequest::class,
            'update_request' => ReceiveMoneyUpdateRequest::class
        ],
        'rmp' => [
            'model' => ReceiveMoney::class,
            'create_request' => ReceiveMoneyCreateRequest::class,
            'update_request' => ReceiveMoneyUpdateRequest::class
        ],
        'rmo' => [
            'model' => ReceiveMoney::class,
            'create_request' => ReceiveMoneyCreateRequest::class,
            'update_request' => ReceiveMoneyUpdateRequest::class
        ],
        'smd' => [
            'model' => SpendMoney::class,
            'create_request' => SpendMoneyCreateRequest::class,
            'update_request' => SpendMoneyUpdateRequest::class
        ],
        'smp' => [
            'model' => SpendMoney::class,
            'create_request' => SpendMoneyCreateRequest::class,
            'update_request' => SpendMoneyUpdateRequest::class
        ],
        'smo' => [
            'model' => SpendMoney::class,
            'create_request' => SpendMoneyCreateRequest::class,
            'update_request' => SpendMoneyUpdateRequest::class
        ]

    ];

    public function __construct()
    {
        if (in_array(request()->method(), ['POST', 'PUT', 'PATCH'])) {
            if (empty(request('order_type'))) {
                abort(422, 'ORDER_TYPE_REQUIRED');
            }

            $type = strtolower(request('order_type'));

            request()->merge(['order_type' => strtoupper($type)]);

            $this->model = $this->types[$type]['model'];

            $this->create_request = $this->types[$type]['create_request'];

            $this->update_request = $this->types[$type]['update_request'];
        }
    }


    public function update($id)
    {
        $this->authorize('update', $this->model);

        $validate = (new $this->update_request);

        request()->validate($validate->rules(), $validate->messages());

        $data = request()->all();

        if (in_array($data['status'], ['APPROVED', 'SENT'])) {
            $this->authorize('approve', $this->model);
        }

        $this->validateOrderWithProject($data);

        try {
            DB::beginTransaction();

            $order = $this->model::with('orderLines.product')->find($id);

            $disallow = config('company.ORDER_TYPES')[$data['order_type']]['DISALLOWED_UPDATE'];

            // update only when allowed
            if (optional($data)['order_lines'] && !in_array($order['status'], $disallow)) {
                $this->orderLineCreateUpdateDelete($order, $data['order_lines']);
            }

            $order->fill($data);

            $order->save();

            DB::commit();

            return $this->resolve($order->fresh(), 'RESOURCE_UPDATED');
        } catch (Exception $ex) {
            DB::rollback();
            return $this->reject($ex->getMessage());
        }
    }

    public function validateOrderWithProject($data)
    {
        if (optional($data)['order_lines']) {
            foreach ($data['order_lines'] as $order_line) {
                if (!empty($order_line['project_id'])) {
                    request()->validate([
                        'order_type' => 'in:BILL,INV,BILL-CN,INV-CN,SMD'
                    ], [
                        'order_type.in' => 'ORDER_TYPE_NOT_ALLOWED_WITH_PROJECT'
                    ]);
                }
            }
        }
    }

    public function store()
    {
        $this->authorize('create', $this->model);

        $validate = (new $this->create_request);

        request()->validate($validate->rules(), $validate->messages());

        $data = request()->all();

        if (in_array($data['status'], ['APPROVED', 'SENT'])) {
            $this->authorize('approve', $this->model);
        }

        $this->validateOrderWithProject($data);

        try {
            DB::beginTransaction();

            if (optional($data)['contact_name']) {
                $contact = Contact::create(['name' => $data['contact_name']]);
                $data['contact_id'] = $contact['id'];
            }

            $order = $this->model::create($data);

            if (optional($data)['order_lines']) {
                $this->orderLineCreateUpdateDelete($order, $data['order_lines']);
            }

            DB::commit();

            return $this->resolve($this->model::with(['orderLines.product', 'orderLines.expense'])->find($order->id), 'RESOURCE_CREATED');
        } catch (Exception $ex) {
            DB::rollback();
            return $this->reject($ex->getMessage());
        }
    }

    public function orderLineCreateUpdateDelete($order, $order_lines)
    {
        $new_order_lines = [];

        foreach ($order_lines as $order_line) {
            if (
                !empty($order_line['project_id']) &&
                (($order['order_type'] === 'BILL' && $order['status'] === 'APPROVED')
                    || in_array($order['order_type'], ['BILL-CN', 'SMD']))
            ) {
                $expense = Expense::create([
                    'name' => $order_line['description'] ?? '',
                    'quantity' => $order_line['quantity'] ?? 0,
                    'unit_price' => $order_line['unit_price'] ?? 0,
                    'charge_type' => 'PASS_COST_ALONG',
                    'is_invoiced' => false,
                    'project_id' => $order_line['project_id']
                ]);
                $order_line['expense_id'] = $expense->id;
            }

            if (in_array($order['order_type'], ['BILL-CN', 'INV-CN'])) {
                $order_line['quantity'] = abs($order_line['quantity']) * -1;
            }
            try {
                if (optional($order_line)['id']) {
                    $line = $order->orderLines()
                        ->find($order_line['id']);

                    $line->update($order_line);

                    $new_order_lines[] = $line;
                } else {
                    $new_order_lines[] = $order->orderLines()
                        ->create($order_line);
                }
            } catch (Exception $e) {
                info("orderline error: " . $e);
            }
        }

        // remove all data that is not in 
        if (request()->method() == "PUT") {
            OrderLine::where('order_id', $order['id'])->whereNotIn('id', collect($new_order_lines)->pluck('id'))->delete();
        }

        return $new_order_lines;
    }

    public function destroy($ids)
    {
        $this->authorize('delete', $this->model);

        $ids = explode(",", $ids);

        $deleted = [];
        foreach ($ids as $id) {
            $resource = $this->model::with(['payments', 'creditNotePayments'])->find($id);
            if (!empty($resource)) {
                if (in_array($resource['status'], config('company.ORDER_TYPES')[$resource['order_type']]['ALLOWED_HARD_DELETE'])) {
                    $destroyed = $resource->tryDelete();
                    if ($destroyed === true) {
                        $deleted[] = $resource;
                    } else {
                        return $this->reject($destroyed);
                    }
                } else {
                    if (!empty($resource['payments']->toArray())) {
                        abort(422, 'ORDER_WITH_PAYMENTS_CANT_BE_VOIDED');
                    }
                    if (!empty($resource['creditNotePayments']->toArray())) {
                        abort(422, 'CREDIT_NOTES_USED_AS_PAYMENTS_CANT_BE_VOIDED_OR_DELETED');
                    }
                    $resource->status = config('company.ORDER_TYPES')[$resource['order_type']]['DELETE_STATUS'];
                    $resource->save();
                    $deleted[] = $resource->fresh();
                }
            }
        }

        return $this->resolve($deleted, "RESOURCE_TRASHED");
    }


    public function initialData(InitialOrderDataRequest $request)
    {
        $order_type = $request->order_type;

        $discount_from_contact = null;

        $default_account = null;

        $default_tax_rate = null;

        $end_date = null;

        $due_type = null;

        $tax_type = "no tax";

        $account = '';

        $tax_rate = 0.0;

        $settings = Setting::first();

        $tax_rate = 0.0;

        $due = null;

        $contact = Contact::with([
            'saleAccount',
            'purchaseAccount',
            'saleTaxRate',
            'purchaseTaxRate',
            'orders' => function ($q) use ($order_type) {
                $q->where(function ($q) use ($order_type) {
                    if (in_array($order_type, ['BILL', 'BILL-CN'])) {
                        $q->whereIn('order_type', ['SMP', 'SMO', 'BILL-CN'])
                            ->where('status', 'APPROVED');
                    } elseif (in_array($order_type, ['INV', 'INV-CN'])) {
                        $q->whereIn('order_type', ['RMP', 'RMO', 'INV-CN'])
                            ->where('status', 'APPROVED');
                    }
                });
            }
        ])->find(request('contact_id'));

        if (in_array($order_type, ['BILL', 'BILL-CN'])) {
            $due = optional($contact)['bill_due'] ?: $settings['bill_due'];
            $due_type = optional($contact)['bill_due_type'] ?: $settings['bill_due_type'];
        } elseif (in_array($order_type, ['INV', 'INV-CN'])) {
            $due = optional($contact)['invoice_due'] ?: $settings['invoice_due'];
            $due_type = optional($contact)['invoice_due_type'] ?: $settings['invoice_due_type'];
        } elseif ($order_type == 'QU') {
            if (!empty($contact)) {
                $due = $settings['invoice_due'];
                $due_type = $settings['invoice_due_type'];
            }
        }

        if ($due_type == 'day(s) after order date') {
            $end_date = now()->addDays($due)->toISOString();
        } elseif ($due_type == 'day(s) after the end of the order month') {
            $end_date = now()->endOfMonth()->addDays($due)->toISOString();
        } elseif ($due_type == 'of the current month') {
            $end_date = now()->toISOString();
        } elseif ($due_type == 'of the following month') {
            $end_date = now()->addMonth(1)->toISOString();
        }

        if (in_array($order_type, ['SO', 'INV', 'INV-CN', 'QU'])) {
            $tax_type = optional($contact)['sale_tax_type'] ?: "no tax";

            $discount_from_contact = optional($contact)['sale_discount'] ?: 0.0;

            $default_account = optional($contact)['sale_account_id'] ?: 1;

            $account = optional($contact)['saleAccount'] ?: ChartOfAccount::find(1);

            $default_tax_rate = optional($contact)['sale_tax_rate_id'] ?: 1;

            $tax_rate = optional($contact)['saleTaxRate'] ?: TaxRate::find(1);
        } elseif (in_array($order_type, ['BILL', 'BILL-CN', 'PO'])) {
            $tax_type = optional($contact)['purchase_tax_type'] ?: "no tax";

            $default_account = optional($contact)['purchase_account_id'] ?: 2;

            $account = optional($contact)['purchaseAccount'] ?: ChartOfAccount::find(2);

            $default_tax_rate = optional($contact)['purchase_tax_rate_id'] ?: 1;

            $tax_rate = optional($contact)['purchaseTaxRate'] ?: TaxRate::find(1);
        }

        /**
         * 
         * RULES:
         * RM (prepayment) - has prefix and number
         * BILL, SM - no prefix, no number
         * INV, PO, SO, QU - has prefix, has number
         * INV (credit note) - has prefix (cn) and has number (based on invoice)
         */
        $prefix = $settings->getPrefix(strtoupper($order_type));

        $order_number = $settings->getNextNumber(strtoupper($order_type));

        return [
            'contact_name' => optional($contact)['name'],
            'order_number_prefix' => $prefix,
            'next_order_number' => $order_number,
            'tax_settings' => $tax_type,
            'currencies' => Currency::getWithDefault($settings['currency_id']),
            'discount_from_contact' => (float) $discount_from_contact,
            'default_account' => $default_account,
            'account' => $account,
            'default_tax_rate' => $default_tax_rate,
            'tax_rate' => $tax_rate,
            'contact' => $contact ?? null,
            'end_date' => $end_date,
            'due_type' => $due_type,
            'due' => $due,
        ];
    }

    public function activities()
    {
        $this->authorize('viewAny', $this->model);

        $this->model = new OrderActivityView;

        $query = $this->model->when(request('with'), function ($q) {
            $q->with($this->parseWith());
        })
            ->when(request('scopes'), function ($q) {
                foreach (request('scopes') as $scope) {
                    $q->{$scope}();
                }
            })
            ->when(request('fields'), function ($q) {
                $q->select(array_merge(request('fields'), ['id']));
            })
            ->when(request('sortKey'), function ($q) {
                $q->arrange(request('sortKey'), request('sortOrder'));
            })
            ->when(request('order_type'), function ($q) {
                $types = explode(",", request('order_type'));
                $q->whereIn('order_type', $types);
            })
            ->when(request('ids'), function ($q) {
                $ids = explode(",", request('ids'));
                $q->whereIn('id', $ids);
            });

        $query = $this->parseFields($query);

        return $this->resolve(
            $query->list(null, '*', 'updated_at'),
            "RESOURCE_LIST"
        );
    }
}
