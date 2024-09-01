<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SenderId;
use App\Models\SmsAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class SmsAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Sms Accounts';
        $title = 'Sms Accounts';

        $sms_accounts = SmsAccount::join('shops', 'shops.id', '=', 'sms_accounts.shop_id')->select('sms_accounts.id as id', 'sms_accounts.username as username', 'sms_accounts.password as password')->get();
        $shops = User::role('manager')->join('shop_user', 'shop_user.user_id', '=', 'users.id')
            ->join('shops', 'shops.id', '=', 'shop_user.shop_id')
            ->join('payments', 'payments.shop_id', '=', 'shops.id')
            ->where('is_expired', false)
            ->select('users.first_name as first_name', 'users.last_name as last_name', 'users.phone as phone', 'shops.id as id', 'shop_user.is_default as is_default', 'shops.created_at as created_at', 'payments.expire_date as expire_date', 'payments.is_expired as is_expired')->get();

        $senderids = SenderId::join('sms_accounts', 'sms_accounts.id', '=', 'sender_ids.sms_account_id')->select('sender_ids.id as id', 'name', 'username', 'auto_sms')->get();

        return view('admin.sms-accounts.index', compact('page', 'title', 'sms_accounts', 'shops', 'senderids'));
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
        $smsacc = SmsAccount::create([
            'shop_id' => $request['shop_id'],
            'username' => $request['username'],
            'password' => $request['password']
        ]); 

        return redirect()->back()->with('success', 'SMS Account created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Edit SMS Account';
        $title = 'Edit SMS Account';
        $smsacc = SmsAccount::find(Crypt::decrypt($id));

        $shops = User::role('manager')->join('shop_user', 'shop_user.user_id', '=', 'users.id')->join('shops', 'shops.id', '=', 'shop_user.shop_id')->join('payments', 'payments.shop_id', '=', 'shops.id')->where('is_expired', false)->select('users.first_name as first_name', 'users.last_name as last_name', 'users.phone as phone', 'shops.id as id', 'shop_user.is_default as is_default', 'shops.created_at as created_at', 'payments.expire_date as expire_date', 'payments.is_expired as is_expired')->get();

        return view('admin.sms-accounts.edit', compact('page', 'title', 'smsacc', 'shops'));
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
        $smsacc = SmsAccount::find(Crypt::decrypt($id));
        $smsacc->shop_id = $request['shop_id'];
        $smsacc->username = $request['username'];
        $smsacc->password = $request['password'];
        $smsacc->save();

        return redirect('admin/sms-accounts')->with('success', 'SMS Account updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $smsacc = SmsAccount::find(Crypt::decrypt($id));

        if (!is_null($smsacc)) {
            $smsacc->delete();
        }

        return redirect()->back()->with('success', 'SMS Account deleted successfully');
    }
}
