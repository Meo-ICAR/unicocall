<?php

namespace Database\Seeders;

use App\Models\ClientType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $clientTypes = [
            [
                'id' => 1,
                'name' => 'Privato',
                'description' => 'Persona fisica privata',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'PMI',
                'description' => 'Piccola o Media Impresa',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'PA',
                'description' => 'Pubblica Amministrazione',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Azienda',
                'description' => 'Azienda / Persona giuridica',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'Lead',
                'description' => 'Contatto non ancora convertito',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'name' => 'Professionista',
                'description' => 'Libero professionista',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'name' => 'Istituzione',
                'description' => 'Istituzione Governativa',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($clientTypes as $clientType) {
            ClientType::create($clientType);
            $this->command->info("Created client type: {$clientType['name']} - {$clientType['description']}");
        }

        $this->command->info('ClientType seeder completed successfully!');
    }
}
