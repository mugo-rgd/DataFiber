<?php
// database/seeders/DocumentTypeSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentType;
use Illuminate\Support\Facades\DB;

class DocumentTypeSeeder extends Seeder
{
    public function run()
    {
        $documentTypes = [
            [
                'document_type' => 'business_license',
                'name' => 'Business License',
                'description' => 'Valid business license certificate',
                'is_required' => true,
                'max_file_size' => 2048,
                'allowed_extensions' => json_encode(['pdf', 'jpg', 'jpeg', 'png']),
                'sort_order' => 1,
            ],
            [
                'document_type' => 'tax_certificate',
                'name' => 'Tax Certificate',
                'description' => 'Tax registration certificate',
                'is_required' => true,
                'max_file_size' => 2048,
                'allowed_extensions' => json_encode(['pdf', 'jpg', 'jpeg', 'png']),
                'sort_order' => 2,
            ],
            [
                'document_type' => 'id_proof',
                'name' => 'Trade Licence',
                'description' => 'Valid trade license document',
                'is_required' => true,
                'max_file_size' => 2048,
                'allowed_extensions' => json_encode(['pdf', 'jpg', 'jpeg', 'png']),
                'sort_order' => 3,
            ],
            [
                'document_type' => 'ca_licence',
                'name' => 'CA Certificate',
                'description' => 'Chartered Accountant certification',
                'is_required' => false,
                'max_file_size' => 2048,
                'allowed_extensions' => json_encode(['pdf', 'jpg', 'jpeg', 'png']),
                'sort_order' => 4,
            ],
            [
                'document_type' => 'cr12_certificate',
                'name' => 'CR12 Certificate',
                'description' => 'Company directors and shareholders certificate',
                'is_required' => false,
                'max_file_size' => 2048,
                'allowed_extensions' => json_encode(['pdf', 'jpg', 'jpeg', 'png']),
                'sort_order' => 5,
            ],
            [
                'document_type' => 'tax_compliance_certificate',
                'name' => 'Tax Compliance Certificate',
                'description' => 'Tax compliance status certificate',
                'is_required' => true,
                'max_file_size' => 2048,
                'allowed_extensions' => json_encode(['pdf', 'jpg', 'jpeg', 'png']),
                'sort_order' => 6,
            ],
        ];

        foreach ($documentTypes as $type) {
            DocumentType::create($type);
        }
    }
}
