<?php
// app/Http/Requests/StoreColocationSiteRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreColocationSiteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'colocation_sites' => 'required|array|min:1',
            'colocation_sites.*.site_name' => 'required|string|max:255',
            'colocation_sites.*.service_type' => 'required|in:shelter_space,rack,cage,suites',
            'design_request_id' => 'required|exists:design_requests,id'
        ];
    }

    public function messages()
    {
        return [
            'colocation_sites.required' => 'At least one colocation site is required',
            'colocation_sites.*.site_name.required' => 'Site name is required',
            'colocation_sites.*.service_type.required' => 'Service type is required',
            'colocation_sites.*.service_type.in' => 'Service type must be one of: shelter_space, rack, cage, suites',
            'design_request_id.required' => 'Design request ID is required',
            'design_request_id.exists' => 'The selected design request does not exist'
        ];
    }
}
