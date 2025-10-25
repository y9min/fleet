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

        $data = [
            'page_title' => 'Driver Onboarding',
            'page_description' => 'Manage driver onboarding process',
            'custom_fields' => CustomFormField::ordered()->get(),
            'field_types' => CustomFormField::getFieldTypes(),
            'field_configs' => OnboardingFormFieldConfig::ordered()->get(),
            'pending_count' => $pending_count,
            'approved_count' => $approved_count,
            'rejected_count' => $rejected_count,
            'total_count' => $total_count,
            'saved_links' => OnboardingLink::active()->with('createdBy')->orderBy('created_at', 'desc')->get()
        ];

        return view('onboarding.index', $data);
    }

    /**
     * Get onboarding drivers data for DataTables
     */
    public function fetchData(Request $request)
    {
        $auth = \Auth::user();
        $query = OnboardingDriver::select([
            'id',
            'name',
            'email',
            'phone',
            'license_number',
            'status',
            'license_upload_path',
            'insurance_upload_path',
            'created_at',
            'license_expiry',
            'address',
            'emergency_contact',
            'emergency_phone',
            'vehicle_id',
            'scheme',
            'insurance_selection',
            'custom_data',
            'form_data'
        ]);

        // Company scoping via vehicle_id - consistent with stats logic
        if (in_array($auth->user_type, ['S','O']) && !is_null($auth->company_id)) {
            $vehicleIds = \App\Model\VehicleModel::where('company_id', $auth->company_id)->pluck('id');
            // Include records with matching vehicle_id OR null vehicle_id for historic data
            $query->where(function($q) use ($vehicleIds) {
                $q->whereIn('vehicle_id', $vehicleIds)
                  ->orWhereNull('vehicle_id');
            });
        } elseif ($auth->user_type === 'B' && is_null($auth->company_id)) {
            // For broker users without company, show all records
            // Remove the whereRaw('1=0') restriction to show historic data
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addColumn('actions', function ($driver) {
                $actions = '<div class="d-flex justify-content-center gap-1">';
                
                if ($driver->isSubmitted()) {
                    $actions .= '<button class="btn btn-sm btn-success" onclick="approveDriver(\'' . $driver->id . '\')" title="Approve" style="padding: 6px 8px; min-width: 32px; height: 32px; border-radius: 4px; font-size: 12px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease-in-out;">
                        <i class="fas fa-check"></i>
                    </button>';
                    $actions .= '<button class="btn btn-sm btn-warning" onclick="rejectDriver(\'' . $driver->id . '\')" title="Reject" style="padding: 6px 8px; min-width: 32px; height: 32px; border-radius: 4px; font-size: 12px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease-in-out;">
                        <i class="fas fa-times"></i>
                    </button>';
                }
                
                // Prepare driver data for instant display (embedded in button to avoid AJAX calls)
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
                if ($driver->vehicle_id) {
                    $vehicle = \App\Model\VehicleModel::find($driver->vehicle_id);
                    if ($vehicle) {
                        $driverData['vehicle_details'] = [
                            'make_name' => $vehicle->make_name,
                            'model_name' => $vehicle->model_name,
                            'license_plate' => $vehicle->license_plate,
                        ];
                    }
                }
                
                $jsonData = json_encode($driverData);
                $actions .= '<button class="btn btn-sm btn-info" data-driver-id="' . $driver->id . '" data-driver-info=\'' . htmlspecialchars($jsonData, ENT_QUOTES) . '\' onclick="toggleDriverDetailsInstant(this)" title="Toggle Details" style="padding: 6px 8px; min-width: 32px; height: 32px; border-radius: 4px; font-size: 12px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease-in-out;">
                    <i class="fas fa-eye"></i>
                </button>';
                
                $actions .= '<button class="btn btn-sm btn-danger" onclick="deleteDriver(\'' . $driver->id . '\')" title="Delete" style="padding: 6px 8px; min-width: 32px; height: 32px; border-radius: 4px; font-size: 12px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease-in-out;">
                    <i class="fas fa-trash"></i>
                </button>';
                
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
            'field_type' => $request->field_type,
            'is_required' => $request->has('is_required'),
            'sort_order' => CustomFormField::max('sort_order') + 1
        ];

        if ($request->field_type === 'dropdown' && $request->has('dropdown_options')) {
            $fieldData['field_options'] = [
                'options' => array_filter($request->dropdown_options)
            ];
        }

        CustomFormField::create($fieldData);

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
                $user->is_active = 1;
                $user->company_id = Auth::user()->company_id ?? 2; // Ensure company_id is set, default to 2 if null
                $user->save();
            } else {
                // Create a new user record in the drivers table
                $userId = \App\Model\User::create([
                    "name" => $onboardingDriver->name,
                    "email" => $onboardingDriver->email,
                    "password" => bcrypt('password'), // Default password
                    "user_type" => "D",
                    "is_active" => 1, // Set driver as active by default
                    'api_token' => \Illuminate\Support\Str::random(60),
                    'company_id' => Auth::user()->company_id ?? 2, // Set company_id from approving admin, default to 2 if null
                ])->id;
                
                \Log::info('Created new user with ID: ' . $userId);
                $user = \App\Model\User::find($userId);
            }
            
            \Log::info('Final driver details - ID: ' . $user->id . ', Name: ' . $user->name . ', Company ID: ' . ($user->company_id ?? 'NULL') . ', Active: ' . $user->is_active);
            
            $user->user_id = Auth::user()->id;
            
            // Set metadata from onboarding data - comprehensive transfer
            $metadata = [
                // Basic information
                'first_name' => explode(' ', $onboardingDriver->name)[0] ?? '',
                'last_name' => explode(' ', $onboardingDriver->name, 2)[1] ?? '',
                'phone' => $onboardingDriver->phone,
                'license_number' => $onboardingDriver->license_number,
                'is_active' => 1, // Set driver as active by default
                
                // Document paths
                'license_image' => $onboardingDriver->license_upload_path,
                'license_upload_path' => $onboardingDriver->license_upload_path,
                'insurance_upload_path' => $onboardingDriver->insurance_upload_path,
                'documents' => $onboardingDriver->insurance_upload_path,
                'id_proof_type' => 'License',
                
                // Personal details from custom_data (ensure string values)
                'address' => is_array($onboardingDriver->custom_data['address'] ?? '') ? json_encode($onboardingDriver->custom_data['address']) : ($onboardingDriver->custom_data['address'] ?? ''),
                'city' => is_array($onboardingDriver->custom_data['city'] ?? '') ? json_encode($onboardingDriver->custom_data['city']) : ($onboardingDriver->custom_data['city'] ?? ''),
                'state' => is_array($onboardingDriver->custom_data['state'] ?? '') ? json_encode($onboardingDriver->custom_data['state']) : ($onboardingDriver->custom_data['state'] ?? ''),
                'country' => is_array($onboardingDriver->custom_data['country'] ?? '') ? json_encode($onboardingDriver->custom_data['country']) : ($onboardingDriver->custom_data['country'] ?? ''),
                'postal_code' => is_array($onboardingDriver->custom_data['postal_code'] ?? '') ? json_encode($onboardingDriver->custom_data['postal_code']) : ($onboardingDriver->custom_data['postal_code'] ?? ''),
                'date_of_birth' => is_array($onboardingDriver->custom_data['date_of_birth'] ?? '') ? json_encode($onboardingDriver->custom_data['date_of_birth']) : ($onboardingDriver->custom_data['date_of_birth'] ?? ''),
                'gender' => is_array($onboardingDriver->custom_data['gender'] ?? '') ? json_encode($onboardingDriver->custom_data['gender']) : ($onboardingDriver->custom_data['gender'] ?? ''),
                
                // Emergency contacts (ensure string values)
                'emergency_contact_name' => is_array($onboardingDriver->custom_data['emergency_contact_name'] ?? '') ? json_encode($onboardingDriver->custom_data['emergency_contact_name']) : ($onboardingDriver->custom_data['emergency_contact_name'] ?? ''),
                'emergency_contact_phone' => is_array($onboardingDriver->custom_data['emergency_contact_phone'] ?? '') ? json_encode($onboardingDriver->custom_data['emergency_contact_phone']) : ($onboardingDriver->custom_data['emergency_contact_phone'] ?? ''),
                'emergency_contact_number' => is_array($onboardingDriver->custom_data['emergency_contact_phone'] ?? '') ? json_encode($onboardingDriver->custom_data['emergency_contact_phone']) : ($onboardingDriver->custom_data['emergency_contact_phone'] ?? ''),
                
                // Expiry dates (ensure string values)
                'driver_license_expiry' => is_array($onboardingDriver->custom_data['driver_license_expiry'] ?? '') ? json_encode($onboardingDriver->custom_data['driver_license_expiry']) : ($onboardingDriver->custom_data['driver_license_expiry'] ?? ''),
                'license_expiry_date' => is_array($onboardingDriver->custom_data['driver_license_expiry'] ?? '') ? json_encode($onboardingDriver->custom_data['driver_license_expiry']) : ($onboardingDriver->custom_data['driver_license_expiry'] ?? ''),
                'insurance_expiry' => is_array($onboardingDriver->custom_data['insurance_expiry'] ?? '') ? json_encode($onboardingDriver->custom_data['insurance_expiry']) : ($onboardingDriver->custom_data['insurance_expiry'] ?? ''),
                'insurance_expiry_date' => is_array($onboardingDriver->custom_data['insurance_expiry'] ?? '') ? json_encode($onboardingDriver->custom_data['insurance_expiry']) : ($onboardingDriver->custom_data['insurance_expiry'] ?? ''),
                
                // Additional custom fields
                'custom_data' => is_array($onboardingDriver->custom_data) ? json_encode($onboardingDriver->custom_data) : $onboardingDriver->custom_data,
            ];
            
            // Add all custom fields from the onboarding form
            if (is_array($onboardingDriver->custom_data)) {
                foreach ($onboardingDriver->custom_data as $key => $value) {
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
        $driver = OnboardingDriver::findOrFail($id);
        $driver->update(['status' => 'rejected']);

        return response()->json(['success' => true]);
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
            foreach ($customFields as $field) {
                $fieldKey = 'custom_' . $field->id;
                if ($field->field_type === 'file' && isset($driver->custom_data[$fieldKey])) {
                    $filePath = $driver->custom_data[$fieldKey];
                    if ($filePath) {
                        $driverData['custom_data'][$fieldKey . '_url'] = asset('storage/' . $filePath);
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
        $driver = OnboardingDriver::findOrFail($id);
        $driver->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Show public onboarding form
     */
    public function showPublicForm($token)
    {
        $link = OnboardingLink::where('link', 'like', '%' . $token)->where('is_active', true)->first();
        
        if (!$link) {
            abort(404);
        }

        $customFields = CustomFormField::ordered()->get();
        $custom_fields = $customFields; // Alias for view compatibility
        $fieldConfigs = OnboardingFormFieldConfig::visible()->ordered()->get();
        
        // Get available vehicles for selection
        $availableVehicles = VehicleModel::where('in_service', 1)
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
            ->where('is_active', true)
            ->first();
        
        if ($link) {
            $link->incrementUsage();
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
            'form_data' => $customData
        ];

        OnboardingDriver::create($driverData);

        return redirect()->back()->with('success', 'Application submitted successfully!');
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
