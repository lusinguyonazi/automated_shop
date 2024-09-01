<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('postapi', [App\Http\Controllers\Auth\PaymentController::class, 'testApi']);
Route::get('an-sales', [App\Http\Controllers\Api\AnSaleController::class, 'index']);

Route::get('sm_cu_payments', [App\Http\Controllers\Auth\PaymentController::class, 'store']);

Route::get('financial-reports', [App\Http\Controllers\Api\ReportsController::class, 'financialReports']);
    
Route::group(['middleware' => 'cors'], function (){

    Route::post('register', [App\Http\Controllers\Api\UserController::class, 'register']);
    Route::post('register-shop', [App\Http\Controllers\Api\UserController::class, 'registerShop']);
    Route::post('login', [App\Http\Controllers\Api\AuthenticateController::class, 'authenticate']);
    Route::get('business-areas', [App\Http\Controllers\Api\UserController::class, 'businAreas']);
    Route::post('service-charges', [App\Http\Controllers\Api\UserController::class, 'serviceCharges']);
    Route::get('products', [App\Http\Controllers\Api\ProductController::class, 'index']);

    Route::get('btypes', [App\Http\Controllers\Api\UserController::class, 'btypes']);
    Route::get('countries', [App\Http\Controllers\Api\UserController::class, 'countries']);

    // Reset password
    Route::post('forgot-pass', [App\Http\Controllers\Api\UserController::class, 'forgotPass']);
    Route::post('reset-code', [App\Http\Controllers\Api\UserController::class, 'verifyCode']);
    Route::post('reset-pass', [App\Http\Controllers\Api\UserController::class, 'resetPass']);

 
        Route::group(['middleware' => 'jwt.auth'], function(){
        // SMART MAUZO API SOKONI 
        Route::post('check-token', function(){
            return response()->json(['error' => false]);
        });

       Route::post('home-dashboard' , [App\Http\Controllers\Api\HomeController::class, 'index']);
       Route::post('get-user-info' , [App\Http\Controllers\Api\UserController::class, 'getUserInfo']);
       Route::post('update-user-info' , [App\Http\Controllers\Api\UserController::class, 'updateUser']);
        
        Route::get('user-busin-area', [App\Http\Controllers\Api\UserController::class, 'getAuthUserBusinArea']);
        Route::get('user', [App\Http\Controllers\Api\UserController::class, 'getAuthUser']);
        Route::post('change-password', [App\Http\Controllers\Api\UserController::class, 'changePass']);
        Route::post('add-shop-user', [App\Http\Controllers\Api\UserController::class, 'addUser']);
        Route::get('shop-user', [App\Http\Controllers\Api\UserController::class, 'getAuthShopUser']);
        Route::post('add-shop', [App\Http\Controllers\Api\ShopController::class, 'create']);
        Route::post('update-shop', [App\Http\Controllers\Api\ShopController::class, 'update']);
        Route::post('get-settings', [App\Http\Controllers\Api\SettingsController::class, 'index']);
        Route::post('update-settings', [App\Http\Controllers\Api\SettingsController::class, 'update']);

        Route::post('change-btype', [App\Http\Controllers\Api\SettingsController::class, 'changeBusinessType']);
        Route::post('change-stype', [App\Http\Controllers\Api\SettingsController::class, 'changeSubscriptionType']);

        Route::post('switch-shop', [App\Http\Controllers\Api\ShopController::class, 'index']);
        Route::post('switched', [App\Http\Controllers\Api\ShopController::class, 'switched']);
        Route::post('shop-sellers', [App\Http\Controllers\Api\ShopController::class, 'shopSellers']);
        Route::post('delete-salesman', [App\Http\Controllers\Api\ShopController::class, 'detachUser']);
        Route::post('shop-data', [App\Http\Controllers\Api\ShopController::class, 'shopData']);
        Route::post('detach-user', [App\Http\Controllers\Api\ShopController::class, 'detachUser']);

        Route::post('shop-reports', [App\Http\Controllers\Api\ReportsController::class, 'reportsByDateRange']);
        Route::post('profit-reports', [App\Http\Controllers\Api\ReportsController::class, 'profits']);
        Route::post('debts-report', [App\Http\Controllers\Api\ReportsController::class, 'debts']);
        Route::post('charts', [App\Http\Controllers\Api\ChartsController::class, 'index']);
        Route::post('stock-status-report', [App\Http\Controllers\Api\StockReportsController::class, 'stockStatusReport']);
        Route::post('stock-reports', [App\Http\Controllers\Api\StockReportsController::class, 'index']);
        Route::post('stock-capital', [App\Http\Controllers\Api\StockReportsController::class, 'stockCapital']);
        Route::post('capital-summary', [App\Http\Controllers\Api\StockReportsController::class, 'capitalSummary']);
        Route::post('purchase-report', [App\Http\Controllers\Api\StockReportsController::class, 'purchases']);
        Route::post('an-preview-sales', [App\Http\Controllers\Api\ReportsController::class, 'sales']);
        Route::post('expense-report', [App\Http\Controllers\Api\ReportsController::class, 'expenses']);

        Route::post('an-total-amount-by-date', [App\Http\Controllers\Api\ReportsController::class, 'totalAmountByDate']);
        Route::post('consolidated', [App\Http\Controllers\Api\ReportsController::class, 'consolidated']);
        Route::post('summary-report' , [App\Http\Controllers\Api\ReportsController::class, 'summaryReport']);
        
        Route::post('shop-reports-date-range', [App\Http\Controllers\Api\ReportsController::class, 'reportsByDateRange']);
        Route::post('profit-reports-date-range', [App\Http\Controllers\Api\ReportsController::class, 'profits']);
        Route::post('an-preview-sales-date-range', [App\Http\Controllers\Api\AnSaleController::class, 'previewSalesByDateRange']);
        Route::post('stock-reports-date-range', [App\Http\Controllers\Api\StockReportsController::class, 'reportsByDateRange']);


        Route::post('cash-inflows', [App\Http\Controllers\Api\CashinController::class, 'index']);
        Route::post('new-cashin', [App\Http\Controllers\Api\CashinController::class, 'store']);
        Route::post('update-cashin', [App\Http\Controllers\Api\CashinController::class, 'update']);
        Route::post('delete-cashin', [App\Http\Controllers\Api\CashinController::class, 'destroy']);

        Route::post('cash-outflows', [App\Http\Controllers\Api\CashOutController::class, 'index']);
        Route::post('new-cashout', [App\Http\Controllers\Api\CashOutController::class, 'store']);
        Route::post('update-cashout', [App\Http\Controllers\Api\CashOutController::class, 'update']);
        Route::post('delete-cashout', [App\Http\Controllers\Api\CashOutController::class, 'destroy']);

        Route::post('account-transactions', [App\Http\Controllers\Api\AccountTransController::class, 'index']);
        Route::post('new-transaction', [App\Http\Controllers\Api\AccountTransController::class, 'store']);
        Route::post('update-transaction', [App\Http\Controllers\Api\AccountTransController::class, 'update']);
        Route::post('delete-transaction', [App\Http\Controllers\Api\AccountTransController::class, 'destroy']);
        Route::post('bank-details', [App\Http\Controllers\Api\AccountTransController::class, 'create']);

        Route::post('cash-flow-stmt', [App\Http\Controllers\Api\ReportsController::class, 'financialReports']);

        Route::post('update-account', [App\Http\Controllers\Api\UserController::class, 'update']);

        Route::post('payment-info', [App\Http\Controllers\Api\ShopController::class, 'getPaymentInfo']);
        Route::post('payment-proof', [App\Http\Controllers\Api\PaymentController::class, 'create']);
        //Customers route
        Route::post('customers', [App\Http\Controllers\Api\CustomerController::class, 'index']);
        Route::post('create-customer', [App\Http\Controllers\Api\CustomerController::class, 'create']);
        Route::post('update-customer', [App\Http\Controllers\Api\CustomerController::class, 'update']);
        Route::post('delete-customer', [App\Http\Controllers\Api\CustomerController::class, 'delete']);
        
        //Suppliers route
        Route::post('suppliers', [App\Http\Controllers\Api\SupplierController::class, 'index']);
        Route::post('create-supplier', [App\Http\Controllers\Api\SupplierController::class, 'create']);
        Route::post('update-supplier', [App\Http\Controllers\Api\SupplierController::class, 'update']);
        Route::post('delete-supplier', [App\Http\Controllers\Api\SupplierController::class, 'delete']);
        
        //Products Routes
        Route::post('shop-products', [App\Http\Controllers\Api\ProductController::class, 'getUserProducts']);
        Route::get('units', [App\Http\Controllers\Api\ProductController::class, 'basicUnits']);
        Route::post('units', [App\Http\Controllers\Api\ProductController::class, 'basicUnits']);
        Route::post('add-product', [App\Http\Controllers\Api\ProductController::class, 'create']);
        Route::post('update-product', [App\Http\Controllers\Api\ProductController::class, 'update']);
        Route::post('delete-product', [App\Http\Controllers\Api\ProductController::class, 'delete']);
        Route::post('product-sales', [App\Http\Controllers\Api\ProductController::class, 'show']);

        //Stocks route
        Route::post('stocks', [App\Http\Controllers\Api\StockController::class, 'index']);
        
        Route::post('product-stocks', [App\Http\Controllers\Api\StockController::class, 'productStocks']);

        Route::post('add-stock', [App\Http\Controllers\API\StockController::class, 'createStock']);
        // Route::post('new-stock-out', [App\Http\Controllers\Api\StockController::class, 'newStockOut']);
        Route::post('update-stock', [App\Http\Controllers\Api\StockController::class, 'updateStock']);
        Route::post('delete-stock', [App\Http\Controllers\Api\StockController::class, 'deleteStock']);

        //Stock Details route
        Route::post('stock_details', [App\Http\Controllers\Api\StockController::class, 'stockDetails']);
        
        Route::post('product-stocks', [App\Http\Controllers\Api\StockController::class, 'productStocks']);
        Route::post('add-stockdetail', [App\Http\Controllers\Api\StockController::class, 'addStockDetail']);
        Route::post('update-stockdetail', [App\Http\Controllers\Api\StockController::class, 'updateStockDetail']);
        Route::post('delete-stockdetail', [App\Http\Controllers\Api\StockController::class, 'deleteStockDetail']);

        //Purchases Routes
        Route::post('purchases', [App\Http\Controllers\Api\StockController::class, 'purchases']);
        Route::post('new-purchase', [App\Http\Controllers\Api\StockController::class, 'newPurchase']);
        Route::post('update-purchase', [App\Http\Controllers\Api\StockController::class, 'updatePurchase']);
        Route::post('delete-purchase', [App\Http\Controllers\Api\StockController::class, 'deletePurchase']);
        Route::post('add-purchase-payment', [App\Http\Controllers\Api\PurchasePaymentController::class, 'store']);
        //Damages route
        Route::post('damages', [App\Http\Controllers\Api\ProdDamageController::class, 'index']);
        
        Route::post('add-damage', [App\Http\Controllers\Api\ProdDamageController::class, 'store']);
        Route::post('update-damage', [App\Http\Controllers\Api\ProdDamageController::class, 'update']);
        Route::post('delete-damage', [App\Http\Controllers\Api\ProdDamageController::class, 'destroy']);
        
        // Route::post('product-buying-price', [App\Http\Controllers\Api\ProductController::class, 'buyingPrice']);
        // Route::post('product-price', [App\Http\Controllers\Api\ProductController::class, 'unitPrice']);
        // Route::post('price-list', [App\Http\Controllers\Api\ProductController::class, 'priceList']);

        //Services route
        Route::post('services', [App\Http\Controllers\Api\ServiceController::class, 'index']);
        Route::post('add-service', [App\Http\Controllers\Api\ServiceController::class, 'store']);
        Route::post('update-service', [App\Http\Controllers\Api\ServiceController::class, 'update']);
        Route::post('delete-service', [App\Http\Controllers\Api\ServiceController::class, 'destroy']);

        Route::post('devices', [App\Http\Controllers\Api\DeviceController::class, 'index']);
        Route::post('add-device', [App\Http\Controllers\Api\DeviceController::class, 'store']);
        Route::post('update-device', [App\Http\Controllers\Api\DeviceController::class, 'update']);
        Route::post('delete-device', [App\Http\Controllers\Api\DeviceController::class, 'destroy']);
        Route::post('device-sales', [App\Http\Controllers\Api\DeviceController::class, 'deviceSales']);
        Route::post('device-expenses', [App\Http\Controllers\Api\DeviceController::class, 'deviceExpenses']);
        Route::post('device-sale', [App\Http\Controllers\Api\DeviceController::class, 'newDeviceSale']);
        Route::post('device-expense', [App\Http\Controllers\Api\DeviceController::class, 'newDeviceExpense']);
        Route::post('update-device-sale', [App\Http\Controllers\Api\DeviceController::class, 'updateDeviceSale']);
        Route::post('update-device-expense', [App\Http\Controllers\Api\DeviceController::class, 'updateDeviceExpense']);

        Route::post('an-sales', [App\Http\Controllers\Api\AnSaleController::class, 'index']);
        // Route::post('create-an-sale', [App\Http\Controllers\Api\AnSaleController::class, 'create']);
        Route::post('update-an-sale', [App\Http\Controllers\Api\AnSaleController::class, 'update']);
        Route::post('an-add-paid-amount', [App\Http\Controllers\Api\AnSaleController::class, 'updateAmountPaid']);
        Route::post('delete-an-sale', [App\Http\Controllers\Api\AnSaleController::class, 'delete']);

        Route::post('pos', [App\Http\Controllers\Api\POSController::class, 'index']);
        Route::post('shop-items', [App\Http\Controllers\Api\POSController::class, 'products']);
        Route::post('create-an-sale', [App\Http\Controllers\Api\POSController::class, 'store']);
        Route::post('cancel-sale', [App\Http\Controllers\Api\POSController::class, 'cancel']);
        Route::post('sale-temp-items', [App\Http\Controllers\Api\SaleItemTempController::class, 'index']);
        Route::post('add-temp-item', [App\Http\Controllers\Api\SaleItemTempController::class, 'store']);
        Route::post('update-temp-item', [App\Http\Controllers\Api\SaleItemTempController::class, 'update']);
        Route::post('delete-temp-item', [App\Http\Controllers\Api\SaleItemTempController::class, 'destroy']);

        Route::post('an-cash-sales', [App\Http\Controllers\Api\AnSaleController::class, 'cashSales']);
        Route::post('debt-sales', [App\Http\Controllers\Api\InvoicesController::class, 'debtSales']);
        Route::post('invoices', [App\Http\Controllers\Api\InvoicesController::class, 'index']);
        Route::post('create-invoice', [App\Http\Controllers\Api\InvoicesController::class, 'store']);
        Route::post('update-invoice', [App\Http\Controllers\Api\InvoicesController::class, 'update']);
        Route::post('delete-invoice', [App\Http\Controllers\Api\InvoicesController::class, 'destroy']);
        Route::post('an-total-cash', [App\Http\Controllers\Api\AnSaleController::class, 'totalCashAmount']);
        Route::post('an-total-loaned', [App\Http\Controllers\Api\AnSaleController::class, 'totalLoanedAmount']);

        Route::post('an-preview-sales-period', [App\Http\Controllers\Api\AnSaleController::class, 'salesByPeriod']);
        Route::post('an-saleitems', [App\Http\Controllers\Api\AnSaleItemController::class, 'saleItems']);      
        Route::post('create-sale-items', [App\Http\Controllers\Api\AnSaleItemController::class, 'create']);
        
        Route::post('service-sale-items', [App\Http\Controllers\Api\ServiceSaleItemController::class, 'index']);
        Route::post('create-service-sale-item', [App\Http\Controllers\Api\ServiceSaleItemController::class, 'store']);

        Route::post('an-sale-items', [App\Http\Controllers\Api\AnSaleItemController::class, 'index']);

        Route::post('add-an-sale-item', [App\Http\Controllers\Api\AnSaleItemController::class, 'create']);
        Route::post('update-an-sale-item', [App\Http\Controllers\Api\AnSaleItemController::class, 'update']);
        Route::post('delete-an-sale-item', [App\Http\Controllers\Api\AnSaleItemController::class, 'delete']);

        Route::post('an-total-sale', [App\Http\Controllers\Api\AnSaleItemController::class, 'totalSale']);

        Route::post('sale-payments', [App\Http\Controllers\Api\SalePaymentController::class, 'index']);
        Route::post('salepayment', [App\Http\Controllers\Api\SalePaymentController::class, 'salePayments']);
        
        Route::post('add-sale-payment', [App\Http\Controllers\Api\SalePaymentController::class, 'store']);
        Route::post('update-sale-payment', [App\Http\Controllers\Api\SalePaymentController::class, 'update']);
        Route::post('delete-sale-payment', [App\Http\Controllers\Api\SalePaymentController::class, 'destroy']);

        //Cost routes
        Route::post('an-costs', [App\Http\Controllers\Api\AnCostsController::class, 'index']);
        Route::post('an-add-cost', [App\Http\Controllers\Api\AnCostsController::class, 'create']);
        Route::post('an-cost-types', [App\Http\Controllers\Api\AnCostsController::class, 'costTypes']);
        Route::post('an-update-cost', [App\Http\Controllers\Api\AnCostsController::class, 'update']);
        Route::post('an-delete-cost', [App\Http\Controllers\Api\AnCostsController::class, 'delete']);
        Route::post('an-preview-cost', [App\Http\Controllers\Api\AnCostsController::class, 'costsByPeriod']);
        Route::post('an-total-cost', [App\Http\Controllers\Api\AnCostsController::class, 'totalCost']);
        Route::post('an-total-cost-period', [App\Http\Controllers\Api\AnCostsController::class, 'totalCostByPeriod']);

        Route::post('add-exp-payment', [App\Http\Controllers\Api\ExpensePaymentController::class, 'store']);

        Route::post('latest-sold-logs', [App\Http\Controllers\Api\AnSaleItemController::class, 'getShopSlodLogs']);
        Route::post('new-latest-sold-log', [App\Http\Controllers\Api\AnSaleItemController::class, 'newLatestSoldLog']);
        Route::post('update-sold-log', [App\Http\Controllers\Api\AnSaleItemController::class, 'updateSoldLog']);
        //Sales Return
        Route::post('sales-returns', [App\Http\Controllers\Api\SalesReturnController::class, 'index']);
        Route::post('get-sales', [App\Http\Controllers\Api\SalesReturnController::class, 'getSales']);
        Route::post('get-items', [App\Http\Controllers\Api\SalesReturnController::class, 'getItems']);
        Route::post('new-sale-return', [App\Http\Controllers\Api\SalesReturnController::class, 'store']);
        Route::post('update-return-item', [App\Http\Controllers\Api\SalesReturnController::class, 'edit']);
        Route::post('remove-return-item', [App\Http\Controllers\Api\SalesReturnController::class, 'removeItem']);
        Route::post('update-return', [App\Http\Controllers\Api\SalesReturnController::class, 'update']);
        Route::post('cancel-return', [App\Http\Controllers\Api\SalesReturnController::class, 'destroy']);
        //Reports start here

        Route::post('sales-stats', [App\Http\Controllers\Api\ReportsController::class, 'index']);
        // SMART MAUZO SHOPPING API En
    });
});
