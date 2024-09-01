<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use Response;
use Session;
use Auth;
use App\Models\Shop;
use App\Models\User;
use App\Models\PackingMaterial;
use App\Models\PmUseItemTemp;
use App\Models\ProductMadeApiTemp;
use App\Models\Product;

class PmUseItemTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(PmUseItemTemp::where('pm_use_item_temps.shop_id', Session::get('shop_id'))->where('user_id', Auth::user()->id)->join('packing_materials', 'packing_materials.id', '=', 'pm_use_item_temps.packing_material_id')->select('pm_use_item_temps.id as id', 'pm_use_item_temps.quantity as quantity', 'pm_use_item_temps.unit_cost as unit_cost', 'pm_use_item_temps.total as total', 'packing_materials.name as name' , 'pm_use_item_temps.product_packed as product_packed' , 'pm_use_item_temps.unit_packed as unit_packed')->get());
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
        $sameitems = PmUseItemTemp::where('packing_material_id', $request['packing_material_id'])->where('user_id', $user->id)->count();
        
        if ($sameitems == 0) {
            $packing_material = $shop->packingMaterials()->where('packing_material_id', $request['packing_material_id'])->where('is_deleted' , false)->first();
            $stockItemTemp = new PmUseItemTemp;
            $stockItemTemp->shop_id = $shop->id;
            $stockItemTemp->user_id = $user->id;
            $stockItemTemp->packing_material_id = $request['packing_material_id'];
            $stockItemTemp->quantity  = 0;
            $stockItemTemp->unit_packed = 1;
            $stockItemTemp->unit_cost = is_null($packing_material->pivot->unit_cost) ? 0 :$packing_material->pivot->unit_cost ;
            $stockItemTemp->total = 0;
            $stockItemTemp->save();
            return $stockItemTemp;
            
        }else{
            $warning = 'Ooops!. The packing material already in selected items.';
            return redirect('add-stocks')->with('warning', $warning);
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

    public function saveProdTemp(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $pmusedItemTemp =  PmUseItemTemp::where('id', $request['tempid'])->where('user_id', Auth::user()->id)->where('shop_id', $shop->id)->first();
        if(!is_null($pmusedItemTemp)){
            $pmusedItemTemp->product_packed = $request['product_id'];
            $pmusedItemTemp->save();
        }

        return response()->json($pmusedItemTemp);
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
        $stockItemTemp =  PmUseItemTemp::where('id', $id)->where('user_id', $user->id)->where('shop_id', $shop->id)->first();
        if (!is_null($stockItemTemp)) {

            if ($stockItemTemp->total != $request['total']) {
                $stockItemTemp->total = $request['total'];
                if ($stockItemTemp->quantity != 0) {
                    $stockItemTemp->unit_cost = $stockItemTemp->total/$stockItemTemp->quantity;
                }
                $stockItemTemp->save();

                return $stockItemTemp;
                
            }elseif($stockItemTemp->unit_cost != $request['unit_cost']) { 
                $stockItemTemp->unit_cost = $request['unit_cost'];
                $stockItemTemp->total = $stockItemTemp->quantity*$stockItemTemp->unit_cost;
                $stockItemTemp->save();

                return $stockItemTemp;
            }elseif ($stockItemTemp->quantity != $request['quantity']) {

                $packing_material  = $shop->packingMaterials()->where('is_deleted', false)->where('packing_materials.id' , $stockItemTemp->packing_material_id)->first();


            if (is_null($packing_material->pivot->in_store) || $packing_material->pivot->in_store < $request['quantity']) {

                  return response()->json(['status' => 'LOW', 'msg' => 'Stock of Your Packing Material '.$packing_material->name.' is currently less than.'.($request['quantity'])]);
            } else {

                if ($packing_material->basic_unit == 'pcs' || $packing_material->basic_unit == 'prs' || $packing_material->basic_unit == 'box' || $packing_material->basic_unit == 'btl' || $packing_material->basic_unit == 'pks' || $packing_material->basic_unit == 'gls') {
                    if (!$this->is_decimal($request['quantity'])) {
                        $stockItemTemp->quantity  = $request['quantity'];
                        $stockItemTemp->total = $stockItemTemp->quantity*$stockItemTemp->unit_cost;
                        $stockItemTemp->save();
                        $prodmade = ProductMadeApiTemp::where('product_id',$stockItemTemp->product_packed)->where('shop_id' , $shop->id)->where('user_id', $user->id)->first();
                        if(!is_null($prodmade)){
                            $prodmade->qty = $request['quantity'];
                            $prodmade->save();
                        }

                        return $stockItemTemp;
                    }else{

                        return response()->json(['status' => 'WRONG', 'msg' => 'This packing material '.$packing_material->name.' can not accept decimal quantity. Please change its basic unit if you want to set decimal for stock quantity values']);
                    }
                }else{
                    $stockItemTemp->quantity  = $request['quantity'];
                    $stockItemTemp->total = $stockItemTemp->quantity*$stockItemTemp->unit_cost;
                    $stockItemTemp->save();

                    $prodmade = ProductMadeApiTemp::where('product_id',$stockItemTemp->product_packed)->where('shop_id' , $shop->id)->where('user_id', $user->id)->first();
                    if(!is_null($prodmade)){
                        $prodmade->qty = $request['quantity'];
                        $prodmade->save();
                    }

                    return $stockItemTemp;
                }
            }

            }elseif ($stockItemTemp->unit_packed != $request['unit_packed'] && $request['unit_packed'] != 0 ) {
                $stockItemTemp->unit_packed = $request['unit_packed'];
                $stockItemTemp->save();
                $prodmade = ProductMadeApiTemp::where('product_id',$stockItemTemp->product_packed)->where('shop_id' , $shop->id)->where('user_id', $user->id)->first();
                if(!is_null($prodmade)){
                    $prodmade->unit_packed = $request['unit_packed'];
                    $prodmade->save();
                }
                return $stockItemTemp;

            }elseif ($stockItemTemp->product_packed != $request['product_packed']) {

                    $prod = Product::find($request['product_packed']);
                    
                    $oldset = ProductMadeApiTemp::where('product_id',$stockItemTemp->product_packed)->where('shop_id' , $shop->id)->where('user_id', $user->id)->where('packing_material_id' , $stockItemTemp->packing_material_id);
                    
                    if ($oldset->count() == 0) { 

                        $product_made = $user->ProductMade()->create([
                            'name' => $prod->name,
                            'shop_id' => $shop->id,
                            'product_id' =>$request['product_packed'],
                            'packing_material_id' => $stockItemTemp->packing_material_id,
                            'qty' => 1,
                            'cost_per_unit' => 0,
                        ]);  
                        
                    }else{

                        $oldset->first()->delete();

                        $product_made = $user->ProductMade()->create([
                            'name' => $prod->name,
                            'shop_id' => $shop->id,
                            'product_id' =>$request['product_packed'],
                            'packing_material_id' => $stockItemTemp->packing_material_id,
                            'qty' => 1,
                            'cost_per_unit' => 0,
                        ]);  
                        
                    }

                    $stockItemTemp->product_packed = $request['product_packed'];
                    $stockItemTemp->save();

                    return $stockItemTemp;
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
        $pmutemp = PmUseItemTemp::find($id);
        if (!is_null($pmutemp)) {
            $prodmade = ProductMadeApiTemp::where('product_id',$pmutemp->product_packed)->where('shop_id' , $pmutemp->shop_id)->where('user_id', $pmutemp->user_id)->where('packing_material_id' , $pmutemp->packing_material_id)->first();
            if (!is_null($prodmade)) {
                $prodmade->delete();
            }
            $pmutemp->delete();
        }
    }

     function is_decimal($val)
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }
}
