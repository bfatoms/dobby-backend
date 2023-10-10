<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChartOfAccountCreateRequest;
use App\Http\Requests\ChartOfAccountUpdateRequest;
use App\Models\BankAccountTransaction;
use App\Models\ChartOfAccount;
use App\Models\Order;
use App\Models\Payment;
use App\Models\TransferMoney;
use Illuminate\Support\Facades\DB;


class ChartOfAccountController extends BaseController
{
    protected $model = ChartOfAccount::class;

    protected $create_request = ChartOfAccountCreateRequest::class;

    protected $update_request = ChartOfAccountUpdateRequest::class;

    public function transactions($id)
    {
        $this->authorize('viewAny', BankAccountTransaction::class);

        $query = BankAccountTransaction::addSelect(['balance' => function ($query) use ($id) {
            $query->selectRaw("sum(received) - sum(spent)")
                ->from('bank_account_transactions as t2')
                ->whereColumn('t2.transaction_unique_date', '<=', 'bank_account_transactions.transaction_unique_date')
                ->where('account_id', $id);
        }])->where('account_id', $id)
            ->when(request('with'), function ($query) {
                $query->with($this->parseWith());
            });

        if (empty(request('sortKey', null)) || request('sortKey') == 'transaction_date') {
            $query->orderBy('transaction_unique_date', request('sortOrder', 'desc'));
        } else {
            $query->orderBy(request('sortKey'), request('sortOrder', 'desc'));
        }

        $transactions = $query->paginate(10);

        if (request('sortKey') != 'transaction_date' && !empty(request('sortKey'))) {
            $transactions->makeHidden('balance');
        }

        return $this->resolve($transactions, 'BANK_TRANSACTIONS');
    }

    public function deleteTransactions()
    {
        $this->authorize('delete', BankAccountTransaction::class);

        $deleted = [];

        foreach (request()->all() as $request) {
            if ($request['type'] == 'transfer-money') {
                $resource = TransferMoney::find($request['id']);
                if (optional($resource)->delete()) {
                    $deleted[] = $resource;
                }
            } elseif ($request['type'] == 'order') {
                $resource = Order::find($request['id']);
                if (optional($resource)->delete()) {
                    $deleted[] = $resource;
                }
            } else {
                $resource = Payment::with('orders')->find($request['id']);
                foreach ($resource->orders as $order) {
                    $order->status = "APPROVED";
                    $order->save();
                }
                if (optional($resource)->delete()) {
                    $deleted[] = $resource;
                }
            }
        }

        return $this->resolve($deleted, "DELETED_TRANSACTIONS");
    }

    public function trends()
    {
        $this->authorize('viewAny', BankAccountTransaction::class);

        $accounts = ChartOfAccount::where('type', 'bank')->get();

        $account_trends = [];

        foreach ($accounts as $account) {
            $account_trends[] = [
                "account_id" => $account['id'],
                "trend" => $this->trend($account['id'], request('from'), request('to'))
            ];
        }

        return $this->resolve($account_trends, "BANK_ACCOUNT_TRENDS");
    }

    public function trend($account_id, $from, $to)
    {
        // $date_range = DB::select(DB::raw("WITH recursive date_ranges AS (
        //     select ? as gen_date
        //    union all
        //    select gen_date + interval 1 day
        //    from date_ranges
        //    where gen_date < ?)
        // select * from date_ranges"), [$from, $to]);

        $date_range = DB::select(DB::raw("select * from
        (select adddate('1970-01-01',t4*10000 + t3*1000 + t2*100 + t1*10 + t0) gen_date from
        (select 0 t0 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
        (select 0 t1 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
        (select 0 t2 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
        (select 0 t3 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
        (select 0 t4 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
        Where gen_date between ? and ?
        ")->getValue(DB::connection()->getQueryGrammar()), [$from, $to]);


        $date_range = json_decode(json_encode($date_range), true);

        $query = BankAccountTransaction::selectRaw("DATE(`bank_account_transactions`.transaction_date) as trans_date")
            ->addSelect(['balance' => function ($query) use ($account_id) {
                $query->selectRaw("sum(received) - sum(spent)")
                    ->from('bank_account_transactions as t2')
                    ->whereColumn('t2.transaction_date', '<=', 'bank_account_transactions.transaction_date')
                    ->where('account_id', $account_id);
            }])->where('account_id', $account_id)
            ->whereBetween('transaction_date', [$from, $to])
            ->groupByRaw('1, 2')
            ->when(request('with'), function ($query) {
                $query->with($this->parseWith());
            });

        $transactions = $query->get();

        $grouped_transactions = $transactions->groupBy('trans_date')->toArray();

        $data = [];

        $balance = 0;

        foreach ($date_range as $date) {
            if (in_array($date['gen_date'], array_keys($grouped_transactions))) {
                $balance = $grouped_transactions[$date['gen_date']][0]['balance'];
            }
            $data[] = [
                "transaction_date" => $date['gen_date'],
                "balance" => $balance
            ];
        }

        return $data;
    }

    public function transactionTrends($id)
    {
        $this->authorize('viewAny', BankAccountTransaction::class);

        return $this->resolve($this->trend($id, request('from'), request('to')), "BANK_ACCOUNT_TREND");
    }

}
