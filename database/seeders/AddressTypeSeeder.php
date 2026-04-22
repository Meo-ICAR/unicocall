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
        $now = now();

        $addressTypes = [
            [
                'id' => 1,
                'name' => 'Residenza',
                'is_person' => true,
            ],
            [
                'id' => 2,
                'name' => 'Domicilio',
                'is_person' => true,
            ],
            [
                'id' => 3,
                'name' => 'Sede Legale',
                'is_person' => true,
            ],
            [
                'id' => 4,
                'name' => 'Sede Operativa',
                'is_person' => true,
            ],
            [
                'id' => 5,
                'name' => 'Sede Amministrativa',
                'is_person' => true,
            ],
            [
                'id' => 6,
                'name' => 'Sede Stabilimento',
                'is_person' => true,
            ],
            [
                'id' => 7,
                'name' => 'Sede Secondaria',
                'is_person' => true,
            ],
            [
                'id' => 8,
                'name' => 'Domicilio Fiscale',
                'is_person' => true,
            ],
            [
                'id' => 9,
                'name' => 'Domicilio Aziendale',
                'is_person' => false,
            ],
            [
                'id' => 10,
                'name' => 'Sede Legale Aziendale',
                'is_person' => false,
            ],
            [
                'id' => 11,
                'name' => 'Sede Operativa Aziendale',
                'is_person' => false,
            ],
            [
                'id' => 12,
                'name' => 'Sede Amministrativa Aziendale',
                'is_person' => false,
            ],
            [
                'id' => 13,
                'name' => 'Sede Stabilimento Aziendale',
                'is_person' => false,
            ],
            [
                'id' => 14,
                'name' => 'Sede Secondaria Aziendale',
                'is_person' => false,
            ],
            [
                'id' => 15,
                'name' => 'Sede Occasionale Aziendale',
                'is_person' => false,
            ],
            [
                'id' => 16,
                'name' => 'Domicilio Digitale',
                'is_person' => false,
            ],
        ];

        foreach ($addressTypes as $addressType) {
            AddressType::create($addressType);
            $this->command->info("Created address type: {$addressType['name']} (" . ($addressType['is_person'] ? 'Persona' : 'Azienda') . ')');
        }

        $this->command->info('AddressType seeder completed successfully!');
    }
}
