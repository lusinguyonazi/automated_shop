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
                    <form class="dashform row g-3" action="{{url('sales-by-service')}}" method="POST">
                        @csrf
                        <div class="col-md-6">
                            <select name="service_id" class="form-control select2">
                                @if(!is_null($service))
                                <option value="{{$service->id}}">{{$service->name}}</option>
                                <option value="">{{trans('navmenu.select_service')}}</option>
                                @else
                                <option value="">{{trans('navmenu.select_service')}}</option>
                                @endif
                                @foreach($services as $serv)
                                <option value="{{$serv->id}}">{{$serv->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="start_date" id="start_input" value="">
                        <input type="hidden" name="end_date" id="end_input" value="">
                        <!-- Date and time range -->
                        <div class=" col-md-6">
                            <div class="input-group">
                                <button type="button" class="btn btn-white pull-right" id="reportrange">
                                <span><i class="bx bx-calendar"></i></span>
                                <i class="bx bx-caret-down"></i>
                                </button>
                            </div>
                        </div>
                        <!-- /.form group -->
                    </form>
                </div>
                <div class="card-body">
                    <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                        @if(!is_null($shop->logo_location))
                        <figure>
                            <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                        </figure>
                        @endif
                        <h5>{{ $shop->name }}</h5>
                        <h6>{{trans('navmenu.sales_by_service')}} <br><b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h6>
                    </div>
                    <div class="col-xs-12 table-responsive">
                        <table id="salesservice" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                            <thead style="background:#E0E0E0;">
                                <tr>
                                    <th>{{trans('navmenu.service')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $index => $sale)
                                <tr>
                                    <td>{{$sale->name}}</td>
                                    <td style="text-align: center;">{{$sale->quantity}}</td>
                                    <td style="text-align: center;">{{number_format($sale->price-$sale->discount, 0, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($sale->total-$sale->total_discount, 0, '.', ',')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>{{trans('navmenu.total')}}</th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: center;"><strong>{{number_format($total_selling)}}/=</strong></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection