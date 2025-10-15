<?php
/*
@copyright
Fleet Manager v7.1.2
Copyright (C) 2017-2025 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>
 */
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportRequest;
use App\Http\Requests\InsuranceRequest;
use App\Http\Requests\VehicleRequest;
use App\Http\Requests\VehiclReviewRequest;
use App\Imports\VehicleImport;
use App\Model\Bookings;
use App\Model\DriverLogsModel;
use App\Model\DriverVehicleModel;
use App\Model\Expense;
use App\Model\FuelModel;
use App\Model\Hyvikk;
use App\Model\IncomeModel;
use App\Model\ServiceReminderModel;
use App\Model\User;
use App\Model\VehicleGroupModel;
use App\Model\VehicleModel;
use App\Model\VehicleReviewModel;
use App\Model\VehicleTypeModel;
use Auth;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Redirect;

class VehiclesController extends Controller {
        public function __construct() {
                // $this->middleware(['role:Admin']);
                $this->middleware('permission:Vehicles add', ['only' => ['create', 'upload_file', 'upload_doc', 'store']]);
                $this->middleware('permission:Vehicles edit', ['only' => ['edit', 'upload_file', 'upload_doc', 'update']]);
                $this->middleware('permission:Vehicles delete', ['only' => ['bulk_delete', 'destroy']]);
                $this->middleware('permission:Vehicles list', ['only' => ['index', 'driver_logs', 'view_event', 'store_insurance', 'assign_driver']]);
                $this->middleware('permission:Vehicles import', ['only' => ['importVehicles']]);
                $this->middleware('permission:VehicleInspection add', ['only' => ['vehicle_review', 'store_vehicle_review', 'vehicle_inspection_create']]);
                $this->middleware('permission:VehicleInspection edit', ['only' => ['review_edit', 'update_vehicle_review']]);
                $this->middleware('permission:VehicleInspection delete', ['only' => ['bulk_delete_reviews', 'destroy_vehicle_review']]);
                $this->middleware('permission:VehicleInspection list', ['only' => ['vehicle_review_index', 'print_vehicle_review', 'view_vehicle_review']]);
        }
        public function importVehicles(ImportRequest $request) {
                $file = $request->excel;
                $destinationPath = './uploads/xml'; // upload path
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::uuid() . '.' . $extension;

                // Ensure the uploads directory exists and is writable
                if (!is_dir($destinationPath)) {
                        mkdir($destinationPath, 0755, true); // Create directory if not exists
                }
                if (!is_writable($destinationPath)) {
                        return back()->withErrors(['error' => 'The upload directory is not writable.']);
                }

                $file->move($destinationPath, $fileName);
                Excel::import(new VehicleImport, $destinationPath . '/' . $fileName);
                return back();
        }
        public function index() {
                $user = Auth::user();
                // Get vehicles data for initial load - fix PostgreSQL GROUP BY issue
                if ($user->getRawOriginal('user_type') == "B") {
                    // Boss Admin - if no company assigned, see no vehicles
                    if (is_null($user->company_id)) {
                        $vehicles = collect([]);
                    } else {
                        // Boss Admin with a company assigned (rare): limit to their company
                        $vehicles = VehicleModel::select('vehicles.*', DB::raw('MAX(users.name) as driver_name'), DB::raw('MAX(users.id) as driver_id'))
                            ->where('vehicles.company_id', $user->company_id)
                            ->leftJoin('driver_vehicle', 'driver_vehicle.vehicle_id', '=', 'vehicles.id')
                            ->leftJoin('users', 'users.id', '=', 'driver_vehicle.driver_id')
                            ->with('types', 'company') // Load vehicle types and company relationship
                            ->groupBy('vehicles.id')
                            ->get();
                    }
                } elseif (($user->getRawOriginal('user_type') == "S" || $user->getRawOriginal('user_type') == "O")) {
                    // Super/Office Admin - must have a company assigned; otherwise none
                    if (is_null($user->company_id)) {
                        $vehicles = collect([]);
                    } else {
                        $vehicles = VehicleModel::select('vehicles.*', DB::raw('MAX(users.name) as driver_name'), DB::raw('MAX(users.id) as driver_id'))
                            ->where('vehicles.company_id', $user->company_id)
                            ->leftJoin('driver_vehicle', 'driver_vehicle.vehicle_id', '=', 'vehicles.id')
                            ->leftJoin('users', 'users.id', '=', 'driver_vehicle.driver_id')
                            ->with('types', 'company') // Load vehicle types and company relationship
                            ->groupBy('vehicles.id')
                            ->get();
                    }
                } else {
                    // Driver - see only assigned vehicles
                    $vehicle_ids = $user->vehicles->pluck('id')->toArray();
                    $vehicles = VehicleModel::select('vehicles.*', DB::raw('MAX(users.name) as driver_name'), DB::raw('MAX(users.id) as driver_id'))
                        ->whereIn('vehicles.id', $vehicle_ids)
                        ->leftJoin('driver_vehicle', 'driver_vehicle.vehicle_id', '=', 'vehicles.id')
                        ->leftJoin('users', 'users.id', '=', 'driver_vehicle.driver_id')
                        ->with('types', 'company') // Load vehicle types and company relationship
                        ->groupBy('vehicles.id')
                        ->get();
                }

                // Already handled Boss-without-company above
                
                // Get filter data
                $groups = \App\Model\VehicleGroupModel::where('deleted_at', null)->get();
                $vehicle_types = \App\Model\VehicleTypeModel::where('deleted_at', null)->get();
                
                // Get all available drivers for dropdown
                $drivers = User::where('user_type', 'D')
                        ->whereHas('metas', function($query) {
                                $query->where('key', 'is_active')
                                      ->where('value', '1');
                        })
                        ->get();
                
                // Debug: Log the vehicles data structure
                \Log::info('Vehicles loaded for index:', [
                    'count' => $vehicles->count(),
                    'first_vehicle' => $vehicles->first() ? $vehicles->first()->toArray() : null,
                    'sample_ids' => $vehicles->take(3)->pluck('id')->toArray()
                ]);
                
                return view("vehicles.index", compact('vehicles', 'groups', 'vehicle_types', 'drivers'));
        }
        
        public function show($id) {
                $vehicle = VehicleModel::with(['group', 'types', 'drivers'])->findOrFail($id);
                
                // Debug: Direct database query to verify metadata
                $directMeta = DB::table('vehicles_meta')
                    ->where('vehicle_id', $id)
                    ->whereIn('key', ['vehicle_price', 'insurance_discount', 'initial_cost', 'price_period', 'vehicle_scheme', 'price'])
                    ->pluck('value', 'key');
                
                // Debug: Get all metadata for this vehicle
                $allMeta = DB::table('vehicles_meta')
                    ->where('vehicle_id', $id)
                    ->get();
                
                // Build purchase info from individual metadata fields to ensure display
                $purchaseInfo = [];
                
                // Check if we have individual metadata fields and build purchase info
                $vehiclePrice = $vehicle->getMeta('vehicle_price');
                $initialCost = $vehicle->getMeta('initial_cost');
                
                // Enhanced debugging and fallback logic
                if (!$vehiclePrice) {
                    // Try alternative key names
                    $vehiclePrice = $vehicle->getMeta('price') ?: $directMeta['price'] ?: $directMeta['vehicle_price'];
                }
                if (!$initialCost) {
                    $initialCost = $directMeta['initial_cost'];
                }
                
                // Debug logging (remove in production)
                \Log::info('Vehicle Show Debug', [
                    'vehicle_id' => $id,
                    'direct_meta' => $directMeta->toArray(),
                    'getMeta_vehicle_price' => $vehicle->getMeta('vehicle_price'),
                    'getMeta_initial_cost' => $vehicle->getMeta('initial_cost'),
                    'final_vehicle_price' => $vehiclePrice,
                    'final_initial_cost' => $initialCost,
                    'all_meta_count' => $allMeta->count()
                ]);
                
                // For existing vehicles with missing metadata, try to fix it
                if (!$vehiclePrice && !$initialCost && $allMeta->count() > 0) {
                    // Check if there's legacy purchase_info data we can extract
                    $legacyPurchaseInfo = $vehicle->getMeta('purchase_info');
                    if ($legacyPurchaseInfo) {
                        try {
                            $legacyData = json_decode($legacyPurchaseInfo, true) ?: unserialize($legacyPurchaseInfo);
                            if (is_array($legacyData)) {
                                foreach ($legacyData as $item) {
                                    if (isset($item['exp_name']) && isset($item['exp_amount'])) {
                                        if (strpos($item['exp_name'], 'Price') !== false) {
                                            $vehiclePrice = $item['exp_amount'];
                                        } elseif (strpos($item['exp_name'], 'Initial') !== false) {
                                            $initialCost = $item['exp_amount'];
                                        }
                                    }
                                }
                            }
                        } catch (Exception $e) {
                            // Ignore errors in legacy data parsing
                        }
                    }
                }
                
                // Build purchase info as array of items (expected by view)
                $purchaseItems = [];
                if ($vehiclePrice) {
                    $purchaseItems[] = [
                        'exp_name' => 'Vehicle Price (' . ($vehicle->getMeta('price_period') ?: $directMeta['price_period'] ?: 'Monthly') . ')',
                        'exp_amount' => (float)$vehiclePrice
                    ];
                }
                if ($initialCost) {
                    $purchaseItems[] = [
                        'exp_name' => 'Initial Cost',
                        'exp_amount' => (float)$initialCost
                    ];
                }
                
                if (count($purchaseItems) > 0) {
                        $purchaseInfo = $purchaseItems;
                } else {
                        // Fallback to check legacy purchase_info metadata
                        $purchaseInfoRaw = $vehicle->getMeta('purchase_info');
                        if ($purchaseInfoRaw) {
                                try {
                                        // Try JSON first (preferred secure method)
                                        $purchaseInfo = json_decode($purchaseInfoRaw, true);
                                        if (json_last_error() !== JSON_ERROR_NONE) {
                                                // Fallback to unserialize for legacy data (with validation)
                                                if (is_string($purchaseInfoRaw) && strpos($purchaseInfoRaw, 'a:') === 0) {
                                                        $purchaseInfo = @unserialize($purchaseInfoRaw);
                                                        if ($purchaseInfo === false) {
                                                                $purchaseInfo = [];
                                                        }
                                                }
                                        }
                                } catch (Exception $e) {
                                        $purchaseInfo = [];
                                }
                        }
                }
                
                // Ensure purchaseInfo is always an array
                if (!is_array($purchaseInfo)) {
                        $purchaseInfo = [];
                }
                
                // Get vehicle type properly - Enhanced with fallback logic
                $vehicleType = 'Not Selected';
                if ($vehicle->type_id) {
                        // First try the relationship
                        if ($vehicle->types && $vehicle->types->vehicletype) {
                                $vehicleType = $vehicle->types->vehicletype;
                        } else {
                                // Fallback to direct database query if relationship fails
                                $type = DB::table('vehicle_types')
                                        ->where('id', $vehicle->type_id)
                                        ->where('deleted_at', null)
                                        ->first();
                                if ($type && $type->vehicletype) {
                                        $vehicleType = $type->vehicletype;
                                }
                        }
                } else {
                        // Check metadata for vehicle type as final fallback
                        $metaVehicleType = $vehicle->getMeta('vehicle_type');
                        if ($metaVehicleType) {
                                $vehicleType = $metaVehicleType;
                        }
                }
                
                // Get driver information
                $driverName = 'Not Assigned';
                $driverId = $vehicle->getMeta('assign_driver_id');
                if ($driverId) {
                        $driver = User::find($driverId);
                        $driverName = $driver ? $driver->name : 'Driver Not Found';
                } else if ($vehicle->drivers && $vehicle->drivers->isNotEmpty()) {
                        $driverName = $vehicle->drivers->first()->name;
                }
                
                // Get group name properly - Enhanced with fallback logic
                $groupName = 'Not Selected';
                if ($vehicle->group_id) {
                        // First try the relationship
                        if ($vehicle->group && $vehicle->group->name) {
                                $groupName = $vehicle->group->name;
                        } else {
                                // Fallback to direct database query if relationship fails
                                $group = DB::table('vehicle_group')
                                        ->where('id', $vehicle->group_id)
                                        ->where('deleted_at', null)
                                        ->first();
                                if ($group && $group->name) {
                                        $groupName = $group->name;
                                }
                        }
                }
                
                // Get all additional metadata
                $additionalMeta = [
                        'scheme' => $vehicle->getMeta('scheme') ?: $vehicle->getMeta('vehicle_scheme'),
                        'telematics_link' => $vehicle->getMeta('telematics_link'),
                        'gps_number' => $vehicle->getMeta('gps_number'),
                        'rc_number' => $vehicle->getMeta('rc_number'),
                        'permit_number' => $vehicle->getMeta('permit_number'),
                        'permit_validity' => $vehicle->getMeta('permit_validity'),
                        'driving_license' => $vehicle->getMeta('driving_license'),
                        'ecocert' => $vehicle->getMeta('ecocert'),
                        'tax_token_number' => $vehicle->getMeta('tax_token_number'),
                        'tax_token_validity' => $vehicle->getMeta('tax_token_validity'),
                        'fitness_cert' => $vehicle->getMeta('fitness_cert'),
                        'fitness_validity' => $vehicle->getMeta('fitness_validity'),
                        'pollution_cert' => $vehicle->getMeta('pollution_cert'),
                        'pollution_validity' => $vehicle->getMeta('pollution_validity'),
                        'national_permit' => $vehicle->getMeta('national_permit'),
                        'national_permit_validity' => $vehicle->getMeta('national_permit_validity'),
                ];
                
                // Filter out null/empty metadata
                $additionalMeta = array_filter($additionalMeta, function($value) {
                        return !is_null($value) && $value !== '';
                });
                
                $data = [
                        'vehicle' => $vehicle,
                        'vehicle_type' => $vehicleType,
                        'group_name' => $groupName,
                        'driver_name' => $driverName,
                        'purchase_info' => $purchaseInfo,
                        'additional_meta' => $additionalMeta
                ];
                
                return view('vehicles.show', $data);
        }

