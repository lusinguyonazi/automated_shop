<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Crypt;
use Auth;
use Session;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\User;
use App\Models\Setting;
use App\Models\PmPurchase;
use App\Models\PmSupplierTransaction;
use App\Models\PmItem;
use App\Models\PmUseItem;
use App\Models\PmUse;
use App\Models\PmPurchaseItemTemp;
use App\Models\PmPurchasePayment;
use App\Models\PackingMaterial;
use App\Models\PaymentVoucher;
use App\Models\Supplier;
use App\Models\PmDamage;
use App\Models\Payment;
use App\Models\Module;
use App\Models\ShopCurrency;
use App\Models\PmPurchaseTemp;
use App\Models\BankDetail;

class PmPurchaseController extends Controller
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
        $page = 'Packing Materials';
        $title = 'Packing Materials Purchases';
        $title_sw = 'Manunuzi ya Malighafi';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
        $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
        $bdetails = BankDetail::where('shop_id', $shop->id)->get();
        $pmpurchases = PmPurchase::where('shop_id', $shop->id)->where('is_deleted' , false)->orderBy('date', 'desc')->get();
        $suppliers = Supplier::where('shop_id', $shop->id)->where('supplier_for', 'Packing Materials')->get();

        return view('production.packing-materials.purchases.index', compact('page', 'title', 'title_sw', 'pmpurchases', 'suppliers', 'shop' , 'settings' , 'currencies' , 'defcurr' , 'dfcurr', 'bdetails'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'Packing Materials';
        $title = 'New Packing Material Purchase';
        $title_sw = 'Manunuzi Mapya ya Malighafi';

        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $module = Module::where('name', 'Production')->first();
        $settings = Setting::where('shop_id', $shop->id)->first();
        // $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', true)->where('module', $module->id)->first();


        // if (!is_null($payment)) {

            $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
            $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            $bdetails = BankDetail::where('shop_id', $shop->id)->get();

            if (is_null($dfcurr)) {
                return redirect('settings')->with('error', 'Please add your Default Currency to continue...');
            }

            $pmtemp = PmPurchaseTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->first();
                
            if (is_null($pmtemp)) {
                $pmtemp = new PmPurchaseTemp();
                $pmtemp->shop_id = $shop->id;
                $pmtemp->user_id = $user->id;
                $pmtemp->currency = $dfcurr->code;
                $pmtemp->defcurr = $dfcurr->code;
                $pmtemp->save();
            }

            $suppliers = Supplier::where('shop_id', $shop->id)->where('supplier_for', 'Packing Materials')->get();
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
            
            return view('production.packing-materials.purchases.create', compact('page', 'title', 'title_sw', 'units', 'suppliers', 'shop' , 'currencies' , 'dfcurr' , 'settings' , 'pmtemp'));
        // }else{
        //      $info = 'Dear customer you have not subscribed to this module please make payment and activate now.';
        //     // Alert::info("Payment Expired", $info);
        //     return redirect('verify-module-payment/'.encrypt($module->id))->with('error', $info);
        // }
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
        $now = null;
        $pmtemp = PmPurchaseTemp::find($request['pm_purchase_temp_id']);
        if (is_null($request['rmitem_date'])) {
            $now = Carbon::now();
        }else{
            $now = $request['rmitem_date'];
        }

        $supplier_id = null;
        if (!is_null($pmtemp->supplier_id)) {
            $supplier_id = $pmtemp->supplier_id;
        }

        $pitems = PmPurchaseItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();

        if (!is_null($pitems)) {
            $temps = array();
            foreach ($pitems as $key => $value) {
                if ($value->qty == 0) {
                    array_push($temps, $value->qty);
                }
            }

            if (!empty($temps)) {
                return redirect()->back()->with('warning', 'Please update the quantity and Unit cost of each item to continue');
            }else{

                $max_no = PmPurchase::where('shop_id', $shop->id)->orderBy('grn_no', 'desc')->first();
                $grnno = 0;

                if (!is_null($max_no)) {
                    $grnno = $max_no->grn_no+1;
                }else{
                    $grnno = 1;
                }

                $total_amount = 0;
                $amount_paid = 0;
                $purchase = PmPurchase::create([
                    'shop_id' => $shop->id,
                    'user_id' => $user->id,
                    'supplier_id' => $supplier_id,
                    'grn_no' => $grnno, 
                    'currency' => $pmtemp->currency,
                    'defcurr' => $pmtemp->defcurr,
                    'ex_rate' => $pmtemp->ex_rate,
                    'order_no' => $request['order_no'],
                    'delivery_note_no' => $request['delivery_note_no'],
                    'invoice_no' => $request['invoice_no'],
                    'total_amount' => $total_amount,
                    'amount_paid' => $amount_paid,
                    'comments' => $request['comments'],
                    'date' => $now,
                    'purchase_type' => $pmtemp->purchase_type,
                    'status' =>  $pmtemp->purchase_type == 'credit' ? 'Pending' : 'Paid'
                ]);

                foreach ($pitems as $key => $item) {
                    $packing_material = PackingMaterial::find($item->packing_material_id);
                    $stock  = new PmItem;
                    $stock->pm_purchase_id = $purchase->id;
                    $stock->packing_material_id = $packing_material->id;
                    $stock->shop_id = $shop->id;
                    $stock->qty = $item->qty;
                    $stock->unit_cost = $item->unit_cost;
                    $stock->total = $item->total;
                    $stock->date = $now;
                    $stock->save();

                    $shop_packing_material = $shop->packingMaterials()->where('packing_material_id', $packing_material->id)->where('is_deleted' , false)->first();

                    if ($shop_packing_material->pivot->in_store == 0) {
                        $shop_packing_material->pivot->unit_cost = $item->unit_cost;
                        $shop_packing_material->pivot->save();
                    }


                    $purchased = PmItem::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->sum('qty');           
                     $used = PmUse::where('packing_material_id', $packing_material->id)->where('pm_uses.shop_id', $shop->id)->where('is_deleted' , false)->join('pm_use_items' , 'pm_use_items.pm_use_id' , '=' , 'pm_uses.id')->sum('quantity');
                    $damaged = PmDamage::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->sum('quantity');

                    $shop_packing_material = $shop->packingMaterials()->where('packing_material_id', $packing_material->id)->where('is_deleted' , false)->first();
                    $instore = $purchased-($used+$damaged); 
                    $shop_packing_material->pivot->in_store = $instore;
                    $shop_packing_material->pivot->save();
                    
                    $total_amount += $item->total;
                }

                if (is_null($request['amount_paid']) && $pmtemp->purchase_type == 'cash') {
                    $amount_paid = $total_amount;
                    $purchase->status = "Paid";
                }elseif (is_null($request['amount_paid']) && $pmtemp->purchase_type == 'credit') {
                    $amount_paid = 0;
                    $purchase->status = "Pending";
                }else{
                    $amount_paid = $request['amount_paid'];
                }

                $purchase->total_amount = $total_amount;
                $purchase->amount_paid = $amount_paid;
                $purchase->save();


               $pvno = null;
                if ($amount_paid > 0) {
                    
                    $pvno = 0;
                    $max_pv_no = PaymentVoucher::where('shop_id', $shop->id)->orderBy('pv_no', 'desc')->first();
                    if (!is_null($max_pv_no)) {
                        $pvno = $max_pv_no->pv_no+1;
                    }else{
                        $pvno = 1;
                    }

                    $pv = new PaymentVoucher();
                    $pv->shop_id = $shop->id;
                    $pv->user_id = $user->id;
                    $pv->pv_no =$pvno;
                    $pv->amount = $amount_paid;
                    $pv->account = $request['account'];
                    $pv->voucher_for = 'PM Purchase';
                    $pv->save();

                    $payment = PmPurchasePayment::create([
                        'shop_id' => $shop->id,
                        'pm_purchase_id' => $purchase->id,
                        'account' => $request['account'],
                        'pay_date' => $now,
                        'amount' => $amount_paid,
                        'pv_no' => $pvno
                    ]);
                }
                if (!is_null($supplier_id)) {
                    
                    $acctrans = new PmSupplierTransaction();
                    $acctrans->shop_id = $shop->id;
                    $acctrans->user_id = $user->id;
                    $acctrans->supplier_id = $supplier_id;
                    $acctrans->invoice_no = $request['invoice_no'];
                    $acctrans->pm_purchase_id = $purchase->id; 
                    $acctrans->amount = $total_amount;
                    $acctrans->pv_no = $pvno;
                    if ($amount_paid > 0) {
                        $acctrans->payment = $amount_paid;
                        $acctrans->payment_mode = $request['account'];  
                    }
                    $acctrans->date = $now;
                    $acctrans->save();
                }

                $puritems = PmPurchaseItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
                foreach ($puritems as $key => $value) {
                    $value->delete();
                }

                $pmtemp->delete();

                return redirect()->back()->with('success', 'Packing Materials PmItems were added successfully');
            }
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
        $page = 'Packing Materials';
        $title = 'Purchase details';
        $title_sw = 'Maelezo ya Manunuzi';

        $shop = Shop::find(Session::get('shop_id'));
        $purchase = PmPurchase::where('id', decrypt($id))->first();
        $supplier = null;
        if (!is_null($purchase->supplier_id)) {
            $supplier = Supplier::find($purchase->supplier_id);
        }

        $pitems = PmItem::where('pm_purchase_id', $purchase->id)->where('pm_items.shop_id', $shop->id)->where('is_deleted' , false)->join('packing_materials', 'packing_materials.id', '=', 'pm_items.packing_material_id')->select('pm_items.id as id', 'pm_items.qty as qty', 'pm_items.unit_cost as unit_cost', 'pm_items.date as date', 'pm_items.created_at as created_at', 'packing_materials.name as name', 'packing_materials.basic_unit as basic_unit')->orderBy('date', 'desc')->get();
        
        return view('production.packing-materials.purchases.show', compact('page', 'title', 'title_sw', 'purchase', 'pitems', 'supplier', 'shop'));
    }

      /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function purchaseItems($id)
    {
        $page = 'Packing Materials';
        $title = 'Purchase details';
        $title_sw = 'Maelezo ya Manunuzi';

        $shop = Shop::find(Session::get('shop_id'));
        $purchase = PmPurchase::where('id', decrypt($id))->where('is_deleted' , false)->first();
        $supplier = null;
        if (!is_null($purchase->supplier_id)) {
            $supplier = Supplier::find($purchase->supplier_id);
        }

        $pitems = PmItem::where('pm_purchase_id', $purchase->id)->where('pm_items.shop_id', $shop->id)->where('is_deleted' , false)->join('packing_materials', 'packing_materials.id', '=', 'pm_items.packing_material_id')->select('pm_items.id as id', 'pm_items.qty as qty', 'pm_items.unit_cost as unit_cost', 'pm_items.date as date', 'pm_items.created_at as created_at', 'packing_materials.name as name', 'packing_materials.basic_unit as basic_unit')->orderBy('date', 'desc')->get();
        
        $payments = PmPurchasePayment::where('pm_purchase_id', $purchase->id)->get();
        
        return view('production.packing-materials.purchases.items', compact('page', 'title', 'title_sw', 'purchase', 'pitems', 'supplier', 'payments'));
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
        $title = 'Edit Purchase';
        $title_sw = 'Hariri Manunuzi';
        $shop = Shop::find(Session::get('shop_id'));
        $purchase = PmPurchase::find(decrypt($id));
        $suppliers = $shop->suppliers()->get();
        return view('production.packing-materials.purchases.edit', compact('page', 'title', 'title_sw', 'purchase', 'suppliers'));
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
        $purchase = PmPurchase::find(decrypt($id));

        $purchase->supplier_id = $request['supplier_id'];
        $purchase->order_no = $request['order_no'];
        $purchase->delivery_note_no = $request['delivery_note_no'];
        $purchase->invoice_no = $request['invoice_no'];
        $purchase->comments = $request['comments'];
        $purchase->date = $request['date'];
        $purchase->save();
 
        return redirect('pm-purchases')->with('success', 'Purchase was updated successfully');
    }

    public function updateTemp(Request $request , $id){
        $pmtemp = PmPurchasetemp::find($id);

        $local_ex_rate = 1;
        $foreign_ex_rate = 1;
        $ex_rate = 1;
        if ($request['currency'] != $pmtemp->defcurr) {
            if ($request['ex_rate_mode'] == 'Foreign') {
                $local_ex_rate = $request['local_ex_rate'];
                $ex_rate = 1/$local_ex_rate;
            }else{
                $foreign_ex_rate = $request['foreign_ex_rate'];
                $ex_rate = $foreign_ex_rate;
            }
        }

        $pmtemp->supplier_id = $request['supplier_id'];
        $pmtemp->date_set = $request['date_set'];
        $pmtemp->date = $request['date'];
        $pmtemp->purchase_type = $request['purchase_type'];
        $pmtemp->pay_type = $request['pay_type'];
        $pmtemp->currency = $request['currency'];
        $pmtemp->ex_rate_mode = $request['ex_rate_mode'];
        $pmtemp->local_ex_rate = $local_ex_rate;
        $pmtemp->foreign_ex_rate = $foreign_ex_rate;
        $pmtemp->ex_rate = $ex_rate;
        $pmtemp->due_date = $request['due_date'];
        $pmtemp->comments = $request['comments'];
        $pmtemp->grn_no = $request['grn_no'];
        $pmtemp->order_no  = $request['order_no'];
        $pmtemp->invoice_no  = $request['invoice_no'];
        $pmtemp->delivery_note_no  = $request['delivery_note_no'];
        $pmtemp->save();

        return $pmtemp;
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
        $user = Auth::user();
        $purchase = PmPurchase::where('id', decrypt($id))->where('shop_id', $shop->id)->where('is_deleted' , false)->first();

        $pitems = PmItem::where('pm_purchase_id', $purchase->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->get();

        foreach ($pitems as $key => $value) {
            // $value->is_deleted = true;
            // $value->save();
            $packing_material = PackingMaterial::find($value->packing_material_id);
            $shop_packing_material = $shop->packingMaterials()->where('packing_material_id', $packing_material->id)->where('is_deleted' , false)->first();

            $stock_in = PmItem::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->where('is_deleted' , false)->sum('qty');

            $used = PmUse::where('packing_material_id', $packing_material->id)->where('pm_uses.shop_id', $shop->id)->where('is_deleted' , false)->join('pm_use_items' , 'pm_use_items.pm_use_id' , '=' , 'pm_uses.id')->sum('quantity');

            $damaged = PmDamage::where('packing_material_id', $packing_material->id)->where('shop_id', $shop->id)->sum('quantity');

            $instock = $stock_in - ($used + $damaged);
            if(!is_null($shop_packing_material)){
              $shop_packing_material->pivot->in_store = $instock;
              $shop_packing_material->pivot->save();
            }   
         

            $message = 'Stock was successfully deleted';
  
        }

        $payments = PmPurchasePayment::where('pm_purchase_id', $purchase->id)->get();

        foreach ($payments as $key => $payment) {
            $pv = PaymentVoucher::where('pv_no', $payment->pv_no)->first();
            if (!is_null($pv)) {
                $acctrans = PmSupplierTransaction::where('pv_no', $pv->pv_no)->where('shop_id', $shop->id)->where('is_deleted' , false)->first();
                if (!is_null($acctrans)) {
                    $acctrans->delete();
                }
                $pv->delete();
            }
            $payment->delete();
        }

        $acctrans = PmSupplierTransaction::where('invoice_no', $purchase->invoice_no)->where('shop_id', $shop->id)->where('is_deleted' , false)->first();
        if (!is_null($acctrans)) {
            $acctrans->delete();
        }
        
        $purchase->delete();

        return redirect()->back()->with('success', 'Purchase was deleted successfully');
    }

    public function cancel(){
        $shop = Shop::find(Session::get('shop_id'));
        $rmtemp =PmPurchaseTemp::where('shop_id' , $shop->id)->where('user_id' , Auth::id())->first();
        $rmtemp->delete();

        return redirect()->back();
    }
}
