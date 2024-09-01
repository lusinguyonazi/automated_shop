<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Crypt;
use Auth;
use App\Models\Shop;
use App\Models\PackingMaterial;
use App\Models\PmItem;
use App\Models\PmDamage;
use App\Models\PmUseItem;


class PackingMaterialController extends Controller
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
        $expired = Session::get('expired');

        $page = 'Packing Materials';
        $title = 'Packing Materials';
        $title_sw = 'Malighafi';
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
        
        // if ($expired == 0) {
            $shop = Shop::find(Session::get('shop_id'));

            $pmaterials = $shop->packingMaterials()->where('is_deleted' , false)->get();
            return view('production.packing-materials.index', compact('page', 'title', 'title_sw', 'pmaterials', 'units', 'shop'));
        // } else {
        //     $info = 'Dear customer your account is not activated please make payment and activate now.';
        //     return redirect('verify-payment')->with('error', $info);
        // }
        
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

        $now = \Carbon\Carbon::now();
        $material = PackingMaterial::where('name', $request['name'])->where('basic_unit', $request['basic_unit'])->first();

        if (is_null($material)) {
            $material = PackingMaterial::create([
                'shop_id' => $shop->id, 
                'name' => $request['name'], 
                'basic_unit' => $request['basic_unit'], 
                'type' => $request['type']
            ]);
        }

        if (!is_null($request['qty'])) {
            $unitcost = 0;
            if (!is_null($request['unitcost'])) {        
               $unitcost = $request['unit_cost'];
            }
            $total = $request['qty']*$unitcost;
            $pmitem = PmItem::create([
                'shop_id' => $shop->id,
                'packing_material_id' => $material->id,
                'qty' => $request['qty'],
                'unit_cost' => $unitcost,
                'total' => $total,
                'date' => $now
            ]);
        }

        $rmshop = $shop->packingMaterials()->where('is_deleted' , false)->where('packing_material_id', $material->id)->first();

        if (is_null($rmshop)) {
            $shop->packingMaterials()->attach($material, ['in_store' => $request['qty'], 'unit_cost' => $request['unit_cost'], 'description' => $request['description']]);
        }

        return redirect()->back()->with('success', 'Packing Material was registered successful');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = 'Packing Materials';
        $title = 'Packing Material Details';
        $title_sw = 'Maelezo ya Malighafi';
        $shop = Shop::find(Session::get('shop_id'));
        $packing_material = PackingMaterial::find(decrypt($id));

        $material = $shop->packingMaterials()->where('is_deleted' , false)->where('packing_material_id', $packing_material->id)->first();
        $pmitems = PmItem::where('packing_material_id', $packing_material->id)->where('pm_items.shop_id', $shop->id)->where('pm_items.is_deleted' , false)->join('packing_materials', 'packing_materials.id', '=', 'pm_items.packing_material_id')->leftJoin('pm_purchases' , 'pm_purchases.id' , '=' , 'pm_items.pm_purchase_id')->leftJoin('suppliers' ,  'suppliers.id' , '=' , 'pm_purchases.supplier_id')->orderBy('pm_items.date', 'desc')->get([
                'pm_items.id as id',
                'packing_materials.name' , 
                'suppliers.name as sp_name',
                'total',
                'qty',
                'unit_cost',
                'pm_purchase_id',
                'purchase_type',
                'pm_items.date',
             ]);
        $pm_uses = PmUseItem::where('packing_material_id', $material->id)->where('pm_use_items.shop_id', $shop->id)->join('pm_uses'  , 'pm_uses.id' , '=' , 'pm_use_items.pm_use_id' )->get();
        $damages = PmDamage::where('packing_material_id', $material->id)->where('shop_id', $shop->id)->get();
        $t_dam = PmDamage::where('packing_material_id', $material->id)->where('shop_id', $shop->id)->sum('quantity');
        return view('production.packing-materials.show', compact('page', 'title', 'title_sw', 'material', 'shop', 'pmitems', 't_dam', 'damages' , 'pm_uses'));
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
        $title = 'Edit Packing Material';
        $title_sw = 'Hariri Malighafi';
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
        $material = $shop->packingMaterials()->where('is_deleted' , false)->where('packing_material_id', decrypt($id))->first();
        // $material = PackingMaterial::find(Crypt::decrypt($id));
        $pmitem = PmItem::where('packing_material_id', $material->id)->where('shop_id', $shop->id)->first();
        

        return view('production.packing-materials.edit', compact('page', 'title', 'title_sw', 'material', 'pmitem', 'units'));
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
        $material = PackingMaterial::find(decrypt($id));
        $material->name = $request['name'];
        $material->basic_unit = $request['basic_unit'];
        $material->save();

        return redirect('packing-materials')->with('success', 'Packing Material was updated successful');
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

        $material = PackingMaterial::find(decrypt($id));
        $pmaterials = $shop->packingMaterials()->where('is_deleted' , false)->where('packing_material_id' , $material->id)->get();

        if (!is_null($pmaterials)) {

            foreach($pmaterials as $value){
                $value->pivot->delete();
            }
        }

        return redirect()->back()->with('success', 'Packing Material was deleted successful');
    }

     public function deleteMultiple(Request $request){

        $shop = Shop::find(Session::get('shop_id'));

        $user = Auth::user();
        if (!is_null($request->input('id'))) {
                
            foreach ($request->input('id') as $key => $id) {
                $material = PackingMaterial::find($id);
                $pmaterials = $shop->packingMaterials()->where('is_deleted' , false)->where('packing_material_id' , $material->id)->get();
                
                if (!is_null($pmaterials)) {
                    foreach($pmaterials as $value){
                        $value->pivot->delete();
                    }

                }
            }
            
            return redirect()->back()->with('success', 'Packing Material was deleted successful');  
        }else{

            $warning = 'No items selected. Please select at least one item';
            return redirect('packing-materials')->with('warning', $warning); 
        }
    }

     public function newReorderPoint(Request $request)
    {
        
        $shop = Shop::find(Session::get('shop_id'));
        $material = $shop->packingMaterials()->where('packing_material_id', $request['packing_material_id'])->first();


        if (!is_null($material)) {
            $material->pivot->reorder_point = $request['reorder_point'];
            $material->pivot->save();
        }

        return redirect()->back()->with('success', 'New Reorder Point was updated successful');
    }

    public function newBuyPrice(Request $request)
    {

        $shop = Shop::find(Session::get('shop_id'));
        $material = $shop->packingMaterials()->where('packing_material_id', $request['packing_material_id'])->first();

         if (!is_null($material)) {
            $material->pivot->unit_cost = $request['buying_per_unit'];
            $material->pivot->save();
        }

        $message = 'Price was successfully updated';

        return redirect()->route('packing-materials.show', encrypt($material->id))->with('message', $message);

    }
}
