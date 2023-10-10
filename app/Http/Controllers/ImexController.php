<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\Import;
use App\Http\Requests\ImportRequest;
use App\Services\Export;
use Maatwebsite\Excel\Facades\Excel;

class ImexController extends BaseController
{    
    public function import(ImportRequest $request)
    {
        $import = new Import();

        $import->request($request);

        $import->model(request('model'));

        return $this->resolve($import->now(), "DATA_IMPORTED");
    }

    
    public function export(Request $request)
    {
        return Excel::download(new Export(request('model')), 'data.csv');
    }
}
