<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ComplianceReturnsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, ensure a user exists for created_by (user ID 111)
        // If not, create a default user
        $userExists = DB::table('users')->where('id', 111)->exists();

        if (!$userExists) {
            DB::table('users')->insert([
                'id' => 111,
                'name' => 'Robert Mugo',
                'email' => 'robert.mugo@kplc.co.ke',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ==================== ASP COMPLIANCE ====================
        DB::table('asp_compliances')->insert([
            'id' => 1,
            'licensee_name' => 'Kenya Power & Lighting Company (KPLC)',
            'license_no' => 'AFP:TL/NFP/00051',
            'other_licenses' => 'CSP:TL/CSP/00451  and	ASP:TL/ASP/00551',
            'financial_year' => '2025/2026',
            'quarter' => 'Q1',
            'form_data' => $this->getAspFormData(),
            'attachments' => json_encode([
                'signature' => 'cak/attachments/asp/PwWIRUqKif8W1w1CsczkxGrHEaxE5vmwDJMkqKZB.png',
                'company_stamp' => 'cak/attachments/asp/iweZlRp64QYjzQUV8blyFtSuQxV6hufZrg138mVh.png'
            ]),
            'status' => 'generated',
            'pdf_path' => 'cak/generated/asp/2025-2026/Q1/ASP_AFP_TL_NFP_00051_2025-2026_Q1.pdf',
            'submitted_at' => null,
            'created_by' => 111,
            'approved_by' => null,
            'approved_at' => null,
            'created_at' => '2026-05-08 10:23:52',
            'updated_at' => '2026-05-12 19:07:12',
        ]);

        // ==================== CSP COMPLIANCE ====================
        DB::table('csp_compliances')->insert([
            'id' => 1,
            'licensee_name' => 'Kenya Power & Lighting Company (KPLC)',
            'license_no' => 'CSP:TL/CSP/00451',
            'other_licenses' => 'ASP:TL/ASP/00551 , NFP:TL/NFP/00051',
            'financial_year' => '2025/2026',
            'quarter' => 'Q4',
            'form_data' => $this->getCspFormData(),
            'attachments' => json_encode([]),
            'status' => 'generated',
            'pdf_path' => 'cak/generated/csp/2025-2026/Q4/CSP_CSP_TL_CSP_00451_2025-2026_Q4.pdf',
            'submitted_at' => null,
            'created_by' => 111,
            'approved_by' => 111,
            'approved_at' => '2026-05-12 09:35:47',
            'created_at' => '2026-05-12 08:28:47',
            'updated_at' => '2026-05-12 09:36:46',
        ]);

        // ==================== NFP COMPLIANCE ====================
        DB::table('nfp_compliances')->insert([
            'id' => 1,
            'licensee_name' => 'Kenya Power & Lighting Company (KPLC)',
            'license_no' => 'NFP:TL/NFP/00051',
            'other_licenses' => 'CSP:TL/CSP/00451  and	ASP:TL/ASP/00551',
            'financial_year' => '2025/2026',
            'quarter' => 'Q4',
            'form_data' => $this->getNfpFormData(),
            'attachments' => json_encode([
                'signature' => 'cak/attachments/nfp/sczmFlIWM1YQbTj87ypMhLEQURgrxXCUMs6Y6eWo.png',
                'company_stamp' => 'cak/attachments/nfp/CMP0t6CaIeQtr5AgpUw39uFmAS2yiQ3FYeHcy4Y8.png'
            ]),
            'status' => 'generated',
            'pdf_path' => 'cak/generated/nfp/2025-2026/Q4/NFP_NFP_TL_NFP_00051_2025-2026_Q4.pdf',
            'submitted_at' => null,
            'created_by' => 111,
            'approved_by' => null,
            'approved_at' => null,
            'created_at' => '2026-05-04 06:45:36',
            'updated_at' => '2026-05-12 20:36:28',
            'latitude' => -1.2863890,
            'longitude' => 36.8172230,
            'fibre_km' => 0.00,
            'tower_count' => 0,
            'infrastructure' => null,
            'primary_numbers' => null,
            'secondary_numbers' => null,
            'bulk_sms' => null,
            'broadband' => null,
            'staff' => null,
        ]);

        $this->command->info('Compliance returns seeded successfully!');
        $this->command->info('- ASP Compliance: 1 record');
        $this->command->info('- CSP Compliance: 1 record');
        $this->command->info('- NFP Compliance: 1 record');
    }

    /**
     * Get ASP Form Data as JSON
     */
    private function getAspFormData(): string
    {
        return json_encode([
            'licensee_name' => 'Kenya Power & Lighting Company (KPLC)',
            'license_no' => 'AFP:TL/NFP/00051',
            'other_licenses' => 'CSP:TL/CSP/00451  and	ASP:TL/ASP/00551',
            'financial_year' => '2025/2026',
            'quarter' => 'Q1',
            'county' => 'Nairobi',
            'town' => 'Nairobi',
            'street_road' => 'kolobot',
            'building_name' => 'Stima Plaza',
            'floor_no' => '8',
            'room_no' => 'n/a',
            'p_o_box' => '30099',
            'postal_town' => 'Nairobi',
            'postal_code' => '00100',
            'tel_no' => '020 3201 000',
            'mobile_no' => '0703 070707',
            'other_tel' => '0732170170',
            'email' => 'Customercare@kplc.co.ke',
            'web_address' => 'www.kplc.co.ke',
            'ceo_name' => 'Eng Joseph Siror',
            'contact_person' => 'Robert Mugo',
            'contact_landline' => '020 3201 000',
            'contact_mobile' => '0703 070707',
            'contact_email' => 'Customercare@kplc.co.ke',
            'address_changed' => 'no',
            'pwd_aware' => 'yes',
            'pwd_complied' => 'yes',
            'pwd_actions' => "All Banking halls and office entrances have ramps for ease of facilitating PWDs. We have toilet for\nuse by PWDS. We have rooms for lactating mothers (Express rooms), we have engineers with\ndisabilities, that equal opportunity for all",
            'pwd_challenges' => "KPLC has bulk of consumables being special, hence YWPWDS find it faculty to comply due to\nfinancial constraints, however the opportunity is consistently offered year on year.",
            'pwd_future_plans' => "Continuous review of what materials and services can be supplied by YWPWDS.",
            'carbon_initiatives' => "• Encourage use of energy saving bulbs\nMore use of concrete poles to deter felling of trees in search for wooden poles\n• Continuous phase out of thermal power generation stations such as Garissa, Mpeketoni,\nLamu and Hola which are now connected to the grid\n• Hybridization of thermal power plants in off-grid areas by incorporating solar and wind\ngeneration\n• Controlled use of paper for printing through allocation of quotas to every employee, hence\nsaving on trees\n• Embracing Information and communication technology; currently e-mails are the key mode\nof communication within the compапу\n• Planting of trees in partnership with KFS, KWS and other interested partners\n• Adoption of E-mobility with the company adopting electrically powered motorcycles for its\noperations, and the company entering into partnership with the government to provide\ncharging stations for car batteries\n• Promote use of electrical energy Saving equipment like use of cookers, and encouraging the\npublic out there to embrace electricity to uplift their livelihood, this reduces over reliance of\ntraditional equipment powered with diesel/fuel\n• Over 90% of all energy purchase and distributed by KPLC is green. The National control\nand Energy Dispatch Centre give preference to green energy and the Power Purchase\nAgreements are designed to support this",
            'ewaste_initiatives' => "• Exchange of incandescent bulbs with energy saving bulbs; KPLC collected incandescent\nbulbs through a contracted service provider, who crashed them and segregated materials for\nrecycling industry\n• KPLC encourages customers not to mix obsolete bulbs and tubes with normal mainstream\nwaste, but rather segregate at source. This eases the work of waste collectors because those\nsearching for recyclables access them easily.\n• KPLC recovers all obsolete meters from customers, those that cannot be repaired are held in\ncontainments at Ruaraka stores and other depots awaiting safe disposal\n• A high penalty of KES 1,000,000/= or jail term offive years has been imposed to deter vandals\nwho may wish to tamper with meters or other electrical infrastructure.\n• Printing services are contracted and the service providers replenishes and disposes used toner\ncartridges and obsolete printers as per contractual agreements.\n• Obsolete computers and computer accessories are disposed guided by the procurement and\nasset disposal act where highest bidders collect the equipment for a fee.\n• Currently working with MOCTDE to deliver all ICT e-waste to E-Waste Disposal Centre in\nNairobi Industrial Area.",
            'emca_status' => "• All new energy projects are subjected to an Integrated Environmental Impacts assessment\nand ongoing projects are subjected to annual environmental audits.\n• The company issues to the contractor Environmental and Social Management Plan which is\nenforced by KLPC SHE department, and supervising consultant\n• Environmental inspections and environmental management plans monitors for projects\nduring implementation phase\n• Promote Public participation for projects in line with the Kenya Constitution 2010, EMCA\n1999 and development partners safeguard requirements\n• KPLC has a fully-fledged HSE Department that works closely with all company departments\nto ensure environmental matters are factored in daily running of the company",
            'comments' => "i.The reviewed forms good however, PDFformat requires conversion to Word forfaster data entry\nand review else, the manual option requires printing and handwriting which are time consuming.\nii.There is need for increased stakeholder forums with CA to up capacity and awareness for\nensuring compliance with all requirements amidst first changing business and regulatory\nlandscape.\niii.Staffing details provided are for those directly interact with the business noting a reduction of by\none technical staff to three (3) through transfer.\niV.Infrastructure details continually changing due to works in progress for infrastructure\ndevelopments.\nV.Inclusion of climate change is very welcome more so at sector level to ensure realization of\nNational Determined Contributions NDCs and global obligations.\nvi.The Communication Authority to assist in capacity building in the area of regulatory compliance\nfor telecom business, carbon reduction and adaptations, tariffs, policy & regulation and\nemerging technologies. This is through identification of strategic training and capacity building\nopportunities for consideration by players and facilitation",
            'submitter_name' => 'Robert Mugo',
            'submitter_title' => 'GM - ICT',
            'submitter_date' => '2026-05-09',
            'asp_form_complete' => '1',
            'staff' => [
                'tech_perm' => ['local_m' => '4', 'local_f' => '3', 'exp_m' => '0', 'exp_f' => '0'],
                'tech_cont' => ['local_m' => '0', 'local_f' => '0', 'exp_m' => '0', 'exp_f' => '1'],
                'tech_temp' => ['local_m' => '0', 'local_f' => '0', 'exp_m' => '0', 'exp_f' => '0'],
                'nontech_perm' => ['local_m' => '6', 'local_f' => '8', 'exp_m' => '0', 'exp_f' => '0'],
                'nontech_cont' => ['local_m' => '2', 'local_f' => '3', 'exp_m' => '0', 'exp_f' => '0'],
                'nontech_temp' => ['local_m' => '2', 'local_f' => '2', 'exp_m' => '1', 'exp_f' => '0'],
            ],
        ], JSON_PRETTY_PRINT);
    }

    /**
     * Get CSP Form Data as JSON
     */
    private function getCspFormData(): string
    {
        return json_encode([
            'licensee_name' => 'Kenya Power & Lighting Company (KPLC)',
            'license_no' => 'CSP:TL/CSP/00451',
            'other_licenses' => 'ASP:TL/ASP/00551 , NFP:TL/NFP/00051',
            'financial_year' => '2025/2026',
            'quarter' => 'Q4',
            'county' => 'nairobi',
            'town' => 'Nairobi',
            'street_road' => 'Kolobot',
            'building_name' => 'Stima Plaza',
            'floor_no' => '8',
            'room_no' => 'n/a',
            'p_o_box' => '30099',
            'postal_town' => 'Nairobi',
            'postal_code' => '00100',
            'tel_no' => '020 3201 000',
            'mobile_no' => '070332101010',
            'other_tel' => '0732170170',
            'email' => 'Customercare@kplc.co.ke',
            'web_address' => 'www.kplc.co.ke',
            'ceo_name' => 'Eng. Joseph Siror',
            'contact_person' => 'Robert Mugo',
            'contact_landline' => '020 3201 000',
            'contact_mobile' => '0732170170',
            'contact_email' => 'Customercare@kplc.co.ke',
            'address_changed' => 'no',
            'pwd_aware' => 'yes',
            'pwd_complied' => 'yes',
            'pwd_actions' => "All Banking halls and office entrances have ramps for ease of facilitating PWDs. We have toilet for\nuse by PWDS. We have rooms for lactating mothers (Express rooms), we have engineers with\ndisabilities, that equal opportunity for all",
            'pwd_reasons' => "Globally KPLC gives 30% of it s Tenders to PWDS and mores so to the kenyans",
            'pwd_challenges' => "KPLC has bulk of consumables being special, hence YWPWDS find it faculty to comply due to\nfinancial constraints, however the opportunity is consistently offered year on year.",
            'pwd_future_plans' => "Continuous review of what materials and services can be supplied by YWPWDS.",
            'carbon_initiatives' => "• Encourage use of energy saving bulbs\nMore use of concrete poles to deter felling of trees in search for wooden poles\n• Continuous phase out of thermal power generation stations such as Garissa, Mpeketoni,\nLamu and Hola which are now connected to the grid\n• Hybridization of thermal power plants in off-grid areas by incorporating solar and wind\ngeneration\n• Controlled use of paper for printing through allocation of quotas to every employee, hence\nsaving on trees\n• Embracing Information and communication technology; currently e-mails are the key mode\nof communication within the compапу\n• Planting of trees in partnership with KFS, KWS and other interested partners\n• Adoption of E-mobility with the company adopting electrically powered motorcycles for its\noperations, and the company entering into partnership with the government to provide\ncharging stations for car batteries\n• Promote use of electrical energy Saving equipment like use of cookers, and encouraging the\npublic out there to embrace electricity to uplift their livelihood, this reduces over reliance of\ntraditional equipment powered with diesel/fuel\n• Over 90% of all energy purchase and distributed by KPLC is green. The National control\nand Energy Dispatch Centre give preference to green energy and the Power Purchase\nAgreements are designed to support this",
            'ewaste_initiatives' => "∙ Exchange of incandescent bulbs with energy saving bulbs; KPLC collected incandescent\nbulbs through a contracted service provider, who crashed them and segregated materials for\nrecycling industry\n∙ KPLC encourages customers not to mix obsolete bulbs and tubes with normal mainstream\nwaste, but rather segregate at source. This eases the work of waste collectors because those\nsearching for recyclables access them easily.\n∙ KPLC recovers all obsolete meters from customers, those that cannot be repaired are held in\ncontainments at Ruaraka stores and other depots awaiting safe disposal\n∙ A high penalty of KES 1,000,000/= or jail term offive years has been imposed to deter vandals\nwho may wish to tamper with meters or other electrical infrastructure.\n∙ Printing services are contracted and the service providers replenishes and disposes used toner\ncartridges and obsolete printers as per contractual agreements.\n∙ Obsolete computers and computer accessories are disposed guided by the procurement and\nasset disposal act where highest bidders collect the equipment for a fee.\n∙ Currently working with MOCTDE to deliver all ICT e-waste to E-Waste Disposal Centre in\nNairobi Industrial Area.",
            'emca_status' => "• All new energy projects are subjected to an Integrated Environmental Impacts assessment\nand ongoing projects are subjected to annual environmental audits.\n• The company issues to the contractor Environmental and Social Management Plan which is\nenforced by KLPC SHE department, and supervising consultant\n• Environmental inspections and environmental management plans monitors for projects\nduring implementation phase\n• Promote Public participation for projects in line with the Kenya Constitution 2010, EMCA\n1999 and development partners safeguard requirements\n• KPLC has a fully-fledged HSE Department that works closely with all company departments\nto ensure environmental matters are factored in daily running of the company",
            'comments' => "i. The copy shared in PDF version requires conversion to Word format to ease process of\ncompilation else a manual equivalent process of data entry involves first printing and writing\nwhich is time consuming, prone to errors and inflexible where several stakeholders required.\nii. No services offered using this license during the reporting period.",
            'submitter_name' => 'Robert Mugo',
            'submitter_title' => 'ICT General Manager',
            'submitter_date' => '2026-05-03',
            'financial_year_start_date' => '2026-03-01',
            'financial_year_end_date' => '2026-06-30',
        ], JSON_PRETTY_PRINT);
    }

    /**
     * Get NFP Form Data as JSON
     */
    private function getNfpFormData(): string
    {
        return json_encode([
            'licensee_name' => 'Kenya Power & Lighting Company (KPLC)',
            'license_no' => 'NFP:TL/NFP/00051',
            'other_licenses' => 'CSP:TL/CSP/00451  and	ASP:TL/ASP/00551',
            'financial_year' => '2025/2026',
            'quarter' => 'Q4',
            'county' => 'Nairobi',
            'town' => 'Nairobi',
            'street_road' => 'kolobot',
            'building_name' => 'Stima Plaza',
            'floor_no' => '8',
            'room_no' => 'n/a',
            'p_o_box' => '30099',
            'postal_town' => 'Nairobi',
            'postal_code' => '00100',
            'tel_no' => '020 3201 000',
            'mobile_no' => '0703 070707',
            'other_tel' => '0732170170',
            'email' => 'Customercare@kplc.co.ke',
            'web_address' => 'www.kplc.co.ke',
            'ceo_name' => 'Eng Joseph Siror',
            'contact_person' => 'Robert Mugo',
            'contact_landline' => '020 3201 000',
            'contact_mobile' => '0703 070707',
            'contact_email' => 'Customercare@kplc.co.ke',
            'address_changed' => 'no',
            'pwd_aware' => 'yes',
            'pwd_complied' => 'yes',
            'pwd_actions' => "All Banking halls and office entrances have ramps for ease of facilitating PWDs. We have toilet for\nuse by PWDS. We have rooms for lactating mothers (Express rooms), we have engineers with\ndisabilities, that equal opportunity for all",
            'pwd_challenges' => "KPLC has bulk of consumables being special, hence YWPWDS find it faculty to comply due to\nfinancial constraints, however the opportunity is consistently offered year on year.",
            'pwd_future_plans' => "Continuous review of what materials and services can be supplied by YWPWDS.",
            'carbon_initiatives' => "• Encourage use of energy saving bulbs\nMore use of concrete poles to deter felling of trees in search for wooden poles\n• Continuous phase out of thermal power generation stations such as Garissa, Mpeketoni,\nLamu and Hola which are now connected to the grid\n• Hybridization of thermal power plants in off-grid areas by incorporating solar and wind\ngeneration\n• Controlled use of paper for printing through allocation of quotas to every employee, hence\nsaving on trees\n• Embracing Information and communication technology; currently e-mails are the key mode\nof communication within the compапу\n• Planting of trees in partnership with KFS, KWS and other interested partners\n• Adoption of E-mobility with the company adopting electrically powered motorcycles for its\noperations, and the company entering into partnership with the government to provide\ncharging stations for car batteries\n• Promote use of electrical energy Saving equipment like use of cookers, and encouraging the\npublic out there to embrace electricity to uplift their livelihood, this reduces over reliance of\ntraditional equipment powered with diesel/fuel\n• Over 90% of all energy purchase and distributed by KPLC is green. The National control\nand Energy Dispatch Centre give preference to green energy and the Power Purchase\nAgreements are designed to support this",
            'ewaste_initiatives' => "• Exchange of incandescent bulbs with energy saving bulbs; KPLC collected incandescent\nbulbs through a contracted service provider, who crashed them and segregated materials for\nrecycling industry\n• KPLC encourages customers not to mix obsolete bulbs and tubes with normal mainstream\nwaste, but rather segregate at source. This eases the work of waste collectors because those\nsearching for recyclables access them easily.\n• KPLC recovers all obsolete meters from customers, those that cannot be repaired are held in\ncontainments at Ruaraka stores and other depots awaiting safe disposal\n• A high penalty of KES 1,000,000/= or jail term offive years has been imposed to deter vandals\nwho may wish to tamper with meters or other electrical infrastructure.\n• Printing services are contracted and the service providers replenishes and disposes used toner\ncartridges and obsolete printers as per contractual agreements.\n• Obsolete computers and computer accessories are disposed guided by the procurement and\nasset disposal act where highest bidders collect the equipment for a fee.\n• Currently working with MOCTDE to deliver all ICT e-waste to E-Waste Disposal Centre in\nNairobi Industrial Area.",
            'emca_status' => "• All new energy projects are subjected to an Integrated Environmental Impacts assessment\nand ongoing projects are subjected to annual environmental audits.\n• The company issues to the contractor Environmental and Social Management Plan which is\nenforced by KLPC SHE department, and supervising consultant\n• Environmental inspections and environmental management plans monitors for projects\nduring implementation phase\n• Promote Public participation for projects in line with the Kenya Constitution 2010, EMCA\n1999 and development partners safeguard requirements\n• KPLC has a fully-fledged HSE Department that works closely with all company departments\nto ensure environmental matters are factored in daily running of the company",
            'comments' => "i.The reviewed forms good however, PDFformat requires conversion to Word forfaster data entry\nand review else, the manual option requires printing and handwriting which are time consuming.\nii.There is need for increased stakeholder forums with CA to up capacity and awareness for\nensuring compliance with all requirements amidst first changing business and regulatory\nlandscape.\niii.Staffing details provided are for those directly interact with the business noting a reduction of by\none technical staff to three (3) through transfer.\niV.Infrastructure details continually changing due to works in progress for infrastructure\ndevelopments.\nV.Inclusion of climate change is very welcome more so at sector level to ensure realization of\nNational Determined Contributions NDCs and global obligations.\nvi.The Communication Authority to assist in capacity building in the area of regulatory compliance\nfor telecom business, carbon reduction and adaptations, tariffs, policy & regulation and\nemerging technologies. This is through identification of strategic training and capacity building\nopportunities for consideration by players and facilitation",
            'submitter_name' => 'Robert Mugo',
            'submitter_title' => 'GM - ICT',
            'submitter_date' => '2026-05-04',
            'staff' => [
                'technical_permanent' => ['local_m' => '3', 'local_f' => '0', 'exp_m' => '0', 'exp_f' => '0'],
                'technical_contract' => ['local_m' => '0', 'local_f' => '0', 'exp_m' => '0', 'exp_f' => '0'],
                'technical_temporary' => ['local_m' => '0', 'local_f' => '0', 'exp_m' => '0', 'exp_f' => '0'],
                'non_technical_permanent' => ['local_m' => '2', 'local_f' => '2', 'exp_m' => '0', 'exp_f' => '0'],
                'non_technical_contract' => ['local_m' => '4', 'local_f' => '4', 'exp_m' => '0', 'exp_f' => '0'],
                'non_technical_temporary' => ['local_m' => '0', 'local_f' => '0', 'exp_m' => '0', 'exp_f' => '0'],
            ],
            'infrastructure' => [
                '1' => ['type' => 'Fibre Optic Cable', 'description' => "Cable infrastructure comprises of the following cable types deployed aerial with\nunderground segments\ni.  Optical ground wire - OPGW with aerial deployment\nii. All dielectric self-support - ADSS with aerial deployment\niii.Fig 8- with aerial deployment\niv. Buried ducted - underground deployment"],
                '2' => ['type' => 'Telecommunication Mast/Tower', 'description' => "Deployed at strategic sites i.e. mountain/hill tops for line of sight requirements"],
                '3' => ['type' => null, 'description' => null],
                '4' => ['type' => null, 'description' => null],
                '5' => ['type' => null, 'description' => null],
                '6' => ['type' => null, 'description' => null],
            ],
            'primary_numbers' => [
                'short_codes' => ['assigned' => '0', 'utilized' => '0'],
                'ussd_codes' => ['assigned' => '0', 'utilized' => '0'],
                'premium_rate_numbers' => ['assigned' => '0', 'utilized' => '0'],
                'toll_free_numbers' => ['assigned' => '0', 'utilized' => '0'],
            ],
            'secondary_numbers' => [
                '1' => ['csp_name' => 'n/a', 'shortcode_ussd' => 'n/a', 'tariff' => '0', 'volume' => '0'],
                '2' => ['csp_name' => 'n/a', 'shortcode_ussd' => 'n/a', 'tariff' => '0', 'volume' => '0'],
                '3' => ['csp_name' => 'n/a', 'shortcode_ussd' => 'n/a', 'tariff' => '0', 'volume' => '0'],
                '4' => ['csp_name' => 'n/a', 'shortcode_ussd' => 'n/a', 'tariff' => '0', 'volume' => '0'],
                '5' => ['csp_name' => 'n/a', 'shortcode_ussd' => 'n/a', 'tariff' => '0', 'volume' => '0'],
            ],
            'bulk_sms' => [
                '1' => ['csp_name' => 'n/a', 'tariff' => '0', 'volume' => '0'],
                '2' => ['csp_name' => 'n/a', 'tariff' => '0', 'volume' => '0'],
                '3' => ['csp_name' => 'n/a', 'tariff' => '0', 'volume' => '0'],
                '4' => ['csp_name' => 'n/a', 'tariff' => '0', 'volume' => '0'],
                '5' => ['csp_name' => 'n/a', 'tariff' => '0', 'volume' => '0'],
            ],
            'broadband_infrastructure' => [
                '1' => ['type' => 'Optical Fibre', 'ownership' => 'kenya power', 'capacity_owned' => '0', 'capacity_utilized' => '0'],
                '2' => ['type' => 'Telecomm Mask/Towers', 'ownership' => 'kenya power', 'capacity_owned' => '0', 'capacity_utilized' => '0'],
                '3' => ['type' => null, 'ownership' => null, 'capacity_owned' => null, 'capacity_utilized' => null],
                '4' => ['type' => null, 'ownership' => null, 'capacity_owned' => null, 'capacity_utilized' => null],
                '5' => ['type' => null, 'ownership' => null, 'capacity_owned' => null, 'capacity_utilized' => null],
                '6' => ['type' => null, 'ownership' => null, 'capacity_owned' => null, 'capacity_utilized' => null],
            ],
        ], JSON_PRETTY_PRINT);
    }
}
