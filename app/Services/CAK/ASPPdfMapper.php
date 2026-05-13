<?php

namespace App\Services\CAK;

class ASPPdfMapper
{
    public static function map(): array
    {
        return array_merge(
            self::page1(),
            self::page2(),
            self::page3Services(),
            self::page4Broadband(),
            self::page5VoiceSms(),
            self::page6International(),
            self::page7QosComplaints(),
            self::page8CountyPart1(),
            self::page9CountyStaffNumbering(),
            self::page10Cyber(),
            self::page11Pwd(),
            self::page12Environment(),
            self::page13Submitter()
        );
    }

    private static function page1(): array
    {
        return [
            'page_1' => [
                'licensee_name'  => ['x' => 60, 'y' => 151, 'w' => 130, 'font_size' => 10],
                'license_no'     => ['x' => 60, 'y' => 158, 'w' => 130, 'font_size' => 10],
                'other_licenses' => ['x' => 60, 'y' => 165, 'w' => 130, 'font_size' => 10],
                'financial_year' => ['x' => 65, 'y' => 183, 'w' => 80, 'font_size' => 10],

                'quarter_q1' => ['x' => 40,  'y' => 208, 'type' => 'checkbox'],
                'quarter_q2' => ['x' => 82,  'y' => 208, 'type' => 'checkbox'],
                'quarter_q3' => ['x' => 125, 'y' => 208, 'type' => 'checkbox'],
                'quarter_q4' => ['x' => 168, 'y' => 208, 'type' => 'checkbox'],

                'county'        => ['x' => 60,  'y' => 226, 'w' => 45, 'font_size' => 10],
                'town'          => ['x' => 102,  'y' => 226, 'w' => 45, 'font_size' => 10],
                'street_road'   => ['x' => 155, 'y' => 226, 'w' => 45, 'font_size' => 10],

                'building_name' => ['x' => 60,  'y' => 234, 'w' => 45, 'font_size' => 10],
                'floor_no'      => ['x' => 103, 'y' => 234, 'w' => 30, 'font_size' => 10],
                'room_no'       => ['x' => 155, 'y' => 234, 'w' => 35, 'font_size' => 10],

                'p_o_box'       => ['x' => 57,  'y' => 253, 'w' => 45, 'font_size' => 10],
                'postal_town'   => ['x' => 98,  'y' => 253, 'w' => 45, 'font_size' => 10],
                'postal_code'   => ['x' => 153, 'y' => 253, 'w' => 35, 'font_size' => 10],
            ],
        ];
    }

    private static function page2(): array
    {
        return [
            'page_2' => [
                'tel_no'        => ['x' => 37,  'y' => 37,  'w' => 55, 'font_size' => 10],
                'mobile_no'     => ['x' => 125, 'y' => 37,  'w' => 55, 'font_size' => 10],
                'other_tel'     => ['x' => 50,  'y' => 45,  'w' => 130, 'font_size' => 10],

                'email'         => ['x' => 48,  'y' => 61,  'w' => 130, 'font_size' => 10],
                'web_address'   => ['x' => 48,  'y' => 69, 'w' => 130, 'font_size' => 10],

                'ceo_name'         => ['x' => 87,  'y' => 91, 'w' => 100, 'font_size' => 10],
                'contact_person'   => ['x' => 70,  'y' => 98, 'w' => 100, 'font_size' => 10],
                'contact_landline' => ['x' => 68,  'y' => 105, 'w' => 55, 'font_size' => 10],
                'contact_mobile'   => ['x' => 140, 'y' => 105, 'w' => 55, 'font_size' => 10],
                'contact_email'    => ['x' => 40,  'y' => 112, 'w' => 130, 'font_size' => 10],

                'address_changed_yes' => [
                    'x' => 161, 'y' => 129, 'type' => 'checkbox_match',
                    'source' => 'address_changed', 'value' => 'yes', 'font_size' => 10,
                ],
                'address_changed_no' => [
                    'x' => 173, 'y' => 129, 'type' => 'checkbox_match',
                    'source' => 'address_changed', 'value' => 'no', 'font_size' => 10,
                ],
            ],
        ];
    }

