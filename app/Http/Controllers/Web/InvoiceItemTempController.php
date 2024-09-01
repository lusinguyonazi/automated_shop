<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;
use Response;
use Auth;
use App\Models\Shop;
use App\Models\ProInvoice;
use App\Models\InvoiceItemTemp;
use App\Models\Customer;
use App\Models\User;
use App\Models\Setting;

class InvoiceItemTempController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(InvoiceItemTemp::where('shop_id', Session::get('shop_id'))->where('user_id', Auth::user()->id)->with('product')->get());
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
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $invoice = ProInvoice::where('shop_id', $shop->id)->count(); 
        $customers = Customer::where('shop_id', $shop->id)->pluck('name', 'id');
        $sameitems = InvoiceItemTemp::where('product_id', $request['product_id'])->where('user_id', $user->id)->count();
        
        if ($sameitems == 0) {
            
            $invoiceItemTemp = new InvoiceItemTemp;
            $invoiceItemTemp->shop_id = $shop->id;
            $invoiceItemTemp->user_id = $user->id;
            $invoiceItemTemp->product_id = $request['product_id'];
            $invoiceItemTemp->quantity = 0;
            $invoiceItemTemp->cost_per_unit = $request['cost_per_unit'] == null ? 0 : $request['cost_per_unit'] ;
            $invoiceItemTemp->amount = $invoiceItemTemp->cost_per_unit*$invoiceItemTemp->quantity;
            $invoiceItemTemp->save();

            return $invoiceItemTemp;
        }else{
            $warning = 'Ooops!. The product already in selected items.';

            return redirect()->back()->with('warning', $warning);
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
        $shop = Shop::find(Session::get('shop_id'));
        $invoiceItemTemp =  InvoiceItemTemp::where('id', $id)->where('user_id', Auth::user()->id)->first();
        $settings = Setting::where('shop_id', $shop->id)->first();
        
        if (!is_null($invoiceItemTemp)) {
        
            $invoiceItemTemp->quantity = $request['quantity'];
            $invoiceItemTemp->cost_per_unit = $request['cost_per_unit'];
            $invoiceItemTemp->amount = $request['price'];
            $invoiceItemTemp->with_vat = $request['with_vat'];
            if($invoiceItemTemp->with_vat == 'yes'){
                $invoiceItemTemp->vat_amount = ($invoiceItemTemp->amount) * ($settings->tax_rate / 100);
            }else{
                $invoiceItemTemp->vat_amount = 0;
            }
            $invoiceItemTemp->save();

            return $invoiceItemTemp;   
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
        InvoiceItemTemp::destroy($id);
    }

    public function ajaxPost(Request $request)
    { 
        
        $page = 'Point of Sale';
        $title = 'New Ivoice';
        $title_sw = 'Ankara Mpya';
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $invoice = ProInvoice::where('shop_id', $shop->id)->count(); 
        $customers = Customer::where('shop_id', $shop->id)->pluck('name', 'id');

            $product = $shop->products()->where('barcode', $request['barcode'])->first();
            if ($product->pivot->in_stock < $request['quantity'] || $product->pivot->in_stock == 0) {
                Session::flash('warning', 'Ooops!. The stock of '.$product->name.' is lower than the quantity your trying to sell');

                $settings = Setting::where('shop_id', $shop->id)->first();
                return view('sales.invoices.pos', compact('warning', 'page', 'title', 'title_sw', 'invoice', 'customers', 'settings'));
            } else {
                if (is_null($product->pivot->buying_per_unit)) {

                    $invoiceItemTemp = new InvoiceItemTemp;
                    $invoiceItemTemp->shop_id = $shop->id;
                    $invoiceItemTemp->user_id = $user->id;
                    $invoiceItemTemp->product_id = $product->pivot->product_id;
                    $invoiceItemTemp->quantity = 0;
                    $invoiceItemTemp->curr_stock = $product->pivot->in_stock;
                    $invoiceItemTemp->buying_per_unit = 0;
                    $invoiceItemTemp->buying_cost = $invoiceItemTemp->buying_per_unit*$invoiceItemTemp->quantity;
                    $invoiceItemTemp->cost_per_unit = $product->pivot->cost_per_unit;
                    $invoiceItemTemp->cost = $invoiceItemTemp->cost_per_unit*$invoiceItemTemp->quantity;
                    $invoiceItemTemp->discount = 0;
                    $invoiceItemTemp->save();
                    return $invoiceItemTemp;
                }else{
                    $invoiceItemTemp = new InvoiceItemTemp;
                    $invoiceItemTemp->shop_id = $shop->id;
                    $invoiceItemTemp->user_id = $user->id;
                    $invoiceItemTemp->product_id = $product->pivot->product_id;
                    $invoiceItemTemp->quantity = 0;
                    $invoiceItemTemp->curr_stock = $product->pivot->in_stock;
                    $invoiceItemTemp->buying_per_unit = $product->pivot->buying_per_unit;
                    $invoiceItemTemp->buying_cost = $invoiceItemTemp->buying_per_unit*$invoiceItemTemp->quantity;
                    $invoiceItemTemp->cost_per_unit = $product->pivot->cost_per_unit;
                    $invoiceItemTemp->cost = $invoiceItemTemp->cost_per_unit*$invoiceItemTemp->quantity;
                    $invoiceItemTemp->discount = 0;
                    $invoiceItemTemp->save();
                    return $invoiceItemTemp;
                }
            }
    }
}
