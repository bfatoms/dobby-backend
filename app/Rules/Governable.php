<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;

trait Governable
{
    protected $validator = null;

    protected function validate(array $rules, $name, $value, $messages = [])
    {
        $this->validator = Validator::make([$name => $value], [$name => $rules], $messages);

        return $this->validator->passes();
    }

    public function message()
    {
        $errors = $this->validator->errors();

        return $errors->all();
    }
}
