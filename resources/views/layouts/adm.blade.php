<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--favicon-->
    <link rel="icon" href="{{ asset('assets/images/icon.png') }}" type="image/png" />
    <!--plugins-->
    {{-- <link href="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet" /> --}}
    <link href="{{ asset('assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/highcharts/css/highcharts.css') }}" rel="stylesheet" />
    <!-- loader-->
    <link href="{{ asset('assets/css/pace.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/js/pace.min.js') }}"></script>
    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
    <!-- Theme Style CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/dark-theme.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/semi-dark.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/header-colors.css') }}" />
    <!-- Daterange Picker -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />

    <title>SmartMauzo - {{ $page }}</title>
</head>

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
                <li>
                    <a href="{{ url('admin/home') }}">
                        <div class="parent-icon"><i class='bx bx-home-circle'></i></div>
                        <div class="menu-title">Dashboard</div>
                    </a>
                </li>

                <li class="menu-label">Service Payments</li>
                <li>
                    <a href="{{ url('admin/payments') }}">
                        <div class="parent-icon"><i class='bx bx-money'></i>
                        </div>
                        <div class="menu-title">Payments</div>
                    </a>
                </li>
                <li>
                    <a href="{{ url('admin/service-charges') }}">
                        <div class="parent-icon"><i class='bx bx-purchase-tag'></i>
                        </div>
                        <div class="menu-title">Service Charges</div>
                    </a>
                </li>
                <li>
                    <a href="{{ url('admin/subscriptions') }}">
                        <div class="parent-icon"><i class='bx bx-package'></i>
                        </div>
                        <div class="menu-title">Subscription Types/Packages</div>
                    </a>
                </li>

                <li class="menu-label">Accounts</li>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class='bx bx-store-alt'></i>
                        </div>
                        <div class="menu-title">Shop/Businesses</div>
                    </a>
                    <ul>
                        <li> <a href="{{ url('admin/shops') }}"><i class="bx bx-right-arrow-alt"></i>All
                                Shop/Businesses</a></li>
                        <li> <a href="{{ url('admin/active-shops') }}"><i class="bx bx-right-arrow-alt"></i>Active
                                Shop/Businesses</a></li>
                        <li><a href="{{ url('admin/types') }}"><i class="bx bx-right-arrow-alt"></i>Business Types</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class='bx bx-group'></i>
                        </div>
                        <div class="menu-title">Users</div>
                    </a>
                    <ul>
                        <li><a href="{{ url('admin/users') }}"><i class="bx bx-right-arrow-alt"></i> All Users</a></li>
                        <li><a href="{{ url('admin/active-users') }}"><i class="bx bx-right-arrow-alt"></i> Active
                                Users</a></li>
                        <li><a href="{{ url('admin/guest-users') }}"><i class="bx bx-right-arrow-alt"></i> Guest
                                Users</a></li>

                        <li><a href="{{ url('admin/roles') }}"><i class="bx bx-right-arrow-alt"></i>User Roles</a></li>
                        <li><a href="{{ url('admin/permissions') }}"><i class="bx bx-right-arrow-alt"></i>User
                                Permissions</a></li>
                    </ul>
                </li>

                <li class="menu-label">Bulk SMS</li>
                <li>
                    <a href="{{ url('admin/sms-accounts') }}">
                        <div class="parent-icon"><i class='bx bx-accessibility'></i>
                        </div>
                        <div class="menu-title">Bulk SMS Accounts</div>
                    </a>
                </li>
                <li>
                    <a href="{{ url('admin/sms-logs') }}">
                        <div class="parent-icon"><i class='bx bx-message-alt'></i>
                        </div>
                        <div class="menu-title">SMS Logs</div>
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
                            <img src="{{ asset('assets/images/icon.png') }}" class="user-img" alt="user avatar">
                            <div class="user-info ps-3">
                                <p class="user-name mb-0">{{ Auth::user()->first_name }}
                                    {{ Auth::user()->last_name }}</p>
                                <p class="designattion mb-0">{{ Auth::user()->roles[0]['name'] }}</p>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="javascript:;"><i
                                        class="bx bx-user"></i><span>Profile</span></a>
                            </li>
                            <li><a class="dropdown-item" href="javascript:;"><i
                                        class="bx bx-cog"></i><span>Settings</span></a>
                            </li>
                            <li><a class="dropdown-item" href="javascript:;"><i
                                        class='bx bx-home-circle'></i><span>Dashboard</span></a>
                            </li>
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

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <!--plugins-->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
    <script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/plugins/smart-wizard/js/jquery.smartWizard.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <!-- Datatables -->
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-select/js/dataTables.select.min.js') }}"></script>
    <!-- Date and TimePicker -->
    <script src="{{ asset('assets/plugins/datetimepicker/js/legacy.js') }}"></script>
    <script src="{{ asset('assets/plugins/datetimepicker/js/picker.js') }}"></script>
    <script src="{{ asset('assets/plugins/datetimepicker/js/picker.time.js') }}"></script>
    <script src="{{ asset('assets/plugins/datetimepicker/js/picker.date.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-material-datetimepicker/js/moment.min.js') }}"></script>
    <script
        src="{{ asset('assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.min.js') }}">
    </script>

    <!-- date-range-picker -->
    <script src="{{ asset('assets/plugins/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/plugins/fullcalendar/js/main.min.js') }}"></script>
    <!-- date-range-picker -->
    <script src="{{ asset('assets/plugins/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/plugins/fullcalendar/js/main.min.js') }}"></script>

    <script src="{{ asset('assets/plugins/fancy-file-uploader/jquery.ui.widget.js') }}"></script>
    <script src="{{ asset('assets/plugins/fancy-file-uploader/jquery.fileupload.js') }}"></script>
    <script src="{{ asset('assets/plugins/fancy-file-uploader/jquery.iframe-transport.js') }}"></script>
    <script src="{{ asset('assets/plugins/fancy-file-uploader/jquery.fancy-fileupload.js') }}"></script>
    <script src="{{ asset('assets/plugins/Drag-And-Drop/dist/imageuploadify.min.js') }}"></script>

    <script src="{{ asset('assets/plugins/fullcalendar/js/main.min.js') }}"></script>

    <script src="{{ asset('assets/plugins/chartjs/js/Chart.min.js') }}"></script>
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

    <!-- highcharts js -->
    <script src="{{ asset('assets/plugins/highcharts/js/highcharts.js') }}"></script>
    <!--app JS-->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <script type="text/javascript">
        var path = "{{ url('autocomplete-search-query') }}";
        $('input.typeahead').typeahead({
            source: function(query, process) {
                return $.get(path, {
                    query: query
                }, function(data) {
                    return process(data);
                });
            }
        });
    </script>

    <script>
        $(function() {
            "use strict";
            // chart 12
            var date_labels = {!! json_encode($date_labels ?? []) !!}
            var standard_data = {!! json_encode($standard_data ?? []) !!}
            var premium_data = {!! json_encode($premium_data ?? []) !!}
            var uncategorized_data = {!! json_encode($uncategorized_data ?? []) !!}
            var average_data = {!! json_encode($average_data ?? []) !!}

            var standard_overall = {!! $standard_overall ?? 0 !!};
            var premium_overall = {!! $premium_overall ?? 0 !!};
            var uncategorized_overall = {!! $uncategorized_overall ?? 0 !!}

            Highcharts.chart('chart12', {
                chart: {
                    styledMode: true
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: 'TOTAL AMOUNT PAID PER DAY'
                },
                xAxis: {
                    categories: date_labels // Date of the months
                },
                labels: {
                    items: [{
                        html: 'Total Amount',
                        style: {
                            left: '50px',
                            top: '18px',
                            color: ( // theme
                                Highcharts.defaultOptions.title.style && Highcharts
                                .defaultOptions.title.style.color) || 'black'
                        }
                    }]
                },
                series: [{
                        type: 'column',
                        name: 'Standard',
                        data: standard_data
                    }, {
                        type: 'column',
                        name: 'uncategorized',
                        data: uncategorized_data
                    }, {
                        type: 'column',
                        name: 'Premium',
                        data: premium_data
                    },

                    { //Average
                        type: 'spline',
                        name: 'Average',
                        data: average_data,
                        marker: {
                            lineWidth: 2,
                            lineColor: Highcharts.getOptions().colors[3],
                            fillColor: 'white'
                        }
                    },




                    {
                        type: 'pie',
                        name: 'Total Amount',
                        data: [{
                            name: 'Standard',
                            y: standard_overall,
                            color: Highcharts.getOptions().colors[0] // Jane's color
                        }, {
                            name: 'uncategorized',
                            y: uncategorized_overall,
                            color: Highcharts.getOptions().colors[1] // John's color
                        }, {
                            name: 'Premium',
                            y: premium_overall,
                            color: Highcharts.getOptions().colors[2] // Joe's color
                        }],
                        center: [100, 80],
                        size: 100,
                        showInLegend: false,
                        dataLabels: {
                            enabled: false
                        }
                    }
                ]
            });
        });
    </script>


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

    <?php
    $is_post = '';
    $startdate = '';
    $enddate = '';
    
    if ($page == 'Payments' || $page == 'Users' || $page == 'Shops') {
        $is_post = $is_post_query;
        $startdate = $start_date;
        $enddate = $end_date;
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

            $(".knob").knob();

            $('.multiple-select').select2({
                theme: 'bootstrap4',
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' :
                    'style',
                placeholder: $(this).data('placeholder'),
                allowClear: Boolean($(this).data('allow-clear')),
            });

            var d = new Date();
            const months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
            var day = d.getDate();
            var month = d.getMonth();
            var year = d.getFullYear();
            var date = day + " " + months[month] + " " + year;
            var duration = "";
            $('#example1').DataTable({
                "scrollX": true
            })
            $('#example').DataTable({
                "scrollX": true
            })
            $('#example4').DataTable({
                "scrollX": true
            })

            //Date range as a button
            var start = moment().startOf('month');
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

            var dateFormatsArray = {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                    .endOf(
                        'month')
                ],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf(
                    'year')]
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: dateFormatsArray
            }, cb)
            cb(start, end);

            $('#reportrange').on('click.daterangepicker', function() {
                // $('.dashform').submit();
                reportrangequery = true;
            });

            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

            //Initialize Select2 Elements
            $('.select2').select2()

            $('#actshops').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('admin/act-shops') }}"
                },
                columns: [{
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'shop_id',
                        name: 'shop_id'
                    },
                    {
                        data: 'first_name',
                        name: 'first_name'
                    },
                    {
                        data: 'last_name',
                        name: 'last_name'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'display_name',
                        name: 'display_name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'is_default',
                        name: 'is_default'
                    },
                    {
                        data: 'expire_date',
                        name: 'expire_date'
                    },
                    {
                        data: 'is_expired',
                        name: 'is_expired'
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            })

            $('#shsales').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('admin/sales') }}"
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'shop_id',
                        name: 'shop_id'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'sale_amount',
                        name: 'sale_amount'
                    },
                    {
                        data: 'time_created',
                        name: 'time_created'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                ],
                order: [
                    [0, 'desc']
                ]
            })


            $('#shitems').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('admin/items') }}"
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'sale_id',
                        name: 'sale_id'
                    },
                    {
                        data: 'product_id',
                        name: 'product_id'
                    },
                    {
                        data: 'shop_id',
                        name: 'shop_id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'time_created',
                        name: 'time_created'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            })


            $('#shproducts').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('admin/products') }}"
                },
                columns: [{
                        data: 'product_id',
                        name: 'product_id'
                    },
                    {
                        data: 'shop_id',
                        name: 'shop_id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'in_stock',
                        name: 'in_stock'
                    },
                    {
                        data: 'time_created',
                        name: 'time_created'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            })


            $('#shstocks').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('admin/stocks') }}"
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'product_id',
                        name: 'product_id'
                    },
                    {
                        data: 'shop_id',
                        name: 'shop_id'
                    },
                    {
                        data: 'quantity_in',
                        name: 'quantity_in'
                    },
                    {
                        data: 'time_created',
                        name: 'time_created'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                ],
                order: [
                    [0, 'desc']
                ]
            })

            $('#example5').DataTable({
                "scrollX": true,
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true
                    }
                ],
            })
            $('#example6').DataTable({
                "scrollX": true
            })
            $('#example3').DataTable({
                "scrollX": true,
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true
                    }
                ],
            })

            $('#example7').DataTable({
                "scrollX": true,
                dom: '<"row"<"col-sm-4"l><"float-left"B><"float-right"f>rt<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: 'MonthActivationsReport_' + date,
                        title: 'MONTHLY ACTIVATIONS REPORT',
                        messageTop: duration
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: 'MonthlyActivationsReport_' + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: 'SMART MAUZO - OVALTECH \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: 'MONTHLY ACTIVATIONS REPORT \n',
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
                                                    text: 'Smart Mauzo Reports',
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

            $('#example2').DataTable({
                'paging': true,
                'lengthChange': false,
                'searching': false,
                'ordering': true,
                'info': true,
                'autoWidth': false
            })

            $('#shopsactivated').DataTable({
                "scrollX": true,
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
                        filename: 'ActivationsReport_' + date,
                        title: 'ACTIVATIONS REPORT',
                        messageTop: duration
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: 'ActivationsReport_' + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: 'SMART MAUZO - OVALTECH \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: 'ACTIVATIONS REPORT \n',
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
                                                    text: 'Smart Mauzo Reports',
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


            $('#agentshopsactivated').DataTable({
                "scrollX": true,
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
                        filename: 'AgetnsActivationsReport_' + date,
                        title: 'AGENTS ACTIVATIONS REPORT',
                        messageTop: duration
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: 'AgentsActivationsReport_' + date,
                        customize: function(doc) {
                            doc.content.splice(0, 1, {
                                text: [{
                                    text: 'SMART MAUZO - OVALTECH \n',
                                    bold: true,
                                    fontSize: 20
                                }, {
                                    text: 'AGENTS ACTIVATIONS REPORT \n',
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
                                                    text: 'Smart Mauzo Reports',
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


            //Auto submit on item selec
            $('#auto_submit').on('change', function(e) {
                $(this).closest('form').submit();
            });

            //Password confirmation
            //Date range picker with time picker
            $('#reportperiod').daterangepicker({
                timePicker: true,
                timePickerIncrement: 30
            })

        });
    </script>
    <script src="{{ asset('assets/js/index.js') }}"></script>
    <!--app JS-->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/plugins/notifications/js/notification-custom-script.js') }}"></script>
</body>

</html>
