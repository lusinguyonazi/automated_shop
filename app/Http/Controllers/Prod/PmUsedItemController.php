<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;
use Crypt;
use Auth;
use App\Models\Shop;
use App\Models\PmUseItem;
use App\Models\pmitem;
use App\Models\PmUse;
use App\Models\PackingMaterial;
use App\Models\Product;

class PmUsedItemController extends Controller
{
     public function __construct()
    {
        $this->middleware(['auth']);
    }

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
        $page = 'Packing Materials';
        $title = 'Edit Item Used';
        $title_sw = 'Hariri Kilichotumika';
        
        $shop = Shop::find(Session::get('shop_id'));
        $pmitem = PmUseItem::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        $products = $shop->products()->get();

        $prod = Product::where('id' ,  $pmitem->product_packed)->first();

        if (is_null($pmitem)) {
            return redirect('forbiden');
        }else{
            $pmuseditem = PmUseItem::where('pm_use_items.id', decrypt($id))->where('pm_use_items.shop_id', $shop->id)->join('packing_materials' , 'packing_materials.id' , '=' , 'pm_use_items.packing_material_id')->first();


            return view('production.packing-materials.pm-uses.edit-used-item', compact('page', 'title', 'title_sw', 'pmuseditem', 'pmitem', 'products', 'prod', 'shop'));
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
        $pmitem = PmUseItem::find(decrypt($id));
        $pmuse = PmUse::find($pmitem->pm_use_id);
        $pm = $shop->packingMaterials()->where('packing_material_id' , $pmitem->packing_material_id)->first();

          if( ($pm->pivot->in_store + $pmitem->quantity)-($request['quantity']) < 0 ){

            return redirect()->route('pm-uses.show', encrypt($pmitem->pm_use_id))->with('error' , "Stock Available is less than edited amount");
          }else{

             $diff = $pmitem->quantity - $request['quantity'];
             $pmitem->product_packed  = $request['produt_id'];
             $pmitem->unit_packed = $request['unit_packed'];
             $pmitem->quantity = $request['quantity'];
             $pmitem->unit_cost = $request['unit_cost'];
             $total_cost = $request['quantity']* $request['unit_cost'];
             $pmitem->total = $total_cost;
             $pmitem->save();
             
             $pm->pivot->in_store = ($pm->pivot->in_store + $diff);
             $pm->pivot->save();
          }

          $total_use = PmUseItem::where('shop_id' , $shop->id)->where('pm_use_id' , $pmitem->pm_use_id)->sum('total');
          $pmuse->total_cost = $total_use;
          $pmuse->save();

           return redirect()->route('pm-uses.show', encrypt($pmitem->pm_use_id))->with('Success' , "Item Updated Successful");
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
        $pmitem = PmUseItem::find(decrypt($id));
        $pmuse = PmUse::find($pmitem->pm_use_id);

        $pm = $shop->packingMaterials()->where('packing_material_id' , $pmitem->packing_material_id)->first();
        $pm->pivot->in_store =  $pm->pivot->in_store + $pmitem->quantity;
        $pm->pivot->save();
        $pmitem->delete();

         $total_use = PmUseItem::where('shop_id' , $shop->id)->where('pm_use_id' , $pmuse->id)->sum('total');
          $pmuse->total_cost = $total_use;
          $pmuse->save();

        $left_pmitem = PmUseItem::where('shop_id' , $shop->id)->where('pm_use_id' , $pmuse->id)->first();

        if(is_null($left_pmitem)){
            $pmuse->delete();
            return redirect('pm-uses')->with('success' , 'Packing Materials Uses Deleted successfully');
        }

        return redirect()->route('pm-uses.show', Crypt::encrypt($pmitem->pm_use_id))->with('Success' , "Item Deleted Successful");
    }
}
