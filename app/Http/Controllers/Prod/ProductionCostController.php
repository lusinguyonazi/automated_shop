<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use Response;
use Session;
use Crypt;
use Auth;
use Log;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\MroItem;
use App\Models\MroUse;
use App\Models\MroUsedItemTemp;

use App\Models\RawMaterial;
use App\Models\RmUse;
use App\Models\RmItem;
use App\Models\RmUseItem;
use App\Models\RmUseItemTemp;
use App\Models\RmDamage;

use App\Models\PackingMaterial;
use App\Models\PmUse;
use App\Models\PmUseItem;
use App\Models\PmItem;
use App\Models\PmUseItemTemp;
use App\Models\PmDamage;

use App\Models\ProductionCost;
use App\Models\ProductionCostItem;
use App\Models\Payment;

use App\Models\Product;
use App\Models\ProductMadeApiTemp;

class ProductionCostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $expired = Session::get('expired');
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $page = 'Production Records';
        $title = 'Production Records';
        $title_sw = 'Taarifa za Uzalishaji';
        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;            
        $end_date = null;
  
        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['stock_date'])) {

            $start_date = $request['stock_date'];
            $end_date = $request['stock_date'];
            $start = $request['stock_date'].' 00:00:00';
            $end = $request['stock_date'].' 23:59:59';
            $is_post_query = true;

        }else if (!is_null($request['start_date'])) {

            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;

        }else{

            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }


        $prod_records=ProductionCost::where('shop_id' , $shop->id)->where('is_deleted' , false)->whereBetween('date' , [$start , $end])->get(); 

         return view('production.index' , compact(['prod_records' , 'page' , 'title' , 'title_sw' , 'start' , 'end' , 'is_post_query' , 'start_date' , 'end_date']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $expired = Session::get('expired');
        $shop = Shop::find(Session::get('shop_id'));
        $page = 'Production Costs';
        $title = 'Production Costs';
        $title_sw = 'Gharama za Uzalishaji';
        $prod_record = ProductionCost::where('shop_id' , $shop->id)->max('prod_batch');

        $prod_batch = $prod_record+1;

        $mros = $shop->mro()->where('is_deleted' , false)->get();
        $pms = $shop->packingMaterials()->where('is_deleted' , false)->get();
        $rms = $shop->rawMaterials()->where('is_deleted' , false)->get();

        return view('production.create', compact(['page' , 'title' , 'title_sw' , 'shop' , 'mros' ,  'pms' , 'rms' , 'prod_batch']));
    }

    public function createold(Request $request){

        $expired = Session::get('expired');
        $shop = Shop::find(Session::get('shop_id'));
        $page = 'Production Costs';
        $title = 'Production Costs';
        $title_sw = 'Gharama za Uzalishaji';
        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', true)->first();
        if (!!is_null($payment)) {

        $rmuse_prod_batch = $shop->rmuse()->where('is_deleted' , false)->pluck('prod_batch');
        $mro_prod_batch = $shop->MroUse()->where('is_deleted' , false)->pluck('prod_batch');
        $pmuse_prod_batch = $shop->pmuse()->where('is_deleted' , false)->pluck('prod_batch');
        $products = $shop->products()->get();

        $all_batch = $rmuse_prod_batch->merge($mro_prod_batch)->merge($pmuse_prod_batch);

        $all_batch = collect($all_batch)->unique()->sortDesc()->values();

        $prod_b = ProductionCost::where('shop_id' , $shop->id)->where('is_transferred' , true)->pluck('prod_batch');

        $old_prod_batch = $all_batch->reject(function($all_batch) use($prod_b){
            return $prod_b->contains($all_batch);
        });
  
          if(is_null($request->prod_batch)){
         
            if($rmuse_prod_batch->isNotEmpty()){
                $prod_batch = $shop->rmuse()->where('is_deleted' , false)->get()->last()->prod_batch;
                $prod_date = $shop->rmuse()->where('is_deleted' , false)->get()->last()->created_at;
            }else{
             return redirect('rm-uses/create')->with('warning' , "No Raw Material was utilized");   
            }

          }else{
             $prod_batch = $request['prod_batch'];
             $prod_date = $shop->rmuse()->where('prod_batch' , $prod_batch)->where('is_deleted' , false)->get()->last();

             if(is_null($prod_date)){
                 $prod_date = $shop->pmuse()->where('prod_batch' , $prod_batch)->where('is_deleted' , false)->get()->last();
                 
                   if(is_null($prod_date)){
                     $prod_date = $shop->MroUse()->where('prod_batch' , $prod_batch)->where('is_deleted' , false)->get()->last();
                   }
             }

          }


                $mrouse = $shop->mroUse()->where('prod_batch' , $prod_batch)->where('is_deleted' , false)->get();
                $rmuse = RmUse::where('rm_uses.shop_id', $shop->id)->join('users', 'users.id', '=', 'rm_uses.user_id')->select('rm_uses.id as id', 'rm_uses.total_cost as total_cost', 'rm_uses.date as date', 'users.first_name as first_name', 'users.last_name as last_name', 'rm_uses.created_at as created_at')->where('prod_batch' , $prod_batch)->get();
                $pmuse = PmUse::where('pm_uses.shop_id', $shop->id)->where('is_deleted' , false)->join('users', 'users.id', '=', 'pm_uses.user_id')->select('pm_uses.id as id', 'pm_uses.total_cost as total_cost', 'pm_uses.date as date', 'users.first_name as first_name', 'users.last_name as last_name', 'pm_uses.created_at as created_at')->where('prod_batch' , $prod_batch)->get();

                $mros = [];
                $rms = [];
                $pms = []; 
                $pms_grouped = collect([]); 

                foreach($mrouse as $key => $v){
                      $mro_used_items = $shop->mroItems()->where('mro_items.is_deleted' , false)->join('mros' , 'mros.id' , '=' , 'mro_items.mro_id')->where('mro_use_id' , $v->id)->get();
                       array_push($mros , $mro_used_items); 
                }

                foreach($rmuse as $key => $v){

                      $rm_use_items = RmUseItem::where('rm_use_items.shop_id', $shop->id)->where('rm_use_id' , $v->id)->join('raw_materials', 'raw_materials.id', '=', 'rm_use_items.raw_material_id')->select('rm_use_items.id as id', 'rm_use_items.quantity as quantity', 'rm_use_items.unit_cost as unit_cost', 'rm_use_items.total as total', 'rm_use_items.date as date', 'raw_materials.name as name', 'raw_materials.basic_unit as basic_unit')->get();

                       array_push($rms , $rm_use_items); 
                }

                $pm_qty = 0; $pm_group = collect([]); $sum_unit_packed = 0;
                foreach($pmuse as $key => $v){

                      $pm_use_items  = PmUseItem::where('pm_use_items.shop_id', $shop->id)->where('pm_use_id' , $v->id)->join('packing_materials', 'packing_materials.id', '=', 'pm_use_items.packing_material_id')->join('products' , 'pm_use_items.product_packed' ,'=' , 'products.id')->select('pm_use_items.id as id', 'pm_use_items.quantity as quantity', 'pm_use_items.unit_cost as unit_cost', 'pm_use_items.total as total', 'pm_use_items.date as date', 'packing_materials.name as name', 'products.basic_unit as basic_unit' , 'products.name as product_name' , 'packing_materials.basic_unit as package_unit' , 'pm_use_items.unit_packed as unit_packed' , 'pm_use_items.packing_material_id as packing_material_id' , 'products.id as product_id')->get();



                       $pm_use_items_g  = PmUseItem::where('pm_use_items.shop_id', $shop->id)->where('pm_use_id' , $v->id)->join('products' , 'pm_use_items.product_packed' ,'=' , 'products.id')->groupBy('product_packed')->get([
                            \DB::raw('unit_packed as unit_packed'),
                            \DB::raw('SUM(quantity) as quantity'),
                            \DB::raw('pm_use_items.unit_cost as unit_cost'),
                            \DB::raw('product_packed as product_packed' ),
                            \DB::raw('products.name as name'),
                            \DB::raw('pm_use_items.packing_material_id as packing_material_id'),
                            \DB::raw('products.id as product_id'),
                       ]);

                      if(!is_null($pm_use_items->first())  && count($pm_use_items_g) == 1) {

                            $pm_qty = $pm_qty + $pm_use_items->sum('quantity');
                        }

                           array_push($pms , $pm_use_items); 
                     

                        if(count($pm_use_items_g) >= 1){

                            $pm_group->push($pm_use_items_g);
                        }

                 }   
           
                $pm_group =$pm_group->collapse()->groupby('product_packed');
                foreach($pm_group as $value){

                    $col = collect(['quantity' =>$value->sum('quantity') , 'unit_packed' => floatval($value->first()->unit_packed), 'product_packed' => $value->first()->product_packed  , 'name' => $value->first()->name , 'unit_cost' => $value->first()->unit_cost , 'product_id' => $value->first()->product_id , 'packing_material_id' => $value->first()->packing_material_id]);
                    $pms_grouped->push($col);

                    $sum_unit_packed += ($value->sum('quantity') * floatval($value->first()->unit_packed));
                }

                 return view('production.prod-cost.create' , compact('page', 'title', 'title_sw','pmuse' , 'rmuse' , 'mrouse' , 'mros' , 'rms' , 'pms' , 'old_prod_batch' , 'prod_batch' , 'prod_date' , 'pm_qty' , 'pms_grouped' , 'sum_unit_packed' , 'products' ));

        } else {
            $info = 'Dear customer you have not subscribed to this module please make payment and activate now.';
            return redirect('verify-payment')->with('error', $info);
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
        $user  = Auth::user();  
        $product_qty = $request['prod_qty'];
        $profit_margin = $request['profit_margin'];
        $date = Carbon::now();

        $product_id = $request->product_id;

        $prod_record = ProductionCost::where('shop_id' , $shop->id)->where('prod_batch' , $request['prod_batch'])->first();

        if(!is_null($prod_record)){
            $prod_items = ProductionCostItem::where('production_cost_id' , $prod_record)->get();
            foreach($prod_items as $prod_item){
                $prod_item->delete();
            }
            $prod_record->delete();
        }
  
         $prod_cost = ProductionCost::create([
            'total_prod_qty' =>  $request['product_v'],
            'total_cost' => $request['total_cost'], 
            'prod_batch' => $request['prod_batch'],
            'date' => $date,
            'user_id'=> $user->id,
            'shop_id'=> $shop->id,
        ]);
         
       if(!isset($request['no_pack'])){

        for ($i=0; $i < count($product_id); $i++) { 

         $prod_cost_item = new ProductionCostItem();
         $prod_cost_item->product_id = $request->product_id[$i];
         $prod_cost_item->packing_material_id = $request->package_id[$i];
         $prod_cost_item->unit_packed = $request->unit_p[$i];
         $prod_cost_item->quantity = $request->qty_p[$i];
         $prod_cost_item->cost_per_unit = floatval($request->unit_cost_p[$i]) ;
         $prod_cost_item->profit_margin = $request->prof_margin_p[$i];
         $prod_cost_item->selling_price = $request->price_p[$i];
         $prod_cost_item->production_cost_id = $prod_cost->id;
         $prod_cost_item->save();
      }

       }elseif($request['no_pack'] == 1){
         $prod_cost_item = new ProductionCostItem();
         $prod_cost_item->product_id = $request->product;
         $prod_cost_item->unit_packed = 1;
         $prod_cost_item->quantity = $request->product_v;
         $prod_cost_item->cost_per_unit = floatval($request->prod_cost) ;
         $prod_cost_item->profit_margin = $request->profit_margin;
         $prod_cost_item->selling_price = $request->prod_price;
         $prod_cost_item->production_cost_id = $prod_cost->id;
         $prod_cost_item->save();

       }
      
        
      return redirect()->route('prod-costs.index'); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $page = 'Production';
        $title = 'Production Details';
        $title_sw = 'Harifa za Uzalishaji';
        $prod_cost = ProductionCost::find(decrypt($id));

        $prod_cost_items = ProductionCostItem::where('production_cost_id' , $prod_cost->id)->join('products' , 'products.id' , '=' , 'production_cost_items.product_id')->get();

        $mrouse = $shop->mroUse()->where('prod_batch' , $prod_cost->prod_batch)->where('is_deleted' , false)->get();
        $rmuse = RmUse::where('rm_uses.shop_id', $shop->id)->join('users', 'users.id', '=', 'rm_uses.user_id')->select('rm_uses.id as id', 'rm_uses.total_cost as total_cost', 'rm_uses.date as date', 'users.first_name as first_name', 'users.last_name as last_name', 'rm_uses.created_at as created_at')->where('prod_batch' , $prod_cost->prod_batch)->get();
        $pmuse = PmUse::where('pm_uses.shop_id', $shop->id)->where('is_deleted' , false)->join('users', 'users.id', '=', 'pm_uses.user_id')->select('pm_uses.id as id', 'pm_uses.total_cost as total_cost', 'pm_uses.date as date', 'users.first_name as first_name', 'users.last_name as last_name', 'pm_uses.created_at as created_at')->where('prod_batch' , $prod_cost->prod_batch)->get();


        $mros = [];
        $rms = [];
        $pms = []; 
         
        foreach($mrouse as $key => $v){
            $mro_used_items = $shop->mroItems()->where('mro_items.is_deleted' , false)->join('mros' , 'mros.id' , '=' , 'mro_items.mro_id')->where('mro_use_id' , $v->id)->get();
             array_push($mros , $mro_used_items); 
            }

        foreach($rmuse as $key => $v){
            $rm_use_items = RmUseItem::where('rm_use_items.shop_id', $shop->id)->where('rm_use_id' , $v->id)->join('raw_materials', 'raw_materials.id', '=', 'rm_use_items.raw_material_id')->select('rm_use_items.id as id', 'rm_use_items.quantity as quantity', 'rm_use_items.unit_cost as unit_cost', 'rm_use_items.total as total', 'rm_use_items.date as date', 'raw_materials.name as name', 'raw_materials.basic_unit as basic_unit')->get();
            array_push($rms , $rm_use_items); 
            }

        foreach($pmuse as $key => $v){

            $pm_use_items  = PmUseItem::where('pm_use_items.shop_id', $shop->id)->where('pm_use_id' , $v->id)->join('packing_materials', 'packing_materials.id', '=', 'pm_use_items.packing_material_id')->join('products' , 'pm_use_items.product_packed' ,'=' , 'products.id')->select('pm_use_items.id as id', 'pm_use_items.quantity as quantity', 'pm_use_items.unit_cost as unit_cost', 'pm_use_items.total as total', 'pm_use_items.date as date', 'packing_materials.name as name', 'products.basic_unit as basic_unit' , 'products.name as product_name' , 'packing_materials.basic_unit as package_unit' , 'pm_use_items.unit_packed as unit_packed' , 'pm_use_items.packing_material_id as packing_material_id' , 'products.id as product_id')->get();

            array_push($pms , $pm_use_items);

        }

        return view('production.show' , compact(['prod_cost' , 'prod_cost_items' ,'page' , 'title' , 'title_sw' ,'mros' , 'pms' , 'rms' , 'mrouse' , 'pmuse' , 'rmuse' ]));
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
        //
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
        $prod_record = ProductionCost::find(decrypt($id));

        
        if(!$prod_record->is_transferred){
            $prod_items = ProductionCostItem::where('production_cost_id' , $prod_record)->get();
            foreach($prod_items as $prod_item){
                $prod_item->delete();
            }

            $prod_record->delete();

            return redirect()->back()->with('success' , "Production Records Deleted Successful");
        }else{
              return redirect()->back()->with('error' , " Fail to Delete ,Production Records Is Already Transferred ");
        }
    }

    public function savePanel (Request $request){
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $now = Carbon::now();

        $pm = PmUseItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->get();
        $rm = RmUseItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->get();
        $mro = MroUsedItemTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->get();
        $product_made = ProductMadeApiTemp::where('shop_id' , $shop->id)->where('user_id' , $user->id)->get();

        if (!is_null($pm)) {
           
            $pmuse = PmUse::create([
                'shop_id' => $shop->id,
                'user_id' => $user->id,
                'total_cost' => $pm->sum('total'),
                'date' => $now,
                'prod_batch' => $request['prod_batch']
            ]);

            foreach ($pm as $key => $item) {
                $packing_material = PackingMaterial::find($item->packing_material_id);
                $useditem  = new PmUseItem;
                $useditem->pm_use_id = $pmuse->id;
                $useditem->user_id = $user->id;
                $useditem->packing_material_id = $packing_material->id;
                $useditem->shop_id = $shop->id;
                $useditem->quantity = $item->quantity;
                $useditem->unit_cost = $item->unit_cost;
                $useditem->total = $item->total;
                $useditem->unit_packed = $item->unit_packed;
                $useditem->date = $now;
                $useditem->product_packed = $item->product_packed;
                $useditem->save();

                $item->delete();

                $shop_packing_material = $shop->packingMaterials()->where('packing_material_id', $packing_material->id)->where('is_deleted' , false)->first();

                $purchased = PmItem::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->sum('qty');
                $used = PmUseItem::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->sum('quantity');
                $damaged = PmDamage::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->sum('quantity');
                                    
                $instore = $purchased-($used+$damaged); 
                                 
                $shop_packing_material->pivot->in_store = $instore;
                $lastpur = PmItem::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->latest()->first();
                if ($shop_packing_material->pivot->in_store <= $lastpur->qty) {
                    $shop_packing_material->pivot->unit_cost = $lastpur->unit_cost;
                }
                $shop_packing_material->pivot->save();

            }

        }
       

         if (!is_null($rm)) {

            $rmuse = RmUse::create([
                'shop_id' => $shop->id,
                'user_id' => $user->id,
                'total_cost' => $rm->sum('total'),
                'date' => $now,
                'prod_batch' => $request['prod_batch'],
            ]);

            foreach ($rm as $key => $item){ 
                $raw_material = RawMaterial::find($item->raw_material_id);
                $useditem  = new RmUseItem;
                $useditem->rm_use_id = $rmuse->id;
                $useditem->raw_material_id = $raw_material->id;
                $useditem->shop_id = $shop->id;
                $useditem->quantity = $item->quantity;
                $useditem->unit_cost = $item->unit_cost;
                $useditem->total = $item->total;
                $useditem->date = $now;
                $useditem->save();

                $item->delete();

                $shop_raw_material = $shop->rawMaterials()->where('raw_material_id', $raw_material->id)->where('is_deleted' , false)->first();

                $purchased = RmItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->sum('qty');
                $used = RmUseItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');
                $damaged = RmDamage::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->sum('quantity');
                                    
                $instore = $purchased-($damaged +$used); 
                                 
                $shop_raw_material->pivot->in_store = $instore;
                $lastpur = RmItem::where('raw_material_id', $raw_material->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->latest()->first();
                if ($shop_raw_material->pivot->in_store <= $lastpur->qty) {
                    $shop_raw_material->pivot->unit_cost = $lastpur->unit_cost;
                }
                $shop_raw_material->pivot->save();



            }

        }

        if (!is_null($mro)) {

            $mrouse = MroUse::create([
                'shop_id' => $shop->id,
                'user_id' => $user->id,
                'total_cost' => $mro->sum('total'),
                'date' => $now,
                'prod_batch' => $request['prod_batch'],
            ]);

            foreach ($mro as $key => $item) {
            
                $mro = MroItem::create([
                    'mro_use_id' => $mrouse->id,
                    'mro_id' => $item->mro_id,
                    'shop_id' => $shop->id,
                    'qty' => $item->quantity,
                    'unit_cost' => $item->unit_cost,
                    'total' => $item->total,
                    'date' => $now,
                ]);

                $item->delete();
            }
        }

        $total_cost = $pm->sum('total')+$rm->sum('total')+$mro->sum('total');

        if(!is_null($product_made)){

            $prod_cost = ProductionCost::create([
                'total_prod_qty' =>  $product_made->sum('qty'),
                'total_cost' => $total_cost, 
                'prod_batch' => $request['prod_batch'],
                'date' => $now,
                'user_id'=> $user->id,
                'shop_id'=> $shop->id,
            ]);

            foreach ($product_made as $key => $value) {
                
                $prod_cost_item = new ProductionCostItem();
                $prod_cost_item->product_id = $value->product_id;
                $prod_cost_item->packing_material_id = $value->packing_material_id;
                $prod_cost_item->unit_packed = 1;
                $prod_cost_item->quantity = $value->qty;
                $prod_cost_item->cost_per_unit = $value->cost_per_unit;
                $prod_cost_item->profit_margin = $value->profit_margin;
                $prod_cost_item->selling_price = $value->selling_price;
                $prod_cost_item->production_cost_id = $prod_cost->id;
                $prod_cost_item->save();

                $value->delete();
            }

        } 


        return redirect()->back();

    }
}
