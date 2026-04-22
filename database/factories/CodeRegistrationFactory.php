<?php

namespace Database\Factories;

use App\Models\CodeRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CodeRegistration>
 */
class CodeRegistrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $registrationCodes = [
            'COGE' => 'Conto Contabilità',
            'IBAN' => 'Estremi conto corrente',
            'PIVA' => 'Partita IVA',
            'CF' => 'Codice Fiscale',
            'REA' => 'Numero REA',
            'CAP_SOC' => 'Capitale Sociale',
            'P_IVA' => 'Partita IVA Estera',
            'EMAIL' => 'Indirizzo Email',
            'PEC' => 'Posta Elettronica Certificata',
            'TEL' => 'Numero di Telefono',
            'FAX' => 'Numero di Fax',
            'WEB' => 'Sito Web',
            'INDIRIZZO' => 'Indirizzo Sede Legale',
            'INDIRIZZO_OP' => 'Indirizzo Sede Operativa',
            'BANCA' => 'Dati Bancari',
            'SWIFT' => 'Codice SWIFT/BIC',
            'ABI' => 'Codice ABI',
            'CAB' => 'Codice CAB',
            'CIN' => 'Codice di Controllo IBAN',
        ];

        $code = $this->faker->unique()->randomElement(array_keys($registrationCodes));

        return [
            'code' => $code,
            'name' => $registrationCodes[$code],
            'is_mandatory' => $this->faker->boolean(80),  // 80% chance of being mandatory
            'codeable_type' => $this->faker->randomElement([
                'App\Models\Client',
                'App\Models\Registration',
                'App\Models\Company',
            ]),
        ];
    }

    /**
     * Create a mandatory code registration
     */
    public function mandatory(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_mandatory' => true,
        ]);
    }

    /**
     * Create an optional code registration
     */
    public function optional(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_mandatory' => false,
        ]);
    }

    /**
     * Create COGE code registration
     */
    public function coge(): static
    {
        return $this->state(fn(array $attributes) => [
            'code' => 'COGE',
            'name' => 'Conto Contabilità',
            'is_mandatory' => true,
            'codeable_type' => 'App\Models\Client',
        ]);
    }

    /**
     * Create IBAN code registration
     */
    public function iban(): static
    {
        return $this->state(fn(array $attributes) => [
            'code' => 'IBAN',
            'name' => 'Estremi conto corrente',
            'is_mandatory' => true,
            'codeable_type' => 'App\Models\Client',
        ]);
    }
}
