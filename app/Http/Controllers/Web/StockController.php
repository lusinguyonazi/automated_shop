<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Crypt;
use Session;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\Product;
use App\Models\Stock;
use App\Models\AnSaleItem;
use App\Models\Purchase;
use App\Models\SaleReturnItem;
use App\Models\TransferOrderItem;
use App\Models\ProdDamage;
use App\Models\SupplierTransaction;

class StockController extends Controller
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
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $stock = Stock::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        $products = $shop->products()->get();
        $suppliers = $shop->suppliers()->get();
        if (is_null($stock)) {
            return redirect('forbiden');
        }else{
            $product = Product::find($stock->product_id);
            $page = 'Edit stock';
            $title = 'Edit stock';
            $title_sw = 'Hariri Stock';

            return view('products.stocks.edit', compact('page', 'title', 'title_sw', 'stock', 'product', 'products', 'suppliers', 'shop', 'settings'));
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
        $stock = Stock::find(decrypt($id));
        $stock->product_id = $request['product_id'];
        $stock->quantity_in = $request['quantity_in'];
        $stock->buying_per_unit = $request['buying_per_unit'];
        $stock->expire_date = $request['exp_date'];
        $stock->save();

        $product = Product::find($stock->product_id);
        $shop_product = $shop->products()->where('product_id', $product->id)->first();

        $stock_in = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
        $sold = AnSaleItem::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
        $damaged = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
        $tranfered =  TransferOrderItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
        $returned = SaleReturnItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');

        $instock = ($stock_in+$returned)-($sold+$damaged+$tranfered); 
        if ($shop_product->pivot->buying_per_unit == 0) {
            $shop_product->pivot->buying_per_unit = $stock->buying_per_unit;    
            $shop_product->pivot->in_stock = $instock;
            if ($shop_product->pivot->in_stock > $shop_product->pivot->reorder_point) {
                $shop_product->pivot->status = 'In Stock';
            }elseif ($shop_product->pivot->in_stock == 0) {
                $shop_product->pivot->status = 'Out of Stock';
            }elseif($shop_product->pivot->in_stock <= $shop_product->pivot->reorder_point && $shop_product->pivot->in_stock != 0){
                $shop_product->pivot->status = 'Low Stock';
            }
            $shop_product->pivot->save();    
        }else{
            $shop_product->pivot->in_stock = $instock;
            if ($shop_product->pivot->in_stock > $shop_product->pivot->reorder_point) {
                $shop_product->pivot->status = 'In Stock';
            }elseif ($shop_product->pivot->in_stock == 0) {
                $shop_product->pivot->status = 'Out of Stock';
            }elseif($shop_product->pivot->in_stock <= $shop_product->pivot->reorder_point && $shop_product->pivot->in_stock != 0){
                $shop_product->pivot->status = 'Low Stock';
            }
            $shop_product->pivot->save();
        }
        
        $purchase = Purchase::find($stock->purchases_id);

        if (!is_null($purchase)) {
            $pitems = Stock::where('purchases_id', $purchase->id)->where('shop_id', $shop->id)->get();
            $total_amount = 0;
            foreach ($pitems as $key => $item) {
                $total_amount += ($item->quantity_in*$item->buying_per_unit);
            }

            $purchase->total_amount = $total_amount;
            $purchase->save();


            $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->where('shop_id', $purchase->shop_id)->first();
            if (!is_null($acctrans) ) {
                $acctrans->amount = $purchase->total_amount;
                $acctrans->save();
            }
        }

        $message = 'Stock was successfully updated';
        return redirect()->route('products.show', encrypt($product->id))->with('success', $message);
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
        $user = Auth::user();
        if ($user->hasRole('manager')) {
             
            $stock = Stock::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
            if (!is_null($stock)) {
                $product = Product::find($stock->product_id);

                $stock_in = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                $sold = AnSaleItem::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                $damaged = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                $tranfered =  TransferOrderItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                $returned = SaleReturnItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                            
                $instockbf = (($stock_in+$returned)-$stock->quantity_in)-($sold+$damaged+$tranfered);  
                if ($instockbf >= 0 ) {
                        
                    $stock->is_deleted = true;
                    $now = Carbon::now();
                    $stock->del_by = $user->first_name.'('.$now.')';
                    $stock->save();

                    $shop_product = $shop->products()->where('product_id', $product->id)->first();

                    $stock_in = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                    $sold = AnSaleItem::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                    $damaged = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                    $tranfered =  TransferOrderItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                    $returned = SaleReturnItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                                
                    $instock = ($stock_in+$returned)-($sold+$damaged+$tranfered);   
                    $shop_product->pivot->in_stock = $instock;
                    if ($shop_product->pivot->in_stock > $shop_product->pivot->reorder_point) {
                        $shop_product->pivot->status = 'In Stock';
                    }elseif ($shop_product->pivot->in_stock == 0) {
                        $shop_product->pivot->status = 'Out of Stock';
                    }elseif($shop_product->pivot->in_stock <= $shop_product->pivot->reorder_point && $shop_product->pivot->in_stock != 0){
                        $shop_product->pivot->status = 'Low Stock';
                    }
                    
                    $shop_product->pivot->save();
                    $message = 'Stock was successfully deleted';

                    $purchase = Purchase::where('id', $stock->purchase_id)->where('shop_id', $shop->id)->first();
                    if (!is_null($purchase)) {
                        $pitems = Stock::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->get();
                        $total_amount = 0;
                        foreach ($pitems as $key => $item) {
                            $total_amount += ($item->quantity_in*$item->buying_per_unit);
                        }

                        $purchase->total_amount = $total_amount;
                        $purchase->save();

                        $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->first();
                        if (!is_null($acctrans)) {
                            $acctrans->amount = $purchase->total_amount;
                            $acctrans->save();
                        }
                    }
                    
                    return redirect()->route('products.show' , encrypt($product->id))->with('success', $message);
                }else{

                    $message = 'Stock cannot be deleted because has sales associated with';
                    return redirect()->route('products.show' , encrypt($product->id))->with('info', $message);
                }
            }
        }else{
            return redirect('unauthorized');
        }
    }
}
