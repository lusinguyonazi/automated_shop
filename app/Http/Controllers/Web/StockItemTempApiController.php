<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Response;
use Session;
use Auth;
use Log;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\Supplier;
use App\Models\PurchaseTemp;
use App\Models\PurchaseItemTemp;
use App\Models\User;
use App\Models\Product;

class StockItemTempApiController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function updatePurchaseTemp(Request $request)
    {
        $purchasetemp = PurchaseTemp::find($request['id']);
        $local_ex_rate = 1;
        $foreign_ex_rate = 1;
        $ex_rate = 1;
        if ($request['currency'] != $purchasetemp->defcurr) {
            if ($request['ex_rate_mode'] == 'Foreign') {
                $local_ex_rate = $request['local_ex_rate'];
                $ex_rate = 1/$local_ex_rate;
            }else{
                $foreign_ex_rate = $request['foreign_ex_rate'];
                $ex_rate = $foreign_ex_rate;
            }
        }

        $purchasetemp->supplier_id = $request['supplier_id'];
        $purchasetemp->date_set = $request['date_set'];
        $purchasetemp->purchase_date = $request['purchase_date'];
        $purchasetemp->purchase_type = $request['purchase_type'];
        $purchasetemp->pay_type = $request['pay_type'];
        $purchasetemp->currency = $request['currency'];
        $purchasetemp->ex_rate_mode = $request['ex_rate_mode'];
        $purchasetemp->local_ex_rate = $local_ex_rate;
        $purchasetemp->foreign_ex_rate = $foreign_ex_rate;
        $purchasetemp->ex_rate = $ex_rate;
        $purchasetemp->comments = $request['comments'];
        $purchasetemp->save();

        return $purchasetemp;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {   
        $shop = Shop::find(Session::get('shop_id'));
        $suppliers = Supplier::where('shop_id', $shop->id)->get();
        $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
        $purchasetemp = PurchaseTemp::find($id);
        $itemtemps = PurchaseItemTemp::where('purchase_temp_id', $purchasetemp->id)->with('product')->get();
        $temps = array();
        foreach ($itemtemps as $key => $temp) {
            array_push($temps, [
                'id' => $temp->id,
                'purchase_temp_id' => $temp->purchase_temp_id,
                'product_id' => $temp->product_id,
                'name' => $temp->product->name,
                'quantity_in' => $temp->quantity_in,
                'buying_per_unit' => round($temp->buying_per_unit*$purchasetemp->ex_rate, 2),
                'total' => round($temp->total*$purchasetemp->ex_rate, 2),
                'price_per_unit' => round($temp->price_per_unit*$purchasetemp->ex_rate, 2),
                'expire_date' => $temp->expire_date,
                'created_at' => $temp->created_at,
                'updated_at' => $temp->updated_at
            ]);
        }
        return Response::json(['purchasetemp' => $purchasetemp, 'suppliers' => $suppliers, 'currencies' => $currencies, 'items' =>$temps]);
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
        $purchasetemp = PurchaseTemp::find($request['purchase_temp_id']);
        $sameitems = PurchaseItemTemp::where('product_id', $request['product_id'])->where('purchase_temp_id', $purchasetemp->id)->count();
        
        if ($sameitems == 0) {
            $product = $shop->products()->where('product_id', $request['product_id'])->first();
            if (!is_null($product)) {
                $stockItemTemp = new PurchaseItemTemp;
                $stockItemTemp->purchase_temp_id = $purchasetemp->id;
                $stockItemTemp->product_id = $request['product_id'];
                $stockItemTemp->quantity_in  = 0;
                $stockItemTemp->buying_per_unit = $product->pivot->buying_per_unit;
                if (!is_null($product->pivot->price_per_unit)) {
                    $stockItemTemp->price_per_unit = $product->pivot->price_per_unit;
                }else{
                    $stockItemTemp->price_per_unit = 0;
                }
                $stockItemTemp->total = 0;
                // $stockItemTemp->retail_price = $product->pivot->price_with_vat;
                $stockItemTemp->save();

                return $stockItemTemp;
            }
        }else{
            $warning = 'Ooops!. The product already in selected items.';
            return response()->json(['status' =>'DUPL', 'msg' => $warning]);
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
        $stockItemTemp =  PurchaseItemTemp::find($id);
        if (!is_null($stockItemTemp)) {
            $purchasetemp = PurchaseTemp::find($stockItemTemp->purchase_temp_id);
            if ($stockItemTemp->quantity_in != $request['quantity_in']) {
                $product = Product::find($stockItemTemp->product_id);
                if ($product->basic_unit == 'pcs' || $product->basic_unit == 'prs' || $product->basic_unit == 'box' || $product->basic_unit == 'btl' || $product->basic_unit == 'pks' || $product->basic_unit == 'gls') {
                    if (!$this->is_decimal($request['quantity_in'])) {
                        $stockItemTemp->quantity_in  = $request['quantity_in'];
                        $stockItemTemp->total = $stockItemTemp->quantity_in*$stockItemTemp->buying_per_unit;
                        $stockItemTemp->save();

                        return $stockItemTemp;
                    }else{

                        return response()->json(['status' => 'WRONG', 'msg' => 'This product '.$product->name.' can not accept decimal quantity. Please change its basic unit if you want to set decimal for stock quantity values']);
                    }
                }else{
                    $stockItemTemp->quantity_in  = $request['quantity_in'];
                    $stockItemTemp->total = $stockItemTemp->quantity_in*$stockItemTemp->buying_per_unit;
                    $stockItemTemp->save();

                    return $stockItemTemp;
                }
            }else{
                if($stockItemTemp->buying_per_unit != round($request['buying_per_unit']/$purchasetemp->ex_rate,2)) {
                    if ($purchasetemp->currency != $purchasetemp->defcurr) {
                        $stockItemTemp->buying_per_unit = $request['buying_per_unit']/$purchasetemp->ex_rate;     
                    }else{
                        $stockItemTemp->buying_per_unit = $request['buying_per_unit'];
                    }
                    $stockItemTemp->total = $stockItemTemp->quantity_in*$stockItemTemp->buying_per_unit;
                    $stockItemTemp->save();

                    return $stockItemTemp;
                }else{
                    if ($stockItemTemp->total != $request['total']) {
                        if ($purchasetemp->currency != $purchasetemp) {
                            $stockItemTemp->total = $request['total']/$purchasetemp->ex_rate;
                        }else{
                            $stockItemTemp->total = $request['total'];
                        }
                        if ($stockItemTemp->quantity_in > 0) {
                            $stockItemTemp->buying_per_unit = $stockItemTemp->total/$stockItemTemp->quantity_in;
                        }
                        $stockItemTemp->save();

                        return $stockItemTemp;   
                    }else{
                        if($stockItemTemp->price_per_unit != round($request['price_per_unit']/$purchasetemp->ex_rate,2)){
                            if ($purchasetemp->currency != $purchasetemp->defcurr) {
                                $stockItemTemp->price_per_unit = $request['price_per_unit']/$purchasetemp->ex_rate;
                            }else{
                                $stockItemTemp->price_per_unit = $request['price_per_unit'];
                            }
                            $stockItemTemp->save();
                            return $stockItemTemp;
                        }else{
                            if ($stockItemTemp->expire_date != $request['expire_date']) {
                                try {
                                    $expdate = Carbon::parse($request['expire_date']);
                                    $now = Carbon::now();
                                    $numd = $expdate->gt($now);
                                    if ($numd) {
                                        $stockItemTemp->expire_date = $request['expire_date'];
                                        $stockItemTemp->save();

                                        return $stockItemTemp;
                                    }else{
                                        return response()->json(['status' => 'FAIL']);
                                    }
                                } catch (\Exception $e) {

                                }
                            }
                        }    
                    }
                }
            }
        }
    }

    function is_decimal($val)
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        PurchaseItemTemp::destroy($id);
    }

    public function cancelPurchase($id)
    {
        $purchasetemp = PurchaseTemp::find(decrypt($id));
        if (!is_null($purchasetemp)) {
            $items = PurchaseItemTemp::where('purchase_temp_id', $purchasetemp->id)->get();
            foreach ($items as $key => $item) {
                $item->delete();
            }
            $purchasetemp->delete();
        }

        return redirect()->route('purchases.create')->with('success', 'Purchase cancelled successfully');
    }

    public function ajaxPost(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        
        $product = $shop->products()->where('barcode', $request['barcode'])->first();
        if (!is_null($product)) {
            $stockItemTemp = PurchaseItemTemp::where('product_id', $product->pivot->product_id)->where('user_id', $user->id)->where('shop_id', $shop->id)->first();
            if (!is_null($stockItemTemp)) {
                
                $stockItemTemp->quantity_in  = $stockItemTemp->quantity_in+1;
                $stockItemTemp->total = $stockItemTemp->quantity_in*$stockItemTemp->buying_per_unit;
                $stockItemTemp->save();
            }else{
                $stockItemTemp = new PurchaseItemTemp;
                $stockItemTemp->shop_id = $shop->id;
                $stockItemTemp->user_id = $user->id;
                $stockItemTemp->product_id = $product->pivot->product_id;
                $stockItemTemp->quantity_in  = 1;
                $stockItemTemp->buying_per_unit = $product->pivot->buying_per_unit;
                $stockItemTemp->total = $stockItemTemp->quantity_in*$stockItemTemp->buying_per_unit;
                $stockItemTemp->save();
            }
            return response()->json(['status' => 'OK']);
        }else{
            $warning = "Sorry, Scanned barcode value does not match any of your products . Please Try Again";
            return response()->json(['status' => 'Fail', 'msg' => $warning]);
        }
    }
}

