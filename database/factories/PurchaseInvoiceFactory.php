<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\PurchaseInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseInvoice>
 */
class PurchaseInvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $company = Company::inRandomOrder()->first() ?? Company::first();
        $fornitori = [
            'Amazon EU S.a r.l., Succursale Italiana',
            'OVH SRL',
            'DOTT. PENNACCHIO NICOLA',
            'COMPAGNIA IMMOBILIARE ALBERGHI SPA',
            'Microsoft Ireland Operations Ltd',
            'Google Ireland Ltd',
            'Telecom Italia S.p.A.',
            'Enel Energia S.p.A.',
            'Vodafone Italia S.p.A.',
        ];

        $partitaIva = $this->faker->numerify('###########');
        $totaleImponibile = $this->faker->randomFloat(2, 50, 5000);
        $ivaAmount = $totaleImponibile * 0.22;  // 22% IVA
        $totaleDocumento = $totaleImponibile + $ivaAmount;

        return [
            'numero' => $this->faker->randomElement(['IT' . $this->faker->numerify('###########'), 'FPR ' . $this->faker->numberBetween(1, 100) . '/' . date('y')]),
            'nome_file' => 'IT' . $partitaIva . '_' . $this->faker->lexify('????') . '.xml',
            'id_sdi' => $this->faker->numerify('###########'),
            'data_ricezione' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'data_documento' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'tipo_documento' => 'Fattura - TD01',
            'fornitore' => $this->faker->randomElement($fornitori),
            'partita_iva' => $partitaIva,
            'codice_fiscale' => $this->faker->optional(0.7)->numerify('###########'),
            'metodo_pagamento' => $this->faker->randomElement(['MP05 - Bonifico', 'MP08 - Carta di pagamento', 'MP12 - RID']),
            'totale_imponibile' => $totaleImponibile,
            'totale_escluso_iva_n1' => 0,
            'totale_non_soggetto_iva_n2' => 0,
            'totale_non_imponibile_iva_n3' => 0,
            'totale_esente_iva_n4' => 0,
            'totale_regime_margine_iva_n5' => 0,
            'totale_inversione_contabile_n6' => 0,
            'totale_iva_assolta_altro_stato_ue_n7' => 0,
            'totale_iva' => $ivaAmount,
            'totale_documento' => $totaleDocumento,
            'netto_a_pagare' => $totaleDocumento,
            'pagamenti' => $this->faker->randomElement(['Pagata', 'Non pagata']),
            'data_pagamento' => $this->faker->optional(0.4)->dateTimeBetween('-3 months', 'now'),
            'stato' => 'Letta',
            'company_id' => $company->id,
        ];
    }

    /**
     * Create a paid invoice
     */
    public function paid(): static
    {
        return $this->state(fn(array $attributes) => [
            'pagamenti' => 'Pagata',
            'data_pagamento' => $this->faker->dateTimeBetween($attributes['data_documento'], 'now'),
        ]);
    }

    /**
     * Create an unpaid invoice
     */
    public function unpaid(): static
    {
        return $this->state(fn(array $attributes) => [
            'pagamenti' => 'Non pagata',
            'data_pagamento' => null,
        ]);
    }

    /**
     * Create a read invoice
     */
    public function read(): static
    {
        return $this->state(fn(array $attributes) => [
            'stato' => 'Letta',
        ]);
    }

    /**
     * Create an unread invoice
     */
    public function unread(): static
    {
        return $this->state(fn(array $attributes) => [
            'stato' => $this->faker->randomElement(['Da leggere', 'In elaborazione']),
        ]);
    }

    /**
     * Create a recent invoice
     */
    public function recent(): static
    {
        return $this->state(fn(array $attributes) => [
            'data_documento' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'data_ricezione' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Create an old invoice
     */
    public function old(): static
    {
        return $this->state(fn(array $attributes) => [
            'data_documento' => $this->faker->dateTimeBetween('-6 months', '-3 months'),
            'data_ricezione' => $this->faker->dateTimeBetween('-6 months', '-3 months'),
        ]);
    }
}
