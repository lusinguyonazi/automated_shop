<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Crypt;
use Auth;
use Session;
use Log;
use \Carbon\Carbon;
// use App\Shop;
// use App\AnSale;
// use App\AnCost;
use App\Models\AnSaleItem;
use App\Models\ServiceSaleItem;
use App\Models\Stock;
use App\Models\CustomerTransaction;
// use App\Models\CustomerAccount;
use App\Models\SaleReturnItem;
use App\Models\TransferOrderItem;
use App\Models\ProdDamage;
use App\Models\SalePayment;
use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\PurchasePayment;
use App\Models\SupplierTransaction;
use App\Models\SupplierAccount;
use App\Models\PaymentVoucher;
use App\Models\AnSale;
use App\Models\Customer;
use App\Models\Shop;
use App\Models\Expense;
use Exception;
use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Support\Facades\Crypt as FacadesCrypt;

class RecycleBinController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'isManager']);
    }

    public function index(Request $request)
    {
        $page = 'Recycle Bin';
        $title = 'Recycle Bin';
        $title_sw = 'Recycle Bin';
        $shop = Shop::find(Session::get('shop_id'));

        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;
        $end_date = null;

        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['sale_date'])) {
            $start_date = $request['sale_date'];
            $end_date = $request['sale_date'];
            $start = $request['sale_date'] . ' 00:00:00';
            $end = $request['sale_date'] . ' 23:59:59';
            $is_post_query = true;
        } else if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        } else {
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        if (!empty($request->customer)) {
            $cus_id = Crypt::decrypt($request->customer);
            $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('is_deleted', true)->where('customer_id', $cus_id)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.time_created as time_created', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.del_by as del_by', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'users.first_name as first_name')->orderBy('an_sales.time_created', 'desc')->paginate(20)->withQueryString();
        } else {
            $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('is_deleted', true)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.time_created as time_created', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.del_by as del_by', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'users.first_name as first_name')->orderBy('an_sales.time_created', 'desc')->paginate(20)->withQueryString();
        }
        $customers = Customer::where('shop_id', $shop->id)->get();

        return view('accounts.recyclebin', compact('page', 'title', 'title_sw', 'sales', 'is_post_query', 'start_date', 'end_date', 'customers'));
    }

    public function recycleSale($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->hasRole('manager')) {
            $sale = AnSale::where('id', Crypt::decrypt($id))->where('shop_id', $shop->id)->first();
            if (is_null($sale)) {
                return redirect('forbiden');
            } else {
                $items = AnSaleItem::where('an_sale_id', $sale->id)->get();
                if (!is_null($items)) {
                    foreach ($items as $key => $item) {
                        $shop_product = $shop->products()->where('product_id', $item->product_id)->first();

                        $item->is_deleted = false;
                        $item->del_by = null;
                        $item->save();
                        // $item->delete();
                        if (!is_null($shop_product)) {

                            $stock_in = Stock::where('product_id', $shop_product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                            $stock_out = AnSaleItem::where('product_id', $shop_product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                            $damaged = ProdDamage::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                            $tranfered =  TransferOrderItem::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                            $returned = SaleReturnItem::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');

                            $instock = ($stock_in + $returned) - ($stock_out + $damaged + $tranfered);
                            $shop_product->pivot->in_stock = $instock;

                            $shop_product->pivot->save();
                        }
                    }
                }

                $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->get();
                if (!is_null($servitems)) {
                    foreach ($servitems as $key => $sitem) {
                        $sitem->is_deleted = false;
                        $sitem->del_by = null;
                        $sitem->save();
                        // $sitem->delete();
                    }
                }

                $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                if (!is_null($payments)) {
                    foreach ($payments as $key => $payment) {
                        $payment->is_deleted = false;
                        $payment->save();
                        // $payment->delete();
                    }
                }

                $invoice = Invoice::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                if (!is_null($invoice)) {
                    $acctrans = CustomerTransaction::where('invoice_no', $invoice->inv_no)->where('shop_id', $shop->id)->first();
                    if (!is_null($acctrans)) {
                        $acctrans->is_deleted = false;
                        $acctrans->save();
                        // $acctrans->delete();
                    }
                    $invoice->is_deleted = false;
                    $invoice->save();
                    // $invoice->delete();
                }

                // $acctrans = CustomerAccount::where('sale_no', $sale->sale_no)->where('shop_id', $shop->id)->first();
                // if (!is_null($acctrans)) {
                //     $acctrans->delete();
                // }

                $sale->is_deleted = false;
                $sale->del_by = null;
                $sale->save();
                // $sale->delete();

                $success = 'Your sale was succesfuly Restored';
                return redirect()->back()->with('success', $success);
            }
        } else {
            return redirect('unauthorized');
        }
    }

    public function delRecycleSale($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->hasRole('manager')) {
            $sale = AnSale::where('id', Crypt::decrypt($id))->where('shop_id', $shop->id)->first();
            if (is_null($sale)) {
                return redirect('forbiden');
            } else {
                $items = AnSaleItem::where('an_sale_id', $sale->id)->get();
                if (!is_null($items)) {
                    foreach ($items as $key => $item) {
                        $shop_product = $shop->products()->where('product_id', $item->product_id)->first();

                        $item->delete();
                        if (!is_null($shop_product)) {

                            $stock_in = Stock::where('product_id', $shop_product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                            $stock_out = AnSaleItem::where('product_id', $shop_product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                            $damaged = ProdDamage::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                            $tranfered =  TransferOrderItem::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                            $returned = SaleReturnItem::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');

                            $instock = ($stock_in + $returned) - ($stock_out + $damaged + $tranfered);
                            $shop_product->pivot->in_stock = $instock;

                            $shop_product->pivot->save();
                        }
                    }
                }

                $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->get();
                if (!is_null($servitems)) {
                    foreach ($servitems as $key => $sitem) {
                        $sitem->delete();
                    }
                }

                $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                if (!is_null($payments)) {
                    foreach ($payments as $key => $payment) {
                        $payment->delete();
                    }
                }

                $invoice = Invoice::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                if (!is_null($invoice)) {
                    $acctrans = CustomerTransaction::where('invoice_no', $invoice->inv_no)->where('shop_id', $shop->id)->first();
                    if (!is_null($acctrans)) {
                        $acctrans->delete();
                    }

                    $invoice->delete();
                }

                // $acctrans = CustomerAccount::where('sale_no', $sale->sale_no)->where('shop_id', $shop->id)->first();
                // if (!is_null($acctrans)) {
                //     $acctrans->delete();
                // }

                $sale->delete();

                $success = 'Your sale was succesfuly deleted';
                return redirect()->back()->with('success', $success);
            }
        } else {
            return redirect('unauthorized');
        }
    }


    public function delMultipleRecycleSales(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));

        if (Auth::user()->hasRole('manager')) {
            if (!is_null($request->input('id'))) {
                foreach ($request->id as $key => $sid) {
                    $decrypted = Crypt::decrypt($sid);
                    $sale = AnSale::where('id', $decrypted)->where('shop_id', $shop->id)->first();
                    $sale->delete();
                }
                $success = 'Sales were deleted successfully';
                return redirect('recyclebin')->with('success', $success);
            } else {
                return redirect('recyclebin')->with('info', 'No Recycle Sales selected to Delete');
            }
        } else {
            return redirect('unauthorized');
        }
    }




    

    //Delete multiple purchases
    public function delMultipleRecyclePurchases(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));

        if (Auth::user()->hasRole('manager')) {
            if (!is_null($request->input('id'))) {
                foreach ($request->id as $key => $pid) {
                    $decrypted = Crypt::decrypt($pid);
                    $purchase = Purchase::where('id', $decrypted)->where('shop_id', $shop->id)->first();
                    $purchase->delete();
                }
                $success = 'Purchases were deleted successfully';
                return redirect('recycle-purchases')->with('success', $success);
            } else {
                return redirect('recycle-purchases')->with('info', 'No Recycle Purchases selected to Delete');
            }
        } else {
            return redirect('unauthorized');
        }
    }

    //delMultipleRecycleExpense

    public function delMultipleRecycleExpense(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));

        if (Auth::user()->hasRole('manager')) {
            if (!is_null($request->input('id'))) {
                foreach ($request->id as $key => $pid) {
                    $decrypted = Crypt::decrypt($pid);
                    $purchase = Expense::where('id', $decrypted)->where('shop_id', $shop->id)->first();
                    $purchase->delete();
                }
                $success = 'Expenses were deleted successfully';
                return redirect('recycle-expenses')->with('success', $success);
            } else {
                return redirect('recycle-expenses')->with('info', 'No Recycle Purchases selected to Delete');
            }
        } else {
            return redirect('unauthorized');
        }
    }

    //Recycle Purchases
    public function recyclePurchase($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->hasRole('manager')) {

            $purchase = Purchase::where('id', Crypt::decrypt($id))->where('shop_id', $shop->id)->first();
            if (!is_null($purchase)) {
                $pitems = Stock::where('time_created', $purchase->time_created)->where('shop_id', $shop->id)->get();

                foreach ($pitems as $key => $value) {
                    $value->is_deleted = false;
                    $value->del_by = Auth::user()->first_name . '(' . Carbon::now() . ')';
                    $value->save();
                    // $value->delete();
                    $product = Product::find($value->product_id);
                    $shop_product = $shop->products()->where('product_id', $product->id)->first();

                    $stock_in = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                    $sold = AnSaleItem::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                    $damaged = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                    $tranfered =  TransferOrderItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                    $returned = SaleReturnItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');

                    $instock = ($stock_in + $returned) - ($sold + $damaged + $tranfered);
                    $shop_product->pivot->in_stock = $instock;
                    if ($shop_product->pivot->in_stock > $shop_product->pivot->reorder_point) {
                        $shop_product->pivot->status = 'In Stock';
                    } elseif ($shop_product->pivot->in_stock == 0) {
                        $shop_product->pivot->status = 'Out of Stock';
                    } elseif ($shop_product->pivot->in_stock <= $shop_product->pivot->reorder_point && $shop_product->pivot->in_stock != 0) {
                        $shop_product->pivot->status = 'Low Stock';
                    }

                    $shop_product->pivot->save();
                    $message = 'Stock was successfully deleted';
                }

                $payments = PurchasePayment::where('purchase_id', $purchase->id)->get();

                foreach ($payments as $key => $payment) {
                    $pv = PaymentVoucher::where('pv_no', $payment->pv_no)->first();
                    if (!is_null($pv)) {
                        $pv->delete();
                    }
                    $payment->is_deleted = false;
                    $payment->save();
                    // $payment->delete();
                }

                $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->first();
                if ($acctrans) {
                    $acctrans->is_deleted = false;
                    $acctrans->save();
                    // $acctrans->delete();
                }

                $purchase->is_deleted = false;
                $purchase->del_by = Auth::user()->first_name . ' (' . Carbon::now() . ')';
                $purchase->save();
                // $purchase->delete();
                return redirect()->back()->with('success', 'Purchase was restored successfully');
            }
        } else {
            return redirect('unauthorized');
        }
    }

    //Delete Purchases
    public function delRecyclePurchase($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->hasRole('manager')) {

            $purchase = Purchase::where('id', Crypt::decrypt($id))->where('shop_id', $shop->id)->first();
            if (!is_null($purchase)) {
                $pitems = Stock::where('time_created', $purchase->time_created)->where('shop_id', $shop->id)->get();

                foreach ($pitems as $key => $value) {
                    $value->delete();
                    $product = Product::find($value->product_id);
                    $shop_product = $shop->products()->where('product_id', $product->id)->first();

                    $stock_in = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                    $sold = AnSaleItem::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                    $damaged = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                    $tranfered =  TransferOrderItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                    $returned = SaleReturnItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');

                    $instock = ($stock_in + $returned) - ($sold + $damaged + $tranfered);
                    $shop_product->pivot->in_stock = $instock;
                    if ($shop_product->pivot->in_stock > $shop_product->pivot->reorder_point) {
                        $shop_product->pivot->status = 'In Stock';
                    } elseif ($shop_product->pivot->in_stock == 0) {
                        $shop_product->pivot->status = 'Out of Stock';
                    } elseif ($shop_product->pivot->in_stock <= $shop_product->pivot->reorder_point && $shop_product->pivot->in_stock != 0) {
                        $shop_product->pivot->status = 'Low Stock';
                    }

                    $shop_product->pivot->save();
                    $message = 'Stock was successfully deleted';
                }

                $payments = PurchasePayment::where('purchase_id', $purchase->id)->get();

                foreach ($payments as $key => $payment) {
                    $pv = PaymentVoucher::where('pv_no', $payment->pv_no)->first();
                    if (!is_null($pv)) {
                        if ($shop->subscription_type_id == 2) {

                            $acctrans = SupplierTransaction::where('pv_no', $purchase->pv_no)->where('shop_id', $shop->id)->first();
                            if (!is_null($acctrans)) {
                                $acctrans->delete();
                            }
                        } else {
                            $acctrans = SupplierAccount::where('pv_no', $purchase->pv_no)->where('shop_id', $shop->id)->first();
                            if (!is_null($acctrans)) {
                                $acctrans->delete();
                            }
                        }
                        $pv->delete();
                    }
                    $payment->delete();
                }

                $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->first();
                if ($acctrans) {
                    $acctrans->delete();
                }
                $purchase->delete();
                return redirect()->back()->with('success', 'Purchase was deleted successfully');
            }
        } else {
            return redirect('unauthorized');
        }
    }

    //Delete Expenses
    public function delRecycleExpense($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->hasRole('manager')) {

            $expense = Expense::where('id', Crypt::decrypt($id))->where('shop_id', $shop->id)->first();
            if (!is_null($expense)) {
                $pitems = Stock::where('time_created', $expense->time_created)->where('shop_id', $shop->id)->get();

                foreach ($pitems as $key => $value) {
                    $value->delete();
                    $product = Product::find($value->product_id);
                    $shop_product = $shop->products()->where('product_id', $product->id)->first();

                    $stock_in = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                    $sold = AnSaleItem::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                    $damaged = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                    $tranfered =  TransferOrderItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                    $returned = SaleReturnItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');

                    $instock = ($stock_in + $returned) - ($sold + $damaged + $tranfered);
                    $shop_product->pivot->in_stock = $instock;
                    if ($shop_product->pivot->in_stock > $shop_product->pivot->reorder_point) {
                        $shop_product->pivot->status = 'In Stock';
                    } elseif ($shop_product->pivot->in_stock == 0) {
                        $shop_product->pivot->status = 'Out of Stock';
                    } elseif ($shop_product->pivot->in_stock <= $shop_product->pivot->reorder_point && $shop_product->pivot->in_stock != 0) {
                        $shop_product->pivot->status = 'Low Stock';
                    }

                    $shop_product->pivot->save();
                    $message = 'Stock was successfully deleted';
                }

                $payments = PurchasePayment::where('purchase_id', $expense->id)->get();

                foreach ($payments as $key => $payment) {
                    $pv = PaymentVoucher::where('pv_no', $payment->pv_no)->first();

                    $payment->delete();
                }

                $expense->delete();
                return redirect()->back()->with('success', 'Expense was deleted successfully');
            }
        } else {
            return redirect('unauthorized');
        }
    }


    //Recycle Purchases
    public function recyclePurchases(Request $request)
    {
        $page = 'Recycle Bin';
        $title = 'Recycle Bin';
        $title_sw = 'Recycle Bin';
        $shop = Shop::find(Session::get('shop_id'));

        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;
        $end_date = null;

        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['sale_date'])) {
            $start_date = $request['sale_date'];
            $end_date = $request['sale_date'];
            $start = $request['sale_date'] . ' 00:00:00';
            $end = $request['sale_date'] . ' 23:59:59';
            $is_post_query = true;
        } else if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        } else {
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }
        $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('is_deleted', true)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.time_created as time_created', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.del_by as del_by', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'users.first_name as first_name')->orderBy('an_sales.time_created', 'desc')->get();
        $recycledPurchases = Purchase::where('shop_id', $shop->id)->where('is_deleted', true)->orderBy('purchases.time_created', 'desc')->paginate(20);
        return view('accounts.delete_purchases', compact('recycledPurchases', 'page', 'page', 'title', 'title_sw', 'sales', 'is_post_query', 'start_date', 'end_date'));
    }


    //Recycle Expenses
    public function recycleExpenses(Request $request)
    {
        $page = 'Recycle Bin';
        $title = 'Recycle Bin';
        $title_sw = 'Recycle Bin';
        $shop = Shop::find(Session::get('shop_id'));

        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;
        $end_date = null;

        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['exp_date'])) {
            $start_date = $request['exp_date'];
            $end_date = $request['exp_date'];
            $start = $request['exp_date'] . ' 00:00:00';
            $end = $request['exp_date'] . ' 23:59:59';
            $is_post_query = true;
        } else if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        } else {
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        if (!empty($request->expense)) {
            // $cus_id = Crypt::decrypt($request->customer);
            $expenseType = Crypt::decrypt($request->expense);
            $expenses =  Expense::where('shop_id', $shop->id)->where('is_deleted', true)->where('id', $expenseType)->whereBetween('time_created', [$start, $end])->join('users', 'users.id', '=', 'expenses.user_id')->select('users.first_name as first_name', 'expenses.id as id', 'expenses.supplier_id as supplier_id', 'expense_type as expenses_type', 'amount as amount', 'amount_paid as amount_paid', 'exp_vat as exp_vat', 'wht_rate as wht_rate', 'wht_amount as wht_amount', 'time_created as created_at', 'exp_type as exp_type', 'status as status', 'del_by as del_by', 'description as description')
                ->orderBy('time_created', 'desc')->paginate(20)->withQueryString();
        } else {
            $expenses =  Expense::where('shop_id', $shop->id)->where('is_deleted', true)->whereBetween('time_created', [$start, $end])->join('users', 'users.id', '=', 'expenses.user_id')->select('users.first_name as first_name', 'expenses.id as id', 'expenses.supplier_id as supplier_id', 'expense_type as expenses_type', 'amount as amount', 'amount_paid as amount_paid', 'exp_vat as exp_vat', 'wht_rate as wht_rate', 'wht_amount as wht_amount', 'time_created as created_at', 'exp_type as exp_type', 'status as status', 'del_by as del_by', 'description as description')
                ->orderBy('time_created', 'desc')->paginate(20)->withQueryString();
        }

        $expense_select =  Expense::where('shop_id', $shop->id)->where('is_deleted', true)->whereBetween('time_created', [$start, $end])->join('users', 'users.id', '=', 'expenses.user_id')->select('users.first_name as first_name', 'expenses.id as id', 'expenses.supplier_id as supplier_id', 'expense_type as expenses_type', 'amount as amount', 'amount_paid as amount_paid', 'exp_vat as exp_vat', 'wht_rate as wht_rate', 'wht_amount as wht_amount', 'time_created as created_at', 'exp_type as exp_type', 'status as status', 'del_by as del_by', 'description as description')
            ->orderBy('time_created', 'desc')->groupBy('expenses_type')->distinct()->get();
        return view('accounts.recycle-expenses', compact('expense_select', 'page', 'title', 'title_sw', 'expenses', 'is_post_query', 'start_date', 'end_date', 'page'));
    }

    //Restore Expenses
    public function recycleExpensesRestore($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->hasRole('manager')) {
            $expense = Expense::where('id',  Crypt::decrypt($id))->where('shop_id', $shop->id)->first();
            if (!is_null($expense)) {
                $expense->is_deleted = false;
                $expense->save();
                // dd($expense);
                $success = 'Your Expense was succesfuly Restored';
                return redirect()->back()->with('success', $success);
            }
        } else {
            return redirect('unauthorized');
        }
    }

    public function recycleItem($id)
    {
    }

    public function recycleStock($id)
    {
    }

    //Empty Sales
    public function emptyRecycleSales(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));

        if (Auth::user()->hasRole('manager')) {
            AnSale::where('an_sales.shop_id', $shop->id)->where('is_deleted', true)->delete();
            $success = 'Sales deleted permanently . . .';
            return redirect('recyclebin')->with('success', $success);
        } else {
            return redirect('unauthorized');
        }
    }

    //Empty Expenses
    public function emptyRecycleExpenses(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));

        if (Auth::user()->hasRole('manager')) {
            // Expense::where('an_sales.shop_id', $shop->id)->where('is_deleted', true)->delete();
            Expense::where('shop_id', $shop->id)->where('is_deleted', true)->delete();
            $success = 'Expenses deleted permanently . . .';
            return redirect('recycle-expenses')->with('success', $success);
        } else {
            return redirect('unauthorized');
        }
    }

    //Empty Purchases
    public function emptyRecyclePurchases(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));

        if (Auth::user()->hasRole('manager')) {
            $purchase = Purchase::where('shop_id', $shop->id)->where('is_deleted', true)->delete();




            if (!is_null($purchase)) {
                $pitems = Stock::where('shop_id', $shop->id)->get();

                foreach ($pitems as $key => $value) {
                    $value->delete();
                    $product = Product::find($value->product_id);
                    $shop_product = $shop->products()->where('product_id', $product->id)->delete();

                    $stock_in = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                    $sold = AnSaleItem::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                    $damaged = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                    $tranfered =  TransferOrderItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                    $returned = SaleReturnItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');

                    $instock = ($stock_in + $returned) - ($sold + $damaged + $tranfered);
                    $shop_product->pivot->in_stock = $instock;
                    if ($shop_product->pivot->in_stock > $shop_product->pivot->reorder_point) {
                        $shop_product->pivot->status = 'In Stock';
                    } elseif ($shop_product->pivot->in_stock == 0) {
                        $shop_product->pivot->status = 'Out of Stock';
                    } elseif ($shop_product->pivot->in_stock <= $shop_product->pivot->reorder_point && $shop_product->pivot->in_stock != 0) {
                        $shop_product->pivot->status = 'Low Stock';
                    }

                    $shop_product->pivot->save();
                    $message = 'Stock was successfully deleted';
                }
                $success = 'Purchases deleted permanently . . .';
                return redirect('delete_purchases')->with('success', $success);
            } else {
                return redirect('unauthorized');
            }
        }
    }



    public function deleteAndRestoreMultipleSales(Request $request)
    {
        // dd($request);
        switch ($request->input('action')) {
            case 'delete': {
                    $shop = Shop::find(Session::get('shop_id'));
            
                    // return $request->input('id');
                    if (Auth::user()->hasRole('manager')) {
                        foreach ($request->input('id') as $key => $id) {
                            $sale = AnSale::where('id', $id)->where('shop_id', $shop->id)->first();
                            // return $id;
            
                            if (!is_null($sale)) {
                                $items = AnSaleItem::where('an_sale_id', $sale->id)->get();
                                if (!is_null($items)) {
                                    foreach ($items as $key => $item) {
                                        
                                        $shop_product = $shop->products()->where('product_id', $item->product_id)->first();
                                        // $item->is_deleted = true;
                                        $item->delete();
                                        $item->save();
                                        
                                        if (!is_null($shop_product)) {
                                             
                                            $stock_in = Stock::where('product_id', $shop_product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                                            $stock_out = AnSaleItem::where('product_id', $shop_product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                                            $damaged = ProdDamage::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                                            $tranfered =  TransferOrderItem::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                                            $returned = SaleReturnItem::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                                                        
                                            $instock = ($stock_in+$returned)-($stock_out+$damaged+$tranfered);
                                            $shop_product->pivot->in_stock = $instock;
                                                
                                            $shop_product->pivot->save();
                                        }
                                    }
                                }
            
                                $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->get();
                                if (!is_null($servitems)) {
                                    foreach ($servitems as $key => $sitem) {
                                        // $sitem->is_deleted  = true;
                                        $sitem->delete();
                                        $sitem->save();
                                        
                                    }
                                }
            
                                $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                                if (!is_null($payments)) {
                                    foreach ($payments as $key => $payment) {
                                        // $payment->is_deleted = true;
                                        $payment->delete();
                                        $payment->save();
                                        
                                    }
                                }
            
                                $invoice = Invoice::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                                if (!is_null($invoice)) {
                                    $acctrans = CustomerTransaction::where('invoice_no', $invoice->inv_no)->where('shop_id', $shop->id)->first();
                                    if (!is_null($acctrans)) {
                                        // $acctrans->is_deleted = true;
                                        $acctrans->delete();
                                        $acctrans->save();
                                        
                                    }
            
                                    // $invoice->is_deleted = true;
                                    $invoice->delete();
                                    $invoice->save();

                                }
                                
                                // $sale->is_deleted = true;
                                $sale->delete();
                                $sale->save();
            
                            }
                        }
                        
                        $success = 'Sales were deleted successfully';
                        return redirect('recyclebin')->with('info', 'No Recycle Sales selected to Delete');
                        
                    }else{
                        return redirect('unauthorized');
                    }
                }
                    // $shop = Shop::find(Session::get('shop_id'));

                    // if (Auth::user()->hasRole('manager')) {
                    //     if (!is_null($request->input('id'))) {
                    //         foreach ($request->id as $key => $sid) {
                    //             $decrypted = Crypt::decrypt($sid);
                    //             $sale = AnSale::where('id', $decrypted)->where('shop_id', $shop->id)->first();
                    //             $sale->delete();
                    //         }
                    //         $success = 'Sales were deleted successfully';
                    //         return redirect('recyclebin')->with('success', $success);
                    //     } else {
                    //         return redirect('recyclebin')->with('info', 'No Recycle Sales selected to Delete');
                    //     }
                    // } else {
                    //     return redirect('unauthorized');
                    // }
                

                break;

            case 'restore': {
                    $shop = Shop::find(Session::get('shop_id'));
                    if (Auth::user()->hasRole('manager')) {
                        // Log::info($request->id);
                        if (!is_null($request->input('id'))) {
                            foreach ($request->id as $id) {
                                $sale = AnSale::where('id', Crypt::decrypt($id))->where('shop_id', $shop->id)->first();
                                if (is_null($sale)) {
                                    return redirect('forbiden');
                                } else {
                                    $items = AnSaleItem::where('an_sale_id', $sale->id)->get();
                                    if (!is_null($items)) {
                                        foreach ($items as $key => $item) {
                                            $shop_product = $shop->products()->where('product_id', $item->product_id)->first();

                                            $item->is_deleted = false;
                                            $item->del_by = null;
                                            $item->save();
                                            // $item->delete();
                                            if (!is_null($shop_product)) {

                                                $stock_in = Stock::where('product_id', $shop_product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                                                $stock_out = AnSaleItem::where('product_id', $shop_product->pivot->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                                                $damaged = ProdDamage::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                                                $tranfered =  TransferOrderItem::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                                                $returned = SaleReturnItem::where('product_id', $shop_product->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');

                                                $instock = ($stock_in + $returned) - ($stock_out + $damaged + $tranfered);
                                                $shop_product->pivot->in_stock = $instock;

                                                $shop_product->pivot->save();
                                            }
                                        }
                                    }

                                    $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->first();
                                    if (!is_null($servitems)) {
                                        foreach ($servitems as $key => $sitem) {
                                            $sitem->is_deleted = false;
                                            $sitem->del_by = null;
                                            $sitem->save();
                                            // $sitem->delete();
                                        }
                                    }

                                    $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                                    if (!is_null($payments)) {
                                        foreach ($payments as $key => $payment) {
                                            $payment->is_deleted = false;
                                            $payment->save();
                                            // $payment->delete();
                                        }
                                    }

                                    $invoice = Invoice::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                                    if (!is_null($invoice)) {
                                        $acctrans = CustomerTransaction::where('invoice_no', $invoice->inv_no)->where('shop_id', $shop->id)->first();
                                        if (!is_null($acctrans)) {
                                            $acctrans->is_deleted = false;
                                            $acctrans->save();
                                            // $acctrans->delete();
                                        }
                                        $invoice->is_deleted = false;
                                        $invoice->save();
                                        // $invoice->delete();
                                    }

                                    // $acctrans = CustomerAccount::where('sale_no', $sale->sale_no)->where('shop_id', $shop->id)->first();
                                    // if (!is_null($acctrans)) {
                                    //     $acctrans->delete();
                                    // }

                                    $sale->is_deleted = false;
                                    $sale->del_by = null;
                                    $sale->save();
                                    // $sale->delete();

                                    $success = 'Your sale was succesfuly Restored';
                                    return redirect()->back()->with('success', $success);
                                }
                            }
                        } else {
                            return redirect('recyclebin')->with('info', 'No Recycle Sales selected to Delete');
                        }
                    } else {
                        return redirect('unauthorized');
                    }
                }



                break;
        }
    }
}
