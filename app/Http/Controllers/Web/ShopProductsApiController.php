<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Response;
use App\Models\Shop;
use App\Models\Setting;

class ShopProductsApiController extends Controller
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
        $products = $shop->products()->get([
            \DB::raw('product_id as id'),
            \DB::raw('product_no'),
            \DB::raw('barcode'),
            \DB::raw('name'),
            \DB::raw('description'),
            \DB::raw('in_stock'),
            \DB::raw('buying_per_unit'),
            \DB::raw('price_per_unit')]);
        // return json_encode($products);
        return Response::json($products);
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

    public function useBarcode()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $setting = Setting::where('shop_id', $shop->id)->first();
        if ($setting->use_barcode) {
            $usebarcode = true;
        }else{
            $usebarcode = false;
        }

        return Response::json(['usebarcode' => $usebarcode]);
    }
}
