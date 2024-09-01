<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Crypt;
use \Carbon\Carbon;
use Session;
use App\Models\Shop;
use App\Models\Product;
use App\Models\ProdDamage;
use App\Models\Stock;
use App\Models\AnSaleItem;
use App\Models\TransferOrderItem;
use App\Models\SaleReturnItem;
use App\Models\Setting;

class ProdDamageController extends Controller
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
        $shop = Shop::find(Session::get('shop_id'));
        $product = Product::find($request['product_id']);
        $now = Carbon::now();
        if (!is_null($request['dam_date'])) {
            $time = date('h:i:s', strtotime($now));
            $now = $request['dam_date'].' '.$time;
        }
        $shop_product = $shop->products()->where('product_id', $product->id)->first();
        if (!is_null($request['quantity'])) {
            $pdam = ProdDamage::create([
                'product_id' => $product->id,
                'shop_id' => $shop->id,
                'quantity' => $request['quantity'],
                'selling_price' => $shop_product->pivot->price_per_unit,
                'buying_price'  => $shop_product->pivot->buying_per_unit,
                'reason' => $request['reason'],
                'time_created' => $now
            ]);
        }else{
            if (!is_null($request['deph_measure'])) {
                $quantity = $shop_product->pivot->in_stock-$request['deph_measure'];
                $pdam = ProdDamage::create([
                    'product_id' => $product->id,
                    'shop_id' => $shop->id,
                    'deph_measure' => $request['deph_measure'],
                    'in_stock' => $shop_product->pivot->in_stock,
                    'quantity' => $quantity,
                    'selling_price' => $shop_product->pivot->price_per_unit,
                    'buying_price' => $shop_product->pivot->buying_per_unit,
                    'reason' => $request['reason'],
                    'time_created' => $now
                ]);
            }
        }


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

        $message = 'Damaged Items was successfully recorded';

        return redirect()->back()->with('success', $message);
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
        $pdam = ProdDamage::where('id', Crypt::decrypt($id))->where('shop_id', $shop->id)->first();
        if (is_null($pdam)) {
            return redirect('forbiden');
        }else{
            $product = Product::find($pdam->product_id);
            $page = 'Edit damaged';
            $title = 'Edit damaged';
            $title_sw = 'Hariri Iliyoharibika';

            return view('products.damaged.edit', compact('page', 'title', 'title_sw', 'pdam', 'product', 'settings'));
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
        $settings = Setting::where('shop_id', $shop->id)->first();
        $now = Carbon::now();
        if (!is_null($request['dam_date'])) {
            $time = date('h:i:s', strtotime($now));
            $now = $request['dam_date'].' '.$time;
        }   

        $pdam = ProdDamage::find(decrypt($id));
        if ($settings->is_filling_station) {
            $pdam->deph_measure = $request['deph_measure'];
            $pdam->quantity = $pdam->in_stock-$pdam->deph_measure;
        }else{
            $pdam->quantity = $request['quantity'];
        }
        $pdam->reason = $request['reason'];
        $pdam->save();

        $product = Product::find($pdam->product_id);
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

        $message = 'Product Damage was successfully updated';

        return redirect()->route('products.show' , encrypt($product->id))->with('success', $message);
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
        $pdam = ProdDamage::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (is_null($pdam)) {
            return redirect('forbiden');
        }else{
            $product = Product::find($pdam->product_id);
            $pdam->delete();

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

            return redirect()->route('products.show' , encrypt($product->id))->with('success', $message);
        }
    }
}


