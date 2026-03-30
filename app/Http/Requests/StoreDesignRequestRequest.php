<?php
// app/Http/Requests/StoreDesignRequestRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDesignRequestRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'technical_requirements' => 'required|string',

            // Route details
            'route_points' => 'nullable|json',
            'cores_required' => 'nullable|integer|min:1',
            'distance' => 'nullable|numeric|min:0.1',
            'terms' => 'nullable|string|max:255',
            'technology_type' => 'nullable|string|max:255',
            'link_class' => 'nullable|string|max:255',

            // Colocation sites validation
            'colocation_sites' => 'sometimes|array',
            'colocation_sites.*.site_name' => 'required_with:colocation_sites|string|max:255',
            'colocation_sites.*.service_type' => 'required_with:colocation_sites|in:shelter_space,rack,cage,suites',

            // Additional services
            'colocation_services' => 'sometimes|array',
            'colocation_services.*' => 'exists:colocation_services,service_id',
        ];
    }

    public function messages()
    {
        return [
            'colocation_sites.*.site_name.required_with' => 'Site name is required for all colocation sites',
            'colocation_sites.*.service_type.required_with' => 'Service type is required for all colocation sites',
            'colocation_sites.*.service_type.in' => 'Service type must be one of: shelter_space, rack, cage, suites',
        ];
    }
}
