<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimeEntryCreateRequest;
use App\Http\Requests\TimeEntryUpdateRequest;
use App\Models\TimeEntry;
use Exception;

class TimeEntryController extends BaseController
{
    protected $model = TimeEntry::class;

    protected $view_table = 'time_entries_view';

    protected $create_request = TimeEntryCreateRequest::class;

    protected $update_request = TimeEntryUpdateRequest::class;

    public function store()
    {
        $this->authorize('viewAny', $this->model);

        $user = auth()->user();
        $user_id = $user->id; // if limited user

        // if standard/admin
        if ($user->isAllowedTo('projects', 'create')) {
            $user_id = request('user_id');
        }

        if (isset($this->create_request)) {
            $request = (new $this->create_request);

            request()->validate($request->rules(), $request->messages());
        }

        $data = request()->all();

        return $this->transact(function () use ($data, $user_id) {
            try {
                $data['user_id'] = $user_id;
                return $this->model::create($data);
            } catch (Exception $ex) {
                dd($ex);
            }
        }, "RESOURCE_CREATED", "RESOURCE_NOT_CREATED");
    }

    public function update($id)
    {
        $this->authorize('viewAny', $this->model);

        $user = auth()->user();
        $user_id = $user->id; // if limited user

        // if standard/admin
        if ($user->isAllowedTo('projects', 'update')) {
            $user_id = request('user_id');
        }

        if (isset($this->update_request)) {
            $request = (new $this->update_request);

            request()->validate($request->rules(), $request->messages());
        }

        $data = request()->all();
        $data['user_id'] = $user_id;

        return $this->transact(function () use ($id, $data) {
            $resource = $this->model::find($id);

            $resource->update($data);

            return $resource;
        }, "RESOURCE_UPDATED", "RESOURCE_NOT_UPDATED");
    }

    public function index()
    {
        $this->authorize('viewAny', $this->model);

        $user = auth()->user();

        $this->model = new $this->model;

        $is_standard = true;
        $standardPermissions = ["show", "trash", "create", "list", "update"];

        foreach ($standardPermissions as $permission) {
            if ($user->isAllowedTo('projects', $permission) === false) {
                $is_standard = false;
                break;
            }
        }

        $query = $this->model->setTable($this->view_table ?? $this->model->getTable())
            ->when(request('tasks_project_id'), function ($q) {
                $q->whereHas('task', function ($q) {
                    $q->where('tasks.project_id', request('tasks_project_id'));
                });
            })
            ->when($is_standard === false && $user->isAllowedTo('projects', 'all') === false, function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
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
            })
            ->when(request('start_at'), function($q){
                $q->whereBetween('time_entry_date', [request('start_at'), request('end_at')]);
            });

        $query = $this->parseFields($query, ['end_at', 'start_at']);

        return $this->resolve(
            $query->list(),
            "RESOURCE_LIST"
        );
    }

    protected function show($id)
    {
        $this->authorize('view', $this->model);

        $user = auth()->user();

        $this->model = new $this->model;

        $data = $this->model->setTable($this->view_table ?? $this->model->getTable())
            ->when($user->isAllowedTo('projects', 'all') === false, function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
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
            ->find($id);

        if (empty($data)) {
            return $this->reject('RESOURCE_NOT_FOUND');
        }

        return $this->resolve($data, "RESOURCE_FOUND");
    }
}
