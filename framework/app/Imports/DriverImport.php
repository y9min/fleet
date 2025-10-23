<?php

namespace App\Imports;

use App\Model\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class DriverImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable; // Enables importing and catching validation errors
    
    protected $companyId;

    public function __construct($companyId = null)
    {
        $this->companyId = $companyId ?? \Auth::user()->company_id ?? null;
    }

    public $importStats = [
        'total_rows' => 0,
        'processed' => 0,
        'duplicates_skipped' => 0,
        'validation_failed' => 0,
        'successfully_imported' => 0,
        'errors' => 0
    ];

    public function model(array $driver)
    {
        $this->importStats['total_rows']++;
        
        // Skip empty rows
        if (empty($driver['email']) || empty($driver['first_name']) || empty($driver['last_name'])) {
            $this->importStats['validation_failed']++;
            Log::warning('Skipping empty driver row', ['row' => $driver]);
            return null;
        }

        // Check for duplicate email
        if (!$this->validateEmail($driver['email'])) {
            $this->importStats['duplicates_skipped']++;
            Log::info('Skipping duplicate driver email', ['email' => $driver['email']]);
            return null;
        }

        try {
            $user = User::create([
                "name" => $driver['first_name'] . " " . $driver['last_name'],
                "email" => $driver['email'],
                "password" => bcrypt($driver['password'] ?? 'password123'),
                "user_type" => "D",
                'api_token' => str_random(60),
                'company_id' => $this->companyId,
            ]);

            $user->is_active = 1;
            
            // Store driver-specific fields in metadata
            $user->setMeta([
                'is_available' => 0,
                'first_name' => $driver['first_name'],
                'middle_name' => $driver['middle_name'] ?? '',
                'last_name' => $driver['last_name'],
                'address' => $driver['address'] ?? '',
                'phone' => $driver['phone'] ?? '',
                'phone_code' => "+" . ($driver['country_code'] ?? '44'),
                'emp_id' => $driver['employee_id'] ?? '',
                'contract_number' => $driver['contract_number'] ?? '',
                'license_number' => $driver['licence_number'] ?? '',
                'gender' => (($driver['gender'] ?? 'male') == 'female') ? 0 : 1,
                'econtact' => $driver['emergency_contact_details'] ?? '',
            ]);
            
            if (!empty($driver['issue_date'])) {
                $user->setMeta(['issue_date' => date('Y-m-d', strtotime($driver['issue_date']))]);
            }

            if (!empty($driver['expiration_date'])) {
                $user->setMeta(['exp_date' => date('Y-m-d', strtotime($driver['expiration_date']))]);
            }

            if (!empty($driver['join_date'])) {
                $user->setMeta(['start_date' => date('Y-m-d', strtotime($driver['join_date']))]);
            }

            if (!empty($driver['leave_date'])) {
                $user->setMeta(['end_date' => date('Y-m-d', strtotime($driver['leave_date']))]);
            }

            $user->givePermissionTo([
                'Notes add', 'Notes edit', 'Notes delete', 'Notes list',
                'Drivers list', 'VehicleInspection add', 'VehicleInspection list',
                'VehicleInspection edit', 'VehicleInspection delete'
            ]);

            $user->save();
            
            $this->importStats['successfully_imported']++;
            $this->importStats['processed']++;
            
            Log::info('Driver imported successfully', ['email' => $driver['email'], 'name' => $user->name]);
            
            return $user;
            
        } catch (\Exception $e) {
            $this->importStats['errors']++;
            Log::error('Error importing driver', [
                'email' => $driver['email'],
                'error' => $e->getMessage(),
                'row' => $driver
            ]);
            return null;
        }
    }

    // Email validation to check if email exists in database
    private function validateEmail($email)
    {
        return !User::where('email', $email)->where('user_type', 'D')->exists();
    }

    // Add validation rules
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'password' => ['nullable', 'min:6'],
            'phone' => ['nullable'],
            'contract_number' => ['nullable'],
            'licence_number' => ['nullable'],
            'join_date' => ['nullable', 'date'],
            'leave_date' => ['nullable', 'date'],
            'issue_date' => ['nullable', 'date'],
            'expiration_date' => ['nullable', 'date'],
        ];
    }
}
