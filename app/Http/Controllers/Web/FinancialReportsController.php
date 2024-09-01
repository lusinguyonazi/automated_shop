<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use DB;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\Category;
use App\Models\AnSale;
use App\Models\SaleReturn;
use App\Models\TransferOrderItem;
use App\Models\Expense;
use App\Models\CashIn;
use App\Models\CashOut;
use App\Models\Purchase;
use App\Models\AccountTransaction;
use App\Models\OCAmount;
use App\Models\Setting;
use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\SupplierTransaction;
use App\Models\ExpensePayment;
use App\Models\BusinessValue;
use App\Models\SalePayment;

class FinancialReportsController extends Controller
{
    public function BusinessValue(Request $request)
    {
        $page = 'Reports';
        $title = 'Business value Reports';
        $title_sw = 'Ripoti ya Thamani ya Biashara';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        $now = Carbon::now();
        $start_date = null;            
        $end_date = null;
      
        //check if user opted for date range
        $is_post_query = false;
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
    
        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        $balances =  $this->accountsBalance($shop);

        $total_balance = $balances['cashBal']+$balances['mobiBal']+$balances['bankBal'];
        // Business value
        // Assets
        $cash_in_hand = $total_balance;
        $account_receivable = 0;
        $inventory = 0;
        $total_ob = 0;
        $total_invoices = 0;
        $supp_debtor = 0;
        $other_loan = 0;

        $customers = Customer::where('shop_id', $shop->id)->get();
        foreach ($customers as $key => $customer) {
            $obtrans = CustomerTransaction::where('customer_id', $customer->id)->where('invoice_no', 'OB')->where('shop_id', $shop->id)->first();
            $opening_balance = 0;
            if (!is_null($obtrans)) {
                $opening_balance = $obtrans->amount-$obtrans->ob_paid;
            }

            $totalsales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->where('customer_id', $customer->id)->get([
              DB::raw('SUM(sale_amount) as sale_amount'),
              DB::raw('SUM(sale_discount) as sale_discount'),
              DB::raw('SUM(adjustment) as adjustment'),
              DB::raw('SUM(sale_amount_paid) as amount_paid')
            ]);
        
            $new_invoices = 0;
            foreach ($totalsales as $key => $value) {
                $new_invoices += $value->sale_amount-($value->sale_discount+$value->adjustment+$value->amount_paid);
            }

            $account_receivable += ($opening_balance+$new_invoices);
        }

        $products = $shop->products()->select('id as id', 'name as name', 'basic_unit as basic_unit', 'product_shop.time_created as created_at', 'product_shop.in_stock as in_stock', 'product_shop.price_per_unit as price_per_unit',  'product_shop.wholesale_price as wholesale_price',  'product_shop.buying_per_unit as buying_per_unit')->get();

        foreach ($products as $key => $product) {
            $inventory += ($product->in_stock*$product->price_per_unit);
        }

        $supptrans = SupplierTransaction::where('shop_id', $shop->id)->where('is_deleted', false)->groupBy('supplier_id')->get([
            \DB::raw('supplier_id as supplier_id'),
            \DB::raw('SUM(amount) as amount'),
            \DB::raw('SUM(payment) as payment'),
            \DB::raw('SUM(adjustment) as adjustment')
        ]);

        foreach ($supptrans as $key => $trans) {
            $suppbal = $trans->amount-($trans->payment+$trans->adjustment);
            if ($suppbal < 0) {
                $supp_debtor += -($suppbal);
            }
        }

        // Liabilities
        $account_payable = 0;
        $total_sup_ob = 0;
        $total_sup_invoices = 0;
        $supplier_payable = 0;
        $cust_creditor = 0;
        $other_credits = 0;

        $suppliers = $shop->suppliers()->get();
        foreach ($suppliers as $key => $supplier) {
            $supobtrans = SupplierTransaction::where('supplier_id', $supplier->id)->where('invoice_no', 'OB')->where('shop_id', $shop->id)->first();
            $supopening_balance = 0;
            if (!is_null($supobtrans)) {
                $supopening_balance = $supobtrans->amount-$supobtrans->ob_paid;
            }

            $totalpurchases = Purchase::where('shop_id', $shop->id)->where('is_deleted', false)->where('supplier_id', $supplier->id)->get([
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('SUM(amount_paid) as amount_paid')
            ]);
            $new_sup_invoices = 0;
            foreach ($totalpurchases as $key => $value) {
                $new_sup_invoices += $value->total_amount-$value->amount_paid;
            }

            $supplier_payable += ($supopening_balance+$new_sup_invoices);
        }

        $unpaidexp = 0;
        $pexpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('status', 'Pending')->get();

        foreach ($pexpenses as $key => $expense) {
            $unpaidexp += ($expense->amount-$expense->amount_paid);
        }

        $custtrans = CustomerTransaction::where('shop_id', $shop->id)->where('is_deleted', false)->groupBy('customer_id')->get([
            \DB::raw('customer_id as customer_id'),
            \DB::raw('SUM(amount) as amount'),
            \DB::raw('SUM(payment) as payment'),
            \DB::raw('SUM(adjustment) as adjustment')
        ]);
      
        foreach ($custtrans as $key => $trans) {
            $custbal = $trans->amount-($trans->payment+$trans->adjustment);
            if ($custbal < 0) {
                $cust_creditor += -($custbal);
            }
        }

        $account_payable = $supplier_payable+$unpaidexp+$cust_creditor+$other_credits;


        $total_assets = $cash_in_hand+$account_receivable+$inventory+$supp_debtor+$other_loan;
         $owners_equity = $total_assets-$account_payable;
        // return $owners_equity;

        $discounts_made = AnSale::where('shop_id', $shop->id)->whereBetween('time_created', [$start, $end])->where('is_deleted', false)->sum('sale_discount');
        $paid_expenses = ExpensePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->sum('amount');

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.financial.business-value',  compact('page', 'title', 'title_sw', 'is_post_query', 'shop', 'settings', 'crtime', 'reporttime','cash_in_hand', 'inventory', 'account_receivable', 'supp_debtor', 'other_loan', 'total_assets', 'supplier_payable', 'cust_creditor', 'unpaidexp', 'other_credits', 'account_payable', 'paid_expenses', 'discounts_made', 'start_date', 'end_date','duration', 'duration_sw' ));
    }

