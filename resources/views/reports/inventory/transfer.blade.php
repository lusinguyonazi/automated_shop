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
            <form class="row g-3 dashform" action="{{url('transfer-report')}}" method="POST" id="stockform">
                @csrf
                <div class="col-md-6"></div>
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                <div class="col-md-6 float-md-end">
                    <div class="input-group">
                        <button type="button" class="btn btn-white pull-right mb-3" id="reportrange">
                            <span><i class="bx bx-calendar"></i></span>
                            <i class="bx bx-caret-down"></i>
                        </button>
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-header">
                    <div class="float-md-end">
                        <button class="btn btn-success btn-sm" data-bs-toggle="card" onclick="$('#received').hide('fast'); $('#transfered').show('fast') ; ">{{trans('navmenu.stock_transfered')}}</button>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="card" onclick="$('#transfered').hide('fast') ; $('#received').show('fast'); $($.fn.dataTable.tables(true)).DataTable().columns.adjust(); ">{{trans('navmenu.stock_received')}}</button>
                    </div>
                </div>
                <div class="card-body" id="transfered">
                    <div style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                        @if(!is_null($shop->logo_location))
                        <figure>
                            <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                        </figure>
                        @endif
                        <h6>{{ $shop->name}}</h6>
                        <h5>{{trans('navmenu.transfer_report')}}</h5>
                        <p> @if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</p>
                    </div>
                    <div class="col-xs-12 table-responsive">
                        <table id="stocktransfer" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                            <thead style="background:#E0E0E0; text-align: center;">
                                <tr>
                                    <th>#</th>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.sto_no')}}</th>
                                    <th>{{trans('navmenu.destin_shop')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.source_unit_cost')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.destin_unit_cost')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.profit')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.transfer_type')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $tqty = 0; $tscost = 0; $tdcost = 0; $tprofit = 0; $total = 0; ?>
                                @foreach($transfers as $index => $transfer)
                                <?php $tqty += $transfer->quantity; $tscost += $transfer->source_unit_cost; $tdcost += $transfer->destin_unit_cost; $tprofit = 0; $total += (($transfer->destin_unit_cost-$transfer->source_unit_cost)*$transfer->quantity); ?>
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td>{{$transfer->name}}</td>
                                    <td style="text-align: center;">{{$transfer->quantity}}</td>
                                    <?php 
                                       $order =App\Models\TransferOrder::find($transfer->transfer_order_id);
                                       $destin = App\Models\Shop::find($order->destination_id);
                                       if($order->is_transfomation_transfer) {
                                          $transfer_type_en = "Transformation";
                                          $transfer_type_sw = "Kubadilisha";
                                       }else{
                                          $transfer_type_en = "Normal";
                                          $transfer_type_sw = "Kawaida";
                                       }
                                     ?>
                                    <td style="text-align: center;"><a href="{{route('transfer-orders.show', encrypt($order->id))}}"> {{ sprintf('%05d', $order->order_no)}}</a></td>
                                    <td>{{ $destin->name }}</td>
                                    <td style="text-align: center;">
                                      {{$transfer->source_unit_cost}}
                                    </td>
                                    <td style="text-align: center;">
                                      {{$transfer->destin_unit_cost}}
                                    </td>
                                    <td style="text-align: center;">
                                      {{number_format($transfer->destin_unit_cost-$transfer->source_unit_cost)}}
                                    </td>
                                    <td style="text-align: center;">
                                      {{number_format(($transfer->destin_unit_cost-$transfer->source_unit_cost)*$transfer->quantity)}}
                                    </td >
                                    @if($order->is_transfomation_transfer)
                                      <td style="text-align: center;">
                                        <span class="badge bg-warning">
                                          @if(app()->getLocale() == 'en'){{$transfer_type_en}}@else{{$transfer_type_sw}}@endif
                                        </span>
                                      </td>
                                    @else
                                    <td style="text-align: center;">
                                      <span class="badge bg-success">
                                        @if(app()->getLocale() == 'en'){{$transfer_type_en}}@else{{$transfer_type_sw}}@endif
                                      </span></td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                              <tr>
                                    <th>#</th>
                                    <th>{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{$tqty}}</th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: center;">{{number_format($tscost)}}</th>
                                    <th style="text-align: center;">{{number_format($tdcost)}}</th>
                                    <th style="text-align: center;"></th>
                                    <th style="text-align: center;">{{number_format($total)}}</th>
                                    <th style="text-align: center;"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>    
                <div class="card-body" id="received" style="display: none;">
                    <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                        @if(!is_null($shop->logo_location))
                        <figure>
                            <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                            </figure>
                        @endif
                        <h6>{{ $shop->name}}</h6>
                        <h5 class="title">{{trans('navmenu.stock_received_report')}}</h5>
                        <p> @if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</p>
                    </div>
                    <div class="col-xs-12 table-responsive">
                        <table id="stockreceiver" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                            <thead style="background:#E0E0E0; text-align: center;">
                                <tr>
                                    <th>#</th>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.sto_no')}}</th>
                                    <th>{{trans('navmenu.source_shop')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.transfer_type')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($received_transfers as $key => $rtransfer)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$rtransfer->name}}</td>
                                    <td style="text-align: center;">{{$rtransfer->quantity}}</td>
                                    <?php 
                                       $order =App\Models\TransferOrder::find($rtransfer->transfer_order_id);
                                       $source = App\Models\Shop::find($order->shop_id);
                                       if($order->is_transfomation_transfer) {
                                          $transfer_type_en = "Transformation";
                                          $transfer_type_sw = "Kubadilisha";
                                       }else{
                                          $transfer_type_en = "Normal";
                                          $transfer_type_sw = "Kawaida";
                                       }
                                     ?>
                                    <td style="text-align: center;"><a href="#"> {{ sprintf('%05d', $order->order_no)}}</a></td>
                                    <td>{{ $source->name }}</td>
                                    @if($order->is_transfomation_transfer)
                                      <td style="text-align: center;">
                                        <span class="badge bg-warning">
                                          @if(app()->getLocale() == 'en'){{$transfer_type_en}}@else{{$transfer_type_sw}}@endif
                                        </span>
                                      </td>
                                    @else
                                    <td style="text-align: center;">
                                      <span class="badge bg-success">
                                        @if(app()->getLocale() == 'en'){{$transfer_type_en}}@else{{$transfer_type_sw}}@endif
                                      </span></td>
                                    @endif
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