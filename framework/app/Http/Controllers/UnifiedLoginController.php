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
            'password' => 'required'
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


        // Additional checks for drivers
        if ($user->user_type == 'D') {
            // Check driver verification if enabled
            if (Hyvikk::get('driver_doc_verification') == 1 && $user->getMeta('is_verified') != '1') {
                return back()
                    ->withErrors(['email' => 'Your profile is not verified. Please contact the administrator.'])
                    ->withInput($request->only('email', 'remember'));
            }
        }

        // Attempt authentication
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            $request->session()->regenerateToken();
            
            // Create static user for Firebase if needed (for admin/driver users)
            if (in_array($user->user_type, ['B', 'S', 'O', 'D'])) {
                $this->createStaticUser($user->email, $request->password);
            }

            // Check if driver is using default password and prompt for change
            if ($user->user_type == 'D' && $request->password === 'password') {
                $request->session()->put('force_password_change', true);
                $request->session()->put('password_change_message', 'Please change your default password for security reasons.');
            }

            // Redirect based on user type
            return $this->redirectAfterLogin($user);
        }

        return back()
            ->withErrors(['email' => 'Authentication failed. Please try again.'])
            ->withInput($request->only('email', 'remember'));
    }


    /**
     * Redirect user after successful login based on user type
     */
    private function redirectAfterLogin($user)
    {
        switch ($user->user_type) {
            case 'C': // Customer
                return redirect('/dashboard');
            case 'D': // Driver
                return redirect('/driver-dashboard');
            case 'B': // Boss Admin
            case 'S': // Super Admin
            case 'O': // Office Admin
                return redirect('/admin');
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

    /**
     * Show driver forgot password form
     */
    public function showDriverForgotPasswordForm()
    {
        return view('driver_dashboard.forgot_password');
    }

    /**
     * Send driver password reset email
     */
    public function sendDriverPasswordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)
                   ->where('user_type', 'D')
                   ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No driver account found with this email address.']);
        }

        // Generate reset token
        $token = str_random(60);
        
        // Store token in user metadata (expires in 1 hour)
        $user->setMeta([
            'password_reset_token' => $token,
            'password_reset_expires' => now()->addHour()->toDateTimeString()
        ]);
        $user->save();

        // For now, we'll show the reset link directly (in production, send via email)
        $resetUrl = url("driver-reset-password/{$token}");
        
        return back()->with('success', "Password reset link generated. For testing purposes, use this link: {$resetUrl}");
    }

    /**
     * Show driver reset password form
     */
    public function showDriverResetPasswordForm($token)
    {
        $user = User::where('user_type', 'D')
                   ->whereHas('metas', function($query) use ($token) {
                       $query->where('key', 'password_reset_token')
                             ->where('value', $token);
                   })
                   ->first();

        if (!$user) {
            return redirect('/login')->with('error', 'Invalid or expired reset token.');
        }

        // Check if token has expired
        $expiresAt = $user->getMeta('password_reset_expires');
        if ($expiresAt && now()->gt($expiresAt)) {
            return redirect('/login')->with('error', 'Reset token has expired. Please request a new one.');
        }

        return view('driver_dashboard.reset_password', compact('token'));
    }

    /**
     * Reset driver password
     */
    public function resetDriverPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required'
        ]);

        $user = User::where('user_type', 'D')
                   ->whereHas('metas', function($query) use ($request) {
                       $query->where('key', 'password_reset_token')
                             ->where('value', $request->token);
                   })
                   ->first();

        if (!$user) {
            return back()->withErrors(['token' => 'Invalid or expired reset token.']);
        }

        // Check if token has expired
        $expiresAt = $user->getMeta('password_reset_expires');
        if ($expiresAt && now()->gt($expiresAt)) {
            return back()->withErrors(['token' => 'Reset token has expired. Please request a new one.']);
        }

        // Update password
        $user->password = Hash::make($request->password);
        
        // Clear reset token
        $user->setMeta([
            'password_reset_token' => null,
            'password_reset_expires' => null,
            'password_changed' => '1',
            'password_changed_at' => now()
        ]);
        $user->save();

        return redirect('/login')->with('success', 'Password reset successfully! You can now log in with your new password.');
    }
}