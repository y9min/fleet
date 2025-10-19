<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class SessionClearController extends Controller
{
    public function clearSession()
    {
        // Clear all session data
        Session::flush();
        
        // Logout any authenticated user
        Auth::logout();
        
        // Clear all cookies
        if (isset($_COOKIE)) {
            foreach ($_COOKIE as $name => $value) {
                setcookie($name, '', time() - 3600, '/');
            }
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Session cleared successfully. Please refresh the page.',
            'timestamp' => now()->toDateTimeString()
        ]);
    }
}
