<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" ng-app="smartpos">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!--favicon-->
    <link rel="icon" href="{{ asset('assets/images/icon.png') }}" type="image/png" />
    <!--plugins-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/notifications/css/lobibox.min.css') }}" />
    <link href="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2-bootstrap4.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/css/intlTelInput.css" />
    <link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/highcharts/css/highcharts.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}"
        rel="stylesheet" />
    <!-- loader-->
    <link href="{{ asset('assets/css/pace.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/js/pace.min.js') }}"></script>
    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <!-- Daterange Picker -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.all.js') }}"></script>
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
    <!-- Theme Style CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/dark-theme.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/semi-dark.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/header-colors.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/invoices/blue.css') }}">
    @livewireStyles
    <style>
        .searchitype {
            max-width: 600px;
        }
    </style>
    <style type="text/css">
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        /*----------------------- Preloader -----------------------*/
        .preloader-wrapper {
            height: 100%;
            width: 100%;
            background: transparent; 
            /* background: #f4f4f4;  */
            position: absolute;
            z-index: 9999999;
        }

        @media (max-width: 767px) {
            .breadcrumb {
                display: none;
            }
        }

        .preloader-wrapper .preloader {
            position: absolute;
            top: 40%;
            left: 40%;
            -webkit-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            width: 120px;
        }
    </style>
    <title>SmartMauzo - {{ $page }}</title>
</head>

<?php
$shop = App\Models\Shop::find(Session::get('shop_id'));
$settings = App\Models\Setting::where('shop_id', $shop->id)->first();
?>

