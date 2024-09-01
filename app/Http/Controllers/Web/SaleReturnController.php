<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use \DB;
use \Carbon\Carbon;
use Auth;
use App\Models\Shop;
use App\Models\User;
use App\Models\Setting;
use App\Models\Invoice;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\CreditNoteItem;
use App\Models\Customer;
use App\Models\salereturn;
use App\Models\SaleReturnItem;

class SaleReturnController extends Controller
{
    /**
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
    public function index(Request $request)
    {
        $page = 'Sales';
        $title = 'Sales Returns';
        $title_sw = 'Mauzo yaliyorudishwa';

        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $sreturns = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('sale_returns.id as id', 'sale_returns.sale_return_amount as sale_return_amount', 'sale_returns.sale_return_discount as sale_return_discount', 'sale_returns.return_tax_amount as return_tax_amount', 'sale_returns.created_at as created_at', 'sale_returns.updated_at as updated_at', 'customers.name as name')->get();

        $customer = Customer::where('shop_id', $shop->id)->first();

        $is_post_query = false;
        $start_date = '';
        $end_date = '';
        $start = null;
        $end = null;
        if (!is_null($request['sale_date'])) {
            $start_date = $request['sale_date'];
            $end_date = $request['sale_date'];
            $start = $request['sale_date'].' 00:00:00';
            $end = $request['sale_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = \Carbon\Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
            $end = \Carbon\Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
            $is_post_query = false;
        }
        $duration = '';
        $mysales = AnSale::where('an_sales.shop_id', $shop->id)->whereBetween('an_sales.time_created', [$start, $end])->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id', 'an_sales.time_created as time_created', 'customers.name')->get();
        
        $sales = array();
        foreach ($mysales as $key => $sale) {
            $items = AnSaleItem::where('an_sale_id', $sale->id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->groupBy('name')->orderBy('an_sale_items.time_created', 'desc')->get([
                DB::raw('products.name as name'),
                DB::raw('an_sale_items.product_id as product_id'),
                DB::raw('SUM(an_sale_items.quantity_sold) as quantity_sold')
            ]);

            $saleitems = array();
            foreach ($items as $key => $item) {
                array_push($saleitems, $item->name.'('.$item->quantity_sold.')');
            }
            array_push($sales, ['id' => $sale->id, 'customer' => $sale->name, 'date' => $sale->time_created, 'items' => implode(',', $saleitems)]);
        }

        $salesdate = date('d M Y', strtotime($start));

        return view('sales.returns.index', compact('page', 'title', 'title_sw', 'sreturns', 'customer', 'duration', 'is_post_query', 'start_date', 'end_date', 'settings', 'sales', 'salesdate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $page = 'Sales';
        $title = 'Create Sale Return';
        $title_sw = 'Tengeneza Uzo lilirudishwa';

        $is_post_query = false;
        $start_date = '';
        $end_date = '';
        $duration = '';
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $sale = AnSale::where('an_sales.id', decrypt($id))->where('an_sales.shop_id', $shop->id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.phone as phone', 'customers.email as email', 'an_sales.id as id', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.time_created as time_created')->first();
        if (is_null($sale)) {
            return redirect('forbiden');
        }else{

            $items = AnSaleItem::where('an_sale_id', $sale->id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->groupBy('name')->orderBy('an_sale_items.time_created', 'desc')->get([
                DB::raw('products.name as name'),
                DB::raw('an_sale_items.product_id as product_id'),
                DB::raw('SUM(an_sale_items.quantity_sold) as quantity_sold'),
                DB::raw('an_sale_items.price_per_unit as price_per_unit'),
                DB::raw('SUM(an_sale_items.price) as price')
            ]);

            $date = Carbon::now()->toDayDateTimeString();

            $salereturn = SaleReturn::where('an_sale_id', $sale->id)->first();
            if (is_null($salereturn)) {
                $salereturn = SaleReturn::create([
                    'an_sale_id' => $sale->id,
                    'shop_id' => $shop->id,
                    'user_id' => $user->id,
                ]);
            }
            

            $sritems = SaleReturnItem::where('sale_return_id', $salereturn->id)->join('products', 'products.id', '=', 'sale_return_items.product_id')->select('products.id as p_id', 'products.name as name', 'products.basic_unit as basic_unit', 'sale_return_items.product_id as product_id', 'sale_return_items.id as id', 'sale_return_items.quantity as quantity', 'sale_return_items.price_per_unit as price_per_unit', 'sale_return_items.price as price','sale_return_items.total_discount as discount', 'sale_return_items.tax_amount as tax_amount', 'sale_return_items.created_at as created_at')->orderBy('sale_return_items.created_at', 'desc')->get();

            $total = 0;
            $discount = 0;
            $tax = 0;
            foreach ($sritems as $key => $item) {
                $total += $item->price;
                $discount += $item->discount;
                $tax += $item->tax_amount;
            }

            return view('sales.returns.create', compact('page', 'title', 'title_sw', 'sale', 'sale', 'items', 'shop', 'settings', 'date', 'salereturn', 'sritems', 'total', 'discount', 'is_post_query', 'start_date', 'end_date', 'duration'));
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
        $sale = AnSale::find($request['an_sale_id']);
        $user = Auth::user();
        if (!is_null($sale)) {
            $salereturn = SaleReturn::where('an_sale_id', $sale->id)->first();
            if (is_null($salereturn)) {
                $salereturn = SaleReturn::create([
                    'an_sale_id' => $sale->id,
                    'shop_id' => $shop->id,
                    'user_id' => $user->id,
                ]);
            }

            return redirect('create-sale-return/'.encrypt($sale->id));
        }else{
            return redirect()->back()->with('info', 'No Sales with info provided');
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
        $page = 'Sale items';
        $title = 'Sale Return';
        $title_sw = 'Uzo lilirudishwa';
        $shop = Shop::find(Session::get('shop_id'));
        $user = User::find(Session::get('user_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();    

        $salereturn = SaleReturn::where('sale_returns.id', decrypt($id))->where('sale_returns.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('sale_returns.id as id', 'sale_returns.sale_return_amount as sale_return_amount', 'sale_returns.sale_return_discount as sale_return_discount', 'sale_returns.reason as reason', 'sale_returns.created_at as created_at', 'sale_returns.updated_at as updated_at', 'customers.name as name', 'customers.postal_address as postal_address', 'customers.physical_address as physical_address', 'customers.street as street', 'customers.email as email', 'customers.phone as phone')->first();

        $sritems = SaleReturnItem::where('sale_return_id', $salereturn->id)->join('products', 'products.id', '=', 'sale_return_items.product_id')->select('products.id as p_id', 'products.name as name', 'products.basic_unit as basic_unit', 'sale_return_items.product_id as product_id', 'sale_return_items.id as id', 'sale_return_items.quantity as quantity', 'sale_return_items.price_per_unit as price_per_unit', 'sale_return_items.price as price','sale_return_items.total_discount as discount', 'sale_return_items.tax_amount as tax_amount', 'sale_return_items.created_at as created_at')->orderBy('sale_return_items.created_at', 'desc')->get();

        $total = 0;
        $discount = 0;
        $tax = 0;
        foreach ($sritems as $key => $item) {
            $total += $item->price;
            $discount += $item->discount;
            $tax += $item->tax_amount;
        }

        return view('sales.returns.show', compact('page', 'title', 'title_sw', 'settings', 'shop', 'settings', 'salereturn', 'sritems', 'total', 'discount', 'tax'));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $page = 'Edit sale';
        $title = 'Edit Sale Return';
        $title_sw = 'Hariri uzo lilirudishwa';
        
        $shop = Shop::find(Session::get('shop_id'));
        $user = User::find(Session::get('user_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();    

        $salereturn = SaleReturn::where('sale_returns.id', decrypt($id))->where('sale_returns.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('sale_returns.id as id', 'sale_returns.sale_return_amount as sale_return_amount', 'sale_returns.sale_return_discount as sale_return_discount', 'sale_returns.reason as reason', 'sale_returns.created_at as created_at', 'sale_returns.updated_at as updated_at', 'customers.name as name', 'customers.postal_address as postal_address', 'customers.physical_address as physical_address', 'customers.street as street', 'customers.email as email', 'customers.phone as phone')->first();

        $sritems = SaleReturnItem::where('sale_return_id', $salereturn->id)->join('products', 'products.id', '=', 'sale_return_items.product_id')->select('products.id as p_id', 'products.name as name', 'products.basic_unit as basic_unit', 'sale_return_items.product_id as product_id', 'sale_return_items.id as id', 'sale_return_items.quantity as quantity', 'sale_return_items.price_per_unit as price_per_unit', 'sale_return_items.price as price','sale_return_items.total_discount as discount', 'sale_return_items.tax_amount as tax_amount', 'sale_return_items.created_at as created_at')->orderBy('sale_return_items.created_at', 'desc')->get();

        $total = 0;
        $discount = 0;
        $tax = 0;
        foreach ($sritems as $key => $item) {
            $total += $item->price;
            $discount += $item->discount;
            $tax += $item->tax_amount;
        }

        $items = AnSaleItem::where('an_sale_id', $salereturn->an_sale_id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->groupBy('name')->orderBy('an_sale_items.time_created', 'desc')->get([
                DB::raw('products.name as name'),
                DB::raw('an_sale_items.product_id as product_id'),
                DB::raw('SUM(an_sale_items.quantity_sold) as quantity_sold'),
                DB::raw('an_sale_items.price_per_unit as price_per_unit'),
                DB::raw('SUM(an_sale_items.price) as price')
            ]);

        return view('sales.returns.edit', compact('page', 'title', 'title_sw', 'settings', 'shop', 'settings', 'salereturn', 'sritems', 'total', 'discount', 'tax', 'items'));
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
        $salereturn = SaleReturn::find($id);
        if (!is_null($salereturn)) {
                
            $salereturn->reason = $request['reason'];
            $salereturn->save();
        }

        return redirect('sales-returns')->with('success', 'Sale Return was creadted successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $salereturn = SaleReturn::find(decrypt($id));
        if (!is_null($salereturn)) {
            $sale = AnSale::find($salereturn->an_sale_id);
            $sritems = SaleReturnItem::where('sale_return_id', $salereturn->id)->get();
            foreach ($sritems as $key => $item) {
                $item->delete();
            }
            $salereturn->delete();

            $sale->adjustment = 0;
            $sale->save();
            if (true) {
                if ($sale->sale_amount-($sale->sale_discount+$sale->adjustment) == $sale->sale_amount_paid) {
                    $sale->status = 'Paid';
                    $sale->time_paid = \Carbon\Carbon::now();
                    $sale->save();
                }elseif ($sale->sale_amount-($sale->sale_discount+$sale->adjustment) > $sale->sale_amount_paid && $sale->sale_amount_paid > 0) {
                    $sale->status = 'Partially Paid';
                    $sale->time_paid = null;
                    $sale->save();
                }elseif ($sale->sale_amount-($sale->sale_discount+$sale->adjustment) < $sale->sale_amount_paid) {
                    $sale->status = 'Excess Paid';
                    $sale->time_paid = \Carbon\Carbon::now();
                    $sale->save();
                }else{
                    $sale->status = 'Unpaid';
                    $sale->time_paid = null;
                    $sale->save();
                }
            }
        }

        return redirect('sales-returns')->with('success', 'Sale Return was successfully canceled');
    }
}