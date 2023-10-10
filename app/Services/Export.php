<?php

namespace App\Services;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Str;

class Export implements FromCollection
{
    public function __construct($model)
    {
        if (is_string($model)) {
            $model = Str::studly(Str::singular(request('model')));
            $this->model_name = $model;
            $model = $this->getModel($model);
        }
        $this->model = $model;
    }

    public function collection()
    {
        return $this->model::get();
    }

    public function getModel($model)
    {
        return (config('imex.model_path') ?? "App\Models") . '\\' . $model ?? "App" . '\\' . $model;
    }
}