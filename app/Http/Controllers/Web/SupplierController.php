<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Crypt;
use Auth;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use App\Models\SmsAccount;
use App\Models\Purchase;
use App\Models\Setting;
use App\Models\BankDetail;
use App\Models\AnCost;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $suppliers = Supplier::where('shop_id', $shop->id)->where('supplier_for', 'Stock')->get();

        $page = 'Suppliers';
        $title = 'My Suppliers';
        $title_sw = 'Wauzaji Wangu';
        return view('products.suppliers.index', compact('page', 'title', 'title_sw', 'suppliers'));
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
        $now = Carbon::now();
        $shop = Shop::find(Session::get('shop_id'));
        $supplier = Supplier::where('name', $request['name'])->where('contact_no', $request['contact_no'])->where('supplier_for', $request['supplier_for'])->where('shop_id' , $shop->id)->first();
        if (!is_null($supplier)) {
             return redirect()->back()->with('warning', 'This Supplier has been added earlier');        
        }else{
            $supp_id = Supplier::where('shop_id' , $shop->id)->get()->max('supp_id');
            $supplier = Supplier::create([
                'name' => $request['name'],
                'shop_id' => $shop->id,
                'supp_id' =>  !is_null($supp_id) & ($supp_id > 1) ? $supp_id+1 : 1 , 
                'contact_no' => $request['contact_no'],
                'email' => $request['email'],
                'address' => $request['address'],
                'country_code' => $request['phone_country'],
                'supplier_for' => $request['supplier_for'],
                'time_created' => $now
            ]);
            return redirect()->back()->with('message', 'Your Supplier was added successfully.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id , Request $request)
    {
        $page = 'Suppliers';
        $title = 'Supplier Account Statement';
        $title_sw = 'Taarifa ya Akaunti ya Supplier';
        $shop = Shop::find(Session::get('shop_id'));
        $supplier = Supplier::find(decrypt($id));

        $customer = null;
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
            $ftrans = SupplierTransaction::where('shop_id', $shop->id)->where('supplier_id', $supplier->id)->orderBy('id', 'asc')->first();
            $sdate = date('Y-m-d', strtotime($supplier->created_at)).' 00:00:00';
            if (!is_null($ftrans)) {
                $sdate = $ftrans->date.' 00:00:00';
            }
            $start = $sdate;
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        $transactions = SupplierTransaction::where('supplier_id', $supplier->id)->where('is_deleted', false)->whereBetween('created_at', [$start, $end])->orderBy('date', 'asc')->get();
        $invtrans = SupplierTransaction::where('supplier_id', $supplier->id)->where('is_deleted', false)->where('amount', '>', 0)->whereBetween('created_at', [$start, $end])->orderBy('date', 'asc')->get();
        $payments = SupplierTransaction::where('payment', '!=', null)->where('supplier_id', $supplier->id)->where('is_deleted', false)->whereBetween('created_at', [$start, $end])->orderBy('date', 'desc')->get();
        $purchases = Purchase::where('shop_id', $shop->id)->where('supplier_id', $supplier->id)->where('is_deleted', false)->where('status', 'Pending')->orderBy('time_created', 'desc')->get();
        $obal = SupplierTransaction::where('supplier_id', $supplier->id)->where('is_ob', true)->first();

        $settings = Setting::where('shop_id', $shop->id)->first();
        $smsacc = SmsAccount::where('shop_id', $shop->id)->first();
        $bdetails = BankDetail::where('shop_id', $shop->id)->get();
        $senderids = null;
        if (!is_null($smsacc)) {
            $senderids = $smsacc->senderIds()->get();
        }

        $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
        $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('products.suppliers.show', compact('page', 'title', 'title_sw', 'shop', 'transactions', 'payments', 'purchases', 'supplier', 'is_post_query', 'duration', 'duration_sw', 'start_date', 'end_date', 'reporttime', 'customer', 'settings', 'obal', 'invtrans', 'senderids', 'defcurr', 'bdetails', 'currencies')); 
    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Edit Supplier';
        $title = 'Edit Supplier Info';
        $title_sw = 'Hariri tarifa za Muuzaji';
        $supplier = Supplier::find(Crypt::decrypt($id));

        $countries = Countries::all()->map(function ($country) {
            $commonName = $country->name->common;
            $languages = $country->languages ?? collect();
            $language = $languages->keys()->first() ?? null;
            $nativeNames = $country->name->native ?? null;
            if (
                filled($language) &&
                filled($nativeNames) &&
                filled($nativeNames[$language]) ?? null
            ) {
                $native = $nativeNames[$language]['common'] ?? null;
            }

            if (blank($native ?? null) && filled($nativeNames)) {
                $native = $nativeNames->first()['common'] ?? null;
            }

            $native = $native ?? $commonName;

            if ($native !== $commonName && filled($native)) {
                $native = "$native ($commonName)";
            }

            return [$country->cca2 => $native];
        })->values()->toArray();

        $countries = call_user_func_array('array_merge', $countries);
        return view('suppliers.edit', compact('page', 'title', 'title_sw', 'supplier', 'countries'));
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
        $shop = Shop::find(Session::get('shop_id'));
        $supplier = Supplier::find($request['id']);
        $sup_shops = $supplier->shops()->count();
        $now = Carbon::now();
        if ($sup_shops > 1) {
            $newsupplier = Supplier::create([
                'name' => $request['name'],
                'contact_no' => $request['contact_no'],
                'email' => $request['email'],
                'address' => $request['address'],
                'country_code' => $request['phone_country'],
                'time_created' => $now
            ]);

            $shop->suppliers()->attach($newsupplier);
            $shop->suppliers()->detach($supplier);
            return redirect('suppliers')->with('message', 'Your Supplier was updated successfully.');
        }else{
            $supplier->name = $request['name'];
            $supplier->contact_no = $request['contact_no'];
            $supplier->email = $request['email'];
            $supplier->address = $request['address'];
            $supplier->country_code = $request['phone_country'];

            $supplier->save();

            return redirect('suppliers')->with('message', 'Your Supplier was updated successfully.');

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
        $shop = Shop::find(Session::get('shop_id'));
        $supplier = Supplier::find(decrypt($id));
        if (is_null($supplier)) {
            return redirect('forbiden');
        }else{


            $supplier->delete($supplier);
            $message = 'Supplier was successfully removed from your supplier list';

            // Alert::success('Success!', $message);
            return redirect('suppliers')->with('message', $message);
        }
    }
}
