<?php

namespace App\Imports;

use App\Models\Company;
use App\Services\ExcelImportService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Pxlrbt\FilamentExcel\Imports\ExcelImport;

class CompanyImport extends ExcelImport
{
    protected ?string $model = Company::class;

    public function collection(Collection $rows): void
    {
        // Create a temporary Excel file from the collection
        $tempFilePath = $this->createTempExcelFile($rows);

        try {
            // Use the ExcelImportService to handle the import
            $importService = app(ExcelImportService::class);
            $result = $importService->importCompanies($tempFilePath);

            if (!$result['success']) {
                throw ValidationException::withMessages([
                    'import' => $result['error'] ?? "Errore durante l'importazione"
                ]);
            }

            if (!empty($result['errors'])) {
                throw ValidationException::withMessages($result['errors']);
            }

            session()->flash('import_summary', [
                'imported' => $result['imported'],
                'skipped' => $result['skipped'],
                'total' => $rows->count(),
            ]);
        } finally {
            // Clean up temporary file
            if (File::exists($tempFilePath)) {
                File::delete($tempFilePath);
            }
        }
    }

    protected function createTempExcelFile(Collection $rows): string
    {
        // Create temporary file
        $tempFilePath = tempnam(sys_get_temp_dir(), 'company_import_') . '.xlsx';

        // Create new spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add header row
        $headers = [
            'Denominazione',
            'Partita IVA',
            'Nazione',
            'CAP',
            'Provincia',
            'Comune',
            'Indirizzo',
            'Numero civico',
            'Indirizzo telematico',
            'Email',
            'PEC'
        ];

        $sheet->fromArray($headers, null, 'A1');

        // Add data rows
        $rowData = [];
        foreach ($rows as $row) {
            $rowData[] = $row->toArray();
        }

        if (!empty($rowData)) {
            $sheet->fromArray($rowData, null, 'A2');
        }

        // Save the file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tempFilePath);

        return $tempFilePath;
    }
}
