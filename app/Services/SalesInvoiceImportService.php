<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Company;
use App\Models\Registration;
use App\Models\SalesInvoice;
use App\Services\ExcelImportHelpers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ZipArchive;

class SalesInvoiceImportService
{
    protected array $errors = [];
    protected int $imported = 0;
    protected int $skipped = 0;

    /**
     * Import sales invoices from Excel or ZIP file for a specific company
     */
    public function importSalesInvoices(string $filePath, string $companyId): array
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

                // Validate row structure
                if (!is_array($row)) {
                    $this->errors[] = "Riga {$rowIndex}: Dati riga non validi - formato non corretto";
                    $this->skipped++;
                    continue;
                }

                if (count($row) < 24) {  // Expected 24 columns (0-23)
                    $this->errors[] = "Riga {$rowIndex}: Numero colonne insufficiente - richieste 24, trovate " . count($row);
                    $this->skipped++;
                    continue;
                }

                if (empty($row[0]) && empty($row[7])) {
                    $this->skipped++;
                    continue;
                }

                $this->importSalesInvoiceRow($row, $rowIndex, $company);
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
            Log::error('Sales invoice import error: ' . $e->getMessage());

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
     * Import a single sales invoice row
     */
    protected function importSalesInvoiceRow(array $row, int $rowIndex, Company $company): void
    {
        try {
            // Map Excel columns to database fields
            $data = [
                'numero' => ExcelImportHelpers::cleanString($row[0] ?? ''),
                'nome_file' => ExcelImportHelpers::cleanString($row[1] ?? ''),
                'id_sdi' => ExcelImportHelpers::cleanString($row[2] ?? ''),
                'data_invio' => ExcelImportHelpers::parseDate($row[3] ?? ''),
                'data_documento' => ExcelImportHelpers::parseDate($row[4] ?? ''),
                'tipo_documento' => ExcelImportHelpers::cleanString($row[5] ?? ''),
                'tipo_cliente' => ExcelImportHelpers::cleanString($row[6] ?? ''),
                'cliente' => ExcelImportHelpers::cleanString($row[7] ?? ''),
                'partita_iva' => ExcelImportHelpers::cleanVatNumber($row[8] ?? ''),
                'codice_fiscale' => ExcelImportHelpers::cleanFiscalCode($row[9] ?? ''),
                'indirizzo_telematico' => ExcelImportHelpers::cleanString($row[10] ?? ''),
                'metodo_pagamento' => ExcelImportHelpers::cleanString($row[11] ?? ''),
                'totale_imponibile' => ExcelImportHelpers::parseAmount($row[12] ?? ''),
                'totale_escluso_iva_n1' => ExcelImportHelpers::parseAmount($row[13] ?? ''),
                'totale_non_soggetto_iva_n2' => ExcelImportHelpers::parseAmount($row[14] ?? ''),
                'totale_non_imponibile_iva_n3' => ExcelImportHelpers::parseAmount($row[15] ?? ''),
                'totale_esente_iva_n4' => ExcelImportHelpers::parseAmount($row[16] ?? ''),
                'totale_regime_margine_iva_n5' => ExcelImportHelpers::parseAmount($row[17] ?? ''),
                'totale_inversione_contabile_n6' => ExcelImportHelpers::parseAmount($row[18] ?? ''),
                'totale_iva_assolta_altro_stato_ue_n7' => ExcelImportHelpers::parseAmount($row[19] ?? ''),
                'totale_iva' => ExcelImportHelpers::parseAmount($row[20] ?? ''),
                'totale_documento' => ExcelImportHelpers::parseAmount($row[21] ?? ''),
                'netto_a_pagare' => ExcelImportHelpers::parseAmount($row[22] ?? ''),
                'incassi' => ExcelImportHelpers::cleanString($row[23] ?? ''),
                'data_incasso' => ExcelImportHelpers::parseDate($row[24] ?? ''),
                'stato' => ExcelImportHelpers::cleanString($row[25] ?? ''),
                'company_id' => $company->id,
            ];

            // Validate required fields
            if (empty($data['numero']) || empty($data['cliente'])) {
                $this->errors[] = "Riga {$rowIndex}: Numero e Cliente sono obbligatori";
                return;
            }

            // Check if invoice already exists
            $existingInvoice = SalesInvoice::where('numero', $data['numero'])
                ->where('company_id', $company->id)
                ->first();

            if ($existingInvoice) {
                $this->skipped++;
                return;
            }

            // Create the sales invoice
            SalesInvoice::create($data);
            $this->imported++;
        } catch (\Exception $e) {
            $this->errors[] = "Riga {$rowIndex}: Errore durante l'importazione - " . $e->getMessage();
            Log::error("Sales invoice import error on row {$rowIndex}: " . $e->getMessage());
        }
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
                'Numero', 'Nome file', 'ID SdI', 'Data invio', 'Data documento', 'Tipo documento', 'Tipo cliente',
                'Cliente', 'P.IVA', 'Codice Fiscale', 'Indirizzo telematico', 'Metodo di pagamento',
                'Totale imponibile', 'Totale escluso IVA (N1)', 'Totale non soggetto IVA (N2)',
                'Totale non imponibile IVA (N3)', 'Totale esente IVA (N4)',
                'Totale regime del margine/IVA non esposta (N5)', 'Totale inversione contabile (N6)',
                'Totale importo assoggettato ad IVA assolta in altro stato UE (N7)', 'Totale IVA',
                'Totale documento', 'Netto a pagare', 'Incassi', 'Data incasso', 'Stato'
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
