<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferMoneyCreateRequest;
use App\Http\Requests\TransferMoneyUpdateRequest;
use App\Models\TransferMoney;


class TransferMoneyController extends BaseController
{
    protected $model = TransferMoney::class;

    protected $create_request = TransferMoneyCreateRequest::class;

    protected $update_request = TransferMoneyUpdateRequest::class;
}
