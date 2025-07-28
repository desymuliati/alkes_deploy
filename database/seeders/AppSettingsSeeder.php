<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppSetting;

class AppSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['setting_key' => 'limit_stok_pcs', 'setting_value' => 100, 'unit' => 'pcs', 'description' => 'Limit stok minimum untuk satuan pcs'],
            ['setting_key' => 'limit_stok_galon', 'setting_value' => 1, 'unit' => 'galon', 'description' => 'Limit stok minimum untuk satuan galon'],
            ['setting_key' => 'limit_stok_box', 'setting_value' => 10, 'unit' => 'box', 'description' => 'Limit stok minimum untuk satuan box'],
            ['setting_key' => 'limit_stok_unit', 'setting_value' => 20, 'unit' => 'unit', 'description' => 'Limit stok minimum untuk satuan unit'],
        ];

        foreach ($settings as $setting) {
            AppSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }
    }
}