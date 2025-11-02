<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Model\Company;
use App\Model\User;
use App\Model\VehicleModel;
use App\Model\Bookings;
use App\Services\StripeSubscriptionService;
use App\Services\CompanyPaymentEmailService;
use Illuminate\Support\Facades\Log;

class CompaniesController extends Controller {
    public function index() {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        // Aggregate counts per company
        $companies = Company::select('companies.*')
            ->withCount(['users as accounts_count' => function($q){}])
            ->withCount(['vehicles as vehicles_count' => function($q){}])
            ->withCount(['bookings as bookings_count' => function($q){}])
            ->orderBy('name')
            ->get();

        // Include a pseudo row for users without company (company_id null)
        $uncountedUsers = User::whereNull('company_id')->count();

        return view('admin.yamz.companies-index', compact('companies', 'uncountedUsers'));
    }

    public function show($companyId) {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $company = Company::findOrFail($companyId);

        $supers = User::where('user_type','S')->where('company_id',$company->id)->get();
        $offices = User::where('user_type','O')->where('company_id',$company->id)->get();
        $drivers = User::where('user_type','D')->where('company_id',$company->id)->get();

        $vehiclesCount = VehicleModel::where('company_id',$company->id)->count();
        $vehicles = VehicleModel::where('company_id',$company->id)->orderBy('id','desc')->limit(50)->get();

        return view('admin.yamz.company-show', compact('company','supers','offices','drivers','vehicles','vehiclesCount'));
    }

    public function create() {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        return view('admin.yamz.company-create');
    }

    public function store(Request $request) {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:companies,name',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000'
        ]);

        $company = Company::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'description' => $request->description,
            'is_active' => true,
        ]);

        // Create Stripe customer if company has super admin
        try {
            $superAdmin = User::where('company_id', $company->id)
                ->where('user_type', 'S')
                ->first();

            if ($superAdmin) {
                $stripeService = new StripeSubscriptionService();
                $stripeService->createCustomer($company);
            }
        } catch (\Exception $e) {
            // Don't fail company creation if Stripe fails
            Log::warning('Failed to create Stripe customer during company creation', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('admin.yamz.companies')->with('success', 'Company created successfully!');
    }

    public function edit($companyId) {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $company = Company::findOrFail($companyId);
        return view('admin.yamz.company-edit', compact('company'));
    }

    public function update(Request $request, $companyId) {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $company = Company::findOrFail($companyId);

        $request->validate([
            'name' => 'required|string|max:255|unique:companies,name,' . $company->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean'
        ]);

        $company->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'description' => $request->description,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('admin.yamz.companies')->with('success', 'Company updated successfully!');
    }

    public function destroy($companyId) {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $company = Company::findOrFail($companyId);
        
        // Check if company has users
        if ($company->users()->count() > 0) {
            return redirect()->route('admin.yamz.companies')->with('error', 'Cannot delete company with existing users. Please reassign users first.');
        }

        $company->delete();

        return redirect()->route('admin.yamz.companies')->with('success', 'Company deleted successfully!');
    }

    /**
     * Send payment setup email to company super admin
     */
    public function sendPaymentSetupEmail($companyId)
    {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $company = Company::findOrFail($companyId);

        // Find super admin for this company
        $superAdmin = User::where('company_id', $company->id)
            ->where('user_type', 'S')
            ->first();

        if (!$superAdmin) {
            return redirect()->route('admin.yamz.companies.show', $companyId)
                ->with('error', 'No super admin found for this company.');
        }

        try {
            $emailService = new CompanyPaymentEmailService(new StripeSubscriptionService());
            $sent = $emailService->sendPaymentSetupEmail($company, $superAdmin);

            if ($sent) {
                return redirect()->route('admin.yamz.companies.show', $companyId)
                    ->with('success', 'Payment setup email sent successfully to ' . $superAdmin->email . '!');
            } else {
                return redirect()->route('admin.yamz.companies.show', $companyId)
                    ->with('error', 'Failed to send payment setup email. Please check logs.');
            }
        } catch (\Exception $e) {
            Log::error('Error sending payment setup email', [
                'company_id' => $companyId,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('admin.yamz.companies.show', $companyId)
                ->with('error', 'An error occurred while sending the email. Please try again.');
        }
    }
}
