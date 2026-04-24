<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\AddressType;
use App\Models\Client;
use App\Models\ClientType;
use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Get companies and client types
            $hassisto = Company::where('name', 'Hassisto Srl')->first();
            $innovatech = Company::where('name', 'Innovatech Holdings Ltd')->first();
            $datlia = Company::where('name', 'DATALIA SRL')->first();

            if (!$hassisto || !$innovatech || !$datlia) {
                $this->command->error('Companies not found. Please run CompanySeeder first.');
                return;
            }

            $clientTypes = ClientType::all()->keyBy('name');
            $addressTypes = AddressType::all()->keyBy('name');

            // Create Innovatech Holdings Ltd as a client for Hassisto
            $innovatechClient = Client::create([
                'company_id' => $hassisto->id,
                'name' => 'Innovatech Holdings Ltd',
                'is_person' => false,
                'is_client' => true,
                'is_lead' => false,
                'vat_number' => 'GB361144034',
                'client_type_id' => $clientTypes['Azienda']->id ?? null,
                'email' => 'info@innovatech.co.uk',
                'phone' => '+44 20 7946 0958',
                'is_approved' => true,
                'status' => 'cliente_attivo',
            ]);

            // Create address for Innovatech
            Address::create([
                'addressable_type' => Client::class,
                'addressable_id' => $innovatechClient->id,
                'address_type_id' => $addressTypes['Sede Legale']->id ?? null,
                'street' => '124 City Road',
                'city' => 'London',
                'zip_code' => 'EC1V 2NX',
                'country' => 'England',
                'company_id' => $hassisto->id,
            ]);

            $this->command->info("Client created: {$innovatechClient->name} (ID: {$innovatechClient->id})");

            // Create DATALIA SRL as a client for Hassisto
            $datliaClient = Client::create([
                'company_id' => $hassisto->id,
                'name' => 'DATALIA SRL',
                'is_person' => false,
                'is_client' => true,
                'is_lead' => false,
                'vat_number' => '05993670651',
                'client_type_id' => $clientTypes['Azienda']->id ?? null,
                'email' => 'datalia@pec.it',
                'phone' => '+39 06 12345678',
                'is_approved' => true,
                'status' => 'cliente_attivo',
            ]);

            // Create legal address for DATALIA SRL (Sede legale)
            Address::create([
                'addressable_type' => Client::class,
                'addressable_id' => $datliaClient->id,
                'address_type_id' => $addressTypes['Sede Legale']->id ?? null,
                'street' => 'Viale Luigi Schiavonetti 270',
                'city' => 'Roma',
                'zip_code' => '00173',
                'country' => 'Italia',
                'company_id' => $hassisto->id,
            ]);

            // Create operational address for DATALIA SRL (Sede operativa)
            Address::create([
                'addressable_type' => Client::class,
                'addressable_id' => $datliaClient->id,
                'address_type_id' => $addressTypes['Sede Operativa']->id ?? null,
                'street' => 'Via Aldo Moro snc Centro "IL GRANAIO"',
                'city' => 'Pontecagnano Faiano',
                'zip_code' => '84098',
                'country' => 'Italia',
                'company_id' => $hassisto->id,
            ]);

            $this->command->info("Client created: {$datliaClient->name} (ID: {$datliaClient->id})");

            // Create sample clients for Innovatech
            $sampleClients = [
                [
                    'name' => 'John Smith',
                    'is_person' => true,
                    'client_type' => 'Privato',
                    'email' => 'john.smith@email.com',
                    'phone' => '+44 77 1234 5678',
                    'tax_code' => 'SM123456789',
                ],
                [
                    'name' => 'TechStart Solutions Ltd',
                    'is_person' => false,
                    'client_type' => 'PMI',
                    'email' => 'contact@techstart.com',
                    'phone' => '+44 20 8123 4567',
                    'vat_number' => 'GB987654321',
                ],
                [
                    'name' => 'Sarah Johnson',
                    'is_person' => true,
                    'client_type' => 'Professionista',
                    'email' => 'sarah.j@consulting.com',
                    'phone' => '+44 75 9876 5432',
                    'tax_code' => 'JO987654321',
                ],
                [
                    'name' => 'Digital Marketing Agency',
                    'is_person' => false,
                    'client_type' => 'Azienda',
                    'email' => 'hello@digitalagency.co.uk',
                    'phone' => '+44 20 7123 8901',
                    'vat_number' => 'GB456789123',
                ],
                [
                    'name' => 'Michael Brown',
                    'is_person' => true,
                    'client_type' => 'Lead',
                    'email' => 'm.brown@email.com',
                    'phone' => '+44 78 2345 6789',
                    'tax_code' => 'BR567890123',
                ],
            ];

            foreach ($sampleClients as $clientData) {
                $client = Client::create([
                    'company_id' => $innovatech->id,
                    'name' => $clientData['name'],
                    'is_person' => $clientData['is_person'],
                    'is_client' => $clientData['client_type'] !== 'Lead',
                    'is_lead' => $clientData['client_type'] === 'Lead',
                    'client_type_id' => $clientTypes[$clientData['client_type']]->id ?? null,
                    'email' => $clientData['email'],
                    'phone' => $clientData['phone'],
                    'tax_code' => $clientData['tax_code'] ?? null,
                    'vat_number' => $clientData['vat_number'] ?? null,
                    'is_approved' => true,
                    'status' => $clientData['client_type'] === 'Lead' ? 'nuovo_lead' : 'cliente_attivo',
                ]);

                $this->command->info("Client created: {$client->name} (ID: {$client->id})");
            }

            $this->command->info('ClientSeeder completed successfully!');
        } catch (\Exception $e) {
            Log::error('Error in ClientSeeder: ' . $e->getMessage());
            $this->command->error('Error in ClientSeeder: ' . $e->getMessage());
        }
    }
}
