
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\DriverOnboarding;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class OnboardingController extends Controller
{
    public function index()
    {
        return view('onboarding.index');
    }

    public function data()
    {
        $onboarding = DriverOnboarding::orderBy('created_at', 'desc');
        
        return DataTables::of($onboarding)
            ->addColumn('actions', function ($row) {
                $actions = '<div class="btn-group">';
                $actions .= '<button class="btn btn-info btn-sm view-docs" data-id="'.$row->id.'"><i class="fa fa-eye"></i> View Docs</button>';
                if ($row->status == 'Submitted') {
                    $actions .= '<button class="btn btn-success btn-sm approve-driver" data-id="'.$row->id.'"><i class="fa fa-check"></i> Approve</button>';
                    $actions .= '<button class="btn btn-warning btn-sm reject-driver" data-id="'.$row->id.'"><i class="fa fa-times"></i> Reject</button>';
                }
                $actions .= '<button class="btn btn-danger btn-sm delete-driver" data-id="'.$row->id.'"><i class="fa fa-trash"></i> Delete</button>';
                $actions .= '</div>';
                return $actions;
            })
            ->addColumn('documents', function ($row) {
                $docs = $row->documents;
                return implode(', ', $docs);
            })
            ->addColumn('status_badge', function ($row) {
                $class = $row->status == 'Submitted' ? 'primary' : ($row->status == 'Approved' ? 'success' : 'danger');
                return '<span class="badge badge-'.$class.'">'.$row->status.'</span>';
            })
            ->rawColumns(['actions', 'status_badge'])
            ->make(true);
    }

    public function approve(Request $request, $id)
    {
        $onboarding = DriverOnboarding::findOrFail($id);
        
        // Create driver in main drivers table
        $user = new User();
        $user->name = $onboarding->name;
        $user->email = $onboarding->email;
        $user->phone = $onboarding->phone;
        $user->password = Hash::make('password123'); // Default password
        $user->user_type = 'D'; // Driver
        $user->license_number = $onboarding->license_number;
        
        if ($onboarding->drivers_license_file) {
            $user->license_image = $onboarding->drivers_license_file;
        }
        
        $user->save();
        
        // Update onboarding status
        $onboarding->status = 'Approved';
        $onboarding->save();
        
        return response()->json(['success' => true, 'message' => 'Driver approved and moved to main system']);
    }

    public function reject(Request $request, $id)
    {
        $onboarding = DriverOnboarding::findOrFail($id);
        $onboarding->status = 'Rejected';
        $onboarding->save();
        
        return response()->json(['success' => true, 'message' => 'Driver application rejected']);
    }

    public function delete($id)
    {
        $onboarding = DriverOnboarding::findOrFail($id);
        
        // Delete uploaded files
        if ($onboarding->drivers_license_file) {
            Storage::delete($onboarding->drivers_license_file);
        }
        if ($onboarding->pco_license_file) {
            Storage::delete($onboarding->pco_license_file);
        }
        if ($onboarding->insurance_file) {
            Storage::delete($onboarding->insurance_file);
        }
        
        $onboarding->delete();
        
        return response()->json(['success' => true, 'message' => 'Driver application deleted']);
    }

    public function viewDocuments($id)
    {
        $onboarding = DriverOnboarding::findOrFail($id);
        return response()->json($onboarding);
    }

    // Public form methods
    public function showForm($link)
    {
        $onboarding = DriverOnboarding::where('unique_link', $link)->first();
        
        if (!$onboarding || $onboarding->status != 'Submitted') {
            abort(404);
        }
        
        return view('onboarding.form', compact('onboarding'));
    }

    public function submitForm(Request $request, $link)
    {
        $onboarding = DriverOnboarding::where('unique_link', $link)->first();
        
        if (!$onboarding) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'license_number' => 'required|string|max:50',
            'drivers_license' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pco_license' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'insurance' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Handle file uploads
        $driversLicense = $request->file('drivers_license')->store('onboarding/drivers_license');
        $pcoLicense = $request->file('pco_license')->store('onboarding/pco_license');
        $insurance = $request->file('insurance')->store('onboarding/insurance');

        // Update onboarding record
        $onboarding->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'license_number' => $request->license_number,
            'drivers_license_file' => $driversLicense,
            'pco_license_file' => $pcoLicense,
            'insurance_file' => $insurance,
            'submitted_at' => now(),
        ]);

        return view('onboarding.success');
    }

    public function getCount()
    {
        return DriverOnboarding::where('status', 'Submitted')->count();
    }
}
