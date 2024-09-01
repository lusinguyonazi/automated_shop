<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use PragmaRX\Countries\Package\Countries;
use Session;
use Crypt;
use Auth;
use App\Models\Payment;
use App\Models\User;
use App\Models\Shop;
use App\Models\BusinessType;
use App\Models\BusinessSubType;
use App\Models\SubscriptionType;
use App\Models\BankDetail;


class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $page = 'Profile';
        $title = 'My Account';
        $title_sw = 'Akaunti Yangu';
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $users = $shop->users()->get();
        $payments = Payment::where('shop_id', $shop->id)->join('users', 'users.id', '=', 'payments.user_id')->select('users.first_name as first_name', 'users.last_name as last_name', 'users.phone as phone', 'payments.phone_number as phone_number', 'payments.id as id', 'payments.transaction_id as transaction_id', 'payments.code as code', 'payments.amount_paid as amount_paid', 'payments.period as period', 'payments.activation_time as activation_time', 'payments.expire_date as expire_date', 'payments.is_expired as is_expired', 'payments.created_at as created_at')->orderBy('payments.created_at', 'desc')->get();

        $shops = $user->shops()->get();
        $btypes = BusinessType::all();
        $bsubtypes = BusinessSubType::all();
        $stypes = SubscriptionType::all();
        $roles = Role::where('guard_name', 'api')->get();
        return view('accounts.index', compact('page', 'title', 'title_sw', 'users', 'shops', 'btypes', 'stypes', 'roles', 'payments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'Profile';
        $title = 'Add New User';
        $title_sw = 'Ongeza Mtumiaji Mpya';
        $roles = Role::where('guard_name', 'web')->skip(2)->take(6)->get();


        return view('accounts.create', compact('page', 'title', 'title_sw', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = User::where('phone', $request['phone'])->first();
            // $role = Role::find(decrypt($id));
            if (!is_null($user)) {
                $shop = Shop::find(Session::get('shop_id'));
                $user->shops()->attach($shop, ['is_default' => true]);
                // $user->assignRole($request['role']);
                $user->assignRole('salesman');
                $message = 'User was added successfully';
                return redirect('user-profile')->with('success', $message);
            } else {
                $user = User::create([
                    'ba_id' => 3,
                    'first_name' => $request['first_name'],
                    'last_name' => $request['last_name'],
                    'phone' => $request['phone'],
                    'email' => $request['email'],
                    'password' => bcrypt($request['password']),
                    'country_code' => $request['phone_country'],
                    // 'role' => $request['role'],
                ]);
                if ($user) {
                    $shop = Shop::find(Session::get('shop_id'));
                    $user->shops()->attach($shop, ['is_default' => true]);
                    $user->assignRole($request['role']);
                    // $user->assignRole('salesman');
                    $message = 'User was added successfully';
                    return redirect('user-profile')->with('success', $message);
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode = '1062') {
                $msg = $errorCode . 'Ooops! Duplicate. Mobile number already used.';
                return redirect()->back()->with('error', $msg);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function show($id)
    {
        $page = 'Profile';
        $title = 'User information';
        $title_sw = 'Taarifa za Mtumiaji';

        $user = User::find(decrypt($id));
        $user_permissions = $user->permissions()->get();
        $permissions = Permission::get();
        $currPermissions = $user->permissions()->pluck('permission_id')->toArray();

        $permissions = Permission::all();
        $roles = Role::where('guard_name', 'web')->get();
        // return $roles;
        $manager = Auth::user();
        $shops = $manager->shops()->get();
        $usershops = $user->shops()->get();

        return view('accounts.show', compact('page', 'title', 'title_sw', 'user', 'user_permissions', 'currPermissions',  'permissions', 'roles', 'shops', 'usershops'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Profile';
        $title = 'Edit User Info';
        $title_sw = 'Hariri Taarifa za Mtumiaji';
        $user = User::find(decrypt($id));
        // $countries = Countries::all()->map(function ($country) {
        //     $commonName = $country->name->common;
        //     $languages = $country->languages ?? collect();
        //     $language = $languages->keys()->first() ?? null;
        //     $nativeNames = $country->name->native ?? null;
        //     if (
        //         filled($language) &&
        //         filled($nativeNames) &&                    
        //         filled($nativeNames[$language]) ?? null
        //     ) {
        //         $native = $nativeNames[$language]['common'] ?? null;
        //     }

        //     if (blank($native ?? null) && filled($nativeNames)) {
        //         $native = $nativeNames->first()['common'] ?? null;
        //     }

        //     $native = $native ?? $commonName;

        //     if ($native !== $commonName && filled($native)) {
        //         $native = "$native ($commonName)";
        //     }

        //     return [$country->cca2 => $native];
        // })->values()->toArray();

        // $countries = call_user_func_array('array_merge', $countries);

        return view('accounts.edit', compact('page', 'title', 'title_sw', 'user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find(decrypt($id));
        $user->first_name = $request['first_name'];
        $user->last_name = $request['last_name'];
        $user->phone = $request['phone'];
        $user->email = $request['email'];
        $user->country_code = $request['country_code'];
        // $user->user_role = $request['user_role'];
        $user->save();

        $message = 'Your information updated successfully';
        return redirect('user-profile')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));
        $role = $user->roles[0]['name'];

        if ($role == 'manager') {
            $info = 'Shop super user cannot be deleted';
            return redirect('employees')->with('info', $info);
        } else {
            $shop->users()->detach($user);
            $message = 'Your sale man ' . $user->name . ' was successfully removed in our records';
            return redirect('user-profile')->with('success', $message);
        }
    }

    public function changePassForm()
    {
        $page = "Change Password";
        return view('auth.passwords.change-password', compact('page'));
    }


    public function changePass(Request $request)
    {
        $user = User::find(Auth::user()->id);

        if (Hash::check($request['curr_password'], $user->password)) {

            $this->passvalidator($request->all())->validate();

            $user->password = bcrypt($request['password']);
            $user->save();

            return redirect('login')->with('success', 'Hi ' . $user->name . ', Your Password has been reseted successfuly. Login now');
        } else {
            return redirect()->back()->with('error', 'Your current password does not matches with the password you provided. Please try again.');
        }
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function passvalidator(array $data)
    {
        return Validator::make($data, [
            'curr_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }



    public function assignBusiness(Request $request)
    {
        $shop = Shop::find($request['shop_id']);

        if (!is_null($shop)) {
            $user = User::find($request['user_id']);
            $attshop = $user->shops()->where('shop_id', $shop->id)->first();
            if (is_null($attshop)) {
                $user->shops()->attach($shop);
                return redirect()->back()->with('success', 'User attached to ' . $shop->name . ' successfully');
            } else {
                return redirect()->back()->with('info', 'User already attached to ' . $shop->name);
            }
        } else {
            return redirect()->back();
        }
    }

    public function detachBusiness(Request $request)
    {
        $shop = Shop::find($request['shop_id']);
        if (!is_null($shop)) {
            $user = User::find($request['user_id']);
            $attshop = $user->shops()->where('shop_id', $shop->id)->where('is_default', false)->first();
            if (!is_null($attshop)) {
                $user->shops()->detach($shop);
                return redirect()->back()->with('success', 'User detached from ' . $shop->name . ' successfully');
            } else {
                $attshop = $user->shops()->where('is_default', false)->first();
                if (!is_null($attshop)) {
                    $user->shops()->detach($shop);
                    $attshop->pivot->is_default = true;
                    $attshop->pivot->save();
                    return redirect()->back()->with('success', 'User detached from ' . $shop->name . ' successfully');
                } else {
                    return redirect()->back()->with('info', 'User already detached from ' . $shop->name . ' Or is Users Default Business');
                }
            }
        } else {
            return redirect()->back();
        }
    }


    //Assign Role
    public function assignUserRole(Request $request)
    {
        $user = User::find($request['user_id']);
        $currrole = $user->roles[0]['name'];
        $user->removeRole($currrole);
        $user->assignRole($request['role']);
        return redirect()->back()->with('success', 'User Role was Changed successfully');
    }
    public function assignPermissions(Request $request)
    {

        // $role = Role::find($request['role_id']);
        // $user = User::find($request['user_id']);
        // if (!is_null($user)) {
        //      foreach ($request['permission'] as $perm) {
        //         $permi = Permission::find($perm);
        //         $user->givePermissionTo($permi);
        //     }


        $user = User::find($request['user_id']);
        if (is_array($request['permission']) || is_object($request['permission'])) {
            if (!is_null($user)) {
                foreach ($request['permission'] as $perm) {
                    $user->givePermissionTo($perm);
                }
            }
        } else {
            return redirect()->back()->with('info', 'No permission added to user ' . $user->first_name);
        }

        return redirect()->back()->with('success', 'permissions were added to user ' . $user->first_name);
    }

    public function removePermissions(Request $request)
    {
        $user = User::find($request['user_id']);
        if (is_array($request['permission']) || is_object($request['permission'])) {


            foreach ($request['permission'] as $perm) {
                $permission = Permission::find($perm);
                // dd($permission);
                $user->revokePermissionTo($permission);
            }
        } else {
            return redirect()->back()->with('info', 'Nothing was revoked from user ' . $user->first_name);
        }

        return redirect()->back()->with('success', 'permissions were revoked from user ' . $user->first_name);
    }

    public function revokeAll($id)
    {
        $user = User::find(Crypt::decrypt($id));

        $user_permissions = $user->permissions()->get();
        foreach ($user_permissions as $key => $perm) {
            $user->revokePermissionTo($perm);
        }

        return redirect()->back()->with('success', 'permissions were revoked from user ' . $user->first_name);
    }

    public function viewReceipt($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $page = 'Receipt';
        $title = 'Service Payment Receipt';
        $title_sw = 'Risiti ya malipo ya Huduma';
        $receipt = Payment::find($id);
        return view('accounts.receipt', compact('title', 'title_sw', 'page', 'receipt', 'shop', 'user'));
    }

    public function getBSubTypes(Request $request)
    {
        $bsubtypes = BusinessSubType::where('business_type_id', $request['business_type_id'])->get();

        return response()->json($bsubtypes->toArray());
    }
}
