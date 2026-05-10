<?php

namespace App\Services\CAK;

class NFPPdfMapper
{
    public static function map(array $data = []): array
    {
        return array_merge(
            self::page1(),
            self::page2(),
            self::page3Quarterly(),
            self::page4AnnualStaff(),
            self::page5Pwd(),
            self::page6EnvironmentComments(),
            self::page7Submitter()
        );
    }

    private static function page1(): array
    {
        return [
            'page_1' => [
                'licensee_name'  => ['x' => 60, 'y' => 160, 'w' => 125, 'font_size' => 10],
                'license_no'     => ['x' => 60, 'y' => 168, 'w' => 125, 'font_size' => 10],
                'other_licenses' => ['x' => 60, 'y' => 176, 'w' => 130, 'font_size' => 10],
                'financial_year' => ['x' => 70, 'y' => 208, 'w' => 70, 'font_size' => 10],

                'quarter_q1' => ['x' => 38,  'y' => 235, 'type' => 'checkbox', 'font_size' => 10],
                'quarter_q2' => ['x' => 81,  'y' => 235, 'type' => 'checkbox', 'font_size' => 10],
                'quarter_q3' => ['x' => 124, 'y' => 235, 'type' => 'checkbox', 'font_size' => 10],
                'quarter_q4' => ['x' => 167, 'y' => 235, 'type' => 'checkbox', 'font_size' => 10],
            ],
        ];
    }

    private static function page2(): array
    {
        return [
            'page_2' => [
    'county'        => ['x' => 40,  'y' => 42,  'w' => 45, 'font_size' => 10],
    'town'          => ['x' => 100,  'y' => 42,  'w' => 45, 'font_size' => 10],
    'street_road'   => ['x' => 155, 'y' => 42,  'w' => 45, 'font_size' => 10],

    'building_name' => ['x' => 55,  'y' => 50,  'w' => 45, 'font_size' => 10],
    'floor_no'      => ['x' => 108, 'y' => 50,  'w' => 30, 'font_size' => 10],
    'room_no'       => ['x' => 155, 'y' => 50,  'w' => 35, 'font_size' => 10],

    'p_o_box'       => ['x' => 55,  'y' => 66, 'w' => 45, 'font_size' => 10],
    'postal_town'   => ['x' => 100,  'y' => 66, 'w' => 45, 'font_size' => 10],
    'postal_code'   => ['x' => 150, 'y' => 66, 'w' => 35, 'font_size' => 10],

    'tel_no'        => ['x' => 55,  'y' => 82, 'w' => 55, 'font_size' => 10],
    'mobile_no'     => ['x' => 124, 'y' => 82, 'w' => 55, 'font_size' => 10],
    'other_tel'     => ['x' => 55,  'y' => 92, 'w' => 130, 'font_size' => 10],

    'email'         => ['x' => 48,  'y' => 113, 'w' => 130, 'font_size' => 10],
    'web_address'   => ['x' => 48,  'y' => 120, 'w' => 130, 'font_size' => 10],

    'ceo_name'         => ['x' => 94,  'y' => 149, 'w' => 100, 'font_size' => 10],
    'contact_person'   => ['x' => 80,  'y' => 158, 'w' => 100, 'font_size' => 10],
    'contact_landline' => ['x' => 68,  'y' => 164, 'w' => 55, 'font_size' => 10],
    'contact_mobile'   => ['x' => 140, 'y' => 164, 'w' => 55, 'font_size' => 10],
    'contact_email'    => ['x' => 60,  'y' => 172, 'w' => 130, 'font_size' => 10],

    'address_changed_yes' => [
        'x' => 158, 'y' => 188, 'type' => 'checkbox_match',
        'source' => 'address_changed', 'value' => 'yes'
    ],
    'address_changed_no' => [
        'x' => 173, 'y' => 188, 'type' => 'checkbox_match',
        'source' => 'address_changed', 'value' => 'no'
    ],
],
        ];
    }

