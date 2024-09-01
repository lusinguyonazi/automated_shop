<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Session;
use DB;
use Auth;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\User;
use App\Models\AnSale;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Product;
use App\Models\Service;
use App\Models\Customer;
use App\Models\CashOut;
use App\Models\CashIn;
use App\Models\SaleReturn;
use App\Models\Setting;
use App\Models\Device;
use App\Models\DeviceSale;
use App\Models\DeviceExpense;
use App\Models\Purchase;
use App\Models\AccountTransaction;
use App\Models\TransferOrderItem;
use App\Models\Grade;
use App\Models\OCAmount;
use App\Models\CustomerTransaction;
use App\Models\SupplierTransaction;
use App\Models\SalePayment;
use App\Models\ExpensePayment;
use App\Models\BusinessValue;
use App\Models\Category;
use App\Models\PurchasePayment;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
      $page = 'Reports';
      $title = 'General Reports';
      $title_sw = 'Ripoti ya ujumla';

      $shop = Shop::find(Session::get('shop_id'));
      $settings = Setting::where('shop_id', $shop->id)->first();
      $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
      $customers = Customer::where('shop_id', $shop->id)->get();
      $users = $shop->users()->get();

      $now = Carbon::now();
      $start = null;
      $end = null;
      $start_date = null;            
      $end_date = null;
      $devices = null;
      $servsales = null;
      $expenses = null;
      $ctnexpenses = null;
      $all_expenses = null;
      $all_ctnexpenses = null;
      $total_serv_selling = null;
      $tsales = null;
      $total_paid = null;
      $total_vat = null;
      $total_expenses = null;
      if ($shop->business_type_id == 3) {
        $devices = Device::where('shop_id', $shop->id)->get();
      }

      $device = null;
      if (!is_null($request['device_id'])) {
        $device = Device::find($request['device_id']);
      }
      //check if user opted for date range
      $is_post_query = false;
      if (!is_null($request['start_date'])) {
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        $start = $request['start_date'].' 00:00:00';
        $end = $request['end_date'].' 23:59:59';
        $is_post_query = true;
      }else{
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $is_post_query = false;
      }

      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $categories = Category::where('shop_id', $shop->id)->get();
      $category = Category::find($request['category_id']);

      $sales = null; $returns = null; $total_prod_selling = null; $total_buying = null; $total_return_prod_selling = null; $total_return_buying = null; $shared_expenses = 0;
      if (!is_null($category)) {
        
        $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->groupBy('price_per_unit')->groupBy('discount')->groupBy('buying_per_unit')->groupBy('products.name')->orderBy('an_sale_items.time_created', 'desc')->get([
          \DB::raw('products.name as name'),
          \DB::raw('SUM(quantity_sold) as quantity'),          
          \DB::raw('price_per_unit as price_per_unit'),
          \DB::raw('an_sale_items.tax_amount as tax'),
          \DB::raw('SUM(price) as price'),
          \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
          \DB::raw('discount as discount'),
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('buying_per_unit as buying_per_unit'),
          \DB::raw('SUM(buying_price) as buying_price'),          
          \DB::raw('an_sale_items.time_created as created_at')
        ]);

        $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'sale_return_items.product_id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->groupBy('price_per_unit')->groupBy('buying_per_unit')->groupBy('products.name')->orderBy('sale_return_items.created_at', 'desc')->get([
          \DB::raw('products.name as name'),
          \DB::raw('SUM(quantity) as quantity'),      
          \DB::raw('price_per_unit as price_per_unit'),
          \DB::raw('sale_return_items.tax_amount as tax'),
          \DB::raw('SUM(price) as price'),
          \DB::raw('SUM(sale_return_items.tax_amount) as tax_amount'),
          \DB::raw('discount as discount'),
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('buying_per_unit as buying_per_unit'),
          \DB::raw('SUM(buying_price) as buying_price'), 
          \DB::raw('sale_return_items.created_at as created_at')
        ]);


        $total_prod_selling = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->sum('price')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->sum('total_discount')+AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->sum('an_sale_items.tax_amount');

        
        $total_buying = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->sum('buying_price');

         $total_return_prod_selling = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'sale_return_items.product_id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->sum('price')-SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'sale_return_items.product_id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->sum('total_discount')+SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'sale_return_items.product_id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->sum('sale_return_items.tax_amount');

        $total_return_buying = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'sale_return_items.product_id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->sum('buying_price');
      
      }else{
        $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->groupBy('price_per_unit')->groupBy('discount')->groupBy('buying_per_unit')->groupBy('products.name')->orderBy('an_sale_items.time_created', 'desc')->get([
          \DB::raw('products.name as name'),
          \DB::raw('SUM(quantity_sold) as quantity'),          
          \DB::raw('price_per_unit as price_per_unit'),
          \DB::raw('an_sale_items.tax_amount as tax'),
          \DB::raw('SUM(price) as price'),
          \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
          \DB::raw('discount as discount'),
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('buying_per_unit as buying_per_unit'),
          \DB::raw('SUM(buying_price) as buying_price'),          
          \DB::raw('an_sale_items.time_created as created_at')
        ]);

        $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'sale_return_items.product_id')->groupBy('price_per_unit')->groupBy('buying_per_unit')->groupBy('products.name')->orderBy('sale_return_items.created_at', 'desc')->get([
          \DB::raw('products.name as name'),
          \DB::raw('SUM(quantity) as quantity'),      
          \DB::raw('price_per_unit as price_per_unit'),
          \DB::raw('sale_return_items.tax_amount as tax'),
          \DB::raw('SUM(price) as price'),
          \DB::raw('SUM(sale_return_items.tax_amount) as tax_amount'),
          \DB::raw('discount as discount'),
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('buying_per_unit as buying_per_unit'),
          \DB::raw('SUM(buying_price) as buying_price'), 
          \DB::raw('sale_return_items.created_at as created_at')
        ]);

        $tsamout = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('price');
        $tdsc = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('total_discount');
        $tvt =AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('an_sale_items.tax_amount');

        $total_prod_selling = ($tsamout+$tvt)-$tdsc;;
        
        $total_buying = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('buying_price');

         $total_return_prod_selling = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->sum('price')-SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->sum('total_discount')+SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->sum('sale_return_items.tax_amount');

        $total_return_buying = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->sum('buying_price');
      }

 
      $total_return_gross_profit = $total_return_prod_selling-$total_return_buying;

      if (!is_null($device)) {
        $servsales = DeviceSale::where('device_id', $device->id)->join('an_sales', 'an_sales.id', '=', 'device_sales.an_sale_id')->where('an_sales.shop_id', $shop->id)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->groupBy('price')->groupBy('services.name')->orderBy('service_sale_items.time_created', 'desc')->get([
          \DB::raw('services.name as name'),
          \DB::raw('SUM(no_of_repeatition) as repeatition'),
          \DB::raw('price as price'),
          \DB::raw('SUM(total) as total'),
          \DB::raw('discount as discount'),
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('service_sale_items.time_created as created_at')
        ]);

        $total_serv_selling = DeviceSale::where('device_id', $device->id)->join('an_sales', 'an_sales.id', '=', 'device_sales.an_sale_id')->where('an_sales.shop_id', $shop->id)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->sum('total')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->sum('total_discount');


        $ctnexpenses = DeviceExpense::where('device_id', $device->id)->join('expenses', 'expenses.id', '=', 'device_expenses.an_expense_id')->where('shop_id', $shop->id)->where('expire_at', '>', $start)->where('time_created', '<', $start)->orderBy('expense_type', 'asc')->get([
            \DB::raw('expense_type as expense_type'),
            \DB::raw('amount as amount'),
            \DB::raw('no_days as no_days'),
            \DB::raw('exp_vat as exp_vat'),
            \DB::raw('time_created as time_created'),
            \DB::raw('expire_at as expire_at')
          ]);

        $expenses = DeviceExpense::where('device_id', $device->id)->join('expenses', 'expenses.id', '=', 'device_expenses.an_expense_id')->where('shop_id', $shop->id)->whereBetween('time_created', [$start, $end])->orderBy('expense_type', 'asc')->get([
            \DB::raw('expense_type as expense_type'),
            \DB::raw('amount as amount'),
            \DB::raw('no_days as no_days'),
            \DB::raw('exp_vat as exp_vat'),
            \DB::raw('time_created as time_created'),
            \DB::raw('expire_at as expire_at')
          ]);

        $tsales = DeviceSale::where('device_id', $device->id)->join('an_sales', 'an_sales.id', '=', 'device_sales.an_sale_id')->where('shop_id', $shop->id)->whereBetween('time_created', [$start, $end])->get();
                  
        $total_expenses = DeviceExpense::where('device_id', $device->id)->join('expenses', 'expenses.id', '=', 'device_expenses.an_expense_id')->where('shop_id', $shop->id)->whereBetween('time_created', [$start, $end])->sum('amount');
      
      }else{

        $servsales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->groupBy('price')->groupBy('services.name')->orderBy('service_sale_items.time_created', 'desc')->get([
          \DB::raw('services.name as name'),
          \DB::raw('SUM(no_of_repeatition) as repeatition'),
          \DB::raw('price as price'),
          \DB::raw('SUM(total) as total'),
          \DB::raw('discount as discount'),
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('service_sale_items.time_created as created_at')
        ]);

        $total_serv_selling = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->sum('total')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->sum('total_discount');

        if (!is_null($category)) {

          $ctnexpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('expire_at', '>', $start)->where('time_created', '<', $start)->where('category_id', $category->id)->orderBy('expense_type', 'asc')->get([
            \DB::raw('expense_type as expense_type'),
            \DB::raw('amount as amount'),
            \DB::raw('no_days as no_days'),
            \DB::raw('exp_vat as exp_vat'),
            \DB::raw('time_created as time_created'),
            \DB::raw('expire_at as expire_at')
          ]);

          $expenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->where('category_id', $category->id)->orderBy('expense_type', 'asc')->get([
            \DB::raw('expense_type as expense_type'),
            \DB::raw('amount as amount'),
            \DB::raw('no_days as no_days'),
            \DB::raw('exp_vat as exp_vat'),
            \DB::raw('time_created as time_created'),
            \DB::raw('expire_at as expire_at')
          ]);


           $all_ctnexpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('expire_at', '>', $start)->where('time_created', '<', $start)->where('category_id', null)->orderBy('expense_type', 'asc')->get([
            \DB::raw('expense_type as expense_type'),
            \DB::raw('amount as amount'),
            \DB::raw('no_days as no_days'),
            \DB::raw('exp_vat as exp_vat'),
            \DB::raw('time_created as time_created'),
            \DB::raw('expire_at as expire_at')
          ]);

          $all_expenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->where('category_id', null)->orderBy('expense_type', 'asc')->get([
            \DB::raw('expense_type as expense_type'),
            \DB::raw('amount as amount'),
            \DB::raw('no_days as no_days'),
            \DB::raw('exp_vat as exp_vat'),
            \DB::raw('time_created as time_created'),
            \DB::raw('expire_at as expire_at')
          ]);

          $tsales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->get();
          
        }else{
          $ctnexpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('expire_at', '>', $start)->where('time_created', '<', $start)->orderBy('expense_type', 'asc')->get([
            \DB::raw('expense_type as expense_type'),
            \DB::raw('amount as amount'),
            \DB::raw('no_days as no_days'),
            \DB::raw('exp_vat as exp_vat'),
            \DB::raw('time_created as time_created'),
            \DB::raw('expire_at as expire_at')
          ]);

          $expenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->orderBy('expense_type', 'asc')->get([
            \DB::raw('expense_type as expense_type'),
            \DB::raw('amount as amount'),
            \DB::raw('no_days as no_days'),
            \DB::raw('exp_vat as exp_vat'),
            \DB::raw('time_created as time_created'),
            \DB::raw('expire_at as expire_at')
          ]);

          $tsales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->get();
        }
      }

      $myexpenses = collect([]);
      $totalexpenses = 0;
      //All Continue Expenses
      if (!is_null($ctnexpenses)) {
          
        foreach ($ctnexpenses as $key => $expense) {
          $expdays = 0;
          if ($expense->expire_at > $end && $expense->time_created < $start) {
            $expdays = \Carbon\Carbon::parse($end)->diffInDays(\Carbon\Carbon::parse($start))+1;
          }else{
            $expdays = \Carbon\Carbon::parse($expense->expire_at)->diffInDays(\Carbon\Carbon::parse($start))+1;
          }
          $amount = (($expense->amount-$expense->exp_vat)/$expense->no_days)*$expdays;
          $totalexpenses += $amount;
          $myexpenses->push(['expense_type' => $expense->expense_type, 'amount' => $amount]);
        }
      }

      //Categorized continue expenses
      if (!is_null($all_ctnexpenses)) {
          
        foreach ($all_ctnexpenses as $key => $expense) {
          $expdays = 0;
          if ($expense->expire_at > $end && $expense->time_created < $start) {
            $expdays = \Carbon\Carbon::parse($end)->diffInDays(\Carbon\Carbon::parse($start))+1;
          }else{
            $expdays = \Carbon\Carbon::parse($expense->expire_at)->diffInDays(\Carbon\Carbon::parse($start))+1;
          }
          $amount = ((($expense->amount-$expense->exp_vat)/$expense->no_days)*$expday)/$categories->count();
          $totalexpenses += $amount;
          $myexpenses->push(['expense_type' => $expense->expense_type, 'amount' => $amount]);
        }
      }

      //All Normal Expenses
      if (!is_null($expenses)) {
          
        foreach($expenses as $key => $expense) {
          $amount = 0;
          if ($expense->no_days == 1) {
            $amount = ($expense->amount-$expense->exp_vat);
            $totalexpenses += $amount; 
          }else{
            $expdays = 0;
            if($expense->expire_at > $end){
              $expdays= \Carbon\Carbon::parse($end)->diffInDays(\Carbon\Carbon::parse($expense->time_created))+1;
            }else{
              $expdays = $expense->no_days;
            }
            $amount = (($expense->amount-$expense->exp_vat)/$expense->no_days)*$expdays;
            $totalexpenses += $amount;
          }
          $myexpenses->push(['expense_type' => $expense->expense_type, 'amount' => $amount]);
        }
      }

      //All Shared expenses in Categorized business
      if (!is_null($all_expenses)) {
          
        foreach ($all_expenses as $key => $expense) {
          $amount = 0;
          if ($expense->no_days == 1) {
            $amount = ($expense->amount-$expense->exp_vat)/$categories->count();
            $totalexpenses += $amount; 
          }else{
            $expdays = 0;
            if($expense->expire_at > $end){
              $expdays= \Carbon\Carbon::parse($end)->diffInDays(\Carbon\Carbon::parse($expense->time_created))+1;
            }else{
              $expdays = $expense->no_days;
            }
            $amount = ((($expense->amount-$expense->exp_vat)/$expense->no_days)*$expdays)/$categories->count();
            $totalexpenses += $amount;
          }
          $myexpenses->push(['expense_type' => $expense->expense_type, 'amount' => $amount]);
        }
      }

      $groups = $myexpenses->groupby('expense_type');
      

      // we will use map to cumulate each group of rows into single row.
      // $group is a collection of rows that has the same opposition_id.
      $expenses = $groups->map(function ($group) {
          return [
              'expense_type' => $group->first()['expense_type'], // expense_type is constant inside the same group, so just take the first or whatever.
              'amount' => $group->sum('amount'),
          ];
      });
      
      $total_gross_profit = $total_prod_selling-$total_buying;

      $total_selling = ($total_prod_selling-$total_return_prod_selling)+$total_serv_selling;

      $gross_profit = $total_selling-($total_buying-$total_return_buying);


      $total_unpaid = 0;
      foreach ($tsales as $key => $tsale) {
        $total_paid += $tsale->sale_amount_paid;
        $total_vat += $tsale->tax_amount;
        $total_unpaid += ($tsale->sale_amount-$tsale->sale_discount-$tsale->adjustment-$tsale->sale_amount_paid);
      }

      $net_profit = 0; 
      // $net_profit = $gross_profit-$total_expenses-$total_vat;

      $cash_pay = SalePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start,$end])->where('pay_mode', 'Cash')->sum('amount');
      $mob_pay = SalePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start,$end])->where('pay_mode', 'Mobile Money')->sum('amount'); 
      $bank_pay = SalePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start,$end])->where('pay_mode', 'Bank')->sum('amount');

      $total_pay = $cash_pay+$mob_pay+$bank_pay;

      $cash_amount = $total_pay-$total_expenses;

      $total_sales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('sale_amount')-AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('sale_discount')-AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('adjustment');

      $total_expenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('amount');
      
      $total_collections = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->sum('amount')+CashIn::where('shop_id', $shop->id)->whereBetween('in_date', [$start, $end])->sum('amount');
      
      $dpsales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->groupBy('date')->orderBy('date', 'asc')->get([
        \DB::raw('SUM(sale_amount) as amount'),
        \DB::raw('SUM(sale_discount) as discount'),
        \DB::raw('SUM(sale_amount_paid) as amount_paid'),
        \DB::raw('SUM(adjustment) as adjustment'),
        \DB::raw('DATE(an_sales.time_created) as date')
      ]);

      $paid_expenses = ExpensePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->sum('amount');

      $purchase_payments = PurchasePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->sum('amount');

      $total_debts = 0;
      $total_payments = 0;

      foreach ($dpsales as $key => $debt) {
        $total_debts += ($debt->amount-($debt->discount+$debt->adjustment+$debt->amount_paid));
        $total_payments += $debt->amount_paid;
      }

      $paid_debts = $total_collections-$total_paid;

      $closing_balance = $total_collections-$paid_expenses-$purchase_payments;

      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.index', compact('page', 'title', 'title_sw', 'shop', 'reporttime', 'duration', 'duration_sw', 'sales', 'returns', 'servsales', 'expenses', 'total_prod_selling', 'total_buying', 'total_serv_selling', 'total_gross_profit', 'total_selling', 'gross_profit', 'net_profit', 'total_paid', 'totalexpenses', 'total_unpaid', 'cash_amount', 'total_vat', 'is_post_query', 'start_date', 'end_date', 'settings', 'devices', 'device', 'total_return_prod_selling', 'total_return_buying', 'total_return_gross_profit', 'cash_pay', 'mob_pay', 'bank_pay', 'total_pay', 'categories', 'category', 'shared_expenses', 'total_sales', 'total_payments', 'total_debts', 'total_expenses', 'paid_expenses', 'purchase_payments', 'paid_debts', 'total_collections', 'closing_balance', 'start', 'end', 'ctnexpenses', 'defcurr'));
    }

    public function profitReports(Request $request)
    {

      $page = 'Reports';
      $title = 'Profit Reports';
      $title_sw = 'Ripoti ya Faida';
      
      $shop = Shop::find(Session::get('shop_id'));
      
      $products = $shop->products()->get();
      $now = Carbon::now();
      $start = null;
      $end = null;
      $start_date = null;            
      $end_date = null;
      
      //check if user opted for date range
      $is_post_query = false;
      if (!is_null($request['start_date'])) {
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        $start = $request['start_date'].' 00:00:00';
        $end = $request['end_date'].' 23:59:59';
        $is_post_query = true;
      }else{
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $is_post_query = false;
      }
      $sales = null;
      $total_selling = 0;
      $total_buying = 0;
     
      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->groupBy('price_per_unit')->groupBy('buying_per_unit')->groupBy('products.name')->orderBy('an_sale_items.time_created', 'desc')->get([
          \DB::raw('products.id as id'),
          \DB::raw('products.name as name'),          
          \DB::raw('SUM(quantity_sold) as quantity'),
          \DB::raw('price_per_unit as price_per_unit'),
          \DB::raw('SUM(price) as price'),\DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
          \DB::raw('discount as discount'),          
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('buying_per_unit as buying_per_unit'),
          \DB::raw('SUM(buying_price) as buying_price'),
          \DB::raw('SUM(input_tax) as input_tax'),
          \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
          \DB::raw('an_sale_items.time_created as created_at')]);

          
        $total_selling = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('price')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('total_discount');
                  
        $total_buying = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('buying_price');
        $input_tax = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('input_tax');

        $output_tax = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('an_sale_items.tax_amount');


        $total_return_selling = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->sum('price')-SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->sum('total_discount');
                  
        $total_return_buying = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->sum('buying_price');
        $input_return_tax = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->sum('input_tax');

        $output_return_tax = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->sum('sale_return_items.tax_amount');

        $vat_payable = $output_tax-$input_tax;
        $total_gprofit = ($total_selling-$total_buying)-$vat_payable;

        $vat_return_payable = $output_return_tax-$input_return_tax;
        $total_rprofit = ($total_return_selling-$total_return_buying)-$vat_return_payable;

        $total_gross_profit = $total_gprofit-$total_rprofit;
                
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        return view('reports.profits', compact('page', 'title', 'title_sw', 'shop', 'reporttime', 'duration', 'duration_sw', 'sales', 'total_selling', 'total_buying', 'total_gross_profit', 'is_post_query', 'start_date', 'end_date'));
    }

    public function salesByProduct(Request $request)
    {

      $page = 'Reports';
      $title = 'Sales By Product Reports';
      $title_sw = 'Ripoti ya Mauzo kwa Bidhaa';

      $shop = Shop::find(Session::get('shop_id'));
      $settings = Setting::where('shop_id', $shop->id)->first();
      $products = $shop->products()->get();
      $now = Carbon::now();
      $start = null;
      $end = null;
      $start_date = null;            
      $end_date = null;
      
      //check if user opted for date range
      $is_post_query = false;
      if (!is_null($request['start_date'])) {
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        $start = $request['start_date'].' 00:00:00';
        $end = $request['end_date'].' 23:59:59';
        $is_post_query = true;
      }else{
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $is_post_query = false;
      }
      $sales = null;
      $total_selling = 0;
      $total_buying = 0;
      $input_tax = 0;
      $output_tax = 0;
      $vat_payable = 0;
     
      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $product = null;
      if (!is_null($request['product_id'])) {
        $product = Product::find($request['product_id']);

        $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->where('products.id', $request['product_id'])->groupBy('price_per_unit')->groupBy('buying_per_unit')->groupBy('products.name')->orderBy('an_sale_items.time_created', 'desc')->get([
          \DB::raw('products.id as id'),
          \DB::raw('products.name as name'),          
          \DB::raw('SUM(quantity_sold) as quantity'),
          \DB::raw('price_per_unit as price_per_unit'),
          \DB::raw('SUM(price) as price'),\DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
          \DB::raw('discount as discount'),          
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('buying_per_unit as buying_per_unit'),
          \DB::raw('SUM(buying_price) as buying_price'),
          \DB::raw('SUM(input_tax) as input_tax'),
          \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
          \DB::raw('an_sale_items.time_created as created_at')]);

          
        $total_selling = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->where('products.id', $request['product_id'])->sum('price')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->where('products.id', $request['product_id'])->sum('total_discount');
                  
        $total_buying = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->where('products.id', $request['product_id'])->sum('buying_price');
    
        $input_tax = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->where('products.id', $request['product_id'])->sum('input_tax');

        $output_tax = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->where('products.id', $request['product_id'])->sum('an_sale_items.tax_amount');
        $vat_payable = $output_tax-$input_tax;
        $total_gross_profit = ($total_selling-$total_buying)-$vat_payable;
      }else{

        $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->groupBy('price_per_unit')->groupBy('buying_per_unit')->groupBy('products.name')->orderBy('an_sale_items.time_created', 'desc')->get([
          \DB::raw('products.id as id'),
          \DB::raw('products.name as name'),          
          \DB::raw('SUM(quantity_sold) as quantity'),
          \DB::raw('price_per_unit as price_per_unit'),
          \DB::raw('SUM(price) as price'),\DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
          \DB::raw('discount as discount'),          
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('buying_per_unit as buying_per_unit'),
          \DB::raw('SUM(buying_price) as buying_price'),
          \DB::raw('SUM(input_tax) as input_tax'),
          \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
          \DB::raw('an_sale_items.time_created as created_at')]);

          
        $total_selling = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('price')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('total_discount');
                  
        $total_buying = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('buying_price');
    
        $input_tax = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('input_tax');
        $output_tax = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('an_sale_items.tax_amount');
        $vat_payable = $output_tax-$input_tax;
        $total_gross_profit = ($total_selling-$total_buying)-$vat_payable;
      }
                
      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.sales-by-product', compact('page', 'title', 'title_sw', 'shop', 'reporttime', 'duration', 'duration_sw', 'sales', 'products', 'total_selling', 'total_buying', 'total_gross_profit', 'is_post_query', 'start_date', 'end_date', 'product', 'settings', 'input_tax', 'output_tax', 'vat_payable'));
    }

    public function salesByService(Request $request)
    {
      $page = 'Reports';
      $title = 'Sales By Service Reports';
      $title_sw = 'Ripoti ya Mauzo kwa Huduma';

      $shop = Shop::find(Session::get('shop_id'));
      
      $services = $shop->services()->get();
      $now = Carbon::now();
      $start = null;
      $end = null;
      $start_date = null;            
      $end_date = null;
      
      //check if user opted for date range
      $is_post_query = false;
      if (!is_null($request['start_date'])) {
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        $start = $request['start_date'].' 00:00:00';
        $end = $request['end_date'].' 23:59:59';
        $is_post_query = true;
      }else{
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $is_post_query = false;
      }
      $sales = null;
      $total_selling = 0;
      $total_buying = 0;
     
      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $service = null;
      if (!is_null($request['service_id'])) {
        $service = Service::find($request['service_id']);
        
        $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->where('services.id', $service->id)->groupBy('price')->groupBy('services.name')->orderBy('service_sale_items.time_created', 'desc')->get([
          \DB::raw('services.name as name'),
          \DB::raw('SUM(no_of_repeatition) as quantity'),
          \DB::raw('price as price'),
          \DB::raw('SUM(total) as total'),
          \DB::raw('discount as discount'),
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('SUM(service_sale_items.tax_amount) as tax_amount'),
          \DB::raw('service_sale_items.time_created as created_at')
        ]);

        $total_selling = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->where('services.id', $service->id)->sum('total')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->where('services.id', $service->id)->sum('total_discount');                

        $total_gross_profit = $total_selling;
      }else{

        $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->groupBy('price')->groupBy('services.name')->orderBy('service_sale_items.time_created', 'desc')->get([
          \DB::raw('services.name as name'),
          \DB::raw('SUM(no_of_repeatition) as quantity'),
          \DB::raw('price as price'),
          \DB::raw('SUM(total) as total'),
          \DB::raw('discount as discount'),
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('SUM(service_sale_items.tax_amount) as tax_amount'),
          \DB::raw('service_sale_items.time_created as created_at')
        ]);

        $total_selling = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->sum('total')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->sum('total_discount');                


        $total_gross_profit = $total_selling;
      }

      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.sales-by-service', compact('page', 'title', 'title_sw', 'shop', 'reporttime', 'duration', 'duration_sw', 'sales', 'total_selling', 'total_gross_profit', 'service', 'services', 'is_post_query', 'start_date', 'end_date'));
    }

    public function debts(Request $request)
    {
      $page = 'Reports';
      $title = 'Debts Reports';
      $title_sw = 'Ripoti ya Madeni';

      $this->shop = Shop::find(Session::get('shop_id'));
      $settings = Setting::where('shop_id', $this->shop->id)->first();
      $customers = Customer::where('shop_id', $this->shop->id)->get();
      $users = $this->shop->users()->get();

      $now = Carbon::now();
      $this->start = null;
      $this->end = null;
      $start_date = null;            
      $end_date = null;
      
      //check if user opted for date range
      $is_post_query = false;
      if (!is_null($request['start_date'])) {
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        $this->start = $request['start_date'].' 00:00:00';
        $this->end = $request['end_date'].' 23:59:59';
        $is_post_query = true;
      }else{
        $this->start = $now->startOfDay();
        $this->end = \Carbon\Carbon::now();
        $start_date = date('Y-m-d', strtotime($this->start));
        $end_date = date('Y-m-d', strtotime($this->end));
        $is_post_query = false;
      }

      $duration = 'From '.date('d-m-Y', strtotime($this->start)).' To '.date('d-m-Y', strtotime($this->end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($this->start)).' Mpaka '.date('d-m-Y', strtotime($this->end)).'.';

      $debts = null;
      
      $debts = AnSale::where('an_sales.shop_id', $this->shop->id)->where('an_sales.is_deleted', false)->where('status', 'Unpaid')->whereBetween('an_sales.time_created', [$this->start, $this->end])->orWhere(function($query){
          $query->where('an_sales.shop_id', $this->shop->id)->where('an_sales.is_deleted', false)->where('an_sales.status', 'Partially Paid')->whereBetween('an_sales.time_created', [$this->start, $this->end]);
        })->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.phone as phone', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.adjustment as adjustment', 'an_sales.time_created as time_created', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.pay_type as pay_type', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'an_sales.comments as comments', 'an_sales.grade_id as grade_id', 'an_sales.year as year')->orderBy('an_sales.time_created', 'desc')->get();

      $total_amount = 0;
      $total_discount = 0;
      $total_adjustment = 0;
      $total_paid = 0;
      $total_debts = 0;

      foreach ($debts as $key => $value) {
        $total_amount += $value->sale_amount;
        $total_discount += $value->sale_discount;
        $total_adjustment += $value->adjustment;
        $total_paid += $value->sale_amount_paid;
        $total_debts += (($value->sale_amount-$value->sale_discount-$value->adjustment)-$value->sale_amount_paid);
      }


      $purchases = Purchase::where('shop_id', $this->shop->id)->where('is_deleted', false)->where('status', 'Pending')->orderBy('purchases.time_created', 'desc')->get();

      $tp_amount = 0;
      $tp_amount_paid = 0;

      foreach ($purchases as $key => $purchase) {
        $tp_amount += $purchase->total_amount;
        $tp_amount_paid += $purchase->amount_paid;
      }

      $totaldebts = array();
      $total_ob = 0;
      $total_invoices = 0;
      foreach ($customers as $key => $customer) {
        $obtrans = CustomerTransaction::where('customer_id', $customer->id)->where('is_ob', true)->where('shop_id', $this->shop->id)->first();
        $opening_balance = 0;
        if (!is_null($obtrans)) {
          $opening_balance = $obtrans->amount-$obtrans->ob_paid;
        }

        $totalsales = AnSale::where('shop_id', $this->shop->id)->where('is_deleted', false)->where('customer_id', $customer->id)->get([
          DB::raw('SUM(sale_amount) as sale_amount'),
          DB::raw('SUM(sale_discount) as sale_discount'),
          DB::raw('SUM(adjustment) as adjustment'),
          DB::raw('SUM(sale_amount_paid) as amount_paid')
        ]);
        $new_invoices = 0;
        foreach ($totalsales as $key => $value) {
          
          $new_invoices += $value->sale_amount-($value->sale_discount+$value->adjustment+$value->amount_paid);
        }

        $total_d = $opening_balance+$new_invoices;

        $total_ob += $opening_balance;
        $total_invoices += $new_invoices;

        array_push($totaldebts, ['customer_id' => $customer->id, 'cust_no' => $customer->cust_no, 'name' => $customer->name, 'phone' => $customer->phone, 'opening_balance' => $opening_balance, 'new_invoices' => $new_invoices, 'total' =>  $total_d]);
      }
      // return $totaldebts;
      $suppliers = $this->shop->suppliers()->get();
      $totalsupdebts = array();
      $total_sup_ob = 0;
      $total_sup_invoices = 0;
      foreach ($suppliers as $key => $supplier) {
        $supobtrans = SupplierTransaction::where('supplier_id', $supplier->id)->where('is_ob', true)->where('shop_id', $this->shop->id)->first();
        $supopening_balance = 0;
        if (!is_null($supobtrans)) {
          $supopening_balance = $supobtrans->amount-$supobtrans->ob_paid;
        }

        $totalpurchases = Purchase::where('shop_id', $this->shop->id)->where('is_deleted', false)->where('supplier_id', $supplier->id)->get([
          DB::raw('SUM(total_amount) as total_amount'),
          DB::raw('SUM(amount_paid) as amount_paid')
        ]);
        $new_sup_invoices = 0;
        foreach ($totalpurchases as $key => $value) {
          
          $new_sup_invoices += $value->total_amount-$value->amount_paid;
        }

        $total_supd = $supopening_balance+$new_sup_invoices;

        $total_sup_ob += $supopening_balance;
        $total_sup_invoices += $new_sup_invoices;

        array_push($totalsupdebts, ['supplier_id' => $supplier->id, 'supp_no' => $supplier->supp_id, 'name' => $supplier->name, 'contact_no' => $supplier->contact_no, 'opening_balance' => $supopening_balance, 'new_invoices' => $new_sup_invoices, 'total' =>  $total_supd]);
      }

      $shop = $this->shop;
      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.debts', compact('page', 'title', 'title_sw', 'shop', 'debts', 'total_amount', 'total_discount', 'total_adjustment', 'total_paid', 'total_debts', 'totaldebts', 'total_ob', 'total_invoices', 'purchases', 'totalsupdebts', 'total_sup_ob', 'total_sup_invoices', 'tp_amount', 'tp_amount_paid', 'duration', 'duration_sw', 'reporttime', 'is_post_query', 'start_date', 'end_date', 'shop', 'settings'));


    }

    public function sales(Request $request)
    {
      $page = 'Reports';
      $title = 'Sales Reports';
      $title_sw = 'Ripoti ya Mauzo';

      $shop = Shop::find(Session::get('shop_id'));
      $settings = Setting::where('shop_id', $shop->id)->first();
      $customers = Customer::where('shop_id', $shop->id)->get();
      $grades = Grade::where('shop_id', $shop->id)->get();
      $users = $shop->users()->get();

      $years = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereNotNull('year')->select('year')->groupBy('year')->orderBy('year', 'asc')->get();

      $now = Carbon::now();
      $start = null;
      $end = null;
      $start_date = null;            
      $end_date = null;
      
      //check if user opted for date range
      $is_post_query = false;
      if (!is_null($request['start_date'])) {
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        $start = $request['start_date'].' 00:00:00';
        $end = $request['end_date'].' 23:59:59';
        $is_post_query = true;
      }else{
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $is_post_query = false;
      }

      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $sales = null;
      $customer = null;
      $grade = null;
      $year = null;
      $user = null;
      if (is_null($request['customer_id'])) {
        if (!is_null($request['user_id'])) {
          $user = User::find($request['user_id']);
          $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->where('users.id', $user->id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.adjustment as adjustment', 'an_sales.time_created as time_created', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.pay_type as pay_type', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'an_sales.comments as comments', 'users.first_name as first_name', 'an_sales.grade_id as grade_id', 'an_sales.year as year')->orderBy('an_sales.time_created', 'desc')->get();
        }else{
          if (!is_null($request['grade_id'])) {
            $grade = Grade::find($request['grade_id']);
            if (!is_null($request['year'])) {
              $year = $request['year'];
              $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->where('grade_id', $request['grade_id'])->where('year', $year)->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.adjustment as adjustment', 'an_sales.time_created as time_created', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.pay_type as pay_type', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'an_sales.comments as comments', 'users.first_name as first_name', 'an_sales.grade_id as grade_id', 'an_sales.year as year')->orderBy('an_sales.time_created', 'desc')->get();
            }else{
              $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->where('grade_id', $request['grade_id'])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.adjustment as adjustment', 'an_sales.time_created as time_created', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.pay_type as pay_type', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'an_sales.comments as comments', 'users.first_name as first_name', 'an_sales.grade_id as grade_id', 'an_sales.year as year')->orderBy('an_sales.time_created', 'desc')->get();
            }
          }else{
            $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.adjustment as adjustment', 'an_sales.time_created as time_created', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.pay_type as pay_type', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'an_sales.comments as comments', 'users.first_name as first_name', 'an_sales.grade_id as grade_id', 'an_sales.year as year')->orderBy('an_sales.time_created', 'desc')->get();
          }
        }
      }else{
        $customer = Customer::find($request['customer_id']);
        if (!is_null($request['user_id'])) {
          $user = User::find($request['user_id']);
          $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->where('users.id', $user->id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->where('customers.id', $customer->id)->select('customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.adjustment as adjustment', 'an_sales.time_created as time_created', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.pay_type as pay_type', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'an_sales.comments as comments', 'users.first_name as first_name', 'an_sales.grade_id as grade_id', 'an_sales.year as year')->orderBy('an_sales.time_created', 'desc')->get();
        }else{
          $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->where('customers.id', $customer->id)->select('customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.adjustment as adjustment', 'an_sales.time_created as time_created', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.pay_type as pay_type', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'an_sales.comments as comments', 'users.first_name as first_name', 'an_sales.grade_id as grade_id', 'an_sales.year as year')->orderBy('an_sales.time_created', 'desc')->get();
        }
      }

      $total_amount = 0;
      $total_discount = 0;
      $total_adjustment = 0;
      $total_paid = 0;
      $total_debts = 0;
      $total_tax = 0;

      foreach ($sales as $key => $value) {
        $total_amount += $value->sale_amount;
        $total_discount += $value->sale_discount;
        $total_adjustment += $value->adjustment;
        $total_paid += $value->sale_amount_paid;
        $total_debts += (($value->sale_amount-$value->sale_discount-$value->adjustment)-$value->sale_amount_paid);
        $total_tax += $value->tax_amount;
      }

      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.sales', compact('page', 'title', 'title_sw', 'shop', 'sales', 'total_amount', 'total_discount', 'total_adjustment', 'total_paid', 'total_debts', 'total_tax', 'duration', 'duration_sw', 'reporttime', 'customers', 'customer', 'users', 'user', 'is_post_query', 'start_date', 'end_date', 'settings', 'grade', 'grades', 'year', 'years'));

    }

    public function salesReturns(Request $request)
    {
      $page = 'Reports';
      $title = 'Sales Return Reports';
      $title_sw = 'Ripoti ya Mauzo yaliyorudishwa';

      $shop = Shop::find(Session::get('shop_id'));
      $settings = Setting::where('shop_id', $shop->id)->first();
      $customers = Customer::where('shop_id', $shop->id)->get();
      $users = $shop->users()->get();

      $now = Carbon::now();
      $start = null;
      $end = null;
      $start_date = null;            
      $end_date = null;
      
      //check if user opted for date range
      $is_post_query = false;
      if (!is_null($request['start_date'])) {
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        $start = $request['start_date'].' 00:00:00';
        $end = $request['end_date'].' 23:59:59';
        $is_post_query = true;
      }else{
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $is_post_query = false;
      }

      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $returns = null;
      $customer = null;
      $user = null;
      if (is_null($request['customer_id'])) {
        if (!is_null($request['user_id'])) {
          $user = User::find($request['user_id']);
          $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->whereBetween('sale_returns.created_at', [$start, $end])->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->join('users', 'users.id', '=', 'an_sales.user_id')->where('users.id', $user->id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'sale_returns.sale_return_amount as sale_return_amount', 'sale_returns.sale_return_discount as sale_return_discount', 'sale_returns.return_tax_amount as return_tax_amount', 'sale_returns.reason as reason', 'sale_returns.created_at as created_at', 'sale_returns.updated_at as updated_at', 'users.first_name as first_name')->orderBy('sale_returns.created_at', 'desc')->get();
        }else{

          $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->whereBetween('sale_returns.created_at', [$start, $end])->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'sale_returns.sale_return_amount as sale_return_amount', 'sale_returns.sale_return_discount as sale_return_discount', 'sale_returns.return_tax_amount as return_tax_amount', 'sale_returns.reason as reason', 'sale_returns.created_at as created_at', 'sale_returns.updated_at as updated_at', 'users.first_name as first_name')->orderBy('sale_returns.created_at', 'desc')->get();
        }
      }else{
        $customer = Customer::find($request['customer_id']);
        if (!is_null($request['user_id'])) {
          $user = User::find($request['user_id']);

          $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->whereBetween('sale_returns.created_at', [$start, $end])->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->join('users', 'users.id', '=', 'an_sales.user_id')->where('users.id', $user->id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->where('customers.id', $customer->id)->select('customers.name as name', 'customers.id as customer_id', 'sale_returns.sale_return_amount as sale_return_amount', 'sale_returns.sale_return_discount as sale_return_discount', 'sale_returns.return_tax_amount as return_tax_amount', 'sale_returns.reason as reason', 'sale_returns.created_at as created_at', 'sale_returns.updated_at as updated_at', 'users.first_name as first_name')->orderBy('sale_returns.created_at', 'desc')->get();
        }else{
          $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->whereBetween('sale_returns.created_at', [$start, $end])->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->where('customers.id', $customer->id)->select('customers.name as name', 'customers.id as customer_id', 'sale_returns.sale_return_amount as sale_return_amount', 'sale_returns.sale_return_discount as sale_return_discount', 'sale_returns.return_tax_amount as return_tax_amount', 'sale_returns.reason as reason', 'sale_returns.created_at as created_at', 'sale_returns.updated_at as updated_at', 'users.first_name as first_name')->orderBy('sale_returns.created_at', 'desc')->get();
        }
      }

      $total_amount = 0;
      $total_discount = 0;
      $total_tax = 0;

      foreach ($returns as $key => $value) {
        $total_amount += $value->sale_return_amount;
        $total_discount += $value->sale_return_discount;
        $total_tax += $value->return_tax_amount;
      }

      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.sales-returns', compact('page', 'title', 'title_sw', 'shop', 'returns', 'total_amount', 'total_discount', 'total_tax', 'duration', 'duration_sw', 'reporttime', 'customers', 'customer', 'users', 'user', 'is_post_query', 'start_date', 'end_date', 'settings'));

    }

    public function expenses(Request $request)
    {
      $page = 'Reports';
      $title = 'Operating Expenses Reports';
      $title_sw = 'Ripoti ya Gharama za uendeshaji';
      
      $shop = Shop::find(Session::get('shop_id'));
      $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();

      $exptypes = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->groupBy('expense_type')->get();
      $users = $shop->users()->get();

      $now = Carbon::now();
      $start = null;
      $end = null;
      $start_date = null;            
      $end_date = null;
      
      //check if user opted for date range
      $is_post_query = false;
      if (!is_null($request['start_date'])) {
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        $start = $request['start_date'].' 00:00:00';
        $end = $request['end_date'].' 23:59:59';
        $is_post_query = true;
      }else{
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $is_post_query = false;
      }

      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $qty_produced = 0;
      $texpenses = null;
      $expenses = null;
      $expcat = null;
      $expense1 = null;
      $expcategories = ExpenseCategory::where('shop_id', $shop->id)->get();

      if (!is_null($request['expense_category_id'])) {
        $expcat = ExpenseCategory::find($request['expense_category_id']);
        if (!is_null($request['expense'])) {
          $expense1 = Expense::where('expense_type', $request['expense'])->first();
          $expenses =  Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('expenses.expense_type', $request['expense'])->whereBetween('expenses.time_created', [$start, $end])->where('expense_category_id', $request['expense_category_id'])->join('users', 'users.id', '=', 'expenses.user_id')->select('users.first_name as first_name', 'expenses.id as id', 'expense_category_id', 'expenses.supplier_id as supplier_id', 'expenses.expense_type as expense_type', 'expenses.amount as amount', 'expenses.description as description', 'expenses.exp_vat as exp_vat', 'expenses.wht_rate as wht_rate', 'expenses.wht_amount as wht_amount', 'expenses.exp_type as exp_type', 'expenses.status as status', 'expenses.time_created as created_at')->orderBy('time_created', 'desc')->get();
        }else{
          $expenses =  Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('expenses.time_created', [$start, $end])->where('expense_category_id', $request['expense_category_id'])->join('users', 'users.id', '=', 'expenses.user_id')->select('users.first_name as first_name', 'expenses.id as id', 'expense_category_id', 'expenses.supplier_id as supplier_id', 'expenses.expense_type as expense_type', 'expenses.amount as amount', 'expenses.description as description', 'expenses.exp_vat as exp_vat', 'expenses.wht_rate as wht_rate', 'expenses.wht_amount as wht_amount', 'expenses.exp_type as exp_type', 'expenses.status as status', 'expenses.time_created as created_at')->orderBy('time_created', 'desc')->get();
        }

        $texpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('expenses.time_created', [$start, $end])->where('expense_category_id', $request['expense_category_id'])->groupBy('expense_type')->get([
          \DB::raw('expense_type as expense_type'),
          \DB::raw('SUM(amount) as amount'),
          \DB::raw('SUM(exp_vat) as exp_vat'),
          \DB::raw('wht_rate as wht_rate'),
          \DB::raw('SUM(wht_amount) as wht_amount')
        ]);

        if ($expcat->is_included_in_prod_cost) {   
          //Get quantity of product produced
          $qty_produced = 1230;
        }
      }else{
        if (!is_null($request['expense'])) {
          $expense1 = Expense::where('expense_type', $request['expense'])->first();
          $expenses =  Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('expenses.expense_type', $request['expense'])->whereBetween('expenses.time_created', [$start, $end])->join('users', 'users.id', '=', 'expenses.user_id')->select('users.first_name as first_name', 'expenses.id as id', 'expense_category_id', 'expenses.supplier_id as supplier_id', 'expenses.expense_type as expense_type', 'expenses.amount as amount', 'expenses.description as description', 'expenses.exp_vat as exp_vat', 'expenses.wht_rate as wht_rate', 'expenses.wht_amount as wht_amount', 'expenses.exp_type as exp_type', 'expenses.status as status', 'expenses.time_created as created_at')->orderBy('time_created', 'desc')->get();
        }else{
          $expenses =  Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('expenses.time_created', [$start, $end])->join('users', 'users.id', '=', 'expenses.user_id')->select('users.first_name as first_name', 'expenses.id as id', 'expense_category_id', 'expenses.supplier_id as supplier_id', 'expenses.expense_type as expense_type', 'expenses.amount as amount', 'expenses.description as description', 'expenses.exp_vat as exp_vat', 'expenses.wht_rate as wht_rate', 'expenses.wht_amount as wht_amount', 'expenses.exp_type as exp_type', 'expenses.status as status', 'expenses.time_created as created_at')->orderBy('time_created', 'desc')->get();
        }

        $texpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('expenses.time_created', [$start, $end])->groupBy('expense_type')->get([
          \DB::raw('expense_type as expense_type'),
          \DB::raw('SUM(amount) as amount'),
          \DB::raw('SUM(exp_vat) as exp_vat'),
          \DB::raw('wht_rate as wht_rate'),
          \DB::raw('SUM(wht_amount) as wht_amount')
        ]);
      }

      $total = 0; 
      $total_vat = 0;
      $total_wht = 0;


      foreach ($expenses as $key => $value) {
        $total += $value->amount;
        $total_vat += $value->exp_vat;
        $total_wht += $value->wht_amount;
      }

      $settings = Setting::where('shop_id', $shop->id)->first();
      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.expenses', compact('page', 'title', 'title_sw', 'shop', 'settings', 'exptypes', 'expenses', 'expense1', 'expcategories', 'expcat', 'total', 'total_vat', 'total_wht', 'duration', 'duration_sw', 'reporttime', 'is_post_query', 'start_date', 'end_date', 'texpenses', 'defcurr', 'qty_produced'));
    }

    public function singleExpenseReport(Request $request, $type)
    {
      $page = 'Reports';
      $title = 'Operating Expenses Reports';
      $title_sw = 'Ripoti ya Gharama za uendeshaji';
      
      $shop = Shop::find(Session::get('shop_id'));
      $settings = Setting::where('shop_id', $shop->id)->first();

      $now = Carbon::now();
      $start = null;
      $end = null;
      $start_date = null;            
      $end_date = null;
      
      //check if user opted for date range
      $is_post_query = false;
      if (!is_null($request['start_date'])) {
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        $start = $request['start_date'].' 00:00:00';
        $end = $request['end_date'].' 23:59:59';
        $is_post_query = true;
      }else{
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $is_post_query = false;
      }

      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

        
      $texpenses = Expense::where('expense_type', $type)->where('shop_id', $shop->id)->whereBetween('expenses.time_created', [$start, $end])->groupBy('time_created')->get([
        \DB::raw('DATE(time_created) as date'),
        \DB::raw('SUM(amount) as amount'),
        \DB::raw('description'),
        \DB::raw('SUM(exp_vat) as exp_vat'),
        \DB::raw('wht_rate as wht_rate'),
        \DB::raw('SUM(wht_amount) as wht_amount')]);

      $total = 0; 
      $total_vat = 0;
      $total_wht = 0;


      foreach ($texpenses as $key => $value) {
        $total += $value->amount;
        $total_vat += $value->exp_vat;
        $total_wht += $value->wht_amount;
      }
      

      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.single-expense', compact('page', 'title', 'title_sw', 'shop', 'settings', 'total', 'total_vat', 'total_wht', 'duration', 'duration_sw', 'reporttime', 'is_post_query', 'start_date', 'end_date', 'texpenses', 'type'));
    }

    public function totalAmounts(Request $request)
    {
      
      $shop = Shop::find(Session::get('shop_id'));
      $user = Auth::user();
      $settings = Setting::where('shop_id', $shop->id)->first();
      $now = Carbon::now();
      $start = null;
      $end = null;
      $start_date = null;            
      $end_date = null;
      
      //check if user opted for date range
      $is_post_query = false;
      if (!is_null($request['sale_date'])) {
        $start_date = $request['sale_date'];
        $end_date = $request['sale_date'];
        $start = $request['sale_date'].' 00:00:00';
        $end = $request['sale_date'].' 23:59:59';
        $is_post_query = true;
      }else if (!is_null($request['start_date'])) {
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        $start = $request['start_date'].' 00:00:00';
        $end = $request['end_date'].' 23:59:59';
        $is_post_query = true;
      }else{
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $is_post_query = false;
      }

      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $totals = array();
      if ($shop->business_type_id == 1 || $shop->business_type_id == 2) {
    
        $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->groupBy('date')->orderBy('date', 'asc')->get([
          \DB::raw('SUM(price) as price'),\DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
          \DB::raw('SUM(total_discount) as discount'),
          \DB::raw('SUM(buying_price) as buying_price'),
          \DB::raw('SUM(an_sale_items.input_tax) as input_vat'),
          \DB::raw('SUM(an_sale_items.tax_amount) as output_vat'),
          \DB::raw('DATE(an_sales.time_created) as date')
          ]);
          
          foreach ($sales as $key => $sale) {
            $expenseamount = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$sale->date.' 00:00:00', $sale->date.' 23:59:59'])->sum('amount')-Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$sale->date.' 00:00:00', $sale->date.' 23:59:59'])->sum('exp_vat');
              array_push($totals, array_merge($sale->toArray(), ['amount' => $expenseamount]));
          }
      }elseif ($shop->business_type_id == 3) {
        
        $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->groupBy('date')->orderBy('date', 'asc')->get([
          \DB::raw('SUM(total) as price'),
          \DB::raw('SUM(total_discount) as discount'),
          \DB::raw('SUM(service_sale_items.tax_amount) as output_vat'),
          \DB::raw('DATE(an_sales.time_created) as date')
        ]);

        foreach ($sales as $key => $sale) {
            $expenseamount = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$sale->date.' 00:00:00', $sale->date.' 23:59:59'])->sum('amount')-Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$sale->date.' 00:00:00', $sale->date.' 23:59:59'])->sum('exp_vat');
              array_push($totals, array_merge($sale->toArray(), ['buying_price' => 0, 'input_vat' => 0, 'amount' => $expenseamount]));
          }
      }else{

        $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->groupBy('date')->orderBy('date', 'asc')->get([
          \DB::raw('SUM(price) as price'),\DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
          \DB::raw('SUM(total_discount) as discount'),
          \DB::raw('SUM(buying_price) as buying_price'),
          \DB::raw('SUM(an_sale_items.input_tax) as input_vat'),
          \DB::raw('SUM(an_sale_items.tax_amount) as output_vat'),
          \DB::raw('DATE(an_sales.time_created) as date')
        ]);

        $ptotals = array();
        foreach ($sales as $key => $sale) {

          $ctnexpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('expire_at', '>', $sale->date.' 00:00:00')->where('time_created', '<', $sale->date.' 00:00:00')->orderBy('expense_type', 'asc')->get([
              \DB::raw('expense_type as expense_type'),
              \DB::raw('amount as amount'),
              \DB::raw('no_days as no_days'),
              \DB::raw('exp_vat as exp_vat'),
              \DB::raw('time_created as time_created'),
              \DB::raw('expire_at as expire_at')
          ]);

          $expenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$sale->date.' 00:00:00', $sale->date.' 23:59:59'])->orderBy('expense_type', 'asc')->get([
              \DB::raw('expense_type as expense_type'),
              \DB::raw('amount as amount'),
              \DB::raw('no_days as no_days'),
              \DB::raw('exp_vat as exp_vat'),
              \DB::raw('time_created as time_created'),
              \DB::raw('expire_at as expire_at')
          ]);

          $expenseamount = 0;
          //All Continue Expenses
          if (!is_null($ctnexpenses)) {
              
            foreach ($ctnexpenses as $key => $expense) {
              $amount = (($expense->amount-$expense->exp_vat)/$expense->no_days);
              $expenseamount += $amount;
            }
          }

          //All Normal Expenses
          if (!is_null($expenses)) {
              
            foreach($expenses as $key => $expense) {
              $amount = 0;
              if ($expense->no_days == 1) {
                $amount = ($expense->amount-$expense->exp_vat);
                $expenseamount += $amount; 
              }else{
                $amount = (($expense->amount-$expense->exp_vat)/$expense->no_days);
                $expenseamount += $amount;
              }
            }
          } 

          array_push($ptotals, array_merge($sale->toArray(), ['amount' => $expenseamount]));
        }


        $servsales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->groupBy('date')->orderBy('date', 'asc')->get([
          \DB::raw('SUM(total) as price'),
          \DB::raw('SUM(total_discount) as discount'),
          \DB::raw('SUM(service_sale_items.tax_amount) as output_vat'),
          \DB::raw('DATE(an_sales.time_created) as date')
        ]);
        

        $stotals = array();
        foreach ($servsales as $key => $sale) {
              array_push($stotals, array_merge($sale->toArray(), ['buying_price' => 0, 'input_vat' => 0, 'amount' => 0]));
        }

        $arrays = array_merge($ptotals, $stotals);

        // return $arrays;
        $sum = array();
        foreach ($arrays as $array) {
          if (isset($sum[$array['date']])) {
            $sum[$array['date']]['price'] += $array['price'];
            $sum[$array['date']]['discount'] += $array['discount'];
            $sum[$array['date']]['buying_price'] += $array['buying_price'];
            $sum[$array['date']]['input_vat'] += $array['input_vat'];
            $sum[$array['date']]['output_vat'] += $array['output_vat'];
            $sum[$array['date']]['amount'] += $array['amount'];
          } else {
            $sum[$array['date']]['price'] = $array['price'];
            $sum[$array['date']]['discount'] = $array['discount'];
            $sum[$array['date']]['buying_price'] = $array['buying_price'];
            $sum[$array['date']]['input_vat'] = $array['input_vat'];
            $sum[$array['date']]['output_vat'] = $array['output_vat'];
            $sum[$array['date']]['amount'] = $array['amount'];
            $sum[$array['date']]['date'] = $array['date'];
          }
        } 
        foreach ($sum as $key => $value) {
          array_push($totals, $value);
        }
      }


        $tsales = 0;
        $tcsales = 0;
        $texpenses = 0;

        $labels = array();
        $grosses = array();
        $expensesdata = array();
        $netprofits = array();

        foreach ($totals as $total) {
          array_push($labels, $total['date']);
          array_push($grosses, (($total['price']-$total['discount'])-$total['output_vat'])-($total['buying_price']-$total['input_vat']));
          array_push($expensesdata, $total['amount']);
          array_push($netprofits, ((($total['price']-$total['discount'])-$total['output_vat'])-($total['buying_price']-$total['input_vat'])-$total['amount']));

          $tsales += (($total['price']-$total['discount'])-$total['output_vat']);
          $tcsales += ($total['buying_price']-$total['input_vat']);
          $texpenses += $total['amount'];
        }

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        $page = "Reports";
        $title = 'Daily profit or loss report';
        $title_sw = 'Ripoti ya Faida au Hasara ya Kila siku';
        return view('reports.daily-profit', compact('page', 'title', 'title_sw', 'totals', 'reporttime', 'duration', 'duration_sw', 'is_post_query', 'start_date', 'end_date', 'shop', 'user', 'settings', 'tsales', 'tcsales', 'texpenses', 'labels', 'grosses', 'expensesdata', 'netprofits'));
    }

    public function consolidated(Request $request)
    {
      $shop = Shop::find(Session::get('shop_id'));
      $user = Auth::user();
      $settings = Setting::where('shop_id', $shop->id)->first();
      $now = Carbon::now();
      $start = null;
      $end = null;
      $start_date = null;            
      $end_date = null;
      
      //check if user opted for date range
      $is_post_query = false;
      if (!is_null($request['sale_date'])) {
        $start_date = $request['sale_date'];
        $end_date = $request['sale_date'];
        $start = $request['sale_date'].' 00:00:00';
        $end = $request['sale_date'].' 23:59:59';
        $is_post_query = true;
      }else if (!is_null($request['start_date'])) {
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        $start = $request['start_date'].' 00:00:00';
        $end = $request['end_date'].' 23:59:59';
        $is_post_query = true;
      }else{
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $is_post_query = false;
      }

      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $shops = $user->shops()->get();

      $data = array();
      foreach ($shops as $key => $mshop) {
        if ($mshop->business_type_id == 1 || $mshop->business_type_id == 2) {
            
          $mshopsales = AnSale::where('an_sales.shop_id', $mshop->id)->where('an_sales.is_deleted' , false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->get([
            \DB::raw('SUM(price) as price'),\DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
            \DB::raw('SUM(total_discount) as discount'),
            \DB::raw('SUM(buying_price) as buying_price'),
            \DB::raw('SUM(an_sale_items.input_tax) as input_vat'),
            \DB::raw('SUM(an_sale_items.tax_amount) as output_vat')
          ]);

          foreach ($mshopsales as $msale) {
             $ctnexpenses = Expense::where('shop_id', $mshop->id)->where('is_deleted', false)->where('expire_at', '>', $start)->where('time_created', '<', $start)->orderBy('expense_type', 'asc')->get([
              \DB::raw('expense_type as expense_type'),
              \DB::raw('amount as amount'),
              \DB::raw('no_days as no_days'),
              \DB::raw('exp_vat as exp_vat'),
              \DB::raw('time_created as time_created'),
              \DB::raw('expire_at as expire_at')
            ]);

            $expenses = Expense::where('shop_id', $mshop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->orderBy('expense_type', 'asc')->get([
                \DB::raw('expense_type as expense_type'),
                \DB::raw('amount as amount'),
                \DB::raw('no_days as no_days'),
                \DB::raw('exp_vat as exp_vat'),
                \DB::raw('time_created as time_created'),
                \DB::raw('expire_at as expire_at')
            ]);

            $expenseamount = 0;
            //All Continue Expenses
            if (!is_null($ctnexpenses)) {
                
              foreach ($ctnexpenses as $key => $expense) {
                $expdays = 0;
                if ($expense->expire_at > $end && $expense->time_created < $start) {
                  $expdays = \Carbon\Carbon::parse($end)->diffInDays(\Carbon\Carbon::parse($start))+1;
                }else{
                  $expdays = \Carbon\Carbon::parse($expense->expire_at)->diffInDays(\Carbon\Carbon::parse($start))+1;
                }
                $amount = (($expense->amount-$expense->exp_vat)/$expense->no_days)*$expdays;
                $expenseamount += $amount;
              }
            }

            //All Normal Expenses
            if (!is_null($expenses)) {
                
              foreach($expenses as $key => $expense) {
                $amount = 0;
                if ($expense->no_days == 1) {
                  $amount = ($expense->amount-$expense->exp_vat);
                  $expenseamount += $amount; 
                }else{
                  $expdays = 0;
                  if($expense->expire_at > $end){
                    $expdays= \Carbon\Carbon::parse($end)->diffInDays(\Carbon\Carbon::parse($expense->time_created))+1;
                  }else{
                    $expdays = $expense->no_days;
                  }
                  $amount = (($expense->amount-$expense->exp_vat)/$expense->no_days)*$expdays;
                  $expenseamount += $amount;
                }
              }
            }

            array_push($data, array_merge($msale->toArray(), ['amount' => $expenseamount], ['bizname' => $mshop->name]));
          }
        }elseif ($mshop->business_type_id == 4) {
          $ctnexpenses = Expense::where('shop_id', $mshop->id)->where('is_deleted', false)->where('expire_at', '>', $start)->where('time_created', '<', $start)->orderBy('expense_type', 'asc')->get([
              \DB::raw('expense_type as expense_type'),
              \DB::raw('amount as amount'),
              \DB::raw('no_days as no_days'),
              \DB::raw('exp_vat as exp_vat'),
              \DB::raw('time_created as time_created'),
              \DB::raw('expire_at as expire_at')
          ]);

          $expenses = Expense::where('shop_id', $mshop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->orderBy('expense_type', 'asc')->get([
              \DB::raw('expense_type as expense_type'),
              \DB::raw('amount as amount'),
              \DB::raw('no_days as no_days'),
              \DB::raw('exp_vat as exp_vat'),
              \DB::raw('time_created as time_created'),
              \DB::raw('expire_at as expire_at')
          ]);

          $expenseamount = 0;
          //All Continue Expenses
          if (!is_null($ctnexpenses)) {
                
            foreach ($ctnexpenses as $key => $expense) {
              $expdays = 0;
              if ($expense->expire_at > $end && $expense->time_created < $start) {
                $expdays = \Carbon\Carbon::parse($end)->diffInDays(\Carbon\Carbon::parse($start))+1;
              }else{
                $expdays = \Carbon\Carbon::parse($expense->expire_at)->diffInDays(\Carbon\Carbon::parse($start))+1;
              }
              $amount = (($expense->amount-$expense->exp_vat)/$expense->no_days)*$expdays;
              $expenseamount += $amount;
            }
          }

          //All Normal Expenses
          if (!is_null($expenses)) {
                
            foreach($expenses as $key => $expense) {
              $amount = 0;
              if ($expense->no_days == 1) {
                $amount = ($expense->amount-$expense->exp_vat);
                $expenseamount += $amount; 
              }else{
                $expdays = 0;
                if($expense->expire_at > $end){
                  $expdays= \Carbon\Carbon::parse($end)->diffInDays(\Carbon\Carbon::parse($expense->time_created))+1;
                }else{
                  $expdays = $expense->no_days;
                }
                $amount = (($expense->amount-$expense->exp_vat)/$expense->no_days)*$expdays;
                $expenseamount += $amount;
              }
            }
          }

          $mshopprodsales = AnSale::where('an_sales.shop_id', $mshop->id)->where('an_sales.is_deleted' , false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->get([
            \DB::raw('SUM(price) as price'),\DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
            \DB::raw('SUM(total_discount) as discount'),
            \DB::raw('SUM(buying_price) as buying_price'),
            \DB::raw('SUM(an_sale_items.input_tax) as input_vat'),
            \DB::raw('SUM(an_sale_items.tax_amount) as output_vat')
          ]);

          if (!is_null($mshopprodsales)) {
            foreach ($mshopprodsales as $msale) {  
              array_push($data, array_merge($msale->toArray(), ['amount' => $expenseamount], ['bizname' => $mshop->name]));
            }
          }

          $mshopsservsales = AnSale::where('an_sales.shop_id', $mshop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->get([
            \DB::raw('SUM(total) as price'),
            \DB::raw('SUM(total_discount) as discount'),
            \DB::raw('SUM(service_sale_items.tax_amount) as output_vat')
          ]);

          foreach ($mshopsservsales as $msale) {

            if (!is_null($mshopprodsales)) {
              array_push($data, array_merge($msale->toArray(), ['buying_price' => 0, 'input_vat' => 0], ['amount' => 0], ['bizname' => $mshop->name]));
            }else{
               array_push($data, array_merge($msale->toArray(), ['buying_price' => 0, 'input_vat' => 0], ['amount' => $expenseamount], ['bizname' => $mshop->name]));
            }
          }
        }else{
          $mshopsales = AnSale::where('an_sales.shop_id', $mshop->id)->where('an_sales.is_deleted' , false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sales.time_created', [$start, $end])->get([
            \DB::raw('SUM(total) as price'),
            \DB::raw('SUM(total_discount) as discount'),
            \DB::raw('SUM(service_sale_items.tax_amount) as output_vat')
          ]);
          foreach ($mshopsales as $msale) {
             $ctnexpenses = Expense::where('shop_id', $mshop->id)->where('is_deleted', false)->where('expire_at', '>', $start)->where('time_created', '<', $start)->orderBy('expense_type', 'asc')->get([
              \DB::raw('expense_type as expense_type'),
              \DB::raw('amount as amount'),
              \DB::raw('no_days as no_days'),
              \DB::raw('exp_vat as exp_vat'),
              \DB::raw('time_created as time_created'),
              \DB::raw('expire_at as expire_at')
            ]);

            $expenses = Expense::where('shop_id', $mshop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->orderBy('expense_type', 'asc')->get([
                \DB::raw('expense_type as expense_type'),
                \DB::raw('amount as amount'),
                \DB::raw('no_days as no_days'),
                \DB::raw('exp_vat as exp_vat'),
                \DB::raw('time_created as time_created'),
                \DB::raw('expire_at as expire_at')
            ]);

            $expenseamount = 0;
            //All Continue Expenses
            if (!is_null($ctnexpenses)) {
                
              foreach ($ctnexpenses as $key => $expense) {
                $expdays = 0;
                if ($expense->expire_at > $end && $expense->time_created < $start) {
                  $expdays = \Carbon\Carbon::parse($end)->diffInDays(\Carbon\Carbon::parse($start))+1;
                }else{
                  $expdays = \Carbon\Carbon::parse($expense->expire_at)->diffInDays(\Carbon\Carbon::parse($start))+1;
                }
                $amount = (($expense->amount-$expense->exp_vat)/$expense->no_days)*$expdays;
                $expenseamount += $amount;
              }
            }

            //All Normal Expenses
            if (!is_null($expenses)) {
                
              foreach($expenses as $key => $expense) {
                $amount = 0;
                if ($expense->no_days == 1) {
                  $amount = ($expense->amount-$expense->exp_vat);
                  $expenseamount += $amount; 
                }else{
                  $expdays = 0;
                  if($expense->expire_at > $end){
                    $expdays= \Carbon\Carbon::parse($end)->diffInDays(\Carbon\Carbon::parse($expense->time_created))+1;
                  }else{
                    $expdays = $expense->no_days;
                  }
                  $amount = (($expense->amount-$expense->exp_vat)/$expense->no_days)*$expdays;
                  $expenseamount += $amount;
                }
              }
            }  
            array_push($data, array_merge($msale->toArray(), ['buying_price' => 0, 'input_vat' => 0], ['amount' => $expenseamount], ['bizname' => $mshop->name]));
          }
        }
      }

        $totals = array();
        $tprice = array_reduce($data, function ($a, $b) {
            isset($a[$b['bizname']]) ? $a[$b['bizname']]['price'] += $b['price'] : $a[$b['bizname']] = $b;
           return $a;
        });
        $tbuying = array_reduce($tprice, function ($a, $b) {
            isset($a[$b['bizname']]) ? $a[$b['bizname']]['buying_price'] += $b['buying_price'] : $a[$b['bizname']] = $b;  
              return $a;
        });
        $tdisc = array_reduce($tbuying, function ($a, $b) {
             isset($a[$b['bizname']]) ? $a[$b['bizname']]['discount'] += $b['discount'] : $a[$b['bizname']] = $b;
            return $a;
        });
        $tinput = array_reduce($tdisc, function ($a, $b) {
            isset($a[$b['bizname']]) ? $a[$b['bizname']]['input_vat'] += $b['input_vat'] : $a[$b['bizname']] = $b;
             return $a;
        });
        $toutput = array_reduce($tinput, function ($a, $b) {
            isset($a[$b['bizname']]) ? $a[$b['bizname']]['output_vat'] += $b['output_vat'] : $a[$b['bizname']] = $b;
            return $a;
        });
        $totals = array_reduce($toutput, function ($a, $b) {
            isset($a[$b['bizname']]) ? $a[$b['bizname']]['amount'] += $b['amount'] : $a[$b['bizname']] = $b;
            return $a;
        });

        // return $totals;
        $tsales = 0;
        $tcsales = 0;
        $texpenses = 0;

        foreach ($totals as $total) {
          $tsales += (($total['price']-$total['discount'])-$total['output_vat']);
          $tcsales += ($total['buying_price']-$total['input_vat']);
          $texpenses += $total['amount'];
        }

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        $page = "Reports";
        $title = 'Consolidated profit or loss report';
        $title_sw = 'Ripoti ya Faida au Hasara iliyojuishwa';

        return view('reports.consolidated', compact('page', 'title', 'title_sw', 'totals', 'reporttime', 'duration', 'duration_sw', 'is_post_query', 'start_date', 'end_date', 'shop', 'settings', 'tsales', 'tcsales', 'texpenses'));
    }

    public function collectionsReport(Request $request)
    {
      $page = 'Reports';
      $title = 'Collections Reports';
      $title_sw = 'Ripoti za Makusanyo';
      $shop = Shop::find(Session::get('shop_id'));
      $customers = Customer::where('shop_id', $shop->id)->get(); 
      $customer = Customer::where('id', $request['customer_id'])->where('shop_id', $shop->id)->first(); 
      $now = Carbon::now();
      $start = null;
      $end = null;
      $start_date = null;            
      $end_date = null;
      
      //check if user opted for date range
      $is_post_query = false;
      if (!is_null($request['start_date'])) {
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        $start = $request['start_date'].' 00:00:00';
        $end = $request['end_date'].' 23:59:59';
        $is_post_query = true;
      }else{
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $is_post_query = false;
      }
     
      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      if (!is_null($customer)) {
        $collections = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->where('customers.id', $customer->id)->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->whereBetween('sale_payments.pay_date', [$start, $end])->select('customers.cust_no as cust_no', 'customers.name as name', 'sale_payments.pay_mode as pay_mode', 'sale_payments.bank_name as bank_name', 'sale_payments.cheque_no as cheque_no', 'sale_payments.pay_date as pay_date', 'sale_payments.receipt_no as receipt_no', 'sale_payments.amount as amount', 'an_sales.sale_type as sale_type')->get();

        $debt_collections = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->where('sale_type', 'Credit')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->where('customers.id', $customer->id)->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->whereBetween('sale_payments.pay_date', [$start, $end])->select('customers.cust_no as cust_no', 'customers.name as name', 'sale_payments.pay_mode as pay_mode', 'sale_payments.bank_name as bank_name', 'sale_payments.cheque_no as cheque_no', 'sale_payments.pay_date as pay_date', 'sale_payments.receipt_no as receipt_no', 'sale_payments.amount as amount', 'an_sales.sale_type as sale_type')->get();

      }else{
        $collections = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->whereBetween('sale_payments.pay_date', [$start, $end])->select('customers.cust_no as cust_no', 'customers.name as name', 'sale_payments.pay_mode as pay_mode', 'sale_payments.bank_name as bank_name', 'sale_payments.cheque_no as cheque_no', 'sale_payments.pay_date as pay_date', 'sale_payments.receipt_no as receipt_no', 'sale_payments.amount as amount', 'an_sales.sale_type as sale_type')->get();

        $debt_collections = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->where('sale_type', 'Credit')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->join('sale_payments', 'sale_payments.an_sale_id', '=', 'an_sales.id')->whereBetween('sale_payments.pay_date', [$start, $end])->select('customers.cust_no as cust_no', 'customers.name as name', 'sale_payments.pay_mode as pay_mode', 'sale_payments.bank_name as bank_name', 'sale_payments.cheque_no as cheque_no', 'sale_payments.pay_date as pay_date', 'sale_payments.receipt_no as receipt_no', 'sale_payments.amount as amount', 'an_sales.sale_type as sale_type')->get();
      }

      // return $collections;

      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.financial.collections-report', compact('page', 'title', 'title_sw', 'shop', 'duration', 'duration_sw', 'start_date', 'end_date', 'is_post_query', 'collections', 'debt_collections', 'reporttime', 'customers', 'customer'));

    }

    public function summaryReport(Request $request)
    {
      $page = 'Reports';
      $title = 'summary Report';
      $title_sw = 'Ripoti ya Muhtasari';

      $shop = Shop::find(Session::get('shop_id'));
      $settings = Setting::where('shop_id', $shop->id)->first();

      $customers = Customer::where('shop_id', $shop->id)->get();
      $users = $shop->users()->get();

      $now = Carbon::now();
      $start = null;
      $end = null;
      $start_date = null;            
      $end_date = null;
      $devices = null;
      $servsales = null;
      $expenses = null;
      $ctnexpenses = null;
      $all_expenses = null;
      $all_ctnexpenses = null;
      $total_serv_selling = null;
      $tsales = null;
      $total_paid = null;
      $total_vat = null;
      $total_expenses = null;
      if ($shop->business_type_id == 3) {
        $devices = Device::where('shop_id', $shop->id)->get();
      }

      $device = null;
      if (!is_null($request['device_id'])) {
        $device = Device::find($request['device_id']);
      }
      //check if user opted for date range
      $is_post_query = false;
      if (!is_null($request['start_date'])) {
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        $start = $request['start_date'].' 00:00:00';
        $end = $request['end_date'].' 23:59:59';
        $is_post_query = true;
      }else{
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $is_post_query = false;
      }

      $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
      $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';

      $categories = Category::where('shop_id', $shop->id)->get();
      $category = Category::find($request['category_id']);

      $sales = null; $returns = null; $total_prod_selling = null; $total_buying = null; $total_return_prod_selling = null; $total_return_buying = null; $shared_expenses = 0;
      if (!is_null($category)) {
        
        $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->groupBy('price_per_unit')->groupBy('discount')->groupBy('buying_per_unit')->groupBy('products.name')->orderBy('an_sale_items.time_created', 'desc')->get([
          \DB::raw('products.name as name'),
          \DB::raw('SUM(quantity_sold) as quantity'),          
          \DB::raw('price_per_unit as price_per_unit'),
          \DB::raw('an_sale_items.tax_amount as tax'),
          \DB::raw('SUM(price) as price'),
          \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
          \DB::raw('discount as discount'),
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('buying_per_unit as buying_per_unit'),
          \DB::raw('SUM(buying_price) as buying_price'),          
          \DB::raw('an_sale_items.time_created as created_at')
        ]);

        $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'sale_return_items.product_id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->groupBy('price_per_unit')->groupBy('buying_per_unit')->groupBy('products.name')->orderBy('sale_return_items.created_at', 'desc')->get([
          \DB::raw('products.name as name'),
          \DB::raw('SUM(quantity) as quantity'),      
          \DB::raw('price_per_unit as price_per_unit'),
          \DB::raw('sale_return_items.tax_amount as tax'),
          \DB::raw('SUM(price) as price'),
          \DB::raw('SUM(sale_return_items.tax_amount) as tax_amount'),
          \DB::raw('discount as discount'),
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('buying_per_unit as buying_per_unit'),
          \DB::raw('SUM(buying_price) as buying_price'), 
          \DB::raw('sale_return_items.created_at as created_at')
        ]);


        $total_prod_selling = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->sum('price')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->sum('total_discount')+AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->sum('an_sale_items.tax_amount');

        
        $total_buying = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->sum('buying_price');

         $total_return_prod_selling = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'sale_return_items.product_id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->sum('price')-SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'sale_return_items.product_id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->sum('total_discount')+SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'sale_return_items.product_id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->sum('sale_return_items.tax_amount');

        $total_return_buying = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'sale_return_items.product_id')->join('category_product', 'category_product.product_id', '=', 'products.id')->where('category_id', $category->id)->sum('buying_price');
      
      }else{
        $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->join('products', 'an_sale_items.product_id', '=', 'products.id')->groupBy('price_per_unit')->groupBy('discount')->groupBy('buying_per_unit')->groupBy('products.name')->orderBy('an_sale_items.time_created', 'desc')->get([
          \DB::raw('products.name as name'),
          \DB::raw('SUM(quantity_sold) as quantity'),          
          \DB::raw('price_per_unit as price_per_unit'),
          \DB::raw('an_sale_items.tax_amount as tax'),
          \DB::raw('SUM(price) as price'),
          \DB::raw('SUM(an_sale_items.tax_amount) as tax_amount'),
          \DB::raw('discount as discount'),
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('buying_per_unit as buying_per_unit'),
          \DB::raw('SUM(buying_price) as buying_price'),          
          \DB::raw('an_sale_items.time_created as created_at')
        ]);

        $returns = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'sale_return_items.product_id')->groupBy('price_per_unit')->groupBy('buying_per_unit')->groupBy('products.name')->orderBy('sale_return_items.created_at', 'desc')->get([
          \DB::raw('products.name as name'),
          \DB::raw('SUM(quantity) as quantity'),      
          \DB::raw('price_per_unit as price_per_unit'),
          \DB::raw('sale_return_items.tax_amount as tax'),
          \DB::raw('SUM(price) as price'),
          \DB::raw('SUM(sale_return_items.tax_amount) as tax_amount'),
          \DB::raw('discount as discount'),
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('buying_per_unit as buying_per_unit'),
          \DB::raw('SUM(buying_price) as buying_price'), 
          \DB::raw('sale_return_items.created_at as created_at')
        ]);

        $tsamout = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('price');
        $tdsc = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('total_discount');
        $tvt =AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('an_sale_items.tax_amount');

        $total_prod_selling = ($tsamout+$tvt)-$tdsc;;
        
        $total_buying = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('an_sale_items', 'an_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('an_sale_items.time_created', [$start, $end])->sum('buying_price');

         $total_return_prod_selling = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->sum('price')-SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->sum('total_discount')+SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->sum('sale_return_items.tax_amount');

        $total_return_buying = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start, $end])->sum('buying_price');
      }

 
      $total_return_gross_profit = $total_return_prod_selling-$total_return_buying;

      if (!is_null($device)) {
        $servsales = DeviceSale::where('device_id', $device->id)->join('an_sales', 'an_sales.id', '=', 'device_sales.an_sale_id')->where('an_sales.shop_id', $shop->id)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->groupBy('price')->groupBy('services.name')->orderBy('service_sale_items.time_created', 'desc')->get([
          \DB::raw('services.name as name'),
          \DB::raw('SUM(no_of_repeatition) as repeatition'),
          \DB::raw('price as price'),
          \DB::raw('SUM(total) as total'),
          \DB::raw('discount as discount'),
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('service_sale_items.time_created as created_at')
        ]);

        $total_serv_selling = DeviceSale::where('device_id', $device->id)->join('an_sales', 'an_sales.id', '=', 'device_sales.an_sale_id')->where('an_sales.shop_id', $shop->id)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->sum('total')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->sum('total_discount');


        $ctnexpenses = DeviceExpense::where('device_id', $device->id)->join('expenses', 'expenses.id', '=', 'device_expenses.an_expense_id')->where('shop_id', $shop->id)->where('expire_at', '>', $start)->where('time_created', '<', $start)->orderBy('expense_type', 'asc')->get([
            \DB::raw('expense_type as expense_type'),
            \DB::raw('amount as amount'),
            \DB::raw('no_days as no_days'),
            \DB::raw('exp_vat as exp_vat'),
            \DB::raw('time_created as time_created'),
            \DB::raw('expire_at as expire_at')
          ]);

        $expenses = DeviceExpense::where('device_id', $device->id)->join('expenses', 'expenses.id', '=', 'device_expenses.an_expense_id')->where('shop_id', $shop->id)->whereBetween('time_created', [$start, $end])->orderBy('expense_type', 'asc')->get([
            \DB::raw('expense_type as expense_type'),
            \DB::raw('amount as amount'),
            \DB::raw('no_days as no_days'),
            \DB::raw('exp_vat as exp_vat'),
            \DB::raw('time_created as time_created'),
            \DB::raw('expire_at as expire_at')
          ]);

        $tsales = DeviceSale::where('device_id', $device->id)->join('an_sales', 'an_sales.id', '=', 'device_sales.an_sale_id')->where('shop_id', $shop->id)->whereBetween('time_created', [$start, $end])->get();
                  
        $total_expenses = DeviceExpense::where('device_id', $device->id)->join('expenses', 'expenses.id', '=', 'device_expenses.an_expense_id')->where('shop_id', $shop->id)->whereBetween('time_created', [$start, $end])->sum('amount');
      
      }else{

        $servsales = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->join('services', 'service_sale_items.service_id', '=', 'services.id')->groupBy('price')->groupBy('services.name')->orderBy('service_sale_items.time_created', 'desc')->get([
          \DB::raw('services.name as name'),
          \DB::raw('SUM(no_of_repeatition) as repeatition'),
          \DB::raw('price as price'),
          \DB::raw('SUM(total) as total'),
          \DB::raw('discount as discount'),
          \DB::raw('SUM(total_discount) as total_discount'),
          \DB::raw('service_sale_items.time_created as created_at')
        ]);

        $total_serv_selling = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->sum('total')-AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->join('service_sale_items', 'service_sale_items.an_sale_id', '=', 'an_sales.id')->whereBetween('service_sale_items.time_created', [$start, $end])->sum('total_discount');

        if (!is_null($category)) {

          $ctnexpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('expire_at', '>', $start)->where('time_created', '<', $start)->where('category_id', $category->id)->orderBy('expense_type', 'asc')->get([
            \DB::raw('expense_type as expense_type'),
            \DB::raw('amount as amount'),
            \DB::raw('no_days as no_days'),
            \DB::raw('exp_vat as exp_vat'),
            \DB::raw('time_created as time_created'),
            \DB::raw('expire_at as expire_at')
          ]);

          $expenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->where('category_id', $category->id)->orderBy('expense_type', 'asc')->get([
            \DB::raw('expense_type as expense_type'),
            \DB::raw('amount as amount'),
            \DB::raw('no_days as no_days'),
            \DB::raw('exp_vat as exp_vat'),
            \DB::raw('time_created as time_created'),
            \DB::raw('expire_at as expire_at')
          ]);


           $all_ctnexpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('expire_at', '>', $start)->where('time_created', '<', $start)->where('category_id', null)->orderBy('expense_type', 'asc')->get([
            \DB::raw('expense_type as expense_type'),
            \DB::raw('amount as amount'),
            \DB::raw('no_days as no_days'),
            \DB::raw('exp_vat as exp_vat'),
            \DB::raw('time_created as time_created'),
            \DB::raw('expire_at as expire_at')
          ]);

          $all_expenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->where('category_id', null)->orderBy('expense_type', 'asc')->get([
            \DB::raw('expense_type as expense_type'),
            \DB::raw('amount as amount'),
            \DB::raw('no_days as no_days'),
            \DB::raw('exp_vat as exp_vat'),
            \DB::raw('time_created as time_created'),
            \DB::raw('expire_at as expire_at')
          ]);

          $tsales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->get();
          
        }else{
          $ctnexpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->where('expire_at', '>', $start)->where('time_created', '<', $start)->orderBy('expense_type', 'asc')->get([
            \DB::raw('expense_type as expense_type'),
            \DB::raw('amount as amount'),
            \DB::raw('no_days as no_days'),
            \DB::raw('exp_vat as exp_vat'),
            \DB::raw('time_created as time_created'),
            \DB::raw('expire_at as expire_at')
          ]);

          $expenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->orderBy('expense_type', 'asc')->get([
            \DB::raw('expense_type as expense_type'),
            \DB::raw('amount as amount'),
            \DB::raw('no_days as no_days'),
            \DB::raw('exp_vat as exp_vat'),
            \DB::raw('time_created as time_created'),
            \DB::raw('expire_at as expire_at')
          ]);

          $tsales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->get();
        }
      }

      $myexpenses = collect([]);
      $totalexpenses = 0;
      //All Continue Expenses
      if (!is_null($ctnexpenses)) {
          
        foreach ($ctnexpenses as $key => $expense) {
          $expdays = 0;
          if ($expense->expire_at > $end && $expense->time_created < $start) {
            $expdays = \Carbon\Carbon::parse($end)->diffInDays(\Carbon\Carbon::parse($start))+1;
          }else{
            $expdays = \Carbon\Carbon::parse($expense->expire_at)->diffInDays(\Carbon\Carbon::parse($start))+1;
          }
          $amount = (($expense->amount-$expense->exp_vat)/$expense->no_days)*$expdays;
          $totalexpenses += $amount;
          $myexpenses->push(['expense_type' => $expense->expense_type, 'amount' => $amount]);
        }
      }

      //Categorized continue expenses
      if (!is_null($all_ctnexpenses)) {
          
        foreach ($all_ctnexpenses as $key => $expense) {
          $expdays = 0;
          if ($expense->expire_at > $end && $expense->time_created < $start) {
            $expdays = \Carbon\Carbon::parse($end)->diffInDays(\Carbon\Carbon::parse($start))+1;
          }else{
            $expdays = \Carbon\Carbon::parse($expense->expire_at)->diffInDays(\Carbon\Carbon::parse($start))+1;
          }
          $amount = ((($expense->amount-$expense->exp_vat)/$expense->no_days)*$expday)/$categories->count();
          $totalexpenses += $amount;
          $myexpenses->push(['expense_type' => $expense->expense_type, 'amount' => $amount]);
        }
      }

      //All Normal Expenses
      if (!is_null($expenses)) {
          
        foreach($expenses as $key => $expense) {
          $amount = 0;
          if ($expense->no_days == 1) {
            $amount = ($expense->amount-$expense->exp_vat);
            $totalexpenses += $amount; 
          }else{
            $expdays = 0;
            if($expense->expire_at > $end){
              $expdays= \Carbon\Carbon::parse($end)->diffInDays(\Carbon\Carbon::parse($expense->time_created))+1;
            }else{
              $expdays = $expense->no_days;
            }
            $amount = (($expense->amount-$expense->exp_vat)/$expense->no_days)*$expdays;
            $totalexpenses += $amount;
          }
          $myexpenses->push(['expense_type' => $expense->expense_type, 'amount' => $amount]);
        }
      }

      //All Shared expenses in Categorized business
      if (!is_null($all_expenses)) {
          
        foreach ($all_expenses as $key => $expense) {
          $amount = 0;
          if ($expense->no_days == 1) {
            $amount = ($expense->amount-$expense->exp_vat)/$categories->count();
            $totalexpenses += $amount; 
          }else{
            $expdays = 0;
            if($expense->expire_at > $end){
              $expdays= \Carbon\Carbon::parse($end)->diffInDays(\Carbon\Carbon::parse($expense->time_created))+1;
            }else{
              $expdays = $expense->no_days;
            }
            $amount = ((($expense->amount-$expense->exp_vat)/$expense->no_days)*$expdays)/$categories->count();
            $totalexpenses += $amount;
          }
          $myexpenses->push(['expense_type' => $expense->expense_type, 'amount' => $amount]);
        }
      }

      $groups = $myexpenses->groupby('expense_type');
      

      // we will use map to cumulate each group of rows into single row.
      // $group is a collection of rows that has the same opposition_id.
      $expenses = $groups->map(function ($group) {
          return [
              'expense_type' => $group->first()['expense_type'], // expense_type is constant inside the same group, so just take the first or whatever.
              'amount' => $group->sum('amount'),
          ];
      });
      
      $total_gross_profit = $total_prod_selling-$total_buying;

      $total_selling = ($total_prod_selling-$total_return_prod_selling)+$total_serv_selling;

      $gross_profit = $total_selling-($total_buying-$total_return_buying);

      $total_unpaid = 0;
      $tpaids = 0;
      foreach ($tsales as $key => $tsale) {
        $total_paid += $tsale->sale_amount_paid;
        $total_vat += $tsale->tax_amount;
        $total_unpaid += ($tsale->sale_amount-$tsale->sale_discount-$tsale->adjustment-$tsale->sale_amount_paid);
        if ($tsale->sale_amount_paid > 0) {
          $tpaids++;
        }
      }

      $net_profit = 0; 
      // $net_profit = $gross_profit-$total_expenses-$total_vat;

      $cash_pay = SalePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start,$end])->where('pay_mode', 'Cash')->sum('amount');
      $mob_pay = SalePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start,$end])->where('pay_mode', 'Mobile Money')->sum('amount'); 
      $bank_pay = SalePayment::where('shop_id', $shop->id)->whereBetween('pay_date', [$start,$end])->where('pay_mode', 'Bank')->sum('amount');

      $total_pay = $cash_pay+$mob_pay+$bank_pay;

      $cash_amount = $total_pay-$total_expenses;

      $total_sales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('sale_amount')-AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('sale_discount')-AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('adjustment');

      $total_expenses = Expense::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('time_created', [$start, $end])->sum('amount');
      
      $total_collections = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->sum('amount')+CashIn::where('shop_id', $shop->id)->whereBetween('in_date', [$start, $end])->sum('amount');
      
      $dpsales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->groupBy('date')->orderBy('date', 'asc')->get([
        \DB::raw('SUM(sale_amount) as amount'),
        \DB::raw('SUM(sale_discount) as discount'),
        \DB::raw('SUM(sale_amount_paid) as amount_paid'),
        \DB::raw('SUM(adjustment) as adjustment'),
        \DB::raw('DATE(an_sales.time_created) as date')
      ]);

      $paid_expenses = ExpensePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->sum('amount');

      $purchase_payments = PurchasePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->sum('amount');

      $total_cashout = CashOut::where('shop_id', $shop->id)->whereBetween('out_date', [$start, $end])->sum('amount');
      $ncouts = CashOut::where('shop_id', $shop->id)->whereBetween('out_date', [$start, $end])->count();
      $purchpays = PurchasePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->count();
      $paidexps = ExpensePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->count();
      $tcols = SalePayment::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('pay_date', [$start, $end])->count();
      $nsales = AnSale::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('an_sales.time_created', [$start, $end])->count();
      $ndebts = 0;

      $total_debts = 0;
      $total_payments = 0;

      foreach ($dpsales as $key => $debt) {
        $total_debts += ($debt->amount-($debt->discount+$debt->adjustment+$debt->amount_paid));
        $total_payments += $debt->amount_paid;
        if (($debt->amount-($debt->discount+$debt->adjustment+$debt->amount_paid)) > 0) {
          $ndebts++;
        }
      }

      $paid_debts = $total_collections-$total_paid;
      $tpdebts = $tcols-$tpaids;

      $closing_balance = $total_collections-$paid_expenses-$purchase_payments-$total_cashout;

      $crtime = \Carbon\Carbon::now();
      $reporttime = $crtime->toDayDateTimeString();
      return view('reports.report-summary', compact('page', 'title', 'title_sw', 'shop', 'reporttime', 'duration', 'duration_sw', 'sales', 'returns', 'servsales', 'expenses', 'total_prod_selling', 'total_buying', 'total_serv_selling', 'total_gross_profit', 'total_selling', 'gross_profit', 'net_profit', 'total_paid', 'totalexpenses', 'total_unpaid', 'cash_amount', 'total_vat', 'is_post_query', 'start_date', 'end_date', 'settings', 'devices', 'device', 'total_return_prod_selling', 'total_return_buying', 'total_return_gross_profit', 'cash_pay', 'mob_pay', 'bank_pay', 'total_pay', 'categories', 'category', 'shared_expenses', 'total_sales', 'total_payments', 'total_debts', 'total_expenses', 'paid_expenses', 'purchase_payments', 'paid_debts', 'total_collections', 'total_cashout', 'closing_balance', 'start', 'end', 'ctnexpenses', 'nsales', 'ndebts', 'tcols', 'paidexps', 'purchpays', 'tpaids', 'tpdebts', 'ncouts'));
    }
}
