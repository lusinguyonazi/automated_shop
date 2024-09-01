<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Response;
use Session;
use Auth;
use App\Models\Shop;
use App\Models\User;
use App\Models\AnSale;
use App\Models\Customer;
use App\Models\AnSaleItem;
use App\Models\Setting;
use App\Models\SaleTemp;
use App\Models\SaleItemTemp;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Stock;
use App\Models\ShopCurrency;
use Log;

class SaleItemTempController extends Controller
{/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {   
        $shop = Shop::find(Session::get('shop_id'));
        $saletemp = SaleTemp::find($id);
        $customers = Customer::where('shop_id', $shop->id)->get();
        $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
        $itemtemps = SaleItemTemp::where('sale_temp_id', $id)->with('product')->orderBy('id', 'desc')->get();
        // Log::info($itemtemps);
        $temps = array();
        foreach ($itemtemps as $temp) {
            $itemTemp = [
                'id' =>  $temp->id,
                'sale_temp_id' =>  $temp->sale_temp_id,
                'product_id' => $temp->product_id,
                'product_unit_id' => $temp->product_unit_id,
                'name' => $temp->product->name,
                'curr_stock' => $temp->curr_stock,
                'quantity_sold' => $temp->quantity_sold,
                'sold_in' => $temp->sold_in,
                'used_stock' => $temp->used_stock,
                'buying_per_unit' => $temp->buying_per_unit,
                'buying_price' => $temp->buying_price,
                'price_per_unit' => round($temp->price_per_unit*$saletemp->ex_rate, 2),
                'discount' => round($temp->discount*$saletemp->ex_rate, 2),
                'price' => $temp->price*$saletemp->ex_rate,
                'total_discount' => round($temp->total_discount*$saletemp->ex_rate, 2),
                'with_vat' => $temp->with_vat,
                'vat_amount' => round($temp->vat_amount*$saletemp->ex_rate, 2),
                'created_at' => $temp->created_at,
                'updated_at' => $temp->updated_at
            ];
            $itemTemp['units'] = ProductUnit::where('product_id', $temp->product_id)->get()->toArray();
            array_push($temps, array_merge($itemTemp, $itemTemp['units']));
        }
        return Response::json(['saletemp' => $saletemp, 'items' => $temps, 'customers' => $customers, 'currencies' => $currencies]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sales.create');
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
        $user = Auth::user();
        $sale = AnSale::where('shop_id', $shop->id)->count(); 
        $customer = Customer::where('id', Session::get('cust_id'))->where('check_last_sale', true)->first();
        // Log::info($customer);
        $cust_item_lb = null;
        if (!is_null($customer)) {
            $cust_item_lb = AnSaleItem::where('product_id', $request['product_id'])->join('an_sales', 'an_sales.id', '=', 'an_sale_items.an_sale_id')->where('customer_id', $customer->id)->orderBy('an_sale_items.time_created', 'DESC')->select('an_sale_items.id as id', 'product_id', 'buying_per_unit', 'price_per_unit', 'an_sale_items.discount as discount', 'sold_in', 'an_sale_items.time_created as time_created')->first();
        }
        $sameitems = SaleItemTemp::where('product_id', $request['product_id'])->where('sale_temp_id', $request['sale_temp_id'])->count();
        
        if ($sameitems == 0) {
            $product = $shop->products()->where('product_id', $request['product_id'])->first();
            if (!is_null($product)) {
                $bunit = ProductUnit::where('shop_id', $shop->id)->where('product_id', $product->id)->where('is_basic', true)->first();
                $settings = Setting::where('shop_id', $shop->id)->first();
                if (!$settings->allow_sp_less_bp && $product->pivot->price_per_unit <= $product->pivot->buying_per_unit) {
                    $warning = 'Ooops!. The product has wrong prices. Please update the prices for accurate calculations';
                    return response()->json(['status' => 'WP', 'msg' => $warning]);
                }else {
                    if (is_null($product->pivot->in_stock) || $product->pivot->in_stock < $request['quantity_sold']) {
                        return response()->json(['status' => 'LOW', 'msg' => 'Stock of Your Product '.$product->name.' is currently less than.'.($request['quantity_sold'])]);
                    } else {
                        if (!is_null($cust_item_lb)) {
                            $saleItemTemp = new SaleItemTemp;
                            $saleItemTemp->sale_temp_id = $request['sale_temp_id'];
                            $saleItemTemp->product_unit_id = $bunit->id;
                            $saleItemTemp->product_id = $request['product_id'];
                            $saleItemTemp->quantity_sold = 0;
                            $saleItemTemp->curr_stock = $product->pivot->in_stock;
                            $saleItemTemp->buying_per_unit = $cust_item_lb->buying_per_unit;
                            $saleItemTemp->buying_price = $saleItemTemp->buying_per_unit*$saleItemTemp->quantity_sold;
                            $saleItemTemp->price_per_unit = $cust_item_lb->price_per_unit;
                            $saleItemTemp->price = $saleItemTemp->price_per_unit*$saleItemTemp->quantity_sold;
                            $saleItemTemp->discount = $cust_item_lb->discount;
                            $saleItemTemp->used_stock = 'Old';
                            $saleItemTemp->sold_in = 'Retail Price';
                            $saleItemTemp->save();
                            return $saleItemTemp;
                        }else{
                            if (is_null($request['buying_per_unit'])) {

                                $saleItemTemp = new SaleItemTemp;
                                $saleItemTemp->sale_temp_id = $request['sale_temp_id'];
                                $saleItemTemp->product_id = $request['product_id'];
                                $saleItemTemp->product_unit_id = $bunit->id;
                                $saleItemTemp->quantity_sold = 0;
                                $saleItemTemp->curr_stock = $product->pivot->in_stock;
                                $saleItemTemp->buying_per_unit = 0;
                                $saleItemTemp->buying_price = $saleItemTemp->buying_per_unit*$saleItemTemp->quantity_sold;
                                $saleItemTemp->price_per_unit = $request['price_per_unit'];
                                $saleItemTemp->price = $saleItemTemp->price_per_unit*$saleItemTemp->quantity_sold;
                                $saleItemTemp->discount = 0;
                                $saleItemTemp->used_stock = 'Old';
                                $saleItemTemp->sold_in = 'Retail Price';
                                $saleItemTemp->save();
                                return $saleItemTemp;
                            }else{
                                $saleItemTemp = new SaleItemTemp;
                                $saleItemTemp->sale_temp_id = $request['sale_temp_id'];
                                $saleItemTemp->product_id = $request['product_id'];
                                $saleItemTemp->product_unit_id = $bunit->id;
                                $saleItemTemp->quantity_sold = 0;
                                $saleItemTemp->curr_stock = $product->pivot->in_stock;
                                $saleItemTemp->buying_per_unit = $request['buying_per_unit'];
                                $saleItemTemp->buying_price = $saleItemTemp->buying_per_unit*$saleItemTemp->quantity_sold;
                                $saleItemTemp->price_per_unit = $request['price_per_unit'];
                                $saleItemTemp->price = $saleItemTemp->price_per_unit*$saleItemTemp->quantity_sold;
                                $saleItemTemp->discount = 0;
                                $saleItemTemp->used_stock = 'Old';
                                $saleItemTemp->sold_in = 'Retail Price';
                                $saleItemTemp->save();
                                return $saleItemTemp;
                            }
                        }
                    }
                }
            }
        }else{
            $warning = 'Ooops!. The product already in selected items.';
            return response()->json(['status' =>'DUPL', 'msg' => $warning]);
        }
        
    }


    public function ajaxPost(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $sale = AnSale::where('shop_id', $shop->id)->count(); 
        $customers = Customer::where('shop_id', $shop->id)->pluck('name', 'id');
    
        $product = $shop->products()->where('barcode', $request['barcode'])->first();
        if (!is_null($product)) {
            $bunit = ProductUnit::where('shop_id', $shop->id)->where('product_id', $product->id)->where('is_basic', true)->first();
           $itemtemp = SaleItemTemp::where('product_id', $product->pivot->product_id)->where('sale_temp_id', $request['sale_temp_id'])->first();
            if (!is_null($itemtemp)) {
                if (($itemtemp->quantity_sold+1) <= $itemtemp->curr_stock) {
                    
                    $itemtemp->quantity_sold = $itemtemp->quantity_sold+1;
                    $itemtemp->buying_price = $itemtemp->buying_per_unit*$itemtemp->quantity_sold;
                    $itemtemp->price = $itemtemp->price_per_unit*$itemtemp->quantity_sold;
                    $itemtemp->save();
                }else{
                    return response()->json(['status' => 'LOW', 'msg' => 'Stock of Your Product '.$product->name.' is currently less than.'.($itemtemp->quantity_sold+1)]);
                }
            }else{
                if ($product->pivot->in_stock == 0) {

                    return response()->json(['status' => 'ZERO', 'msg' => 'The stock of '.$product->name.' is currently ZERO. Please Purchase new Stock.']);
                } else {
                    if (is_null($product->pivot->buying_per_unit)) {

                        $saleItemTemp = new SaleItemTemp;
                        $saleItemTemp->sale_temp_id = $request['sale_temp_id'];
                        $saleItemTemp->product_id = $product->pivot->product_id;
                        $saleItemTemp->product_unit_id = $bunit->id;
                        $saleItemTemp->quantity_sold = 1;
                        $saleItemTemp->curr_stock = $product->pivot->in_stock;
                        $saleItemTemp->buying_per_unit = 0;
                        $saleItemTemp->buying_price = $saleItemTemp->buying_per_unit*$saleItemTemp->quantity_sold;
                        $saleItemTemp->price_per_unit = $product->pivot->price_per_unit;
                        $saleItemTemp->price = $saleItemTemp->price_per_unit*$saleItemTemp->quantity_sold;
                        $saleItemTemp->discount = 0;
                        $saleItemTemp->used_stock = 'Old';
                        $saleItemTemp->sold_in = 'Retail Price';
                        $saleItemTemp->save();
                        // return $saleItemTemp;
                    }else{
                        $saleItemTemp = new SaleItemTemp;
                        $saleItemTemp->shop_id = $shop->id;
                        $saleItemTemp->user_id = $user->id;
                        $saleItemTemp->product_id = $product->pivot->product_id;
                        $saleItemTemp->product_unit_id = $bunit->id;
                        $saleItemTemp->quantity_sold = 1;
                        $saleItemTemp->curr_stock = $product->pivot->in_stock;
                        $saleItemTemp->buying_per_unit = $product->pivot->buying_per_unit;
                        $saleItemTemp->buying_price = $saleItemTemp->buying_per_unit*$saleItemTemp->quantity_sold;
                        $saleItemTemp->price_per_unit = $product->pivot->price_per_unit;
                        $saleItemTemp->price = $saleItemTemp->price_per_unit*$saleItemTemp->quantity_sold;
                        $saleItemTemp->discount = 0;
                        $saleItemTemp->used_stock = 'Old';
                        $saleItemTemp->sold_in = 'Retail Price';
                        $saleItemTemp->save();
                        // return $saleItemTemp;
                    }
                }
            }
            // return redirect('pos');
            return response()->json(['status' => 'OK']);
        }else{
            $warning = "Sorry, Scanned barcode value does not match any of your products . Please Try Again";
            return response()->json(['status' => 'Fail', 'msg' => $warning]);
            // return redirect('pos')->with('warning', $warning);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\SaleItemTemp  $saleItemTemp
     * @return \Illuminate\Http\Response
     */
    public function show(SaleItemTemp $saleItemTemp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SaleItemTemp  $saleItemTemp
     * @return \Illuminate\Http\Response
     */
    public function edit(SaleItemTemp $saleItemTemp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SaleItemTemp  $saleItemTemp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $saleItemTemp =  SaleItemTemp::find($id);
        $settings = Setting::where('shop_id', $shop->id)->first();
        if (!is_null($saleItemTemp) ) {
            $punit = ProductUnit::find($request['product_unit_id']);
            if ($saleItemTemp->product_unit_id != $punit->id) {
                $saleItemTemp->product_unit_id = $punit->id;
                $saleItemTemp->price_per_unit = $punit->unit_price;
                $saleItemTemp->price = $saleItemTemp->price_per_unit*$saleItemTemp->quantity_sold;
                if ($saleItemTemp->with_vat == 'yes') {
                    $vat_amount =  ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                    $saleItemTemp->vat_amount = $vat_amount;
                }else{
                    $saleItemTemp->vat_amount = 0;
                }
                $saleItemTemp->save();

                return $saleItemTemp;
            }else{
                $saletemp = SaleTemp::find($saleItemTemp->sale_temp_id);
                if (!is_null($request['price_per_unit']) && $request['price_per_unit'] != $saleItemTemp->price_per_unit) {
                    if($saletemp->currency != $saletemp->defcurr){
                        $saleItemTemp->price_per_unit = $request['price_per_unit']/$saletemp->ex_rate;
                    }else{
                        $saleItemTemp->price_per_unit = $request['price_per_unit'];
                    }
                    $saleItemTemp->price = $saleItemTemp->price_per_unit*$saleItemTemp->quantity_sold;
                    $saleItemTemp->save();
                }

                if (!is_null($request['used_stock'])) {
                    if ($request['used_stock'] == 'New') {
                                
                        $lateststock = Stock::where('product_id', $saleItemTemp->product_id)->where('shop_id', $shop->id)->latest()->first();
                        // $saleItemTemp->quantity_sold = ;
                        $saleItemTemp->buying_per_unit = $lateststock->buying_per_unit;
                        $saleItemTemp->buying_price = $saleItemTemp->buying_per_unit*$saleItemTemp->quantity_sold;
                        // $saleItemTemp->discount = $request('discount');
                        $saleItemTemp->total_discount = $saleItemTemp->quantity_sold*$saleItemTemp->discount;
                        $saleItemTemp->price = $saleItemTemp->price_per_unit*$saleItemTemp->quantity_sold;
                        $saleItemTemp->used_stock = $request['used_stock'];
                        $saleItemTemp->save();
                        return $saleItemTemp;
                    }else{
                        $shopproduct = $shop->products()->where('product_id', $saleItemTemp->product_id)->first();
                        // $saleItemTemp->quantity_sold = $request('quantity_sold');
                        $saleItemTemp->buying_per_unit = $shopproduct->pivot->buying_per_unit;
                        $saleItemTemp->buying_price = $saleItemTemp->buying_per_unit*$saleItemTemp->quantity_sold;
                        // $saleItemTemp->discount = $request('discount');
                        $saleItemTemp->total_discount = $saleItemTemp->quantity_sold*$saleItemTemp->discount;
                        $saleItemTemp->price = $saleItemTemp->price_per_unit*$saleItemTemp->quantity_sold;
                        $saleItemTemp->used_stock = $request['used_stock'];
                        $saleItemTemp->save();

                        return $saleItemTemp;
                    }
                }
                    
                if ($request['sold_in'] == $saleItemTemp->sold_in) {
                    if ($request['with_vat'] == $saleItemTemp->with_vat) {
                        if (!is_null($saleItemTemp)) {
                            $qty_sold = $request['quantity_sold'];
                            if ($qty_sold == $saleItemTemp->quantity_sold && $qty_sold != 0) {
                                if($request['discount'] != $saleItemTemp->discount){
                                    if($saletemp->currency != $saletemp->defcurr){
                                        $saleItemTemp->discount = $request['discount']/$saletemp->ex_rate;
                                    }else{
                                        $saleItemTemp->discount = $request['discount'];
                                    }
                                    $saleItemTemp->total_discount = $saleItemTemp->discount*$saleItemTemp->quantity_sold;     
                                    $saleItemTemp->with_vat = $request['with_vat'];
                                    if ($saleItemTemp->with_vat == 'yes') {
                                        $vat_amount =  ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                                        $saleItemTemp->vat_amount = $vat_amount;
                                    }else{
                                        $saleItemTemp->vat_amount = 0;
                                    }
                                                   
                                    $saleItemTemp->save();

                                    return $saleItemTemp;
                                }else{
                                    if($saletemp->currency != $saletemp->defcurr){
                                        $saleItemTemp->total_discount = $request['total_discount']/$saletemp->ex_rate;
                                    }else{
                                        $saleItemTemp->total_discount = $request['total_discount'];
                                    }
                                    $saleItemTemp->discount = $saleItemTemp->total_discount/$saleItemTemp->quantity_sold;    
                                    $saleItemTemp->with_vat = $request['with_vat']; 
                                    if ($saleItemTemp->with_vat == 'yes') {
                                        $vat_amount =  ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                                        $saleItemTemp->vat_amount = $vat_amount;
                                    }else{
                                        $saleItemTemp->vat_amount = 0;
                                    }
                                    $saleItemTemp->save();
                                    return $saleItemTemp;
                                }       
                            }else{
                                $product = Product::find($saleItemTemp->product_id);
                                $shopproduct = $shop->products()->where('product_id', $product->id)->first();
                                $lateststock = Stock::where('product_id', $product->id)->where('shop_id', $shop->id)->latest()->first();

                                if (!is_null($lateststock) && $shopproduct->pivot->in_stock > $lateststock->quantity_in && $shopproduct->pivot->buying_per_unit != $lateststock->buying_per_unit) {

                                    $saleItemTemp->quantity_sold = $request['quantity_sold'];
                                    $saleItemTemp->buying_price = $saleItemTemp->buying_per_unit*$saleItemTemp->quantity_sold;
                                    $saleItemTemp->total_discount = $saleItemTemp->quantity_sold*$saleItemTemp->discount;
                                    $saleItemTemp->price = $saleItemTemp->price_per_unit*$saleItemTemp->quantity_sold;
                                    $saleItemTemp->with_vat = $request['with_vat'];
                                    if ($saleItemTemp->with_vat == 'yes') {
                                        $vat_amount =  ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                                        $saleItemTemp->vat_amount = $vat_amount;
                                    }else{
                                        $saleItemTemp->vat_amount = 0;
                                    }
                                                
                                    $saleItemTemp->save();
                                    if (!$settings->always_sell_old) {
                                        return response()->json(['status' => 'SHARED', 'msg' => 'This product ('.$product->name.') has stock with different purchase prices. Please select which stock you are currently selling..']);
                                    }
                                }else{
                                    if ($qty_sold > $saleItemTemp->curr_stock) {
                                        return response()->json(['status' => 'LOW', 'msg' => 'Stock of Your Product '.$product->name.' is currently less than.'.$qty_sold]);
                                    }else{

                                        if ($product->basic_unit == 'pcs' || $product->basic_unit == 'prs' || $product->basic_unit == 'box' || $product->basic_unit == 'btl' || $product->basic_unit == 'pks' || $product->basic_unit == 'gls') {
                                            if (!$this->is_decimal($request['quantity_sold'])) {
                                                $saleItemTemp->quantity_sold = $request['quantity_sold'];
                                                $saleItemTemp->buying_price = $saleItemTemp->buying_per_unit*$saleItemTemp->quantity_sold;
                                                $saleItemTemp->total_discount = $saleItemTemp->quantity_sold*$saleItemTemp->discount;
                                                $saleItemTemp->price = $saleItemTemp->price_per_unit*$saleItemTemp->quantity_sold;
                                                    
                                                $saleItemTemp->with_vat = $request['with_vat'];
                                                if ($saleItemTemp->with_vat == 'yes') {
                                                    $vat_amount =  ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                                                    $saleItemTemp->vat_amount = $vat_amount;
                                                }else{
                                                    $saleItemTemp->vat_amount = 0;
                                                }
                                                    
                                                $saleItemTemp->save();
                                                return $saleItemTemp;
                                            } else{
                                                return response()->json(['status' => 'WRONG', 'msg' => 'This product '.$product->name.' can not accept decimal quantity. Please change its basic unit if you want to set decimal for stock quantity values']);
                                            }            
                                        }else{
                                            $saleItemTemp->quantity_sold = $request['quantity_sold'];
                                            $saleItemTemp->buying_price = $saleItemTemp->buying_per_unit*$saleItemTemp->quantity_sold;
                                            $saleItemTemp->total_discount = $saleItemTemp->quantity_sold*$saleItemTemp->discount;
                                            $saleItemTemp->price = $saleItemTemp->price_per_unit*$saleItemTemp->quantity_sold;
                                            $saleItemTemp->with_vat = $request['with_vat'];
                                            if ($saleItemTemp->with_vat == 'yes') {
                                                $vat_amount =  ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                                                $saleItemTemp->vat_amount = $vat_amount;
                                            }else{
                                                $saleItemTemp->vat_amount = 0;
                                            }
                                            $saleItemTemp->save();
                                            return $saleItemTemp;
                                        }   
                                    }
                                }
                            }   
                        }
                    }else{
                        $saleItemTemp->with_vat = $request['with_vat'];
                        if ($saleItemTemp->with_vat == 'yes') {
                            $vat_amount =  ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                            $saleItemTemp->vat_amount = $vat_amount;
                        }else{
                            $saleItemTemp->vat_amount = 0;
                        }
                        $saleItemTemp->save();
                        return $saleItemTemp;
                    }

                }else{
                    $myproduct = $shop->products()->where('product_id', $saleItemTemp->product_id)->first();
                    if ($request['sold_in'] == 'Retail Price') {
                        $saleItemTemp->price_per_unit = $myproduct->pivot->price_per_unit;
                        $saleItemTemp->price = $saleItemTemp->price_per_unit*$saleItemTemp->quantity_sold;
                        $saleItemTemp->sold_in = $request['sold_in'];
                        $saleItemTemp->save();
                        return $saleItemTemp;
                    }else{
                        if ($myproduct->pivot->wholesale_price != 0 && !is_null($myproduct->pivot->wholesale_price)) {
                            
                            $saleItemTemp->price_per_unit = $myproduct->pivot->wholesale_price;
                            $saleItemTemp->price = $saleItemTemp->price_per_unit*$saleItemTemp->quantity_sold;
                            $saleItemTemp->sold_in = $request['sold_in'];
                            $saleItemTemp->save();

                            return $saleItemTemp;
                        }
                    }
                }
            }
        }
    }

    function is_decimal($val)
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SaleItemTemp  $saleItemTemp
     * @return \Illuminate\Http\Response
     */
    public function updateDiscount($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $saleItemTemp =  SaleItemTemp::find($id);
        
        if (!is_null($saleItemTemp)) {
         
            $saleItemTemp->quantity_sold = $request['quantity_sold'];
            $saleItemTemp->buying_price = $request['buying_price'];
            $saleItemTemp->discount = $request['discount'];
            $saleItemTemp->price = $request['price'];        
            $saleItemTemp->save();

            return $saleItemTemp;
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SaleItemTemp  $saleItemTemp
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        SaleItemTemp::destroy($id);
    }

    public function selectedCustomer(Request $request)
    {
        // Log::info($request->cust_id);
        Session::forget('cust_id');

        $customer = Customer::find($request->cust_id);
        // Log::info($customer);
        Session::put('cust_id', $customer->id);
    }
}
