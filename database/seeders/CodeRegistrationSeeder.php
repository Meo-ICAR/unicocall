<?php

namespace Database\Seeders;

use App\Models\CodeRegistration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CodeRegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Clear existing records using Eloquent model
            CodeRegistration::query()->delete();

            $codes = [
                [
                    'code' => 'COGE',
                    'name' => 'Conto Contabilità',
                    'is_mandatory' => false
                    'codeable_type' => 'App\Models\Client',
                ],
                [
                    'code' => 'SDI',
                    'name' => 'Sistema di Interscambio',
                    'is_mandatory' => false
                    'codeable_type' => 'App\Models\Company',
                ],
                [
                    'code' => 'IVA',
                    'name' => 'Partita IVA',
                    'is_mandatory' => false
                    'codeable_type' => 'App\Models\Client',
                ],
                [
                    'code' => 'SDI',
                    'name' => 'Sistema di Interscambio',
                    'is_mandatory' => false
                    'codeable_type' => 'App\Models\Company',
                ],
                [
                    'code' => 'IBAN',
                    'name' => 'Estremi conto corrente',
                    'is_mandatory' => false
                    'codeable_type' => 'App\Models\Client',
                ],
                [
                    'code' => 'OAM',
                    'name' => 'Organismo di Agenti e Mediatori',
                    'is_mandatory' => false
                    'codeable_type' => 'App\Models\Client',
                ],
                [
                    'code' => 'OAM',
                    'name' => 'Organismo di Agenti e Mediatori',
                    'is_mandatory' => false
                    'codeable_type' => 'App\Models\Company',
                ],
                [
                    'code' => 'IVASS',
                    'name' => 'Istituto per la vigilanza sulle assicurazioni',
                    'is_mandatory' => false
                    'codeable_type' => 'App\Models\Client',
                ],
                [
                    'code' => 'IVASS',
                    'name' => 'Istituto per la vigilanza sulle assicurazioni',
                    'is_mandatory' => false
                    'codeable_type' => 'App\Models\Company',
                ],
                [
                    'code' => 'ISO27001',
                    'name' => 'ISO 27001',
                    'is_mandatory' => false
                    'codeable_type' => 'App\Models\Client',
                ],
            ];

            foreach ($codes as $codeData) {
                CodeRegistration::create($codeData);
                $this->command->info("Created code registration: {$codeData['code']} - {$codeData['name']}");
            }

            $this->command->info('CodeRegistration seeder completed successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error in CodeRegistration seeder: ' . $e->getMessage());
            throw $e;
        }
    }
}
