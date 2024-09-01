<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Crypt;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\CashIn;

class CashInController extends Controller
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
        $indate = '';

        if (!is_null($request['in_date'])) {
            $indate = $request['in_date'];
        }else{
            $indate = Carbon::now();
        }
        $cashin = CashIn::create([
            'shop_id' => $shop->id,
            'account' => $request['account'],
            'amount' => $request['amount'],
            'source' => $request['source'],
            'in_date' => $indate
        ]);

        return redirect('cash-flows')->with('success', 'Your Data recorded successfuly');
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
        $title = 'Edit Cash In';
        $title_sw = 'Hariri Pesa iliyotoka';
        $cashin = CashIn::find(Crypt::decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));
        $cins = CashIn::where('shop_id', $shop->id)->select('source')->groupBy('source')->orderBy('source')->get();

        $start_date = null;            
        $end_date = null;
              
        //check if user opted for date range
        $is_post_query = false;

        return view('cash-flows.edit-cash-in', compact('page', 'title', 'title_sw', 'cins', 'cashin','is_post_query', 'start_date', 'end_date'));
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
        $cashin = CashIn::find(decrypt($id));
        $cashin->account = $request['account'];
        $cashin->amount = $request['amount'];
        $cashin->source = $request['source'];
        $cashin->in_date = $request['in_date'];
        $cashin->save();

        return redirect('cash-flows')->with('success', 'Cash In updated successfuly');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cashin = CashIn::find(decrypt($id));
        if (!is_null($cashin)) {
            $cashin->delete();
        }

        return redirect('cash-flows')->with('success', 'Cash In was deleted successfuly');
    }
}
