<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\Supplier;
use App\Models\RmSupplierTransaction;
use App\Models\SmsAccount;
use App\Models\RmPurchase;
use App\Models\Setting;
use App\Models\ShopCurrency;
use App\Models\BankDetail;
use App\Models\PaymentVoucher;
use App\Models\RmPurchasePayment;

class RmSupplierTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $suppliers = Supplier::where('shop_id', $shop->id)->where('supplier_for', 'Raw Materials')->get();
        $page = 'Raw Material Puchases Suppliers Account';
        $title = 'My Suppliers';
        $title_sw = 'Wauzaji Wangu';
        return view('production.raw-materials.purchases.supplier-account', compact('page', 'title', 'title_sw', 'suppliers'));
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
    public function show(Request $request , $id)
    {
        $page = 'Suppliers';
        $title = 'Supplier Account Statement';
        $title_sw = 'Taarifa ya Akaunti ya Supplier';
        $shop = Shop::find(Session::get('shop_id'));
        $supplier = Supplier::find(decrypt($id));
        $settings = Setting::where('shop_id', $shop->id)->first(); 
        $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
        $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
        $bdetails = BankDetail::where('shop_id', $shop->id)->get();

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
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $ftrans = RmSupplierTransaction::where('shop_id', $shop->id)->where('supplier_id', $supplier->id)->orderBy('id', 'asc')->first();
            $sdate = date('Y-m-d', strtotime($supplier->created_at)).' 00:00:00';
            if (!is_null($ftrans)) {
                $sdate = $ftrans->date.' 00:00:00';
            }
            $start = $sdate;
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

            $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
            $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

           $transactions = RmSupplierTransaction::where('supplier_id', $supplier->id)->where('is_deleted', false)->whereBetween('created_at', [$start, $end])->orderBy('date', 'asc')->get();


            $invtrans = RmSupplierTransaction::where('supplier_id', $supplier->id)->where('is_deleted', false)->where('amount', '>', 0)->whereBetween('created_at', [$start, $end])->orderBy('date', 'asc')->get();


            $payments = RmSupplierTransaction::where('payment', '!=', null)->where('supplier_id', $supplier->id)->where('is_deleted', false)->whereBetween('created_at', [$start, $end])->orderBy('date', 'desc')->get();

            
            $purchases = RmPurchase::where('shop_id', $shop->id)->where('supplier_id', $supplier->id)->where('is_deleted', false)->where('status', 'Pending')->orderBy('date', 'desc')->get();

            $obal = RmSupplierTransaction::where('supplier_id', $supplier->id)->where('invoice_no', 'OB')->first();

            

            $smsacc = SmsAccount::where('shop_id', $shop->id)->first();
            $senderids = null;
            if (!is_null($smsacc)) {
                $senderids = $smsacc->senderIds()->get();
            }

            $crtime = \Carbon\Carbon::now();
            $reporttime = $crtime->toDayDateTimeString();
            return view('production.raw-materials.purchases.account-stmt', compact('page', 'title', 'title_sw', 'shop', 'transactions', 'payments', 'purchases', 'supplier', 'is_post_query', 'duration', 'duration_sw', 'start_date', 'end_date', 'reporttime', 'customer', 'settings', 'obal', 'invtrans', 'senderids' , 'currencies' , 'defcurr' , 'dfcurr', 'bdetails')); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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

    
    public function deletePayment($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $accpay = RmSupplierTransaction::find(decrypt($id));
        if (!is_null($accpay)) {
            $voucher = PaymentVoucher::where('pv_no', $accpay->pv_no)->where('shop_id', $shop->id)->first();
            if ($accpay->trans_ob_amount > 0) {
                $obtrans = RmSupplierTransaction::where('shop_id', $shop->id)->where('supplier_id', $accpay->supplier_id)->where('is_ob', true)->first();
                if (!is_null($obtrans)) {
                    $obtrans->ob_paid = $obtrans->ob_paid-$accpay->trans_ob_amount;
                    $obtrans->save();
                }
            }

            // $cashouts = CashOut::where('trans_id', $accpay->id)->get();
            // foreach ($cashous as $key => $out) {
            //     $cashin = CashIn::find($out->cash_in_id);
            //     if (!is_null($cashin)) {
            //         $cashin->amount_paid = $cashin->amount_paid-$ins->amount;
            //         $cashin->status = 'Pending';
            //         $cashin->save();
            //     }
            //     $out->delete();
            // }
            
            $ppays = RmPurchasePayment::where('rm_trans_id', $accpay->id)->where('shop_id', $shop->id)->get();
            foreach ($ppays as $key => $payment) {
                $purchase = RmPurchase::find($payment->purchase_id);
                $payment->delete();
                if ($purchase->amount_paid > 0) {
                    $ppays = RmPurchasePayment::where('rm_purchase_id', $purchase->id)->get();
                    $amount_paid = 0;
                    foreach ($ppays as $key => $pay) {
                        $amount_paid += $pay->amount;
                    }

                    $purchase->amount_paid = $amount_paid;
                    if (($purchase->total_amount-$purchase->amount_paid) == 0) {
                        $purchase->status = 'Paid';
                    }else{
                        $purchase->status = 'Pending';
                    }
                    $purchase->save();
                }
            }
            $voucher->delete();
            
            $accpay->delete();
        }

        return redirect()->back()->with('success', 'Payments were deleted successful');
    }
}
