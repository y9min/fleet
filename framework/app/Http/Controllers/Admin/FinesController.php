<?php

/*
@copyright

Fleet Manager v7.1.2

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Fine;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\VehicleModel;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Redirect;
use Validator;

class FinesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:Fines add', ['only' => ['create', 'store']]);
        $this->middleware('permission:Fines edit', ['only' => ['edit', 'update', 'updateStatus']]);
        $this->middleware('permission:Fines delete', ['only' => ['bulk_delete', 'destroy']]);
        $this->middleware('permission:Fines list', ['only' => ['index', 'show', 'fetch_data']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('fines.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $auth = \Auth::user();
        if (in_array($auth->user_type, ['S','O']) && !is_null($auth->company_id)) {
            $vehicles = VehicleModel::whereRaw('in_service IS TRUE')->where('company_id', $auth->company_id)->get();
            $drivers = User::where('user_type', 'D')->where('company_id', $auth->company_id)->get();
        } elseif ($auth->user_type === 'B' && is_null($auth->company_id)) {
            $vehicles = collect();
            $drivers = collect();
        } else {
            $vehicles = VehicleModel::whereRaw('in_service IS TRUE')->get();
            $drivers = User::where('user_type', 'D')->get();
        }
        $fine_types = Fine::getFineTypes();
        
        return view('fines.create', compact('vehicles', 'drivers', 'fine_types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fine_type' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'admin_fee' => 'nullable|numeric|min:0',
            'discount_window_days' => 'nullable|integer|min:0',
            'escalation_days' => 'nullable|integer|min:0',
            'escalation_multiplier' => 'nullable|numeric|min:1',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,notified,paid,disputed,escalated',
            'date_logged' => 'required|date',
            'date_issued' => 'nullable|date',
            'evidence_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'notes' => 'nullable|string',
            'contravention_code' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Handle file upload
        if ($request->hasFile('evidence_file')) {
            $file = $request->file('evidence_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('fines/evidence', $filename, 'public');
            $data['evidence_file'] = $path;
        }

        // Auto-populate vehicle registration and driver from selected vehicle
        if ($data['vehicle_id']) {
            $vehicle = VehicleModel::find($data['vehicle_id']);
            if ($vehicle) {
                $data['vehicle_reg'] = $vehicle->license_plate;
                
                // Auto-populate driver from vehicle if not provided
                if (!$data['driver_id'] && $vehicle->drivers()->count() > 0) {
                    $data['driver_id'] = $vehicle->drivers()->first()->id;
                }
            }
        }

        // Calculate total amount
        $data['total_amount'] = $data['price'] + ($data['admin_fee'] ?? 0);
        
        // Calculate discount amount (50% by default)
        if ($data['discount_window_days']) {
            $data['discount_amount'] = $data['total_amount'] * 0.5;
        }

        // Calculate due date and escalation date
        if ($data['escalation_days']) {
            $date_logged = Carbon::parse($data['date_logged']);
            $data['due_date'] = $date_logged->copy()->addDays($data['escalation_days']);
            $data['escalation_date'] = $date_logged->copy()->addDays($data['escalation_days']);
        }

        // Set default escalation multiplier if not provided
        if (!$data['escalation_multiplier']) {
            $data['escalation_multiplier'] = 1.5;
        }

        $fine = Fine::create($data);

        // Send fine notification email to driver
        if ($fine->driver_id) {
            try {
                $driver = User::find($fine->driver_id);
                if ($driver && $driver->email) {
                    $emailService = new \App\Utils\ResendEmailService();
                    $emailResult = $emailService->sendFineNotificationEmail(
                        $driver->email,
                        $driver->name,
                        $fine->fine_type,
                        $fine->total_amount,
                        $fine->due_date ? $fine->due_date->format('Y-m-d') : 'Not specified'
                    );
                    
                    if ($emailResult['success']) {
                        \Log::info('Fine notification email sent successfully', [
                            'driver_email' => $driver->email,
                            'driver_name' => $driver->name,
                            'fine_type' => $fine->fine_type,
                            'fine_amount' => $fine->total_amount,
                            'resend_id' => $emailResult['resend_id'] ?? null
                        ]);
                    } else {
                        \Log::warning('Failed to send fine notification email', [
                            'driver_email' => $driver->email,
                            'driver_name' => $driver->name,
                            'fine_type' => $fine->fine_type,
                            'fine_amount' => $fine->total_amount,
                            'error' => $emailResult['message']
                        ]);
                    }
                }
            } catch (\Exception $emailException) {
                \Log::error('Exception while sending fine notification email', [
                    'fine_id' => $fine->id,
                    'driver_id' => $fine->driver_id,
                    'error' => $emailException->getMessage()
                ]);
                // Don't fail the fine creation process if email fails
            }
        }

        return redirect()->route('fines.index')
            ->with('success', 'Fine created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $fine = Fine::with(['vehicle', 'driver'])->findOrFail($id);
        return view('fines.show', compact('fine'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $fine = Fine::findOrFail($id);
        $auth = \Auth::user();
        if (in_array($auth->user_type, ['S','O']) && !is_null($auth->company_id)) {
            $vehicles = VehicleModel::whereRaw('in_service IS TRUE')->where('company_id', $auth->company_id)->get();
            $drivers = User::where('user_type', 'D')->where('company_id', $auth->company_id)->get();
        } elseif ($auth->user_type === 'B' && is_null($auth->company_id)) {
            $vehicles = collect();
            $drivers = collect();
        } else {
            $vehicles = VehicleModel::whereRaw('in_service IS TRUE')->get();
            $drivers = User::where('user_type', 'D')->get();
        }
        $fine_types = Fine::getFineTypes();
        
        return view('fines.edit', compact('fine', 'vehicles', 'drivers', 'fine_types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $fine = Fine::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'fine_type' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'admin_fee' => 'nullable|numeric|min:0',
            'discount_window_days' => 'nullable|integer|min:0',
            'escalation_days' => 'nullable|integer|min:0',
            'escalation_multiplier' => 'nullable|numeric|min:1',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,notified,paid,disputed,escalated',
            'date_logged' => 'required|date',
            'date_issued' => 'nullable|date',
            'evidence_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'notes' => 'nullable|string',
            'contravention_code' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Handle file upload
        if ($request->hasFile('evidence_file')) {
            // Delete old file if exists
            if ($fine->evidence_file && Storage::disk('public')->exists($fine->evidence_file)) {
                Storage::disk('public')->delete($fine->evidence_file);
            }
            
            $file = $request->file('evidence_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('fines/evidence', $filename, 'public');
            $data['evidence_file'] = $path;
        }

        // Auto-populate vehicle registration and driver from selected vehicle
        if ($data['vehicle_id']) {
            $vehicle = VehicleModel::find($data['vehicle_id']);
            if ($vehicle) {
                $data['vehicle_reg'] = $vehicle->license_plate;
                
                // Auto-populate driver from vehicle if not provided
                if (!$data['driver_id'] && $vehicle->drivers()->count() > 0) {
                    $data['driver_id'] = $vehicle->drivers()->first()->id;
                }
            }
        }

        // Calculate total amount
        $data['total_amount'] = $data['price'] + ($data['admin_fee'] ?? 0);
        
        // Calculate discount amount (50% by default)
        if ($data['discount_window_days']) {
            $data['discount_amount'] = $data['total_amount'] * 0.5;
        }

        // Calculate due date and escalation date
        if ($data['escalation_days']) {
            $date_logged = Carbon::parse($data['date_logged']);
            $data['due_date'] = $date_logged->copy()->addDays($data['escalation_days']);
            $data['escalation_date'] = $date_logged->copy()->addDays($data['escalation_days']);
        }

        // Set default escalation multiplier if not provided
        if (!$data['escalation_multiplier']) {
            $data['escalation_multiplier'] = 1.5;
        }

        $fine->update($data);

        return redirect()->route('fines.index')
            ->with('success', 'Fine updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $fine = Fine::findOrFail($id);
        
        // Delete evidence file if exists
        if ($fine->evidence_file && Storage::disk('public')->exists($fine->evidence_file)) {
            Storage::disk('public')->delete($fine->evidence_file);
        }
        
        $fine->delete();

        // Return JSON response for AJAX requests
        if (request()->ajax()) {
            return response()->json(['success' => 'Fine deleted successfully']);
        }

        return redirect()->route('fines.index')
            ->with('success', 'Fine deleted successfully.');
    }

    /**
     * Bulk delete fines
     */
    public function bulk_delete(Request $request)
    {
        $ids = $request->get('ids');
        
        if (empty($ids)) {
            return response()->json(['error' => 'No fines selected'], 400);
        }

        $fines = Fine::whereIn('id', $ids)->get();
        
        foreach ($fines as $fine) {
            // Delete evidence file if exists
            if ($fine->evidence_file && Storage::disk('public')->exists($fine->evidence_file)) {
                Storage::disk('public')->delete($fine->evidence_file);
            }
            $fine->delete();
        }

        return response()->json(['success' => 'Selected fines deleted successfully']);
    }

    /**
     * Update fine status
     */
    public function updateStatus(Request $request, $id)
    {
        $fine = Fine::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,notified,paid,disputed,escalated',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid status'], 400);
        }

        $fine->update(['status' => $request->status]);

        return response()->json(['success' => 'Status updated successfully']);
    }

    /**
     * Get button class for fine status
     */
    private function getButtonClassForStatus($status)
    {
        switch ($status) {
            case 'pending':
                return 'btn-outline-warning';
            case 'notified':
                return 'btn-outline-info';
            case 'disputed':
                return 'btn-outline-danger';
            case 'paid':
                return 'btn-outline-success';
            case 'escalated':
                return 'btn-outline-dark';
            default:
                return 'btn-outline-secondary';
        }
    }

    /**
     * Get driver by vehicle ID
     */
    public function getDriverByVehicle(Request $request)
    {
        $vehicle_id = $request->get('vehicle_id');
        
        if (!$vehicle_id) {
            return response()->json(['driver' => null]);
        }

        $vehicle = VehicleModel::find($vehicle_id);
        
        if (!$vehicle) {
            return response()->json(['driver' => null]);
        }

        // Get assigned driver ID from vehicle meta
        $assignedDriverId = $vehicle->getMeta('assign_driver_id');
        
        if ($assignedDriverId) {
            $driver = User::find($assignedDriverId);
            if ($driver) {
                return response()->json([
                    'driver' => [
                        'id' => $driver->id,
                        'name' => $driver->name,
                        'email' => $driver->email
                    ]
                ]);
            }
        }
        
        return response()->json(['driver' => null]);
    }

    /**
     * Get contravention codes by fine type
     */
    public function getContraventionCodes(Request $request)
    {
        $fine_type = $request->get('fine_type');
        $fine_types = Fine::getFineTypes();
        
        $codes = $fine_types[$fine_type] ?? [];
        
        return response()->json(['codes' => $codes]);
    }

    /**
     * Get fine details for AJAX display
     */
    public function getDetails($id)
    {
        $fine = Fine::with(['vehicle', 'driver'])->findOrFail($id);
        
        return response()->json([
            'id' => $fine->id,
            'fine_type_title' => $fine->fine_type_title,
            'contravention_code' => $fine->contravention_code ?? 'N/A',
            'reference_number' => $fine->reference_number ?? 'N/A',
            'vehicle_reg' => $fine->vehicle_reg,
            'vehicle_details' => $fine->vehicle ? 
                $fine->vehicle->make_name . ' ' . $fine->vehicle->model_name . ' (' . $fine->vehicle->license_plate . ')' : 
                'N/A',
            'driver_details' => $fine->driver ? 
                $fine->driver->name . ' (' . $fine->driver->email . ')' : 
                'N/A',
            'date_logged' => $fine->date_logged ? $fine->date_logged->format('d/m/Y H:i') : 'N/A',
            'due_date' => $fine->due_date ? $fine->due_date->format('d/m/Y') : 'N/A',
            'escalation_date' => $fine->escalation_date ? $fine->escalation_date->format('d/m/Y') : 'N/A',
            'price' => '£' . number_format($fine->price, 2),
            'admin_fee' => '£' . number_format($fine->admin_fee, 2),
            'total_amount' => '£' . number_format($fine->total_amount, 2),
            'current_amount' => '£' . number_format($fine->current_amount, 2),
            'discount_amount' => $fine->discount_amount ? '£' . number_format($fine->discount_amount, 2) : 'N/A',
            'escalation_multiplier' => $fine->escalation_multiplier,
            'status' => ucfirst($fine->status),
            'notes' => $fine->notes ?? 'No notes available',
            'is_escalated' => $fine->is_escalated,
            'is_in_discount_window' => $fine->is_in_discount_window,
            'is_overdue' => $fine->due_date && $fine->due_date < now() && !in_array($fine->status, ['paid', 'disputed'])
        ]);
    }

    /**
     * Fetch data for DataTables
     */
    public function fetch_data(Request $request)
    {
        $auth = \Auth::user();
        $query = Fine::with(['vehicle', 'driver']);
        // Company scoping via vehicle company
        if (in_array($auth->user_type, ['S','O']) && !is_null($auth->company_id)) {
            $vehicleIds = VehicleModel::where('company_id', $auth->company_id)->pluck('id');
            $query->whereIn('vehicle_id', $vehicleIds);
        } elseif ($auth->user_type === 'B' && is_null($auth->company_id)) {
            $query->whereRaw('1=0');
        }

        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        } else {
            // By default, exclude paid fines unless specifically requested
            $query->where('status', '!=', 'paid');
        }

        if ($request->has('fine_type') && $request->fine_type) {
            $query->where('fine_type', $request->fine_type);
        }

        if ($request->has('vehicle') && $request->vehicle) {
            $query->where('vehicle_reg', 'LIKE', '%' . $request->vehicle . '%');
        }

        if ($request->has('driver_id') && $request->driver_id) {
            $query->where('driver_id', $request->driver_id);
        }

        if ($request->has('vehicle_id') && $request->vehicle_id) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('date_logged', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('date_logged', '<=', $request->date_to);
        }

        return DataTables::of($query)
            ->addColumn('DT_RowId', function ($fine) {
                return 'fine-row-' . $fine->id;
            })
            ->addColumn('fine_type_title', function ($fine) {
                return $fine->fine_type_title;
            })
            ->addColumn('vehicle_info', function ($fine) {
                if ($fine->vehicle) {
                    return $fine->vehicle->make_name . ' ' . $fine->vehicle->model_name . ' (' . $fine->vehicle->license_plate . ')';
                }
                return $fine->vehicle_reg;
            })
            ->addColumn('driver_info', function ($fine) {
                if ($fine->driver) {
                    return $fine->driver->name . ' (' . $fine->driver->email . ')';
                }
                return 'N/A';
            })
            ->addColumn('current_amount', function ($fine) {
                return '£' . number_format($fine->current_amount, 2);
            })
            ->addColumn('status_badge', function ($fine) {
                $status = $fine->status;
                $badgeClass = $fine->status_badge;
                $buttonClass = $this->getButtonClassForStatus($status);
                
                return '<div class="status-container" data-fine-id="' . $fine->id . '">
                    <div class="status-display">
                        <button class="btn btn-sm ' . $buttonClass . ' custom-dropdown-toggle" type="button">
                            <span class="badge ' . $badgeClass . '">' . ucfirst($status) . '</span> 
                            <span class="dropdown-arrow">▼</span>
                        </button>
                    </div>
                    <div class="custom-dropdown-menu" style="display: none;">
                        <a class="dropdown-item status-change" href="#" data-status="pending" data-fine-id="' . $fine->id . '">
                            <span class="badge badge-warning">Pending</span>
                        </a>
                        <a class="dropdown-item status-change" href="#" data-status="notified" data-fine-id="' . $fine->id . '">
                            <span class="badge badge-info">Notified</span>
                        </a>
                        <a class="dropdown-item status-change" href="#" data-status="disputed" data-fine-id="' . $fine->id . '">
                            <span class="badge badge-danger">Disputed</span>
                        </a>
                        <a class="dropdown-item status-change" href="#" data-status="paid" data-fine-id="' . $fine->id . '">
                            <span class="badge badge-success">Paid</span>
                        </a>
                        <a class="dropdown-item status-change" href="#" data-status="escalated" data-fine-id="' . $fine->id . '">
                            <span class="badge badge-dark">Escalated</span>
                        </a>
                    </div>
                </div>';
            })
            ->addColumn('due_date_formatted', function ($fine) {
                if ($fine->due_date) {
                    $isOverdue = $fine->due_date < Carbon::now() && !in_array($fine->status, ['paid', 'disputed']);
                    $class = $isOverdue ? 'text-danger' : '';
                    return '<span class="' . $class . '">' . $fine->due_date->format('d/m/Y') . '</span>';
                }
                return 'N/A';
            })
            ->addColumn('date_logged', function ($fine) {
                return $fine->date_logged ? $fine->date_logged->format('d/m/Y') : 'N/A';
            })
            ->addColumn('actions', function ($fine) {
                $actions = '<div class="btn-group" role="group">';
                $actions .= '<button type="button" class="btn btn-sm btn-info" onclick="toggleFineDetails(' . $fine->id . ')" title="View Details" id="details-btn-' . $fine->id . '"><i class="fas fa-eye"></i></button>';
                $actions .= '<a href="' . route('fines.edit', $fine->id) . '" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>';
                $actions .= '<button type="button" class="btn btn-sm btn-danger" onclick="deleteFine(' . $fine->id . ')" title="Delete"><i class="fas fa-trash"></i></button>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['status_badge', 'due_date_formatted', 'actions'])
            ->make(true);
    }

}
