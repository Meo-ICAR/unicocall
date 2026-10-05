<?php

namespace Database\Seeders;

use App\Models\Dpia;
use App\Models\DpiaItem;
use App\Models\RegistroTrattamentiItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * DPIA — List Provider & Lead Generation (V3)
 * Documento: DPIA-Analisi-Rischi-ListProvider-V3.md
 * Tenant: Phoenix2Value srls
 */
class DpiaListProviderPhoenix2ValueSeeder extends Seeder
{
    private const COMPANY_ID = '019dcae1-4ac7-7086-bda3-6cba86aa74b3'; // Phoenix2Value srls

    public function run(): void
    {
        // ── 1. Registro Trattamenti Item ─────────────────────────────────────
        // Bypass booted() che richiede utente autenticato — insert diretto su mariadb
        $existing = RegistroTrattamentiItem::where('company_id', self::COMPANY_ID)
            ->where('Attivita', 'Acquisizione Lead da List Provider e Social Lead Generation')
            ->first();

        if (!$existing) {
            DB::connection('mariadb')->table('registro_trattamenti_items')->insert([
                'company_id'    => self::COMPANY_ID,
                'Attivita'      => 'Acquisizione Lead da List Provider e Social Lead Generation',
                'Finalita'      => 'Acquisizione di database nominativi da fornitori esterni e generazione diretta di lead tramite campagne social (Facebook/Instagram/LinkedIn Lead Ads) per attività di telemarketing e consulenza finanziaria.',
                'Interessati'   => 'Potenziali clienti (prospect) — persone fisiche che hanno espresso interesse tramite form social o sono presenti in liste di terze parti.',
                'Dati'          => json_encode([
                    'anagrafica', 'telefono', 'email', 'consensi_marketing',
                    'profilo_social', 'metadata_lead_id',
                ]),
                'Giuridica'     => 'Art. 6 par. 1 lett. a — Consenso dell\'interessato; Art. 6 par. 1 lett. f — Legittimo interesse (verifica e due diligence sui fornitori).',
                'Destinatari'   => 'CRM interno, Dialer, Bridge software (Zapier/Make), Fornitori list provider, Piattaforme social (Meta, LinkedIn) in qualità di contitolari.',
                'extraEU'       => true,
                'Conservazione' => 'Dati lead attivi: fino a revoca consenso o 24 mesi dall\'ultimo contatto. Lead sui server social: cancellazione automatica entro 90 giorni dalla raccolta.',
                'Sicurezza'     => 'Crittografia in transito (TLS 1.2+); MFA su bridge software; Nomina responsabili esterni (integratori); Sanitizzazione lead; Verifica blacklist interna; Clausole SCC con fornitori extra-UE.',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        $registroItem = RegistroTrattamentiItem::where('company_id', self::COMPANY_ID)
            ->where('Attivita', 'Acquisizione Lead da List Provider e Social Lead Generation')
            ->firstOrFail();

        // ── 2. Registro Trattamenti (testata) ────────────────────────────────
        $registroExists = DB::connection('mariadb')
            ->table('registro_trattamentis')
            ->where('company_id', self::COMPANY_ID)
            ->exists();

        if (!$registroExists) {
            DB::connection('mariadb')->table('registro_trattamentis')->insert([
                'company_id'  => self::COMPANY_ID,
                'name'        => 'Registro Trattamenti Phoenix2Value srls',
                'approved_at' => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // ── 3. DPIA principale ───────────────────────────────────────────────
        $dpia = Dpia::create([
            'company_id'                  => self::COMPANY_ID,
            'registro_trattamenti_item_id' => $registroItem->id,
            'name'                        => 'DPIA — List Provider & Lead Generation (V3)',
            'description_of_processing'   =>
                "Il trattamento consiste nell'acquisizione di database nominativi da fornitori esterni (list provider) "
                . "e nella generazione diretta di lead tramite asset proprietari e campagne social (Lead Ads su Facebook, Instagram, LinkedIn). "
                . "L'attività coinvolge il trattamento massivo di dati di contatto.\n\n"
                . "Categorie dati: anagrafica, telefono, email, consensi, profilo social, metadata Lead ID.\n"
                . "Tecnologie: API, Social Lead Forms, integratori (Zapier/Make), CRM/Dialer.\n"
                . "Flusso: Social Ads → Form modulo → Bridge Software → CRM → Contatto.",
            'necessity_assessment'        =>
                "Il trattamento è necessario per l'attività core di Phoenix2Value srls (intermediazione e consulenza). "
                . "L'acquisizione di lead da fonti esterne e social è proporzionata alla finalità commerciale, "
                . "a condizione che i consensi alla fonte siano validi e documentati. "
                . "La due diligence preventiva sui list provider e la verifica campionaria dei log di consenso "
                . "garantiscono la proporzionalità del trattamento.",
            'is_necessary'                => true,
            'is_proportional'             => true,
            'status'                      => 'completed',
            'dpo_opinion'                 =>
                "Il DPO ha esaminato il trattamento in data 25/04/2026. "
                . "Si ritiene che le misure adottate (SCC con fornitori extra-UE, nomina responsabili esterni per gli integratori, "
                . "MFA, sanitizzazione lead, retention 90gg sui server social) siano adeguate a ridurre il rischio residuo a livello MEDIO. "
                . "Si raccomanda revisione annuale e monitoraggio continuo della conformità dei list provider.",
            'completion_date'             => '2026-04-25',
            'next_review_date'            => '2027-04-25',
        ]);

        // ── 4. DpiaItems — i 4 scenari di rischio del documento ─────────────
        // Mappatura misure di sicurezza esistenti:
        // id 1 = Crittografia, id 4 = MFA, id 6 = Policy Privacy,
        // id 9 = Procedure Data Breach, id 11 = Gestione Accessi, id 12 = Audit

        $items = [
            [
                // Scenario 1 — Mancata regolamentazione Contitolarità (Meta)
                // Prob 2, Imp 3 → Inerente 6 (Medio)
                'risk_source'                  => 'Mancata regolamentazione Contitolarità con Facebook/Meta per Lead Ads',
                'potential_impact'             => 'Violazione della privacy — assenza di accordo di contitolarità ex Art. 26 GDPR; sanzioni Garante; danno reputazionale',
                'probability'                  => 2,
                'severity'                     => 3,
                'inherent_risk_score'          => 6,
                'privacy_security_id'          => 6,  // Policy di Privacy
                'residual_risk_score'          => 4,
                // Misure: presa visione Terms for Lead Ads Meta; link privacy nel form
            ],
            [
                // Scenario 2 — Trasferimento Dati Extra-UE (server USA)
                // Prob 3, Imp 3 → Inerente 9 (Alto)
                'risk_source'                  => 'Trasferimento Dati Extra-UE — server social e integratori localizzati in USA',
                'potential_impact'             => 'Violazione della compliance — trasferimento senza garanzie adeguate ex Art. 44-49 GDPR; sanzioni fino al 4% fatturato globale',
                'probability'                  => 3,
                'severity'                     => 3,
                'inherent_risk_score'          => 9,
                'privacy_security_id'          => 1,  // Crittografia dei Dati
                'residual_risk_score'          => 6,
                // Misure: verifica Data Privacy Framework; SCC con integratori terzi
            ],
            [
                // Scenario 3 — Intercettazione dati via Bridge Software (Zapier/Make)
                // Prob 2, Imp 4 → Inerente 8 (Alto)
                'risk_source'                  => 'Intercettazione dati in transito via Bridge Software (Zapier/Make)',
                'potential_impact'             => 'Perdita di riservatezza — accesso non autorizzato ai dati lead durante il transito; violazione integrità del flusso CRM',
                'probability'                  => 2,
                'severity'                     => 4,
                'inherent_risk_score'          => 8,
                'privacy_security_id'          => 4,  // Autenticazione a Due Fattori (MFA)
                'residual_risk_score'          => 4,
                // Misure: nomina responsabile esterno integratore; MFA; crittografia log transito
            ],
            [
                // Scenario 4 — Inidoneità del Consenso alla fonte (liste terze)
                // Prob 3, Imp 4 → Inerente 12 (Critico)
                'risk_source'                  => 'Inidoneità del Consenso alla fonte — liste di terze parti con consensi non validi o scaduti',
                'potential_impact'             => 'Violazione della compliance — trattamento senza base giuridica valida ex Art. 6 GDPR; rischio sanzioni Garante e contenzioso con interessati',
                'probability'                  => 3,
                'severity'                     => 4,
                'inherent_risk_score'          => 12,
                'privacy_security_id'          => 12, // Audit di Sicurezza
                'residual_risk_score'          => 6,
                // Misure: due diligence preventiva sui list provider; verifica campionaria log consensi
            ],
        ];

        foreach ($items as $item) {
            DpiaItem::create(array_merge($item, ['dpia_id' => $dpia->id]));
        }

        $this->command->info('✅ DPIA List Provider Phoenix2Value creata:');
        $this->command->info("   DPIA id: {$dpia->id}");
        $this->command->info("   RegistroTrattamentiItem id: {$registroItem->id}");
        $this->command->info('   Scenari di rischio: ' . count($items));
        $this->command->info('   Rischio residuo finale: MEDIO');
    }
}