 private static function page3Services(): array
{
    $map = ['page_3' => []];

    $m2mY = 58;
    $m2mGap = 4.0;

    for ($i = 1; $i <= 10; $i++) {
        $y = $m2mY + (($i - 1) * $m2mGap);

        $map['page_3']["m2m_services.$i.service"] = [
            'x' => 45, 'y' => $y, 'w' => 45, 'font_size' => 8, 'max_chars' => 35,
        ];

        $map['page_3']["m2m_services.$i.description"] = [
            'x' => 90, 'y' => $y, 'w' => 75, 'h' => 2.4,
            'type' => 'multiline', 'font_size' => 8, 'max_lines' => 2, 'max_chars' => 90,
        ];

        $map['page_3']["m2m_services.$i.subscriptions"] = [
            'x' => 160, 'y' => $y, 'w' => 25, 'font_size' => 8,
        ];
    }

    $telecomStartY = 121;
    $telecomGap = 4.5;

    $telecomRows = [
        'postpaid_gsm',
        'postpaid_fixed_line',
        'postpaid_fixed_wireless',
        'prepaid_gsm',
        'prepaid_fixed_line',
        'prepaid_fixed_wireless',
        'voip_mobile',
        'voip_fixed',
        'leased_mobile',
        'leased_fixed',
    ];

    foreach ($telecomRows as $index => $key) {
        $y = $telecomStartY + ($index * $telecomGap);

        $map['page_3']["subscriptions.$key.m1"] = ['x' => 110, 'y' => $y, 'w' => 25, 'font_size' => 8];
        $map['page_3']["subscriptions.$key.m2"] = ['x' => 140, 'y' => $y, 'w' => 25, 'font_size' => 8];
        $map['page_3']["subscriptions.$key.m3"] = ['x' => 171, 'y' => $y, 'w' => 25, 'font_size' => 8];
    }

    $deviceStartY = 181;
$deviceGap = 4.5;

$deviceRows = [
    'feature_phone',
    'smart_phone',
    'others',
];

foreach ($deviceRows as $index => $key) {
    $y = $deviceStartY + ($index * $deviceGap);

    $map['page_3']["mobile_devices.$key"] = [
        'x' => 105,
        'y' => $y,
        'w' => 50,
        'font_size' => 8,
    ];
}

$dataStartY = 213;
$dataGap = 4.4;

$dataRows = [
    'data_enabled_sim',
    'ftth',
    'ftto',
    'fixed_wireless',
    'satellite',
    'copper',
    'cable_modem',
    'other_fixed',
];

foreach ($dataRows as $index => $key) {
    $y = $dataStartY + ($index * $dataGap);

    $map['page_3']["data_subscriptions.$key.m1"] = [
        'x' => 95,
        'y' => $y,
        'w' => 25,
        'font_size' => 8,
    ];

    $map['page_3']["data_subscriptions.$key.m2"] = [
        'x' => 130,
        'y' => $y,
        'w' => 25,
        'font_size' => 8,
    ];

    $map['page_3']["data_subscriptions.$key.m3"] = [
        'x' => 165,
        'y' => $y,
        'w' => 25,
        'font_size' => 8,
    ];
}


    return $map;
}


    private static function page4Broadband(): array
{
    $map = ['page_4' => []];

    $broadbandStartY = 46;
    $broadbandGap = 5;

    $broadbandRows = [
        '3g',
        '4g',
        '5g',
        'ftth',
        'ftto',
        'fixed_wireless',
        'satellite',
        'copper',
        'cable_modem',
        'other_fixed',
    ];

    foreach ($broadbandRows as $index => $key) {
        $y = $broadbandStartY + ($index * $broadbandGap);

        $map['page_4']["broadband.$key.m1"] = ['x' => 79, 'y' => $y, 'w' => 20, 'font_size' => 8];
        $map['page_4']["broadband.$key.m2"] = ['x' => 105, 'y' => $y, 'w' => 20, 'font_size' => 8];
        $map['page_4']["broadband.$key.m3"] = ['x' => 132, 'y' => $y, 'w' => 20, 'font_size' => 8];
        $map['page_4']["broadband.$key.volume"] = ['x' => 165, 'y' => $y, 'w' => 25, 'font_size' => 8];
    }

    $speedStartY = 122;
    $speedGap = 7.5;

    $speedRows = [
        'ftth',
        'ftto',
        'fixed_wireless',
        'satellite',
        'copper',
        'cable_modem',
        'other_fixed',
        'total',
    ];

    $cols = [
        'lt_256' => 55,
        'kbps_256_2mbps' => 76,
        'mbps_2_10' => 98,
        'mbps_10_30' => 120,
        'mbps_30_100' => 138,
        'mbps_100_1gbps' => 154,
        'gt_1gbps' => 175,
    ];

    foreach ($speedRows as $index => $row) {
        $y = $speedStartY + ($index * $speedGap);

        foreach ($cols as $col => $x) {
            $map['page_4']["speed_subscriptions.$row.$col"] = [
                'x' => $x,
                'y' => $y,
                'w' => 18,
                'font_size' => 8,
            ];
        }
    }

    $mnpStartY = 196;
    $mnpGap = 6.0;

    for ($i = 1; $i <= 3; $i++) {
        $y = $mnpStartY + (($i - 1) * $mnpGap);

        $map['page_4']["mnp.$i.operator"] = [
            'x' => 28,
            'y' => $y,
            'w' => 70,
            'font_size' => 8,
        ];

        $map['page_4']["mnp.$i.in_ports"] = [
            'x' => 105,
            'y' => $y,
            'w' => 60,
            'font_size' => 8,
        ];
    }

    return $map;
}


