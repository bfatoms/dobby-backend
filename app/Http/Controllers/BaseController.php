<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;


abstract class BaseController extends Controller
{
    protected $model;

    protected $view_table;

    protected $create_request;

    protected $update_request;

    protected function resolve($data, $message = "", $code = 200, $headers = [])
    {
        return response()->json([
            'data' => $data,
            'message' => $message
        ], $code, $headers);
    }

    protected function reject($message = "", $code = 422, $headers = [])
    {
        return response()->json([
            'data' => [],
            'message' => $message
        ], $code, $headers);
    }

    protected function transact($fn, $on_success = "", $on_error = null)
    {
        try {
            DB::beginTransaction();
            $data = $fn();
            //do something
            DB::commit();
            return $this->resolve($data, $on_success);
        } catch (QueryException $ex) {
            DB::rollback();
            if (config('app.env') == 'local') {
                $on_error = $ex;
            }
            return $this->reject($on_error);
        } catch (Exception $ex) {
            DB::rollback();
            if (config('app.env') == 'local') {
                $on_error = $ex;
            }
            return $this->reject($on_error, $ex->getCode());
        }
    }

    public function avatar($id)
    {
        $model = $this->model::with('avatar')->find($id);
    
        if (!$model) {
            return $this->reject("Model Not Found");
        }

        $file = request()->file('file');

        $files = is_array($file) ? $file : [$file];

        $uploaded = [];
    
        foreach ($files as $file) {
            $uploaded[] = $model->upload($file, 'avatar');
        }
    
        $message = count($uploaded) > 1 ? "Files have been uploaded" : "File has been uploaded";
    
        return $this->resolve($uploaded, $message);
    }
    

    public function trashAvatar($id)
    {
        $model = $this->model::with('avatar')->find($id);

        $result = $model->deleteAvatar();

        if ($result == null) {
            return $this->reject('NO_AVATAR_DELETED');
        } elseif ($result == false) {
            return $this->reject('AVATAR_NOT_DELETED');
        }

        return $this->resolve($model['avatar'], "AVATAR_DELETED");
    }

    public function attach($id)
    {
        $model = $this->model::find($id);

        if (!empty($model)) {
            $files = request()->file('file');

            if (is_array($files) === false) {
                return $this->resolve($model->upload($files, request('type', null)), "File has been uploaded");
            }

            $uploaded = [];

            foreach ($files as $file) {
                $uploaded[] = $model->upload($file, request('type', null));
            }

            return $this->resolve($uploaded, "File has been uploaded");
        }

        return $this->reject("Model Not Found");
    }

    public function parseWith()
    {
        $queries = explode(",", request('with'));

        $new_queries = [];

        foreach ($queries as $query) {
            $query = str_replace(";", ",", $query);

            $new_queries[] = $query;
        }

        if (empty($new_queries)) {
            $new_queries = $queries;
        }

        return $new_queries;
    }

    // public function parseFields($query, $additional_known_fields = [])
    // {
    //     $known_fields = array_merge([
    //         'all', 'with', 'sortKey', 'sortOrder', 'page', 'limit', 'offset', 'search', 'order_type', 'ids', 'tasks_project_id'
    //     ], $additional_known_fields);
    
    //     $possible_fields = request()->except($known_fields);
    
    //     if (!empty($possible_fields)) {
    //         foreach ($possible_fields as $key => $value) {
    //             list($key, $operation) = explode(":", $key, 2);
    
    //             $like = ($operation === 'like');
    
    //             if ($like) {
    //                 $value = "%$value%";
    //             }
    
    //             $query->where(function ($query) use ($key, $operation, $value, $like) {
    //                 if ($like) {
    //                     $query->orWhere($key, 'like', $value);
    //                 } else {
    //                     $query->orWhere($key, $operation, $value);
    //                 }
    //             });
    //         }
    //     }
    
    //     return $query;
    // }

    public function parseFields($query, $additional_known_fields = [])
    {
        $known_fields = array_merge([
            'all', 'with', 'sortKey', 'sortOrder', 'page', 'limit', 'offset', 'search', 'order_type', 'ids', 'tasks_project_id'
        ], $additional_known_fields);

        $possible_fields = request()->except($known_fields);

        if (!empty($possible_fields)) {
            foreach ($possible_fields as $key => $value) {
                $val_operation = explode(":", $value);

                $key = explode(":", $key);

                if (count($key) > 1) {

                    $like = (count($val_operation) > 1 && $val_operation[0] == 'like') ? true : false;

                    $val = ($like == true) ? "%$val_operation[1]%" : $val_operation[0];

                    $operation = ($like == true) ? "like" : $val_operation[0];

                    if ($like == false && count($val_operation) <= 1) {
                        $operation = "=";
                        $val = $val_operation[0];
                    }

                    $query->whereHas($key[0], function ($q) use ($key, $operation, $val) {
                        $q->where($key[1], $operation, $val);
                    });
                } else {
                    $like = (count($val_operation) > 1 && $val_operation[0] == 'like') ? true : false;

                    try {
                        $val = ($like == true) ? "%$val_operation[1]%" : $val_operation[1];
                    } catch (Exception $ex) {
                    }

                    $operation = ($like == true) ? "like" : $val_operation[0];

                    if ($like == false && count($val_operation) <= 1) {
                        $operation = "=";
                        $val = $val_operation[0];
                    }

                    $query->where($key[0], $operation, $val);
                }
            }
        }

        return $query;
    }