 private static function page3Quarterly(): array
{
    $map = ['page_3' => []];

    /*
    |--------------------------------------------------------------------------
    | 2. TYPES OF INFRASTRUCTURE DEPLOYED
    |--------------------------------------------------------------------------
    */
    $infraY = 58;
    $infraGap = 5.0;

    for ($i = 1; $i <= 4; $i++) {
        $y = $infraY + (($i - 1) * $infraGap);

        $map['page_3']["infrastructure.$i.type"] = [
            'x' => 45,
            'y' => $y,
            'w' => 55,
            'font_size' => 8,
            'max_chars' => 32,
        ];

        $map['page_3']["infrastructure.$i.description"] = [
            'x' => 103,
            'y' => $y - 0.8,
            'w' => 92,
            'h' => 2.4,
            'type' => 'multiline',
            'font_size' => 8,
            'max_lines' => 2,
            'max_chars' => 105,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | 3.1 PRIMARY NUMBER ASSIGNMENTS
    |--------------------------------------------------------------------------
    */
    $secondaryGap = 5.0;
    $numberRows = [
        'short_codes' => 123,
        'ussd_codes' => 126,
        'premium_rate_numbers' => 132,
        'toll_free_numbers' => 136,
    ];

    foreach ($numberRows as $key => $y) {
        $map['page_3']["primary_numbers.$key.assigned"] = [
            'x' => 98,
            'y' => $y,
            'w' => 30,
            'font_size' => 8,
        ];

        $map['page_3']["primary_numbers.$key.utilized"] = [
            'x' => 157,
            'y' => $y,
            'w' => 30,
            'font_size' => 8,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | 3.2 SECONDARY NUMBER ASSIGNMENT
    |--------------------------------------------------------------------------
    */
    $secondaryY = 164;
    $secondaryGap = 5.0;

    for ($i = 1; $i <= 4; $i++) {
        $y = $secondaryY + (($i - 1) * $secondaryGap);

        $map['page_3']["secondary_numbers.$i.csp_name"] = [
            'x' => 45,
            'y' => $y,
            'w' => 35,
            'font_size' => 8,
            'max_chars' => 25,
        ];

        $map['page_3']["secondary_numbers.$i.shortcode_ussd"] = [
            'x' => 70,
            'y' => $y,
            'w' => 40,
            'font_size' => 8,
            'max_chars' => 30,
        ];

        $map['page_3']["secondary_numbers.$i.tariff"] = [
            'x' => 115,
            'y' => $y,
            'w' => 30,
            'font_size' => 8,
            'max_chars' => 15,
        ];

        $map['page_3']["secondary_numbers.$i.volume"] = [
            'x' => 165,
            'y' => $y,
            'w' => 30,
            'font_size' => 8,
            'max_chars' => 15,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | 3.3 BULK SMS
    |--------------------------------------------------------------------------
    */
    $bulkY = 210;
    $bulkGap = 5.0;

    for ($i = 1; $i <= 4; $i++) {
        $y = $bulkY + (($i - 1) * $bulkGap);

        $map['page_3']["bulk_sms.$i.csp_name"] = [
            'x' => 50,
            'y' => $y,
            'w' => 45,
            'font_size' => 8,
            'max_chars' => 30,
        ];

        $map['page_3']["bulk_sms.$i.tariff"] = [
            'x' => 112,
            'y' => $y,
            'w' => 35,
            'font_size' => 8,
            'max_chars' => 15,
        ];

        $map['page_3']["bulk_sms.$i.volume"] = [
            'x' => 165,
            'y' => $y,
            'w' => 35,
            'font_size' => 8,
            'max_chars' => 15,
        ];
    }

    return $map;
}

    private static function page4AnnualStaff(): array
    {
        $map = ['page_4' => []];

        $broadbandY = 135;
        $broadbandGap = 8.3;

        for ($i = 1; $i <= 5; $i++) {
            $y = $broadbandY + (($i - 1) * $broadbandGap);

            $map['page_4']["broadband_infrastructure.$i.type"] = [
                'x' => 30, 'y' => $y, 'w' => 42, 'font_size' => 8,
            ];
            $map['page_4']["broadband_infrastructure.$i.ownership"] = [
                'x' => 68, 'y' => $y, 'w' => 55, 'font_size' => 8,
            ];
            $map['page_4']["broadband_infrastructure.$i.capacity_owned"] = [
                'x' => 132, 'y' => $y, 'w' => 25, 'font_size' => 8,
            ];
            $map['page_4']["broadband_infrastructure.$i.capacity_utilized"] = [
                'x' => 170, 'y' => $y, 'w' => 25, 'font_size' => 8,
            ];
        }

        $staffRows = [
            'technical_permanent' => 191,
            'technical_contract' => 196,
            'technical_temporary' => 200,
            'non_technical_permanent' => 205,
            'non_technical_contract' => 209,
            'non_technical_temporary' => 214,
            'staff_total' => 219,
        ];

        foreach ($staffRows as $key => $y) {
            $map['page_4']["staff.$key.local_m"] = ['x' => 70, 'y' => $y, 'w' => 20, 'font_size' => 8];
            $map['page_4']["staff.$key.local_f"] = ['x' => 100, 'y' => $y, 'w' => 20, 'font_size' => 8];
            $map['page_4']["staff.$key.exp_m"]   = ['x' => 128, 'y' => $y, 'w' => 20, 'font_size' => 8];
            $map['page_4']["staff.$key.exp_f"]   = ['x' => 155, 'y' => $y, 'w' => 20, 'font_size' => 8];
        }

        return $map;
    }

    private static function page5Pwd(): array
    {
        return [
            'page_5' => [
                'pwd_aware_yes' => [
                    'x' => 92, 'y' => 56, 'type' => 'checkbox_match',
                    'source' => 'pwd_aware', 'value' => 'yes'
                ],
                'pwd_aware_no' => [
                    'x' => 95, 'y' => 56, 'type' => 'checkbox_match',
                    'source' => 'pwd_aware', 'value' => 'no'
                ],

                'pwd_complied_yes' => [
                    'x' => 97, 'y' => 63, 'type' => 'checkbox_match',
                    'source' => 'pwd_complied', 'value' => 'yes'
                ],
                'pwd_complied_no' => [
                    'x' => 95, 'y' => 63, 'type' => 'checkbox_match',
                    'source' => 'pwd_complied', 'value' => 'no'
                ],

               'pwd_actions' => [
    'x' => 40, 'y' => 93, 'w' => 170, 'h' => 6.2,
    'type' => 'multiline', 'font_size' => 10,
    'max_lines' => 6,
],

'pwd_challenges' => [
    'x' => 40, 'y' => 153, 'w' => 170, 'h' => 6.2,
    'type' => 'multiline', 'font_size' => 10,
    'max_lines' => 6,
],

'pwd_future_plans' => [
    'x' => 40, 'y' => 206, 'w' => 170, 'h' => 6.2,
    'type' => 'multiline', 'font_size' => 10,
    'max_lines' => 6,
],
            ],
        ];
    }

    private static function page6EnvironmentComments(): array
    {
        return [
            'page_6' => [
                'ewaste_initiatives' => [
    'x' => 40, 'y' => 62, 'w' => 170, 'h' => 6.2,
    'type' => 'multiline', 'font_size' => 10,
    'max_lines' => 7,
],

'carbon_initiatives' => [
    'x' => 40, 'y' => 134, 'w' => 170, 'h' => 6.2,
    'type' => 'multiline', 'font_size' => 10,
    'max_lines' => 6,
],

'emca_status' => [
    'x' => 40, 'y' => 192, 'w' => 170, 'h' => 6.2,
    'type' => 'multiline', 'font_size' => 10,
    'max_lines' => 5,
],
            ],
        ];
    }

    private static function page7Submitter(): array
    {
        return [
            'page_7' => [
    'comments' => [
        'x' => 25, 'y' => 48, 'w' => 175, 'h' => 3.5,
        'type' => 'multiline', 'font_size' => 10,
        'max_lines' => 4,
    ],

    'submitter_name'  => ['x' => 42, 'y' => 110, 'w' => 120, 'font_size' => 10],
    'submitter_title' => ['x' => 42, 'y' => 119, 'w' => 120, 'font_size' => 10],
    'submitter_date'  => ['x' => 42, 'y' => 128, 'w' => 120, 'font_size' => 10],

    'signature_image' => [
        'x' => 50, 'y' => 135, 'w' => 50, 'h' => 10, 'type' => 'image',
    ],

    'company_stamp_image' => [
        'x' => 148, 'y' => 115, 'w' => 40, 'h' => 35, 'type' => 'image',
    ],
],
        ];
    }
}
