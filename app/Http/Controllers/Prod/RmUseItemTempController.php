<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Response;
use Input;
use Session;
use App\Models\Shop;
use App\Models\User;
use App\Models\RmUseItemTemp;
use App\Models\RawMaterial;
use Log;

class RmUseItemTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(RmUseItemTemp::where('rm_use_item_temps.shop_id', Session::get('shop_id'))->where('user_id', Auth::user()->id)->join('raw_materials', 'raw_materials.id', '=', 'rm_use_item_temps.raw_material_id')->select('rm_use_item_temps.id as id', 'rm_use_item_temps.quantity as quantity', 'rm_use_item_temps.unit_cost as unit_cost', 'rm_use_item_temps.total as total', 'raw_materials.name as name')->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
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
        $sameitems = RmUseItemTemp::where('raw_material_id', $request['raw_material_id'])->where('user_id', $user->id)->count();

        if ($sameitems == 0) {
            $raw_material = $shop->rawMaterials()->where('raw_material_id', $request['raw_material_id'])->where('is_deleted' , false)->first();

            $stockItemTemp = new RmUseItemTemp;
            $stockItemTemp->shop_id = $shop->id;
            $stockItemTemp->user_id = $user->id;
            $stockItemTemp->raw_material_id = $request['raw_material_id'];
            $stockItemTemp->quantity  = 0;
            $stockItemTemp->unit_cost = is_null($raw_material->pivot->unit_cost) ? 0 : $raw_material->pivot->unit_cost ;
            $stockItemTemp->total = 0;
            $stockItemTemp->save();
            return $stockItemTemp;

            
        }else{
            $warning = 'Ooops!. The raw_material already in selected items.';
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
        $stockItemTemp =  RmUseItemTemp::where('id', $id)->where('user_id', Auth::user()->id)->where('shop_id', $shop->id)->first();
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

            $raw_material = $shop->rawMaterials()->where('is_deleted', false)->where('raw_materials.id' , $stockItemTemp->raw_material_id)->first();


            if (is_null($raw_material->pivot->in_store) || $raw_material->pivot->in_store < $request['quantity']) {

                  return response()->json(['status' => 'LOW', 'msg' => 'Stock of Your Product '.$raw_material->name.' is currently less than.'.($request['quantity'])]);
            } else {

                if ($raw_material->basic_unit == 'pcs' || $raw_material->basic_unit == 'prs' || $raw_material->basic_unit == 'box' || $raw_material->basic_unit == 'btl' || $raw_material->basic_unit == 'pks' || $raw_material->basic_unit == 'gls') {
                    if (!$this->is_decimal($request->quantity)) {
                        $stockItemTemp->quantity  = $request['quantity'];
                        $stockItemTemp->total = $stockItemTemp->quantity*$stockItemTemp->unit_cost;
                        $stockItemTemp->save();

                        return $stockItemTemp;
                    }else{

                        return response()->json(['status' => 'WRONG', 'msg' => 'This raw_material '.$raw_material->name.' can not accept decimal quantity '.$request->quantity.'. Please change its basic unit if you want to set decimal for stock quantity values']);
                    }
                }else{

                    $raw_material = RawMaterial::find($stockItemTemp->raw_material_id);
                    $raw_material_detail = $shop->rawMaterials()->where('raw_material_id', $raw_material->id)->where('is_deleted' , false)->first();
                    $stockItemTemp->quantity  = $request['quantity'];
                    $stockItemTemp->total = $stockItemTemp->quantity*$stockItemTemp->unit_cost;
                    $stockItemTemp->save();
                    return $stockItemTemp;
                }

            }
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
        RmUseItemTemp::destroy($id);
    }


      function is_decimal($val)
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }
}
