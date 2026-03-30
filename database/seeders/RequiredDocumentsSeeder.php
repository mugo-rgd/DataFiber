<?php
// database/seeders/RequiredDocumentsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use Illuminate\Support\Facades\DB;

class RequiredDocumentsSeeder extends Seeder
{
    public function run()
    {
        $requiredDocuments = [
            [
                'name' => 'KRA Pin Certificate',
                'slug' => 'kra-pin-certificate',
                'has_expiry' => false,
                'document_type' => 'kra_pin_certificate',
                'is_required' => true,
                'description' => 'Valid KRA Pin Certificate in PDF format',
                'file_path' => null,
                'file_name' => null,
                'uploaded_by' => null,
                'status' => 'pending_review',
            ],
            [
                'name' => 'Business Registration Certificate',
                'slug' => 'business-registration-certificate',
                'has_expiry' => true,
                'document_type' => 'business_registration_certificate',
                'is_required' => true,
                'description' => 'Certificate of Incorporation or Business Registration in PDF format',
                'file_path' => null,
                'file_name' => null,
                'uploaded_by' => null,
                'status' => 'pending_review',
            ],
            [
                'name' => 'ID Copy',
                'slug' => 'id-copy',
                'has_expiry' => false,
                'document_type' => 'id_copy',
                'is_required' => true,
                'description' => 'Copy of National ID/Passport of primary contact person in PDF format',
                'file_path' => null,
                'file_name' => null,
                'uploaded_by' => null,
                'status' => 'pending_review',
            ],
            [
                'name' => 'Company Profile Document',
                'slug' => 'company-profile-document',
                'has_expiry' => false,
                'document_type' => 'company_profile',
                'is_required' => false,
                'description' => 'Optional company profile document',
                'file_path' => null,
                'file_name' => null,
                'uploaded_by' => null,
                'status' => 'pending_review',
            ],
            [
                'name' => 'Tax Compliance Certificate',
                'slug' => 'tax-compliance-certificate',
                'has_expiry' => true,
                'document_type' => 'tax_compliance',
                'is_required' => false,
                'description' => 'Tax Compliance Certificate from KRA',
                'file_path' => null,
                'file_name' => null,
                'uploaded_by' => null,
                'status' => 'pending_review',
            ],
        ];

        foreach ($requiredDocuments as $document) {
            // Use updateOrCreate to avoid duplicates
            Document::updateOrCreate(
                ['slug' => $document['slug']],
                $document
            );
        }
    }
}
