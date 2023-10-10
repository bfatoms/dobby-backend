<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPermission;

class PermissionController extends BaseController
{


    public function index()
    {
        $data = [];
        foreach (config('permissions') as $module => $actions) {
            $data[] = [
                'module' => $module,
                'action' => $actions
            ];
        }
        return $this->resolve($data, "MODULE_PERMISSION_LIST");
    }


    public function toggle($user, $module, $action)
    {
        $user = User::find($user);

        if (empty($user)) {
            return $this->reject("USER_NOT_FOUND");
        }

        $this->authorize('assignPermission', UserPermission::class);

        $permission = $user->permissions();

        $data = $permission->where('module', $module)->where('action', $action)->first();

        if (!empty($data)) {
            $data->delete();

            return $this->resolve([], 'MODULE_PERMISSION_DISABLED');
        } else {
            $permission->create([
                'module' => $module,
                'action' => $action
            ]);

            return $this->resolve([], 'MODULE_PERMISSION_ENABLED');
        }

        return $this->reject('UNKNOWN_RESULT');
    }
}
