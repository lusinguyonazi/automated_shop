<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Response;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\PurchaseOrderTemp;
use App\Models\User;
use App\Models\Product;

class PurchaseOrderTempApiController extends Controller
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
        return Response::json(PurchaseOrderTemp::where('shop_id', Session::get('shop_id'))->where('user_id', Auth::user()->id)->with('product')->get());
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
        $sameitems = PurchaseOrderTemp::where('product_id', $request['product_id'])->where('user_id', $user->id)->where('shop_id', $shop->id)->count();
        
        if ($sameitems == 0) {
            $product = $shop->products()->where('product_id', $request['product_id'])->first();
            if (!is_null($product)) {
                
                $itemTemp = new PurchaseOrderTemp;
                $itemTemp->shop_id = $shop->id;
                $itemTemp->user_id = $user->id;
                $itemTemp->product_id = $request['product_id'];
                $itemTemp->qty  = 0;
                $itemTemp->unit_cost = $product->pivot->buying_per_unit;
                $itemTemp->save();

                return $itemTemp;
            }
        }else{
            $warning = 'Ooops!. The product already in selected items.';
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
        $itemTemp =  PurchaseOrderTemp::where('id', $id)->where('user_id', Auth::user()->id)->where('shop_id', $shop->id)->first();
        if (!is_null($itemTemp)) {

            if ($itemTemp->unit_cost != $request['unit_cost']) { 
                $itemTemp->unit_cost = $request['unit_cost'];
                $itemTemp->save();

                return $itemTemp;
            }elseif ($itemTemp->qty != $request['qty']) {

                $product = Product::find($itemTemp->product_id);

                if ($product->basic_unit == 'pcs' || $product->basic_unit == 'prs' || $product->basic_unit == 'box' || $product->basic_unit == 'btl' || $product->basic_unit == 'pks' || $product->basic_unit == 'gls') {
                    if (is_int($request['qty'])) {
                        $itemTemp->qty  = $request['qty'];
                        $itemTemp->save();

                        return $itemTemp;
                    }else{

                        return response()->json(['status' => 'WRONG', 'msg' => 'This product '.$product->name.' can not accept decimal quantity. Please change its basic unit if you want to set decimal for stock quantity values']);
                    }
                }else{
                    $itemTemp->qty  = $request['qty'];
                    $itemTemp->save();

                    return $itemTemp;
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
        PurchaseOrderTemp::destroy($id);
    }

    public function ajaxPost(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = User::find(Session::get('user_id'));
        
        $product = $shop->products()->where('barcode', $request['barcode'])->first();
        if (!is_null($product)) {
            $itemTemp = PurchaseOrderTemp::where('product_id', $product->pivot->product_id)->where('user_id', $user->id)->where('shop_id', $shop->id)->first();
            if (!is_null($itemTemp)) {
                
                $itemTemp->quantity_in  = $itemTemp->quantity_in+1;
                $itemTemp->save();
            }else{
                $itemTemp = new PurchaseOrderTemp;
                $itemTemp->shop_id = $shop->id;
                $itemTemp->user_id = $user->id;
                $itemTemp->product_id = $product->pivot->product_id;
                $itemTemp->quantity_in  = 1;
                $itemTemp->unit_cost = $product->pivot->buying_per_unit;
                $itemTemp->save();
            }
            return response()->json(['status' => 'OK']);
        }else{
            $warning = "Sorry, Scanned barcode value does not match any of your products . Please Try Again";
            return response()->json(['status' => 'Fail', 'msg' => $warning]);
        }
    }
}
