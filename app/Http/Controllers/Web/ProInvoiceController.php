<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Crypt;
use Response;
use Session;
use \Carbon\Carbon;
use Auth;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\Payment;
use App\Models\ProInvoice;
use App\Models\Customer;
use App\Models\InvoiceItem;
use App\Models\InvoiceItemTemp;
use App\Models\InvoiceServitem;
use App\Models\InvoiceServiceItemTemp;
use App\Models\AnSale;
use App\Models\Invoice;
use App\Models\ServiceSaleItem;
use App\Models\CustomerTransaction;
use App\Models\AnSaleItem;
use App\Models\Stock;
use App\Models\ProdDamage;
use App\Models\TransferOrderItem;
use App\Models\Product;
use App\Models\SaleReturnItem;
use App\Models\LatestStockSoldLog;

class ProInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct()
    {
        $this->middleware(['auth']);
    }


    public function index()
    {
        $page = 'Invoices';
        $title = 'Profoma Invoices';
        $title_sw = 'Ankara za Profoma';

        $shop = Shop::find(Session::get('shop_id'));
        $invoices = ProInvoice::where('pro_invoices.shop_id', $shop->id)->join('customers', 'customers.id', '=', 'pro_invoices.customer_id')->select('customers.name as name', 'pro_invoices.id as id', 'pro_invoices.invoice_no as invoice_no', 'pro_invoices.status as status', 'pro_invoices.due_date as due_date', 'pro_invoices.created_at as created_at', 'pro_invoices.updated_at as updated_at')->orderBy('pro_invoices.created_at', 'desc')->get();

        $customer = Customer::where('shop_id', $shop->id)->first();

        $duration = '';
        return view('sales.invoices.pro-invoices.index', compact('page', 'title', 'title_sw', 'invoices', 'customer', 'duration'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'Proforma Invoice';
        $title = 'New Proforma Invoice';
        $title_sw = 'Ankara mpya ya Proforma';

        $shop = Shop::find(Session::get('shop_id'));
        $invoice = ProInvoice::where('shop_id', $shop->id)->count();
        $customers = Customer::where('shop_id', $shop->id)->pluck('name', 'id');
        $settings = Setting::where('shop_id', $shop->id)->first();
        if (is_null($settings)) {
            $settings = Setting::create([
                'shop_id' => $shop->id,
                'tax_rate' => 18,
                'inv_no_type' => 'Automatic'                
            ]);
        }

        $status = null;
        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->first();
        if (!is_null($payment)) {
            $date = Carbon::parse($payment->expire_date);
            $now = Carbon::now();
            $status = $date->diffInDays($now);
        }

        $custids = array(
                ['id' => 1, 'name' => 'TIN'],
                ['id' => 2, 'name' => 'Driving License'],
                ['id' => 3, 'name' => 'Voters Number'],
                ['id' => 4, 'name' => 'Passport'],
                ['id' => 5, 'name' => 'NID'],
                ['id' => 6, 'name' => 'NIL'],
                ['id' => 7, 'name' => 'Meter No']
            );
        

        if ($shop->business_type_id == 3) {
            return view('sales.invoices.pro-invoices.service-pos', compact('page', 'title', 'title_sw', 'invoice', 'customers', 'settings', 'payment', 'status' , 'custids'));    
        } elseif ($shop->business_type_id == 4) {
            return view('sales.invoices.pro-invoices.both-pos', compact('page', 'title', 'title_sw', 'invoice', 'customers', 'settings', 'payment', 'status', 'custids'));
        }else{
            return view('sales.invoices.pro-invoices.pos', compact('page', 'title', 'title_sw', 'invoice', 'customers', 'settings', 'payment', 'status' , 'custids'));
        }

        
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
        $now = null;
        if (is_null($request['invoice_date'])) {
            $now = Carbon::now();
        }else{
            $now = $request['invoice_date'].' 16:00:00';
        }
        

        if ($shop->business_type_id == 3) {
            
            $invoiceitems = InvoiceServiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id )->get();
            
            if (!is_null($invoiceitems)) {

                $max_no = ProInvoice::where('shop_id', $shop->id)->orderBy('invoice_no', 'desc')->first();
                $invoice_no = 0;
                if (!is_null($max_no)) {
                    $invoice_no = $max_no->invoice_no+1;
                }else{
                    $invoice_no = 1;
                }
                $invoice = ProInvoice::create([
                    'customer_id' => $request['customer_id'],
                    'shop_id' => $shop->id,
                    'user_id' => $user->id,
                    'invoice_no' => $invoice_no,
                    'summary' => $request['summary'],
                    'due_date' => $request['due_date'],
                    'discount' => $request['discount'],
                    'shipping_cost' => $request['shipping_cost'],
                    'adjustment' => $request['adjustment'],
                    'notice' => $request['notice'],
                    'status' => 'Pending',
                    'terms_and_conditions' => $request['terms_and_conditions'],
                    'time_created' => $now,
                ]);

                foreach ($invoiceitems as $key => $value) {
                    $invoiceitemData = new InvoiceServitem;
                    $invoiceitemData->pro_invoice_id = $invoice->id;
                    $invoiceitemData->service_id = $value->service_id;
                    $invoiceitemData->repeatition = $value->repeatition;
                    $invoiceitemData->cost_per_unit = $value->cost_per_unit;
                    $invoiceitemData->amount = $value->amount;
                    $invoiceitemData->tax_amount = $value->vat_amount;
                    $invoiceitemData->time_created = $now;
                    $invoiceitemData->save();
                }

                 $temp_items = InvoiceServiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
                foreach ($temp_items as $key => $item) {
                    $item->delete();
                }
                return redirect('pro-invoices')->with('success', 'Your Data was submitted successfully');

            }else{
                return redirect()->back()->with('warning', 'Please Select at least one Product to continue!.');
            }
        }elseif ($shop->business_type_id == 4) {
            $servitems = InvoiceServiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
            $proditems = InvoiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
            
            if (!is_null($servitems) || !is_null($proditems)) {

                $invoice = ProInvoice::create([
                    'customer_id' => $request['customer_id'],
                    'shop_id' => $shop->id,
                    'user_id' => $user->id,
                    'summary' => $request['summary'],
                    'due_date' => $request['due_date'],
                    'discount' => $request['discount'],
                    'shipping_cost' => $request['shipping_cost'],
                    'adjustment' => $request['adjustment'],
                    'notice' => $request['notice'],
                    'status' => 'Pending',
                    'terms_and_conditions' => $request['terms_and_conditions'],
                    'time_created' => $now,
                ]);

                if ($invoice) {
                    $counti = ProInvoice::where('shop_id', $shop->id)->where('id', '<', $invoice->id)->count();
                    $invoiceno = $counti+1;
                    $invoice->invoice_no = $invoiceno;
                    $invoice->save();
                }

                if (!is_null($servitems)) {
                 
                    foreach ($servitems as $key => $value) {
                        $shop_service = $shop->services()->where('service_id', $value->service_id)->first();
                        $servitemData = new InvoiceServitem;
                        $servitemData->pro_invoice_id = $invoice->id;
                        $servitemData->service_id = $value->service_id;
                        $servitemData->repeatition = $value->repeatition;
                        $servitemData->cost_per_unit = $value->cost_per_unit;
                        $servitemData->amount = $value->amount;
                        $servitemData->tax_amount = $value->vat_amount;
                        $servitemData->time_created = $now;
                        $servitemData->save();
                    }
                }

                if (!is_null($proditems)) {
                    $temps = array();
                    $valid = array();
                    foreach ($proditems as $key => $value) {
                        $shop_product = $shop->products()->where('product_id', $value->product_id)->first();
                        if (is_null($shop_product)) {
                            array_push($valid, $key+1);
                        }
                        if ($value->quantity == 0) {
                            array_push($temps, $value->quantity);
                        }
                    }

                    if (!empty($temps)) {
                        return redirect()->back()->with('warning', 'Please update the quantity of each item to continue');
                    }else if(!empty($valid)){
                        return redirect()->back()->with('warning', 'You have selected Product/Products which are not registered for this shop. Please review your products and try again.');
                    }else{

                        foreach ($proditems as $key => $value) {

                            $invoiceitemData = new InvoiceItem;
                            $invoiceitemData->pro_invoice_id = $invoice->id;
                            $invoiceitemData->product_id = $value->product_id;
                            $invoiceitemData->quantity = $value->quantity;
                            $invoiceitemData->cost_per_unit = $value->cost_per_unit;
                            $invoiceitemData->amount = $value->amount;
                            $invoiceitemData->tax_amount = $value->vat_amount;
                            $invoiceitemData->time_created = $now;
                            $invoiceitemData->save();
                        }
                    }
                }
                    
                $temp_serv_items = InvoiceServiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
                foreach ($temp_serv_items as $key => $item) {
                    $item->delete();
                }$temp_items = InvoiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
                foreach ($temp_items as $key => $item) {
                    $item->delete();
                }
                return redirect('pro-invoices')->with('success', 'Your Data was submitted successfully');

            }else{
                return redirect()->back()->with('warning', 'Please Select at least one Product to continue!.');
            }
        }else{
            $invoiceitems = InvoiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
            
            if (!is_null($invoiceitems)) {
                $temps = array();
                $valid = array();
                foreach ($invoiceitems as $key => $value) {
                    $shop_product = $shop->products()->where('product_id', $value->product_id)->first();
                    if (is_null($shop_product)) {
                        array_push($valid, $key+1);
                    }
                    if ($value->quantity == 0) {
                        array_push($temps, $value->quantity);
                    }
                }

                if (!empty($temps)) {
                    return redirect()->back()->with('warning', 'Please update the quantity of each item to continue');
                }else if(!empty($valid)){
                    return redirect()->back()->with('warning', 'You have selected Product/Products which are not registered for this shop. Please review your products and try again.');
                }else{
                
                    $invoice = ProInvoice::create([
                        'customer_id' => $request['customer_id'],
                        'shop_id' => $shop->id,
                        'user_id' => $user->id,
                        'summary' => $request['summary'],
                        'due_date' => $request['due_date'],
                        'discount' => $request['discount'],
                        'shipping_cost' => $request['shipping_cost'],
                        'adjustment' => $request['adjustment'],
                        'notice' => $request['notice'],
                        'status' => 'Pending',
                        'terms_and_conditions' => $request['terms_and_conditions'],
                        'time_created' => $now,
                    ]);

                    if ($invoice) {
                        $counti = ProInvoice::where('shop_id', $shop->id)->where('id', '<', $invoice->id)->count();
                        $invoiceno = $counti+1;
                        $invoice->invoice_no = $invoiceno;
                        $invoice->save();
                    }

                    foreach ($invoiceitems as $key => $value) {
                            
                        $invoiceitemData = new InvoiceItem;
                        $invoiceitemData->pro_invoice_id = $invoice->id;
                        $invoiceitemData->product_id = $value->product_id;
                        $invoiceitemData->quantity = $value->quantity;
                        $invoiceitemData->cost_per_unit = $value->cost_per_unit;
                        $invoiceitemData->amount = $value->amount;
                        $invoiceitemData->tax_amount = $value->vat_amount;
                        $invoiceitemData->time_created = $now;
                        $invoiceitemData->save();
                    }

                    $temp_items = InvoiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
                    foreach ($temp_items as $key => $item) {
                        $item->delete();
                    }
                    return redirect('pro-invoices')->with('success', 'Your Data was submitted successfully');
                }
            }else{
                return redirect()->back()->with('warning', 'Please Select at least one Product to continue!.');
            }
        }
    }

    public function cancel()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();

        if ($shop->business_type_id == 3) {
            $temp_items = InvoiceServiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
            foreach ($temp_items as $key => $item) {
                $item->delete();
            }
        }elseif ($shop->business_type_id == 4) {
            $temp_serv_items = InvoiceServiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
            foreach ($temp_serv_items as $key => $item) {
                $item->delete();
            }$temp_items = InvoiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
            foreach ($temp_items as $key => $item) {
                $item->delete();
            }
        }else{
            $temp_items = InvoiceItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
            foreach ($temp_items as $key => $item) {
                $item->delete();
            }
        }

        $success = 'Invoice creation was successfully canceled.';
        return redirect('pro-invoices')->with('success', '$success');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = 'Invoice';
        $title = 'Invoice Details';
        $title_sw = 'Maelezo ya Ankara';

        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $user = Auth::user();
        $invoice = ProInvoice::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        $customer = Customer::find($invoice->customer_id);
        if (is_null($invoice)) {
            return redirect('forbiden');
        }else{

            $items = null;
            $servitems = null;
            $grandtotal = 0;
            $tax = 0;

            if ($shop->business_type_id === 3) {
                $servitems = InvoiceServitem::where('pro_invoice_id', $invoice->id)->join('services', 'services.id', '=', 'invoice_servitems.service_id')->join('service_shop', 'service_shop.service_id', '=', 'services.id')->where('service_shop.shop_id', $shop->id)->select('services.id as serv_id', 'services.name as name', 'service_shop.description as description', 'service_shop.price as price', 'invoice_servitems.id as id', 'invoice_servitems.repeatition as repeatition', 'invoice_servitems.cost_per_unit as cost_per_unit', 'invoice_servitems.amount as amount', 'invoice_servitems.time_created as time_created' , 'invoice_servitems.tax_amount as tax_amount')->get();
                

                $grandtotal = 0;
                $tax = 0;
                foreach ($servitems as $key => $item) {
                    $grandtotal += $item->amount;
                    $tax += $item->tax_amount;    
                }

                $subtotal = $grandtotal;
                

            }elseif ($shop->business_type_id === 4) {
                $servitems = InvoiceServitem::where('pro_invoice_id', $invoice->id)->join('services', 'services.id', '=', 'invoice_servitems.service_id')->join('service_shop', 'service_shop.service_id', '=', 'services.id')->where('service_shop.shop_id', $shop->id)->select('services.id as serv_id', 'services.name as name', 'service_shop.description as description', 'service_shop.price as price', 'invoice_servitems.id as id', 'invoice_servitems.repeatition as repeatition', 'invoice_servitems.cost_per_unit as cost_per_unit', 'invoice_servitems.amount as amount', 'invoice_servitems.time_created as time_created' , 'invoice_servitems.tax_amount as tax_amount')->get();

                $items = InvoiceItem::where('pro_invoice_id', $invoice->id)->join('products', 'products.id', '=', 'invoice_items.product_id')->join('product_shop', 'product_shop.product_id', '=', 'products.id')->where('product_shop.shop_id', $shop->id)->select('products.id as prod_id', 'products.name as name', 'product_shop.description as description', 'product_shop.price_per_unit as price_per_unit', 'invoice_items.id as id', 'invoice_items.quantity as quantity', 'invoice_items.cost_per_unit as cost_per_unit', 'invoice_items.amount as amount', 'invoice_items.time_created as time_created','invoice_items.tax_amount as tax_amount')->get();
                    

                $grandtotal1 = 0;
                $tax1 = 0;
                foreach ($servitems as $key => $item) {
                    $grandtotal1 += $item->amount;
                    $tax1 += $item->tax_amount;    
                }

                $grandtotal2 = 0;
                $tax2 = 0;
                foreach ($items as $key => $item) {
                    $grandtotal2 += $item->amount;
                    $tax2 += $item->tax_amount;    
                }

                $grandtotal = $grandtotal1+$grandtotal2;
                $tax = ($tax1+$tax2);
                $subtotal = $grandtotal;

            }else{

                $items = InvoiceItem::where('pro_invoice_id', $invoice->id)->join('products', 'products.id', '=', 'invoice_items.product_id')->join('product_shop', 'product_shop.product_id', '=', 'products.id')->where('product_shop.shop_id', $shop->id)->select('products.id as prod_id', 'products.name as name', 'product_shop.description as description', 'product_shop.price_per_unit as price_per_unit', 'invoice_items.id as id', 'invoice_items.quantity as quantity', 'invoice_items.cost_per_unit as cost_per_unit', 'invoice_items.amount as amount', 'invoice_items.time_created as time_created' ,'invoice_items.tax_amount as tax_amount')->get();
                
                $grandtotal = 0;
                $tax = 0;
                foreach ($items as $key => $item) {
                    $grandtotal += $item->amount;
                    $tax += $item->tax_amount;    
                }

                $subtotal = $grandtotal;
            }

            return view('sales.invoices.pro-invoices.show', compact('page', 'title', 'title_sw', 'invoice', 'customer', 'items', 'servitems', 'grandtotal', 'tax', 'subtotal', 'shop' , 'settings'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         $page = 'Invoice';
        $title = 'Update Invoice Details';
        $title_sw = 'Hariri Maelezo ya Ankara';

        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $invoice = ProInvoice::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        $customer = Customer::find($invoice->customer_id);

        $customers = Customer::where('shop_id', $shop->id)->pluck('name', 'id');

        $custids = array(
                ['id' => 1, 'name' => 'TIN'],
                ['id' => 2, 'name' => 'Driving License'],
                ['id' => 3, 'name' => 'Voters Number'],
                ['id' => 4, 'name' => 'Passport'],
                ['id' => 5, 'name' => 'NID'],
                ['id' => 6, 'name' => 'NIL'],
                ['id' => 7, 'name' => 'Meter No']
            );
        
        if (is_null($invoice)) {
            return redirect('forbiden');
        }else{

            $items = null;
            $servitems = null;

            if ($shop->business_type_id === 3) {
                $servitems = InvoiceServitem::where('pro_invoice_id', $invoice->id)->join('services', 'services.id', '=', 'invoice_servitems.service_id')->join('service_shop', 'service_shop.service_id', '=', 'services.id')->where('service_shop.shop_id', $shop->id)->select('services.id as serv_id', 'services.name as name', 'service_shop.description as description', 'service_shop.price as price', 'invoice_servitems.id as id', 'invoice_servitems.repeatition as repeatition', 'invoice_servitems.cost_per_unit as cost_per_unit', 'invoice_servitems.amount as amount', 'invoice_servitems.time_created as time_created')->get();
                

                $grandtotal = 0;
                $tax = 0;
                foreach ($servitems as $key => $item) {
                    $grandtotal += $item->amount;
                    $tax += ($item->cost_per_unit-$item->price)*$item->repeatition;    
                }

                $subtotal = $grandtotal-$tax;

                $services = $shop->services()->get([
                    \DB::raw('service_id as id'),
                    \DB::raw('name'),
                    \DB::raw('price')
                ]);
                
                return view('sales.invoices.pro-invoices.edit', compact('page', 'title', 'title_sw', 'invoice', 'customer', 'customers', 'servitems', 'shop', 'services' ,'subtotal' , 'tax' , 'grandtotal', 'custids' ));
            }elseif ($shop->business_type_id === 4) {
                $servitems = InvoiceServitem::where('pro_invoice_id', $invoice->id)->join('services', 'services.id', '=', 'invoice_servitems.service_id')->join('service_shop', 'service_shop.service_id', '=', 'services.id')->where('service_shop.shop_id', $shop->id)->select('services.id as serv_id', 'services.name as name', 'service_shop.description as description', 'service_shop.price as price', 'invoice_servitems.id as id', 'invoice_servitems.repeatition as repeatition', 'invoice_servitems.cost_per_unit as cost_per_unit', 'invoice_servitems.amount as amount', 'invoice_servitems.time_created as time_created')->get();

                $items = InvoiceItem::where('pro_invoice_id', $invoice->id)->join('products', 'products.id', '=', 'invoice_items.product_id')->join('product_shop', 'product_shop.product_id', '=', 'products.id')->where('product_shop.shop_id', $shop->id)->select('products.id as prod_id', 'products.name as name', 'product_shop.description as description', 'product_shop.price_per_unit as price_per_unit', 'invoice_items.id as id', 'invoice_items.quantity as quantity', 'invoice_items.cost_per_unit as cost_per_unit', 'invoice_items.amount as amount', 'invoice_items.time_created as time_created')->get();
                    

                $grandtotal1 = 0;
                $tax1 = 0;
                foreach ($servitems as $key => $item) {
                    $grandtotal1 += $item->amount;
                    $tax1 += ($item->cost_per_unit-$item->price)*$item->repeatition;    
                }

                $grandtotal2 = 0;
                $tax2 = 0;
                foreach ($items as $key => $item) {
                    $grandtotal2 += $item->amount;
                    $tax1 += ($item->cost_per_unit-$item->price_per_unit)*$item->quantity;    
                }

                $grandtotal = $grandtotal1+$grandtotal2;
                $tax = $tax1+$tax2;
                $subtotal = $grandtotal-$tax;

                $services = $shop->services()->get([
                    \DB::raw('service_id as id'),
                    \DB::raw('name'),
                    \DB::raw('price')
                ]);

                $products = $shop->products()->get([
                    \DB::raw('product_id as id'),
                    \DB::raw('barcode'),
                    \DB::raw('name '),
                    \DB::raw('in_stock'),
                    \DB::raw('buying_per_unit'),
                    \DB::raw('price_per_unit')
                ]);

                return view('sales.invoices.pro-invoices.edit', compact('page', 'title', 'title_sw', 'invoice', 'customer', 'customers', 'servitems', 'items', 'shop', 'products', 'services' , 'subtotal' , 'tax' , 'grandtotal' , 'custids'));

            }else{

                $items = InvoiceItem::where('pro_invoice_id', $invoice->id)->join('products', 'products.id', '=', 'invoice_items.product_id')->join('product_shop', 'product_shop.product_id', '=', 'products.id')->where('product_shop.shop_id', $shop->id)->select('products.id as prod_id', 'products.name as name', 'product_shop.description as description', 'product_shop.price_per_unit as price_per_unit', 'invoice_items.id as id', 'invoice_items.quantity as quantity', 'invoice_items.cost_per_unit as cost_per_unit', 'invoice_items.amount as amount', 'invoice_items.time_created as time_created')->get();
                
                $grandtotal = 0;
                $tax = 0;
                foreach ($items as $key => $item) {
                    $grandtotal += $item->amount;
                    $tax += ($item->cost_per_unit-$item->price_per_unit)*$item->quantity;    
                }

                $subtotal = $grandtotal-$tax;

                $products = $shop->products()->get([
                    \DB::raw('product_id as id'),
                    \DB::raw('barcode'),
                    \DB::raw('name '),
                    \DB::raw('in_stock'),
                    \DB::raw('buying_per_unit'),
                    \DB::raw('price_per_unit')
                ]);

                

                return view('sales.invoices.pro-invoices.edit', compact('page', 'title', 'title_sw', 'invoice', 'customer', 'customers', 'items', 'grandtotal', 'tax', 'subtotal', 'shop', 'products' , 'custids'));
            }
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
        $shop = Shop::find(Session::get('shop_id'));
        $invoice = ProInvoice::find(decrypt($id));
        if (!is_null($invoice)) {
            $invoice->due_date = $request['due_date'];
            $invoice->discount = $request['discount'];
            $invoice->shipping_cost = $request['shipping_cost'];
            $invoice->adjustment = $request['adjustment'];
            $invoice->notice = $request['notice'];
            $invoice->terms_and_conditions = $request['terms_and_conditions'];
            $invoice->summary = $request['summary'];
            $invoice->save();
        }

        $success = 'Invoice was successfully updated';
        return redirect('pro-invoices/'.encrypt($invoice->id))->with('success', $success);
    }


     //Update Invoice Items
    public function updateInvoiceItem(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $invoice = ProInvoice::find($request['invoice_id']);

        if (!is_null($invoice)) {
            $item = InvoiceItem::where('id', $request['id'])->where('pro_invoice_id', $invoice->id)->first();
            if (!is_null($item)) {
                $item->quantity = $request['quantity'];
                $item->amount = $item->cost_per_unit*$item->quantity;
                $item->save();
            }

            $servitem = InvoiceServitem::where('id', $request['id'])->where('pro_invoice_id', $invoice->id)->first();

            if (!is_null($servitem)) {
                $servitem->repeatition = $request['repeatition'];
                $servitem->amount = $servitem->cost_per_unit*$servitem->repeatition;
                $servitem->save();
            }
        }

        return redirect('pro-invoices/'.encrypt($invoice->id)."/edit");
    }

    // Add Invoice Item
    public function addItem(Request $request)
    {        
        $shop = Shop::find(Session::get('shop_id'));
        $invoice = ProInvoice::find($request['invoice_id']);
        $now = Carbon::now();
        if (!is_null($invoice)) {
            $product = $shop->products()->where('product_id', $request['product_id'])->first();
            if (!is_null($product)) {
                $invoiceitemData = InvoiceItem::where('product_id', $product->id)->where('pro_invoice_id', $invoice->id)->first();
                if (is_null($invoiceitemData)) {
                    $invoiceitemData = new InvoiceItem;
                    $invoiceitemData->pro_invoice_id = $invoice->id;
                    $invoiceitemData->product_id = $product->pivot->product_id;
                    $invoiceitemData->quantity = 1;
                    $invoiceitemData->cost_per_unit = $product->pivot->price_per_unit;
                    $invoiceitemData->amount = $product->pivot->price_per_unit;
                    $invoiceitemData->time_created = $now;
                    $invoiceitemData->save();
                }else{
                    return redirect()->route('pro-invoices.edit', encrypt($invoice->id))->with('info', 'Item already selected');
                }

                return redirect()->route('pro-invoices.edit', encrypt($invoice->id));
            }
        }
    }

    public function addServiceItem(Request $request)
    {        

        $shop = Shop::find(Session::get('shop_id'));
        $invoice = ProInvoice::find($request['invoice_id']);
        $now = Carbon::now();
        if (!is_null($invoice)) {
            $service = $shop->services()->where('service_id', $request['service_id'])->first();
            if (!is_null($service)) {
                $invoiceitemData = new InvoiceServitem;
                $invoiceitemData->pro_invoice_id = $invoice->id;
                $invoiceitemData->service_id = $service->pivot->service_id;
                $invoiceitemData->repeatition = 1;
                $invoiceitemData->cost_per_unit = $service->pivot->price;
                $invoiceitemData->amount = $service->pivot->price;
                $invoiceitemData->time_created = $now;
                $invoiceitemData->save();
            }
        }

        return redirect('pro-invoices/'.encrypt($invoice->id)."/edit");
    }

    public function deleteItem($id)
    {
            
        $shop = Shop::find(Session::get('shop_id'));
        $item = InvoiceItem::find(decrypt($id));

        if (!is_null($item)) {
            $invoice = ProInvoice::where('id', $item->pro_invoice_id)->where('shop_id', $shop->id)->first();
            if (!is_null($invoice)) {
                $item->delete();
                
                return redirect('pro-invoices/'.encrypt($invoice->id)."/edit");
            }
        }

        return redirect()->back();
    }

     public function deleteServiceItem($id)
    {
            
        $shop = Shop::find(Session::get('shop_id'));
        $item = InvoiceServitem::find(decrypt($id));

        if (!is_null($item)) {
            $invoice = ProInvoice::where('id', $item->pro_invoice_id)->where('shop_id', $shop->id)->first();
            if (!is_null($invoice)) {
                $item->delete();

                return redirect('pro-invoices/'.encrypt($invoice->id)."/edit");
            }
        }
        return redirect()->back();
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
        $invoice = ProInvoice::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (!is_null($invoice)) {
            $invoiceitems = InvoiceItem::where('pro_invoice_id', $invoice->id)->get();
            foreach ($invoiceitems as $key => $item) {
                $item->delete();
            }
            $invoice_servitems = InvoiceServitem::where('pro_invoice_id', $invoice->id)->get();
            foreach ($invoice_servitems as $key => $servitem) {
                $servitem->delete();
            }
            $invoice->delete();
        }

        $success = 'Invoice was successfully deleted';
        return redirect('pro-invoices')->with('success', $success);
    }

    public function updateCustomer(Request $request)
    {
        $customer = Customer::find($request['customer_id']);
        $customer->name = $request['name'];
        $customer->phone = $request['phone'];
        $customer->email = $request['email'];
        $customer->address = $request['address'];
        $customer->tin = $request['tin'];
        $customer->save();

        return redirect('pro-invoices/'.encrypt($request['invoice_id'].'/edit'));
    }

    public function changeCustomer(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $invoice = ProInvoice::where('id', $request['invoice_id'])->where('shop_id', $shop->id)->first();
        $invoice->customer_id = $request['customer_id'];
        $invoice->save();
        
        return redirect('pro-invoices/'.encrypt($invoice->id.'/edit'));
    }

     public function cancelProfoma($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $invoice = ProInvoice::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (!is_null($invoice)) {
            $invoice->status = 'Cancelled';
            $invoice->save();
        }

        return redirect('pro-invoices')->with('success', 'Profoma Invoice canceled successfully');
    }


    public function resumeProfoma($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $invoice = ProInvoice::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (!is_null($invoice)) {
            $invoice->status = 'Pending';
            $invoice->save();
        }

        return redirect('pro-invoices')->with('success', 'Profoma Invoice resumed successfully');;
    }

     public function finalize($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $proinvoice = ProInvoice::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        $now = Carbon::now();
        $maxsaleno = AnSale::where('shop_id', $shop->id)->latest()->first();

        $sale_no = null;
        if (!is_null($maxsaleno)) {
            $sale_no = $maxsaleno->sale_no+1;
        }else{
            $sale_no = 1;
        }

        if (!is_null($proinvoice)) {

            $sale = AnSale::create([
                'customer_id' => $proinvoice->customer_id,
                'shop_id' => $shop->id,
                'user_id' => $user->id,
                'sale_amount_paid' => 0,
                'sale_discount' => $proinvoice->discount,
                'comments' => $proinvoice->notice,
                'status' => 'Unpaid',
                'pay_type' => 'Cash',
                'time_created' => $now,
                'sale_type' => 'credit',
                'sale_no' => $sale_no,
            ]);

            if ($shop->business_type_id == 3) {
                $items = InvoiceServitem::where('pro_invoice_id', $proinvoice->id)->get();
                $tamount = InvoiceServitem::where('pro_invoice_id', $proinvoice->id)->sum('amount');
                $discperc = $proinvoice->discount/$tamount;

                foreach ($items as $key => $value) {
                    $shop_service = $shop->services()->where('service_id', $value->service_id)->first();
                    $saleitemData = new ServiceSaleItem;
                    $saleitemData->an_sale_id = $sale->id;
                    $saleitemData->service_id = $value->service_id;
                    $saleitemData->no_of_repeatition = $value->repeatition;
                    $saleitemData->price = $value->cost_per_unit;
                    $saleitemData->total = $value->amount;
                    $saleitemData->discount = $value->cost_per_unit*$discperc;
                    $saleitemData->total_discount = $saleitemData->discount*$saleitemData->no_of_repeatition;
                    $saleitemData->tax_amount = $value->tax_amount;
                    $saleitemData->time_created = $now;
                    $saleitemData->save();
                }

                $sale_amount = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total');
                $sale_discount = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total_discount');
                $tax_amount = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('tax_amount');
                // $sale = AnSale::find($sale->id);
                $sale->sale_amount = $sale_amount;                
                $sale->sale_discount = $sale_discount;
                $sale->tax_amount = $tax_amount;
                $sale->save();

                // foreach ($items as $key => $item) {
                //     $item->delete();
                // }
            }elseif($shop->business_type_id == 4){
                $saleitems = InvoiceItem::where('pro_invoice_id', $proinvoice->id)->get();
                $tamount_i = InvoiceItem::where('pro_invoice_id', $proinvoice->id)->sum('amount');
                $tamount_s = InvoiceServitem::where('pro_invoice_id', $proinvoice->id)->sum('amount');
                $discperc = $proinvoice->discount/($tamount_i + $tamount_s);

                foreach ($saleitems as $key => $value) {
                    $shop_product = $shop->products()->where('product_id', $value->product_id)->first();

                    if (!is_null($shop_product)) {
                        $saleitemData = new AnSaleItem;
                        $saleitemData->shop_id = $shop->id;
                        $saleitemData->an_sale_id = $sale->id;
                        $saleitemData->product_id = $value->product_id;
                        $saleitemData->quantity_sold = $value->quantity;
                        $saleitemData->buying_per_unit = $shop_product->pivot->buying_per_unit;
                        $saleitemData->buying_price = $saleitemData->buying_per_unit*$saleitemData->quantity_sold;
                        $saleitemData->price_per_unit = $value->cost_per_unit;
                        $saleitemData->price = $value->amount;
                        $saleitemData->discount = $saleitemData->price_per_unit*$discperc;
                        $saleitemData->total_discount = $saleitemData->discount*$saleitemData->quantity_sold;
                        $saleitemData->time_created = $now;
                        $saleitemData->time_created = $value->tax_amount;
                        $saleitemData->save();

                        $stock_in = Stock::where('product_id', $value->product_id)->where('shop_id', $shop->id)->sum('quantity_in');
                        $sold = AnSaleItem::where('product_id', $value->product_id)->where('shop_id', $shop->id)->sum('quantity_sold');
                        $damaged = ProdDamage::where('product_id', $value->product_id)->where('shop_id', $shop->id)->sum('quantity');
                        $tranfered =  TransferOrderItem::where('product_id', $value->product_id)->where('shop_id', $shop->id)->sum('quantity');

                        $returned = SaleReturnItem::where('product_id', $value->product_id)->where('shop_id', $shop->id)->sum('quantity');
                                        
                        $instock = ($stock_in+$returned)-($sold+$damaged+$tranfered);
                        $shop_product->pivot->in_stock = $instock;
                        $shop_product->pivot->save();

                        //Updatting price if the old stock is finished
                        $prodshop = $shop->products()->where('product_id', $value->product_id)->first();

                        $lateststock = Stock::where('product_id', $value->product_id)->where('shop_id', $shop->id)->latest()->first();
                        if (!is_null($lateststock)) {
                                
                            $qtysoldlog = LatestStockSoldLog::where('stock_id', $lateststock->id)->where('shop_id', $shop->id)->first();

                            $newstdiff = 0;
                            if (!is_null($qtysoldlog)) {
                                $newstdiff = $instock-($lateststock->quantity_in-$qtysoldlog->qty_out);
                                if ($qtysoldlog->qty_in-$qtysoldlog->qty_out == 0) {
                                    $qtysoldlog->delete();
                                }
                            }else{
                                $newstdiff = $instock-$lateststock->quantity_in;
                            }

                            if ($newstdiff <= 0) {
                                $prodshop->pivot->in_stock = $instock;
                                $prodshop->pivot->buying_per_unit = $lateststock->buying_per_unit;
                                $prodshop->pivot->save();
                            }else{
                                $prodshop->pivot->in_stock = $instock;
                                $prodshop->pivot->save();   
                            }
                        }else{
                            $prodshop->pivot->in_stock = $instock;
                            $prodshop->pivot->save();
                        }
                    }
                }

                // foreach ($saleitems as $key => $item) {
                //     $item->delete();
                // } 

                $items = InvoiceServitem::where('pro_invoice_id', $proinvoice->id)->get();

                foreach ($items as $key => $value) {
                    $shop_service = $shop->services()->where('service_id', $value->service_id)->first();
                    $saleitemData = new ServiceSaleItem;
                    $saleitemData->an_sale_id = $sale->id;
                    $saleitemData->service_id = $value->service_id;
                    $saleitemData->no_of_repeatition = $value->repeatition;
                    $saleitemData->price = $value->cost_per_unit;
                    $saleitemData->total = $value->amount;
                    $saleitemData->discount = $value->cost_per_unit*$discperc;
                    $saleitemData->total_discount = $saleitemData->discount*$saleitemData->no_of_repeatition;
                    $saleitemData->tax_amount = $value->tax_amount;
                    $saleitemData->time_created = $now;
                    $saleitemData->save();
                }

                $sale_amount = AnSaleItem::where('an_sale_id', $sale->id)->sum('price');
                $sale_discount = AnSaleItem::where('an_sale_id', $sale->id)->sum('total_discount');
                $tax_amount = AnSaleItem::where('an_sale_id', $sale->id)->sum('tax_amount');

                $sale_amount_s = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total');
                $sale_discount_s = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total_discount');
                $tax_amount_s = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('tax_amount');

                $sale->sale_amount = $sale_amount + $sale_amount_s;                
                $sale->sale_discount = $sale_discount + $sale_discount_s ;
                $sale->tax_amount = $tax_amount + $tax_amount_s;
                $sale->save();

                // foreach ($items as $key => $item) {
                //     $item->delete();
                // }

            }else{
                 $saleitems = InvoiceItem::where('pro_invoice_id', $proinvoice->id)->get();
                $tamount = InvoiceItem::where('pro_invoice_id', $proinvoice->id)->sum('amount');
                $discperc = $proinvoice->discount/$tamount;

                foreach ($saleitems as $key => $value) {
                    $shop_product = $shop->products()->where('product_id', $value->product_id)->first();

                    if (!is_null($shop_product)) {
                         $saleitemData = new AnSaleItem;
                        $saleitemData->shop_id = $shop->id;
                        $saleitemData->an_sale_id = $sale->id;
                        $saleitemData->product_id = $value->product_id;
                        $saleitemData->quantity_sold = $value->quantity;
                        $saleitemData->buying_per_unit = $shop_product->pivot->buying_per_unit;
                        $saleitemData->buying_price = $saleitemData->buying_per_unit*$saleitemData->quantity_sold;
                        $saleitemData->price_per_unit = $value->cost_per_unit;
                        $saleitemData->price = $value->amount;
                        $saleitemData->discount = $saleitemData->price_per_unit*$discperc;
                        $saleitemData->total_discount = $saleitemData->discount*$saleitemData->quantity_sold;
                        $saleitemData->time_created = $now;
                        $saleitemData->time_created = $value->tax_amount;
                        $saleitemData->save();

                        $stock_in = Stock::where('product_id', $value->product_id)->where('shop_id', $shop->id)->sum('quantity_in');
                        $sold = AnSaleItem::where('product_id', $value->product_id)->where('shop_id', $shop->id)->sum('quantity_sold');
                        $damaged = ProdDamage::where('product_id', $value->product_id)->where('shop_id', $shop->id)->sum('quantity');
                        $tranfered =  TransferOrderItem::where('product_id', $value->product_id)->where('shop_id', $shop->id)->sum('quantity');

                        $returned = SaleReturnItem::where('product_id', $value->product_id)->where('shop_id', $shop->id)->sum('quantity');
                                        
                        $instock = ($stock_in+$returned)-($sold+$damaged+$tranfered);
                        $shop_product->pivot->in_stock = $instock;
                        $shop_product->pivot->save();

                        //Updatting price if the old stock is finished
                        $prodshop = $shop->products()->where('product_id', $value->product_id)->first();

                        $lateststock = Stock::where('product_id', $value->product_id)->where('shop_id', $shop->id)->latest()->first();
                        if (!is_null($lateststock)) {
                                
                            $qtysoldlog = LatestStockSoldLog::where('stock_id', $lateststock->id)->where('shop_id', $shop->id)->first();

                            $newstdiff = 0;
                            if (!is_null($qtysoldlog)) {
                                $newstdiff = $instock-($lateststock->quantity_in-$qtysoldlog->qty_out);
                                if ($qtysoldlog->qty_in-$qtysoldlog->qty_out == 0) {
                                    $qtysoldlog->delete();
                                }
                            }else{
                                $newstdiff = $instock-$lateststock->quantity_in;
                            }

                            if ($newstdiff <= 0) {
                                $prodshop->pivot->in_stock = $instock;
                                $prodshop->pivot->buying_per_unit = $lateststock->buying_per_unit;
                                $prodshop->pivot->save();
                            }else{
                                $prodshop->pivot->in_stock = $instock;
                                $prodshop->pivot->save();   
                            }
                        }else{
                            $prodshop->pivot->in_stock = $instock;
                            $prodshop->pivot->save();
                        }
                    }
                }

                $sale_amount = AnSaleItem::where('an_sale_id', $sale->id)->sum('price');
                $sale_discount = AnSaleItem::where('an_sale_id', $sale->id)->sum('total_discount');
                $tax_amount = AnSaleItem::where('an_sale_id', $sale->id)->sum('tax_amount');
                // $sale = AnSale::find($sale->id);
                $sale->sale_amount = $sale_amount;
                $sale->sale_discount = $sale_discount;
                $sale->tax_amount = $tax_amount;
                $sale->save();  


                // foreach ($saleitems as $key => $item) {
                //     $item->delete();
                // } 
            }

            $max_no = Invoice::where('shop_id', $shop->id)->orderBy('inv_no', 'desc')->first();
            $invoice_no = 0;
            if (!is_null($max_no)) {
                $invoice_no = $max_no->inv_no +1;
            }else{
                $invoice_no = 1;
            }

            $invoice = Invoice::create([
                'inv_no' => $invoice_no,
                'shop_id' => $shop->id,
                'user_id' => Auth::user()->id,
                'an_sale_id' => $sale->id,
                'due_date' => $proinvoice->due_date, 
                'status' => 'Pending',
                'note' => $proinvoice->notice
            ]);

            $invoice->created_at = $now;
            $invoice->save();

            $acctrans = new CustomerTransaction();
            $acctrans->shop_id = $shop->id;
            $acctrans->user_id = Auth::user()->id;
            $acctrans->customer_id = $proinvoice->customer_id;
            $acctrans->invoice_no = $invoice->inv_no;
            $acctrans->amount = ($sale->sale_amount-$sale->sale_discount);
            $acctrans->date = $now;
            $acctrans->save();

            $proinvoice->status = 'Completed';
            $proinvoice->save();
        }

        return redirect('pro-invoices')->with('success', 'Invoice created successfully');
    }

    public function cpOrders(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $orders = ProInvoice::where('customer_id', $request['cust_id'])->where('shop_id', $shop->id)->where('status', 'Pending')->get();
        return Response::json($orders);
    }

    public function pendingOrders($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $page = 'Invoices';
        $title = 'Profoma Invoices';
        $title_sw = 'Ankara za Profoma';

        $invoices = ProInvoice::where('customer_id', $id)->where('pro_invoices.shop_id', $shop->id)->where('status', 'Pending')->join('customers', 'customers.id', '=', 'pro_invoices.customer_id')->select('customers.name as name', 'pro_invoices.id as id', 'pro_invoices.invoice_no as invoice_no', 'pro_invoices.status as status', 'pro_invoices.due_date as due_date', 'pro_invoices.created_at as created_at', 'pro_invoices.updated_at as updated_at')->orderBy('pro_invoices.created_at', 'desc')->get();

        $customer = Customer::where('shop_id', $shop->id)->first();

        $duration = '';
        return view('sales.invoices.pro-invoices.index', compact('page', 'title', 'title_sw', 'invoices', 'customer', 'duration'));

    }
}
