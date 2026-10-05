<?php

namespace App\Console\Commands;

use App\Services\ExcelImportService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('import:companies {file=ReportClienti.xls}')]
#[Description('Import companies from Excel file')]
class ImportCompaniesCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(ExcelImportService $importService)
    {
        $fileName = $this->argument('file');
        $filePath = storage_path("app/private/{$fileName}");

        if (!file_exists($filePath)) {
            $this->error("File non trovato: {$filePath}");
            return 1;
        }

        $this->info("Inizio importazione aziende da: {$fileName}");

        $result = $importService->importCompanies($filePath);

        if ($result['success']) {
            $this->info('Importazione completata con successo!');
            $this->info("Aziende importate: {$result['imported']}");
            $this->info("Aziende saltate: {$result['skipped']}");

            if (!empty($result['errors'])) {
                $this->warn('Errori riscontrati:');
                foreach ($result['errors'] as $error) {
                    $this->line("  - {$error}");
                }
            }

            return 0;
        } else {
            $this->error("Errore durante l'importazione: {$result['error']}");

            if (!empty($result['errors'])) {
                $this->warn('Errori aggiuntivi:');
                foreach ($result['errors'] as $error) {
                    $this->line("  - {$error}");
                }
            }

            return 1;
        }
    }
}
