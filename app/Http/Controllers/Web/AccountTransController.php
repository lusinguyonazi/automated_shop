<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use Crypt;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\User;
use App\Models\AccountTransaction;
use App\Models\BankDetail;

class AccountTransController extends Controller
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

        $date = Carbon::now();
        if (!is_null($request['date'])) {
            $date = $request['date'];
        }

        $acctrans = AccountTransaction::create([
            'shop_id' => $shop->id,
            'user_id' => $user->id,
            'bank_detail_id' => $request['bank_detail_id'],
            'from' => $request['from'],
            'to' => $request['to'],
            'amount' => $request['amount'],
            'reason' => $request['reason'],
            'date' => $date
        ]);

        return redirect('cash-flows')->with('success', 'Transaction recorded successfully');
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
        $page = 'Cash Flows';
        $title = 'Edit Account Transaction';
        $title_sw = 'Hariri Muamala wa akaunti';
        $shop = Shop::find(Session::get('shop_id'));
        $acctrans = AccountTransaction::find(Crypt::decrypt($id));
        $bdetails = BankDetail::where('shop_id', $shop->id)->get();
        
        $is_post_query = false;
        $start_date = null;
        $end_date = null;

        return view('cash-flows.edit-trans', compact('page', 'title', 'title_sw', 'acctrans', 'bdetails', 'is_post_query', 'start_date', 'end_date',));
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
        $acctrans = AccountTransaction::find(Crypt::decrypt($id));
        $acctrans->from = $request['from'];
        $acctrans->to = $request['to'];
        $acctrans->bank_detail_id = $request['bank_detail_id'];
        $acctrans->amount = $request['amount'];
        $acctrans->reason = $request['reason'];
        $acctrans->date = $request['date'];
        $acctrans->save();

        return redirect('cash-flows')->with('success', 'Transaction was updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        AccountTransaction::destroy(Crypt::decrypt($id));

        return redirect()->back()->with('success', 'Transaction was deleted successfully');
    }
}
