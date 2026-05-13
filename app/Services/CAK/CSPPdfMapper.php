<?php

namespace App\Services\CAK;

class CSPPdfMapper
{
    public static function map(): array
    {
        return array_merge(
            self::page1(),
            self::page2(),
            self::page3Services(),
            self::page4MoneyAndNumbering(),
            self::page5ComplaintsAndFinancials(),
            self::page6Pwd(),
            self::page7EnvironmentAndComments(),
            self::page8Submitter()
        );
    }

    private static function page1(): array
    {
        return [
            'page_1' => [
                'licensee_name'   => ['x' => 58, 'y' => 179, 'w' => 130,'font_size' => 10],
                'license_no'      => ['x' => 58, 'y' => 186, 'w' => 130,'font_size' => 10],
                'other_licenses'  => ['x' => 58, 'y' => 193, 'w' => 130,'font_size' => 10],
                'financial_year'  => ['x' => 62, 'y' => 234, 'w' => 70,'font_size' => 10],

                'quarter_q1'      => ['x' => 35,  'y' => 260, 'type' => 'checkbox','font_size' => 10],
                'quarter_q2'      => ['x' => 78,  'y' => 260, 'type' => 'checkbox','font_size' => 10],
                'quarter_q3'      => ['x' => 121, 'y' => 260, 'type' => 'checkbox','font_size' => 10],
                'quarter_q4'      => ['x' => 164, 'y' => 260, 'type' => 'checkbox','font_size' => 10],
            ],
        ];
    }

    private static function page2(): array
    {
        return [
            'page_2' => [
                'county'           => ['x' => 54,  'y' => 39,  'w' => 42,'font_size' => 10],
                'town'             => ['x' => 98,  'y' => 39,  'w' => 40,'font_size' => 10],
                'street_road'      => ['x' => 153, 'y' => 39,  'w' => 45,'font_size' => 10],

                'building_name'    => ['x' => 54,  'y' => 47,  'w' => 45,'font_size' => 10],
                'floor_no'         => ['x' => 104, 'y' => 47,  'w' => 25,'font_size' => 10],
                'room_no'          => ['x' => 154, 'y' => 47,  'w' => 35,'font_size' => 10],

                'p_o_box'          => ['x' => 54,  'y' => 72,  'w' => 45,'font_size' => 10],
                'postal_town'      => ['x' => 104,  'y' => 72,  'w' => 45,'font_size' => 10],
                'postal_code'      => ['x' => 154, 'y' => 72,  'w' => 35,'font_size' => 10],

                'tel_no'           => ['x' => 54,  'y' => 92,  'w' => 55,'font_size' => 10],
                'mobile_no'        => ['x' => 126, 'y' => 92,  'w' => 55,'font_size' => 10],
                'other_tel'        => ['x' => 54,  'y' => 100, 'w' => 130,'font_size' => 10],

                'email'            => ['x' => 54,  'y' => 128, 'w' => 130,'font_size' => 10],
                'web_address'      => ['x' => 54,  'y' => 135, 'w' => 130,'font_size' => 10],

                'ceo_name'         => ['x' => 86,  'y' => 167, 'w' => 100,'font_size' => 10],
                'contact_person'   => ['x' => 70,  'y' => 178, 'w' => 100,'font_size' => 10],
                'contact_landline' => ['x' => 70,  'y' => 186, 'w' => 55,'font_size' => 10],
                'contact_mobile'   => ['x' => 140, 'y' => 185, 'w' => 55,'font_size' => 10],
                'contact_email'    => ['x' => 40,  'y' => 195, 'w' => 130,'font_size' => 10],

                'address_changed_yes' => ['x' => 158, 'y' => 252, 'type' => 'checkbox'],
                'address_changed_no'  => ['x' => 174, 'y' => 220, 'type' => 'checkbox'],
            ],
        ];
    }

