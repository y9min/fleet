<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class ProfileController extends Controller
{
    /**
     * Display the user's profile
     */
    public function index()
    {
        $user = Auth::user();
        $company = null;
        
        if ($user->company_id) {
            $company = Company::find($user->company_id);
        }
        
        return view('admin.profile.index', compact('user', 'company'));
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check current password if changing password
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }
        
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Display company settings (for Boss Admin and Super Admin)
     */
    public function company()
    {
        $user = Auth::user();
        
        if (!in_array($user->user_type, ['B', 'S'])) {
            return redirect()->route('admin.profile')->with('error', 'Access denied.');
        }

        $company = null;
        if ($user->company_id) {
            $company = Company::find($user->company_id);
        }

        return view('admin.profile.company', compact('user', 'company'));
    }

    /**
     * Update company settings
     */
    public function updateCompany(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->user_type, ['B', 'S'])) {
            return redirect()->route('admin.profile')->with('error', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($user->company_id) {
            $company = Company::find($user->company_id);
            $company->update($request->only(['name', 'description', 'email', 'phone', 'address']));
        } else {
            // Create new company for Boss Admin
            $company = Company::create($request->only(['name', 'description', 'email', 'phone', 'address']));
            $user->company_id = $company->id;
            $user->save();
        }

        return back()->with('success', 'Company settings updated successfully.');
    }

    /**
     * Display office admins management
     */
    public function officeAdmins()
    {
        $user = Auth::user();
        
        if (!in_array($user->user_type, ['B', 'S'])) {
            return redirect()->route('admin.profile')->with('error', 'Access denied.');
        }

        $officeAdmins = User::where('user_type', 'O')
            ->when($user->user_type == 'S', function($query) use ($user) {
                return $query->where('company_id', $user->company_id);
            })
            ->with('company')
            ->get();

        return view('admin.profile.office-admins', compact('user', 'officeAdmins'));
    }

    /**
     * Create new office admin
     */
    public function createOfficeAdmin(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->user_type, ['B', 'S'])) {
            return redirect()->route('admin.profile')->with('error', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $officeAdmin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'O',
            'company_id' => $user->company_id,
        ]);

        // Assign Office Admin role
        $role = Role::where('name', 'Admin')->first();
        if ($role) {
            $officeAdmin->assignRole($role);
        }

        return back()->with('success', 'Office Admin created successfully.');
    }

    /**
     * Update office admin
     */
    public function updateOfficeAdmin(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!in_array($user->user_type, ['B', 'S'])) {
            return redirect()->route('admin.profile')->with('error', 'Access denied.');
        }

        $officeAdmin = User::where('user_type', 'O')
            ->when($user->user_type == 'S', function($query) use ($user) {
                return $query->where('company_id', $user->company_id);
            })
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $officeAdmin->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $officeAdmin->name = $request->name;
        $officeAdmin->email = $request->email;
        
        if ($request->filled('password')) {
            $officeAdmin->password = Hash::make($request->password);
        }
        
        $officeAdmin->save();

        return back()->with('success', 'Office Admin updated successfully.');
    }

    /**
     * Delete office admin
     */
    public function deleteOfficeAdmin($id)
    {
        $user = Auth::user();
        
        if (!in_array($user->user_type, ['B', 'S'])) {
            return redirect()->route('admin.profile')->with('error', 'Access denied.');
        }

        $officeAdmin = User::where('user_type', 'O')
            ->when($user->user_type == 'S', function($query) use ($user) {
                return $query->where('company_id', $user->company_id);
            })
            ->findOrFail($id);

        $officeAdmin->delete();

        return back()->with('success', 'Office Admin deleted successfully.');
    }

    /**
     * Yamz-only: View all users across all companies
     */
    public function allUsers(Request $request)
    {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $query = User::with('company');
        
        // Filter by user type if provided
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }
        
        $allUsers = $query->orderBy('created_at', 'desc')->get();
        
        // Get selected user type for the filter dropdown
        $selectedUserType = $request->user_type;
        
        return view('admin.yamz.all-users', compact('allUsers', 'selectedUserType'));
    }

    /**
     * Yamz-only: Create new user form
     */
    public function createUser()
    {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $companies = Company::where('is_active', 1)->orderBy('name')->get();
        $roles = Role::whereIn('name', ['Super Admin', 'Admin'])->get();
        
        return view('admin.yamz.user-create', compact('companies', 'roles'));
    }

    /**
     * Yamz-only: Store new user
     */
    public function storeUser(Request $request)
    {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'user_type' => 'required|in:B,S,O,D,C',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        // Create the user with default password
        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password'), // Default password
            'user_type' => $request->user_type,
            'company_id' => $request->company_id,
            'is_active' => true,
            'api_token' => \Illuminate\Support\Str::random(60),
        ]);

        // Assign role based on user type
        if ($request->user_type === 'B') {
            $role = Role::where('name', 'Super Admin')->first();
            if ($role) {
                $newUser->assignRole($role);
            }
        } elseif ($request->user_type === 'S') {
            $role = Role::where('name', 'Super Admin')->first();
            if ($role) {
                $newUser->assignRole($role);
            }
        } elseif ($request->user_type === 'O') {
            $role = Role::where('name', 'Admin')->first();
            if ($role) {
                $newUser->assignRole($role);
            }
        }

        return redirect()->route('admin.yamz.all-users')->with('success', 'User created successfully! Default password is "password".');
    }

    /**
     * Yamz-only: Edit user form
     */
    public function editUser($userId)
    {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $editUser = User::findOrFail($userId);
        $companies = Company::orderBy('name')->get();
        
        return view('admin.yamz.user-edit', compact('editUser', 'companies'));
    }

    /**
     * Yamz-only: Update user
     */
    public function updateUser(Request $request, $userId)
    {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $editUser = User::findOrFail($userId);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $editUser->id,
            'user_type' => 'required|in:B,S,O,D,C',
            'company_id' => 'nullable|exists:companies,id',
            'is_active' => 'required|boolean',
        ]);

        $editUser->update([
            'name' => $request->name,
            'email' => $request->email,
            'user_type' => $request->user_type,
            'company_id' => $request->company_id,
            'is_active' => $request->is_active,
        ]);

        // Update roles if user type changed
        $editUser->roles()->detach();
        if ($request->user_type === 'B') {
            $role = Role::where('name', 'Super Admin')->first();
            if ($role) {
                $editUser->assignRole($role);
            }
        } elseif ($request->user_type === 'S') {
            $role = Role::where('name', 'Super Admin')->first();
            if ($role) {
                $editUser->assignRole($role);
            }
        } elseif ($request->user_type === 'O') {
            $role = Role::where('name', 'Admin')->first();
            if ($role) {
                $editUser->assignRole($role);
            }
        }

        return redirect()->route('admin.yamz.all-users')->with('success', 'User updated successfully!');
    }

    /**
     * Yamz-only: Delete user
     */
    public function destroyUser($userId)
    {
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $deleteUser = User::findOrFail($userId);
        
        // Prevent deleting self
        if ($deleteUser->id === $user->id) {
            return redirect()->route('admin.yamz.all-users')->with('error', 'Cannot delete your own account.');
        }

        // Prevent deleting master account
        if ($deleteUser->email === 'master@admin.com') {
            return redirect()->route('admin.yamz.all-users')->with('error', 'Cannot delete the master account.');
        }

        $deleteUser->delete();

        return redirect()->route('admin.yamz.all-users')->with('success', 'User deleted successfully!');
    }
}
