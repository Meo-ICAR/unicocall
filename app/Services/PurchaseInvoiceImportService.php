<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Company;
use App\Models\PurchaseInvoice;
use App\Models\Registration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ZipArchive;

class PurchaseInvoiceImportService
{
    protected array $errors = [];
    protected int $imported = 0;
    protected int $skipped = 0;

    /**
     * Import purchase invoices from Excel or ZIP file for a specific company
     */
    public function importPurchaseInvoices(string $filePath, string $companyId): array
    {
        $this->errors = [];
        $this->imported = 0;
        $this->skipped = 0;

        try {
            $company = Company::findOrFail($companyId);

            // Handle ZIP files
            if (strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) === 'zip') {
                $excelFilePath = $this->extractZipFile($filePath);
                if (!$excelFilePath) {
                    return [
                        'success' => false,
                        'error' => 'Impossibile estrarre il file ZIP',
                        'imported' => $this->imported,
                        'skipped' => $this->skipped,
                        'errors' => $this->errors,
                    ];
                }
                $filePath = $excelFilePath;
            }

            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            $dataRows = array_slice($rows, 1);

            foreach ($dataRows as $index => $row) {
                $rowIndex = $index + 2;  // Excel row number (1-based + header)

                if (empty($row[0]) && empty($row[6])) {
                    $this->skipped++;
                    continue;
                }

                $this->importPurchaseInvoiceRow($row, $rowIndex, $company);
            }

            // Clean up temporary file if it was extracted from ZIP
            if (isset($excelFilePath) && file_exists($excelFilePath)) {
                unlink($excelFilePath);
            }

            return [
                'success' => true,
                'imported' => $this->imported,
                'skipped' => $this->skipped,
                'errors' => $this->errors,
            ];
        } catch (\Exception $e) {
            Log::error('Purchase invoice import error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'imported' => $this->imported,
                'skipped' => $this->skipped,
                'errors' => $this->errors,
            ];
        }
    }

    /**
     * Import a single purchase invoice row
     */
    protected function importPurchaseInvoiceRow(array $row, int $rowIndex, Company $company): void
    {
        try {
            // Map Excel columns to database fields
            $data = [
                'numero' => $this->cleanString($row[0] ?? ''),
                'nome_file' => $this->cleanString($row[1] ?? ''),
                'id_sdi' => $this->cleanString($row[2] ?? ''),
                'data_ricezione' => $this->parseDate($row[3] ?? ''),
                'data_documento' => $this->parseDate($row[4] ?? ''),
                'tipo_documento' => $this->cleanString($row[5] ?? ''),
                'fornitore' => $this->cleanString($row[6] ?? ''),
                'partita_iva' => $this->cleanVatNumber($row[7] ?? ''),
                'codice_fiscale' => $this->cleanString($row[8] ?? ''),
                'metodo_pagamento' => $this->cleanString($row[9] ?? ''),
                'totale_imponibile' => $this->parseAmount($row[10] ?? ''),
                'totale_escluso_iva_n1' => $this->parseAmount($row[11] ?? ''),
                'totale_non_soggetto_iva_n2' => $this->parseAmount($row[12] ?? ''),
                'totale_non_imponibile_iva_n3' => $this->parseAmount($row[13] ?? ''),
                'totale_esente_iva_n4' => $this->parseAmount($row[14] ?? ''),
                'totale_regime_margine_iva_n5' => $this->parseAmount($row[15] ?? ''),
                'totale_inversione_contabile_n6' => $this->parseAmount($row[16] ?? ''),
                'totale_iva_assolta_altro_stato_ue_n7' => $this->parseAmount($row[17] ?? ''),
                'totale_iva' => $this->parseAmount($row[18] ?? ''),
                'totale_documento' => $this->parseAmount($row[19] ?? ''),
                'netto_a_pagare' => $this->parseAmount($row[20] ?? ''),
                'pagamenti' => $this->cleanString($row[21] ?? ''),
                'data_pagamento' => $this->parseDate($row[22] ?? ''),
                'stato' => $this->cleanString($row[23] ?? ''),
                'company_id' => $company->id,
            ];

            // Validate required fields
            if (empty($data['numero']) || empty($data['fornitore'])) {
                $this->errors[] = "Riga {$rowIndex}: Numero e Fornitore sono obbligatori";
                return;
            }

            // Check if invoice already exists
            $existingInvoice = PurchaseInvoice::where('numero', $data['numero'])
                ->where('company_id', $company->id)
                ->first();

            if ($existingInvoice) {
                $this->skipped++;
                return;
            }

            // Create the purchase invoice
            PurchaseInvoice::create($data);
            $this->imported++;
        } catch (\Exception $e) {
            $this->errors[] = "Riga {$rowIndex}: Errore durante l'importazione - " . $e->getMessage();
            Log::error("Purchase invoice import error on row {$rowIndex}: " . $e->getMessage());
        }
    }

    /**
     * Clean and trim string values
     */
    protected function cleanString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return trim(preg_replace('/\s+/', ' ', $value));
    }

    /**
     * Clean VAT number (remove spaces, dots, etc.)
     */
    protected function cleanVatNumber(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Parse date from Excel format
     */
    protected function parseDate(?string $value): ?\DateTime
    {
        if (empty($value)) {
            return null;
        }

        try {
            // Try Excel serial date format first
            if (is_numeric($value)) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value);
                return $date;
            }

            // Try Italian date format
            $date = \DateTime::createFromFormat('m/d/Y', $value);
            if ($date) {
                return $date;
            }

            // Try other formats
            $date = \DateTime::createFromFormat('d/m/Y', $value);
            if ($date) {
                return $date;
            }

            $date = \DateTime::createFromFormat('Y-m-d', $value);
            if ($date) {
                return $date;
            }

            // Try to create from string
            return new \DateTime($value);
        } catch (\Exception $e) {
            Log::warning("Could not parse date: {$value}");
            return null;
        }
    }

    /**
     * Parse amount from Italian currency format
     */
    protected function parseAmount(?string $value): ?float
    {
        if (empty($value)) {
            return 0.0;
        }

        // Remove currency symbol, spaces, and replace comma with dot
        $cleaned = str_replace(['EUR', 'EUR ', ' ', ''], '', $value);
        $cleaned = str_replace(['.', ','], ['', '.'], $cleaned);

        $amount = (float) $cleaned;

        return $amount;
    }

    /**
     * Extract ZIP file and return path to Excel file
     */
    protected function extractZipFile(string $zipFilePath): ?string
    {
        try {
            $zip = new ZipArchive();

            if ($zip->open($zipFilePath) !== TRUE) {
                $this->errors[] = "Impossibile aprire il file ZIP: {$zipFilePath}";
                return null;
            }

            // Look for Excel files in the ZIP
            $excelFile = null;
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (in_array($extension, ['xls', 'xlsx'])) {
                    $excelFile = $filename;
                    break;
                }
            }

            if (!$excelFile) {
                $this->errors[] = 'Nessun file Excel trovato nel file ZIP';
                $zip->close();
                return null;
            }

            // Extract the Excel file to temporary location
            $tempDir = sys_get_temp_dir();
            $tempFilePath = $tempDir . '/' . uniqid('excel_import_') . '.' . pathinfo($excelFile, PATHINFO_EXTENSION);

            if (!$zip->extractTo($tempDir, $excelFile)) {
                $this->errors[] = 'Impossibile estrarre il file Excel dal ZIP';
                $zip->close();
                return null;
            }

            $zip->close();

            // Move the extracted file to our temporary location
            $extractedFilePath = $tempDir . '/' . basename($excelFile);
            if (file_exists($extractedFilePath)) {
                rename($extractedFilePath, $tempFilePath);
            }

            if (!file_exists($tempFilePath)) {
                $this->errors[] = 'File Excel estratto non trovato';
                return null;
            }

            Log::info("ZIP file extracted successfully: {$zipFilePath} -> {$tempFilePath}");
            return $tempFilePath;
        } catch (\Exception $e) {
            $this->errors[] = "Errore durante l'estrazione del file ZIP: " . $e->getMessage();
            Log::error('ZIP extraction error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get import summary
     */
    public function getImportSummary(): array
    {
        return [
            'imported' => $this->imported,
            'skipped' => $this->skipped,
            'errors' => $this->errors,
            'total_processed' => $this->imported + $this->skipped,
        ];
    }

    /**
     * Validate Excel file structure
     */
    public function validateFileStructure(string $filePath): array
    {
        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $headerRow = $worksheet->toArray()[0];

            $expectedHeaders = [
                'Numero', 'Nome file', 'ID SdI', 'Data ricezione', 'Data documento',
                'Tipo documento', 'Fornitore', 'P.IVA', 'Codice Fiscale', 'Metodo di pagamento',
                'Totale imponibile', 'Totale escluso IVA (N1)', 'Totale non soggetto IVA (N2)',
                'Totale non imponibile IVA (N3)', 'Totale esente IVA (N4)',
                'Totale regime del margine/IVA non esposta (N5)', 'Totale inversione contabile (N6)',
                'Totale importo assoggettato ad IVA assolta in altro stato UE (N7)', 'Totale IVA',
                'Totale documento', 'Netto a pagare', 'Pagamenti', 'Data pagamento', 'Stato'
            ];

            $missingHeaders = array_diff($expectedHeaders, $headerRow);
            $extraHeaders = array_diff($headerRow, $expectedHeaders);

            return [
                'valid' => empty($missingHeaders),
                'missing_headers' => $missingHeaders,
                'extra_headers' => $extraHeaders,
                'found_headers' => $headerRow,
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
