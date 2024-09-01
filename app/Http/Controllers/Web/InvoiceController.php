<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \DB;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\ServiceSaleItem;
use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\SalePayment;
use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\BankDetail;
use App\Models\SmsAccount;
use App\Models\SenderId;
use App\Models\SmsTemplate;
use App\Models\CashIn;
use App\Models\CashOut;

class InvoiceController extends Controller
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
    public function index(Request $request)
    {
        $page = 'Invoices';
        $title = 'Invoices';
        $title_sw = 'Ankara';
        

        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($shop)) {
            $settings = Setting::where('shop_id', $shop->id)->first();
            $now = Carbon::now();
                $start = null;
                $end = null;
                $start_date = null;            
                $end_date = null;

                $is_post_query = false;
                if (!is_null($request['exp_date'])) {
                    $start_date = $request['exp_date'];
                    $end_date = $request['exp_date'];
                    $start = $request['exp_date'].' 00:00:00';
                    $end = $request['exp_date'].' 23:59:59';
                    $is_post_query = true;
                } else if (!is_null($request['start_date'])) {
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
            $invoices = Invoice::where('invoices.shop_id', $shop->id)->where('invoices.is_deleted', false)->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.id as customer_id', 'customers.name as name', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.sale_amount_paid as sale_amount_paid', 'invoices.status as status', 'invoices.id as id', 'invoices.an_sale_id as an_sale_id', 'invoices.inv_no as inv_no', 'vehicle_no', 'invoices.due_date as due_date', 'invoices.created_at as created_at', 'invoices.updated_at as updated_at')->orderBy('invoices.created_at', 'desc')->get();

            $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('is_deleted', false)->whereBetween('an_sales.time_created', [$start_date, $end_date])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.adjustment as adjustment', 'an_sales.time_created as time_created', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.pay_type as pay_type', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'an_sales.sale_type as sale_type', 'an_sales.comments as comments', 'users.first_name as first_name', 'an_sales.grade_id as grade_id', 'an_sales.year as year')->orderBy('an_sales.time_created', 'desc')->get();
            // $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('is_deleted', false)->whereRaw('(an_sales.sale_amount-an_sales.sale_discount) > an_sales.sale_amount_paid')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id', 'customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.time_created as created_at')->orderBy('an_sales.time_created', 'desc')->get();
            // return $sales;

            $customer = null;
            $duration = '';
            $myinvoices = Invoice::where('shop_id', $shop->id)->get();

            foreach ($myinvoices as $key => $invoice) {
                if (is_null($invoice->bank_detail_id)) {
                    $bdetail = $shop->bankDetails()->first();
                    if (!is_null($bdetail)) {
                        $invoice->bank_detail_id = $bdetail->id;
                        $invoice->save();
                    }
                }
            }
            return view('sales.invoices.index', compact('page', 'title', 'title_sw', 'invoices', 'sales', 'customer', 'duration', 'settings', 'shop','start_date','end_date'));
        }else{
            return redirect('forbiden');
        }
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $page = 'Invoice';
        $title = 'Invoice Preview';
        $title_sw = 'Hakiki ya ankara';

        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $invoice = Invoice::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (is_null($invoice)) {
            return redirect('forbiden');
        }else{

            $bankdetail = BankDetail::where('id', $invoice->bank_detail_id)->first();
            $counti = Invoice::where('shop_id', $shop->id)->where('id', '<', $invoice->id)->count();
            $invoiceno = $counti+1;
            $sale = AnSale::where('an_sales.id', $invoice->an_sale_id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.cust_no as cust_no', 'customers.postal_address as po_address', 'customers.physical_address as ph_address', 'customers.street as street', 'customers.email as email', 'customers.phone as phone', 'customers.tin as tin', 'customers.vrn as vrn', 'an_sales.id as id', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.tax_amount as tax_amount', 'an_sales.currency as currency', 'an_sales.ex_rate as ex_rate')->first();
            $items = AnSaleItem::where('an_sale_id', $sale->id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->groupBy('name')->orderBy('an_sale_items.time_created', 'desc')->get([
                DB::raw('products.name as name'),
                DB::raw('an_sale_items.product_id as product_id'),
                DB::raw('an_sale_items.product_unit_id as product_unit_id'),
                DB::raw('SUM(an_sale_items.quantity_sold) as quantity_sold'),
                DB::raw('an_sale_items.price_per_unit as price_per_unit'),
                DB::raw('an_sale_items.discount as discount'),
                DB::raw('SUM(an_sale_items.price) as price'),
                DB::raw('SUM(an_sale_items.total_discount) as total_discount'),
                DB::raw('an_sale_items.tax_amount as tax_amount')
            ]);
            // return $items;
            $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->join('services', 'services.id', '=', 'service_sale_items.service_id')->groupBy('name')->orderBy('service_sale_items.time_created', 'desc')->get([
                DB::raw('services.name as name'),
                DB::raw('service_sale_items.service_id as service_id'),
                DB::raw('SUM(service_sale_items.no_of_repeatition) as quantity_sold'),
                DB::raw('service_sale_items.price as price'),
                DB::raw('SUM(service_sale_items.total) as total')
              ]);
            // return $servitems;
            $date = Carbon::now()->toDayDateTimeString();

            $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
            $stmtcurrencies = array($defcurr, $sale->currency);
            $stmtcurr = $sale->currency;
            if (!is_null($request['stmt_currency'])) {
                $stmtcurr = $request['stmt_currency'];
            }
            return view('sales.invoices.show', compact('page', 'title', 'title_sw', 'invoice', 'sale', 'items', 'servitems', 'shop', 'bankdetail', 'invoiceno', 'settings', 'date', 'defcurr', 'stmtcurrencies', 'stmtcurr'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Invoice';
        $title = 'Edit Invoice';   
        $title_sw = 'Hariri Ankara';

        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $invoice = Invoice::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        $bankdetail = BankDetail::where('id', $invoice->bank_detail_id)->first();
        $bdetails = $shop->bankDetails()->get();
        if (is_null($invoice)) {
            return redirect('forbiden');
        }else{
            $currsale = AnSale::where('an_sales.id', $invoice->an_sale_id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.phone as phone', 'customers.email as email', 'an_sales.id as id', 'an_sales.sale_amount as sale_amount', 'an_sales.time_created as created_at')->first();
            $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('is_deleted', false)->whereRaw('an_sales.sale_amount > an_sales.sale_amount_paid')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id', 'customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount as sale_amount', 'an_sales.time_created as created_at')->orderBy('an_sales.time_created', 'desc')->get();

            return view('sales.invoices.edit', compact('page', 'title', 'title_sw', 'invoice', 'sales', 'settings', 'currsale', 'shop', 'bankdetail', 'bdetails'));
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
        $invoice = Invoice::find(decrypt($id));
        $invoice->vehicle_no = $request['vehicle_no'];
        $invoice->due_date = $request['due_date'];
        $invoice->note = $request['note'];   
        $invoice->bank_detail_id = $request['bank_detail_id']; 
        $invoice->save();
        if (!is_null($request['inv_no'])) {
            $invoice->inv_no = $request['inv_no'];
            $invoice->save();
        }

        return redirect('invoices');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function accountStmt($id, Request $request)
    {
        $page = 'Account Statement';
        $title = 'Customer Account Statement';
        $title_sw = 'Taarifa ya Akaunti ya Mteja';

        $shop = Shop::find(Session::get('shop_id')); 
        $customer = Customer::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (!is_null($customer)) {
            
            $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
            $currencies = ShopCurrency::where('shop_id', $shop->id)->get();

            $invoices = Invoice::where('invoices.shop_id', $shop->id)->where('invoices.status', 'Pending')->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->where('an_sales.customer_id', $customer->id)->where('an_sales.is_deleted', false)->select('invoices.id as id', 'invoices.inv_no as inv_no')->get();
         
            $utransactions = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $customer->id)->whereNotNull('receipt_no')->where('is_utilized', false)->where('is_deleted', false)->get();
            if (!is_null($utransactions)) {
                foreach ($utransactions as $key => $trans) {
                    $rem_amount = $trans->payment-($trans->trans_invoice_amount+$trans->trans_ob_amount+$trans->trans_credit_amount);
                    if ($rem_amount > 0) {
                        $pinvoices = Invoice::where('invoices.shop_id', $shop->id)->where('invoices.status', 'Pending')->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->where('an_sales.customer_id', $customer->id)->get();
                        $curr_amount = $rem_amount;
                        foreach ($pinvoices as $key => $invoice) {
                            $sale = AnSale::find($invoice->an_sale_id);
                            $tunpaid = ($sale->sale_amount-($sale->sale_discount+$sale->adjustment+$sale->sale_amount_paid));
                            if ($curr_amount > 0) {
                                if ($curr_amount <= $tunpaid) {
                                    $amountpaid = $curr_amount;
                                    $this->clearOldInvoice($invoice, $amountpaid, $trans->payment_mode, $trans->bank_name, $trans->branch_name, $trans->cheque_no, $trans->date, $trans->receipt_no, $trans->currency, $trans->defcurr, $trans->ex_rate, '', $trans);
                                }elseif ($curr_amount > $tunpaid) {
                                    $amountpaid = $tunpaid;
                                    $this->clearOldInvoice($invoice, $amountpaid, $trans->payment_mode, $trans->bank_name, $trans->branch_name, $trans->cheque_no, $trans->date, $trans->receipt_no, $trans->currency, $trans->defcurr, $trans->ex_rate, '', $trans);
                                }
                            }
                            $curr_amount -= $tunpaid;
                        }
                    }    
                }
            }

            $now = \Carbon\Carbon::now();
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
                $ftrans = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $customer->id)->where('is_deleted', false)->orderBy('date', 'asc')->first();
                $sdate = date('Y-m-d', strtotime($customer->created_at)).' 00:00:00';
                if (!is_null($ftrans)) {
                    $sdate = $ftrans->date.' 00:00:00';
                }
                $start = $sdate;
                $end = \Carbon\Carbon::now();
                $is_post_query = false;
            }

            $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
            $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

            $transactions = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $customer->id)->where('is_deleted', false)->whereBetween('date', [$start, $end])->orderBy('date', 'asc')->get();

            $invtrans = CustomerTransaction::where('amount', '>', 0)->where('shop_id', $shop->id)->where('customer_id', $customer->id)->where('is_deleted', false)->whereBetween('date', [$start, $end])->orderBy('date', 'asc')->get();

            $payments = CustomerTransaction::where('payment', '!=', null)->where('shop_id', $shop->id)->where('customer_id', $customer->id)->whereBetween('created_at', [$start, $end])->orderBy('date', 'desc')->get();
            
            $obal = CustomerTransaction::where('customer_id', $customer->id)->where('is_ob', true)->first();
            $settings = Setting::where('shop_id', $shop->id)->first();
            $bdetails = BankDetail::where('shop_id', $shop->id)->get();

            $smsacc = SmsAccount::where('shop_id', $shop->id)->first();
            $senderids = null;
            if (!is_null($smsacc)) {
                $senderids = $smsacc->senderIds()->get();
            }

            $items = null;
            $itemtotals = null;
            $products = null;
            $is_filling_station = false;
            if ($is_filling_station) {
                $items = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->where('customer_id', $customer->id)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->groupBy('discount')->groupBy('products.name')->orderBy('name')->get([
                    \DB::raw('products.name as name'),
                    \DB::raw('SUM(quantity_sold) as quantity'),          
                    \DB::raw('price_per_unit as price_per_unit'),
                    \DB::raw('SUM(price) as price'),
                    \DB::raw('discount as discount'),
                    \DB::raw('SUM(total_discount) as total_discount')
                ]);


                $itemtotals = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->where('customer_id', $customer->id)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->groupBy('products.name')->orderBy('name')->get([
                    \DB::raw('products.name as name'),
                    \DB::raw('SUM(quantity_sold) as quantity'),          
                    \DB::raw('price_per_unit as price_per_unit'),
                    \DB::raw('SUM(price) as price'),
                    \DB::raw('discount as discount'),
                    \DB::raw('SUM(total_discount) as total_discount')
                ]);

                $products = $shop->products()->get();
            }

            $stmtcurrencies = array($defcurr);
            $currs = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $customer->id)->where('is_deleted', false)->groupBy('currency')->select('currency')->get();
            foreach ($currs as $key => $value) {
                array_push($stmtcurrencies, $value->currency);
            }

            $stmtcurr = $defcurr;
            if (!is_null($request['stmt_currency'])) {
                $stmtcurr = $request['stmt_currency'];
            }
            $supplier = null;
            $crtime = \Carbon\Carbon::now();
            $reporttime = $crtime->toDayDateTimeString();
            return view('sales.invoices.account-stmt', compact('page', 'title', 'title_sw', 'shop', 'transactions', 'invtrans', 'payments', 'customer', 'supplier', 'invoices', 'is_post_query', 'duration', 'duration_sw', 'start_date', 'end_date', 'reporttime', 'settings', 'is_filling_station', 'obal', 'bdetails', 'senderids', 'items','itemtotals','products', 'defcurr', 'currencies', 'stmtcurrencies', 'stmtcurr'));
        }else{
            return redirect('/home');
        }
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
        $acctrans = CustomerTransaction::where('customer_id', $request['customer_id'])->where('is_ob', true)->first();
        if (!is_null($acctrans)) {
            $acctrans->amount = $request['amount'];
            $acctrans->ob_paid = $request['ob_paid'];
            $acctrans->date = $opdate;
            $acctrans->save();
        }else{
            $acctrans = new CustomerTransaction();
            $acctrans->shop_id = $shop->id;
            $acctrans->user_id = $user->id;
            $acctrans->customer_id = $request['customer_id'];
            $acctrans->is_ob = true;
            $acctrans->amount = $request['amount'];
            $acctrans->currency = $request['currency'];
            $acctrans->date = $opdate;
            $acctrans->save();
        }

        return redirect()->back()->with('success', 'Opening balance was created successfully');
    }
    
    public function showReceipt($id)
    {
        $page = 'Invoice';
        $title = 'Receipt';
        $title_sw = 'Risiti';

        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        $accpay = CustomerTransaction::find(decrypt($id));
        $customer = Customer::find($accpay->customer_id);
        $currencies = currenciesList();
        $currency = $currencies[$accpay->currency];
        $amount_in_words = $this->convert_number_to_words($accpay->payment+0).' '.$currency.' Only.';

        $sale_payments = SalePayment::where('trans_id', $accpay->id)->join('an_sales', 'an_sales.id', '=', 'sale_payments.an_sale_id')->where('an_sales.shop_id', $shop->id)->join('invoices', 'invoices.an_sale_id', '=', 'an_sales.id')->where('an_sales.is_deleted', false)->select('invoices.inv_no as invoice_no', 'invoices.created_at as date', 'sale_payments.pay_date as pay_date', 'sale_payments.amount as amount')->get();
        
        return view('sales.invoices.receipt', compact('page', 'title', 'title_sw', 'shop', 'settings', 'accpay', 'customer', 'sale_payments', 'amount_in_words'));
    }

    public function accPayments(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $paydate = \Carbon\Carbon::now();

        if (!is_null($request['pay_date'])) {
            $paydate = $request['pay_date'];
        }

        if (!is_null($request['invoice_id'])) {
            $invoice = Invoice::find($request['invoice_id']);
            $sale = AnSale::find($invoice->an_sale_id);
    
            if (!is_null($sale)) {
                    
                $maxrec_no = SalePayment::where('shop_id', $shop->id)->latest()->first();
                $receipt_no = 0;
                if (!is_null($maxrec_no)) {
                    $receipt_no = $maxrec_no->receipt_no+1;
                }else{
                    $receipt_no = 1;
                }

                $pay_mode = null;
                if ($request['pay_mode'] == 'Cheque') {
                    $pay_mode = 'Bank';
                }else{
                    $pay_mode = $request['pay_mode'];
                }
                
                $bank_name = null;
                $branch_name = null;
                if (!is_null($request['bank_name'])) {
                    $bdetail = BankDetail::find($request['bank_name']);
                    $bank_name = $bdetail->bank_name;
                    $branch_name = $bdetail->branch_name;
                }elseif (!is_null($request['operator'])) {
                    $bank_name = $request['operator'];
                }else{
                    $bank_name = '';
                }
                
                $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
                $ex_rate = 1;
                $amount = $request['amount'];
                if ($request['currency'] != $defcurr) {
                    if ($request['ex_rate_mode'] == 'foreign') {
                        $local_ex_rate = $request['local_ex_rate'];
                        $ex_rate = 1/$local_ex_rate;
                    }else{
                        $foreign_ex_rate = $request['foreign_ex_rate'];
                        $ex_rate = $foreign_ex_rate;
                    }
                    $amount = $request['amount']/$ex_rate;
                }
                $acctrans = new CustomerTransaction();
                $acctrans->shop_id = $shop->id;
                $acctrans->user_id = $user->id;
                $acctrans->customer_id = $sale->customer_id;
                $acctrans->invoice_no = $invoice->inv_no;
                $acctrans->receipt_no = $receipt_no;
                $acctrans->payment = $amount;
                $acctrans->trans_invoice_amount = $amount;
                $acctrans->currency = $request['currency'];
                $acctrans->defcurr = $defcurr;
                $acctrans->ex_rate = $ex_rate;
                $acctrans->payment_mode = $payment_mode;
                $acctrans->bank_name = $bank_name;
                $acctrans->bank_branch = $branch_name;
                $acctrans->cheque_no = $cheque_no;
                $acctrans->expire_date = $request['expire_date'];
                $acctrans->date = $paydate;
                $acctrans->save();                    

                $payment = SalePayment::create([
                    'an_sale_id' => $sale->id,
                    'shop_id' => $shop->id,
                    'trans_id' => $acctrans->id,
                    'receipt_no' => $receipt_no,
                    'pay_mode' => $pay_mode,
                    'bank_name' => $bank_name,
                    'bank_branch' => $branch_name,
                    'pay_date' => $paydate,
                    'cheque_no' => $request['cheque_no'],
                    'amount' => $amount,
                    'currency' => $request['currency'],
                    'defcurr' => $defcurr,
                    'ex_rate' => $ex_rate,
                    'comments' => $request['comments']
                ]);

                if ($payment) {
                      
                    $sale->sale_amount_paid = $sale->sale_amount_paid+$payment->amount;
                    $sale->save();
                    if (true) {
                        if ($sale->sale_amount-($sale->sale_discount+$sale->adjustment) == $sale->sale_amount_paid) {
                            $sale->status = 'Paid';
                            $sale->time_paid = \Carbon\Carbon::now();
                            $sale->save();
                            $invoice->status = 'Paid';
                            $invoice->save();
                        }elseif ($sale->sale_amount-($sale->sale_discount+$sale->adjustment) > $sale->sale_amount_paid && $sale->sale_amount_paid > 0) {
                            $sale->status = 'Partially Paid';
                            $sale->time_paid = null;
                            $sale->save();
                        }elseif ($sale->sale_amount-($sale->sale_discount+$sale->adjustment) < $sale->sale_amount_paid) {
                            $sale->status = 'Excess Paid';
                            $sale->time_paid = \Carbon\Carbon::now();
                            $sale->save();
                            $invoice->status = 'Paid';
                            $invoice->save();
                        }else{
                            $sale->status = 'Unpaid';
                            $sale->time_paid = null;
                            $sale->save();
                        }
                    }

                    $payment_mode = null;
                    $cheque_no = $request['cheque_no'];
                    if ($request['pay_mode'] == 'Bank') {
                        $payment_mode = $request['deposit_mode'];
                        $cheque_no = $request['slip_no'];
                    }else{
                        $payment_mode = $request['pay_mode'];
                    }

                    //Send SMS to Customer
                    $tamount = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $sale->customer_id)->sum('amount');
                    $tpayment = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $sale->customer_id)->sum('payment');
                    $tadjst = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $sale->customer_id)->sum('adjustment');
                    $tbalance = $tamount-$tpayment-$tadjst;

                    $cust = Customer::where('id', $sale->customer_id)->whereNotNull('phone')->first();
                    if (!is_null($cust)) {
                            
                        $smsacc = SmsAccount::where('shop_id', $shop->id)->first();
                        if (!is_null($smsacc)) {
                            $senderid = SenderId::where('sms_account_id', $smsacc->id)->where('auto_sms', true)->first();
                            if (!is_null($senderid)) {
                                $autotemp = SmsTemplate::where('shop_id', $shop->id)->where('is_auto_sms', true)->where('temp_for', 'cust_pay')->first();
                                if (!is_null($autotemp)) {
                                    $message = $autotemp->message;
                                    
                                    $ph = PhoneNumber::make($cust->phone, $cust->country_code)->formatInternational(); // +32 12 34 56 78
                                    $ph = str_replace(' ', '', $ph);
                                    $phone = str_replace('+', '', $ph);
                                    $numbers = [$phone];
                                    $due_date = '';
                                    $invoice_no = '';
                                    $invoice = Invoice::where('an_sale_id', $sale->id)->first();
                                    if (!is_null($invoice)) {
                                        $invoice_no = sprintf('%04d', $invoice->inv_no);
                                        $due_date = date('d, M Y', strtotime($invoice->due_date));
                                    }
                                    $amount_due = $tbalance;
                                    $sms = str_replace('{customer_name}', $cust->name, $message);
                                    $sms1 = str_replace('{sale_date}', date('d, M Y', strtotime($cust->sale_date)), $sms);
                                    $sms2 = str_replace('{due_date}', $due_date, $sms1);
                                    $sms3 = str_replace('{invoice_no}', $invoice_no, $sms2);
                                    $msg = str_replace('{amount_due}', number_format($amount_due), $sms3);   
                                    
                                    $token = '8b49c1406246765709bfdbaa6b8a9232';
                                    $sender = $senderid->name;
                                    $client = new \GuzzleHttp\Client();
                                    $url = "https://ovalbsms.co.tz/api/send-sms";
                                    $data = array(
                                        'form_params' => array(
                                            'username' => $smsacc->username,
                                            'password' => $smsacc->password,
                                            'sender' => $sender,
                                            'receiver' =>array($phone),
                                            'message' => $msg,
                                        ),
                                        'headers' => [
                                            'Authorization' => 'Bearer '.$token,
                                            'Accept' => 'application/json',
                                        ],
                                    );
                                    $req = $client->post($url,  $data);
                                    $response = $req->getBody();
                                    $result = json_decode($response);
                                }
                            }
                        }
                    }
                }
                $success = 'Payments were added successful';
                return redirect()->back()->with('success', $success);
            }
        }else{
            
            $pay_mode = null;
            if ($request['pay_mode'] == 'Cheque') {
                $pay_mode = 'Bank';
            }else{
                $pay_mode = $request['pay_mode'];
            }

            $amount = $request['amount'];
            $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
            $ex_rate = 1;
            if ($request['currency'] != $defcurr) {
                if ($request['ex_rate_mode'] == 'Foreign') {
                    $local_ex_rate = $request['local_ex_rate'];
                    $ex_rate = 1/$local_ex_rate;
                }else{
                    $foreign_ex_rate = $request['foreign_ex_rate'];             
                    $ex_rate = $foreign_ex_rate;
                }
                $amount = $request['amount']/$ex_rate;
            }
            
            $bank_name = null;
            $branch_name = null;
            if (!is_null($request['bank_name'])) {
                $bdetail = BankDetail::find($request['bank_name']);
                $bank_name = $bdetail->bank_name;
                $branch_name = $bdetail->branch_name;
            }elseif (!is_null($request['operator'])) {
                $bank_name = $request['operator'];
            }else{
                $bank_name = '';
            }
            
            $payment_mode = null;
            $cheque_no = $request['cheque_no'];
            if ($request['pay_mode'] == 'Bank') {
                $payment_mode = $request['deposit_mode'];
                $cheque_no = $request['slip_no'];
            }else{
                $payment_mode = $request['pay_mode'];
            }
            
            $maxrec_no = SalePayment::where('shop_id', $shop->id)->latest()->first();
            $receipt_no = 0;
            if (!is_null($maxrec_no)) {                    
                $receipt_no = $maxrec_no->receipt_no+1;
            }else{
                $receipt_no = 1;
            }

            $acctrans = new CustomerTransaction();
            $acctrans->shop_id = $shop->id;
            $acctrans->user_id = $user->id;
            $acctrans->customer_id = $request['customer_id'];
            $acctrans->receipt_no = $receipt_no;
            $acctrans->payment = $amount;
            $acctrans->save();

            $rem_amount = 0;
            $trans_ob_amount = 0;
            $obtrans = CustomerTransaction::where('customer_id', $request['customer_id'])->where('is_ob', true)->where('shop_id', $shop->id)->first();
            if (!is_null($obtrans)) {
                $ob_pending = $obtrans->amount-$obtrans->ob_paid;
                if ($ob_pending > 0) {
                    if ($ob_pending >= $amount) {
                        $trans_ob_amount = $amount;
                        $obtrans->ob_paid = $obtrans->ob_paid+$amount;
                        $obtrans->save();
                        $cashin = CashIn::create([
                            'shop_id' => $shop->id,
                            'trans_id' => $acctrans->id,
                            'account' => $pay_mode,
                            'amount' => $amount,
                            'source' => 'Customer Opening balance payment',
                            'in_date' => $paydate
                        ]);
                    }else{
                        $obtrans->ob_paid = $obtrans->ob_paid+$ob_pending;
                        $obtrans->save();
                        $trans_ob_amount = $ob_pending;
                        $rem_amount = $amount-$ob_pending;
                        $cashin = CashIn::create([
                            'shop_id' => $shop->id,
                            'trans_id' => $acctrans->id,
                            'account' => $pay_mode,
                            'amount' => $ob_pending,
                            'source' => 'Customer Opening balance payment',
                            'in_date' => $paydate
                        ]);
                    }
                }else{
                    $rem_amount = $amount;
                }
            }else{
                $rem_amount = $amount;
            }

            // Pending Cash credits
            $trans_credit_amount = 0;
            if ($rem_amount > 0) {
                $cashcredits = CashOut::where('shop_id', $shop->id)->where('customer_id', $request['customer_id'])->where('is_borrowed', true)->where('status', 'Pending')->get();
                if (!is_null($cashcredits)) {
                    foreach ($cashcredits as $key => $credit) {
                        $pendcr = $credit->amount-$credit->amount_paid;
                        if ($rem_amount > 0) {
                            if ($rem_amount <= $pendcr) {
                                $credit->amount_paid = $credit->amount_paid+$rem_amount;
                                $credit->save();
                                $cashin = CashIn::create([
                                    'shop_id' => $shop->id,
                                    'trans_id' => $acctrans->id,
                                    'cash_out_id' => $credit->id,
                                    'account' => $pay_mode,
                                    'amount' => $rem_amount,
                                    'source' => 'Customer Cash Debts payments',
                                    'in_date' => $paydate
                                ]);
                            }else{
                                $credit->amount_paid = $credit->amount_paid+$pendcr;
                                $credit->save();
                                $cashin = CashIn::create([
                                    'shop_id' => $shop->id,
                                    'trans_id' => $acctrans->id,
                                    'cash_out_id' => $credit->id,
                                    'account' => $pay_mode,
                                    'amount' => $pendcr,
                                    'source' => 'Customer Cash Debts payments',
                                    'in_date' => $paydate
                                ]);
                            }
                            if ($credit->amount-$credit->amount_paid <= 0) {
                                $credit->status = 'Paid';
                                $credit->save();
                            }
                        }
                        $trans_credit_amount += $pendcr;
                        $rem_amount -= $pendcr;
                    }
                }
            }
            
            $acctrans->trans_ob_amount = $trans_ob_amount;
            $acctrans->trans_credit_amount = $trans_credit_amount;
            $acctrans->is_utilized = false;
            $acctrans->currency = $request['currency'];
            $acctrans->defcurr = $defcurr;
            $acctrans->ex_rate = $ex_rate;
            $acctrans->payment_mode = $payment_mode;
            $acctrans->bank_name = $bank_name;
            $acctrans->bank_branch = $branch_name;
            $acctrans->cheque_no = $cheque_no;
            $acctrans->expire_date = $request['expire_date'];
            $acctrans->date = $paydate;
            $acctrans->save();

            //Pending Invoices
            if ($rem_amount > 0) {
                $pinvoices = Invoice::where('invoices.shop_id', $shop->id)->where('invoices.status', 'Pending')->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->where('an_sales.customer_id', $request['customer_id'])->get();
                $curr_amount = $rem_amount;
                foreach ($pinvoices as $key => $invoice) {
                    $sale = AnSale::find($invoice->an_sale_id);
                    $tunpaid = ($sale->sale_amount-($sale->sale_discount+$sale->adjustment+$sale->sale_amount_paid));
                    if ($curr_amount > 0) {
                        if ($curr_amount <= $tunpaid) {
                            $amountpaid = $curr_amount;
                            $this->clearOldInvoice($invoice, $amountpaid, $pay_mode, $bank_name, $branch_name, $cheque_no, $paydate, $receipt_no, $request['currency'], $defcurr, $ex_rate, $request['comments'], $acctrans);
                        }elseif ($curr_amount > $tunpaid) {
                            $amountpaid = $tunpaid;
                            $this->clearOldInvoice($invoice, $amountpaid, $pay_mode, $bank_name, $branch_name, $cheque_no, $paydate, $receipt_no, $request['currency'], $defcurr, $ex_rate, $request['comments'], $acctrans);
                        }
                    }
                    $curr_amount -= $tunpaid;
                }
            }

            //Send SMS to Customer
            $tamount = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $request['customer_id'])->sum('amount');
            $tpayment = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $request['customer_id'])->sum('payment');
            $tadjst = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $request['customer_id'])->sum('adjustment');
            $tbalance = $tamount-$tpayment-$tadjst;

            $cust = Customer::where('id', $request['customer_id'])->whereNotNull('phone')->first();
            if (!is_null($cust)) {
                            
                $smsacc = SmsAccount::where('shop_id', $shop->id)->first();
                if (!is_null($smsacc)) {
                    $senderid = SenderId::where('sms_account_id', $smsacc->id)->where('auto_sms', true)->first();
                    if (!is_null($senderid)) {
                        $autotemp = SmsTemplate::where('shop_id', $shop->id)->where('is_auto_sms', true)->where('temp_for', 'cust_pay')->first();
                        if (!is_null($autotemp)) {
                            $message = $autotemp->message;
                                    
                            $ph = PhoneNumber::make($cust->phone, $cust->country_code)->formatInternational(); // +32 12 34 56 78
                            $ph = str_replace(' ', '', $ph);
                            $phone = str_replace('+', '', $ph);
                            $numbers = [$phone];
                            $due_date = '';
                            $invoice_no = '';

                            $amount_due = $tbalance;
                            $sms = str_replace('{customer_name}', $cust->name, $message);
                            $msg = str_replace('{amount_due}', number_format($amount_due), $sms);   
                                    
                            $token = '8b49c1406246765709bfdbaa6b8a9232';
                            $sender = $senderid->name;
                            $client = new \GuzzleHttp\Client();
                            $url = "https://ovalbsms.co.tz/api/send-sms";
                            $data = array(
                                'form_params' => array(
                                    'username' => $smsacc->username,
                                    'password' => $smsacc->password,
                                    'sender' => $sender,
                                    'receiver' =>array($phone),
                                    'message' => $msg,
                                ),
                                'headers' => [
                                    'Authorization' => 'Bearer '.$token,
                                    'Accept' => 'application/json',
                                ],
                            );
                            $req = $client->post($url,  $data);
                            $response = $req->getBody();
                            $result = json_decode($response);
                        }
                    }
                }
            }

            $success = 'Payments were added successful';
            return redirect()->back()->with('success', $success);
        }
    }

    public function clearOldInvoice($invoice, $amount, $pay_mode, $bank_name, $bank_branch, $cheque_no, $paydate, $receipt_no, $currency, $defcurr, $ex_rate, $comments, $acctrans)
    {   
        $shop = Shop::find(Session::get('shop_id'));
        $sale = AnSale::find($invoice->an_sale_id);
        if (!is_null($sale)) {
            $payment = SalePayment::create([
                'an_sale_id' => $sale->id,
                'shop_id' => $shop->id,
                'trans_id' => $acctrans->id,
                'receipt_no' => $receipt_no,
                'pay_mode' => $pay_mode,
                'bank_name' => $bank_name,
                'bank_branch' => $bank_branch,
                'cheque_no' => $cheque_no,
                'pay_date' => $paydate,
                'amount' => $amount,
                'currency' => $currency,
                'defcurr' => $defcurr,
                'ex_rate' => $ex_rate,
                'comments' => $comments
            ]);

            if ($payment) {
                $acctrans->trans_invoice_amount = $acctrans->trans_invoice_amount+$payment->amount;
                $acctrans->save();
                if (($acctrans->payment-($acctrans->trans_invoice_amount+$acctrans->trans_ob_amount+$acctrans->trans_credit_amount)) == 0){
                    $acctrans->is_utilized = true;
                    $acctrans->save();
                }

                $updateinv = Invoice::where('an_sale_id', $sale->id)->first();
                $sale->sale_amount_paid = $sale->sale_amount_paid+$payment->amount;
                $sale->save();
                if (true) {
                    if ($sale->sale_amount-($sale->sale_discount+$sale->adjustment) == $sale->sale_amount_paid) {
                        $sale->status = 'Paid';
                        $sale->time_paid = \Carbon\Carbon::now();
                        $sale->save();
                        $updateinv->status = 'Paid';
                        $updateinv->save();
                    }elseif ($sale->sale_amount-($sale->sale_discount+$sale->adjustment) > $sale->sale_amount_paid && $sale->sale_amount_paid > 0) {
                        $sale->status = 'Partially Paid';
                        $sale->time_paid = null;
                        $sale->save();
                    }elseif ($sale->sale_amount-($sale->sale_discount+$sale->adjustment) < $sale->sale_amount_paid) {
                        $sale->status = 'Excess Paid';
                        $sale->time_paid = \Carbon\Carbon::now();
                        $sale->save();
                        $updateinv->status = 'Paid';
                        $updateinv->save();
                    }else{
                        $sale->status = 'Unpaid';
                        $sale->time_paid = null;
                        $sale->save();
                    }
                }
            }
        }
    }

    public function deleteTrans($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $acctrans = CustomerTransaction::find(decrypt($id));
        if (!is_null($acctrans)) {
            $invoice = Invoice::find($acctrans->invoice_id);
            if (is_null($invoice)) {
                $acctrans->delete();
            }
        }

        return redirect()->back()->with('success', 'Record was removed successfully');
    }
    public function deletePayment($receipt_no)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $accpay = CustomerTransaction::where('id', decrypt($receipt_no))->where('shop_id', $shop->id)->first();
        
        if (!is_null($accpay)) {
            if ($accpay->trans_ob_amount > 0) {
                $obtrans = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $accpay->customer_id)->where('is_ob', true)->first();
                if (!is_null($obtrans)) {
                    $obtrans->ob_paid = $obtrans->ob_paid-$accpay->trans_ob_amount;
                    $obtrans->save();
                }
            }

            $cashins = CashIn::where('trans_id', $accpay->id)->get();
            foreach ($cashins as $key => $ins) {
                $cashout = CashOut::find($ins->cash_out_id);
                if (!is_null($cashout)) {
                    $cashout->amount_paid = $cashout->amount_paid-$ins->amount;
                    $cashout->status = 'Pending';
                    $cashout->save();
                }
                $ins->delete();
            }
            $sale_payments = SalePayment::where('trans_id', $accpay->id)->where('shop_id', $shop->id)->get();
            foreach ($sale_payments as $key => $payment) {

                $sale = AnSale::find($payment->an_sale_id);
                $payment->delete();
                if ($sale->sale_amount_paid > 0) {
                  
                    $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                    $amount_paid = 0;
                    foreach ($payments as $key => $pay) {
                        $amount_paid += $pay->amount;
                    }
                    $invoice = Invoice::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                    $sale->sale_amount_paid = $amount_paid;
                    $sale->save();
                    if ($sale->sale_amount-($sale->sale_discount+$sale->adjustment) == $sale->sale_amount_paid) {
                        $sale->status = 'Paid';
                        $sale->time_paid = \Carbon\Carbon::now();
                        $sale->save();
                    }elseif ($sale->sale_amount-($sale->sale_discount+$sale->adjustment) > $sale->sale_amount_paid && $sale->sale_amount_paid > 0) {
                        $sale->status = 'Partially Paid';
                        $sale->time_paid = null;
                        $sale->save();
                        if (!is_null($invoice)) {
                            $invoice->status = 'Pending';
                            $invoice->save();
                        }
                    }elseif ($sale->sale_amount-($sale->sale_discount+$sale->adjustment) < $sale->sale_amount_paid) {
                        $sale->status = 'Excess Paid';
                        $sale->time_paid = \Carbon\Carbon::now();
                        $sale->save();
                    }else{
                        $sale->status = 'Unpaid';
                        $sale->time_paid = null;
                        $sale->save();
                        if (!is_null($invoice)) {
                            $invoice->status = 'Pending';
                            $invoice->save();
                        }
                    }
                }
            }
            $accpay->delete();

            return redirect()->back()->with('success', 'Payments were deleted successful');
        }
    }

    public function invoiceReport(Request $request)
    {
        $page = 'Reports';
        $title = 'Invoices Reports';
        $title_sw = 'Ripoti za Ankara';

        $shop = Shop::find(Session::get('shop_id'));
        $customers = Customer::where('shop_id', $shop->id)->get(); 
        $customer = Customer::where('id', $request['customer_id'])->where('shop_id', $shop->id)->first();
        $settings = Setting::where('shop_id', $shop->id)->first();

        $now = \Carbon\Carbon::now();
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
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $is_post_query = false;
      }

      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        if (!is_null($customer)) {
            $invoices = Invoice::where('invoices.shop_id', $shop->id)->whereBetween('invoices.created_at', [$start, $end])->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->where('an_sales.is_deleted', false)->where('an_sales.customer_id', $customer->id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->join('products', 'products.id', '=', 'an_sale_items.product_id')->select('invoices.created_at as date', 'invoices.inv_no as inv_no', 'customers.name as customer', 'invoices.vehicle_no as vehicle_no', 'invoices.due_date as due_date', 'products.name as name', 'an_sale_items.quantity_sold as qty', 'an_sale_items.price_per_unit as price')->orderBy('date', 'desc')->get();

            $allinvoices = Invoice::where('invoices.shop_id', $shop->id)->whereBetween('invoices.created_at', [$start, $end])->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->where('an_sales.is_deleted', false)->where('an_sales.customer_id', $customer->id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('invoices.inv_no as invoiceno', 'invoices.due_date as due_date', 'an_sales.status as status', 'invoices.created_at as created_at', 'an_sales.sale_amount as amount', 'an_sales.sale_discount as discount', 'customers.cust_no as cust_no', 'customers.name as name')->get();
                // return json_encode($cinvoices);
            $total = 0;

            foreach ($invoices as $key => $invoice) {
                $total += $invoice->qty*$invoice->price;
            }

            // return json_encode($customer);
            $crtime = \Carbon\Carbon::now();
            $reporttime = $crtime->toDayDateTimeString();
            return view('sales.invoices.invoice-report', compact('page', 'title', 'title_sw', 'invoices', 'allinvoices', 'customers', 'customer', 'reporttime', 'duration', 'duration_sw', 'is_post_query', 'start_date', 'end_date', 'total', 'shop', 'settings'));
            
        }else{

            $invoices = Invoice::where('invoices.shop_id', $shop->id)->whereBetween('invoices.created_at', [$start, $end])->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->where('an_sales.is_deleted', false)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->join('products', 'products.id', '=', 'an_sale_items.product_id')->select('invoices.created_at as date', 'invoices.inv_no as inv_no', 'customers.name as customer', 'invoices.vehicle_no as vehicle_no', 'invoices.due_date as due_date', 'products.name as name', 'an_sale_items.quantity_sold as qty', 'an_sale_items.price_per_unit as price')->orderBy('date', 'asc')->get();


            $allinvoices = Invoice::where('invoices.shop_id', $shop->id)->whereBetween('invoices.created_at', [$start, $end])->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->where('an_sales.is_deleted', false)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('invoices.inv_no as invoiceno', 'invoices.due_date as due_date', 'an_sales.status as status', 'invoices.created_at as created_at', 'an_sales.sale_amount as amount', 'an_sales.sale_discount as discount', 'customers.cust_no as cust_no', 'customers.name as name')->get();
            // return json_encode($allinvoices);
            $total = 0;

            foreach ($invoices as $key => $invoice) {
                $total += $invoice->qty*$invoice->price;
            }

            // return json_encode($customer);
            $crtime = \Carbon\Carbon::now();
            $reporttime = $crtime->toDayDateTimeString();
            return view('sales.invoices.invoice-report', compact('page', 'title', 'title_sw', 'invoices', 'allinvoices', 'customers', 'customer', 'reporttime', 'duration', 'duration_sw', 'is_post_query', 'start_date', 'end_date', 'total', 'shop', 'settings'));
        }
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

        // return Invoice::where('invoices.shop_id', $shop->id)->whereDate('invoices.created_at', '>=', $date4)->get();

        $customers = Invoice::where('invoices.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->whereRaw('((sale_amount-sale_discount)-sale_amount_paid) > 0')->where('an_sales.is_deleted', false)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.id as cuid', 'customers.cust_no as cust_no', 'customers.name as name')->groupBy('name')->get();

        $agings = array();
        foreach ($customers as $key => $customer) {
            $d3 = Invoice::where('invoices.shop_id', $shop->id)->whereDate('invoices.created_at', '>=', $date3)->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->whereRaw('((sale_amount-sale_discount)-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((sale_amount-sale_discount)-sale_amount_paid)) as amount'))->first();

            $d6 = Invoice::where('invoices.shop_id', $shop->id)->whereDate('invoices.created_at', '<=', $date3)->whereDate('invoices.created_at', '>', $date6)->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->whereRaw('((sale_amount-sale_discount)-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((sale_amount-sale_discount)-sale_amount_paid)) as amount'))->first();

            $d9 = Invoice::where('invoices.shop_id', $shop->id)->whereDate('invoices.created_at', '<=', $date6)->whereDate('invoices.created_at', '>', $date9)->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->whereRaw('((sale_amount-sale_discount)-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((sale_amount-sale_discount)-sale_amount_paid)) as amount'))->first();
            
            $d12 = Invoice::where('invoices.shop_id', $shop->id)->whereDate('invoices.created_at', '<=', $date9)->whereDate('invoices.created_at', '>', $date12)->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->whereRaw('((sale_amount-sale_discount)-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((sale_amount-sale_discount)-sale_amount_paid)) as amount'))->first();

            $d15 = Invoice::where('invoices.shop_id', $shop->id)->whereDate('invoices.created_at', '<=', $date12)->whereDate('invoices.created_at', '>', $date15)->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->whereRaw('((sale_amount-sale_discount)-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((sale_amount-sale_discount)-sale_amount_paid)) as amount'))->first();
            
            $d18 = Invoice::where('invoices.shop_id', $shop->id)->whereDate('invoices.created_at', '<=', $date15)->whereDate('invoices.created_at', '>', $date18)->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->whereRaw('((sale_amount-sale_discount)-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((sale_amount-sale_discount)-sale_amount_paid)) as amount'))->first();
            
            $d21 = Invoice::where('invoices.shop_id', $shop->id)->whereDate('invoices.created_at', '<=', $date18)->whereDate('invoices.created_at', '>', $date21)->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->whereRaw('((sale_amount-sale_discount)-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((sale_amount-sale_discount)-sale_amount_paid)) as amount'))->first();
            
            $d24 = Invoice::where('invoices.shop_id', $shop->id)->whereDate('invoices.created_at', '<=', $date21)->whereDate('invoices.created_at', '>', $date24)->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->whereRaw('((sale_amount-sale_discount)-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((sale_amount-sale_discount)-sale_amount_paid)) as amount'))->first();
            
            $d27 = Invoice::where('invoices.shop_id', $shop->id)->whereDate('invoices.created_at', '<=', $date24)->whereDate('invoices.created_at', '>', $date27)->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->whereRaw('((sale_amount-sale_discount)-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((sale_amount-sale_discount)-sale_amount_paid)) as amount'))->first();
            
            $d30 = Invoice::where('invoices.shop_id', $shop->id)->whereDate('invoices.created_at', '<=', $date27)->whereDate('invoices.created_at', '>', $date30)->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->whereRaw('((sale_amount-sale_discount)-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((sale_amount-sale_discount)-sale_amount_paid)) as amount'))->first();
            
            $d33 = Invoice::where('invoices.shop_id', $shop->id)->whereDate('invoices.created_at', '<=', $date30)->whereDate('invoices.created_at', '>', $date33)->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->whereRaw('((sale_amount-sale_discount)-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((sale_amount-sale_discount)-sale_amount_paid)) as amount'))->first();
            
            $d36 = Invoice::where('invoices.shop_id', $shop->id)->whereDate('invoices.created_at', '<=', $date33)->whereDate('invoices.created_at', '>', $date36)->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->whereRaw('((sale_amount-sale_discount)-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((sale_amount-sale_discount)-sale_amount_paid)) as amount'))->first();

            $ab360 = Invoice::where('invoices.shop_id', $shop->id)->whereDate('invoices.created_at', '<=', $date36)->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->whereRaw('((sale_amount-sale_discount)-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((sale_amount-sale_discount)-sale_amount_paid)) as amount'))->first();

            $ctotal = Invoice::where('invoices.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'invoices.an_sale_id')->where('an_sales.is_deleted', false)->whereRaw('((sale_amount-sale_discount)-sale_amount_paid) > 0')->where('an_sales.customer_id', $customer->cuid)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select(
                \DB::raw('SUM(((sale_amount-sale_discount)-sale_amount_paid)) as amount'))->first();

            array_push($agings, ['cust_no' => $customer->cust_no, 'name' => $customer->name, '0-30' => $d3->amount, '31-60' => $d6->amount, '61-90' => $d9->amount, '91-120' => $d12->amount, '121-150' => $d15->amount, '151-180' => $d18->amount, '181-210' => $d21->amount, '211-240' => $d24->amount, '241-270' => $d27->amount, '271-300' => $d30->amount, '301-330' => $d33->amount, '331-360' => $d36->amount, '>360' => $ab360->amount, 'ctotal' => $ctotal->amount]);
        }

        // return $agings;

        $start_date = null;            
        $end_date = null;
        $is_post_query = false;
        $customer = null;
        $customers = null;
        $crtime = \Carbon\Carbon::now();
        $duration = date('d M, Y', strtotime($crtime));
        $duration_sw = date('d M, Y', strtotime($crtime));
        $reporttime = $crtime->toDayDateTimeString();

        return view('sales.invoices.aging-report', compact('page', 'title', 'title_sw', 'shop', 'agings', 'start_date', 'end_date', 'is_post_query', 'customer', 'customers', 'duration', 'duration_sw', 'reporttime'));
    }


    function convert_number_to_words($number) {
   
        $hyphen      = '-';
        $conjunction = '  ';
        $separator   = ' ';
        $negative    = 'negative ';
        $decimal     = ' and ';
        $dictionary  = array(
            0                   => 'Zero',
            1                   => 'One',
            2                   => 'Two',
            3                   => 'Three',
            4                   => 'Four',
            5                   => 'Five',
            6                   => 'Six',
            7                   => 'Seven',
            8                   => 'Eight',
            9                   => 'Nine',
            10                  => 'Ten',
            11                  => 'Eleven',
            12                  => 'Twelve',
            13                  => 'Thirteen',
            14                  => 'Fourteen',
            15                  => 'Fifteen',
            16                  => 'Sixteen',
            17                  => 'Seventeen',
            18                  => 'Eighteen',
            19                  => 'Nineteen',
            20                  => 'Twenty',
            30                  => 'Thirty',
            40                  => 'Fourty',
            50                  => 'Fifty',
            60                  => 'Sixty',
            70                  => 'Seventy',
            80                  => 'Eighty',
            90                  => 'Ninety',
            100                 => 'Hundred',
            1000                => 'Thousand',
            1000000             => 'Million',
            1000000000          => 'Billion',
            1000000000000       => 'Trillion',
            1000000000000000    => 'Quadrillion',
            1000000000000000000 => 'Quintillion'
        );
       
        if (!is_numeric($number)) {
            return false;
        }
       
        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . $this->convert_number_to_words(abs($number));
        }
       
        $string = $fraction = null;
       
        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }
       
        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . $this->convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $this->convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $this->convert_number_to_words($remainder);
                }
                break;
        }
       
        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }
        
        return $string;
    }

    public function changeDiscount(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $start = $request['from_date'].' 00:00:00';
        $end = $request['to_date'].' 23:59:59';
        $sales = AnSale::where('shop_id', $shop->id)->where('customer_id',$request['customer_id'])->whereBetween('time_created', [$start, $end])->where('sale_type', 'Credit')->get();
        foreach ($sales as $key => $sale) {
            $item = AnSaleItem::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->where('product_id', $request['product_id'])->first();
            if (!is_null($item)) {
                    
                $item->discount = $request['discount'];
                $item->total_discount = $item->discount*$item->quantity_sold;
                $item->save();

                $sale->sale_discount = $item->total_discount;
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
            }
        }

        return redirect()->back()->with('success', 'Discount was successful changed');
    }
}
