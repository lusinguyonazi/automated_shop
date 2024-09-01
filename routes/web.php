<?php

use Illuminate\Support\Facades\Route;

// Auth Controllers
use App\Http\Controllers\Auth\RegisterController;

// Admin routes
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ServiceChargeController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserTransactionController;
use App\Http\Controllers\Admin\SenderIDController;
use App\Http\Controllers\Admin\BusinessTypeController;
use App\Http\Controllers\Admin\SubscriptionTypeController;
use App\Http\Controllers\Admin\SmsAccountController;

// Customer Controllers
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\ShopController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\BankDetailController;

use App\Http\Controllers\Web\ProductsController;
use App\Http\Controllers\Web\PurchasesController;
use App\Http\Controllers\Web\StockItemTempApiController;
use App\Http\Controllers\Web\ServiceController;
use App\Http\Controllers\Web\DeviceController;
use App\Http\Controllers\Web\GradeController;
use App\Http\Controllers\Web\ShopServiceApiController;
use App\Http\Controllers\Web\ShopProductsApiController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\StockController;
use App\Http\Controllers\Web\PurchasePaymentController;
use App\Http\Controllers\Web\PurchaseOrderController;
use App\Http\Controllers\Web\PurchaseOrderItemController;
use App\Http\Controllers\Web\PurchaseOrderTempApiController;
use App\Http\Controllers\Web\ProdDamageController;
use App\Http\Controllers\Web\TransferOrderController;
use App\Http\Controllers\Web\TransferOrderItemTempController;
use App\Http\Controllers\Web\TransformationTransferItemController;
use App\Http\Controllers\Web\TransformationTransferItemTempController;
use App\Http\Controllers\Web\SupplierController;
use App\Http\Controllers\Web\ProductUnitController;


use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\SaleController;
use App\Http\Controllers\Web\SaleItemTempController;
use App\Http\Controllers\Web\ServiceSaleItemTempController;
use App\Http\Controllers\Web\AnSaleController;
use App\Http\Controllers\Web\AnSaleItemController;
use App\Http\Controllers\Web\ServiceSaleItemController;
use App\Http\Controllers\Web\SalePaymentController;
use App\Http\Controllers\Web\SaleReturnController;
use App\Http\Controllers\Web\SaleReturnItemController;
use App\Http\Controllers\Web\RecycleBinController;

use App\Http\Controllers\Web\InvoiceController;
use App\Http\Controllers\Web\ProInvoiceController;
use App\Http\Controllers\Web\InvoiceItemTempController;
use App\Http\Controllers\Web\ServiceInvoiceItemTempController;
use App\Http\Controllers\Web\DeliveryNoteController;
use App\Http\Controllers\Web\CreditNoteController;

use App\Http\Controllers\Web\ExpenseCategoryController;
use App\Http\Controllers\Web\ExpenseController;
use App\Http\Controllers\Web\ExpenseTempController;
use App\Http\Controllers\Web\ExpensePaymentController;

use App\Http\Controllers\Web\CashOutController;
use App\Http\Controllers\Web\CashInController;
use App\Http\Controllers\Web\AccountTransController;

use App\Http\Controllers\Web\ReportsController;
use App\Http\Controllers\Web\FinancialReportsController;
use App\Http\Controllers\Web\StockReportController;

use App\Http\Controllers\Web\VerifyPaymentController;

// Payroll
use App\Http\Controllers\Web\Payroll\PositionController;
use App\Http\Controllers\Web\Payroll\EmployeeController;
use App\Http\Controllers\Web\Payroll\PayrollController;
use App\Http\Controllers\Web\Payroll\PayrollTempController;

//VFD
use App\Http\Controllers\VFD\RegInfoController;
use App\Http\Controllers\VFD\RctInfoController;
use App\Http\Controllers\VFD\ZReportController;

// PRODUCTION 
use App\Http\Controllers\Prod\RawMaterialController;
use App\Http\Controllers\Prod\RmPurchaseController;
use App\Http\Controllers\Prod\RmSupplierTransactionController;
use App\Http\Controllers\Prod\RmPurchasePaymentController;
use App\Http\Controllers\Prod\RmPurchaseItemApiController;
use App\Http\Controllers\Prod\RawMaterialApiController;
use App\Http\Controllers\Prod\RmUseController;
use App\Http\Controllers\Prod\RmUseItemTempController;
use App\Http\Controllers\Prod\RmUsedItemController;
use App\Http\Controllers\Prod\PackingMaterialController;
use App\Http\Controllers\Prod\PackingMaterialApiController;
use App\Http\Controllers\Prod\PmPurchaseItemApiController;
use App\Http\Controllers\Prod\PmSupplierTransactionController;
use App\Http\Controllers\Prod\PmPurchaseController;
use App\Http\Controllers\Prod\PmPurchasePaymentController;
use App\Http\Controllers\Prod\PmItemController;
use App\Http\Controllers\Prod\PmUseItemTempController;
use App\Http\Controllers\Prod\MROItemController;
use App\Http\Controllers\Prod\MROController;
use App\Http\Controllers\Prod\MroUsedItemTempController;
use App\Http\Controllers\Prod\MroUseController;
use App\Http\Controllers\Prod\MroApiController;
use App\Http\Controllers\Prod\SettingController;
use App\Http\Controllers\Prod\ProductionCostController;
use App\Http\Controllers\Prod\ProductionApiController;

//Api Test
use App\Http\Controllers\ApiTestController;
use App\Http\Controllers\AutoCompleteSearch;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  return view('welcome');
});

Auth::routes();
Route::post('get-sub-types', [RegisterController::class, 'getBSubTypes']);
Route::post('password-phone', [ForgotPasswordController::class, 'forgotPass']);
Route::post('verify-code', [ResetPasswordController::class, 'verifyCode']);

Route::post('reset-pass', [ResetPasswordController::class, 'resetPass']);

