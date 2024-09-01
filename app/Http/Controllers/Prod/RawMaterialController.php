<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Crypt;
use DB;
use Auth;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\RawMaterial;
use App\Models\RmItem;
use App\Models\RmDamage;
use App\Models\RmUseItem;

class RawMaterialController extends Controller
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

        $units = array(
            'pcs' => 'Piece',
            'kgs' => 'Kilogram',
            'lts' => 'Liter',
            'prs' => 'Pair',
            'cts' => 'Carton',
            'pks' => 'Pack',
            'dzs' => 'Dozen',
            'fts' => 'Foot',
            'set' => 'Set',
            'gls' => 'Gallon',
            'box' => 'Box',
            'btl' => 'Bottle',
            'mts' => 'Meter'
        );
        
        $page = 'Raw Materials';
        $title = 'Raw Materials';
        $title_sw = 'Malighafi';
        // if ($expired == 0) {
            $shop = Shop::find(Session::get('shop_id'));

            $materials = $shop->rawMaterials()->where('is_deleted' , false)->get();

            return view('production.raw-materials.index', compact('page', 'title', 'title_sw', 'materials', 'units', 'shop'));
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

          
        $now = Carbon::now();
        $material = RawMaterial::where('name', $request['name'])->where('basic_unit', $request['basic_unit'])->first();

        // return $material;

        if (is_null($material)) {
            $material = RawMaterial::create([
                'shop_id' => $shop->id, 
                'name' => $request['name'], 
                'basic_unit' => $request['basic_unit'], 
                'type' => $request['type']
            ]);
        }

        if (!is_null($request['qty'])) {
            $unitcost = $request['unit_cost'];
            $total = $request['qty']*$unitcost;

            $rmitem = RmItem::create([
                'raw_material_id' => $material->id,
                'qty' => $request['qty'],
                'unit_cost' => $unitcost,
                'total' => $total,
                'date' => $now,
                'shop_id' => $shop->id
            ]);
        }

        $rmshop = $shop->rawMaterials()->where('raw_material_id', $material->id)->where('is_deleted' , false)->first();

        if (is_null($rmshop)) {
            $shop->rawMaterials()->attach($material, ['in_store' => $request['qty'], 'unit_cost' => $unitcost = $request['unit_cost'] , 'description' => $request['description']]);
        }

        return redirect()->back()->with('success', 'Raw Material was registered successful');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = 'Raw Materials';
        $title = 'Raw Material Details';
        $title_sw = 'Maelezo ya Malighafi';
        $shop = Shop::find(Session::get('shop_id'));
        $raw_material = RawMaterial::find(Crypt::decrypt($id));

        $material = $shop->rawMaterials()->where('raw_material_id', $raw_material->id)->where('is_deleted' , false)->first();


        $rmitems = RmItem::where('raw_material_id', $raw_material->id)->where('rm_items.shop_id', $shop->id)->where('rm_items.is_deleted' , false)->join('raw_materials', 'raw_materials.id', '=', 'rm_items.raw_material_id')->leftJoin('rm_purchases' , 'rm_purchases.id' , '=' , 'rm_items.rm_purchase_id')->leftJoin('suppliers' ,  'suppliers.id' , '=' , 'rm_purchases.supplier_id')->orderBy('rm_items.date', 'desc')->get([
                'rm_items.id',
                'raw_materials.name' , 
                'suppliers.name as sp_name',
                'total',
                'qty',
                'unit_cost',
                'rm_purchase_id',
                'purchase_type',
                'rm_items.date',
             ]);

        $rm_uses = RmUseItem::where('raw_material_id', $material->id)->where('rm_use_items.shop_id', $shop->id)->leftJoin('rm_uses' , 'rm_uses.id' , '=' , 'rm_use_items.rm_use_id')->get();

        $damages = RmDamage::where('raw_material_id', $material->id)->where('shop_id', $shop->id)->get();
        $t_dam = RmDamage::where('raw_material_id', $material->id)->where('shop_id', $shop->id)->sum('quantity');
        return view('production.raw-materials.show', compact('page', 'title', 'title_sw', 'material', 'shop', 'rmitems', 't_dam', 'damages' , 'rm_uses'));
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
        $page = 'Edit Raw Material';
        $title = 'Edit Raw Material';
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
        
        $material = $shop->rawMaterials()->where('raw_materials.id' , decrypt($id))->where('is_deleted' , false)->first();

        return view('production.raw-materials.edit', compact('page', 'title', 'title_sw', 'material', 'units'));
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
        $material = RawMaterial::find(Crypt::decrypt($id));
        $material->name = $request['name'];
        $material->basic_unit = $request['basic_unit'];
        $material->type = $request['type'];
        $material->save();
        
        $material_pivot = DB::table('raw_material_shop')->where('raw_material_id' , $material->id)->where('shop_id' , $shop->id)->where('is_deleted' , false)->update( ['description' => $request['description'] , 'in_store' => $request['in_store'] ,  'unit_cost' => $request['unit_cost']]);


        return redirect('raw-materials')->with('success', 'Raw Material was updated successful');
    }


     public function newReorderPoint(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $material = $shop->rawMaterials()->where('raw_material_id', $request['raw_material_id'])->first();

        if (!is_null($material)) {
            $material->pivot->reorder_point = $request['reorder_point'];
            $material->pivot->save();
        }

        return redirect()->route('raw-materials.show', encrypt($material->id))->with('success', 'New Reorder Point was updated successful');
    }

    public function newBuyPrice(Request $request)
    {

        $shop = Shop::find(Session::get('shop_id'));
        $material = $shop->rawMaterials()->where('raw_material_id', $request['raw_material_id'])->first();

         if (!is_null($material)) {
            $material->pivot->unit_cost = $request['buying_per_unit'];
            $material->pivot->save();
        }

        $message = 'Price was successfully updated';

        return redirect()->back()->with('message', $message);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $material = RawMaterial::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));
        $rmaterials = $shop->rawMaterials()->where('is_deleted' , false)->where('raw_material_id' , $material->id)->get();
        
        if (!is_null($rmaterials)) {
            foreach($rmaterials as $value){
                $value->pivot->delete();
            }

        }

        return redirect()->back()->with('success', 'Raw Material was deleted successful');
    }

    public function deleteMultiple(Request $request){

        $shop = Shop::find(Session::get('shop_id'));

        $user = Auth::user();
        if (!is_null($request->input('id'))) {
                
            foreach ($request->input('id') as $key => $id) {
                $material = RawMaterial::find($id);
                $rmaterials = $shop->rawMaterials()->where('is_deleted' , false)->where('raw_material_id' , $material->id)->get();
                
                if (!is_null($rmaterials)) {
                    foreach($rmaterials as $value){
                        $value->pivot->delete();
                    }

                }
            }
            
            return redirect()->back()->with('success', 'Raw Material was deleted successful');  
        }else{

            $warning = 'No items selected. Please select at least one item';
            return redirect('raw-materials')->with('warning', $warning); 
        }
    }
}
