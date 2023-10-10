<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DueTypeRule implements Rule
{
    use Governable;
    
    private $name;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->name = strtoupper($attribute);
        
        return $this->validate([
            'in:' . implode(",", config('company.due_date_types')), 'nullable'
        ], $attribute, $value, $this->formatMessage($attribute));
    }

    public function formatMessage($attribute)
    {
        $attribute = strtoupper($attribute);

        return [
            'in' => "{$attribute}_TYPE_MUST_BE_ONE_OF_DUE_TYPES"
        ];
    }
}
