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
                    <form class="row g-3 dashform" action="{{url('sales-return-report')}}" method="POST">
                        @csrf
                        <div class="col-md-3">
                            <div class="form-group">
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
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
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
                        </div>

                        <input type="hidden" name="start_date" id="start_input" value="">
                        <input type="hidden" name="end_date" id="end_input" value="">
                        <!-- Date and time range -->
                        <div class="col-md-6">  
                            <div class="form-group">
                                <div class="input-group">
                                    <button type="button" class="btn btn-default pull-right" id="reportrange">
                                        <span><i class="fa fa-calendar"></i></span>
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                </div>
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
                            <h5>{{$shop->name}}</h5>
                            <h6>{{trans('navmenu.sales_return_report')}} <br><b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h6>
                        </div>
                    </div>
                    <!-- Table row -->
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="returns" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                <thead style="background:#E0E0E0;">
                                    <tr>
                                        <th>#</th>
                                        <th>{{trans('navmenu.seller')}}</th>
                                        <th>{{trans('navmenu.customer_name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.amount')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.discount')}}</th>
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                        @endif
                                        <th style="text-align: center;">{{trans('navmenu.return_date')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.last_updated')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($returns as $index => $return)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$return->first_name}} {{$return->last_name}}</td>
                                        <td>{{$return->name}}</td>
                                        <td style="text-align: center;">{{number_format($return->sale_return_amount)}}</td>
                                        <td style="text-align: center;">{{number_format($return->sale_return_discount)}}</td>

                                        @if($settings->is_vat_registered)
                                        <td style="text-align: center;">{{number_format($return->return_tax_amount)}}</td>
                                        @endif
                                        <td style="text-align: center;"> {{$return->created_at}} </td>
                                        <td style="text-align: center;">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $return->updated_at)->diffForHumans() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th><b>{{trans('navmenu.total')}}</b></th>
                                        <th></th>
                                        <th style="text-align: center;"><b>{{number_format($total_amount)}}</b></th>
                                        <th style="text-align: center;"><b>{{number_format($total_discount)}}</b></th>
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;"><b>{{$total_tax}}</b></th>
                                        @endif
                                        <th></th>
                                        <th></th>
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
@endsection

