@extends('layouts.app')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('/home')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        
        <div class="col-md-11 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr/>
            <form class="dashform row g-3" action="{{url('debts-report')}}" method="POST">
                @csrf
                <div class="col-md-6"></div>
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                <!-- Date and time range -->
                <div class="col-md-6">
                    <div class="input-group">
                        <button type="button" class="btn btn-white pull-right" id="reportrange">
                            <span><i class="bx bx-calendar"></i></span>
                            <i class="bx bx-caret-down"></i>
                        </button>
                    </div>
                </div>
                <!-- /.form group -->
            </form>
            <div class="card">
                <div class="card-body">
                        <ul class="nav nav-tabs nav-success" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#debtors" role="tab" aria-selected="true">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-list-minus font-18 me-1'></i>
                                        </div>
                                        <div class="tab-title">{{trans('navmenu.debtors')}}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#debtors-total" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i>
                                        </div>
                                        <div class="tab-title">{{trans('navmenu.debtors_total')}}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="{{ url('aging-report')}}" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-list-check font-18 me-1'></i>
                                        </div>
                                        <div class="tab-title">{{trans('navmenu.debtors-aging-report')}}</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="debtors" role="tabpanel">
                            <div class="row">
                                <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                                    @if(!is_null($shop->logo_location))
                                    <figure>
                                        <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                                    </figure>
                                    @endif
                                    <h4>{{$shop->name}}</h4>
                                    <h5>{{trans('navmenu.debt_report')}} <br><b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 table-responsive">
                                    <table id="debts" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                        <thead style="background:#E0E0E0;">
                                            <tr>
                                                <th>{{trans('navmenu.saledate')}}</th>@if($settings->is_school)
                                                <th>{{trans('navmenu.student_name')}}</th>
                                                <th>{{trans('navmenu.grade')}}</th>
                                                <th>{{trans('navmenu.year_of_study')}}</th>
                                                @else
                                                <th>{{trans('navmenu.customer_name')}}</th>
                                                @endif
                                                <th>{{trans('navmenu.phone_number')}}</th>
                                                <th>{{trans('navmenu.sale_amount')}}</th>
                                                <th>{{trans('navmenu.discount')}}</th>
                                                <th>{{trans('navmenu.adjustments')}}</th>
                                                <th>{{trans('navmenu.total_payable')}}</th>
                                                <th>{{trans('navmenu.paid')}}</th>
                                                <th>{{trans('navmenu.unpaid')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($debts as $index => $sale)
                                            <tr>
                                                <td>{{date('d-m-Y', strtotime($sale->time_created))}}</td>
                                                @if($settings->is_school)
                                                <td><a href="{{ url('customer-account-stmt/'.encrypt($sale->customer_id)) }}">{{$sale->name}}</a></td>
                                                <td>@if(!is_null($sale->grade_id)){{App\Models\Grade::find($sale->grade_id)->name}}@endif</td>
                                                <td style="text-align: center;">{{$sale->year}}</td>
                                                @else
                                                @if($shop->subscription_type_id == 2)
                                                <td><a href="{{ url('customer-account-stmt/'.encrypt($sale->customer_id)) }}">{{$sale->name}}</a></td>
                                                @else
                                                <td><a href="{{url('sale-items/'.encrypt($sale->id))}}">{{$sale->name}}</a></td>
                                                @endif
                                                @endif
                                                <td>{{$sale->phone}}</td>
                                                <td style="text-align: center;">{{number_format($sale->sale_amount, 2, '.', ',')}}</td>
                                                <td style="text-align: center;">{{number_format($sale->sale_discount, 2, '.', ',')}}</td>
                                                <td style="text-align: center;">{{number_format($sale->adjustment, 2, '.', ',')}}</td>
                                                <td style="text-align: center;">{{number_format(($sale->sale_amount-$sale->sale_discount-$sale->adjustment), 2, '.', ',')}}</td>
                                                <td style="text-align: center;">{{number_format($sale->sale_amount_paid, 2, '.', ',')}}</td>
                                                <td style="text-align: center;">{{number_format((($sale->sale_amount-$sale->sale_discount-$sale->adjustment)-$sale->sale_amount_paid), 2, '.', ',')}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot style="background:#E0E0E0;">
                                            <tr>
                                                <th></th>
                                                @if($settings->is_school)
                                                <th>{{trans('navmenu.total')}}</th>
                                                <th></th>
                                                <th></th>
                                                @else
                                                <th>{{trans('navmenu.total')}}</th>
                                                @endif
                                                <th></th>
                                                <th style="text-align: center;">{{number_format($total_amount, 2,'.',',')}}</th>
                                                <th style="text-align: center;">{{number_format($total_discount, 2,'.',',')}}</th>
                                                <th style="text-align: center;">{{number_format($total_adjustment, 2,'.',',')}}</th>
                                                <th style="text-align: center;">{{number_format($total_amount-$total_discount-$total_adjustment, 2,'.',',')}}</th>
                                                <th style="text-align: center;">{{number_format($total_paid, 2,'.',',')}}</th>
                                                <th style="text-align: center;">{{number_format($total_debts, 2,'.',',')}}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                            
                        </div>
                        <div class="tab-pane fade" id="debtors-total" role="tabpanel">
                            <div class="row">
                                <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                                    @if(!is_null($shop->logo_location))
                                    <figure>
                                        <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                                    </figure>
                                    @endif
                                    <h4>{{$shop->name}}</h4>
                                    <h5 class="title">{{trans('navmenu.debtors_total')}} <br>
                                    <p>{{$reporttime}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 table-responsive">
                                    <table id="totaldebts" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                        <thead style="background:#E0E0E0;">
                                            <tr>
                                                <th>#</th>
                                                <th>{{trans('navmenu.customer_name')}}</th>
                                                <th>{{trans('navmenu.phone_number')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.opening_balance')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.new_invoices')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($totaldebts as $index => $sale)
                                            <tr>
                                                <td>{{$index+1}}</td>
                                                <td><a href="{{ url('customer-account-stmt/'.encrypt($sale['customer_id'])) }}">{{$sale['name']}}</a></td>
                                                <td>{{$sale['phone']}}</td>
                                                <td style="text-align: center;">{{number_format($sale['opening_balance'], 2, '.', ',')}}</td>
                                                <td style="text-align: center;">{{number_format($sale['new_invoices'], 2, '.', ',')}}</td>
                                                <td style="text-align: center;">{{number_format($sale['total'], 2, '.', ',')}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot style="background:#E0E0E0;">
                                            <tr>
                                                <th></th>
                                                <th>{{trans('navmenu.total')}}</th>
                                                <th></th>
                                                <th style="text-align: center;">{{number_format($total_ob, 2, '.', ',')}}</th>
                                                <th style="text-align: center;">{{number_format($total_invoices, 2, '.', ',')}}</th>
                                                <th style="text-align: center;">{{number_format($total_ob+$total_invoices, 2, '.', ',')}}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection