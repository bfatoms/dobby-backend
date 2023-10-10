<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Http\Requests\SettingCreateRequest;
use App\Http\Requests\SettingUpdateRequest;

class SettingController extends BaseController
{
    protected $model = Setting::class;

    protected $create_request = SettingCreateRequest::class;

    protected $update_request = SettingUpdateRequest::class;

    public function index()
    {
        return $this->show($this->model::first()->id);
    }

    public function store()
    {
        return $this->reject('CREATING_NEW_SETTING_NOT_ALLOWED_PLEASE_USE_UPDATE');
    }

    public function destroy($id)
    {
        return $this->reject('DELETE_NOT_ALLOWED_ON_SETTINGS');
    }
}
