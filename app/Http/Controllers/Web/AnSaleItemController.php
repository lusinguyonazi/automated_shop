<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\ServiceSaleItem;
use App\Models\User;
use App\Models\Product;
use App\Models\Stock;
use App\Models\ProdDamage;
use App\Models\TransferOrderItem;
use App\Models\SaleReturnItem;
use App\Models\SalePayment;
use App\Models\Settings;
use App\Models\BankDetail;
USE App\Models\Invoice;
use App\Models\CustomerTransaction;

class AnSaleItemController extends Controller
{
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
        $shop_product =$shop->products()->where('product_id', $request['product_id'])->first();
        $in_stock = $shop_product->pivot->in_stock;
        if (!is_null($shop_product->pivot->price_per_unit)) {
                
            $sale = AnSale::find($request['an_sale_id']);
            if ($request['quantity_sold'] <= $in_stock) {
                $saleitem = new AnSaleItem;
                $saleitem->an_sale_id = $sale->id;
                $saleitem->product_id = $request['product_id'];
                $saleitem->shop_id = $shop->id;
                $saleitem->quantity_sold = $request['quantity_sold'];
                $saleitem->buying_per_unit = $shop_product->pivot->buying_per_unit;
                $saleitem->buying_price = $saleitem->quantity_sold*$saleitem->buying_per_unit;
                $saleitem->price_per_unit = $shop_product->pivot->price_per_unit;
                $saleitem->price = $saleitem->price_per_unit*$saleitem->quantity_sold;
                $saleitem->tax_amount = ($shop_product->pivot->price_with_vat-$shop_product->pivot->price_per_unit)*$saleitem->quantity_sold;
                $saleitem->time_created = $sale->time_created;
                $saleitem->save();

                $stock_in = Stock::where('product_id', $shop_product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                $sold = AnSaleItem::where('product_id', $shop_product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                $damaged = ProdDamage::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                $tranfered =  TransferOrderItem::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                $returned = SaleReturnItem::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                                
                $instock = ($stock_in+$returned)-($sold+$damaged+$tranfered);    
                $shop_product->pivot->in_stock = $instock;
                
                if ($shop_product->pivot->in_stock > $shop_product->pivot->reorder_point) {
                    $shop_product->pivot->status = 'In Stock';
                }elseif ($shop_product->pivot->in_stock == 0) {
                    $shop_product->pivot->status = 'Out of Stock';
                }elseif($shop_product->pivot->in_stock <= $shop_product->pivot->reorder_point && $shop_product->pivot->in_stock != 0){
                    $shop_product->pivot->status = 'Low Stock';
                }
                $shop_product->pivot->save();
                
                $amountp = AnSaleItem::where('an_sale_id', $saleitem->an_sale_id)->sum('price'); 
                $discountp = AnSaleItem::where('an_sale_id', $saleitem->an_sale_id)->sum('total_discount'); 
                $amounts = ServiceSaleItem::where('an_sale_id', $saleitem->an_sale_id)->sum('total');
                $discounts = ServiceSaleItem::where('an_sale_id', $saleitem->an_sale_id)->sum('total_discount');

                $sale = AnSale::find($saleitem->an_sale_id);
                $sale->sale_amount = ($amountp+$amounts);
                $sale->sale_discount = ($discountp+$discounts);
                $sale->save();

                if (($sale->sale_amount-$sale->sale_discount) == $sale->sale_amount_paid) {
                    $sale->status = 'Paid';
                    $sale->time_paid = \Carbon\Carbon::now();
                    $sale->save();
                }elseif (($sale->sale_amount-$sale->sale_discount) > $sale->sale_amount_paid && $sale->sale_amount_paid > 0) {
                    $sale->status = 'Partially Paid';
                    $sale->time_paid = null;
                    $sale->save();
                }elseif (($sale->sale_amount-$sale->sale_discount) < $sale->sale_amount_paid) {
                    $sale->status = 'Excess Paid';
                    $sale->time_paid = \Carbon\Carbon::now();
                    $sale->save();
                }else{
                    $sale->status = 'Unpaid';
                    $sale->time_paid = null;
                    $sale->save();
                }

                $invoice = Invoice::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                if (!is_null($invoice)) {
                    $acctrans = CustomerTransaction::where('invoice_no', $invoice->inv_no)->where('shop_id', $shop->id)->first();
                    if (!is_null($acctrans)) {
                        $acctrans->amount = ($sale->sale_amount-$sale->sale_discount);
                        $acctrans->save();
                    }
                }

                $success = 'Sale item was successfully updated';
                return redirect()->back()->with('success', $success);
            }else{
                $msg_warning = 'Your '.$shop_product->name.' stock is insufficient please reduce no of items or update your stock';
                return redirect('sale-items/'.encrypt($sale->id))->with('warning', $msg_warning);
            }   
        }else{
            $msg_error = 'Your '.$shop_product->name.' has no selling price. Update selling price to sale';
            return redirect('sale-items/'.encrypt($sale->id))->with('error', $msg_error);
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
        $saleitem = AnSaleItem::find(decrypt($id));
        $sale = AnSale::find($saleitem->an_sale_id);
        
        $shop = Shop::find(Session::get('shop_id'));
        $products = $shop->products()->get();
        $product = Product::find($saleitem->product_id);
        $page = 'Edit sale item';
        $title = 'Edit sale item';
        $title_sw = 'Hariri Bidhaa iliyouzwa';
        $prod_detail = 'No';
        return view('sales.edit-item', compact('page', 'title', 'title_sw', 'sale', 'saleitem', 'product', 'products', 'prod_detail'));
    }


    public function editItem($id, Request $request)
    {
        $saleitem = AnSaleItem::find(decrypt($id));
        $sale = AnSale::find($saleitem->an_sale_id);
        
        $shop = Shop::find(Session::get('shop_id'));
        $products = $shop->products()->get();
        $product = Product::find($saleitem->product_id);
        $page = 'Edit sale item';
        $title = 'Edit sale item';
        $title_sw = 'Hariri Bidhaa iliyouzwa';
        $prod_detail = 'Yes';
        return view('sales.edit-item', compact('page', 'title', 'title_sw', 'sale', 'saleitem', 'product', 'products', 'prod_detail'));
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
        if (Auth::user()->hasRole('manager') || Auth::user()->can('manage_sales')) {
            $settings = Setting::where('shop_id', $shop->id)->first();
            $shop_product =$shop->products()->where('product_id', $request['product_id'])->first();
            $in_stock = $shop_product->pivot->in_stock;

            $saleitem = AnSaleItem::find(decrypt($id));

            if ($request['quantity_sold'] <= ($in_stock+$saleitem->quantity_sold)) {
                $saleitem->product_id = $request['product_id'];
                $saleitem->quantity_sold = $request['quantity_sold'];
                $saleitem->buying_per_unit = $request['buying_per_unit'];
                $saleitem->buying_price = $saleitem->quantity_sold*$saleitem->buying_per_unit;
                $saleitem->price_per_unit = $request['price_per_unit'];
                $saleitem->price = $saleitem->price_per_unit*$saleitem->quantity_sold;
                if ($saleitem->quantity_sold > 0) {
                    $saleitem->discount = $request['total_discount']/$saleitem->quantity_sold;
                }
                $saleitem->total_discount = $request['total_discount'];
                $saleitem->with_vat = $request['with_vat'];
                if ($saleitem->with_vat == 'yes') {
                    $vat_amount =  ($saleitem->price-$saleitem->total_discount)*($settings->tax_rate/100);
                    $saleitem->tax_amount = $vat_amount;
                }else{
                    $saleitem->tax_amount = 0;
                }
                $saleitem->save();

                $stock_in = Stock::where('product_id', $shop_product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                $sold = AnSaleItem::where('product_id', $shop_product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                $damaged = ProdDamage::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                $tranfered =  TransferOrderItem::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                $returned = SaleReturnItem::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                            
                $instock = ($stock_in+$returned)-($sold+$damaged+$tranfered);    
                $shop_product->pivot->in_stock = $instock;
                
                if ($shop_product->pivot->in_stock > $shop_product->pivot->reorder_point) {
                    $shop_product->pivot->status = 'In Stock';
                }elseif ($shop_product->pivot->in_stock == 0) {
                    $shop_product->pivot->status = 'Out of Stock';
                }elseif($shop_product->pivot->in_stock <= $shop_product->pivot->reorder_point && $shop_product->pivot->in_stock != 0){
                    $shop_product->pivot->status = 'Low Stock';
                }
                $shop_product->pivot->save();
                

                $amountp = AnSaleItem::where('an_sale_id', $saleitem->an_sale_id)->sum('price'); 
                $discountp = AnSaleItem::where('an_sale_id', $saleitem->an_sale_id)->sum('total_discount'); 
                $amounts = ServiceSaleItem::where('an_sale_id', $saleitem->an_sale_id)->sum('total');
                $discounts = ServiceSaleItem::where('an_sale_id', $saleitem->an_sale_id)->sum('total_discount');
                $taxp = AnSaleItem::where('an_sale_id', $saleitem->an_sale_id)->sum('tax_amount');
                $taxs = ServiceSaleItem::where('an_sale_id', $saleitem->an_sale_id)->sum('tax_amount');

                $sale = AnSale::find($saleitem->an_sale_id);
                $sale->sale_amount = ($amountp+$amounts)+($taxp+$taxs);
                $sale->sale_discount = ($discountp+$discounts);
                $sale->tax_amount = ($taxp+$taxs);
                $sale->save();

                if (($sale->sale_amount-$sale->sale_discount) == $sale->sale_amount_paid) {
                    $sale->status = 'Paid';
                    $sale->time_paid = \Carbon\Carbon::now();
                    $sale->save();
                }elseif (($sale->sale_amount-$sale->sale_discount) > $sale->sale_amount_paid && $sale->sale_amount_paid > 0) {
                    $sale->status = 'Partially Paid';
                    $sale->time_paid = null;
                    $sale->save();
                }elseif (($sale->sale_amount-$sale->sale_discount) < $sale->sale_amount_paid) {
                    $sale->status = 'Excess Paid';
                    $sale->time_paid = \Carbon\Carbon::now();
                    $sale->save();
                }else{
                    $sale->status = 'Unpaid';
                    $sale->time_paid = null;
                    $sale->save();
                }

                $invoice = Invoice::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                if (!is_null($invoice)) {
                    $acctrans = CustomerTransaction::where('invoice_id', $invoice->id)->where('shop_id', $shop->id)->first();
                    if (!is_null($acctrans)) {
                        $acctrans->amount = ($sale->sale_amount-$sale->sale_discount);
                        $acctrans->save();
                    }
                }

                $success = 'Sale item was successfully updated';
                
                if ($request['prod_detail'] == 'Yes') {
                    return redirect()->route('products.show', encrypt($saleitem->product_id))->with('success', $success);
                }else{
                    return redirect()->route('an-sales.show', encrypt($saleitem->an_sale_id))->with('success', $success);
                }
            }else{
                $msg_warning = 'Your '.$shop_product->name.' stock is insufficient please reduce no of items or update your stock';
                return redirect()->route('an-sales.show', encrypt($saleitem->an_sale_id))->with('warning', $msg_warning);
            }
        }else{
            return redirect('unauthorized');
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
        $saleitem = AnSaleItem::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->hasRole('manager') || Auth::user()->can('manage_sales')) {
                    
            if (!is_null($saleitem)) {
            
                $sale = AnSale::find($saleitem->an_sale_id);
                if (!is_null($sale)) {
                        
                    $shop_product = $shop->products()->where('product_id', $saleitem->product_id)->first();
                    $saleitem->delete();
                    if (!is_null($shop_product)) {
                            
                        $stock_in = Stock::where('product_id', $shop_product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                        $sold = AnSaleItem::where('product_id', $shop_product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                        $damaged = ProdDamage::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                        $tranfered =  TransferOrderItem::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                        $returned = SaleReturnItem::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                                        
                        $instock = ($stock_in+$returned)-($sold+$damaged+$tranfered);    
                        $shop_product->pivot->in_stock = $instock;
                        
                        if ($shop_product->pivot->in_stock > $shop_product->pivot->reorder_point) {
                            $shop_product->pivot->status = 'In Stock';
                        }elseif ($shop_product->pivot->in_stock == 0) {
                            $shop_product->pivot->status = 'Out of Stock';
                        }elseif($shop_product->pivot->in_stock <= $shop_product->pivot->reorder_point && $shop_product->pivot->in_stock != 0){
                            $shop_product->pivot->status = 'Low Stock';
                        }
                        $shop_product->pivot->save();
                    }
                    

                    $amountp = AnSaleItem::where('an_sale_id', $sale->id)->sum('price'); 
                    $discountp = AnSaleItem::where('an_sale_id', $sale->id)->sum('total_discount'); 
                    $amounts = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total');
                    $discounts = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total_discount');
                    $taxp = AnSaleItem::where('an_sale_id', $saleitem->an_sale_id)->sum('tax_amount');
                    $taxs = ServiceSaleItem::where('an_sale_id', $saleitem->an_sale_id)->sum('tax_amount');

                    $sale->sale_amount = ($amountp+$amounts)+($taxp+$taxs);
                    $sale->sale_discount = ($discountp+$discounts);
                    $sale->tax_amount = ($taxp+$taxs);
                    $sale->save();

                    if (($sale->sale_amount-$sale->sale_discount) == $sale->sale_amount_paid) {
                        $sale->status = 'Paid';
                        $sale->time_paid = \Carbon\Carbon::now();
                        $sale->save();
                    }elseif (($sale->sale_amount-$sale->sale_discount) > $sale->sale_amount_paid && $sale->sale_amount_paid > 0) {
                        $sale->status = 'Partially Paid';
                        $sale->time_paid = null;
                        $sale->save();
                    }elseif (($sale->sale_amount-$sale->sale_discount) < $sale->sale_amount_paid) {
                        $sale->status = 'Excess Paid';
                        $sale->time_paid = \Carbon\Carbon::now();
                        $sale->save();
                    }else{
                        $sale->status = 'Unpaid';
                        $sale->time_paid = null;
                        $sale->save();
                    }

                    $invoice = Invoice::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                    if (!is_null($invoice)) {
                        $acctrans = CustomerTransaction::where('invoice_id', $invoice->id)->where('shop_id', $shop->id)->first();
                        if (!is_null($acctrans)) {
                            $acctrans->amount = ($sale->sale_amount-$sale->sale_discount);
                            $acctrans->save();
                        }
                    }
                }

                $success = 'Sale item was successfully deleted';
                return redirect()->route('an-sales.show', encrypt($saleitem->an_sale_id))->with('success', $success);
            }
        }else{
            return redirect('unauthorized');
        }
    }
}
