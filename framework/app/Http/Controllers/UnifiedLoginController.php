<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Session;
use Hyvikk;
use App\Traits\FirebasePassword;

class UnifiedLoginController extends Controller
{
    use FirebasePassword;

    /**
     * Show the unified login page
     */
    public function showLoginForm()
    {
        return view('unified_login');
    }

    /**
     * Handle unified login for all user types (Customer, Driver, Admin)
     */
    public function login(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'login_type' => 'required|in:customer,driver,admin'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('email', 'remember'));
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        // Find the user first
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()
                ->withErrors(['email' => 'No account found with this email address.'])
                ->withInput($request->only('email', 'remember'));
        }

        // Check password
        if (!Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['password' => 'The password you entered is incorrect.'])
                ->withInput($request->only('email', 'remember'));
        }

        // Determine expected user types based on login type
        $expectedUserTypes = $this->getExpectedUserTypes($request->login_type);
        
        // Check if user type matches the selected login type
        if (!in_array($user->user_type, $expectedUserTypes)) {
            $loginTypeText = ucfirst($request->login_type);
            $actualType = $this->getUserTypeText($user->user_type);
            
            return back()
                ->withErrors(['email' => "This account is registered as {$actualType}. Please select the correct login type or use the appropriate login option."])
                ->withInput($request->only('email', 'remember'));
        }

        // Additional checks for drivers
        if ($user->user_type == 'D') {
            // Check driver verification if enabled
            if (Hyvikk::get('driver_doc_verification') == 1 && $user->is_verified != '1') {
                return back()
                    ->withErrors(['email' => 'Your profile is not verified. Please contact the administrator.'])
                    ->withInput($request->only('email', 'remember'));
            }
        }

        // Attempt authentication
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Create static user for Firebase if needed (for admin/driver users)
            if (in_array($user->user_type, ['S', 'O', 'D'])) {
                $this->createStaticUser($user->email, $request->password);
            }

            // Redirect based on user type
            return $this->redirectAfterLogin($user);
        }

        return back()
            ->withErrors(['email' => 'Authentication failed. Please try again.'])
            ->withInput($request->only('email', 'remember'));
    }

    /**
     * Get expected user types for each login type
     */
    private function getExpectedUserTypes($loginType)
    {
        switch ($loginType) {
            case 'customer':
                return ['C']; // Customer
            case 'driver':
                return ['D']; // Driver
            case 'admin':
                return ['S', 'O']; // Super Admin, Office Admin
            default:
                return [];
        }
    }

    /**
     * Get human-readable user type text
     */
    private function getUserTypeText($userType)
    {
        switch ($userType) {
            case 'C':
                return 'Customer';
            case 'D':
                return 'Driver';
            case 'S':
                return 'Super Admin';
            case 'O':
                return 'Office Admin';
            default:
                return 'User';
        }
    }

    /**
     * Redirect user after successful login based on user type
     */
    private function redirectAfterLogin($user)
    {
        switch ($user->user_type) {
            case 'C': // Customer
                return redirect()->intended('/dashboard');
            case 'D': // Driver
            case 'S': // Super Admin
            case 'O': // Office Admin
                return redirect()->intended('/admin');
            default:
                Auth::logout();
                return redirect('/login')->with('error', 'Invalid user type.');
        }
    }

    /**
     * Handle logout for all user types
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }
}