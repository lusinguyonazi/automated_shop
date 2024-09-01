<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;
use \Carbon\Carbon;
use Auth;
use App\Models\Shop;
use App\Models\User;
use App\Models\Setting;
use App\Models\Payment;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderTemp;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\PurchaseItemTemp;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Products';
        $title = 'Purchase Orders';
        $title_sw = 'Oda za Manunuzi';
        $shop = Shop::find(Session::get('shop_id'));
        $porders = PurchaseOrder::where('shop_id', $shop->id)->where('is_deleted', false)->orderBy('created_at', 'desc')->get();
        $suppliers = $shop->suppliers()->get();

        return view('products.purchase-orders.index', compact('page', 'title', 'title_sw', 'shop', 'porders', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'Products';
        $title = 'New Purchase Order';
        $title_sw = 'Agizo Jipya la Ununuzi';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $max_no = PurchaseOrder::where('shop_id', $shop->id)->orderBy('order_no', 'desc')->first();
        $order_no = 1;
        if (!is_null($max_no)) {
            $order_no = $max_no->order_no+1;
        }else{
            $order_no = 1;
        }

        $mindays = 0;
        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->first();
        if (!is_null($payment)) {
            $now = Carbon::now();
            $paydate = Carbon::parse($payment->created_at);

            $lastpay = Payment::where('shop_id', $shop->id)->where('is_expired', 1)->orderBy('created_at', 'desc')->first();
            if (!is_null($lastpay)) {
                $lastexp = Carbon::parse($lastpay->expire_date);
                $oldpaydate = Carbon::parse($lastpay->created_at);
                $slipdays = $paydate->diffInDays($lastexp);
                // return $slipdays;
                if ($slipdays < 15) {
                    $mindays = $now->diffInDays($oldpaydate);
                }else{
                    $mindays = $now->diffInDays($paydate);
                } 
            }else{
                $mindays = $now->diffInDays($paydate);
            }
        }

        if ($mindays < 10) {
            $mindays = 15;
        }

        $units = array(
            'pcs' => 'Piece',
            'prs' => 'Pair',
            'cts' => 'Carton',
            'pks' => 'Pack',
            'kgs' => 'Kilogram',
            'lts' => 'Liter',
            'dzs' => 'Dozen',
            'fts' => 'Foot',
            'fls' => 'Float',
            'crs' => 'Crete',
            'set' => 'Set',
            'gls' => 'Gallon',
            'box' => 'Box',
            'btl' => 'Bottle',
            'mts' => 'Meter'
        );

        $suppliers = $shop->suppliers()->pluck('name', 'id');

        return view('products.purchase-orders.create', compact('page', 'title', 'title_sw', 'units', 'shop', 'settings', 'order_no', 'suppliers', 'mindays'));
    }

    public function cancelPorder()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $puritems = PurchaseOrderTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
        foreach ($puritems as $key => $value) {
            $value->delete();
        }

        return redirect()->route('purchase-orders.create')->with('success', 'Order cancelled successfully');
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
        $max_no = PurchaseOrder::where('shop_id', $shop->id)->orderBy('created_at', 'desc')->first();
        $orderno = 0;
        if (!is_null($max_no)) {
            $orderno = $max_no->order_no+1;
        }else{
            $orderno = 1;
        }
    
        $supplier_id = null;
        if (!is_null($request['supplier_id']) && $request['supplier_id'] != 0) {
            $supplier_id = $request['supplier_id'];
        }

        $pitems = PurchaseOrderTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();

        if (!is_null($pitems)) {
            $temps = array();
            foreach ($pitems as $key => $value) {
                if ($value->qty == 0) {
                    array_push($temps, $value->qty);
                }
            }

            if (!empty($temps)) {
                return redirect()->back()->with('warning', 'Please update the quantity and Unit cost of each item to continue');
            }else{


                $porder = PurchaseOrder::create([
                    'shop_id' => $shop->id,
                    'user_id' => $user->id,
                    'supplier_id' => $supplier_id,
                    'order_no' => $orderno, 
                    'amount' => 0,
                    'comments' => $request['comments'],
                ]);

                $amount = 0;
                foreach ($pitems as $key => $item) {
                    $product = Product::find($item->product_id);
                    $poitem  = new PurchaseOrderItem;
                    $poitem->purchase_order_id = $porder->id;
                    $poitem->product_id = $product->id;
                    $poitem->shop_id = $shop->id;
                    $poitem->qty = $item->qty;
                    $poitem->unit_cost = $item->unit_cost;
                    $poitem->save();

                    $amount += $item->qty*$item->unit_cost;
                }

                $porder->amount = $amount;
                $porder->save();

                $puritems = PurchaseOrderTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
                foreach ($puritems as $key => $value) {
                    $value->delete();
                }

                return redirect()->back()->with('success', 'Purchase Order were added successfully');
            }
        }else{
            return redirect()->back()->with('warning', 'Please Select at least one Product to continue!.');
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
        $page = 'Products';
        $title = 'Purchase Order';
        $title_sw = 'Oda ya Manunuzi';

        $shop = Shop::find(Session::get('shop_id'));
        $porder = PurchaseOrder::find(decrypt($id));
        $user = User::find($porder->user_id);
        $supplier = Supplier::find($porder->supplier_id);
        $pitems = PurchaseOrderItem::where('purchase_order_id', $porder->id)->where('shop_id', $shop->id)->join('products', 'products.id', '=', 'purchase_order_items.product_id')->select('purchase_order_items.id as id', 'purchase_order_items.qty as qty', 'purchase_order_items.unit_cost as unit_cost', 'purchase_order_items.created_at as created_at', 'products.name as name', 'products.basic_unit as basic_unit')->orderBy('created_at', 'desc')->get();
        
        return view('products.purchase-orders.show', compact('page', 'title', 'title_sw', 'shop', 'user', 'porder', 'pitems','supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       $page = 'Products';
        $title = 'Update Purchase';
        $title_sw = 'Hariri Manunuzi';
        $shop = Shop::find(Session::get('shop_id'));
        $suppliers = $shop->suppliers()->get();
        $products = $shop->products()->get();
        $porder = PurchaseOrder::find(decrypt($id));
        $pitems = PurchaseOrderItem::where('purchase_order_id', $porder->id)->where('shop_id', $shop->id)->join('products', 'products.id', '=', 'purchase_order_items.product_id')->select('purchase_order_items.id as id', 'purchase_order_items.qty as qty', 'purchase_order_items.unit_cost as unit_cost', 'purchase_order_items.created_at as created_at', 'products.name as name', 'products.basic_unit as basic_unit')->orderBy('created_at', 'desc')->get();

        $statuses = [
            ['value' => 'Pending'], 
            ['value' => 'Submitted'], 
            ['value' => 'Delivered'],
            ['value' => 'Cancelled']
        ];

        return view('products.purchase-orders.edit', compact('page', 'title', 'title_sw', 'shop', 'suppliers', 'porder', 'pitems', 'products', 'statuses'));
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
        $porder = PurchaseOrder::find(decrypt($id));
        if ($request['status'] == 'Delivered') {
            $porder->status = 'Submitted';
            $porder->save();
            return redirect('create-purchase/'.encrypt($porder->id));
        }else{
            $porder->supplier_id = $request['supplier_id'];
            $porder->status = $request['status'];
            $porder->comments = $request['comments'];
            $porder->save();
            return redirect('purchase-orders')->with('success', 'Purchase order was updated successfully');
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
        $porder = PurchaseOrder::find(decrypt($id));
        if (!is_null($porder)) {
            $pitems = PurchaseOrderItem::where('purchase_order_id', $porder->id)->get();
            foreach ($pitems as $key => $item) {
                $item->delete();
            }
        }

        $porder->delete();

        return redirect()->back()->with('success', 'Purchase order was updated successfully');
    }

    public function orderItems($id)
    {
        $page = 'Products';
        $title = 'Purchase Order Items';
        $title_sw = 'Bidhaa za Oda ya Manunuzi';

        $shop = Shop::find(Session::get('shop_id'));
        $porder = PurchaseOrder::find(decrypt($id));
        $supplier = Supplier::find($porder->supplier_id);
        
        $pitems = PurchaseOrderItem::where('purchase_order_id', $porder->id)->where('shop_id', $shop->id)->join('products', 'products.id', '=', 'purchase_order_items.product_id')->select('purchase_order_items.id as id', 'purchase_order_items.qty as qty', 'purchase_order_items.unit_cost as unit_cost', 'purchase_order_items.created_at as created_at', 'products.name as name', 'products.basic_unit as basic_unit')->orderBy('created_at', 'desc')->get();

        return view('products.purchase-orders.items', compact('page', 'title', 'title_sw', 'shop', 'porder', 'pitems', 'supplier'));
    }

    public function deleteMultiple(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));

        $user = User::find(Session::get('user_id'));
        foreach ($request->input('id') as $key => $id) {
            
            $porder = PurchaseOrder::find($id);
            if (!is_null($porder)) {
                $pitems = PurchaseOrderItem::where('purchase_order_id', $porder->id)->get();
                foreach ($pitems as $key => $item) {
                    $item->delete();
                }
            }

            $porder->delete();
        }

        return redirect()->back()->with('success', 'Purchase Orders were deleted successfully');
    }


    public function createPurchase($id)
    {

        $page = 'Products';
        $title = 'New Purchase';
        $title_sw = 'Manunuzi Mapya';
        $units = array(
            'pcs' => 'Piece',
            'prs' => 'Pair',
            'cts' => 'Carton',
            'pks' => 'Pack',
            'kgs' => 'Kilogram',
            'lts' => 'Liter',
            'dzs' => 'Dozen',
            'fts' => 'Foot',
            'fls' => 'Float',
            'crs' => 'Crete',
            'set' => 'Set',
            'gls' => 'Gallon',
            'box' => 'Box',
            'btl' => 'Bottle',
            'mts' => 'Meter'
        );

        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();

        $mindays = 0;
        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->first();
        if (!is_null($payment)) {
            $now = Carbon::now();
            $paydate = Carbon::parse($payment->created_at);

            $lastpay = Payment::where('shop_id', $shop->id)->where('is_expired', 1)->orderBy('created_at', 'desc')->first();
            if (!is_null($lastpay)) {
                $lastexp = Carbon::parse($lastpay->expire_date);
                $oldpaydate = Carbon::parse($lastpay->created_at);
                $slipdays = $paydate->diffInDays($lastexp);
                // return $slipdays;
                if ($slipdays < 15) {
                    $mindays = $now->diffInDays($oldpaydate);
                }else{
                    $mindays = $now->diffInDays($paydate);
                } 
            }else{
                $mindays = $now->diffInDays($paydate);
            }
        }

        if ($mindays < 10) {
            $mindays = 15;
        }

        $products = $shop->products()->get();
        $suppliers = $shop->suppliers()->pluck('name', 'id');

        $porder = PurchaseOrder::find(decrypt($id));
        if ($porder->status == 'Submitted') {
                
            $poitems = PurchaseOrderItem::where('purchase_order_id', $porder->id)->get();
            foreach ($poitems as $key => $item) {
                $product = $shop->products()->where('product_id', $item->product_id)->first();
                if (!is_null($product)) {
                    $stockItemTemp = new PurchaseItemTemp;
                    $stockItemTemp->shop_id = $shop->id;
                    $stockItemTemp->user_id = $user->id;
                    $stockItemTemp->product_id = $item->product_id;
                    $stockItemTemp->quantity_in  = $item->qty;
                    $stockItemTemp->buying_per_unit = $item->unit_cost;
                    if (!is_null($product->pivot->price_per_unit)) {
                        $stockItemTemp->price_per_unit = $product->pivot->price_per_unit;
                    }else{
                        $stockItemTemp->price_per_unit = 0;
                    }
                    $stockItemTemp->total = $item->qty*$item->unit_cost;
                    $stockItemTemp->save();
                }
            }

            $porder->status = 'Delivered';
            $porder->save();

            return view('products.purchases.create-purchase', compact('page', 'title', 'title_sw', 'units', 'settings', 'products', 'suppliers', 'shop', 'mindays', 'porder'));
        }else{
            return redirect()->back()->with('info', 'Purchase already created');
        }
    }
}


