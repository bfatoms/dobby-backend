<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class DueRule implements Rule
{
    use Governable;
    
    private $name;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    { }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $type = request("{$attribute}_type", null);

        $this->name = strtoupper(Str::snake($attribute));

        $rule = ['integer', 'min:1'];

        if (empty($type)) {
            $rule[] = 'nullable';
            return $this->validate($rule, $attribute, $value);
        }

        if (in_array($type, ["of the following month", "of the current month"])) {
            $rule[] = 'max:31';
        }

        return $this->validate($rule, $attribute, $value, $this->formatMessage($attribute));
    }

    public function formatMessage($attribute)
    {
        $attribute = strtoupper($attribute);

        return [
            'min' => "{$attribute}_MIN_VALUE_IS_1",
            'max' => "{$attribute}_MAX_VALUE_IS_31",
            'integer' => "{$attribute}_MUST_BE_AN_INTEGER",
            'required' => "{$attribute}_REQUIRED"
        ];
    }
}