   private static function page3Services(): array
{
    $map = ['page_3' => []];

    $startY = 88;
$gap = 8.5;

    for ($i = 1; $i <= 10; $i++) {
        $y = $startY + (($i - 1) * $gap);

        $map['page_3']["services.$i.short_code"]       = ['x' => 35,  'y' => $y, 'w' => 18,'font_size' => 8];
$map['page_3']["services.$i.service_provided"] = ['x' => 60,  'y' => $y, 'w' => 22,'font_size' => 8];
$map['page_3']["services.$i.company_name"]     = ['x' => 85,  'y' => $y, 'w' => 24,'font_size' => 8];
//  $map['page_3']["services.$i.authorization"]    = ['x' => 105, 'y' => $y - 1, 'w' => 25, 'h' => 3, 'type' => 'multiline', 'font_size' => 6];

$map['page_3']["services.$i.authorization"]    = [
    'x' => 110,
    'y' => $y - 1,
    'w' => 25,
    'h' => 3,
    'type' => 'multiline',
    'font_size' => 8
];


       $map['page_3']["services.$i.charges"] = ['x' => 140, 'y' => $y, 'w' => 15,'font_size' => 8];
$map['page_3']["services.$i.m1"]      = ['x' => 170, 'y' => $y,'font_size' => 8];
$map['page_3']["services.$i.m2"]      = ['x' => 200, 'y' => $y,'font_size' => 8];
$map['page_3']["services.$i.m3"]      = ['x' => 230, 'y' => $y,'font_size' => 8];
$map['page_3']["services.$i.total"]   = ['x' => 260, 'y' => $y,'font_size' => 8];
    }

    return $map;
}

    private static function page4MoneyAndNumbering(): array
    {
        $map = ['page_4' => []];

        $moneyRows = [
            'active_agents',
            'registered_active_subscriptions',
            'c2b_value',
            'b2c_value',
            'b2b_value',
            'g2c_value',
            'c2g_value',
            'volumes_sent_other_networks',
            'volumes_received_other_networks',
            'value_sent_other_networks',
            'value_received_other_networks',
            'p2p_volumes',
            'p2p_received_other_networks',
            'p2p_value_sent_other_networks',
            'p2p_value_received_other_networks',
        ];

          $startY = 46;
$gap = 8.8;

        foreach ($moneyRows as $index => $key) {
            $y = $startY + ($index * $gap);

$map['page_4']["money_transfer.$key.m1"] = ['x' => 90,  'y' => $y,'font_size' => 8];
$map['page_4']["money_transfer.$key.m2"] = ['x' => 130, 'y' => $y,'font_size' => 8];
$map['page_4']["money_transfer.$key.m3"] = ['x' => 170, 'y' => $y,'font_size' => 8];
        }

        $numberingStartY = 222;
$numberingGap = 6.4;

        for ($i = 1; $i <= 7; $i++) {
            $y = $numberingStartY + (($i - 1) * $numberingGap);

           $map['page_4']["numbering_resources.$i.resource"]       = ['x' => 40,  'y' => $y, 'w' => 35,'font_size' => 8];
$map['page_4']["numbering_resources.$i.total_assigned"] = ['x' => 70,  'y' => $y,'font_size' => 8];
$map['page_4']["numbering_resources.$i.in_use"]         = ['x' => 100,  'y' => $y,'font_size' => 8];
$map['page_4']["numbering_resources.$i.not_in_use"]     = ['x' => 140, 'y' => $y,'font_size' => 8];
$map['page_4']["numbering_resources.$i.reason"] = [
    'x' => 170,
    'y' => $y,
    'w' => 35,
    'h' => 3,
    'type' => 'multiline',
    'font_size' => 8
];
        }

        return $map;
    }

