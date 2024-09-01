<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Auth;
use Session;
use App\Models\Shop;
use App\Models\ExpenseTemp;
use App\Models\User;
use App\Models\Expense;
use App\Models\ExpenseCategory;

class ExpenseTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(ExpenseTemp::where('shop_id', Session::get('shop_id'))->where('user_id', Auth::user()->id)->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $expenses = Expense::where('expenses.shop_id', Session::get('shop_id'))->groupBy('expense_type')->get();
        $exp_types = array();
        foreach ($expenses as $key => $expense) {
            if (!is_null($expense->expense_category_id)) {
                $category = ExpenseCategory::find($expense->expense_category_id);
                array_push($exp_types, ['name' => $expense->expense_type, 'category' => $category->name]);
            }else{
                array_push($exp_types, ['name' => $expense->expense_type, 'category' => '']);
            }
        }
        return json_encode($exp_types);
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
        $sameitems = ExpenseTemp::where('expense_type', $request['expense_type'])->where('user_id', $user->id)->count();
        
        if ($sameitems == 0) {
            $expenseTemp = new ExpenseTemp;
            $expenseTemp->shop_id = $shop->id;
            $expenseTemp->user_id = $user->id;
            $expenseTemp->expense_type  = $request['expense_type'];
            $expenseTemp->amount = 0;
            $expenseTemp->no_days = 1;
            $expenseTemp->save();
            return $expenseTemp;
            
        }else{
            $warning = 'Ooops!. The Expense Type already in selected items.';
            return redirect()->back()->with('warning', $warning);
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
        $shop = Shop::find(Session::get('shop_id'));
        $expenseTemp =  ExpenseTemp::where('id', $id)->where('user_id', Auth::user()->id)->where('shop_id', $shop->id)->first();
        if (!is_null($expenseTemp)) {
            $expenseTemp->amount = $request['amount'];
            if (!is_null($request['no_days'])) {
                $expenseTemp->no_days = $request['no_days'];
            }else{
                $expenseTemp->no_days = 1;
            }
            $expenseTemp->wht_rate = $request['wht_rate'];
            $expenseTemp->has_vat = $request['has_vat'];
            $expenseTemp->description = $request['description'];
            $expenseTemp->save();

            return $expenseTemp;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ExpenseTemp::destroy($id);
    }
}
