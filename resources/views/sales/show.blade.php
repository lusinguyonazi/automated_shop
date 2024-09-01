@extends('layouts.app')

<script>
    function confirmDeletePayment(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href="{{url('sale-payments/destroy/')}}/"+id;
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }


    function confirmDeleteItem(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href="{{url('delete-serviceitem/')}}/"+id;
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href="{{url('delete-item/')}}/"+id;
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function validateform(form){
        if ($('$pay_date').val() == '') {
            return false;
        }
        return true;
    }

    function detailUpdate(elem) {
        var b = document.getElementById('bankdetail');
        var m = document.getElementById('mobaccount');

        var dpm = document.getElementById('deposit_mode');
        var chq = document.getElementById('cheque');
        var slip = document.getElementById('slip');
        var expire = document.getElementById('expire');
        if (elem.value === 'Bank' || elem.value === 'Cheque') {
            b.style.display = 'block';
            m.style.display = 'none';
            if (elem.value === 'Bank') {
                dpm.style.display = "block";
                slip.style.display = 'block'
                chq.style.display = 'none';
                expire.style.display = "none";
            }else{
                dpm.style.display = 'none';
                slip.style.display = "none";
                chq.style.display = "block";
                expire.style.display = "block";
            }
        }else if (elem.value === 'Mobile Money') {
            b.style.display = 'none';
            m.style.display = 'block';
        }else{
            b.style.display = 'none';
            m.style.display = 'none';
        }
    }
</script>
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
        <div class="col-xl-4 mx-auto">
            <h6 class="mb-0 text-uppercase">{{ trans('navmenu.sale_details') }}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <table class="table mb-0 table-striped" style="width: 100%; font-size: 13px;">
                        <tbody>
                            <tr>
                                <td>{{trans('navmenu.sale_amount')}}</td> 
                                <td style="text-align: right;"><b>{{number_format($sale->sale_amount, 2, '.', ',')}}</b>/=</td>
                            </tr>
                            <tr>
                                <td>{{trans('navmenu.discount')}}</td> 
                                <td style="text-align: right;"><b>{{number_format($sale->sale_discount, 2, '.', ',')}}</b>/=</td>
                            </tr>
                            <tr>
                                <td>{{trans('navmenu.adjustments')}}</td> 
                                <td style="text-align: right;"><b>{{number_format($sale->adjustment, 2, '.', ',')}}</b>/=</td>
                            </tr>
                            <tr>
                                <td>{{trans('navmenu.paid_amount')}}</td> 
                                <td style="text-align: right;"><b>{{number_format($sale->sale_amount_paid, 2, '.', ',')}}</b>/=</td>
                            </tr>
                            <tr>
                                <td>{{trans('navmenu.unpaid_amount')}}</td> 
                                <td style="text-align: right;"><b>{{number_format(($sale->sale_amount-$sale->sale_discount-$sale->adjustment)-$sale->sale_amount_paid, 2, '.', ',')}}</b>/=</td>
                            </tr>

                            @if($settings->is_vat_registered)
                            <tr>
                                <td>{{trans('navmenu.vat')}}</td> 
                                <td style="text-align: right;"><b>{{number_format($sale->tax_amount, 2, '.', ',')}}</b>/=</td>
                            </tr>
                            @endif
                            <tr>
                                <td>{{trans('navmenu.paid_by')}} </td>
                                <td style="text-align: right;"><b>
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
                                    @endif</b>
                                </td>
                            </tr>
                            <tr>
                                <td>{{trans('navmenu.comments')}}</td>
                                <td style="text-align: right;"><b>{{$sale->comments}}</b></td>
                            </tr>
                            <tr>
                                <td>{{trans('navmenu.saledate')}} </td>
                                <td style="text-align: right;"><b>{{$sale->time_created}}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    @if((($sale->sale_amount-$sale->sale_discount)-$sale->sale_amount_paid) > 0)
                    <div class="col-sm-12 text-center">
                        <a class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#payModal" data-bs-backdrop="static" data-keyboard="false" style="margin: 5px;"><b><i class="bx bx-money"></i>{{trans('navmenu.add_amount_paid')}}</b></a>
                    </div>
                    @endif
                    <div class="col-sm-12 text-center">
                        <a href="{{url('create-sale-return/'.encrypt($sale->id))}}"  class="btn btn-danger" style="margin 5px;"><i class="bx bx-file-o"></i> {{trans('navmenu.create_a_sale_return')}}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8 mx-auto">
            <h6 class="mb-0 text-uppercase">{{ trans('navmenu.sale_items') }}</h6>
            <hr>
            @if($message = Session::get('info'))
            <div class="alert alert-info border-0 bg-info alert-dismissible fade show py-2">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-dark"><i class='bx bx-info-square'></i></div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-dark">Info Alerts</h6>
                        <div class="text-dark">{{$message}}</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            @if($shop->business_type_id == 3)
            <div class="card">
                <div class="card-body">
                    <div class="d-lg-flex align-items-center mb-4 gap-3">
                        <div class="position-relative">
                            <h6 class="mb-0 text-uppercase">{{ trans('navmenu.sale_items') }}</h6>
                        </div>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-warning pull-right" data-bs-toggle="modal" data-bs-target="#servitemModal"><i class="bx bxs-cart"></i>{{trans('navmenu.add_serv_sale_item')}}</button>
                        </div>
                    </div>

                    <div class="p-4 border rounded table-responsive">
                        <table id="svitems" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 13px;">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>{{trans('navmenu.service')}}</th>
                                    <th>{{trans('navmenu.qty')}}</th>
                                    <th>{{trans('navmenu.price')}}</th>
                                    <th>{{trans('navmenu.total')}} </th>
                                    <th>{{trans('navmenu.discount')}}</th>
                                    @if($settings->is_vat_registered)
                                    <th>{{trans('navmenu.vat')}} </th>
                                    @endif
                                    <!-- <th>Date stored</th> -->
                                    <th>{{trans('navmenu.actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($serv_items as $index => $item)
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td>{{$item->name}}</td>
                                    <td>{{$item->no_of_repeatition}}</td>
                                    <td>{{number_format($item->price, 2, '.', ',')}}</td>
                                    <td>{{number_format($item->total, 2, '.', ',')}}</td>
                                    <td>{{number_format($item->total_discount, 2, '.', ',')}}</td>
                                    @if($settings->is_vat_registered)
                                    <td>{{number_format($item->tax_amount, 2, '.', ',')}}</td>
                                    @endif
                                    <!-- <td>{{$item->created_at}}</td> -->
                                    <td>

                                        @if($sale->created_at > \Carbon\Carbon::now()->subDays(180)->toDateTimeString())
                                        <a href="{{route('service-items.edit', encrypt($item->id))}}">
                                            <i class="bx bx-edit" style="color: blue;"></i>
                                        </a>
                                        <a href="#" onclick="confirmDeleteItem('<?php echo encrypt($item->id); ?>')">
                                            <i class="bx bx-trash" style="color: red;"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>  
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @elseif($shop->business_type_id == 4)
            <div class="card">
                <div class="card-body">
                    <div class="d-lg-flex align-items-center mb-4 gap-3">
                        <div class="position-relative">
                            <h6 class="mb-0 text-uppercase">{{ trans('navmenu.sale_items') }}</h6>
                        </div>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-warning pull-right" data-bs-toggle="modal" data-bs-target="#servitemModal" style="margin-left: 5px;"><i class="bx bx-cart"></i> {{trans('navmenu.add_serv_sale_item')}}</button>
                            <button type="button" class="btn btn-success pull-right" data-bs-toggle="modal" data-bs-target="#itemModal"><i class="bx bx-cart"></i> {{trans('navmenu.add_sale_item')}}</button>
                        </div>
                    </div>
                    <div class="p-4 border rounded">
                        @if($sale_items->count() > 0)
                        <div class="table-responsive">
                            <table id="saleitems" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 13px;">
                                <thead>
                                    <th style="width: 10px">#</th>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th>{{trans('navmenu.qty')}}</th>
                                    <th>{{trans('navmenu.buying')}}</th>
                                    <th>{{trans('navmenu.total')}}</th>
                                    <th>{{trans('navmenu.selling')}}</th>
                                    <th>{{trans('navmenu.total')}} </th>
                                    <th>{{trans('navmenu.discount')}}</th>
                                    @if($settings->is_vat_registered)
                                    <th>{{trans('navmenu.vat')}} </th>
                                    @endif
                                    <th>{{trans('navmenu.actions')}}</th>
                                </thead>
                                <tbody>
                                    @foreach($sale_items as $index => $item)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$item->name}}</td>
                                        <td>{{$item->quantity_sold}}</td>
                                        <th>{{number_format($item->buying_per_unit, 2, '.', ',')}}</th>
                                        <td>{{number_format($item->buying_price, 2, '.', ',')}}</td>
                                        <td>{{number_format($item->price_per_unit, 2, '.', ',')}}</td>
                                        <td>{{number_format($item->price, 2, '.', ',')}}</td>
                                        <td>{{number_format($item->total_discount, 2, '.', ',')}}</td>
                                        @if($settings->is_vat_registered)
                                        <td>{{number_format($item->tax_amount, 2, '.', ',')}}</td>
                                        @endif
                                        <td>
                                            @if($sale->created_at > \Carbon\Carbon::now()->subDays(180)->toDateTimeString())
                                            <a href="{{ route('sale-items.edit',encrypt($item->id)) }}"><i class="bx bx-edit" style="color: blue;"></i>
                                            </a>
                                            <a href="#" onclick="confirmDelete('<?php echo encrypt($item->id); ?>')">
                                                    <i class="bx bx-trash" style="color: red;"></i>
                                            </a>
                                            @endif
                                        </td>
                                    </tr>  
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                        @if($serv_items->count() > 0)
                        <hr>
                        <div class="table-responsive">
                            <table id="servitems" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 13px;">
                                <thead>
                                    <th style="width: 10px">#</th>
                                    <th>{{trans('navmenu.service')}}</th>
                                    <th>{{trans('navmenu.qty')}}</th>
                                    <th>{{trans('navmenu.price')}}</th>
                                    <th>{{trans('navmenu.total')}} </th>
                                    <th>{{trans('navmenu.discount')}}</th>
                                    @if($settings->is_vat_registered)
                                    <th>{{trans('navmenu.vat')}} </th>
                                    @endif
                                    <!-- <th>Date stored</th> -->
                                    <th>{{trans('navmenu.actions')}}</th>
                                </thead>
                                <tbody>
                                    @foreach($serv_items as $index => $item)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$item->name}}</td>
                                        <td>{{$item->no_of_repeatition}}</td>
                                        <td>{{number_format($item->price, 2, '.', ',')}}</td>
                                        <td>{{number_format($item->total, 2, '.', ',')}}</td>
                                        <td>{{number_format($item->total_discount, 2, '.', ',')}}</td>
                                        @if($settings->is_vat_registered)
                                        <td>{{number_format($item->tax_amount, 2, '.', ',')}}</td>
                                        @endif
                                        <!-- <td>{{$item->created_at}}</td> -->
                                        <td>

                                            @if($sale->created_at > \Carbon\Carbon::now()->subDays(180)->toDateTimeString())
                                            <a href="{{route('service-items.edit', encrypt($item->id))}}">
                                                <i class="bx bx-edit" style="color: blue;"></i>
                                            </a>
                                            <a href="#" onclick="confirmDeleteItem('<?php echo encrypt($item->id); ?>')">
                                                <i class="bx bx-trash" style="color: red;"></i>
                                            </a>
                                            @endif
                                        </td>
                                    </tr>  
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @else
            <div class="card">
                <div class="card-body">
                    <div class="d-lg-flex align-items-center mb-4 gap-3">
                        <div class="position-relative">
                            <h6 class="mb-0 text-uppercase">{{ trans('navmenu.sale_items') }}</h6>
                        </div>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-success pull-right" data-bs-toggle="modal" data-bs-target="#itemModal"><i class="bx bx-cart"></i> {{trans('navmenu.add_sale_item')}}</button>
                        </div>
                    </div>
                    <div class="p-4 border rounded table-responsive">
                        <table id="items" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 13px;">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th>{{trans('navmenu.qty')}}</th>
                                    <th>{{trans('navmenu.buying')}}</th>
                                    <th>{{trans('navmenu.total')}}</th>
                                    <th>{{trans('navmenu.selling')}}</th>
                                    <th>{{trans('navmenu.total')}} </th>
                                    <th>{{trans('navmenu.discount')}}</th>
                                    @if($settings->is_vat_registered)
                                    <th>{{trans('navmenu.vat')}} </th>
                                    @endif
                                    <th>{{trans('navmenu.actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale_items as $index => $item)
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td>{{$item->name}}</td>
                                    <td>{{$item->quantity_sold}}</td>
                                    <th>{{number_format($item->buying_per_unit, 2, '.', ',')}}</th>
                                    <td>{{number_format($item->buying_price, 2, '.', ',')}}</td>
                                    <td>{{number_format($item->price_per_unit, 2, '.', ',')}}</td>
                                    <td>{{number_format($item->price, 2, '.', ',')}}</td>
                                    <td>{{number_format($item->total_discount, 2, '.', ',')}}</td>
                                    @if($settings->is_vat_registered)
                                    <td>{{number_format($item->tax_amount, 2, '.', ',')}}</td>
                                    @endif
                                    <!-- <td>{{$item->created_at}}</td> -->
                                    <td>
                                        @if($sale->created_at > \Carbon\Carbon::now()->subDays(180)->toDateTimeString())
                                        <a href="{{ route('sale-items.edit', encrypt($item->id)) }}"><i class="bx bx-edit" style="color: blue;"></i></a>
                                        <a href="#" onclick="confirmDelete('<?php echo encrypt($item->id); ?>')"><i class="bx bx-trash" style="color: red;"></i></a>
                                        @endif
                                    </td>
                                </tr>  
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="d-lg-flex align-items-center mb-4 gap-3">
                        <div class="position-relative">
                            <h6 class="mb-0 text-uppercase">{{trans('navmenu.sale_payments')}}</h6>
                        </div>
                    </div>
                    <div class="p-4 border rounded table-responsive">
                        <table id="example" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>{{trans('navmenu.pay_date')}}</th>
                                    <th>{{trans('navmenu.amount')}}</th>
                                    <th>{{trans('navmenu.pay_mode')}}</th>
                                    <th>{{trans('navmenu.record_at')}}</th>
                                    <th>{{trans('navmenu.actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $index => $payment)
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td>{{$payment->pay_date}}</td>
                                    <td>{{number_format($payment->amount, 2, '.', ',')}}</td>
                                    <td>
                                        @if($payment->pay_mode == 'Cash')
                                          @if(app()->getLocale() == 'en')
                                            {{$payment->pay_mode}}
                                          @else
                                          {{trans('navmenu.cash')}}
                                          @endif
                                        @elseif($payment->pay_mode == 'Mobile Money')
                                          @if(app()->getLocale() == 'en')
                                            {{$payment->pay_mode}}
                                          @else
                                            {{trans('navmenu.mobilemoney')}}
                                          @endif
                                        @elseif($payment->pay_mode == 'Cheque')
                                          @if(app()->getLocale() == 'en')
                                        {{$payment->pay_mode}}
                                          @else
                                            {{trans('navmenu.cheque')}}
                                          @endif
                                        @elseif($payment->pay_mode == 'Bank')
                                          @if(app()->getLocale() == 'en')
                                            {{$payment->pay_mode}}
                                          @else
                                            {{trans('navmenu.bank')}}
                                          @endif                           
                                        @endif
                                    </td>
                                    <td>{{$payment->created_at}}</td>
                                    <td>
                                        @if(is_null($payment->trans_id))
                                        <a href="{{ route('sale-payments.edit', encrypt($payment->id))}}">
                                            <i class="bx bx-edit" style="color: blue;"></i>
                                        </a>
                                        <a href="#" onclick="confirmDeletePayment('<?php echo encrypt($payment->id); ?>')">
                                            <i class="bx bx-trash" style="color: red;"></i>
                                        </a>  
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

    <!-- Modal -->
    <div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{trans('navmenu.add_payment')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ route('sale-payments.store')}}">
                    <div class="modal-body row">
                        @csrf
                        <input type="hidden" name="an_sale_id" value="{{$sale->id}}">
                        <div class="form-group col-md-6">
                            <label class="form-label">{{trans('navmenu.pay_date')}}</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>                                
                                <input type="text" name="pay_date" id="pay_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" step="any" name="amount" required placeholder="{{trans('navmenu.hnt_amount_paid')}}" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label">{{trans('navmenu.pay_mode')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-control" name="pay_mode" onchange="detailUpdate(this)" required>
                                <option value="Cash">{{trans('navmenu.cash')}}</option>
                                @if($shop->subscription_type_id == 2)
                                <option value="Cheque">{{trans('navmenu.cheque')}}</option>
                                @endif
                                <option value="Bank">{{trans('navmenu.bank')}}</option>
                                <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                            </select>
                        </div>
                        
                        @if($shop->subscription_type_id ==2)
                        <div id="bankdetail" style="display: none;">
                            <div class="form-group col-md-6" id="deposit_mode" style="display: none;">
                                <label class="form-label">Deposit Mode</label>
                                <select name="deposit_mode" class="form-control">
                                    <option>Direct Deposit</option>
                                    <option>Bank Transfer</option>
                                </select>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label class="form-label">Bank Name </label>
                                <select name="bank_name" class="form-control">

                                    <option value="">Select Bank Account</option>
                                    @foreach($bdetails as $detail)
                                    <option value="{{$detail->id}}">{{$detail->bank_name}} - {{$detail->branch_name}}</option>
                                    @endforeach
                                </select>                          
                            </div>

                            <div class="form-group col-md-6" id="cheque" style="display: none;">
                                <label class="form-label">Cheque Number</label>
                                <input id="name" type="text" name="cheque_no" placeholder="Please enter Cheque Number" class="form-control">
                            </div>

                            <div class="form-group col-md-6" id="expire" style="display: none;">
                                <label class="form-label">Expire Date</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div> 
                                    <input id="name" type="text" name="expire_date" placeholder="Please enter Expire Date" class="form-control">
                                </div>
                            </div>

                            <div class="form-group col-md-6" id="slip" style="display: none;">
                                <label class="form-label">Credit Card/Bank Slip Number</label>
                                <input id="name" type="text" name="slip_no" placeholder="Please enter Credit Card/Bank Slip number" class="form-control">
                            </div>
                        </div>
                        <div id="mobaccount" style="display: none;">
                            <div class="form-group col-md-6">
                                <label class="form-label">Mobile Money Operator </label>
                                <select class="form-control" name="operator">
                                    <option value="">Select Operator</option>
                                    <option>AirtelMoney</option>
                                    <option>EzyPesa</option>
                                    <option>M-Pesa</option>
                                    <option>TigoPesa</option>
                                    <option>HaloPesa</option>
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="form-group col-md-12">
                            <label class="form-label">{{trans('navmenu.comments')}}</label>
                            <textarea class="form-control" name="comments" placeholder="Enter Comments (Optional)...."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                        <button type="submit" class="btn btn-primary">{{trans('navmenu.btn_save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="servitemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{trans('navmenu.add_serv_sale_item')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ route('service-items.store') }}">
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" name="an_sale_id" value="{{$sale->id}}">
                    <div class="form-group col-md-6">
                        <label>{{trans('navmenu.service')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <select class="form-select select2" id="serv-select" name="service_id" required style="width: 100%;">
                            <option value="">Select Service</option>
                            @foreach($services as $key => $service)
                            <option value="{{$service->id}}">{{$service->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">{{trans('navmenu.quantity')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="number" step="any" min="0" name="quantity" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    <button type="submit" class="btn btn-primary">{{trans('navmenu.btn_save')}}</button>
                </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true" style="overflow: hidden;">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{trans('navmenu.add_sale_item')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ route('sale-items.store') }}">
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" name="an_sale_id" value="{{$sale->id}}">
                    <div class="form-group col-md-6">
                        <label>{{trans('navmenu.product_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <select class="form-select select2" id="my-select" name="product_id" required style="width: 100%;">
                            <option value="">Select Product</option>
                            @foreach($products as $key => $product)
                            <option value="{{$product->id}}">{{$product->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">{{trans('navmenu.quantity')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="number" step="any" min="0" name="quantity_sold" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    <button type="submit" class="btn btn-primary">{{trans('navmenu.btn_save')}}</button>
                </div>
                </form>
            </div>
        </div>
    </div>

@endsection
<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">

<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="pay_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>