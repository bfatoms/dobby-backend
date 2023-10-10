<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskCreateRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class TaskController extends BaseController
{
    protected $model = Task::class;

    protected $view_table = 'tasks_view';

    protected $create_request = TaskCreateRequest::class;

    protected $update_request = TaskUpdateRequest::class;

}
