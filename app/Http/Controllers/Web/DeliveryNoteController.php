<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use DB;
use App\Models\Shop;
use App\Models\User;
use App\Models\DeliveryNote;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\Setting;

class DeliveryNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Delivery Notes';
        $title = 'Delivery Notes';
        $title_sw = 'Vidokezo vya Uwasilishaji';
        $shop = Shop::find(Session::get('shop_id'));
        $dnotes = DeliveryNote::where('delivery_notes.shop_id', $shop->id)->orderBy('delivery_notes.created_at', 'desc')->join('an_sales', 'an_sales.id', '=', 'delivery_notes.an_sale_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('delivery_notes.id as id', 'note_no', 'delivery_notes.comments as comments', 'delivery_notes.created_at as created_at', 'delivery_notes.updated_at as updated_at', 'name')->get();

        return view('sales.delivery-notes.index', compact('page', 'title', 'title_sw', 'dnotes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $page = 'Delivery Notes';
        $title = 'New Delivery Note';
        $title_sw = 'Kidokezo Kipya cha Uwasilishaji';
        $shop = Shop::find(Session::get('shop_id'));
        $sale = AnSale::where('an_sales.id', decrypt($id))->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id', 'name')->first();
        if (!is_null($sale)) {
            $dnote = DeliveryNote::where('an_sale_id', $sale->id)->first();
            if (!is_null($dnote)) {
                return redirect()->route('delivery-notes.show', encrypt($dnote->id))->with('success', 'Delivery Note already created successfully');
            }else{
                return view('sales.delivery-notes.create', compact('page', 'title', 'title_sw', 'shop', 'sale'));
            }
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
        $maxno = DeliveryNote::where('shop_id', $shop->id)->orderBy('created_at', 'desc')->first();
        $noteno = null;
        if (!is_null($maxno)) {
            $noteno = $maxno->note_no+1;
        }else{
            $noteno = 1;
        }
        $dnote = DeliveryNote::where('an_sale_id', $request['an_sale_id'])->first();
        if (is_null($dnote)) {
            $dnote = DeliveryNote::create([
                'an_sale_id' => $request['an_sale_id'],
                'shop_id' => $shop->id,
                'user_id' => $user->id,
                'note_no' => $noteno,
                'comments' => $request['comments']
            ]);
        }

        return redirect()->route('delivery-notes.show', encrypt($dnote->id))->with('success', 'Delivery Note created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = 'Delivery Notes';
        $title = 'Delivery Note';
        $title_sw = 'Kidokezo cha Uwasilishaji';

        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $dnote = DeliveryNote::find(decrypt($id));
        $user = User::find($dnote->user_id);
        if (!is_null($dnote)) {
            $sale = AnSale::where('an_sales.id', $dnote->an_sale_id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.cust_no as cust_no', 'customers.postal_address as po_address', 'customers.physical_address as ph_address', 'customers.street as street', 'customers.email as email', 'customers.phone as phone', 'customers.tin as tin', 'customers.vrn as vrn', 'an_sales.id as id', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.tax_amount as tax_amount')->first();
            
            $items = AnSaleItem::where('an_sale_id', $sale->id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->groupBy('name')->orderBy('an_sale_items.time_created', 'desc')->get([
                DB::raw('products.name as name'),
                DB::raw('an_sale_items.product_id as product_id'),
                DB::raw('SUM(an_sale_items.quantity_sold) as quantity_sold'),
                DB::raw('an_sale_items.price_per_unit as price_per_unit'),
                DB::raw('an_sale_items.discount as discount'),
                DB::raw('SUM(an_sale_items.price) as price'),
                DB::raw('SUM(an_sale_items.total_discount) as total_discount'),
                DB::raw('an_sale_items.tax_amount as tax_amount')
            ]);
            
            return view('sales.delivery-notes.show', compact('page', 'title', 'title_sw', 'shop', 'user', 'settings', 'dnote', 'sale', 'items'));
        }else{
            return redirect('forbiden');
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
        $page = 'Delivery Notes';
        $title = 'Edit Delivery Note';
        $title_sw = 'Hariri Kidokezo cha Uwasilishaji';
        $dnote = DeliveryNote::find(decrypt($id));
        $sale = AnSale::where('an_sales.id', $dnote->an_sale_id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id', 'name')->first();
        return view('sales.delivery-notes.edit', compact('page', 'title', 'title_sw', 'dnote', 'sale'));
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
        $dnote = DeliveryNote::find(decrypt($id));
        $dnote->comments = $request['comments'];
        $dnote->save();

        return redirect('delivery-notes')->with('success', 'Delivery Note updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $dnote = DeliveryNote::find(decrypt($id));
        if (!is_null($dnote)) {
            $dnote->delete();
        }
        return redirect('delivery-notes')->with('success', 'Delivery Note deleted successfully');
    }    
}
