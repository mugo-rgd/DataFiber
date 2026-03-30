<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:500',
            'transaction_date' => 'required|date',
            'payment_method' => 'required|in:credit_card,bank_transfer,cash,digital_wallet,check',
            'category' => 'required|in:invoice_payment,refund,fee,salary,rent,utilities,maintenance,equipment,software,other',
            'status' => 'required|in:pending,completed,failed,cancelled',
            'customer_id' => 'nullable|exists:users,id',
            'billing_id' => 'nullable|exists:lease_billings,id',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ];
    }
}
