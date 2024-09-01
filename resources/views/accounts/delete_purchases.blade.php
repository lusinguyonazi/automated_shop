@extends('layouts.app')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure_delete') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                document.getElementById('delete-form-' + id).submit();
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
                    'success'
                )
            }
        })
    }

    function confirmRecycle(id) {
        Swal.fire({
            title: "{{ trans('navmenu.sure_restore') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.yes_restore') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                window.location.href = "{{ url('recycle-purchase/') }}/" + id;
                Swal.fire(
                    "{{ trans('navmenu.restored') }}",
                    "{{ trans('navmenu.res_succ') }}",
                    'success'
                )
            }
        })
    }

    function confirmDeletePurchase(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure_delete') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                window.location.href = "{{ url('del-recy-purchase/') }}/" + id;
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
                    'success'
                )
            }
        })
    }

    function confirmRecyclePurchase(id) {
        Swal.fire({
            title: "{{ trans('navmenu.sure_restore') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.yes_restore') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                window.location.href = "{{ url('recycle-purchase/') }}/" + id;
                Swal.fire(
                    "{{ trans('navmenu.restored') }}",
                    "{{ trans('navmenu.res_succ') }}",
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
            } else {
                dpm.style.display = 'none';
                slip.style.display = "none";
                chq.style.display = "block";
                expire.style.display = "block";
            }
        } else if (elem.value === 'Mobile Money') {
            b.style.display = 'none';
            m.style.display = 'block';
        } else {
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
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $page }}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">

        </div>
    </div>
    <!--end breadcrumb-->
    <div>
        <form class="dashform row g-3" action="{{ url('recyclebinPurchase') }}" method="GET" id="stockform">
            @csrf
            <div class="col-sm-5"></div>
            <div class="form-group col-sm-3">
                {{-- <div class="input-group mb-1"> <span class="input-group-text" id="basic-addon1"><i
                            class="bx bx-calendar"></i></span>
                    <input type="text" name="sale_date" id="saledate" placeholder="{{ trans('navmenu.pick_date') }}"
                        class="form-control form-control-sm mb-1" autocomplete="off">
                </div> --}}
            </div>
            <input type="hidden" name="start_date" id="start_input" value="">
            <input type="hidden" name="end_date" id="end_input" value="">



            <div class="form-group col-md-12">
                <div class="input-group d-flex justify-content-between justify-space-between">
                    <select id="searchCustomer" method="get" name="customer" class="form-control" value="{{old('purchase')}}">
                        {{-- <option value="{{$user->roles[0]['name']}}">{{$user->roles[0]['display_name']}}</option> --}}
                        <option value="">--- Select Supplier ---</option>
                        @foreach($recycledPurchases as $index => $purchase)
                        {{-- <option value="{{ Crypt::encrypt($purchase->id) }}">{{ App\Models\Supplier::find($purchase->supplier_id)->name}}</option> --}}
                        @endforeach
                    </select>
                    {{-- <input id="register-username" type="text" name="last_name" placeholder="Search Here . . ." class="form-control"> --}}
                    <button type="button" class="btn btn-white pull-right" id="reportrange"><span><i
                        class="bx bx-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                </div>
            </div>



            
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs nav-success" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="{{ url('recyclebin') }}">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i>
                            </div>
                            <div class="tab-title">{{ trans('navmenu.sales') }}</div>
                        </div>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="{{ url('recycle-purchases') }}" role="tab"
                        aria-selected="true">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='bx bx-export font-18 me-1'></i>
                            </div>
                            <div class="tab-title">{{ trans('navmenu.purchases') }}</div>
                        </div>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="{{ url('recycle-expenses') }}">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='bx bx-list-check font-18 me-1'></i>
                            </div>
                            <div class="tab-title">{{ trans('navmenu.expenses') }}</div>
                        </div>
                    </a>
                </li>
            </ul>

        </div>
    </div>



    <div class="row">
        <div class="col-md-12 d-flex justify-content-end">
            <form action="{{ url('empty-recycle-purchases') }}" method="POST" id="empty-recycle-sales">
                @csrf
                <button type="submit" class="btn btn-outline-danger " id="submitdr"> <i class="bx bx-trash"></i> Empty Purchases's Recyclebin</button>
            </form>
        </div>
    </div>



    <div class="tab-content py-3">
        <div class="tab-pane fade show active" id="purchases" role="tabpanel">
            <div class="table-responsive">
                <form id="multiple-purchase-recycle-form" action="{{ url('del-multiple-recycle-purchase') }}" method="POST">
                    @csrf
                    <table id="" class="table table-responsive table-striped display nowrap "
                        style="width: 100%;">
                        <thead>
                            <tr>
                               
                                <th></th>
                                <th>#</th>
                                {{-- <th>{{ trans('navmenu.seller') }}</th> --}}
                                <th>{{ trans('navmenu.purchase_date') }}</th>
                                <th>{{ trans('navmenu.supplier') }}</th>
                                <th>{{ trans('navmenu.grn_no') }}</th>
                                <th>{{ trans('navmenu.invoice_no') }}</th>
                                <th>{{ trans('navmenu.amount') }}</th>
                                <th>{{ trans('navmenu.amount_paid') }}</th>
                                <th>{{ trans('navmenu.unpaid') }}</th>
                                <th>{{ trans('navmenu.del_by') }}</th>
                                <th>{{ trans('navmenu.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($recycledPurchases as $index => $purchase)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="id[]" value="{{ Crypt::encrypt($purchase->id) }}">
                                    </td>
                                    <td>{{ $index + 1 }}</td>
                                    {{-- <td>{{ $sale->first_name }}</td> --}}
                                    <td>{{ date('d-m-Y', strtotime($purchase->time_created)) }}</td>
                                    @if (!is_null($purchase->supplier_id) && !is_null(App\Models\Supplier::find($purchase->supplier_id)))
                                        <td>{{ App\Models\Supplier::find($purchase->supplier_id)->name }}
                                        </td>
                                    @else
                                        <td>{{ trans('navmenu.unknown') }}
                                        </td>
                                    @endif

                                    <td>{{ sprintf('%04d', $purchase->grn_no) }}
                                    </td>
                                    <td>{{ $purchase->invoice_no }}</td>
                                    <td>{{ number_format($purchase->total_amount) }}</td>
                                    <td>{{ number_format($purchase->amount_paid) }}</td>
                                    <td>{{ number_format($purchase->total_amount - $purchase->amount_paid) }}</td>
                                    <td>{{ $purchase->del_by }} </td>
                                    {{-- <td>{{ $purchase->created_at }}</td> --}}
                                    <td>

                                        <a href="#" class="button"
                                            onclick="confirmRecyclePurchase('<?php echo Crypt::encrypt($purchase->id); ?>')">
                                            <i class="fa fa-recycle"></i> Restore
                                        </a> | <a href="#" class="button"
                                            onclick="confirmDeletePurchase('<?php echo Crypt::encrypt($purchase->id); ?>')"><i class="fa fa-trash"
                                                style="color: red;"></i> Delete Parmanently</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button id="submitRecyclePurchases"
                                class="btn btn-danger">{{ trans('navmenu.delete_selected') }}</button>

                                <button id="submitRecyclePurchases"
                                class="btn btn-primary">{{ trans('navmenu.restore_selected') }}</button>
                        </form>

                    <div class="row">
                        {{ $recycledPurchases->links() }}
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">�</span></button>
                    <h4 class="modal-title" id="myModalLabel">{{ trans('navmenu.add_payment') }}</h4>
                </div>
                <form class="form" method="POST" action="{{ route('sale-payments.store') }}">
                    @csrf
                    <input type="hidden" name="an_sale_id" id="id_hide">
                    <div class="modal-body">

                        <div class="form-group col-md-4">
                            <label>{{ trans('navmenu.pay_date') }}</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" name="pay_date" id="pay_date"
                                    placeholder="{{ trans('navmenu.pick_date') }}" class="form-control" required>

                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label class="label-control">{{ trans('navmenu.amount_paid') }} <span
                                    style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" name="amount" required
                                placeholder="{{ trans('navmenu.hnt_amount_paid') }}" class="form-control">
                        </div>




                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn btn-success">{{ trans('navmenu.btn_save') }}</button>
                        <button type="button" class="btn btn-warning"
                            data-dismiss="modal">{{ trans('navmenu.btn_cancel') }}</button>
                            
                            
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        var $min = document.querySelector('[name="pay_date"]'),
            $max = document.querySelector('[name="sale_date"]');


        $min.DatePickerX.init({
            mondayFirst: true,
            // minDate    : new Date(),
            format: 'yyyy-mm-dd',
            maxDate: new Date()
        });

        $max.DatePickerX.init({
            mondayFirst: true,
            format: 'yyyy-mm-dd',
            // minDate    : new Date(),
            maxDate: new Date()
        });
    });
</script>
