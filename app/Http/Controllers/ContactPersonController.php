<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactPersonCreateRequest;
use App\Http\Requests\ContactPersonUpdateRequest;
use App\Models\ContactPerson;

class ContactPersonController extends BaseController
{
    protected $model = ContactPerson::class;

    protected $create_request = ContactPersonCreateRequest::class;

    protected $update_request = ContactPersonUpdateRequest::class;
}