 private static function page5VoiceSms(): array
{
    $map = ['page_5' => []];

    /*
    |--------------------------------------------------------------------------
    | 4.1 LOCAL VOICE TRAFFIC - INTRA NETWORK
    |--------------------------------------------------------------------------
    */

    $intraVoiceRows = [
        'intra_mobile' => 56,
        'intra_fixed_wireless' => 62,
        'intra_fixed_line' => 68,
    ];

    $intraVoiceCols = [
        'voice_minutes' => 68,
        'voice_calls' => 95,
        'voip_minutes' => 147,
        'voip_calls' => 179,
    ];

    foreach ($intraVoiceRows as $key => $y) {
        foreach ($intraVoiceCols as $field => $x) {
            $map['page_5']["voice_traffic.$key.$field"] = [
                'x' => $x,
                'y' => $y,
                'w' => 16,
                'font_size' => 8,
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 4.1 LOCAL VOICE TRAFFIC - OTHER NETWORKS
    |--------------------------------------------------------------------------
    */

    $otherVoiceRows = [
        'other_1_mobile' => 86,
        'other_1_fixed_line' => 92,
        'other_1_fixed_wireless' => 99,

        'other_2_mobile' => 106,
        'other_2_fixed_line' => 111,
        'other_2_fixed_wireless' => 119,

        'other_3_mobile' => 127,
        'other_3_fixed_line' => 134,
        'other_3_fixed_wireless' => 142,

        'other_4_mobile' => 149,
        'other_4_fixed_line' => 155,
        'other_4_fixed_wireless' => 162,
    ];

    $otherVoiceCols = [
        'voice_in_minutes' => 67,
        'voice_in_calls' => 84,
        'voice_out_minutes' => 100,
        'voice_out_calls' => 118,
        'voip_in_minutes' => 136,
        'voip_in_calls' => 154,
        'voip_out_minutes' => 172,
        'voip_out_calls' => 188,
    ];

    foreach ($otherVoiceRows as $key => $y) {
        foreach ($otherVoiceCols as $field => $x) {
            $map['page_5']["voice_traffic.$key.$field"] = [
                'x' => $x,
                'y' => $y,
                'w' => 16,
                'font_size' => 8,
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 4.2 LOCAL SMS TRAFFIC
    |--------------------------------------------------------------------------
    */

    //intra-network
$smsRows = [
    'intra_mobile' => 191,
    'intra_fixed_wireless' => 195.2,
];

foreach ($smsRows as $key => $y) {
    $map['page_5']["sms_traffic.$key.sms"] = [
        'x' => 118,
        'y' => $y,
        'w' => 30,
        'font_size' => 8,
    ];
}


    //other-networks
  $smsRows = [
      'other_1_mobile' => 203,
    'other_1_fixed_wireless' => 207,

    'other_2_mobile' => 212,
    'other_2_fixed_wireless' => 216,

    'other_3_mobile' => 220,
    'other_3_fixed_wireless' => 224,

    'other_4_mobile' => 228,
    'other_4_fixed_wireless' => 233,

    'other_5_mobile' => 238,
    'other_5_fixed_wireless' => 243,
];

foreach ($smsRows as $key => $y) {
    $map['page_5']["sms_traffic.$key.incoming"] = [
        'x' => 118,
        'y' => $y,
        'w' => 30,
        'font_size' => 8,
    ];

    $map['page_5']["sms_traffic.$key.outgoing"] = [
        'x' => 160,
        'y' => $y,
        'w' => 30,
        'font_size' => 8,
    ];
}
    return $map;
}



    private static function page6International(): array
{
    $map = ['page_6' => []];

    /*
    |--------------------------------------------------------------------------
    | 4.3 INTERNATIONAL TRAFFIC
    |--------------------------------------------------------------------------
    */

    $countries = [
        'uganda' => 47,
        'tanzania' => 51,
        'rwanda' => 55,
        'burundi' => 59,
        'south_sudan' => 63,
        'drc' => 69,
        'others' => 75,
        'total' => 80,
    ];

    foreach ($countries as $key => $y) {
        $map['page_6']["international_traffic.$key.voice_in_mobile"] = [
            'x' => 65, 'y' => $y, 'w' => 13, 'font_size' => 8,
        ];

        $map['page_6']["international_traffic.$key.voice_in_fixed"] = [
            'x' => 75, 'y' => $y, 'w' => 13, 'font_size' => 8,
        ];

        $map['page_6']["international_traffic.$key.voice_out_mobile"] = [
            'x' => 90, 'y' => $y, 'w' => 13, 'font_size' => 8,
        ];

        $map['page_6']["international_traffic.$key.voice_out_fixed"] = [
            'x' => 100, 'y' => $y, 'w' => 13, 'font_size' => 8,
        ];

        $map['page_6']["international_traffic.$key.voip_in_mobile"] = [
            'x' => 110, 'y' => $y, 'w' => 13, 'font_size' => 8,
        ];

        $map['page_6']["international_traffic.$key.voip_in_fixed"] = [
            'x' => 120, 'y' => $y, 'w' => 13, 'font_size' => 8,
        ];

        $map['page_6']["international_traffic.$key.voip_out_mobile"] = [
            'x' => 132, 'y' => $y, 'w' => 13, 'font_size' => 8,
        ];

        $map['page_6']["international_traffic.$key.voip_out_fixed"] = [
            'x' => 145, 'y' => $y, 'w' => 13, 'font_size' => 8,
        ];

        $map['page_6']["international_traffic.$key.sms_in"] = [
            'x' => 165, 'y' => $y, 'w' => 13, 'font_size' => 8,
        ];

        $map['page_6']["international_traffic.$key.sms_out"] = [
            'x' => 185, 'y' => $y, 'w' => 13, 'font_size' => 8,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | 4.4.1 OUT-BOUND MOBILE ROAMING TRAFFIC
    |--------------------------------------------------------------------------
    */

    $outboundRows = [
        'uganda' => 119,
        'tanzania' => 124,
        'rwanda' => 128,
        'burundi' => 132,
        'south_sudan' => 138,
        'drc' => 144,
        'others' => 150,
        'total' => 154,
    ];

    foreach ($outboundRows as $key => $y) {
        $map['page_6']["roaming_outbound.$key.voice_in"] = [
            'x' => 63, 'y' => $y, 'w' => 22, 'font_size' => 8,
        ];

        $map['page_6']["roaming_outbound.$key.voice_out"] = [
            'x' => 91, 'y' => $y, 'w' => 22, 'font_size' => 8,
        ];

        $map['page_6']["roaming_outbound.$key.sms_in"] = [
            'x' => 118, 'y' => $y, 'w' => 22, 'font_size' => 8,
        ];

        $map['page_6']["roaming_outbound.$key.sms_out"] = [
            'x' => 146, 'y' => $y, 'w' => 22, 'font_size' => 8,
        ];

        $map['page_6']["roaming_outbound.$key.data"] = [
            'x' => 178, 'y' => $y, 'w' => 22, 'font_size' => 8,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | 4.4.2 IN-BOUND MOBILE ROAMING TRAFFIC
    |--------------------------------------------------------------------------
    */

    $inboundRows = [
        'uganda' => 184,
        'tanzania' => 187,
        'rwanda' => 192,
        'burundi' => 196,
        'south_sudan' => 200,
        'drc' => 208,
        'others' => 214,
        'total' => 218,
    ];

    foreach ($inboundRows as $key => $y) {
        $map['page_6']["roaming_inbound.$key.voice_in"] = [
            'x' => 63, 'y' => $y, 'w' => 22, 'font_size' => 8,
        ];

        $map['page_6']["roaming_inbound.$key.voice_out"] = [
            'x' => 91, 'y' => $y, 'w' => 22, 'font_size' => 8,
        ];

        $map['page_6']["roaming_inbound.$key.sms_in"] = [
            'x' => 118, 'y' => $y, 'w' => 22, 'font_size' => 8,
        ];

        $map['page_6']["roaming_inbound.$key.sms_out"] = [
            'x' => 146, 'y' => $y, 'w' => 22, 'font_size' => 8,
        ];

        $map['page_6']["roaming_inbound.$key.data"] = [
            'x' => 178, 'y' => $y, 'w' => 22, 'font_size' => 8,
        ];
    }

    return $map;
}

   private static function page7QosComplaints(): array
{
    $map = ['page_7' => []];

    $qosRows = [
        'unsuccessful_call_ratio' => 39,
        'dropped_call_ratio' => 44,
        'call_setup_time' => 47,
        'voice_quality' => 50,
        'handover_success' => 54,
        'sms_successful_ratio' => 65,
        'sms_completion_rate' => 69,
        'sms_delivery_ratio' => 73,
        'jitter_latency' => 82,
        'throughput' => 86,
        'browsing' => 90,
    ];

    foreach ($qosRows as $key => $y) {
        $map['page_7']["qos.$key"] = [
            'x' => 165,
            'y' => $y,
            'w' => 30,
            'font_size' => 8,
        ];
    }

    $complaints = [
        'network_faults' => 130,
        'poor_service' => 138,
        'disconnections' => 146,
        'billing' => 156,
        'customer_care' => 164,
        'spam_malware' => 172,
        'online_scam' => 176,
        'childline' => 183,
        'others' => 188,
        'total' => 198,
    ];

    $complaintCols = [
        'm1_received' => 70,
        'm1_resolved' => 88,
        'm2_received' => 112,
        'm2_resolved' => 135,
        'm3_received' => 154,
        'm3_resolved' => 172,
    ];

    foreach ($complaints as $key => $y) {
        foreach ($complaintCols as $field => $x) {
            $map['page_7']["complaints.$key.$field"] = [
                'x' => $x,
                'y' => $y,
                'w' => 18,
                'font_size' => 8,
            ];
        }
    }

    return $map;
}


  private static function page8CountyPart1(): array
{
    $map = ['page_8' => []];

    $countyRows = [
        'mombasa' => 104,
        'kwale' => 109,
        'kilifi' => 114,
        'tana_river' => 119,
        'lamu' => 124,
        'taita_taveta' => 128,
        'garissa' => 133,
        'wajir' => 138,
        'mandera' => 143,
        'marsabit' => 147,
        'isiolo' => 152,
        'meru' => 157,
        'tharaka_nithi' => 162,
        'embu' => 167,
        'kitui' => 172,
        'machakos' => 177,
        'makueni' => 182,
        'nyandarua' => 187,
        'nyeri' => 192,
        'kirinyaga' => 197,
        'muranga' => 202,
        'kiambu' => 207,
        'turkana' => 212,
        'west_pokot' => 215,
        'samburu' => 222,
        'trans_nzoia' => 225,
        'uasin_gishu' => 230,
        'elgeyo_marakwet' => 235,
        'nandi' => 240,
        'baringo' => 245,
        'laikipia' => 250,
        'nakuru' => 255,
        'narok' => 260,
        'kajiado' => 265,
    ];

    $countyCols = [
        'terrestrial_fixed_wireless' => 58,
        'terrestrial_fixed_line' => 76,
        'ftth' => 100,
        'ftto' => 116,
        'fixed_wireless' => 130,
        'satellite' => 144,
        'copper' => 155,
        'cable_modem' => 169,
        'other_fixed' => 183,
    ];

    foreach ($countyRows as $county => $y) {
        foreach ($countyCols as $col => $x) {
            $map['page_8']["county_subscriptions.$county.$col"] = [
                'x' => $x,
                'y' => $y,
                'w' => 14,
                'font_size' => 8,
            ];
        }
    }

    return $map;
}


   private static function page9CountyStaffNumbering(): array
{
    $map = ['page_9' => []];

    $countyRows = [
    'kericho' => 52,
    'bomet' => 57,
    'kakamega' => 62,
    'vihiga' => 67,
    'bungoma' => 72,
    'busia' => 77,
    'siaya' => 80,
    'kisumu' => 85,
    'homa_bay' => 90,
    'migori' => 95,
    'kisii' => 100,
    'nyamira' => 105,
    'nairobi_city' => 110,
];

$countyCols = [
    'terrestrial_fixed_wireless' => 58,
    'terrestrial_fixed_line' => 80,
    'ftth' => 100,
    'ftto' => 116,
    'fixed_wireless' => 132,
    'satellite' => 144,
    'copper' => 155,
    'cable_modem' => 168,
    'other_fixed' => 182,
];

foreach ($countyRows as $county => $y) {
    foreach ($countyCols as $col => $x) {
        $map['page_9']["county_subscriptions.$county.$col"] = [
            'x' => $x,
            'y' => $y,
            'w' => 14,
            'font_size' => 8,
        ];
    }
}

$staffRows = [
    'tech_perm' => 136,
    'tech_cont' => 140,
    'tech_temp' => 144,
    'nontech_perm' => 148,
    'nontech_cont' => 152,
    'nontech_temp' => 158,
];

$staffCols = [
    'local_m' => 82,
    'local_f' => 105,
    'exp_m' => 128,
    'exp_f' => 160,
];

foreach ($staffRows as $key => $y) {
    foreach ($staffCols as $field => $x) {
        $map['page_9']["staff.$key.$field"] = [
            'x' => $x,
            'y' => $y,
            'w' => 20,
            'font_size' => 8,
        ];
    }
}

$staffTotalY = 162;

foreach ($staffCols as $field => $x) {
    $map['page_9']["staff_total.$field"] = [
        'x' => $x,
        'y' => $staffTotalY,
        'w' => 20,
        'font_size' => 10,
    ];
}


    //add for table 10.1 and table 10.2  for page 9
        /*
    |--------------------------------------------------------------------------
    | 10.1 Numbers for Fixed Telephony, Mobile Telephony, Free Phone and Other Services
    |--------------------------------------------------------------------------
    */

    $fixedNumberingRows = [
        1 => 199,
        2 => 205,
        // 3 => 238,
    ];

    foreach ($fixedNumberingRows as $i => $y) {
        $map['page_9']["fixed_numbering.$i.resource"] = [
            'x' => 40, 'y' => $y, 'w' => 32, 'font_size' => 8,
        ];

        $map['page_9']["fixed_numbering.$i.purpose"] = [
            'x' => 76, 'y' => $y, 'w' => 32, 'font_size' => 8,
        ];

        $map['page_9']["fixed_numbering.$i.total"] = [
            'x' => 96, 'y' => $y, 'w' => 24, 'font_size' => 8,
        ];

        $map['page_9']["fixed_numbering.$i.in_use"] = [
            'x' => 125, 'y' => $y, 'w' => 22, 'font_size' => 8,
        ];

        $map['page_9']["fixed_numbering.$i.not_in_use"] = [
            'x' => 144, 'y' => $y, 'w' => 22, 'font_size' => 8,
        ];

        $map['page_9']["fixed_numbering.$i.reason"] = [
            'x' => 168, 'y' => $y - 1, 'w' => 32, 'h' => 3,
            'type' => 'multiline', 'font_size' => 8, 'max_lines' => 2,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | 10.2 Other Numbering Resources
    |--------------------------------------------------------------------------
    */

    $otherNumberingRows = [
        1 => 230,
        2 => 234,
        // 3 => 272,
    ];

    foreach ($otherNumberingRows as $i => $y) {
        $map['page_9']["other_numbering.$i.resource"] = [
            'x' => 40, 'y' => $y, 'w' => 34, 'font_size' => 8,
        ];

        $map['page_9']["other_numbering.$i.purpose"] = [
            'x' => 76, 'y' => $y, 'w' => 34, 'font_size' => 8,
        ];

        $map['page_9']["other_numbering.$i.total"] = [
            'x' => 96, 'y' => $y, 'w' => 22, 'font_size' => 8,
        ];

        $map['page_9']["other_numbering.$i.in_use"] = [
            'x' => 125, 'y' => $y, 'w' => 20, 'font_size' => 8,
        ];

        $map['page_9']["other_numbering.$i.not_in_use"] = [
            'x' => 144, 'y' => $y, 'w' => 20, 'font_size' => 8,
        ];

        $map['page_9']["other_numbering.$i.reason"] = [
            'x' => 168, 'y' => $y - 1, 'w' => 30, 'h' => 3,
            'type' => 'multiline', 'font_size' => 8, 'max_lines' => 2,
        ];
    }


    return $map;
}


  private static function page10Cyber(): array
{
    return [
        'page_10' => [
            'cyber_team_yes' => [
                'x' => 44, 'y' => 53.4, 'type' => 'checkbox_match',
                'source' => 'cybersecurity.has_team', 'value' => 'yes',
            ],
            'cyber_team_no' => [
                'x' => 152, 'y' => 53.4, 'type' => 'checkbox_match',
                'source' => 'cybersecurity.has_team', 'value' => 'no',
            ],

            'cybersecurity.staff_total' => ['x' => 44, 'y' => 80, 'w' => 30, 'font_size' => 8],
            'cybersecurity.staff_male' => ['x' => 110, 'y' => 80, 'w' => 30, 'font_size' => 8],
            'cybersecurity.staff_female' => ['x' => 158, 'y' => 80, 'w' => 30, 'font_size' => 8],

            'cyber_tools_yes' => [
                'x' => 44, 'y' => 102, 'type' => 'checkbox_match',
                'source' => 'cybersecurity.has_tools', 'value' => 'yes',
            ],
            'cyber_tools_no' => [
                'x' => 102, 'y' => 102, 'type' => 'checkbox_match',
                'source' => 'cybersecurity.has_tools', 'value' => 'no',
            ],

            'cybersecurity.tools_deployed' => [
                'x' => 44, 'y' => 114.5, 'w' => 170, 'h' => 3.5,
                'type' => 'multiline', 'font_size' => 8.5, 'max_lines' => 5,
            ],

            'cyber_incident_yes' => [
                'x' => 44, 'y' => 134, 'type' => 'checkbox_match',
                'source' => 'cybersecurity.had_incident', 'value' => 'yes',
            ],
            'cyber_incident_no' => [
                'x' => 102, 'y' => 134, 'type' => 'checkbox_match',
                'source' => 'cybersecurity.had_incident', 'value' => 'no',
            ],

            'cyber_incident_malware' => [
    'x' => 30,
    'y' => 158,
    'type' => 'checkbox_in_array',
    'source' => 'cybersecurity.incident_types',
    'value' => 'malware',
],
            'cyber_incident_ransomware' => [
                'x' => 72, 'y' => 158, 'type' => 'checkbox_contains',
                'source' => 'cybersecurity.incident_types', 'value' => 'ransomware',
            ],
            'cyber_incident_web_attack' => [
                'x' => 118, 'y' => 158, 'type' => 'checkbox_contains',
                'source' => 'cybersecurity.incident_types', 'value' => 'web_attack',
            ],
            'cyber_incident_impersonation' => [
                'x' => 166, 'y' => 158, 'type' => 'checkbox_contains',
                'source' => 'cybersecurity.incident_types', 'value' => 'impersonation',
            ],

            'cyber_reported_yes' => [
                'x' => 158, 'y' => 178, 'type' => 'checkbox_match',
                'source' => 'cybersecurity.reported', 'value' => 'yes',
            ],
            'cyber_reported_no' => [
                'x' => 101, 'y' => 185, 'type' => 'checkbox_match',
                'source' => 'cybersecurity.reported', 'value' => 'no',
            ],

           'cyber_reported_ca' => [
    'x' => 30,
    'y' => 194,
    'type' => 'checkbox_in_array',
    'source' => 'cybersecurity.reported_to',
    'value' => 'ca',
],

            'cyber_reported_ke_cirt' => [
                'x' => 72, 'y' => 194, 'type' => 'checkbox_contains',
                'source' => 'cybersecurity.reported_to', 'value' => 'ke_cirt',
            ],
            'cyber_reported_sector_cirt' => [
                'x' => 118, 'y' => 194, 'type' => 'checkbox_contains',
                'source' => 'cybersecurity.reported_to', 'value' => 'sector_cirt',
            ],
            'cyber_reported_police' => [
                'x' => 30, 'y' => 204, 'type' => 'checkbox_contains',
                'source' => 'cybersecurity.reported_to', 'value' => 'police',
            ],
            'cyber_reported_others' => [
                'x' => 72, 'y' => 204, 'type' => 'checkbox_contains',
                'source' => 'cybersecurity.reported_to', 'value' => 'others',
            ],

            'cyber_awareness_yes' => [
                'x' => 44, 'y' => 242, 'type' => 'checkbox_match',
                'source' => 'cybersecurity.has_awareness', 'value' => 'yes',
            ],
            'cyber_awareness_no' => [
                'x' => 182, 'y' => 242, 'type' => 'checkbox_match',
                'source' => 'cybersecurity.has_awareness', 'value' => 'no',
            ],

            'cybersecurity.awareness_activities' => [
                'x' => 38, 'y' => 260, 'w' => 170, 'h' => 3.5,
                'type' => 'multiline', 'font_size' => 10, 'max_lines' => 5,
            ],
        ],
    ];
}

    private static function page11Pwd(): array
{
    return [
        'page_11' => [
            'pwd_aware_yes' => [
                'x' => 94, 'y' => 56, 'type' => 'checkbox_match',
                'source' => 'pwd_aware', 'value' => 'yes',
            ],
            'pwd_aware_no' => [
                'x' => 112, 'y' => 56, 'type' => 'checkbox_match',
                'source' => 'pwd_aware', 'value' => 'no',
            ],
            'pwd_complied_yes' => [
                'x' => 94, 'y' => 66, 'type' => 'checkbox_match',
                'source' => 'pwd_complied', 'value' => 'yes',
            ],
            'pwd_complied_no' => [
                'x' => 112, 'y' => 66, 'type' => 'checkbox_match',
                'source' => 'pwd_complied', 'value' => 'no',
            ],

            'pwd_actions' => [
                'x' => 40, 'y' => 94, 'w' => 170, 'h' => 6.0,
                'type' => 'multiline', 'font_size' => 10, 'max_lines' => 7,
            ],
            'pwd_challenges' => [
                'x' => 40, 'y' => 155, 'w' => 170, 'h' => 6.0,
                'type' => 'multiline', 'font_size' => 10, 'max_lines' => 7,
            ],
            'pwd_future_plans' => [
                'x' => 40, 'y' => 210, 'w' => 170, 'h' => 6.0,
                'type' => 'multiline', 'font_size' => 10, 'max_lines' => 7,
            ],
        ],
    ];
}


    private static function page12Environment(): array
    {
        return [
            'page_12' => [
                'ewaste_initiatives' => [
                    'x' => 40, 'y' => 58, 'w' => 170, 'h' => 6.0,
                    'type' => 'multiline', 'font_size' => 10, 'max_lines' => 7,
                ],
                'carbon_initiatives' => [
                    'x' => 40, 'y' => 112, 'w' => 170, 'h' => 6.0,
                    'type' => 'multiline', 'font_size' => 10, 'max_lines' => 6,
                ],
                'emca_status' => [
                    'x' => 40, 'y' => 208, 'w' => 170, 'h' => 6.0,
                    'type' => 'multiline', 'font_size' => 10, 'max_lines' => 5,
                ],
            ],
        ];
    }

    private static function page13Submitter(): array
    {
        return [
            'page_13' => [
                'comments' => [
                    'x' => 30, 'y' => 46, 'w' => 175, 'h' => 6.0,
                    'type' => 'multiline', 'font_size' => 10, 'max_lines' => 4,
                ],

                'submitter_name'  => ['x' => 44, 'y' => 106, 'w' => 120, 'font_size' => 10],
                'submitter_title' => ['x' => 44, 'y' => 116, 'w' => 120, 'font_size' => 10],
                'submitter_date'  => ['x' => 44, 'y' => 124, 'w' => 120, 'font_size' => 10],

                'signature_image' => [
                    'x' => 46, 'y' => 131, 'w' => 50, 'h' => 10, 'type' => 'image',
                ],
                'company_stamp_image' => [
                    'x' => 143, 'y' => 108, 'w' => 40, 'h' => 34, 'type' => 'image',
                ],
            ],
        ];
    }
}
