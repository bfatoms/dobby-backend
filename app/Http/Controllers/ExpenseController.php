<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseCreateRequest;
use App\Http\Requests\ExpenseUpdateRequest;
use App\Models\Expense;
use Exception;
use Illuminate\Support\Facades\DB;

class ExpenseController extends BaseController
{
    protected $model = Expense::class;

    protected $view_table = 'expenses_view';

    protected $create_request = ExpenseCreateRequest::class;

    protected $update_request = ExpenseUpdateRequest::class;

    public function store()
    {
        $this->authorize('all', $this->model);

        if (isset($this->create_request)) {
            $request = (new $this->create_request);

            request()->validate($request->rules(), $request->messages());
        }

        $data = request()->all();

        return $this->transact(function () use ($data) {
            try {
                return $this->model::create($data);
            } catch (Exception $ex) {
                // dd($ex);
            }
        }, "RESOURCE_CREATED", "RESOURCE_NOT_CREATED");
    }

    public function index()
    {
        $this->authorize('all', $this->model);

        $this->model = new $this->model;

        $query = $this->model->setTable($this->view_table ?? $this->model->getTable())
            ->when(request('with'), function ($q) {
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
            $query->list(),
            "RESOURCE_LIST"
        );
    }

    public function trackExpense()
    {
        request()->validate([
            'estimated_expense_id' => 'required',
            'tracked_expense_id' => 'required',
        ], [
            'estimated_expense_id.required' => 'ESTIMATED_EXPENSE_ID_REQUIRED',
            'tracked_expense_id.required' => 'TRACKED_EXPENSE_ID_REQUIRED',
        ]);

        try {
            DB::beginTransaction();

            $estimatedExpense = Expense::find(request('estimated_expense_id'));

            $trackedExpense = Expense::find(request('tracked_expense_id'));

            if ($estimatedExpense->is_estimated === false) {
                abort(422, 'ESTIMATED_EXPENSE_ID_SHOULD_BE_TYPE_ESTIMATED');
            }

            if ($trackedExpense->is_estimated === true) {
                abort(422, 'TRACKED_EXPENSE_ID_SHOULD_NOT_BE_TYPE_ESTIMATED');
            }

            $exists = $estimatedExpense->trackedExpenses()->where('tracked_expense_id', $trackedExpense->id)->exists();

            if ($exists) {
                abort(422, 'EXPENSES_ALREADY_CONNECTED');
            }

            $result = $estimatedExpense->trackedExpenses()->save($trackedExpense);

            DB::commit();

            return $this->resolve($result, "RESOURCE");
        } catch (Exception $ex) {
            DB::rollback();
            return $this->reject($ex->getMessage());
        }
    }
}
