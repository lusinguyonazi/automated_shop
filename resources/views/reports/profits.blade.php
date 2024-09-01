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
                    <form class="dashform row g-3" action="{{url('profits')}}" method="POST">
                        @csrf
                        <div class="col-md-6"></div>
                        <input type="hidden" name="start_date" id="start_input" value="">
                        <input type="hidden" name="end_date" id="end_input" value="">
                        <!-- Date and time range -->
                        <div class="form-group col-md-6">
                            <div class="input-group">
                                <button type="button" class="btn btn-white float-end" id="reportrange">
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
                        <h6>{{trans('navmenu.profit_report')}}<br> <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h6>
                    </div>
                    <div class="col-xs-12 table-responsive">
                        <table id="profitst" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                            <thead style="background:#E0E0E0;">
                                <tr>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.profit')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.percent')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $index => $sale)
                                <?php 
                                $return = App\Models\SaleReturn::where('sale_returns.shop_id', $shop->id)->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->whereBetween('sale_return_items.created_at', [$start_date, $end_date])->where('sale_return_items.product_id', $sale->id)->where('sale_return_items.buying_per_unit', $sale->buying_per_unit)->where('sale_return_items.price_per_unit', $sale->price_per_unit)->get();
                                ?>
                                <tr>
                                    <td>{{$sale->name}}</td>
                                    <td style="text-align: center;">{{$sale->quantity-$return->sum('quantity')}}</td>
                                    <td style="text-align: center;">{{number_format(((($sale->price-$return->sum('price'))-($sale->total_discount-$return->sum('total_discount'))-($sale->tax_amount-$return->sum('tax_amount')))-(($sale->buying_price-$return->sum('buying_price'))-($sale->input_tax*$return->sum('quantity')))), 0, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format((((($sale->price-$return->sum('price'))-($sale->total_discount-$return->sum('total_discount'))-($sale->tax_amount-$return->sum('tax_amount')))-(($sale->buying_price-$return->sum('buying_price'))-($sale->input_tax*$return->sum('quantity'))))/($total_gross_profit))*100, 2, '.', ',')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th><b>{{trans('navmenu.total')}}</b></th>
                                    <th></th>
                                    <th style="text-align: center;"><b>{{number_format($total_gross_profit)}}</b></th>
                                    <th style="text-align: center;"><b>100</b></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection