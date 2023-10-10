<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\Contact;
use App\Models\Currency;
use App\Models\Setting;
use App\Models\Task;
use App\Models\TaxRate;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDO;

class OrderService
{
    public function getInitialData($order_type, $contact_id)
    {
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
        ])->find($contact_id);

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

    public function getFixedRateTasks($task_ids)
    {
        $fixedPriceTasksQuery = Task::whereIn('tasks.id', $task_ids)
            ->selectRaw('
                tasks.rate as unit_price,
                CONCAT(projects.name, " - ", tasks.name) as description,
                1 as quantity,
                tasks.project_id
            ')
            ->leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
            ->where('charge_type', 'FIXED_RATE')
            ->where('tasks.status', 'ON_GOING');

        return $fixedPriceTasksQuery;
    }

    public function markTimeEntriesAsInvoiced($task_ids, $from_date, $to_date)
    {
        // Mark as invoiced
        TimeEntry::whereIn('task_id', $task_ids)
            ->whereBetween('time_entries.time_entry_date', [
                'from_date' => Carbon::parse($from_date)->setTime(0, 0, 0),
                'to_date' =>  Carbon::parse($to_date)->setTime(23, 59, 59),
            ])
            ->update(['is_invoiced' => true]);
    }

    public function baseTaskQuery($task_ids, $from_date, $to_date)
    {
        $tasksQuery = Task::whereIn('tasks.id', $task_ids)
            ->selectRaw('sum(time_entries.duration) as quantity, tasks.project_id')
            ->leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
            ->leftJoin('time_entries', 'time_entries.task_id', '=', 'tasks.id')
            ->where('time_entries.is_invoiced', false)
            ->where('tasks.charge_type', ['HOURLY_RATE', 'NON_CHARGEABLE'])
            ->when(request('orderline_option') === 3, function ($query) use ($from_date, $to_date) {
                $query->whereBetween('time_entries.time_entry_date', [
                    'from_date' => Carbon::parse($from_date)->setTime(0, 0, 0),
                    'to_date' =>  Carbon::parse($to_date)->setTime(23, 59, 59),
                ]);
            })
            ->groupBy('tasks.id');

        return $tasksQuery;
    }

    public function applyQueryByDescriptionType($query, $type, $rate_type = null, $project_id = null)
    {
        if ($type === 'tasks') {
            $query->addSelect(DB::raw('tasks.rate as unit_price'))
                ->addSelect(DB::raw('CONCAT(projects.name, " - ", tasks.name) as description'))
                ->groupBy('tasks.id');
        } else if ($type === 'employee_task') {
            $query->when($rate_type === 'EMPLOYEE_RATE', function ($q) {
                $q->addSelect(DB::raw('user_project_rates.final_sales_price as unit_price'));
                $q->addSelect(DB::raw('user_project_rates.is_project_setting_id'));
                $q->addSelect(DB::raw('user_project_rates.project_setting_product_id as product_id'));
            })
                ->when($rate_type === 'TASK_RATE', function ($q) {
                    $q->addSelect(DB::raw('tasks.rate as unit_price'));
                })
                ->addSelect(DB::raw('CONCAT(projects.name, " - ", tasks.name, " - ", users.first_name, " ", users.last_name) as description'))
                ->leftJoin('users', 'time_entries.user_id', '=', 'users.id')
                ->leftJoin('user_project_rates', function ($join) use ($project_id) {
                    $join->on('user_project_rates.user_id', '=', 'users.id')
                        ->where('user_project_rates.project_id', '=', $project_id);
                })
                ->groupBy([
                    'tasks.id',
                    'users.id',
                    'user_project_rates.final_sales_price',
                    'user_project_rates.project_id',
                    'user_project_rates.is_project_setting_id',
                    'user_project_rates.project_setting_product_id',
                ]);
        } else if ($type === 'time_entries') {
            $query->when($rate_type === 'EMPLOYEE_RATE', function ($q) {
                $q->addSelect(DB::raw('user_project_rates.final_sales_price as unit_price'));
                $q->addSelect(DB::raw('user_project_rates.is_project_setting_id'));
                $q->addSelect(DB::raw('user_project_rates.project_setting_product_id as product_id'));
            })
                ->when($rate_type === 'TASK_RATE', function ($q) {
                    $q->addSelect(DB::raw('tasks.rate as unit_price'));
                })
                ->addSelect(DB::raw('CONCAT(projects.name, " - ", tasks.name, " - ", users.first_name, " ", users.last_name, " - ", time_entries.time_entry_date, " - " , time_entries.description) as description'))
                ->leftJoin('users', 'time_entries.user_id', '=', 'users.id')
                ->leftJoin('user_project_rates', function ($join) use ($project_id) {
                    $join->on('user_project_rates.user_id', '=', 'users.id')
                        ->where('user_project_rates.project_id', '=', $project_id);
                })
                ->groupBy([
                    'tasks.id',
                    'users.id',
                    'user_project_rates.final_sales_price',
                    'user_project_rates.project_id',
                    'time_entries.id',
                    'time_entries.time_entry_date',
                    'time_entries.description',
                ]);
        }
        return $query;
    }

    public function generateOrderlines($type, $project_id, $task_ids, $from_date, $to_date, $rate_type = null)
    {
        $order_lines = [];

        $fixedPriceTasksQuery = $this->getFixedRateTasks($task_ids);
        $fixedPriceTasks = $fixedPriceTasksQuery->get();
        $fixedPriceTasksQuery->update(['tasks.status' => 'INVOICED']);

        $query = $this->baseTaskQuery($task_ids, $from_date, $to_date);
        $tasks = $this->applyQueryByDescriptionType($query, $type, $rate_type, $project_id)
            ->get()
            ->map(function ($item) {
                if ($item->is_project_setting_id === 0) {
                    $item->product_id = null;
                }
                return $item;
            });

        $this->markTimeEntriesAsInvoiced($task_ids, $from_date, $to_date);

        $order_lines = array_merge(
            $fixedPriceTasks->toArray(),
            $tasks->toArray()
        );

        return $order_lines;
    }
}
