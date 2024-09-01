<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use Crypt;
use App\Models\Shop;
use App\Models\User;
use App\Models\Expense;
use App\Models\ExpensePayment;
use App\Models\PaymentVoucher;
use App\Models\SupplierTransaction;
use App\Models\Setting;
use App\Models\Supplier;

class ExpensePaymentController extends Controller
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
        $user = Auth::user();
        $expense = Expense::find($request['expense_id']);
    
        if (!is_null($expense)) {
                
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
            $payment = ExpensePayment::create([
                'shop_id' => $shop->id,
                'expense_id' => $expense->id,
                'account' => $pay_mode,
                'pay_date' => $paydate,
                'amount' => $request['amount']
            ]);


            if ($payment) {
                       
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
                    $pv->amount = $request['amount'];
                    $pv->account = $pay_mode;
                    $pv->voucher_for = 'Expense';
                    $pv->save();


                    $payment->pv_no = $pv->pv_no;
                    $payment->save();
                    
                    $payment_mode = null;
                    if ($request['pay_mode'] == 'Bank') {
                        $payment_mode = $request['deposit_mode'];
                    }else{
                        $payment_mode = $request['pay_mode'];
                    }

                    if (!is_null($expense->supplier_id)) {
                
                        $acctrans = new SupplierTransaction();
                        $acctrans->shop_id = $shop->id;
                        $acctrans->user_id = $user->id;
                        $acctrans->supplier_id = $expense->supplier_id;
                        $acctrans->pv_no = $pvno;
                        $acctrans->payment = $payment->amount;
                        $acctrans->payment_mode = $payment_mode;
                        $acctrans->bank_name = $bank_name;
                        $acctrans->bank_branch = $request['bank_branch'];
                        $acctrans->cheque_no = $cheque_no;
                        $acctrans->expire_date = $request['expire_date'];
                        $acctrans->date = $paydate;
                        $acctrans->trans_for = 'Expense';
                        $acctrans->save();
                    }
                
                }

                $ppays = ExpensePayment::where('expense_id', $expense->id)->get();
                $amount_paid = 0;
                foreach ($ppays as $key => $pay) {
                    $amount_paid += $pay->amount;
                }

                $expense->amount_paid = $amount_paid;

                if(($expense->amount - $amount_paid) == 0){
                    $expense->status = 'Paid';
                    $expense->save();
                }elseif(($expense->amount - $amount_paid) > 0){
                    $expense->status = 'Partially Paid';
                    $expense->save();
                }elseif(($expense->amount - $amount_paid) < 0){
                    $expense->status = 'Excess Paid';
                    $expense->save();
                }
          
        
            $success = 'Payments were added successfully';
            return redirect()->back()->with('success', $success);
        }else{

            $info = 'Record not Found';
            return redirect()->back()->with('info', $info);
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
        $title = 'Payment Voucher';
        $title_sw = 'Vocha ya Malipo';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        $voucher = SupplierTransaction::where('pv_no', decrypt($id))->where('shop_id', $shop->id)->first();

        $user = User::find($voucher->user_id);
        $supplier = Supplier::find($voucher->supplier_id);
        $amount_in_words = $this->convert_number_to_words($voucher->payment+0).' '.$settings->currency_words.' Only.';

        $ppays = Expense::where('pv_no', $voucher->pv_no)->where('shop_id', $shop->id)->get();

        
        // return $ppays;
        return view('costs.suppliers.pv', compact('page', 'title', 'title_sw', 'shop', 'settings', 'user', 'voucher', 'supplier', 'amount_in_words', 'ppays'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Expenses';
        $title = 'Edit Expense Payment';
        $title_sw = 'Hariri Malipo ya Uzo';
        $is_post_query = false;
        $start_date = '';
        $end_date = '';
        $payment = ExpensePayment::find(decrypt($id));

        return view('costs.edit-payment', compact('page', 'title', 'title_sw', 'payment', 'start_date', 'end_date', 'is_post_query'));
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
        $payment = ExpensePayment::find($id);
        $expense = Expense::find($payment->expense_id);
        
        $payment->account = $request['account'];
        $payment->pay_date = $request['pay_date'];
        $payment->amount = $request['amount'];
        $payment->save();


        if ($payment) {
            $ppays = ExpensePayment::where('expense_id', $expense->id)->get();
            $amount_paid = 0;
            foreach ($ppays as $key => $pay) {
                $amount_paid += $pay->amount;
            }

            $expense->amount_paid = $amount_paid;
            $expense->save();
        }

        $success = 'Payments was updated successfully';
        return redirect('expenses')->with('success', $success);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = ExpensePayment::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($payment)) {
            $expense = Expense::find($payment->expense_id);
            $voucher = PaymentVoucher::where('pv_no', $payment->pv_no)->where('shop_id', $shop->id)->first();
            if (!is_null($voucher)) {
                $voucher->delete();
            }

                $acctrans = SupplierTransaction::where('pv_no', $payment->pv_no)->where('shop_id', $shop->id)->first();
                if (!is_null($acctrans)) {
                    $acctrans->delete();
                }
            
            $payment->delete();
            if (true) {
                $ppays = ExpensePayment::where('expense_id', $expense->id)->get();
                $amount_paid = 0;
                foreach ($ppays as $key => $pay) {
                    $amount_paid += $pay->amount;
                }

                $expense->amount_paid = $amount_paid;
                $expense->save();
            }
        }
        $success = 'Payments was deleted successfully';
        return redirect()->back()->with('success', $success);
    }


    public function accPayments(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $paydate = \Carbon\Carbon::now();

        if (!is_null($request['pay_date'])) {
            $paydate = $request['pay_date'];
        }

        if (!is_null($request['invoice_no'])) {
            $purch = Expense::where('invoice_no', $request['invoice_no'])->where('shop_id', $shop->id)->first();
    
            if (!is_null($purch)) {

                $pvno = 0;
                $max_pv_no = PaymentVoucher::where('shop_id', $shop->id)->orderBy('pv_no', 'desc')->first();
                if (!is_null($max_pv_no)) {
                    $pvno = $max_pv_no->pv_no+1;
                }else{
                    $pvno = 1;
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

                $pv = new PaymentVoucher();
                $pv->shop_id = $shop->id;
                $pv->user_id = $user->id;
                $pv->pv_no = $pvno;
                $pv->amount = $request['amount'];
                $pv->voucher_for = 'Expense';
                $pv->account = $pay_mode;
                $pv->save();

                $payment = ExpensePayment::create([
                    'shop_id' => $shop->id,
                    'expense_id' => $purch->id,
                    'pv_no' => $pv_no,
                    'account' => $pay_mode,
                    'pay_date' => $paydate,
                    'amount' => $request['amount']
                ]);
            
                $payment_mode = null;
                $cheque_no = $request['cheque_no'];
                if ($request['pay_mode'] == 'Bank') {
                    $payment_mode = $request['deposit_mode'];
                    $cheque_no = $request['slip_no'];
                }else{
                    $payment_mode = $request['pay_mode'];
                }

                $acctrans = new SupplierTransaction();
                $acctrans->shop_id = $shop->id;
                $acctrans->user_id = $user->id;
                $acctrans->supplier_id = $purch->supplier_id;
                $acctrans->pv_no = $pvno;
                $acctrans->payment = $request['amount'];
                $acctrans->payment_mode = $payment_mode;
                $acctrans->bank_name = $bank_name;
                $acctrans->bank_branch = $request['bank_branch'];
                $acctrans->cheque_no = $cheque_no;
                $acctrans->expire_date = $request['expire_date'];
                $acctrans->date = $paydate;
                $acctrans->trans_for = 'Expense';
                $acctrans->save();
                
                if ($payment) {
                    $exppays = ExpensePayment::where('expense_id', $purch->id)->get();
                    $amount_paid = 0;
                    foreach ($exppays as $key => $pay) {
                        $amount_paid += $pay->amount;
                    }
                    $purch->amount_paid = $amount_paid;
                    $purch->account = $pay_mode;
                    $purch->pv_no = $pvno;
                    if ($purch->amount == $purch->amount_paid) {
                        $purch->status = 'Paid';
                    }
                    $purch->save();
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
            $bank_branch = $request['bank_branch'];
            $amount = $request['amount'];
           
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
            $pv->pv_no = $pvno;
            $pv->amount = $amount;                
            $pv->voucher_for = 'Expense';
            $pv->account = $pay_mode;
            $pv->save();

            $suppexps = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('supplier_id', $request['supplier_id'])->whereRaw('(amount-amount_paid) > 0')->get();
            $curr_amount = $amount;
            foreach ($suppexps as $key => $purch) {
                $tunpaid = ($purch->amount-$purch->amount_paid);
                if ($curr_amount > 0) {
                    if ($curr_amount <= $tunpaid) {
                        $amountpaid = $curr_amount;
                        $this->clearOldInvoice($purch, $amountpaid, $pay_mode, $pvno, $paydate);
                    }elseif ($curr_amount > $tunpaid) {
                        $amountpaid = $tunpaid;
                        $this->clearOldInvoice($purch, $amountpaid, $pay_mode, $pvno, $paydate);
                    }
                }
                $curr_amount -= $tunpaid;
            }

            $acctrans = new SupplierTransaction();
            $acctrans->shop_id = $shop->id;
            $acctrans->user_id = $user->id;
            $acctrans->supplier_id = $request['supplier_id'];
            $acctrans->pv_no = $pvno;
            $acctrans->payment = $amount;
            $acctrans->payment_mode = $payment_mode;
            $acctrans->bank_name = $bank_name;
            $acctrans->bank_branch = $request['bank_branch'];
            $acctrans->cheque_no = $cheque_no;
            $acctrans->expire_date = $request['expire_date'];
            $acctrans->date = $paydate;
            $acctrans->trans_for = 'Expense';
            $acctrans->save();

            $success = 'Payments were added successfully';
            return redirect()->back()->with('success', $success);
        }
    }

    public function clearOldInvoice($purch, $amount, $pay_mode, $pv_no, $paydate)
    {   
        $shop = Shop::find(Session::get('shop_id'));
        $payment = ExpensePayment::create([
            'shop_id' => $shop->id,
            'expense_id' => $purch->id,
            'pv_no' => $pv_no,
            'account' => $pay_mode,
            'pay_date' => $paydate,
            'amount' => $amount
        ]);

        if ($payment) {
            $exppays = ExpensePayment::where('expense_id', $purch->id)->get();
            $amount_paid = 0;
            foreach ($exppays as $key => $pay) {
                $amount_paid += $pay->amount;
            }

            $purch->amount_paid = $amount_paid;
            $purch->account = $pay_mode;
            $purch->pv_no = $pv_no;
            if ($purch->amount == $purch->amount_paid) {
                $purch->status = 'Paid';
            }
            $purch->save();
        }
    }

    public function updateAdjustment(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $now = Carbon::now();
        if (!is_null($request['adjust_date'])) {
            $now = $request['adjust_date'];
        }
        $acctrans = new SupplierTransaction();
        $acctrans->shop_id = $shop->id;
        $acctrans->user_id = $user->id;
        $acctrans->supplier_id = $request['supplier_id'];
        $acctrans->invoice_no = $request['invoice_no'];
        $acctrans->cn_no = $request['cn_no'];
        $acctrans->adjustment = $request['adjustment'];
        $acctrans->reason = $request['reason'];
        $acctrans->date = $now;
        $acctrans->trans_for = 'Expense';
        $acctrans->save();

        return redirect()->back()->with('success', 'Adjustment was updated successfully');
    }

    public function showCreditNote($id)
    {
        $page = 'Products';
        $title = 'Credit Note';
        $title_sw = 'Ujumbe wa Mkopo';

        $shop = Shop::find(Session::get('shop_id'));
        $user = User::find(Session::get('user_id'));
        $settings = Settings::where('shop_id', $shop->id)->first();
        $acctrans = SupplierTransaction::where('supplier_transactions.id', decrypt($id))->join('suppliers', 'suppliers.id', '=', 'supplier_transactions.supplier_id')->select('supplier_transactions.id as id', 'supplier_transactions.invoice_no as invoice_no', 'supplier_transactions.cn_no as cn_no', 'supplier_transactions.adjustment as adjustment', 'supplier_transactions.reason as reason', 'supplier_transactions.date as date', 'supplier_transactions.created_at as created_at', 'suppliers.name as name', 'suppliers.contact_no as contact_no', 'suppliers.email as email', 'suppliers.address as address')->first();

        $amount_in_words = $this->convert_number_to_words($acctrans->adjustment+0).' '.$settings->currency_words;

        return view('costs.credit-note', compact('page', 'title', 'title_sw', 'shop', 'settings', 'acctrans', 'amount_in_words'));
    }


    public function deleteCN($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $acctrans = SupplierTransaction::where('id', Crypt::decrypt($id))->where('shop_id', $shop->id)->first();
        if (!is_null($acctrans)) {
            $trans_for = $acctrans->trans_for;
            $supplier = $acctrans->supplier_id;
            $acctrans->delete();
            if ($trans_for == 'Expense') {
            return redirect('expense-account-stmt/'.Crypt::encrypt($supplier))->with('success', 'Credit Note removed successful');
            }else{
                return redirect('supplier-account-stmt/'.Crypt::encrypt($supplier))->with('success', 'Credit Note removed successful');
            }
        }else{
            return redirect()->back();
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
                
        $acctrans = SupplierTransaction::where('supplier_id', $request['supplier_id'])->where('trans_for', 'Expense')->where('invoice_no', 'OB')->first();
        if (!is_null($acctrans)) {
            $acctrans->amount = $request['amount'];
            $acctrans->date = $opdate;
            $acctrans->save();
        }else{
            $acctrans = new SupplierTransaction();
            $acctrans->shop_id = $shop->id;
            $acctrans->user_id = $user->id;
            $acctrans->supplier_id = $request['supplier_id'];
            $acctrans->invoice_no = 'OB';
            $acctrans->amount = $request['amount'];
            $acctrans->date = $opdate;
            $acctrans->trans_for = 'Expense';
            $acctrans->save();
        }
       
        return redirect()->back()->with('success', 'Opening balance was created successfully');
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
        
        $settings = Setting::where('shop_id', $shop->id)->first();
        return $string.' '.$settings->currency.' tu';
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
}
