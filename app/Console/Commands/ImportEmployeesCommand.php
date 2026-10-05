<?php

namespace App\Console\Commands;

use App\Services\EmployeeImportService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('import:employees {file? : The Excel file to import (default: dipendenti No&mi Gennaio 2026 (1).xlsx)}')]
#[Description('Import employees from Excel file')]
class ImportEmployeesCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(EmployeeImportService $importService)
    {
        $filename = $this->argument('file') ?? 'dipendenti No&mi Gennaio 2026 (1).xlsx';
        $filePath = storage_path("app/private/{$filename}");

        $this->info("Starting employee import from: {$filename}");

        // Check if file exists
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        // Get file info
        $fileInfo = $importService->getFileInfo($filePath);
        $this->info("File size: {$fileInfo['size_formatted']}");
        $this->info("Last modified: {$fileInfo['modified']}");
        $this->info("File type: {$fileInfo['type']}");

        // Validate file format
        if (!$importService->validateFile($filePath)) {
            $this->error('Invalid file format. Please provide a valid Excel file.');
            return 1;
        }

        try {
            // Perform import
            $this->info('Importing employees...');
            $results = $importService->importFromFile($filePath);

            // Display results
            $this->displayResults($results);

            return 0;
        } catch (\Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Display import results
     */
    protected function displayResults(array $results): void
    {
        $this->newLine();
        $this->info('=== Import Results ===');

        if ($results['imported'] > 0) {
            $this->info('✅ Import completed successfully!');
        } else {
            $this->warn('⚠️  Import completed with issues.');
        }

        $totalProcessed = $results['imported'] + $results['skipped'];

        $this->info('📊 Statistics:');
        $this->info("   - Total processed: {$totalProcessed}");
        $this->info("   - Imported: {$results['imported']}");
        $this->info("   - Skipped: {$results['skipped']}");

        if (!empty($results['errors'])) {
            $this->newLine();
            $this->error('❌ Errors found:');
            foreach ($results['errors'] as $error) {
                $this->error("   - {$error}");
            }
        }

        $this->newLine();
        $this->info('Import process completed.');
    }
}
