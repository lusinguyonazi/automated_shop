
@extends('layouts.app')

<script>
    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_delete')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('delete-form-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function confirmDeleteSupplier(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_delete')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('delete-form-supplier'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function showModal(id) {
        $('#id_hide').val(id);
        $('#payModal').modal('show');
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
        <div class="col-md-11 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr/>
            <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-end px-3 py-3">
                        <ul class="nav nav-tabs nav-primary" role="tablist"  >
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#purchases" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-list-check font-18 me-1'></i>
                                        </div>
                                        <div class="tab-title">{{trans('navmenu.purchases')}}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="{{ url('suppliers') }}" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-outline font-18 me-1'></i>
                                        </div>
                                        <div class="tab-title">{{trans('navmenu.supplier_accounts')}}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="{{ url('purchase-aging-report') }}" >
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-left-indent font-18 me-1'></i>
                                        </div>
                                        <div class="tab-title">{{trans('navmenu.aging_report')}}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="{{ route('purchases.create') }}">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-home-alt font-18 me-1'></i>
                                        </div>
                                        <div class="tab-title">{{trans('navmenu.new_purchase')}}</div>
                                    </div>
                                </a>
                            </li>
                           
                        </ul>
                    </div>
                </div>
                <div class="tab-content py-3">
                    <div class="tab-pane fade show active table-responsive" id="purchases" role="tabpanel">
                        <table id="del-multiple" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>{{trans('navmenu.purchase_date')}}</th>
                                    <th>{{trans('navmenu.supplier')}}</th>
                                    @if($shop->subscription_type_id == 2)
                                    <th>{{trans('navmenu.grn_no')}}</th>
                                    <th>{{trans('navmenu.invoice_no')}}</th>
                                    @endif
                                    <th>{{trans('navmenu.amount')}}</th>
                                    <th>{{trans('navmenu.amount_paid')}}</th>
                                    <th>{{trans('navmenu.unpaid')}}</th>
                                    <th>{{trans('navmenu.created_at')}}</th>
                                    <th>{{trans('navmenu.actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchases as $index => $purchase)
                                
                                <tr>
                                    @if(Auth::user()->hasRole('manager'))
                                    <td>{{$purchase->id}}</td>
                                    <td>{{date('d-m-Y', strtotime($purchase->time_created))}}</td>
                                    @if(!is_null($purchase->supplier_id) && !is_null(App\Models\Supplier::find($purchase->supplier_id)))
                                    <td><a href="{{url('purchase-items/'.encrypt($purchase->id))}}">{{App\Models\Supplier::find($purchase->supplier_id)->name}}</a></td>
                                    @else
                                    <td><a href="{{url('purchase-items/'.encrypt($purchase->id))}}">{{trans('navmenu.unknown')}}</a></td>
                                    @endif
                                    @if($shop->subscription_type_id == 2)
                                    <td><a href="{{ route('purchases.show', encrypt($purchase->id))}}">{{ sprintf('%04d', $purchase->grn_no)}}</a></td>
                                    <td>{{$purchase->invoice_no}}</td>
                                    @endif
                                    <td>{{number_format($purchase->total_amount)}}</td>
                                    <td>{{number_format($purchase->amount_paid)}}</td>
                                    <td>{{number_format($purchase->total_amount-$purchase->amount_paid)}}</td>
                                    <td>{{$purchase->created_at}}</td>
                                    <td>
                                        @endif
                                        @if(!(Auth::user()->hasRole('manager')))
                                        <td>{{$purchase->id}}</td>
                                    <td>{{date('d-m-Y', strtotime($purchase->time_created))}}</td>
                                    @if(!is_null($purchase->supplier_id) && !is_null(App\Models\Supplier::find($purchase->supplier_id)))
                                    <td>{{App\Models\Supplier::find($purchase->supplier_id)->name}}</td>
                                    @else
                                    <td>{{trans('navmenu.unknown')}}</td>
                                    @endif
                                    @if($shop->subscription_type_id == 2)
                                    <td><a href="{{ route('purchases.show', encrypt($purchase->id))}}">{{ sprintf('%04d', $purchase->grn_no)}}</a></td>
                                    <td>{{$purchase->invoice_no}}</td>
                                    @endif
                                    <td>{{number_format($purchase->total_amount)}}</td>
                                    <td>{{number_format($purchase->amount_paid)}}</td>
                                    <td>{{number_format($purchase->total_amount-$purchase->amount_paid)}}</td>
                                    <td>{{$purchase->created_at}}</td>
                                    <td>@endif
                                        <a href="{{route('purchases.show' , encrypt($purchase->id))}}"><i class="bx  bx-show-alt"></i></a> | 
                                        @if($purchase->amount_paid < $purchase->total_amount)
                                        <a href="#" onclick="showModal('<?php echo $purchase->id; ?>')" data-id="{{$purchase->id}}"><i class="bx bx-money"></i></a> |@endif 
                                       @if(Auth::user()->hasRole('manager')||Auth::user()->can('edit-puurchase')) <a href="{{route('purchases.edit' , encrypt($purchase->id))}}"><i class="bx bx-edit" style="color: blue;"></i></a> |@endif
                                        @if(Auth::user()->hasRole('manager')||Auth::user()->can('delete-purchase'))
                                        <form id="delete-form-{{$index}}" method="POST" action="{{route('purchases.destroy' , encrypt($purchase->id))}}" style="display: inline;"> 
                                            @csrf
                                            @method('DELETE')
                                            <a href="javascript:;" class="text-danger" onclick=" return confirmDelete({{$index}})"><span class="bx bx-trash" aria-hidden="true" style="color: red"></span></a>
                                        </form>
                                        @endif 
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if(Auth::user()->hasRole('manager'))
                        <form id="frm-example" action="{{url('delete-multiple-purchases')}}" method="POST">
                            @csrf
                            <button id="submitButton" class="btn btn-danger ">{{trans('navmenu.delete_selected')}}</button>
                        </form>
                        @endif
                    </div>

                    <div class="tab-pane fade" id="supplier_accounts" role="tabpanel">
                        <table id="example2" class="table align-middle mb-0" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{trans('navmenu.supplier_name')}}</th>
                                    <th>{{trans('navmenu.contact_number')}}.</th>
                                    <th>{{trans('navmenu.email_address')}}</th>
                                    <th>{{trans('navmenu.address')}}</th>
                                    <th>{{trans('navmenu.created_at')}}</th>
                                    <th>{{trans('navmenu.actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($suppliers as $i => $supplier)
                                <tr>
                                    <td>{{$i+1}}</td>
                                    <td><a href="{{ route('suppliers.show', encrypt($supplier->id)) }}">{{$supplier->name}}</a></td>
                                    <td>{{$supplier->contact_no}}</td>
                                    <td>{{$supplier->email}}</td>
                                    <td>{{ $supplier->address}}</td>
                                    <td>{{$supplier->created_at}}</td>
                                    <td>
                                        <a href="{{route('suppliers.edit' , encrypt($supplier->id))}}">
                                            <i class="bx bx-edit" style="color: blue;"></i>
                                        </a>
                                        |
                                        <form id="delete-form-supplier-{{$i}}" method="POST" action="{{route('suppliers.destroy' , encrypt($supplier->id))}}" style="display: inline;"> 
                                         @csrf
                                         @method('DELETE')
                                         <a href="javascript:;" class="text-danger" onclick=" return confirmDeleteSupplier({{$i}})"><span class="bx bx-trash" aria-hidden="true" style="color: red"></span></a>
                                        </form>  
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
<div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">{{trans('navmenu.add_amount_paid')}}</h4>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="" method="POST" action="{{ route('purchase-payments.store')}}">
                @csrf
                <input type="hidden" name="purchase_id" id="id_hide">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 pt-2">
                            <label class="form-label">{{trans('navmenu.pay_date')}}</label>
                            <div class="input-group date">
                                <div class="input-group-text " id="basic-addon1">
                                    <i class="bx bx-calendar"></i>
                                </div>                                
                                <input type="text" name="pay_date" id="pay_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control"  aria-describedby="basic-addon1" required>
                                    
                            </div>
                        </div>

                        <div class="col-md-6 pt-2">
                            <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" name="amount" required placeholder="{{trans('navmenu.hnt_amount_paid')}}" class="form-control">
                        </div>

                        <div class="col-md-6 pt-2">
                            <label class="form-label">{{trans('navmenu.paid_from')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-control" name="pay_mode" required>
                                <option value="Cash">{{trans('navmenu.cash')}}</option>
                                <option value="Bank">{{trans('navmenu.bank')}}</option>
                                <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_save')}}</button>
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

<link rel="stylesheet" href="css/DatePickerX.css">

<script src="js/DatePickerX.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="pay_date"]'),
                $max = document.querySelector('[name="sale_date"]');


            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                // minDate    : new Date(),
                maxDate    : new Date()
            });
        });

    </script>