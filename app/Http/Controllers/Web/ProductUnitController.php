<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\Product;
use App\Models\ProductUnit;

class ProductUnitController extends Controller
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
        $product = Product::find($request['product_id']);
        if ($product) {
            $prod_unit = new ProductUnit();
            $prod_unit->shop_id = $shop->id;
            $prod_unit->product_id = $product->id;
            $prod_unit->unit_name = $request['unit_name'];
            $prod_unit->qty_equal_to_basic = $request['qty_equal_to_basic'];
            $prod_unit->unit_price = $request['unit_price'];
            $prod_unit->save();

            return redirect()->route('products.show', encrypt($product->id))->with('success', 'Product Unit was added successfully');
        }else{
            return redirect()->back()->with('error', 'Product not Found');
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
        $page = 'Edit Product Unit';
        $title = 'Edit Product Unit';
        $title_sw = 'Hariri kipimio cha bidhaa';
        $prod_unit = ProductUnit::find(decrypt($id));
        
        $units = array(
            'pcs' => 'Piece',
            'prs' => 'Pair',
            'cts' => 'Carton',
            'pks' => 'Pack',
            'kgs' => 'Kilogram',
            'lts' => 'Liter',
            'dzs' => 'Dozen',
            'fts' => 'Foot',
            'fls' => 'Float',
            'crs' => 'Crete',
            'set' => 'Set',
            'gls' => 'Gallon',
            'box' => 'Box',
            'btl' => 'Bottle',
            'mts' => 'Meter'
        );
        return view('products.edit-unit', compact('page', 'title', 'title_sw', 'prod_unit', 'units'));
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
        $prod_unit = ProductUnit::find(decrypt($id));
        $prod_unit->unit_name = $request['unit_name'];
        $prod_unit->qty_equal_to_basic = $request['qty_equal_to_basic'];
        $prod_unit->unit_price = $request['unit_price'];
        $prod_unit->save();

        if ($prod_unit->is_basic) {
            $product = $shop->products()->where('product_id', $prod_unit->product_id)->first();
            if (!is_null($product)) {
                $product->pivot->price_per_unit = $prod_unit->unit_price;
                $product->pivot->save();
            }
        }

        return redirect()->route('products.show', encrypt($prod_unit->product_id))->with('success', 'Product Unit updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $prod_unit = ProductUnit::find(decrypt($id));
        if (!is_null($prod_unit)) {
            $prod_unit->delete();
        }

        return redirect()->route('products.show', encrypt($prod_unit->product_id))->with('success', 'Product Unit deleted successfully');
    }
}
