<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use \DB;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\User;
use App\Models\Settings;
use App\Models\Invoice;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\CreditNoteItem;
use App\Models\Stock;
use App\Models\ProdDamage;
use App\Models\TransferOrderItem;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\SalePayment;

class SaleReturnItemController extends Controller
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
        $shop = Shop::find(Session::get('shop_id'));
        $salereturn = SaleReturn::find($request['sale_return_id']);
        $sale = AnSale::find($salereturn->an_sale_id);
        $saleitem = AnSaleItem::where('an_sale_id', $sale->id)->where('product_id', $request['product_id'])->first();
        $sritem = SaleReturnItem::where('sale_return_id', $salereturn->id)->where('product_id', $saleitem->product_id)->first();
        if (is_null($sritem)) {
            $sritem = SaleReturnItem::create([
                'sale_return_id' => $salereturn->id,
                'product_id' => $saleitem->product_id,
                'shop_id' => $shop->id,
                'quantity' => 0,
                'buying_per_unit' => $saleitem->buying_per_unit,
                'price_per_unit' => $saleitem->price_per_unit,
                'discount' => $saleitem->discount,
            ]);
        }
        

        return redirect()->back();
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
        $salereturn = SaleReturn::find($request['sale_return_id']);
        $sritem = SaleReturnItem::where('sale_return_id', $salereturn->id)->where('id', $id)->first();
        if (!is_null($sritem)) {
            $product = $shop->products()->where('product_id', $sritem->product_id)->first();
            $sritem->quantity = $request['quantity'];
            $sritem->buying_price = $sritem->quantity*$sritem->buying_per_unit;
            $sritem->price = $sritem->quantity*$sritem->price_per_unit;
            $sritem->total_discount = $sritem->quantity*$sritem->discount;
            $sritem->tax_amount = ($product->pivot->price_with_vat-$product->pivot->price_per_unit)*$sritem->quantity;
            $sritem->save();

            $stock_in = Stock::where('product_id', $product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
            $sold = AnSaleItem::where('product_id', $product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
            $damaged = ProdDamage::where('product_id', $product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
            $tranfered =  TransferOrderItem::where('product_id', $product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
            $returned = SaleReturnItem::where('product_id', $product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                        
            $instock = ($stock_in+$returned)-($sold+$damaged+$tranfered);   
            $product->pivot->in_stock = $instock;
            
            $product->pivot->save();
            

            $items = SaleReturnItem::where('sale_return_id', $salereturn->id)->get();
            $amount = 0;
            $discount = 0;
            $tax = 0;
            foreach ($items as $key => $item) {
                $amount += $item->price;
                $discount += $item->total_discount;
                $tax += $item->tax_amount;
            }

            $salereturn->sale_return_amount = $amount;
            $salereturn->sale_return_discount = $discount;
            $salereturn->return_tax_amount = $tax;
            $salereturn->save();

            $saleupdate = AnSale::find($salereturn->an_sale_id);
            $saleupdate->adjustment = ($salereturn->sale_return_amount-$salereturn->sale_return_discount);
            $saleupdate->save();

            $salepayments = SalePayment::where('an_sale_id', $saleupdate->id)->get();
            $curr_adjs = $saleupdate->adjustment;
            foreach ($salepayments as $key => $pay) {
                if ($curr_adjs > 0) {
                    if ($curr_adjs < $pay->amount) {
                        $pay->amount = $pay->amount-$curr_adjs;
                        $pay->save();
                    }else{
                        $pay->delete();
                    }
                }
                $curr_adjs -= $pay->amount;    
            }

            $sale = AnSale::find($saleupdate->id);
            if ($sale->sale_amount_paid > 0) {
                    
                $amount_paid = 0;
                $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                foreach ($payments as $key => $pay) {  
                    $amount_paid += $pay->amount;
                }

                $sale->sale_amount_paid = $amount_paid;
                $sale->save();
                if (true) {
                    if ($sale->sale_amount-($sale->sale_discount+$sale->adjustment) == $sale->sale_amount_paid) {
                        $sale->status = 'Paid';
                        $sale->time_paid = \Carbon\Carbon::now();
                        $sale->save();
                    }elseif ($sale->sale_amount-($sale->sale_discount+$sale->adjustment) > $sale->sale_amount_paid && $sale->sale_amount_paid > 0) {
                        $sale->status = 'Partially Paid';
                        $sale->time_paid = null;
                        $sale->save();
                    }elseif ($sale->sale_amount-($sale->sale_discount+$sale->adjustment) < $sale->sale_amount_paid) {
                        $sale->status = 'Excess Paid';
                        $sale->time_paid = \Carbon\Carbon::now();
                        $sale->save();
                    }else{
                        $sale->status = 'Unpaid';
                        $sale->time_paid = null;
                        $sale->save();
                    }
                }
            }
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        SaleReturnItem::destroy(decrypt($id));

        return redirect()->back();
    }
}