    public function CashFlowStatement(Request $request)
    {
        $page = 'Reports';
        $title = 'Cash Flow Statement';
        $title_sw = 'Ripoti ya Mzunguuko wa Cashi';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;            
        $end_date = null;
      
        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfDay();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        //Cash flow Stment starts here
        $salescashins = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->whereBetween('sale_payments.pay_date', [$start, $end])->where('pay_mode', 'Cash')->sum('amount');

        $salesbankins = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->whereBetween('sale_payments.pay_date', [$start, $end])->where('pay_mode', 'Bank')->sum('amount');
      
        $salesmobiins = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->whereBetween('sale_payments.pay_date', [$start, $end])->where('pay_mode', 'Mobile Money')->sum('amount');

        $cashin = CashIn::where('shop_id', $shop->id)->whereBetween('in_date', [$start, $end])->where('account', 'Cash')->sum('amount');

        $bankin = CashIn::where('shop_id', $shop->id)->whereBetween('in_date', [$start, $end])->where('account', 'Bank')->sum('amount');

        $mobiin = CashIn::where('shop_id', $shop->id)->whereBetween('in_date', [$start, $end])->where('account', 'Mobile Money')->sum('amount');

        $cashins = array(
            ['account' => 'Cash', 'amount' => ($salescashins+$cashin)],
            ['account' => 'Bank', 'amount' => ($salesbankins+$bankin)],
            ['account' => 'Mobile Money', 'amount' => ($salesmobiins+$mobiin)]
        );

        //Cashouts 
        $cashout = CashOut::where('shop_id', $shop->id)->whereBetween('out_date', [$start, $end])->where('account', 'Cash')->sum('amount');

        $bankout = CashOut::where('shop_id', $shop->id)->whereBetween('out_date', [$start, $end])->where('account', 'Bank')->sum('amount');

        $mobiout = CashOut::where('shop_id', $shop->id)->whereBetween('out_date', [$start, $end])->where('account', 'Mobile Money')->sum('amount');

        $cashppay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->whereBetween('purchase_payments.pay_date', [$start, $end])->where('pay_mode', 'Cash')->sum('amount');

        $bankppay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->whereBetween('purchase_payments.pay_date', [$start, $end])->where('pay_mode', 'Bank')->sum('amount');

        $mobippay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->whereBetween('purchase_payments.pay_date', [$start, $end])->where('pay_mode', 'Mobile Money')->sum('amount');

        $cashexp = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->whereBetween('expense_payments.pay_date', [$start, $end])->where('expense_payments.account', 'Cash')->sum('expense_payments.amount');

        $bankexp = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->whereBetween('expense_payments.pay_date', [$start, $end])->where('expense_payments.account', 'Bank')->sum('expense_payments.amount');

        $mobiexp = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->whereBetween('expense_payments.pay_date', [$start, $end])->where('expense_payments.account', 'Mobile Money')->sum('expense_payments.amount');

        $cashouts = array(
            ['account' => 'Cash', 'amount' => ($cashout+$cashppay+$cashexp)],
            ['account' => 'Bank', 'amount' => ($bankout+$bankppay+$bankexp)],
            ['account' => 'Mobile Money', 'amount' => ($mobiout+$mobippay+$mobiexp)]
        );
      
        $sales_cashin = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->whereBetween('sale_payments.pay_date', [$start, $end])->sum('amount');
        $other_cashin = CashIn::where('shop_id', $shop->id)->whereBetween('in_date', [$start, $end])->sum('amount');
      
        $total_cashin = $sales_cashin+$other_cashin;

        $tcashout = (($cashout+$cashppay+$cashexp)+($mobiout+$mobippay+$mobiexp)+($bankout+$bankppay+$bankexp));

        $total_cashout = $tcashout;

        $balances =  $this->accountsBalance($shop);

        $total_balance = $balances['cashBal']+$balances['mobiBal']+$balances['bankBal'];

        $cashin_outs = array(
            ['account' => 'Cash', 'amount'=> $balances['cashBal']],
            ['account' => 'Mobile Money', 'amount' => $balances['mobiBal']],
            ['account' => 'Bank', 'amount' => $balances['bankBal']]
        );
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.financial.cash-flow-statement',  compact('page', 'title', 'title_sw', 'is_post_query', 'shop', 'settings', 'reporttime', 'start_date', 'end_date','duration', 'duration_sw', 'cashins','total_cashin','cashouts','total_cashout', 'cashin_outs','total_balance'  ));
    }

