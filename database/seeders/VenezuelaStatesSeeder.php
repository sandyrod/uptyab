<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VenezuelaStatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $estados = [
            'Amazonas',
            'Anzoátegui',
            'Apure',
            'Aragua',
            'Barinas',
            'Bolívar',
            'Carabobo',
            'Cojedes',
            'Delta Amacuro',
            'Falcón',
            'Guárico',
            'Lara',
            'Mérida',
            'Miranda',
            'Monagas',
            'Nueva Esparta',
            'Portuguesa',
            'Sucre',
            'Táchira',
            'Trujillo',
            'La Guaira',
            'Yaracuy',
            'Zulia',
            'Distrito Capital'
        ];

        $data = [];
        foreach ($estados as $state) {
            $data[] = [
                'nombre' => $state,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }

        DB::table('estados')->insert($data);
    }
}
