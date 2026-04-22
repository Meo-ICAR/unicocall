<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            CompanySeeder::class,
        ]);

        User::factory()->create([
            'name' => 'admin',
            'email' => 'hassistosrl@gmail.com',
            'password' => bcrypt('password'),
            'company_name' => Company::first()->name,
            'is_approved' => true,
            'is_super_admin' => true,
            'current_company_id' => Company::first()->id,
        ]);

        $this->call([
            ClientTypeSeeder::class,
            AddressTypeSeeder::class,
            CompanySeeder::class,
            //     FacebookLeadAcquisitionSeeder::class,
            //     LeadAcquisitionProcessSeeder::class,
            //     LeadCessionWorkflowSeeder::class,
            //     LeadReturnLogSeeder::class,
            //     LeadTransferProcessSeeder::class,
            //     LeadTransferSeeder::class,
            //     ListSanitizationRPOProcessSeeder::class,
            //     OptOutManagementProcessSeeder::class,
            //     OutboundCallArt14ProcessSeeder::class,
            //     VocalOrderProcessSeeder::class,
        ]);
    }
}
