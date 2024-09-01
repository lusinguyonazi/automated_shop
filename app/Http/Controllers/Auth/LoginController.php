<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        if (is_numeric($request->get('email'))) {
            return ['phone' => $request->get('email'), 'password' => $request->get('password')];
        }
        return $request->only($this->username(), 'password');
    }


    public function authenticated($request, $user)
    {
        if ($user->roles()->count() == 0) {
            return redirect('unauthorized');
        }else{
            $role = $user->roles[0]['name'];
            if ($role == 'super_admin') {
                return redirect()->intended('/admin/home');
            }else{
                $shop = $user->shops()->wherePivot('is_default', 1)->first();

                if (!is_null($shop)) {
                    Session::put('shop_id', $shop->id);
                    if ($role == 'manager') {
                        return redirect()->intended('/home'); 
                    }elseif ($role == 'salesman') {
                        return redirect()->intended('/pos');
                    }elseif ($role == 'storekeeper') {
                        return redirect()->intended('/products');
                    }
                }else{
                    $this->guard()->logout();
                    $request->session()->invalidate();
                    $message = 'Seems you are not associated with any Shop';
                    return redirect('/')->with('warning', $message);
                }
            } 
        }
    }

    protected $redirectAfterLogout = '/login';

    public function logout(Request $request){
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect($this->redirectAfterLogout);
    }
}
