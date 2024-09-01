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
            <form class="row g-3" action="{{url('stock-reports')}}" method="POST" id="stockform">
                @csrf
                <div class="col-md-6 p-2">
                    <select name="store" class="form-control" onchange='this.form.submit();'>
                        @if(!is_null($currstore))
                        <option value="{{$currstore->id}}">{{$currstore->name}}</option>
                        @endif
                        <option value="">All Stores</option>
                        @foreach($shops as $store)
                        <option value="{{$store->id}}">{{$store->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 p-2">
                    <select name="status" class="form-control" onchange='if(this.value != "") { this.form.submit(); }'>
                        @foreach($statuses as $st)
                        @if($st['value'] == $currstatus)
                        <option selected>{{$st['value']}}</option>
                        @else
                        <option>{{$st['value']}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </form>

            <div class="card">
                <div class="card-header">
                    <div style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                        @if(!is_null($currstore))
                            @if(!is_null($currstore->logo_location))
                            <figure>
                                <img class="invoice-logo" src="{{asset('storage/logos/'.$currstore->logo_location)}}" alt="">            
                            </figure>
                            @endif
                        <h6>{{$currstore->name}}</h6>
                        @else
                        <h6>{{trans('navmenu.all_stores')}}</h6>
                        @endif
                        <h5 class="title">{{trans('navmenu.stock_status_report')}}</h5>
                        <p>{{$reporttime}}</p>
                    </div>

                    <div class="table-responsive">
                        <table id="stockstatus" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                            <thead style="background:#E0E0E0; text-align: center;">
                                <tr>
                                    <th>#</th>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    @if(is_null($currstore))
                                    @foreach($shops as $store)
                                    <th>{{$store->name}}</th>
                                    @endforeach
                                    @endif
                                    <th style="text-align: center;">{{trans('navmenu.total')}} {{trans('navmenu.in_stock')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.status')}}</th>  
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stockstatus as $index => $stock)
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td>{{$stock['name']}}</td>
                                    @if(is_null($currstore))
                                    @foreach($shops as $key => $store)
                                    <td>{{$stock[$key][$store->name]}}</td>
                                    @endforeach
                                    @endif
                                    <td style="text-align: center;">
                                    @if(is_numeric( $stock['in_stock'] ) && floor( $stock['in_stock'] ) != $stock['in_stock']) {{$stock['in_stock']}} @else {{number_format($stock['in_stock'])}} @endif
                                    </td>
                                    <td style="text-align: center;">
                                    @if($stock['status'] == 'In Stock')
                                    <span class="badge  bg-success">{{$stock['status']}}</span>
                                    @elseif($stock['status'] == 'Low Stock')
                                    <span class="badge  bg-warning">{{$stock['status']}}</span>
                                    @else
                                    <span class="badge  bg-danger">{{$stock['status']}}</span>
                                    @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection