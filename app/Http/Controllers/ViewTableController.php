<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ViewTableController extends BaseController
{

    public function updateViewTable()
    {
        if (request()->view_table === 'tasks_view') {
            Artisan::call('update-view:tasks');
            return $this->resolve(null, 'TASKS_VIEW_TABLE_UPDATED');
        } else if (request()->view_table === 'expenses_view') {
            Artisan::call('update-view:expenses');
            return $this->resolve(null, 'EXPENSES_VIEW_TABLE_UPDATED');
        } else if (request()->view_table === 'projects_view') {
            Artisan::call('update-view:projects');
            return $this->resolve(null, 'PROJECTS_VIEW_TABLE_UPDATED');
        } else {
            return $this->resolve(null, 'NO_VIEW_TABLE_UPDATED');
        }
    }
}
