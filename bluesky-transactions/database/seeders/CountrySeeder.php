<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'République Démocratique du Congo', 'code' => 'CD', 'currency_code' => 'CDF', 'currency_name' => 'Franc Congolais', 'flag_emoji' => '🇨🇩', 'phone_code' => '+243', 'default_fee_percentage' => 3.50],
            ['name' => 'Zambie', 'code' => 'ZM', 'currency_code' => 'ZMW', 'currency_name' => 'Kwacha zambien', 'flag_emoji' => '🇿🇲', 'phone_code' => '+260', 'default_fee_percentage' => 3.00],
            ['name' => 'Tanzanie', 'code' => 'TZ', 'currency_code' => 'TZS', 'currency_name' => 'Shilling tanzanien', 'flag_emoji' => '🇹🇿', 'phone_code' => '+255', 'default_fee_percentage' => 3.00],
            ['name' => 'Kenya', 'code' => 'KE', 'currency_code' => 'KES', 'currency_name' => 'Shilling kenyan', 'flag_emoji' => '🇰🇪', 'phone_code' => '+254', 'default_fee_percentage' => 2.50],
            ['name' => 'Malawi', 'code' => 'MW', 'currency_code' => 'MWK', 'currency_name' => 'Kwacha malawien', 'flag_emoji' => '🇲🇼', 'phone_code' => '+265', 'default_fee_percentage' => 3.50],
            ['name' => 'Zimbabwe', 'code' => 'ZW', 'currency_code' => 'ZWL', 'currency_name' => 'Dollar zimbabwéen', 'flag_emoji' => '🇿🇼', 'phone_code' => '+263', 'default_fee_percentage' => 3.00],
            ['name' => 'Afrique du Sud', 'code' => 'ZA', 'currency_code' => 'ZAR', 'currency_name' => 'Rand sud-africain', 'flag_emoji' => '🇿🇦', 'phone_code' => '+27', 'default_fee_percentage' => 2.00],
            ['name' => 'Namibie', 'code' => 'NA', 'currency_code' => 'NAD', 'currency_name' => 'Dollar namibien', 'flag_emoji' => '🇳🇦', 'phone_code' => '+264', 'default_fee_percentage' => 2.50],
        ];

        foreach ($countries as $country) {
            DB::table('countries')->insertOrIgnore(array_merge($country, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
