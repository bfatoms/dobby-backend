<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectCreateRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Models\Contact;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Project;
use App\Models\Quote;
use App\Models\Setting;
use App\Models\Task;
use App\Models\TimeEntry;
use Carbon\Carbon;
use App\Models\User;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends BaseController
{
    protected $model = Project::class;

    protected $view_table = 'projects_view';

    protected $create_request = ProjectCreateRequest::class;

    protected $update_request = ProjectUpdateRequest::class;

    public function __construct(
        OrderService $orderService
    ) {
        $this->orderService = $orderService;
    }

    public function store()
    {
        $this->authorize('create', $this->model);

        if (isset($this->create_request)) {
            $request = (new $this->create_request);

            request()->validate($request->rules(), $request->messages());
        }

        $data = request()->all();

        try {
            DB::beginTransaction();

            if (optional($data)['contact_name']) {
                $contact = Contact::create(['name' => $data['contact_name']]);
                $data['contact_id'] = $contact['id'];
            }

            $project = $this->model::create($data);

            DB::commit();

            return $this->resolve($project, 'RESOURCE_CREATED');
        } catch (Exception $ex) {
            DB::rollback();
            return $this->reject($ex->getMessage());
        }
    }

    public function update($id)
    {
        $this->authorize('update', $this->model);

        if (isset($this->update_request)) {
            $request = (new $this->update_request);

            request()->validate($request->rules(), $request->messages());
        }

        $data = request()->all();

        try {
            DB::beginTransaction();

            $project = $this->model::findOrFail($id);

            if (optional($data)['contact_name']) {
                $contact = Contact::create(['name' => $data['contact_name']]);
                $data['contact_id'] = $contact['id'];
            }

            $project->fill($data);

            $project->save();

            DB::commit();

            return $this->resolve($project->fresh(), 'RESOURCE_UPDATED');
        } catch (Exception $ex) {
            DB::rollback();
            return $this->reject($ex->getMessage());
        }
    }

    public function projectInvoiceInitialData(Request $request, $project_id)
    {
        $this->authorize('all', $this->model);

        $result = Project::where('id', $project_id)
            ->with([
                'expenses' => function ($query) {
                    // $is_invoiced = in_array(request('is_invoiced'), ['true', 1]);
                    $query->where('is_invoiced', request('is_invoiced') ?? false);
                    return $query;
                },
                'tasks' => function ($query) {
                    $query->where('status', 'ON_GOING');
                    return $query;
                },
                'tasks.timeEntries' => function ($query)  use ($project_id) {
                    $query->where('is_invoiced', request('is_invoiced') ?? false);

                    $query->when(request('from_date') && request('to_date'), function ($query) {
                        $from_date = Carbon::parse(request('from_date'))->setTime(0, 0, 0);
                        $to_date = Carbon::parse(request('to_date'))->setTime(23, 59, 59);
                        $query->whereBetween('time_entry_date', [
                            $from_date,
                            $to_date
                        ]);
                    });

                    $query->with(['user' => function ($query) use ($project_id) {
                        $query->select([
                            'id',
                            'first_name',
                            'last_name',
                            'email',
                            'user_project_rates.final_sales_price as sales_price',
                            'user_project_rates.final_purchase_price as purchase_price',
                        ])->leftJoin('user_project_rates', function ($leftJoin) use ($project_id) {
                            $leftJoin->on('user_project_rates.user_id', '=', 'users.id')
                                ->where('user_project_rates.project_id', '=', $project_id);
                        });
                        return $query;
                    }]);
                    return $query;
                }
            ])->first();

        return $this->resolve(
            $result,
            "Tasks And Expenses"
        );
    }

    public function getPriceSettings(Project $project)
    {
        $this->authorize('view', $this->model);

        $this->model = new $this->model;

        $query = User::select([
            'users.id',
            'users.first_name',
            'users.last_name',
            'project_prices.sales_price',
            'project_prices.purchase_price',
            'users.created_at',
            'users.updated_at',
        ])
            ->leftJoin('project_prices', function ($join) use ($project) {
                $join->on('project_prices.user_id', '=', 'users.id')
                    ->where('project_prices.project_id', '=', $project->id);
            });

        $query = $this->applyFilterQueries($query);

        $query = $this->parseFields($query);

        return $this->resolve(
            $query->list(),
            "PROJECT_EMPLOYEES_COST_AND_SALES_PRICE"
        );
    }


    public function createInvoice(Project $project)
    {
        $this->authorize('create', Invoice::class);

        request()->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'rate_type' => 'required|in:TASK_RATE,EMPLOYEE_RATE',
            'orderline_option' => 'required|in:1,2,3'
        ], [
            'from_date.required' => 'FROM_DATE_IS_REQUIRED',
            'to_date.required' => 'TO_DATE_IS_REQUIRED',
            'from_date.date' => 'FROM_DATE_INVALID',
            'to_date.date' => 'TO_DATE_INVALID',
            'rate_type.required' => 'RATE_TYPE_REQUIRED',
            'rate_type.in' => 'RATE_TYPE_INVALID',
            'orderline_option.required' => 'ORDERLINE_OPTION_REQUIRED',
            'orderline_option.in' => 'ORDERLINE_OPTION_INVALID'
        ]);

        try {
            DB::beginTransaction();

            $order_lines = [];

            if (request('orderline_option') === 1 && request('rate_type') !== 'TASK_RATE') {
                abort(422, 'ORDERLINE_OPTION_1_IS_ONLY_FOR_TASK_RATE');
            }

            if (request('orderline_option') === 1) {
                $order_lines = $this->orderService->generateOrderlines(
                    'tasks',
                    $project->id,
                    request('tasks'),
                    request('from_date'),
                    request('to_date')
                );
            } else if (request('orderline_option') === 2) {
                $order_lines = $this->orderService->generateOrderlines(
                    'employee_task',
                    $project->id,
                    request('tasks'),
                    request('from_date'),
                    request('to_date'),
                    request('rate_type')
                );
            } else if (request('orderline_option') === 3) {
                $order_lines = $this->orderService->generateOrderlines(
                    'time_entries',
                    $project->id,
                    request('tasks'),
                    request('from_date'),
                    request('to_date'),
                    request('rate_type')
                );
            }

            $expenses = Expense::whereIn('id', request('expenses'))->where('is_invoiced', false);
            foreach ($expenses->get() as $expense) {
                $order_lines[] = [
                    'project_id' => $project->id,
                    'expense_id' => $expense->id,
                    'description' => $expense->name,
                    'unit_price' => $expense->unit_price,
                    'quantity' => $expense->quantity,
                ];
            }
            $expenses->update(['is_invoiced' => true]);

            if (empty($order_lines)) {
                abort(422, 'NO_ORDERLINES');
            }

            $initial_data = $this->orderService->getInitialData('INV', $project->contact_id);

            $invoice = Order::create([
                'contact_id' => $project->contact_id,
                'order_type' => 'INV',
                'order_date' => now(),
                'end_date' => $initial_data['end_date'],
                'status' => 'DRAFT',
                'due_type' => $initial_data['due_type'],
                'tax_setting' => $initial_data['tax_settings'],
                'currency_id' => Setting::first()->currency_id,
            ]);

            foreach ($order_lines as $order_line) {
                $order_line['tax_rate'] = $initial_data['default_tax_rate'];
                $order_line['tax_rate_id'] = $initial_data['tax_rate']['id'];
                $order_line['chart_of_account_id'] = $initial_data['default_account'];
                $order_line['discount'] = $initial_data['discount_from_contact'];
            }

            $invoice->orderLines()->createMany($order_lines);

            DB::commit();

            return $this->resolve($invoice->load('orderLines'), 'RESOURCE');
        } catch (Exception $ex) {
            DB::rollback();
            return $this->reject($ex->getMessage());
        }
    }


    public function invoiceFixedAmount(Project $project)
    {
        $this->authorize('all', $this->model);

        request()->validate([
            'mark_all_as_invoiced' => 'boolean',
            'close_project' => 'boolean',
            'amount' => 'required|numeric',
        ], [
            'amount.required' => 'AMOUNT_REQUIRED',
        ]);

        DB::beginTransaction();

        if (request('mark_all_as_invoiced') === true) {
            $tasks = Task::where('project_id', $project->id);
            $taskIds = $tasks->get('id')->map(function ($item) {
                return $item->id;
            })->toArray();

            $timeEntries = TimeEntry::whereIn('task_id', $taskIds);
            $expenses = Expense::where('project_id', $project->id);

            $tasks->markAsInvoiced();
            $timeEntries->markAsInvoiced();
            $expenses->markAsInvoiced();
        }

        if (request('close_project') === true) {
            $project->update(['status' => 'CLOSED']);
        }

        $initial_data = $this->orderService->getInitialData('INV', $project->contact_id);

        $invoice = Order::create([
            'status' => 'DRAFT',
            'order_type' => 'INV',
            'order_date' => now(),
            'contact_id' => $project->contact_id,
            'currency_id' => Setting::first()->currency_id,
            'end_date' => $initial_data['end_date'],
            'due_type' => $initial_data['due_type'],
            'tax_setting' => $initial_data['tax_settings'],
        ]);

        $invoice->orderLines()->createMany([
            [
                'description' => $project->name,
                'unit_price' => request('amount'),
                'quantity' => 1,
                'project_id' => $project->id,
                'amount' => request('amount'),
                'tax_rate' => $initial_data['default_tax_rate'],
                'tax_rate_id' => $initial_data['tax_rate']['id'],
                'chart_of_account_id' => $initial_data['default_account'],
                'discount' => $initial_data['discount_from_contact'],
            ]
        ]);

        DB::commit();

        return $this->resolve($invoice->load('orderLines'), 'RESOURCE');
    }


    public function createQuotationFixedAmount(Project $project)
    {
        request()->validate([
            'quotation_title' => 'required'
        ], [
            'quotation_title.required' => 'QUOTATION_TITLE_REQUIRED'
        ]);

        try {
            DB::beginTransaction();

            $initial_data = $this->orderService->getInitialData('QU', $project->contact_id);

            $projectDetails = $project->getDetails();

            $quotation = Quote::create([
                'quotation_project_id' => $project->id,
                'quotation_title' => request('quotation_title'),
                'status' => 'DRAFT',
                'order_type' => 'QU',
                'order_date' => now(),
                'contact_id' => $project->contact_id,
                'currency_id' => Setting::first()->currency_id,
                'end_date' => $initial_data['end_date'],
                'due_type' => $initial_data['due_type'],
                'tax_setting' => $initial_data['tax_settings'],
            ]);

            $quotation->orderLines()->createMany([
                [
                    'description' => $project->name,
                    'unit_price' => $projectDetails->cost,
                    'quantity' => 1,
                    'amount' => $projectDetails->cost,
                    'tax_rate' => $initial_data['default_tax_rate'],
                    'tax_rate_id' => $initial_data['tax_rate']['id'],
                    'chart_of_account_id' => $initial_data['default_account'],
                    'discount' => $initial_data['discount_from_contact'],
                ]
            ]);

            DB::commit();

            return $this->resolve($quotation->load('orderLines'), 'RESOURCE');
        } catch (Exception $ex) {
            DB::rollback();
            return $this->reject($ex->getMessage());
        }
    }

    public function projectQuotationInitialData($project_id)
    {
        $this->authorize('all', $this->model);

        $result = Project::where('id', $project_id)
            ->with([
                'expenses' => function ($query) {
                    $query->where([
                        'is_invoiced' => false,
                        'is_estimated' => true
                    ]);
                },
                'tasks' => function ($query) {
                    $query->where('status', 'ON_GOING')
                        ->whereIn('charge_type', ['FIXED_RATE', 'HOURLY_RATE']);
                },
            ])->first();

        return $this->resolve(
            $result,
            "RESOURCE"
        );
    }

    public function createQuotation(Project $project)
    {
        request()->validate([
            'quotation_title' => 'required'
        ], [
            'quotation_title.required' => 'QUOTATION_TITLE_REQUIRED'
        ]);

        try {
            DB::beginTransaction();

            $order_lines = [];
            $tasks = Task::whereIn('id', request('tasks'))->get();
            $expenses = Expense::whereIn('id', request('expenses'))->get();

            $initial_data = $this->orderService->getInitialData('QU', $project->contact_id);

            foreach ($tasks as $task) {
                $unit_price = $task->rate;

                if ($task->charge_type === 'HOURLY_RATE') {
                    $unit_price = $task->rate * $task->estimated_hours;
                }

                $order_lines[] = [
                    'description' => $project->name . ' - ' . $task->name,
                    'unit_price' => $unit_price,
                    'quantity' => 1,
                    'amount' => $unit_price,
                    'tax_rate' => $initial_data['default_tax_rate'],
                    'tax_rate_id' => $initial_data['tax_rate']['id'],
                    'chart_of_account_id' => $initial_data['default_account'],
                    'discount' => $initial_data['discount_from_contact'],
                ];
            }

            foreach ($expenses as $expense) {
                $order_lines[] = [
                    'expense_id' => $expense->id,
                    'description' =>  $expense->name,
                    'unit_price' => $expense->unit_price,
                    'quantity' => $expense->quantity,
                    'tax_rate' => $initial_data['default_tax_rate'],
                    'tax_rate_id' => $initial_data['tax_rate']['id'],
                    'chart_of_account_id' => $initial_data['default_account'],
                    'discount' => $initial_data['discount_from_contact'],
                ];
            }

            $quotation = Quote::create([
                'quotation_project_id' => $project->id,
                'quotation_title' => request('quotation_title'),
                'status' => 'DRAFT',
                'order_type' => 'QU',
                'order_date' => now(),
                'contact_id' => $project->contact_id,
                'currency_id' => Setting::first()->currency_id,
                'end_date' => $initial_data['end_date'],
                'due_type' => $initial_data['due_type'],
                'tax_setting' => $initial_data['tax_settings'],
            ]);

            $quotation->orderLines()->createMany($order_lines);

            DB::commit();

            return $this->resolve($quotation->load('orderLines'), 'RESOURCE');
        } catch (Exception $ex) {
            DB::rollback();
            return $this->reject($ex->getMessage());
        }
    }

    public function search()
    {
        // do contact + pject search for frontend...
    }

    public function getProjectQuotations(Project $project)
    {
        $this->authorize('all', $this->model);

        $query = Quote::where('quotation_project_id', $project->id);

        $query = $this->applyFilterQueries($query);

        $query = $this->parseFields($query);

        return $this->resolve(
            $query->list(),
            "RESOURCE"
        );
    }


    public function getProjectInvoiceList(Project $project)
    {
        $this->authorize('all', $this->model);

        $query = Order::where('order_type', 'INV')
            ->whereHas('orderLines', function ($q) use ($project) {
                return $q->where('project_id', $project->id);
            });

        $query = $this->applyFilterQueries($query);

        $query = $this->parseFields($query);

        return $this->resolve(
            $query->list(),
            "RESOURCE"
        );
    }
}