//Admin Routes
Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function () {
  Route::get('/home', [DashboardController::class, 'index'])->name('home');
  Route::get('totals', [DashboardController::class, 'total']);
  Route::get('new-activations', [DashboardController::class, 'newActivations']);
  Route::get('sms-logs', [DashboardController::class, 'smsResponseLogs']);
  Route::get('clear-logs', [DashboardController::class, 'clearSMSLogs']);
  Route::resource('service-charges', ServiceChargeController::class);
  Route::resource('sms-templates', SmsTemplateControlle::class);
  Route::resource('modules', ModuleController::class);
  Route::resource('payments', PaymentController::class);
  Route::get('payments-export/{from}/{to}/{term}/{id}', [PaymentController::class, 'paymentsExport']);
  Route::get('/payments-search-query', [PaymentController::class, 'query'])->name('payments.search.query');
  Route::get('service-payments', [PaymentController::class, 'index']);
  Route::get('activated-payments', [PaymentController::class, 'activatedPayments']);
  Route::get('agent-activations', [PaymentController::class, 'agentActivations']);
  Route::get('activations-once', [PaymentController::class, 'activationsOnce']);
  Route::post('post-users', [UserController::class, 'index']);
  Route::resource('users', UserController::class);
  Route::get('export-users', [UserController::class, 'exportUsers']);
  Route::get('reset-password', [UserController::class, 'passwordResets']);
  Route::get('staffs', [UserController::class, 'staffs']);
  Route::get('users-destroy/{id}', [UserController::class, 'destroy']);
  Route::get('clear-reset-codes', [UserController::class, 'clearResetCodes']);
  Route::post('registered-users', [UserController::class, 'index']);
  Route::post('new-role', [UserController::class, 'newRole']);
  Route::get('active-users', [UserController::class, 'activeUsers']);
  Route::get('guest-users', [UserController::class, 'guestUsers']);
  Route::get('shops', [UserController::class, 'shops']);
  Route::get('export-shops', [UserController::class, 'exportShops']);
  Route::post('shops', [UserController::class, 'shops']);
  Route::get('change-subscr-type/{id}', [UserController::class, 'changeSubscriptionType']);
  Route::get('active-shops', [UserController::class, 'activeShops']);
  Route::resource('roles', RoleController::class);
  Route::get('roles/destroy/{id}', [RoleController::class, 'destroy']);
  Route::post('assign-role', [UserController::class, 'assignUserRole']);
  Route::post('detach-role', [UserController::class, 'detachUserRole']);
  Route::post('create-agent-code', [UserController::class, 'createAgentCode']);
  Route::get('customers-by-agents', [UserController::class, 'agentsCustomers']);
  Route::resource('permissions', PermissionController::class);
  Route::get('permissions/destroy/{id}', [PermissionController::class, 'destroy']);

  Route::get('user-shops', [UserTransactionController::class, 'shops']);
  Route::get('act-shops', [UserTransactionController::class, 'getShops']);
  Route::get('sh-sales', [Admin\UserTransactionController::class, 'sales']);
  Route::get('sales', [UserTransactionController::class, 'getSales']);
  Route::get('sh-items', [UserTransactionController::class, 'items']);
  Route::get('items', [UserTransactionController::class, 'getItems']);
  Route::get('sh-products', [UserTransactionController::class, 'products']);
  Route::get('products', [UserTransactionController::class, 'getProducts']);
  Route::get('sh-stocks', [UserTransactionController::class, 'stocks']);
  Route::get('stocks', [UserTransactionController::class, 'getStocks']);
  Route::get('item/{id}', [UserTransactionController::class, 'getItem']);
  Route::post('update-item', [UserTransactionController::class, 'updateItem']);

  Route::get('profile', [UserController::class, 'profile']);
  Route::post('update-profile', [UserController::class, 'updateProfile']);
  Route::get('change-password', [UserController::class, 'changePassForm']);
  Route::post('change-password', [UserController::class, 'changePass']);

  Route::resource('types', BusinessTypeController::class);
  Route::resource('subscriptions', SubscriptionTypeController::class);
  Route::get('types/destroy/{id}', [BusinessTypeController::class, 'destroy']);
  Route::get('subscriptions/destroy/{id}', [SubscriptionTypeController::class, 'destroy']);

  Route::resource('sms-accounts', SmsAccountController::class);
  Route::get('sms-accounts/destroy/{id}', [SmsAccountController::class, 'destroy']);
  Route::resource('sender-ids', SenderIDController::class);
  Route::get('sender-ids/destroy/{id}', [SenderIDController::class, 'destroy']);
});

//Joint Venture Rouutes
Route::group(['middleware' => 'auth', 'prefix' => 'jointventure'], function () {
  Route::get('home', [JointVentureController::class, 'index']);
  Route::get('users', [JointVentureController::class, 'users']);
  Route::get('shops', [JointVentureController::class, 'shops']);
  Route::get('payments', [JointVentureController::class, 'payments']);
  Route::post('home', [JointVentureController::class, 'index']);
  Route::post('users', [JointVentureController::class, 'users']);
  Route::post('shops', [JointVentureController::class, 'shops']);
  Route::post('payments', [JointVentureController::class, 'payments']);
  Route::get('totals', [JointVentureController::class, 'totals']);
  Route::get('new-activations', [JointVentureController::class, 'newActivations']);
  Route::get('profile', [UserController::class, 'profile']);
  Route::post('update-profile', [UserController::class, 'updateProfile']);
  Route::get('change-password', [UserController::class, 'changePassForm']);
  Route::post('change-password', [UserController::class, 'changePass']);
});

Route::group(['middleware' => 'auth', 'prefix' => 'sales-officer'], function () {
  Route::resource('customer-visits', App\Http\Controllers\Sales\CustomerVisitController::class);
  Route::get('customer-visits-destroy/{id}', [App\Http\Controllers\Sales\CustomerVisitController::class, 'destroy']);
  Route::get('visit-reports', [App\Http\Controllers\Sales\CustomerVisitController::class, 'visitReports']);
});

Route::group(['middleware' => 'auth', 'prefix' => 'agent'], function () {
  Route::resource('my-customers', App\Http\Controllers\Sales\AgentCustomerController::class);
  // Route::get('my-customers-destroy/{id}', [App\Http\Controllers\Sales\AgentCustomerController::class, 'destroy']);
  Route::get('paid-customers', [App\Http\Controllers\Sales\AgentCustomerController::class, 'paidCustomers']);
});

//Joint Venture Rouutes
Route::group(['middleware' => 'auth', 'prefix' => 'customercare'], function () {
  Route::get('users', [App\Http\Controllers\Sales\CustomerCareController::class, 'users']);
  Route::get('shops', [App\Http\Controllers\Sales\CustomerCareController::class, 'shops']);
  Route::get('payments', [App\Http\Controllers\Sales\CustomerCareController::class, 'payments']);
  Route::post('users', [App\Http\Controllers\Sales\CustomerCareController::class, 'users']);
  Route::post('shops', [App\Http\Controllers\Sales\CustomerCareController::class, 'shops']);
  Route::post('payments', [App\Http\Controllers\Sales\CustomerCareController::class, 'payments']);
});

