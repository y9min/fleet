<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use App\Traits\FirebasePassword;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

	use FirebasePassword;

    public function __construct()
    {
        $this->middleware('guest');
    }

    // Override the default password reset logic
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        if($user->save())
		{
			$this->newpassword($user->email,$password);
		}
    }

    // Redirect after successful password reset
    protected function redirectTo()
    {
        return '/admin/login';
    }

    // Handle invalid token case
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }
}