        public function getCompleteData($id) {
                $vehicle = VehicleModel::with(['group', 'types', 'drivers'])->find($id);
                
                if (!$vehicle) {
                        return response()->json(['error' => 'Vehicle not found'], 404);
                }
                
                // Get purchase info metadata (secure JSON handling)
                $purchaseInfo = [];
                $purchaseInfoRaw = $vehicle->getMeta('purchase_info');
                if ($purchaseInfoRaw) {
                        try {
                                // Try JSON first (preferred secure method)
                                $purchaseInfo = json_decode($purchaseInfoRaw, true);
                                if (json_last_error() !== JSON_ERROR_NONE) {
                                        // Fallback to unserialize for legacy data (with validation)
                                        if (is_string($purchaseInfoRaw) && strpos($purchaseInfoRaw, 'a:') === 0) {
                                                $purchaseInfo = @unserialize($purchaseInfoRaw);
                                                if ($purchaseInfo === false) {
                                                        $purchaseInfo = [];
                                                }
                                        }
                                }
                        } catch (Exception $e) {
                                $purchaseInfo = [];
                                \Log::warning('Failed to decode purchase_info for vehicle ' . $id . ': ' . $e->getMessage());
                        }
                }
                
                // Ensure purchaseInfo is always an array
                if (!is_array($purchaseInfo)) {
                        $purchaseInfo = [];
                }
                
                // Get vehicle type properly - Enhanced with fallback logic (SAME AS SHOW METHOD)
                $vehicleType = 'Not Selected';
                if ($vehicle->type_id) {
                        // First try the relationship
                        if ($vehicle->types && $vehicle->types->vehicletype) {
                                $vehicleType = $vehicle->types->vehicletype;
                        } else {
                                // Fallback to direct database query if relationship fails
                                $type = DB::table('vehicle_types')
                                        ->where('id', $vehicle->type_id)
                                        ->where('deleted_at', null)
                                        ->first();
                                if ($type && $type->vehicletype) {
                                        $vehicleType = $type->vehicletype;
                                }
                        }
                } else {
                        // Check metadata for vehicle type as final fallback
                        $metaVehicleType = $vehicle->getMeta('vehicle_type');
                        if ($metaVehicleType) {
                                $vehicleType = $metaVehicleType;
                        }
                }
                
                // Get driver information (SAME AS SHOW METHOD)
                $driverName = 'Not Assigned';
                $driverId = $vehicle->getMeta('assign_driver_id');
                if ($driverId) {
                        $driver = User::find($driverId);
                        $driverName = $driver ? $driver->name : 'Driver Not Found';
                } else if ($vehicle->drivers && $vehicle->drivers->isNotEmpty()) {
                        $driverName = $vehicle->drivers->first()->name;
                }
                
                // Get group name properly - Enhanced with fallback logic (SAME AS SHOW METHOD)
                $groupName = 'Not Selected';
                if ($vehicle->group_id) {
                        // First try the relationship
                        if ($vehicle->group && $vehicle->group->name) {
                                $groupName = $vehicle->group->name;
                        } else {
                                // Fallback to direct database query if relationship fails
                                $group = DB::table('vehicle_group')
                                        ->where('id', $vehicle->group_id)
                                        ->where('deleted_at', null)
                                        ->first();
                                if ($group && $group->name) {
                                        $groupName = $group->name;
                                }
                        }
                }
                
                // Get all metadata (SAME AS SHOW METHOD)
                $metadata = [
                        'vehicle_price' => $vehicle->getMeta('vehicle_price'),
                        'price' => $vehicle->getMeta('price'),
                        'insurance_discount' => $vehicle->getMeta('insurance_discount'),
                        'initial_cost' => $vehicle->getMeta('initial_cost'),
                        'price_period' => $vehicle->getMeta('price_period'),
                        'vehicle_scheme' => $vehicle->getMeta('vehicle_scheme'),
                        'vehicle_status' => $vehicle->getMeta('vehicle_status'),
                        'telematics_link' => $vehicle->getMeta('telematics_link'),
                        'assign_driver_id' => $vehicle->getMeta('assign_driver_id'),
                        'luggage' => $vehicle->getMeta('luggage'),
                        'ins_number' => $vehicle->getMeta('ins_number'),
                        'ins_exp_date' => $vehicle->getMeta('ins_exp_date'),
                        'documents' => $vehicle->getMeta('documents'),
                        'traccar_device_id' => $vehicle->getMeta('traccar_device_id'),
                        'traccar_vehicle_id' => $vehicle->getMeta('traccar_vehicle_id'),
                        'icon' => $vehicle->getMeta('icon'),
                        'mot_expiry_date' => $vehicle->getMeta('mot_expiry_date'),
                        'exp_date' => $vehicle->getMeta('exp_date'),
                ];
                
                // Get all metadata from database
                $allMetadata = DB::table('vehicles_meta')
                        ->where('vehicle_id', $vehicle->id)
                        ->get()
                        ->map(function($item) {
                                return [
                                        'key' => $item->key,
                                        'value' => $item->value,
                                        'type' => $item->type,
                                        'updated_at' => $item->updated_at
                                ];
                        });
                
                // Prepare complete vehicle data
                $completeData = [
                        'id' => $vehicle->id,
                        'purchase_info' => $purchaseInfo,
                        'driver_name' => $driverName,
                        'group_name' => $groupName,
                        'vehicle_type' => $vehicleType,
                        'created_at' => $vehicle->created_at,
                        'updated_at' => $vehicle->updated_at,
                        'metadata' => $metadata,
                        'all_metadata' => $allMetadata,
                        'additional_meta' => [
                                'scheme' => $vehicle->getMeta('scheme') ?: $vehicle->getMeta('vehicle_scheme'),
                                'telematics_link' => $vehicle->getMeta('telematics_link'),
                                'gps_number' => $vehicle->getMeta('gps_number'),
                                'rc_number' => $vehicle->getMeta('rc_number'),
                                'permit_number' => $vehicle->getMeta('permit_number'),
                                'permit_validity' => $vehicle->getMeta('permit_validity'),
                                'driving_license' => $vehicle->getMeta('driving_license'),
                                'ecocert' => $vehicle->getMeta('ecocert'),
                                'tax_token_number' => $vehicle->getMeta('tax_token_number'),
                                'tax_token_validity' => $vehicle->getMeta('tax_token_validity'),
                                'fitness_cert' => $vehicle->getMeta('fitness_cert'),
                                'fitness_validity' => $vehicle->getMeta('fitness_validity'),
                                'pollution_cert' => $vehicle->getMeta('pollution_cert'),
                                'pollution_validity' => $vehicle->getMeta('pollution_validity'),
                                'national_permit' => $vehicle->getMeta('national_permit'),
                                'national_permit_validity' => $vehicle->getMeta('national_permit_validity'),
                        ]
                ];
                
                // Filter out null/empty metadata
                $completeData['additional_meta'] = array_filter($completeData['additional_meta'], function($value) {
                        return !is_null($value) && $value !== '';
                });
                
                return response()->json($completeData);
        }
        
