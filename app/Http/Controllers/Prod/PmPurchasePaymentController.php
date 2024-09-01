<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use Crypt;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\User;
use App\Models\Setting;
use App\Models\ShopCurrency;
use App\Models\PmPurchasePayment;
use App\Models\PaymentVoucher;
use App\Models\PmPurchase;
use App\Models\PmSupplierTransaction;
use App\Models\Supplier;
use App\Models\CashIn;
use App\Models\CashOut;

class PmPurchasePaymentController extends Controller
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
        $user = Auth::user();
        $purchase = PmPurchase::find($request['pm_purchase_id']);
    
        if (!is_null($purchase)) {
                
            $paydate = \Carbon\Carbon::now();

            if (!is_null($request['pay_date'])) {
                $paydate = $request['pay_date'];
            }

            $pay_mode = null;
            if ($request['pay_mode'] == 'Cheque') {
                $pay_mode = 'Bank';
            }else{
                $pay_mode = $request['pay_mode'];
            }

            $bank_name = null;
            if (!is_null($request['bank_name'])) {
                $bank_name = $request['bank_name'];
            }elseif (!is_null($request['operator'])) {
                $bank_name = $request['operator'];
            }

            $cheque_no = null;
            if (!is_null($request['slip_no'])) {
                $cheque_no = $request['slip_no'];
            }else{
                $cheque_no = $request['cheque_no'];
            }
            $payment = PmPurchasePayment::create([
                'pm_purchase_id' => $purchase->id,
                'account' => $pay_mode,
                'pay_date' => $paydate,
                'amount' => $request['amount']
            ]);

            if ($payment) {
                if ($shop->subscription_type_id == 2) {
                       
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
                    $pv->amount = $request['amount'];
                    $pv->account = $pay_mode;
                    $pv->voucher_for = 'Purchase';
                    $pv->save();


                    $payment_mode = null;
                    if ($request['pay_mode'] == 'Bank') {
                        $payment_mode = $request['deposit_mode'];
                    }else{
                        $payment_mode = $request['pay_mode'];
                    }

                   if(!is_null($purchase->supplier_id)){
                        $acctrans = new PmSupplierTransaction();
                        $acctrans->shop_id = $shop->id;
                        $acctrans->user_id = $user->id;
                        $acctrans->supplier_id = $purchase->supplier_id;
                        $acctrans->pv_no = $pvno;
                        $acctrans->payment = $payment->amount;
                        $acctrans->payment_mode = $payment_mode;
                        $acctrans->bank_name = $bank_name;
                        $acctrans->bank_branch = $request['bank_branch'];
                        $acctrans->cheque_no = $cheque_no;
                        $acctrans->expire_date = $request['expire_date'];
                        $acctrans->date = $paydate;
                        $acctrans->pm_purchase_id = $purchase->id;
                        $acctrans->trans_for = 'Packages Purchase';
                        $acctrans->save();
                   }
                  
                }

                $ppays = PmPurchasePayment::where('pm_purchase_id', $purchase->id)->get();
                $amount_paid = 0;
                foreach ($ppays as $key => $pay) {
                    $amount_paid += $pay->amount;
                }

                $purchase->amount_paid = $amount_paid;
                $purchase->save();
            }
        
            $success = 'Payments were added successfully';
            return redirect()->back()->with('success', $success);
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
        $page = 'Packing Materials';
        $title = 'Edit Purchase Payment';
        $title_sw = 'Hariri Malipo ya Manunuzi';
       
        $payment = PmPurchasePayment::find(decrypt($id));

        return view('production.packing-materials.purchases.edit-payment', compact('page', 'title', 'title_sw', 'payment'));
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
        $payment = PmPurchasePayment::find(decrypt($id));
        $purchase = PmPurchase::find($payment->pm_purchase_id);
        
        $payment->account = $request['account'];
        $payment->pay_date = $request['pay_date'];
        $payment->amount = $request['amount'];
        $payment->save();


        if (!is_null($payment)) {

            if ($shop->subscription_type_id == 3 || $shop->subscription_type_id == 4) {
                   
                    $pv = PaymentVoucher::where('shop_id' , $shop->id)->where('pv_no' , $payment->pv_no)->first();
                    if(!is_null($pv)){
                      $pv->amount = $request['amount'];
                      $pv->account = $request['account'];
                      $pv->save();  
                    }
                    


                    $payment_mode = null;
                    if ($request['pay_mode'] == 'Bank') {
                        $payment_mode = $request['deposit_mode'];
                    }else{
                        $payment_mode = $request['pay_mode'];
                    }

                   if(!is_null($purchase->supplier_id)){
                        $acctrans = PmSupplierTransaction::where('shop_id' , $shop->id)->where('pm_purchase_id' , $purchase->id)->where('pv_no' , $payment->pv_no)->first();

                        if (!is_null($acctrans)) {
                            $acctrans->payment = $payment->amount;
                            $acctrans->payment_mode = $payment_mode;
                            $acctrans->date = $request['pay_date'];
                            $acctrans->save();
                        }
                        
                   }

                   $total_amount = PmPurchasePayment::where('pm_purchase_id' , $purchase->id)->sum('amount');
                   $purchase->amount_paid =  $total_amount;
                   $purchase->save();
                  
                }
        }



        $success = 'Payments was updated successfully';
        return redirect()->back()->with('success', $success);
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
                
            $acctrans = PmSupplierTransaction::where('supplier_id', $request['supplier_id'])->where('invoice_no', 'OB')->first();
            if (!is_null($acctrans)) {
                $acctrans->amount = $request['amount'];
                $acctrans->ob_paid = $request['ob_paid'];
                $acctrans->date = $opdate;
                $acctrans->save();
            }else{
                $acctrans = new PmSupplierTransaction();
                $acctrans->shop_id = $shop->id;
                $acctrans->user_id = $user->id;
                $acctrans->supplier_id = $request['supplier_id'];
                $acctrans->invoice_no = 'OB';
                $acctrans->amount = $request['amount'];
                $acctrans->date = $opdate;
                $acctrans->trans_for = 'Packing Malighafi Purchase';
                $acctrans->save();
            }
       
        

        return redirect()->back()->with('success', 'Opening balance was created successfully');
    }
    

    public function showVoucher($pv_no)
    {
        $page = 'Purchases';
        $title = 'Payment Voucher';
        $title_sw = 'Vocha ya Malipo';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $voucher = null;
        $ppays = null;

        $voucher = PmSupplierTransaction::where('pv_no', decrypt($pv_no))->where('shop_id', $shop->id)->first();
        $ppays = PmPurchasePayment::where('pv_no', $voucher->pv_no)->where('pm_purchase_payments.shop_id', $shop->id)->join('pm_purchases', 'pm_purchases.id', '=', 'pm_purchase_payments.pm_purchase_id')->select('pm_purchase_payments.pay_date as pay_date', 'pm_purchase_payments.amount as amount', 'pm_purchase_payments.pv_no as pv_no', 'pm_purchases.date as date', 'pm_purchases.invoice_no as invoice_no')->get();
       

        $user = User::find($voucher->user_id);
        $supplier = Supplier::find($voucher->supplier_id);
        $amount_in_words = $this->convert_number_to_words($voucher->payment+0).' '.$settings->currency_words.' Only.';

        // return $ppays;
        return view('production.packing-materials.purchases.pv', compact('page', 'title', 'title_sw', 'shop', 'settings', 'user', 'voucher', 'supplier', 'amount_in_words', 'ppays'));
    }

    public function updateAdjustment(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $now = Carbon::now();
        if (!is_null($request['adjust_date'])) {
            $now = $request['adjust_date'];
        }

        $acctrans = new PmSupplierTransaction();
        $acctrans->shop_id = $shop->id;
        $acctrans->user_id = $user->id;
        $acctrans->supplier_id = $request['supplier_id'];
        $acctrans->invoice_no = $request['invoice_no'];
        $acctrans->cn_no = $request['cn_no'];
        $acctrans->adjustment = $request['adjustment'];
        $acctrans->reason = $request['reason'];
        $acctrans->date = $now;
        $acctrans->trans_for = 'Packages Material Purchase';
        $acctrans->save();

        return redirect()->back()->with('success', 'Adjustment was updated successfully');
    }

     public function accPayments(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $paydate = \Carbon\Carbon::now();

        if (!is_null($request['pay_date'])) {
            $paydate = $request['pay_date'];
        }

        if (!is_null($request['purchase_id'])) {
            $purchase = PmPurchase::find($request['purchase_id']);
            if (!is_null($purchase)) {
                $pvno = 0;
                $max_pv_no = PaymentVoucher::where('shop_id', $shop->id)->latest()->first();
                if (!is_null($max_pv_no)) {
                    $pvno = $max_pv_no->pv_no+1;
                }else{
                    $pvno = 1;
                }
                
                $amount = 0;
                $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
                $ex_rate = 1;
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

                $pay_mode = null;
                if ($request['pay_mode'] == 'Cheque') {
                    $pay_mode = 'Bank';
                }else{
                    $pay_mode = $request['pay_mode'];
                }

                $bank_name = null;
                if (!is_null($request['bank_name'])) {
                    $bank_name = $request['bank_name'];
                }elseif (!is_null($request['operator'])) {
                    $bank_name = $request['operator'];
                }

                $payment_mode = null;
                $cheque_no = $request['cheque_no'];
                if ($request['pay_mode'] == 'Bank') {
                    $payment_mode = $request['deposit_mode'];
                    $cheque_no = $request['slip_no'];
                }else{
                    $payment_mode = $request['pay_mode'];
                }

                $pv = new PaymentVoucher();
                $pv->shop_id = $shop->id;
                $pv->user_id = $user->id;
                $pv->pv_no = $pvno;
                $pv->amount = $amount;
                $pv->voucher_for = 'Purchase';
                $pv->account = $pay_mode;
                $pv->save();

                $payment = PmPurchasePayment::create([
                    'shop_id' => $shop->id,
                    'pm_purchase_id' => $purchase->id,
                    'pv_no' => $pvno,
                    'pay_mode' => $pay_mode,
                    'pay_date' => $paydate,
                    'amount' => $amount,
                    'bank_name' => $bank_name,
                    'bank_branch' => $bank_branch,
                    'cheque_no' => $cheque_no,
                    'pay_date' => $paydate,
                    'currency' => $currency,
                    'defcurr' => $defcurr,
                    'ex_rate' => $ex_rate,
                    'comments' => $comments
                ]);

                if ($payment) {
                    $acctrans = new PmSupplierTransaction();
                    $acctrans->shop_id = $shop->id;
                    $acctrans->user_id = $user->id;
                    $acctrans->supplier_id = $purchase->supplier_id;
                    $acctrans->pv_no = $pvno;
                    $acctrans->payment = $payment->amount;
                    $acctrans->trans_invoice_amount = $payment->amount;
                    $acctrans->currency = $request['currency'];
                    $acctrans->defcurr = $defcurr;
                    $acctrans->ex_rate = $ex_rate;
                    $acctrans->payment_mode = $payment_mode;
                    $acctrans->bank_name = $bank_name;
                    $acctrans->bank_branch = $bank_branch;
                    $acctrans->cheque_no = $cheque_no;
                    $acctrans->expire_date = $request['expire_date'];
                    $acctrans->date = $paydate;
                    $acctrans->save();
            
                    $ppays = PmPurchasePayment::where('pm_purchase_id', $purchase->id)->get();
                    $amount_paid = 0;
                    foreach ($ppays as $key => $pay) {
                        $amount_paid += $pay->amount;
                    }

                    $purchase->amount_paid = $amount_paid;
                    $purchase->save();
                }
                $success = 'Payments were added successfully';
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
                if ($request['ex_rate_mode'] == 'foreign') {
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
            
            $pvno = 0;
            $max_pv_no = PaymentVoucher::where('shop_id', $shop->id)->latest()->first();
            if (!is_null($max_pv_no)) {
                $pvno = $max_pv_no->pv_no+1;
            }else{
                $pvno = 1;
            }

            $pv = new PaymentVoucher();
            $pv->shop_id = $shop->id;
            $pv->user_id = $user->id;
            $pv->pv_no = $pvno;
            $pv->amount = $amount;                
            $pv->voucher_for = 'Purchase';
            $pv->account = $pay_mode;
            $pv->save();
            
            $acctrans = new PmSupplierTransaction();
            $acctrans->shop_id = $shop->id;
            $acctrans->user_id = $user->id;
            $acctrans->supplier_id = $request['supplier_id'];
            $acctrans->pv_no = $pvno;
            $acctrans->payment = $amount;
            $acctrans->save();

            $rem_amount = 0;
            $trans_ob_amount = 0;
            $obtrans = PmSupplierTransaction::where('supplier_id', $request['supplier_id'])->where('is_ob', true)->where('shop_id', $shop->id)->first();
            if (!is_null($obtrans)) {
                $ob_pending = $obtrans->amount-$obtrans->ob_paid;
                if ($ob_pending > 0) {
                    if ($ob_pending >= $amount) {
                        $trans_ob_amount = $amount;
                        $obtrans->ob_paid = $obtrans->ob_paid+$amount;
                        $obtrans->save();
                        $cashout = CashOut::create([
                            'shop_id' => $shop->id,
                            'pm_trans_id' => $acctrans->id,
                            'account' => $pay_mode,
                            'amount' => $amount,
                            'reason' => 'Supplier Opening balance payment',
                            'out_date' => $paydate
                        ]);
                    }else{
                        $obtrans->ob_paid = $obtrans->ob_paid+$ob_pending;
                        $obtrans->save();
                        $trans_ob_amount = $ob_pending;
                        $rem_amount = $amount-$ob_pending;
                        $cashout = CashOut::create([
                            'shop_id' => $shop->id,
                            'pm_trans_id' => $acctrans->id,
                            'account' => $pay_mode,
                            'amount' => $amount,
                            'reason' => 'Supplier Opening balance payment',
                            'out_date' => $paydate
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
                $cashcredits = CashIn::where('shop_id', $shop->id)->where('supplier_id', $request['supplier_id'])->where('is_loan', true)->where('status', 'Pending')->get();
                if (!is_null($cashcredits)) {
                    foreach ($cashcredits as $key => $credit) {
                        $pendcr = $credit->amount-$credit->amount_paid;
                        if ($rem_amount > 0) {
                            if ($rem_amount <= $pendcr) {
                                $credit->amount_paid = $credit->amount_paid+$rem_amount;
                                $credit->save();
                                $cashin = CashOut::create([
                                    'shop_id' => $shop->id,
                                    'pm_trans_id' => $acctrans->id,
                                    'cash_in_id' => $credit->id,
                                    'account' => $pay_mode,
                                    'amount' => $rem_amount,
                                    'source' => 'Supplier Cash Debts payments',
                                    'in_date' => $paydate
                                ]);
                            }else{
                                $credit->amount_paid = $credit->amount_paid+$pendcr;
                                $credit->save();
                                $cashin = CashOut::create([
                                    'shop_id' => $shop->id,
                                    'pm_trans_id' => $acctrans->id,
                                    'cash_in_id' => $credit->id,
                                    'account' => $pay_mode,
                                    'amount' => $pendcr,
                                    'source' => 'Supplier Cash Debts payments',
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

            if ($rem_amount > 0) {
                $pps = PmPurchase::where('shop_id', $shop->id)->where('supplier_id', $request['supplier_id'])->where('is_deleted', false)->where('status', 'Pending')->get();
                    
                $curr_amount = $rem_amount;
                foreach ($pps as $key => $purch) {
                    $tunpaid = ($purch->total_amount-$purch->amount_paid);
                    if ($curr_amount > 0) {
                        if ($curr_amount <= $tunpaid) {
                            $amountpaid = $curr_amount;
                            $this->clearOldInvoice($purch, $amountpaid, $pay_mode, $paydate, $pvno, $bank_name, $branch_name, $cheque_no, $request['currency'], $defcurr, $ex_rate, $request['comments'], $acctrans);
                        }elseif ($curr_amount > $tunpaid) {
                            $amountpaid = $tunpaid;
                            $this->clearOldInvoice($purch, $amountpaid, $pay_mode, $paydate, $pvno, $bank_name, $branch_name, $cheque_no, $request['currency'], $defcurr, $ex_rate, $request['comments'], $acctrans);
                        }
                    }
                    $curr_amount -= $tunpaid;
                }
            }
            $success = 'Payments were added successfully';
            return redirect()->back()->with('success', $success);
        }
    }

    public function clearOldInvoice($purch, $amount, $pay_mode, $paydate, $pv_no, $bank_name, $bank_branch, $cheque_no, $currency, $defcurr, $ex_rate, $comments, $acctrans)
    {   
        $shop = Shop::find(Session::get('shop_id'));
        $payment = PmPurchasePayment::create([
            'shop_id' => $shop->id,
            'pm_purchase_id' => $purch->id,
            'rm_trans_id' => $acctrans->id,
            'pv_no' => $pv_no,
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

            $ppays = PmPurchasePayment::where('pm_purchase_id', $purch->id)->get();
            $amount_paid = 0;
            foreach ($ppays as $key => $pay) {
                $amount_paid += $pay->amount;
            }

            $purch->amount_paid = $amount_paid;
            if (($purch->total_amount-$purch->amount_paid) == 0) {
                $purch->status = 'Paid';
            }
            $purch->save();
        }
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

    function convert_number_to_wordsSW($number, $shop) {
   
        $hyphen      = '-';
        $conjunction = '  ';
        $separator   = ' ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'Sifuri',
            1                   => 'Moja',
            2                   => 'Mbili',
            3                   => 'Tatu',
            4                   => 'Nne',
            5                   => 'Tano',
            6                   => 'Sita',
            7                   => 'Saba',
            8                   => 'Nane',
            9                   => 'Tisa',
            10                  => 'Kumi',
            11                  => 'Kumi na moja',
            12                  => 'Kumi na mbili',
            13                  => 'Kumi na tatu',
            14                  => 'Kumi na nne',
            15                  => 'Kumi na tano',
            16                  => 'Kumi na sita',
            17                  => 'Kumi na saba',
            18                  => 'Kumi na nane',
            19                  => 'Kumi na tisa',
            20                  => 'Ishirini',
            30                  => 'Thelathini',
            40                  => 'Arobaini',
            50                  => 'Hamsini',
            60                  => 'Sitini',
            70                  => 'Sabini',
            80                  => 'Themanini',
            90                  => 'Tisini',
            100                 => 'Mia',
            1000                => 'Elfu',
            1000000             => 'Milioni',
            1000000000          => 'Bilioni',
            1000000000000       => 'Trillioni',
            1000000000000000    => 'Quadrillioni',
            1000000000000000000 => 'Quintillioni'
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
            return $negative . $this->convert_number_to_wordsSW(abs($number), $shop);
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
                    $string .= $conjunction . $this->convert_number_to_wordsSW($remainder, $shop);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $this->convert_number_to_wordsSW($numBaseUnits, $shop) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $this->convert_number_to_words($remainder, $shop);
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
        
        $settings = Settings::where('shop_id', $shop->id)->first();
        return $string.' '.$settings->currency.' tu';
    }

}
