<?php
// app/Models/CompanyProfile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyProfile extends Model
{
    use HasFactory;

    protected $fillable = [
         'user_id',
    'company_name',  // Add this if not already present
    'kra_pin',
    'phone_number',
    'registration_number',
    'sap_account',  // This exists but won't be editable
    'company_type',
    'contact_name_1',
    'contact_phone_1',
    'contact_name_2',
    'contact_phone_2',
    'physical_location',
    'road',
    'town',
    'address',
    'code',
    'description',
    'profile_photo',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the company profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if profile is complete
     */
    public function isComplete(): bool
    {
        return !empty($this->kra_pin) &&
               !empty($this->phone_number) &&
               !empty($this->registration_number) &&
               !empty($this->company_type) &&
               !empty($this->contact_name_1) &&
               !empty($this->contact_phone_1) &&
               !empty($this->physical_location) &&
               !empty($this->road) &&
               !empty($this->town) &&
               !empty($this->address) &&
               !empty($this->code);
    }

    /**
     * Get formatted address
     */
    public function getFullAddressAttribute(): string
    {
        return "{$this->physical_location}, {$this->road}, {$this->town}, {$this->address} - {$this->code}";
    }

    /**
 * Get company name from user relationship
 */
public function getCompanyNameAttribute(): string
{
    return $this->user->name ?? 'Not provided';
}

/**
 * Get company email from user relationship
 */
public function getCompanyEmailAttribute(): string
{
    return $this->user->email ?? 'Not provided';
}

/**
 * Alias for phone_number
 */
public function getCompanyPhoneAttribute(): string
{
    return $this->phone_number ?? 'Not provided';
}

/**
 * Alias for contact_name_1
 */
public function getContactPersonAttribute(): string
{
    return $this->contact_name_1 ?? 'Not provided';
}

/**
 * Alias for kra_pin
 */
public function getTaxIdAttribute(): string
{
    return $this->kra_pin ?? 'Not provided';
}

/**
 * Get company address
 */
public function getCompanyAddressAttribute(): string
{
    return $this->full_address ?? 'Not provided';
}
}
