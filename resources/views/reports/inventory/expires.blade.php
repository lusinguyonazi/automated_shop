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
                <div class="card-body">
                    <div style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                        @if(!is_null($shop->logo_location))
                        <figure>
                            <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                        </figure>
                        @endif
                        <h6>{{$shop->name}}</h6>
                        <h5>{{trans('navmenu.expiration_report')}}</h5>
                        <p>{{$reporttime}}</p>          
                    </div>
                    <div class="col-xs-12 table-responsive">
                        <table id="stockexpires" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                            <thead style="background:#E0E0E0;">
                                <tr>
                                    <th>#</th>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.remain_qty')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.purchase_date')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.expire_date')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.remain_days')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.expired')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expstocks as $index => $stock)
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td>{{$stock['name']}}</td>
                                    <td style="text-align: center;">
                                      @if(is_numeric( $stock['quantity_in'] ) && floor( $stock['quantity_in'] ) != $stock['quantity_in']) {{$stock['quantity_in']}} @else {{number_format($stock['quantity_in'])}} @endif
                                    </td>
                                    <td style="text-align: center;">
                                      @if(is_numeric( $stock['qty_expired'] ) && floor( $stock['qty_expired'] ) != $stock['qty_expired']) {{$stock['qty_expired']}} @else {{number_format($stock['qty_expired'])}} @endif
                                    </td>
                                    <td style="text-align: center;">{{number_format($stock['buying_per_unit'])}}</td>
                                    <td style="text-align: center;">{{number_format($stock['qty_expired']*$stock['buying_per_unit'])}}</td>
                                    <td style="text-align: center;">{{date('d, M Y', strtotime($stock['purchase_date']))}}</td>
                                    <td style="text-align: center;">{{date('d, M Y', strtotime($stock['expire_date']))}}</td>
                                    <td style="text-align: center;">{{$stock['numdays']}}</td>
                                    <td style="text-align: center;">{{$stock['status']}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>{{trans('navmenu.total')}}</th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: center;">{{number_format(0)}}</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>    
                </div>
            </div>
        </div>
    </div>
@endsection