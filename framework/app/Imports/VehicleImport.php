<?php

namespace App\Imports;

use App\Model\VehicleModel;
use App\Model\VehicleTypeModel;
use App\Model\VehicleGroupModel;
use App\Model\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class VehicleImport implements ToCollection, WithHeadingRow
{
    public $importStats = [
        'total_rows' => 0,
        'processed' => 0,
        'duplicates_skipped' => 0,
        'validation_failed' => 0,
        'successfully_imported' => 0,
        'errors' => 0
    ];

    /**
     * Normalize registration plate for duplicate checking
     * Removes spaces, hyphens, and converts to uppercase
     * Examples: "G4 PYU" -> "G4PYU", "G4-PYU" -> "G4PYU", "g4 pyu" -> "G4PYU"
     */
    private function normalizeRegistrationPlate($plate)
    {
        if (empty($plate)) {
            return '';
        }
        
        // Remove spaces, hyphens, and convert to uppercase
        return strtoupper(str_replace([' ', '-'], '', trim($plate)));
    }

    /**
     * Normalize column names to handle variations
     * Examples: "Insurance Discount" -> "insurance_discount", "Vehicle Type" -> "vehicle_type"
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
            'registration_plate' => 'registration_plate',
            'make' => 'make',
            'model' => 'model',
            'year' => 'year',
            'color' => 'color',
            'vehicle_type' => 'vehicle_type',
            'fuel_type' => 'fuel_type',
            'mileage' => 'mileage',
            'price' => 'price',
            'price_period' => 'price_period',
            'initial_cost' => 'initial_cost',
            'vehicle_scheme' => 'vehicle_scheme',
            'insurance_discount' => 'insurance_discount',
            'available' => 'available',
            'vehicle_status' => 'vehicle_status',
            'vehicle_group' => 'vehicle_group',
            'mot_expiry_day' => 'mot_expiry_day',
            'mot_expiry_month' => 'mot_expiry_month',
            'mot_expiry_year' => 'mot_expiry_year',
            'telematics_link' => 'telematics_link',
            'assigned_driver_first_name' => 'assigned_driver_first_name',
            'assigned_driver_last_name' => 'assigned_driver_last_name',
        ];
        
        return $mapping[$normalized] ?? $normalized;
    }

    public function collection(Collection $rows)
    {
        $this->importStats['total_rows'] = $rows->count();
        
        Log::info('Vehicle import started', [
            'total_rows' => $this->importStats['total_rows'],
            'sample_row' => $rows->first() ? $rows->first()->toArray() : null
        ]);
        
        $rowNumber = 0;
        foreach ($rows as $row) {
            $rowNumber++;
            try {
                // Skip empty rows
                if (empty($row['registration_plate'])) {
                    Log::info("Skipping empty row $rowNumber");
                    continue;
                }

                // Validate required fields
                $rowData = is_array($row) ? $row : $row->toArray();
                
                Log::info("Processing row $rowNumber", [
                    'registration_plate' => $rowData['registration_plate'] ?? 'MISSING',
                    'make' => $rowData['make'] ?? 'MISSING',
                    'model' => $rowData['model'] ?? 'MISSING',
                    'year' => $rowData['year'] ?? 'MISSING'
                ]);
                
                // Normalize registration plate for duplicate checking
                $normalizedPlate = $this->normalizeRegistrationPlate($rowData['registration_plate']);
                
                // Check for existing vehicle with same normalized registration plate
                $existingVehicle = VehicleModel::whereRaw('UPPER(REPLACE(REPLACE(license_plate, " ", ""), "-", "")) = ?', [$normalizedPlate])->first();
                
                if ($existingVehicle) {
                    $this->importStats['duplicates_skipped']++;
                    Log::info('Duplicate vehicle skipped during import', [
                        'original_plate' => $rowData['registration_plate'],
                        'normalized_plate' => $normalizedPlate,
                        'existing_vehicle_id' => $existingVehicle->id,
                        'existing_plate' => $existingVehicle->license_plate
                    ]);
                    continue; // Skip this row as it's a duplicate
                }
                
                $this->importStats['processed']++;
                
                // Convert year to integer if it's a string
                if (isset($rowData['year']) && is_string($rowData['year'])) {
                    $rowData['year'] = (int) $rowData['year'];
                }
                
                $validator = Validator::make($rowData, [
                    'registration_plate' => 'required|string|max:255',
                    'make' => 'required|string|max:255',
                    'model' => 'required|string|max:255',
                    'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                ]);

                if ($validator->fails()) {
                    $this->importStats['validation_failed']++;
                    Log::warning('Vehicle import validation failed', [
                        'row' => $rowData,
                        'errors' => $validator->errors()
                    ]);
                    continue;
                }

                        // Find or create vehicle type
                        $type = VehicleTypeModel::firstOrCreate(
                            ['vehicletype' => $rowData['vehicle_type'] ?? 'Unknown'],
                            ['vehicletype' => $rowData['vehicle_type'] ?? 'Unknown']
                        );

                // Find or create vehicle group
                $group = null;
                if (!empty($rowData['vehicle_group'])) {
                    $group = VehicleGroupModel::firstOrCreate(
                        ['name' => $rowData['vehicle_group']],
                        ['name' => $rowData['vehicle_group']]
                    );
                }

                // Find driver if assigned
                $driver = null;
                if (!empty($rowData['assigned_driver_first_name']) && !empty($rowData['assigned_driver_last_name'])) {
                    $driverName = $rowData['assigned_driver_first_name'] . ' ' . $rowData['assigned_driver_last_name'];
                    $driver = User::where('name', $driverName)->first();
                }

                // Create MOT expiry date - FIXED to handle zero-padded values
                $motExpiryDate = null;
                if (!empty($rowData['mot_expiry_day']) && !empty($rowData['mot_expiry_month']) && !empty($rowData['mot_expiry_year'])) {
                    try {
                        // Handle 2-digit years (e.g., 25 becomes 2025)
                        $year = intval($rowData['mot_expiry_year']);
                        if ($year < 100) {
                            $year += 2000; // Convert 25 to 2025
                        }
                        
                        // Convert day and month to integers to handle zero-padded values (e.g., "09" -> 9)
                        $day = intval($rowData['mot_expiry_day']);
                        $month = intval($rowData['mot_expiry_month']);
                        
                        // Validate the date components
                        if ($day >= 1 && $day <= 31 && $month >= 1 && $month <= 12 && $year >= 1900) {
                            $motExpiryDate = Carbon::create($year, $month, $day);
                            
                            Log::info('MOT expiry date created successfully', [
                                'original_day' => $rowData['mot_expiry_day'],
                                'original_month' => $rowData['mot_expiry_month'],
                                'original_year' => $rowData['mot_expiry_year'],
                                'parsed_date' => $motExpiryDate->format('Y-m-d'),
                                'vehicle' => $rowData['registration_plate']
                            ]);
                        } else {
                            Log::warning('Invalid MOT expiry date components', [
                                'day' => $day,
                                'month' => $month,
                                'year' => $year,
                                'original_day' => $rowData['mot_expiry_day'],
                                'original_month' => $rowData['mot_expiry_month'],
                                'original_year' => $rowData['mot_expiry_year'],
                                'vehicle' => $rowData['registration_plate']
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Invalid MOT expiry date - Carbon creation failed', [
                            'day' => $rowData['mot_expiry_day'],
                            'month' => $rowData['mot_expiry_month'],
                            'year' => $rowData['mot_expiry_year'],
                            'error' => $e->getMessage(),
                            'vehicle' => $rowData['registration_plate']
                        ]);
                    }
                } else {
                    Log::info('MOT expiry date fields are empty', [
                        'day' => $rowData['mot_expiry_day'] ?? 'empty',
                        'month' => $rowData['mot_expiry_month'] ?? 'empty',
                        'year' => $rowData['mot_expiry_year'] ?? 'empty',
                        'vehicle' => $rowData['registration_plate']
                    ]);
                }

                // Handle Available field for both boolean and string values
                $isAvailable = 1; // Default to true
                if (isset($rowData['available'])) {
                    $available = $rowData['available'];
                    if (is_bool($available)) {
                        $isAvailable = $available ? 1 : 0;
                    } else {
                        $isAvailable = (strtolower(trim($available)) === 'true') ? 1 : 0;
                    }
                }

                // Create vehicle - REMOVED exp_date as it doesn't exist in vehicles table
                $vehicle = VehicleModel::create([
                    'license_plate' => $rowData['registration_plate'],
                    'make_name' => $rowData['make'],
                    'model_name' => $rowData['model'],
                    'year' => $rowData['year'],
                    'color_name' => $rowData['color'] ?? 'Unknown',
                    'type' => $rowData['vehicle_type'] ?? 'Unknown',
                    'engine_type' => $rowData['fuel_type'] ?? 'Petrol',
                    'mileage' => $rowData['mileage'] ?? 0,
                    'int_mileage' => $rowData['mileage'] ?? 0,
                    'in_service' => $isAvailable,
                    'group_id' => $group ? $group->id : null,
                    'type_id' => $type->id,
                    'user_id' => auth()->id(),
                    'company_id' => auth()->user()->company_id ?? null,
                ]);

                // Set metadata - ensure all fields are saved INCLUDING MOT expiry date
                $vehicle->setMeta('vehicle_status', $rowData['vehicle_status'] ?? 'Available');
                $vehicle->setMeta('vehicle_scheme', $rowData['vehicle_scheme'] ?? '');
                $vehicle->setMeta('price', $rowData['price'] ?? '');
                $vehicle->setMeta('price_period', $rowData['price_period'] ?? 'Weekly');
                $vehicle->setMeta('initial_cost', $rowData['initial_cost'] ?? '');
                $vehicle->setMeta('insurance_discount', $rowData['insurance_discount'] ?? '0');
                $vehicle->setMeta('telematics_link', $rowData['telematics_link'] ?? '');
                
                Log::info('Vehicle metadata set', [
                    'vehicle_id' => $vehicle->id,
                    'registration_plate' => $vehicle->license_plate,
                    'metadata' => [
                        'vehicle_status' => $rowData['vehicle_status'] ?? 'Available',
                        'vehicle_scheme' => $rowData['vehicle_scheme'] ?? '',
                        'price' => $rowData['price'] ?? '',
                        'price_period' => $rowData['price_period'] ?? 'Weekly',
                        'initial_cost' => $rowData['initial_cost'] ?? '',
                        'insurance_discount' => $rowData['insurance_discount'] ?? '',
                        'telematics_link' => $rowData['telematics_link'] ?? ''
                    ]
                ]);
                
                // FIXED: Store MOT expiry date in metadata instead of non-existent exp_date column
                if ($motExpiryDate) {
                    $vehicle->setMeta('mot_expiry_date', $motExpiryDate->format('Y-m-d'));
                    $vehicle->setMeta('exp_date', $motExpiryDate->format('Y-m-d')); // For compatibility
                    
                    Log::info('MOT expiry date saved to metadata', [
                        'vehicle_id' => $vehicle->id,
                        'mot_date' => $motExpiryDate->format('Y-m-d'),
                        'vehicle' => $rowData['registration_plate']
                    ]);
                }
                
                if ($driver) {
                    $vehicle->setMeta('assign_driver_id', $driver->id);
                }
                
                // Save the vehicle to ensure metadata is persisted
                $vehicle->save();

                $this->importStats['successfully_imported']++;
                Log::info('Vehicle imported successfully', [
                    'vehicle_id' => $vehicle->id,
                    'license_plate' => $vehicle->license_plate,
                    'mot_expiry_date' => $motExpiryDate ? $motExpiryDate->format('Y-m-d') : 'null'
                ]);

            } catch (\Exception $e) {
                $this->importStats['errors']++;
                Log::error('Vehicle import failed', [
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
        
        // Log final import statistics
        Log::info('Vehicle import completed', $this->importStats);
        
        // Also log to Laravel's main log for easier debugging
        \Log::info('VEHICLE IMPORT SUMMARY', $this->importStats);
    }
}