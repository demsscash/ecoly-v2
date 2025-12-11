<?php

namespace Database\Seeders;

use App\Models\SchoolSetting;
use Illuminate\Database\Seeder;

class SchoolSettingSeeder extends Seeder
{
    /**
     * Seed the school settings.
     */
    public function run(): void
    {
        SchoolSetting::updateOrCreate(
            ['id' => 1],
            [
                'name_fr' => 'École Aboubacar Fall',
                'name_ar' => 'مدرسة أبوبكر فال',
                'address_fr' => 'Nouakchott, Mauritanie',
                'address_ar' => 'نواكشوط، موريتانيا',
                'phone' => '+222 00 00 00 00',
                'email' => 'contact@aboubacarfall.mr',
            ]
        );
    }
}
