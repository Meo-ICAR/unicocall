<?php

namespace App\Console\Commands;

use App\Services\SalesInvoiceImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportSalesInvoicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:sales-invoices {company_id} {--file=20260422_ReportFattureInviate.xls}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa fatture emesse da file Excel o ZIP per una specifica azienda';

    /**
     * Execute the console command.
     */
    public function handle(SalesInvoiceImportService $importService): int
    {
        $companyId = $this->argument('company_id');
        $fileName = $this->option('file');

        $filePath = storage_path("app/private/{$fileName}");

        $this->info("Inizio importazione fatture emesse per azienda ID: {$companyId}");
        $this->info("File: {$filePath}");

        // Check if file exists
        if (!file_exists($filePath)) {
            $this->error("File non trovato: {$filePath}");
            return Command::FAILURE;
        }

        // Detect file type
        $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $fileType = match ($fileExtension) {
            'zip' => 'ZIP (compresso)',
            'xls', 'xlsx' => 'Excel',
            default => 'Sconosciuto'
        };

        $this->info("Tipo file rilevato: {$fileType}");

        // Validate file structure
        $this->info('Validazione struttura file...');
        $validation = $importService->validateFileStructure($filePath);

        if (!$validation['valid']) {
            $this->error('Struttura file non valida!');
            if (isset($validation['error'])) {
                $this->error('Errore: ' . $validation['error']);
            }
            if (!empty($validation['missing_headers'])) {
                $this->error('Intestazioni mancanti: ' . implode(', ', $validation['missing_headers']));
            }
            return Command::FAILURE;
        }

        $this->info('Struttura file valida. Inizio importazione...');

        // Import the invoices
        $result = $importService->importSalesInvoices($filePath, $companyId);

        // Display results
        $this->newLine();
        $this->info('=== RIEPILOGO IMPORTAZIONE ===');

        if ($result['success']) {
            $this->info('Importazione completata con successo!');
            $this->info("Fatture importate: {$result['imported']}");
            $this->info("Fatture saltate: {$result['skipped']}");

            if (!empty($result['errors'])) {
                $this->newLine();
                $this->error('Errori riscontrati:');
                foreach ($result['errors'] as $error) {
                    $this->error("  - {$error}");
                }
            }
        } else {
            $this->error('Importazione fallita!');
            $this->error('Errore: ' . $result['error']);

            if (!empty($result['errors'])) {
                $this->newLine();
                $this->error('Errori aggiuntivi:');
                foreach ($result['errors'] as $error) {
                    $this->error("  - {$error}");
                }
            }

            return Command::FAILURE;
        }

        $this->newLine();
        $this->info('Importazione completata!');

        // Log the import
        Log::info('Sales invoices import completed', [
            'company_id' => $companyId,
            'file' => $fileName,
            'imported' => $result['imported'],
            'skipped' => $result['skipped'],
            'errors_count' => count($result['errors']),
        ]);

        return Command::SUCCESS;
    }
}
