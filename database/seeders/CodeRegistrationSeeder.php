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
            // Clear existing records including soft deletes
            CodeRegistration::query()->forceDelete();

            $codes = [
                [
                    'code' => 'COGE',
                    'name' => 'Conto Contabilità',
                    'is_mandatory' => false,
                    'codeable_type' => 'App\Models\Client',
                ],
                [
                    'code' => 'SDI',
                    'name' => 'Sistema di Interscambio',
                    'is_mandatory' => false,
                    'codeable_type' => 'App\Models\Company',
                ],
                [
                    'code' => 'IVA',
                    'name' => 'Partita IVA',
                    'is_mandatory' => false,
                    'codeable_type' => 'App\Models\Client',
                ],
                [
                    'code' => 'IBAN',
                    'name' => 'Estremi conto corrente',
                    'is_mandatory' => false,
                    'codeable_type' => 'App\Models\Client',
                ],
                [
                    'code' => 'OAM_CLIENT',
                    'name' => 'Organismo di Agenti e Mediatori',
                    'is_mandatory' => false,
                    'codeable_type' => 'App\Models\Client',
                ],
                [
                    'code' => 'OAM_COMPANY',
                    'name' => 'Organismo di Agenti e Mediatori',
                    'is_mandatory' => false,
                    'codeable_type' => 'App\Models\Company',
                ],
                [
                    'code' => 'IVASS_CLIENT',
                    'name' => 'Istituto per la vigilanza sulle assicurazioni',
                    'is_mandatory' => false,
                    'codeable_type' => 'App\Models\Client',
                ],
                [
                    'code' => 'IVASS_COMPANY',
                    'name' => 'Istituto per la vigilanza sulle assicurazioni',
                    'is_mandatory' => false,
                    'codeable_type' => 'App\Models\Company',
                ],
                [
                    'code' => 'ISO27001',
                    'name' => 'ISO 27001',
                    'is_mandatory' => false,
                    'codeable_type' => 'App\Models\Client',
                ],
            ];

            foreach ($codes as $codeData) {
                $code = CodeRegistration::firstOrCreate(
                    ['code' => $codeData['code']],
                    $codeData
                );
                $this->command->info("Processed code registration: {$codeData['code']} - {$codeData['name']}");
            }

            $this->command->info('CodeRegistration seeder completed successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error in CodeRegistration seeder: ' . $e->getMessage());
            throw $e;
        }
    }
}