    // Account Balances
    public function accountsBalance($shop)
    {
        
        // Accounts balances;
        $cashAccin = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->where('pay_mode', 'Cash')->sum('amount');

        $mobileAccin = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->where('pay_mode', 'Mobile Money')->sum('amount');
      
        $bankAccin = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->where('pay_mode', 'Bank')->sum('amount');

        $totalcashin = CashIn::where('shop_id', $shop->id)->where('account', 'Cash')->sum('amount');

        $totalbankin = CashIn::where('shop_id', $shop->id)->where('account', 'Bank')->sum('amount');

        $totalmobiin = CashIn::where('shop_id', $shop->id)->where('account', 'Mobile Money')->sum('amount');

        $totalcashout = CashOut::where('shop_id', $shop->id)->where('account', 'Cash')->sum('amount');

        $totalbankout = CashOut::where('shop_id', $shop->id)->where('account', 'Bank')->sum('amount');

        $totalmobiout = CashOut::where('shop_id', $shop->id)->where('account', 'Mobile Money')->sum('amount');

        $totalcashppay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('pay_mode', 'Cash')->sum('amount');

        $totalbankppay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('pay_mode', 'Bank')->sum('amount');

        $totalmobippay = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', false)->join('purchase_payments', 'purchase_payments.purchase_id', '=', 'purchases.id')->where('pay_mode', 'Mobile Money')->sum('amount');

        $totalcashexp = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->where('expense_payments.account', 'Cash')->sum('expense_payments.amount');

        $totalbankexp = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->where('expense_payments.account', 'Bank')->sum('expense_payments.amount');

        $totalmobiexp = Expense::where('expenses.shop_id', $shop->id)->where('expenses.is_deleted', false)->join('expense_payments', 'expense_payments.expense_id', '=', 'expenses.id')->where('expense_payments.account', 'Mobile Money')->sum('expense_payments.amount');

        $cash_to_bank = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Cash')->where('to', 'Bank')->sum('amount');
        $cash_to_mobile = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Cash')->where('to', 'Mobile Money')->sum('amount');

        $mobile_to_bank = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Mobile Money')->where('to', 'Bank')->sum('amount');
        $mobile_to_cash = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Mobile Money')->where('to', 'Cash')->sum('amount');

        $bank_to_cash = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Bank')->where('to', 'Cash')->sum('amount');
        $bank_to_mobile = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Bank')->where('to', 'Mobile Money')->sum('amount');
      
        $cashBal = ($totalcashin+$cashAccin+$bank_to_cash+$mobile_to_cash)- ($totalcashout+$totalcashppay+$totalcashexp+$cash_to_bank+$cash_to_mobile);

        $mobiBal = ($totalmobiin+$mobileAccin+$cash_to_mobile+$bank_to_mobile)-($totalmobiout+$totalmobippay+$totalmobiexp+$mobile_to_cash+$mobile_to_bank);
      
        $bankBal = ($totalbankin+$bankAccin+$cash_to_bank+$mobile_to_bank)-($totalbankout+$totalbankppay+$totalbankexp+$bank_to_cash+$bank_to_mobile);

        return array('cashBal' => $cashBal, 'mobiBal' => $mobiBal, 'bankBal' => $bankBal);
    }

