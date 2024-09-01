<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\Payment;
use App\Models\Device;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\ExpenseTemp;
use App\Models\ExpensePayment;
use App\Models\Category;
use App\Models\PaymentVoucher;
use App\Models\Supplier;
use App\Models\ExpSupplierTransaction;
use App\Models\BankDetail;
use App\Models\SmsAccount;
use App\Models\DeviceExpense;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * 
     */

    public function __construct(Expense $expense)
    {
        $this->middleware('auth');
        $this->expense = $expense;
    }

    public function index(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($shop)) {
            $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', false)->first();
            if (!is_null($payment)) {
                $user = Auth::user();
                $settings = Setting::where('shop_id', $shop->id)->first();

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
                    $start = $now->startOfDay();
                    $end = \Carbon\Carbon::now();
                    $is_post_query = false;
                }
                $devices = null;
                if ($shop->business_type_id == 3) {
                    $devices = Device::where('shop_id', $shop->id)->get();
                }

                $expenses = null;
                if (Session::get('role') == 'salesman') {
                    $expenses =  Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('user_id', $user->id)->whereBetween('time_created', [$start, $end])->join('users', 'users.id', '=', 'expenses.user_id')->select('users.first_name as first_name', 'expenses.id as id', 'expense_category_id', 'expenses.supplier_id as supplier_id', 'expenses.expense_type as expense_type', 'expenses.amount as amount', 'expenses.no_days as no_days', 'expenses.amount_paid as amount_paid', 'expenses.exp_vat as exp_vat', 'expenses.wht_rate as wht_rate', 'expenses.wht_amount as wht_amount', 'expenses.time_created as created_at', 'expenses.exp_type as exp_type', 'expenses.status as status', 'expenses.description as description', 'expenses.expire_at as expire_at')->orderBy('time_created', 'desc')->get();
                } else {
                    $expenses =  Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->join('users', 'users.id', '=', 'expenses.user_id')->select('users.first_name as first_name', 'expenses.id as id', 'expense_category_id', 'expenses.supplier_id as supplier_id', 'expenses.expense_type as expense_type', 'expenses.amount as amount', 'expenses.no_days as no_days', 'expenses.amount_paid as amount_paid', 'expenses.exp_vat as exp_vat', 'expenses.wht_rate as wht_rate', 'expenses.wht_amount as wht_amount', 'expenses.time_created as created_at', 'expenses.exp_type as exp_type', 'expenses.status as status', 'expenses.description as description', 'expenses.expire_at as expire_at')->orderBy('time_created', 'desc')->get();
                }

                $pvs = PaymentVoucher::where('voucher_for', 'Expense')->where('shop_id', $shop->id)->whereBetween('created_at', [$start, $end])->get();

                $ctypes = array();
                $expense_types =  Expense::where('shop_id', $shop->id)->where('is_deleted', false)->select('expense_type')->groupBy('expense_type')->orderBy('expense_type')->get();

                foreach ($expense_types as $key => $value) {
                    array_push($ctypes, $value->expense_type);
                }

                $mindays = 0;
                $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', false)->first();
                if (!is_null($payment)) {
                    $now = Carbon::now();
                    $paydate = Carbon::parse($payment->created_at);

                    $lastpay = Payment::where('shop_id', $shop->id)->where('is_expired', 1)->orderBy('created_at', 'desc')->first();
                    if (!is_null($lastpay)) {
                        $lastexp = Carbon::parse($lastpay->expire_date);
                        $oldpaydate = Carbon::parse($lastpay->created_at);
                        $slipdays = $paydate->diffInDays($lastexp);
                        // return $slipdays;
                        if ($slipdays < 15) {
                            $mindays = $now->diffInDays($oldpaydate);
                        } else {
                            $mindays = $now->diffInDays($paydate) + 2;
                        }
                    } else {
                        $mindays = $now->diffInDays($paydate) + 2;
                    }
                }
                if ($mindays < 10) {
                    $mindays = 15;
                }

                $suppliers = Supplier::where('shop_id', $shop->id)->where('supplier_for', 'Expense')->get();
                $categories = Category::where('shop_id', $shop->id)->get();
                $expcategories = ExpenseCategory::where('shop_id', $shop->id)->get();

                $page = 'expenses';
                $title = 'My Operating Expenses';
                $title_sw = 'Gharama zangu za Uendeshaji';
                return view('expenses.index', compact('page', 'title', 'title_sw', 'shop', 'expenses', 'ctypes', 'settings', 'is_post_query', 'start_date', 'end_date', 'devices', 'pvs', 'suppliers', 'mindays', 'expcategories', 'categories'));
            } else {
                $info = 'Dear customer your account is not activated please make payment and activate now.';
                // Alert::info("Payment Expired", $info);
                return redirect('verify-payment')->with('error', $info);
            }
        } else {
            return redirect('unauthorized');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($shop)) {

            $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', false)->first();
            if (!is_null($payment)) {
                $user = Auth::user();
                $settings = Setting::where('shop_id', $shop->id)->first();

                $ctypes = array();
                $exptypes =  Expense::where('shop_id', $shop->id)->where('is_deleted', false)->select('expense_type')->groupBy('expense_type')->orderBy('expense_type')->get();

                foreach ($exptypes as $key => $value) {
                    array_push($ctypes, $value->expense_type);
                }

                $devices = null;
                if ($shop->business_type_id == 3) {
                    $devices = Device::where('shop_id', $shop->id)->get();
                }
                $mindays = 0;
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
                        } else {
                            $mindays = $now->diffInDays($paydate) + 2;
                        }
                    } else {
                        $mindays = $now->diffInDays($paydate) + 2;
                    }
                }
                if ($mindays < 10) {
                    $mindays = 15;
                }

                $suppliers = Supplier::where('shop_id', $shop->id)->where('supplier_for', 'Expense')->get();
                $categories = Category::where('shop_id', $shop->id)->get();
                $expcategories = ExpenseCategory::where('shop_id', $shop->id)->get();

                $page = 'New Expenses';
                $title = 'New Expenses';
                $title_sw = 'Gharama mpya za Uendeshaji';
                return view('expenses.create', compact('page', 'title', 'title_sw', 'shop', 'ctypes', 'suppliers', 'settings', 'devices', 'mindays', 'categories', 'expcategories'));
            } else {
                $info = 'Dear customer your account is not activated please make payment and activate now.';
                // Alert::info("Payment Expired", $info);
                return redirect('verify-payment')->with('error', $info);
            }
        } else {
            return redirect('unauthorized');
        }
    }

    public function ExpSuppliers()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $suppliers = Supplier::where('shop_id', $shop->id)->where('supplier_for', 'Expense')->get();

        $page = 'Suppliers';
        $title = 'My Suppliers';
        $title_sw = 'Wauzaji Wangu';
        return view('expenses.suppliers.index', compact('page', 'title', 'title_sw', 'suppliers'));
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
        $settings = Setting::where('shop_id', $shop->id)->first();
        $now = null;
        if (is_null($request['expense_date'])) {
            $now = Carbon::now();
        } else {
            $now = Carbon::now();
            $time = date('h:i:s', strtotime($now));
            $now = $request['expense_date'] . ' ' . $time;
        }

        $exp_type = $request['exp_type'];
        $pvno = null;
        if ($exp_type == 'credit') {
            $status = 'Pending';
        } else {
            $status = 'Paid';
            $max_pv_no = PaymentVoucher::where('shop_id', $shop->id)->latest()->first();
            if (!is_null($max_pv_no)) {
                $pvno = $max_pv_no->pv_no + 1;
            } else {
                $pvno = 1;
            }
        }

        $exptemps = ExpenseTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();

        $zeroams = array();
        foreach ($exptemps as $key => $value) {
            if ($value->amount == 0) {
                array_push($zeroams, $value->expense_type);
            }
        }

        if (!empty($zeroams)) {
            return redirect()->back()->with('warning', 'Please update the amount of each expense to continue');
        } else {


            $suppliierid = null;
            if (!is_null($request['supplier_id']) && $request['supplier_id'] != 0) {
                $suppliierid = $request['supplier_id'];
            }

            $total_amount = 0;
            $trans_id = null;
            $acctrans = null;

            $description = '';
            if ($shop->subscription_type_id >= 3) {
                if ($exp_type == 'credit') {
                    $supplier = Supplier::find($request['supplier_id']);
                    if (!is_null($supplier)) {

                        $acctrans = new ExpSupplierTransaction();
                        $acctrans->shop_id = $shop->id;
                        $acctrans->user_id = $user->id;
                        $acctrans->supplier_id = $supplier->id;
                        $acctrans->invoice_no = $request['invoice_no'];
                        $acctrans->amount = $total_amount;
                        $acctrans->date = $now;
                        $acctrans->save();

                        $trans_id = $acctrans->id;
                    }
                }
            }

            foreach ($exptemps as $key => $exp) {
                $total_amount += $exp->amount;
                if ($key > 0) {
                    $description .= ', ' . $exp->expense_type . ': ' . $exp->description;
                } else {
                    $description .= $exp->expense_type . ': ' . $exp->description;
                }

                $exp_vat = 0;
                if ($exp->has_vat == 'yes') {
                    $exp_vat = $exp->amount * (($settings->tax_rate / 100) / (1 + ($settings->tax_rate / 100)));
                }

                $wht_rate = $exp->wht_rate;
                $wht_amount = ($exp->amount - $exp_vat) * ($wht_rate / 100);
                $amountpaid = null;
                if ($exp_type == 'credit') {
                    $amountpaid = 0;
                } else {
                    $amountpaid = $exp->amount;
                }
                $expcategory = Expense::where('expense_type', $exp->expense_type)->where('shop_id', $shop->id)->latest()->first();
                $expense = Expense::create([
                    'shop_id' => $shop->id,
                    'user_id' => $user->id,
                    'expense_category_id' => $expcategory->expense_category_id,
                    'expense_type' => $exp->expense_type,
                    'amount' => $exp->amount,
                    'no_days' => $exp->no_days,
                    'exp_vat' => $exp_vat,
                    'account' => $request['account'],
                    'wht_rate' => $wht_rate,
                    'wht_amount' => $wht_amount,
                    'description' => $exp->description,
                    'time_created' => $now,
                    'exp_type' => $exp_type,
                    'amount_paid' => $amountpaid,
                    'status' => $status,
                    'order_no' => $request['order_no'],
                    'supplier_id' => $suppliierid,
                    'invoice_no' => $request['invoice_no'],
                    'trans_id' => $trans_id,
                    'category_id' => $request['category_id'],
                ]);

                if ($expense->no_days > 1) {
                    $expense->expire_at = \Carbon\Carbon::parse($expense->time_created)->addDays($expense->no_days);
                } else {
                    $date = date('Y-m-d', strtotime($expense->time_created));
                    $expense->expire_at = $date . ' 23:59:59';
                }
                $expense->save();

                if ($exp_type == 'cash') {
                    $payment = ExpensePayment::create([
                        'shop_id' => $shop->id,
                        'expense_id' => $expense->id,
                        'account' => $request['account'],
                        'pay_date' => $now,
                        'amount' => $amountpaid,
                        'pv_no' => $pvno,
                    ]);

                    $pv = new PaymentVoucher();
                    $pv->shop_id = $shop->id;
                    $pv->user_id = $user->id;
                    $pv->pv_no = $pvno;
                    $pv->amount = $total_amount;
                    $pv->account = $request['account'];
                    $pv->voucher_for = 'Expense';
                    $pv->reason = $description;
                    $pv->save();
                }

                if (!is_null($request['device_id'])) {
                    DeviceExpense::create([
                        'device_id' => $request['device_id'],
                        'expense_id' => $expense->id
                    ]);
                }
            }
            if (!is_null($acctrans)) {
                $acctrans->amount = $total_amount;
                $acctrans->save();
            }

            foreach ($exptemps as $key => $temp) {
                $temp->delete();
            }

            return redirect()->route('expenses.create')->with('success', 'Expenses were added successfully');
        }
    }

    public function storeExpense(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $now = null;
        if (is_null($request['expense_date'])) {
            $now = Carbon::now();
        } else {
            $now = \Carbon\Carbon::now();
            $time = date('h:i:s', strtotime($now));
            $now = $request['expense_date'] . ' ' . $time;
        }

        $exp_type = $request['exp_type'];
        $amount = null;
        if (is_null($request['amount'])) {
            $amount = 0;
        } else {
            $amount = $request['amount'];
        }
        $pvno = null;
        $amountpaid = null;
        if ($exp_type == 'credit') {
            $status = 'Pending';
            $amountpaid = 0;
        } else {
            $status = 'Paid';
            $amountpaid = $amount;
            $max_pv_no = PaymentVoucher::where('shop_id', $shop->id)->orderBy('pv_no', 'desc')->first();
            if (!is_null($max_pv_no)) {
                $pvno = $max_pv_no->pv_no + 1;
            } else {
                $pvno = 1;
            }
        }

        $wht_rate = 0;
        $wht_amount = 0;
        if (!is_null($request['wht_rate'])) {
            $wht_rate = $request['wht_rate'];
        }

        $exp_vat = 0;
        if ($request['has_vat'] == 'yes') {
            $exp_vat = $amount * (($settings->tax_rate / 100) / (1 + ($settings->tax_rate / 100)));
        }
        $wht_amount = ($amount - $exp_vat) * ($wht_rate / 100);

        $suppliierid = null;
        if (!is_null($request['supplier_id']) && $request['supplier_id'] != 0) {
            $suppliierid = $request['supplier_id'];
        }

        $no_days = 1;
        if (!is_null($request['no_days'])) {
            $no_days = $request['no_days'];
        }

        $expense = $this->expense->create([
            'shop_id' => $shop->id,
            'user_id' => $user->id,
            'expense_category_id' => $request['expense_category_id'],
            'expense_type' => $request->get('expense_type'),
            'amount' => $amount,
            'no_days' => $no_days,
            'exp_vat' => $exp_vat,
            'wht_rate' => $wht_rate,
            'wht_amount' => $wht_amount,
            'account' => $request['account'],
            'description' => $request['description'],
            'time_created' => $now,
            'exp_type' => $exp_type,
            'amount_paid' => $amountpaid,
            'status' => $status,
            'order_no' => $request['order_no'],
            'supplier_id' => $suppliierid,
            'invoice_no' => $request['invoice_no'],
            'category_id' => $request['category_id'],
        ]);

        if ($expense->no_days > 1) {
            $expense->expire_at = \Carbon\Carbon::parse($expense->time_created)->addDays($expense->no_days);
        } else {
            $date = date('Y-m-d', strtotime($expense->time_created));
            $expense->expire_at = $date . ' 23:59:59';
        }
        $expense->save();

        if (!is_null($request['device_id'])) {
            DeviceExpense::create([
                'device_id' => $request['device_id'],
                'expense_id' => $expense->id
            ]);
        }

        if ($exp_type == 'cash') {

            $pv = new PaymentVoucher();
            $pv->shop_id = $shop->id;
            $pv->user_id = $user->id;
            $pv->pv_no = $pvno;
            $pv->amount = $amount;
            $pv->account = $request['account'];
            $pv->voucher_for = 'Expense';
            $pv->reason = $request['description'];
            $pv->save();

            $payment = ExpensePayment::create([
                'shop_id' => $shop->id,
                'expense_id' => $expense->id,
                'account' => $request['account'],
                'pay_date' => $now,
                'amount' => $amount,
                'pv_no' => $pv->pv_no,
            ]);
            if ($payment) {
                $payment->created_at = $now;
                $payment->save();
            }
        } else {
            if ($shop->subscription_type_id >= 3) {
                $supplier = Supplier::find($request['supplier_id']);
                if (!is_null($supplier)) {

                    $acctrans = new ExpSupplierTransaction();
                    $acctrans->shop_id = $shop->id;
                    $acctrans->user_id = $user->id;
                    $acctrans->supplier_id = $supplier->id;
                    $acctrans->expense_id = $expense->id;
                    $acctrans->invoice_no = $request['invoice_no'];
                    $acctrans->amount = $amount;
                    $acctrans->date = $now;
                    $acctrans->save();

                    $expense->trans_id = $acctrans->id;
                    $expense->save();
                }
            }
        }

        $success = 'expense was successfully saved';

        // Alert::success('Success!', $success);
        return redirect()->route('expenses.create')->with('success', $success);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = 'Expense Preview';
        $title = 'Expense Preview';
        $title_sw = 'Hakiki Gharama';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $expense = Expense::findOrFail(decrypt($id));
        $date = date("d, M Y H:i:sA", strtotime($expense->time_created));

        $prev_shop_expenses = Expense::where('shop_id', $shop->id)->where('id', '<', $expense->id)->count();
        $recno = $prev_shop_expenses + 1;

        return view('expenses.show', compact('page', 'title', 'title_sw', 'recno', 'expense', 'shop', 'date', 'settings'))->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $expense = Expense::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (is_null($expense)) {
            return redirect('forbiden');
        } else {
            $ctypes = array();
            $expense_types =  Expense::where('shop_id', $shop->id)->select('expense_type')->groupBy('expense_type')->orderBy('expense_type')->get();

            foreach ($expense_types as $key => $value) {
                array_push($ctypes, $value->expense_type);
            }

            $expcategories = ExpenseCategory::where('shop_id', $shop->id)->get();
            $categories = Category::where('shop_id', $shop->id)->get();
            $category = Category::find($expense->category_id);
            $devices = Device::where('shop_id', $shop->id)->get();
            $dexpense = DeviceExpense::where('expense_id', $expense->id)->first();
            $page = 'Edit expense';
            $title = 'Edit Operating Expense';
            $title_sw = 'Hariri Gharama ya uendeshaji';

            $mindays = 0;
            $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->first();
            if (!is_null($payment)) {
                $now = Carbon::now();
                $paydate = Carbon::parse($payment->created_at);

                $lastpay = Payment::where('shop_id', $shop->id)->where('is_expired', 1)->orderBy('created_at', 'desc')->first();
                if (!is_null($lastpay)) {
                    $lastexp = Carbon::parse($lastpay->expire_date);
                    $oldpaydate = Carbon::parse($lastpay->created_at);
                    $slipdays = $paydate->diffInDays($lastexp);
                    // return $slipdays;
                    if ($slipdays < 15) {
                        $mindays = $paydate->diffInDays($oldpaydate);
                    } else {
                        $mindays = $now->diffInDays($paydate) + 2;
                    }
                } else {
                    $mindays = $now->diffInDays($paydate) + 2;
                }
            }
            if ($mindays < 10) {
                $mindays = 15;
            }
            return view('expenses.edit', compact('page', 'title', 'title_sw', 'expense', 'ctypes', 'settings', 'devices', 'dexpense', 'categories', 'category', 'expcategories', 'mindays'));
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
        $settings = Setting::where('shop_id', $shop->id)->first();

        $no_days = 1;
        if (!is_null($request['no_days'])) {
            $no_days = $request['no_days'];
        }

        $expense = Expense::find(decrypt($id));
        $expense->expense_category_id = $request['expense_category_id'];
        $expense->expense_type = $request->get('expense_type');
        $expense->amount = $request->get('amount');
        $expense->no_days = $no_days;
        $wht_rate = 0;
        $wht_amount = 0;
        if (!is_null($request['wht_rate'])) {
            $wht_rate = $request['wht_rate'];
            $wht_amount = $expense->amount * ($wht_rate / 100);
        }

        $exp_vat = 0;
        if ($request['has_vat'] == 'yes') {
            $exp_vat = $expense->amount * (($settings->tax_rate / 100) / (1 + ($settings->tax_rate / 100)));
        }
        $expense->exp_vat = $exp_vat;
        $expense->account = $request['account'];
        $expense->wht_rate = $wht_rate;
        $expense->wht_amount = $wht_amount;
        $expense->description = $request['description'];
        if (!is_null($request['expense_date'])) {
            $now = \Carbon\Carbon::now();
            $time = date('h:i:s', strtotime($now));
            $expense->time_created = $request['expense_date'] . ' ' . $time;
        }
        $expense->category_id = $request['category_id'];
        if ($expense->no_days > 1) {
            $expense->expire_at = \Carbon\Carbon::parse($expense->time_created)->addDays($expense->no_days);
        } else {
            $date = date('Y-m-d', strtotime($expense->time_created));
            $expense->expire_at = $date . ' 23:59:59';
        }
        $expense->save();

        if (!is_null($request['device_id'])) {
            $dexpense = DeviceExpense::where('expense_id', $expense->id)->first();
            if (!is_null($dexpense)) {
                $dexpense->device_id = $request['device_id'];
                $dexpense->save();
            } else {
                DeviceExpense::create([
                    'device_id' => $request['device_id'],
                    'expense_id' => $expense->id
                ]);
            }
        }
        $success = 'expense updated successfully';
        // Alert::success('Success!', $success);
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
        if (Auth::user()->hasRole('manager')) {
            $shop = Shop::find(Session::get('shop_id'));
            $expense = Expense::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
            if (is_null($expense)) {
                return redirect('forbiden');
            } else {
                $exppays = ExpensePayment::where('expense_id', $expense->id)->get();
                foreach ($exppays as $key => $pay) {

                    $pv = PaymentVoucher::where('pv_no', $pay->pv_no)->where('shop_id', $shop->id)->first();
                    if (!is_null($pv)) {
                        $pv->amount = $pv->amount - $pay->amount;
                        $pv->save();


                        $trans = ExpSupplierTransaction::where('pv_no', $pv->pv_no)->where('shop_id', $shop->id)->first();

                        if (!is_null($trans)) {
                            $trans->payment = $trans->payment - $pay->amount;
                            $trans->save();

                            if ($trans->payment <= 0) {
                                $trans->delete();
                            }
                        }
                        if ($pv->amount <= 0) {
                            $pv->delete();
                        }
                    }
                    $pay->delete();
                }

                $invoexps = Expense::where('trans_id', $expense->trans_id)->where('shop_id', $shop->id)->count();
                if ($invoexps > 1) {
                    $trans = ExpSupplierTransaction::where('id', $expense->trans_id)->where('shop_id', $shop->id)->first();
                    if (!is_null($trans)) {
                        $trans->amount = $trans->amount - $expense->amount;
                        $trans->save();
                    }
                } else {
                    $trans = ExpSupplierTransaction::where('id', $expense->trans_id)->where('shop_id', $shop->id)->first();
                    if (!is_null($trans)) {
                        $trans->delete();
                    }
                }

                $expense->is_deleted = true;
                $expense->del_by = Auth::user()->first_name . '(' . Carbon::now() . ')';
                $expense->save();

                $success = 'Expense has been deleted successfully';
                return redirect('expenses')->with('success', $success);
            }
        } else {
            return redirect('unauthorized');
        }
    }

    public function deleteMultiple(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($request->custom_name)) {

            foreach ($request->custom_name as $key => $id) {
                $expense = Expense::where('id', $id)->where('shop_id', $shop->id)->first();
                $pvexps = 0;
                $invoexps = 0;
                if (!is_null($expense)) {
                    $exppays = ExpensePayment::where('expense_id', $expense->id)->get();
                    foreach ($exppays as $key => $pay) {

                        $pv = PaymentVoucher::where('pv_no', $pay->pv_no)->where('shop_id', $shop->id)->first();
                        if (!is_null($pv)) {
                            $pv->amount = $pv->amount - $pay->amount;
                            $pv->save();


                            $trans = ExpSupplierTransaction::where('pv_no', $pv->pv_no)->where('shop_id', $shop->id)->first();

                            if (!is_null($trans)) {
                                $trans->payment = $trans->payment - $pay->amount;
                                $trans->save();

                                if ($trans->payment <= 0) {
                                    $trans->delete();
                                }
                            }
                            if ($pv->amount <= 0) {
                                $pv->delete();
                            }
                        }
                        $pay->delete();
                    }

                    $invoexps = Expense::where('trans_id', $expense->trans_id)->where('shop_id', $shop->id)->count();
                    if ($invoexps > 1) {
                        $trans = ExpSupplierTransaction::where('id', $expense->trans_id)->where('shop_id', $shop->id)->where('created_at', $expense->created_at)->first();
                        if (!is_null($trans)) {
                            $trans->amount = $trans->amount - $expense->amount;
                            $trans->save();
                        }
                    } else {
                        $trans = ExpSupplierTransaction::where('id', $expense->trans_id)->where('shop_id', $shop->id)->where('created_at', $expense->created_at)->first();
                        if (!is_null($trans)) {
                            $trans->delete();
                        }
                    }

                    $expense->is_deleted = true;
                    $expense->del_by = Auth::user()->first_name . '(' . Carbon::now() . ')';
                    $expense->save();
                    // $expense->delete();
                }
            }

            $success = 'Expenses were deleted successfully';
            return redirect('expenses')->with('success', $success);
        } else {
            return redirect('expenses')->with('info', 'No Items selected to Delete.');
        }
    }

    public function cancel()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();

        $puritems = ExpenseTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
        foreach ($puritems as $key => $value) {
            $value->delete();
        }

        return redirect()->back()->with('success', 'Stocks entry was cancelled successfully');
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

        $suppliers = Expense::where('expenses.shop_id', $shop->id)->whereRaw('(amount-amount_paid) > 0')->join('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')->select('suppliers.id as id', 'suppliers.supp_id as supp_id', 'suppliers.name as name')->groupBy('name')->get();

        $agings = array();
        foreach ($suppliers as $key => $supplier) {
            $d3 = Expense::where('expenses.shop_id', $shop->id)->whereDate('expenses.time_created', '>=', $date3)->whereRaw('(amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')->select(
                \DB::raw('SUM((amount-amount_paid)) as amount')
            )->first();

            $d6 = Expense::where('expenses.shop_id', $shop->id)->whereDate('expenses.time_created', '<=', $date3)->whereDate('expenses.time_created', '>', $date6)->whereRaw('(amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')->select(
                \DB::raw('SUM((amount-amount_paid)) as amount')
            )->first();

            $d9 = Expense::where('expenses.shop_id', $shop->id)->whereDate('expenses.time_created', '<=', $date6)->whereDate('expenses.time_created', '>', $date9)->whereRaw('(amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')->select(
                \DB::raw('SUM((amount-amount_paid)) as amount')
            )->first();

            $d12 = Expense::where('expenses.shop_id', $shop->id)->whereDate('expenses.time_created', '<=', $date9)->whereDate('expenses.time_created', '>', $date12)->whereRaw('(amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')->select(
                \DB::raw('SUM((amount-amount_paid)) as amount')
            )->first();

            $d15 = Expense::where('expenses.shop_id', $shop->id)->whereDate('expenses.time_created', '<=', $date12)->whereDate('expenses.time_created', '>', $date15)->whereRaw('(amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')->select(
                \DB::raw('SUM((amount-amount_paid)) as amount')
            )->first();

            $d18 = Expense::where('expenses.shop_id', $shop->id)->whereDate('expenses.time_created', '<=', $date15)->whereDate('expenses.time_created', '>', $date18)->whereRaw('(amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')->select(
                \DB::raw('SUM((amount-amount_paid)) as amount')
            )->first();

            $d21 = Expense::where('expenses.shop_id', $shop->id)->whereDate('expenses.time_created', '<=', $date18)->whereDate('expenses.time_created', '>', $date21)->whereRaw('(amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')->select(
                \DB::raw('SUM((amount-amount_paid)) as amount')
            )->first();

            $d24 = Expense::where('expenses.shop_id', $shop->id)->whereDate('expenses.time_created', '<=', $date21)->whereDate('expenses.time_created', '>', $date24)->whereRaw('(amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')->select(
                \DB::raw('SUM((amount-amount_paid)) as amount')
            )->first();

            $d27 = Expense::where('expenses.shop_id', $shop->id)->whereDate('expenses.time_created', '<=', $date24)->whereDate('expenses.time_created', '>', $date27)->whereRaw('(amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')->select(
                \DB::raw('SUM((amount-amount_paid)) as amount')
            )->first();

            $d30 = Expense::where('expenses.shop_id', $shop->id)->whereDate('expenses.time_created', '<=', $date27)->whereDate('expenses.time_created', '>', $date30)->whereRaw('(amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')->select(
                \DB::raw('SUM((amount-amount_paid)) as amount')
            )->first();

            $d33 = Expense::where('expenses.shop_id', $shop->id)->whereDate('expenses.time_created', '<=', $date30)->whereDate('expenses.time_created', '>', $date33)->whereRaw('(amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')->select(
                \DB::raw('SUM((amount-amount_paid)) as amount')
            )->first();

            $d36 = Expense::where('expenses.shop_id', $shop->id)->whereDate('expenses.time_created', '<=', $date33)->whereDate('expenses.time_created', '>', $date36)->whereRaw('(amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')->select(
                \DB::raw('SUM((amount-amount_paid)) as amount')
            )->first();

            $ab360 = Expense::where('expenses.shop_id', $shop->id)->whereDate('expenses.time_created', '<=', $date36)->whereRaw('(amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')->select(
                \DB::raw('SUM((amount-amount_paid)) as amount')
            )->first();

            $ctotal = Expense::where('expenses.shop_id', $shop->id)->whereRaw('(amount-amount_paid) > 0')->where('supplier_id', $supplier->id)->join('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')->select(
                \DB::raw('SUM((amount-amount_paid)) as amount')
            )->first();

            array_push($agings, ['supp_id' => $supplier->supp_id, 'name' => $supplier->name, '0-30' => $d3->amount, '31-60' => $d6->amount, '61-90' => $d9->amount, '91-120' => $d12->amount, '121-150' => $d15->amount, '151-180' => $d18->amount, '181-210' => $d21->amount, '211-240' => $d24->amount, '241-270' => $d27->amount, '271-300' => $d30->amount, '301-330' => $d33->amount, '331-360' => $d36->amount, '>360' => $ab360->amount, 'ctotal' => $ctotal->amount]);
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

        return view('expenses.aging-report', compact('page', 'title', 'title_sw', 'shop', 'agings', 'start_date', 'end_date', 'is_post_query', 'customer', 'customers', 'duration', 'duration_sw', 'reporttime'));
    }

    public function accountStmt($id, Request $request)
    {
        $page = 'Expenses';
        $title = 'Supplier Account Statement';
        $title_sw = 'Taarifa ya Akaunti ya Supplier';
        $shop = Shop::find(Session::get('shop_id'));
        $supplier = Supplier::find(decrypt($id));

        $customer = null;
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
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        } else {
            $ftrans = ExpSupplierTransaction::where('shop_id', $shop->id)->where('supplier_id', $supplier->id)->orderBy('date', 'asc')->first();
            $sdate = date('Y-m-d', strtotime($supplier->created_at)) . ' 00:00:00';
            if (!is_null($ftrans)) {
                $sdate = $ftrans->date . ' 00:00:00';
            }
            $start = $sdate;
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        $duration = 'From ' . date('d-m-Y', strtotime($start)) . ' To ' . date('d-m-Y', strtotime($end)) . '.';
        $duration_sw = 'Kuanzia ' . date('d-m-Y', strtotime($start)) . ' Mpaka ' . date('d-m-Y', strtotime($end)) . '.';


        $transactions = ExpSupplierTransaction::where('supplier_id', $supplier->id)->whereBetween('created_at', [$start, $end])->orderBy('date', 'asc')->get();


        $invtrans = ExpSupplierTransaction::where('supplier_id', $supplier->id)->where('amount', '>', 0)->whereBetween('created_at', [$start, $end])->orderBy('date', 'asc')->get();

        $payments = ExpSupplierTransaction::where('payment', '!=', null)->where('supplier_id', $supplier->id)->whereBetween('created_at', [$start, $end])->orderBy('date', 'desc')->get();

        $obal = ExpSupplierTransaction::where('supplier_id', $supplier->id)->where('invoice_no', 'OB')->first();

        $purchases = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('supplier_id', $supplier->id)->whereRaw('(amount-amount_paid) > 0')->orderBy('time_created', 'desc')->get();

        $bdetails = BankDetail::where('shop_id', $shop->id)->get();
        $settings = Setting::where('shop_id', $shop->id)->first();

        $smsacc = SmsAccount::where('shop_id', $shop->id)->first();
        $senderids = null;
        if (!is_null($smsacc)) {
            $senderids = $smsacc->senderIds()->get();
        }

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('expenses.suppliers.show', compact('page', 'title', 'title_sw', 'shop', 'transactions', 'payments', 'purchases', 'supplier', 'is_post_query', 'duration', 'duration_sw', 'start_date', 'end_date', 'reporttime', 'customer', 'settings', 'bdetails', 'senderids', 'obal', 'invtrans'));
    }



    function convert_number_to_words($number)
    {

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