    public function index()
    {
        $this->authorize('viewAny', $this->model);
    
        $this->model = new $this->model;
    
        $query = $this->model->setTable($this->view_table ?? $this->model->getTable())
            ->when(request('with'), fn ($q) => $q->with($this->parseWith()))
            ->when(request('scopes'), fn ($q) => collect(request('scopes'))->each(fn ($scope) => $q->$scope()))
            ->when(request('fields'), fn ($q) => $q->select(array_merge(request('fields'), ['id'])))
            ->when(request('sortKey'), fn ($q) => $q->arrange(request('sortKey'), request('sortOrder')))
            ->when(request('order_type'), fn ($q) => $q->whereIn('order_type', explode(',', request('order_type'))))
            ->when(request('ids'), fn ($q) => $q->whereIn('id', explode(',', request('ids'))));
    
        $query = $this->parseFields($query);
    
        return $this->resolve(
            $query->list(),
            "RESOURCE_LIST"
        );
    }

    // public function index()
    // {
    //     $this->authorize('viewAny', $this->model);

    //     $this->model = new $this->model;

    //     $query = $this->model->setTable($this->view_table ?? $this->model->getTable())
    //         ->when(request('with'), function ($q) {
    //             $q->with($this->parseWith());
    //         })
    //         ->when(request('scopes'), function ($q) {
    //             foreach (request('scopes') as $scope) {
    //                 $q->{$scope}();
    //             }
    //         })
    //         ->when(request('fields'), function ($q) {
    //             $q->select(array_merge(request('fields'), ['id']));
    //         })
    //         ->when(request('sortKey'), function ($q) {
    //             $q->arrange(request('sortKey'), request('sortOrder'));
    //         })
    //         ->when(request('order_type'), function ($q) {
    //             $types = explode(",", request('order_type'));
    //             $q->whereIn('order_type', $types);
    //         })
    //         ->when(request('ids'), function ($q) {
    //             $ids = explode(",", request('ids'));
    //             $q->whereIn('id', $ids);
    //         });

    //     $query = $this->parseFields($query);

    //     return $this->resolve(
    //         $query->list(),
    //         "RESOURCE_LIST"
    //     );
    // }

    public function applyFilterQueries($query)
    {
        $query->when(request('with'), function ($q) {
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

        return $query;
    }

    public function trashIndex()
    {
        $this->authorize('viewAny', $this->model);

        $query = $this->model::onlyTrashed()->when(request('with'), function ($q) {
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
            });

        $query = $this->parseFields($query);

        return $this->resolve(
            $query->list(),
            "RESOURCE_LIST"
        );
    }

    protected function show($id)
    {
        $this->authorize('view', $this->model);

        $this->model = new $this->model;

        $data = $this->model->setTable($this->view_table ?? $this->model->getTable())
            ->when(request('with'), fn ($q) => $q->with($this->parseWith()))
            ->when(request('scopes'), fn ($q) => collect(request('scopes'))->each(fn ($q) => $q->scope()))
            ->when(request('fields'), fn ($q) => $q->select(array_merge(request('fields'), ['id'])))
            ->find($id);

        if (empty($data)) {
            return $this->reject('RESOURCE_NOT_FOUND');
        }

        return $this->resolve($data, "RESOURCE_FOUND");
    }

    public function store()
    {
        $this->authorize('create', $this->model);

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

    public function update($id)
    {
        $this->authorize('update', $this->model);

        if (isset($this->update_request)) {
            $request = (new $this->update_request);

            request()->validate($request->rules(), $request->messages());
        }

        $data = request()->all();

        return $this->transact(function () use ($id, $data) {
            $resource = $this->model::find($id);

            $resource->update($data);

            return $resource;
        }, "RESOURCE_UPDATED", "RESOURCE_NOT_UPDATED");
    }

    public function destroy($ids)
    {
        $this->authorize('delete', $this->model);

        $ids = explode(",", $ids);

        $deleted = [];
        foreach ($ids as $id) {
            $resource = $this->model::find($id);
            if (!empty($resource)) {
                $destroyed = $resource->tryDelete();
                if ($destroyed === true) {
                    $deleted[] = $resource;
                } else {
                    return $this->reject($destroyed);
                }
            }
        }

        return $this->resolve($deleted, "RESOURCE_TRASHED");
    }

    public function restore($ids)
    {
        $this->authorize('restore', $this->model);

        $ids = explode(",", $ids);

        return $this->transact(function () use ($ids) {
            $restored = [];

            foreach ($ids as $id) {
                $restore = $this->model::onlyTrashed()->find($id);
                $restore->restore();
                $restored[] = $restore;
            }

            return $restored;
        }, "RESOURCE_RESTORED", "RESOURCE_NOT_RESTORED");
    }

    public function forceDelete($ids)
    {
        $this->authorize('forceDelete', $this->model);

        $ids = explode(",", $ids);

        return $this->transact(function () use ($ids) {
            $deleted = [];

            foreach ($ids as $id) {
                $resource = $this->model::onlyTrashed()->find($id);
                if (!empty($resource)) {
                    $resource->forceDelete();

                    $deleted[] = $resource;
                }
            }

            return $deleted;
        }, "RESOURCE_DELETED", "RESOURCE_NOT_DELETED");
    }
}