    public function DailyCashFlowStatement(Request $request)
    { 
        $page = 'Reports';
        $title = 'Daily Cash Flow Statement';
        $title_sw = 'Ripoti ya Mzunguuko wa Cashi kilasiku';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;            
        $end_date = null;
      
        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfDay();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }
        
        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        // Business Review
        $cash_pay = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start,$end])->where('pay_mode', 'Cash')->sum('amount')+CashIn::where('shop_id', $shop->id)->whereBetween('in_date', [$start, $end])->where('account', 'Cash')->sum('amount');
        $mob_pay = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start,$end])->where('pay_mode', 'Mobile Money')->sum('amount')+CashIn::where('shop_id', $shop->id)->whereBetween('in_date', [$start, $end])->where('account', 'Mobile Money')->sum('amount'); 
        $bank_pay = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start,$end])->where('pay_mode', 'Bank')->sum('amount')+CashIn::where('shop_id', $shop->id)->whereBetween('in_date', [$start, $end])->where('account', 'Bank')->sum('amount');

        $total_pay = $cash_pay+$mob_pay+$bank_pay;

        $c_to_b = AccountTransaction::where('shop_id', $shop->id)->where('from', 'Cash')->where('to', 'Bank')->whereBetween('date', [$start, $end])->sum('amount');
      
        $couts = CashOut::where('shop_id', $shop->id)->where('account', 'Cash')->whereBetween('out_date', [$start, $end])->get();
        $cash_out = CashOut::where('shop_id', $shop->id)->where('account', 'Cash')->whereBetween('out_date', [$start, $end])->sum('amount');

        $paid_expenses = ExpensePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->sum('amount');

        $dexpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->groupBy('expense_type')->orderBy('expense_type', 'asc')->get([
            \DB::raw('expense_type as expense_type'),
            \DB::raw('SUM(amount) as amount'),
            \DB::raw('SUM(exp_vat) as exp_vat')
        ]);            

        $balances =  $this->accountsBalance($shop);
        $cashBal = $balances['cashBal'];

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
       return view('reports.financial.daily-cash-flow-statement',  compact('page', 'title', 'title_sw', 'is_post_query', 'settings', 'shop', 'reporttime', 'start_date', 'end_date','duration', 'duration_sw', 'cash_pay', 'mob_pay','bank_pay', 'total_pay', 'c_to_b', 'cash_out', 'paid_expenses', 'cashBal', 'dexpenses', 'couts'  ));
        
    }

    public function IncomeStatement(Request $request)
    {
        $page = 'Reports';
        $title = 'Income Statement';
        $title_sw = 'Ripoti ya Kipato';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;            
        $end_date = null;
      
        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfDay();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        $total_sales = 0;
        $total_co_sales = 0;
        $total_expense = 0;
        $shared_expenses = 0;

        $categories = Category::where('shop_id', $shop->id)->get();
        $category = Category::find($request['category_id']);

        if ($shop->business_type_id == 1 || $shop->business_type_id == 2) {
            if (!is_null($category)) {
                $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->get([
                    \DB::raw('SUM(price) as price'),
                    \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
                    \DB::raw('SUM(total_discount) as discount'),
                    \DB::raw('SUM(buying_price) as buying_price'),
                    \DB::raw('SUM(an_sale_items.input_tax) as input_vat'),
                    \DB::raw('SUM(an_sale_items.tax_amount) as output_vat')
                ])->first();

                $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->join('products', 'sale_return_items.product_id', '=', 'products.id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->get([
                    \DB::raw('SUM(price) as return_price'),
                    \DB::raw('SUM(total_discount) as return_discount'),
                    \DB::raw('SUM(buying_price) as return_buying_price')
                ])->first();

                $transfers = TransferOrderItem::where('shop_id', $shop->id)->whereBetween('transfer_order_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'transfer_order_items.product_id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->get();

                $tqty = 0; $tsexpense = 0; $tdexpense = 0; $total = 0;
                foreach ($transfers as $key => $transfer) {
                    $tqty += $transfer->quantity; 
                    $tsexpense += $transfer->source_unit_expense; 
                    $tdexpense += $transfer->destin_unit_expense; 
                    $tprofit = 0; 
                    $total += (($transfer->destin_unit_expense-$transfer->source_unit_expense)*$transfer->quantity);
                }

                $total_sales = (($sales->price+$sales->tax_amount-$sales->discount)-($returns->return_price-$returns->return_discount))+$tdexpense;
                $total_co_sales = ($sales->buying_price-$returns->return_buying_price)+$tsexpense;
            }else{
                $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->get([
                    \DB::raw('SUM(price) as price'),
                    \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
                    \DB::raw('SUM(an_sale_items.total_discount) as discount'),
                    \DB::raw('SUM(buying_price) as buying_price'),
                    \DB::raw('SUM(an_sale_items.input_tax) as input_vat'),
                    \DB::raw('SUM(an_sale_items.tax_amount) as output_vat')
                ])->first();

                $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->get([
                    \DB::raw('SUM(price) as return_price'),
                    \DB::raw('SUM(total_discount) as return_discount'),
                    \DB::raw('SUM(buying_price) as return_buying_price')
                ])->first();
            
                $transfers = TransferOrderItem::where('shop_id', $shop->id)->whereBetween('transfer_order_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'transfer_order_items.product_id')->get();

                $tqty = 0; $tsexpense = 0; $tdexpense = 0; $total = 0;
                foreach ($transfers as $key => $transfer) {
                    $tqty += $transfer->quantity; 
                    $tsexpense += $transfer->source_unit_expense; 
                    $tdexpense += $transfer->destin_unit_expense; 
                    $tprofit = 0; 
                    $total += (($transfer->destin_unit_expense-$transfer->source_unit_expense)*$transfer->quantity);
                }

                $total_sales = (($sales->price+$sales->tax_amount-$sales->discount)-($returns->return_price-$returns->return_discount))+$tdexpense;
                $total_co_sales = ($sales->buying_price-$returns->return_buying_price)+$tsexpense;
            }
        }elseif ($shop->business_type_id == 3) {
            $servsales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->get([
                \DB::raw('SUM(total) as price'),
                \DB::raw('SUM(total_discount) as discount'),
                \DB::raw('SUM(service_sale_items.tax_amount) as tax_amount'),
                \DB::raw('DATE(an_sales.time_created) as date')
            ])->first();
          
            $total_sales = $servsales->price+$servsales->tax_amount-$servsales->discount;
        }else{
            $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->get([
                \DB::raw('SUM(price) as price'),
                \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
                \DB::raw('SUM(total_discount) as discount'),
                \DB::raw('SUM(buying_price) as buying_price'),
                \DB::raw('SUM(an_sale_items.input_tax) as input_vat'),
                \DB::raw('SUM(an_sale_items.tax_amount) as output_vat')
            ])->first();

            $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->get([
                \DB::raw('SUM(price) as return_price'),
                \DB::raw('SUM(total_discount) as return_discount'),
                \DB::raw('SUM(buying_price) as return_buying_price')
            ])->first();

            $transfers = TransferOrderItem::where('shop_id', $shop->id)->whereBetween('transfer_order_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'transfer_order_items.product_id')->get();
            $tqty = 0; $tsexpense = 0; $tdexpense = 0; $total = 0;
            foreach ($transfers as $key => $transfer) {
                $tqty += $transfer->quantity; 
                $tsexpense += $transfer->source_unit_expense; 
                $tdexpense += $transfer->destin_unit_expense; 
                $tprofit = 0; 
                $total += (($transfer->destin_unit_expense-$transfer->source_unit_expense)*$transfer->quantity);
            }

            $servsales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->get([
                \DB::raw('SUM(total) as price'),
                \DB::raw('SUM(total_discount) as discount'),
                \DB::raw('SUM(service_sale_items.tax_amount) as tax_amount'),
                \DB::raw('DATE(an_sales.time_created) as date')
            ])->first();


            $total_sales = (($sales->price+$sales->tax_amount-$sales->discount)-($returns->return_price-$returns->return_discount))+($servsales->price+$servsales->tax_amount-$servsales->discount)+$tdexpense;
            $total_co_sales = ($sales->buying_price-$returns->return_buying_price)+$tsexpense;     
        }

        $expenses = null;
        $ctnexpenses = null;
        $all_expenses = null;
        $all_ctnexpenses = null;
        if (!is_null($category)) {
            $ctnexpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('expire_at', '>', $start)->where('time_created', '<', $start)->where('category_id', $category->id)->orderBy('expense_type', 'asc')->get([
                \DB::raw('expense_type as expense_type'),
                \DB::raw('amount as amount'),
                \DB::raw('no_days as no_days'),
                \DB::raw('exp_vat as exp_vat'),
                \DB::raw('time_created as time_created'),
                \DB::raw('expire_at as expire_at')
            ]);

            $expenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->where('category_id', $category->id)->orderBy('expense_type', 'asc')->get([
                \DB::raw('expense_type as expense_type'),
                \DB::raw('amount as amount'),
                \DB::raw('no_days as no_days'),
                \DB::raw('exp_vat as exp_vat'),
                \DB::raw('time_created as time_created'),
                \DB::raw('expire_at as expire_at')
            ]);

            $all_ctnexpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('expire_at', '>', $start)->where('time_created', '<', $start)->where('category_id', null)->orderBy('expense_type', 'asc')->get([
                \DB::raw('expense_type as expense_type'),
                \DB::raw('amount as amount'),
                \DB::raw('no_days as no_days'),
                \DB::raw('exp_vat as exp_vat'),
                \DB::raw('time_created as time_created'),
                \DB::raw('expire_at as expire_at')
            ]);

            $all_expenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->where('category_id', null)->orderBy('expense_type', 'asc')->get([
                \DB::raw('expense_type as expense_type'),
                \DB::raw('amount as amount'),
                \DB::raw('no_days as no_days'),
                \DB::raw('exp_vat as exp_vat'),
                \DB::raw('time_created as time_created'),
                \DB::raw('expire_at as expire_at')
            ]);
          
        }else{
            $ctnexpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('expire_at', '>', $start)->where('time_created', '<', $start)->orderBy('expense_type', 'asc')->get([
                \DB::raw('expense_type as expense_type'),
                \DB::raw('amount as amount'),
                \DB::raw('no_days as no_days'),
                \DB::raw('exp_vat as exp_vat'),
                \DB::raw('time_created as time_created'),
                \DB::raw('expire_at as expire_at')
            ]);

            $expenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->orderBy('expense_type', 'asc')->get([
                \DB::raw('expense_type as expense_type'),
                \DB::raw('amount as amount'),
                \DB::raw('no_days as no_days'),
                \DB::raw('exp_vat as exp_vat'),
                \DB::raw('time_created as time_created'),
                \DB::raw('expire_at as expire_at')
            ]);

            $dexpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->groupBy('expense_type')->orderBy('expense_type', 'asc')->get([
                \DB::raw('expense_type as expense_type'),
                \DB::raw('SUM(amount) as amount'),
                \DB::raw('SUM(exp_vat) as exp_vat')
            ]);
        }

        $myexpenses = collect([]);
        $totalexpenses = 0;
        //All Continue Expenses
        if (!is_null($ctnexpenses)) {
            foreach ($ctnexpenses as $key => $expense) {
                $expdays = 0;
                if ($expense->expire_at > $end && $expense->time_created < $start) {
                    $expdays = \Carbon\Carbon::parse($end)->diffInDays(\Carbon\Carbon::parse($start))+1;
                }else{
                    $expdays = \Carbon\Carbon::parse($expense->expire_at)->diffInDays(\Carbon\Carbon::parse($start))+1;
                }
                $amount = (($expense->amount-$expense->exp_vat)/$expense->no_days)*$expdays;
                $totalexpenses += $amount;
                $myexpenses->push(['expense_type' => $expense->expense_type, 'amount' => $amount]);
            }
        }

        //Categorized continue expenses
        if (!is_null($all_ctnexpenses)) {  
            foreach ($all_ctnexpenses as $key => $expense) {
                $expdays = 0;
                if ($expense->expire_at > $end && $expense->time_created < $start) {
                    $expdays = \Carbon\Carbon::parse($end)->diffInDays(\Carbon\Carbon::parse($start))+1;
                }else{
                    $expdays = \Carbon\Carbon::parse($expense->expire_at)->diffInDays(\Carbon\Carbon::parse($start))+1;
                }
                $amount = ((($expense->amount-$expense->exp_vat)/$expense->no_days)*$expday)/$categories->count();
                $totalexpenses += $amount;
                $myexpenses->push(['expense_type' => $expense->expense_type, 'amount' => $amount]);
            }
        }

        //All Normal Expenses
        if (!is_null($expenses)) {  
            foreach($expenses as $key => $expense) {
                $amount = 0;
                if ($expense->no_days == 1) {
                    $amount = ($expense->amount-$expense->exp_vat);
                    $totalexpenses += $amount; 
                }else{
                    $expdays = 0;
                    if($expense->expire_at > $end){
                        $expdays= \Carbon\Carbon::parse($end)->diffInDays(\Carbon\Carbon::parse($expense->time_created))+1;
                    }else{
                        $expdays = $expense->no_days;
                    }
                    $amount = (($expense->amount-$expense->exp_vat)/$expense->no_days)*$expdays;
                    $totalexpenses += $amount;
                }
                $myexpenses->push(['expense_type' => $expense->expense_type, 'amount' => $amount]);
            }
        }

        //All Shared expenses in Categorized business
        if (!is_null($all_expenses)) {  
            foreach ($all_expenses as $key => $expense) {
                $amount = 0;
                if ($expense->no_days == 1) {
                    $amount = ($expense->amount-$expense->exp_vat)/$categories->count();
                    $totalexpenses += $amount; 
                }else{
                    $expdays = 0;
                    if($expense->expire_at > $end){
                        $expdays= \Carbon\Carbon::parse($end)->diffInDays(\Carbon\Carbon::parse($expense->time_created))+1;
                    }else{
                        $expdays = $expense->no_days;
                    }
                    $amount = ((($expense->amount-$expense->exp_vat)/$expense->no_days)*$expdays)/$categories->count();
                    $totalexpenses += $amount;
                }
                $myexpenses->push(['expense_type' => $expense->expense_type, 'amount' => $amount]);
            }
        }

        $groups = $myexpenses->groupby('expense_type');
        // we will use map to cumulate each group of rows into single row.
        // $group is a collection of rows that has the same opposition_id.
        $expenses = $groups->map(function ($group) {
            return [
                'expense_type' => $group->first()['expense_type'], // expense_type is constant inside the same group, so just take the first or whatever.
                'amount' => $group->sum('amount'),
            ];
        });

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.financial.income-statement',  compact('page', 'title', 'title_sw', 'is_post_query', 'settings', 'shop', 'reporttime', 'start_date', 'end_date','duration', 'duration_sw', 'total_sales', 'total_co_sales', 'expenses', 'totalexpenses'));
    }

    public function MonthyClosingBusinessValue(Request $request)
    {
        $page = 'Reports';
        $title = 'Monthy Closing Business Value';
        $title_sw = 'Ripoti ya Thamani ya Biashara ya Kufunga Mwezi';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;            
        $end_date = null;
      
        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfDay();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';


        $bvalues = BusinessValue::where('shop_id', $shop->id)->whereBetween('created_at', [$start,$end])->get();
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.financial.monthly-closing-business-value',  compact('page', 'title', 'title_sw', 'is_post_query', 'settings', 'shop', 'reporttime', 'start_date', 'end_date','duration', 'duration_sw', 'bvalues' ));
    }

    public function OpenClosingAmoutStatement(Request $request)
    {
        $page = 'Reports';
        $title = 'Open-Closing Amout Statement';
        $title_sw = 'Ripoti ya Kufunga na kufungua Biashara';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;            
        $end_date = null;
      
        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfDay();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        //Copening and Closing Amount for Agents
        $ocamounts = [];
        if ($settings->is_agent) {
            $ocashs = OCAmount::where('shop_id', $shop->id)->whereBetween('date', [$start, $end])->where('record_type', 'Opening')->groupBy('date')->where('amount_type', 'cash')->get([
                \DB::raw('date'),
                \DB::raw('SUM(amount) as ot_cash')
            ])->toArray();

            $ofloats = OCAmount::where('shop_id', $shop->id)->whereBetween('date', [$start, $end])->where('record_type', 'Opening')->groupBy('date')->where('amount_type', 'float')->get([
              \DB::raw('date'),
              \DB::raw('SUM(amount) as ot_float')
            ])->toArray();


            $ccashs = OCAmount::where('shop_id', $shop->id)->whereBetween('date', [$start, $end])->where('record_type', 'Closing')->groupBy('date')->where('amount_type', 'cash')->get([
              \DB::raw('date'),
              \DB::raw('SUM(amount) as ct_cash')
            ])->toArray();

            $cfloats = OCAmount::where('shop_id', $shop->id)->whereBetween('date', [$start, $end])->where('record_type', 'Closing')->groupBy('date')->where('amount_type', 'float')->get([
              \DB::raw('date'),
              \DB::raw('SUM(amount) as ct_float')
            ])->toArray();

            $data = array_merge($ocashs, $ofloats, $ccashs, $cfloats);

            foreach ($data as $item)  {
                if (!isset($ocamounts[$item['date']])) {
                    $ocamounts[$item['date']] = [];
                }

                if (!array_key_exists('ot_cash', $ocamounts[$item['date']])) {
                    $ocamounts[$item['date']]['ot_cash'] = 0;
                }

                if (!array_key_exists('ot_float', $ocamounts[$item['date']])) {
                    $ocamounts[$item['date']]['ot_float'] = 0;
                }

                if (!array_key_exists('ct_cash', $ocamounts[$item['date']])) {
                    $ocamounts[$item['date']]['ct_cash'] = 0;
                }

                if (!array_key_exists('ct_float', $ocamounts[$item['date']])) {
                    $ocamounts[$item['date']]['ct_float'] = 0;
                }

                foreach ($item as $key => $value) {
                    // if ($key == 'date') continue;
                    $ocamounts[$item['date']][$key] = $value;
                }
            }
        }

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.financial.open-closing-amount-statement',  compact('page', 'title', 'title_sw', 'is_post_query', 'settings', 'shop', 'reporttime', 'start_date', 'end_date','duration', 'duration_sw', 'ocamounts' ));
    }
}
