<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\OnboardingDriver;
use App\OnboardingLink;
use App\CustomFormField;
use App\Model\User;
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
        $data = [
            'page_title' => 'Driver Onboarding',
            'page_description' => 'Manage driver onboarding process',
            'custom_fields' => CustomFormField::ordered()->get(),
            'field_types' => CustomFormField::getFieldTypes(),
            'pending_count' => OnboardingDriver::submitted()->count(),
            'approved_count' => OnboardingDriver::approved()->count(),
            'rejected_count' => OnboardingDriver::rejected()->count(),
            'total_count' => OnboardingDriver::count(),
            'saved_links' => OnboardingLink::active()->with('createdBy')->orderBy('created_at', 'desc')->get()
        ];

        return view('onboarding.index', $data);
    }

    /**
     * Get onboarding drivers data for DataTables
     */
    public function fetchData(Request $request)
    {
        $query = OnboardingDriver::select([
            'id',
            'name',
            'email',
            'phone',
            'license_number',
            'status',
            'license_upload_path',
            'insurance_upload_path',
            'created_at'
        ]);

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addColumn('actions', function ($driver) {
                $actions = '';
                
                if ($driver->isSubmitted()) {
                    $actions .= '<button class="btn btn-success btn-sm mx-1" onclick="approveDriver(' . $driver->id . ')" title="Approve">
                        <i class="fa fa-check"></i>
                    </button>';
                    $actions .= '<button class="btn btn-warning btn-sm mx-1" onclick="rejectDriver(' . $driver->id . ')" title="Reject">
                        <i class="fa fa-times"></i>
                    </button>';
                }
                
                $actions .= '<button class="btn btn-info btn-sm mx-1" onclick="viewDriver(' . $driver->id . ')" title="View Details">
                    <i class="fa fa-eye"></i>
                </button>';
                
                $actions .= '<button class="btn btn-danger btn-sm mx-1" onclick="deleteDriver(' . $driver->id . ')" title="Delete">
                    <i class="fa fa-trash"></i>
                </button>';

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
                $docs = '';
                
                if ($driver->license_upload_path) {
                    $docs .= '<a href="' . $driver->license_url . '" class="btn btn-sm btn-outline-primary mx-1" target="_blank" title="View License">
                        <i class="fa fa-id-card"></i>
                    </a>';
                }
                
                if ($driver->insurance_upload_path) {
                    $docs .= '<a href="' . $driver->insurance_url . '" class="btn btn-sm btn-outline-info mx-1" target="_blank" title="View Insurance">
                        <i class="fa fa-shield-alt"></i>
                    </a>';
                }
                
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
            'field_type' => 'required|in:text,email,phone,dropdown,date,file,textarea',
            'is_required' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $fieldOptions = [];
        
        if ($request->field_type === 'dropdown' && $request->has('dropdown_options')) {
            $fieldOptions['options'] = array_filter($request->dropdown_options);
        }
        
        if ($request->field_type === 'file') {
            $fieldOptions['max_size'] = $request->max_file_size ?? 2048; // KB
            $fieldOptions['allowed_types'] = $request->allowed_file_types ?? ['pdf', 'jpg', 'png'];
        }

        $field = CustomFormField::create([
            'field_name' => $request->field_name,
            'field_type' => $request->field_type,
            'field_options' => $fieldOptions,
            'is_required' => $request->has('is_required'),
            'sort_order' => CustomFormField::count()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Custom field added successfully',
            'field' => $field
        ]);
    }

    /**
     * Delete custom form field
     */
    public function deleteField($id)
    {
        $field = CustomFormField::findOrFail($id);
        $field->delete();

        return response()->json([
            'success' => true,
            'message' => 'Custom field deleted successfully'
        ]);
    }

    /**
     * Generate onboarding link
     */
    public function generateLink()
    {
        $token = Str::random(40);
        $link = url('/driver-onboarding/' . $token);
        
        // Save the link to database
        $onboardingLink = OnboardingLink::create([
            'token' => $token,
            'link' => $link,
            'created_by' => Auth::id()
        ]);
        
        return response()->json([
            'success' => true,
            'link' => $link,
            'token' => $token,
            'id' => $onboardingLink->id
        ]);
    }

    /**
     * Show public onboarding form
     */
    public function showPublicForm($token = null)
    {
        $customFields = CustomFormField::ordered()->get();
        
        // Track link usage if token is provided
        if ($token) {
            $onboardingLink = OnboardingLink::where('token', $token)->where('is_active', true)->first();
            if ($onboardingLink) {
                $onboardingLink->incrementUsage();
            }
        }
        
        return view('onboarding.public_form', [
            'token' => $token,
            'custom_fields' => $customFields
        ]);
    }

    /**
     * Handle public form submission
     */
    public function submitPublicForm(Request $request)
    {
        // Basic validation
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:onboarding_drivers,email',
            'phone' => 'required|string|max:20',
            'license_number' => 'required|string|max:50',
            'license_upload' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'insurance_upload' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ];

        // Add validation for custom fields
        $customFields = CustomFormField::all();
        foreach ($customFields as $field) {
            if ($field->isRequired()) {
                $rules['custom_' . $field->id] = $field->getValidationRules();
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Handle file uploads
        $licenseFileName = null;
        $insuranceFileName = null;

        if ($request->hasFile('license_upload')) {
            $licenseFileName = time() . '_license_' . $request->file('license_upload')->getClientOriginalName();
            $request->file('license_upload')->move(public_path('uploads/onboarding'), $licenseFileName);
        }

        if ($request->hasFile('insurance_upload')) {
            $insuranceFileName = time() . '_insurance_' . $request->file('insurance_upload')->getClientOriginalName();
            $request->file('insurance_upload')->move(public_path('uploads/onboarding'), $insuranceFileName);
        }

        // Collect custom field data
        $customData = [];
        foreach ($customFields as $field) {
            $fieldKey = 'custom_' . $field->id;
            if ($request->has($fieldKey)) {
                if ($field->isFileUpload() && $request->hasFile($fieldKey)) {
                    $fileName = time() . '_custom_' . $field->id . '_' . $request->file($fieldKey)->getClientOriginalName();
                    $request->file($fieldKey)->move(public_path('uploads/onboarding'), $fileName);
                    $customData[$field->field_name] = $fileName;
                } else {
                    $customData[$field->field_name] = $request->input($fieldKey);
                }
            }
        }

        // Create onboarding record
        $driver = OnboardingDriver::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'license_number' => $request->license_number,
            'license_upload_path' => $licenseFileName,
            'insurance_upload_path' => $insuranceFileName,
            'custom_data' => $customData,
            'status' => OnboardingDriver::STATUS_SUBMITTED
        ]);

        return view('onboarding.success', [
            'driver' => $driver,
            'message' => 'Your application has been submitted successfully! We will review your information and get back to you soon.'
        ]);
    }

    /**
     * Show driver details
     */
    public function show($id)
    {
        $driver = OnboardingDriver::findOrFail($id);
        $customFields = CustomFormField::all()->keyBy('field_name');
        
        return response()->json([
            'success' => true,
            'driver' => $driver,
            'custom_fields' => $customFields
        ]);
    }

    /**
     * Approve driver and move to main drivers table
     */
    public function approve($id)
    {
        $onboardingDriver = OnboardingDriver::findOrFail($id);
        
        if (!$onboardingDriver->isSubmitted()) {
            return response()->json([
                'success' => false,
                'message' => 'Driver is not in submitted status'
            ], 400);
        }

        // Create new user
        $user = User::create([
            'name' => $onboardingDriver->name,
            'email' => $onboardingDriver->email,
            'password' => bcrypt('password123'), // Default password
            'user_type' => 'D'
        ]);

        // Set user meta data
        $user->setMeta('phone', $onboardingDriver->phone);
        $user->setMeta('license_number', $onboardingDriver->license_number);
        
        if ($onboardingDriver->license_upload_path) {
            $user->setMeta('documents', $onboardingDriver->license_upload_path);
        }

        // Set custom data as meta
        if ($onboardingDriver->custom_data) {
            foreach ($onboardingDriver->custom_data as $key => $value) {
                $user->setMeta($key, $value);
            }
        }

        // Update onboarding status
        $onboardingDriver->update(['status' => OnboardingDriver::STATUS_APPROVED]);

        return response()->json([
            'success' => true,
            'message' => 'Driver approved and added to main drivers list',
            'user_id' => $user->id
        ]);
    }

    /**
     * Reject driver application
     */
    public function reject($id)
    {
        $driver = OnboardingDriver::findOrFail($id);
        
        if (!$driver->isSubmitted()) {
            return response()->json([
                'success' => false,
                'message' => 'Driver is not in submitted status'
            ], 400);
        }

        $driver->update(['status' => OnboardingDriver::STATUS_REJECTED]);

        return response()->json([
            'success' => true,
            'message' => 'Driver application rejected'
        ]);
    }

    /**
     * Delete onboarding driver
     */
    public function destroy($id)
    {
        $driver = OnboardingDriver::findOrFail($id);
        
        // Delete uploaded files
        if ($driver->license_upload_path) {
            $licensePath = public_path('uploads/onboarding/' . $driver->license_upload_path);
            if (file_exists($licensePath)) {
                unlink($licensePath);
            }
        }
        
        if ($driver->insurance_upload_path) {
            $insurancePath = public_path('uploads/onboarding/' . $driver->insurance_upload_path);
            if (file_exists($insurancePath)) {
                unlink($insurancePath);
            }
        }

        // Delete custom field files
        if ($driver->custom_data) {
            foreach ($driver->custom_data as $key => $value) {
                $customFields = CustomFormField::where('field_name', $key)->first();
                if ($customFields && $customFields->isFileUpload()) {
                    $customFilePath = public_path('uploads/onboarding/' . $value);
                    if (file_exists($customFilePath)) {
                        unlink($customFilePath);
                    }
                }
            }
        }

        $driver->delete();

        return response()->json([
            'success' => true,
            'message' => 'Driver record deleted successfully'
        ]);
    }

    /**
     * Get onboarding statistics
     */
    public function getStats()
    {
        return response()->json([
            'pending' => OnboardingDriver::submitted()->count(),
            'approved' => OnboardingDriver::approved()->count(),
            'rejected' => OnboardingDriver::rejected()->count(),
            'total' => OnboardingDriver::count()
        ]);
    }

    /**
     * Update form field order
     */
    public function updateFieldOrder(Request $request)
    {
        $fieldOrder = $request->input('field_order', []);
        
        foreach ($fieldOrder as $index => $fieldId) {
            CustomFormField::where('id', $fieldId)->update(['sort_order' => $index]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Field order updated successfully'
        ]);
    }

    /**
     * Deactivate onboarding link
     */
    public function deactivateLink($id)
    {
        $link = OnboardingLink::findOrFail($id);
        $link->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Onboarding link deactivated successfully'
        ]);
    }
}