<body>
    <!--wrapper-->
    <div class="wrapper">
        <!--sidebar wrapper -->
        <div class="sidebar-wrapper" data-simplebar="true">
            <div class="sidebar-header">
                <div>
                    <img src="{{ asset('assets/images/icon.png') }}" class="logo-icon" alt="logo icon">
                </div>
                <div>
                    <h4 class="logo-text">SmartMauzo</h4>
                </div>
                <div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i>
                </div>
            </div>
            <!--navigation-->
            <ul class="metismenu" id="menu">
                @if (Auth::user()->hasRole('manager'))
                    <li>
                        <a href="{{ url('home') }}" class="has-arrow">
                            <div class="parent-icon"><i class='bx bx-home-circle'></i>
                            </div>
                            <div class="menu-title">Dashboard</div>
                        </a>
                    </li>
                @endif
                @if ($shop->subscription_type_id >= 2)

                    @if (Auth::user()->hasRole('manager'))
                        <li>
                            <a href="{{ url('prod-home') }}" class="has-arrow">
                                <div class="parent-icon"><i class='bx bx-package'></i>
                                </div>
                                <div class="menu-title">Production</div>
                            </a>
                        </li>
                    @endif
                @endif
                @if (Auth::user()->hasRole('manager') || Auth::user()->hasRole('salesman') || Auth::user()->can('create-sales'))
                    {{-- @if (Session::get('role') == 'manager' || Session::get('role') == 'salesman') --}}
                    @if (Auth::user()->hasRole('manager'))
                        <li class="menu-label">{{ trans('navmenu.sales') }}</li>
                    @endif
                    <li>
                        <a href="{{ url('pos') }}">
                            <div class="parent-icon"><i class='bx bx-calculator'></i>
                            </div>
                            <div class="menu-title">POS</div>
                        </a>
                    </li>
                @endif
                @if (Auth::user()->hasRole('manager') ||
                        Auth::user()->can('view-sales') ||
                        Auth::user()->can('delete-sales') ||
                        Auth::user()->can('edit-sales'))
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class='lni lni-dollar'></i>
                            </div>
                            <div class="menu-title">{{ trans('navmenu.sales') }}</div>
                        </a>
                        <ul>
                            <li> <a href="{{ url('an-sales') }}"><i
                                        class="bx bx-right-arrow-alt"></i>{{ trans('navmenu.sales_list') }}</a></li>
                            <li> <a href="{{ url('sales-returns') }}"><i
                                        class="bx bx-right-arrow-alt"></i>{{ trans('navmenu.sales_returns') }}</a></li>
                            <li><a href="{{ url('customers') }}"><i class="bx bx-right-arrow-alt"></i>
                                    {{ trans('navmenu.customers') }}</a></li>
                        </ul>
                    </li>
                @endif

                @if ($shop->subscription_type_id >= 2)
                    @if (Auth::user()->hasRole('manager') || Auth::user()->hasRole('salesman') || Auth::user()->can('manage-invoice'))
                        <li>
                            <a class="has-arrow" href="javascript:;">
                                <div class="parent-icon"><i class='lni lni-files'></i>
                                </div>
                                <div class="menu-title">{{ trans('navmenu.invoices') }}</div>
                            </a>
                            <ul>
                                <li> <a href="{{ url('customers') }}"><i
                                            class="bx bx-right-arrow-alt"></i>{{ trans('navmenu.customer_accounts') }}</a>
                                </li>
                                <li> <a href="{{ url('invoices') }}"><i
                                            class="bx bx-right-arrow-alt"></i>{{ trans('navmenu.invoice_list') }}</a>
                                </li>
                                <li> <a href="{{ url('delivery-notes') }}"><i
                                            class="bx bx-right-arrow-alt"></i>{{ trans('navmenu.delivery_notes') }}</a>
                                </li>
                                <li> <a href="{{ url('credit-notes') }}"><i
                                            class="bx bx-right-arrow-alt"></i>{{ trans('navmenu.credit_notes') }}</a>
                                </li>
                                <li> <a href="{{ url('pro-invoices') }}"><i
                                            class="bx bx-right-arrow-alt"></i>{{ trans('navmenu.pro_invoice') }}</a>
                                </li>
                                <li> <a href="{{ url('invoice-report') }}"><i
                                            class="bx bx-right-arrow-alt"></i>{{ trans('navmenu.invoice_report') }}</a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endif
                @if (Auth::user()->hasRole('manager') || Auth::user()->can('view-report'))
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class='lni lni-revenue'></i>
                            </div>
                            <div class="menu-title">{{ trans('navmenu.sales_summary_reports') }}</div>
                        </a>
                        <ul>
                            <li><a href="{{ url('sales-report') }}"><i class="bx bx-right-arrow-alt"></i>
                                    {{ trans('navmenu.sales_report') }}</a></li>
                            <li><a href="{{ url('debts-report') }}"><i class="bx bx-right-arrow-alt"></i>
                                    {{ trans('navmenu.debt_report') }}</a></li>
                            <li><a href="{{ url('sales-by-product') }}"><i class="bx bx-right-arrow-alt"></i>
                                    {{ trans('navmenu.sales_by_product') }}</a></li>
                            <li><a href="{{ url('sales-by-service') }}"><i class="bx bx-right-arrow-alt"></i>
                                    {{ trans('navmenu.sales_by_service') }}</a></li>
                            <li><a href="{{ url('profits') }}"><i class="bx bx-right-arrow-alt"></i>
                                    {{ trans('navmenu.profit_report') }}</a></li>
                            <li><a href="{{ url('sales-return-report') }}"><i class="bx bx-right-arrow-alt"></i>
                                    {{ trans('navmenu.sales_return_report') }}</a></li>
                        </ul>
                    </li>
                @endif


                @if (Auth::user()->hasRole('manager') || Auth::user()->hasRole('salesman'))
                    <li>
                        <a href="{{ url('price-list') }}">
                            <div class="parent-icon"><i class='bx bx-calculator'></i>
                            </div>
                            <div class="menu-title">{{ trans('navmenu.prices') }}</div>
                        </a>
                    </li>
                @endif


                {{-- <li>
                    <a href="{{url('price-list')}}">
                        <i class='lni lni-dollar'></i>
                      <span>{{trans('navmenu.prices')}}</span>
                      <span class="pull-right-container">
                        <i class="fa fa-angle-right pull-right"></i>
                      </span>
                    </a>
                  </li> --}}
                @if (Auth::user()->hasRole('manager'))
                    <li class="menu-label">{{ trans('navmenu.vfd') }}</li>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class='bx bx-save'></i>
                            </div>
                            <div class="menu-title">{{ trans('navmenu.vfd') }}</div>
                        </a>
                        <ul>
                            <li><a href="{{ route('vfd-rct-infos.create') }}"><i class="bx bx-right-arrow-alt"></i>
                                    {{ trans('navmenu.vfd_pos') }}</a></li>
                            <li><a href="{{ url('vfd-rct-infos') }}"><i class="bx bx-right-arrow-alt"></i>
                                    {{ trans('navmenu.vfd_receipts') }}</a></li>
                            <li><a href="{{ url('vfd-zreports') }}"><i class="bx bx-right-arrow-alt"></i>
                                    {{ trans('navmenu.vfd_zreports') }}</a></li>
                            <li><a href="{{ url('vfd-register-infos') }}"><i class="bx bx-right-arrow-alt"></i>
                                    {{ trans('navmenu.vfd_register') }}</a></li>
                            <li><a href="{{ url('vfd-items') }}"><i class="bx bx-right-arrow-alt"></i> My Items</a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if (Auth::user()->hasRole('manager'))
                    <li class="menu-label">Products & Services</li>
                @endif
                @if ($shop->business_type_id != 3)


                    @if (Auth::user()->can('edit-stock') ||
                            Auth::user()->can('delete-stock') ||
                            Auth::user()->hasRole('manager') ||
                            Auth::user()->hasRole('storekeeper') ||
                            Auth::user()->can('add-product') ||
                            Auth::user()->can('view-purchase') ||
                            Auth::user()->can('add-purchase') ||
                            Auth::user()->can('view-supplier') ||
                            Auth::user()->can('delete-supplier') ||
                            Auth::user()->can('edit-supplier') ||
                            Auth::user()->can('view-product') ||
                            Auth::user()->can('edit-product'))
                        <li>
                            <a class="has-arrow" href="javascript:;">
                                <div class="parent-icon"><i class='bx bx-cart'></i>
                                </div>
                                <div class="menu-title">{{ trans('navmenu.products') }}</div>
                            </a>
                            <ul>
                                @if (Auth::user()->hasRole('manager') ||
                                        Auth::user()->can('edit-stock') ||
                                        Auth::user()->can('delete-stock') ||
                                        Auth::user()->hasRole('storekeeper') ||
                                        Auth::user()->can('add-product') ||
                                        Auth::user()->can('view-product') ||
                                        Auth::user()->can('edit-product'))
                                    <li><a href="{{ url('products') }}"><i class="bx bx-right-arrow-alt"></i>
                                            {{ trans('navmenu.product_list') }}</a></li>
                                @endif
                                @if (Auth::user()->hasRole('manager') || Auth::user()->hasRole('storekeeper'))
                                    <li><a href="{{ url('categories') }}"><i
                                                class="bx bx-right-arrow-alt"></i>{{ trans('navmenu.categories') }}</a>
                                    </li>
                                    <li><a href="{{ route('transfer-orders.create') }}"><i
                                                class="bx bx-right-arrow-alt"></i>
                                            {{ trans('navmenu.new_stock_transfer') }}</a></li>
                                    <li><a href="{{ url('transfer-orders') }}"><i class="bx bx-right-arrow-alt"></i>
                                            {{ trans('navmenu.stock_transfer') }}</a></li>
                                    <li><a href="{{ route('purchase-orders.create') }}"><i
                                                class="bx bx-right-arrow-alt"></i>
                                            {{ trans('navmenu.new_purchase_order') }}</a></li>
                                @endif
                                @if (Auth::user()->hasRole('manager'))
                                    <li><a href="{{ url('purchase-orders') }}"><i class="bx bx-right-arrow-alt"></i>
                                            {{ trans('navmenu.purchase_orders') }}</a></li>
                                @endif
                                @if (Auth::user()->hasRole('manager') || Auth::user()->hasRole('storekeeper') || Auth::user()->can('add-purchase'))
                                    <li><a href="{{ route('purchases.create') }}"><i
                                                class="bx bx-right-arrow-alt"></i>
                                            {{ trans('navmenu.new_purchase') }}</a></li>
                                @endif
                                @if (Auth::user()->hasRole('manager') || Auth::user()->hasRole('storekeeper') || Auth::user()->can('view-purchase'))
                                    <li><a href="{{ url('purchases') }}"><i class="bx bx-right-arrow-alt"></i>
                                            {{ trans('navmenu.purchases') }}</a></li>
                                @endif

                                @if (Auth::user()->hasRole('manager') ||
                                        Auth::user()->can('add-supplier') ||
                                        Auth::user()->can('view-supplier') ||
                                        Auth::user()->can('delete-supplier') ||
                                        Auth::user()->can('edit-supplier'))
                                    <li><a href="{{ url('suppliers') }}"><i class="bx bx-right-arrow-alt"></i>
                                            {{ trans('navmenu.supplier_accounts') }}</a></li>
                                @endif
                            </ul>
                    @endif
                    </li>

                    @if (Auth::user()->hasRole('manager') || Auth::user()->can('view-reports'))
                        <li>
                            <a class="has-arrow" href="javascript:;">
                                <div class="parent-icon"><i class='lni lni-invest-monitor'></i>
                                </div>
                                <div class="menu-title">{{ trans('navmenu.inventory_reports') }}</div>
                            </a>
                            <ul>
                                <li><a href="{{ url('stock-reports') }}"><i class="bx bx-right-arrow-alt"></i>
                                        {{ trans('navmenu.stock_status_report') }} </a></li>
                                <li><a href="{{ url('stock-taking') }}"><i class="bx bx-right-arrow-alt"></i>
                                        {{ trans('navmenu.stock_purchase_report') }}</a></li>
                                <li><a href="{{ url('stock-expires') }}"><i class="bx bx-right-arrow-alt"></i>
                                        {{ trans('navmenu.expiration_report') }}</a></li>
                                <li><a href="{{ url('stock-capital') }}"><i class="bx bx-right-arrow-alt"></i>
                                        {{ trans('navmenu.current_stock_capital') }} </a></li>
                                <li><a href="{{ url('reorder-reports') }}"><i class="bx bx-right-arrow-alt"></i>
                                        {{ trans('navmenu.re_ordering_report') }}</a></li>
                                <li><a href="{{ url('transfer-report') }}"><i class="bx bx-right-arrow-alt"></i>
                                        {{ trans('navmenu.transfer_report') }} </a></li>
                            </ul>
                        </li>
                    @endif
                @endif
                @if ($shop->business_type_id == 3 || $shop->business_type_id == 4)
                    <li>
                        <a class="has-arrow" href="{{ url('services') }}">
                            <div class="parent-icon"><i class="bx bx-cog"></i>
                            </div>
                            <div class="menu-title">{{ trans('navmenu.services') }}</div>
                        </a>
                    </li>

                    {{-- <li class="menu-label">Accounting & Finance</li> --}}
                @endif

                @if (Auth::user()->hasRole('manager') ||
                        Auth::user()->can('add-expenses') ||
                        Auth::user()->can('delete-expenses') ||
                        Auth::user()->can('view-expenses') ||
                        Auth::user()->can('edit-expenses'))
                    <li>
                        <a class="has-arrow" href="javascript:;">
                            <div class="parent-icon"><i class="bx bx-pulse"></i>
                            </div>
                            <div class="menu-title">{{ trans('navmenu.expenses') }}</div>
                        </a>
                        <ul>
                            @if (Auth::user()->hasRole('manager') || Auth::user()->can('add-expenses'))
                                <li><a href="{{ route('expenses.create') }}"><i class="bx bx-right-arrow-alt"></i>
                                        {{ trans('navmenu.add_new_expense') }}</a></li>
                            @endif
                            @if (Auth::user()->hasRole('manager') ||
                                    Auth::user()->can('view-expenses') ||
                                    Auth::user()->can('edit-expenses') ||
                                    Auth::user()->can('delete-expenses'))
                                <li><a href="{{ url('expenses') }}"><i class="bx bx-right-arrow-alt"></i>
                                        {{ trans('navmenu.expenses_list') }}</a></li>
                            @endif
                            {{-- @if ($shop->subscription_type_id >= 1) --}}
                            @if (Auth::user()->hasRole('manager'))
                                <li><a href="{{ url('exp-suppliers') }}"><i class="bx bx-right-arrow-alt"></i>
                                        {{ trans('navmenu.supplier_accounts') }}</a></li>
                            @endif
                        </ul>

                    </li>
                @endif
                @if (Auth::user()->hasRole('manager') ||
                        Auth::user()->can('view-cashflow') ||
                        Auth::user()->can('add-cashin') ||
                        Auth::user()->can('delete-cashin') ||
                        Auth::user()->can('edit-cashin') ||
                        Auth::user()->can('add-cashout') ||
                        Auth::user()->can('delete-cashout') ||
                        Auth::user()->can('edit-cashout') ||
                        Auth::user()->can('view-transaction') ||
                        Auth::user()->can('add-transaction') ||
                        Auth::user()->can('edit-transaction') ||
                        Auth::user()->can('delete-transaction'))
                    <li>
                        <a href="{{ url('cash-flows') }}" class="has-arrow">
                            <div class="parent-icon"><i class="bx bx-shuffle"></i>
                            </div>
                            <div class="menu-title">{{ trans('navmenu.cash_flows') }}</div>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->hasRole('manager') || Auth::user()->can('view-reports'))
                    <li>
                        <a class="has-arrow" href="javascript:;">
                            <div class="parent-icon"> <i class="lni lni-stats-up"></i>
                            </div>
                            <div class="menu-title">{{ trans('navmenu.financial_report') }}</div>
                        </a>
                        <ul>
                            <li><a href="{{ url('expenses-report') }}"><i
                                        class="bx bx-right-arrow-alt"></i>{{ trans('navmenu.expense_report') }} </a>
                            </li>
                            <li><a href="{{ url('daily-cash-flow-statement') }}"><i
                                        class="bx bx-right-arrow-alt"></i>{{ trans('navmenu.daily_cashflow_stmt') }}
                                </a></li>
                            <li><a href="{{ url('cash-flow-statement') }}"><i
                                        class="bx bx-right-arrow-alt"></i>{{ trans('navmenu.cash_flow_stmt') }}</a>
                            </li>
                            <li><a href="{{ url('income-statement') }}"><i
                                        class="bx bx-right-arrow-alt"></i>{{ trans('navmenu.income_stmt') }} </a>
                            </li>
                            <li><a href="{{ url('business-value') }}"><i
                                        class="bx bx-right-arrow-alt"></i>{{ trans('navmenu.business_value') }}</a>
                            </li>
                            <li><a href="{{ url('closing-business-value') }}"><i
                                        class="bx bx-right-arrow-alt"></i>{{ trans('navmenu.monthly_value') }}</a>
                            </li>
                            <li><a href="{{ url('collections-report') }}"><i
                                        class="bx bx-right-arrow-alt"></i>{{ trans('navmenu.collections_report') }}</a>
                            </li>
                            <li><a href="{{ url('open-closing-amount-statement') }}"><i
                                        class="bx bx-right-arrow-alt"></i>{{ trans('navmenu.oca_stmt') }} </a></li>
                        </ul>
                    </li>
                    <li class="menu-label">General Reports</li>
                    <li>
                        <a class="has-arrow" href="javascript:;">
                            <div class="parent-icon"><i class="bx bx-line-chart"></i>
                            </div>
                            <div class="menu-title">Daily Business Reports</div>
                        </a>
                        <ul>
                            <li><a href="{{ url('dreport-summary') }}"><i
                                        class="bx bx-right-arrow-alt"></i>{{ trans('navmenu.report_summary') }}</a>
                            </li>
                            <li><a href="{{ url('reports') }}"><i class="bx bx-right-arrow-alt"></i>
                                    {{ trans('navmenu.gr_report') }}</a></li>
                            <li><a href="{{ url('total-report') }}"><i
                                        class="bx bx-right-arrow-alt"></i>{{ trans('navmenu.daily_profit_loss_report') }}</a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (Auth::user()->hasRole('manager'))
                    <li class="menu-label">Account & Settings</li>
                    <li>
                        <a href="{{ url('verify-payment') }}">
                            <div class="parent-icon"><i class="bx bx-money"></i>
                            </div>
                            <div class="menu-title">{{ trans('navmenu.payments') }}</div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('user-profile') }}">
                            <div class="parent-icon"><i class="bx bx-user-pin"></i>
                            </div>
                            <div class="menu-title">{{ trans('navmenu.my_account') }}</div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('settings') }}">
                            <div class="parent-icon"><i class="lni lni-cogs"></i>
                            </div>
                            <div class="menu-title">{{ trans('navmenu.settings') }}</div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('recyclebin') }}">
                            <div class="parent-icon"><i class="bx bx-trash"></i>
                            </div>
                            <div class="menu-title">{{ trans('navmenu.recyclebin') }}</div>
                        </a>
                    </li>
                @endif
                <li>
                    <a href="#" target="_blank">
                        <div class="parent-icon"><i class="bx bx-folder"></i>
                        </div>
                        <div class="menu-title">User Guide</div>
                    </a>
                </li>
                <li>
                    <a href="{{ url('') }}" target="_blank">
                        <div class="parent-icon"><i class="bx bx-support"></i>
                        </div>
                        <div class="menu-title">Support</div>
                    </a>
                </li>
            </ul>
            <!--end navigation-->
        </div>
        <!--end sidebar wrapper -->
        <!--start header -->
        <header>
            <div class="topbar d-flex align-items-center">
                <nav class="navbar navbar-expand">
                    <div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
                    </div>
                    <div class="select-bar">
                        <form method="POST" action="{{ url('switch-shop') }}">
                            @csrf
                            <select name="shop_id" id="auto_submit"
                                onchange='if(this.value != 0) { this.form.submit(); }'
                                class="form-select form-select-sm select-bar-box">
                                @foreach (Auth::user()->shops()->get() as $key => $shop)
                                    @if ($shop->id == Session::get('shop_id'))
                                        <option value="{{ $shop->id }}" selected>{{ $shop->name }}</option>
                                    @else
                                        <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <div class="top-menu ms-auto">
                        <ul class="navbar-nav align-items-center">
                            <li class="nav-item dropdown dropdown-large">
                                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative"
                                    href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="alert-count">0</span>
                                    <i class='bx bx-bell'></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="javascript:;">
                                        <div class="msg-header">
                                            <p class="msg-header-title">Notifications</p>
                                            <p class="msg-header-clear ms-auto">Marks all as read</p>
                                        </div>
                                    </a>

                                    <div class="header-notifications-list">

                                    </div>
                                    <a href="javascript:;">
                                        <div class="text-center msg-footer">View All Notifications</div>
                                    </a>
                                </div>
                            </li>
                            <li class="nav-item dropdown dropdown-large">
                                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative"
                                    href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="alert-count">0</span>
                                    <i class='bx bx-comment'></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="javascript:;">
                                        <div class="msg-header">
                                            <p class="msg-header-title">Messages</p>
                                            <p class="msg-header-clear ms-auto">Marks all as read</p>
                                        </div>
                                    </a>
                                    <div class="header-message-list">

                                    </div>
                                    <a href="javascript:;">
                                        <div class="text-center msg-footer">View All Messages</div>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="user-box dropdown">
                        <a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret"
                            href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ asset('assets/images/user.jpg') }}" class="user-img" alt="user avatar">
                            <div class="user-info ps-3">
                                <p class="user-name mb-0">{{ Auth::user()->first_name }}
                                    {{ Auth::user()->last_name }}</p>
                                <p class="designattion mb-0">{{ Auth::user()->roles[0]['name'] }}</p>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ url('user-profile') }}"><i
                                        class="bx bx-user"></i><span>My Account</span></a>
                            </li>
                            @if (Auth::user()->hasRole('manager'))
                                <li><a class="dropdown-item" href="{{ url('home') }}"><i
                                            class="bx bx-home"></i><span> {{ trans('navmenu.dashboard') }}</span></a>
                                </li>
                                {{-- <li><a href="{{ url('home') }}" class="has-arrow"><i class="bx bx-key"></i><b> {{trans('navmenu.dashboard')}}</b></a>
                            </li> --}}
                                <li><a class="dropdown-item" href="{{ url('settings') }}"><i
                                            class="lni lni-cogs"></i><span>
                                            {{ trans('navmenu.settings') }}</span></a>
                                </li>
                            @endif
                            {{-- <li><a href="{{ url('settings') }}"><i class="bx bx-key"></i><b> {{trans('navmenu.setting')}}</b></a>
                            </li> --}}
                            <li>
                                <div class="dropdown-divider mb-0"></div>
                            </li>
                            <li><a class="dropdown-item" href="javascript:;"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                        class='bx bx-log-out-circle'></i><span>Logout</span></a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </header>
        <!--end header -->
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="preloader-wrapper">
                <div class="preloader">
                    <img src="{{ asset('assets/images/4.gif') }}" alt="SmartMauzo">
                </div>
            </div>
            <div class="page-content">
                @yield('content')
            </div>
        </div>
        <!--end page wrapper -->
        <!--start overlay-->
        <div class="overlay toggle-icon"></div>
        <!--end overlay-->
        <!--Start Back To Top Button--> <a href="javaScript:;" class="back-to-top"><i
                class='bx bxs-up-arrow-alt'></i></a>
        <!--End Back To Top Button-->
        <footer class="page-footer">
            <p class="mb-0">{{ trans('navmenu.copyright') }} &copy;
                <script>
                    document.write(new Date().getFullYear());
                </script> {{ trans('navmenu.all_right') }}.
            </p>
        </footer>
    </div>
    <!--end wrapper-->
    <!--start switcher-->
    <div class="switcher-wrapper">
        <div class="switcher-btn"> <i class='bx bx-cog bx-spin'></i>
        </div>
        <div class="switcher-body">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 text-uppercase">Theme Customizer</h5>
                <button type="button" class="btn-close ms-auto close-switcher" aria-label="Close"></button>
            </div>
            <hr />
            <h6 class="mb-0">Theme Styles</h6>
            <hr />
            <div class="d-flex align-items-center justify-content-between">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="lightmode" checked>
                    <label class="form-check-label" for="lightmode">Light</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="darkmode">
                    <label class="form-check-label" for="darkmode">Dark</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="semidark">
                    <label class="form-check-label" for="semidark">Semi Dark</label>
                </div>
            </div>
            <hr />
            <div class="form-check">
                <input class="form-check-input" type="radio" id="minimaltheme" name="flexRadioDefault">
                <label class="form-check-label" for="minimaltheme">Minimal Theme</label>
            </div>
            <hr />
            <h6 class="mb-0">Header Colors</h6>
            <hr />
            <div class="header-colors-indigators">
                <div class="row row-cols-auto g-3">
                    <div class="col">
                        <div class="indigator headercolor1" id="headercolor1"></div>
                    </div>
                    <div class="col">
                        <div class="indigator headercolor2" id="headercolor2"></div>
                    </div>
                    <div class="col">
                        <div class="indigator headercolor3" id="headercolor3"></div>
                    </div>
                    <div class="col">
                        <div class="indigator headercolor4" id="headercolor4"></div>
                    </div>
                    <div class="col">
                        <div class="indigator headercolor5" id="headercolor5"></div>
                    </div>
                    <div class="col">
                        <div class="indigator headercolor6" id="headercolor6"></div>
                    </div>
                    <div class="col">
                        <div class="indigator headercolor7" id="headercolor7"></div>
                    </div>
                    <div class="col">
                        <div class="indigator headercolor8" id="headercolor8"></div>
                    </div>
                </div>
            </div>

            <hr />
            <h6 class="mb-0">Sidebar Backgrounds</h6>
            <hr />
            <div class="header-colors-indigators">
                <div class="row row-cols-auto g-3">
                    <div class="col">
                        <div class="indigator sidebarcolor1" id="sidebarcolor1"></div>
                    </div>
                    <div class="col">
                        <div class="indigator sidebarcolor2" id="sidebarcolor2"></div>
                    </div>
                    <div class="col">
                        <div class="indigator sidebarcolor3" id="sidebarcolor3"></div>
                    </div>
                    <div class="col">
                        <div class="indigator sidebarcolor4" id="sidebarcolor4"></div>
                    </div>
                    <div class="col">
                        <div class="indigator sidebarcolor5" id="sidebarcolor5"></div>
                    </div>
                    <div class="col">
                        <div class="indigator sidebarcolor6" id="sidebarcolor6"></div>
                    </div>
                    <div class="col">
                        <div class="indigator sidebarcolor7" id="sidebarcolor7"></div>
                    </div>
                    <div class="col">
                        <div class="indigator sidebarcolor8" id="sidebarcolor8"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--end switcher-->
    @livewireScripts
    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <!--plugins-->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/plugins/smart-wizard/js/jquery.smartWizard.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <!-- Datatables -->
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <!-- Date and TimePicker -->
    <script src="{{ asset('assets/plugins/datetimepicker/js/legacy.js') }}"></script>
    <script src="{{ asset('assets/plugins/datetimepicker/js/picker.js') }}"></script>
    <script src="{{ asset('assets/plugins/datetimepicker/js/picker.time.js') }}"></script>
    <script src="{{ asset('assets/plugins/datetimepicker/js/picker.date.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-material-datetimepicker/js/moment.min.js') }}"></script>
    <script
        src="{{ asset('assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.min.js') }}">
    </script>

    <script src="{{ asset('assets/plugins/fancy-file-uploader/jquery.ui.widget.js') }}"></script>
    <script src="{{ asset('assets/plugins/fancy-file-uploader/jquery.fileupload.js') }}"></script>
    <script src="{{ asset('assets/plugins/fancy-file-uploader/jquery.iframe-transport.js') }}"></script>
    <script src="{{ asset('assets/plugins/fancy-file-uploader/jquery.fancy-fileupload.js') }}"></script>
    <script src="{{ asset('assets/plugins/Drag-And-Drop/dist/imageuploadify.min.js') }}"></script>

    <!-- InputMask -->
    <script src="{{ asset('assets/plugins/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('assets/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ asset('assets/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>

    <!-- date-range-picker -->
    <script src="{{ asset('assets/plugins/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/plugins/fullcalendar/js/main.min.js') }}"></script>

    <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery.easy-pie-chart/jquery.easypiechart.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/sparkline-charts/jquery.sparkline.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-knob/excanvas.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-knob/jquery.knob.js') }}"></script>
    <!--notification js -->
    <script src="{{ asset('assets/plugins/notifications/js/lobibox.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/notifications/js/notifications.min.js') }}"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.11/sorting/date-eu.js"></script>

    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/js/intlTelInput-jquery.min.js"></script>

    <!-- Page Scripts -->
    @yield('page-scripts')

    @if ($message = Session::get('success'))
        <script>
            $(document).ready(function() {
                success_noti("{{ $message }}");
            });
        </script>
    @endif
    @if ($message = Session::get('error'))
        <script>
            $(document).ready(function() {
                console.log("{{ $message }}");
                error_noti("{{ $message }}");
            });
        </script>
    @endif

    @if ($message = Session::get('info'))
        <script>
            $(document).ready(function() {
                info_noti("{{ $message }}");
            });
        </script>
    @endif

    @if ($message = Session::get('warning'))
        <script>
            $(document).ready(function() {
                warning_noti("{{ $message }}");
            });
        </script>
    @endif

    <!-- page script -->
    <?php
    $dur = '';
    $cust = '';
    $supp = '';
    $is_post = '';
    $startdate = '';
    $enddate = '';
    $is_pos = false;
    $loadcountries = false;
    $is_school = false;
    $is_filling_station = false;
    if (!is_null($settings)) {
        if ($settings->is_school) {
            $is_school = true;
        }
        if ($settings->is_filling_station) {
            $is_filling_station = true;
        }
    }
    
    if ($page == 'Home' || $page == 'Reports' || $page == 'Sales' || $page == 'Costs' || $page == 'Cash Flows' || $page == 'Account Statement' || $page == 'Recycle Bin' || $page == 'Opening/Closing Amount') {
        $is_post = $is_post_query;
        $startdate = $start_date;
        $enddate = $end_date;
    }
    
    if ($page == 'Customers' || $page == 'Suppliers' || $page == 'Profile') {
        $loadcountries = true;
    }
    
    if ($page == 'Point of Sale') {
        $is_pos = true;
    }
    
    if ($page == 'Reports') {
        if (app()->getLocale() == 'en') {
            $dur = $duration;
        } else {
            $dur = $duration_sw;
        }
    }
    
    if ($page == 'Invoices') {
        if (!is_null($customer)) {
            $cust = $customer->name;
            $dur = $duration;
        } else {
            $cust = 'All';
            $dur = $duration;
        }
    }
    
    if ($page == 'Account Statement') {
        if (!is_null($customer)) {
            $cust = $customer->name;
        } else {
            $cust = '';
        }
    
        if (!is_null($supplier)) {
            $supp = $supplier->name;
        } else {
            $supp = '';
        }
    }
    ?>
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
    <script>
        $(document).ready(function() {

            setTimeout(function() {
                $('.preloader-wrapper').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 1300);

            var d = new Date();
            const months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
            var day = d.getDate();
            var month = d.getMonth();
            var year = d.getFullYear();
            var date = day + " " + months[month] + " " + year;

            var duration = "<?php echo $dur; ?>";
            var customer = "<?php echo $cust; ?>";
            var supplier = "<<?php echo $supp; ?>";
            var shop_name = "<?php echo $shop->name; ?>";

            var userlang = "<?php echo app()->getLocale(); ?>";
            var languageUrl = "";
            if (userlang === 'en') {
                languageUrl = "{{ asset('assets/plugins/libs/English.json') }}";
            } else {
                languageUrl = "{{ asset('assets/plugins/libs/Swahili.json') }}";
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            $(".knob").knob();

            $('.select2').select2();

            $('.multiple-select').select2({
                theme: 'bootstrap4',
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' :
                    'style',
                placeholder: $(this).data('placeholder'),
                allowClear: Boolean($(this).data('allow-clear')),
            });

            $('#image-uploadify').imageuploadify();

            $('[data-mask]').inputmask();

            $('#users').DataTable({
                "scrollX": true
            });

            $('#employees').DataTable({
                "scrollX": true
            });

            $('#positions').DataTable({
                "scrollX": true
            });

            $('#saledate').on('change', function() {
                $(".dashform").submit();
            });

            $('#expensedate').on('change', function() {
                $(".dashform").submit();
            });

            $('#example').DataTable({
                "scrollX": true,
                paging: false
            });

            $('#devices').DataTable({
                "scrollX": true
            });

            $('#grades').DataTable({
                "scrollX": true
            });

            $('#psettings').DataTable({
                "scrollX": true
            })

            $('#shop-users').DataTable({
                "scrollX": true
            });

            $('#items').DataTable({
                "scrollX": true
            });

            $('#saleitems').DataTable({
                "scrollX": true
            });

            $('#svitems').DataTable({
                "scrollX": true
            });

            $('#servitems').DataTable({
                "scrollX": true
            });

            $('#creditsales').DataTable({
                "scrollX": true
            });

            $('#creditor-acc-stmt').DataTable({
                'scrollX': true,
            });

            $('#example1').DataTable({
                'scrollX': true
            });

            $('#example3').DataTable({
                'scrollX': true
            });

            $('#example4').DataTable({
                'scrollX': true
            });

            $('#example5').DataTable({
                'scrollX': true
            });

            $('#example6').DataTable({
                'scrollX': true
            });

            $('#sales-history-table').DataTable({
                'scrollX': true
            });

            $('#transfered-tabl').DataTable({
                'scrollX': true
            });

            $('#damaged-table').DataTable({
                'scrollX': true
            });

            var deltable = $('#del-multiple').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                'columnDefs': [{
                    'targets': 0,
                    'checkboxes': {
                        'selectRow': true
                    }
                }],
                'select': {
                    'style': 'multi'
                },
                // 'order': [[1, 'asc']]
            })

            var counterChecked = 0;
            $('#submitButton').prop("disabled", true);

            $('body').on('change', 'input[type="checkbox"]', function() {
                this.checked ? counterChecked++ : counterChecked--;
                counterChecked > 0 ? $('#submitButton').prop("disabled", false) : $('#submitButton').prop(
                    "disabled", true);
                counterChecked < 0 ? counterChecked = 0 : counterChecked;
                console.log(counterChecked);
            });

            $('#submitButton').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "{{ trans('navmenu.are_you_sure_delete') }}",
                    text: "{{ trans('navmenu.no_revert') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
                    cancelButtonText: "{{ trans('navmenu.no') }}"
                }).then((result) => {
                    if (result.value) {
                        $('#del-multiple-sales').submit();
                        Swal.fire(
                            "{{ trans('navmenu.deleted') }}",
                            "{{ trans('navmenu.cancelled') }}",
                            'success'
                        )
                    }
                })
            });

            // .then((result) => {
            //         if (result.value) {
            //             $('#multiple-sales-recycle-form').submit();
            //             Swal.fire(
            //                 "{{ trans('navmenu.restored') }}",
            //                 "{{ trans('navmenu.cancelled') }}",
            //                 'success'
            //             )
            //         }
            //     })


            $('#submitRecycleSales').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "{{ trans('navmenu.are_you_sure_delete') }}",
                    text: "{{ trans('navmenu.no_revert') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
                    cancelButtonText: "{{ trans('navmenu.no') }}"
                }).then((result) => {
                    if (result.value) {
                        $('#multiple-sales-recycle-form').submit();
                        Swal.fire(
                            "{{ trans('navmenu.deleted') }}",
                            "{{ trans('navmenu.cancelled') }}",
                            'success'
                        )
                    }
                })
            });



            $('#submitRecycleSalesRestore').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "{{ trans('navmenu.are_you_sure_restore_selected') }}",
                    // text: "{{ trans('navmenu.no_revert') }}",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
                    cancelButtonText: "{{ trans('navmenu.no') }}"
                }).then((result) => {
                    if (result.value) {
                        $('#multiple-sales-recycle-form').submit();
                        Swal.fire(
                            "{{ trans('navmenu.restored') }}",
                            "{{ trans('navmenu.cancelled') }}",
                            'success'
                        )
                    }
                })
            });


            $('#submitdr').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "{{ trans('navmenu.are_you_sure_you_want_to_delete_all_records') }}",
                    text: "{{ trans('navmenu.no_revert') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ trans('navmenu.delete_them') }}",
                    cancelButtonText: "{{ trans('navmenu.no') }}"
                }).then((result) => {
                    if (result.value) {
                        $('#empty-recycle-sales').submit();
                        Swal.fire(
                            "{{ trans('navmenu.deleted') }}",
                            "{{ trans('navmenu.cancelled') }}",
                            'success'
                        )
                    }
                })
            });




            $('#submitRecyclePurchases').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "{{ trans('navmenu.are_you_sure_delete') }}",
                    text: "{{ trans('navmenu.no_revert') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
                    cancelButtonText: "{{ trans('navmenu.no') }}"
                }).then((result) => {
                    if (result.value) {
                        $('#multiple-purchase-recycle-form').submit();
                        Swal.fire(
                            "{{ trans('navmenu.deleted') }}",
                            "{{ trans('navmenu.cancelled') }}",
                            'success'
                        )
                    }
                })
            });

            $('#submitRecycleExpenses').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "{{ trans('navmenu.are_you_sure_delete') }}",
                    text: "{{ trans('navmenu.no_revert') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
                    cancelButtonText: "{{ trans('navmenu.no') }}"
                }).then((result) => {
                    if (result.value) {
                        $('#multiple-expense-recycle-form').submit();
                        Swal.fire(
                            "{{ trans('navmenu.deleted') }}",
                            "{{ trans('navmenu.cancelled') }}",
                            'success'
                        )
                    }
                })
            })



            // Handle form submission event 
            $('#frm-example').on('submit', function(e) {
                var form = this;
                var rows_selected = deltable.column(0).checkboxes.selected();
                if (rows_selected.length > 0) {
                    // Iterate over all selected checkboxes
                    $.each(rows_selected, function(index, rowId) {
                        // Create a hidden element 
                        $(form).append(
                            $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', 'id[]')
                            .val(rowId)
                        );
                    });
                }
            });


            var daccstmtcl = [];

            var isfill = "<?php echo $is_filling_station; ?>";
            if (isfill) {
                daccstmtcl = [{
                        "sType": "date-uk"
                    },
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                ];
            } else {
                daccstmtcl = [{
                        "sType": "date-uk"
                    },
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                ];
            }
            $('#debtor-acc-stmt').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "aoColumns": daccstmtcl,

                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.debtor_account_stmt') }}_" + date,
                        title: "{{ trans('navmenu.debtor_account_stmt') }} : " + customer,
                        messageTop: duration
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.debtor_account_stmt') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.debtor_account_stmt') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: "{{ trans('navmenu.name') }} : " +
                                        customer + " \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            // doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            });

            $('#all-invoices').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "order": [
                    [3, "desc"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.invoice_report') }}_" + date,
                        title: customer + " {{ trans('navmenu.invoices') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.invoice_report') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: customer +
                                        " {{ trans('navmenu.invoices') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            })

            $('#aging-report').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "order": [
                    [1, "asc"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.aging_report') }}_" + date,
                        title: " {{ trans('navmenu.aging_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.aging_report') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: " {{ trans('navmenu.aging_report') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            // doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            })


            $('#sales').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "order": [
                    [0, "asc"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.sales_report') }}_" + date,
                        title: "{{ trans('navmenu.sales_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.sales_report') }}_" + date,
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.sales_report') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            })

            $('#returns').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "order": [
                    [0, "asc"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.sales_return_report') }}_" + date,
                        title: "{{ trans('navmenu.sales_return_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.sales_return_report') }}_" + date,
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.sales_return_report') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            })

            $('#sales-report').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "order": [
                    [3, "desc"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.sales_report') }}_" + date,
                        title: "{{ trans('navmenu.sales_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.sales_report') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.sales_report') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            })

            var is_school = "<?php echo $is_school; ?>";

            var columns = [{
                    "sType": "date-uk"
                },
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null
            ];
            if (is_school) {
                columns = [{
                        "sType": "date-uk"
                    },
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                ];
            }

            $('#debts').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "aoColumns": columns,
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.debt_report') }}_" + date,
                        title: "{{ trans('navmenu.debt_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.debt_report') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.debt_report') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            });


            var tdcolumns = [
                null,
                null,
                null,
                null,
                null,
                null
            ];

            $('#totaldebts').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "aoColumns": tdcolumns,
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.debtors_total') }}_" + date,
                        title: "{{ trans('navmenu.debtors_total') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.debtors_total') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.debtors_total') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            });

            $('#totals').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "order": [
                    [0, "desc"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.daily_profit_loss_report') }}" + date,
                        title: "{{ trans('navmenu.daily_profit_loss_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.daily_profit_loss_report') }}" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.daily_profit_loss_report') }}",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            })

            $('#consolidated').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "order": [
                    [1, "desc"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.consolidated_report') }}_" + date,
                        title: "{{ trans('navmenu.consolidated_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.consolidated_report') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: 'All Businesses \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.consolidated_report') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            })

            $('#salesproduct').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "order": [
                    [3, "desc"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.sales_by_product') }}_" + date,
                        title: "{{ trans('navmenu.sales_by_product') }}",
                        messageTop: duration,
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.sales_by_product') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.sales_by_product') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            });

            $('#salesservice').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "order": [
                    [3, "desc"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.sales_by_service') }}_" + date,
                        title: "{{ trans('navmenu.sales_by_service') }}",
                        messageTop: duration,
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.sales_by_service') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.sales_by_service') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            });

            $('#profitst').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "order": [
                    [3, "desc"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.profit_report') }}_" + date,
                        title: "{{ trans('navmenu.profit_report') }}",
                        messageTop: duration,
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.profit_report') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.profit_report') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            })


            $('#expenses').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "order": [
                    [3, "desc"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.expense_report') }}_" + date,
                        title: "{{ trans('navmenu.expense_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.expense_report') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.expense_report') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],

            })

            $('#texpenses').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.total_expense_report') }}_" + date,
                        title: "{{ trans('navmenu.total_expense_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.total_expense_report') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.total_expense_report') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],

            })


            $('#sexpenses').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.single_expense_report') }}_" + date,
                        title: "{{ trans('navmenu.single_expense_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.single_expense_report') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.single_expense_report') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],

            })

            $('#stockstatus').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.stock_status_report') }}_" + date,
                        title: "{{ trans('navmenu.stock_status_report') }} " + date,
                        messageTop: 'DATE: ' + date
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.stock_status_report') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.stock_status_report') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: 'Generated On: ' + date,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            })

            $('#stocktransfer').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.transfer_report') }}_" + date,
                        title: "{{ trans('navmenu.transfer_report') }} " + date,
                        messageTop: 'DATE: ' + date
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.transfer_report') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.transfer_report') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: 'Generated On: ' + date,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            })
            $('#stockreceiver').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.stock_received_report') }}_" + date,
                        title: "{{ trans('navmenu.stock_received_report') }} " + date,
                        messageTop: 'DATE: ' + date
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.stock_received_report') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.stock_received_report') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: 'Generated On: ' + date,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            })


            $('#reorderstatus').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.re_ordering_report') }}_" + date,
                        title: "{{ trans('navmenu.re_ordering_report') }} " + date,
                        messageTop: 'DATE: ' + date
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.re_ordering_report') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.re_ordering_report') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: 'Generated On: ' + date,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            })

            $('#stocktaking').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.stock_purchase_report') }}_" + date,
                        title: "{{ trans('navmenu.stock_purchase_report') }}",
                        messageTop: 'DATE: ' + date
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.stock_purchase_report') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.stock_purchase_report') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration + ' \n',
                                    bold: true,
                                    fontSize: 11
                                }, {
                                    text: 'Generated On: ' + date,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            })

            $('#stockexpires').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.expiration_report') }}_" + date,
                        title: "{{ trans('navmenu.expiration_report') }}",
                        messageTop: 'DATE: ' + date
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.expiration_report') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.expiration_report') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration + ' \n',
                                    bold: true,
                                    fontSize: 11
                                }, {
                                    text: 'Generated On: ' + date,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            })


            $('#stockcapital').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.current_stock_capital') }}_" + date,
                        title: "{{ trans('navmenu.current_stock_capital') }}",
                        messageTop: 'DATE : ' + date
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.current_stock_capital') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: "{{ trans('navmenu.current_stock_capital') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: 'Generated On : ' + date,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            })

            $('#collections-report').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "order": [
                    [5, "desc"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.collections_report') }}_" + date,
                        title: " {{ trans('navmenu.collections_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.collections_report') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: " {{ trans('navmenu.collections_report') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            // doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            })

            $('#debt-collections-report').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                "order": [
                    [5, "desc"]
                ],
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.debt_collections_report') }}_" + date,
                        title: " {{ trans('navmenu.debt_collections_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.debt_collections_report') }}_" + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: shop_name + ' \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: " {{ trans('navmenu.debt_collections_report') }} \n",
                                    bold: false,
                                    fontSize: 14
                                }, {
                                    text: duration,
                                    bold: true,
                                    fontSize: 11
                                }],
                                margin: [0, 0, 0, 12],
                                alignment: 'center'
                            });
                            // doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: [{
                                                    text: shop_name + ' Reports',
                                                    italics: true
                                                },
                                                ' - ',
                                                {
                                                    text: 'Powered by SmartMauzo',
                                                    italics: true
                                                }
                                            ]
                                        },
                                        {
                                            alignment: 'right',
                                            text: [{
                                                    text: page.toString(),
                                                    italics: true
                                                },
                                                ' of ',
                                                {
                                                    text: pages.toString(),
                                                    italics: true
                                                }
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                        }
                    }
                ],
            })


            var table = $('#example2').DataTable({
                'scrollX': true,
                lengthChange: false,
                buttons: ['excel', 'pdf', 'print', 'colvis']
            });

            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(0)');

            var stmttable = $('#stmt').DataTable({
                lengthChange: false,
                "initComplete": function() {
                    $("#stmt").show();
                },
                "ordering": true,
                "columnDefs": [{
                    "targets": 0,
                    "type": "date-eu"
                }],
                "bInfo": true,
                "buttons": ['copy', 'excel', 'print']
            });

            stmttable.buttons().container().appendTo('#stmt_wrapper .col-md-6:eq(0)');


            $("#show_hide_password a").on('click', function(event) {
                event.preventDefault();
                if ($('#show_hide_password input').attr("type") == "text") {
                    $('#show_hide_password input').attr('type', 'password');
                    $('#show_hide_password i').addClass("bx-hide");
                    $('#show_hide_password i').removeClass("bx-show");
                } else if ($('#show_hide_password input').attr("type") == "password") {
                    $('#show_hide_password input').attr('type', 'text');
                    $('#show_hide_password i').removeClass("bx-hide");
                    $('#show_hide_password i').addClass("bx-show");
                }
            });

            var code = "+255"; // Assigning value from model.
            $('#inputPhoneNumber').val(code);
            $('#inputPhoneNumber').intlTelInput({
                autoHideDialCode: true,
                autoPlaceholder: "ON",
                dropdownContainer: document.body,
                formatOnDisplay: true,
                hiddenInput: "full_number",
                initialCountry: "auto",
                nationalMode: true,
                placeholderNumberType: "MOBILE",
                preferredCountries: ['TZ'],
                separateDialCode: true
            });
            // Toolbar extra buttons
            var btnFinish = $('#btn-submit').on('click', function() {
                // alert('Finish Clicked');
                'use strict'

                var iso2 = $("#inputPhoneNumber").intlTelInput("getSelectedCountryData").iso2;
                var dialCode = $("#inputPhoneNumber").intlTelInput("getSelectedCountryData").dialCode;
                var cc = $('#countryCode').val(iso2.toUpperCase());
                var dc = $('#dialCode').val(dialCode);
            });

            var previous;

            $("#searchCustomer").on('focus', function() {
                // Store the current value on focus and on change
                previous = this.value;
            }).change(function() {
                this.form.submit();
                previous = this.value;
            });


            //Date range as a button
            var start = moment();
            var end = moment();
            var reportrangequery = false;
            var is_postq = "<?php echo $is_post; ?>";

            if (is_postq) {
                var startstring = "<?php echo $startdate; ?>";
                var endstring = "<?php echo $enddate; ?>";
                start = moment(startstring, 'YYYY-MM-DD');
                end = moment(endstring, 'YYYY-MM-DD');
                // alert('Date is '+start+' - '+end);
            }

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                if (reportrangequery) {
                    $('#start_input').val(start.format('YYYY-MM-DD'));
                    $('#end_input').val(end.format('YYYY-MM-DD'));
                    $('.dashform').submit();
                }
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                    'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year')
                        .endOf('year')
                    ]
                }
            }, cb)
            cb(start, end);

            $('#reportrange').on('click.daterangepicker', function() {
                // $('.dashform').submit();
                reportrangequery = true;
            });

            $("#btype").on('change', function() {
                var typeid = $(this).val(); // this.value
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ url('get-sub-types') }}",
                    data: {
                        business_type_id: typeid
                    },
                    type: 'post'
                }).done(function(data) {
                    console.log('Done: ', data);
                    var sel = $("#sub-type");
                    sel.empty();
                    for (var i = 0; i < data.length; i++) {
                        sel.append('<option value="' + data[i].id + '">' + data[i].name +
                            '</option>');
                    }
                }).fail(function() {
                    console.log('Failed');
                });
            });

            $('#my-select').select2({
                dropdownParent: $('#itemModal')
            });

            $('#serv-select').select2({
                dropdownParent: $('#servitemModal')
            });

            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        });
    </script>
    <!--app JS-->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/plugins/notifications/js/notification-custom-script.js') }}"></script>

</body>

</html>
