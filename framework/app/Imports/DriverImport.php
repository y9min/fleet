<?php

namespace App\Imports;

use App\Model\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DriverImport implements ToCollection, WithHeadingRow
{
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

    /**
     * Normalize column names to handle variations
     * Examples: "Middle name" -> "middle_name", "Country code" -> "country_code"
     */
    private function normalizeColumnName($name)
    {
        if (empty($name)) {
            return '';
        }
        
        // Convert to lowercase and replace spaces/underscores/hyphens with underscores
        return strtolower(str_replace([' ', '-'], '_', trim($name)));
    }

    /**
     * Map column names to expected field names
     */
    private function mapColumnName($columnName)
    {
        $normalized = $this->normalizeColumnName($columnName);
        
        $mapping = [
            'first_name' => 'first_name',
            'middle_name' => 'middle_name',
            'last_name' => 'last_name',
            'address' => 'address',
            'email' => 'email',
            'phone' => 'phone',
            'country_code' => 'country_code',
            'employee_id' => 'employee_id',
            'contract_number' => 'contract_number',
            'licence_number' => 'licence_number',
            'license_number' => 'licence_number',
            'issue_date' => 'issue_date',
            'expiration_date' => 'expiration_date',
            'join_date' => 'join_date',
            'leave_date' => 'leave_date',
            'gender' => 'gender',
            'password' => 'password',
            'emergency_contact_details' => 'emergency_contact_details',
        ];
        
        return $mapping[$normalized] ?? $normalized;
    }

    public function collection(Collection $rows)
    {
        $this->importStats['total_rows'] = $rows->count();
        
        Log::info('Driver import started', [
            'total_rows' => $this->importStats['total_rows'],
            'sample_row' => $rows->first() ? $rows->first()->toArray() : null
        ]);
        
        // Start database transaction for rollback capability
        \DB::beginTransaction();
        
        try {
            $rowNumber = 0;
            foreach ($rows as $row) {
                $rowNumber++;
                try {
                    // Convert row to array if it's not already
                    $rowData = is_array($row) ? $row : $row->toArray();
                    
                    // Skip empty rows
                    if (empty($rowData['email']) && empty($rowData['first_name']) && empty($rowData['last_name'])) {
                        Log::info("Skipping empty row $rowNumber");
                        continue;
                    }

                    // Validate required fields
                    if (empty($rowData['email']) || empty($rowData['first_name']) || empty($rowData['last_name'])) {
                        $this->importStats['validation_failed']++;
                        Log::warning("Skipping row $rowNumber - missing required fields", [
                            'email' => $rowData['email'] ?? 'missing',
                            'first_name' => $rowData['first_name'] ?? 'missing',
                            'last_name' => $rowData['last_name'] ?? 'missing'
                        ]);
                        continue;
                    }

                    // Validate email format
                    if (!filter_var($rowData['email'], FILTER_VALIDATE_EMAIL)) {
                        $this->importStats['validation_failed']++;
                        Log::warning("Skipping row $rowNumber - invalid email format", [
                            'email' => $rowData['email']
                        ]);
                        continue;
                    }

                    // Check for duplicate email
                    $existingDriver = User::where('email', $rowData['email'])
                        ->where('user_type', 'D')
                        ->first();
                    
                    if ($existingDriver) {
                        $this->importStats['duplicates_skipped']++;
                        Log::info('Duplicate driver skipped during import', [
                            'email' => $rowData['email'],
                            'existing_driver_id' => $existingDriver->id
                        ]);
                        continue; // Skip this row as it's a duplicate
                    }
                    
                    $this->importStats['processed']++;
                    
                    // Clean and validate required fields
                    $rowData['first_name'] = trim($rowData['first_name']);
                    $rowData['last_name'] = trim($rowData['last_name']);
                    $rowData['email'] = trim($rowData['email']);
                    
                    // Create the driver user
                    $user = User::create([
                        "name" => $rowData['first_name'] . " " . $rowData['last_name'],
                        "email" => $rowData['email'],
                        "password" => bcrypt($rowData['password'] ?? 'password123'),
                        "user_type" => "D",
                        'api_token' => str_random(60),
                        'company_id' => $this->companyId,
                    ]);

                    $user->is_active = 1;
                    
                    // Store driver-specific fields in metadata
                    $user->setMeta([
                        'is_available' => 0,
                        'first_name' => $rowData['first_name'],
                        'middle_name' => $rowData['middle_name'] ?? '',
                        'last_name' => $rowData['last_name'],
                        'address' => $rowData['address'] ?? '',
                        'phone' => $rowData['phone'] ?? '',
                        'phone_code' => "+" . ($rowData['country_code'] ?? '44'),
                        'emp_id' => $rowData['employee_id'] ?? '',
                        'contract_number' => $rowData['contract_number'] ?? '',
                        'license_number' => $rowData['licence_number'] ?? '',
                        'gender' => (($rowData['gender'] ?? 'male') == 'female') ? 0 : 1,
                        'econtact' => $rowData['emergency_contact_details'] ?? '',
                    ]);
                    
                    // Handle date fields with error handling
                    if (!empty($rowData['issue_date'])) {
                        try {
                            $issueDate = date('Y-m-d', strtotime($rowData['issue_date']));
                            if ($issueDate !== '1970-01-01') { // Only set if valid date
                                $user->setMeta(['issue_date' => $issueDate]);
                            }
                        } catch (\Exception $e) {
                            Log::warning("Invalid issue_date for driver {$rowData['email']}", [
                                'date' => $rowData['issue_date'],
                                'error' => $e->getMessage()
                            ]);
                        }
                    }

                    if (!empty($rowData['expiration_date'])) {
                        try {
                            $expDate = date('Y-m-d', strtotime($rowData['expiration_date']));
                            if ($expDate !== '1970-01-01') { // Only set if valid date
                                $user->setMeta(['exp_date' => $expDate]);
                            }
                        } catch (\Exception $e) {
                            Log::warning("Invalid expiration_date for driver {$rowData['email']}", [
                                'date' => $rowData['expiration_date'],
                                'error' => $e->getMessage()
                            ]);
                        }
                    }

                    if (!empty($rowData['join_date'])) {
                        try {
                            $joinDate = date('Y-m-d', strtotime($rowData['join_date']));
                            if ($joinDate !== '1970-01-01') { // Only set if valid date
                                $user->setMeta(['start_date' => $joinDate]);
                            }
                        } catch (\Exception $e) {
                            Log::warning("Invalid join_date for driver {$rowData['email']}", [
                                'date' => $rowData['join_date'],
                                'error' => $e->getMessage()
                            ]);
                        }
                    }

                    if (!empty($rowData['leave_date'])) {
                        try {
                            $leaveDate = date('Y-m-d', strtotime($rowData['leave_date']));
                            if ($leaveDate !== '1970-01-01') { // Only set if valid date
                                $user->setMeta(['end_date' => $leaveDate]);
                            }
                        } catch (\Exception $e) {
                            Log::warning("Invalid leave_date for driver {$rowData['email']}", [
                                'date' => $rowData['leave_date'],
                                'error' => $e->getMessage()
                            ]);
                        }
                    }

                    // Assign permissions
                    try {
                        $user->givePermissionTo([
                            'Notes add', 'Notes edit', 'Notes delete', 'Notes list',
                            'Drivers list', 'VehicleInspection add', 'VehicleInspection list',
                            'VehicleInspection edit', 'VehicleInspection delete'
                        ]);
                    } catch (\Exception $e) {
                        Log::warning("Failed to assign permissions for driver {$rowData['email']}", [
                            'error' => $e->getMessage()
                        ]);
                        // Continue - permissions can be assigned later
                    }

                    // Save the user to ensure metadata is persisted
                    $user->save();
                    
                    $this->importStats['successfully_imported']++;
                    
                    Log::info('Driver imported successfully', [
                        'email' => $rowData['email'],
                        'name' => $user->name,
                        'driver_id' => $user->id
                    ]);

                } catch (\Exception $e) {
                    $this->importStats['errors']++;
                    Log::error('Driver import failed', [
                        'row_number' => $rowNumber,
                        'row' => $rowData ?? null,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    // Add error details for user feedback
                    if (!isset($this->importStats['error_details'])) {
                        $this->importStats['error_details'] = [];
                    }
                    $this->importStats['error_details'][] = "Row $rowNumber: " . $e->getMessage();
                }
            }
            
            // Commit transaction if we get here
            \DB::commit();
            
            // Log final import statistics
            Log::info('Driver import completed successfully', $this->importStats);
            \Log::info('DRIVER IMPORT SUMMARY', $this->importStats);
            
        } catch (\Exception $e) {
            // Rollback transaction on any error
            \DB::rollback();
            
            $this->importStats['errors']++;
            Log::error('Driver import failed - transaction rolled back', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'stats' => $this->importStats
            ]);
            
            // Add error details for user feedback
            if (!isset($this->importStats['error_details'])) {
                $this->importStats['error_details'] = [];
            }
            $this->importStats['error_details'][] = "Import failed: " . $e->getMessage();
            
            throw $e; // Re-throw to be caught by controller
        }
    }
}
