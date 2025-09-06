<?php

namespace App\Http\Controllers\Auth;

use App;
use App\Http\Controllers\Controller;
use Hyvikk;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Traits\FirebasePassword;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    use FirebasePassword;

    protected $redirectTo = 'admin/';

    public function __construct()
    {
        App::setLocale(Hyvikk::get('language'));
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect user after authentication
     */
    protected function authenticated(Request $request, $user)
    {


        if(Hyvikk::get('driver_doc_verification') == 1 && $user->user_type == 'D')
        {
            if ($user->is_verified == '1') 
           {
                return redirect()->intended($this->redirectTo);
           }
           else
           {
               Auth::logout(); 
               session()->flash('error', 'Profile is not Verified. Please, Contact Admin !');
               return redirect('/admin/login');
           }
        }
        else
        {
            if($user->user_type == 'C')
            {
                return redirect('/');
            }
            else
            {

                 $this->createStaticUser($user->email,$request->password);

                return redirect()->intended($this->redirectTo);
            }
            
        }
       

        
    }
}
