<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Create Hassisto Srl
            $hassisto = Company::create([
                'name' => 'Hassisto Srl',
                'vat_number' => '09006331210',
                'company_type' => 'sw house',
            ]);

            $this->command->info("Company created: {$hassisto->name} (ID: {$hassisto->id})");

            // Create Innovatech Holdings Ltd
            $innovatech = Company::create([
                'name' => 'Innovatech Holdings Ltd',
                'vat_number' => 'GB361144034',
                'company_type' => 'call center',
                'contact_email' => 'info@innovatech.co.uk',
                'page_header' => 'Innovatech Holdings Ltd - Leading Technology Solutions',
                'page_footer' => '© 2024 Innovatech Holdings Ltd. Company number: 12958832',
            ]);

            $this->command->info("Company created: {$innovatech->name} (ID: {$innovatech->id})");

            // Create DATALIA SRL
            $datlia = Company::create([
                'name' => 'DATALIA SRL',
                'vat_number' => '05993670651',
                'company_type' => 'call center',
                'contact_email' => 'datalia@pec.it',
                'page_header' => 'DATALIA SRL - Servizi di Call Center e Telemarketing',
                'page_footer' => '© 2024 DATALIA SRL - P.IVA 05993670651 - Capitale Sociale €10.000,00',
            ]);

            $this->command->info("Company created: {$datlia->name} (ID: {$datlia->id})");
        } catch (\Exception $e) {
            Log::error('Error creating company: ' . $e->getMessage());
            $this->command->error('Error creating company: ' . $e->getMessage());
        }
    }
}
