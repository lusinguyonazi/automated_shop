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
    <div>
        <form class="dashform row g-3" action="{{url('f-invoices')}}" method="POST" id="stockform">
                @csrf
                <div class="col-sm-5"></div>
                <div class="form-group col-sm-3">
                    <div class="input-group mb-1"> <span class="input-group-text" id="basic-addon1"><i class="bx bx-calendar"></i></span>
                        <input type="text" name="sale_date" id="saledate" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" autocomplete="off">
                    </div>
                </div>
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                <!-- Date and time range -->
                <div class="form-group col-sm-4">
                    <div class="input-group">
                        <button type="button" class="btn btn-default pull-right" id="reportrange"><span><i class="bx bx-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                    </div>
                </div>
                <!-- /.form group -->
            </form>
    </div>

    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs nav-success" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="#manage-invoices" role="tab" aria-selected="true">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i>
                            </div>
                            <div class="tab-title">{{trans('navmenu.manage_invoice')}}</div>
                        </div>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#export-invoices" role="tab" aria-selected="false">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='bx bx-export font-18 me-1'></i>
                            </div>
                            <div class="tab-title">{{trans('navmenu.export_invoice')}}</div>
                        </div>
                    </a>
                </li>
            </ul>
            <div class="tab-content py-3">
                <div class="tab-pane fade show active" id="manage-invoices" role="tabpanel">
                    <form id="frm-example" action="{{url('delete-multiple-invoices')}}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table id="del-multiple" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                <thead style="font-weight: bold; font-size: 14;">
                                    <tr>
                                        <th>#</th>
                                        <th>{{trans('navmenu.invoice_no')}}</th>
                                        <th>{{trans('navmenu.customer_name')}}</th>
                                        <th>{{trans('navmenu.vehicle_no')}}</th>
                                        <th>{{trans('navmenu.amount')}}</th>
                                        <th>{{trans('navmenu.amount_due')}}</th>
                                        <th>{{trans('navmenu.due_date')}}</th>
                                        <th>{{trans('navmenu.created_at')}}</th>
                                        <th>{{trans('navmenu.last_updated')}}</th>
                                        <th>{{trans('navmenu.status')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $index => $invoice)
                                    <tr>
                                        <td>{{$invoice->id}}</td>
                                        <td> {{ sprintf('%04d', $invoice->inv_no)}}</td>
                                        <td><a href="{{ route('invoices.show', Crypt::encrypt($invoice->id)) }}">{{$invoice->name}}</a></td>
                                        <td>{{$invoice->vehicle_no}}</td>
                                        <td>{{number_format($invoice->sale_amount-$invoice->sale_discount, 2, '.',',')}}</td>
                                        <td>{{number_format($invoice->sale_amount-$invoice->sale_discount-$invoice->sale_amount_paid, 2, '.',',')}}</td>
                                        <td>{{$invoice->due_date}}</td>
                                        <td>{{$invoice->created_at}}</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $invoice->updated_at)->diffForHumans() }}</td>
                                        <td>{{$invoice->status}}</td>
                                        @if($invoice->status == 'Paid')
                                        <td></td>
                                        @else
                                        <td>
                                            <a href="{{ route('invoices.show', encrypt($invoice->id)) }}" title="View invoice">
                                            <i class="bx bx-detail"></i></a> | 
                                            <a href="{{url('create-dnote/'.encrypt($invoice->an_sale_id))}}" title="Create Delivery Note"><i class="bx bx-file" style="color: orange;"></i></a> |
                                            <a href="{{ route('invoices.edit', encrypt($invoice->id)) }}" title="Edit invoice"><i class="bx bx-edit" style="color: blue;"></i></a>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>  
                            </table>
                        </div>
                        <button id="submitButton" class="btn btn-danger">{{trans('navmenu.delete_selected')}}</button>
                    </form>
                </div>
                <div class="tab-pane fade" id="export-invoices" role="tabpanel">
                    <div class="table-responsive">
                        <table id="example2" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                            <thead style="font-weight: bold; font-size: 14;">
                                <tr>
                                    <th>#</th>
                                    <th>{{trans('navmenu.invoice_no')}}</th>
                                    <th>{{trans('navmenu.customer_name')}}</th>
                                    <th>{{trans('navmenu.amount')}}</th>
                                    <th>{{trans('navmenu.due_date')}}</th>
                                    <th>{{trans('navmenu.created_at')}}</th>
                                    <th>{{trans('navmenu.last_updated')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $index => $invoice)
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td style="text-align: center;"> {{ sprintf('%04d', $invoice->inv_no)}}</td>
                                    <td style="text-align: center;">{{$invoice->name}}</td>
                                    <td style="text-align: center;">{{number_format($invoice->sale_amount)}}</td>
                                    <td>{{$invoice->due_date}}</td>
                                    <td>{{$invoice->created_at}}</td>
                                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $invoice->updated_at)->diffForHumans() }}</td>
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

