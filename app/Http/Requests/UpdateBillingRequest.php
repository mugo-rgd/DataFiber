<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBillingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $billingId = $this->route('id');

        return [
            'lease_id' => 'required|exists:leases,id',
            'customer_id' => 'required|exists:users,id',
            'billing_number' => 'required|unique:lease_billings,billing_number,' . $billingId,
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
            'status' => 'required|in:pending,paid,overdue,draft'
        ];
    }
}