        public function fetch_data(Request $request) {
                if ($request->ajax()) {
                        // Debug: Log the request data
                        \Log::info('Vehicles fetch_data called with filters:', [
                            'group_filter' => $request->get('group_filter'),
                            'type_filter' => $request->get('type_filter'),
                            'fuel_filter' => $request->get('fuel_filter'),
                            'status_filter' => $request->get('status_filter'),
                            'all_request' => $request->all()
                        ]);
                        
                        $user = Auth::user();
                        
                        // Use the same structure as index method
                        if ($user->getRawOriginal('user_type') == "B") {
                                // Boss Admin - if no company assigned, return empty set
                                if (is_null($user->company_id)) {
                                        $vehicles = VehicleModel::query()->whereRaw('1=0');
                                } else {
                                        $vehicles = VehicleModel::select('vehicles.*', DB::raw('MAX(users.name) as driver_name'), DB::raw('MAX(users.id) as driver_id'))
                                                ->where('vehicles.company_id', $user->company_id)
                                                ->leftJoin('driver_vehicle', 'driver_vehicle.vehicle_id', '=', 'vehicles.id')
                                                ->leftJoin('users', 'users.id', '=', 'driver_vehicle.driver_id')
                                                ->with('types', 'company'); // Load vehicle types and company relationship
                                }
                        } elseif ($user->getRawOriginal('user_type') == "S" || $user->getRawOriginal('user_type') == "O") {
                                // Super/Office Admin - require company_id, else none
                                if (is_null($user->company_id)) {
                                        $vehicles = VehicleModel::query()->whereRaw('1=0');
                                } else {
                                        $vehicles = VehicleModel::select('vehicles.*', DB::raw('MAX(users.name) as driver_name'), DB::raw('MAX(users.id) as driver_id'))
                                                ->where('vehicles.company_id', $user->company_id)
                                                ->leftJoin('driver_vehicle', 'driver_vehicle.vehicle_id', '=', 'vehicles.id')
                                                ->leftJoin('users', 'users.id', '=', 'driver_vehicle.driver_id')
                                                ->with('types', 'company'); // Load vehicle types and company relationship
                                }
                        } else {
                                // Driver - see only assigned vehicles
                                $vehicle_ids = $user->vehicles->pluck('id')->toArray();
                                $vehicles = VehicleModel::select('vehicles.*', DB::raw('MAX(users.name) as driver_name'), DB::raw('MAX(users.id) as driver_id'))
                                        ->whereIn('vehicles.id', $vehicle_ids)
                                        ->leftJoin('driver_vehicle', 'driver_vehicle.vehicle_id', '=', 'vehicles.id')
                                        ->leftJoin('users', 'users.id', '=', 'driver_vehicle.driver_id')
                                        ->with('types', 'company'); // Load vehicle types and company relationship
                        }
                        
                        // Apply filters
                        if ($request->has('group_filter') && $request->get('group_filter') != '') {
                                $vehicles->where('vehicles.group_id', $request->get('group_filter'));
                        }
                        
                        if ($request->has('type_filter') && $request->get('type_filter') != '') {
                                $vehicles->where('vehicles.type_id', $request->get('type_filter'));
                        }
                        
                        if ($request->has('fuel_filter') && $request->get('fuel_filter') != '') {
                                $vehicles->where('vehicles.engine_type', $request->get('fuel_filter'));
                        }
                        
                        if ($request->has('status_filter') && $request->get('status_filter') != '') {
                                if ($request->get('status_filter') == 'available') {
                                        $vehicles->where('vehicles.in_service', 1)
                                                ->whereNotExists(function($query) {
                                                        $query->select(DB::raw(1))
                                                              ->from('vehicles_meta')
                                                              ->whereRaw('vehicles_meta.vehicle_id = vehicles.id')
                                                              ->where('vehicles_meta.key', 'assign_driver_id')
                                                              ->whereNotNull('vehicles_meta.value');
                                                });
                                } elseif ($request->get('status_filter') == 'rented') {
                                        $vehicles->where('vehicles.in_service', 1)
                                                ->whereExists(function($query) {
                                                        $query->select(DB::raw(1))
                                                              ->from('vehicles_meta')
                                                              ->whereRaw('vehicles_meta.vehicle_id = vehicles.id')
                                                              ->where('vehicles_meta.key', 'assign_driver_id')
                                                              ->whereNotNull('vehicles_meta.value');
                                                });
                                } elseif ($request->get('status_filter') == 'disabled') {
                                        $vehicles->where('vehicles.in_service', 0);
                                }
                        }
                        
                        // Group by vehicles.id and execute
                        $vehicles = $vehicles->groupBy('vehicles.id')->get();
                        
                        try {
                                // Process each vehicle to match the frontend expectations
                                $processedVehicles = [];
                                foreach ($vehicles as $vehicle) {
                                        // Get vehicle metadata
                                        $metas = DB::table('vehicles_meta')
                                                ->where('vehicle_id', $vehicle->id)
                                                ->get();
                                        
                                        // Convert metas to array format expected by frontend
                                        $metaArray = [];
                                        $metaDataArray = [];
                                        foreach ($metas as $meta) {
                                                $metaArray[] = [
                                                        'id' => $meta->id,
                                                        'vehicle_id' => $meta->vehicle_id,
                                                        'type' => $meta->type,
                                                        'key' => $meta->key,
                                                        'value' => $meta->value,
                                                        'deleted_at' => $meta->deleted_at,
                                                        'created_at' => $meta->created_at,
                                                        'updated_at' => $meta->updated_at
                                                ];
                                                $metaDataArray[$meta->key] = $meta->value;
                                        }
                                        
                                        // Get vehicle status from metadata
                                        $vehicleStatus = 'Available';
                                        $assignedDriverId = null;
                                        foreach ($metas as $meta) {
                                                if ($meta->key == 'vehicle_status') {
                                                        $vehicleStatus = $meta->value ?: 'Available';
                                                }
                                                if ($meta->key == 'assign_driver_id') {
                                                        $assignedDriverId = $meta->value;
                                                }
                                        }
                                        
                                        // If no vehicle_status in meta but has assigned driver, set as Rented
                                        if ($vehicleStatus == 'Available' && $assignedDriverId) {
                                                $vehicleStatus = 'Rented';
                                        }
                                        
                                        // Create vehicle array in the format expected by frontend
                                        $processedVehicle = $vehicle->toArray();
                                        $processedVehicle['metas'] = $metaArray;
                                        $processedVehicle['meta_data'] = $metaDataArray;
                                        $processedVehicle['vehicle_status'] = $vehicleStatus;
                                        
                                        $processedVehicles[] = $processedVehicle;
                                }
                                
                                \Log::info('Vehicles processed successfully:', [
                                        'count' => count($processedVehicles)
                                ]);
                                
                                // Return the vehicles in the expected format
                                return response()->json([
                                        'data' => $processedVehicles,
                                        'success' => true
                                ]);
                                
                        } catch (\Exception $e) {
                                \Log::error('DataTables error: ' . $e->getMessage());
                                \Log::error('DataTables error trace: ' . $e->getTraceAsString());
                                
                                return response()->json([
                                        'error' => 'Failed to fetch vehicles data',
                                        'message' => $e->getMessage()
                                ], 500);
                        }
                }
        }
        public function driver_logs() {
                return view('vehicles.driver_logs');
        }
        public function driver_logs_fetch_data(Request $request) {
                if ($request->ajax()) {
                        $date_format_setting = (Hyvikk::get('date_format')) ? Hyvikk::get('date_format') : 'd-m-Y';
                        $user = Auth::user();
                        if ($user->group_id == null || $user->user_type == "S") {
                                $vehicle_ids = VehicleModel::select('id')->get('id')->pluck('id')->toArray();
                        } else {
                                $vehicle_ids = VehicleModel::select('id')->where('group_id', $user->group_id)->get('id')->pluck('id')->toArray();
                        }
                        $logs = DriverLogsModel::select('driver_logs.*')->with('driver')
                                ->whereIn('vehicle_id', $vehicle_ids)
                                ->leftJoin('vehicles', 'vehicles.id', '=', 'driver_logs.vehicle_id');
                        return DataTables::eloquent($logs)
                                ->addColumn('check', function ($vehicle) {
                                        $tag = '<input type="checkbox" name="ids[]" value="' . $vehicle->id . '" class="checkbox" id="chk' . $vehicle->id . '" onclick=\'checkcheckbox();\'>';
                                        return $tag;
                                })
                                ->addColumn('vehicle', function ($user) {
                                        return $user->make_name . '-' . $user->model_name . '-' . $user->vehicle->license_plate;
                                })
                                ->addColumn('driver', function ($log) {
                                        return ($log->driver->name) ?? "";
                                })
                                ->editColumn('date', function ($log) use ($date_format_setting) {
                                        // return date($date_format_setting . ' g:i A', strtotime($log->date));
                                        return [
                                                'display' => date($date_format_setting . ' g:i A', strtotime($log->date)),
                                                'timestamp' => Carbon::parse($log->date),
                                        ];
                                })
                                ->filterColumn('date', function ($query, $keyword) {
                                        $query->whereRaw("DATE_FORMAT(date,'%d-%m-%Y %h:%i %p') LIKE ?", ["%$keyword%"]);
                                })
                                ->filterColumn('vehicle', function ($query, $keyword) {
                                        $query->whereRaw("CONCAT(vehicles.make_name , '-' , vehicles.model_name , '-' , vehicles.license_plate) like ?", ["%$keyword%"]);
                                        return $query;
                                })
                                ->addColumn('action', function ($vehicle) {
                                        return view('vehicles.driver-logs-list-actions', ['row' => $vehicle]);
                                })
                                ->addIndexColumn()
                                ->rawColumns(['action', 'check'])
                                ->make(true);
                }
        }
        public function create() {
                if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
                        $index['groups'] = VehicleGroupModel::all();
                } else {
                        $index['groups'] = VehicleGroupModel::where('id', Auth::user()->group_id)->get();
                }
                // $index['types'] = VehicleTypeModel::all();
                $index['types'] = VehicleTypeModel::where('isenable', 1)->get();
                $index['makes'] = VehicleModel::select('make_name')->distinct()->whereNotNull('make_name')->pluck('make_name')->toArray();
                $index['models'] = VehicleModel::select('model_name')->distinct()->whereNotNull('model_name')->pluck('model_name')->toArray();
                $index['colors'] = VehicleModel::select('color_name')->distinct()->whereNotNull('color_name')->pluck('color_name')->toArray();
                // Get drivers excluding those already assigned to any vehicle and inactive drivers
                $index['drivers'] = User::whereUser_type("D")
                        ->whereHas('metas', function($query) {
                                $query->where('key', 'is_active')
                                      ->where('value', '1');
                        })
                        ->whereDoesntHave('vehicles')
                        ->get();
                return view("vehicles.create", $index);
        }
        public function get_models($name) {
                $makes = VehicleModel::select('model_name')->distinct()->where('make_name', $name)->whereNotNull('model_name')->get();
                $data = array();
                foreach ($makes as $make) {
                        array_push($data, array("id" => $make->model_name, "text" => $make->model_name));
                }
                return $data;
        }
        public function destroy(Request $request) {
                try {
                        $vehicleId = $request->get('id');
                        \Log::info('Starting vehicle deletion process for ID: ' . $vehicleId);
                        
                        $vehicle = VehicleModel::find($vehicleId);
                        
                        // Check if vehicle exists
                        if (!$vehicle) {
                                \Log::warning('Vehicle not found for deletion: ' . $vehicleId);
                                if ($request->ajax()) {
                                        return response()->json(['error' => 'Vehicle not found.'], 404);
                                }
                                return redirect()->route('vehicles.index')->with('error', 'Vehicle not found.');
                        }
                        
                        \Log::info('Vehicle found for deletion: ' . $vehicle->license_plate . ' (ID: ' . $vehicleId . ')');
                        
                        // Handle driver relationships if they exist
                        if ($vehicle->driver_id) {
                                if ($vehicle->drivers && $vehicle->drivers->count()) {
                                        $vehicle->drivers()->detach($vehicle->drivers->pluck('id')->toArray());
                                }
                        }
                        
                        // Delete vehicle image if it exists
                        if ($vehicle->vehicle_image && file_exists('./uploads/' . $vehicle->vehicle_image) && !is_dir('./uploads/' . $vehicle->vehicle_image)) {
                                unlink('./uploads/' . $vehicle->vehicle_image);
                        }
                        
                        // Delete related records
                        DriverVehicleModel::where('vehicle_id', $request->id)->delete();
                        
                        // Permanently delete income and expense records if they exist
                        if ($vehicle->income) {
                                $vehicle->income()->forceDelete();
                        }
                        if ($vehicle->expense) {
                                $vehicle->expense()->forceDelete();
                        }
                        
                        // Permanently delete the vehicle (force delete to bypass soft deletes)
                        $vehicle->forceDelete();
                        \Log::info('Vehicle force deleted: ' . $vehicleId);
                        
                        // Permanently delete other related records (force delete to bypass soft deletes)
                        VehicleReviewModel::where('vehicle_id', $request->get('id'))->forceDelete();
                        ServiceReminderModel::where('vehicle_id', $request->get('id'))->forceDelete();
                        FuelModel::where('vehicle_id', $request->get('id'))->forceDelete();
                        
                        // Delete bookings that reference this vehicle
                        \DB::table('bookings')->where('vehicle_id', $request->get('id'))->delete();
                        \DB::table('booking_quotation')->where('vehicle_id', $request->get('id'))->delete();
                        
                        // Delete work orders that reference this vehicle
                        \DB::table('work_orders')->where('vehicle_id', $request->get('id'))->delete();
                        
                        // Delete notes that reference this vehicle
                        \DB::table('notes')->where('vehicle_id', $request->get('id'))->delete();
                        
                        // Delete driver logs that reference this vehicle
                        \DB::table('driver_logs')->where('vehicle_id', $request->get('id'))->delete();
                        
                        // Delete work order logs that reference this vehicle
                        \DB::table('work_order_logs')->where('vehicle_id', $request->get('id'))->delete();
                        
                        // Delete vehicle metadata
                        \DB::table('vehicles_meta')->where('vehicle_id', $request->get('id'))->delete();
                        
                        \Log::info('Related records force deleted for vehicle: ' . $vehicleId);
                        
                        // Verify deletion by checking if vehicle still exists
                        $deletedVehicle = VehicleModel::withTrashed()->find($vehicleId);
                        if ($deletedVehicle) {
                                \Log::warning('Vehicle still exists after force delete: ' . $vehicleId);
                        } else {
                                \Log::info('Vehicle successfully deleted and verified: ' . $vehicleId);
                        }
                        
                        // Return appropriate response based on request type
                        if ($request->ajax()) {
                                return response()->json(['success' => true, 'message' => 'Vehicle deleted successfully.']);
                        }
                        
                        return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted successfully.');
                        
                } catch (\Exception $e) {
                        \Log::error('Vehicle deletion failed: ' . $e->getMessage());
                        
                        if ($request->ajax()) {
                                return response()->json(['error' => 'Failed to delete vehicle: ' . $e->getMessage()], 500);
                        }
                        
                        return redirect()->route('vehicles.index')->with('error', 'Failed to delete vehicle: ' . $e->getMessage());
                }
        }
        public function edit($id) {
                if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
                        $groups = VehicleGroupModel::all();
                } else {
                        $groups = VehicleGroupModel::where('id', Auth::user()->group_id)->get();
                }
                // Get drivers excluding those already assigned to other vehicles and inactive drivers
                $drivers = User::whereUser_type("D")
                        ->whereHas('metas', function($query) {
                                $query->where('key', 'is_active')
                                      ->where('value', '1');
                        })
                        ->whereDoesntHave('vehicles', function($query) use ($id) {
                                $query->where('vehicles.id', '!=', $id);
                        })
                        ->orWhereHas('vehicles', function($query) use ($id) {
                                $query->where('vehicles.id', $id);
                        })
                        ->get();
                $vehicle = VehicleModel::findOrFail($id);
                $vehicle->load('drivers');
                $udfs = unserialize($vehicle->getMeta('udf'));
                $makes = VehicleModel::select('make_name')->distinct()->whereNotNull('make_name')->pluck('make_name')->toArray();
                $models = VehicleModel::select('model_name')->distinct()->whereNotNull('model_name')->pluck('model_name')->toArray();
                $colors = VehicleModel::select('color_name')->distinct()->whereNotNull('color_name')->pluck('color_name')->toArray();
                // $types = VehicleTypeModel::all();
                $types = VehicleTypeModel::where('isenable', 1)->get();
                return view("vehicles.edit", compact('vehicle', 'groups', 'drivers', 'udfs', 'types', 'makes', 'models', 'colors'));
        }
        private function upload_file($file, $field, $id) {
                $destinationPath = './uploads'; // upload path
                $extension = $file->getClientOriginalExtension();
                $fileName1 = Str::uuid() . '.' . $extension;
                $file->move($destinationPath, $fileName1);
                $x = VehicleModel::find($id)->update([$field => $fileName1]);
        }
        private function upload_doc($file, $field, $id) {
                $destinationPath = './uploads'; // upload path
                $extension = $file->getClientOriginalExtension();
                $fileName1 = Str::uuid() . '.' . $extension;
                $file->move($destinationPath, $fileName1);
                $vehicle = VehicleModel::find($id);
                $vehicle->setMeta([$field => $fileName1]);
                $vehicle->save();
        }
        public function update(VehicleRequest $request) {

                
                $id = $request->get('id');
                $vehicle = VehicleModel::find($request->get("id"));
                if ($request->file('vehicle_image') && $request->file('vehicle_image')->isValid()) {
                        if (file_exists('./uploads/' . $vehicle->vehicle_image) && !is_dir('./uploads/' . $vehicle->vehicle_image)) {
                                unlink('./uploads/' . $vehicle->vehicle_image);
                        }
                        $this->upload_file($request->file('vehicle_image'), "vehicle_image", $id);
                }

                if ($request->file('icon') && $request->file('icon')->isValid()) {
                        if (file_exists('./uploads/' . $vehicle->icon) && !is_dir('./uploads/' . $vehicle->icon)) {
                                unlink('./uploads/' . $vehicle->icon);
                        }
                        $icon=$request->file('icon');
                        $icon_path=uniqid().$icon->getClientOriginalName();
            $icon->move('./uploads/', $icon_path);

                        $vehicle->setMeta([
                                'icon'=>$icon_path
                        ]);
                }

                $form_data = $request->all();
                // dd($form_data);
                unset($form_data['vehicle_image']);
                unset($form_data['documents']);
                unset($form_data['udf']);
                // Remove metadata fields that should not be mass-assigned
                unset($form_data['vehicle_price']);
                unset($form_data['insurance_discount']);
                unset($form_data['initial_cost']);
                unset($form_data['price_period']);
                unset($form_data['vehicle_scheme']);
                unset($form_data['telematics_link']);
                $vehicle->update($form_data);
                // Prepare metadata array for edit - only include non-empty values
                $editMetadata = [
                        'vehicle_status' => $request->vehicle_status ?? 'Available',
                        'vehicle_scheme' => $request->vehicle_scheme ?: 'Rental',
                        'price_period' => $request->price_period ?: 'monthly',
                ];
                
                // Only add non-empty values to avoid storing empty strings
                if ($request->traccar_device_id) $editMetadata['traccar_device_id'] = $request->traccar_device_id;
                if ($request->traccar_vehicle_id) $editMetadata['traccar_vehicle_id'] = $request->traccar_vehicle_id;
                if ($request->luggage) $editMetadata['luggage'] = $request->luggage;
                if ($request->telematics_link) $editMetadata['telematics_link'] = $request->telematics_link;
                
                // Handle MOT expiry date
                if ($request->mot_exp_day && $request->mot_exp_month && $request->mot_exp_year) {
                        try {
                                // Convert 2-digit year to 4-digit year
                                $year = intval($request->mot_exp_year);
                                if ($year < 100) {
                                        $year += 2000; // Convert 25 to 2025
                                }
                                
                                // Convert day and month to integers to handle zero-padded values
                                $day = intval($request->mot_exp_day);
                                $month = intval($request->mot_exp_month);
                                
                                // Validate the date components
                                if ($day >= 1 && $day <= 31 && $month >= 1 && $month <= 12 && $year >= 1900) {
                                        $motExpiryDate = \Carbon\Carbon::create($year, $month, $day)->format('Y-m-d');
                                        $editMetadata['mot_expiry_date'] = $motExpiryDate;
                                        
                                        \Log::info('MOT expiry date saved successfully', [
                                                'vehicle_id' => $vehicle->id,
                                                'mot_expiry_date' => $motExpiryDate,
                                                'original_day' => $request->mot_exp_day,
                                                'original_month' => $request->mot_exp_month,
                                                'original_year' => $request->mot_exp_year
                                        ]);
                                } else {
                                        \Log::warning('Invalid MOT expiry date components', [
                                                'vehicle_id' => $vehicle->id,
                                                'day' => $day,
                                                'month' => $month,
                                                'year' => $year
                                        ]);
                                }
                        } catch (\Exception $e) {
                                \Log::error('Failed to save MOT expiry date', [
                                        'vehicle_id' => $vehicle->id,
                                        'error' => $e->getMessage(),
                                        'day' => $request->mot_exp_day,
                                        'month' => $request->mot_exp_month,
                                        'year' => $request->mot_exp_year
                                ]);
                        }
                } else {
                        // If any MOT expiry field is empty, clear the MOT expiry date
                        $editMetadata['mot_expiry_date'] = null;
                }
                
                // Handle price fields - store as strings but only if not empty
                if ($request->vehicle_price && $request->vehicle_price !== '' && $request->vehicle_price !== '0') {
                        $editMetadata['vehicle_price'] = (string)$request->vehicle_price;
                        $editMetadata['price'] = (string)$request->vehicle_price; // For backward compatibility
                }
                
                if ($request->insurance_discount && $request->insurance_discount !== '' && $request->insurance_discount !== '0') {
                        $editMetadata['insurance_discount'] = (string)$request->insurance_discount;
                }
                
                if ($request->initial_cost && $request->initial_cost !== '' && $request->initial_cost !== '0') {
                        $editMetadata['initial_cost'] = (string)$request->initial_cost;
                }
                
                $vehicle->setMeta($editMetadata);
                
                // Handle driver assignment
                if ($request->has('driver_id') && $request->driver_id) {
                        // Assign driver to vehicle
                        $vehicle->setMeta(['assign_driver_id' => $request->driver_id]);
                        // Driver assigned - set status to "Rented"
                        $vehicle->setMeta(['vehicle_status' => 'Rented']);
                        $vehicle->drivers()->sync([$request->driver_id]);
                        
                        // Create driver log entry
                        DriverLogsModel::create([
                                'driver_id' => $request->driver_id, 
                                'vehicle_id' => $vehicle->id, 
                                'date' => date('Y-m-d H:i:s')
                        ]);
                } else {
                        // Remove driver assignment if no driver selected
                        $vehicle->setMeta(['assign_driver_id' => null]);
                        // Driver removed - set status to "Available"
                        $vehicle->setMeta(['vehicle_status' => 'Available']);
                        $vehicle->drivers()->detach();
                }
                
                if ($request->get("in_service")) {
                        $vehicle->in_service = 1;
                } else {
                        $vehicle->in_service = 0;
                }
                $vehicle->int_mileage = $request->get("int_mileage") ? (int) $request->get("int_mileage") : null;
                $vehicle->lic_exp_date = $request->get('lic_exp_date');
                $vehicle->reg_exp_date = $request->get('reg_exp_date');
                $vehicle->udf = serialize($request->get('udf'));
                $vehicle->average = $request->average;
                $vehicle->save();
                $to = \Carbon\Carbon::now();
                
                // Check registration expiry date
                if ($request->get('reg_exp_date') && !empty($request->get('reg_exp_date'))) {
                        try {
                                $from = \Carbon\Carbon::createFromFormat('Y-m-d', $request->get('reg_exp_date'));
                                $diff_in_days = $to->diffInDays($from);
                                if ($diff_in_days > 20) {
                                        $t = DB::table('notifications')
                                                ->where('type', 'like', '%RenewRegistration%')
                                                ->where('data', 'like', '%"vid":' . $vehicle->id . '%')
                                                ->delete();
                                }
                        } catch (\Exception $e) {
                                \Log::warning('Invalid reg_exp_date format: ' . $request->get('reg_exp_date'));
                        }
                }
                
                // Check license expiry date
                if ($request->get('lic_exp_date') && !empty($request->get('lic_exp_date'))) {
                        try {
                                $from = \Carbon\Carbon::createFromFormat('Y-m-d', $request->get('lic_exp_date'));
                                $diff_in_days = $to->diffInDays($from);
                                if ($diff_in_days > 20) {
                                        DB::table('notifications')
                                                ->where('type', 'like', '%RenewVehicleLicence%')
                                                ->where('data', 'like', '%"vid":' . $vehicle->id . '%')
                                                ->delete();
                                }
                        } catch (\Exception $e) {
                                \Log::warning('Invalid lic_exp_date format: ' . $request->get('lic_exp_date'));
                        }
                }
                return Redirect::route("vehicles.index");
        }

        /**
         * Update vehicle status via AJAX
         */
        public function updateStatus(Request $request) {
                try {
                        $vehicleId = $request->input('vehicle_id');
                        $status = $request->input('status');
                        
                        // Validate input
                        if (!$vehicleId || !$status) {
                                return response()->json([
                                        'success' => false,
                                        'message' => 'Vehicle ID and status are required'
                                ], 400);
                        }
                        
                        // Validate status
                        $validStatuses = ['Available', 'Rented', 'Workshop', 'Disabled'];
                        if (!in_array($status, $validStatuses)) {
                                return response()->json([
                                        'success' => false,
                                        'message' => 'Invalid status'
                                ], 400);
                        }
                        
                        // Find vehicle
                        $vehicle = VehicleModel::find($vehicleId);
                        if (!$vehicle) {
                                return response()->json([
                                        'success' => false,
                                        'message' => 'Vehicle not found'
                                ], 404);
                        }
                        
                        // Update status
                        $vehicle->setMeta(['vehicle_status' => $status]);
                        
                        // Clear inspection notes when vehicle is no longer in workshop
                        if ($status !== 'Workshop') {
                                $vehicle->setMeta(['inspection_notes' => null]);
                        }
                        
                        // Force save the vehicle to persist the meta changes
                        $vehicle->save();
                        
                        // Log the status change
                        \Log::info('Vehicle status updated', [
                                'vehicle_id' => $vehicleId,
                                'old_status' => $vehicle->getMeta('vehicle_status'),
                                'new_status' => $status,
                                'updated_by' => auth()->id()
                        ]);
                        
                        return response()->json([
                                'success' => true,
                                'message' => 'Vehicle status updated successfully',
                                'status' => $status
                        ]);
                        
                } catch (\Exception $e) {
                        \Log::error('Vehicle status update failed: ' . $e->getMessage());
                        return response()->json([
                                'success' => false,
                                'message' => 'Failed to update vehicle status: ' . $e->getMessage()
                        ], 500);
                }
        }

        /**
         * Update vehicle assigned driver via AJAX
         */
        public function updateDriver(Request $request) {
                try {
                        $vehicleId = $request->input('vehicle_id');
                        $driverId = $request->input('driver_id');
                        
                        // Validate input
                        if (!$vehicleId) {
                                return response()->json([
                                        'success' => false,
                                        'message' => 'Vehicle ID is required'
                                ], 400);
                        }
                        
                        // Find vehicle
                        $vehicle = VehicleModel::find($vehicleId);
                        if (!$vehicle) {
                                return response()->json([
                                        'success' => false,
                                        'message' => 'Vehicle not found'
                                ], 404);
                        }
                        
                        // If assigning a driver, validate driver exists and is active
                        if ($driverId) {
                                $driver = User::where('id', $driverId)
                                        ->where('user_type', 'D')
                                        ->whereHas('metas', function($query) {
                                                $query->where('key', 'is_active')
                                                      ->where('value', '1');
                                        })
                                        ->first();
                                
                                if (!$driver) {
                                        return response()->json([
                                                'success' => false,
                                                'message' => 'Driver not found or inactive'
                                        ], 404);
                                }
                                
                                // If assigning a driver, first detach them from any other vehicle
                                $otherVehicles = VehicleModel::whereHas('metas', function($query) use ($driverId) {
                                        $query->where('key', 'assign_driver_id')
                                              ->where('value', $driverId);
                                })->get();
                                
                                foreach ($otherVehicles as $otherVehicle) {
                                        $otherVehicle->setMeta(['assign_driver_id' => null]);
                                        $otherVehicle->save();
                                        $otherVehicle->drivers()->detach($driverId);
                                }
                                
                                // Also detach any existing driver from the current vehicle
                                $currentDriverId = $vehicle->getMeta('assign_driver_id');
                                if ($currentDriverId) {
                                        $vehicle->drivers()->detach($currentDriverId);
                                }
                        } else {
                                // If unassigning driver, detach current driver
                                $currentDriverId = $vehicle->getMeta('assign_driver_id');
                                if ($currentDriverId) {
                                        $vehicle->drivers()->detach($currentDriverId);
                                }
                        }
                        
                        // Update assigned driver
                        $vehicle->setMeta(['assign_driver_id' => $driverId]);
                        
                        // Automatically update vehicle status based on driver assignment
                        if ($driverId) {
                                // Driver assigned - set status to "Rented"
                                $vehicle->setMeta(['vehicle_status' => 'Rented']);
                                $vehicle->drivers()->sync($driverId);
                                DriverLogsModel::create([
                                        'driver_id' => $driverId, 
                                        'vehicle_id' => $vehicleId, 
                                        'date' => date('Y-m-d H:i:s')
                                ]);
                        } else {
                                // Driver removed - set status to "Available"
                                $vehicle->setMeta(['vehicle_status' => 'Available']);
                        }
                        
                        $vehicle->save();
                        
                        // Get updated driver name for response
                        $driverName = $driverId ? User::find($driverId)->name : null;
                        
                        // Get updated vehicle status
                        $vehicleStatus = $vehicle->getMeta('vehicle_status') ?: 'Available';
                        
                        return response()->json([
                                'success' => true,
                                'message' => 'Driver assignment updated successfully',
                                'driver_name' => $driverName,
                                'vehicle_status' => $vehicleStatus
                        ]);
                        
                } catch (\Exception $e) {
                        \Log::error('Driver assignment update failed: ' . $e->getMessage());
                        return response()->json([
                                'success' => false,
                                'message' => 'Failed to update driver assignment: ' . $e->getMessage()
                        ], 500);
                }
        }

        /**
         * Update vehicle inspection notes via AJAX
         */
        public function updateNotes(Request $request) {
                try {
                        $vehicleId = $request->input('vehicle_id');
                        $notes = $request->input('notes');
                        
                        \Log::info('UpdateNotes called', [
                                'vehicle_id' => $vehicleId,
                                'notes' => $notes,
                                'user_id' => auth()->id()
                        ]);
                        
                        // Validate input
                        if (!$vehicleId) {
                                return response()->json([
                                        'success' => false,
                                        'message' => 'Vehicle ID is required'
                                ], 400);
                        }
                        
                        // Find vehicle
                        $vehicle = VehicleModel::find($vehicleId);
                        if (!$vehicle) {
                                \Log::error('Vehicle not found', ['vehicle_id' => $vehicleId]);
                                return response()->json([
                                        'success' => false,
                                        'message' => 'Vehicle not found'
                                ], 404);
                        }
                        
                        \Log::info('Vehicle found', [
                                'vehicle_id' => $vehicle->id,
                                'make' => $vehicle->make_name,
                                'model' => $vehicle->model_name,
                                'current_notes' => $vehicle->getMeta('inspection_notes')
                        ]);
                        
                        // Update notes - try multiple approaches
                        $vehicle->setMeta(['inspection_notes' => $notes]);
                        
                        // Force save the vehicle
                        $vehicle->save();
                        
                        // Verify the update by refreshing from database
                        $updatedVehicle = VehicleModel::find($vehicleId);
                        $updatedVehicle->load('metas'); // Ensure metas are loaded
                        $savedNotes = $updatedVehicle->getMeta('inspection_notes');
                        
                        // If still null, try direct database update
                        if ($savedNotes === null) {
                                \Log::info('setMeta failed, trying direct database update');
                                
                                // Check if meta record exists
                                $existingMeta = \DB::table('vehicles_meta')
                                        ->where('vehicle_id', $vehicleId)
                                        ->where('key', 'inspection_notes')
                                        ->first();
                                
                                if ($existingMeta) {
                                        // Update existing meta
                                        \DB::table('vehicles_meta')
                                                ->where('vehicle_id', $vehicleId)
                                                ->where('key', 'inspection_notes')
                                                ->update([
                                                        'value' => $notes,
                                                        'updated_at' => now()
                                                ]);
                                } else {
                                        // Create new meta record
                                        \DB::table('vehicles_meta')->insert([
                                                'vehicle_id' => $vehicleId,
                                                'type' => 'string',
                                                'key' => 'inspection_notes',
                                                'value' => $notes,
                                                'created_at' => now(),
                                                'updated_at' => now()
                                        ]);
                                }
                                
                                // Get the saved notes again
                                $savedNotes = \DB::table('vehicles_meta')
                                        ->where('vehicle_id', $vehicleId)
                                        ->where('key', 'inspection_notes')
                                        ->value('value');
                        }
                        
                        \Log::info('Notes update completed', [
                                'vehicle_id' => $vehicleId,
                                'notes_sent' => $notes,
                                'notes_saved' => $savedNotes,
                                'updated_by' => auth()->id()
                        ]);
                        
                        return response()->json([
                                'success' => true,
                                'message' => 'Notes updated successfully',
                                'notes' => $savedNotes ?: 'No notes'
                        ]);
                        
                } catch (\Exception $e) {
                        \Log::error('Failed to update vehicle notes', [
                                'vehicle_id' => $request->input('vehicle_id'),
                                'notes' => $request->input('notes'),
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                        ]);
                        
                        return response()->json([
                                'success' => false,
                                'message' => 'Failed to update notes: ' . $e->getMessage()
                        ], 500);
                }
        }
        public function store(VehicleRequest $request) {
                // dd($request->all());
                $user_id = $request->get('user_id');
                $vehicle = VehicleModel::create([
                        'make_name' => $request->get("make_name"),
                        'model_name' => $request->get("model_name"),
                        // 'type' => $request->get("type"),
                        'year' => $request->get("year"),
                        'engine_type' => $request->get("engine_type"),
                        'horse_power' => $request->get("horse_power"),
                        'color_name' => $request->get("color_name"),
                        'vin' => $request->get("vin"),
                        'license_plate' => $request->get("license_plate"),
                        'int_mileage' => $request->get("int_mileage") ? (int) $request->get("int_mileage") : null,
                        'group_id' => $request->get('group_id') ?: null, // Set to null if empty
                        'user_id' => $request->get('user_id'),
                        'lic_exp_date' => $request->get('lic_exp_date'),
                        'reg_exp_date' => $request->get('reg_exp_date'),
                        'in_service' => $request->get("in_service"),
                        'type_id' => $request->get('type_id') ?: 1, // Default to type ID 1 if empty
                        // 'vehicle_image' => $request->get('vehicle_image'),
                        'height' => $request->height,
                        'length' => $request->length,
                        'breadth' => $request->breadth,
                        'weight' => $request->weight,
                ])->id;
                if ($request->file('vehicle_image') && $request->file('vehicle_image')->isValid()) {
                        $this->upload_file($request->file('vehicle_image'), "vehicle_image", $vehicle);
                }
                


                $meta = VehicleModel::find($vehicle);
                
                if ($request->file('icon') && $request->file('icon')->isValid()) {
                        if (!empty($meta->icon) && file_exists('./uploads/' . $meta->icon) && !is_dir('./uploads/' . $meta->icon)) {
                                unlink('./uploads/' . $meta->icon);
                        }
                
                        $icon = $request->file('icon');
                        $icon_path = uniqid() . $icon->getClientOriginalName();
                        $icon->move('./uploads/', $icon_path);
                
                        $meta->setMeta([
                                'icon' => $icon_path
                        ]);
                }

                // Prepare metadata array - only include non-empty values
                $metadata = [
                        'ins_number' => "",
                        'ins_exp_date' => "",
                        'documents' => "",
                        'vehicle_status' => $request->vehicle_status ?: 'Available',
                        'vehicle_scheme' => $request->vehicle_scheme ?: 'Rental',
                        'price_period' => $request->price_period ?: 'monthly',
                ];
                
                // Only add non-empty values to avoid storing empty strings
                if ($request->traccar_device_id) $metadata['traccar_device_id'] = $request->traccar_device_id;
                if ($request->traccar_vehicle_id) $metadata['traccar_vehicle_id'] = $request->traccar_vehicle_id;
                if ($request->driver_id) {
                        $metadata['assign_driver_id'] = $request->driver_id;
                        // Driver assigned - set status to "Rented"
                        $metadata['vehicle_status'] = 'Rented';
                } else {
                        // Driver removed - set status to "Available"
                        $metadata['vehicle_status'] = 'Available';
                }
                if ($request->luggage) $metadata['luggage'] = $request->luggage;
                if ($request->telematics_link) $metadata['telematics_link'] = $request->telematics_link;
                
                // Handle price fields - store as strings but only if not empty
                if ($request->vehicle_price && $request->vehicle_price !== '' && $request->vehicle_price !== '0') {
                        $metadata['vehicle_price'] = (string)$request->vehicle_price;
                        $metadata['price'] = (string)$request->vehicle_price; // For backward compatibility
                }
                
                if ($request->insurance_discount && $request->insurance_discount !== '' && $request->insurance_discount !== '0') {
                        $metadata['insurance_discount'] = (string)$request->insurance_discount;
                }
                
                if ($request->initial_cost && $request->initial_cost !== '' && $request->initial_cost !== '0') {
                        $metadata['initial_cost'] = (string)$request->initial_cost;
                }
                
                $meta->setMeta($metadata);
                $meta->udf = serialize($request->get('udf'));
                $meta->average = $request->average;
                $meta->save();
                // Handle driver sync
                if ($request->driver_id) {
                        $meta->drivers()->sync($request->driver_id);
                        DriverLogsModel::create(['driver_id' => $request->driver_id, 'vehicle_id' => $meta->id, 'date' => date('Y-m-d H:i:s')]);
                } else {
                        // Remove all driver assignments if no driver selected
                        $meta->drivers()->detach();
                }
                
                // Set default in_service status based on vehicle_status
                $status = $request->vehicle_status ?? 'Available';
                $meta->in_service = ($status === 'Available' || $status === 'Rented') ? 1 : 0;
                $meta->save();
                
                return redirect()->route('vehicles.index')->with('success', 'Vehicle created successfully!');
        }
        /**
         * Repair metadata for existing vehicles that might have corrupted data
         */
        public function repairMetadata($id) {
                $vehicle = VehicleModel::findOrFail($id);
                
                // Get all metadata for this vehicle
                $allMeta = DB::table('vehicles_meta')
                    ->where('vehicle_id', $id)
                    ->get();
                
                $repaired = false;
                
                // Check if we have legacy purchase_info data
                $legacyPurchaseInfo = $vehicle->getMeta('purchase_info');
                if ($legacyPurchaseInfo) {
                        try {
                                $legacyData = json_decode($legacyPurchaseInfo, true) ?: unserialize($legacyPurchaseInfo);
                                if (is_array($legacyData)) {
                                        $newMetadata = [];
                                        foreach ($legacyData as $item) {
                                                if (isset($item['exp_name']) && isset($item['exp_amount'])) {
                                                        if (strpos($item['exp_name'], 'Price') !== false) {
                                                                $newMetadata['vehicle_price'] = (string)$item['exp_amount'];
                                                                $newMetadata['price'] = (string)$item['exp_amount'];
                                                                $repaired = true;
                                                        } elseif (strpos($item['exp_name'], 'Initial') !== false) {
                                                                $newMetadata['initial_cost'] = (string)$item['exp_amount'];
                                                                $repaired = true;
                                                        }
                                                }
                                        }
                                        
                                        if (!empty($newMetadata)) {
                                                $vehicle->setMeta($newMetadata);
                                                $vehicle->save();
                                        }
                                }
                        } catch (Exception $e) {
                                // Ignore errors
                        }
                }
                
                return redirect()->back()->with('success', $repaired ? 'Metadata repaired successfully!' : 'No metadata repair needed.');
        }
        
        public function store_insurance(InsuranceRequest $request) {
                $vehicle = VehicleModel::find($request->get('vehicle_id'));
                $vehicle->setMeta([
                        'ins_number' => $request->get("insurance_number"),
                        'ins_exp_date' => $request->get('exp_date'),
                        // 'documents' => $request->get('documents'),
                ]);
                $vehicle->save();
                if ($vehicle->getMeta('ins_exp_date') != null && !empty($vehicle->getMeta('ins_exp_date'))) {
                        $ins_date = $vehicle->getMeta('ins_exp_date');
                        try {
                                $to = \Carbon\Carbon::now();
                                $from = \Carbon\Carbon::createFromFormat('Y-m-d', $ins_date);
                                $diff_in_days = $to->diffInDays($from);
                                if ($diff_in_days > 20) {
                                        $t = DB::table('notifications')
                                                ->where('type', 'like', '%RenewInsurance%')
                                                ->where('data', 'like', '%"vid":' . $vehicle->id . '%')
                                                ->delete();
                                }
                        } catch (\Exception $e) {
                                \Log::warning('Invalid ins_exp_date format: ' . $ins_date);
                        }
                }
                if ($request->file('documents') && $request->file('documents')->isValid()) {
                        $this->upload_doc($request->file('documents'), 'documents', $vehicle->id);
                }
                // return $vehicle;
                return redirect('admin/vehicles/' . $request->get('vehicle_id') . '/edit?tab=insurance');
        }
        public function view_event($id) {
                $data['vehicle'] = VehicleModel::with(['drivers.metas', 'types', 'metas'])->where('id', $id)->get()->first();
                return view("vehicles.view_event", $data);
        }
        public function assign_driver(Request $request) {
                $vehicle = VehicleModel::find($request->get('vehicle_id'));
                
                // If assigning a driver, first detach them from any other vehicle
                if ($request->driver_id) {
                        // Find and detach the driver from any other vehicle
                        $otherVehicles = VehicleModel::whereHas('metas', function($query) use ($request) {
                                $query->where('key', 'assign_driver_id')
                                      ->where('value', $request->driver_id);
                        })->get();
                        
                        foreach ($otherVehicles as $otherVehicle) {
                                $otherVehicle->setMeta(['assign_driver_id' => null]);
                                $otherVehicle->save();
                                $otherVehicle->drivers()->detach($request->driver_id);
                        }
                        
                        // Also detach any existing driver from the current vehicle
                        $currentDriverId = $vehicle->getMeta('assign_driver_id');
                        if ($currentDriverId) {
                                $vehicle->drivers()->detach($currentDriverId);
                        }
                }
                
                $vehicle->setMeta([
                        'assign_driver_id' => $request->driver_id,
                ]);
                
                // Automatically update vehicle status based on driver assignment
                if ($request->driver_id) {
                        // Driver assigned - set status to "Rented"
                        $vehicle->setMeta(['vehicle_status' => 'Rented']);
                        $vehicle->drivers()->sync($request->driver_id);
                        DriverLogsModel::create(['driver_id' => $request->driver_id, 'vehicle_id' => $request->get('vehicle_id'), 'date' => date('Y-m-d H:i:s')]);
                } else {
                        // Driver removed - set status to "Available"
                        $vehicle->setMeta(['vehicle_status' => 'Available']);
                }
                
                $vehicle->save();
                return redirect('admin/vehicles/' . $request->get('vehicle_id') . '/edit?tab=driver');
        }
        public function vehicle_review() {
                $user = Auth::user();
                if ($user->group_id == null || $user->user_type == "S") {
                        $data['vehicles'] = VehicleModel::get();
                } else {
                        $data['vehicles'] = VehicleModel::where('group_id', $user->group_id)->get();
                }
                return view('vehicles.vehicle_review', $data);
        }
        public function vehicle_inspection_create() {
                // // old get vehicles before driver vehicles many-to-many
                // $data['vehicles'] = DriverLogsModel::where('driver_id', Auth::user()->id)->get();
                $user = Auth::user();
                if ($user->user_type == "D") {
                        $assign_vehicles = VehicleModel::whereIn_service("1")->whereMeta('assign_driver_id', Auth::user()->id)->pluck('id')->toArray();
                        $booking_associated_vehicle_1 = Bookings::where('driver_id', Auth::user()->id)
                                ->whereMeta('ride_status', 'Upcoming')
                                ->pluck('vehicle_id')->toArray();
                        $booking_associated_vehicle_2 = Bookings::where('driver_id', Auth::user()->id)
                                ->whereMeta('ride_status', 'Ongoing')
                                ->pluck('vehicle_id')->toArray();
                        $mergedArray = array_unique(array_merge($booking_associated_vehicle_1, $booking_associated_vehicle_2, $assign_vehicles));
                        $data['vehicles'] = VehicleModel::whereIn('id', $mergedArray)->get();
                } elseif ($user->user_type == "B" && $user->company_id == null) {
                        // Boss Admin with no company sees no vehicles
                        $data['vehicles'] = collect();
                } else {
                        // Super Admin or Office Admin - filter by company
                        if ($user->company_id) {
                                $data['vehicles'] = VehicleModel::where('company_id', $user->company_id)->with('metas')->get();
                        } else {
                                $data['vehicles'] = Auth::user()->vehicles()->with('metas')->get();
                        }
                }
                
                // Pre-select vehicle if coming from workshop status
                $data['selected_vehicle_id'] = request('vehicle_id');
                
                return view('vehicles.vehicle_inspection_create', $data);
        }
        public function vehicle_inspection_index() {
                $vehicle = DriverLogsModel::where('driver_id', Auth::user()->id)->get()->toArray();
                $user = Auth::user();
                
                // Get existing vehicle reviews
                $existingReviews = VehicleReviewModel::select('vehicle_review.*')
                        ->whereHas('vehicle', function ($q) {
                                $q->whereHas('drivers', function ($q) {
                                        $q->where('users.id', auth()->id());
                                });
                        })
                        ->orderBy('vehicle_review.id', 'desc')->get();

                // Get vehicles with "Workshop" status that haven't been inspected yet
                // Handle different user types (Admin vs Driver)
                $workshopVehicles = VehicleModel::with('metas')->whereMeta('vehicle_status', 'Workshop')
                        ->whereNotIn('id', $existingReviews->pluck('vehicle_id'));
                
                // For drivers, only show vehicles assigned to them
                if ($user->user_type == "D") {
                        $workshopVehicles->whereHas('drivers', function ($q) {
                                $q->where('users.id', auth()->id());
                        });
                } elseif ($user->user_type == "B" && $user->company_id == null) {
                        // Boss Admin with no company sees no vehicles
                        $workshopVehicles->whereRaw('1 = 0');
                } elseif ($user->company_id) {
                        // Super Admin or Office Admin - filter by company
                        $workshopVehicles->where('company_id', $user->company_id);
                }
                // For admins, show all workshop vehicles
                
                $workshopVehicles = $workshopVehicles->get();

                // Create virtual inspection records for workshop vehicles
                $workshopInspections = collect();
                foreach ($workshopVehicles as $vehicle) {
                        // Ensure vehicle metadata is loaded
                        $vehicle->load('metas');
                        
                        $virtualInspection = new \stdClass();
                        $virtualInspection->id = 'workshop_' . $vehicle->id;
                        $virtualInspection->vehicle_id = $vehicle->id;
                        $virtualInspection->vehicle = $vehicle;
                        $virtualInspection->user = auth()->user();
                        $virtualInspection->reg_no = $vehicle->license_plate;
                        $virtualInspection->is_workshop_vehicle = true;
                        $virtualInspection->created_at = now();
                        $workshopInspections->push($virtualInspection);
                }

                // Get ALL vehicles with MOT expiry dates (let frontend filter handle timeframe)
                $motExpiryVehicles = VehicleModel::with('metas')
                        ->where(function($query) {
                                $query->whereHas('metas', function($q) {
                                        $q->where('key', 'mot_expiry_date')
                                          ->whereNotNull('value');
                                })
                                ->orWhereHas('metas', function($q) {
                                        $q->where('key', 'exp_date')
                                          ->whereNotNull('value');
                                })
                                ->orWhereNotNull('lic_exp_date');
                        });
                
                // Apply company filtering for MOT vehicles
                if ($user->user_type == "B" && $user->company_id == null) {
                        // Boss Admin with no company sees no vehicles
                        $motExpiryVehicles->whereRaw('1 = 0');
                } elseif ($user->company_id) {
                        // Super Admin or Office Admin - filter by company
                        $motExpiryVehicles->where('company_id', $user->company_id);
                }
                
                $motExpiryVehicles = $motExpiryVehicles->get()
                        ->filter(function($vehicle) {
                                $motExpiryDate = $vehicle->getMeta('mot_expiry_date') ?: 
                                               $vehicle->getMeta('exp_date') ?: 
                                               $vehicle->lic_exp_date;
                                
                                if (!$motExpiryDate) {
                                        return false;
                                }
                                
                                try {
                                        $expiryDate = \Carbon\Carbon::parse($motExpiryDate);
                                        // Only exclude vehicles that are already expired (more than 1 year ago)
                                        // This allows frontend to filter by timeframe
                                        return $expiryDate->isAfter(now()->subYear());
                                } catch (\Exception $e) {
                                        return false;
                                }
                        })
                        ->sortBy(function($vehicle) {
                                $motExpiryDate = $vehicle->getMeta('mot_expiry_date') ?: 
                                               $vehicle->getMeta('exp_date') ?: 
                                               $vehicle->lic_exp_date;
                                return \Carbon\Carbon::parse($motExpiryDate);
                        });

                // Merge existing reviews with workshop vehicles
                $allInspections = $existingReviews->concat($workshopInspections);
                
                $data['reviews'] = $allInspections->sortByDesc('created_at');
                $data['motExpiryVehicles'] = $motExpiryVehicles;
                
                return view('vehicles.vehicle_inspection_index', $data);
        }
        public function view_vehicle_inspection($id) {
                $data['review'] = VehicleReviewModel::find($id);
                return view('vehicles.view_vehicle_inspection', $data);
        }
        public function print_vehicle_inspection($id) {
                $data['review'] = VehicleReviewModel::find($id);
                return view('vehicles.print_vehicle_inspection', $data);
        }
        public function store_vehicle_review(VehiclReviewRequest $request) {
                $petrol_card = array('flag' => $request->get('petrol_card'), 'text' => $request->get('petrol_card_text'));
                $lights = array('flag' => $request->get('lights'), 'text' => $request->get('lights_text'));
                $invertor = array('flag' => $request->get('invertor'), 'text' => $request->get('invertor_text'));
                $car_mats = array('flag' => $request->get('car_mats'), 'text' => $request->get('car_mats_text'));
                $int_damage = array('flag' => $request->get('int_damage'), 'text' => $request->get('int_damage_text'));
                $int_lights = array('flag' => $request->get('int_lights'), 'text' => $request->get('int_lights_text'));
                $ext_car = array('flag' => $request->get('ext_car'), 'text' => $request->get('ext_car_text'));
                $tyre = array('flag' => $request->get('tyre'), 'text' => $request->get('tyre_text'));
                $ladder = array('flag' => $request->get('ladder'), 'text' => $request->get('ladder_text'));
                $leed = array('flag' => $request->get('leed'), 'text' => $request->get('leed_text'));
                $power_tool = array('flag' => $request->get('power_tool'), 'text' => $request->get('power_tool_text'));
                $ac = array('flag' => $request->get('ac'), 'text' => $request->get('ac_text'));
                $head_light = array('flag' => $request->get('head_light'), 'text' => $request->get('head_light_text'));
                $lock = array('flag' => $request->get('lock'), 'text' => $request->get('lock_text'));
                $windows = array('flag' => $request->get('windows'), 'text' => $request->get('windows_text'));
                $condition = array('flag' => $request->get('condition'), 'text' => $request->get('condition_text'));
                $oil_chk = array('flag' => $request->get('oil_chk'), 'text' => $request->get('oil_chk_text'));
                $suspension = array('flag' => $request->get('suspension'), 'text' => $request->get('suspension_text'));
                $tool_box = array('flag' => $request->get('tool_box'), 'text' => $request->get('tool_box_text'));
                $data = VehicleReviewModel::create([
                        'user_id' => $request->get('user_id'),
                        'vehicle_id' => $request->get('vehicle_id'),
                        'reg_no' => $request->get('reg_no'),
                        'kms_outgoing' => $request->get('kms_out'),
                        'kms_incoming' => $request->get('kms_in'),
                        'fuel_level_out' => $request->get('fuel_out'),
                        'fuel_level_in' => $request->get('fuel_in'),
                        'datetime_outgoing' => $request->get('datetime_out'),
                        'datetime_incoming' => $request->get('datetime_in'),
                        'petrol_card' => serialize($petrol_card),
                        'lights' => serialize($lights),
                        'invertor' => serialize($invertor),
                        'car_mats' => serialize($car_mats),
                        'int_damage' => serialize($int_damage),
                        'int_lights' => serialize($int_lights),
                        'ext_car' => serialize($ext_car),
                        'tyre' => serialize($tyre),
                        'ladder' => serialize($ladder),
                        'leed' => serialize($leed),
                        'power_tool' => serialize($power_tool),
                        'ac' => serialize($ac),
                        'head_light' => serialize($head_light),
                        'lock' => serialize($lock),
                        'windows' => serialize($windows),
                        'condition' => serialize($condition),
                        'oil_chk' => serialize($oil_chk),
                        'suspension' => serialize($suspension),
                        'tool_box' => serialize($tool_box),
                ]);
                $data->udf = serialize($request->get('udf'));
                $file = $request->file('image');
                if ($request->file('image') && $file->isValid()) {
                        $destinationPath = './uploads'; // upload path
                        $extension = $file->getClientOriginalExtension();
                        $fileName1 = Str::uuid() . '.' . $extension;
                        $file->move($destinationPath, $fileName1);
                        $data->image = $fileName1;
                }
                $data->save();
                if (Auth::user()->user_type == "D") {
                        return redirect()->route('vehicle_inspection');
                }
                return redirect()->route('vehicle_reviews');
        }
        public function vehicle_review_index() {
                $data['reviews'] = VehicleReviewModel::orderBy('id', 'desc')->get();
                return view('vehicles.vehicle_review_index', $data);
        }
        public function vehicle_review_fetch_data(Request $request) {
                if ($request->ajax()) {
                        $reviews = VehicleReviewModel::select('vehicle_review.*')->with('user')
                                ->leftJoin('vehicles', 'vehicle_review.vehicle_id', '=', 'vehicles.id')
                                ->leftJoin('vehicle_types', 'vehicle_types.id', '=', 'vehicles.type_id')
                                ->orderBy('id', 'desc');
                        return DataTables::eloquent($reviews)
                                ->addColumn('check', function ($vehicle) {
                                        $tag = '<input type="checkbox" name="ids[]" value="' . $vehicle->id . '" class="checkbox" id="chk' . $vehicle->id . '" onclick=\'checkcheckbox();\'>';
                                        return $tag;
                                })
                                ->editColumn('vehicle_image', function ($vehicle) {
                                        $src = ($vehicle->vehicle_image != null) ? asset('uploads/' . $vehicle->vehicle_image) : asset('assets/images/vehicle.jpeg');
                                        return '<img src="' . $src . '" height="70px" width="70px">';
                                })
                                ->addColumn('user', function ($vehicle) {
                                        return ($vehicle->user->name) ?? '';
                                })
                                ->addColumn('vehicle', function ($review) {
                                        return $review->vehicle->make_name . '-' . $review->vehicle->model_name . '-' . $review->vehicle->types->displayname;
                                })
                                ->addColumn('action', function ($vehicle) {
                                        return view('vehicles.vehicle_review_index_list_actions', ['row' => $vehicle]);
                                })
                                ->filterColumn('vehicle', function ($query, $keyword) {
                                        $query->whereRaw("CONCAT(vehicles.make_name , '-' , vehicles.model_name , '-' , vehicle_types.displayname) like ?", ["%$keyword%"]);
                                        return $query;
                                })
                                ->addIndexColumn()
                                ->rawColumns(['vehicle_image', 'action', 'check'])
                                ->make(true);
                        //return datatables(User::all())->toJson();
                }
        }
        public function review_edit($id) {
                // dd($id);
                $data['review'] = VehicleReviewModel::find($id);
                $user = Auth::user();
                if ($user->group_id == null || $user->user_type == "S") {
                        $data['vehicles'] = VehicleModel::get();
                } else {
                        $data['vehicles'] = VehicleModel::where('group_id', $user->group_id)->get();
                }
                $vehicleReview = VehicleReviewModel::where('id', $id)->get()->first();
                $data['udfs'] = unserialize($vehicleReview->udf);
                return view('vehicles.vehicle_review_edit', $data);
        }
        public function update_vehicle_review(VehiclReviewRequest $request) {
                // dd($request->all());
                $petrol_card = array('flag' => $request->get('petrol_card'), 'text' => $request->get('petrol_card_text'));
                $lights = array('flag' => $request->get('lights'), 'text' => $request->get('lights_text'));
                $invertor = array('flag' => $request->get('invertor'), 'text' => $request->get('invertor_text'));
                $car_mats = array('flag' => $request->get('car_mats'), 'text' => $request->get('car_mats_text'));
                $int_damage = array('flag' => $request->get('int_damage'), 'text' => $request->get('int_damage_text'));
                $int_lights = array('flag' => $request->get('int_lights'), 'text' => $request->get('int_lights_text'));
                $ext_car = array('flag' => $request->get('ext_car'), 'text' => $request->get('ext_car_text'));
                $tyre = array('flag' => $request->get('tyre'), 'text' => $request->get('tyre_text'));
                $ladder = array('flag' => $request->get('ladder'), 'text' => $request->get('ladder_text'));
                $leed = array('flag' => $request->get('leed'), 'text' => $request->get('leed_text'));
                $power_tool = array('flag' => $request->get('power_tool'), 'text' => $request->get('power_tool_text'));
                $ac = array('flag' => $request->get('ac'), 'text' => $request->get('ac_text'));
                $head_light = array('flag' => $request->get('head_light'), 'text' => $request->get('head_light_text'));
                $lock = array('flag' => $request->get('lock'), 'text' => $request->get('lock_text'));
                $windows = array('flag' => $request->get('windows'), 'text' => $request->get('windows_text'));
                $condition = array('flag' => $request->get('condition'), 'text' => $request->get('condition_text'));
                $oil_chk = array('flag' => $request->get('oil_chk'), 'text' => $request->get('oil_chk_text'));
                $suspension = array('flag' => $request->get('suspension'), 'text' => $request->get('suspension_text'));
                $tool_box = array('flag' => $request->get('tool_box'), 'text' => $request->get('tool_box_text'));
                $review = VehicleReviewModel::find($request->get('id'));
                $review->user_id = $request->get('user_id');
                $review->vehicle_id = $request->get('vehicle_id');
                $review->reg_no = $request->get('reg_no');
                $review->kms_outgoing = $request->get('kms_out');
                $review->kms_incoming = $request->get('kms_in');
                $review->fuel_level_out = $request->get('fuel_out');
                $review->fuel_level_in = $request->get('fuel_in');
                $review->datetime_outgoing = $request->get('datetime_out');
                $review->datetime_incoming = $request->get('datetime_in');
                $review->petrol_card = serialize($petrol_card);
                $review->lights = serialize($lights);
                $review->invertor = serialize($invertor);
                $review->car_mats = serialize($car_mats);
                $review->int_damage = serialize($int_damage);
                $review->int_lights = serialize($int_lights);
                $review->ext_car = serialize($ext_car);
                $review->tyre = serialize($tyre);
                $review->ladder = serialize($ladder);
                $review->leed = serialize($leed);
                $review->power_tool = serialize($power_tool);
                $review->ac = serialize($ac);
                $review->head_light = serialize($head_light);
                $review->lock = serialize($lock);
                $review->windows = serialize($windows);
                $review->condition = serialize($condition);
                $review->oil_chk = serialize($oil_chk);
                $review->suspension = serialize($suspension);
                $review->tool_box = serialize($tool_box);
                $file = $request->file('image');
                if ($request->file('image') && $file->isValid()) {
                        $destinationPath = './uploads'; // upload path
                        $extension = $file->getClientOriginalExtension();
                        $fileName1 = Str::uuid() . '.' . $extension;
                        $file->move($destinationPath, $fileName1);
                        $review->image = $fileName1;
                }
                $review->udf = serialize($request->get('udf'));
                $review->save();
                // return back();
                return redirect()->route('vehicle_reviews');
        }
        public function destroy_vehicle_review(Request $request) {
                VehicleReviewModel::find($request->get('id'))->delete();
                return redirect()->route('vehicle_reviews');
        }
        public function view_vehicle_review($id) {
                $data['review'] = VehicleReviewModel::find($id);
                return view('vehicles.view_vehicle_review', $data);
        }
        public function print_vehicle_review($id) {
                $data['review'] = VehicleReviewModel::find($id);
                return view('vehicles.print_vehicle_review', $data);
        }
        public function bulk_delete(Request $request) {
                $vehicles = VehicleModel::whereIn('id', $request->ids)->get();
                foreach ($vehicles as $vehicle) {
                        if ($vehicle->drivers->count()) {
                                $vehicle->drivers()->detach($vehicle->drivers->pluck('id')->toArray());
                        }
                        if (file_exists('./uploads/' . $vehicle->vehicle_image) && !is_dir('./uploads/' . $vehicle->vehicle_image)) {
                                unlink('./uploads/' . $vehicle->vehicle_image);
                        }
                }
                DriverVehicleModel::whereIn('vehicle_id', $request->ids)->delete();
                VehicleModel::whereIn('id', $request->ids)->forceDelete();
                IncomeModel::whereIn('vehicle_id', $request->ids)->forceDelete();
                Expense::whereIn('vehicle_id', $request->ids)->forceDelete();
                VehicleReviewModel::whereIn('vehicle_id', $request->ids)->forceDelete();
                ServiceReminderModel::whereIn('vehicle_id', $request->ids)->forceDelete();
                FuelModel::whereIn('vehicle_id', $request->ids)->forceDelete();
                
                // Delete bookings that reference these vehicles
                \DB::table('bookings')->whereIn('vehicle_id', $request->ids)->delete();
                \DB::table('booking_quotation')->whereIn('vehicle_id', $request->ids)->delete();
                
                // Delete work orders that reference these vehicles
                \DB::table('work_orders')->whereIn('vehicle_id', $request->ids)->delete();
                
                // Delete notes that reference these vehicles
                \DB::table('notes')->whereIn('vehicle_id', $request->ids)->delete();
                
                // Delete driver logs that reference these vehicles
                \DB::table('driver_logs')->whereIn('vehicle_id', $request->ids)->delete();
                
                // Delete work order logs that reference these vehicles
                \DB::table('work_order_logs')->whereIn('vehicle_id', $request->ids)->delete();
                
                // Delete vehicle metadata
                \DB::table('vehicles_meta')->whereIn('vehicle_id', $request->ids)->delete();
                
                return back();
        }
        public function bulk_delete_reviews(Request $request) {
                VehicleReviewModel::whereIn('id', $request->ids)->delete();
                return back();
        }
        public function enable($id) {
                $vehicle = VehicleModel::find($id);
                $vehicle->in_service = 1;
                $vehicle->save();
                return redirect()->back();
        }
        public function disable($id) {
                $vehicle = VehicleModel::find($id);
                $vehicle->in_service = 0;
                $vehicle->save();
                return redirect()->back();
        }
}


