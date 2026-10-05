<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Services\ExcelImportHelpers;

class EmployeeImportService
{
    protected array $results = [
        'imported' => 0,
        'skipped' => 0,
        'errors' => []
    ];

    protected Company $company;
    protected ?User $user;

    public function __construct()
    {
        $this->company = Company::first();
        $this->user = auth()->user();
    }

    /**
     * Import employees from Excel file
     */
    public function importFromFile(string $filePath): array
    {
        try {
            // Reset results before starting import
            $this->results = ['imported' => 0, 'skipped' => 0, 'errors' => []];

            if (!file_exists($filePath)) {
                throw new \Exception("File not found: {$filePath}");
            }

            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row (assume first row contains headers)
            $dataRows = array_slice($rows, 1);

            DB::beginTransaction();

            foreach ($dataRows as $index => $row) {
                $rowIndex = $index + 2;  // +2 because we skipped header and 0-indexed

                try {
                    $this->processRow($row, $rowIndex);
                } catch (\Exception $e) {
                    $this->results['errors'][] = "Row {$rowIndex}: " . $e->getMessage();
                    $this->results['skipped']++;
                }
            }

            DB::commit();

            return $this->results;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee import failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process a single row from the Excel file
     */
    protected function processRow(array $row, int $rowIndex): void
    {
        // Skip empty rows
        if (empty(array_filter($row))) {
            $this->results['skipped']++;
            return;
        }

        $employeeData = $this->mapRowToEmployeeData($row);

        // Debug: Log the mapped data
        Log::info("Processing row {$rowIndex}: " . json_encode($employeeData));

        // Validate required fields
        if (empty($employeeData['name'])) {
            throw new \Exception('Name is required');
        }

        // Skip duplicate check for now - we'll add it back later
        // $existingEmployee = Employee::where('company_id', $this->company->id)
        //     ->where('name', $employeeData['name'])
        //     ->first();

        // if ($existingEmployee) {
        //     Log::info("Employee already exists: {$employeeData['name']}");
        //     $this->results['skipped']++;
        //     return;
        // }

        // Create employee
        try {
            $employee = Employee::create([
                'company_id' => $this->company->id,
                'company_branch_id' => $employeeData['company_branch_id'],
                'user_id' => $employeeData['user_id'],
                'name' => $employeeData['name'],
                'cf' => $employeeData['tax_code'] ?? null,  // Codice Fiscale
                'phone' => $employeeData['phone'] ?? null,
                'role' => $employeeData['role'] ?? 'Dipendente',
                'department' => $employeeData['department'] ?? null,
                'hiring_date' => $employeeData['hire_date'] ?? null,  // hiring_date instead of hire_date
                'termination_date' => $employeeData['end_date'] ?? null,  // termination_date instead of end_date
                'coordinated_by_id' => $employeeData['coordinated_by_id'] ?? null,
                'privacy_role' => $employeeData['position'] ?? null,  // Use position as privacy_role
                'employee_type' => $employeeData['contract_type'] ?? null,  // Use contract_type as employee_type
            ]);

            Log::info("Successfully created employee: {$employee->name} (ID: {$employee->id})");
            $this->results['imported']++;
        } catch (\Exception $e) {
            Log::error('Failed to create employee: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Map Excel row to employee data array
     */
    protected function mapRowToEmployeeData(array $row): array
    {
        // Actual Excel structure:
        // A: IMPIEGATO (Nome completo)
        // B: CODICE FISCALE
        // C: LUOGO DI NASCITA
        // D: DATA DI NASCITA
        // E: SEDE
        // F: TIPOCTR (Tipo contratto)
        // G: INIZIOCTR (Inizio contratto)
        // H: FINE CTR (Fine contratto)
        // I: MANSIONE

        return [
            'name' => $this->cleanValue($row[0] ?? ''),  // Column A: IMPIEGATO
            'tax_code' => $this->cleanValue($row[1] ?? ''),  // Column B: CODICE FISCALE
            'birth_place' => $this->cleanValue($row[2] ?? ''),  // Column C: LUOGO DI NASCITA
            'birth_date' => $this->parseDate($row[3] ?? null),  // Column D: DATA DI NASCITA
            'company_branch_id' => $this->findBranchId($row[4] ?? null),  // Column E: SEDE
            'contract_type' => $this->cleanValue($row[5] ?? ''),  // Column F: TIPOCTR
            'hire_date' => $this->parseDate($row[6] ?? null),  // Column G: INIZIOCTR
            'end_date' => $this->parseDate($row[7] ?? null),  // Column H: FINE CTR
            'position' => $this->cleanValue($row[8] ?? ''),  // Column I: MANSIONE
            'email' => null,  // Non presente nel file
            'phone' => null,  // Non presente nel file
            'role' => $this->cleanValue($row[8] ?? 'Dipendente'),  // Column I: MANSIONE come role
            'department' => null,  // Non presente nel file
            'salary' => null,  // Non presente nel file
            'notes' => null,  // Non presente nel file
            'user_id' => null,  // Will be set if a matching user is found
            'coordinated_by_id' => null,  // Will be set if a supervisor is found
        ];
    }

    /**
     * Clean and trim value
     */
    protected function cleanValue(?string $value): ?string
    {
        return ExcelImportHelpers::cleanString($value);
    }

    /**
     * Parse date from Excel
     */
    protected function parseDate(?string $date): ?string
    {
        $parsedDate = ExcelImportHelpers::parseDate($date);
        return $parsedDate ? $parsedDate->format("Y-m-d") : null;
    }

    /**
     * Parse salary
     */
    protected function parseSalary(?string $salary): ?float
    {
        return ExcelImportHelpers::parseAmount($salary);
    }

    /**
     * Find branch ID by name
     */
    protected function findBranchId(?string $branchName): ?int
    {
        if (empty($branchName)) {
            return null;
        }

        $branch = Branch::where('company_id', $this->company->id)
            ->where('name', 'like', '%' . $branchName . '%')
            ->first();

        return $branch?->id;
    }

    /**
     * Get import results summary
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * Get formatted results for display
     */
    public function getFormattedResults(): array
    {
        return [
            'success' => $this->results['imported'] > 0,
            'imported' => $this->results['imported'],
            'skipped' => $this->results['skipped'],
            'total_processed' => $this->results['imported'] + $this->results['skipped'],
            'errors' => $this->results['errors'],
            'has_errors' => !empty($this->results['errors']),
        ];
    }

    /**
     * Validate file format
     */
    public function validateFile(string $filePath): bool
    {
        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            // Check if file has data
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            return $highestRow > 1 && !empty($highestColumn);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get file info
     */
    public function getFileInfo(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return ['exists' => false];
        }

        return [
            'exists' => true,
            'size' => filesize($filePath),
            'size_formatted' => $this->formatBytes(filesize($filePath)),
            'modified' => date('Y-m-d H:i:s', filemtime($filePath)),
            'type' => mime_content_type($filePath),
        ];
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Public method for testing row mapping
     */
    public function testMapRow(array $row): array
    {
        return $this->mapRowToEmployeeData($row);
    }
}
