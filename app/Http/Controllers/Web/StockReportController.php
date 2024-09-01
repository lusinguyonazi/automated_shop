<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use Log;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\Product;
use App\Models\AnSaleItem;
use App\Models\ProdDamage;
use App\Models\TransferOrderItem;
use App\Models\TransferOrder;
use App\Models\SaleReturnItem;
use App\Models\Setting;


class StockReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $shops = $user->shops()->get();
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $instocks = array();
        $stock_ins = null;
        if ($settings->is_filling_station) {

            $currstatus = 'All Items';

            $statuses = array(
                ['value' => 'All Items'],
                ['value' => 'In Stock'],
                ['value' => 'Low Stock'],
                ['value' => 'Out of Stock']
            );

            $start_date = null;
            $end_date = null;
            //check if user opted for date range
            $is_post_query = false;
            $currstore = null;

            if (is_null($request['status']) && is_null($request['store'])) {
                $currstore = Shop::find(Session::get('shop_id'));
            } elseif (!is_null($request['store'])) {
                $currstore = Shop::find($request['store']);
            }

            $prodstocks = array();
            if (!is_null($currstore)) {
                $shop = $currstore;
                if (is_null($request['status']) || $request['status'] == 'All Items') {
                    $products = $shop->products()->get([
                        \DB::raw('id'),
                        \DB::raw('name as name'),
                        \DB::raw('in_stock as in_stock'),
                        \DB::raw('status as status')
                    ]);
                } else {
                    $products = $shop->products()->where('status', $request['status'])->get([
                        \DB::raw('id'),
                        \DB::raw('name as name'),
                        \DB::raw('in_stock as in_stock'),
                        \DB::raw('status as status')
                    ]);
                    $currstatus = $request['status'];
                }
                foreach ($products as $key => $stock) {
                    array_push($prodstocks, ['id' => $stock->id, 'name' => $stock->name, 'in_stock' => $stock->in_stock, 'status' => $stock->status]);
                }
            } else {
                foreach ($shops as $key => $shop) {
                    if (!is_null($request['status'])) {

                        if ($request['status'] == 'All Items') {
                            $products = $shop->products()->get([
                                \DB::raw('id'),
                                \DB::raw('name as name'),
                                \DB::raw('in_stock as in_stock'),
                                \DB::raw('status as status')
                            ]);
                        } else {
                            $products = $shop->products()->where('status', $request['status'])->get([
                                \DB::raw('id'),
                                \DB::raw('name as name'),
                                \DB::raw('in_stock as in_stock'),
                                \DB::raw('status as status')
                            ]);
                            $currstatus = $request['status'];
                        }
                    } else {
                        $products = $shop->products()->get([
                            \DB::raw('id'),
                            \DB::raw('name as name'),
                            \DB::raw('in_stock as in_stock'),
                            \DB::raw('status as status')
                        ]);
                    }
                    foreach ($products as $key => $stock) {
                        array_push($prodstocks, ['id' => $stock->id, 'name' => $stock->name, 'in_stock' => $stock->in_stock, 'status' => $stock->status]);
                    }
                }
            }


            $result = array();
            foreach ($prodstocks as $value) {
                if (isset($result[$value["name"]])) {
                    $result[$value["name"]]["in_stock"] += $value["in_stock"];
                } else {
                    $result[$value["name"]] = $value;
                }
            }

            // return $result;

            $stockstatus = [];
            foreach ($result as $key => $value) {
                $shopsstocks = array();
                foreach ($shops as $key => $store) {
                    $sqty = 0;
                    $sq = $store->products()->where('product_id', $value['id'])->first();
                    if (!is_null($sq)) {
                        $sqty = $sq->pivot->in_stock;
                    }
                    array_push($shopsstocks, [$store->name => $sqty]);
                }
                array_push($stockstatus, array_merge($value, $shopsstocks));
            }

            // return $stockstatus;

            $crtime = \Carbon\Carbon::now();
            $reporttime = $crtime->toDayDateTimeString();
            $page = 'Stock Report';
            $title = 'Stock Status Report';
            $title_sw = 'Ripoti ya hali ya Stock';
            return view('reports.inventory.index', compact('page', 'title', 'title_sw', 'reporttime', 'instocks', 'shop', 'currstatus', 'statuses', 'is_post_query', 'start_date', 'end_date', 'settings', 'stockstatus', 'shops', 'currstore'));
        } else {
            
            $now = Carbon::now();
            $start = null;
            $end = null;
            $start_date = null;
            $end_date = null;

            //check if user opted for date range
            $is_post_query = false;
            if (!is_null($request['stock_date'])) {
                $start_date = $request['stock_date'];
                $end_date = $request['stock_date'];
                $start = $request['stock_date'] . ' 00:00:00';
                $end = $request['stock_date'] . ' 23:59:59';
                $is_post_query = true;
            } else if (!is_null($request['start_date'])) {
                $start_date = $request['start_date'];
                $end_date = $request['end_date'];
                $start = $request['start_date'] . ' 00:00:00';
                $end = $request['end_date'] . ' 23:59:59';
                $is_post_query = true;
            } else {
                $start = $now->startOfMonth();
                $end = \Carbon\Carbon::now();
                $is_post_query = false;
            }

            $duration = 'From ' . date('d-m-Y', strtotime($start)) . ' To ' . date('d-m-Y', strtotime($end)) . '.';
            $duration_sw = 'Kuanzia ' . date('d-m-Y', strtotime($start)) . ' Mpaka ' . date('d-m-Y', strtotime($end)) . '.';


            $products = $shop->products()->get();

            $product = null;
            $damages = null;
            if (!is_null($request->product_id)) {
                $product = Product::find($request->product_id);
                $damages = ProdDamage::where('shop_id', $shop->id)->where('product_id', $product->id)->whereBetween('time_created', [$start, $end])->join('products', 'products.id', '=', 'prod_damages.product_id')->select('products.name as name', 'time_created', 'deph_measure', 'in_stock', 'quantity')->get();
            } else {
                $damages = ProdDamage::where('shop_id', $shop->id)->whereBetween('time_created', [$start, $end])->join('products', 'products.id', '=', 'prod_damages.product_id')->select('products.name as name', 'time_created', 'deph_measure', 'in_stock', 'quantity')->get();
            }

            $stock_ins = $shop->products()->join('stocks', 'stocks.product_id', '=', 'product_shop.product_id')->where('stocks.shop_id', $shop->id)->where('stocks.is_deleted', false)->groupBy('product_shop.product_id')->get([
                \DB::raw('name as name'),
                \DB::raw('SUM(quantity_in) as stock_in'),
                \DB::raw('in_stock as in_stock'),
                \DB::raw('status as status')
            ]);

            foreach ($stock_ins as $key => $stock) {
                $returned = SaleReturnItem::where('product_id', $stock->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                $sold = AnSaleItem::where('product_id', $stock->pivot->product_id)->where('shop_id', $shop->id)->where('is_deleted', false)->sum('quantity_sold');
                $damage = ProdDamage::where('product_id', $stock->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                $transfered = TransferOrderItem::where('product_id', $stock->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                array_push($instocks, array_merge($stock->toArray(), ['returned' => $returned], ['sold' => $sold], ['transfered' => $transfered], ['damage' => $damage]));
            }

            $crtime = \Carbon\Carbon::now();
            $reporttime = $crtime->toDayDateTimeString();
            $page = 'Stock Report';
            $title = 'Stock Status Report';
            $title_sw = 'Ripoti ya hali ya Stock';
            return view('reports.inventory.index-filling', compact('page', 'title', 'title_sw', 'reporttime', 'instocks', 'shop', 'is_post_query', 'start_date', 'end_date', 'settings', 'damages', 'product', 'products', 'duration', 'duration_sw'));
        }
    }


    public function transfers(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        if (!is_null($shop)) {

            $now = Carbon::now();
            $start = null;
            $end = null;
            $start_date = null;
            $end_date = null;

            //check if user opted for date range
            $is_post_query = false;
            if (!is_null($request['stock_date'])) {
                $start_date = $request['stock_date'];
                $end_date = $request['stock_date'];
                $start = $request['stock_date'] . ' 00:00:00';
                $end = $request['stock_date'] . ' 23:59:59';
                $is_post_query = true;
            } else if (!is_null($request['start_date'])) {
                $start_date = $request['start_date'];
                $end_date = $request['end_date'];
                $start = $request['start_date'] . ' 00:00:00';
                $end = $request['end_date'] . ' 23:59:59';
                $is_post_query = true;
            } else {
                $start = $now->startOfMonth();
                $end = \Carbon\Carbon::now();
                $is_post_query = false;
            }

            $duration = 'From ' . date('d-m-Y', strtotime($start)) . ' To ' . date('d-m-Y', strtotime($end)) . '.';
            $duration_sw = 'Kuanzia ' . date('d-m-Y', strtotime($start)) . ' Mpaka ' . date('d-m-Y', strtotime($end)) . '.';

            $transfers = TransferOrderItem::where('shop_id', $shop->id)->whereBetween('transfer_order_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'transfer_order_items.product_id')->get();



            $received_transfers_normal = TransferOrder::where('destination_id', $shop->id)->where('is_transfomation_transfer', false)->whereBetween('transfer_orders.created_at', [$start, $end])->join('transfer_order_items', 'transfer_orders.id', '=', 'transfer_order_items.transfer_order_id')->join('products', 'products.id', '=', 'transfer_order_items.product_id')->get([
                'product_id',
                'transfer_orders.shop_id',
                'source_stock',
                'destin_stock',
                'name',
                'order_no',
                'transfer_order_id',
                'quantity',
            ]);


            $received_transfers_transfo = TransferOrder::where('destination_id', $shop->id)->where('is_transfomation_transfer', true)->whereBetween('transfer_orders.created_at', [$start, $end])->join('transformation_transfer_items', 'transformation_transfer_items.transfer_order_id', '=', 'transfer_orders.id')->join('products', 'products.id', '=', 'transformation_transfer_items.product_id')->get([
                'product_id',
                'transfer_orders.shop_id',
                'source_stock',
                'destin_stock',
                'name',
                'order_no',
                'transfer_order_id',
                'quantity',
            ]);


            $received_transfers = collect([$received_transfers_transfo, $received_transfers_normal])->collapse();


            $crtime = \Carbon\Carbon::now();
            $reporttime = $crtime->toDayDateTimeString();
            $page = 'Stock Report';
            $title = 'Stock Transfer Report';
            $title_sw = 'Ripoti ya Uhamishaji wa Stock';
            return view('reports.inventory.transfer', compact('page', 'title', 'title_sw', 'reporttime', 'transfers', 'shop', 'is_post_query', 'start_date', 'end_date', 'settings', 'duration', 'duration_sw', 'received_transfers'));
        }
    }



    public function reorderReports()
    {
        $shop = Shop::find(Session::get('shop_id'));

        $start_date = null;
        $end_date = null;
        $duration = null;
        $duration_sw = null;
        //check if user opted for date range
        $is_post_query = false;

        $products = $shop->products()->whereRaw('product_shop.in_stock <= product_shop.reorder_point')->get();
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        $page = 'Stock Report';
        $title = 'Stock Reorder Report';
        $title_sw = 'Ripoti ya Kuagiza Stock';
        return view('reports.inventory.reorder', compact('page', 'title', 'title_sw', 'reporttime', 'shop', 'products',  'duration', 'duration_sw', 'is_post_query', 'start_date', 'end_date'));
    }


    public function stockCapital(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $products = $shop->products()->select('id as id', 'name as name', 'basic_unit as basic_unit', 'product_shop.time_created as created_at', 'product_shop.in_stock as in_stock', 'product_shop.price_per_unit as price_per_unit',  'product_shop.wholesale_price as wholesale_price', 'product_shop.buying_per_unit as buying_per_unit')->get();
        $total = 0;
        $total_sales = 0;
        $total_wholesales = 0;
        foreach ($products as $key => $product) {
            $lateststock = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->latest()->first();
            if (!is_null($lateststock) && $product->in_stock > $lateststock->quantity_in && $product->buying_per_unit != $lateststock->buying_per_unit) {

                $total = $total + ($lateststock->quantity_in * $lateststock->buying_per_unit) + (($product->in_stock - $lateststock->quantity_in) * $product->buying_per_unit);
            } else {
                $total = $total + ($product->in_stock * $product->buying_per_unit);
            }
            $total_sales = $total_sales + ($product->in_stock * $product->price_per_unit);
            $total_wholesales = $total_wholesales + ($product->in_stock * $product->wholesale_price);
        }

        $total_profit = $total_sales - $total;
        $total_ws_profit = $total_wholesales - $total;

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        $page = 'Stock Report';
        $title = 'Stock Capital Report';
        $title_sw = 'Ripoti ya Mtaji';
        return view('reports.inventory.capital', compact('products', 'page', 'title', 'title_sw', 'reporttime', 'total', 'total_sales', 'total_wholesales', 'total_profit', 'total_ws_profit', 'shop', 'settings'));
    }

    public function stockTaking(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));

        $products = $shop->products()->get();

        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;
        $end_date = null;

        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['stock_date'])) {
            $start_date = $request['stock_date'];
            $end_date = $request['stock_date'];
            $start = $request['stock_date'] . ' 00:00:00';
            $end = $request['stock_date'] . ' 23:59:59';
            $is_post_query = true;
        } else if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        } else {
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        $duration = 'From ' . date('d-m-Y', strtotime($start)) . ' To ' . date('d-m-Y', strtotime($end)) . '.';
        $duration_sw = 'Kuanzia ' . date('d-m-Y', strtotime($start)) . ' Mpaka ' . date('d-m-Y', strtotime($end)) . '.';


        $product = null;
        if (!is_null($request['product_id'])) {
            $product = Product::find($request['product_id']);

            $stocks = Stock::where('shop_id', $shop->id)->where('product_id', $product->id)->where('is_deleted', false)->whereBetween('stocks.time_created', [$start, $end])->join('products', 'products.id', '=', 'stocks.product_id')->orderBy('time_created', 'desc')->get();
        } else {

            $stocks = Stock::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('stocks.time_created', [$start, $end])->join('products', 'products.id', '=', 'stocks.product_id')->orderBy('time_created', 'desc')->get();
        }

        $total_buying = 0;

        foreach ($stocks as $key => $value) {
            $total_buying += $value->buying_per_unit * $value->quantity_in;
        }

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        $page = 'Reports';
        $title = 'Purchasing Report';
        $title_sw = 'Ripoti ya Manunuzi';
        return view('reports.inventory.purchases', compact('page', 'title', 'title_sw', 'stocks', 'duration', 'duration_sw', 'is_post_query', 'product', 'products', 'start_date', 'end_date', 'total_buying', 'reporttime', 'shop'));
    }



    public function stockExpires(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;
        $end_date = null;

        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['stock_date'])) {
            $start_date = $request['stock_date'];
            $end_date = $request['stock_date'];
            $start = $request['stock_date'] . ' 00:00:00';
            $end = $request['stock_date'] . ' 23:59:59';
            $is_post_query = true;
        } else if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        } else {
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        $duration = 'From ' . date('d-m-Y', strtotime($start)) . ' To ' . date('d-m-Y', strtotime($end)) . '.';
        $duration_sw = 'Kuanzia ' . date('d-m-Y', strtotime($start)) . ' Mpaka ' . date('d-m-Y', strtotime($end)) . '.';


        $product = null;
        $products = $shop->products()->select('id as id', 'name as name', 'basic_unit as basic_unit', 'product_shop.time_created as created_at', 'product_shop.in_stock as in_stock', 'product_shop.price_per_unit as price_per_unit',  'product_shop.wholesale_price as wholesale_price', 'product_shop.buying_per_unit as buying_per_unit')->get();

        $expstocks = array();
        foreach ($products as $key => $product) {
            $stocks = Stock::where('stocks.shop_id', $shop->id)->where('product_id', $product->id)->where('is_deleted', false)->whereNotNull('expire_date')->orderBy('created_at', 'DESC')->get();
            $instock = $product->in_stock;
            foreach ($stocks as $key => $stock) {
                $status = 'No';
                if ($stock->expire_date < Carbon::now()) {
                    $status = 'Yes';
                }

                $pdate = Carbon::parse($stock->time_created);
                $edate = Carbon::parse($stock->expire_date);
                $days = $edate->diffInDays($pdate);
                if ($instock > 0) {
                    if ($instock <= $stock->quantity_in) {
                        $qty_expired = $instock;
                        $expstock = ['name' => $product->name, 'quantity_in' => $stock->quantity_in, 'qty_expired' => $qty_expired, 'purchase_date' => $stock->time_created, 'expire_date' => $stock->expire_date, 'numdays' => $days, 'status' => $status, 'buying_per_unit' => $stock->buying_per_unit];
                        array_push($expstocks, $expstock);
                    } else {
                        $qty_expired = $stock->quantity_in;
                        $expstock = ['name' => $product->name, 'quantity_in' => $stock->quantity_in, 'qty_expired' => $qty_expired, 'purchase_date' => $stock->time_created, 'expire_date' => $stock->expire_date, 'numdays' => $days, 'status' => $status, 'buying_per_unit' => $stock->buying_per_unit];
                        array_push($expstocks, $expstock);
                    }
                }
                $instock -= $stock->quantity_in;
            }
        }

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        $page = 'Stock Report';
        $title = 'Expiration Report';
        $title_sw = 'Ripoti ya Kumalizika Muda';
        return view('reports.inventory.expires', compact('expstocks', 'page', 'title', 'title_sw', 'reporttime', 'duration', 'duration_sw', 'is_post_query', 'product', 'products', 'start_date', 'end_date', 'shop', 'settings'));
    }
}
