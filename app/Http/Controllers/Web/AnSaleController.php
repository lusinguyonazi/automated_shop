<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \GuzzleHttp\Client;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\User;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\ServiceSaleItem;
use App\Models\TransferOrderItem;
use App\Models\SaleReturnItem;
use App\Models\Customer;
use App\Models\Stock;
use App\Models\ProdDamage;
use App\Models\SalePayment;
use App\Models\ActionHistory;
use App\Models\Setting;
use App\Models\Device;
use App\Models\DeviceSale;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ServiceCharge;
use App\Models\CustomerAccount;
use App\Models\SmsAccount;
use App\Models\BankDetail;
use App\Models\CustomerTransaction;
use App\Models\EfdmsRctInfo;
use App\Models\EfdmsRegInfo;
use App\Models\EfdmsRctItem;
use App\Models\EfdmsZReport;
use App\Models\EfdmsRctPayment;
use App\Models\EfdmsRctVatTotal;
use App\Models\Taxcode;
use Illuminate\Support\Facades\Log;

class AnSaleController extends Controller
{
    private $shop;
    private $start;
    private $end;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->shop = Shop::find(Session::get('shop_id'));
        if (!is_null($this->shop)) {
            if ($this->shop->subscription_type_id == 2) {
                $lastpay = Payment::where('shop_id', $this->shop->id)->where('amount_paid', '>', 0)->where('status', 'Activated')->where('is_for_module', false)->latest()->first();

                $premserv = ServiceCharge::where('type', 2)->where('duration', 'Monthly')->first();
                if (!is_null($lastpay)) {
                    if ($lastpay->amount_paid % $premserv->initial_pay == 0 && $lastpay->subscr_type == 2) {
                        return $this->getSales($request, $this->shop);
                    }else{
                        $wrn_en = 'You have Upgraded your account  to have PREMIUM subscription but no any payments verified after this changes.Please make payment for any amount for PREMIUM subscription as shown in table below then enter your verification code here inorder to continue using our service. Thank you for using SmartMauzo service.';
                        $wrn_sw = 'Umeboresha akaunti yako kuwa na usajili wa PREMIUM lakini hakuna malipo yoyote yaliyothibitishwa baada ya mabadiliko haya. Tafadhali fanya malipo kwa kiasi chochote cha usajili wa PREMIUM kama inavyoonyeshwa kwenye jedwali hapa chini kisha ingiza nambari yako ya uthibitishaji hapa ili kuendelea kutumia huduma yetu. Asante kwa kutumia huduma ya SmartMauzo.';
                        if (app()->getLocale() == 'en') {
                            return redirect('verify-payment')->with('info', $wrn_en);
                        }else{
                            return redirect('verify-payment')->with('info', $wrn_sw);
                        }
                    }
                }else{
                    return $this->getSales($request, $this->shop);
                }
            }else{
                return $this->getSales($request, $this->shop);
            }
        }else{
            return redirect('unauthorized');
        }
    }

    public function getSales($request, $shop)
    {
        $user = Auth::user();
        $settings = Setting::where('shop_id', $this->shop->id)->first();
        
        $bdetails = BankDetail::where('shop_id', $shop->id)->get();
        $customers = Customer::where('shop_id', $this->shop->id)->get();            
        $users = $this->shop->users()->get();
        
        $expired = Session::get('expired');
        $now = Carbon::now();
        $this->start = null;
        $this->end = null;
        $start_date = null;            
        $end_date = null;
          
        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['sale_date'])) {
            $start_date = $request['sale_date'];
            $end_date = $request['sale_date'];
            $this->start = $request['sale_date'].' 00:00:00';
            $this->end = $request['sale_date'].' 23:59:59';
            $is_post_query = true;
        }else if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $this->start = $request['start_date'].' 00:00:00';
            $this->end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $this->start = $now->startOfDay();
            $this->end = \Carbon\Carbon::now();
            $start_date = date('Y-m-d', strtotime($this->start));
            $end_date = date('Y-m-d', strtotime($this->end));
            $is_post_query = false;
        }
        $sales = null; $cashsales = null; $creditsales = null;
        $customer = null;

        if (Auth::user()->hasRole('manager') || Auth::user()->can('manage_sales')) {
            $sales = AnSale::where('an_sales.shop_id', $this->shop->id)->where('is_deleted', false)->whereBetween('an_sales.time_created', [$this->start, $this->end])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.adjustment as adjustment', 'an_sales.time_created as time_created', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.pay_type as pay_type', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'an_sales.sale_type as sale_type', 'an_sales.comments as comments', 'users.first_name as first_name', 'an_sales.grade_id as grade_id', 'an_sales.year as year')->orderBy('an_sales.time_created', 'desc')->get();


            $cashsales = AnSale::where('an_sales.shop_id', $this->shop->id)->where('is_deleted', false)->whereBetween('an_sales.time_created', [$this->start, $this->end])->where('an_sales.status', 'Paid')->orWhere(function($query){
                $query->where('an_sales.shop_id', $this->shop->id)->whereBetween('an_sales.time_created', [$this->start, $this->end])->where('an_sales.status', 'Excess Paid');
            })->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.adjustment as adjustment', 'an_sales.time_created as time_created', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.pay_type as pay_type', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'an_sales.sale_type as sale_type', 'an_sales.comments as comments', 'users.first_name as first_name', 'an_sales.grade_id as grade_id', 'an_sales.year as year')->orderBy('an_sales.time_created', 'desc')->get();

            $creditsales = AnSale::where('an_sales.shop_id', $this->shop->id)->where('is_deleted', false)->whereBetween('an_sales.time_created', [$this->start, $this->end])->where('an_sales.status', 'Unpaid')->orWhere(function($query){
                $query->where('an_sales.shop_id', $this->shop->id)->whereBetween('an_sales.time_created', [$this->start, $this->end])->where('an_sales.status', 'Partially Paid');
            })->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.adjustment as adjustment', 'an_sales.time_created as time_created', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.pay_type as pay_type', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'an_sales.sale_type as sale_type', 'an_sales.comments as comments', 'users.first_name as first_name', 'an_sales.grade_id as grade_id', 'an_sales.year as year')->orderBy('an_sales.time_created', 'desc')->get();
        }else{
            $sales = AnSale::where('an_sales.shop_id', $this->shop->id)->where('is_deleted', false)->where('an_sales.user_id', $user->id)->whereBetween('an_sales.time_created', [$this->start, $this->end])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('users.first_name as first_name', 'customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.adjustment as adjustment', 'an_sales.time_created as time_created', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.pay_type as pay_type', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'an_sales.sale_type as sale_type', 'an_sales.comments as comments', 'an_sales.grade_id as grade_id', 'an_sales.year as year')->orderBy('an_sales.time_created', 'desc')->get();

            $cashsales = AnSale::where('an_sales.shop_id', $this->shop->id)->where('is_deleted', false)->where('an_sales.user_id', $user->id)->whereBetween('an_sales.time_created', [$this->start, $this->end])->where('an_sales.status', 'Paid')->orWhere('an_sales.status', 'Excess Paid')->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('users.first_name as first_name', 'customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.adjustment as adjustment', 'an_sales.time_created as time_created', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.pay_type as pay_type', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'an_sales.sale_type as sale_type', 'an_sales.comments as comments', 'an_sales.grade_id as grade_id', 'an_sales.year as year')->orderBy('an_sales.time_created', 'desc')->get();

            $creditsales = AnSale::where('an_sales.shop_id', $this->shop->id)->where('is_deleted', false)->whereBetween('an_sales.time_created', [$this->start, $this->end])->where('an_sales.status', 'Unpaid')->orWhere(function($query){
                $query->where('an_sales.shop_id', $this->shop->id)->whereBetween('an_sales.time_created', [$this->start, $this->end])->where('an_sales.status', 'Partially Paid');
            })->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('users.first_name as first_name', 'customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.adjustment as adjustment', 'an_sales.time_created as time_created', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.pay_type as pay_type', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'an_sales.sale_type as sale_type', 'an_sales.comments as comments', 'an_sales.grade_id as grade_id', 'an_sales.year as year')->orderBy('an_sales.time_created', 'desc')->get();
                
        }
        
        $nullsaleno = AnSale::where('shop_id', $this->shop->id)->where('sale_no', null)->count();
        if ($nullsaleno > 0) {       
            $mysales = AnSale::where('shop_id', $this->shop->id)->get();
            foreach ($mysales as $key => $sale) {
                $sale->sale_no = $key+1;
                $sale->save();
            }
        }

        $page = 'Sales';
        $title = 'My Sales';
        $title_sw = 'Mauzo Yangu';
        if ($expired == 0) {
            $shop = $this->shop;
            return view('sales.index', compact('page', 'title', 'title_sw', 'sales', 'cashsales', 'creditsales', 'customers', 'customer', 'settings', 'shop', 'start_date', 'end_date', 'is_post_query', 'bdetails'));
        } else {
            $info = 'Dear customer your account is not activated please make payment and activate now.';
            return redirect('verify-payment')->with('info', $info);
        }
            
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $products = $shop->products()->get();
        $services = $shop->services()->get();
        $sale = AnSale::where('an_sales.id', decrypt($id))->where('an_sales.shop_id', $shop->id)->join('customers', 'an_sales.customer_id', '=', 'customers.id')->select('an_sales.id as id', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount', 'an_sales.adjustment as adjustment', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.tax_amount as tax_amount', 'an_sales.pay_type as pay_type', 'an_sales.status as status', 'an_sales.comments as comments', 'customers.id as customer_id',  'customers.name as name', 'an_sales.time_created as time_created', 'an_sales.created_at as created_at')->first();
        if (is_null($sale)) {
            return redirect('forbiden');
        }else{

            $page = 'Sale items';
            $title = "Sale details";
            $title_sw = 'Maelezo ya Uzo';
            $bdetails = BankDetail::where('shop_id', $shop->id)->get();
            $payments = SalePayment::where('an_sale_id', $sale->id)->get();
            $total_paid = SalePayment::where('an_sale_id', $sale->id)->sum('amount');

            $serv_items = ServiceSaleItem::where('an_sale_id', $sale->id)->join('services', 'services.id', '=', 'service_sale_items.service_id')->select('services.id as s_id', 'services.name as name', 'service_sale_items.service_id as service_id', 'service_sale_items.id as id', 'service_sale_items.no_of_repeatition as no_of_repeatition', 'service_sale_items.price as price', 'service_sale_items.discount as discount', 'service_sale_items.total_discount as total_discount', 'service_sale_items.total as total','service_sale_items.tax_amount as tax_amount', 'service_sale_items.time_created as created_at')->orderBy('service_sale_items.time_created', 'desc')->get();
                
            $sale_items = AnSaleItem::where('an_sale_id', $sale->id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->select('products.id as p_id', 'products.name as name', 'products.basic_unit as basic_unit', 'an_sale_items.product_id as product_id', 'an_sale_items.id as id', 'an_sale_items.quantity_sold as quantity_sold', 'an_sale_items.buying_per_unit as buying_per_unit', 'an_sale_items.buying_price as buying_price', 'an_sale_items.price_per_unit as price_per_unit', 'an_sale_items.discount as discount', 'an_sale_items.price as price', 'an_sale_items.total_discount as total_discount', 'an_sale_items.tax_amount as tax_amount', 'an_sale_items.time_created as created_at')->orderBy('an_sale_items.time_created', 'desc')->get();

            return view('sales.show', compact('page', 'title', 'title_sw', 'serv_items', 'sale_items', 'sale', 'shop', 'payments', 'total_paid', 'settings', 'bdetails', 'products', 'services'));
        }
    }

    public function printReceipt($id)
    {
        
        $page = 'Sale Receipt';
        $title = 'Normal Receipt';
        $title_sw = 'Risiti ys Kawaida';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $sale = AnSale::findOrFail(decrypt($id));
        $customer = Customer::find($sale->customer_id);
        $items = AnSaleItem::where('an_sale_id',  $sale->id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->get();
        $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->join('services', 'services.id', '=', 'service_sale_items.service_id')->get();
        $date = date("d, M Y H:i:sA", strtotime($sale->time_created));

        $prev_shop_sales = AnSale::where('shop_id', $shop->id)->where('id', '<', $sale->id)->count();
        $recno = $prev_shop_sales+1;


        return view('sales.receipt', compact('page', 'title', 'title_sw', 'customer', 'recno', 'sale', 'items', 'servitems', 'shop', 'date', 'settings'))->render();
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $sale = AnSale::find(decrypt($id));

        if (is_null($sale)) {
            return redirect('forbiden');
        }else{
            $customer = Customer::find($sale->customer_id);
            $customers = Customer::where('shop_id', $shop->id)->get();
            $page = 'Edit sale';
            $title = "Edit sale";
            $title_sw = "Hariri Uzo";

            $settings = Setting::where('shop_id', $shop->id)->first();
            $devices = Device::where('shop_id', $shop->id)->get();
            $dsale = DeviceSale::where('an_sale_id', $sale->id)->first();

            return view('sales.edit', compact('page', 'sale', 'title', 'title_sw', 'customers', 'customer', 'settings', 'devices', 'dsale'));
        }
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
        //
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
        if (Auth::user()->hasRole('manager') ) {
            $sale = AnSale::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
            if (is_null($sale)) {
                return redirect('forbiden');
            }else{
                $items = AnSaleItem::where('an_sale_id', $sale->id)->get();
                if (!is_null($items)) {
                    foreach ($items as $key => $item) {
                        $shop_product = $shop->products()->where('product_id', $item->product_id)->first();

                        $item->is_deleted = true;
                        $item->del_by = Auth::user()->first_name.'('.Carbon::now().')';
                        $item->save();
                        // $item->delete();
                        if (!is_null($shop_product)) {
                                
                            $stock_in = Stock::where('product_id', $shop_product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                            $stock_out = AnSaleItem::where('product_id', $shop_product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                            $damaged = ProdDamage::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                            $tranfered =  TransferOrderItem::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                            $returned = SaleReturnItem::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                                        
                            $instock = ($stock_in+$returned)-($stock_out+$damaged+$tranfered);
                            $shop_product->pivot->in_stock = $instock;
                                
                            $shop_product->pivot->save();
                        }
                    }
                }

                $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->get();
                if (!is_null($servitems)) {
                    foreach ($servitems as $key => $sitem) {
                        $sitem->is_deleted = true;
                        $sitem->del_by = Auth::user()->first_name.'('.Carbon::now().')';
                        $sitem->save();
                        // $sitem->delete();
                    }
                }

                $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                if (!is_null($payments)) {
                    foreach ($payments as $key => $payment) {
                        $payment->is_deleted = true;
                        $payment->save();
                        $acctrans = CustomerTransaction::find($payment->trans_id);
                        if (!is_null($acctrans)) {
                            $acctrans->trans_invoice_amount = $acctrans->trans_invoice_amount-$payment->amount;
                            $acctrans->is_utilized = false;
                            $acctrans->save();
                        }
                    }
                }

                $invoice = Invoice::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                if (!is_null($invoice)) {
                    $acctrans = CustomerTransaction::where('invoice_no', $invoice->inv_no)->where('shop_id', $shop->id)->first();
                    if (!is_null($acctrans)) {
                        $acctrans->is_deleted = true;
                        $acctrans->save();
                        // $acctrans->delete();
                    }
                    $invoice->is_deleted = true;
                    $invoice->save();
                    // $invoice->delete();
                }

                $sale->is_deleted = true;
                $sale->del_by = Auth::user()->first_name.' ('.Carbon::now().')';
                $sale->save();
                // $sale->delete();

                $success = 'Your sale was succesfuly deleted';
                return redirect('an-sales')->with('success', $success);
            }
        }else{
            return redirect('unauthorized');
        }
    }

    public function deleteMultiple(Request $request)
    {
        // dd($request);
        Log::info();
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->hasRole('manager')) {
            // dd($request);
            if (!is_null($request->input('id'))) {
                foreach ($request->input('id') as $key => $id) {
                    $sale = AnSale::where('id', $id)->where('shop_id', $shop->id)->first();
                    // return $id;

                    if (!is_null($sale)) {
                        $items = AnSaleItem::where('an_sale_id', $sale->id)->get();
                        if (!is_null($items)) {
                            foreach ($items as $key => $item) {
                                
                                $shop_product = $shop->products()->where('product_id', $item->product_id)->first();
                                $item->is_deleted = true;
                                $item->del_by = Auth::user()->first_name.' ('.Carbon::now().')';
                                $item->save();
                                // $item->delete();
                                if (!is_null($shop_product)) {
                                     
                                    $stock_in = Stock::where('product_id', $shop_product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                                    $stock_out = AnSaleItem::where('product_id', $shop_product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                                    $damaged = ProdDamage::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                                    $tranfered =  TransferOrderItem::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                                    $returned = SaleReturnItem::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                                                
                                    $instock = ($stock_in+$returned)-($stock_out+$damaged+$tranfered);
                                    $shop_product->pivot->in_stock = $instock;
                                        
                                    $shop_product->pivot->save();
                                }
                            }
                        }

                        $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->get();
                        if (!is_null($servitems)) {
                            foreach ($servitems as $key => $sitem) {
                                $sitem->is_deleted  = true;
                                $sitem->del_by = Auth::user()->first_name.'('.Carbon::now().')';
                                $sitem->save();
                                // $sitem->delete();
                            }
                        }

                        $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                        if (!is_null($payments)) {
                            foreach ($payments as $key => $payment) {
                                $payment->is_deleted = true;
                                $payment->save();
                                $acctrans = CustomerTransaction::find($payment->trans_id);
                                if (!is_null($acctrans)) {
                                    $acctrans->trans_invoice_amount = $acctrans->trans_invoice_amount-$payment->amount;
                                    $acctrans->is_utilized = false;
                                    $acctrans->save();
                                }
                            }
                        }

                        $invoice = Invoice::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                        if (!is_null($invoice)) {
                            $acctrans = CustomerTransaction::where('invoice_no', $invoice->inv_no)->where('shop_id', $shop->id)->first();
                            if (!is_null($acctrans)) {
                                $acctrans->is_deleted = true;
                                $acctrans->save();
                                // $acctrans->delete();
                            }

                            $invoice->is_deleted = true;
                            $invoice->save();
                            // $invoice->delete();
                        }

                        $sale->is_deleted = true;
                        $sale->del_by = Auth::user()->first_name.' ('.Carbon::now().')';
                        $sale->save();
                        // $sale->delete();
                    }
                }
                
                $success = 'Sales were deleted successfully';
                return redirect('an-sales')->with('success', $success);
            }else{
                return redirect('an-sales')->with('info', 'No Sales selected to Delete');
            }
        }else{
            return redirect('unauthorized');
        }
    }

    public function issueVFD($id)
    {
        $sale = AnSale::find(decrypt($id));
        if (!is_null($sale)) {
            $now = Carbon::now();
            $shop = Shop::find(Session::get('shop_id'));
            $reginfo = EfdmsRegInfo::where('shop_id', $shop->id)->first();
            $customer = Customer::find($sale->customer_id);
            $zreport = EfdmsZReport::where('shop_id', $shop->id)->where('status', 'Not Submitted')->first();
            $znum = null;
            if (!is_null($zreport)) {
                $znum = $zreport->znum;
            }else{
                $lastzr_sub = EfdmsZReport::where('shop_id', $shop->id)->latest()->first();
                if (!is_null($lastzr_sub)) {
                    $znum = $lastzr_sub->znum+1;
                }else{
                    $znum = 1;
                }

                $znumber = date('Ymd', strtotime($now));
                $zreport = new EfdmsZReport();
                $zreport->shop_id = $shop->id;
                $zreport->date = $now;
                $zreport->tin = $reginfo->tin;
                $zreport->vrn = $reginfo->vrn;
                $zreport->taxoffice = $reginfo->taxoffice;
                $zreport->regid = $reginfo->regid;
                $zreport->znum = $znum;
                $zreport->znumber = $znumber;
                $zreport->efdserial = $reginfo->serial;
                $zreport->registration_date = date('Y-m-d', strtotime($reginfo->created_at));
                $zreport->simimsi = "WEBAPI";
                $zreport->fwversion = '3.0';
                $zreport->fwchecksum = 'WEBAPI';
                $zreport->save();
            }
            $lastrct = EfdmsRctInfo::where('shop_id', $shop->id)->latest()->first();
            $rctnum = 1;
            if (!is_null($lastrct)) {
                $rctnum = $lastrct->rctnum+1;
            }
            $ldc = EfdmsRctInfo::where('shop_id', $shop->id)->whereDate('created_at', Carbon::today())->count();
            $lgc = EfdmsRctInfo::where('shop_id', $shop->id)->count();


            $taxcode = Taxcode::where('value', $reginfo->taxcode)->first();
            if (!is_null($reginfo)) {
                $rectvnum = $reginfo->receiptcode.''.($lgc+1);
                $rctinfo = new EfdmsRctInfo();
                $rctinfo->shop_id = $shop->id;
                $rctinfo->an_sale_id = $sale->id;
                $rctinfo->efdms_z_report_id = $zreport->id;
                $rctinfo->date = $now;
                $rctinfo->tin = $reginfo->tin;
                $rctinfo->regid = $reginfo->regid;
                $rctinfo->efdserial = $reginfo->serial;
                $rctinfo->custidtype = $customer->cust_id_type;
                $rctinfo->custid = $customer->custid;
                $rctinfo->custname = $customer->name;
                $rctinfo->mobilenum = $customer->phone;
                $rctinfo->rctnum = $rctnum;
                $rctinfo->dc = $ldc+1;
                $rctinfo->gc = $ldc+1;
                $rctinfo->znum = $znum;
                $rctinfo->rctvnum = $rectvnum;
                $rctinfo->total_tax_excl = ($sale->sale_amount-$sale->sale_discount);
                $rctinfo->total_tax_incl = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
                $rctinfo->discount = $sale->sale_discount;
                $rctinfo->save();

                $saleitems = AnSaleItem::where('an_sale_id', $sale->id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->select('an_sale_items.id as id', 'name', 'quantity_sold', 'price', 'total_discount')->get();

                $code_a_netamount = 0; $code_a_taxamount = 0;
                $code_b_netamount = 0; $code_b_taxamount = 0;
                $code_c_netamount = 0; $code_c_taxamount = 0;
                foreach ($saleitems as $key => $item) {
                    $rctitem = new EfdmsRctItem();
                    $rctitem->efdms_rct_info_id = $rctinfo->id;
                    $rctitem->item_code = $item->id;
                    $rctitem->desc = $item->name;
                    $rctitem->qty = $item->quantity_sold;
                    $rctitem->taxcode = $taxcode->id;
                    $rctitem->amt = $item->price+$item->tax_amount;
                    $rctitem->save();

                    $code_a_netamount += ($item->price-$item->total_discount)+$item->tax_amount;
                    $code_a_taxamount += $item->tax_amount;
                }

                $cashpayment = 0;
                $chequepayment = 0;
                $ccardpayment = 0;
                $emoneypayment = 0;
                $invoicepayment = (($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount)-$sale->sale_amount_paid;
                $spayments = SalePayment::where('an_sale_id', $sale->id)->get();
                
                foreach ($spayments as $key => $spay) {
                     if ($spay->pay_mode == 'Cash') {
                        $cashpayment += $spay->amount;
                    }elseif ($spay->pay_mode == 'Bank' || $spay->pay_mode == 'Cheque') {
                        $chequepayment += $spay->amount;
                    }elseif ($spay->pay_mode == 'Mobile Money') {
                        $ccardpayment += $spay->amount;
                    }
                }

                // Payment Types
                $pmttypes = array(
                    ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'CHEQUE',  'pmtamount' => $chequepayment],
                    ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'CCARD', 'pmtamount' => $ccardpayment],
                    ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'CASH', 'pmtamount' => $cashpayment],
                    ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'EMONEY', 'pmtamount' => $emoneypayment],
                    ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'INVOICE', 'pmtamount' => $invoicepayment]
                );

                foreach ($pmttypes as $key => $pmt) {
                    EfdmsRctPayment::create($pmt);
                }

                // VAT Totals
                $vattotals = array(
                    ['efdms_rct_info_id' => $rctinfo->id, 'vatrate' => 'A',  'netamount' => $code_a_netamount, 'taxamount' => $code_a_taxamount],
                    ['efdms_rct_info_id' => $rctinfo->id, 'vatrate' => 'B', 'netamount' => $code_b_netamount, 'taxamount' => $code_b_taxamount],
                    ['efdms_rct_info_id' => $rctinfo->id, 'vatrate' => 'C', 'netamount' => $code_c_netamount, 'taxamount' => $code_c_taxamount]
                );

                foreach ($vattotals as $key => $vatt) {
                    EfdmsRctVatTotal::create($vatt);
                }

                $this->sendReceiptReq($rctinfo);
                // return redirect()->back()->with('success', 'VFD Receipt sent successfully');
                return redirect('vfd-rct-infos')->with('success', 'Your receipt submitted successfully');
            }else{
                return redirect()->back()->with('error', 'Sorry!. Your registration for VFD not Acknowledged yet or Something went wrong please check registration status and try again');
            }
        }
    }

    public function sendReceiptReq($rctinfo)
    {
        $rctitems = EfdmsRctItem::where('efdms_rct_info_id', $rctinfo->id)->get();

        $xmldoc =  "<?xml version='1.0' encoding='UTF-8'?>";
        $efdms_open = "<EFDMS>";
        $efdms_close = "</EFDMS>";
        $efdms_signatureOpen="<EFDMSSIGNATURE>";
        $efdms_signatureClose="</EFDMSSIGNATURE>";

        $rctitemsxmlopen = '<ITEMS>';
        $rctitemsxmlclose = '</ITEMS>'; 
        $xmlitems = '';
        foreach ($rctitems as $key => $rctitem) {
            $xmlitems.= '<ITEM> 
                            <ID>'.$rctitem->item_code.'</ID> 
                            <DESC>'.$rctitem->desc.'</DESC> 
                            <QTY>'.$rctitem->qty.'</QTY> 
                            <TAXCODE>'.$rctitem->taxcode.'</TAXCODE> 
                            <AMT>'.$rctitem->amt.'</AMT> 
                        </ITEM>';
        }

        $rctitemsxml = $rctitemsxmlopen.$xmlitems.$rctitemsxmlclose;

        $xmlpayments = '';
        $rctpayments = EfdmsRctPayment::where('efdms_rct_info_id', $rctinfo->id)->get();
        foreach ($rctpayments as $key => $rctp) {
            $xmlpayments.= '<PMTTYPE>'.$rctp->pmttype.'</PMTTYPE> 
                            <PMTAMOUNT>'.$rctp->pmtamount.'</PMTAMOUNT>';
        }

        $xmlvattotals = '';
        $vattotals = EfdmsRctVatTotal::where('efdms_rct_info_id', $rctinfo->id)->get();
        foreach ($vattotals as $key => $vatt) {
            $xmlvattotals.= '<VATRATE>'.$vatt->vattotals.'</VATRATE> 
                            <NETTAMOUNT>'.$vatt->netamount.'</NETTAMOUNT> 
                            <TAXAMOUNT>'.$vatt->taxamount.'</TAXAMOUNT>';
        }

        $rctxml = '<RCT> 
                        <DATE>'.date('Y-m-d', strtotime($rctinfo->date)).'</DATE> 
                        <TIME>'.date('H:i:s', strtotime($rctinfo->date)).'</TIME> 
                        <TIN>'.$rctinfo->tin.'</TIN> 
                        <REGID>'.$rctinfo->regid.'</REGID> 
                        <EFDSERIAL>'.$rctinfo->efdserial.'</EFDSERIAL> 
                        <CUSTIDTYPE>'.$rctinfo->cust_id_type.'</CUSTIDTYPE> 
                        <CUSTID>'.$rctinfo->custid.'</CUSTID> 
                        <CUSTNAME>'.$rctinfo->custname.'</CUSTNAME> 
                        <MOBILENUM>'.$rctinfo->mobilenum.'</MOBILENUM> 
                        <RCTNUM>'.$rctinfo->rctnum.'</RCTNUM> 
                        <DC>'.$rctinfo->dc.'</DC> 
                        <GC>'.$rctinfo->gc.'</GC> 
                        <ZNUM>'.$rctinfo->znum.'</ZNUM> 
                        <RCTVNUM>'.$rctinfo->rctvnum.'</RCTVNUM>'.$rctitemsxml.'
                        <TOTALS>
                            <TOTALTAXEXCL>'.$rctinfo->total_tax_excl.'</TOTALTAXEXCL> 
                            <TOTALTAXINCL>'.$rctinfo->total_tax_incl.'</TOTALTAXINCL> 
                            <DISCOUNT>'.$rctinfo->discount.'</DISCOUNT> 
                        </TOTALS> 
                        <PAYMENTS>'.$xmlpayments.'</PAYMENTS>
                        <VATTOTALS>'.$xmlvattotals.'</VATTOTALS> 
                    </RCT>';
        $certbase = base64_encode('');
        $rctsignature = base64_encode(hash('sha1', $rctxml));
        $xmlbody = $xmldoc.$efdms_open.$rctxml.$efdms_signatureOpen.$rctsignature.$efdms_signatureClose.$efdms_close;

        $client = new Client();
        $url = 'http://localhost/smartmauzo/public/efdms-rct-ack-infos';

        $createRequest = new \GuzzleHttp\Psr7\Request(
            'POST', 
            $url, 
            [
                'X-CSRF-Token'=> csrf_token(),
                'Content-Type' => 'Application\xml',
                'Cert-Serial' => $certbase,
                'Client' => 'WEBAPI'
            ],
            $xmlbody,
            '1.1'
        );

        $response = $client->sendRequest($createRequest);
        $rctinfo->is_submitted = true;
        $rctinfo->save();
         // configure options
        // $options = [
        //     'headers' => [
        //         'Content-Type' => 'text/xml; charset=UTF8',
        //         'X-CSRF-TOKEN' => csrf_token(),
        //         // 'Content-Type' => 'Application\xml',
        //         'Cert-Serial' => $certbase,
        //         'Client' => 'WEBAPI'
        //     ],
        //     'body' => $xmlbody
        // ];

        // $response = $client->request('POST', $url, $options);

        // return redirect('vfd-rct-infos')->with('success', 'Your receipt submitted successfully');
    }
}
