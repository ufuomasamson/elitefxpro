<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create basic currency settings
        SystemSetting::updateOrCreate(
            ['key' => 'currency_symbol'],
            [
                'value' => '€',
                'type' => 'string',
                'description' => 'Currency symbol to display',
                'group' => 'currency'
            ]
        );

        SystemSetting::updateOrCreate(
            ['key' => 'default_currency'],
            [
                'value' => 'EUR',
                'type' => 'string',
                'description' => 'Default currency code',
                'group' => 'currency'
            ]
        );

        SystemSetting::updateOrCreate(
            ['key' => 'app_name'],
            [
                'value' => 'TradeTrust Point',
                'type' => 'string',
                'description' => 'Application name',
                'group' => 'general'
            ]
        );

        SystemSetting::updateOrCreate(
            ['key' => 'support_email'],
            [
                'value' => 'support@tradetrustpoint.com',
                'type' => 'string',
                'description' => 'Support email address',
                'group' => 'general'
            ]
        );
    }
}
