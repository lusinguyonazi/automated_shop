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
                    <form class="dashform row g-3" action="{{url('sales-by-product')}}" method="POST">
                        @csrf
                        <div class=" col-md-6">
                            <select name="product_id" class="form-control select2">
                                @if(!is_null($product))
                                <option value="{{$product->id}}">{{$product->name}}</option>
                                <option value="">{{trans('navmenu.select_product')}}</option>
                                @else
                                <option value="">{{trans('navmenu.select_product')}}</option>
                                @endif
                                @foreach($products as $prod)
                                <option value="{{$prod->id}}">{{$prod->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="start_date" id="start_input" value="">
                        <input type="hidden" name="end_date" id="end_input" value="">
                        <!-- Date and time range -->
                        <div class="col-md-6">  
                            <div class="form-group">
                                <div class="input-group">
                                    <button type="button" class="btn btn-white pull-right" id="reportrange">
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
                    <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                        @if(!is_null($shop->logo_location))
                        <figure>
                            <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                        </figure>
                        @endif
                        <h5>{{ $shop->name }}</h5>
                        <h6>{{trans('navmenu.sales_by_product')}}<br> <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h6>
                    </div>
                    <div class="col-xs-12 table-responsive">
                        <table id="salesproduct" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                            <thead style="background:#E0E0E0;">
                                <tr>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                    @if($settings->is_vat_registered)
                                    <th style="text-align: center;">{{trans('navmenu.input_tax')}}</th>
                                    @endif
                                    <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                    @if($settings->is_vat_registered)
                                    <th style="text-align: center;">{{trans('navmenu.output_tax')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.vat_payable')}}</th>
                                    @endif
                                    <th style="text-align: center;">{{trans('navmenu.profit')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.percent')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $index => $sale)
                                <tr>
                                    <td>{{$sale->name}}</td>
                                    <td style="text-align: center;">@if($sale->quantity-floor($sale->quantity) >= 0.01){{$sale->quantity}}@else{{number_format($sale->quantity, 0)}}@endif</td>
                                    <td style="text-align: center;">{{number_format($sale->buying_per_unit, 0, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($sale->buying_price, 0, '.', ',')}}</td>

                                    @if($settings->is_vat_registered)
                                    <td style="text-align: center;">{{number_format($sale->input_tax)}}</td>
                                    @endif

                                    <td style="text-align: center;">{{number_format($sale->price_per_unit-$sale->discount, 0, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($sale->price-$sale->total_discount, 0, '.', ',')}}</td>

                                    @if($settings->is_vat_registered)
                                    <td style="text-align: center;">{{number_format($sale->tax_amount)}}</td>
                                    <td style="text-align: center;">{{number_format($sale->tax_amount-$sale->input_tax)}}</td>
                                    @endif

                                    <td style="text-align: center;">{{number_format((($sale->price-$sale->total_discount)-$sale->tax_amount)-($sale->buying_price-$sale->input_tax), 0, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format((((($sale->price-$sale->total_discount)-$sale->tax_amount)-($sale->buying_price-$sale->input_tax))/($total_gross_profit))*100, 2, '.', ',')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>{{trans('navmenu.total')}}</th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: center;"><strong>{{number_format($total_buying)}}</strong></th>
                                    @if($settings->is_vat_registered)
                                    <th style="text-align: center;"><strong>{{number_format($input_tax)}}</strong></th>
                                    @endif
                                    <th></th>
                                    <th style="text-align: center;"><strong>{{number_format($total_selling)}}</strong></th>
                                    @if($settings->is_vat_registered)
                                    <th style="text-align: center;"><strong>{{number_format($output_tax)}}</strong></th>
                                    <th style="text-align: center;"><strong>{{number_format($vat_payable)}}</strong></th>
                                    @endif
                                    <th style="text-align: center;"><strong>{{number_format($total_gross_profit)}}</strong></th>
                                    <th style="text-align: center;"><strong>100</strong></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection