<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Mail;
use RaggiTech\Laravel\Currency\Currency;
use Session;
use App\Models\User;
use App\Models\Shop;
use App\Models\BusinessSubType;
use App\Models\Setting;
use App\Models\SocialMediaAgent;
use App\Models\AgentCustomer;
use App\Models\Payment;
use App\Mail\WelcomeMail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|phone|unique:users',
            'phone_country' => 'required_with:phone',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'shop_name' => 'required|string|max:255',
            'business_type_id' => 'required',
            'business_sub_type_id' => 'required',
            'subscription_type_id' => 'required',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $userexist  = User::where('phone', $data['phone'])->orWhere('email', $data['email'])->first();
        if (is_null($userexist)) {
            
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'country_code' => $data['phone_country'],
                'dial_code' => $data['dial_code']
            ]);
            
            $shop_name = $data['shop_name'];
            $shop = Shop::create([
                'suid' => 'SM-'.$this->unique_code(16),
                'name' => $shop_name,
                'business_type_id' => $data['business_type_id'],
                'business_sub_type_id' => $data['business_sub_type_id'],
                'subscription_type_id' => $data['subscription_type_id']
            ]);

            $user->shops()->attach($shop, ['is_default' => true]);
            $user->assignRole('manager');

            $code = $this->generatePIN(6);
            $payment = Payment::create([
                'transaction_id' => 'SM_'.time(),
                'code' => $code,                    
                'phone_number' => $data['phone'],
                'amount_paid' => 0,
            ]);

            if ($payment) {
                
                $expire_date = \Carbon\Carbon::now()->addMinutes(5);
                $payment->user_id = $user->id;
                $payment->shop_id = $shop->id;
                $payment->period = 'Trial Days';
                $payment->is_expired = false;
                $payment->expire_date = $expire_date;
                $payment->save();
            }

            if (!is_null($data['agent_code'])) {
                $smagent = SocialMediaAgent::where('agent_code', $data['agent_code'])->first();
                if (!is_null($smagent)) {
                    $agent = User::find($smagent->user_id);                        
                    $acustomer = AgentCustomer::create([
                        'agent_id' => $agent->id,
                        'user_id' => $user->id,
                        'agent_code' => $smagent->agent_code
                    ]);
                }
            }

            $permissions = Permission::all();
            foreach ($permissions as $key => $permi) {
                $user->givePermissionTo($permi);
            }
            return $user;
        }else{       
            return redirect()->back()->with('error', 'Sorry! The Mobile number or Email Address already used with another user Account');
        }
    }


    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        
        $shop = $user->shops()->wherePivot('is_default', 1)->first();
        Session::put('shop_id', $shop->id);
        // if (!is_null($user->email)) {
        //    Mail::to($user->email)->send(new WelcomeMail($user));
        // }
        $role = $user->roles[0]['name'];
        if ($role == 'manager') {
            return redirect()->intended('/home');
        }elseif ($role == 'saleman') {
            return redirect()->intended('/pos');
        }
    }

    public function getBSubTypes(Request $request)
    {
        $bsubtypes = BusinessSubType::where('business_type_id', $request['business_type_id'])->get();

        return response()->json($bsubtypes->toArray());
    }

    public function generatePIN($digits = 4)
    {
        $i = 0; //counter
        $pin = ""; //our default pin is blank.
        while($i < $digits){
            //generate a random number between 0 and 9.
            $pin .= mt_rand(0, 9);
            $i++;
        }
        return $pin;

    }

    private function unique_code($limit)
    {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);
    }
}
