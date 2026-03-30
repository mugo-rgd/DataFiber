<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AssignCustomersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

     /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'account_manager_id' => 'required|exists:users,id',
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'exists:users,id,role,customer',
            'assignment_notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'account_manager_id.required' => 'Please select an account manager.',
            'account_manager_id.exists' => 'The selected account manager does not exist.',
            'customer_ids.required' => 'Please select at least one customer.',
            'customer_ids.array' => 'Customers must be provided as an array.',
            'customer_ids.min' => 'Please select at least one customer.',
            'customer_ids.*.exists' => 'One or more selected customers are invalid or not customers.',
        ];
    }
}
