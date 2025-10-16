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
            'is_active' => 1,
        ]);

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
}
