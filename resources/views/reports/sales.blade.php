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
            <div class="card">
                <div class="card-header">
                    <form class="row g-3 dashform" action="{{url('sales-report')}}" method="POST">
                        @csrf
                        <div class="col-md-3">
                            <select name="user_id" class="form-control select2">
                                @if(!is_null($user))
                                <option value="{{$user->id}}">{{$user->first_name}}</option>
                                <option value="">{{trans('navmenu.select_by_seller')}}</option>
                                @else
                                <option value="">{{trans('navmenu.select_by_seller')}} </option>
                                @endif
                                @foreach($users as $user1)
                                <option value="{{$user1->id}}">{{$user1->first_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="customer_id" class="form-control select2">
                                @if(!is_null($customer))
                                <option value="{{$customer->id}}">{{$customer->name}}</option>
                                <option value="">{{trans('navmenu.select_by_customer')}}</option>
                                @else
                                <option value="">{{trans('navmenu.select_by_customer')}} </option>
                                @endif
                                @foreach($customers as $cust)
                                <option value="{{$cust->id}}">{{$cust->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($settings->is_school)
                        <div class="col-md-3">
                            <select name="grade_id" class="form-control select2">
                                @if(!is_null($grade))
                                <option value="{{$grade->id}}">{{$grade->name}}</option>
                                <option value="">{{trans('navmenu.select_grade')}}</option>
                                @else
                                <option value="">{{trans('navmenu.select_grade')}} </option>
                                @endif
                                @foreach($grades as $grd)
                                <option value="{{$grd->id}}">{{$grd->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="year" class="form-control select2">
                                @if(!is_null($year))
                                <option>{{$year}}</option>
                                <option value="">{{trans('navmenu.select_year')}}</option>
                                @else
                                <option value="">{{trans('navmenu.select_year')}} </option>
                                @endif
                                @foreach($years as $yr)
                                <option>{{$yr->year}}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <input type="hidden" name="start_date" id="start_input" value="">
                        <input type="hidden" name="end_date" id="end_input" value="">
                        <!-- Date and time range -->
                        <div class="col-md-6">  
                            <div class="input-group">
                                <button type="button" class="btn btn-white pull-right" id="reportrange"><span><i class="bx bx-calendar"></i></span>
                                  <i class="bx bx-caret-down"></i>
                                </button>
                            </div>
                            <!-- /.form group -->
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                            @if(!is_null($shop->logo_location))
                            <figure>
                                <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                            </figure>
                            @endif
                            <h6>{{$shop->name}}</h6>
                            <h6>{{trans('navmenu.sales_report')}} <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h6>
                        </div>
                    </div>
                    <!-- Table row -->
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="sales" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                <thead style="background:#E0E0E0;">
                                    <tr>
                                        <th>#</th>
                                        <th>{{trans('navmenu.seller')}}</th>
                                        @if($settings->is_school)
                                        <th>{{trans('navmenu.student_name')}}</th>
                                        <th>{{trans('navmenu.grade')}}</th>
                                        <th>{{trans('navmenu.year_of_study')}}</th>
                                        @else
                                        <th>{{trans('navmenu.customer_name')}}</th>
                                        @endif
                                        <th style="text-align: center;">{{trans('navmenu.sale_amount')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.discount')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.adjustment')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.total_payable')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.paid')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.unpaid')}}</th>
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                        @endif
                                        <th style="text-align: center;">{{trans('navmenu.paid_by')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.time_paid')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.status')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.saledate')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.last_updated')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sales as $index => $sale)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$sale->first_name}} {{$sale->last_name}}</td>
                                        @if($settings->is_school)
                                        <td><a href="{{url('sale-items/'.encrypt($sale->id))}}">{{$sale->name}}</a></td>
                                        <td>@if(!is_null($sale->grade_id)){{App\Models\Grade::find($sale->grade_id)->name}}@endif</td>
                                        <td style="text-align: center;">{{$sale->year}}</td>
                                        @else
                                        <td><a href="{{url('sale-items/'.encrypt($sale->id))}}">{{$sale->name}}</a></td>
                                        @endif
                          
                                        <td style="text-align: center;">{{number_format($sale->sale_amount, 2, '.',',')}}</td>
                                        <td style="text-align: center;">{{number_format($sale->sale_discount, 2, '.',',')}}</td>
                                        <td style="text-align: center;">{{number_format($sale->adjustment, 2, '.',',')}}</td>
                                        <td style="text-align: center;">{{number_format($sale->sale_amount-$sale->sale_discount-$sale->adjustment, 2, '.',',')}}</td>
                                        <td style="text-align: center;">{{number_format($sale->sale_amount_paid, 2, '.',',')}}</td>
                                        <td style="text-align: center;">{{number_format(($sale->sale_amount-$sale->sale_discount-$sale->adjustment)-$sale->sale_amount_paid, 2, '.',',')}}</td>
                                        @if($settings->is_vat_registered)
                                        <td style="text-align: center;">{{number_format($sale->tax_amount, 2, '.',',')}}</td>
                                        @endif
                                        <td style="text-align: center;">
                                          @if($sale->pay_type == 'Cash')
                                              @if(app()->getLocale() == 'en')
                                                {{$sale->pay_type}}
                                              @else
                                              {{trans('navmenu.cash')}}
                                            @endif
                                            @elseif($sale->pay_type == 'Mobile Money')
                                              @if(app()->getLocale() == 'en')
                                                {{$sale->pay_type}}
                                              @else
                                                {{trans('navmenu.mobilemoney')}}
                                              @endif
                                            @elseif($sale->pay_type == 'Bank')
                                              @if(app()->getLocale() == 'en')
                                                {{$sale->pay_type}}
                                              @else
                                                {{trans('navmenu.bank')}}
                                              @endif                           
                                            @endif
                                        </td>
                                        <td style="text-align: center;">{{ $sale->time_paid}} </td>
                                        <td style="text-align: center;">{{$sale->status}}</td>
                                        <td style="text-align: center;"> {{$sale->time_created}} </td>
                                        <td style="text-align: center;">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sale->updated_at)->diffForHumans() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>#</th>
                                        @if($settings->is_school)
                                        <th>{{trans('navmenu.total')}}</th>
                                        <th></th>
                                        <th></th>
                                        @else
                                        <th>{{trans('navmenu.total')}}</th>
                                        @endif
                                        <th></th>
                                        <th style="text-align: center;">{{number_format($total_amount, 2,'.', ',')}}</th>
                                        <th style="text-align: center;">{{number_format($total_discount, 2,'.', ',')}}</th>
                                        <th style="text-align: center;">{{number_format($total_adjustment, 2,'.', ',')}}</th>
                                        <th style="text-align: center;">{{number_format($total_amount-$total_discount-$total_adjustment, 2,'.', ',')}}</th>
                                        <th style="text-align: center;">{{number_format($total_paid, 2,'.', ',')}}</th>
                                        <th style="text-align: center;">{{number_format($total_debts, 2,'.', ',')}}</th>
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;">{{$total_tax}}</th>
                                        @endif
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- /.col -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

