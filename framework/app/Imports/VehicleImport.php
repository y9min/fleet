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
use App\Support\Import\FieldNormalizers;

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
        
        // Safely get sample row for logging
        $sampleRow = null;
        try {
            $firstRow = $rows->first();
            if ($firstRow !== null) {
                if (is_array($firstRow)) {
                    $sampleRow = $firstRow;
                } elseif (is_object($firstRow) && method_exists($firstRow, 'toArray')) {
                    $sampleRow = $firstRow->toArray();
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get sample row for logging', ['error' => $e->getMessage()]);
        }
        
        Log::info('VEHICLE_IMPORT_DEBUG: Vehicle import started', [
            'total_rows' => $this->importStats['total_rows'],
            'sample_row' => $sampleRow
        ]);
        
        // Process each row in its own transaction so one failure doesn't abort all
            $rowNumber = 0;
            foreach ($rows as $row) {
            $rowNumber++;
            
            // Early validation: skip null rows before starting transaction
            if ($row === null) {
                $this->importStats['validation_failed']++;
                Log::warning("VEHICLE_IMPORT_DEBUG: Skipping row $rowNumber - row is null", [
                    'row_number' => $rowNumber
                ]);
                continue;
            }
            
            \DB::beginTransaction();
            try {
                // Log raw row structure for debugging
                Log::info("VEHICLE_IMPORT_DEBUG: Starting row processing", [
                    'row_number' => $rowNumber,
                    'row_type' => gettype($row),
                    'row_class' => is_object($row) ? get_class($row) : null,
                    'is_collection' => $row instanceof \Illuminate\Support\Collection,
                    'is_array' => is_array($row),
                    'row_dump' => is_object($row) ? 'OBJECT' : $row
                ]);
                
                // Convert to array first with defensive error handling
                $rowData = null;
                try {
                    // Handle Collection objects (what ToCollection actually passes)
                    if ($row instanceof \Illuminate\Support\Collection) {
                        Log::info("VEHICLE_IMPORT_DEBUG: Converting Collection to array", [
                            'row_number' => $rowNumber,
                            'collection_count' => $row->count(),
                            'collection_keys' => $row->keys()->toArray(),
                            'collection_class' => get_class($row)
                        ]);
                        
                        // Convert Collection to array directly - Laravel Excel handles header mapping
                        $rowData = $row->toArray();
                        
                        // If we got an empty array but Collection has items, build array using Collection methods
                        if (empty($rowData) && $row->count() > 0) {
                            Log::warning("VEHICLE_IMPORT_DEBUG: Collection toArray() returned empty but count > 0", [
                                'row_number' => $rowNumber,
                                'collection_count' => $row->count(),
                                'collection_keys' => $row->keys()->toArray()
                            ]);
                            
                            // Use Collection methods to safely extract values
                            $rowData = [];
                            $collectionKeys = $row->keys();
                            
                            foreach ($collectionKeys as $key) {
                                Log::debug("VEHICLE_IMPORT_DEBUG: Processing key from Collection", [
                                    'row_number' => $rowNumber,
                                    'key' => $key,
                                    'key_type' => gettype($key),
                                    'has_key' => $row->has($key)
                                ]);
                                
                                if ($key === null) {
                                    Log::warning("VEHICLE_IMPORT_DEBUG: Skipping null key", [
                                        'row_number' => $rowNumber
                                    ]);
                                    continue;
                                }
                                
                                $value = $row->get($key);
                                if ($key !== null) {
                                    $rowData[$key] = $value;
                                    Log::debug("VEHICLE_IMPORT_DEBUG: Key assigned from Collection", [
                                        'row_number' => $rowNumber,
                                        'key' => $key,
                                        'value' => $value,
                                        'value_type' => gettype($value)
                                    ]);
                                }
                            }
                        }
                    } elseif (is_array($row)) {
                        Log::info("VEHICLE_IMPORT_DEBUG: Row is already an array", [
                            'row_number' => $rowNumber,
                            'array_count' => count($row),
                            'array_keys' => array_keys($row)
                        ]);
                        $rowData = $row;
                    } elseif (is_object($row) && method_exists($row, 'toArray')) {
                        Log::info("VEHICLE_IMPORT_DEBUG: Converting object with toArray()", [
                            'row_number' => $rowNumber,
                            'object_class' => get_class($row)
                        ]);
                        $rowData = $row->toArray();
                    } else {
                        throw new \Exception("VEHICLE_IMPORT_ERROR: Row is not a Collection, array, or object with toArray method. Got: " . gettype($row) . (is_object($row) ? " (" . get_class($row) . ")" : ""));
                    }
                    
                    // IMPORTANT: Check if we got numeric keys (header mismatch!)
                    if (is_array($rowData) && !empty($rowData)) {
                        $keys = array_keys($rowData);
                        $allNumeric = true;
                        foreach ($keys as $key) {
                            if (!is_numeric($key)) {
                                $allNumeric = false;
                                break;
                            }
                        }
                        if ($allNumeric) {
                            throw new \Exception("VEHICLE_IMPORT_ERROR: Row has numeric keys - header mapping failed! First 5 keys: " . implode(', ', array_slice($keys, 0, 5)) . ". This suggests WithHeadingRow is not working correctly.");
                        }
                    }
                    
                    // Ensure toArray() didn't return null
                    if ($rowData === null) {
                        throw new \Exception("VEHICLE_IMPORT_ERROR: toArray() returned null");
                    }
                    
                    // Ensure it's actually an array
                    if (!is_array($rowData)) {
                        throw new \Exception("VEHICLE_IMPORT_ERROR: toArray() returned non-array: " . gettype($rowData));
                    }
                    
                    Log::info("VEHICLE_IMPORT_DEBUG: Collection converted to array", [
                        'row_number' => $rowNumber,
                        'rowData_type' => gettype($rowData),
                        'rowData_is_array' => is_array($rowData),
                        'rowData_count' => is_array($rowData) ? count($rowData) : 0,
                        'rowData_keys' => is_array($rowData) ? array_keys($rowData) : [],
                        'rowData_sample' => is_array($rowData) ? array_slice($rowData, 0, 3, true) : null
                    ]);
                    
                } catch (\Exception $e) {
                    $this->importStats['validation_failed']++;
                    Log::error("VEHICLE_IMPORT_ERROR: Failed to convert Collection to array", [
                        'row_number' => $rowNumber,
                        'error_message' => $e->getMessage(),
                        'error_file' => $e->getFile(),
                        'error_line' => $e->getLine(),
                        'row_type' => gettype($row),
                        'row_class' => is_object($row) ? get_class($row) : null,
                        'collection_count' => $row instanceof \Illuminate\Support\Collection ? $row->count() : 'N/A',
                        'collection_keys' => $row instanceof \Illuminate\Support\Collection ? $row->keys()->toArray() : 'N/A',
                        'stack_trace' => $e->getTraceAsString()
                    ]);
                    
                    $errorMsg = "VEHICLE_IMPORT_ERROR: Failed to convert row $rowNumber Collection to array. Error: " . $e->getMessage() . " | Row type: " . gettype($row) . " | Collection keys: " . ($row instanceof \Illuminate\Support\Collection ? implode(', ', $row->keys()->toArray()) : 'N/A');
                    throw new \Exception($errorMsg);
                }
                
                // Validate that rowData is an array and not empty
                if (!is_array($rowData)) {
                    $this->importStats['validation_failed']++;
                    Log::warning("VEHICLE_IMPORT_DEBUG: Row $rowNumber: rowData is not an array after conversion", [
                        'row_number' => $rowNumber,
                        'rowData_type' => gettype($rowData),
                        'rowData_value' => $rowData
                    ]);
                    \DB::rollBack();
                    continue;
                }
                
                // Normalize column names - Laravel Excel may preserve spaces or use different formats
                // Convert all keys to lowercase with underscores for consistency
                Log::info("VEHICLE_IMPORT_DEBUG: Before normalization", [
                    'row_number' => $rowNumber,
                    'rowData_type' => gettype($rowData),
                    'rowData_keys' => is_array($rowData) ? array_keys($rowData) : 'NOT_ARRAY',
                    'rowData_empty' => empty($rowData)
                ]);
                
                $normalizedRowData = [];
                if (is_array($rowData) && !empty($rowData)) {
                    try {
                        foreach ($rowData as $key => $value) {
                            // Skip null keys - this can cause "array offset on null" errors
                            if ($key === null) {
                                Log::warning("VEHICLE_IMPORT_DEBUG: Skipping null key during normalization", [
                                    'row_number' => $rowNumber
                                ]);
                                continue;
                            }
                            
                            // Ensure key is a string before processing
                            $keyString = is_string($key) ? $key : (string)$key;
                            
                            // Guard against null keyString
                            if ($keyString === null || $keyString === '') {
                                Log::warning("VEHICLE_IMPORT_DEBUG: Skipping empty keyString", [
                                    'row_number' => $rowNumber,
                                    'original_key' => $key,
                                    'key_type' => gettype($key)
                                ]);
                                continue;
                            }
                            
                            $normalizedKey = strtolower(str_replace([' ', '-'], '_', trim($keyString)));
                            
                            Log::debug("VEHICLE_IMPORT_DEBUG: Normalizing key", [
                                'row_number' => $rowNumber,
                                'original_key' => $key,
                                'key_type' => gettype($key),
                                'key_string' => $keyString,
                                'normalized_key' => $normalizedKey,
                                'value' => $value,
                                'value_type' => gettype($value)
                            ]);
                            
                            $normalizedRowData[$normalizedKey] = $value;
                        }
                    } catch (\Exception $e) {
                        Log::error("VEHICLE_IMPORT_ERROR: Failed to normalize row keys", [
                            'row_number' => $rowNumber,
                            'error_message' => $e->getMessage(),
                            'error_file' => $e->getFile(),
                            'error_line' => $e->getLine(),
                            'current_key' => isset($key) ? var_export($key, true) : 'NOT_SET',
                            'rowData_keys' => is_array($rowData) ? array_keys($rowData) : 'NOT_ARRAY',
                            'rowData_type' => gettype($rowData),
                            'stack_trace' => $e->getTraceAsString()
                        ]);
                        
                        $errorMsg = "VEHICLE_IMPORT_ERROR: Failed to normalize row $rowNumber keys. Error: " . $e->getMessage() . " | Current key: " . (isset($key) ? var_export($key, true) : 'NOT_SET') . " | RowData keys: " . (is_array($rowData) ? implode(', ', array_keys($rowData)) : 'NOT_ARRAY');
                        throw new \Exception($errorMsg);
                    }
                }
                $rowData = $normalizedRowData;
                
                // Comprehensive diagnostic logging
                Log::info("VEHICLE_IMPORT_DEBUG: After normalization", [
                    'row_number' => $rowNumber,
                    'normalized_keys' => array_keys($normalizedRowData),
                    'normalized_keys_count' => count($normalizedRowData),
                    'has_registration_plate' => isset($normalizedRowData['registration_plate']),
                    'registration_plate_value' => $normalizedRowData['registration_plate'] ?? 'NOT_SET',
                    'has_price' => isset($normalizedRowData['price']),
                    'price_value' => $normalizedRowData['price'] ?? 'NOT_SET',
                    'has_price_period' => isset($normalizedRowData['price_period']),
                    'price_period_value' => $normalizedRowData['price_period'] ?? 'NOT_SET',
                    'has_vehicle_status' => isset($normalizedRowData['vehicle_status']),
                    'vehicle_status_value' => $normalizedRowData['vehicle_status'] ?? 'NOT_SET',
                    'has_insurance_discount' => isset($normalizedRowData['insurance_discount']),
                    'insurance_discount_value' => $normalizedRowData['insurance_discount'] ?? 'NOT_SET',
                    'has_initial_cost' => isset($normalizedRowData['initial_cost']),
                    'initial_cost_value' => $normalizedRowData['initial_cost'] ?? 'NOT_SET',
                    'has_vehicle_scheme' => isset($normalizedRowData['vehicle_scheme']),
                    'vehicle_scheme_value' => $normalizedRowData['vehicle_scheme'] ?? 'NOT_SET',
                    'has_telematics_link' => isset($normalizedRowData['telematics_link']),
                    'telematics_link_value' => $normalizedRowData['telematics_link'] ?? 'NOT_SET',
                    'rowData_type' => gettype($rowData),
                    'rowData_is_array' => is_array($rowData),
                    'rowData_keys' => is_array($rowData) ? array_keys($rowData) : 'NOT_ARRAY',
                    'rowData_sample' => is_array($rowData) ? array_slice($rowData, 0, 10, true) : 'NOT_ARRAY',
                ]);
                
                // CRITICAL: Ensure $rowData is always an array before any access
                if (!is_array($rowData)) {
                    Log::error("VEHICLE_IMPORT_ERROR: rowData is not an array after normalization", [
                        'row_number' => $rowNumber,
                        'rowData_type' => gettype($rowData),
                        'rowData_value' => $rowData
                    ]);
                    throw new \Exception("VEHICLE_IMPORT_ERROR: rowData is not an array after normalization. Type: " . gettype($rowData));
                }
                
                // Skip empty rows - use safe array access
                // CRITICAL: Ensure $rowData is an array before accessing keys
                if (!is_array($rowData) || empty($rowData)) {
                    Log::warning("VEHICLE_IMPORT_DEBUG: Skipping row $rowNumber - rowData is not a valid array", [
                        'row_number' => $rowNumber,
                        'rowData_type' => gettype($rowData),
                        'rowData_value' => $rowData
                    ]);
                    \DB::rollBack();
                    continue;
                }
                
                Log::info("VEHICLE_IMPORT_DEBUG: Before accessing registration_plate", [
                    'row_number' => $rowNumber,
                    'rowData_keys' => array_keys($rowData),
                    'has_registration_plate' => isset($rowData['registration_plate']),
                    'registration_plate_value' => $rowData['registration_plate'] ?? 'NOT_SET'
                ]);
                
                if (empty($rowData['registration_plate'] ?? null)) {
                    Log::info("VEHICLE_IMPORT_DEBUG: Skipping empty row $rowNumber", [
                        'row_number' => $rowNumber,
                        'rowData_keys' => array_keys($rowData),
                        'has_registration_plate' => isset($rowData['registration_plate'])
                    ]);
                    \DB::rollBack();
                    continue;
                }
                
                // CRITICAL: Final validation that $rowData is still an array before processing
                if (!is_array($rowData)) {
                    $errorMsg = "VEHICLE_IMPORT_ERROR at row $rowNumber: rowData is not an array before main processing. Type: " . gettype($rowData) . ". This indicates a critical data conversion issue.";
                    Log::error("VEHICLE_IMPORT_ERROR: " . $errorMsg);
                    throw new \Exception($errorMsg);
                }
                
                // Extract all values with null safety BEFORE any processing
                // Handle alternative column name formats from Laravel Excel slug formatter
                // Laravel Excel may produce: "price-period" or "price_period" -> both normalize to "price_period"
                $getValue = function($key, $alternatives = []) use ($rowData) {
                    if (isset($rowData[$key]) && $rowData[$key] !== null && $rowData[$key] !== '') {
                        return $rowData[$key];
                    }
                    foreach ($alternatives as $alt) {
                        if (isset($rowData[$alt]) && $rowData[$alt] !== null && $rowData[$alt] !== '') {
                            return $rowData[$alt];
                        }
                    }
                    return null;
                };
                
                // Extract values once to avoid duplicate calls
                $rawPrice = $getValue('price', ['price']);
                $rawPricePeriod = $getValue('price_period', ['price_period', 'price-period']);
                $rawInitialCost = $getValue('initial_cost', ['initial_cost', 'initial-cost']);
                $rawVehicleScheme = $getValue('vehicle_scheme', ['vehicle_scheme', 'vehicle-scheme']);
                $rawInsuranceDiscount = $getValue('insurance_discount', ['insurance_discount', 'insurance-discount']);
                $rawVehicleStatus = $getValue('vehicle_status', ['vehicle_status', 'vehicle-status']);
                $rawTelematicsLink = $getValue('telematics_link', ['telematics_link', 'telematics-link']);
                
                $safeRowData = [
                    'registration_plate' => ($val = $getValue('registration_plate', ['registration_plate'])) ? (string)$val : '',
                    'make' => ($val = $getValue('make', ['make'])) ? (string)$val : '',
                    'model' => ($val = $getValue('model', ['model'])) ? (string)$val : '',
                    'year' => $getValue('year', ['year']) ?: null,
                    'color' => ($val = $getValue('color', ['color'])) ? (string)$val : 'Unknown',
                    'vehicle_type' => ($val = $getValue('vehicle_type', ['vehicle_type', 'vehicle-type'])) ? (string)$val : 'Unknown',
                    'fuel_type' => ($val = $getValue('fuel_type', ['fuel_type', 'fuel-type'])) ? (string)$val : 'Petrol',
                    'mileage' => $getValue('mileage', ['mileage']) ?: 0,
                    'price' => $rawPrice ? (string)$rawPrice : null,
                    // Remove default - only use CSV value, normalize to lowercase
                    'price_period' => $rawPricePeriod ? strtolower(trim((string)$rawPricePeriod)) : null,
                    'initial_cost' => $rawInitialCost ? (string)$rawInitialCost : null,
                    'vehicle_scheme' => $rawVehicleScheme ? (string)$rawVehicleScheme : null,
                    'insurance_discount' => $rawInsuranceDiscount ? (string)$rawInsuranceDiscount : null,
                    'available' => $getValue('available', ['available']) ?: null,
                    // Remove default - only use CSV value, normalize to proper case (Available, Rented)
                    'vehicle_status' => $rawVehicleStatus ? ucfirst(strtolower(trim((string)$rawVehicleStatus))) : null,
                    'vehicle_group' => ($val = $getValue('vehicle_group', ['vehicle_group', 'vehicle-group'])) ? (string)$val : null,
                    'mot_expiry_day' => $getValue('mot_expiry_day', ['mot_expiry_day', 'mot-expiry-day', 'mot_expiry_da']) ?: null,
                    'mot_expiry_month' => $getValue('mot_expiry_month', ['mot_expiry_month', 'mot-expiry-month']) ?: null,
                    'mot_expiry_year' => $getValue('mot_expiry_year', ['mot_expiry_year', 'mot-expiry-year']) ?: null,
                    'telematics_link' => $rawTelematicsLink ? (string)$rawTelematicsLink : null,
                    'assigned_driver_first_name' => ($val = $getValue('assigned_driver_first_name', ['assigned_driver_first_name', 'assigned-driver-first-name'])) ? (string)$val : null,
                    'assigned_driver_last_name' => ($val = $getValue('assigned_driver_last_name', ['assigned_driver_last_name', 'assigned-driver-last-name'])) ? (string)$val : null,
                ];
                
                // Log extracted values for debugging
                Log::info("VEHICLE_IMPORT_DEBUG: Extracted CSV values", [
                    'row_number' => $rowNumber,
                    'csv_keys' => array_keys($rowData),
                    'extracted_values' => [
                        'price' => $safeRowData['price'],
                        'price_period' => $safeRowData['price_period'],
                        'initial_cost' => $safeRowData['initial_cost'],
                        'vehicle_scheme' => $safeRowData['vehicle_scheme'],
                        'insurance_discount' => $safeRowData['insurance_discount'],
                        'vehicle_status' => $safeRowData['vehicle_status'],
                        'telematics_link' => $safeRowData['telematics_link'],
                        'mot_expiry_day' => $safeRowData['mot_expiry_day'],
                        'mot_expiry_month' => $safeRowData['mot_expiry_month'],
                        'mot_expiry_year' => $safeRowData['mot_expiry_year'],
                    ]
                ]);
                
                Log::info("VEHICLE_IMPORT_DEBUG: Starting main processing for row $rowNumber with safe data extraction", [
                    'row_number' => $rowNumber,
                    'rowData_type' => gettype($rowData),
                    'rowData_is_array' => is_array($rowData),
                    'rowData_keys' => is_array($rowData) ? array_keys($rowData) : 'NOT_ARRAY',
                    'registration_plate' => $safeRowData['registration_plate'],
                    'make' => $safeRowData['make'],
                    'model' => $safeRowData['model'],
                    'year' => $safeRowData['year']
                ]);
                
                // Normalize registration plate for duplicate checking - use safe access
                $registrationPlate = trim($safeRowData['registration_plate']);
                $normalizedPlate = $this->normalizeRegistrationPlate($registrationPlate);
                
                // Check for existing vehicle with same normalized registration plate
                $existingVehicle = VehicleModel::whereRaw('UPPER(REPLACE(REPLACE(license_plate, \' \', \'\'), \'-\', \'\')) = ?', [$normalizedPlate])->first();
                
                if ($existingVehicle) {
                    $this->importStats['duplicates_skipped']++;
                    Log::info('Duplicate vehicle skipped during import', [
                        'original_plate' => $safeRowData['registration_plate'],
                        'normalized_plate' => $normalizedPlate,
                        'existing_vehicle_id' => $existingVehicle->id,
                        'existing_plate' => $existingVehicle->license_plate
                    ]);
                    \DB::rollBack();
                    continue; // Skip this row as it's a duplicate
                }
                
                $this->importStats['processed']++;
                
                // Convert year to integer if it's a string - with null safety
                $yearValue = $safeRowData['year'];
                if ($yearValue !== null && is_string($yearValue)) {
                    $yearValue = (int) $yearValue;
                }
                
                // Clean and validate required fields - ensure we never pass null to trim()
                $registrationPlate = trim($safeRowData['registration_plate']);
                $make = trim($safeRowData['make']);
                $model = trim($safeRowData['model']);
                
                // Skip if essential fields are empty
                if (empty($registrationPlate) || empty($make) || empty($model)) {
                    $this->importStats['validation_failed']++;
                    $errorMsg = "VEHICLE_IMPORT_ERROR at row $rowNumber: Missing required fields. Registration Plate: '" . ($registrationPlate ?: 'EMPTY') . "', Make: '" . ($make ?: 'EMPTY') . "', Model: '" . ($model ?: 'EMPTY') . "'";
                    Log::warning("VEHICLE_IMPORT_DEBUG: " . $errorMsg);
                    \DB::rollBack();
                    continue;
                }
                
                // Validate input data - ensure $safeRowData is still an array before validation
                if (!is_array($safeRowData)) {
                    $errorMsg = "VEHICLE_IMPORT_ERROR at row $rowNumber: safeRowData is not an array before validator. Type: " . gettype($safeRowData);
                    Log::error("VEHICLE_IMPORT_ERROR: " . $errorMsg);
                    throw new \Exception($errorMsg);
                }
                
                Log::info("VEHICLE_IMPORT_DEBUG: Before Validator::make", [
                    'row_number' => $rowNumber,
                    'safeRowData_type' => gettype($safeRowData),
                    'safeRowData_is_array' => is_array($safeRowData),
                    'registration_plate' => $registrationPlate,
                    'make' => $make,
                    'model' => $model,
                    'year' => $yearValue
                ]);
                
                try {
                    $validator = Validator::make([
                        'registration_plate' => $registrationPlate,
                        'make' => $make,
                        'model' => $model,
                        'year' => $yearValue,
                    ], [
                        'registration_plate' => 'required|string|max:255',
                        'make' => 'required|string|max:255',
                        'model' => 'required|string|max:255',
                        'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                    ]);
                } catch (\Exception $e) {
                    $errorMsg = "VEHICLE_IMPORT_ERROR at row $rowNumber: Validator::make failed. Error: " . $e->getMessage() . " | safeRowData type: " . gettype($safeRowData);
                    Log::error("VEHICLE_IMPORT_ERROR: " . $errorMsg);
                    throw new \Exception($errorMsg);
                }

                if ($validator->fails()) {
                    $this->importStats['validation_failed']++;
                    $errorMsg = "VEHICLE_IMPORT_ERROR at row $rowNumber: Validation failed. Errors: " . json_encode($validator->errors()->all());
                    Log::warning('VEHICLE_IMPORT_DEBUG: ' . $errorMsg);
                    \DB::rollBack();
                    continue;
                }

                        // Find or create vehicle type
                        $type = null;
                        try {
                            $vehicleTypeName = $safeRowData['vehicle_type'];
                            if (empty($vehicleTypeName)) {
                                $vehicleTypeName = 'Unknown';
                            }
                            $type = VehicleTypeModel::firstOrCreate(
                                ['name' => $vehicleTypeName],
                                ['name' => $vehicleTypeName]
                            );
                            if (!$type || !$type->id) {
                                throw new \Exception("Failed to create or find vehicle type: " . $vehicleTypeName);
                            }
                        } catch (\Exception $e) {
                            $errorMsg = "VEHICLE_IMPORT_ERROR at row $rowNumber: Failed to create vehicle type. Error: " . $e->getMessage() . " | Vehicle Type: " . $safeRowData['vehicle_type'];
                            Log::error("VEHICLE_IMPORT_ERROR: " . $errorMsg);
                            throw new \Exception($errorMsg);
                        }

                // Find or create vehicle group
                $group = null;
                $vehicleGroup = $safeRowData['vehicle_group'];
                if (!empty($vehicleGroup) && $vehicleGroup !== null) {
                    try {
                        $groupName = trim((string)$vehicleGroup);
                        $group = VehicleGroupModel::firstOrCreate(
                            ['name' => $groupName],
                            ['name' => $groupName]
                        );
                    } catch (\Exception $e) {
                        Log::warning("VEHICLE_IMPORT_DEBUG: Failed to create vehicle group for row $rowNumber", [
                            'row_number' => $rowNumber,
                            'group_name' => $vehicleGroup,
                            'error' => $e->getMessage()
                        ]);
                        // Continue without group assignment
                    }
                }

                // Find driver if assigned
                $driver = null;
                $driverFirstName = $safeRowData['assigned_driver_first_name'];
                $driverLastName = $safeRowData['assigned_driver_last_name'];
                if (!empty($driverFirstName) && !empty($driverLastName)) {
                    $driverName = trim((string)$driverFirstName) . ' ' . trim((string)$driverLastName);
                    $driver = User::where('name', $driverName)->first();
                }

                // Create MOT expiry date - FIXED to handle zero-padded values
                $motExpiryDate = null;
                $motDay = $safeRowData['mot_expiry_day'];
                $motMonth = $safeRowData['mot_expiry_month'];
                $motYear = $safeRowData['mot_expiry_year'];
                
                if (!empty($motDay) && !empty($motMonth) && !empty($motYear)) {
                    try {
                        // Handle 2-digit years (e.g., 25 becomes 2025)
                        $year = intval($motYear ?? 0);
                        if ($year < 100 && $year > 0) {
                            $year += 2000; // Convert 25 to 2025
                        }
                        
                        // Convert day and month to integers to handle zero-padded values (e.g., "09" -> 9)
                        $day = intval($motDay ?? 0);
                        $month = intval($motMonth ?? 0);
                        
                        // Validate the date components
                        if ($day >= 1 && $day <= 31 && $month >= 1 && $month <= 12 && $year >= 1900) {
                            $motExpiryDate = Carbon::create($year, $month, $day);
                            
                            Log::info('VEHICLE_IMPORT_DEBUG: MOT expiry date created successfully', [
                                'row_number' => $rowNumber,
                                'original_day' => $motDay,
                                'original_month' => $motMonth,
                                'original_year' => $motYear,
                                'parsed_date' => $motExpiryDate->format('Y-m-d'),
                                'vehicle' => $safeRowData['registration_plate']
                            ]);
                        } else {
                            Log::warning('VEHICLE_IMPORT_DEBUG: Invalid MOT expiry date components', [
                                'row_number' => $rowNumber,
                                'day' => $day,
                                'month' => $month,
                                'year' => $year,
                                'original_day' => $motDay,
                                'original_month' => $motMonth,
                                'original_year' => $motYear,
                                'vehicle' => $safeRowData['registration_plate']
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::warning('VEHICLE_IMPORT_DEBUG: Invalid MOT expiry date - Carbon creation failed', [
                            'row_number' => $rowNumber,
                            'day' => $motDay ?? 'NULL',
                            'month' => $motMonth ?? 'NULL',
                            'year' => $motYear ?? 'NULL',
                            'error' => $e->getMessage(),
                            'vehicle' => $safeRowData['registration_plate']
                        ]);
                    }
                } else {
                    Log::info('VEHICLE_IMPORT_DEBUG: MOT expiry date fields are empty', [
                        'row_number' => $rowNumber,
                        'day' => $motDay ?? 'empty',
                        'month' => $motMonth ?? 'empty',
                        'year' => $motYear ?? 'empty',
                        'vehicle' => $safeRowData['registration_plate']
                    ]);
                }

                // Normalize Available -> boolean (explicitly ensure boolean type for PostgreSQL)
                $availableValue = $safeRowData['available'];
                $isAvailableNormalized = FieldNormalizers::toBoolean($availableValue);
                if (!empty($safeRowData['available']) && $isAvailableNormalized === null) {
                    $this->importStats['validation_failed']++;
                    $errorMsg = "VEHICLE_IMPORT_ERROR at row $rowNumber: Invalid Available value. Value: " . var_export($availableValue, true);
                    Log::warning("VEHICLE_IMPORT_DEBUG: " . $errorMsg);
                    \DB::rollBack();
                    continue;
                }
                // Explicitly cast to boolean to ensure PostgreSQL receives true/false, not 1/0
                $isAvailable = ($isAvailableNormalized === null ? true : $isAvailableNormalized) ? true : false;

                // Create vehicle - REMOVED exp_date as it doesn't exist in vehicles table
                $vehicleData = [
                    'license_plate' => $registrationPlate,
                    'make_name' => $make,
                    'model_name' => $model,
                    'year' => $yearValue,
                    'color_name' => $safeRowData['color'],
                    'type' => $safeRowData['vehicle_type'],
                    'engine_type' => $safeRowData['fuel_type'],
                    'mileage' => (int) $safeRowData['mileage'],
                    'int_mileage' => (int) $safeRowData['mileage'],
                    'in_service' => $isAvailable,
                    'group_id' => ($group && isset($group->id)) ? $group->id : null,
                    'type_id' => ($type && isset($type->id)) ? $type->id : null,
                    'user_id' => auth()->id(),
                    'company_id' => (auth()->user() && isset(auth()->user()->company_id)) ? auth()->user()->company_id : null,
                ];
                
                Log::info("VEHICLE_IMPORT_DEBUG: Creating vehicle for row $rowNumber", [
                    'row_number' => $rowNumber,
                    'vehicle_data' => $vehicleData,
                    'in_service' => $isAvailable
                ]);
                
                // Use VehicleModel::create() which properly handles the mutator and performInsert
                $vehicle = VehicleModel::create($vehicleData);

                // Set metadata - ensure all fields are saved INCLUDING MOT expiry date
                // Only save non-empty values (null or empty string are skipped)
                try {
                    $metadataSaved = [];
                    
                    if (!empty($safeRowData['vehicle_status']) && $safeRowData['vehicle_status'] !== null) {
                        $vehicle->setMeta('vehicle_status', $safeRowData['vehicle_status']);
                        $metadataSaved['vehicle_status'] = $safeRowData['vehicle_status'];
                    }
                    
                    if (!empty($safeRowData['vehicle_scheme']) && $safeRowData['vehicle_scheme'] !== null) {
                        $vehicle->setMeta('vehicle_scheme', $safeRowData['vehicle_scheme']);
                        $metadataSaved['vehicle_scheme'] = $safeRowData['vehicle_scheme'];
                    }
                    
                    if (!empty($safeRowData['price']) && $safeRowData['price'] !== null && trim($safeRowData['price']) !== '') {
                        $vehicle->setMeta('price', trim($safeRowData['price']));
                        $metadataSaved['price'] = trim($safeRowData['price']);
                    }
                    
                    if (!empty($safeRowData['price_period']) && $safeRowData['price_period'] !== null && trim($safeRowData['price_period']) !== '') {
                        $vehicle->setMeta('price_period', trim($safeRowData['price_period']));
                        $metadataSaved['price_period'] = trim($safeRowData['price_period']);
                    }
                    
                    if (!empty($safeRowData['initial_cost']) && $safeRowData['initial_cost'] !== null && trim($safeRowData['initial_cost']) !== '') {
                        $vehicle->setMeta('initial_cost', trim($safeRowData['initial_cost']));
                        $metadataSaved['initial_cost'] = trim($safeRowData['initial_cost']);
                    }
                    
                    if (!empty($safeRowData['insurance_discount']) && $safeRowData['insurance_discount'] !== null && trim($safeRowData['insurance_discount']) !== '') {
                        $vehicle->setMeta('insurance_discount', trim($safeRowData['insurance_discount']));
                        $metadataSaved['insurance_discount'] = trim($safeRowData['insurance_discount']);
                    }
                    
                    if (!empty($safeRowData['telematics_link']) && $safeRowData['telematics_link'] !== null && trim($safeRowData['telematics_link']) !== '') {
                        $vehicle->setMeta('telematics_link', trim($safeRowData['telematics_link']));
                        $metadataSaved['telematics_link'] = trim($safeRowData['telematics_link']);
                    }
                    
                    // Save metadata changes
                    $vehicle->save();
                    
                    Log::info('VEHICLE_IMPORT_DEBUG: Vehicle metadata saved', [
                        'vehicle_id' => $vehicle->id,
                        'registration_plate' => $vehicle->license_plate,
                        'metadata_saved' => $metadataSaved,
                        'metadata_source' => [
                            'vehicle_status' => $safeRowData['vehicle_status'],
                            'vehicle_scheme' => $safeRowData['vehicle_scheme'],
                            'price' => $safeRowData['price'],
                            'price_period' => $safeRowData['price_period'],
                            'initial_cost' => $safeRowData['initial_cost'],
                            'insurance_discount' => $safeRowData['insurance_discount'],
                            'telematics_link' => $safeRowData['telematics_link']
                        ]
                    ]);
                } catch (\Exception $e) {
                    Log::warning("VEHICLE_IMPORT_ERROR: Failed to set metadata for vehicle {$vehicle->id} in row $rowNumber", [
                        'error' => $e->getMessage(),
                        'error_file' => $e->getFile(),
                        'error_line' => $e->getLine(),
                        'metadata_values' => [
                            'vehicle_status' => $safeRowData['vehicle_status'] ?? 'NOT_SET',
                            'vehicle_scheme' => $safeRowData['vehicle_scheme'] ?? 'NOT_SET',
                            'price' => $safeRowData['price'] ?? 'NOT_SET',
                            'price_period' => $safeRowData['price_period'] ?? 'NOT_SET',
                            'initial_cost' => $safeRowData['initial_cost'] ?? 'NOT_SET',
                            'insurance_discount' => $safeRowData['insurance_discount'] ?? 'NOT_SET',
                            'telematics_link' => $safeRowData['telematics_link'] ?? 'NOT_SET',
                        ]
                    ]);
                    // Continue - metadata is not critical for basic vehicle creation
                }
                
                // FIXED: Store MOT expiry date in metadata instead of non-existent exp_date column
                if ($motExpiryDate) {
                    try {
                        $vehicle->setMeta('mot_expiry_date', $motExpiryDate->format('Y-m-d'));
                        $vehicle->setMeta('exp_date', $motExpiryDate->format('Y-m-d')); // For compatibility
                        
                        Log::info('MOT expiry date saved to metadata', [
                            'vehicle_id' => $vehicle->id,
                            'mot_date' => $motExpiryDate->format('Y-m-d'),
                            'vehicle' => $safeRowData['registration_plate']
                        ]);
                        // Ensure persistence after setting metadata
                        $vehicle->save();
                    } catch (\Exception $e) {
                        Log::warning("Failed to save MOT expiry date for vehicle {$vehicle->id} in row $rowNumber", [
                            'error' => $e->getMessage()
                        ]);
                        // Continue - MOT date is not critical for basic vehicle creation
                    }
                }
                
                if ($driver && isset($driver->id)) {
                    try {
                        $vehicle->setMeta('assign_driver_id', $driver->id);
                    } catch (\Exception $e) {
                        Log::warning("Failed to assign driver for vehicle {$vehicle->id} in row $rowNumber", [
                            'driver_name' => $driverName ?? 'NOT_SET',
                            'error' => $e->getMessage()
                        ]);
                        // Continue - driver assignment is not critical for basic vehicle creation
                    }
                }
                

                $this->importStats['successfully_imported']++;
                Log::info('Vehicle imported successfully', [
                    'vehicle_id' => $vehicle->id,
                    'license_plate' => $vehicle->license_plate,
                    'mot_expiry_date' => $motExpiryDate ? $motExpiryDate->format('Y-m-d') : 'null'
                ]);

                \DB::commit();
                } catch (\Exception $e) {
                    $this->importStats['errors']++;
                    
                    // Get detailed error info for debugging
                    $errorDetails = [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'row_type' => gettype($row),
                        'row_class' => is_object($row) ? get_class($row) : null,
                        'rowData_type' => isset($rowData) ? gettype($rowData) : 'NOT_SET',
                        'rowData_is_array' => isset($rowData) && is_array($rowData),
                    ];
                    
                    // Try to get more info about the row - with null safety
                    try {
                        if (isset($rowData) && is_array($rowData)) {
                            $errorDetails['rowData_keys'] = array_keys($rowData);
                            $errorDetails['rowData_count'] = count($rowData);
                            $errorDetails['rowData_sample'] = array_slice($rowData, 0, 5, true);
                        } elseif (isset($row) && $row instanceof \Illuminate\Support\Collection) {
                            try {
                                $errorDetails['collection_count'] = $row->count();
                                $errorDetails['collection_keys'] = $row->keys()->toArray();
                            } catch (\Exception $collectionError) {
                                $errorDetails['collection_error'] = $collectionError->getMessage();
                            }
                        }
                    } catch (\Exception $infoError) {
                        $errorDetails['info_error'] = $infoError->getMessage();
                    }
                    
                    Log::error("VEHICLE_IMPORT_ERROR: Array access failed", [
                        'row_number' => $rowNumber,
                        'error_message' => $e->getMessage(),
                        'error_file' => $e->getFile(),
                        'error_line' => $e->getLine(),
                        'rowData_type' => isset($rowData) ? gettype($rowData) : 'NOT_SET',
                        'rowData_is_array' => isset($rowData) && is_array($rowData),
                        'rowData_keys' => (isset($rowData) && is_array($rowData)) ? array_keys($rowData) : 'NOT_ARRAY',
                        'error_details' => $errorDetails,
                        'stack_trace' => $e->getTraceAsString()
                    ]);
                    
                    // Add error details for user feedback with more context
                    if (!isset($this->importStats['error_details'])) {
                        $this->importStats['error_details'] = [];
                    }
                    $errorMsg = "Row $rowNumber: " . $e->getMessage();
                    if (isset($errorDetails['rowData_type'])) {
                        $errorMsg .= " | Row type: " . $errorDetails['row_type'] . ", RowData type: " . $errorDetails['rowData_type'];
                    }
                    if (isset($errorDetails['rowData_keys'])) {
                        $errorMsg .= " | RowData keys: " . implode(', ', $errorDetails['rowData_keys']);
                    }
                    // Add file and line number for debugging
                    if (isset($errorDetails['file']) && isset($errorDetails['line'])) {
                        $errorMsg .= " | Error at: " . basename($errorDetails['file']) . ":" . $errorDetails['line'];
                    }
                    $this->importStats['error_details'][] = $errorMsg;
                    \DB::rollBack();
                }
            }
        // Log final import statistics
        Log::info("VEHICLE_IMPORT_DEBUG: Vehicle import completed", [
            'stats' => $this->importStats,
            'total_rows' => $this->importStats['total_rows'],
            'successfully_imported' => $this->importStats['successfully_imported'],
            'errors' => $this->importStats['errors'],
            'validation_failed' => $this->importStats['validation_failed'],
            'duplicates_skipped' => $this->importStats['duplicates_skipped']
        ]);
        \Log::info('VEHICLE IMPORT SUMMARY', $this->importStats);
    }
}