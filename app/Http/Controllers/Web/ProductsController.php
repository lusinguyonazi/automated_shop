<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App;
use Crypt;
use Auth;
use Validator;
use Carbon\Carbon;
use App\Imports\ProductsImport;
use App\Exports\ProductExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\shop;
use App\Models\BarcodeSetting;
use App\Models\categories;
use App\Models\Setting;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Stock;
use App\Models\Category;
use App\Models\AnSaleItem;
use App\Models\ServiceSaleItem;
use App\Models\ProdDamage;
use App\Models\TransferOrder;
use App\Models\SaleReturnItem;
use App\Models\Invoice;
use App\Models\CustomerAccount;
use App\Models\CustomerTransaction;
use App\Models\ShopCurrency;
use Log;
use App\Http\Controllers\Web\SettingsController;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $expired = Session::get('expired');
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

        $page = 'Products';
        $title = 'My products';
        $title_sw = 'Bidhaa Zangu';

        if ($expired == 0) {
            $shop = Shop::find(Session::get('shop_id'));
            $currency = '';
            $shopcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            if (!is_null($shopcurr)) {
                $currency = $shopcurr->code;
            }
            $bsetting = BarcodeSetting::where('shop_id', $shop->id)->first();
            if (is_null($bsetting)) {
                $bsetting = BarcodeSetting::create([
                    'shop_id' => $shop->id,
                ]);
                return redirect('products');
            }

            $settings = Setting::where('shop_id', $shop->id)->first();
            if (is_null($settings)) {
                $settings = Setting::create([
                    'shop_id' => $shop->id,
                    'tax_rate' => 18,
                    'inv_no_type' => 'Automatic'
                ]);
            }
            $now = Carbon::now();
            $shidlen = 0;
            $code = '';
            if ($bsetting->code_type === 'EAN8') {
                $shidlen = strlen($shop->id);
                $code = $shop->id . str_pad(1, $bsetting->code_length - $shidlen, '0', STR_PAD_LEFT);
            } else {
                $shidlen = strlen($shop->id);
                $code = $shop->id . str_pad(1, $bsetting->code_length - $shidlen, '0', STR_PAD_LEFT);
            }

            $pnos = $shop->products()->whereNotNull('product_no')->count();
            $pls = $shop->products()->whereNotNull('location')->count();

            if (!is_null($request['category_id'])) {
                $searchcat = Category::find($request['category_id']);
                $childrens = $searchcat->children()->get();
                $products = null;

                $prods = [];
                if ($searchcat->children->count() > 0) {
                    if ($searchcat->products()->count() > 0) {
                        array_push($prods, $searchcat->products()->get());
                    }

                    array_push($prods, $searchcat->catProducts());

                    $products = array_flatten($prods);
                } else {
                    $products = $searchcat->products()->get();
                }
                $isSearched = true;
                $categories = $shop->categories()->get();

                return view('products.index1', compact('page', 'title', 'title_sw', 'products', 'units', 'childrens', 'categories', 'searchcat', 'isSearched', 'bsetting', 'code', 'settings', 'shop', 'pnos', 'pls', 'currency'));
            } else {

                $products = $shop->products()->get();
                $isSearched = false;
                $categories = $shop->categories()->get();
                return view('products.index', compact('page', 'title', 'title_sw', 'products', 'units', 'categories', 'isSearched', 'bsetting', 'code', 'shop', 'pnos', 'settings', 'pls', 'currency'));
            }
        } else {
            $info = 'Dear customer your account is not activated please make payment and activate now.';
            return redirect('verify-payment')->with('error', $info);
        }
    }

    public function getShopProducts(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = $shop->products()->select('count(*) as allcount')->count();
        $totalRecordswithFilter = $shop->products()->select('count(*) as allcount')->where(\DB::raw('CONCAT_WS(" ", `name`, `barcode`)'), 'like', '%' . $searchValue . '%')->count();

        // Fetch records
        $records = $shop->products()->orderBy('name', 'asc')->where(\DB::raw('CONCAT_WS(" ", `name`, `barcode`)'), 'like', '%' . $searchValue . '%')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $key => $record) {
            $id = $record->id;
            if (Auth::user()->hasRole('manager')) {
                $name = "<a href='" . route('products.show', Crypt::encrypt($record->id)) . "'>" . $record->name . "</a>";
            } else {
                $name = $record->name;
            }
            $basic_unit = $record->basic_unit;
            $instock = $record->pivot->in_stock;
            $price = $record->pivot->price_per_unit;
            $date = date('Y-m-d H:i:s', strtotime($record->pivot->created_at));
            if (Auth::user()->hasRole('manager') || Auth::user()->can('edit-product') || Auth::user()->hasRole('storekeeper') || Auth::user()->can('edit-stock')) {
                $editbtn = "<a href='" . route('products.edit', encrypt($record->id)) . "'><i class='bx bx-edit' style='color: blue;''></i></a>";
            } else {
                $editbtn = "";
            }
            if (Auth::user()->hasRole('manager') || Auth::user()->can('delete-stock') || Auth::user()->hasRole('storekeeper') || Auth::user()->can('delete-stock')) {
                $deletebtn = "<form id='delete-form-" . $record->id . "' method='POST' action='" . route('products.destroy', encrypt($record->id)) . "' style='display: inline;'> 
                   " . csrf_field() . "
                    <input type='hidden' name='_method' value='DELETE'>
                <a href='javascript:;' onclick=' return confirmDelete(" . $record->id . ")'><span class='bx bx-trash' aria-hidden='true' style='color: red'></span></a>
            </form>";
            } else {
                $deletebtn = '';
            }
            $action = $editbtn . ' ' . $deletebtn;


            $data_arr[] = array(
                "id" => $id,
                "name" => $name,
                "basic_unit" => $basic_unit,
                "in_stock" => $instock,
                "price" => $price,
                'date' => $date,
                'action' => $action
            );
        }


        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        echo json_encode($response);
        exit;
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
        $now = Carbon::now();
        $product = Product::where('name', $request['name'])->where('basic_unit', $request['basic_unit'])->first();

        if (is_null($product)) {
            $product = Product::create([
                'name' => $request['name'],
                'basic_unit' => $request['basic_unit']
            ]);
        }
        //Upload Product Image
        if(!is_null($request['image'])){
        $this->validate($request, [
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        $file = $request->file('image');
        $fileName = time() . '.' . $file->getClientOriginalExtension();
        $file->move(storage_path('app/public/images/' . $shop->id, $fileName));
        $image_path = 'images/' . $fileName;
        //End of Upload Product Image
    }else{
        $image_path = null;

    }

        $buying_per_unit = 0;
        if (!is_null($request['buying_per_unit'])) {
            $buying_per_unit = $request['buying_per_unit'];
        }

        if (!is_null($request['quantity_in'])) {

            $stock = Stock::create([
                'shop_id' => $shop->id,
                'product_id' => $product->id,
                'quantity_in' => $request['quantity_in'],
                // 'image' => $request['image_path'],
                'buying_per_unit' => $buying_per_unit,
                'time_created' => $now,
                'source' => 'first stock',
                'expire_date' =>  is_null($request->expire_date) ? null : $request->expire_date,
            ]);
        }

        //Add this product to category
        $category = Category::where('id', $request['category_id'])->where('shop_id', $shop->id)->first();
        if (!is_null($category)) {
            $category->products()->attach($product);
        }


        $shopprod = $shop->products()->where('product_id', $product->id)->first();
        if (is_null($shopprod)) {
            $newbarcode = null;
            $settings = Setting::where('shop_id', $shop->id)->first();
            if ($settings->generate_barcode) {
                $codes = array();
                $bcodes = $shop->products()->select('barcode')->get();
                foreach ($bcodes as $key => $bcode) {
                    array_push($codes, $bcode->barcode);
                }

                if (count($codes) > 0) {
                    $max_code = max($codes);
                    $newbarcode = $max_code + 1;
                }
            } else {
                $newbarcode = $request['barcode'];
            }

            if ($request['with_vat'] == 'Yes') {
                $settings = Setting::where('shop_id', $shop->id)->first();
                $vat = 1 + ($settings->tax_rate / 100);
                $unit_price_vat = $request['price_per_unit'] * $vat;
                $wholesale_vat = $request['wholesale_price'] * $vat;
                $shop->products()->attach($product, ['in_stock' => $request['quantity_in'], 'location' => $request['location'], 'product_no' => $request['product_no'],  'buying_per_unit' => $buying_per_unit, 'price_per_unit' => $request['price_per_unit'], 'image' => $image_path, 'wholesale_price' => $request['wholesale_price'],  'barcode' => $newbarcode, 'description' => $request['description'], 'time_created' => $now]);
            } else {
                $shop->products()->attach($product, ['in_stock' => $request['quantity_in'], 'location' => $request['location'], 'product_no' => $request['product_no'],  'buying_per_unit' => $buying_per_unit, 'price_per_unit' => $request['price_per_unit'], 'image' => $image_path,  'wholesale_price' => $request['wholesale_price'], 'barcode' => $newbarcode, 'description' => $request['description'], 'time_created' => $now]);
            }

            $prod_unit = new ProductUnit();
            $prod_unit->shop_id = $shop->id;
            $prod_unit->product_id = $product->id;
            $prod_unit->unit_name = $product->basic_unit;
            $prod_unit->is_basic = true;
            $prod_unit->qty_equal_to_basic = 1;
            $prod_unit->unit_price = $request['price_per_unit'];
            $prod_unit->save();

            $message = 'Your product was added successfully!';
            return redirect()->back()->with('success', $message);
        } else {
            $message = 'This product already exists in your shop product list';

            return redirect()->back()->with('error', $message);
        }
    }


    public function import(Request $request)
    {
        $rules = array(
            'file' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        // process the form
        if ($validator->fails()) {
            return \Redirect::to('products')->withErrors($validator);
        } else {
            Excel::import(new ProductsImport, request()->file('file'));
            return redirect('products')->with('success', 'All good!');
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
        $shop = Shop::find(Session::get('shop_id'));
        $suppliers = $shop->suppliers()->get();
        $product = $shop->products()->where('product_id', Crypt::decrypt($id))->first();
        $bsetting = BarcodeSetting::where('shop_id', $shop->id)->first();
        $settings = Setting::where('shop_id', $shop->id)->first();

        if (is_null($product)) {
            return redirect('products')->with('info', 'Sorry! The item your trying to view does not exist.');
        } else {
            $stocks = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('stocks.shop_id', $shop->id)->where('quantity_in', '>', 0)->orderBy('time_created', 'desc')->get();
            $d_stocks = Stock::where('product_id', $product->id)->where('stocks.shop_id', $shop->id)->where('quantity_in', '>', 0)->select('stocks.id as id', 'stocks.quantity_in as quantity_in', 'stocks.buying_per_unit as buying_per_unit', 'stocks.time_created as created_at', 'stocks.expire_date as exp_date')->orderBy('stocks.time_created', 'desc')->get();

            $sale_items = AnSaleItem::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->select('products.id as p_id', 'products.name as name', 'products.basic_unit as basic_unit', 'an_sale_items.product_id as product_id', 'an_sale_items.id as id', 'an_sale_items.quantity_sold as quantity_sold', 'an_sale_items.buying_per_unit as buying_per_unit', 'an_sale_items.buying_price as buying_price', 'an_sale_items.price_per_unit as price_per_unit', 'an_sale_items.discount as discount', 'an_sale_items.price as price', 'an_sale_items.total_discount as total_discount', 'an_sale_items.tax_amount as tax_amount', 'an_sale_items.time_created as created_at')->orderBy('an_sale_items.time_created', 'desc')->get();

            $damages = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->get();

            $transfers = TransferOrder::where('transfer_orders.shop_id', $shop->id)->join('transfer_order_items', 'transfer_order_items.transfer_order_id', '=', 'transfer_orders.id')->where('transfer_order_items.product_id', $product->id)->select('transfer_orders.order_no as order_no', 'transfer_orders.order_date as order_date', 'transfer_orders.destination_id as destination_id', 'transfer_orders.reason as reason', 'transfer_orders.user_id as user_id', 'transfer_order_items.quantity as quantity', 'transfer_order_items.created_at as created_at')->orderBy('transfer_order_items.created_at', 'desc')->get();

            $t_in = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
            $t_out = AnSaleItem::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
            $t_dam = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
            $t_transfer = TransferOrder::where('transfer_orders.shop_id', $shop->id)->join('transfer_order_items', 'transfer_order_items.transfer_order_id', '=', 'transfer_orders.id')->where('transfer_order_items.product_id', $product->id)->sum('quantity');

            $returned = SaleReturnItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
            $productunits = ProductUnit::where('product_id', $product->id)->where('shop_id', $shop->id)->get();
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
            $page = 'Product details';
            $title = $product->name . ' details';
            $title_sw = ' Maelezo ya ' . $product->name;

            return view('products.show', compact('page', 'title', 'title_sw', 'product', 'stocks', 'sale_items', 'suppliers', 'd_stocks', 'damages', 'transfers', 't_transfer', 't_in', 't_out', 't_dam', 'returned', 'shop', 'bsetting', 'settings', 'productunits', 'units'));
        }
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
        $product = $shop->products()->where('product_id', Crypt::decrypt($id))->first();
        if (is_null($product)) {
            return redirect('forbiden');
        } else {

            $page = 'Edit product';
            $title = 'Edit product';
            $title_sw = 'Hariri Bidhaa';

            $units = array(
                '' => 'Select Quantity Type',
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

            $settings = Setting::where('shop_id', $shop->id)->first();
            $p_unit = $units[$product->basic_unit];
            return view('products.edit', compact('page', 'title', 'title_sw', 'units', 'product', 'p_unit', 'settings'));
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
        $product = Product::find(decrypt($id));
        $prodshops = $product->shops()->count();

        if ($prodshops > 1 && ($request['name'] != $product->name || $request['basic_unit'] != $product->basic_unit)) {
            $newproduct = Product::where('name', $request['name'])->where('basic_unit', $request['basic_unit'])->first();
            if (is_null($newproduct)) {
                $newproduct = Product::create([
                    'name' => $request['name'],
                    'basic_unit' => $request['basic_unit']
                ]);
            }
            $shopprod = $shop->products()->where('product_id', $product->id)->first();
            if (!is_null($shopprod)) {
                $shopprod->pivot->product_id = $newproduct->id;
                $shopprod->pivot->description = $request['description'];
                $shopprod->pivot->product_no  = $request['product_no'];
                $shopprod->pivot->save();
            }

            $prodstocks = Stock::where('product_id', $product->id)->where('shop_id', $shop->id)->get();
            foreach ($prodstocks as $key => $stock) {
                $stock->product_id = $newproduct->id;
                $stock->save();
            }

            $proditems = AnSaleItem::where('product_id', $product->id)->where('shop_id', $shop->id)->get();
            foreach ($proditems as $key => $item) {
                $item->product_id = $newproduct->id;
                $item->save();
            }
        } else {
            $product->name = $request['name'];
            $product->basic_unit = $request['basic_unit'];
            $product->save();

            $prodshop = $shop->products()->where('product_id', $product->id)->first();
            if (!is_null($prodshop)) {
                $newbarcode = null;
                $settings = Setting::where('shop_id', $shop->id)->first();
                if ($settings->use_barcode && is_null($request['barcode'])) {
                    $codes = array();
                    $bcodes = $shop->products()->select('barcode')->get();
                    foreach ($bcodes as $key => $bcode) {
                        array_push($codes, $bcode->barcode);
                    }
                    $max_code = max($codes);
                    if (is_numeric($max_code)) {
                        $newbarcode = $max_code + 1;
                    }
                } else {
                    $newbarcode = $request['barcode'];
                }
                $prodshop->pivot->barcode = $newbarcode;
                $prodshop->pivot->description = $request['description'];
                $prodshop->pivot->product_no  = $request['product_no'];
                $prodshop->pivot->save();
            }
        }

        $message = 'Your product was updated successfully!';

        return redirect('/products')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find(decrypt($id));
        $user = Auth::user();
        $shop = Shop::find(Session::get('shop_id'));
        $stocks = Stock::where('product_id', $product->id)->where('shop_id', $shop->id)->get();
        foreach ($stocks as $key => $stock) {
            $stock->delete();
        }
        $shop->products()->detach($product);

        $message = 'You have successfully removed this product from your product list!';

        return redirect('/products')->with('message', $message);
    }

    public function deleteMultiple(Request $request)
    {

        $shop = Shop::find(Session::get('shop_id'));

        $user = Auth::user();
        if (!is_null($request->input('id'))) {

            foreach ($request->input('id') as $key => $id) {
                $product = Product::find($id);
                $stocks = Stock::where('product_id', $product->id)->where('shop_id', $shop->id)->get();
                foreach ($stocks as $key => $stock) {
                    $stock->delete();
                }
                $shop->products()->detach($product);
                foreach ($shop->categories()->get() as $key => $category) {
                    $catprod = $category->products()->where('product_id', $product->id)->first();
                    if (!is_null($catprod)) {
                        $category->products()->detach($catprod);
                    }
                }
            }
            $success = 'Products were  successfully removed from your product list!';
            return redirect('products')->with('success', $success);
        } else {

            $warning = 'No items selected. Please select at least one item';
            return redirect('products')->with('warning', $warning);
        }
    }

    public function postPrice(Request $request)
    {
        $product = Product::find($request['product_id']);
        $shop = Shop::find(Session::get('shop_id'));
        $shop_product = $shop->products()->where('product_id', $product->id)->first();
        $shop_product->pivot->price_per_unit = $request['new_unit_price'];
        $shop_product->pivot->wholesale_price = $request['wholesale_price'];
        $shop_product->pivot->save();

        $message = 'Price was successfully updated';

        return redirect()->route('products.show', encrypt($product->id))->with('message', $message);
    }

    public function newBuyPrice(Request $request)
    {

        $shop = Shop::find(Session::get('shop_id'));
        $product = Product::find($request['product_id']);
        $shop_product = $shop->products()->where('product_id', $product->id)->first();
        $shop_product->pivot->buying_per_unit = $request['buying_per_unit'];
        $shop_product->pivot->save();

        $message = 'Price was successfully updated';

        return redirect()->route('products.show', Crypt::encrypt($product->id))->with('message', $message);
    }


    public function newReorderPoint(Request $request)
    {

        $shop = Shop::find(Session::get('shop_id'));
        $product = Product::find($request['product_id']);
        $shop_product = $shop->products()->where('product_id', $product->id)->first();
        $shop_product->pivot->reorder_point = $request['reorder_point'];
        $shop_product->pivot->save();

        $message = 'Re-order Point was successfully updated';

        return redirect()->route('products.show', Crypt::encrypt($product->id))->with('message', $message);
    }

    public function priceList(Request $request)
    {
        $page = 'Price List';
        $title = 'Price list';
        $title_sw = 'Orodha ya Bei';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        if ($shop->business_type_id == 3) {
            $prices = $shop->services()->get();
            return view('services.pricing', compact('page', 'title', 'title_sw', 'prices', 'settings'));
        } elseif ($shop->business_type_id == 4) {
            $prices = $shop->products()->get();
            $serv_prices = $shop->services()->get();
            return view('products.pricing', compact('page', 'title', 'title_sw', 'prices', 'serv_prices', 'settings'));
        } else {
            $prices = $shop->products()->get();

            $pnos = $shop->products()->whereNotNull('product_no')->count();
            $pls = $shop->products()->whereNotNull('location')->count();

            return view('products.price-list', compact('page', 'title', 'title_sw', 'prices', 'settings', 'pnos', 'pls'));
        }
    }

    //Auto generate barcodes
    public function generateBarcode()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $bsetting = BarcodeSetting::where('shop_id', $shop->id)->first();
        $products = $shop->products()->orderBy('name', 'asc')->get();

        $bsetting = BarcodeSetting::where('shop_id', $shop->id)->first();

        $now = Carbon::now();
        $shidlen = 0;
        $code = '';
        $codes = array();
        if ($bsetting->code_type === 'EAN8') {
            $shidlen = strlen($shop->id);
            foreach ($products as $key => $product) {
                $code = $shop->id . str_pad($key + 1, 7 - $shidlen, '0', STR_PAD_LEFT);
                array_push($codes, $code);
                $product->pivot->barcode = $code;
                $product->pivot->save();
            }
        } else {
            $shidlen = strlen($shop->id);
            foreach ($products as $key => $product) {
                $code = $shop->id . str_pad($key + 1, $bsetting->code_length - $shidlen, '0', STR_PAD_LEFT);
                array_push($codes, $code);
                $product->pivot->barcode = $code;
                $product->pivot->save();
            }
        }

        $success = 'Barcodes were generate successfully';
        return redirect('products')->with('success', $success);
    }

    public function changeLocation(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $product = $shop->products()->where('product_id', $request['product_id'])->first();

        if (!is_null($product)) {
            $product->pivot->location = $request['location'];
            $product->pivot->save();
        }

        return redirect()->back()->with('success', 'Product location updated successfully');
    }

    public function setActualPrices($id)
    {

        $product = Product::find(decrypt($id));

        $shop = Shop::find(Session::get('shop_id'));
        $shop_product = $shop->products()->where('product_id', $product->id)->first();
        if (!is_null($shop_product)) {
            $items = AnSaleItem::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->get();
            foreach ($items as $key => $item) {
                $item->buying_per_unit = $shop_product->pivot->buying_per_unit;
                $item->buying_price = $item->quantity_sold * $item->buying_per_unit;
                $item->price_per_unit = $shop_product->pivot->price_per_unit;
                $item->price = $item->price_per_unit * $item->quantity_sold;
                $item->tax_amount = ($shop_product->pivot->price_with_vat - $shop_product->pivot->price_per_unit) * $item->quantity_sold;
                $item->save();

                $amountp = AnSaleItem::where('an_sale_id', $item->an_sale_id)->sum('price');
                $discountp = AnSaleItem::where('an_sale_id', $item->an_sale_id)->sum('total_discount');
                $amounts = ServiceSaleItem::where('an_sale_id', $item->an_sale_id)->sum('total');
                $discounts = ServiceSaleItem::where('an_sale_id', $item->an_sale_id)->sum('total_discount');
                $taxp = AnSaleItem::where('an_sale_id', $item->an_sale_id)->sum('tax_amount');
                $taxs = ServiceSaleItem::where('an_sale_id', $item->an_sale_id)->sum('tax_amount');

                $sale = AnSale::find($item->an_sale_id);
                $sale->sale_amount = ($amountp + $amounts);
                $sale->sale_discount = ($discountp + $discounts);
                $sale->tax_amount = ($taxp + $taxs);
                $sale->save();

                if (($sale->sale_amount - $sale->sale_discount) == $sale->sale_amount_paid) {
                    $sale->status = 'Paid';
                    $sale->time_paid = \Carbon\Carbon::now();
                    $sale->save();
                } elseif (($sale->sale_amount - $sale->sale_discount) > $sale->sale_amount_paid && $sale->sale_amount_paid > 0) {
                    $sale->status = 'Partially Paid';
                    $sale->time_paid = null;
                    $sale->save();
                } elseif (($sale->sale_amount - $sale->sale_discount) < $sale->sale_amount_paid) {
                    $sale->status = 'Excess Paid';
                    $sale->time_paid = \Carbon\Carbon::now();
                    $sale->save();
                } else {
                    $sale->status = 'Unpaid';
                    $sale->time_paid = null;
                    $sale->save();
                }

                $invoice = Invoice::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                if (!is_null($invoice)) {
                    $acctrans = CustomerTransaction::where('invoice_no', $invoice->inv_no)->where('shop_id', $shop->id)->first();
                    if (!is_null($acctrans)) {
                        $acctrans->amount = ($sale->sale_amount - $sale->sale_discount);
                        $acctrans->save();
                    }
                } else {
                    $acctrans = CustomerAccount::where('sale_no', $sale->sale_no)->where('shop_id', $shop->id)->first();
                    if (!is_null($acctrans)) {
                        $acctrans->amount = ($sale->sale_amount - $sale->sale_discount);
                        $acctrans->save();
                    }
                }
            }
        }
        $success = 'Items updated successfully';
        return redirect()->route('products.show', encrypt($product->id))->with('success', $success);
    }

    public function download()
    {
        if (File::exists(public_path('sample-products.xlsx'))) {
            return response()->download(public_path('sample-products.xlsx'));
        } else {
            return redirect()->back()->witherrormessage('NO such File Exists');
        }
    }
}
