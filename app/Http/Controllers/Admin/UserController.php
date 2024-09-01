<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\User;
use App\Models\CustomPassReset;
use App\Models\SocialMediaAgent;
use Cache;
use Illuminate\Support\Facades\Cache as FacadesCache;
use Illuminate\Support\Facades\Crypt;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = 'Users';
        $title = 'Users';
        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;            
        $end_date = null;
        $status1 = 'active';
      
        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }
        
        $duration = ['from' => date('d-m-Y', strtotime($start)), 'to' => date('d-m-Y', strtotime($end))];

        $users = User::whereBetween('created_at', [$start, $end])->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $roles = Role::where('guard_name', 'web')->orderBy('name', 'asc')->get();
        $staffs = User::role('sales_representative')->orderBy('first_name', 'asc')->paginate(10)->withQueryString();
        $passcodes = CustomPassReset::orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        return view('admin.users.index', compact('page', 'title', 'users', 'roles', 'staffs', 'passcodes', 'is_post_query', 'start_date', 'end_date', 'duration', 'status1'));
    }

    public function exportUsers(Request $request){

        $page = 'Users';
        $title = 'Users';
        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;            
        $end_date = null;
        $status2 = 'active';
      
        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }
        
        $duration = ['from' => date('d-m-Y', strtotime($start)), 'to' => date('d-m-Y', strtotime($end))];

        $users = User::whereBetween('created_at', [$start, $end])->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.users.export-users', compact('page', 'title', 'users', 'is_post_query', 'start_date', 'end_date', 'duration', 'status2'));
    }

    public function passwordResets(Request $request){
        $page = 'Users';
        $title = 'Users';
        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;            
        $end_date = null;
        $status3 = 'active';
      
        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }
        
        $duration = ['from' => date('d-m-Y', strtotime($start)), 'to' => date('d-m-Y', strtotime($end))];

        $passcodes = CustomPassReset::orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        return view('admin.users.reset', compact('page', 'title', 'passcodes', 'is_post_query', 'start_date', 'end_date', 'duration', 'status3'));
    }

    public function staffs(Request $request)
    {
        $page = 'Users';
        $title = 'Users';
        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;            
        $end_date = null;
        $status4 = 'active';
      
        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }
        
        $duration = ['from' => date('d-m-Y', strtotime($start)), 'to' => date('d-m-Y', strtotime($end))];

        $staffs = User::role('sales_representative')->orderBy('first_name', 'asc')->paginate(10)->withQueryString();
        return view('admin.users.staffs', compact('page', 'title', 'staffs', 'is_post_query', 'start_date', 'end_date', 'duration', 'status4'));
    }

    public function activeUsers()
    {
        $users = $this->onlineUsers();
        $page = 'Users';
        $title = 'Active Users';
        $start_date = null;
        $end_date = null;
        $is_post_query = false;
        // return $users;

        return view('admin.users.active', compact('page', 'title', 'users', 'is_post_query', 'start_date', 'end_date'));
    }

    public function guestUsers()
    {
        $guests = [];      // Get active guests within the last 48 hours

        $page = 'Users';
        $title = 'Guest Users';
        $start_date = null;
        $end_date = null;
        $is_post_query = false;
        // return $users;

        return view('admin.users.guests', compact('page', 'title', 'guests', 'is_post_query', 'start_date', 'end_date'));
    }

    public function shops(Request $request)
    {
        $page = 'Shops';
        $title = 'Registered Shops';

        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;            
        $end_date = null;
      
        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }
        
        $duration = ['from' => date('d-m-Y', strtotime($start)), 'to' => date('d-m-Y', strtotime($end))];

        $shops = Shop::whereBetween('created_at', [$start, $end])->paginate(10)->withQueryString();

        return view('admin.users.shops', compact('page', 'title', 'shops', 'is_post_query', 'start_date', 'end_date', 'duration'));

    }

    public function exportShops(Request $request){
        $page = 'Shops';
        $title = 'Registered Shops';

        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;            
        $end_date = null;
      
        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }
        
        $duration = ['from' => date('d-m-Y', strtotime($start)), 'to' => date('d-m-Y', strtotime($end))];

        $usershops = User::role('manager')
        ->join('shop_user', 'shop_user.user_id', '=', 'users.id')
        ->join('shops', 'shops.id', '=', 'shop_user.shop_id')
        ->whereBetween('shops.created_at', [$start, $end])
        ->join('payments', 'payments.shop_id', '=', 'shops.id')
        ->select('users.first_name as first_name', 'users.last_name as last_name', 'users.phone as phone', 'shops.name as name', 'shop_user.is_default as is_default', 'shops.created_at as created_at', 'payments.expire_date as expire_date', 'payments.is_expired as is_expired')
        ->paginate(10)->withQueryString();

        return view('admin.users.export', compact('page', 'title', 'usershops', 'is_post_query', 'start_date', 'end_date', 'duration'));
    }

    public function activeShops(Request $request)
    {
        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;            
        $end_date = null;
      
        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }
        
        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';

        $shops = User::role('manager')->join('shop_user', 'shop_user.user_id', '=', 'users.id')->join('shops', 'shops.id', '=', 'shop_user.shop_id')->join('payments', 'payments.shop_id', '=', 'shops.id')->whereRaw('CHAR_LENGTH(transaction_id) = 10')->where('is_expired', false)->select('users.first_name as first_name', 'users.last_name as last_name', 'users.phone as phone', 'shops.id as id', 'shops.name as name', 'shops.street as street', 'shops.district as district', 'shops.city as city', 'shop_user.is_default as is_default', 'shops.created_at as created_at', 'payments.expire_date as expire_date', 'payments.is_expired as is_expired')->groupBy('id')->orderBy('created_at', 'desc')->get();
        $page = 'Shops';
        $title = 'Active Shops';

        return view('admin.users.active-shops', compact('page', 'title', 'shops', 'is_post_query', 'start_date', 'end_date', 'duration'));

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            $user = User::create([
                'ba_id' => 1,
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'phone' => $request['phone'],
                'email' => $request['email'],
                'password' => bcrypt($request['password']),
            ]);

            if ($user) {
                $user->assignRole($request['role']);
            }

            $message = 'User was registered successfully!';
            return redirect()->back()->with('success', $message);

        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode = '1062') {
                $message = 'Ooops! Mobile number already used in our System.';
                return redirect()->back()->with('error', $message);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find(Crypt::decrypt($id));
        $page = 'Assign User role';
        $title = 'User roles';
        $roles = Role::where('guard_name', 'web')->orderBy('name', 'asc')->get();
        return view('admin.users.edit', compact('page', 'title', 'user', 'roles'));
    }

    //Assign Role
    public function assignUserRole(Request $request)
    {
        $user = User::find($request['user_id']);
        $user->assignRole($request['role']);
        return redirect('admin/users');
    }

    //Detach Role
    public function detachUserRole(Request $request)
    {
        $user = User::find($request['user_id']);
        $user->removeRole($request['role']);
        return redirect('admin/users');
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
        try {
            $user = User::where('id', Crypt::decrypt($id))->update([
                'ba_id' => 1,
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'phone' => $request['phone'],
                'email' => $request['email'],
                'password' => bcrypt($request['password']),
            ]);

            if ($user) {
                $user->assignRole($request['role']);
            }

            $message = 'User was updated successfully!';
            return redirect()->back()->with('success', $message);

        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode = '1062') {
                $message = 'Ooops! Mobile number already used in our System.';
                return redirect()->back()->with('error', $message);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find(Crypt::decrypt($id));
        $shops = $user->shops()->count();

        if ($shops == 0) {
            $user->delete();
            return redirect()->back()->with('success', 'User deleted completely because has no Business belongs to him/her');
        }else{

            return redirect()->back()->with('info', 'User can not be removed because has Business belonging to him/her');
        }
    }

   public function changeSubscriptionType($id)
   {
       $shop = Shop::find($id);
       if ($shop->subscription_type_id == 1) {
            $shop->subscription_type_id = 2;
            $shop->save();
       }else{
            $shop->subscription_type_id = 1;
            $shop->save();
       }

       return redirect()->back()->with('success', 'Subscription changed successful');
   }


    public function newRole(Request $request)
    {
        $role = Role::create([
            'name' => $request['name'],
            'guard_name' => 'web',
            'name' => $request['name'],
            'description' => $request['description']
        ]);

        return redirect()->back();
    }


    public function profile()
    {
        $page = 'Profile';
        $title = 'My Profile';
        return view('admin.users.profile', compact('page', 'title', 'users'));
    }


    public function updateProfile(Request $request)
    {
        $user = User::find($request['id']);
        $user->first_name = $request['first_name'];
        $user->last_name = $request['last_name'];
        $user->phone = $request['phone'];
        $user->email = $request['email'];
        $user->save();

        $message = 'Your information updated successfully';
        Alert::success('Success!', $message);
        return redirect()->back();
    }

    public function createAgentCode(Request $request)
    {
        $user = User::find($request['user_id']);
        if (!is_null($user)) {
            $smagent = SocialMediaAgent::where('user_id', $user->id)->first();
            if (!is_null($smagent)) {
                return redirect()->back()->with('info', 'User already assigned as Agent with Agent code : '.$smagent->agent_code);
            }else{
                $code = $this->generateCode(4);
                SocialMediaAgent::create([
                    'user_id' => $user->id,
                    'agent_code' => $code
                ]);
            }
        }
        return redirect()->back()->with('success', 'Agent code was successfully created');
    }

    public function generateCode($digits = 4)
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

    public function agentsCustomers(Request $request)
    {
        $page = 'Users';
        $title = 'Customers from Agents';

        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;            
        $end_date = null;
      
        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }
        
        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';

        $users = AgentCustomer::join('users', 'users.id', '=', 'agent_customers.user_id')->get();
        $agents = User::role('agent')->get();

        return view('admin.users.customers-by-agents', compact('page', 'title', 'users', 'is_post_query', 'start_date', 'end_date', 'duration', 'agents'));
    }

    public function clearResetCodes()
    {
        CustomPassReset::truncate();
        return redirect('admin/users')->with('success', 'Record were removed successfully');
    }

    public static function onlineUsers() {
        // Get the array of users
        $users = Cache::get('online-users');
        if(!$users) return null;
        
        // Add the array to a collection so you can pluck the IDs
        $onlineUsers = collect($users);
        // Get all users by ID from the DB (1 very quick query)
        $dbUsers = User::find($onlineUsers->pluck('id')->toArray());
        
        // Prepare the return array
        $displayUsers = [];

        // Iterate over the retrieved DB users
        foreach ($dbUsers as $user){
            // Get the same user as this iteration from the cache
            // so that we can check the last activity.
            // firstWhere() is a Laravel collection method.
            $onlineUser = $onlineUsers->firstWhere('id', $user['id']) ;
            // Append the data to the return array
            $displayUsers[] = [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
                // This Bool operation below, checks if the last activity
                // is older than 3 minutes and returns true or false,
                // so that if it's true you can change the status color to orange.
                'away' => $onlineUser['last_activity_at'] < now()->subMinutes(3),
            ];
        }
        return collect($displayUsers);
    }
}
