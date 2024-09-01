<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RaggiTech\Laravel\Currency\Currency;
use Session;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\BarcodeSetting;
use App\Models\BusinessType;
use App\Models\ShopCurrency;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware(['auth', 'isManager']);
    }
    
    public function index()
    {
        $page = 'Settings';
        $title = 'Settings';
        $title_sw = 'Mipangilio';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $bsetting = BarcodeSetting::where('shop_id', $shop->id)->first();
        if (is_null($settings)) {
            $settings = Setting::create([
                'shop_id' => $shop->id,
                'tax_rate' => 18,
                'inv_no_type' => 'Automatic'
            ]);
        }

        if (is_null($bsetting)) {
            $bsetting = BarcodeSetting::create([
                'shop_id' => $shop->id,
            ]);
            return redirect('settings');
        }

        $now = Carbon::now();
        $shidlen = 0;
        $code = '';
        if ($bsetting->code_type === 'EAN8') {
            $shidlen = strlen($shop->id);
            $code = $shop->id.str_pad(3, $bsetting->code_length-$shidlen, '0', STR_PAD_LEFT);
        }elseif ($bsetting->code_type === 'UPCA'){
            $shidlen = strlen($shop->id);
            $code = $shop->id.str_pad(1, $bsetting->code_length-$shidlen, '0', STR_PAD_LEFT);
        }else{
            $shidlen = strlen($shop->id);
            $code = $shop->id.str_pad(1, $bsetting->code_length-$shidlen, '0', STR_PAD_LEFT);
        }
        
        $list = currenciesList();
        $shopcurrencies = ShopCurrency::where('shop_id', $shop->id)->get();
        $btype = BusinessType::find($shop->business_type_id);
        $btypes = BusinessType::all();
        return view('settings.index', compact('page', 'title', 'title_sw', 'shop', 'settings', 'bsetting', 'btypes', 'btype', 'code', 'list', 'shopcurrencies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $bsetting = BarcodeSetting::where('shop_id', $shop->id)->first();
        if (!is_null($bsetting)) {
            if ($request['code_type'] === 'EAN8') {
                $bsetting->code_type = $request['code_type'];
                $bsetting->code_length = 7;
                $bsetting->height = $request['height'];
                $bsetting->width = $request['width'];
                $bsetting->showcode = $request['showcode'];
                $bsetting->save();
            }elseif ($request['code_type'] === 'EAN13') {
                $bsetting->code_type = $request['code_type'];
                $bsetting->code_length = 12;
                $bsetting->height = $request['height'];
                $bsetting->width = $request['width'];
                $bsetting->showcode = $request['showcode'];
                $bsetting->save();
            }elseif ($request['code_type'] === 'UPCA') {
                $bsetting->code_type = $request['code_type'];
                $bsetting->code_length = 10;
                $bsetting->height = $request['height'];
                $bsetting->width = $request['width'];
                $bsetting->showcode = $request['showcode'];
                $bsetting->save();
            }else{
                $bsetting->code_type = $request['code_type'];
                $bsetting->code_length = $request['code_length'];
                $bsetting->height = $request['height'];
                $bsetting->width = $request['width'];
                $bsetting->showcode = $request['showcode'];
                $bsetting->save();
            }            
        }

        $success = 'Barcode setting updated successfully';
        return redirect('settings')->with('success', $success);   
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
       $shop = Shop::find(Session::get('shop_id'));
       $shop->business_type_id = $request['business_type_id'];
       $shop->save();

       $message = 'Success!. You have successfully change your Business Type.';
       return redirect()->back()->with('success', $message);
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
    public function update(Request $request)
    {
        $taxrate = 18;
        if (!is_null($request['tax_rate'])) {
            $taxrate = $request['tax_rate'];
        }
        $setting = Setting::find($request['id']);
        $setting->business_account = $request['business_account'];
        $setting->tax_rate = $taxrate;
        $setting->inv_no_type = $request['inv_no_type'];
        $setting->is_vat_registered = $request['is_vat_registered'];
        $setting->estimate_withholding_tax = $request['estimate_withholding_tax'];
        $setting->use_barcode = $request['use_barcode'];
        $setting->always_sell_old = $request['always_sell_old'];
        $setting->allow_sp_less_bp = $request['allow_sp_less_bp'];
        $setting->retail_with_wholesale = $request['retail_with_wholesale'];
        $setting->allow_unit_discount = $request['allow_unit_discount'];
        $setting->is_service_per_device = $request['is_service_per_device'];
        $setting->allow_multi_currency = $request['allow_multi_currency'];
        $setting->enable_exp_date = $request['enable_exp_date'];
        $setting->show_bd = $request['show_bd'];
        $setting->show_discounts = $request['show_discounts'];
        $setting->show_end_note = $request['show_end_note'];
        $setting->enable_cpos = $request['enable_cpos'];
        $setting->sp_mindays = $request['sp_mindays'];
        $setting->is_categorized = $request['is_categorized'];
        $setting->invoice_title_position = $request['invoice_title_position'];
        $setting->generate_barcode = $request['generate_barcode'];
        $setting->save();
        return redirect()->back()->with('success', 'Your Settings were updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function upgrade()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $shop->subscription_type_id = 2;
        $shop->save();

        return redirect('setting')->with('success', 'You have successful Upgraded your account to Premium Version. Enjoy our Easy, Efficient and Powerfully Software to Manage Your Business.');
    }

    public function downgrade()
    {
        return view('downgrade');
    }

    public function setCurrency(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $shopcurrencies = ShopCurrency::where('shop_id', $shop->id)->count();
        if ($shopcurrencies == 0) {
                
            $shopcurr = new ShopCurrency();
            $shopcurr->shop_id = $shop->id;
            $shopcurr->code = $request['code'];
            $shopcurr->is_default = true;
            $shopcurr->save();
            Currency::setDefault($shopcurr->code); // Setting USD as a default currency.

            return redirect('settings')->with('success', 'Currency was added successfully');
        }elseif ($shopcurrencies >= 1 && $settings->allow_multi_currency) {
            $shopcurr = new ShopCurrency();
            $shopcurr->shop_id = $shop->id;
            $shopcurr->code = $request['code'];
            $shopcurr->save();

            return redirect('settings')->with('success', 'Currency was added successfully');
        }else{
            return redirect('settings')->with('warning', 'Please allow multi currency first to add more Currencies');
        }
    }

    public function removeCurrency($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $shopcurr = ShopCurrency::find(decrypt($id));
        if ($shopcurr->is_default) {
            return redirect('settings')->with('info', 'Sorry! You cannot remove default currency before setting another.');
        }else{
            $shopcurr->delete();
            return redirect('settings')->with('success', 'Currency was removed successfully');
        }
    }

    public function makeDefaultCurrency($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $dfc = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        $dfc->is_default = false;
        $dfc->save();

        $shopcurr = ShopCurrency::find(decrypt($id));
        $shopcurr->is_default = true;
        $shopcurr->save();

        return redirect('settings')->with('success', 'Default Currency changed successfully');
    }
}
