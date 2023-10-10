<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaxRateCreateRequest;
use App\Http\Requests\TaxRateUpdateRequest;
use App\Models\TaxRate;


class TaxRateController extends BaseController
{
    protected $model = TaxRate::class;

    protected $create_request = TaxRateCreateRequest::class;

    protected $update_request = TaxRateUpdateRequest::class;
}