//SmartMauzo customers routes
Route::group(['middleware' => 'auth'], function () {

  Route::get('/home', [HomeController::class, 'index']);
  Route::post('home', [HomeController::class, 'index']);
  Route::get('verify-payment', [VerifyPaymentController::class, 'index']);
  Route::post('verify-payment', [VerifyPaymentController::class, 'verify']);
  Route::get('verify-module-payment/{id}', [VerifyPaymentController::class, 'modulePayment']);
  Route::post('verify-module-payment', [VerifyPaymentController::class, 'verifyModulePayment']);
  Route::get('make-payment', function () {
    $charges = App\Models\ServiceCharge::where('type', 1)->orderBy('initial_pay', 'desc')->get();
    $annual_std = App\Models\ServiceCharge::where('duration', 'Annually')->where('type', 1)->first();
    $precharges = App\Models\ServiceCharge::where('type', 2)->orderBy('initial_pay', 'desc')->get();
    $annual_pre = App\Models\ServiceCharge::where('duration', 'Annually')->where('type', 2)->first();
    $shopcount = App\Models\Shop::count();
    return view('payment', compact('charges', 'annual_std', 'precharges', 'annual_pre', 'shopcount'));
  });

  Route::post('pesapal-iframe', [PesapalTransactionController::class, 'store']);
  // Route::get('pesapal-update', [PesaPalPaymentsController::class, 'update']);
  Route::get('pesapal-ipn', [PesapalTransactionController::class, 'paymentConfirmation']);
  Route::get('donepayment', ['as' => 'paymentsuccess', 'uses' => [PesapalTransactionController::class, 'paymentsuccess']]);
  Route::get('view-receipt/{id}', [ProfileController::class, 'viewReceipt']);
  Route::post('update-profile', [ProfileController::class, 'updateProfile']);
  Route::get('change-password', [ProfileController::class, 'changePassForm']);
  Route::post('change-password', [ProfileController::class, 'changePass']);

  Route::get('upgrade', [SettingsController::class, 'upgrade']);
  Route::get('downgrade', [SettingsController::class, 'downgrade']);
  Route::post('update-bsettings', [SettingsController::class, 'store']);
  Route::get('settings', [SettingsController::class, 'index']);
  Route::post('edit-settings', [SettingsController::class, 'update']);
  Route::post('change-btype', [SettingsController::class, 'show']);
  Route::post('set-currency', [SettingsController::class, 'setCurrency']);
  Route::get('rem-currency/{id}', [SettingsController::class, 'removeCurrency']);
  Route::get('make-default-currency/{id}', [SettingsController::class, 'makeDefaultCurrency']);
  Route::resource('prod-settings', SettingController::class);
  Route::get('recyclebin', [RecycleBinController::class, 'index']);
  Route::post('recyclebin', [RecycleBinController::class, 'index']);
  Route::get('recyclebinlive', [RecycleBinController::class, 'indexlive']); /////
  Route::get('recycle-sale/{id}', [RecycleBinController::class, 'recycleSale']);
  Route::get('recycle-purchase/{id}', [RecycleBinController::class, 'recyclePurchase']);
  Route::get('recycle-item/{id}', [RecycleBinController::class, 'recycleItem']);
  Route::get('recycle-stock/{id}', [RecycleBinController::class, 'recycleStock']);
  Route::get('recyclebinPurchase', [RecycleBinController::class, 'recyclePurchases']);
  // Route::post('emptyRecycleSales', [RecycleBinController::class, 'emptysales']);


  Route::get('del-recy-sale/{id}', [RecycleBinController::class, 'delRecycleSale']);
  Route::get('del-recy-purchase/{id}', [RecycleBinController::class, 'delRecyclePurchase']);
  Route::get('del-recy-item/{id}', [RecycleBinController::class, 'delRecycleItem']);
  Route::get('del-recy-stock/{id}', [RecycleBinController::class, 'delRecycleStock']);
  Route::get('recycle-purchases', [RecycleBinController::class, 'recyclePurchases']);
  Route::get('del-recy-expense/{id}', [RecycleBinController::class, 'delRecycleExpense']);
  Route::get('recycle-expenses',  [RecycleBinController::class, 'recycleExpenses']);
  Route::post('recycle-expenses',  [RecycleBinController::class, 'recycleExpenses']);
  Route::get('recycle-expenses/{id}',  [RecycleBinController::class, 'recycleExpensesRestore']);
  


  Route::resource('shops', ShopController::class);
  Route::resource('bank-details', BankDetailController::class);
  Route::get('delete-bank/{id}', [BankDetailController::class, 'destroy']);
  Route::post('switch-shop', [ShopController::class, 'switchShop']);

  Route::get('notifications', [ShopController::class, 'notifications']);
  Route::get('all-notifications', [ShopController::class, 'notifications']);
  Route::get('mark-as-read', [ShopController::class, 'markAsRead']);

  // Users Management
  Route::resource('user-profile', ProfileController::class);
  Route::post('assign-business', [ProfileController::class, 'assignBusiness']);
  Route::post('detach-business', [ProfileController::class, 'detachBusiness']);
  Route::post('change-user-role', [ProfileController::class, 'assignUserRole']);
  Route::post('add-permission', [ProfileController::class, 'assignPermissions']);
  Route::post('remove-permission', [ProfileController::class, 'removePermissions']);
  Route::get('revoke-all-permissions-from-user/{id}', [ProfileController::class, 'revokeAll']);
  Route::get('delete-user/{id}', [ProfileController::class, 'destroy']);


  // Users Management
  // Route::resource('employees', 'UserController');
  // Route::post('add-user', 'Web\UserController@addUser');
  // Route::post('assign-business', 'Web\UserController@assignBusiness');
  // Route::post('detach-business', 'Web\UserController@detachBusiness');
  // Route::post('change-user-role', 'Web\UserController@assignUserRole');
  // Route::post('add-permission', 'Web\UserController@assignPermissions');
  // Route::post('remove-permission', 'Web\UserController@removePermissions');
  // Route::get('revoke-all-permissions-from-user/{id}', 'Web\UserController@revokeAll');
  // Route::get('delete-user/{id}', 'Web\UserController@destroy');


  //Sales routes
  // Route::group(['middleware' => 'auth', 'prefix' => 'salesman'], function(){
  Route::resource('pos', SaleController::class);
  Route::get('close', [SaleController::class, 'getSuccess']);
  Route::post('pt-pos', [SaleController::class, 'edit']);
  Route::get('sale-info/{id}', [SaleController::class, 'show']);
  // });

  Route::resource('an-sales', AnSaleController::class);
  Route::post('an-sales', [AnSaleController::class,  'index']);
  Route::get('cash-sales', [AnSaleController::class, 'cashSales']);
  Route::get('loaned-sales', [AnSaleController::class, 'loanedSales']);
  Route::post('create-sale', [AnSaleController::class, 'create']);
  Route::get('api/sale/{id}', [AnSaleController::class, 'show']);
  Route::get('issue-vfd/{id}', [AnSaleController::class, 'issueVFD']);
  Route::get('print-receipt/{id}', [AnSaleController::class, 'printReceipt']);
  Route::post('delete-multiple-sales', [AnSaleController::class, 'deleteMultiple']);
  Route::post('del-multiple-recycle-sales', [RecycleBinController::class, 'delMultipleRecycleSales']);
  Route::post('del-multiple-recycle-purchase', [RecycleBinController::class, 'delMultipleRecyclePurchases']);
  Route::post('del-multiple-recycle-expense', [RecycleBinController::class, 'delMultipleRecycleExpense']);
  Route::post('empty-recycle-sales', [RecycleBinController::class, 'emptyRecycleSales']);
  Route::post('empty-recycle-expenses', [RecycleBinController::class, 'emptyRecycleExpenses']);
  Route::post('empty-recycle-purchases', [RecycleBinController::class, 'emptyRecyclePurchases']);
  Route::post('delete-and-restore-multiple', [RecycleBinController::class, 'deleteAndRestoreMultipleSales']);


  
  
  Route::post('api/add-customer-to-session', [SaleItemTempController::class, 'selectedCustomer']);
  //SaleItems temp Routes
  Route::resource('api/item', ShopProductsApiController::class);
  Route::get('api/usebarcode', [ShopProductsApiController::class, 'useBarcode']);
  Route::post('api/add-item', [SaleItemTempController::class, 'ajaxPost']);
  Route::get('api/saletemp/{id}', [SaleItemTempController::class, 'index']);
  Route::resource('api/saletemp', SaleItemTempController::class);
  // Route::post('api/saletemp', [SaleItemTempController::class, 'store']);

  Route::resource('sale-items', AnSaleItemController::class);
  Route::post('add-saleitem', [AnSaleItemController::class, 'create']);
  Route::post('add-serviceitem', [AnSaleItemController::class, 'addItem']);
  Route::get('edit-item/{id}', [AnSaleItemController::class, 'edit']);
  Route::get('edit-sale-item/{id}', [AnSaleItemController::class, 'editItem']);
  Route::post('update-item', [AnSaleItemController::class, 'update']);
  Route::get('delete-item/{id}', [AnSaleItemController::class, 'destroy']);
  //Saleitems Temp Routes End

  //ServiceSaleItems temp Routes
  Route::resource('api/servitem', ShopServiceApiController::class);
  Route::get('api/servsaletemp/{id}', [ServiceSaleItemTempController::class, 'index']);
  Route::resource('api/servsaletemp', ServiceSaleItemTempController::class);
  Route::resource('service-items', ServiceSaleItemController::class);
  Route::get('delete-serviceitem/{id}', [ServiceSaleItemController::class, 'destroy']);
  //ServiceSaleitems Temp Routes End

  Route::resource('invoices', InvoiceController::class);
  Route::post('f-invoices', [InvoiceController::class,  'index']);
  Route::post('delete-multiple-invoices', [InvoiceController::class, 'deleteMultiple']);

  Route::get('customer-accounts', [InvoiceController::class, 'customerAccounts']);
  Route::get('customer-account-stmt/{id}', [InvoiceController::class, 'accountStmt']);
  Route::post('customer-account-stmt/{id}', [InvoiceController::class, 'accountStmt']);
  Route::post('acc-payments', [InvoiceController::class, 'accPayments']);
  Route::post('set-ob', [InvoiceController::class, 'setOpeningBalance']);
  Route::get('del-acc-payment/{receipt_no}', [InvoiceController::class, 'deletePayment']);
  Route::get('del-acc-inv/{id}', [InvoiceController::class, 'deleteTrans']);
  Route::get('show-receipt/{id}', [InvoiceController::class, 'showReceipt']);
  Route::post('change-discount', [InvoiceController::class, 'changeDiscount']);

  Route::get('create-an-invoice/{id}', [InvoiceController::class, 'create']);
  Route::get('delete-invoice/{id}', [InvoiceController::class, 'destroy']);
  Route::get('create-credit-note/{id}', [CreditNoteController::class, 'create']);
  Route::get('create-sale-return/{id}', [SaleReturnController::class, 'create']);
  Route::resource('sales-returns', SaleReturnController::class);
  Route::post('sale-returns', [SaleReturnController::class, 'index']);
  Route::get('delete-sale-return/{id}', [SaleReturnController::class, 'destroy']);
  Route::resource('sale-return-items', SaleReturnItemController::class);
  Route::get('delete-sale-return-item/{id}', [SaleReturnItemController::class, 'destroy']);

  Route::resource('credit-notes', CreditNoteController::class);
  Route::get('cancel-credit-note/{id}', [CreditNoteController::class, 'destroy']);
  Route::resource('credit-note-items', CreditNoteItemController::class);
  Route::get('credit-note-items/destroy/{id}', [CreditNoteItemController::class, 'destroy']);

  Route::resource('pro-invoices', ProInvoiceController::class);
  Route::get('pro-invoices/destroy/{id}', [ProInvoiceController::class, 'destroy']);
  Route::get('cancel-invoice', [ProInvoiceController::class, 'cancel']);
  Route::get('create-invoice/{id}', [ProInvoiceController::class, 'finalize']);
  Route::post('update-customer', [ProInvoiceController::class, 'updateCustomer']);
  Route::post('change-customer', [ProInvoiceController::class, 'changeCustomer']);
  Route::post('add-invoice-item', [ProInvoiceController::class, 'addItem']);
  Route::post('add-invocie-servitem', [ProInvoiceController::class, 'addServiceItem']);
  Route::post('update-invoice-item', [ProInvoiceController::class, 'updateInvoiceItem']);
  Route::get('delete-invoice-item/{id}', [ProInvoiceController::class, 'deleteItem']);
  Route::get('delete-invoice-servitem/{id}', [ProInvoiceController::class, 'deleteServiceItem']);
  Route::get('cancel-profoma/{id}', [ProInvoiceController::class, 'cancelProfoma']);
  Route::get('resume-profoma/{id}', [ProInvoiceController::class, 'resumeProfoma']);
  Route::get('invoice-payments/{id}', [InvoicePaymentController::class, 'index']);

  Route::resource('inv-payments', InvoicePaymentController::class);
  Route::get('inv-payments/destroy/{id}', [InvoicePaymentController::class, 'destroy']);
  //ProInvoice Items temp Routes
  Route::resource('api/invoiceitem', ShopProductsApiController::class);
  Route::post('api/add-invoiceitem', [InvoiceItemTempController::class, 'ajaxPost']);
  Route::resource('api/invoicetemp', InvoiceItemTempController::class);

  Route::post('cp-orders', [ProInvoiceController::class, 'cpOrders']);
  Route::get('pending-orders/{id}', [ProInvoiceController::class, 'pendingOrders']);
  //ServiceInvoiceItems temp Routes
  Route::resource('api/invoice-servitem', ShopServiceApiController::class);
  Route::resource('api/servinvoicetemp', ServiceInvoiceItemTempController::class);

  Route::get('invoice-report', [InvoiceController::class, 'invoiceReport']);
  Route::post('invoice-reports', [InvoiceController::class, 'invoiceReport']);
  Route::get('aging-report', [InvoiceController::class, 'agingReport']);

  //Delivery Note
  Route::get('create-dnote/{id}', [DeliveryNoteController::class, 'create']);
  Route::resource('delivery-notes', DeliveryNoteController::class);

  Route::get('customer-account-stmt-std/{id}', [AnSaleController::class, 'accountStmt']);
  Route::post('customer-account-stmt-std/{id}', [AnSaleController::class, 'accountStmt']);
  Route::post('set-cust-ob', [AnSaleController::class, 'setOpeningBalance']);
  Route::post('update-cust-ob', [AnSaleController::class, 'updateOB']);
  Route::get('del-custacc-payment/{receipt_no}', [AnSaleController::class, 'deletePayment']);
  Route::get('show-cust-receipt/{receipt_no}', [AnSaleController::class, 'showReceipt']);
  Route::post('sale-acc-payments', [AnSaleController::class, 'accPayments']);

  Route::resource('sale-payments', SalePaymentController::class);
  Route::get('sale-payments/destroy/{id}', [SalePaymentController::class, 'destroy']);
  //Sales routes End

  //Product Categories Routes
  Route::resource('categories', CategoryController::class);
  Route::get('categories/destroy/{id}', [CategoryController::class, 'destroy']);
  Route::post('delete-multiple-categories', [CategoryController::class, 'deleteMultiple']);
  Route::get('category-products/{id}', [CategoryController::class, 'categoryProducts']);
  Route::post('add-product', [CategoryController::class, 'addProductToCategory']);
  Route::post('remove-product', [CategoryController::class, 'removeProductFromCategory']);
  Route::get('remove-all-prods-from-category/{id}', [CategoryController::class, 'removeAll']);

  //Products Routes
  Route::post('filter-products', [ProductsController::class, 'index']);
  Route::resource('products', ProductsController::class);
  Route::post('products/getShopProducts', [ProductsController::class, 'getShopProducts'])->name('products.getShopProducts');
  Route::get('products-with-vat', [ProductsController::class, 'productsWithVAT']);
  Route::get('products-without-vat', [ProductsController::class, 'productsWithoutVAT']);
  Route::resource('product-units', ProductUnitController::class);
  Route::get('new-product', [ProductsController::class, 'create']);
  Route::get('excel-sample', [ProductsController::class, 'download']);
  Route::post('import-product', [ProductsController::class, 'import']);
  Route::get('export-product', [ProductsController::class, 'export']);
  Route::post('delete-multiple-products', [ProductsController::class, 'deleteMultiple']);
  Route::get('product-details/{id}', [ProductsController::class, 'view']);
  Route::get('set-actual-prices/{id}', [ProductsController::class, 'setActualPrices']);
  Route::get('new-price/{id}', [ProductsController::class, 'newPrice']);
  Route::post('new-sell-price', [ProductsController::class, 'postPrice']);
  Route::post('new-buy-price', [ProductsController::class, 'newBuyPrice']);
  Route::post('new-reorder-point', [ProductsController::class, 'newReorderPoint']);
  Route::post('new-location', [ProductsController::class, 'changeLocation']);
  Route::get('price-list', [ProductsController::class, 'priceList']);

  Route::resource('transfer-orders', TransferOrderController::class);
  Route::get('cancel-order', [TransferOrderController::class, 'cancelOrder']);
  Route::get('delete-order/{id}', [TransferOrderController::class, 'destroy']);
  Route::resource('api/ordertemp', TransferOrderItemTempController::class);
  Route::post('update-transorder-item', [TransferOrderController::class, 'updateTransorderItem']);
  Route::get('delete-transorder-item/{id}', [TransferOrderController::class, 'deleteTransorderItem']);

  Route::resource('transformation-transfer', TransformationTransferItemController::class);
  Route::resource('transformation-transfer-temp', TransformationTransferItemTempController::class);
  Route::post('api/destin_produts', [TransformationTransferItemTempController::class, 'destinProducts']);

  //Products Routes end

  //Supplier and Purchases Routes

  //Suppliers
  Route::resource('suppliers', SupplierController::class);
  Route::get('edit-supplier/{id}', [SupplierController::class, 'edit']);
  Route::post('update-supplier', [SupplierController::class, 'update']);
  Route::get('delete-supplier/{id}', [SupplierController::class, 'destroy']);

  // Purchase Orders
  Route::resource('purchase-orders', PurchaseOrderController::class);
  Route::get('cancel-porder', [PurchaseOrderController::class, 'cancelPorder']);
  Route::post('delete-multiple-porders', [PurchaseOrderController::class, 'deleteMultiple']);
  Route::resource('purchase-orders/api/item', ShopProductsApiController::class);
  Route::get('purchase-orders/api/usebarcode', [ShopProductsApiController::class, 'useBarcode']);
  Route::resource('purchase-orders/api/pordertemp', PurchaseOrderTempApiController::class);
  Route::post('purchase-orders/api/add-poitem-temp', [PurchaseOrderTempApiController::class, 'ajaxPost']);
  Route::get('poitems/{id}', [PurchaseOrderItemController::class, 'index']);
  Route::resource('poitems', PurchaseOrderItemController::class);
  Route::post('add-purchase-order-item', [PurchaseOrderItemController::class, 'create']);
  Route::get('create-purchase/{id}', [PurchaseOrderController::class, 'createPurchase']);
  Route::get('purchases/api/usebarcode', [ShopProductsApiController::class, 'useBarcode']);

  //Purchases
  Route::resource('purchases/api/item', ShopProductsApiController::class);
  Route::get('purchases/api/stocktemp/{id}', [StockItemTempApiController::class, 'index']);
  Route::resource('purchases/api/stocktemp', StockItemTempApiController::class);
  Route::post('api/add-item-temp', [StockItemTempApiController::class, 'ajaxPost']);
  Route::post('purchases/api/update-purchase-temp', [StockItemTempApiController::class, 'updatePurchaseTemp']);
  Route::post('purchases/pt-purchase', [PurchasesController::class, 'pendingPurchase']);
  Route::get('cancel-purchase/{id}', [StockItemTempApiController::class, 'cancelPurchase']);
  Route::resource('purchases', PurchasesController::class);
  Route::get('purchase-items/{id}', [PurchasesController::class, 'purchaseItems']);
  Route::post('add-purchase-item', [PurchasesController::class, 'addItem']);
  Route::post('delete-multiple-purchases', [PurchasesController::class, 'deleteMultiple']);
  Route::get('purchase-aging-report', [PurchasesController::class, 'agingReport']);
  Route::post('set-supp-ob', [PurchasesController::class, 'setOpeningBalance']);
  Route::post('update-adjustment', [PurchasesController::class, 'updateAdjustment']);
  Route::resource('purchase-payments', PurchasePaymentController::class);
  Route::post('supplier-acc-payments', [PurchasePaymentController::class, 'accPayments']);
  Route::get('show-voucher/{pv_no}', [PurchasePaymentController::class, 'showVoucher']);
  Route::get('del-supp-trans/{id}', [PurchasesController::class, 'deleteTrans']);
  Route::get('del-acc-pv/{pv_no}', [PurchasePaymentController::class, 'deletePayment']);
  Route::resource('stocks', StockController::class);
  // Stocks Routes End

  // Damage Reoutes
  Route::resource('damages', ProdDamageController::class);
  // Damages routes End  

  // Costs Routes
  Route::resource('expense-categories', ExpenseCategoryController::class);
  Route::resource('expenses', ExpenseController::class);
  Route::get('exp-suppliers', [ExpenseController::class, 'ExpSuppliers']);
  Route::post('filter-expenses', [ExpenseController::class, 'index']);
  Route::post('store-expenses', [ExpenseController::class, 'storeExpense']);
  Route::get('exp-aging-report', [ExpenseController::class, 'agingReport']);
  Route::get('cancel-expense', [ExpenseController::class, 'cancel']);
  Route::post('delete-multiple-expenses', [ExpenseController::class, 'deleteMultiple']);
  Route::get('expense-account-stmt/{id}', [ExpenseController::class, 'accountStmt']);
  Route::post('expense-account-stmt/{id}', [ExpenseController::class, 'accountStmt']);

  Route::get('api/expenses', [ExpenseTempController::class, 'create']);
  Route::resource('api/expensetemp', ExpenseTempController::class);
  Route::get('expenses/api/expenses', [ExpenseTempController::class, 'create']);
  Route::resource('expenses/api/expensetemp', ExpenseTempController::class);
  Route::resource('expense-payments', ExpensePaymentController::class);
  Route::get('expense-payments/show-cn/{id}', [ExpensePaymentController::class, 'showCreditNote']);
  Route::get('expense-payments/delete-cn/{id}', [ExpensePaymentController::class, 'deleteCN']);
  Route::post('expense-payments/setOpeningBalance', [ExpensePaymentController::class, 'setOpeningBalance']);
  Route::post('expense-payments/update-adjustment', [ExpensePaymentController::class, 'updateAdjustment']);
  Route::get('expense-payments/inv-expenses/{id}', [ExpensePaymentController::class, 'invExpenses']);
  Route::get('expense-payments/delete-trans/{id}', [ExpensePaymentController::class, 'deleteTrans']);
  Route::post('expense-payments/acc-payments', [ExpensePaymentController::class, 'accPayments']);
  Route::get('expense-payments/del-payment/{pv_no}', [ExpensePaymentController::class, 'deletePayment']);

  // Route::get('costs', [ExpenseController::class, 'index']);
  // Route::post('pay-expense', [ExpenseController::class, 'payExpense']);
  // Route::post('costs', [ExpenseController::class, 'index']);
  // Route::post('create-cost', [ExpenseController::class, 'create']);
  // Route::post('add-expenses', [ExpenseController::class, 'addExpenses']);
  // Route::get('show-cost/{id}', [ExpenseController::class, 'show']);
  // Route::get('edit-cost/{id}', [ExpenseController::class, 'edit']);
  // Route::post('update-cost', [ExpenseController::class, 'update']);
  // Route::get('delete-cost/{id}', [ExpenseController::class, 'delete']);
  // Route::post('preview-cost', [ExpenseController::class, 'costsByPeriod']);
  // Route::post('total-cost', [ExpenseController::class, 'totalCost']);
  // Route::post('total-cost-period', [ExpenseController::class, 'totalCostByPeriod']);
  // Route::get('exp-voucher/{id}', [ExpenseController::class, 'previewVoucher']);
  // Route::get('exp-voucher1/{id}', [ExpenseController::class, 'previewVoucher1']);

  // Costs routes End

  // Customers Routes
  Route::get('excel-sample-customers', [CustomerController::class, 'download']);
  Route::post('import-customer', [CustomerController::class, 'import']);
  Route::resource('customers', CustomerController::class);
  Route::post('new-customer', [CustomerController::class, 'createNew']);
  // Route::get('edit-customer/{id}', [CustomerController::class, 'edit']);
  Route::get('delete-customer/{id}', [CustomerController::class, 'destroy']);
  Route::post('delete-multiple-customers', [CustomerController::class, 'deleteMultiple']);

  Route::resource('sms-notifications', SmsTemplateController::class);
  Route::post('sms-dynamic', [SmsTemplateController::class, 'dynamic']);
  Route::get('send-sms/{id}', [SmsTemplateController::class, 'show']);
  Route::get('sms-notifications/destroy/{id}', [SmsTemplateController::class, 'destroy']);

  Route::get('sms-settings', [SmsTemplateController::class, 'getSetting']);
  Route::post('sms-settings', [SmsTemplateController::class, 'settings']);
  // Customers Routes End

  Route::post('filter-cash-flows', [CashOutController::class, 'index']);
  Route::resource('cash-flows', CashOutController::class);
  Route::post('cash-flows.index', [CashOutController::class, 'index']);
  Route::get('delete-cout/{id}', [CashOutController::class, 'destroy']);
  Route::resource('cash-ins', CashInController::class);
  Route::get('delete-cash-in/{id}', [CashInController::class, 'destroy']);

  Route::resource('acc-transactions', AccountTransController::class);
  Route::get('delete-acc-trans/{id}', [AccountTransController::class, 'destroy']);

  Route::get('collections-report', [ReportsController::class, 'collectionsReport']);
  Route::post('collections-report', [ReportsController::class, 'collectionsReport']);
  Route::get('business-value', [FinancialReportsController::class, 'BusinessValue']);
  Route::post('business-value', [FinancialReportsController::class, 'BusinessValue']);
  Route::get('cash-flow-statement', [FinancialReportsController::class, 'CashFlowStatement']);
  Route::post('cash-flow-statement', [FinancialReportsController::class, 'CashFlowStatement']);
  Route::get('daily-cash-flow-statement', [FinancialReportsController::class, 'DailyCashFlowStatement']);
  Route::post('daily-cash-flow-statement', [FinancialReportsController::class, 'DailyCashFlowStatement']);
  Route::get('income-statement', [FinancialReportsController::class, 'IncomeStatement']);
  Route::post('income-statement', [FinancialReportsController::class, 'IncomeStatement']);
  Route::get('closing-business-value', [FinancialReportsController::class, 'MonthyClosingBusinessValue']);
  Route::post('closing-business-value', [FinancialReportsController::class, 'MonthyClosingBusinessValue']);
  Route::get('open-closing-amount-statement', [FinancialReportsController::class, 'OpenClosingAmoutStatement']);
  Route::post('open-closing-amount-statement', [FinancialReportsController::class, 'OpenClosingAmoutStatement']);

  //Reports
  Route::get('reports', [ReportsController::class, 'index']);
  Route::post('reports', [ReportsController::class, 'index']);
  Route::post('reports-by-date', [ReportsController::class, 'reportsByDate']);
  Route::get('sales-report', [ReportsController::class, 'sales']);
  Route::post('sales-report', [ReportsController::class, 'sales']);
  Route::get('sales-return-report', [ReportsController::class, 'salesReturns']);
  Route::post('sales-return-report', [ReportsController::class, 'salesReturns']);
  Route::get('debts-report', [ReportsController::class, 'debts']);
  Route::post('debts-report', [ReportsController::class, 'debts']);
  Route::get('expenses-report', [ReportsController::class, 'expenses']);
  Route::post('expenses-report', [ReportsController::class, 'expenses']);
  Route::get('single-expense-report/{type}', [ReportsController::class, 'singleExpenseReport']);
  Route::post('single-expense-report/{type}', [ReportsController::class, 'singleExpenseReport']);
  Route::get('profits', [ReportsController::class, 'profitReports']);
  Route::post('profits', [ReportsController::class, 'profitReports']);
  Route::get('sales-by-product', [ReportsController::class, 'salesByProduct']);
  Route::post('sales-by-product', [ReportsController::class, 'salesByProduct']);
  Route::get('dreport-summary', [ReportsController::class,  'summaryReport']);
  Route::post('dreport-summary', [ReportsController::class,  'summaryReport']);


  // Stock Reports
  Route::get('stock-reports', [StockReportController::class, 'index']);
  Route::get('reorder-reports', [StockReportController::class, 'reorderReports']);
  Route::post('stock-reports', [StockReportController::class, 'index']);
  Route::post('stock-taking', [StockReportController::class, 'stockTaking']);
  Route::get('stock-taking', [StockReportController::class, 'stockTaking']);
  Route::get('stock-capital', [StockReportController::class, 'stockCapital']);
  Route::get('stock-expires', [StockReportController::class, 'stockExpires']);
  Route::post('stock-expires', [StockReportController::class, 'stockExpires']);
  Route::get('transfer-report', [StockReportController::class, 'transfers']);
  Route::post('transfer-report', [StockReportController::class, 'transfers']);
  Route::post('stock-report-date', [StockReportController::class, 'reportsByDateRange']);
  Route::get('total-report', [ReportsController::class, 'totalAmounts']);
  Route::post('total-report', [ReportsController::class, 'totalAmounts']);
  Route::get('print-report', [ReportsController::class, 'printReport']);

  Route::get('serv-total-report', [ReportsController::class, 'totalAmounts']);
  Route::post('serv-total-report', [ReportsController::class, 'totalAmounts']);

  Route::get('consolidated', [ReportsController::class, 'consolidated']);
  Route::post('consolidated', [ReportsController::class, 'consolidated']);
  //Charts
  Route::get('charts', [ChartsController::class, 'index']);
  Route::post('charts', [ChartsController::class, 'index']);

  // Service Business Routes
  Route::resource('services', ServiceController::class);
  Route::get('services/destroy/{id}', [ServiceController::class, 'destroy']);


  Route::resource('devices', DeviceController::class);
  Route::get('devices/destroy/{id}', [DeviceController::class, 'destroy']);

  Route::resource('grades', GradeController::class);
  Route::get('grades/destroy/{id}', [GradeController::class, 'destroy']);

  Route::get('serv-reports', [ReportsController::class, 'index']);
  Route::post('serv-reports', [ReportsController::class, 'index']);

  Route::get('serv-sales-report', [ReportsController::class, 'sales']);
  Route::post('serv-sales-report', [ReportsController::class, 'sales']);
  Route::get('serv-debts-report', [ReportsController::class, 'debts']);
  Route::post('serv-debts-report', [ReportsController::class, 'debts']);
  Route::get('serv-expenses-report', [ReportsController::class, 'expenses']);
  Route::post('serv-expenses-report', [ReportsController::class, 'expenses']);
  Route::get('sales-by-service', [ReportsController::class, 'salesByService']);
  Route::post('sales-by-service', [ReportsController::class, 'salesByService']);

  Route::get('both-reports', [ReportsController::class, 'index']);
  Route::post('both-reports', [ReportsController::class, 'index']);


  //Agent OCamount routes
  Route::resource('ocamounts', OCamountController::class);
  Route::post('oc-amounts', [OCAmountController::class, 'index']);
  Route::get('delete-ocamount/{id}', [OCAmountController::class, 'destroy']);
  Route::post('delete-multiple-ocamounts', [OCAmountController::class, 'deleteMultiple']);

  // Production Routes
  Route::get('prod-home', [App\Http\Controllers\Prod\HomeController::class, 'index']);
  Route::post('prod-home', [App\Http\Controllers\Prod\HomeController::class, 'index']);

  //Raw Materials
  Route::resource('raw-materials', App\Http\Controllers\Prod\RawMaterialController::class);
  Route::post('rm-new-buy-price', [RawMaterialController::class, 'newBuyPrice']);
  Route::post('new-rm-reorder-point', [App\Http\Controllers\Prod\RawMaterialController::class, 'newReorderPoint']);
  Route::post('delete-multiple-materials', [RawMaterialController::class, 'deleteMultiple']);
  Route::resource('rm-purchases', App\Http\Controllers\Prod\RmPurchaseController::class);
  Route::get('cancel-rmitem', [App\Http\Controllers\Prod\RmPurchaseController::class, 'cancel']);
  Route::resource('rm-items', App\Http\Controllers\Prod\RmItemController::class);
  Route::get('rm-purchases-grn/{id}', [App\Http\Controllers\Prod\RmPurchaseController::class, 'purchaseGRN'])->name('rm-purchase-grn');
  Route::resource('rm-purchases/api/rmitemtemp', App\Http\Controllers\Prod\RmPurchaseItemApiController::class);
  Route::get('rm-purchases/api/rmitem', [App\Http\Controllers\Prod\RawMaterialApiController::class, 'index']);
  Route::put('rm-purchases/rm-temp/{id}', [App\Http\Controllers\Prod\RmPurchaseController::class, 'updateTemp']);
  Route::resource('rm-purchase-payments', App\Http\Controllers\Prod\RmPurchasePaymentController::class);


  Route::post('rm-suppliers-transaction/show', [App\Http\Controllers\Prod\RmSupplierTransactionController::class, 'show']);
  Route::post('pm-suppliers-transaction/show', [App\Http\Controllers\Prod\PmSupplierTransactionController::class, 'show']);

  Route::resource('rm-suppliers-transaction', App\Http\Controllers\Prod\RmSupplierTransactionController::class);
  Route::get('del-rm-acc-pv/{id}', [RmSupplierTransactionController::class, 'deletePayment']);
  Route::resource('pm-suppliers-transaction', App\Http\Controllers\Prod\PmSupplierTransactionController::class);
  Route::get('del-pm-acc-pv/{id}', [PmSupplierTransactionController::class, 'deletePayment']);

  Route::resource('rm-uses', App\Http\Controllers\Prod\RmUseController::class);
  Route::post('rm-uses-store', [RmUseController::class, 'storeProduct']);
  Route::get('delete-rmuse/{id}', [App\Http\Controllers\Prod\RmUseController::class, 'destroy']);
  Route::resource('rm-uses/api/rmitem', App\Http\Controllers\Prod\RawMaterialApiController::class);
  Route::resource('rm-uses/api/rmusedtemp', App\Http\Controllers\Prod\RmUseItemTempController::class);
  Route::resource('rm-used-items', App\Http\Controllers\Prod\RmUsedItemController::class);

  Route::resource('rm-damages', App\Http\Controllers\Prod\RmDamageController::class);



  Route::post('rm-purchase-payments/accPayments', [RmPurchasePaymentController::class, 'accPayments']);
  Route::post('rm-purchase-payments/update-adjustment', [RmPurchasePaymentController::class, 'updateAdjustment']);
  Route::post('rm-purchase-payments/setOpeningBalance', [RmPurchasePaymentController::class, 'setOpeningBalance']);
  Route::get('rm-purchase-payments/show-voucher/{pv_no}', [RmPurchasePaymentController::class, 'showVoucher']);
  Route::get('rm-purchase-payments/previewVoucher', [RmPurchasePaymentController::class, 'previewVoucher']);
  Route::get('rm-purchase-payments/delete-supp-trans/{id}', [RmPurchasePaymentController::class, 'deleteTrans']);


  //Packing Materials
  Route::resource('packing-materials', App\Http\Controllers\Prod\PackingMaterialController::class);
  Route::post('pm-new-buy-price', [PackingMaterialController::class, 'newBuyPrice']);
  Route::resource('pm-purchases', App\Http\Controllers\Prod\PmPurchaseController::class);
  Route::resource('pm-purchases/api/pmitem', App\Http\Controllers\Prod\PackingMaterialApiController::class);
  Route::resource('pm-purchases/api/pmitemtemp', App\Http\Controllers\Prod\PmPurchaseItemApiController::class);
  Route::put('pm-purchases/pm-temp/{id}', [App\Http\Controllers\Prod\PmPurchaseController::class, 'updateTemp']);
  Route::get('cancel-pmitem', [App\Http\Controllers\Prod\PmPurchaseController::class, 'cancel']);
  Route::get('pm-purchase-items/{id}', [App\Http\Controllers\Prod\PmPurchaseController::class, 'purchaseItems']);
  Route::get('delete-pmpurchase/{id}', [App\Http\Controllers\Prod\PmPurchaseController::class, 'destroy']);
  Route::resource('pm-purchase-payments', App\Http\Controllers\Prod\PmPurchasePaymentController::class);
  Route::resource('pm-items', App\Http\Controllers\Prod\PmItemController::class);

  Route::resource('pm-uses', App\Http\Controllers\Prod\PmUseController::class);
  Route::resource('pm-uses/api/pmitem', App\Http\Controllers\Prod\PackingMaterialApiController::class);
  Route::resource('pm-uses/api/pmusedtemp', App\Http\Controllers\Prod\PmUseItemTempController::class);
  Route::resource('pm-used-items', App\Http\Controllers\Prod\PmUsedItemController::class);
  Route::post('pm-uses/api/saveprodtemp', [App\Http\Controllers\Prod\PmUseItemTempController::class, 'saveProdTemp']);


  Route::resource('pm-damages', App\Http\Controllers\Prod\PmDamageController::class);
  Route::get('pm-damages', [App\Http\Controllers\Prod\PmDamageController::class, 'store']);
  Route::get('delete-pmdamage/{id}', [App\Http\Controllers\Prod\PmDamageController::class, 'destroy']);

  Route::post('new-pm-reorder-point', [App\Http\Controllers\Prod\PackingMaterialController::class, 'newReorderPoint']);
  Route::get('pm-supplier-account-stmt/{id}', [App\Http\Controllers\Prod\PmPurchaseController::class, 'accountStmt']);
  Route::post('pm-supplier-account-stmt/{id}', [App\Http\Controllers\Prod\PmPurchaseController::class, 'accountStmt']);
  Route::post('pm-purchase-payments/accPayments', [PmPurchasePaymentController::class, 'accPayments']);
  Route::post('pm-purchase-payments/update-adjustment', [PmPurchasePaymentController::class, 'updateAdjustment']);
  Route::post('pm-purchase-payments/setOpeningBalance', [PmPurchasePaymentController::class, 'setOpeningBalance']);
  Route::get('pm-purchase-payments/show-voucher/{pv_no}', [PmPurchasePaymentController::class, 'showVoucher']);
  Route::get('pm-purchase-payments/previewVoucher', [PmPurchasePaymentController::class, 'previewVoucher']);
  Route::get('pm-purchase-payments/delete-supp-trans/{id}', [PmPurchasePaymentController::class, 'deleteTrans']);





  //MRO Items
  Route::resource('mro', MROController::class);
  Route::resource('mro-items', MROItemController::class);
  Route::post('mro-items', [MROItemController::class, 'index']);
  Route::resource('mro-used-items', MROItemController::class);
  Route::resource('mro-uses', MroUseController::class);
  Route::resource('mro-uses/api/mrousedtemp', MroUsedItemTempController::class);
  Route::resource('mro-uses/api/mro-items', MroApiController::class);

  //Production cost
  // Route::post('prod-costs' , [App\Http\Controllers\Prod\ProductionCostController::class, 'index']);
  Route::resource('prod-costs', ProductionCostController::class);
  Route::post('prod-costs/api/prod-items/create', [ProductionApiController::class, 'create']);
  Route::get('prod-costs/api/product-made', [ProductionApiController::class, 'product_made']);
  Route::get('prod-costs/api/prod-items/recalculate', [ProductionApiController::class, 'recalculate']);
  Route::resource('prod-costs/api/prod-items', ProductionApiController::class);
  Route::resource('prod-costs/api/mrousedtemp', MroUsedItemTempController::class);
  Route::resource('prod-costs/api/rmusedtemp', RmUseItemTempController::class);
  Route::resource('prod-costs/api/pmusedtemp', PmUseItemTempController::class);

  Route::post('prod-costs/savepanel', [ProductionCostController::class, 'savePanel']);

  Route::post('production/createOld', [ProductionCostController::class, 'createold']);
  Route::get('production/createOld', [ProductionCostController::class, 'createold']);

  //Production Reports
  Route::get('pm-purchases-report', [App\Http\Controllers\Prod\ReportsController::class, 'PmPurchases']);
  Route::get('rm-purchases-report', [App\Http\Controllers\Prod\ReportsController::class, 'PmPurchases']);
  Route::post('pm-purchases-report', [App\Http\Controllers\Prod\ReportsController::class, 'PmPurchases']);
  Route::post('rm-purchases-report', [App\Http\Controllers\Prod\ReportsController::class, 'RmPurchases']);
  Route::get('prod-stock-status-report', [App\Http\Controllers\Prod\ReportsController::class, 'StockStatus']);
  Route::get('rm-uses-report', [App\Http\Controllers\Prod\ReportsController::class, 'RmUsesReport']);
  Route::post('rm-uses-report', [App\Http\Controllers\Prod\ReportsController::class, 'RmUsesReport']);
  Route::get('pm-uses-report', [App\Http\Controllers\Prod\ReportsController::class, 'PmUsesReport']);
  Route::post('pm-uses-report', [App\Http\Controllers\Prod\ReportsController::class, 'PmUsesReport']);
  Route::get('general-report', [App\Http\Controllers\Prod\ReportsController::class, 'generalReport']);
  Route::post('general-report', [App\Http\Controllers\Prod\ReportsController::class, 'generalReport']);


  //Production transfer
  Route::get('prod-transfer-to/{id}', [App\Http\Controllers\Prod\ProdTransferController::class, 'index'])->name('prod-transfer-to');
  Route::post('prod-transfer-store', [App\Http\Controllers\Prod\ProdTransferController::class, 'store'])->name('prod-transfer-store');


  //VFD
  Route::resource('vfd-reg-infos', RegInfoController::class);
  Route::get('send-reg-info/{id}', [RegInfoController::class, 'sendRegInfo']);
  Route::resource('vfd-rct-infos', RctInfoController::class);
  Route::get('submit-receipt/{id}', [RctInfoController::class, 'submitReceipt']);
  Route::resource('vfd-zreports', ZReportController::class);
});


Route::post('efdms-reg-ack-infos', [ApiTestController::class, 'store']);
Route::post('efdms-rct-ack-infos', [ApiTestController::class, 'storeRctAck']);
Route::post('efdms-zreport-ack-infos', [ApiTestController::class, 'storeZReportAck']);
//Auto search
Route::get('/autocomplete-search', [RecycleBinController::class, 'autocompleteSearch']);
