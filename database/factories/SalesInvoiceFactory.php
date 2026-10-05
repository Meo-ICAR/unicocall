<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\SalesInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesInvoice>
 */
class SalesInvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $company = Company::inRandomOrder()->first() ?? Company::first();
        $clientNames = [
            "Phoenix2Value Società' a Responsabilità' Limitata Semplificata",
            'Thunder S.R.L.',
            "MR Società' a Responsabilità' Limitata Semplificata",
            "Team2Com - Società' a Responsabilità' Limitata Semplificata",
            'Digital Solutions S.R.L.',
            'Innovation Hub S.R.L.',
            'Tech Services S.R.L.',
            'Business Consulting S.R.L.',
        ];

        $partitaIva = $this->faker->numerify('###########');
        $totaleImponibile = $this->faker->randomFloat(2, 100, 10000);
        $ivaAmount = $totaleImponibile * 0.22;  // 22% IVA
        $totaleDocumento = $totaleImponibile + $ivaAmount;

        return [
            'numero' => 'FPR ' . $this->faker->numberBetween(1, 100) . '/' . date('y'),
            'nome_file' => 'IT' . $company->vat_number . 'A' . date('Y') . '_' . $this->faker->lexify('????') . '.xml.p7m',
            'id_sdi' => $this->faker->numerify('###########'),
            'data_invio' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'data_documento' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'tipo_documento' => 'Fattura - TD01',
            'tipo_cliente' => $this->faker->randomElement(['Privato', 'Azienda']),
            'cliente' => $this->faker->randomElement($clientNames),
            'partita_iva' => $partitaIva,
            'codice_fiscale' => $partitaIva,
            'indirizzo_telematico' => $this->faker->lexify('??????'),
            'metodo_pagamento' => 'MP05 - Bonifico',
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
            'incassi' => $this->faker->randomElement(['Incassata', 'Non incassata']),
            'data_incasso' => $this->faker->optional(0.3)->dateTimeBetween('-3 months', 'now'),
            'stato' => 'Consegnata',
            'company_id' => $company->id,
        ];
    }

    /**
     * Create a paid invoice
     */
    public function paid(): static
    {
        return $this->state(fn(array $attributes) => [
            'incassi' => 'Incassata',
            'data_incasso' => $this->faker->dateTimeBetween($attributes['data_documento'], 'now'),
        ]);
    }

    /**
     * Create an unpaid invoice
     */
    public function unpaid(): static
    {
        return $this->state(fn(array $attributes) => [
            'incassi' => 'Non incassata',
            'data_incasso' => null,
        ]);
    }

    /**
     * Create a recent invoice
     */
    public function recent(): static
    {
        return $this->state(fn(array $attributes) => [
            'data_documento' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'data_invio' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Create an old invoice
     */
    public function old(): static
    {
        return $this->state(fn(array $attributes) => [
            'data_documento' => $this->faker->dateTimeBetween('-6 months', '-3 months'),
            'data_invio' => $this->faker->dateTimeBetween('-6 months', '-3 months'),
        ]);
    }
}
