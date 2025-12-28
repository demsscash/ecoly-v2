<?php

namespace Database\Seeders;

use App\Models\Serie;
use Illuminate\Database\Seeder;

class SerieSeeder extends Seeder
{
    /**
     * Seed default series for lycee classes
     */
    public function run(): void
    {
        $series = [
            [
                'name' => 'Littéraire',
                'code' => 'LIT',
                'description' => 'Série Littéraire - Orientation lettres et sciences humaines',
            ],
            [
                'name' => 'Série C',
                'code' => 'C',
                'description' => 'Série C - Orientation mathématiques et sciences physiques',
            ],
            [
                'name' => 'Série D',
                'code' => 'D',
                'description' => 'Série D - Orientation sciences de la vie et de la terre',
            ],
        ];

        foreach ($series as $serie) {
            Serie::firstOrCreate(
                ['code' => $serie['code']],
                $serie
            );
        }
    }
}
