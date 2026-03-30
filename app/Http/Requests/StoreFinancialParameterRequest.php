<?php
// app/Http/Requests/StoreFinancialParameterRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFinancialParameterRequest extends FormRequest
{
    public function authorize()
    {
        // return $this->user()->hasRole('finance-access');
        return $this->user() && $this->user()->can('isFinance');
    }

    public function rules()
    {
        return [
            'parameter_name' => ['required', 'string', 'max:50'],
            'parameter_value' => ['required', 'numeric', 'min:0'],
            'effective_from' => ['required', 'date'],
            'currency_code' => ['nullable', 'string', 'size:3'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }
}
