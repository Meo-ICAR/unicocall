<?php

namespace Database\Seeders;

use App\Models\AddressType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $addressTypes = [
            ['id' => 1, 'name' => 'Residenza', 'is_person' => 1],
            ['id' => 2, 'name' => 'Domicilio', 'is_person' => 1],
            ['id' => 3, 'name' => 'Domicilio Legale', 'is_person' => 0],
            ['id' => 4, 'name' => 'Domicilio Operativo', 'is_person' => 0],
            ['id' => 5, 'name' => 'Sede Legale', 'is_person' => 0],
            ['id' => 6, 'name' => 'Sede Operativa', 'is_person' => 0],
        ];

        foreach ($addressTypes as $type) {
            AddressType::updateOrCreate(
                ['id' => $type['id']],
                [
                    'name' => $type['name'],
                    'is_person' => $type['is_person'],
                ]
            );
        }
    }
}
