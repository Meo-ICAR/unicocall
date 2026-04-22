<?php

namespace App\Services;

use App\Models\Address;
use App\Models\AddressType;
use App\Models\Company;
use App\Models\Registration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelImportService
{
    protected array $errors = [];
    protected int $imported = 0;
    protected int $skipped = 0;

    public function importCompanies(string $filePath): array
    {
        $this->errors = [];
        $this->imported = 0;
        $this->skipped = 0;

        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            $dataRows = array_slice($rows, 1);

            foreach ($dataRows as $index => $row) {
                $rowIndex = $index + 2;  // Excel row number (1-based + header)

                if (empty($row[0]) && empty($row[1])) {
                    $this->skipped++;
                    continue;
                }

                $this->importCompanyRow($row, $rowIndex);
            }

            return [
                'success' => true,
                'imported' => $this->imported,
                'skipped' => $this->skipped,
                'errors' => $this->errors,
            ];
        } catch (\Exception $e) {
            Log::error('Excel import error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'imported' => $this->imported,
                'skipped' => $this->skipped,
                'errors' => $this->errors,
            ];
        }
    }

    protected function importCompanyRow(array $row, int $rowIndex): void
    {
        $data = [
            'name' => $this->getValue($row, 0),  // Denominazione
            'vat_number' => $this->getValue($row, 1),  // Partita IVA
            'nazione' => $this->getValue($row, 2),  // Nazione
            'cap' => $this->getValue($row, 3),  // CAP
            'provincia' => $this->getValue($row, 4),  // Provincia
            'comune' => $this->getValue($row, 5),  // Comune
            'indirizzo' => $this->getValue($row, 6),  // Indirizzo
            'numero_civico' => $this->getValue($row, 7),  // Numero civico
            'indirizzo_telematico' => $this->getValue($row, 8),  // Indirizzo telematico
            'email' => $this->getValue($row, 9),  // Email
            'pec' => $this->getValue($row, 10),  // PEC
        ];

        // Validate required fields
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'vat_number' => 'nullable|string|max:13',
        ]);

        if ($validator->fails()) {
            $this->errors[] = "Riga {$rowIndex}: " . implode(', ', $validator->errors()->all());
            $this->skipped++;
            return;
        }

        try {
            \DB::beginTransaction();

            // Check if company already exists by VAT number or name
            $existingCompany = Company::where('vat_number', $data['vat_number'])
                ->orWhere('name', $data['name'])
                ->first();

            if ($existingCompany) {
                $this->errors[] = "Riga {$rowIndex}: Azienda già esistente ({$data['name']} - {$data['vat_number']})";
                $this->skipped++;
                \DB::rollBack();
                return;
            }

            // Create company
            $company = Company::create([
                'name' => $data['name'],
                'vat_number' => $data['vat_number'],
                'company_type' => 'call center',  // Default type
            ]);

            // Create address if any address field is provided
            if (!empty($data['nazione']) ||
                    !empty($data['cap']) ||
                    !empty($data['provincia']) ||
                    !empty($data['comune']) ||
                    !empty($data['indirizzo']) ||
                    !empty($data['numero_civico'])) {
                $this->createAddressForCompany($company, $data, $rowIndex);
            }

            // Create registrations for each type if provided
            $this->createRegistrationsForCompany($company, $data, $rowIndex);

            $this->imported++;
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error("Error importing row {$rowIndex}: " . $e->getMessage());
            $this->errors[] = "Riga {$rowIndex}: Errore durante l'importazione - " . $e->getMessage();
            $this->skipped++;
        }
    }

    protected function createAddressForCompany(Company $company, array $data, int $rowIndex): void
    {
        try {
            // Get Sede Legale address type (ID 5 from seeder)
            $addressType = AddressType::find(5);
            if (!$addressType) {
                $this->errors[] = "Riga {$rowIndex}: Tipo indirizzo 'Sede Legale' non trovato";
                return;
            }

            Address::create([
                'addressable_type' => Company::class,
                'addressable_id' => $company->id,
                'name' => 'Sede Legale',
                'country' => $data['nazione'] ?? null,
                'zip_code' => $data['cap'] ?? null,
                'city' => $data['comune'] ?? null,
                'street' => $data['indirizzo'] ?? null,
                'numero' => $data['numero_civico'] ?? null,
                'address_type_id' => $addressType->id,
            ]);
        } catch (\Exception $e) {
            Log::error("Error creating address for company {$company->id}: " . $e->getMessage());
            $this->errors[] = "Riga {$rowIndex}: Errore creazione indirizzo - " . $e->getMessage();
        }
    }

    protected function createRegistrationsForCompany(Company $company, array $data, int $rowIndex): void
    {
        $registrationTypes = [
            'indirizzo_telematico' => 'Indirizzo Telematico',
            'email' => 'Email',
            'pec' => 'PEC',
        ];

        foreach ($registrationTypes as $field => $typeName) {
            if (!empty($data[$field])) {
                $this->createRegistrationForCompany($company, $data[$field], $typeName, $rowIndex);
            }
        }
    }

    protected function createRegistrationForCompany(Company $company, string $value, string $type, int $rowIndex): void
    {
        try {
            Registration::create([
                'registrable_type' => Company::class,
                'registrable_id' => $company->id,
                'company_id' => $company->id,
                'registration_type' => $type,
                'value' => $value,
                'start_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error("Error creating registration for company {$company->id}: " . $e->getMessage());
            $this->errors[] = "Riga {$rowIndex}: Errore creazione registrazione - " . $e->getMessage();
        }
    }

    protected function parseAddress(string $addressString): array
    {
        $parts = [
            'street' => $addressString,
            'city' => null,
            'zip_code' => null,
        ];

        // Try to extract ZIP code (5 digits)
        if (preg_match('/(\d{5})/', $addressString, $matches)) {
            $parts['zip_code'] = $matches[1];
        }

        // Try to extract city (after ZIP code or at the end)
        if (preg_match('/(\d{5})\s*(.+?)(?:\s*$|,)/', $addressString, $matches)) {
            $parts['city'] = trim($matches[2]);
        } elseif (preg_match('/,\s*([^,]+)$/', $addressString, $matches)) {
            $parts['city'] = trim($matches[1]);
        }

        return $parts;
    }

    protected function getValue(array $row, int $index): ?string
    {
        return isset($row[$index]) ? trim($row[$index]) : null;
    }

    public function getImportSummary(): array
    {
        return [
            'imported' => $this->imported,
            'skipped' => $this->skipped,
            'errors' => $this->errors,
        ];
    }
}
