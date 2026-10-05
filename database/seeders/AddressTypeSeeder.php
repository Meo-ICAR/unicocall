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
                'name' => 'Domicilio Digitale',
                'is_person' => true,
            ],
            [
                'id' => 4,
                'name' => 'Domicilio Fiscale',
                'is_person' => true,
            ],
            [
                'id' => 9,
                'name' => 'Indirizzo',
                'is_person' => false,
            ],
            [
                'id' => 10,
                'name' => 'Sede Legale',
                'is_person' => false,
            ],
            [
                'id' => 11,
                'name' => 'Sede Operativa',
                'is_person' => false,
            ],
            [
                'id' => 12,
                'name' => 'Sede Amministrativa',
                'is_person' => false,
            ],
            [
                'id' => 13,
                'name' => 'Sede Stabilimento',
                'is_person' => false,
            ],
            [
                'id' => 14,
                'name' => 'Sede Secondaria',
                'is_person' => false,
            ],
            [
                'id' => 15,
                'name' => 'Sede Occasionale',
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
