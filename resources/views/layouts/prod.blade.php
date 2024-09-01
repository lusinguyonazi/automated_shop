<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  ng-app="smartpos">
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
    <link href="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2-bootstrap4.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/css/intlTelInput.css" />
    <link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/highcharts/css/highcharts.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
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
    <script src="{{asset('assets/plugins/sweetalert2/sweetalert2.all.js')}}"></script>
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
    <!-- Theme Style CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/dark-theme.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/semi-dark.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/header-colors.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{asset('css/invoices/blue.css')}}">
     <style type="text/css">
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
          -webkit-appearance: none; 
        }

        input[type=number] {
          -moz-appearance: textfield;
        }
    </style>
    <title>SmartMauzo - {{$page}}</title>
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
                @if($shop->business_type_id == 1 && $shop->subscription_type_id >= 3)
                <li>
                    <a href="{{ url('prod-home') }}" class="has-arrow">
                        <div class="parent-icon"><i class='bx bx-package'></i>
                        </div>
                        <div class="menu-title">Production</div>
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasRole('manager'))
                <li>
                    <a href="{{ url('home') }}" class="has-arrow">
                        <div class="parent-icon"><i class='bx bx-home-circle'></i>
                        </div>
                        <div class="menu-title">Dashboard</div>
                    </a>
                </li>
                @else
                <li>
                    <a href="{{ url('pos') }}">
                        <div class="parent-icon"><i class='bx bx-calculator'></i>
                        </div>
                        <div class="menu-title">POS</div>
                    </a>
                </li>
                @endif
                <li class="menu-label">{{trans('navmenu.raw_materials')}}</li>
                <li>
                    <a href="{{ url('prod-costs/create') }}">
                        <div class="parent-icon"><i class='bx bx-calculator'></i>
                        </div>
                        <div class="menu-title">PROD PANEL</div>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class='lni lni-dollar'></i>
                        </div>
                        <div class="menu-title">{{trans('navmenu.raw_materials')}}</div>
                    </a>
                    <ul>
                        <li> <a href="{{url('raw-materials')}}"><i class="bx bx-right-arrow-alt"></i>{{trans('navmenu.raw_materials')}}</a></li>
                        <li> <a href="{{route('rm-uses.create')}}"><i class="bx bx-right-arrow-alt"></i>{{trans('navmenu.new_use_of_rm')}}</a></li>
                        <li><a href="{{url('rm-uses')}}"><i class="bx bx-right-arrow-alt"></i> {{trans('navmenu.rm_utilized')}}</a></li>
                        <li><a href="{{route('rm-purchases.create')}}"><i class="bx bx-right-arrow-alt"></i> {{trans('navmenu.purchase_new_rm')}}</a></li>
                        <li><a href="{{url('rm-purchases')}}"><i class="bx bx-right-arrow-alt"></i>{{trans('navmenu.purchase_of_rm')}}</a></li>
                        <li><a href="{{url('rm-suppliers-transaction')}}"><i class="bx bx-right-arrow-alt"></i>{{trans('navmenu.supplier_accounts')}}</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class='lni lni-revenue'></i>
                        </div>
                        <div class="menu-title">{{trans('navmenu.rm_materials_reports')}}</div>
                    </a>
                    <ul>
                        <li><a href="{{url('rm-purchases-report')}}"><i class="bx bx-right-arrow-alt"></i>{{trans('navmenu.rm_purchases')}}</a></li>
                        <li><a href="{{url('rm-uses-report')}}"><i class="bx bx-right-arrow-alt"></i>{{trans('navmenu.rm_uses_report')}}</a></li>
                    </ul>
                </li>
            @if(!$settings->disable_prod_panel)
                <li class="menu-label">Packing Materials</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class='bx bx-cart'></i>
                        </div>
                        <div class="menu-title">{{trans('navmenu.packing_materials')}}</div>
                    </a>
                    <ul>
                        <li><a href="{{url('packing-materials')}}"><i class="bx bx-right-arrow-alt"></i> {{trans('navmenu.packing_materials')}}</a></li>
                        <li><a href="{{route('pm-uses.create')}}"><i class="bx bx-right-arrow-alt"></i>{{trans('navmenu.new_use_of_pm')}}</a></li>
                        <li><a href="{{url('pm-uses')}}"><i class="bx bx-right-arrow-alt"></i>{{trans('navmenu.pm_utilized')}}</a></li>
                        <li><a href="{{route('pm-purchases.create')}}"><i class="bx bx-right-arrow-alt"></i>{{trans('navmenu.purchase_new_pm')}}</a></li>
                        <li><a href="{{url('pm-purchases')}}"><i class="bx bx-right-arrow-alt"></i>{{trans('navmenu.purchase_of_pm')}}</a></li> 
                        <li><a href="{{url('pm-suppliers-transaction')}}"><i class="bx bx-right-arrow-alt"></i> {{trans('navmenu.supplier_accounts')}}</a></li> 
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class='lni lni-invest-monitor'></i>
                        </div>
                        <div class="menu-title">{{trans('navmenu.pm_materials_reports')}}</div>
                    </a>
                    <ul>
                        <li><a href="{{url('pm-purchases-report')}}"><i class="bx bx-right-arrow-alt"></i> {{trans('navmenu.pm_purchases')}} </a></li>
                        <li><a href="{{url('pm-uses-report')}}"><i class="bx bx-right-arrow-alt"></i>{{trans('navmenu.pm_uses_report')}}</a></li>
                    </ul>
                </li>
                <li class="menu-label">OVERHEAD EXPENSES</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-pulse"></i>
                        </div>
                        <div class="menu-title">{{trans('navmenu.overhead_expenses')}}</div>
                    </a>
                    <ul>
                         <li><a href="{{url('mro')}}"><i class="bx bx-right-arrow-alt"></i> {{trans('navmenu.oe_types')}}</a></li>
                        <li><a href="{{route('mro-uses.create')}}"><i class="bx bx-right-arrow-alt"></i>{{trans('navmenu.add_overhead_expenses')}}</a></li>
                         <li><a href="{{url('mro-items')}}"><i class="bx bx-right-arrow-alt"></i>{{trans('navmenu.overhead_expense_incured')}}</a></li>
                       
                    </ul>
                </li>
            @endif
                <li>
                    <a href="{{url('prod-costs')}}" >
                        <div class="parent-icon"><i class="bx bx-shuffle"></i>
                        </div>
                        <div class="menu-title">{{trans('navmenu.production_records')}}</div>
                    </a>
                </li>
                 <li>
                    <a href="{{url('production/createOld')}}">
                        <div class="parent-icon"><i class="bx bx-calculator"></i>
                        </div>
                        <div class="menu-title">{{trans('navmenu.production_costs')}}</div>
                    </a>
                </li>

                <li class="menu-label">General Reports</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-line-chart"></i>
                        </div>
                        <div class="menu-title">Daily Production Reports</div>
                    </a>
                    <ul>
                        <li><a href="{{url('general-report')}}"><i class="bx bx-right-arrow-alt"></i>{{trans('navmenu.gr_report')}}</a></li>
                        <li><a href="{{url('prod-stock-status-report')}}"><i class="bx bx-right-arrow-alt"></i>{{trans('navmenu.stock_status_report')}}</a></li>
                    </ul>
                </li>
                <li class="menu-label">Account & Settings</li>

                <li>
                    <a href="{{url('verify-payment')}}">
                        <div class="parent-icon"><i class="bx bx-money"></i>
                        </div>
                        <div class="menu-title">{{trans('navmenu.payments')}}</div>
                    </a>
                </li>
                <li>
                    <a href="{{ url('user-profile') }}">
                        <div class="parent-icon"><i class="bx bx-user-pin"></i>
                        </div>
                        <div class="menu-title">{{trans('navmenu.my_account')}}</div>
                    </a>
                </li>
                <li>
                    <a href="{{ url('prod-settings') }}">
                        <div class="parent-icon"><i class="lni lni-cogs"></i>
                        </div>
                        <div class="menu-title">{{trans('navmenu.settings')}}</div>
                    </a>
                </li>
                <li>
                    <a href="{{url('recyclebin')}}">
                        <div class="parent-icon"><i class="bx bx-trash"></i>
                        </div>
                        <div class="menu-title">{{trans('navmenu.recyclebin')}}</div>
                    </a>
                </li>
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
                        <form method="POST" action="{{url('switch-shop')}}">
                            @csrf
                            <select name="shop_id" id="auto_submit" onchange='if(this.value != 0) { this.form.submit(); }' class="form-control select-bar-box">
                                @foreach(Auth::user()->shops()->get() as $key => $shop)
                                    @if($shop->id == Session::get('shop_id'))
                                    <option value="{{$shop->id}}" selected>{{$shop->name}}</option>
                                    @else
                                    <option value="{{$shop->id}}">{{$shop->name}}</option>
                                    @endif
                                    @endforeach
                            </select>
                        </form>
                    </div>
                    <div class="top-menu ms-auto">
                        <ul class="navbar-nav align-items-center">
                            <li class="nav-item dropdown dropdown-large">
                                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> <span class="alert-count">0</span>
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
                                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> <span class="alert-count">0</span>
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
                        <a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ asset('assets/images/user.jpg') }}" class="user-img" alt="user avatar">
                            <div class="user-info ps-3">
                                <p class="user-name mb-0">{{Auth::user()->first_name}} {{Auth::user()->last_name}}</p>
                                <p class="designattion mb-0">{{Auth::user()->roles[0]['name']}}</p>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{url('user-profile')}}"><i class="bx bx-user"></i><span>Profile</span></a>
                            </li>
                            <li><a class="dropdown-item" href="{{ url('settings') }}"><i class="bx bx-cog"></i><span>Settings</span></a>
                            </li>
                            <li><a class="dropdown-item" href="{{ url('dashboard') }}"><i class='bx bx-home-circle'></i><span>Dashboard</span></a>
                            </li>
                            <li>
                                <div class="dropdown-divider mb-0"></div>
                            </li>
                            <li><a class="dropdown-item" href="javascript:;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class='bx bx-log-out-circle'></i><span>Logout</span></a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
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
            <div class="page-content">
                @yield('content')
            </div>
        </div>
        <!--end page wrapper -->
        <!--start overlay-->
        <div class="overlay toggle-icon"></div>
        <!--end overlay-->
        <!--Start Back To Top Button--> <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
        <!--End Back To Top Button-->
        <footer class="page-footer">
            <p class="mb-0">{{trans('navmenu.copyright')}} &copy;<script>document.write(new Date().getFullYear());</script> {{trans('navmenu.all_right')}}.</p>
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
            <hr/>
            <h6 class="mb-0">Theme Styles</h6>
            <hr/>
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
            <hr/>
            <div class="form-check">
                <input class="form-check-input" type="radio" id="minimaltheme" name="flexRadioDefault">
                <label class="form-check-label" for="minimaltheme">Minimal Theme</label>
            </div>
            <hr/>
            <h6 class="mb-0">Header Colors</h6>
            <hr/>
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

            <hr/>
            <h6 class="mb-0">Sidebar Backgrounds</h6>
            <hr/>
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

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <!--plugins-->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/plugins/smart-wizard/js/jquery.smartWizard.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
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
    <script src="{{ asset('assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.min.js') }}"></script>

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
    <script src="{{asset('assets/plugins/moment/min/moment.min.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
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
    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/js/intlTelInput-jquery.min.js"></script>
    
    <!-- Page Scripts -->
    @yield('page-scripts')
    
    @if ($message = Session::get('success'))
    <script>
        $(document).ready( function () {
            success_noti("{{$message}}");
        });
    </script>
    @endif
    @if ($message = Session::get('error'))
    <script>
        $(document).ready( function () {
            console.log("{{$message}}");
            error_noti("{{$message}}");
        });
    </script>
    @endif

    @if($message = Session::get('info'))
    <script>
        $(document).ready( function () {
            info_noti("{{$message}}");
        });
    </script>
    @endif

    @if ($message = Session::get('warning')) 
    <script>
        $(document).ready( function () {
            warning_noti("{{$message}}");
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

       {{-- if ($page == 'Home' || $page == 'Reports' || $page == 'Raw Materials' || $page == 'Packing Materials' || $page == 'Production' || $page == 'MRO & EXPENSES') {
            $is_post = $is_post_query;
            $startdate = $start_date;
            $enddate = $end_date;
        } --}}


        if ($page == 'Reports') {
            if(app()->getLocale() == 'en'){
                $dur = $duration;
            }else{
                $dur = $duration_sw;
            }
        }

    ?>
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function () {
          'use strict'
    
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')
    
            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
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

            $('#example1').DataTable({
                'scrollX': true
            });
            
            $('#example3').DataTable({
                'scrollX': true
            });

            var d = new Date();
            const months = ["JAN", "FEB", "MAR","APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
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
            }else{
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
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder'),
                allowClear: Boolean($(this).data('allow-clear')),
            });
            
            $('#image-uploadify').imageuploadify();

            $('[data-mask]').inputmask();

             var table = $('#del-multiple').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                'columnDefs': [
                    {
                        'targets': 0,
                        'checkboxes': {
                            'selectRow': true
                        }
                    }
                ],
                'select': {
                    'style': 'multi'
                },
                // 'order': [[1, 'asc']]
            })

            var counterChecked = 0;
            $('#submitButton').prop("disabled", true);

            $('body').on('change', 'input[type="checkbox"]', function() {
          
                this.checked ? counterChecked++ : counterChecked--;
                counterChecked > 0 ? $('#submitButton').prop("disabled", false): $('#submitButton').prop("disabled", true);
            });

            $('#submitButton').on('click', function(e){
                e.preventDefault();
                Swal.fire({
                    title: "{{trans('navmenu.are_you_sure_delete')}}",
                    text: "{{trans('navmenu.no_revert')}}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{trans('navmenu.cancel_it')}}",
                    cancelButtonText: "{{trans('navmenu.no')}}"
                }).then((result) => {
                    if (result.value) {
                        $('#frm-example').submit();
                        Swal.fire(
                            "{{trans('navmenu.deleted')}}",
                            "{{trans('navmenu.cancelled')}}",
                            'success'
                        )
                    }
                })
            })
      
            // Handle form submission event 
            $('#frm-example').on('submit', function(e){
                var form = this;
                var rows_selected = table.column(0).checkboxes.selected();

                // Iterate over all selected checkboxes
                $.each(rows_selected, function(index, rowId){
                    // Create a hidden element 
                    $(form).append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'id[]')
                        .val(rowId)
                    );
                });
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
                ranges   : {
                    'Today'       : [moment(), moment()],
                    'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month'  : [moment().startOf('month'), moment().endOf('month')],
                    'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year'   : [moment().startOf('year'), moment().endOf('year')],
                    'Last Year'   : [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
                }
            }, cb)
            cb(start, end);
                
            $('#reportrange').on('click.daterangepicker', function(){
                // $('.dashform').submit();
                reportrangequery = true;
            });

        });
    </script>
    <script src="{{ asset('assets/js/index.js') }}"></script>
    <!--app JS-->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/plugins/notifications/js/notification-custom-script.js') }}"></script>

</body>

</html>
