<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Crypt;
use Session;
use File;
use Auth;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\BusinessType;
use App\Models\BusinessSubType;
use App\Models\User;
use App\Models\Payment;
use App\Models\BankDetail;



class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        
        $shop = Shop::create([
            'suid' => 'SM-'.$this->unique_code(16),
            'name' => $request['shop_name'],
            'business_type_id' => $request['business_type_id'],
            'subscription_type_id' => $request['subscription_type_id'],
            'business_sub_type_id' => $request['business_sub_type_id']
        ]);

        $user = Auth::user();
        $user->shops()->attach($shop);

        $setting = Setting::create([
            'shop_id' => $shop->id,
            'tax_rate' => 18,
            'inv_no_type' => 'Automatic'
        ]);        
        $code = $this->generatePIN(6);        
        $payment = Payment::create([
            'transaction_id' => 'SM_'.time(),
            'code' => $code,                    
            'phone_number' => $user->phone,
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
        
        $message = 'Your shop '.$shop->name.' was registered successfully';
        
        return redirect('user-profile')->with('success', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $shop = Shop::find(decrypt($id));
        $page = 'User Profile';
        $title = 'Business details';
        $title_sw = 'Taarifa za biashara';
        $btype = BusinessType::find($shop->business_type_id);
        $bstype = BusinesssubType::find($shop->business_sub_type_id);
        $btypes = BusinessType::all();
        $bankdetails = $shop->bankDetails()->get();

        return view('accounts.shop-details', compact('page', 'title', 'title_sw', 'shop', 'btype', 'btypes', 'bankdetails' , 'bstype'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        Session::forget('shop_name');
        $shop = Shop::find(decrypt($id));
        $shop->name = $request['shop_name'];
        $shop->tin = $request['tin'];
        $shop->vrn = $request['vrn'];
        $shop->tel = $request['tel'];
        $shop->mobile = $request['mobile'];
        $shop->email = $request['email'];
        $shop->postal_address = $request['postal_address'];
        $shop->physical_address = $request['physical_address'];
        $shop->street = $request['street'];
        $shop->district = $request['district'];
        $shop->city = $request['city'];
        $shop->short_desc = $request['short_desc'];
        $shop->website = $request['website'];

        $location = null;
        if ($request->hasFile('image')) {
            //  Let's do everything here
            if ($request->file('image')->isValid()) {
                //
                $validated = $request->validate([
                    'image' => 'mimes:jpeg,png|max:1014',
                ]);

                $logo_path = storage_path('/public/logos/'.$shop->logo_location);
                if (File::exists($logo_path)) {
                    unlink($logo_path);
                }

                $extension = $request->image->extension();
                $request->image->storeAs('/public/logos', $shop->id.'_logo.'.$extension);
                $location = $shop->id.'_logo.'.$extension;
            }
        }else{
            $location = $shop->logo_location;
        }
        
        $shop->logo_location = $location;
        $shop->save();

        Session::put('shop_name', $shop->name);
        $message = 'Shop information was successfully updated';

        // return $request['logo'];
        return redirect()->back()->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $shop = Shop::find(decrypt($id));
        $user = Auth::user();

        $usershop = $user->shops()->where('shop_id', $shop->id)->first();
        if (!is_null($usershop) && !$usershop->pivot->is_default) {
            $currshop = Shop::find(Session::get('shop_id'));

            if ($shop->id != $currshop->id) {
                $user->shops()->detach($shop);
            }
            return redirect()->back()->with('success', 'Your Business was successfully removed');
        }else{

            return redirect()->back()->with('info', 'You can not remove default business Account.');
        }
    }

    public function switchShop(Request $request)
    {
        Session::forget('shop_id');
        Session::forget('shop_name');
        $user = Auth::user();
        $shops = $user->shops()->get();
        foreach ($shops as $key => $mshop) {
            $mshop->pivot->is_default = 0;
            $mshop->pivot->save();
        }

        $shop = Shop::find($request['shop_id']);
        $ushops = $user->shops()->where('shop_id', $shop->id)->first();
        $ushops->pivot->is_default = 1;
        $ushops->pivot->save();

        if ($shop) {
            $payment = Payment::where('shop_id', $shop->id)->where('is_expired', false)->where('is_for_module', false)->first();
            if (!is_null($payment)) {
                Session::put('expired', $payment->is_expired);
            }else{
                Session::put('expired', 1);
            }
            Session::put('shop_id', $shop->id);
            Session::put('shop_name', $shop->name);

            if (Auth::user()->hasRole('manager')) {
                $message = 'You have switched shop to '.$shop->name;
                return redirect('home')->with('success', $message);
            }else{
                $message = 'You have switched shop to '.$shop->name;
                return redirect('pos')->with('success', $message);
            }
        }
    }

    public function notifications()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $notifications = [];
        if (!is_null($shop)) {
            $notifications = $shop->unreadNotifications()->orderBy('created_at', 'asc')->limit(5)->get()->toArray();
        }

        return json_encode($notifications);
    }

    public function markAsRead()
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($shop)) {
            $shop->unreadNotifications->markAsRead();
        }

        return redirect()->back();
    }

    private function unique_code($limit)
    {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);
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
}
