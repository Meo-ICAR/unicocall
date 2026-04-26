<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Website;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class Phoenix2ValueWebsiteSeeder extends Seeder
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

        $websites = [
            [
                'name' => 'AI Clienti',
                'domain' => 'aiclienti.com',
                'type' => 'Lead Generation',
                'is_active' => true,
                'is_typical' => true,
                'is_footercompilant' => true,
                'privacy_date' => Carbon::now()->subMonths(6),
                'transparency_date' => Carbon::now()->subMonths(6),
                'url_privacy' => 'https://aiclienti.com/privacy',
                'url_cookies' => 'https://aiclienti.com/cookies',
                'url_transparency' => 'https://aiclienti.com/transparency',
                'company_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Confronta Facile',
                'domain' => 'confronta-facile.com',
                'type' => 'Comparison Platform',
                'is_active' => true,
                'is_typical' => true,
                'is_footercompilant' => true,
                'privacy_date' => Carbon::now()->subMonths(3),
                'transparency_date' => Carbon::now()->subMonths(3),
                'url_privacy' => 'https://confronta-facile.com/privacy',
                'url_cookies' => 'https://confronta-facile.com/cookies',
                'url_transparency' => 'https://confronta-facile.com/transparency',
                'company_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Facile Facile',
                'domain' => 'facilefacile.net',
                'type' => 'Services Portal',
                'is_active' => true,
                'is_typical' => true,
                'is_footercompilant' => true,
                'privacy_date' => Carbon::now()->subMonths(4),
                'transparency_date' => Carbon::now()->subMonths(4),
                'url_privacy' => 'https://www.facilefacile.net/privacy',
                'url_cookies' => 'https://www.facilefacile.net/cookies',
                'url_transparency' => 'https://www.facilefacile.net/transparency',
                'company_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Energie Rinnovabili Casa',
                'domain' => 'energierinnovabili.casa',
                'type' => 'Energy Portal',
                'is_active' => true,
                'is_typical' => true,
                'is_footercompilant' => true,
                'privacy_date' => Carbon::now()->subMonths(2),
                'transparency_date' => Carbon::now()->subMonths(2),
                'url_privacy' => 'https://www.energierinnovabili.casa/privacy',
                'url_cookies' => 'https://www.energierinnovabili.casa/cookies',
                'url_transparency' => 'https://www.energierinnovabili.casa/transparency',
                'company_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Fotovoltaico Per Tutti',
                'domain' => 'fotovoltaicopertutti.org',
                'type' => 'Solar Energy Portal',
                'is_active' => true,
                'is_typical' => true,
                'is_footercompilant' => true,
                'privacy_date' => Carbon::now()->subMonths(5),
                'transparency_date' => Carbon::now()->subMonths(5),
                'url_privacy' => 'https://www.fotovoltaicopertutti.org/privacy',
                'url_cookies' => 'https://www.fotovoltaicopertutti.org/cookies',
                'url_transparency' => 'https://www.fotovoltaicopertutti.org/transparency',
                'company_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Solar Panel Italia',
                'domain' => 'solarpanelitalia.eu',
                'type' => 'Solar Energy Portal',
                'is_active' => true,
                'is_typical' => true,
                'is_footercompilant' => true,
                'privacy_date' => Carbon::now()->subMonths(1),
                'transparency_date' => Carbon::now()->subMonths(1),
                'url_privacy' => 'https://www.solarpanelitalia.eu/privacy',
                'url_cookies' => 'https://www.solarpanelitalia.eu/cookies',
                'url_transparency' => 'https://www.solarpanelitalia.eu/transparency',
                'company_id' => $phoenix2value->id,
            ],
        ];

        foreach ($websites as $websiteData) {
            $website = Website::create($websiteData);
            $this->command->info("Website created: {$website->name} ({$website->domain}) - ID: {$website->id}");
        }

        $this->command->info(count($websites) . ' Phoenix2Value websites created successfully.');
    }
}
