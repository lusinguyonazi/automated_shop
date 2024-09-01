<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Response;
use Auth;
use Log;
use App\Models\Shop;
use App\Models\User;
use App\Models\RmUseItemTemp;
use App\Models\PmUseItemTemp;
use App\Models\MroUsedItemTemp;
use App\Models\Product;
use App\Models\ProductMadeApiTemp;


class ProductionApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user =  User::find(1);
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $mros = $shop->mro()->where('is_deleted' , false)->get([
            \DB::raw('id'),
            \DB::raw('name'),]);
        $pms = $shop->packingMaterials()->where('is_deleted' , false)->get([
            \DB::raw('packing_material_id as id'),
            \DB::raw('name'),
            \DB::raw('in_store'),
            \DB::raw('unit_cost'),
            \DB::raw('description')]);
        $rms = $shop->rawMaterials()->where('is_deleted' , false)->get([
            \DB::raw('raw_material_id as id'),
            \DB::raw('name'),
            \DB::raw('in_store'),
            \DB::raw('unit_cost'),
            \DB::raw('description')]);

        $products = $shop->products()->get([
            \DB::raw('product_id as id'),
            \DB::raw('product_no'),
            \DB::raw('barcode'),
            \DB::raw('name')]);

        $product_made = ProductMadeApiTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->get();

        return Response::json(['mros'=>$mros , 'pms' => $pms , 'rms' => $rms , 'products' => $products , 'product_made' => $product_made]);
    }

    public function product_made(){
         $shop = Shop::find(Session::get('shop_id'));
         $user = Auth::user();
         $product_made = ProductMadeApiTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->get();
        return Response::json(['product_made' => $product_made]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();

        $prod = Product::find($request->product_packed)->first();

       
       $sameitems = ProductMadeApiTemp::where('product_id', $prod->id)->where('shop_id' , $shop->id)->where('user_id', $user->id)->count();

            if ($sameitems == 0) {

                $product_made = $user->ProductMade()->create([
                    'name' => $prod->name,
                    'shop_id' => $shop->id,
                    'product_id' => $prod->id,
                    'qty' => 1,
                    'cost_per_unit' => 0,
                ]);
                
            }else{

              return Response()->json(['status' => 'warning' , 'msg' => 'This product is Selected Aready']);  
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

        if($request->for == 'rm'){

            $sameitems = RmUseItemTemp::where('raw_material_id', $request['rm_id'])->where('user_id', $user->id)->count();

            if ($sameitems == 0) {
                $raw_material = $shop->rawMaterials()->where('raw_material_id', $request['rm_id'])->where('is_deleted' , false)->first();

                $stockItemTemp = new RmUseItemTemp;
                $stockItemTemp->shop_id = $shop->id;
                $stockItemTemp->user_id = $user->id;
                $stockItemTemp->raw_material_id = $request['rm_id'];
                $stockItemTemp->quantity  = 1;
                $stockItemTemp->unit_cost = is_null($raw_material->pivot->unit_cost) ? 0 : $raw_material->pivot->unit_cost ;
                $stockItemTemp->total = $stockItemTemp->unit_cost;
                $stockItemTemp->save();
                return $stockItemTemp;

                
            }else{
                $rmitems = RmUseItemTemp::where('raw_material_id', $request['rm_id'])->where('user_id', $user->id)->first();
                $rmitems->quantity =  $rmitems->quantity + 1;
                $rmitems->total = ($rmitems->quantity * $rmitems->unit_cost);
                $rmitems->save();
                return $rmitems;
            }
        }elseif($request->for == 'pm'){
            $sameitems = PmUseItemTemp::where('packing_material_id', $request['pm_id'])->where('user_id', $user->id)->count();
        
            if ($sameitems == 0) {
                $packing_material = $shop->packingMaterials()->where('packing_material_id', $request['pm_id'])->where('is_deleted' , false)->first();
                $stockItemTemp = new PmUseItemTemp;
                $stockItemTemp->shop_id = $shop->id;
                $stockItemTemp->user_id = $user->id;
                $stockItemTemp->packing_material_id = $request['pm_id'];
                $stockItemTemp->quantity  = 1;
                $stockItemTemp->unit_packed = 1;
                $stockItemTemp->unit_cost = is_null($packing_material->pivot->unit_cost) ? 0 :$packing_material->pivot->unit_cost ;
                $stockItemTemp->total = is_null($packing_material->pivot->unit_cost) ? 0 :$packing_material->pivot->unit_cost;
                $stockItemTemp->save();
                return $stockItemTemp;
                
            }else{
                $pmitems = PmUseItemTemp::where('packing_material_id', $request['pm_id'])->where('user_id', $user->id)->first();
                $pmitems->quantity = $pmitems->quantity + 1;
                $pmitems->total = $pmitems->quantity * $pmitems->unit_cost;
                $pmitems->save();
                return $pmitems;
            }
        }elseif($request->for = 'mro'){
            $sameitems = MroUsedItemTemp::where('mro_id', $request['mro_id'])->where('user_id', $user->id)->count();
           
            if ($sameitems == 0) {
                $mroItemTemp = new MroUsedItemTemp;
                $mroItemTemp->shop_id = $shop->id;
                $mroItemTemp->user_id = $user->id;
                $mroItemTemp->mro_id = $request['mro_id'];
                $mroItemTemp->quantity  = 1;
                $mroItemTemp->unit_cost = 0;
                $mroItemTemp->total = 0;
                $mroItemTemp->save();
                return $mroItemTemp;  
            }else{
                $mroitems = MroUsedItemTemp::where('mro_id', $request['mro_id'])->where('user_id', $user->id)->first();
                $mroitems->quantity = $mroitems->quantity +1 ;
                $mroitems->total = $mroitems->quantity * $mroitems->unit_cost;
                $mroitems->save();

                return $mroitems;
            }
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
        $user = Auth::user();
        $pm = PmUseItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->sum('total');
        $rm = RmUseItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->sum('total');
        $mro = MroUsedItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->sum('total');
        $product_made = ProductMadeApiTemp::find($id);

        if ($product_made->qty !== $request->qty) {
            $product_made->qty = $request->qty;
            $product_made->save();

            $prod_api = ProductMadeApiTemp::where('shop_id' , $shop->id)->where('user_id', $user->id)->get();
            $total_vol = 0;
            foreach ($prod_api as $key => $prodmade) {
                $total_vol += $prodmade->qty*$prodmade->unit_packed;
            }
            
            if ($total_vol > 0) {
                foreach($prod_api as $value){
                    $value->cost_per_unit = ($pm+$rm+$mro)*(($value->unit_packed)/$total_vol); 
                    $value->selling_price = ($value->profit_margin + $value->cost_per_unit);
                    $value->save();
                }
            }
        }elseif($product_made->selling_price !== $request->selling_price){
            $product_made->profit_margin = $request->selling_price - $product_made->cost_per_unit;
            $product_made->selling_price = $request->selling_price;
            $product_made->save();
        }elseif ($product_made->profit_margin !== $request->profit_margin) {

            $product_made->profit_margin = $request->profit_margin;
            $product_made->selling_price = $product_made->cost_per_unit + $request->profit_margin;
            $product_made->save();
        }elseif ($product_made->unit_packed != $request->unit_packed) {
            $product_made->unit_packed = $request->unit_packed;
            $product_made->save();
            $prod_api = ProductMadeApiTemp::where('shop_id' , $shop->id)->where('user_id', $user->id)->get();
            $total_vol = 0;
            foreach ($prod_api as $key => $prodmade) {
                $total_vol += $prodmade->qty*$prodmade->unit_packed;
            }
            
            if ($total_vol > 0) {
                foreach($prod_api as $value){
                    $value->cost_per_unit = ($pm+$rm+$mro)*(($value->unit_packed)/$total_vol); 
                    $value->selling_price = ($value->profit_margin + $value->cost_per_unit);
                    $value->save();
                }
            }
            if (!is_null($product_made->packing_material_id)) {
                $pm = PmUseItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->where('product_packed', $product_made->product_id)->where('packing_material_id', $product_made->packing_material_id)->first();
                $pm->unit_packed = $product_made->unit_packed;
                $pm->save();
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
        ProductMadeApiTemp::destroy($id);
    }

    public function recalculate() {

        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $pm = PmUseItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->sum('total');
        $rm = RmUseItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->sum('total');
        $mro = MroUsedItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->sum('total');
        $prod_api = ProductMadeApiTemp::where('shop_id' , $shop->id)->where('user_id', $user->id);
        $qty_made = $prod_api->sum('qty') ;
        if($qty_made == 0){
            $cost_per_unit = 0;
        }else{
          $cost_per_unit = ($pm+$rm+$mro)/$qty_made;  
        }
            
        foreach($prod_api->get()  as $value){
            $value->cost_per_unit = $cost_per_unit;
            $value->selling_price = ($value->profit_margin + $value->cost_per_unit);
            $value->save();
        }
    }

}
