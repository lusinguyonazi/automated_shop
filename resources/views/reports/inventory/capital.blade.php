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
                        <h5>{{trans('navmenu.current_stock_capital')}} </h5>
                        <p>{{$reporttime}}</p>          
                    </div>
                    <div class="table-responsive">
                        <table id="stockcapital" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                            <thead style="background:#E0E0E0;">
                                <tr>
                                    <th>#</th>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.selling_price')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.expected_profit')}}</th>
                                    @if($settings->retail_with_wholesale)
                                    <th style="text-align: center;">{{trans('navmenu.wholesale_price')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.expected_profit')}}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $index => $product)
                                <?php 
                                    $lateststock = null;
                                    if (App\Models\Stock::where('product_id', $product->id)->where('shop_id', $shop->id)->where('is_deleted', false)->count() > 0) {
                                      $lateststock = App\Models\Stock::where('product_id', $product->id)->where('shop_id', $shop->id)->where('is_deleted', false)->latest()->first();
                                    }
                                ?>
                                @if(!is_null($lateststock) && $product->in_stock > $lateststock->quantity_in && $product->buying_per_unit != $lateststock->buying_per_unit)
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td>{{$product->name}}</td>
                                    <td style="text-align: center;">
                                    @if(is_numeric( $lateststock->quantity_in ) && floor( $lateststock->quantity_in ) != $lateststock->quantity_in) {{$lateststock->quantity_in}} @else {{number_format($lateststock->quantity_in)}} @endif
                                    </td>
                                    <td style="text-align: center;">{{number_format($lateststock->buying_per_unit)}}</td>
                                    <td style="text-align: center;">{{(number_format($lateststock->quantity_in*$lateststock->buying_per_unit))}}</td>
                                    <td style="text-align: center;">{{number_format($product->price_per_unit)}}</td>
                                    <td style="text-align: center;">{{(number_format($lateststock->quantity_in*$product->price_per_unit))}}</td>
                                    <td style="text-align: center;">
                                    {{(number_format(($lateststock->quantity_in*$product->price_per_unit)-($lateststock->quantity_in*$lateststock->buying_per_unit)))}}
                                    </td>
                                    @if($settings->retail_with_wholesale)
                                    <td style="text-align: center;">{{number_format($product->wholesale_price)}}</td>
                                    <td style="text-align: center;">{{(number_format($lateststock->quantity_in*$product->wholesale_price))}}</td>
                                    <td style="text-align: center;">
                                    {{(number_format(($lateststock->quantity_in*$product->wholesale_price)-($lateststock->quantity_in*$lateststock->buying_per_unit)))}}
                                    </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td>{{$product->name}}</td>
                                    <td style="text-align: center;">
                                      @if(is_numeric( $product->in_stock-$lateststock->quantity_in ) && floor( $product->in_stock-$lateststock->quantity_in ) != $product->in_stock-$lateststock->quantity_in) 
                                        {{$product->in_stock-$lateststock->quantity_in}} @else {{number_format($product->in_stock-$lateststock->quantity_in)}} 
                                      @endif
                                    </td>
                                    <td style="text-align: center;">{{number_format($product->buying_per_unit)}}</td>
                                    <td style="text-align: center;">{{(number_format(($product->in_stock-$lateststock->quantity_in)*$product->buying_per_unit))}}</td>
                                    <td style="text-align: center;">{{number_format($product->price_per_unit)}}</td>
                                    <td style="text-align: center;">{{(number_format(($product->in_stock-$lateststock->quantity_in)*$product->price_per_unit))}}</td>
                                    <td style="text-align: center;">{{(number_format((($product->in_stock-$lateststock->quantity_in)*$product->price_per_unit)-(($product->in_stock-$lateststock->quantity_in)*$product->buying_per_unit)))}}</td>
                                    @if($settings->retail_with_wholesale)
                                    <td style="text-align: center;">{{number_format($product->wholesale_price)}}</td>
                                    <td style="text-align: center;">{{(number_format(($product->in_stock-$lateststock->quantity_in)*$product->wholesale_price))}}</td>
                                    <td style="text-align: center;">{{(number_format((($product->in_stock-$lateststock->quantity_in)*$product->wholesale_price)-(($product->in_stock-$lateststock->quantity_in)*$product->buying_per_unit)))}}</td>
                                    @endif
                                </tr>
                                @else
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td>{{$product->name}}</td>
                                    <td style="text-align: center;">
                                      @if(is_numeric( $product->in_stock ) && floor( $product->in_stock ) != $product->in_stock) {{$product->in_stock}} @else {{number_format($product->in_stock)}} @endif
                                    </td>
                                    <td style="text-align: center;">{{number_format($product->buying_per_unit)}}</td>
                                    <td style="text-align: center;">{{(number_format($product->in_stock*$product->buying_per_unit))}}</td>
                                    <td style="text-align: center;">{{number_format($product->price_per_unit)}}</td>
                                    <td style="text-align: center;">{{(number_format($product->in_stock*$product->price_per_unit))}}</td>
                                    <td style="text-align: center;">{{(number_format(($product->in_stock*$product->price_per_unit)-($product->in_stock*$product->buying_per_unit)))}}</td>
                                    @if($settings->retail_with_wholesale)
                                    <td style="text-align: center;">{{number_format($product->wholesale_price)}}</td>
                                    <td style="text-align: center;">{{(number_format($product->in_stock*$product->wholesale_price))}}</td>
                                    <td style="text-align: center;">{{(number_format(($product->in_stock*$product->wholesale_price)-($product->in_stock*$product->buying_per_unit)))}}</td>
                                    @endif
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>{{trans('navmenu.total')}}</th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: center;">{{number_format($total)}}</th>
                                    <th></th>
                                    <th style="text-align: center;">{{number_format($total_sales)}}</th>
                                    <th style="text-align: center;">{{number_format($total_profit)}}</th>
                                    @if($settings->retail_with_wholesale)
                                    <th></th>
                                    <th style="text-align: center;">{{number_format($total_wholesales)}}</th>
                                    <th style="text-align: center;">{{number_format($total_ws_profit)}}</th>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    </div>    
                </div>
            </div>
        </div>
    </div>
@endsection