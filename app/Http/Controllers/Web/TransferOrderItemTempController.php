<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Response;
use Session;
use Auth;
use Log;
use App\Models\Shop;
use App\Models\User;
use App\Models\TransferOrderItemTemp;
use App\Models\Product;
use App\Models\ProductUnit;

class TransferOrderItemTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(TransferOrderItemTemp::where('shop_id', Session::get('shop_id'))->where('user_id', Auth::user()->id)->with('product')->get());
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
        $sameitems = TransferOrderItemTemp::where('product_id', $request['product_id'])->where('user_id', $user->id)->where('shop_id', $shop->id)->count();
        
        if ($sameitems == 0) {
            $product = $shop->products()->where('product_id', $request['product_id'])->first();

            $destin = Shop::find($request['destin_id']);
            $destinproduct = $destin->products()->where('product_id', $request['product_id'])->first();
            if (is_null($destinproduct)) {
                $newproduct = Product::find($request['product_id']);

                $newdestinproduct = $destin->products()->attach($newproduct, ['in_stock' => 0, 'location' => null, 'product_no' => null, 'barcode' => $product->pivot->barcode, 'buying_per_unit' => $product->pivot->buying_per_unit, 'price_per_unit' => $product->pivot->price_per_unit, 'time_created' => \Carbon\Carbon::now()]);


                $prod_unit = new ProductUnit();
                $prod_unit->shop_id = $destin->id;
                $prod_unit->product_id = $product->id;
                $prod_unit->unit_name = $product->basic_unit;
                $prod_unit->is_basic = true;
                $prod_unit->qty_equal_to_basic = 1;
                $prod_unit->unit_price = $product->pivot->price_per_unit;
                $prod_unit->save();
            
                $destinproduct = $destin->products()->where('product_id', $request['product_id'])->first();
            }

            $orderItemTemp = new TransferOrderItemTemp;
            $orderItemTemp->shop_id = $shop->id;
            $orderItemTemp->user_id = $user->id;
            $orderItemTemp->product_id = $request['product_id'];
            $orderItemTemp->quantity = 0;
            $orderItemTemp->source_stock = $product->pivot->in_stock;
            if (!is_null($destinproduct->pivot->in_stock)) {
                $orderItemTemp->destin_stock = $destinproduct->pivot->in_stock;
            }else{
                $orderItemTemp->destin_stock = 0;
            }
            $orderItemTemp->source_unit_cost = $product->pivot->buying_per_unit;
            if (!is_null($destinproduct->pivot->buying_per_unit) && $destinproduct->pivot->buying_per_unit > 0) {
                $orderItemTemp->destin_unit_cost = $destinproduct->pivot->buying_per_unit;
            }else{
                $orderItemTemp->destin_unit_cost = $product->pivot->buying_per_unit;
            }
            $orderItemTemp->save();
            return $orderItemTemp;
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
        $orderItemTemp =  TransferOrderItemTemp::where('id', $id)->where('user_id', Auth::user()->id)->where('shop_id', $shop->id)->first();
        Log::info($orderItemTemp);
        if (!is_null($orderItemTemp)) {
            $product = Product::find($orderItemTemp->product_id);
            if ($orderItemTemp->source_stock < $request['quantity']) {

                return response()->json(['status' => 'LOW', 'msg' => 'Stock of Your Product '.$product->name.' is currently less than.'.($request['quantity'])]);
            }else{
                $orderItemTemp->quantity = $request['quantity'];
                $orderItemTemp->destin_unit_cost = $request['destin_unit_cost'];
                $orderItemTemp->save();
                return $orderItemTemp;
            }
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
        TransferOrderItemTemp::destroy($id);

        return response::json(['status' =>  'Deleted']);
    }
}