    private static function page5ComplaintsAndFinancials(): array
    {
        $map = ['page_5' => []];

        $complaints = [
            'billing_charges',
            'spamming',
            'unsolicited_content',
            'customer_care',
            'network_failures',
            'delays_onboarding',
            'others',
        ];

  $startY = 54;
$gap = 8.2;

        foreach ($complaints as $index => $key) {
            $y = $startY + ($index * $gap);

$map['page_5']["complaints.$key.m1_received"] = ['x' => 75,  'y' => $y,'font_size' => 8];
$map['page_5']["complaints.$key.m1_resolved"] = ['x' => 100, 'y' => $y,'font_size' => 8];

$map['page_5']["complaints.$key.m2_received"] = ['x' => 120, 'y' => $y,'font_size' => 8];
$map['page_5']["complaints.$key.m2_resolved"] = ['x' => 145, 'y' => $y,'font_size' => 8];

$map['page_5']["complaints.$key.m3_received"] = ['x' => 165, 'y' => $y,'font_size' => 8];
$map['page_5']["complaints.$key.m3_resolved"] = ['x' => 186, 'y' => $y,'font_size' => 8];
        }

        $map['page_5']['financial_year_start_date'] = ['x' => 84,  'y' => 185, 'w' => 45, 'font_size' => 10];
$map['page_5']['financial_year_end_date']   = ['x' => 140, 'y' => 185, 'w' => 45, 'font_size' => 10];

        return $map;
    }

    private static function page6Pwd(): array
    {
        return [
            'page_6' => [
                'pwd_aware_yes'    => ['x' => 88,  'y' => 58, 'type' => 'checkbox'],
                'pwd_aware_no'     => ['x' => 95,  'y' => 58, 'type' => 'checkbox'],

                'pwd_complied_yes' => ['x' => 94,  'y' => 65, 'type' => 'checkbox'],
                'pwd_complied_no'  => ['x' => 96,  'y' => 65, 'type' => 'checkbox'],

                // 'pwd_reasons'      => ['x' => 35, 'y' => 42,  'w' => 170, 'h' => 6, 'type' => 'multiline', 'font_size' => 10],
                'pwd_actions'      => ['x' => 35, 'y' => 80,  'w' => 170, 'h' => 6, 'type' => 'multiline', 'font_size' => 10],
                'pwd_challenges'   => ['x' => 35, 'y' => 112, 'w' => 170, 'h' => 6, 'type' => 'multiline', 'font_size' => 10],
                'pwd_future_plans' => ['x' => 35, 'y' => 140, 'w' => 170, 'h' => 6, 'type' => 'multiline', 'font_size' => 10],
            'ewaste_initiatives' => ['x' => 30, 'y' => 195,  'w' => 170, 'h' => 4.65, 'type' => 'multiline', 'font_size' => 10],
            ],
        ];
    }

    private static function page7EnvironmentAndComments(): array
    {
        return [
            'page_7' => [
                // 'ewaste_initiatives' => ['x' => 35, 'y' => 50,  'w' => 170, 'h' => 6, 'type' => 'multiline', 'font_size' => 10],
                'carbon_initiatives' => ['x' => 30, 'y' => 36, 'w' => 170, 'h' => 3.05, 'type' => 'multiline', 'font_size' => 10],
                'emca_status'        => ['x' => 35, 'y' => 108, 'w' => 170, 'h' => 6, 'type' => 'multiline', 'font_size' => 10],
                'comments'           => ['x' => 35, 'y' => 200, 'w' => 170, 'h' => 6, 'type' => 'multiline', 'font_size' => 10],
            ],
        ];
    }

    private static function page8Submitter(): array
    {
        return [
           'page_8' => [

    'submitter_name' => [
        'x' => 45,
        'y' => 50,
        'w' => 120,
        'font_size' => 10
    ],

    'submitter_title' => [
        'x' => 45,
        'y' => 58,
        'w' => 120,
        'font_size' => 10
    ],

    'submitter_date' => [
        'x' => 45,
        'y' => 66,
        'w' => 120,
        'font_size' => 10
    ],

    'signature_image' => [
        'x' => 40,
        'y' => 30,
        'w' => 55,
        'h' => 15,
        'type' => 'image'
    ],

    'company_stamp_image' => [
        'x' => 140,
        'y' => 35,
        'w' => 45,
        'h' => 40,
        'type' => 'image'
    ],
],
        ];
    }
}
