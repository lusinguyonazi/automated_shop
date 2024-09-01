<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\ExpenseCategory;

class ExpenseCategoryController extends Controller
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
        $expcategory = new ExpenseCategory();
        $expcategory->shop_id = $shop->id;
        $expcategory->name = $request['name'];
        $expcategory->description = $request['description'];
        $expcategory->is_included_in_prod_cost = $request['is_included_in_prod_cost'];
        $expcategory->save();

        return redirect('expenses')->with('success', 'Expenses Category was added successfully');
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
        $page = 'Expenses';
        $title = 'Edit Expense Category';
        $title_sw = 'Hariri Aina ya Matumizi';
        $expcategory = ExpenseCategory::find(decrypt($id));
        return view('expenses.edit-cat', compact('page', 'title', 'title_sw', 'expcategory'));
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
        $expcategory = ExpenseCategory::find(decrypt($id));
        $expcategory->name = $request['name'];
        $expcategory->description = $request['description'];
        $expcategory->is_included_in_prod_cost = $request['is_included_in_prod_cost'];
        $expcategory->save();

        return redirect('expenses')->with('success', 'Expenses Category was updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $expcategory = ExpenseCategory::find(decrypt($id));
        if (!is_null($expcategory)) {
            $expcategory->delete();
        }

        return redirect('expenses')->with('success', 'Expenses Category was deleted successfully');
    }
}
