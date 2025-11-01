<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\OnboardingDriver;
use App\OnboardingLink;
use App\CustomFormField;
use App\OnboardingFormFieldConfig;
use App\Model\User;
use App\Model\VehicleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use DataTables;
use Auth;

class OnboardingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:Drivers list')->except(['showPublicForm', 'submitPublicForm']);
        $this->middleware('permission:Drivers add', ['only' => ['store', 'approve']]);
        $this->middleware('permission:Drivers edit', ['only' => ['approve', 'reject']]);
        $this->middleware('permission:Drivers delete', ['only' => ['destroy']]);
    }

    /**
     * Display onboarding dashboard with form builder and drivers table
     */
    public function index()
    {
        // Initialize default field configurations if none exist
        OnboardingFormFieldConfig::initializeDefaultFields();

        $auth = \Auth::user();
        
        // Cache dashboard statistics to dramatically improve performance
        $cacheKey = 'onboarding_stats_' . $auth->id . '_' . ($auth->company_id ?? 'null');
        
        $stats = Cache::remember($cacheKey, 900, function() use ($auth) {
            return $this->loadDashboardStatistics($auth);
        });
        
        // Get historic stats - correct logic for onboarding vs approved drivers
        // PENDING: Only submitted status in onboarding table
        // APPROVED: Count from users table where user_type = 'D' (actual approved drivers)
        // REJECTED: Only rejected status in onboarding table
        // TOTAL: Onboarding records (submitted + rejected) + approved drivers from users table
        
        if (in_array($auth->user_type, ['S','O']) && !is_null($auth->company_id)) {
            $vehicleIds = \App\Model\VehicleModel::where('company_id', $auth->company_id)->pluck('id')->toArray();
            
            // Pending: Only submitted status in onboarding table
            $pending_count = OnboardingDriver::submitted()
                ->where(function($query) use ($vehicleIds) {
                    $query->whereIn('vehicle_id', $vehicleIds)
                          ->orWhereNull('vehicle_id');
                })->count();
                
            // Approved: Count from users table where user_type = 'D' (actual approved drivers)
            // Filter out test users and only count real drivers from onboarding process
            $approved_count = \App\Model\User::where('user_type', 'D')
                ->where(function($query) use ($vehicleIds) {
                    // Use the many-to-many relationship through driver_vehicle table
                    $query->whereHas('vehicles', function($q) use ($vehicleIds) {
                        $q->whereIn('vehicles.id', $vehicleIds);
                    })->orWhereDoesntHave('vehicles');
                })
                ->where(function($query) {
                    // Exclude test users based on patterns
                    $query->where('email', 'not like', '%@example.%')
                          ->where('name', 'not like', '%snnfjon%')
                          ->where('name', 'not like', '%tarantino%')
                          ->where('email', 'not like', '%josephwilk2022%')
                          ->whereYear('created_at', '>=', 2025);
                })->count();
                
            // Rejected: Only rejected status in onboarding table
            $rejected_count = OnboardingDriver::rejected()
                ->where(function($query) use ($vehicleIds) {
                    $query->whereIn('vehicle_id', $vehicleIds)
                          ->orWhereNull('vehicle_id');
                })->count();
                
            // Total: Onboarding records (submitted + rejected) + approved drivers from users table
            $onboarding_total = OnboardingDriver::where(function($query) use ($vehicleIds) {
                $query->whereIn('vehicle_id', $vehicleIds)
                      ->orWhereNull('vehicle_id');
            })->count();
            $total_count = $onboarding_total + $approved_count;
            
        } elseif ($auth->user_type === 'B' && is_null($auth->company_id)) {
            // For broker users without company, show all records
            $pending_count = OnboardingDriver::submitted()->count();
            $approved_count = \App\Model\User::where('user_type', 'D')
                ->where(function($query) {
                    // Exclude test users based on patterns
                    $query->where('email', 'not like', '%@example.%')
                          ->where('name', 'not like', '%snnfjon%')
                          ->where('name', 'not like', '%tarantino%')
                          ->where('email', 'not like', '%josephwilk2022%')
                          ->whereYear('created_at', '>=', 2025);
                })->count();
            $rejected_count = OnboardingDriver::rejected()->count();
            $onboarding_total = OnboardingDriver::count();
            $total_count = $onboarding_total + $approved_count;
        } else {
            // For admin users or other cases, show all historic records
            $pending_count = OnboardingDriver::submitted()->count();
            $approved_count = \App\Model\User::where('user_type', 'D')
                ->where(function($query) {
                    // Exclude test users based on patterns
                    $query->where('email', 'not like', '%@example.%')
                          ->where('name', 'not like', '%snnfjon%')
                          ->where('name', 'not like', '%tarantino%')
                          ->where('email', 'not like', '%josephwilk2022%')
                          ->whereYear('created_at', '>=', 2025);
                })->count();
            $rejected_count = OnboardingDriver::rejected()->count();
            $onboarding_total = OnboardingDriver::count();
            $total_count = $onboarding_total + $approved_count;
        }

        // Get custom fields filtered by company - cached for 15 minutes
        $customFieldsQuery = CustomFormField::ordered();
        if (in_array($auth->user_type, ['S','O']) && !is_null($auth->company_id)) {
            $customFieldsQuery->where('company_id', $auth->company_id);
        } elseif ($auth->user_type === 'B' && is_null($auth->company_id)) {
            // Broker users without company see all fields
        }

        // Cache custom fields and field configs to reduce queries
        $customFieldsCacheKey = 'onboarding_custom_fields_' . $auth->id . '_' . ($auth->company_id ?? 'null');
        $custom_fields = Cache::remember($customFieldsCacheKey, 900, function() use ($customFieldsQuery) {
            return $customFieldsQuery->get();
        });

        $fieldConfigsCacheKey = 'onboarding_field_configs_' . $auth->id . '_' . ($auth->company_id ?? 'null');
        $field_configs = Cache::remember($fieldConfigsCacheKey, 900, function() {
            return OnboardingFormFieldConfig::ordered()->get();
        });

        // Cache saved links for 15 minutes
        $linksCacheKey = 'onboarding_links_' . $auth->id . '_' . ($auth->company_id ?? 'null');
        $saved_links = Cache::remember($linksCacheKey, 900, function() {
            return OnboardingLink::active()->with('createdBy')->orderBy('created_at', 'desc')->get();
        });

        $data = [
            'page_title' => 'Driver Onboarding',
            'page_description' => 'Manage driver onboarding process',
            'custom_fields' => $custom_fields,
            'field_types' => CustomFormField::getFieldTypes(),
            'field_configs' => $field_configs,
            'pending_count' => $pending_count,
            'approved_count' => $approved_count,
            'rejected_count' => $rejected_count,
            'total_count' => $total_count,
            'saved_links' => $saved_links
        ];

        return view('onboarding.index', $data);
    }

    /**
     * Load dashboard statistics - cached to improve performance
     */
    private function loadDashboardStatistics($auth)
    {
        // This method moved here for clarity - stats already loaded above
        return [];
    }

    /**
     * Get onboarding drivers data for DataTables
     */
    public function fetchData(Request $request)
    {
        $auth = \Auth::user();
        
        // Load CustomFormField ONCE outside the loop to prevent N+1 queries
        // Cache for 5 minutes since custom fields don't change frequently
        $cacheKey = 'onboarding_custom_fields_' . $auth->id . '_' . ($auth->company_id ?? 'null');
        $customFields = Cache::remember($cacheKey, 300, function() use ($auth) {
            $query = CustomFormField::ordered();
            if (in_array($auth->user_type, ['S','O']) && !is_null($auth->company_id)) {
                $query->where('company_id', $auth->company_id);
            }
            return $query->get();
        });
        
        // DEBUG: Log total count before any queries
        try {
            $totalCount = OnboardingDriver::count();
            \Log::info('Total OnboardingDriver records: ' . $totalCount);
            
            // Get column names from schema to debug
            $columns = \Schema::getColumnListing('onboarding_drivers');
            \Log::info('Onboarding drivers table columns: ' . implode(', ', $columns));
        } catch (\Exception $e) {
            \Log::error('Error counting OnboardingDriver records: ' . $e->getMessage());
        }
        
        // SIMPLE WORKING QUERY - explicitly select all necessary columns
        $query = OnboardingDriver::select([
            'id', 'name', 'email', 'phone', 'license_number', 
            'license_upload_path', 'insurance_upload_path', 'vehicle_id',
            'scheme', 'insurance_selection', 'custom_data', 'form_data',
            'status', 'unique_token', 'license_expiry', 'address',
            'emergency_contact', 'emergency_phone', 'created_at', 'updated_at'
        ])->with(['vehicle']);

        // NO FILTERING AT ALL - SHOW EVERYTHING

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Use of() for proper column handling with our model
        return DataTables::of($query)
            ->addColumn('actions', function ($driver) use ($customFields) {
                // CRITICAL DEBUG: Log the driver ID being processed
                \Log::info('FETCHDATA] Processing driver for actions column:', [
                    'driver_id' => $driver->id,
                    'driver_id_raw' => $driver->getOriginal('id'),
                    'driver_id_type' => gettype($driver->id),
                    'driver_name' => $driver->name ?? 'N/A',
                    'driver_email' => $driver->email ?? 'N/A',
                    'full_driver_object' => json_encode($driver->toArray())
                ]);
                
                $actions = '<div class="d-flex justify-content-center gap-1">';
                
                if ($driver->isSubmitted()) {
                    $actions .= '<button class="btn btn-sm btn-success" onclick="approveDriver(\'' . $driver->id . '\')" title="Approve" style="padding: 6px 8px; min-width: 32px; height: 32px; border-radius: 4px; font-size: 12px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease-in-out;">
                        <i class="fas fa-check"></i>
                    </button>';
                    $actions .= '<button class="btn btn-sm btn-warning" onclick="deleteDriver(\'' . $driver->id . '\')" title="Reject" style="padding: 6px 8px; min-width: 32px; height: 32px; border-radius: 4px; font-size: 12px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease-in-out;">
                        <i class="fas fa-times"></i>
                    </button>';
                }
                
                // Prepare comprehensive driver data for instant display (embedded in button to avoid AJAX calls)
                $driverData = [
                    'id' => $driver->id,
                    'name' => $driver->name,
                    'email' => $driver->email,
                    'phone' => $driver->phone,
                    'license_number' => $driver->license_number,
                    'license_expiry' => $driver->license_expiry,
                    'address' => $driver->address,
                    'emergency_contact' => $driver->emergency_contact,
                    'emergency_phone' => $driver->emergency_phone,
                    'vehicle_id' => $driver->vehicle_id,
                    'scheme' => $driver->scheme,
                    'insurance_selection' => $driver->insurance_selection,
                    'status' => $driver->status,
                    'created_at' => $driver->created_at,
                    'license_upload_path' => $driver->license_upload_path,
                    'insurance_upload_path' => $driver->insurance_upload_path,
                    'license_url' => $driver->license_url,
                    'insurance_url' => $driver->insurance_url,
                    'custom_data' => $driver->custom_data,
                    'form_data' => $driver->form_data
                ];
                
                // Add vehicle details if available
                if ($driver->vehicle_id && $driver->vehicle) {
                    $driverData['vehicle_details'] = [
                        'make_name' => $driver->vehicle->make_name,
                        'model_name' => $driver->vehicle->model_name,
                        'license_plate' => $driver->vehicle->license_plate,
                    ];
                }
                
                // Add custom fields data for display and generate URLs for file fields
                if ($driver->custom_data && is_array($driver->custom_data)) {
                    // Initialize custom_data array in driverData if it doesn't exist
                    if (!isset($driverData['custom_data'])) {
                        $driverData['custom_data'] = [];
                    }
                    
                    // Copy all custom data first
                    foreach ($driver->custom_data as $key => $value) {
                        $driverData['custom_data'][$key] = $value;
                        
                        // Generate URL for custom file fields
                        if (strpos($key, 'custom_') === 0) {
                            $fieldId = str_replace('custom_', '', $key);
                            $field = $customFields->find($fieldId);
                            
                            if ($field && $field->field_type === 'file' && $value) {
                                // Generate URL similar to how we do for license and insurance
                                $useS3 = env('AWS_BUCKET') && env('AWS_KEY') && env('AWS_SECRET');
                                $filePath = $value;
                                
                                if ($useS3) {
                                    $s3BaseUrl = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_REGION') . '.amazonaws.com/';
                                    if (strpos($filePath, 'onboarding/documents/') === 0) {
                                        $driverData['custom_data'][$key . '_url'] = $s3BaseUrl . $filePath;
                                    } else {
                                        $driverData['custom_data'][$key . '_url'] = $s3BaseUrl . 'uploads/onboarding/' . $filePath;
                                    }
                                } else {
                                    if (strpos($filePath, 'onboarding/documents/') === 0) {
                                        $driverData['custom_data'][$key . '_url'] = asset('storage/' . $filePath);
                                    } else {
                                        $driverData['custom_data'][$key . '_url'] = asset('uploads/onboarding/' . $filePath);
                                    }
                                }
                            }
                        }
                    }
                }
                
                // If custom_data is empty, use form_data instead (fallback for legacy/new submissions)
                if ((!$driver->custom_data || empty($driver->custom_data)) && $driver->form_data && is_array($driver->form_data)) {
                    if (!isset($driverData['custom_data'])) {
                        $driverData['custom_data'] = [];
                    }
                    
                    // Copy all form_data into custom_data for display
                    foreach ($driver->form_data as $key => $value) {
                        $driverData['custom_data'][$key] = $value;
                        
                        // Generate URL for custom file fields from form_data
                        if (strpos($key, 'custom_') === 0) {
                            $fieldId = str_replace('custom_', '', $key);
                            $field = $customFields->find($fieldId);
                            
                            if ($field && $field->field_type === 'file' && $value) {
                                $useS3 = env('AWS_BUCKET') && env('AWS_KEY') && env('AWS_SECRET');
                                $filePath = $value;
                                
                                if ($useS3) {
                                    $s3BaseUrl = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_REGION') . '.amazonaws.com/';
                                    if (strpos($filePath, 'onboarding/documents/') === 0) {
                                        $driverData['custom_data'][$key . '_url'] = $s3BaseUrl . $filePath;
                                    } else {
                                        $driverData['custom_data'][$key . '_url'] = $s3BaseUrl . 'uploads/onboarding/' . $filePath;
                                    }
                                } else {
                                    if (strpos($filePath, 'onboarding/documents/') === 0) {
                                        $driverData['custom_data'][$key . '_url'] = asset('storage/' . $filePath);
                                    } else {
                                        $driverData['custom_data'][$key . '_url'] = asset('uploads/onboarding/' . $filePath);
                                    }
                                }
                            }
                        }
                    }
                }
                
                $jsonData = json_encode($driverData);
                
                // Debug logging for driver ID
                $deleteButtonHtml = '<button class="btn btn-sm btn-danger" onclick="deleteDriver(\'' . $driver->id . '\')" title="Delete" style="padding: 6px 8px; min-width: 32px; height: 32px; border-radius: 4px; font-size: 12px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease-in-out;">
                    <i class="fas fa-trash"></i>
                </button>';
                
                \Log::info('BUTTON] Delete button HTML:', [
                    'driver_id' => $driver->id,
                    'button_html' => $deleteButtonHtml,
                    'id_in_html' => 'deleteDriver(\'' . $driver->id . '\')'
                ]);
                
                $actions .= '<button class="btn btn-sm btn-info" data-driver-id="' . $driver->id . '" data-driver-info=\'' . htmlspecialchars($jsonData, ENT_QUOTES) . '\' onclick="toggleDriverDetailsInstant(this)" title="Toggle Details" style="padding: 6px 8px; min-width: 32px; height: 32px; border-radius: 4px; font-size: 12px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease-in-out;">
                    <i class="fas fa-eye"></i>
                </button>';
                
                $actions .= $deleteButtonHtml;
                
                $actions .= '</div>';

                return $actions;
            })
            ->addColumn('status_badge', function ($driver) {
                $statusClass = [
                    'submitted' => 'badge-warning',
                    'approved' => 'badge-success',
                    'rejected' => 'badge-danger'
                ];
                
                return '<span class="badge ' . ($statusClass[$driver->status] ?? 'badge-secondary') . '">' 
                    . ucfirst($driver->status) . '</span>';
            })
            ->addColumn('documents', function ($driver) {
                $docs = '<div class="d-flex justify-content-center gap-1">';
                
                if ($driver->license_upload_path) {
                    $docs .= '<a href="' . $driver->license_url . '" class="btn btn-sm btn-primary" target="_blank" title="View License" style="padding: 6px 8px; min-width: 32px; height: 32px; border-radius: 4px; font-size: 12px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease-in-out;">
                        <i class="fa fa-id-card"></i>
                    </a>';
                }
                
                if ($driver->insurance_upload_path) {
                    $docs .= '<a href="' . $driver->insurance_url . '" class="btn btn-sm btn-info" target="_blank" title="View Insurance" style="padding: 6px 8px; min-width: 32px; height: 32px; border-radius: 4px; font-size: 12px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease-in-out;">
                        <i class="fa fa-shield-alt"></i>
                    </a>';
                }
                
                $docs .= '</div>';
                
                return $docs ?: '<span class="text-muted">No documents</span>';
            })
            ->editColumn('created_at', function ($driver) {
                return $driver->created_at->format('M d, Y H:i');
            })
            ->rawColumns(['actions', 'status_badge', 'documents'])
            ->make(true);
    }

    /**
     * Store custom form field
     */
    public function storeField(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'field_name' => 'required|string|max:255',
            'field_type' => 'required|string|in:text,email,phone,dropdown,date,file,textarea',
            'dropdown_options' => 'array'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $fieldData = [
            'field_name' => $request->field_name,
            'field_label' => $request->field_name, // Use field_name as field_label for now
            'field_type' => $request->field_type,
            'is_required' => $request->has('is_required'),
            'sort_order' => CustomFormField::max('sort_order') + 1,
            'company_id' => Auth::user()->company_id
        ];

        if ($request->field_type === 'dropdown' && $request->has('dropdown_options')) {
            $fieldData['field_options'] = [
                'options' => array_filter($request->dropdown_options)
            ];
        }

        CustomFormField::create($fieldData);

        // Clear cache for the current user so new fields appear immediately
        $auth = Auth::user();
        Cache::forget('onboarding_custom_fields_' . $auth->id . '_' . ($auth->company_id ?? 'null'));

        return response()->json(['success' => true]);
    }

    /**
     * Generate onboarding link
     */
    public function generateLink(Request $request)
    {
        try {
            // Generate a unique token
            $token = Str::random(32);
            
            // Create the full link URL - matching the route definition
            $linkUrl = url('/driver-onboarding/' . $token);
            
            // Get the authenticated user's company_id
            $user = Auth::user();
            $companyId = $user->company_id ?? null;
            
            // Create the onboarding link with all required fields
            $link = OnboardingLink::create([
                'company_id' => $companyId,
                'token' => $token,
                'link' => $linkUrl,
                'expires_at' => now()->addDays(30), // Link expires in 30 days
                'is_used' => false,
                'is_active' => true,
                'usage_count' => 0,
                'created_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'link' => $link->link,
                'token' => $link->token,
                'expires_at' => $link->expires_at->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error generating onboarding link: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating link: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deactivate onboarding link
     */
    public function deactivateLink($id)
    {
        $link = OnboardingLink::findOrFail($id);
        $link->update(['is_active' => false]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete onboarding link
     */
    public function deleteLink($id)
    {
        $link = OnboardingLink::findOrFail($id);
        $link->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Delete custom field
     */
    public function deleteField($id)
    {
        CustomFormField::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Approve driver
     */
    public function approve($id)
    {
        \Log::info('Approving driver with ID: ' . $id);
        \Log::info('CSRF Token: ' . request()->header('X-CSRF-TOKEN'));
        \Log::info('Session Token: ' . csrf_token());
        \Log::info('Request Headers: ' . json_encode(request()->headers->all()));
        
        $onboardingDriver = OnboardingDriver::findOrFail($id);
        \Log::info('Found onboarding driver: ' . $onboardingDriver->name);
        
        // Clear performance caches since we're modifying data
        $this->clearOnboardingCaches(\Auth::user());
        
        try {
            // Check if a driver with this email already exists
            $existingDriver = \App\Model\User::where('email', $onboardingDriver->email)
                ->where('user_type', 'D')
                ->first();
            
            \Log::info('Checking for existing driver with email: ' . $onboardingDriver->email);
            if ($existingDriver) {
                \Log::info('Found existing driver: ID=' . $existingDriver->id . ', Name=' . $existingDriver->name . ', Active=' . $existingDriver->is_active . ', Company ID=' . ($existingDriver->company_id ?? 'NULL'));
            } else {
                \Log::info('No existing driver found with email: ' . $onboardingDriver->email);
            }
            
            \Log::info('Admin user company_id: ' . (Auth::user()->company_id ?? 'NULL'));
            
            if ($existingDriver) {
                \Log::info('Driver with email ' . $onboardingDriver->email . ' already exists. Updating existing driver. ID: ' . $existingDriver->id);
                $user = $existingDriver;
                $userId = $existingDriver->id;
                
                // Update the existing driver's basic info
                $user->name = $onboardingDriver->name;
                $user->is_active = true;
                $user->company_id = Auth::user()->company_id ?? 2; // Ensure company_id is set, default to 2 if null
                $user->save();
            } else {
                // Create a new user record in the drivers table
                // Use separate assignment for is_active to ensure proper boolean type
                $userData = [
                    "name" => $onboardingDriver->name,
                    "email" => $onboardingDriver->email,
                    "password" => bcrypt('password'), // Default password
                    "user_type" => "D",
                    'api_token' => \Illuminate\Support\Str::random(60),
                    'company_id' => Auth::user()->company_id ?? 2, // Set company_id from approving admin, default to 2 if null
                ];
                
                // Ensure is_active is set after creation to avoid type mismatch
                $userId = \App\Model\User::create($userData)->id;
                $user = \App\Model\User::find($userId);
                $user->is_active = true; // Set driver as active by default
                $user->save();
                
                \Log::info('Created new user with ID: ' . $userId);
                $user = \App\Model\User::find($userId);
            }
            
            \Log::info('Final driver details - ID: ' . $user->id . ', Name: ' . $user->name . ', Company ID: ' . ($user->company_id ?? 'NULL') . ', Active: ' . $user->is_active);
            
            // Note: user_id column doesn't exist in PostgreSQL users table
            // $user->user_id = Auth::user()->id; // Commented out to avoid SQL error
            
            // Set metadata from onboarding data - comprehensive transfer
            $metadata = [
                // Basic information
                'first_name' => explode(' ', $onboardingDriver->name)[0] ?? '',
                'last_name' => explode(' ', $onboardingDriver->name, 2)[1] ?? '',
                'phone' => $onboardingDriver->phone,
                'license_number' => $onboardingDriver->license_number,
                'is_active' => true, // Set driver as active by default
                
                // CRITICAL: Add DIRECT COLUMN fields from onboarding_drivers table
                'license_expiry' => $onboardingDriver->license_expiry,
                'vehicle_selection' => $onboardingDriver->vehicle_id,
                'vehicle_id' => $onboardingDriver->vehicle_id,
                'scheme' => $onboardingDriver->scheme,
                'scheme_selection' => $onboardingDriver->scheme,
                'insurance_selection' => $onboardingDriver->insurance_selection,
                
                // Document paths
                'license_image' => $onboardingDriver->license_upload_path,
                'license_upload_path' => $onboardingDriver->license_upload_path,
                'insurance_upload_path' => $onboardingDriver->insurance_upload_path,
                'documents' => $onboardingDriver->insurance_upload_path,
                'id_proof_type' => 'License',
                
                // Personal details - prioritize direct columns, fallback to custom_data
                'address' => $onboardingDriver->address ?? (is_array($onboardingDriver->custom_data['address'] ?? '') ? json_encode($onboardingDriver->custom_data['address']) : ($onboardingDriver->custom_data['address'] ?? '')),
                'city' => is_array($onboardingDriver->custom_data['city'] ?? '') ? json_encode($onboardingDriver->custom_data['city']) : ($onboardingDriver->custom_data['city'] ?? ''),
                'state' => is_array($onboardingDriver->custom_data['state'] ?? '') ? json_encode($onboardingDriver->custom_data['state']) : ($onboardingDriver->custom_data['state'] ?? ''),
                'country' => is_array($onboardingDriver->custom_data['country'] ?? '') ? json_encode($onboardingDriver->custom_data['country']) : ($onboardingDriver->custom_data['country'] ?? ''),
                'postal_code' => is_array($onboardingDriver->custom_data['postal_code'] ?? '') ? json_encode($onboardingDriver->custom_data['postal_code']) : ($onboardingDriver->custom_data['postal_code'] ?? ''),
                'date_of_birth' => is_array($onboardingDriver->custom_data['date_of_birth'] ?? '') ? json_encode($onboardingDriver->custom_data['date_of_birth']) : ($onboardingDriver->custom_data['date_of_birth'] ?? ''),
                'gender' => is_array($onboardingDriver->custom_data['gender'] ?? '') ? json_encode($onboardingDriver->custom_data['gender']) : ($onboardingDriver->custom_data['gender'] ?? ''),
                
                // Emergency contacts - prioritize direct columns, fallback to custom_data
                'emergency_contact' => $onboardingDriver->emergency_contact ?? (is_array($onboardingDriver->custom_data['emergency_contact_name'] ?? '') ? json_encode($onboardingDriver->custom_data['emergency_contact_name']) : ($onboardingDriver->custom_data['emergency_contact_name'] ?? '')),
                'emergency_phone' => $onboardingDriver->emergency_phone ?? (is_array($onboardingDriver->custom_data['emergency_contact_phone'] ?? '') ? json_encode($onboardingDriver->custom_data['emergency_contact_phone']) : ($onboardingDriver->custom_data['emergency_contact_phone'] ?? '')),
                'emergency_contact_name' => is_array($onboardingDriver->custom_data['emergency_contact_name'] ?? '') ? json_encode($onboardingDriver->custom_data['emergency_contact_name']) : ($onboardingDriver->custom_data['emergency_contact_name'] ?? ''),
                'emergency_contact_phone' => is_array($onboardingDriver->custom_data['emergency_contact_phone'] ?? '') ? json_encode($onboardingDriver->custom_data['emergency_contact_phone']) : ($onboardingDriver->custom_data['emergency_contact_phone'] ?? ''),
                'emergency_contact_number' => is_array($onboardingDriver->custom_data['emergency_contact_phone'] ?? '') ? json_encode($onboardingDriver->custom_data['emergency_contact_phone']) : ($onboardingDriver->custom_data['emergency_contact_phone'] ?? ''),
                
                // Expiry dates (ensure string values)
                'driver_license_expiry' => is_array($onboardingDriver->custom_data['driver_license_expiry'] ?? '') ? json_encode($onboardingDriver->custom_data['driver_license_expiry']) : ($onboardingDriver->custom_data['driver_license_expiry'] ?? ''),
                'license_expiry_date' => $onboardingDriver->license_expiry ?? (is_array($onboardingDriver->custom_data['driver_license_expiry'] ?? '') ? json_encode($onboardingDriver->custom_data['driver_license_expiry']) : ($onboardingDriver->custom_data['driver_license_expiry'] ?? '')),
                'insurance_expiry' => is_array($onboardingDriver->custom_data['insurance_expiry'] ?? '') ? json_encode($onboardingDriver->custom_data['insurance_expiry']) : ($onboardingDriver->custom_data['insurance_expiry'] ?? ''),
                'insurance_expiry_date' => is_array($onboardingDriver->custom_data['insurance_expiry'] ?? '') ? json_encode($onboardingDriver->custom_data['insurance_expiry']) : ($onboardingDriver->custom_data['insurance_expiry'] ?? ''),
                
                // Additional custom fields
                'custom_data' => is_array($onboardingDriver->custom_data) ? json_encode($onboardingDriver->custom_data) : $onboardingDriver->custom_data,
            ];
            
            // Add all custom fields from the onboarding form
            // Check custom_data first
            if (is_array($onboardingDriver->custom_data)) {
                foreach ($onboardingDriver->custom_data as $key => $value) {
                    if (!isset($metadata[$key])) {
                        // Convert arrays to JSON strings to avoid "Array to string conversion" error
                        $metadata[$key] = is_array($value) ? json_encode($value) : $value;
                    }
                }
            }
            
            // Also check form_data for custom fields (new format)
            if (is_array($onboardingDriver->form_data)) {
                foreach ($onboardingDriver->form_data as $key => $value) {
                    if (!isset($metadata[$key])) {
                        // Convert arrays to JSON strings to avoid "Array to string conversion" error
                        $metadata[$key] = is_array($value) ? json_encode($value) : $value;
                    }
                }
            }
            
            $user->setMeta($metadata);
            
            $user->save();
            \Log::info('Saved user metadata');
            
            // Give driver permissions
            $user->givePermissionTo([
                'Notes add', 'Notes edit', 'Notes delete', 'Notes list', 
                'Drivers list', 'Fuel add', 'Fuel edit', 'Fuel delete', 'Fuel list', 
                'VehicleInspection add', 'Transactions list', 'Transactions add', 
                'Transactions edit', 'Transactions delete'
            ]);
            \Log::info('Assigned permissions to user');
            
            // GDPR: Delete identity documents after approval
            try {
                $deletionService = new \App\Services\DriverIdentityDocumentDeletionService();
                $deletionResult = $deletionService->deleteDriverIdentityDocuments($user);
                \Log::info('GDPR document deletion completed', [
                    'driver_id' => $user->id,
                    'deleted_files_count' => $deletionResult['deleted_files_count'],
                    'errors_count' => count($deletionResult['errors'] ?? [])
                ]);
            } catch (\Exception $deletionException) {
                // Log error but don't fail the approval process
                \Log::error('Failed to delete driver identity documents', [
                    'driver_id' => $user->id,
                    'error' => $deletionException->getMessage(),
                    'trace' => $deletionException->getTraceAsString()
                ]);
            }
            
            // Send approval email notification
            try {
                $emailService = new \App\Utils\ResendEmailService();
                $emailResult = $emailService->sendDriverApprovalEmail($onboardingDriver->email, $onboardingDriver->name);
                
                if ($emailResult['success']) {
                    \Log::info('Driver approval email sent successfully', [
                        'driver_email' => $onboardingDriver->email,
                        'driver_name' => $onboardingDriver->name,
                        'resend_id' => $emailResult['resend_id'] ?? null
                    ]);
                } else {
                    \Log::warning('Failed to send driver approval email', [
                        'driver_email' => $onboardingDriver->email,
                        'driver_name' => $onboardingDriver->name,
                        'error' => $emailResult['message']
                    ]);
                }
            } catch (\Exception $emailException) {
                \Log::error('Exception while sending driver approval email', [
                    'driver_email' => $onboardingDriver->email,
                    'driver_name' => $onboardingDriver->name,
                    'error' => $emailException->getMessage()
                ]);
                // Don't fail the approval process if email fails
            }
            
            // Remove the driver from onboarding table
            $onboardingDriver->delete();
            \Log::info('Deleted onboarding driver record');
            
            return response()->json([
                'success' => true, 
                'message' => 'Driver approved successfully and added to drivers list'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error approving driver: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error approving driver: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject driver
     */
    public function reject($id)
    {
        \Log::info('[REJECT] reject() called with ID:', ['id' => $id, 'type' => gettype($id)]);
        
        try {
            $driver = OnboardingDriver::findOrFail($id);
            \Log::info('[REJECT] Found driver:', ['id' => $driver->id, 'name' => $driver->name, 'email' => $driver->email]);
            
            // Delete the driver instead of just updating status
            $driver->delete();
            \Log::info('[REJECT] Driver deleted successfully');
            
            // Clear performance caches since we're modifying data
            $this->clearOnboardingCaches(\Auth::user());

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('[REJECT] Error rejecting driver:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Error rejecting driver: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh CSRF token
     */
    public function refreshToken()
    {
        return response()->json([
            'csrf_token' => csrf_token()
        ]);
    }

    /**
     * Show driver details
     */
    public function show($id)
    {
        $driver = OnboardingDriver::findOrFail($id);
        $customFields = CustomFormField::ordered()->get();
        
        // Ensure URL accessors are included in the response
        $driverData = $driver->toArray();
        $driverData['license_url'] = $driver->license_url;
        $driverData['insurance_url'] = $driver->insurance_url;
        
        // Add vehicle details if vehicle_id exists
        if ($driver->vehicle_id) {
            $vehicle = VehicleModel::find($driver->vehicle_id);
            if ($vehicle) {
                $driverData['vehicle_details'] = [
                    'id' => $vehicle->id,
                    'make_name' => $vehicle->make_name,
                    'model_name' => $vehicle->model_name,
                    'year' => $vehicle->year,
                    'license_plate' => $vehicle->license_plate,
                    'fuel_type' => $vehicle->getMeta('fuel_type') ?? 'Petrol'
                ];
            }
        }
        
        // Generate URLs for custom file fields
        if ($driver->custom_data) {
            \Log::info('Driver custom_data:', $driver->custom_data);
            
            // Check if S3 is configured
            $useS3 = env('AWS_BUCKET') && env('AWS_KEY') && env('AWS_SECRET');
            
            foreach ($customFields as $field) {
                $fieldKey = 'custom_' . $field->id;
                if ($field->field_type === 'file' && isset($driver->custom_data[$fieldKey])) {
                    $filePath = $driver->custom_data[$fieldKey];
                    if ($filePath) {
                        if ($useS3) {
                            $s3BaseUrl = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_REGION') . '.amazonaws.com/';
                            if (strpos($filePath, 'onboarding/documents/') === 0) {
                                $driverData['custom_data'][$fieldKey . '_url'] = $s3BaseUrl . $filePath;
                            } else {
                                $driverData['custom_data'][$fieldKey . '_url'] = $s3BaseUrl . 'uploads/onboarding/' . $filePath;
                            }
                        } else {
                            if (strpos($filePath, 'onboarding/documents/') === 0) {
                                $driverData['custom_data'][$fieldKey . '_url'] = asset('storage/' . $filePath);
                            } else {
                                $driverData['custom_data'][$fieldKey . '_url'] = asset('uploads/onboarding/' . $filePath);
                            }
                        }
                    }
                }
            }
        }
        
        return response()->json([
            'success' => true, 
            'driver' => $driverData,
            'customFields' => $customFields
        ]);
    }

    /**
     * Delete driver
     */
    public function destroy($id)
    {
        \Log::info('DESTROY] destroy() called with ID:', ['id' => $id, 'type' => gettype($id)]);
        
        try {
            $driver = OnboardingDriver::findOrFail($id);
            \Log::info('DESTROY] Found driver:', ['id' => $driver->id, 'name' => $driver->name, 'email' => $driver->email]);
            
            $driver->delete();
            \Log::info('DESTROY] Driver deleted successfully');
            
            // Clear performance caches since we're modifying data
            $this->clearOnboardingCaches(\Auth::user());

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('DESTROY] Error deleting driver:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Error deleting driver: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Clear all onboarding-related caches when data changes
     */
    private function clearOnboardingCaches($user)
    {
        $userId = $user->id;
        $companyId = $user->company_id ?? 'null';
        
        // Clear all onboarding caches
        Cache::forget('onboarding_stats_' . $userId . '_' . $companyId);
        Cache::forget('onboarding_custom_fields_' . $userId . '_' . $companyId);
        Cache::forget('onboarding_field_configs_' . $userId . '_' . $companyId);
        Cache::forget('onboarding_links_' . $userId . '_' . $companyId);
        
        \Log::info('Cleared onboarding caches for user ' . $userId);
    }

    /**
     * Show public onboarding form
     */
    public function showPublicForm($token)
    {
        $link = OnboardingLink::where('link', 'like', '%' . $token)->whereRaw('is_active IS TRUE')->first();
        
        if (!$link) {
            abort(404);
        }

        // Get custom fields filtered by the link's company
        $customFieldsQuery = CustomFormField::ordered();
        if ($link->company_id) {
            $customFieldsQuery->where('company_id', $link->company_id);
        }
        
        $customFields = $customFieldsQuery->get();
        $custom_fields = $customFields; // Alias for view compatibility
        $fieldConfigs = OnboardingFormFieldConfig::visible()->ordered()->get();
        
        // Get available vehicles for selection
        $availableVehicles = VehicleModel::whereRaw('in_service IS TRUE')
            ->get()
            ->filter(function($vehicle) {
                return $vehicle->getVehicleStatusAttribute() === 'Available';
            })
            ->map(function($vehicle) {
                $fuelType = $vehicle->getMeta('fuel_type') ?? 'Petrol';
                $price = $vehicle->getMeta('price') ?? 'N/A';
                $pricePeriod = $vehicle->getMeta('price_period') ?? 'monthly';
                $vehicleScheme = $vehicle->getMeta('vehicle_scheme') ?? 'Rental';
                $insuranceDiscount = $vehicle->getMeta('insurance_discount') ?? '0';
                
                return [
                    'id' => $vehicle->id,
                    'make_name' => $vehicle->make_name,
                    'model_name' => $vehicle->model_name,
                    'year' => $vehicle->year,
                    'license_plate' => $vehicle->license_plate,
                    'fuel_type' => $fuelType,
                    'price' => $price,
                    'price_period' => $pricePeriod,
                    'vehicle_scheme' => $vehicleScheme,
                    'initial_cost' => $vehicle->getMeta('initial_cost') ?? 'N/A',
                    'insurance_discount' => $insuranceDiscount,
                    'display_text' => $vehicle->make_name . ' ' . $vehicle->model_name . ' - ' . $vehicle->year . ' - ' . $fuelType
                ];
            });
        
        return view('onboarding.public_form', compact('customFields', 'custom_fields', 'fieldConfigs', 'link', 'token', 'availableVehicles'));
    }

    /**
     * Submit public onboarding form
     */
    public function submitPublicForm(Request $request)
    {
        \Log::info('Onboarding form submission started', [
            'method' => $request->method(),
            'has_files' => $request->hasFile('license_file') || $request->hasFile('insurance_file')
        ]);
        
        try {
            // Get all field configurations
            $fieldConfigs = OnboardingFormFieldConfig::all()->keyBy('field_key');

            // Build validation rules dynamically
            $validationRules = [];

            // Map field_key to form input name
            $fieldMapping = [
                'full_name' => 'name',
                'email' => 'email',
                'phone' => 'phone',
                'license_number' => 'license_number',
                'license_file' => 'license_file',
                'insurance_file' => 'insurance_file',
            ];

            foreach ($fieldMapping as $fieldKey => $inputName) {
                $config = $fieldConfigs->get($fieldKey);
                if ($config && $config->is_visible) {
                    $rules = [];
                    if ($config->is_required) {
                        $rules[] = 'required';
                    } else {
                        $rules[] = 'nullable';
                    }
                    
                    // Add type-specific rules
                    if ($fieldKey === 'email') {
                        $rules[] = 'email';
                    }
                    if (in_array($fieldKey, ['license_file', 'insurance_file'])) {
                        $rules[] = 'file';
                        $rules[] = 'mimes:pdf,jpg,jpeg,png';
                        $rules[] = 'max:2048';
                    } else {
                        // Only add max:255 for non-file fields
                        $rules[] = 'max:255';
                    }
                    
                    $validationRules[$inputName] = implode('|', $rules);
                }
            }

            // Handle vehicle selection
            $vehicleConfig = $fieldConfigs->get('vehicle_selection');
            if ($vehicleConfig && $vehicleConfig->is_visible) {
                $rules = $vehicleConfig->is_required ? 'required' : 'nullable';
                $validationRules['vehicle_selection'] = $rules . '|string|exists:vehicles,id';
                $validationRules['insurance_selection'] = $rules . '|in:with_insurance,without_insurance';
            }

            // Handle scheme selection
            $schemeConfig = $fieldConfigs->get('scheme_selection');
            if ($schemeConfig && $schemeConfig->is_visible) {
                $rules = $schemeConfig->is_required ? 'required' : 'nullable';
                $validationRules['scheme_selection'] = $rules . '|in:Rental,Rent to Buy';
            }

            $validator = Validator::make($request->all(), $validationRules);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Store files only if they exist and are configured as visible
            $licensePath = null;
            $insurancePath = null;

            $licenseConfig = $fieldConfigs->get('license_file');
            if ($licenseConfig && $licenseConfig->is_visible && $request->hasFile('license_file')) {
                try {
                    $licenseFile = $request->file('license_file');
                    if ($licenseFile && $licenseFile->isValid()) {
                        // Check if S3 is configured, otherwise use local storage
                        $useS3 = env('AWS_BUCKET') && env('AWS_KEY') && env('AWS_SECRET');
                        
                        if ($useS3) {
                            // Upload to S3
                            $fileName = Str::uuid() . '.' . $licenseFile->getClientOriginalExtension();
                            $path = Storage::disk('s3')->putFileAs('uploads/onboarding', $licenseFile, $fileName);
                            $licensePath = $fileName;
                            \Log::info('License file uploaded to S3 successfully', ['filename' => $fileName, 'path' => $path]);
                        } else {
                            // Fallback to local storage
                            $destinationPath = public_path('uploads/onboarding');
                            if (!file_exists($destinationPath)) {
                                mkdir($destinationPath, 0755, true);
                            }
                            $fileName = Str::uuid() . '.' . $licenseFile->getClientOriginalExtension();
                            $licenseFile->move($destinationPath, $fileName);
                            $licensePath = $fileName;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('License file upload failed', ['error' => $e->getMessage()]);
                    return back()->withErrors(['license_file' => 'License file upload failed.'])->withInput();
                }
            }

            $insuranceConfig = $fieldConfigs->get('insurance_file');
            if ($insuranceConfig && $insuranceConfig->is_visible && $request->hasFile('insurance_file')) {
                try {
                    $insuranceFile = $request->file('insurance_file');
                    if ($insuranceFile && $insuranceFile->isValid()) {
                        // Check if S3 is configured, otherwise use local storage
                        $useS3 = env('AWS_BUCKET') && env('AWS_KEY') && env('AWS_SECRET');
                        
                        if ($useS3) {
                            // Upload to S3
                            $fileName = Str::uuid() . '.' . $insuranceFile->getClientOriginalExtension();
                            $path = Storage::disk('s3')->putFileAs('uploads/onboarding', $insuranceFile, $fileName);
                            $insurancePath = $fileName;
                            \Log::info('Insurance file uploaded to S3 successfully', ['filename' => $fileName, 'path' => $path]);
                        } else {
                            // Fallback to local storage
                            $destinationPath = public_path('uploads/onboarding');
                            if (!file_exists($destinationPath)) {
                                mkdir($destinationPath, 0755, true);
                            }
                            $fileName = Str::uuid() . '.' . $insuranceFile->getClientOriginalExtension();
                            $insuranceFile->move($destinationPath, $fileName);
                            $insurancePath = $fileName;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Insurance file upload failed', ['error' => $e->getMessage()]);
                    return back()->withErrors(['insurance_file' => 'Insurance file upload failed.'])->withInput();
                }
            }

            // Process custom file fields
            $customData = $request->except(['name', 'email', 'phone', 'license_number', 'license_file', 'insurance_file', '_token']);
            $customFields = CustomFormField::all();
            
            foreach ($customFields as $field) {
                $fieldKey = 'custom_' . $field->id;
                if ($field->field_type === 'file' && $request->hasFile($fieldKey)) {
                    try {
                        // Check if S3 is configured, otherwise use local storage
                        $useS3 = env('AWS_BUCKET') && env('AWS_KEY') && env('AWS_SECRET');
                        
                        if ($useS3) {
                            // Upload to S3
                            $fileName = Str::uuid() . '.' . $request->file($fieldKey)->getClientOriginalExtension();
                            $path = Storage::disk('s3')->putFileAs('uploads/onboarding', $request->file($fieldKey), $fileName);
                            $customData[$fieldKey] = $fileName;
                            \Log::info('Custom file uploaded to S3 successfully', ['field' => $fieldKey, 'filename' => $fileName, 'path' => $path]);
                        } else {
                            // Fallback to local storage
                            $destinationPath = public_path('uploads/onboarding');
                            if (!file_exists($destinationPath)) {
                                mkdir($destinationPath, 0755, true);
                            }
                            $fileName = Str::uuid() . '.' . $request->file($fieldKey)->getClientOriginalExtension();
                            $request->file($fieldKey)->move($destinationPath, $fileName);
                            $customData[$fieldKey] = $fileName;
                        }
                    } catch (\Exception $e) {
                        \Log::error('Custom file upload failed for field: ' . $fieldKey, ['error' => $e->getMessage()]);
                        // Continue processing other fields even if one fails
                    }
                }
            }

            // Find the onboarding link and increment usage count
            $token = $request->input('token');
            $link = OnboardingLink::where('token', $token)
                ->whereRaw('is_active IS TRUE')
                ->first();
            
            if ($link) {
                $link->incrementUsage();
            }

            // Get company_id from the onboarding link
            $companyId = null;
            if ($link && $link->company_id) {
                $companyId = $link->company_id;
            }
            
            // Build driver data matching Supabase schema
            $driverData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'license_number' => $request->license_number,
                'license_upload_path' => $licensePath,
                'insurance_upload_path' => $insurancePath,
                'vehicle_id' => $request->vehicle_selection,
                'scheme' => $request->scheme_selection,
                'insurance_selection' => $request->insurance_selection,
                'status' => 'submitted',
                'license_expiry' => $request->license_expiry,
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'form_data' => $customData,
                'company_id' => $companyId // Add company_id to the submission
            ];

            \Log::info('Creating onboarding driver record', ['driver_data' => $driverData]);
            
            OnboardingDriver::create($driverData);

            \Log::info('Onboarding form submitted successfully');
            
            // Clear cache for the admin user viewing the dashboard
            // This ensures new submissions appear immediately in the admin panel
            if (\Auth::check()) {
                $auth = \Auth::user();
                $this->clearOnboardingCaches($auth);
            }

            // Return JSON response for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Application submitted successfully!'
                ]);
            }
            
            return redirect()->back()->with('success', 'Application submitted successfully!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed in onboarding form', [
                'errors' => $e->errors(),
                'input' => $request->except(['_token', 'license_file', 'insurance_file'])
            ]);
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed. Please check your input.',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->validator)->withInput();
            
        } catch (\Exception $e) {
            \Log::error('Critical error in onboarding form submission', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token', 'license_file', 'insurance_file'])
            ]);
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while submitting your application. Please try again or contact support.',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['form_error' => 'An error occurred while submitting your application. Please try again or contact support.'])->withInput();
        }
    }

    /**
     * Update field configuration
     */
    public function updateFieldConfig(Request $request, $id)
    {
        try {
            \Log::info('Field config update request', [
                'id' => $id,
                'data' => $request->all()
            ]);

            // Convert string values to proper booleans
            $data = $request->all();
            if (isset($data['is_visible'])) {
                $data['is_visible'] = filter_var($data['is_visible'], FILTER_VALIDATE_BOOLEAN);
            }
            if (isset($data['is_required'])) {
                $data['is_required'] = filter_var($data['is_required'], FILTER_VALIDATE_BOOLEAN);
            }

            $validator = Validator::make($data, [
                'is_visible' => 'nullable|boolean',
                'is_required' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed', ['errors' => $validator->errors()]);
                return response()->json([
                    'success' => false, 
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed'
                ], 422);
            }

            $fieldConfig = OnboardingFormFieldConfig::findOrFail($id);
            
            $updateData = [];
            if ($request->has('is_visible')) {
                $updateData['is_visible'] = $data['is_visible'];
            }
            if ($request->has('is_required')) {
                $updateData['is_required'] = $data['is_required'];
            }

            \Log::info('Updating field config', [
                'id' => $id,
                'update_data' => $updateData,
                'old_state' => [
                    'is_visible' => $fieldConfig->is_visible,
                    'is_required' => $fieldConfig->is_required
                ]
            ]);

            $fieldConfig->update($updateData);

            \Log::info('Field config updated successfully', [
                'id' => $id,
                'new_state' => [
                    'is_visible' => $fieldConfig->fresh()->is_visible,
                    'is_required' => $fieldConfig->fresh()->is_required
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Field configuration updated successfully',
                'data' => [
                    'is_visible' => $fieldConfig->fresh()->is_visible,
                    'is_required' => $fieldConfig->fresh()->is_required
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating field config', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating field configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get field configurations for public form
     */
    public function getFieldConfigs()
    {
        return OnboardingFormFieldConfig::visible()->ordered()->get();
    }
}
