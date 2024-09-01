<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use Crypt;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\Purchase;
use App\Models\Setting;
use App\Models\Payment;
use App\Models\Stock;
use App\Models\PurchaseTemp;
use App\Models\PurchaseItemTemp;
use App\Models\Product;
use App\Models\ProdDamage;
use App\Models\TransferOrderItem;
use App\Models\AnSaleItem;
use App\Models\SaleReturnItem;
use App\Models\PaymentVoucher;
use App\Models\PurchasePayment;
use App\Models\SupplierTransaction;
use App\Models\SupplierAccount;
use App\Models\Supplier;

class PurchasesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Products';
        $title = 'Purchases';
        $title_sw = 'Manunuzi';
        $shop = Shop::find(Session::get('shop_id'));
        $purchases = Purchase::where('shop_id', $shop->id)->where('is_deleted', false)->orderBy('purchases.time_created', 'desc')->get();
        $suppliers = $shop->suppliers()->get();
        $pvs = PaymentVoucher::where('shop_id', $shop->id)->where('voucher_for', 'Purchase')->orderBy('created_at', 'desc')->get();

        return view('products.purchases.index', compact('page', 'title', 'title_sw', 'shop', 'purchases', 'suppliers', 'pvs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'Products';
        $title = 'New Purchase';
        $title_sw = 'Manunuzi Mapya';
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
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();

        $mindays = 0;
        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', false)->first();
        if (!is_null($payment)) {
            $now = Carbon::now();
            $paydate = Carbon::parse($payment->created_at);

            $lastpay = Payment::where('shop_id', $shop->id)->where('is_expired', 1)->where('is_for_module', false)->orderBy('created_at', 'desc')->first();
            if (!is_null($lastpay)) {
                $lastexp = Carbon::parse($lastpay->expire_date);
                $oldpaydate = Carbon::parse($lastpay->created_at);
                $slipdays = $paydate->diffInDays($lastexp);
                // return $slipdays;
                if ($slipdays < 15) {
                    $mindays = $now->diffInDays($oldpaydate);
                }else{
                    $mindays = $now->diffInDays($paydate);
                } 
            }else{
                $mindays = $now->diffInDays($paydate);
            }
        }

        if ($mindays < 10) {
            $mindays = 15;
        }

        $products = $shop->products()->get();
        $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        if (is_null($dfcurr)) {
            return redirect('settings')->with('error', 'Please add your Default Currency to continue...');
        }

        $purchasetemp = PurchaseTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->whereNull('supplier_id')->first();
        if (is_null($purchasetemp)) {
            $purchasetemp = new PurchaseTemp();
            $purchasetemp->shop_id = $shop->id;
            $purchasetemp->user_id = $user->id;
            $purchasetemp->currency = $dfcurr->code;
            $purchasetemp->defcurr = $dfcurr->code;
            $purchasetemp->save();
        }

        $pendingtemps = PurchaseTemp::where('purchase_temps.shop_id', $shop->id)->where('user_id', $user->id)->whereNotNull('supplier_id')->join('suppliers', 'suppliers.id', '=', 'purchase_temps.supplier_id')->select('purchase_temps.id as id', 'name', 'purchase_temps.created_at as created_at')->get();
        return view('products.purchases.create', compact('page', 'title', 'title_sw', 'units', 'settings', 'products', 'shop', 'mindays', 'dfcurr', 'purchasetemp', 'pendingtemps'));
    }

    public function pendingPurchase(Request $request)
    {
        $page = 'Products';
        $title = 'New Purchase';
        $title_sw = 'Manunuzi Mapya';
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
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();

        $mindays = 0;
        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', false)->first();
        if (!is_null($payment)) {
            $now = Carbon::now();
            $paydate = Carbon::parse($payment->created_at);

            $lastpay = Payment::where('shop_id', $shop->id)->where('is_expired', 1)->where('is_for_module', false)->orderBy('created_at', 'desc')->first();
            if (!is_null($lastpay)) {
                $lastexp = Carbon::parse($lastpay->expire_date);
                $oldpaydate = Carbon::parse($lastpay->created_at);
                $slipdays = $paydate->diffInDays($lastexp);
                // return $slipdays;
                if ($slipdays < 15) {
                    $mindays = $now->diffInDays($oldpaydate);
                }else{
                    $mindays = $now->diffInDays($paydate);
                } 
            }else{
                $mindays = $now->diffInDays($paydate);
            }
        }

        if ($mindays < 10) {
            $mindays = 15;
        }

        $products = $shop->products()->get();
        $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        if (is_null($dfcurr)) {
            return redirect('settings')->with('error', 'Please add your Default Currency to continue...');
        }

        $purchasetemp = PurchaseTemp::find($request['id']);

        $pendingtemps = PurchaseTemp::where('purchase_temps.shop_id', $shop->id)->where('user_id', $user->id)->whereNotNull('supplier_id')->join('suppliers', 'suppliers.id', '=', 'purchase_temps.supplier_id')->select('purchase_temps.id as id', 'name', 'purchase_temps.created_at as created_at')->get();
        return view('products.purchases.create', compact('page', 'title', 'title_sw', 'units', 'settings', 'products', 'shop', 'mindays', 'dfcurr', 'purchasetemp', 'pendingtemps'));
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
        $purchasetemp = PurchaseTemp::find($request['purchase_temp_id']);
        if (!is_null($purchasetemp)) {
            $now = null;
            if (is_null($purchasetemp->purchase_date)) {
                $now = Carbon::now();
            }else{
                $crtime = Carbon::now();
                $time = date('h:i:s', strtotime($crtime));
                $now = $purchasetemp->purchase_date.' '.$time;
            }

            $pitems = PurchaseItemTemp::where('purchase_temp_id', $purchasetemp->id)->get();
            if (!is_null($pitems)) {
                $temps = array();
                foreach ($pitems as $key => $value) {
                    if ($value->quantity_in == 0) {
                        array_push($temps, $value->quantity_in);
                    }
                }

                if (!empty($temps)) {
                    return redirect()->back()->with('warning', 'Please update the quantity and Unit cost of each item to continue');
                }else{
                    $total_amount = 0;
                    $amount_paid = 0; 

                    $max_no = Purchase::where('shop_id', $shop->id)->orderBy('created_at', 'desc')->first();
                    $grnno = 0;
                    if (!is_null($max_no)) {
                        $grnno = $max_no->grn_no+1;
                    }else{
                        $grnno = 1;
                    }

                    $purchase = Purchase::create([
                        'shop_id' => $shop->id,
                        'user_id' => $user->id,
                        'supplier_id' => $purchasetemp->supplier_id,
                        'grn_no' => $grnno, 
                        'order_no' => $purchasetemp->order_no,
                        'delivery_note_no' => $purchasetemp->delivy_note_no,
                        'invoice_no' => $purchasetemp->invoice_no,
                        'total_amount' => $total_amount,
                        'amount_paid' => $amount_paid,
                        'comments' => $purchasetemp->comments,
                        'time_created' => $now,
                        'purchase_type' => $purchasetemp->purchase_type,
                        'currency' => $purchasetemp->currency,
                        'defcurr' => $purchasetemp->defcurr,
                        'ex_rate' => $purchasetemp->ex_rate,
                    ]);

                    foreach ($pitems as $key => $item) {
                        $product = Product::find($item->product_id);
                        $stock  = new Stock;
                        $stock->product_id = $product->id;
                        $stock->purchase_id = $purchase->id;
                        $stock->shop_id = $shop->id;
                        $stock->quantity_in = $item->quantity_in;
                        $stock->buying_per_unit = $item->buying_per_unit;
                        $stock->source = 'Purchased';
                        $stock->time_created = $now;
                        $stock->expire_date = $item->expire_date;
                        $stock->save();

                        $shop_product = $shop->products()->where('product_id', $product->id)->first();
                        if (!is_null($shop_product)) {
                            
                            if ($shop_product->pivot->in_stock == 0) {
                                $shop_product->pivot->buying_per_unit = $item->buying_per_unit;
                                $shop_product->pivot->save();
                            }

                            $stock_in = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                            $sold = AnSaleItem::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                            $damaged = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                            $tranfered =  TransferOrderItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                            $returned = SaleReturnItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                                            
                            $instock = ($stock_in+$returned)-($sold+$damaged+$tranfered); 
                                         
                            $shop_product->pivot->in_stock = $instock;
                            if ($shop_product->pivot->in_stock > $shop_product->pivot->reorder_point) {
                                $shop_product->pivot->status = 'In Stock';
                            }elseif ($shop_product->pivot->in_stock == 0) {
                                    $shop_product->pivot->status = 'Out of Stock';
                            }elseif($shop_product->pivot->in_stock <= $shop_product->pivot->reorder_point && $shop_product->pivot->in_stock != 0){
                                $shop_product->pivot->status = 'Low Stock';
                            }
                            if ($item->price_per_unit != 0) {
                                $shop_product->pivot->price_per_unit = $item->price_per_unit;
                            }
                            $shop_product->pivot->save();
                        }
                     

                        $total_amount += $item->total;
                    }

                    if ($request['purchase_type'] == 'cash') {
                        $amount_paid = $total_amount;
                    }else{
                        $amount_paid = $purchasetemp->amount_paid;
                    }

                    $purchase->total_amount = $total_amount;
                    $purchase->amount_paid = $amount_paid;
                    $purchase->save();

                    $pvno = null;
                    if ($amount_paid > 0) {
                        $pvno = 0;
                        $max_pv_no = PaymentVoucher::where('shop_id', $shop->id)->orderBy('created_at', 'desc')->first();
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
                        $pv->voucher_for = 'Purchase';
                        $pv->save();

                        $payment = PurchasePayment::create([
                            'shop_id' => $shop->id,
                            'purchase_id' => $purchase->id,
                            'account' => $request['account'],
                            'pay_date' => $now,
                            'amount' => $amount_paid,
                            'currency' => $purchasetemp->currency,
                            'defcurr' => $purchasetemp->defcurr,
                            'ex_rate' => $purchasetemp->ex_rate,
                            'pv_no' => $pvno
                        ]);
                    }

                    if (!is_null($purchasetemp->supplier_id)) {
                        $supplier = Supplier::find($purchasetemp->supplier_id);
                        if ($shop->subscription_type_id >= 3) {
                            
                            $acctrans = new SupplierTransaction();
                            $acctrans->shop_id = $shop->id;
                            $acctrans->user_id = $user->id;
                            $acctrans->supplier_id = $supplier->id;
                            $acctrans->purchase_id = $purchase->id;
                            $acctrans->invoice_no = $request['invoice_no'];
                            $acctrans->amount = $total_amount;
                            $acctrans->currency = $purchasetemp->currency;
                            $acctrans->defcurr = $purchasetemp->defcurr;
                            $acctrans->ex_rate = $purchasetemp->ex_rate;
                            $acctrans->pv_no = $pvno;
                            if ($amount_paid > 0) {
                                $acctrans->payment = $amount_paid;
                                $acctrans->payment_mode = $request['account'];  
                            }
                            $acctrans->date = $now;
                            $acctrans->save();

                            $utransactions = SupplierTransaction::where('shop_id', $shop->id)->where('supplier_id', $supplier->id)->whereNotNull('pv_no')->where('is_utilized', false)->where('is_deleted', false)->get();

                            if (!is_null($utransactions)) {
                                foreach ($utransactions as $key => $trans) {
                                    $rem_amount = $trans->payment-($trans->trans_invoice_amount+$trans->trans_ob_amount+$trans->trans_credit_amount);
                                    if ($rem_amount > 0) {
                                        $paidamount = 0;
                                        if ($rem_amount > $purchase->total_amount) {
                                            $paidamount = $purchase->total_amount;
                                            $trans->trans_invoice_amount = $trans->trans_invoice_amount+$paidamount;
                                            $trans->save();
                                        }else{
                                            $paidamount = $rem_amount;
                                            $trans->trans_invoice_amount = $trans->trans_invoice_amount+$paidamount;
                                            $trans->is_utilized = true;
                                            $trans->save();
                                        }

                                        $payment = PurchasePayment::create([
                                            'purchase_id' => $purchase->id,
                                            'shop_id' => $shop->id,
                                            'trans_id' => $trans->id,
                                            'pv_no' => $trans->pv_no,
                                            'pay_mode' => $trans->payment_mode,
                                            'bank_name' => $trans->bank_name,
                                            'bank_branch' => $trans->bank_branch,
                                            'pay_date' => $trans->date,
                                            'cheque_no' => $trans->cheque_no,
                                            'amount' => $paidamount,
                                            'currency' => $trans->currency,
                                            'defcurr' => $trans->defcurr,
                                            'ex_rate' => $trans->ex_rate,
                                        ]);

                                        $purchase->amount_paid = $paidamount;
                                        if (($purchase->total_amount-$purchase->amount_paid) == 0) {
                                            $purchase->status = 'Paid';
                                        }
                                        $purchase->save();
                                    }
                                }
                            }
                        }
                    }
                    
                    $puritems = PurchaseItemTemp::where('purchase_temp_id', $purchasetemp->id)->get();
                    foreach ($puritems as $key => $value) {
                        $value->delete();
                    }
                    $purchasetemp->delete();
                    return redirect('purchases')->with('success', 'Stocks were added successfully');
                }
            }else{
                return redirect()->back()->with('warning', 'Please Select at least one Product to continue!.');
            }
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
        
        $page = 'Products';
        $title = 'Goods Received Note (GRN)';
        $title_sw = 'Bidhaa zilizopokelewa (GRN)';

        $shop = Shop::find(Session::get('shop_id'));
        $purchase = Purchase::where('id', decrypt($id))->first();
        $supplier = Supplier::find($purchase->supplier_id);

        $pitems = Stock::where('purchase_id', $purchase->id)->join('products', 'products.id', '=', 'stocks.product_id')->select('stocks.id as id', 'stocks.quantity_in as quantity_in', 'stocks.buying_per_unit as buying_per_unit', 'stocks.time_created as time_created', 'stocks.created_at as created_at', 'products.name as name', 'products.basic_unit as basic_unit')->orderBy('time_created', 'desc')->get();

        return view('products.purchases.show', compact('page', 'title', 'title_sw', 'shop', 'purchase', 'pitems', 'supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Products';
        $title = 'Update Purchase';
        $title_sw = 'Hariri Manunuzi';
        $shop = Shop::find(Session::get('shop_id'));
        $purchase = Purchase::find(decrypt($id));
        $suppliers = $shop->suppliers()->get();
        if (!is_null($purchase)) {
            return view('products.purchases.edit', compact('page', 'title', 'title_sw', 'purchase', 'suppliers', 'shop'));
        }else{
            return redirect('purchases');
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
        $user = Auth::user();
        $purchase = Purchase::find(decrypt($id));
        $purchase->supplier_id = $request['supplier_id'];
        $purchase->comments = $request['comments'];
        $purchase->order_no = $request['order_no'];
        $purchase->delivery_note_no = $request['delivery_note_no'];
        $purchase->invoice_no = $request['invoice_no'];
        $purchase->save();

        $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->where('shop_id', $purchase->shop_id)->first();
        if (!is_null($acctrans) ) {
            $acctrans->supplier_id = $purchase->supplier_id;
            $acctrans->invoice_no = $purchase->invoice_no;
            $acctrans->save();
        }elseif (!is_null($request['supplier_id'] && $purchase->purchase_type == 'credit')) {
            if ($shop->subscription_type_id >= 3) {
                $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->first();
                if (is_null($acctrans)) {
                    $acctrans = new SupplierTransaction();
                    $acctrans->shop_id = $shop->id;
                    $acctrans->user_id = $user->id;
                    $acctrans->supplier_id = $purchase->supplier_id;
                    $acctrans->purchase_id = $purchase->id;
                    $acctrans->invoice_no = $purchase->invoice_no;
                    $acctrans->amount = $purchase->total_amount;
                    $acctrans->date = date('Y-m-d', strtotime($purchase->time_created));
                    $acctrans->save();
                }else{
                    $acctrans->invoice_no = $purchase->invoice_no;
                    $acctrans->date = date('Y-m-d', strtotime($purchase->time_created));
                    $acctrans->save();
                }
            }
        }

        return redirect('purchases')->with('success', 'Purchase was updated successfully');
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
        $purchase = Purchase::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (!is_null($purchase)) {
            $pitems = Stock::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->get();

            foreach ($pitems as $key => $value) {
                $value->is_deleted = true;
                $value->del_by = $user->first_name.'('.Carbon::now().')';
                $value->save();

                $product = Product::find($value->product_id);
                $shop_product = $shop->products()->where('product_id', $product->id)->first();

                $stock_in = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                $sold = AnSaleItem::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                $damaged = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                $tranfered =  TransferOrderItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                $returned = SaleReturnItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                                
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
                $message = 'Stock was successfully deleted'; 
            }

            $payments = PurchasePayment::where('purchase_id', $purchase->id)->get();
            foreach ($payments as $key => $payment) {
                $pv = PaymentVoucher::where('pv_no', $payment->pv_no)->first();
                if (!is_null($pv)) {
                    $acctrans = SupplierTransaction::where('pv_no', $purchase->pv_no)->where('shop_id', $shop->id)->first();
                    if (!is_null($acctrans)) {
                        $acctrans->is_deleted = true;
                        $acctrans->save();
                    }
                    $pv->delete();
                }
                $payment->is_deleted = true;
                $payment->save();
            }

            $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->first();
            if ($acctrans) {
                $acctrans->is_deleted = true;
                $acctrans->save();
            }
            
            $purchase->is_deleted = true;
            $purchase->del_by = $user->first_name.' ('.Carbon::now().')';
            $purchase->save();
            
            return redirect()->back()->with('success', 'Purchase was deleted successfully');
        }
    }

    public function deleteMultiple(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));

        $user = Auth::user();
        foreach ($request->input('id') as $key => $id) {
            $purchase = Purchase::where('id', $id)->where('shop_id', $shop->id)->first();

            $pitems = Stock::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->get();

            foreach ($pitems as $key => $value) {
                $value->is_deleted = true;
                $value->del_by = $user->first_name.'('.Carbon::now().')';
                $value->save();

                $product = Product::find($value->product_id);
                $shop_product = $shop->products()->where('product_id', $product->id)->first();

                $stock_in = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                $sold = AnSaleItem::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                $damaged = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                $tranfered =  TransferOrderItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                $returned = SaleReturnItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                                
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
                $message = 'Stock was successfully deleted';  
            }

            $payments = PurchasePayment::where('purchase_id', $purchase->id)->get();

            foreach ($payments as $key => $payment) {
                $pv = PaymentVoucher::where('pv_no', $payment->pv_no)->first();
                if (!is_null($pv)) {
                    $acctrans = SupplierTransaction::where('pv_no', $purchase->pv_no)->where('shop_id', $shop->id)->first();
                    if (!is_null($acctrans)) {
                        $acctrans->is_deleted = true;
                        $acctrans->save();
                        $acctrans->delete();
                    }
                   
                    $pv->delete();
                }

                $payment->is_deleted = true;
                $payment->save();
                // $payment->delete();
            }

            $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->first();
            if ($acctrans) {
                $acctrans->is_deleted = true;
                $acctrans->save();
                // $acctrans->delete();
            }
            
            $purchase->is_deleted = true;
            $purchase->del_by = $user()->first_name.' ('.Carbon::now().')';
            $purchase->save();
            // $purchase->delete();
        }

        return redirect()->back()->with('success', 'Purchases were deleted successfully');
    }

    public function purchaseItems($id)
    {
        $page = 'Products';
        $title = 'Purchase details';
        $title_sw = 'Maelezo ya Manunuzi';

        $shop = Shop::find(Session::get('shop_id'));
        $purchase = Purchase::where('id', Crypt::decrypt($id))->first();
        $supplier = Supplier::find($purchase->supplier_id);
        $products = $shop->products()->get();

        $pitems = Stock::where('time_created', $purchase->time_created)->where('shop_id', $shop->id)->join('products', 'products.id', '=', 'stocks.product_id')->select('stocks.id as id', 'stocks.quantity_in as quantity_in', 'stocks.buying_per_unit as buying_per_unit', 'stocks.time_created as time_created', 'stocks.created_at as created_at', 'products.name as name', 'products.basic_unit as basic_unit')->orderBy('time_created', 'desc')->get();
       
        $payments = PurchasePayment::where('purchase_id', $purchase->id)->get();

        return view('products.purchases.items', compact('page', 'title', 'title_sw', 'purchase', 'pitems', 'supplier', 'payments', 'products'));
    }

    public function addItem(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $purchase = Purchase::find($request['purchase_id']);
        if (!is_null($purchase)) {
            $product = Product::find($request['product_id']);
            if (!is_null($product)) {
                
                $stock  = new Stock;
                $stock->product_id = $product->id;
                $stock->shop_id = $shop->id;
                $stock->quantity_in = $request['quantity_in'];
                $stock->buying_per_unit = $request['buying_per_unit'];
                $stock->source = 'Purchased';
                $stock->time_created = $purchase->time_created;
                $stock->save();

                $shop_product = $shop->products()->where('product_id', $product->id)->first();

                if ($shop_product->pivot->in_stock == 0) {
                    $shop_product->pivot->buying_per_unit = $request['buying_per_unit'];
                    $shop_product->pivot->save();
                }

                $stock_in = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                $sold = AnSaleItem::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                $damaged = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                $tranfered =  TransferOrderItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                $returned = SaleReturnItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                                    
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

                $pitems = Stock::where('time_created', $purchase->time_created)->where('shop_id', $shop->id)->get();
                $total_amount = 0;
                foreach ($pitems as $key => $item) {
                    $total_amount += ($item->quantity_in*$item->buying_per_unit);
                }

                $purchase->total_amount = $total_amount;
                $purchase->save();


                $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->where('shop_id', $purchase->shop_id)->first();
                if (!is_null($acctrans) ) {
                    $acctrans->amount = $purchase->total_amount;
                    $acctrans->save();
                }

            }       
        }

        return redirect()->back()->with('success', 'Item Was Added successfully');
    }

    public function agingReport(Request $request)
    {
            
        $page = 'Reports';
        $title = 'Aging Reports';
        $title_sw = 'Ripoti za ';
        $shop = Shop::find(Session::get('shop_id'));

        $date0 = \Carbon\Carbon::today()->format('Y-m-d'); 
        $date3 = \Carbon\Carbon::today()->subDays(30)->format('Y-m-d');
        $date6 = \Carbon\Carbon::today()->subDays(60)->format('Y-m-d');
        $date9 = \Carbon\Carbon::today()->subDays(90)->format('Y-m-d');
        $date12 = \Carbon\Carbon::today()->subDays(120)->format('Y-m-d');
        $date15 = \Carbon\Carbon::today()->subDays(150)->format('Y-m-d');
        $date18 = \Carbon\Carbon::today()->subDays(180)->format('Y-m-d');
        $date21 = \Carbon\Carbon::today()->subDays(210)->format('Y-m-d');
        $date24 = \Carbon\Carbon::today()->subDays(240)->format('Y-m-d');
        $date27 = \Carbon\Carbon::today()->subDays(270)->format('Y-m-d');
        $date30 = \Carbon\Carbon::today()->subDays(300)->format('Y-m-d');
        $date33 = \Carbon\Carbon::today()->subDays(330)->format('Y-m-d');
        $date36 = \Carbon\Carbon::today()->subDays(360)->format('Y-m-d');

        $suppliers = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereRaw('(total_amount-amount_paid) > 0')->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select('suppliers.id as id', 'suppliers.supp_id as supp_id', 'suppliers.name as name')->groupBy('name')->get();

        $agings = array();
        foreach ($suppliers as $key => $supplier) {
            $d3 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '>=', $date3)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();

            $d6 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date3)->whereDate('purchases.created_at', '>', $date6)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();

            $d9 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date6)->whereDate('purchases.created_at', '>', $date9)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();
            
            $d12 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date9)->whereDate('purchases.created_at', '>', $date12)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();

            $d15 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date12)->whereDate('purchases.created_at', '>', $date15)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();
            
            $d18 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date15)->whereDate('purchases.created_at', '>', $date18)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();
            
            $d21 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date18)->whereDate('purchases.created_at', '>', $date21)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();
            
            $d24 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date21)->whereDate('purchases.created_at', '>', $date24)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();
            
            $d27 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date24)->whereDate('purchases.created_at', '>', $date27)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();
            
            $d30 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date27)->whereDate('purchases.created_at', '>', $date30)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();
            
            $d33 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date30)->whereDate('purchases.created_at', '>', $date33)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();
            
            $d36 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date33)->whereDate('purchases.created_at', '>', $date36)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();

            $ab360 = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereDate('purchases.created_at', '<=', $date36)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();

            $ctotal = Purchase::where('purchases.shop_id', $shop->id)->where('is_deleted', false)->whereRaw('(total_amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select(
                \DB::raw('SUM((total_amount-amount_paid)) as amount'))->first();

            array_push($agings, ['supp_id' => $supplier->supp_id, 'name' => $supplier->name, '0-30' => $d3->amount, '31-60' => $d6->amount, '61-90' => $d9->amount, '91-120' => $d12->amount, '121-150' => $d15->amount, '151-180' => $d18->amount, '181-210' => $d21->amount, '211-240' => $d24->amount, '241-270' => $d27->amount, '271-300' => $d30->amount, '301-330' => $d33->amount, '331-360' => $d36->amount, '>360' => $ab360->amount, 'ctotal' => $ctotal->amount]);
        }

        $start_date = null;            
        $end_date = null;
        $is_post_query = false;
        $customer = null;
        $customers = null;
        $crtime = \Carbon\Carbon::now();
        $duration = date('d M, Y', strtotime($crtime));
        $duration_sw = date('d M, Y', strtotime($crtime));
        $reporttime = $crtime->toDayDateTimeString();

        return view('products.purchases.aging-report', compact('page', 'title', 'title_sw', 'shop', 'agings', 'start_date', 'end_date', 'is_post_query', 'customer', 'customers', 'duration', 'duration_sw', 'reporttime'));
    }

    public function setOpeningBalance(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $opdate = null;
        if (!is_null($request['open_date'])) {
            $opdate = $request['open_date'];
        }else{
            $opdate = Carbon::now();
        }

        $acctrans = SupplierTransaction::where('supplier_id', $request['supplier_id'])->where('is_ob', true)->first();
        if (!is_null($acctrans)) {
            $acctrans->amount = $request['amount'];
            $acctrans->ob_paid = $request['ob_paid'];
            $acctrans->date = $opdate;
            $acctrans->save();
        }else{
            $acctrans = new SupplierTransaction();
            $acctrans->shop_id = $shop->id;
            $acctrans->user_id = $user->id;
            $acctrans->supplier_id = $request['supplier_id'];
            $acctrans->is_ob = true;
            $acctrans->amount = $request['amount'];
            $acctrans->currency = $request['currency'];
            $acctrans->date = $opdate;
            $acctrans->save();
        }

        return redirect()->back()->with('success', 'Opening balance was created successfully');
    }
}