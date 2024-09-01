<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use App\Models\Shop;
use App\Models\AnSale;
use App\Models\SalePayment;
use App\Models\Invoice;
use App\Models\CustomerTransaction;
use App\Models\BankDetail;
use App\Models\Customer;
use App\Models\SmsAccount;
use App\Models\SenderId;
use App\Models\SmsTemplate;

class SalePaymentController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
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
        $sale = AnSale::find($request['an_sale_id']);
    
        if (!is_null($sale)) {
                
            $paydate = \Carbon\Carbon::now();

            if (!is_null($request['pay_date'])) {
                $paydate = $request['pay_date'];
            }
            $maxrec_no = SalePayment::where('shop_id', $shop->id)->orderBy('receipt_no', 'desc')->first();
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

            $cheque_no = null;
            if (!is_null($request['slip_no'])) {
                $cheque_no = $request['slip_no'];
            }else{
                $cheque_no = $request['cheque_no'];
            }

            $payment = SalePayment::create([
                'an_sale_id' => $sale->id,
                'shop_id' => $shop->id,
                'receipt_no' => $receipt_no,
                'pay_mode' => $pay_mode,
                'bank_name' => $bank_name,
                'bank_branch' => $branch_name,
                'pay_date' => $paydate,
                'cheque_no' => $cheque_no,
                'amount' => $request['amount'],
                'comments' => $request['comments']
            ]);

            if ($payment) {
                $spayments = SalePayment::where('an_sale_id', $sale->id)->get();
                $amountpaid = 0;
                foreach ($spayments as $key => $value) {
                    $amountpaid += $value->amount;
                }
    
                $sale->sale_amount_paid = $amountpaid;
                $sale->save();

                $invoice = Invoice::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                if ($sale->sale_amount-($sale->sale_discount+$sale->adjustment) == $sale->sale_amount_paid) {
                    $sale->status = 'Paid';
                    $sale->time_paid = \Carbon\Carbon::now();
                    $sale->save();
                    if (!is_null($invoice)) {
                        $invoice->status = 'Paid';
                        $invoice->save();
                    }
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
                    if (!is_null($invoice)) {
                        $invoice->status = 'Paid';
                        $invoice->save();
                    }
                }else{
                    $sale->status = 'Unpaid';
                    $sale->time_paid = null;
                    $sale->save();
                    if (!is_null($invoice)) {
                        $invoice->status = 'Pending';
                        $invoice->save();
                    }
                }
                
                $payment_mode = null;
                if ($request['pay_mode'] == 'Bank') {
                    $payment_mode = $request['deposit_mode'];
                }else{
                    $payment_mode = $request['pay_mode'];
                }

                if (!is_null($invoice)) {
                        
                    $acctrans = new CustomerTransaction();
                    $acctrans->shop_id = $shop->id;
                    $acctrans->user_id = Auth::user()->id;
                    $acctrans->customer_id = $sale->customer_id;
                    $acctrans->invoice_no = $invoice->inv_no;
                    $acctrans->receipt_no = $payment->receipt_no;
                    $acctrans->payment = $payment->amount;
                    $acctrans->trans_invoice_amount = $payment->amount;
                    $acctrans->payment_mode = $payment_mode;
                    $acctrans->bank_name = $bank_name;
                    $acctrans->bank_branch = $branch_name;
                    $acctrans->cheque_no = $cheque_no;
                    $acctrans->expire_date = $request['expire_date'];
                    $acctrans->date = $paydate;
                    $acctrans->save();

                    $payment->trans_id = $acctrans->id;
                    $payment->save();
                }

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
                                $amount_due = $sale->sale_amount-($sale->amount_paid+$sale->sale_discount+$sale->adjustment);
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
        $page = 'Sales';
        $title = 'Edit Sale Payment';
        $title_sw = 'Hariri Malipo ya Uzo';
        $is_post_query = false;
        $start_date = '';
        $end_date = '';
        $payment = SalePayment::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));
        $bdetails = BankDetail::where('shop_id', $shop->id)->get();
        return view('sales.payments.edit', compact('page', 'title', 'title_sw', 'shop', 'payment', 'start_date', 'end_date', 'is_post_query', 'bdetails'));
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
        $payment = SalePayment::find($id);
        $sale = AnSale::find($payment->an_sale_id);
        $sale->sale_amount_paid = $sale->sale_amount_paid-$payment->amount;
        $sale->save();
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
        $payment->pay_mode = $request['pay_mode'];
        $payment->bank_name = $bank_name;
        $payment->bank_branch = $branch_name;
        $payment->pay_date = $request['pay_date'];
        $payment->cheque_no = $request['cheque_no'];
        $payment->amount = $request['amount'];
        $payment->comments = $request['comments'];
        $payment->save();


        if ($payment) {
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
        $success = 'Payments was updated successfully';
        return redirect()->route('an-sales.show', encrypt($sale->id))->with('success', $success);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = SalePayment::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($payment)) {
            $sale = AnSale::find($payment->an_sale_id);
            $acctrans = CustomerTransaction::find($payment->trans_id);
            if (!is_null($acctrans)) {
                $acctrans->trans_invoice_amount = $acctrans->trans_invoice_amount-$payment->amount;
                $acctrans->is_utilized = false;
                $acctrans->save();
            }
            $payment->delete();
            if ($sale->sale_amount_paid > 0) {
              
                $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                $amount_paid = 0;
                foreach ($payments as $key => $pay) {
                    $amount_paid += $pay->amount;
                }

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
        $success = 'Payments was deleted successfully';
        return redirect()->back()->with('success', $success);
    }
}
