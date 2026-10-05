<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Phoenix2ValueWebmasterClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phoenix2value = Company::where('name', 'Phoenix2Value srls')->first();

        if (!$phoenix2value) {
            $this->command->error('Phoenix2Value company not found. Please run CompanySeeder first.');
            return;
        }

        $webmasterClients = [
            [
                'name' => 'Ferrante',
                'first_name' => 'Francesco',
                'tax_code' => 'FRRFNC85M15H501H',
                'vat_number' => null,
                'email' => 'francesco.ferrante@example.com',
                'phone' => '+39 333 1111111',
                'is_person' => true,
                'is_company_consultant' => true,
                'is_lead' => false,
                'is_client' => true,
                'is_approved' => true,
                'status' => 'cliente_attivo',
                'servizio' => 'Web Master',
                'nomina' => 'Web Content Manager',
                'nomina_at' => '2023-06-15',
                'company_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Serafin',
                'first_name' => 'Simone',
                'tax_code' => 'SRFSMN87T20H501X',
                'vat_number' => null,
                'email' => 'simone.serafin@example.com',
                'phone' => '+39 333 2222222',
                'is_person' => true,
                'is_company_consultant' => true,
                'is_lead' => false,
                'is_client' => true,
                'is_approved' => true,
                'status' => 'cliente_attivo',
                'servizio' => 'Web Master',
                'nomina' => 'Web Developer',
                'nomina_at' => '2023-07-20',
                'company_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Pulcinelli',
                'first_name' => 'Marco',
                'tax_code' => 'PLCMRC82S15H501Z',
                'vat_number' => null,
                'email' => 'marco.pulcinelli@example.com',
                'phone' => '+39 333 3333333',
                'is_person' => true,
                'is_company_consultant' => true,
                'is_lead' => false,
                'is_client' => true,
                'is_approved' => true,
                'status' => 'cliente_attivo',
                'servizio' => 'Web Master',
                'nomina' => 'SEO Specialist',
                'nomina_at' => '2023-08-10',
                'company_id' => $phoenix2value->id,
            ],
        ];

        foreach ($webmasterClients as $clientData) {
            $client = Client::create($clientData);
            $this->command->info("Webmaster client created: {$client->first_name} {$client->name} (ID: {$client->id})");
        }

        $this->command->info(count($webmasterClients) . ' Phoenix2Value webmaster clients created successfully.');
    }
}
