<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use Response;
use App\Models\Shop;
use App\Models\Mro;
use App\Models\MorItem;

class MROController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       
        $expired = Session::get('expired');
        $page = 'Overhead Expenses';
        $title = 'Overhead Expenses';
        $title_sw = 'Overhead Expenses';
        $shop = Shop::find(Session::get('shop_id'));
        $mros = $shop->mro()->where('is_deleted' , false)->get();

        return view('production.mro.index', compact('page', 'title', 'title_sw', 'mros', 'shop'));
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

         $mro = Mro::create([
            'shop_id' => $shop->id,
            'name' => $request['name'],
         ]);
         return redirect()->back();
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
        $page = 'Overhead Expenses';
        $title = 'Edit Overhead Expense';
        $title_sw = 'Hariri Overhead Expense';

        $mro = Mro::find(decrypt($id));
        return view('production.mro.edit' , compact('mro' , 'page' , 'title_sw' , 'title'));
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
        $mro =Mro::find(decrypt($id));
        $mro->name = $request['name'];
        $mro->save();

        return redirect()->back()->with('success' , 'Mro have been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $mro =Mro::find(decrypt($id));
        $mro->is_deleted = true;
        $mro->save();
        return redirect('mro');
    }
}
