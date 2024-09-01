<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Response;
use \Carbon\Carbon;
use App\Models\AnSale;
use App\Models\SaleTemp;
use App\Models\SaleItemTemp;
use App\Models\ServiceItemTemp;
use App\Models\AnSaleItem;
use App\Models\ServiceSaleItem;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Stock;
use App\Models\ProdDamage;
use App\Models\Shop;
use App\Models\Customer;
use App\Models\Setting;
use App\Models\Invoice;
use App\Models\TransferOrderItem;
use App\Models\SaleReturnItem;
use App\Models\SalePayment;
use App\Models\LatestStockSoldLog;
use App\Models\Device;
use App\Models\DeviceSale;
use App\Models\CustomerTransaction;
use App\Models\Payment;
use App\Models\ServiceCharge;
use App\Models\BankDetail;
use App\Models\Grade;
use App\Models\SmsAccount;
use App\Models\SenderId;
use App\Models\SmsTemplate;
use App\Models\Jobs\SendSMS;
use App\Models\ShopCurrency;

class SaleController extends Controller
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
        $shop = Shop::find(Session::get('shop_id'));
        // return $shop;
        if (!is_null($shop)) {
            if ($shop->subscription_type_id == 2) {
                $lastpay = Payment::where('shop_id', $shop->id)->where('amount_paid', '>', 0)->where('status', 'Activated')->where('is_for_module', false)->latest()->first();
                $premserv = ServiceCharge::where('type', 2)->where('duration', 'Monthly')->first();
                if (!is_null($lastpay)) {
                    if ($lastpay->amount_paid % $premserv->initial_pay == 0 && $lastpay->subscr_type == 2) {
                        return $this->getPOS($shop);
                    } else {
                        $wrn_en = 'You have Upgraded your account  to have PREMIUM subscription but no any payments verified after this changes.Please make payment for any amount for PREMIUM subscription as shown in table below then enter your verification code here inorder to continue using our service. Thank you for using SmartMauzo service.';
                        $wrn_sw = 'Umeboresha akaunti yako kuwa na usajili wa PREMIUM lakini hakuna malipo yoyote yaliyothibitishwa baada ya mabadiliko haya. Tafadhali fanya malipo kwa kiasi chochote cha usajili wa PREMIUM kama inavyoonyeshwa kwenye jedwali hapa chini kisha ingiza nambari yako ya uthibitishaji hapa ili kuendelea kutumia huduma yetu. Asante kwa kutumia huduma ya SmartMauzo.';
                        if (app()->getLocale() == 'en') {
                            return redirect('verify-payment')->with('info', $wrn_en);
                        } else {
                            return redirect('verify-payment')->with('info', $wrn_sw);
                        }
                    }
                } else {
                    return $this->getPOS($shop);
                }
            } else {
                return $this->getPOS($shop);
            }
        } else {
            
            return redirect('unauthorized');
        }
    }


    public function getPOS($shop)
    {

        $page = 'Point of Sale';
        $title = 'Point of Sale';
        $title_sw = 'Sehemu ya Kuuzia';

        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', false)->first();
        if (!is_null($payment)) {
            $user = Auth::user();
            $sale = AnSale::where('shop_id', $shop->id)->count();
            $customers = Customer::where('shop_id', $shop->id)->orderBy('id', 'desc')->get();

            $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
            $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            if (is_null($dfcurr)) {
                return redirect('settings')->with('error', 'Please add your Default Currency to continue...');
            }

            $saletemp = SaleTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->whereNull('customer_id')->first();
            if (is_null($saletemp)) {
                $saletemp = new SaleTemp();
                $saletemp->shop_id = $shop->id;
                $saletemp->user_id = $user->id;
                $saletemp->currency = $dfcurr->code;
                $saletemp->defcurr = $dfcurr->code;
                $saletemp->save();
            }

            $pendingtemps = SaleTemp::where('sale_temps.shop_id', $shop->id)->where('user_id', $user->id)->whereNotNull('customer_id')->join('customers', 'customers.id', '=', 'sale_temps.customer_id')->select('sale_temps.id as id', 'name', 'sale_temps.created_at as created_at')->get();

            $settings = Setting::where('shop_id', $shop->id)->first();
            if (is_null($settings)) {
                $settings = Setting::create([
                    'shop_id' => $shop->id,
                    'tax_rate' => 18,
                    'inv_no_type' => 'Automatic'
                ]);
            }

            $mindays = 0;
            $date = Carbon::parse($payment->expire_date);
            $now = Carbon::now();
            $status = $date->diffInDays($now);
            $paydate = Carbon::parse($payment->created_at);

            $lastpay = Payment::where('shop_id', $shop->id)->where('is_expired', 1)->where('is_for_module', false)->orderBy('created_at', 'desc')->first();
            if (!is_null($lastpay)) {
                $lastexp = Carbon::parse($lastpay->expire_date);
                $oldpaydate = Carbon::parse($lastpay->created_at);
                $slipdays = $paydate->diffInDays($lastexp);
                // return $slipdays;
                if ($slipdays < 15) {
                    $mindays = $now->diffInDays($oldpaydate);
                } else {
                    $mindays = $now->diffInDays($paydate);
                }
            } else {
                $mindays = $now->diffInDays($paydate);
            }

            if ($mindays < 10) {
                $mindays = 15;
            }

            $products = null;
            if ($settings->is_filling_station) {
                $products = $shop->products()->get();
            }
            $bdetails = BankDetail::where('shop_id', $shop->id)->get();

            $custids = array(
                ['id' => 1, 'name' => 'TIN'],
                ['id' => 2, 'name' => 'Driving License'],
                ['id' => 3, 'name' => 'Voters Number'],
                ['id' => 4, 'name' => 'Passport'],
                ['id' => 5, 'name' => 'NID'],
                ['id' => 6, 'name' => 'NIL'],
                ['id' => 7, 'name' => 'Meter No']
            );

            if ($shop->business_type_id == 3) {
                $devices = Device::where('shop_id', $shop->id)->get();
                $grades = Grade::where('shop_id', $shop->id)->get();
                return view('sales.service-pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'sale', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'devices', 'bdetails', 'mindays', 'grades', 'custids'));
            } elseif ($shop->business_type_id == 4) {
                return view('sales.both-pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'sale', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'bdetails', 'mindays', 'custids', 'products'));
            } else {
                return view('sales.pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'sale', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'bdetails', 'mindays', 'products', 'custids'));
            }
        } else {

            $info = 'Dear customer your account is not activated please make payment and activate now.';
            return redirect('verify-payment')->with('info', $info);
        }
    }

    public function getSuccess()
    {
        $success = 'You have successfully aded sales';

        return redirect()->to('/pos')->with('success', $success);
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
        $now = null;
        if (is_null($request['sale_date'])) {
            $now = Carbon::now();
        } else {

            $now = \Carbon\Carbon::now();
            $time = date('h:i:s', strtotime($now));
            $now = $request['sale_date'] . ' ' . $time;
        }

        if ($shop->subscription_type_id >= 2 && $request['sale_type'] == 'credit' && is_null($request['due_date'])) {
            return redirect()->back()->with('error', 'Sorry you are creating credit sale. Please Select due date for your Invoice');
        }

        $maxsaleno = AnSale::where('shop_id', $shop->id)->latest()->first();
        $sale_no = null;
        if (!is_null($maxsaleno)) {
            $sale_no = $maxsaleno->sale_no + 1;
        } else {
            $sale_no = 1;
        }

        $sale_type = null;
        if ($request['sale_type'] == 'credit') {
            $sale_type = 'credit';
        } else {
            $sale_type = 'cash';
        }

        $settings = Setting::where('shop_id', $shop->id)->first();
        $amount_paid = 0;

        if (!is_null($request['amount_paid'])) {
            $amount_paid = $request['amount_paid'];
        }

        $pay_type = null;
        if ($request['pay_type'] == 'Cheque') {
            $pay_type = 'Bank';
        } else {
            $pay_type = $request['pay_type'];
        }

        $bank_name = null;
        if (!is_null($request['bank_name'])) {
            $bank_name = $request['bank_name'];
        } elseif (!is_null($request['operator'])) {
            $bank_name = $request['operator'];
        }

        $cheque_no = null;
        if (!is_null($request['slip_no'])) {
            $cheque_no = $request['slip_no'];
        } else {
            $cheque_no = $request['cheque_no'];
        }

        $saletemp = SaleTemp::find($request['sale_temp_id']);
        if (is_null($saletemp)) {
            return redirect('pos');
        } else {
            if ($shop->business_type_id == 3) {

                $saleitems = ServiceItemTemp::where('sale_temp_id', $saletemp->id)->get();
                if ($saleitems->count() > 0) {

                    $sale = AnSale::create([
                        'customer_id' => $saletemp->customer_id,
                        'shop_id' => $shop->id,
                        'user_id' => Auth::user()->id,
                        'sale_amount_paid' => $amount_paid,
                        'currency' => $saletemp->currency,
                        'defcurr' => $saletemp->defcurr,
                        'ex_rate' => $saletemp->ex_rate,
                        'comments' => $saletemp->comments,
                        'status' => 'Unpaid',
                        'pay_type' => $saletemp->pay_type,
                        'time_created' => $now,
                        'sale_type' => $saletemp->sale_type,
                        'sale_no' => $sale_no,
                        'grade_id' => $request['grade_id'],
                        'year' => $request['year']
                    ]);

                    foreach ($saleitems as $key => $value) {
                        $shop_service = $shop->services()->where('service_id', $value->service_id)->first();
                        $saleitemData = new ServiceSaleItem;
                        $saleitemData->an_sale_id = $sale->id;
                        $saleitemData->service_id = $value->service_id;
                        $saleitemData->no_of_repeatition = $value->no_of_repeatition;
                        $saleitemData->price = $value->price;
                        $saleitemData->total = $value->total;
                        $saleitemData->discount = $value->discount;
                        $saleitemData->total_discount = $value->discount * $saleitemData->no_of_repeatition;
                        if ($value->vat_amount > 0) {
                            $saleitemData->tax_amount = $value->vat_amount;
                        }
                        $saleitemData->time_created = $now;
                        $saleitemData->save();
                    }

                    $sale_amount = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total');
                    $sale_discount = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total_discount');
                    $tax_amount = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('tax_amount');
                    // $sale = AnSale::find($sale->id);
                    $sale->sale_amount = $sale_amount + $tax_amount;
                    $sale->sale_discount = $sale_discount;
                    if ($request['sale_type'] == 'cash') {
                        $amount_paid = ($sale->sale_amount - $sale->sale_discount);
                        $sale->sale_amount_paid = $amount_paid;
                        $maxrec_no = SalePayment::where('shop_id', $shop->id)->orderBy('created_at', 'desc')->first();
                        $receipt_no = 0;
                        if (!is_null($maxrec_no)) {
                            $receipt_no = $maxrec_no->receipt_no + 1;
                        } else {
                            $receipt_no = 1;
                        }

                        $payment = SalePayment::create([
                            'an_sale_id' => $sale->id,
                            'shop_id' => $shop->id,
                            'receipt_no' => $receipt_no,
                            'pay_mode' => $pay_type,
                            'bank_name' => $bank_name,
                            'bank_branch' => $request['bank_branch'],
                            'pay_date' => $now,
                            'cheque_no' => $cheque_no,
                            'amount' => $amount_paid,
                            'currency' => $saletemp->currency,
                            'defcurr' => $saletemp->defcurr,
                            'ex_rate' => $saletemp->ex_rate,
                        ]);
                    } else {
                        if ($shop->subscription_type_id == 1) {

                            $payment = null;
                            if ($amount_paid > 0) {
                                $maxrec_no = SalePayment::where('shop_id', $shop->id)->orderBy('created_at', 'desc')->first();
                                $receipt_no = 0;
                                if (!is_null($maxrec_no)) {
                                    $receipt_no = $maxrec_no->receipt_no + 1;
                                } else {
                                    $receipt_no = 1;
                                }
                                $payment = SalePayment::create([
                                    'an_sale_id' => $sale->id,
                                    'shop_id' => $shop->id,
                                    'receipt_no' => $receipt_no,
                                    'pay_mode' => $pay_type,
                                    'bank_name' => $bank_name,
                                    'bank_branch' => $request['bank_branch'],
                                    'pay_date' => $now,
                                    'cheque_no' => $cheque_no,
                                    'amount' => $amount_paid,
                                    'currency' => $saletemp->currency,
                                    'defcurr' => $saletemp->defcurr,
                                    'ex_rate' => $saletemp->ex_rate,
                                ]);
                            }
                        }
                    }

                    $sale->tax_amount = $tax_amount;
                    $sale->save();
                    if ($sale->sale_amount - $sale->sale_discount == $sale->sale_amount_paid) {
                        $sale->status = 'Paid';
                        $sale->time_paid = \Carbon\Carbon::now();
                        $sale->save();
                    } elseif ($sale->sale_amount - $sale->sale_discount > $sale->sale_amount_paid && $sale->sale_amount_paid > 0) {
                        $sale->status = 'Partially Paid';
                        $sale->save();
                    } elseif ($sale->sale_amount - $sale->sale_discount < $sale->sale_amount_paid) {
                        $sale->status = 'Excess Paid';
                        $sale->time_paid = \Carbon\Carbon::now();
                        $sale->save();
                    } elseif ($sale->sale_amount_paid == 0) {
                        $sale->status = 'Unpaid';
                        $sale->save();
                    }

                    $customer = Customer::find($sale->customer_id);
                    $temp_items = ServiceItemTemp::where('sale_temp_id', $saletemp->id)->get();
                    foreach ($temp_items as $key => $item) {
                        $item->delete();
                    }
                    $saletemp->delete();

                    $items = ServiceSaleItem::where('an_sale_id',  $sale->id)->join('services', 'services.id', '=', 'service_sale_items.service_id')->get();
                    $date = \Carbon\Carbon::now()->toDayDateTimeString();

                    $recno = AnSale::where('shop_id', $shop->id)->count();
                    if ($shop->subscription_type_id >= 2) {
                        if ($request['sale_type'] == 'credit') {
                            $invoice = null;
                            if (!is_null($request['inv_no'])) {
                                $invoice = Invoice::create([
                                    'inv_no' => $request['inv_no'],
                                    'shop_id' => $shop->id,
                                    'user_id' => $user->id,
                                    'an_sale_id' => $sale->id,
                                    'due_date' => $request['due_date'],
                                    'status' => 'Pending',
                                    'note' => $request['comments']
                                ]);
                                $invoice->created_at = $now;
                                $invoice->save();
                            } else {
                                $max_no = Invoice::where('shop_id', $shop->id)->latest()->first();
                                $invoice_no = 0;
                                if (!is_null($max_no)) {
                                    $invoice_no = (int)$max_no->inv_no + 1;
                                } else {
                                    $invoice_no = 1;
                                }
                                $invoice = Invoice::create([
                                    'inv_no' => $invoice_no,
                                    'shop_id' => $shop->id,
                                    'an_sale_id' => $sale->id,
                                    'user_id' => $user->id,
                                    'due_date' => $request['due_date'],
                                    'status' => 'Pending',
                                    'note' => $request['comments']
                                ]);

                                $invoice->created_at = $now;
                                $invoice->save();
                            }

                            $acctrans = new CustomerTransaction();
                            $acctrans->shop_id = $shop->id;
                            $acctrans->user_id = $user->id;
                            $acctrans->customer_id = $sale->customer_id;
                            $acctrans->invoice_id = $invoice->id;
                            $acctrans->invoice_no = $invoice->inv_no;
                            $acctrans->amount = ($sale->sale_amount - $sale->sale_discount);
                            $acctrans->currency = $saletemp->currency;
                            $acctrans->defcurr = $saletemp->defcurr;
                            $acctrans->ex_rate = $saletemp->ex_rate;
                            $acctrans->date = $now;
                            $acctrans->save();

                            $utransactions = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $customer->id)->whereNotNull('receipt_no')->where('is_utilized', false)->where('is_deleted', false)->get();

                            if (!is_null($utransactions)) {
                                foreach ($utransactions as $key => $trans) {
                                    $rem_amount = $trans->payment - ($trans->trans_invoice_amount + $trans->trans_ob_amount + $trans->trans_credit_amount);
                                    if ($rem_amount > 0) {
                                        $paidamount = 0;
                                        if ($rem_amount > ($sale->sale_amount - $sale->sale_discount)) {
                                            $paidamount = $sale->sale_amount - $sale->sale_discount;
                                            $trans->trans_invoice_amount = $trans->trans_invoice_amount + $paidamount;
                                            $trans->save();
                                        } else {
                                            $paidamount = $rem_amount;
                                            $trans->trans_invoice_amount = $trans->trans_invoice_amount + $paidamount;
                                            $trans->is_utilized = true;
                                            $trans->save();
                                        }

                                        $payment = SalePayment::create([
                                            'an_sale_id' => $sale->id,
                                            'shop_id' => $shop->id,
                                            'trans_id' => $trans->id,
                                            'receipt_no' => $trans->receipt_no,
                                            'pay_mode' => $trans->payment_mode,
                                            'bank_name' => $trans->bank_name,
                                            'bank_branch' => $trans->bank_branch,
                                            'pay_date' => $now,
                                            'cheque_no' => $trans->cheque_no,
                                            'amount' => $paidamount,
                                            'currency' => $trans->currency,
                                            'defcurr' => $trans->defcurr,
                                            'ex_rate' => $trans->ex_rate,
                                        ]);

                                        $sale->sale_amount_paid = $paidamount;
                                        $sale->save();
                                        if ($sale->sale_amount - $sale->sale_discount == $sale->sale_amount_paid) {
                                            $sale->status = 'Paid';
                                            $sale->time_paid = \Carbon\Carbon::now();
                                            $sale->save();
                                            $invoice->status = 'Paid';
                                            $invoice->save();
                                        } elseif ($sale->sale_amount - $sale->sale_discount > $sale->sale_amount_paid && $sale->sale_amount_paid > 0) {
                                            $sale->status = 'Partially Paid';
                                            $sale->save();
                                        } elseif ($sale->sale_amount - $sale->sale_discount < $sale->sale_amount_paid) {
                                            $sale->status = 'Excess Paid';
                                            $sale->time_paid = \Carbon\Carbon::now();
                                            $sale->save();
                                            $invoice->status = 'Paid';
                                            $invoice->save();
                                        } elseif ($sale->sale_amount_paid == 0) {
                                            $sale->status = 'Unpaid';
                                            $sale->save();
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if (!is_null($request['device_id'])) {
                        DeviceSale::create([
                            'device_id' => $request['device_id'],
                            'an_sale_id' => $sale->id
                        ]);
                    }
                    $cust = Customer::where('id', $sale->customer_id)->whereNotNull('phone')->first();
                    if (!is_null($cust)) {

                        $smsacc = SmsAccount::where('shop_id', $shop->id)->first();
                        if (!is_null($smsacc)) {
                            $senderid = SenderId::where('sms_account_id', $smsacc->id)->where('auto_sms', true)->first();
                            if (!is_null($senderid)) {
                                $autotemp = SmsTemplate::where('shop_id', $shop->id)->where('is_auto_sms', true)->where('temp_for', 'sale')->first();
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
                                    $amount_due = $sale->sale_amount - ($sale->amount_paid + $sale->sale_discount + $sale->adjustment);
                                    $sms = str_replace('{customer_name}', $cust->name, $message);
                                    $sms1 = str_replace('{sale_date}', date('d, M Y', strtotime($cust->sale_date)), $sms);
                                    $sms2 = str_replace('{due_date}', $due_date, $sms1);
                                    $sms3 = str_replace('{invoice_no}', $invoice_no, $sms2);
                                    $msg = str_replace('{amount_due}', number_format($amount_due), $sms3);

                                    dispatch(new SendSMS($smsacc->username, $smsacc->password, $senderid->name, $phone, $msg));
                                    dd('sent');
                                }
                            }
                        }
                    }

                    if ($request['issue_vfd'] == 'on') {
                        $this->issueVFD($sale->id);
                    }
                    if ($request['print_receipt'] == 'on') {
                        return redirect('show/' . encrypt($sale->id));
                    } else {
                        return redirect('pos')->with('success', 'Your Data was submitted successfully');
                    }
                } else {
                    return redirect()->back()->with('warning', 'Please Select at least one Product to continue!.');
                }
            } elseif ($shop->business_type_id == 4) {
                $servitems = ServiceItemTemp::where('sale_temp_id', $saletemp->id)->get();
                $proditems = SaleItemTemp::where('sale_temp_id', $saletemp->id)->get();

                if ($servitems->count() > 0 || $proditems->count() > 0) {

                    $sale = AnSale::create([
                        'customer_id' => $saletemp->customer_id,
                        'shop_id' => $shop->id,
                        'user_id' => $user->id,
                        'sale_discount' => 0,
                        'sale_amount_paid' => $amount_paid,
                        'currency' => $saletemp->currency,
                        'defcurr' => $saletemp->defcurr,
                        'ex_rate' => $saletemp->ex_rate,
                        'comments' => $saletemp->comments,
                        'status' => 'Unpaid',
                        'pay_type' => $saletemp->pay_type,
                        'time_created' => $now,
                        'sale_type' => $saletemp->sale_type,
                        'sale_no' => $sale_no,
                    ]);


                    $servsale_amount = 0;
                    $servsale_discount = 0;
                    $servtax_amount = 0;
                    $prodsale_amount = 0;
                    $prodsale_discount = 0;
                    $prodtax_amount = 0;

                    if (!is_null($servitems)) {

                        foreach ($servitems as $key => $value) {
                            $shop_service = $shop->services()->where('service_id', $value->service_id)->first();
                            $servitemData = new ServiceSaleItem;
                            $servitemData->an_sale_id = $sale->id;
                            $servitemData->service_id = $value->service_id;
                            $servitemData->no_of_repeatition = $value->no_of_repeatition;
                            $servitemData->price = $value->price;
                            $servitemData->total = $value->total;
                            $servitemData->discount = $value->discount;
                            $servitemData->total_discount = $value->discount * $servitemData->no_of_repeatition;
                            if ($value->vat_amount > 0) {
                                $servitemData->tax_amount = $value->vat_amount;
                            }
                            $servitemData->time_created = $now;
                            $servitemData->save();
                        }

                        $servsale_amount = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total');
                        $servsale_discount = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total_discount');
                        $servtax_amount = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('tax_amount');
                    }

                    if (!is_null($proditems)) {
                        $temps = array();
                        $valid = array();
                        foreach ($proditems as $key => $value) {
                            $shop_product = $shop->products()->where('product_id', $value->product_id)->first();
                            if (is_null($shop_product)) {
                                array_push($valid, $key + 1);
                            }
                            if ($value->quantity_sold == 0) {
                                array_push($temps, $value->quantity_sold);
                            }
                        }

                        if (!empty($temps)) {
                            return redirect()->back()->with('warning', 'Please update the quantity of each item to continue');
                        } else if (!empty($valid)) {
                            return redirect()->back()->with('warning', 'You have selected Product/Products which are not registered for this shop. Please review your products and try again.');
                        } else {

                            foreach ($proditems as $key => $value) {
                                $shop_product = $shop->products()->where('product_id', $value->product_id)->first();

                                if (!is_null($shop_product)) {
                                    $punit = ProductUnit::find($value->product_unit_id);
                                    $quantity_sold = $value->quantity_sold * $punit->qty_equal_to_basic;
                                    $price_per_unit = $value->price_per_unit / $punit->qty_equal_to_basic;
                                    $unit_discount = $value->discount / $punit->qty_equal_to_basic;

                                    $lateststock = Stock::where('product_id', $value->product_id)->where('shop_id', $shop->id)->latest()->first();
                                    if (!is_null($lateststock)) {

                                        $qtysoldlog = LatestStockSoldLog::where('stock_id', $lateststock->id)->where('shop_id', $shop->id)->first();

                                        if ($value->used_stock == 'Old') {

                                            $stockdiff = 0;
                                            if (!is_null($qtysoldlog)) {
                                                $stockdiff = $shop_product->pivot->in_stock - ($lateststock->quantity_in - $qtysoldlog->qty_out);
                                            } else {
                                                $stockdiff = $shop_product->pivot->in_stock - $lateststock->quantity_in;
                                            }

                                            if ($stockdiff > 0 && $stockdiff < $quantity_sold && $shop_product->pivot->buying_per_unit != $lateststock->buying_per_unit) {

                                                $old_quantity_sold = $stockdiff;
                                                $new_quantiy_sold = $quantity_sold - $stockdiff;

                                                $saleitemDataold = new AnSaleItem;
                                                $saleitemDataold->shop_id = $shop->id;
                                                $saleitemDataold->an_sale_id = $sale->id;
                                                $saleitemDataold->product_id = $value->product_id;
                                                $saleitemDataold->product_unit_id = $punit->id;
                                                $saleitemDataold->quantity_sold = $old_quantity_sold;
                                                $saleitemDataold->buying_per_unit = $value->buying_per_unit;
                                                $saleitemDataold->buying_price = $value->buying_per_unit * $old_quantity_sold;
                                                $saleitemDataold->price_per_unit = $price_per_unit;
                                                $saleitemDataold->price = $price_per_unit * $old_quantity_sold;
                                                $saleitemDataold->discount = $unit_discount;
                                                $saleitemDataold->total_discount = $saleitemDataold->discount * $saleitemDataold->quantity_sold;

                                                $saleitemDataold->time_created = $now;
                                                if ($value->vat_amount > 0) {
                                                    $saleitemDataold->tax_amount = $value->vat_amount;
                                                    $saleitemDataold->input_tax = $saleitemDataold->buying_price * (($settings->tax_rate / 100) / (1 + ($settings->tax_rate / 100)));
                                                }
                                                $saleitemDataold->sold_in = $value->sold_in;
                                                $saleitemDataold->save();

                                                //New Stock
                                                $saleitemDatanew = new AnSaleItem;
                                                $saleitemDatanew->shop_id = $shop->id;
                                                $saleitemDatanew->an_sale_id = $sale->id;
                                                $saleitemDatanew->product_id = $value->product_id;
                                                $saleitemDatanew->product_unit_id = $punit->id;
                                                $saleitemDatanew->quantity_sold = $new_quantiy_sold;
                                                $saleitemDatanew->buying_per_unit = $lateststock->buying_per_unit;
                                                $saleitemDatanew->buying_price = $lateststock->buying_per_unit * $new_quantiy_sold;
                                                $saleitemDatanew->price_per_unit = $price_per_unit;
                                                $saleitemDatanew->price = $price_per_unit * $new_quantiy_sold;
                                                $saleitemDatanew->discount = $unit_discount;
                                                $saleitemDatanew->total_discount = $saleitemDatanew->discount * $saleitemDatanew->quantity_sold;

                                                $saleitemDatanew->time_created = $now;
                                                if ($value->vat_amount > 0) {
                                                    $saleitemDatanew->tax_amount = $value->vat_amount;
                                                    $saleitemDatanew->input_tax = $saleitemDatanew->buying_price * (($settings->tax_rate / 100) / (1 + ($settings->tax_rate / 100)));
                                                }
                                                $saleitemDatanew->sold_in = $value->sold_in;
                                                $saleitemDatanew->save();
                                            } else {
                                                $saleitemData = new AnSaleItem;
                                                $saleitemData->shop_id = $shop->id;
                                                $saleitemData->an_sale_id = $sale->id;
                                                $saleitemData->product_id = $value->product_id;
                                                $saleitemData->product_unit_id = $punit->id;
                                                $saleitemData->quantity_sold = $quantity_sold;
                                                $saleitemData->buying_per_unit = $value->buying_per_unit;
                                                $saleitemData->buying_price = $value->buying_price;
                                                $saleitemData->price_per_unit = $price_per_unit;
                                                $saleitemData->price = $saleitemData->price_per_unit * $saleitemData->quantity_sold;
                                                $saleitemData->discount = $unit_discount;
                                                $saleitemData->total_discount = $saleitemData->discount * $saleitemData->quantity_sold;
                                                $saleitemData->time_created = $now;
                                                if ($value->vat_amount > 0) {
                                                    $saleitemData->tax_amount = $value->vat_amount;
                                                    $saleitemData->input_tax = $saleitemData->buying_price * (($settings->tax_rate / 100) / (1 + ($settings->tax_rate / 100)));
                                                }
                                                $saleitemData->sold_in = $value->sold_in;
                                                $saleitemData->save();
                                            }
                                        } else {

                                            $newstockqty = 0;
                                            if (!is_null($qtysoldlog)) {
                                                $newstockqty = $lateststock->quantity_in - $qtysoldlog->qty_out;
                                            } else {
                                                $newstockqty = $lateststock->quantity_in;

                                                $qtysoldlog = LatestStockSoldLog::create([
                                                    'stock_id' => $lateststock->id,
                                                    'shop_id' => $shop->id,
                                                    'qty_in' => $lateststock->quantity_in,
                                                    'qty_out' => 0,
                                                    'time_created' => $now,
                                                ]);
                                            }

                                            if ($newstockqty < $quantity_sold && $shop_product->pivot->buying_per_unit != $lateststock->buying_per_unit) {

                                                $old_quantity_sold = $quantity_sold - $newstockqty;
                                                $new_quantiy_sold = $newstockqty;

                                                $saleitemDataold = new AnSaleItem;
                                                $saleitemDataold->shop_id = $shop->id;
                                                $saleitemDataold->an_sale_id = $sale->id;
                                                $saleitemDataold->product_id = $value->product_id;
                                                $saleitemDataold->product_unit_id = $punit->id;
                                                $saleitemDataold->quantity_sold = $old_quantity_sold;
                                                $saleitemDataold->buying_per_unit = $shop_product->pivot->buying_per_unit;
                                                $saleitemDataold->buying_price = $saleitemDataold->buying_per_unit * $old_quantity_sold;
                                                $saleitemDataold->price_per_unit = $price_per_unit;
                                                $saleitemDataold->price = $price_per_unit * $old_quantity_sold;
                                                $saleitemDataold->discount = $unit_discount;
                                                $saleitemDataold->total_discount = $saleitemDataold->discount * $saleitemDataold->quantity_sold;

                                                $saleitemDataold->time_created = $now;
                                                if ($value->vat_amount > 0) {
                                                    $saleitemDataold->tax_amount = $value->vat_amount;
                                                    $saleitemDataold->input_tax = $saleitemDataold->buying_price * (($settings->tax_rate / 100) / (1 + ($settings->tax_rate / 100)));
                                                }
                                                $saleitemDataold->sold_in = $value->sold_in;
                                                $saleitemDataold->save();

                                                //New Stock
                                                $saleitemDatanew = new AnSaleItem;
                                                $saleitemDatanew->shop_id = $shop->id;
                                                $saleitemDatanew->an_sale_id = $sale->id;
                                                $saleitemDatanew->product_id = $value->product_id;
                                                $saleitemDatanew->product_unit_id = $punit->id;
                                                $saleitemDatanew->quantity_sold = $new_quantiy_sold;
                                                $saleitemDatanew->buying_per_unit = $lateststock->buying_per_unit;
                                                $saleitemDatanew->buying_price = $lateststock->buying_per_unit * $new_quantiy_sold;
                                                $saleitemDatanew->price_per_unit = $price_per_unit;
                                                $saleitemDatanew->price = $price_per_unit * $new_quantiy_sold;
                                                $saleitemDatanew->discount = $unit_discount;
                                                $saleitemDatanew->total_discount = $saleitemDatanew->discount * $saleitemDatanew->quantity_sold;

                                                $saleitemDatanew->time_created = $now;
                                                if ($value->vat_amount > 0) {
                                                    $saleitemDatanew->tax_amount = $value->vat_amount;
                                                    $saleitemDatanew->input_tax = $saleitemDatanew->buying_price * (($settings->tax_rate / 100) / (1 + ($settings->tax_rate / 100)));
                                                }
                                                $saleitemDatanew->sold_in = $value->sold_in;
                                                $saleitemDatanew->save();

                                                $qtysoldlog->qty_out = $qtysoldlog->qty_out + $new_quantiy_sold;
                                                $qtysoldlog->save();
                                            } else {
                                                $saleitemData = new AnSaleItem;
                                                $saleitemData->shop_id = $shop->id;
                                                $saleitemData->an_sale_id = $sale->id;
                                                $saleitemData->product_id = $value->product_id;
                                                $saleitemData->product_unit_id = $punit->id;
                                                $saleitemData->quantity_sold = $quantity_sold;
                                                $saleitemData->buying_per_unit = $value->buying_per_unit;
                                                $saleitemData->buying_price = $value->buying_price;
                                                $saleitemData->price_per_unit = $price_per_unit;
                                                $saleitemData->price = $saleitemData->price_per_unit * $saleitemData->quantity_sold;
                                                $saleitemData->discount = $unit_discount;
                                                $saleitemData->total_discount = $saleitemData->discount * $saleitemData->quantity_sold;
                                                $saleitemData->time_created = $now;
                                                if ($value->vat_amount > 0) {
                                                    $saleitemData->tax_amount = $value->vat_amount;
                                                    $saleitemData->input_tax = $saleitemData->buying_price * (($settings->tax_rate / 100) / (1 + ($settings->tax_rate / 100)));
                                                }
                                                $saleitemData->sold_in = $value->sold_in;
                                                $saleitemData->save();

                                                $qtysoldlog->qty_out = $qtysoldlog->qty_out + $saleitemData->quantity_sold;
                                                $qtysoldlog->save();
                                            }
                                        }
                                    } else {
                                        $saleitemData = new AnSaleItem;
                                        $saleitemData->shop_id = $shop->id;
                                        $saleitemData->an_sale_id = $sale->id;
                                        $saleitemData->product_id = $value->product_id;
                                        $saleitemData->product_unit_id = $punit->id;
                                        $saleitemData->quantity_sold = $quantity_sold;
                                        $saleitemData->buying_per_unit = $value->buying_per_unit;
                                        $saleitemData->buying_price = $value->buying_price;
                                        $saleitemData->price_per_unit = $price_per_unit;
                                        $saleitemData->price = $saleitemData->price_per_unit * $saleitemData->quantity_sold;
                                        $saleitemData->discount = $unit_discount;
                                        $saleitemData->total_discount = $saleitemData->discount * $saleitemData->quantity_sold;
                                        $saleitemData->time_created = $now;
                                        if ($value->vat_amount > 0) {
                                            $saleitemData->tax_amount = $value->vat_amount;
                                            $saleitemData->input_tax = $saleitemData->buying_price * (($settings->tax_rate / 100) / (1 + ($settings->tax_rate / 100)));
                                        }
                                        $saleitemData->sold_in = $value->sold_in;
                                        $saleitemData->save();
                                    }

                                    $stock_in = Stock::where('product_id', $value->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                                    $sold = AnSaleItem::where('product_id', $value->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                                    $damaged = ProdDamage::where('product_id', $value->product_id)->where('shop_id', $shop->id)->sum('quantity');
                                    $tranfered =  TransferOrderItem::where('product_id', $value->product_id)->where('shop_id', $shop->id)->sum('quantity');

                                    $returned = SaleReturnItem::where('product_id', $value->product_id)->where('shop_id', $shop->id)->sum('quantity');

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

                                    //Updatting price if the old stock is finished
                                    $prodshop = $shop->products()->where('product_id', $value->product_id)->first();

                                    $lateststock = Stock::where('product_id', $value->product_id)->where('shop_id', $shop->id)->latest()->first();
                                    if (!is_null($lateststock)) {

                                        $qtysoldlog = LatestStockSoldLog::where('stock_id', $lateststock->id)->where('shop_id', $shop->id)->first();

                                        $newstdiff = 0;
                                        if (!is_null($qtysoldlog)) {
                                            $newstdiff = $instock - ($lateststock->quantity_in - $qtysoldlog->qty_out);
                                            if ($qtysoldlog->qty_in - $qtysoldlog->qty_out == 0) {
                                                $qtysoldlog->delete();
                                            }
                                        } else {
                                            $newstdiff = $instock - $lateststock->quantity_in;
                                        }

                                        if ($newstdiff <= 0) {
                                            $prodshop->pivot->in_stock = $instock;
                                            $prodshop->pivot->buying_per_unit = $lateststock->buying_per_unit;
                                            $prodshop->pivot->save();
                                        } else {
                                            $prodshop->pivot->in_stock = $instock;
                                            $prodshop->pivot->save();
                                        }
                                    } else {
                                        $prodshop->pivot->in_stock = $instock;
                                        $prodshop->pivot->save();
                                    }
                                }
                            }

                            $prodsale_amount = AnSaleItem::where('an_sale_id', $sale->id)->sum('price');
                            $prodsale_discount = AnSaleItem::where('an_sale_id', $sale->id)->sum('total_discount');
                            $prodtax_amount = AnSaleItem::where('an_sale_id', $sale->id)->sum('tax_amount');
                            // $proddiscount = AnSaleItem::where('an_sale_id', $sale->id)->sum('total_discount');
                        }

                        // $sale = AnSale::find($sale->id);
                        $sale->sale_amount = ($servsale_amount + $prodsale_amount) + ($servtax_amount + $prodtax_amount);
                        $sale->sale_discount = ($servsale_discount + $prodsale_discount);
                        if ($request['sale_type'] == 'cash') {
                            $amount_paid = ($sale->sale_amount - $sale->sale_discount);
                            $sale->sale_amount_paid = $amount_paid;

                            $maxrec_no = SalePayment::where('shop_id', $shop->id)->orderBy('created_at', 'desc')->first();
                            $receipt_no = 0;
                            if (!is_null($maxrec_no)) {
                                $receipt_no = $maxrec_no->receipt_no + 1;
                            } else {
                                $receipt_no = 1;
                            }
                            $payment = SalePayment::create([
                                'an_sale_id' => $sale->id,
                                'shop_id' => $shop->id,
                                'receipt_no' => $receipt_no,
                                'pay_mode' => $pay_type,
                                'bank_name' => $bank_name,
                                'bank_branch' => $request['bank_branch'],
                                'pay_date' => $now,
                                'cheque_no' => $cheque_no,
                                'amount' => $amount_paid,
                                'currency' => $saletemp->currency,
                                'defcurr' => $saletemp->defcurr,
                                'ex_rate' => $saletemp->ex_rate,
                            ]);
                        } else {
                            if ($shop->subscription_type_id == 1) {

                                $payment = null;
                                if ($amount_paid > 0) {
                                    $maxrec_no = SalePayment::where('shop_id', $shop->id)->orderBy('created_at', 'desc')->first();
                                    $receipt_no = 0;
                                    if (!is_null($maxrec_no)) {
                                        $receipt_no = $maxrec_no->receipt_no + 1;
                                    } else {
                                        $receipt_no = 1;
                                    }
                                    $payment = SalePayment::create([
                                        'an_sale_id' => $sale->id,
                                        'shop_id' => $shop->id,
                                        'receipt_no' => $receipt_no,
                                        'pay_mode' => $pay_type,
                                        'bank_name' => $bank_name,
                                        'bank_branch' => $request['bank_branch'],
                                        'pay_date' => $now,
                                        'cheque_no' => $cheque_no,
                                        'amount' => $amount_paid,
                                        'currency' => $saletemp->currency,
                                        'defcurr' => $saletemp->defcurr,
                                        'ex_rate' => $saletemp->ex_rate,
                                    ]);
                                }
                            }
                        }
                        $sale->tax_amount = ($servtax_amount + $prodtax_amount);
                        $sale->save();

                        if (($sale->sale_amount - $sale->sale_discount) == $sale->sale_amount_paid) {
                            $sale->status = 'Paid';
                            $sale->time_paid = \Carbon\Carbon::now();
                            $sale->save();
                        } elseif (($sale->sale_amount - $sale->sale_discount) > $sale->sale_amount_paid && $sale->sale_amount_paid > 0) {
                            $sale->status = 'Partially Paid';
                            $sale->save();
                        } elseif ($sale->sale_amount - $sale->sale_discount < $sale->sale_amount_paid) {
                            $sale->status = 'Excess Paid';
                            $sale->time_paid = \Carbon\Carbon::now();
                            $sale->save();
                        } elseif ($sale->sale_amount_paid == 0) {
                            $sale->status = 'Unpaid';
                            $sale->save();
                        }

                        $customer = Customer::find($sale->customer_id);
                        //delete all data on SaleItemTemp model
                        // SaleItemTemp::truncate();
                        $temp_items = ServiceItemTemp::where('sale_temp_id', $saletemp->id)->get();
                        foreach ($temp_items as $key => $item) {
                            $item->delete();
                        }
                        $saletemp_items = SaleItemTemp::where('sale_temp_id', $saletemp->id)->get();
                        foreach ($saletemp_items as $key => $item) {
                            $item->delete();
                        }
                        $saletemp->delete();

                        $items = ServiceSaleItem::where('an_sale_id',  $sale->id)->join('services', 'services.id', '=', 'service_sale_items.service_id')->get();
                        $date = \Carbon\Carbon::now()->toDayDateTimeString();

                        $recno = AnSale::where('shop_id', $shop->id)->count();

                        if ($shop->subscription_type_id >= 2) {

                            if ($request['sale_type'] == 'credit') {
                                if (!is_null($request['inv_no'])) {
                                    $invoice = Invoice::create([
                                        'inv_no' => $request['inv_no'],
                                        'shop_id' => $shop->id,
                                        'user_id' => $user->id,
                                        'an_sale_id' => $sale->id,
                                        'due_date' => $request['due_date'],
                                        'status' => 'Pending',
                                        'note' => $request['comments']
                                    ]);

                                    $invoice->created_at = $now;
                                    $invoice->save();
                                } else {
                                    $max_no = Invoice::where('shop_id', $shop->id)->latest()->first();
                                    $invoice_no = 0;
                                    if (!is_null($max_no)) {
                                        $invoice_no = (int)$max_no->inv_no + 1;
                                    } else {
                                        $invoice_no = 1;
                                    }
                                    $invoice = Invoice::create([
                                        'inv_no' => $invoice_no,
                                        'shop_id' => $shop->id,
                                        'user_id' => $user->id,
                                        'an_sale_id' => $sale->id,
                                        'due_date' => $request['due_date'],
                                        'status' => 'Pending',
                                        'note' => $request['comments']
                                    ]);

                                    $invoice->created_at = $now;
                                    $invoice->save();
                                }

                                $acctrans = new CustomerTransaction();
                                $acctrans->shop_id = $shop->id;
                                $acctrans->user_id = $user->id;
                                $acctrans->customer_id = $sale->customer_id;
                                $acctrans->invoice_id = $invoice->id;
                                $acctrans->invoice_no = $invoice->inv_no;
                                $acctrans->amount = ($sale->sale_amount - $sale->sale_discount);
                                $acctrans->currency = $saletemp->currency;
                                $acctrans->defcurr = $saletemp->defcurr;
                                $acctrans->ex_rate = $saletemp->ex_rate;
                                $acctrans->date = $now;
                                $acctrans->save();

                                $utransactions = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $customer->id)->whereNotNull('receipt_no')->where('is_utilized', false)->where('is_deleted', false)->get();

                                if (!is_null($utransactions)) {
                                    foreach ($utransactions as $key => $trans) {
                                        $rem_amount = $trans->payment - ($trans->trans_invoice_amount + $trans->trans_ob_amount + $trans->trans_credit_amount);
                                        if ($rem_amount > 0) {
                                            $paidamount = 0;
                                            if ($rem_amount > ($sale->sale_amount - $sale->sale_discount)) {
                                                $paidamount = $sale->sale_amount - $sale->sale_discount;
                                                $trans->trans_invoice_amount = $trans->trans_invoice_amount + $paidamount;
                                                $trans->save();
                                            } else {
                                                $paidamount = $rem_amount;
                                                $trans->trans_invoice_amount = $trans->trans_invoice_amount + $paidamount;
                                                $trans->is_utilized = true;
                                                $trans->save();
                                            }

                                            $payment = SalePayment::create([
                                                'an_sale_id' => $sale->id,
                                                'shop_id' => $shop->id,
                                                'trans_id' => $trans->id,
                                                'receipt_no' => $trans->receipt_no,
                                                'pay_mode' => $trans->payment_mode,
                                                'bank_name' => $trans->bank_name,
                                                'bank_branch' => $trans->bank_branch,
                                                'pay_date' => $now,
                                                'cheque_no' => $trans->cheque_no,
                                                'amount' => $paidamount,
                                                'currency' => $trans->currency,
                                                'defcurr' => $trans->defcurr,
                                                'ex_rate' => $trans->ex_rate,
                                            ]);

                                            $sale->sale_amount_paid = $paidamount;
                                            $sale->save();
                                            if ($sale->sale_amount - $sale->sale_discount == $sale->sale_amount_paid) {
                                                $sale->status = 'Paid';
                                                $sale->time_paid = \Carbon\Carbon::now();
                                                $sale->save();
                                                $invoice->status = 'Paid';
                                                $invoice->save();
                                            } elseif ($sale->sale_amount - $sale->sale_discount > $sale->sale_amount_paid && $sale->sale_amount_paid > 0) {
                                                $sale->status = 'Partially Paid';
                                                $sale->save();
                                            } elseif ($sale->sale_amount - $sale->sale_discount < $sale->sale_amount_paid) {
                                                $sale->status = 'Excess Paid';
                                                $sale->time_paid = \Carbon\Carbon::now();
                                                $sale->save();
                                                $invoice->status = 'Paid';
                                                $invoice->save();
                                            } elseif ($sale->sale_amount_paid == 0) {
                                                $sale->status = 'Unpaid';
                                                $sale->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }


                        $cust = Customer::where('id', $sale->customer_id)->whereNotNull('phone')->first();
                        if (!is_null($cust)) {

                            $smsacc = SmsAccount::where('shop_id', $shop->id)->first();
                            if (!is_null($smsacc)) {
                                $senderid = SenderId::where('sms_account_id', $smsacc->id)->where('auto_sms', true)->first();
                                if (!is_null($senderid)) {
                                    $autotemp = SmsTemplate::where('shop_id', $shop->id)->where('is_auto_sms', true)->where('temp_for', 'sale')->first();
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
                                        $amount_due = $sale->sale_amount - ($sale->amount_paid + $sale->sale_discount + $sale->adjustment);
                                        $sms = str_replace('{customer_name}', $cust->name, $message);
                                        $sms1 = str_replace('{sale_date}', date('d, M Y', strtotime($cust->sale_date)), $sms);
                                        $sms2 = str_replace('{due_date}', $due_date, $sms1);
                                        $sms3 = str_replace('{invoice_no}', $invoice_no, $sms2);
                                        $msg = str_replace('{amount_due}', number_format($amount_due), $sms3);

                                        dispatch(new SendSMS($smsacc->username, $smsacc->password, $senderid->name, $phone, $msg));
                                        dd('sent');
                                    }
                                }
                            }
                        }

                        if ($request['issue_vfd'] == 'on') {
                            $this->issueVFD($sale->id);
                        }
                        if ($request['print_receipt'] == 'on') {
                            return redirect('show/' . encrypt($sale->id));
                        } else {
                            return redirect('pos')->with('success', 'Your Data was submitted successfully');
                        }
                    }
                } else {
                    return redirect()->back()->with('warning', 'Please Select at least one Product to continue!.');
                }
            } else {
                $saleitems = SaleItemTemp::where('sale_temp_id', $saletemp->id)->get();

                // return $saleitems->count();
                if ($saleitems->count() > 0) {
                    $temps = array();
                    $valid = array();
                    foreach ($saleitems as $key => $value) {
                        $shop_product = $shop->products()->where('product_id', $value->product_id)->first();
                        if (is_null($shop_product)) {
                            array_push($valid, $key + 1);
                        }
                        if ($value->quantity_sold == 0) {
                            array_push($temps, $value->quantity_sold);
                        }
                    }

                    if (!empty($temps)) {
                        return redirect()->back()->with('warning', 'Please update the quantity of each item to continue');
                    } else if (!empty($valid)) {
                        return redirect()->back()->with('warning', 'You have selected Product/Products which are not registered for this shop. Please review your products and try again.');
                    } else {

                        $sale = AnSale::create([
                            'customer_id' => $saletemp->customer_id,
                            'shop_id' => Session::get('shop_id'),
                            'user_id' => Auth::user()->id,
                            'sale_amount_paid' => $amount_paid,
                            'currency' => $saletemp->currency,
                            'defcurr' => $saletemp->defcurr,
                            'ex_rate' => $saletemp->ex_rate,
                            'comments' => $saletemp->comments,
                            'status' => 'Unpaid',
                            'pay_type' => $saletemp->pay_type,
                            'time_created' => $now,
                            'sale_type' => $saletemp->sale_type,
                            'sale_no' => $sale_no,
                        ]);


                        foreach ($saleitems as $key => $value) {
                            $shop_product = $shop->products()->where('product_id', $value->product_id)->first();

                            if (!is_null($shop_product)) {
                                $punit = ProductUnit::find($value->product_unit_id);
                                $quantity_sold = $value->quantity_sold * $punit->qty_equal_to_basic;
                                $price_per_unit = $value->price_per_unit / $punit->qty_equal_to_basic;
                                $unit_discount = $value->discount / $punit->qty_equal_to_basic;

                                $lateststock = Stock::where('product_id', $value->product_id)->where('shop_id', $shop->id)->latest()->first();
                                if (!is_null($lateststock)) {

                                    $qtysoldlog = LatestStockSoldLog::where('stock_id', $lateststock->id)->where('shop_id', $shop->id)->first();

                                    if ($value->used_stock == 'Old') {

                                        $stockdiff = 0;
                                        if (!is_null($qtysoldlog)) {
                                            $stockdiff = $shop_product->pivot->in_stock - ($lateststock->quantity_in - $qtysoldlog->qty_out);
                                        } else {
                                            $stockdiff = $shop_product->pivot->in_stock - $lateststock->quantity_in;
                                        }

                                        if ($stockdiff > 0 && $stockdiff < $quantity_sold && $shop_product->pivot->buying_per_unit != $lateststock->buying_per_unit) {

                                            $old_quantity_sold = $stockdiff;
                                            $new_quantiy_sold = $quantity_sold - $stockdiff;

                                            $saleitemDataold = new AnSaleItem;
                                            $saleitemDataold->shop_id = $shop->id;
                                            $saleitemDataold->an_sale_id = $sale->id;
                                            $saleitemDataold->product_id = $value->product_id;
                                            $saleitemDataold->product_unit_id = $value->product_unit_id;
                                            $saleitemDataold->quantity_sold = $old_quantity_sold;
                                            $saleitemDataold->buying_per_unit = $value->buying_per_unit;
                                            $saleitemDataold->buying_price = $value->buying_per_unit * $old_quantity_sold;
                                            $saleitemDataold->price_per_unit = $price_per_unit;
                                            $saleitemDataold->price = $price_per_unit * $old_quantity_sold;
                                            $saleitemDataold->discount = $unit_discount;
                                            $saleitemDataold->total_discount = $saleitemDataold->discount * $saleitemDataold->quantity_sold;

                                            $saleitemDataold->time_created = $now;
                                            if ($value->vat_amount > 0) {
                                                $saleitemDataold->tax_amount = $value->vat_amount;
                                                $saleitemDataold->input_tax = $saleitemDataold->buying_price * (($settings->tax_rate / 100) / (1 + ($settings->tax_rate / 100)));
                                            }
                                            $saleitemDataold->sold_in = $value->sold_in;
                                            $saleitemDataold->save();

                                            //New Stock
                                            $saleitemDatanew = new AnSaleItem;
                                            $saleitemDatanew->shop_id = $shop->id;
                                            $saleitemDatanew->an_sale_id = $sale->id;
                                            $saleitemDatanew->product_id = $value->product_id;
                                            $saleitemDatanew->product_unit_id = $value->product_unit_id;
                                            $saleitemDatanew->quantity_sold = $new_quantiy_sold;
                                            $saleitemDatanew->buying_per_unit = $lateststock->buying_per_unit;
                                            $saleitemDatanew->buying_price = $lateststock->buying_per_unit * $new_quantiy_sold;
                                            $saleitemDatanew->price_per_unit = $price_per_unit;
                                            $saleitemDatanew->price = $price_per_unit * $new_quantiy_sold;
                                            $saleitemDatanew->discount = $unit_discount;
                                            $saleitemDatanew->total_discount = $saleitemDatanew->discount * $saleitemDatanew->quantity_sold;

                                            $saleitemDatanew->time_created = $now;
                                            if ($value->vat_amount > 0) {
                                                $saleitemDatanew->tax_amount = $value->vat_amount;
                                                $saleitemDatanew->input_tax = $saleitemDatanew->buying_price * (($settings->tax_rate / 100) / (1 + ($settings->tax_rate / 100)));
                                            }
                                            $saleitemDatanew->sold_in = $value->sold_in;
                                            $saleitemDatanew->save();
                                        } else {
                                            $saleitemData = new AnSaleItem;
                                            $saleitemData->shop_id = $shop->id;
                                            $saleitemData->an_sale_id = $sale->id;
                                            $saleitemData->product_id = $value->product_id;
                                            $saleitemData->product_unit_id = $value->product_unit_id;
                                            $saleitemData->quantity_sold = $quantity_sold;
                                            $saleitemData->buying_per_unit = $value->buying_per_unit;
                                            $saleitemData->buying_price = $value->buying_price;
                                            $saleitemData->price_per_unit = $price_per_unit;
                                            $saleitemData->price = $saleitemData->price_per_unit * $saleitemData->quantity_sold;
                                            $saleitemData->discount = $unit_discount;
                                            $saleitemData->total_discount = $saleitemData->discount * $saleitemData->quantity_sold;
                                            $saleitemData->time_created = $now;
                                            if ($value->vat_amount > 0) {
                                                $saleitemData->tax_amount = $value->vat_amount;
                                                $saleitemData->input_tax = $saleitemData->buying_price * (($settings->tax_rate / 100) / (1 + ($settings->tax_rate / 100)));
                                            }
                                            $saleitemData->sold_in = $value->sold_in;
                                            $saleitemData->save();
                                        }
                                    } else {

                                        $newstockqty = 0;
                                        if (!is_null($qtysoldlog)) {
                                            $newstockqty = $lateststock->quantity_in - $qtysoldlog->qty_out;
                                        } else {
                                            $newstockqty = $lateststock->quantity_in;

                                            $qtysoldlog = LatestStockSoldLog::create([
                                                'stock_id' => $lateststock->id,
                                                'shop_id' => $shop->id,
                                                'qty_in' => $lateststock->quantity_in,
                                                'qty_out' => 0,
                                                'time_created' => $now,
                                            ]);
                                        }

                                        if ($newstockqty < $quantity_sold && $shop_product->pivot->buying_per_unit != $lateststock->buying_per_unit) {

                                            $old_quantity_sold = $quantity_sold - $newstockqty;
                                            $new_quantiy_sold = $newstockqty;

                                            $saleitemDataold = new AnSaleItem;
                                            $saleitemDataold->shop_id = $shop->id;
                                            $saleitemDataold->an_sale_id = $sale->id;
                                            $saleitemDataold->product_id = $value->product_id;
                                            $saleitemDataold->product_unit_id = $value->product_unit_id;
                                            $saleitemDataold->quantity_sold = $old_quantity_sold;
                                            $saleitemDataold->buying_per_unit = $shop_product->pivot->buying_per_unit;
                                            $saleitemDataold->buying_price = $saleitemDataold->buying_per_unit * $old_quantity_sold;
                                            $saleitemDataold->price_per_unit = $price_per_unit;
                                            $saleitemDataold->price = $price_per_unit * $old_quantity_sold;
                                            $saleitemDataold->discount = $unit_discount;
                                            $saleitemDataold->total_discount = $saleitemDataold->discount * $saleitemDataold->quantity_sold;

                                            $saleitemDataold->time_created = $now;
                                            if ($value->vat_amount > 0) {
                                                $saleitemDataold->tax_amount = $value->vat_amount;
                                                $saleitemDataold->input_tax = $saleitemDataold->buying_price * (($settings->tax_rate / 100) / (1 + ($settings->tax_rate / 100)));
                                            }
                                            $saleitemDataold->sold_in = $value->sold_in;
                                            $saleitemDataold->save();

                                            //New Stock
                                            $saleitemDatanew = new AnSaleItem;
                                            $saleitemDatanew->shop_id = $shop->id;
                                            $saleitemDatanew->an_sale_id = $sale->id;
                                            $saleitemDatanew->product_id = $value->product_id;
                                            $saleitemDatanew->product_unit_id = $value->product_unit_id;
                                            $saleitemDatanew->quantity_sold = $new_quantiy_sold;
                                            $saleitemDatanew->buying_per_unit = $lateststock->buying_per_unit;
                                            $saleitemDatanew->buying_price = $lateststock->buying_per_unit * $new_quantiy_sold;
                                            $saleitemDatanew->price_per_unit = $price_per_unit;
                                            $saleitemDatanew->price = $price_per_unit * $new_quantiy_sold;
                                            $saleitemDatanew->discount = $unit_discount;
                                            $saleitemDatanew->total_discount = $saleitemDatanew->discount * $saleitemDatanew->quantity_sold;

                                            $saleitemDatanew->time_created = $now;
                                            if ($value->vat_amount > 0) {
                                                $saleitemDatanew->tax_amount = $value->vat_amount;
                                                $saleitemDatanew->input_tax = $saleitemDatanew->buying_price * (($settings->tax_rate / 100) / (1 + ($settings->tax_rate / 100)));
                                            }
                                            $saleitemDatanew->sold_in = $value->sold_in;
                                            $saleitemDatanew->save();

                                            $qtysoldlog->qty_out = $qtysoldlog->qty_out + $new_quantiy_sold;
                                            $qtysoldlog->save();
                                        } else {
                                            $saleitemData = new AnSaleItem;
                                            $saleitemData->shop_id = $shop->id;
                                            $saleitemData->an_sale_id = $sale->id;
                                            $saleitemData->product_id = $value->product_id;
                                            $saleitemData->product_unit_id = $value->product_unit_id;
                                            $saleitemData->quantity_sold = $quantity_sold;
                                            $saleitemData->buying_per_unit = $value->buying_per_unit;
                                            $saleitemData->buying_price = $value->buying_price;
                                            $saleitemData->price_per_unit = $price_per_unit;
                                            $saleitemData->price = $saleitemData->price_per_unit * $saleitemData->quantity_sold;
                                            $saleitemData->discount = $unit_discount;
                                            $saleitemData->total_discount = $saleitemData->discount * $saleitemData->quantity_sold;
                                            $saleitemData->time_created = $now;
                                            if ($value->vat_amount > 0) {
                                                $saleitemData->tax_amount = $value->vat_amount;
                                                $saleitemData->input_tax = $saleitemData->buying_price * (($settings->tax_rate / 100) / (1 + ($settings->tax_rate / 100)));
                                            }
                                            $saleitemData->sold_in = $value->sold_in;
                                            $saleitemData->save();

                                            $qtysoldlog->qty_out = $qtysoldlog->qty_out + $saleitemData->quantity_sold;
                                            $qtysoldlog->save();
                                        }
                                    }
                                } else {
                                    $saleitemData = new AnSaleItem;
                                    $saleitemData->shop_id = $shop->id;
                                    $saleitemData->an_sale_id = $sale->id;
                                    $saleitemData->product_id = $value->product_id;
                                    $saleitemData->product_unit_id = $value->product_unit_id;
                                    $saleitemData->quantity_sold = $quantity_sold;
                                    $saleitemData->buying_per_unit = $value->buying_per_unit;
                                    $saleitemData->buying_price = $value->buying_price;
                                    $saleitemData->price_per_unit = $price_per_unit;
                                    $saleitemData->price = $saleitemData->price_per_unit * $saleitemData->quantity_sold;
                                    $saleitemData->discount = $unit_discount;
                                    $saleitemData->total_discount = $saleitemData->discount * $saleitemData->quantity_sold;
                                    $saleitemData->time_created = $now;
                                    if ($value->vat_amount > 0) {
                                        $saleitemData->tax_amount = $value->vat_amount;
                                        $saleitemData->input_tax = $saleitemData->buying_price * (($settings->tax_rate / 100) / (1 + ($settings->tax_rate / 100)));
                                    }
                                    $saleitemData->sold_in = $value->sold_in;
                                    $saleitemData->save();
                                }

                                $stock_in = Stock::where('product_id', $value->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
                                $sold = AnSaleItem::where('product_id', $value->product_id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
                                $damaged = ProdDamage::where('product_id', $value->product_id)->where('shop_id', $shop->id)->sum('quantity');
                                $tranfered =  TransferOrderItem::where('product_id', $value->product_id)->where('shop_id', $shop->id)->sum('quantity');

                                $returned = SaleReturnItem::where('product_id', $value->product_id)->where('shop_id', $shop->id)->sum('quantity');

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

                                //Updatting price if the old stock is finished
                                $prodshop = $shop->products()->where('product_id', $value->product_id)->first();

                                $lateststock = Stock::where('product_id', $value->product_id)->where('shop_id', $shop->id)->latest()->first();
                                if (!is_null($lateststock)) {

                                    $qtysoldlog = LatestStockSoldLog::where('stock_id', $lateststock->id)->where('shop_id', $shop->id)->first();

                                    $newstdiff = 0;
                                    if (!is_null($qtysoldlog)) {
                                        $newstdiff = $instock - ($lateststock->quantity_in - $qtysoldlog->qty_out);
                                        if ($qtysoldlog->qty_in - $qtysoldlog->qty_out == 0) {
                                            $qtysoldlog->delete();
                                        }
                                    } else {
                                        $newstdiff = $instock - $lateststock->quantity_in;
                                    }

                                    if ($newstdiff <= 0) {
                                        $prodshop->pivot->in_stock = $instock;
                                        $prodshop->pivot->buying_per_unit = $lateststock->buying_per_unit;
                                        $prodshop->pivot->save();
                                    } else {
                                        $prodshop->pivot->in_stock = $instock;
                                        $prodshop->pivot->save();
                                    }
                                } else {
                                    $prodshop->pivot->in_stock = $instock;
                                    $prodshop->pivot->save();
                                }
                            }
                        }


                        $sale_amount = AnSaleItem::where('an_sale_id', $sale->id)->sum('price');
                        $sale_discount = AnSaleItem::where('an_sale_id', $sale->id)->sum('total_discount');
                        $tax_amount = AnSaleItem::where('an_sale_id', $sale->id)->sum('tax_amount');
                        // $sale = AnSale::find($sale->id);
                        $sale->sale_amount = $sale_amount + $tax_amount;
                        $sale->sale_discount = $sale_discount;
                        $maxrec_no = SalePayment::where('shop_id', $shop->id)->latest()->first();
                        $receipt_no = 0;
                        if (!is_null($maxrec_no)) {
                            $receipt_no = $maxrec_no->receipt_no + 1;
                        } else {
                            $receipt_no = 1;
                        }
                        if ($request['sale_type'] == 'cash') {
                            $amount_paid = ($sale->sale_amount - $sale->sale_discount);
                            $sale->sale_amount_paid = $amount_paid;

                            $payment = SalePayment::create([
                                'an_sale_id' => $sale->id,
                                'shop_id' => $shop->id,
                                'receipt_no' => $receipt_no,
                                'pay_mode' => $pay_type,
                                'bank_name' => $bank_name,
                                'bank_branch' => $request['bank_branch'],
                                'pay_date' => $now,
                                'cheque_no' => $cheque_no,
                                'amount' => $amount_paid,
                                'currency' => $saletemp->currency,
                                'defcurr' => $saletemp->defcurr,
                                'ex_rate' => $saletemp->ex_rate,
                            ]);
                        } else {
                            if ($shop->subscription_type_id == 1) {
                                $payment = null;
                                if ($amount_paid > 0) {
                                    $payment = SalePayment::create([
                                        'an_sale_id' => $sale->id,
                                        'shop_id' => $shop->id,
                                        'receipt_no' => $receipt_no,
                                        'pay_mode' => $pay_type,
                                        'bank_name' => $bank_name,
                                        'bank_branch' => $request['bank_branch'],
                                        'pay_date' => $now,
                                        'cheque_no' => $cheque_no,
                                        'amount' => $amount_paid,
                                        'currency' => $saletemp->currency,
                                        'defcurr' => $saletemp->defcurr,
                                        'ex_rate' => $saletemp->ex_rate,
                                    ]);
                                }
                            }
                        }

                        $sale->tax_amount = $tax_amount;
                        $sale->save();
                        if ($sale->sale_amount - $sale->sale_discount == $sale->sale_amount_paid) {
                            $sale->status = 'Paid';
                            $sale->time_paid = \Carbon\Carbon::now();
                            $sale->save();
                        } elseif ($sale->sale_amount - $sale->sale_discount > $sale->sale_amount_paid && $sale->sale_amount_paid > 0) {
                            $sale->status = 'Partially Paid';
                            $sale->save();
                        } elseif ($sale->sale_amount - $sale->sale_discount < $sale->sale_amount_paid) {
                            $sale->status = 'Excess Paid';
                            $sale->time_paid = \Carbon\Carbon::now();
                            $sale->save();
                        } elseif ($sale->sale_amount_paid == 0) {
                            $sale->status = 'Unpaid';
                            $sale->save();
                        }

                        $customer = Customer::find($sale->customer_id);
                        //delete all data on SaleItemTemp model
                        // SaleItemTemp::truncate();
                        $temp_items = SaleItemTemp::where('sale_temp_id', $saletemp->id)->get();
                        foreach ($temp_items as $key => $item) {
                            $item->delete();
                        }
                        $saletemp->delete();

                        $items = AnSaleItem::where('an_sale_id',  $sale->id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->get();
                        $date = \Carbon\Carbon::now()->toDayDateTimeString();

                        $recno = AnSale::where('shop_id', $shop->id)->count();

                        if ($shop->subscription_type_id >= 2) {

                            if ($request['sale_type'] == 'credit') {
                                $invoice = null;
                                if (!is_null($request['inv_no'])) {
                                    $invoice = Invoice::create([
                                        'inv_no' => $request['inv_no'],
                                        'shop_id' => $shop->id,
                                        'user_id' => $user->id,
                                        'an_sale_id' => $sale->id,
                                        'due_date' => $request['due_date'],
                                        'status' => 'Pending',
                                        'note' => $request['comments']
                                    ]);

                                    $invoice->created_at = $now;
                                    $invoice->save();
                                } else {
                                    $max_no = Invoice::where('shop_id', $shop->id)->latest()->first();
                                    $invoice_no = 0;
                                    if (!is_null($max_no)) {
                                        $invoice_no = (int)$max_no->inv_no + 1;
                                    } else {
                                        $invoice_no = 1;
                                    }
                                    $invoice = Invoice::create([
                                        'inv_no' => $invoice_no,
                                        'shop_id' => $shop->id,
                                        'user_id' => $user->id,
                                        'an_sale_id' => $sale->id,
                                        'vehicle_no' => $request['vehicle_no'],
                                        'due_date' => $request['due_date'],
                                        'status' => 'Pending',
                                        'note' => $request['comments']
                                    ]);

                                    $invoice->created_at = $now;
                                    $invoice->save();
                                }

                                $acctrans = new CustomerTransaction();
                                $acctrans->shop_id = $shop->id;
                                $acctrans->user_id = $user->id;
                                $acctrans->customer_id = $saletemp->customer_id;
                                $acctrans->invoice_id = $invoice->id;
                                $acctrans->invoice_no = $invoice->inv_no;
                                $acctrans->amount = ($sale->sale_amount - $sale->sale_discount);
                                $acctrans->currency = $saletemp->currency;
                                $acctrans->defcurr = $saletemp->defcurr;
                                $acctrans->ex_rate = $saletemp->ex_rate;
                                $acctrans->date = $now;
                                $acctrans->save();

                                $utransactions = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $customer->id)->whereNotNull('receipt_no')->where('is_utilized', false)->where('is_deleted', false)->get();

                                if (!is_null($utransactions)) {
                                    foreach ($utransactions as $key => $trans) {
                                        $rem_amount = $trans->payment - ($trans->trans_invoice_amount + $trans->trans_ob_amount + $trans->trans_credit_amount);
                                        if ($rem_amount > 0) {
                                            $paidamount = 0;
                                            if ($rem_amount > ($sale->sale_amount - $sale->sale_discount)) {
                                                $paidamount = $sale->sale_amount - $sale->sale_discount;

                                                $trans->trans_invoice_amount = $trans->trans_invoice_amount + $paidamount;
                                                $trans->save();
                                            } else {
                                                $paidamount = $rem_amount;

                                                $trans->trans_invoice_amount = $trans->trans_invoice_amount + $paidamount;
                                                $trans->is_utilized = true;
                                                $trans->save();
                                            }

                                            $payment = SalePayment::create([
                                                'an_sale_id' => $sale->id,
                                                'shop_id' => $shop->id,
                                                'trans_id' => $trans->id,
                                                'receipt_no' => $trans->receipt_no,
                                                'pay_mode' => $trans->payment_mode,
                                                'bank_name' => $trans->bank_name,
                                                'bank_branch' => $trans->bank_branch,
                                                'pay_date' => $now,
                                                'cheque_no' => $trans->cheque_no,
                                                'amount' => $paidamount,
                                                'currency' => $trans->currency,
                                                'defcurr' => $trans->defcurr,
                                                'ex_rate' => $trans->ex_rate,
                                            ]);


                                            $sale->sale_amount_paid = $paidamount;
                                            $sale->save();
                                            if ($sale->sale_amount - $sale->sale_discount == $sale->sale_amount_paid) {
                                                $sale->status = 'Paid';
                                                $sale->time_paid = \Carbon\Carbon::now();
                                                $sale->save();
                                                $invoice->status = 'Paid';
                                                $invoice->save();
                                            } elseif ($sale->sale_amount - $sale->sale_discount > $sale->sale_amount_paid && $sale->sale_amount_paid > 0) {
                                                $sale->status = 'Partially Paid';
                                                $sale->save();
                                            } elseif ($sale->sale_amount - $sale->sale_discount < $sale->sale_amount_paid) {
                                                $sale->status = 'Excess Paid';
                                                $sale->time_paid = \Carbon\Carbon::now();
                                                $sale->save();
                                                $invoice->status = 'Paid';
                                                $invoice->save();
                                            } elseif ($sale->sale_amount_paid == 0) {
                                                $sale->status = 'Unpaid';
                                                $sale->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $cust = Customer::where('id', $sale->customer_id)->whereNotNull('phone')->first();
                        if (!is_null($cust)) {

                            $smsacc = SmsAccount::where('shop_id', $shop->id)->first();
                            if (!is_null($smsacc)) {
                                $senderid = SenderId::where('sms_account_id', $smsacc->id)->where('auto_sms', true)->first();
                                if (!is_null($senderid)) {
                                    $autotemp = SmsTemplate::where('shop_id', $shop->id)->where('is_auto_sms', true)->where('temp_for', 'sale')->first();
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
                                        $amount_due = $sale->sale_amount - ($sale->amount_paid + $sale->sale_discount + $sale->adjustment);
                                        $sms = str_replace('{customer_name}', $cust->name, $message);
                                        $sms1 = str_replace('{sale_date}', date('d, M Y', strtotime($cust->sale_date)), $sms);
                                        $sms2 = str_replace('{due_date}', $due_date, $sms1);
                                        $sms3 = str_replace('{invoice_no}', $invoice_no, $sms2);
                                        $msg = str_replace('{amount_due}', number_format($amount_due), $sms3);

                                        dispatch(new SendSMS($smsacc->username, $smsacc->password, $senderid->name, $phone, $msg));
                                        dd('sent');
                                    }
                                }
                            }
                        }

                        if ($request['issue_vfd'] == 'on') {
                            $this->issueVFD($sale->id);
                        }
                        if ($request['print_receipt'] == 'on') {
                            return redirect('show/' . encrypt($sale->id));
                        } else {
                            return redirect('pos')->with('success', 'Your Data was submitted successfully');
                        }
                    }
                } else {
                    return redirect()->back()->with('warning', 'Please Select at least one Product to continue!.');
                }
            }
        }
    }


    public function cancel()
    {
        $shop = Shop::find(Session::get('shop_id'));

        if ($shop->business_type_id == 3) {
            $temp_items = ServiceItemTemp::where('shop_id', $shop->id)->where('user_id', Auth::user()->id)->get();
            foreach ($temp_items as $key => $item) {
                $item->delete();
            }
        } elseif ($shop->business_type_id == 4) {
            $temp_serv_items = ServiceItemTemp::where('shop_id', $shop->id)->where('user_id', Auth::user()->id)->get();
            foreach ($temp_serv_items as $key => $item) {
                $item->delete();
            }
            $temp_items = SaleItemTemp::where('shop_id', $shop->id)->where('user_id', Auth::user()->id)->get();
            foreach ($temp_items as $key => $item) {
                $item->delete();
            }
        } else {
            $temp_items = SaleItemTemp::where('shop_id', $shop->id)->where('user_id', Auth::user()->id)->get();
            foreach ($temp_items as $key => $item) {
                $item->delete();
            }
        }

        $success = 'Sale was successfully canceled.';
        return redirect('pos')->with('success', $success);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\AnSale  $anSale
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $sale = AnSale::find($id);
        $scount = AnSale::where('shop_id', $shop->id)->where('id', '<', $sale->id)->count();
        $recno = $scount + 1;
        $customer = Customer::find($sale->customer_id);
        $items = AnSaleItem::where('an_sale_id',  $sale->id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->get();
        $date = \Carbon\Carbon::now()->toDayDateTimeString();
        $change = 0;
        $due = 0;
        if ($sale->sale_amount_paid > $sale->sale_amount) {
            $change = $sale->sale_amount_paid - $sale->sale_amount;
        }
        if ($sale->sale_amount_paid <= $sale->sale_amount) {
            $due = $sale->sale_amount - $sale->sale_amount_paid;
        }
        // return Response::json(['sale' => $sale]);
        return response()->json(['shop' => $shop, 'sale' => $sale, 'recno' => $recno, 'customer' => $customer, 'items' => $items, 'date' => $date, 'change' => $change, 'due' => $due]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AnSale  $anSale
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $page = 'Point of Sale';
        $title = 'Point of Sale';
        $title_sw = 'Sehemu ya Kuuzia';
        $shop = Shop::find(Session::get('shop_id'));
        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', false)->first();
        if (!is_null($payment)) {
            $user = Auth::user();
            $sale = AnSale::where('shop_id', $shop->id)->count();
            $customers = Customer::where('shop_id', $shop->id)->orderBy('id', 'desc')->get();

            $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
            $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            $saletemp = SaleTemp::find($request['id']);
            $pendingtemps = SaleTemp::where('sale_temps.shop_id', $shop->id)->where('user_id', $user->id)->whereNotNull('customer_id')->join('customers', 'customers.id', '=', 'sale_temps.customer_id')->select('sale_temps.id as id', 'name', 'sale_temps.created_at as created_at')->get();

            $settings = Setting::where('shop_id', $shop->id)->first();
            if (is_null($settings)) {
                $settings = Setting::create([
                    'shop_id' => $shop->id,
                    'tax_rate' => 18,
                    'inv_no_type' => 'Automatic'
                ]);
            }

            $mindays = 0;
            $date = Carbon::parse($payment->expire_date);
            $now = Carbon::now();
            $status = $date->diffInDays($now);
            $paydate = Carbon::parse($payment->created_at);

            $lastpay = Payment::where('shop_id', $shop->id)->where('is_expired', 1)->where('is_for_module', false)->orderBy('created_at', 'desc')->first();
            if (!is_null($lastpay)) {
                $lastexp = Carbon::parse($lastpay->expire_date);
                $oldpaydate = Carbon::parse($lastpay->created_at);
                $slipdays = $paydate->diffInDays($lastexp);
                // return $slipdays;
                if ($slipdays < 15) {
                    $mindays = $now->diffInDays($oldpaydate);
                } else {
                    $mindays = $now->diffInDays($paydate);
                }
            } else {
                $mindays = $now->diffInDays($paydate);
            }

            if ($mindays < 10) {
                $mindays = 15;
            }

            $products = null;
            if ($settings->is_filling_station) {
                $products = $shop->products()->get();
            }
            $bdetails = BankDetail::where('shop_id', $shop->id)->get();

            $custids = array(
                ['id' => 1, 'name' => 'TIN'],
                ['id' => 2, 'name' => 'Driving License'],
                ['id' => 3, 'name' => 'Voters Number'],
                ['id' => 4, 'name' => 'Passport'],
                ['id' => 5, 'name' => 'NID'],
                ['id' => 6, 'name' => 'NIL'],
                ['id' => 7, 'name' => 'Meter No']
            );

            if ($shop->business_type_id == 3) {
                $devices = Device::where('shop_id', $shop->id)->get();
                $grades = Grade::where('shop_id', $shop->id)->get();
                return view('sales.service-pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'sale', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'devices', 'bdetails', 'mindays', 'grades', 'custids', 'currencies'));
            } elseif ($shop->business_type_id == 4) {
                return view('sales.both-pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'sale', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'bdetails', 'mindays', 'custids', 'products', 'currencies'));
            } else {
                return view('sales.pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'sale', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'bdetails', 'mindays', 'products', 'custids', 'currencies'));
            }
        } else {
            $info = 'Dear customer your account is not activated please make payment and activate now.';
            return redirect('verify-payment')->with('info', $info);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AnSale  $anSale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $saletemp = SaleTemp::find($id);
        $local_ex_rate = 1;
        $foreign_ex_rate = 1;
        $ex_rate = 1;
        if ($request['currency'] != $saletemp->defcurr) {
            if ($request['ex_rate_mode'] == 'Foreign') {
                $local_ex_rate = $request['local_ex_rate'];
                $ex_rate = 1 / $local_ex_rate;
            } else {
                $foreign_ex_rate = $request['foreign_ex_rate'];
                $ex_rate = $foreign_ex_rate;
            }
        }

        $saletemp->customer_id = $request['customer_id'];
        $saletemp->date_set = $request['date_set'];
        $saletemp->sale_date = $request['sale_date'];
        $saletemp->sale_type = $request['sale_type'];
        $saletemp->pay_type = $request['pay_type'];
        $saletemp->currency = $request['currency'];
        $saletemp->ex_rate_mode = $request['ex_rate_mode'];
        $saletemp->local_ex_rate = $local_ex_rate;
        $saletemp->foreign_ex_rate = $foreign_ex_rate;
        $saletemp->ex_rate = $ex_rate;
        $saletemp->due_date = $request['due_date'];
        $saletemp->comments = $request['comments'];

        $saletemp->save();

        return $saletemp;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AnSale  $anSale
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $saletemp = SaleTemp::find(decrypt($id));
        if (!is_null($saletemp)) {
            $temp_serv_items = ServiceItemTemp::where('sale_temp_id', $saletemp->id)->get();
            foreach ($temp_serv_items as $key => $item) {
                $item->delete();
            }

            $temp_items = SaleItemTemp::where('sale_temp_id', $saletemp->id)->get();
            foreach ($temp_items as $key => $item) {
                $item->delete();
            }
            $saletemp->delete();
        }
        $success = 'Sale was successfully canceled.';
        return redirect('pos')->with('success', $success);
    }

    public function issueVFD($saleid)
    {
    }
}
