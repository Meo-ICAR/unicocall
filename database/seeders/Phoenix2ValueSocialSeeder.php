<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Website;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class Phoenix2ValueSocialSeeder extends Seeder
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

        $socialProfiles = [
            // Facebook profiles
            [
                'name' => 'AI Clienti - Facebook',
                'domain' => 'facebook.com/AIclienti',
                'type' => 'Facebook Page',
                'is_active' => true,
                'is_typical' => false,
                'is_footercompilant' => true,
                'privacy_date' => Carbon::now()->subMonths(3),
                'transparency_date' => Carbon::now()->subMonths(3),
                'url_privacy' => 'https://www.facebook.com/AIclienti/about_contact_and_basic_info',
                'url_cookies' => 'https://www.facebook.com/AIclienti/cookies',
                'url_transparency' => 'https://www.facebook.com/AIclienti/directory_privacy_and_legal_info',
                'company_id' => $phoenix2value->id,
                'websiteable_type' => 'App\Models\Company',
                'websiteable_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Confronta Facile - Facebook',
                'domain' => 'facebook.com/confrontafacile',
                'type' => 'Facebook Page',
                'is_active' => true,
                'is_typical' => false,
                'is_footercompilant' => true,
                'privacy_date' => Carbon::now()->subMonths(2),
                'transparency_date' => Carbon::now()->subMonths(2),
                'url_privacy' => 'https://www.facebook.com/confrontafacile/about_contact_and_basic_info',
                'url_cookies' => 'https://www.facebook.com/confrontafacile/cookies',
                'url_transparency' => 'https://www.facebook.com/confrontafacile/transparency',
                'company_id' => $phoenix2value->id,
                'websiteable_type' => 'App\Models\Company',
                'websiteable_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Energy Group - Facebook',
                'domain' => 'facebook.com/people/Energy-Group/61577830200032',
                'type' => 'Facebook Profile',
                'is_active' => true,
                'is_typical' => false,
                'is_footercompilant' => true,
                'privacy_date' => Carbon::now()->subMonths(4),
                'transparency_date' => Carbon::now()->subMonths(4),
                'url_privacy' => 'https://www.facebook.com/people/Energy-Group/61577830200032/about_contact_and_basic_info',
                'url_cookies' => 'https://www.facebook.com/people/Energy-Group/61577830200032/cookies',
                'url_transparency' => 'https://www.facebook.com/people/Energy-Group/61577830200032/transparency',
                'company_id' => $phoenix2value->id,
                'websiteable_type' => 'App\Models\Company',
                'websiteable_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Facile Facile - Facebook',
                'domain' => 'facebook.com/people/Facile-Facile/100088832344228',
                'type' => 'Facebook Profile',
                'is_active' => true,
                'is_typical' => false,
                'is_footercompilant' => true,
                'privacy_date' => Carbon::now()->subMonths(3),
                'transparency_date' => Carbon::now()->subMonths(3),
                'url_privacy' => 'https://www.facebook.com/people/Facile-Facile/100088832344228/about_contact_and_basic_info',
                'url_cookies' => 'https://www.facebook.com/people/Facile-Facile/100088832344228/cookies',
                'url_transparency' => 'https://www.facebook.com/people/Facile-Facile/100088832344228/transparency',
                'company_id' => $phoenix2value->id,
                'websiteable_type' => 'App\Models\Company',
                'websiteable_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Solar Energy Profile 1 - Facebook',
                'domain' => 'facebook.com/profile.php?id=61566146080129',
                'type' => 'Facebook Profile',
                'is_active' => true,
                'is_typical' => false,
                'is_footercompilant' => true,
                'privacy_date' => Carbon::now()->subMonths(2),
                'transparency_date' => Carbon::now()->subMonths(2),
                'url_privacy' => 'https://www.facebook.com/profile.php?id=61566146080129/about_contact_and_basic_info',
                'url_cookies' => 'https://www.facebook.com/profile.php?id=61566146080129/cookies',
                'url_transparency' => 'https://www.facebook.com/profile.php?id=61566146080129/transparency',
                'company_id' => $phoenix2value->id,
                'websiteable_type' => 'App\Models\Company',
                'websiteable_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Solar Energy Profile 2 - Facebook',
                'domain' => 'facebook.com/profile.php?id=100093377842284',
                'type' => 'Facebook Profile',
                'is_active' => true,
                'is_typical' => false,
                'is_footercompilant' => true,
                'privacy_date' => Carbon::now()->subMonths(1),
                'transparency_date' => Carbon::now()->subMonths(1),
                'url_privacy' => 'https://www.facebook.com/profile.php?id=100093377842284/about_contact_and_basic_info',
                'url_cookies' => 'https://www.facebook.com/profile.php?id=100093377842284/cookies',
                'url_transparency' => 'https://www.facebook.com/profile.php?id=100093377842284/transparency',
                'company_id' => $phoenix2value->id,
                'websiteable_type' => 'App\Models\Company',
                'websiteable_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Fotovoltaico Per Tutti - Facebook',
                'domain' => 'facebook.com/fotovoltaicoperteita',
                'type' => 'Facebook Page',
                'is_active' => true,
                'is_typical' => false,
                'is_footercompilant' => true,
                'privacy_date' => Carbon::now()->subMonths(5),
                'transparency_date' => Carbon::now()->subMonths(5),
                'url_privacy' => 'https://www.facebook.com/fotovoltaicoperteita/about_contact_and_basic_info',
                'url_cookies' => 'https://www.facebook.com/fotovoltaicoperteita/cookies',
                'url_transparency' => 'https://www.facebook.com/fotovoltaicoperteita/transparency',
                'company_id' => $phoenix2value->id,
                'websiteable_type' => 'App\Models\Company',
                'websiteable_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Solar Energy Profile 3 - Facebook',
                'domain' => 'facebook.com/profile.php?id=61569332636316',
                'type' => 'Facebook Profile',
                'is_active' => true,
                'is_typical' => false,
                'is_footercompilant' => true,
                'privacy_date' => Carbon::now()->subMonths(2),
                'transparency_date' => Carbon::now()->subMonths(2),
                'url_privacy' => 'https://www.facebook.com/profile.php?id=61569332636316/about_contact_and_basic_info',
                'url_cookies' => 'https://www.facebook.com/profile.php?id=61569332636316/cookies',
                'url_transparency' => 'https://www.facebook.com/profile.php?id=61569332636316/transparency',
                'company_id' => $phoenix2value->id,
                'websiteable_type' => 'App\Models\Company',
                'websiteable_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Risparmio Smart - Facebook',
                'domain' => 'facebook.com/people/RisparmioSmart/61556576282637',
                'type' => 'Facebook Profile',
                'is_active' => true,
                'is_typical' => false,
                'is_footercompilant' => true,
                'privacy_date' => Carbon::now()->subMonths(3),
                'transparency_date' => Carbon::now()->subMonths(3),
                'url_privacy' => 'https://www.facebook.com/people/RisparmioSmart/61556576282637/about_contact_and_basic_info',
                'url_cookies' => 'https://www.facebook.com/people/RisparmioSmart/61556576282637/cookies',
                'url_transparency' => 'https://www.facebook.com/people/RisparmioSmart/61556576282637/transparency',
                'company_id' => $phoenix2value->id,
                'websiteable_type' => 'App\Models\Company',
                'websiteable_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Solar Energy Profile 4 - Facebook',
                'domain' => 'facebook.com/profile.php?id=61556097671155',
                'type' => 'Facebook Profile',
                'is_active' => true,
                'is_typical' => false,
                'is_footercompilant' => true,
                'privacy_date' => Carbon::now()->subMonths(1),
                'transparency_date' => Carbon::now()->subMonths(1),
                'url_privacy' => 'https://www.facebook.com/profile.php?id=61556097671155/about_contact_and_basic_info',
                'url_cookies' => 'https://www.facebook.com/profile.php?id=61556097671155/cookies',
                'url_transparency' => 'https://www.facebook.com/profile.php?id=61556097671155/transparency',
                'company_id' => $phoenix2value->id,
                'websiteable_type' => 'App\Models\Company',
                'websiteable_id' => $phoenix2value->id,
            ],
            // Instagram profiles
            [
                'name' => 'AI Clienti - Instagram',
                'domain' => 'instagram.com/aiclienti',
                'type' => 'Instagram Profile',
                'is_active' => true,
                'is_typical' => false,
                'is_footercompilant' => true,
                'privacy_date' => Carbon::now()->subMonths(3),
                'transparency_date' => Carbon::now()->subMonths(3),
                'url_privacy' => 'https://www.instagram.com/aiclienti/about_contact_and_basic_info',
                'url_cookies' => 'https://www.instagram.com/aiclienti/cookies',
                'url_transparency' => 'https://www.instagram.com/aiclienti/transparency',
                'company_id' => $phoenix2value->id,
                'websiteable_type' => 'App\Models\Company',
                'websiteable_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Confronta Facile - Instagram',
                'domain' => 'instagram.com/confronta_facile',
                'type' => 'Instagram Profile',
                'is_active' => true,
                'is_typical' => false,
                'is_footercompilant' => true,
                'privacy_date' => Carbon::now()->subMonths(2),
                'transparency_date' => Carbon::now()->subMonths(2),
                'url_privacy' => 'https://www.instagram.com/confronta_facile/about_contact_and_basic_info',
                'url_cookies' => 'https://www.instagram.com/confronta_facile/cookies',
                'url_transparency' => 'https://www.instagram.com/confronta_facile/transparency',
                'company_id' => $phoenix2value->id,
                'websiteable_type' => 'App\Models\Company',
                'websiteable_id' => $phoenix2value->id,
            ],
        ];

        foreach ($socialProfiles as $profileData) {
            $profile = Website::create($profileData);
            $this->command->info("Social profile created: {$profile->name} ({$profile->domain}) - ID: {$profile->id}");
        }

        $this->command->info(count($socialProfiles) . ' Phoenix2Value social profiles created successfully.');
    }
}
