<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Crypt;
use Session;
use Auth;
use App\Models\Shop;
use App\Models\User;
use App\Models\TransferOrder;
use App\Models\TransferOrderItemTemp;
use App\Models\TransferOrderItem;
use App\Models\SaleReturnItem;
use App\Models\Stock;
use App\Models\AnSaleItem;
use App\Models\ProdDamage;
use App\Models\TransformationTransferItem;

class TransferOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Products';
        $title = 'Stock Transfer Orders';
        $title_sw = 'Oda za Kuhamisha Stock';

        $shop = Shop::find(Session::get('shop_id'));
        $orders = TransferOrder::where('shop_id', $shop->id)->orderBy('created_at', 'desc')->get();

        $rece_transfers = TransferOrder::where('destination_id', $shop->id)->orderBy('created_at', 'desc')->get();
        
        return view('products.transfers.index', compact('page', 'title', 'title_sw', 'orders', 'shop'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'Products';
        $title = 'New Stock Transfer';
        $title_sw = 'Stock mpy ya kuhamisha';
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();

        $owner = $shop->users()->where('user_id', $user->id)->first();
        $destinations = $owner->shops()->where('shop_id', '!=', $shop->id)->get();
        $max_no = TransferOrder::where('shop_id', $shop->id)->orderBy('order_no', 'desc')->first();
        $order_no = 1;
        if (!is_null($max_no)) {
            $order_no = $max_no->order_no+1;
        }else{
            $order_no = 1;
        }

        return view('products.transfers.create', compact('page', 'title', 'title_sw', 'shop', 'destinations', 'order_no'));
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
        $user = Auth::user();
        $destinshop = $user->shops()->where('shop_id', $request['destin_id'])->first();
        $max_no = TransferOrder::where('shop_id', $shop->id)->orderBy('order_no', 'desc')->first();
        $order_no = 0;
        if (!is_null($max_no)) {
            $order_no = $max_no->order_no+1;
        }else{
            $order_no = 1;
        }

        $orderdate = $now;

        if (!is_null($request['order_date'])) {
            $orderdate = $request['order_date'];
        }

        $itemtemps = TransferOrderItemTemp::where('user_id', $user->id)->where('shop_id', $shop->id)->get();
        if (!is_null($itemtemps)) {
            $temps = array();
            foreach ($itemtemps as $key => $value) {
                if ($value->quantity == 0) {
                    array_push($temps, $value->quantity);
                }
            }

            if (!empty($temps)) {
                return redirect()->back()->with('warning', 'Please update the quantity of each item to continue');
            }else{
                $transorder = TransferOrder::create([
                    'order_no' => $order_no,
                    'order_date' => $orderdate,
                    'reason' => $request['reason'],
                    'user_id' => $user->id,
                    'shop_id' => $shop->id,
                    'destination_id' => $destinshop->id
                ]);

                foreach ($itemtemps as $key => $item) {
                    
                    $orderItem = new TransferOrderItem;
                    $orderItem->shop_id = $shop->id;
                    $orderItem->transfer_order_id = $transorder->id;
                    $orderItem->product_id = $item->product_id;
                    $orderItem->quantity = $item->quantity;
                    $orderItem->source_stock = $item->source_stock;
                    $orderItem->destin_stock = $item->destin_stock;
                    $orderItem->source_unit_cost = $item->source_unit_cost;
                    $orderItem->destin_unit_cost = $item->destin_unit_cost;
                    $orderItem->save();

                    $shop_product = $shop->products()->where('product_id', $item->product_id)->first();
                    $stock_in = Stock::where('product_id', $item->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                    $sold = AnSaleItem::where('product_id', $item->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                    $damaged = ProdDamage::where('product_id', $item->product_id)->where('shop_id', $shop->id)->sum('quantity');
                    $tranfered =  TransferOrderItem::where('product_id', $item->product_id)->where('shop_id', $shop->id)->sum('quantity');
                    $returned = SaleReturnItem::where('product_id', $item->product_id)->where('shop_id', $shop->id)->sum('quantity');
                        
                    $instock = ($stock_in+$returned)-($sold+$damaged+$tranfered);  
                   
                    $shop_product->pivot->in_stock = $instock;
                    $shop_product->pivot->save();

                    $dstock = Stock::create([
                        'product_id' => $item->product_id,
                        'shop_id' => $destinshop->id,
                        'quantity_in' => $item->quantity,
                        'buying_per_unit' => $item->destin_unit_cost,
                        'source' => 'Transfered (From: '.$shop->name.')',
                        'time_created' => $now,
                        'order_id' => $transorder->id
                    ]);

                    $destshop_product = $destinshop->products()->where('product_id', $item->product_id)->first();
                    $deststock_in = Stock::where('product_id', $item->product_id)->where('is_deleted', false)->where('shop_id', $destinshop->id)->sum('quantity_in');
                    $destsold = AnSaleItem::where('product_id', $item->product_id)->where('is_deleted', false)->where('shop_id', $destinshop->id)->sum('quantity_sold');
                    $destdamaged = ProdDamage::where('product_id', $item->product_id)->where('shop_id', $destinshop->id)->sum('quantity');
                    $desttranfered =  TransferOrderItem::where('product_id', $item->product_id)->where('shop_id', $destinshop->id)->sum('quantity');
                    
                    $destreturned = SaleReturnItem::where('product_id', $item->product_id)->where('shop_id', $destinshop->id)->sum('quantity');
                                    
                    $destinstock = ($deststock_in+$destreturned)-($destsold+$destdamaged+$desttranfered);
                    if (!is_null($destshop_product)) {
                            
                        $destshop_product->pivot->in_stock = $destinstock;
                        if ($destshop_product->pivot->in_stock <= $item->quantity) {
                            $destshop_product->pivot->buying_per_unit = $item->destin_unit_cost;
                        }
                        $destshop_product->pivot->save();
                    }
                }
            }

            foreach ($itemtemps as $key => $value) {
                $value->delete();
            }

            $success = 'Transfer Order was created successfully';
            return redirect('transfer-orders')->with('success', $success);
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
        $title = 'Stock Transfer Order';
        $title_sw = 'Oda ya Kuhamisha Stock';
        $transorder = TransferOrder::find(decrypt($id));

        if ($transorder->is_transfomation_transfer == 1) {
            $source = Shop::find($transorder->shop_id);
            $destin = Shop::find($transorder->destination_id);
            $user = User::find($transorder->user_id);
            $orderitems = TransformationTransferItem::where('transfer_order_id', $transorder->id)->with('product')->get();

         $transorder =  $transorder->join('products' , 'products.id' , 'transfer_orders.source_product_id')->where('transfer_orders.id' , Crypt::decrypt($id))->first();

            return view('products.transfers.transformation.show', compact('page', 'title', 'title_sw', 'transorder', 'source', 'destin', 'user', 'orderitems'));
        }else{
            $source = Shop::find($transorder->shop_id);
            $destin = Shop::find($transorder->destination_id);
            $user = User::find($transorder->user_id);
            $orderitems = TransferOrderItem::where('transfer_order_id', $transorder->id)->with('product')->get();

            return view('products.transfers.show', compact('page', 'title', 'title_sw', 'transorder', 'source', 'destin', 'user', 'orderitems'));
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
        $page = 'Products';
        $title = 'Edit Stock Transfer Order';
        $title_sw = 'Hariri Oda ya kuhamisha Stock';

        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $destinations = $user->shops()->where('shop_id', '!=', $shop->id)->get();
        $transorder = TransferOrder::find(decrypt($id));
        $destinshop = Shop::find($transorder->destination_id);
        $orderitems = TransferOrderItem::where('transfer_order_id', $transorder->id)->with('product')->get();
        return view('products.transfers.edit', compact('page', 'title', 'title_sw', 'shop', 'destinations', 'destinshop', 'transorder', 'orderitems'));   
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
        $user = Auth::user();
        $destinshop = $user->shops()->where('shop_id', $request['destin_id'])->first();
        $transorder = TransferOrder::find(Crypt::decrypt($id));
        $transorder->order_date = $request['order_date'];
        $transorder->destination_id = $destinshop->id;
        $transorder->reason = $request['reason'];
        $transorder->save();

        $success = 'Transfer Order updated successfully';
        return redirect('transfer-orders/'.Crypt::encrypt($transorder->id))->with('success', $success);
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
        $transorder = TransferOrder::find(Crypt::decrypt($id));
        if (!is_null($transorder)) {
            $destinshop = Shop::find($transorder->destination_id);
            $orderitems = TransferOrderItem::where('transfer_order_id', $transorder->id)->get();
            foreach ($orderitems as $key => $item) {

                $shop_product = $shop->products()->where('product_id', $item->product_id)->first();

                $item->delete();
                    
                $stock_in = Stock::where('product_id', $item->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                $sold = AnSaleItem::where('product_id', $item->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                $damaged = ProdDamage::where('product_id', $item->product_id)->where('shop_id', $shop->id)->sum('quantity');
                $tranfered =  TransferOrderItem::where('product_id', $item->product_id)->where('shop_id', $shop->id)->sum('quantity');
                $returned = SaleReturnItem::where('product_id', $item->product_id)->where('shop_id', $shop->id)->sum('quantity');
                        
                $instock = ($stock_in+$returned)-($sold+$damaged+$tranfered);
                $shop_product->pivot->in_stock = $instock;
                $shop_product->pivot->save();

            }

            $transorderstocks = Stock::where('shop_id', $destinshop->id)->where('order_id', $transorder->id)->get();
            foreach ($transorderstocks as $key => $orderstock) {
                $destshop_product = $destinshop->products()->where('product_id', $orderstock->product_id)->first();

                $orderstock->delete();
                $deststock_in = Stock::where('product_id', $orderstock->product_id)->where('is_deleted', false)->where('shop_id', $destinshop->id)->sum('quantity_in');
                $destsold = AnSaleItem::where('product_id', $orderstock->product_id)->where('is_deleted', false)->where('shop_id', $destinshop->id)->sum('quantity_sold');
                $destdamaged = ProdDamage::where('product_id', $orderstock->product_id)->where('shop_id', $destinshop->id)->sum('quantity');
                $desttranfered =  TransferOrderItem::where('product_id', $orderstock->product_id)->where('shop_id', $destinshop->id)->sum('quantity');
                $destreturned = SaleReturnItem::where('product_id', $orderstock->product_id)->where('shop_id', $destinshop->id)->sum('quantity');
                        
                $destinstock = ($deststock_in+$destreturned)-($destsold+$destdamaged+$desttranfered);
                if (!is_null($destshop_product)) {
                        
                    $destshop_product->pivot->in_stock = $destinstock;
                    $destshop_product->pivot->save();
                }
            }

            if($transorder->is_transfomation_transfer == 1){

                $transform =TransformationTransferItem::where('transfer_order_id', $transorder->id)->get();
                foreach($transform as $value){
                    $value->delete();

                }
            }
             
            $transorder->delete();
        }  

        $success = 'Transfer Order deleted successfully';
        return redirect('transfer-orders')->with('success', $success);
    }


    public function updateTransorderItem(Request $request)
    {
        $orderItem = TransferOrderItem::find($request['id']);

        if (!is_null($orderItem)) {
            $transorder = TransferOrder::find($orderItem->transfer_order_id);
            $destinshop = Shop::find($transorder->destination_id);
            
            $orderstock = Stock::where('shop_id', $destinshop->id)->where('order_id', $transorder->id)->where('product_id', $orderItem->product_id)->first();
           
            //Update destination stock
            $orderstock->quantity_in = $request['quantity'];
            $orderstock->save();

            $destshop_product = $destinshop->products()->where('product_id', $orderItem->product_id)->first();

            $deststock_in = Stock::where('product_id', $orderstock->product_id)->where('is_deleted', false)->where('shop_id', $destinshop->id)->sum('quantity_in');
            $destsold = AnSaleItem::where('product_id', $orderstock->product_id)->where('is_deleted', false)->where('shop_id', $destinshop->id)->sum('quantity_sold');
            $destdamaged = ProdDamage::where('product_id', $orderstock->product_id)->where('shop_id', $destinshop->id)->sum('quantity');
            $desttranfered =  TransferOrderItem::where('product_id', $orderstock->product_id)->where('shop_id', $destinshop->id)->sum('quantity');
                        
            $destreturned = SaleReturnItem::where('product_id', $orderstock->product_id)->where('shop_id', $destinshop->id)->sum('quantity');
                        
            $destinstock = ($deststock_in+$destreturned)-($destsold+$destdamaged+$desttranfered);
            $destshop_product->pivot->in_stock = $destinstock;
            $destshop_product->pivot->save();
            
            //Update Item
            $orderItem->quantity = $request['quantity'];
            $orderItem->save();

            $shop = Shop::find($transorder->shop_id);
            $shop_product = $shop->products()->where('product_id', $orderItem->product_id)->first();
                    
            $stock_in = Stock::where('product_id', $orderItem->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
            $sold = AnSaleItem::where('product_id', $orderItem->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
            $damaged = ProdDamage::where('product_id', $orderItem->product_id)->where('shop_id', $shop->id)->sum('quantity');
            $tranfered =  TransferOrderItem::where('product_id', $orderItem->product_id)->where('shop_id', $shop->id)->sum('quantity');
            $returned = SaleReturnItem::where('product_id', $orderItem->product_id)->where('shop_id', $shop->id)->sum('quantity');
                        
            $instock = ($stock_in+$returned)-($sold+$damaged+$tranfered);  
                   
            $shop_product->pivot->in_stock = $instock;
            $shop_product->pivot->save();
        }

        return redirect()->back();
    }

    public function deleteTransorderItem($id)
    {
        $orderItem = TransferOrderItem::find(Crypt::decrypt($id));

        if (!is_null($orderItem)) {
            $transorder = TransferOrder::find($orderItem->transfer_order_id);
            $shop = Shop::find($transorder->shop_id);
            $destinshop = Shop::find($transorder->destination_id);
            $orderstock = Stock::where('shop_id', $destinshop->id)->where('order_id', $transorder->id)->where('product_id', $orderItem->product_id)->first();

            $destshop_product = $destinshop->products()->where('product_id', $orderItem->product_id)->first();
            if (!is_null($orderstock)) {
                    
                $orderstock->delete();
                $deststock_in = Stock::where('product_id', $orderstock->product_id)->where('is_deleted', false)->where('shop_id', $destinshop->id)->sum('quantity_in');
                $destsold = AnSaleItem::where('product_id', $orderstock->product_id)->where('is_deleted', false)->where('shop_id', $destinshop->id)->sum('quantity_sold');
                $destdamaged = ProdDamage::where('product_id', $orderstock->product_id)->where('shop_id', $destinshop->id)->sum('quantity');
                $desttranfered =  TransferOrderItem::where('product_id', $orderstock->product_id)->where('shop_id', $destinshop->id)->sum('quantity');
                            
                $destreturned = SaleReturnItem::where('product_id', $orderstock->product_id)->where('shop_id', $shop->id)->sum('quantity');
                            
                $destinstock = ($deststock_in+$destreturned)-($destsold+$destdamaged+$desttranfered);
                $destshop_product->pivot->in_stock = $destinstock;
                $destshop_product->pivot->save();
            }

            $shop_product = $shop->products()->where('product_id', $orderItem->product_id)->first();

            $orderItem->delete();
            if (!is_null($shop_product)) {
                    
                $stock_in = Stock::where('product_id', $orderItem->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                $sold = AnSaleItem::where('product_id', $orderItem->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                $damaged = ProdDamage::where('product_id', $orderItem->product_id)->where('shop_id', $shop->id)->sum('quantity');
                $tranfered =  TransferOrderItem::where('product_id', $orderItem->product_id)->where('shop_id', $shop->id)->sum('quantity');
                $returned = SaleReturnItem::where('product_id', $orderItem->product_id)->where('shop_id', $shop->id)->sum('quantity');
                            
                $instock = ($stock_in+$returned)-($sold+$damaged+$tranfered);  
                       
                $shop_product->pivot->in_stock = $instock;
                $shop_product->pivot->save();
            }
        }

        return redirect()->back();
    }

    public function cancelOrder()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $itemtemps = TransferOrderItemTemp::where('user_id', $user->id)->where('shop_id', $shop->id)->get();
        foreach ($itemtemps as $key => $value) {
            $value->delete();
        }

        $success = 'Your Order was cancelled successfully';
        return redirect()->back()->with('success', $success);
            
    }   
}
