<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\VehicleModel;
use App\Model\Bookings;
use App\Model\Expense;
use App\Model\IncomeModel;
use App\Model\DriverVehicleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class DriverDashboardController extends Controller
{
    /**
     * Show the driver dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if password change is required
        $forcePasswordChange = session('force_password_change', false);
        $passwordChangeMessage = session('password_change_message', '');
        
        // Get assigned vehicle
        $assignedVehicle = null;
        $vehicleAssignment = DriverVehicleModel::where('driver_id', $user->id)->first();
        if ($vehicleAssignment) {
            $assignedVehicle = VehicleModel::find($vehicleAssignment->vehicle_id);
        }
        
        // Get driver's bookings
        $bookings = Bookings::where('driver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get upcoming payments (if any booking payments are due)
        $upcomingPayments = collect();
        foreach ($bookings as $booking) {
            if ($booking->payment_status != 'paid' && $booking->total_amount > 0) {
                $upcomingPayments->push([
                    'booking_id' => $booking->id,
                    'amount' => $booking->total_amount,
                    'due_date' => $booking->created_at->addDays(30)->format('Y-m-d'),
                    'description' => 'Payment for booking #' . $booking->id
                ]);
            }
        }
        
        // Get service reminders from actual vehicle data
        $serviceReminders = collect();
        if ($assignedVehicle) {
            // Get MOT expiry date from vehicle metadata
            $motExpiryDate = $assignedVehicle->getMeta('mot_expiry_date') ?: $assignedVehicle->getMeta('exp_date') ?: $assignedVehicle->lic_exp_date;
            
            if ($motExpiryDate) {
                $motDate = Carbon::parse($motExpiryDate);
                $serviceReminders->push([
                    'type' => 'MOT',
                    'due_date' => $motDate->format('d/m/Y'),
                    'description' => 'MOT due for ' . $assignedVehicle->make_name . ' ' . $assignedVehicle->model_name
                ]);
            }
            
            // Get service reminders from database (with error handling)
            try {
                $serviceRemindersFromDB = \App\Model\ServiceReminderModel::where('vehicle_id', $assignedVehicle->id)
                    ->with('services')
                    ->get();
                    
                foreach ($serviceRemindersFromDB as $reminder) {
                    if ($reminder->services) {
                        $lastDate = Carbon::parse($reminder->last_date);
                        $interval = substr($reminder->services->overdue_unit, 0, -3);
                        $nextServiceDate = $lastDate->add($reminder->services->overdue_time, $interval);
                        
                        $serviceReminders->push([
                            'type' => $reminder->services->service_name,
                            'due_date' => $nextServiceDate->format('d/m/Y'),
                            'description' => $reminder->services->service_name . ' due for ' . $assignedVehicle->make_name . ' ' . $assignedVehicle->model_name
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // Log error but don't break login - service reminders are non-critical
                \Log::warning('Failed to load service reminders for driver dashboard', [
                    'driver_id' => $user->id,
                    'vehicle_id' => $assignedVehicle->id,
                    'error' => $e->getMessage()
                ]);
                // Continue with empty service reminders collection
            }
            
            // If no service reminders found, add a generic one
            if ($serviceReminders->isEmpty()) {
                $serviceReminders->push([
                    'type' => 'Service',
                    'due_date' => Carbon::now()->addMonths(1)->format('d/m/Y'),
                    'description' => 'Regular service due for ' . $assignedVehicle->make_name . ' ' . $assignedVehicle->model_name
                ]);
            }
        }
        
        // Get real fines associated with the driver
        $tickets = collect();
        $driverFines = \App\Fine::where('driver_id', $user->id)
            ->where('status', '!=', 'paid')
            ->orderBy('date_logged', 'desc')
            ->limit(5)
            ->get();
            
        foreach ($driverFines as $fine) {
            $tickets->push([
                'id' => $fine->id,
                'type' => $fine->fine_type_title, // Use the full descriptive title
                'amount' => $fine->total_amount,
                'date' => Carbon::parse($fine->date_logged)->format('d/m/Y'),
                'status' => ucfirst($fine->status),
                'description' => $fine->notes ?: $fine->fine_type_title,
                'reference' => $fine->reference_number,
                'due_date' => $fine->due_date ? Carbon::parse($fine->due_date)->format('d/m/Y') : null
            ]);
        }
        
        $data = [
            'user' => $user,
            'assignedVehicle' => $assignedVehicle,
            'bookings' => $bookings,
            'upcomingPayments' => $upcomingPayments,
            'serviceReminders' => $serviceReminders,
            'tickets' => $tickets,
            'forcePasswordChange' => $forcePasswordChange,
            'passwordChangeMessage' => $passwordChangeMessage
        ];
        
        return view('driver_dashboard.dashboard', $data);
    }
    
    /**
     * Get driver info for AJAX requests
     */
    public function getinfo()
    {
        $user = Auth::user();
        
        // Get assigned vehicle
        $assignedVehicle = null;
        $vehicleAssignment = DriverVehicleModel::where('driver_id', $user->id)->first();
        if ($vehicleAssignment) {
            $assignedVehicle = VehicleModel::find($vehicleAssignment->vehicle_id);
            
            // Load vehicle metadata for dynamic updates
            if ($assignedVehicle) {
                $assignedVehicle->load('metas');
                $assignedVehicle->meta_data = $assignedVehicle->metas->pluck('value', 'key')->toArray();
            }
        }
        
        // Get service reminders for AJAX updates
        $serviceReminders = collect();
        if ($assignedVehicle) {
            // Get MOT expiry date from vehicle metadata
            $motExpiryDate = $assignedVehicle->getMeta('mot_expiry_date') ?: $assignedVehicle->getMeta('exp_date') ?: $assignedVehicle->lic_exp_date;
            
            if ($motExpiryDate) {
                $motDate = Carbon::parse($motExpiryDate);
                $serviceReminders->push([
                    'type' => 'MOT',
                    'due_date' => $motDate->format('d/m/Y'),
                    'description' => 'MOT due for ' . $assignedVehicle->make_name . ' ' . $assignedVehicle->model_name
                ]);
            }
            
            // Get service reminders from database (with error handling)
            try {
                $serviceRemindersFromDB = \App\Model\ServiceReminderModel::where('vehicle_id', $assignedVehicle->id)
                    ->with('services')
                    ->get();
                    
                foreach ($serviceRemindersFromDB as $reminder) {
                    if ($reminder->services) {
                        $lastDate = Carbon::parse($reminder->last_date);
                        $interval = substr($reminder->services->overdue_unit, 0, -3);
                        $nextServiceDate = $lastDate->add($reminder->services->overdue_time, $interval);
                        
                        $serviceReminders->push([
                            'type' => $reminder->services->service_name,
                            'due_date' => $nextServiceDate->format('d/m/Y'),
                            'description' => $reminder->services->service_name . ' due for ' . $assignedVehicle->make_name . ' ' . $assignedVehicle->model_name
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // Log error but don't break AJAX request - service reminders are non-critical
                \Log::warning('Failed to load service reminders for driver AJAX request', [
                    'driver_id' => $user->id,
                    'vehicle_id' => $assignedVehicle->id,
                    'error' => $e->getMessage()
                ]);
                // Continue with empty service reminders collection
            }
        }
        
        // Get real fines for AJAX updates
        $tickets = collect();
        $driverFines = \App\Fine::where('driver_id', $user->id)
            ->where('status', '!=', 'paid')
            ->orderBy('date_logged', 'desc')
            ->limit(5)
            ->get();
            
        foreach ($driverFines as $fine) {
            $tickets->push([
                'id' => $fine->id,
                'type' => $fine->fine_type_title, // Use the full descriptive title
                'amount' => $fine->total_amount,
                'date' => Carbon::parse($fine->date_logged)->format('d/m/Y'),
                'status' => ucfirst($fine->status),
                'description' => $fine->notes ?: $fine->fine_type_title,
                'reference' => $fine->reference_number,
                'due_date' => $fine->due_date ? Carbon::parse($fine->due_date)->format('d/m/Y') : null
            ]);
        }
        
        return response()->json([
            'user' => $user,
            'assignedVehicle' => $assignedVehicle,
            'serviceReminders' => $serviceReminders,
            'tickets' => $tickets,
            'driver_meta' => $user->getMeta()
        ]);
    }
    
    /**
     * Show driver profile
     */
    public function profile()
    {
        $user = Auth::user();
        return view('driver_dashboard.profile', compact('user'));
    }
    
    /**
     * Update driver profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->setMeta([
            'mobno' => $request->phone,
            'address' => $request->address,
        ]);
        $user->save();
        
        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Show driver's bookings
     */
    public function bookings()
    {
        $user = Auth::user();
        $bookings = Bookings::where('driver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('driver_dashboard.bookings', compact('bookings'));
    }
    
    /**
     * Show booking details
     */
    public function bookingDetails($id)
    {
        $user = Auth::user();
        $booking = Bookings::where('id', $id)
            ->where('driver_id', $user->id)
            ->firstOrFail();
            
        return view('driver_dashboard.booking_details', compact('booking'));
    }
    
    /**
     * Show password change form
     */
    public function showChangePassword()
    {
        $user = Auth::user();
        return view('driver_dashboard.change_password', compact('user'));
    }
    
    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required'
        ], [
            'current_password.required' => 'Current password is required.',
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'New password must be at least 8 characters.',
            'new_password.confirmed' => 'New password confirmation does not match.',
            'new_password_confirmation.required' => 'Please confirm your new password.'
        ]);
        
        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        
        // Update password
        $user->password = Hash::make($request->new_password);
        $user->setMeta(['password_changed' => '1', 'password_changed_at' => now()]);
        $user->save();
        
        return redirect()->back()->with('success', 'Password updated successfully!');
    }

    /**
     * Change password from profile page (AJAX)
     */
    public function changePasswordFromProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required'
        ], [
            'current_password.required' => 'Current password is required.',
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'New password must be at least 8 characters.',
            'new_password.confirmed' => 'New password confirmation does not match.',
            'new_password_confirmation.required' => 'Please confirm your new password.'
        ]);
        
        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ], 400);
        }
        
        // Update password
        $user->password = Hash::make($request->new_password);
        $user->setMeta(['password_changed' => '1', 'password_changed_at' => now()]);
        $user->save();
        
        // Clear the password change prompt
        session()->forget('force_password_change');
        session()->forget('password_change_message');
        
        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully!'
        ]);
    }

    /**
     * Clear password change prompt (AJAX)
     */
    public function clearPasswordChangePrompt(Request $request)
    {
        session()->forget('force_password_change');
        session()->forget('password_change_message');
        
        return response()->json([
            'success' => true,
            'message' => 'Password change prompt cleared.'
        ]);
    }

    /**
     * Mark fine as paid (AJAX)
     */
    public function markFineAsPaid(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'fine_id' => 'required|integer|exists:fines,id'
        ]);
        
        $fine = \App\Fine::where('id', $request->fine_id)
            ->where('driver_id', $user->id)
            ->first();
            
        if (!$fine) {
            return response()->json([
                'success' => false,
                'message' => 'Fine not found or you do not have permission to modify it.'
            ], 404);
        }
        
        if ($fine->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'This fine is already marked as paid.'
            ], 400);
        }
        
        // Update fine status to paid
        $fine->status = 'paid';
        $fine->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Fine has been marked as paid successfully.',
            'fine_id' => $fine->id
        ]);
    }
}
