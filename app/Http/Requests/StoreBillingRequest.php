<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBillingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lease_id' => 'required|exists:leases,id',
            'customer_id' => 'required|exists:users,id',
            'billing_number' => 'required|unique:lease_billings',
            'billing_date' => 'required|date',
            'due_date' => 'required|date|after:billing_date',
            'total_amount' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_cycle' => 'required|in:monthly,quarterly,annually,one_time',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,paid,overdue,draft',
            'line_items' => 'nullable|array'
        ];
    }

    public function messages(): array
    {
        return [
            'due_date.after' => 'Due date must be after billing date.',
            'period_end.after' => 'Period end must be after period start.',
            'total_amount.min' => 'Total amount must be at least 0.',
        ];
    }
}
