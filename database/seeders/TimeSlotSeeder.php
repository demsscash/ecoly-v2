<?php

namespace Database\Seeders;

use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    public function run(): void
    {
        $slots = [
            ['name' => '1ère heure', 'start_time' => '08:00', 'end_time' => '09:00', 'order' => 1],
            ['name' => '2ème heure', 'start_time' => '09:00', 'end_time' => '10:00', 'order' => 2],
            ['name' => 'Récréation', 'start_time' => '10:00', 'end_time' => '10:15', 'order' => 3, 'is_break' => true],
            ['name' => '3ème heure', 'start_time' => '10:15', 'end_time' => '11:15', 'order' => 4],
            ['name' => '4ème heure', 'start_time' => '11:15', 'end_time' => '12:15', 'order' => 5],
            ['name' => 'Pause déjeuner', 'start_time' => '12:15', 'end_time' => '14:00', 'order' => 6, 'is_break' => true],
            ['name' => '5ème heure', 'start_time' => '14:00', 'end_time' => '15:00', 'order' => 7],
            ['name' => '6ème heure', 'start_time' => '15:00', 'end_time' => '16:00', 'order' => 8],
        ];

        foreach ($slots as $slot) {
            TimeSlot::create($slot);
        }
    }
}
