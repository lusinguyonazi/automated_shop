<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Session;
use Auth;
use App\Models\MroUsedItemTemp;
use App\Models\Mro;
use App\Models\Shop;
use App\Models\MroItem;
use App\Models\User;

class MroUsedItemTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(MroUsedItemTemp::where('mro_used_item_temps.shop_id', Session::get('shop_id'))->where('user_id', Auth::id())->join('mros', 'mros.id', '=', 'mro_used_item_temps.mro_id')->select('mro_used_item_temps.id as id', 'mro_used_item_temps.quantity as quantity', 'mro_used_item_temps.unit_cost as unit_cost', 'mro_used_item_temps.total as total' ,'mros.name as name')->get());
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
        $sameitems = MroUsedItemTemp::where('mro_id', $request['mro_id'])->where('user_id', $user->id)->count();
           
        if ($sameitems == 0) {
            $mroItemTemp = new MroUsedItemTemp;
            $mroItemTemp->shop_id = $shop->id;
            $mroItemTemp->user_id = $user->id;
            $mroItemTemp->mro_id = $request['mro_id'];
            $mroItemTemp->quantity  = 0;
            $mroItemTemp->unit_cost = 0;
            $mroItemTemp->total = 0;
            $mroItemTemp->save();
            return $mroItemTemp;  
        }else{
            $warning = 'Ooops!. The Mro Item already in selected items.';
            return redirect('add-mros')->with('warning', $warning);
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
        $mroItemTemp =  mroUsedItemTemp::where('id', $id)->where('user_id', Auth::id())->where('shop_id', $shop->id)->first();
        if (!is_null($mroItemTemp)) {

            if ($mroItemTemp->total != $request['total']) {
                $mroItemTemp->total = $request['total'];
                if ($mroItemTemp->quantity != 0) {
                    $mroItemTemp->unit_cost = $mroItemTemp->total/$mroItemTemp->quantity;
                }
                $mroItemTemp->save();

                return $mroItemTemp;
                
            }elseif($mroItemTemp->unit_cost != $request['unit_cost']) {  
                $mroItemTemp->unit_cost = $request['unit_cost'];
                $mroItemTemp->total = $mroItemTemp->quantity*$mroItemTemp->unit_cost;
                $mroItemTemp->save();

                return $mroItemTemp;
            }elseif ($mroItemTemp->quantity != $request['quantity']) {

                $mro = Mro::find($mroItemTemp->mro_id);
                
                    if (!$this->is_decimal($request['quantity'])) {

                        $mroItemTemp->quantity  = $request['quantity'];
                        $mroItemTemp->unit_cost = $request['unit_cost'];
                        $mroItemTemp->total = $mroItemTemp->quantity*$mroItemTemp->unit_cost;
                        $mroItemTemp->save();

                        return $mroItemTemp;
                    }else{

                        return response()->json(['status' => 'WRONG', 'msg' => 'This mro '.$mro->name.' can not accept decimal quantity. Please change its basic unit if you want to set decimal for mro quantity values']);
                    }
                }else{
                    $mro = Mro::find($mroItemTemp->mro_id);
                    $mroItemTemp->unit_cost = $request['unit_cost'];
                    $mroItemTemp->quantity  = $request['quantity'];
                    $mroItemTemp->total = $mroItemTemp->quantity*$mroItemTemp->unit_cost;
                    $mroItemTemp->save();

                    return $mroItemTemp;
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
         MroUsedItemTemp::destroy($id);
    }

    function is_decimal($val)
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }
}
