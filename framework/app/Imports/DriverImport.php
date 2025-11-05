<?php

namespace App\Imports;

use App\Model\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Support\Import\FieldNormalizers;
use Illuminate\Support\Facades\DB;

class DriverImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable; // Enables importing and catching validation errors
    use SkipsFailures; // Track validation failures without stopping import

    /**
     * @var string
     */
    private $companyId;

    public function __construct($companyId)
    {
        $this->companyId = $companyId;
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
            $email = FieldNormalizers::email($driver['email'] ?? null);
            $first = FieldNormalizers::trimOrNull($driver['first_name'] ?? null);
            $last  = FieldNormalizers::trimOrNull($driver['last_name'] ?? null);
            $middle = FieldNormalizers::trimOrNull($driver['middle_name'] ?? null);
            $licenseNumber = FieldNormalizers::toUpper($driver['licence_number'] ?? null);
            $genderValue = $driver['gender'] ?? 'male';
            $genderNormalized = strtolower(trim((string)$genderValue));
            $isFemale = ($genderNormalized === 'female' || $genderNormalized === 'f');
            
            // Wrap each row in its own transaction to isolate failures
            $user = DB::transaction(function () use ($first, $last, $email, $driver, $licenseNumber, $isFemale, $middle) {
                $user = User::create([
                    "name" => ($first ?: '') . " " . ($last ?: ''),
                    "email" => $email,
                    "password" => bcrypt($driver['password'] ?? 'password123'),
                    "user_type" => "D",
                    'api_token' => str_random(60),
                    'company_id' => $this->companyId,
                ]);

                // Do NOT set is_active - let database default handle it (PostgreSQL boolean)

            // Normalize phone - remove Excel formula prefix (=) and any leading +
            $phoneRaw = (string) ($driver['phone'] ?? '');
            $phone = ltrim($phoneRaw, '=+');
            
            // Normalize country_code - remove Excel formula prefix (=) and any leading +
            $countryCodeRaw = (string) ($driver['country_code'] ?? '44');
            $countryCode = ltrim($countryCodeRaw, '=+');

            // Persist profile details into metadata store (users_meta)
            $user->setMeta([
                'first_name' => $first,
                'middle_name' => $middle ?? '',
                'last_name' => $last,
                'address' => $driver['address'] ?? '',
                'phone' => $phone,
                'phone_code' => "+" . $countryCode,
                'employee_id' => $driver['employee_id'] ?? '',
                'contract_number' => $driver['contract_number'] ?? '',
                'license_number' => $licenseNumber ?? '',
                'issue_date' => !empty($driver['issue_date']) ? FieldNormalizers::toDate($driver['issue_date']) : null,
                'expiration_date' => !empty($driver['expiration_date']) ? FieldNormalizers::toDate($driver['expiration_date']) : null,
                'join_date' => !empty($driver['join_date']) ? FieldNormalizers::toDate($driver['join_date']) : null,
                'leave_date' => !empty($driver['leave_date']) ? FieldNormalizers::toDate($driver['leave_date']) : null,
                'gender' => $isFemale ? 0 : 1,
                'emergency_contact_details' => $driver['emergency_contact_details'] ?? '',
            ]);

                $user->givePermissionTo([
                    'Notes add', 'Notes edit', 'Notes delete', 'Notes list',
                    'Drivers list', 'VehicleInspection add', 'VehicleInspection list',
                    'VehicleInspection edit', 'VehicleInspection delete'
                ]);

                $user->save();
                
                return $user;
            });
            
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

    // Add validation rules - only validate if row has data
    public function rules(): array
    {
        return [
            // Only validate if fields are present and not empty
            // Empty rows will pass validation and be skipped in model() method
            'email' => ['nullable', 'email'],
            'first_name' => ['nullable'],
            'last_name' => ['nullable'],
            'password' => ['nullable', 'min:6'],
            'phone' => ['nullable'],
            'contract_number' => ['nullable'],
            'licence_number' => ['nullable'],
            // Use custom validation closure to accept DD/MM/YYYY format
            'join_date' => ['nullable', function ($attribute, $value, $fail) {
                if ($value && !FieldNormalizers::toDate($value)) {
                    $fail('The ' . $attribute . ' is not a valid date.');
                }
            }],
            'leave_date' => ['nullable', function ($attribute, $value, $fail) {
                if ($value && !FieldNormalizers::toDate($value)) {
                    $fail('The ' . $attribute . ' is not a valid date.');
                }
            }],
            'issue_date' => ['nullable', function ($attribute, $value, $fail) {
                if ($value && !FieldNormalizers::toDate($value)) {
                    $fail('The ' . $attribute . ' is not a valid date.');
                }
            }],
            'expiration_date' => ['nullable', function ($attribute, $value, $fail) {
                if ($value && !FieldNormalizers::toDate($value)) {
                    $fail('The ' . $attribute . ' is not a valid date.');
                }
            }],
        ];
    }
    
    /**
     * Handle validation failures - track them but don't stop import
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->importStats['validation_failed']++;
            Log::warning('Driver import validation failure', [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values()
            ]);
        }
    }
}
