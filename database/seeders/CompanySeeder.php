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
            $company = Company::create([
                'name' => 'Hassisto Srl',
                'vat_number' => '09006331210',
                'company_type' => 'call center',
            ]);

            $this->command->info("Company created: {$company->name} (ID: {$company->id})");
        } catch (\Exception $e) {
            Log::error('Error creating company: ' . $e->getMessage());
            $this->command->error('Error creating company: ' . $e->getMessage());
        }
    }
}
