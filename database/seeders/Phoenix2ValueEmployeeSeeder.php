<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Phoenix2ValueEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phoenix2value = Company::where('name', 'Phoenix2Value srls')->first();

        if (!$phoenix2value) {
            $this->command->error('Phoenix2Value company not found. Please run CompanySeeder first.');
            return;
        }

        $employees = [
            [
                'name' => 'Diego Cingolani',
                'cf' => 'CNGDGI91A01H501A',
                'phone' => '+39 333 1234567',
                'role' => 'Account Manager',
                'department' => 'Sales',
                'hiring_date' => '2023-01-15',
                'company_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Caterina Casadei',
                'cf' => 'CSDCTR89S65H501Z',
                'phone' => '+39 333 2345678',
                'role' => 'Marketing Specialist',
                'department' => 'Marketing',
                'hiring_date' => '2023-02-20',
                'company_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Dario Follero',
                'cf' => 'FLLDRI78M30H501X',
                'phone' => '+39 333 3456789',
                'role' => 'Data Analyst',
                'department' => 'Operations',
                'hiring_date' => '2023-03-10',
                'company_id' => $phoenix2value->id,
            ],
            [
                'name' => "Domenico D'Agostino",
                'cf' => 'DGSDMN77D15H501L',
                'phone' => '+39 333 4567890',
                'role' => 'Team Leader',
                'department' => 'Operations',
                'hiring_date' => '2022-11-05',
                'company_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Fabio Figliozzi',
                'cf' => 'FGLFBA80E25H501V',
                'phone' => '+39 333 5678901',
                'role' => 'Software Developer',
                'department' => 'IT',
                'hiring_date' => '2023-04-12',
                'company_id' => $phoenix2value->id,
            ],
            [
                'name' => 'Simona Gramiccia',
                'cf' => 'GRMSMN82R50H501T',
                'phone' => '+39 333 6789012',
                'role' => 'HR Manager',
                'department' => 'Human Resources',
                'hiring_date' => '2022-09-18',
                'company_id' => $phoenix2value->id,
            ],
        ];

        foreach ($employees as $employeeData) {
            $employee = Employee::create($employeeData);
            $this->command->info("Employee created: {$employee->name} (ID: {$employee->id})");
        }

        $this->command->info(count($employees) . ' Phoenix2Value employees created successfully.');
    }
}
