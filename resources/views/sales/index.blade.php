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
        <form class="dashform row g-3" action="{{ url('an-sales') }}" method="POST">
            @csrf
            <div class="col-sm-5"></div>
            <div class="form-group col-sm-3">
                <div class="input-group mb-1"> <span class="input-group-text" id="basic-addon1"><i
                            class="bx bx-calendar"></i></span>
                    <input type="text" name="sale_date" id="saledate" placeholder="{{ trans('navmenu.pick_date') }}"
                        class="form-control form-control-sm mb-1" autocomplete="off">
                </div>
            </div>
            <input type="hidden" name="start_date" id="start_input" value="">
            <input type="hidden" name="end_date" id="end_input" value="">
            <!-- Date and time range -->
            <div class="form-group col-sm-4">
                <div class="input-group">
                    <button type="button" class="btn btn-white pull-right" id="reportrange"><span><i
                                class="bx bx-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                </div>
            </div>
            <!-- /.form group -->
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs nav-success" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="#manage-sales" role="tab"
                        aria-selected="true">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i>
                            </div>
                            <div class="tab-title">{{ trans('navmenu.manage_sales') }}</div>
                        </div>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#export-sales" role="tab" aria-selected="false">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='bx bx-export font-18 me-1'></i>
                            </div>
                            <div class="tab-title">{{ trans('navmenu.export_sales') }}</div>
                        </div>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#cash-sales" role="tab" aria-selected="false">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='bx bx-list-check font-18 me-1'></i>
                            </div>
                            <div class="tab-title">{{ trans('navmenu.cash_sales') }}</div>
                        </div>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#credit-sales" role="tab" aria-selected="false">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='bx bx-list-minus font-18 me-1'></i>
                            </div>
                            <div class="tab-title">{{ trans('navmenu.credit_sales') }}</div>
                        </div>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="{{ url('sales-returns') }}" aria-selected="false">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='bx bx-undo font-18 me-1'></i>
                            </div>
                            <div class="tab-title">{{ trans('navmenu.sales_returns') }}</div>
                        </div>
                    </a>
                </li>
            </ul>
            <div class="tab-content py-3">
                <div class="tab-pane fade show active" id="manage-sales" role="tabpanel">
                    <div class="table-responsive">
                        <table id="del-multiple" class="table table-striped table-bordered display nowrap"
                            style="width:100%; font-size: 14px;">
                            <thead style="font-weight: bold; font-size: 14;">
                                <tr>
                                    <th></th>
                                    <th>{{ trans('navmenu.seller') }}</th>
                                    @if ($settings->is_school)
                                        <th>{{ trans('navmenu.student_name') }}</th>
                                        <th>{{ trans('navmenu.grade') }}</th>
                                        <th>{{ trans('navmenu.year_of_study') }}</th>
                                    @else
                                        <th>{{ trans('navmenu.customer') }}</th>
                                    @endif
                                    <th>{{ trans('navmenu.sale_amount') }}</th>
                                    <th>{{ trans('navmenu.discount') }}</th>
                                    <th>{{ trans('navmenu.adjustments') }}</th>
                                    <th>{{ trans('navmenu.total_payable') }}</th>
                                    <th>{{ trans('navmenu.paid') }}</th>
                                    <th>{{ trans('navmenu.unpaid') }}</th>
                                    @if ($settings->is_vat_registered)
                                        <th>{{ trans('navmenu.vat') }}</th>
                                    @endif
                                    <th>{{ trans('navmenu.paid_by') }}</th>
                                    <th>{{ trans('navmenu.time_paid') }}</th>
                                    <th>{{ trans('navmenu.status') }}</th>
                                    <th>{{ trans('navmenu.saledate') }}</th>
                                    <th>{{ trans('navmenu.created_at') }}</th>
                                    <th>{{ trans('navmenu.last_updated') }}</th>
                                    <th>{{ trans('navmenu.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total_amount = 0;
                                $total_discount = 0;
                                $total_adjustment = 0;
                                $total_paid = 0; ?>
                                @foreach ($sales as $index => $sale)
                                    <?php $total_amount += $sale->sale_amount;
                                    $total_discount += $sale->sale_discount;
                                    $total_adjustment += $sale->adjustment;
                                    $total_paid += $sale->sale_amount_paid; ?>
                                    <tr>
                                        <td>{{ $sale->id }}</td>
                                        <td>{{ $sale->first_name }}</td>
                                        @if ($settings->is_school)
                                            <td><a
                                                    href="{{ route('an-sales.show', encrypt($sale->id)) }}">{{ $sale->name }}</a>
                                            </td>
                                            <td>
                                                @if (!is_null($sale->grade_id))
                                                    {{ App\Grade::find($sale->grade_id)->name }}
                                                @endif
                                            </td>
                                            <td style="text-align: center;">{{ $sale->year }}</td>
                                        @else
                                            <td><a
                                                    href="{{ route('an-sales.show', encrypt($sale->id)) }}">{{ $sale->name }}</a>
                                            </td>
                                        @endif
                                        <td>{{ number_format($sale->sale_amount, 2, '.', ',') }}</td>
                                        <td>{{ number_format($sale->sale_discount, 2, '.', ',') }}</td>
                                        <td>{{ number_format($sale->adjustment, 2, '.', ',') }}</td>
                                        <td>{{ number_format($sale->sale_amount - $sale->sale_discount - $sale->adjustment, 2, '.', ',') }}
                                        </td>
                                        <td>{{ number_format($sale->sale_amount_paid, 2, '.', ',') }}</td>
                                        <td>{{ number_format($sale->sale_amount - $sale->sale_discount - $sale->adjustment - $sale->sale_amount_paid, 2, '.', ',') }}
                                        </td>
                                        @if ($settings->is_vat_registered)
                                            <td>{{ number_format($sale->tax_amount, 2, '.', ',') }}</td>
                                        @endif
                                        <td>
                                            @if ($sale->pay_type == 'Cash')
                                                @if (app()->getLocale() == 'en')
                                                    {{ $sale->pay_type }}
                                                @else
                                                    {{ trans('navmenu.cash') }}
                                                @endif
                                            @elseif($sale->pay_type == 'Mobile Money')
                                                @if (app()->getLocale() == 'en')
                                                    {{ $sale->pay_type }}
                                                @else
                                                    {{ trans('navmenu.mobilemoney') }}
                                                @endif
                                            @elseif($sale->pay_type == 'Bank')
                                                @if (app()->getLocale() == 'en')
                                                    {{ $sale->pay_type }}
                                                @else
                                                    {{ trans('navmenu.bank') }}
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ $sale->time_paid }} </td>
                                        <td>
                                            @if ($sale->status == 'Paid')
                                                @if (app()->getLocale() == 'en')
                                                    <span class="badge rounded-pill bg-success">{{ $sale->status }}</span>
                                                @else
                                                    <span
                                                        class="badge rounded-pill bg-success">{{ trans('navmenu.paid_sale') }}</span>
                                                @endif
                                            @elseif($sale->status == 'Partially Paid')
                                                @if (app()->getLocale() == 'en')
                                                    <span class="badge rounded-pill bg-primary">{{ $sale->status }}</span>
                                                @else
                                                    <span
                                                        class="badge rounded-pill bg-primary">{{ trans('navmenu.partially_paid') }}</span>
                                                @endif
                                            @elseif($sale->status == 'Excess Paid')
                                                @if (app()->getLocale() == 'en')
                                                    <span
                                                        class="badge rounded-pill bg-warning text-dark">{{ $sale->status }}</span>
                                                @else
                                                    <span
                                                        class="badge rounded-pill bg-warning text-dark">{{ trans('navmenu.excess_paid') }}</span>
                                                @endif
                                            @else
                                                @if (app()->getLocale() == 'en')
                                                    <span class="badge rounded-pill bg-danger">{{ $sale->status }}</span>
                                                @else
                                                    <span
                                                        class="badge rounded-pill bg-danger">{{ trans('navmenu.un_paid') }}</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ date('d, M Y', strtotime($sale->time_created)) }}</td>
                                        <td>{{ $sale->created_at }}</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sale->updated_at)->diffForHumans() }}
                                        </td>
                                        <td>
                                            <a href="{{ url('print-receipt/' . encrypt($sale->id)) }}"
                                                title="Print Receipt"><i class="bx bx-printer"></i></a> |
                                            <a href="{{ url('issue-vfd/' . encrypt($sale->id)) }}" title="VFD Receipt"><i
                                                    class="bx bx-receipt"></i></a> |
                                            <a href="{{ route('an-sales.show', encrypt($sale->id)) }}"
                                                title="Show Details" style="color: gray;"><i
                                                    class="bx bx-detail"></i></a> |
                                            <a href="{{ url('create-dnote/' . encrypt($sale->id)) }}"
                                                title="Create Delivery Note" style="color: black;"><i
                                                    class="bx bx-file"></i></a> |
                                            @if ($sale->status == 'Partially Paid' || $sale->status == 'Unpaid')
                                                <a href="{{ url('send-sms/' . encrypt($sale->id)) }}"
                                                    title="{{ trans('navmenu.send_sms') }}" style="color: orange;"><i
                                                        class="bx bx-send"></i></a> |

                                                {{-- <div class="col-sm-12 text-center"> --}}
                                                {{-- <a class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#payModal" data-bs-backdrop="static" data-keyboard="false" style="margin: 5px;"><b><i class="bx bx-money"></i>{{trans('navmenu.add_amount_paid')}}</b></a> --}}
                                                {{-- </div> --}}
                                                <a class="add-payment" data-bs-toggle="modal" data-bs-target="#payModal"
                                                    data-bs-backdrop="static" data-keyboard="false" style="margin: 5px;"
                                                    title="Add Payment"><i class="bx bx-money"
                                                        style="color: green;"></i></a>|
                                            @endif
                                            <a href="{{ route('an-sales.edit', encrypt($sale->id)) }}" title="Edit"><i
                                                    class="bx bx-edit"></i>
                                            </a> |
                                            <form id="delete-form-{{ $index }}" method="POST"
                                                action="{{ route('an-sales.destroy', encrypt($sale->id)) }}"
                                                style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" title="Delete"
                                                    onclick="confirmDelete('<?php echo $index; ?>')"><i class="bx bx-trash"
                                                        style="color: red;"></i></a>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    @if ($settings->is_school)
                                        <th>{{ trans('navmenu.total') }}</th>
                                        <th></th>
                                        <th></th>
                                    @else
                                        <th>{{ trans('navmenu.total') }}</th>
                                    @endif
                                    <th>{{ number_format($total_amount, 2, '.', ',') }}</th>
                                    <th>{{ number_format($total_discount, 2, '.', ',') }}</th>
                                    <th>{{ number_format($total_adjustment, 2, '.', ',') }}</th>
                                    <th>{{ number_format($total_amount - $total_discount - $total_adjustment, 2, '.', ',') }}
                                    </th>
                                    <th>{{ number_format($total_paid, 2, '.', ',') }}</th>
                                    <th>{{ number_format($total_amount - $total_discount - $total_adjustment - $total_paid, 2, '.', ',') }}
                                    </th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <form id="frm-example" action="{{ url('delete-multiple-sales') }}" method="POST">
                        @csrf
                        <button id="submitButton"
                            class="btn btn-danger btn-sm">{{ trans('navmenu.delete_selected') }}</button>
                    </form>
                </div>
                <div class="tab-pane fade" id="export-sales" role="tabpanel">
                    <div class="table-responsive">
                        <table id="example2" class="table table-striped table-bordered display nowrap"
                            style="width:100%; font-size: 14px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('navmenu.seller') }}</th>
                                    @if ($settings->is_school)
                                        <th>{{ trans('navmenu.student_name') }}</th>
                                        <th>{{ trans('navmenu.grade') }}</th>
                                        <th>{{ trans('navmenu.year_of_study') }}</th>
                                    @else
                                        <th>{{ trans('navmenu.customer') }}</th>
                                    @endif
                                    <th>{{ trans('navmenu.sale_amount') }}</th>
                                    <th>{{ trans('navmenu.discount') }}</th>
                                    <th>{{ trans('navmenu.adjustments') }}</th>
                                    <th>{{ trans('navmenu.total_payable') }}</th>
                                    <th>{{ trans('navmenu.paid') }}</th>
                                    <th>{{ trans('navmenu.unpaid') }}</th>
                                    @if ($settings->is_vat_registered)
                                        <th>{{ trans('navmenu.vat') }}</th>
                                    @endif
                                    <th>{{ trans('navmenu.paid_by') }}</th>
                                    <th>{{ trans('navmenu.time_paid') }}</th>
                                    <th>{{ trans('navmenu.status') }}</th>
                                    <th>{{ trans('navmenu.saledate') }}</th>
                                    <th>{{ trans('navmenu.created_at') }}</th>
                                    <th>{{ trans('navmenu.last_updated') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total_amount = 0;
                                $total_discount = 0;
                                $total_adjustment = 0;
                                $total_paid = 0; ?>
                                @foreach ($sales as $index => $sale)
                                    <?php $total_amount += $sale->sale_amount;
                                    $total_discount += $sale->sale_discount;
                                    $total_adjustment += $sale->adjustment;
                                    $total_paid += $sale->sale_amount_paid; ?>
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $sale->first_name }} {{ $sale->last_name }}</td>
                                        @if ($settings->is_school)
                                            <td>{{ $sale->name }}</td>
                                            <td>
                                                @if (!is_null($sale->grade_id))
                                                    {{ App\Grade::find($sale->grade_id)->name }}
                                                @endif
                                            </td>
                                            <td style="text-align: center;">{{ $sale->year }}</td>
                                        @else
                                            <td>{{ $sale->name }}</td>
                                        @endif
                                        <td>{{ number_format($sale->sale_amount, 2, '.', ',') }}</td>
                                        <td>{{ number_format($sale->sale_discount, 2, '.', ',') }}</td>
                                        <td>{{ number_format($sale->adjustment, 2, '.', ',') }}</td>
                                        <td>{{ number_format($sale->sale_amount - $sale->sale_discount - $sale->adjustment, 2, '.', ',') }}
                                        </td>
                                        <td>{{ number_format($sale->sale_amount_paid, 2, '.', ',') }}</td>
                                        <td>{{ number_format($sale->sale_amount - $sale->sale_discount - $sale->adjustment - $sale->sale_amount_paid, 2, '.', ',') }}
                                        </td>
                                        @if ($settings->is_vat_registered)
                                            <td>{{ number_format($sale->tax_amount, 2, '.', ',') }}</td>
                                        @endif
                                        <td>
                                            @if ($sale->pay_type == 'Cash')
                                                @if (app()->getLocale() == 'en')
                                                    {{ $sale->pay_type }}
                                                @else
                                                    {{ trans('navmenu.cash') }}
                                                @endif
                                            @elseif($sale->pay_type == 'Mobile Money')
                                                @if (app()->getLocale() == 'en')
                                                    {{ $sale->pay_type }}
                                                @else
                                                    {{ trans('navmenu.mobilemoney') }}
                                                @endif
                                            @elseif($sale->pay_type == 'Bank')
                                                @if (app()->getLocale() == 'en')
                                                    {{ $sale->pay_type }}
                                                @else
                                                    {{ trans('navmenu.bank') }}
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ $sale->time_paid }} </td>
                                        <td>
                                            @if ($sale->status == 'Paid')
                                                @if (app()->getLocale() == 'en')
                                                    {{ $sale->status }}
                                                @else
                                                    {{ trans('navmenu.paid_sale') }}
                                                @endif
                                            @elseif($sale->status == 'Partially Paid')
                                                @if (app()->getLocale() == 'en')
                                                    {{ $sale->status }}
                                                @else
                                                    {{ trans('navmenu.partially_paid') }}
                                                @endif
                                            @elseif($sale->status == 'Excess Paid')
                                                @if (app()->getLocale() == 'en')
                                                    {{ $sale->status }}
                                                @else
                                                    {{ trans('navmenu.excess_paid') }}
                                                @endif
                                            @else
                                                @if (app()->getLocale() == 'en')
                                                    {{ $sale->status }}
                                                @else
                                                    {{ trans('navmenu.un_paid') }}
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ date('d, M Y', strtotime($sale->time_created)) }}</td>
                                        <td>{{ $sale->created_at }}</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sale->updated_at)->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th></th>
                                    @if ($settings->is_school)
                                        <th>{{ trans('navmenu.total') }}</th>
                                        <th></th>
                                        <th></th>
                                    @else
                                        <th>{{ trans('navmenu.total') }}</th>
                                    @endif
                                    <th>{{ number_format($total_amount, 2, '.', ',') }}</th>
                                    <th>{{ number_format($total_discount, 2, '.', ',') }}</th>
                                    <th>{{ number_format($total_amount - $total_discount, 2, '.', ',') }}</th>
                                    <th>{{ number_format($total_paid, 2, '.', ',') }}</th>
                                    <th>{{ number_format($total_amount - $total_discount - $total_paid, 2, '.', ',') }}
                                    </th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="cash-sales" role="tabpanel">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered display nowrap"
                            style="width:100%; font-size: 14px;">
                            <thead style="font-weight: bold; font-size: 14;">
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('navmenu.seller') }}</th>
                                    @if ($settings->is_school)
                                        <th>{{ trans('navmenu.student_name') }}</th>
                                        <th>{{ trans('navmenu.grade') }}</th>
                                        <th>{{ trans('navmenu.year_of_study') }}</th>
                                    @else
                                        <th>{{ trans('navmenu.customer') }}</th>
                                    @endif
                                    <th>{{ trans('navmenu.sale_amount') }}</th>
                                    <th>{{ trans('navmenu.discount') }}</th>
                                    <th>{{ trans('navmenu.adjustments') }}</th>
                                    <th>{{ trans('navmenu.total_payable') }}</th>
                                    <th>{{ trans('navmenu.paid') }}</th>
                                    <th>{{ trans('navmenu.unpaid') }}</th>
                                    @if ($settings->is_vat_registered)
                                        <th>{{ trans('navmenu.vat') }}</th>
                                    @endif
                                    <th>{{ trans('navmenu.paid_by') }}</th>
                                    <th>{{ trans('navmenu.time_paid') }}</th>
                                    <th>{{ trans('navmenu.status') }}</th>
                                    <th>{{ trans('navmenu.seller') }}</th>
                                    <th>{{ trans('navmenu.saledate') }}</th>
                                    <th>{{ trans('navmenu.created_at') }}</th>
                                    <th>{{ trans('navmenu.last_updated') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cashsales->chunk(100) as $chunk)
                                    @foreach ($chunk as $index => $sale)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $sale->first_name }} {{ $sale->last_name }}</td>
                                            @if ($settings->is_school)
                                                <td>{{ $sale->name }}</td>
                                                <td>
                                                    @if (!is_null($sale->grade_id))
                                                        {{ App\Grade::find($sale->grade_id)->name }}
                                                    @endif
                                                </td>
                                                <td style="text-align: center;">{{ $sale->year }}</td>
                                            @else
                                                <td>{{ $sale->name }}</td>
                                            @endif
                                            <td>{{ number_format($sale->sale_amount, 2, '.', ',') }}</td>
                                            <td>{{ number_format($sale->sale_discount, 2, '.', ',') }}</td>
                                            <td>{{ number_format($sale->adjustment, 2, '.', ',') }}</td>
                                            <td>{{ number_format($sale->sale_amount - $sale->sale_discount - $sale->adjustment, 2, '.', ',') }}
                                            </td>
                                            <td>{{ number_format($sale->sale_amount_paid, 2, '.', ',') }}</td>
                                            <td>{{ number_format($sale->sale_amount - $sale->sale_discount - $sale->adjustment - $sale->sale_amount_paid, 2, '.', ',') }}
                                            </td>
                                            @if ($settings->is_vat_registered)
                                                <td>{{ number_format($sale->tax_amount) }}</td>
                                            @endif
                                            <td>
                                                @if ($sale->pay_type == 'Cash')
                                                    @if (app()->getLocale() == 'en')
                                                        {{ $sale->pay_type }}
                                                    @else
                                                        {{ trans('navmenu.cash') }}
                                                    @endif
                                                @elseif($sale->pay_type == 'Mobile Money')
                                                    @if (app()->getLocale() == 'en')
                                                        {{ $sale->pay_type }}
                                                    @else
                                                        {{ trans('navmenu.mobilemoney') }}
                                                    @endif
                                                @elseif($sale->pay_type == 'Bank')
                                                    @if (app()->getLocale() == 'en')
                                                        {{ $sale->pay_type }}
                                                    @else
                                                        {{ trans('navmenu.bank') }}
                                                    @endif
                                                @endif
                                            </td>
                                            <td>{{ $sale->time_paid }} </td>
                                            <td>
                                                @if ($sale->status == 'Paid')
                                                    <div class="color-palette-set" style="text-align: center;">
                                                        <div class="bg-green-active color-palette">
                                                            @if (app()->getLocale() == 'en')
                                                                <span>{{ $sale->status }}</span>
                                                            @else
                                                                <span>{{ trans('navmenu.paid_sale') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($sale->status == 'Partially Paid')
                                                    <div class="color-palette-set" style="text-align: center;">
                                                        <div class="bg-light-blue-active color-palette">
                                                            @if (app()->getLocale() == 'en')
                                                                <span>{{ $sale->status }}</span>
                                                            @else
                                                                <span>{{ trans('navmenu.partially_paid') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($sale->status == 'Excess Paid')
                                                    <div class="color-palette-set" style="text-align: center;">
                                                        <div class="bg-yellow-active color-palette">
                                                            @if (app()->getLocale() == 'en')
                                                                <span>{{ $sale->status }}</span>
                                                            @else
                                                                <span>{{ trans('navmenu.excess_paid') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="color-palette-set" style="text-align: center;">
                                                        <div class="bg-red-active color-palette">
                                                            @if (app()->getLocale() == 'en')
                                                                <span>{{ $sale->status }}</span>
                                                            @else
                                                                <span>{{ trans('navmenu.un_paid') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $sale->first_name }}</td>
                                            <td>{{ date('d, M Y', strtotime($sale->time_created)) }}</td>
                                            <td>{{ $sale->created_at }}</td>
                                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sale->updated_at)->diffForHumans() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="credit-sales" role="tabpanel">
                    <div class="table-responsive">
                        <table id="creditsales" class="table table-striped table-bordered display nowrap"
                            style="width:100%; font-size: 14px;">
                            <thead style="font-weight: bold; font-size: 14;">
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('navmenu.seller') }}</th>
                                    @if ($settings->is_school)
                                        <th>{{ trans('navmenu.student_name') }}</th>
                                        <th>{{ trans('navmenu.grade') }}</th>
                                        <th>{{ trans('navmenu.year_of_study') }}</th>
                                    @else
                                        <th>{{ trans('navmenu.customer') }}</th>
                                    @endif
                                    <th>{{ trans('navmenu.sale_amount') }}</th>
                                    <th>{{ trans('navmenu.discount') }}</th>
                                    <th>{{ trans('navmenu.adjustments') }}</th>
                                    <th>{{ trans('navmenu.total_payable') }}</th>
                                    <th>{{ trans('navmenu.paid') }}</th>
                                    <th>{{ trans('navmenu.unpaid') }}</th>
                                    @if ($settings->is_vat_registered)
                                        <th>{{ trans('navmenu.vat') }}</th>
                                    @endif
                                    <th>{{ trans('navmenu.paid_by') }}</th>
                                    <th>{{ trans('navmenu.time_paid') }}</th>
                                    <th>{{ trans('navmenu.status') }}</th>
                                    <th>{{ trans('navmenu.seller') }}</th>
                                    <th>{{ trans('navmenu.saledate') }}</th>
                                    <th>{{ trans('navmenu.created_at') }}</th>
                                    <th>{{ trans('navmenu.last_updated') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($creditsales as $index => $sale)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $sale->first_name }} {{ $sale->last_name }}</td>
                                        @if ($settings->is_school)
                                            <td><a
                                                    href="{{ url('an-sales/' . encrypt($sale->id)) }}">{{ $sale->name }}</a>
                                            </td>
                                            <td>
                                                @if (!is_null($sale->grade_id))
                                                    {{ App\Grade::find($sale->grade_id)->name }}
                                                @endif
                                            </td>
                                            <td style="text-align: center;">{{ $sale->year }}</td>
                                        @else
                                            <td><a
                                                    href="{{ url('an-sales/' . encrypt($sale->id)) }}">{{ $sale->name }}</a>
                                            </td>
                                        @endif
                                        <td>{{ number_format($sale->sale_amount, 2, '.', ',') }}</td>
                                        <td>{{ number_format($sale->sale_discount, 2, '.', ',') }}</td>
                                        <td>{{ number_format($sale->adjustment, 2, '.', ',') }}</td>
                                        <td>{{ number_format($sale->sale_amount - $sale->sale_discount - $sale->adjustment, 2, '.', ',') }}
                                        </td>
                                        <td>{{ number_format($sale->sale_amount_paid, 2, '.', ',') }}</td>
                                        <td>{{ number_format($sale->sale_amount - $sale->sale_discount - $sale->adjustment - $sale->sale_amount_paid, 2, '.', ',') }}
                                        </td>
                                        @if ($settings->is_vat_registered)
                                            <td>{{ number_format($sale->tax_amount, 2, '.', ',') }}</td>
                                        @endif
                                        <td>
                                            @if ($sale->pay_type == 'Cash')
                                                @if (app()->getLocale() == 'en')
                                                    {{ $sale->pay_type }}
                                                @else
                                                    {{ trans('navmenu.cash') }}
                                                @endif
                                            @elseif($sale->pay_type == 'Mobile Money')
                                                @if (app()->getLocale() == 'en')
                                                    {{ $sale->pay_type }}
                                                @else
                                                    {{ trans('navmenu.mobilemoney') }}
                                                @endif
                                            @elseif($sale->pay_type == 'Bank')
                                                @if (app()->getLocale() == 'en')
                                                    {{ $sale->pay_type }}
                                                @else
                                                    {{ trans('navmenu.bank') }}
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ $sale->time_paid }} </td>
                                        <td>
                                            @if ($sale->status == 'Paid')
                                                <div class="color-palette-set" style="text-align: center;">
                                                    <div class="bg-green-active color-palette">
                                                        @if (app()->getLocale() == 'en')
                                                            <span>{{ $sale->status }}</span>
                                                        @else
                                                            <span>{{ trans('navmenu.paid_sale') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @elseif($sale->status == 'Partially Paid')
                                                <div class="color-palette-set" style="text-align: center;">
                                                    <div class="bg-light-blue-active color-palette">
                                                        @if (app()->getLocale() == 'en')
                                                            <span>{{ $sale->status }}</span>
                                                        @else
                                                            <span>{{ trans('navmenu.partially_paid') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @elseif($sale->status == 'Excess Paid')
                                                <div class="color-palette-set" style="text-align: center;">
                                                    <div class="bg-yellow-active color-palette">
                                                        @if (app()->getLocale() == 'en')
                                                            <span>{{ $sale->status }}</span>
                                                        @else
                                                            <span>{{ trans('navmenu.excess_paid') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div class="color-palette-set" style="text-align: center;">
                                                    <div class="bg-red-active color-palette">
                                                        @if (app()->getLocale() == 'en')
                                                            <span>{{ $sale->status }}</span>
                                                        @else
                                                            <span>{{ trans('navmenu.un_paid') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $sale->first_name }}</td>
                                        <td>{{ date('d, M Y', strtotime($sale->time_created)) }}</td>
                                        <td>{{ $sale->created_at }}</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sale->updated_at)->diffForHumans() }}
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
    {{-- <div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content"> --}}


    <!-- Modal -->
    <div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('navmenu.add_payment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">�</span></button>
                    <h4 class="modal-title" id="myModalLabel">{{ trans('navmenu.add_payment') }}</h4>
                </div> --}}


                <form class="form" method="POST" action="{{ route('sale-payments.store') }}">
                    <div class="modal-body row">
                        @csrf
                        <input type="hidden" name="an_sale_id" value="{{ $sale->id }}">
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ trans('navmenu.pay_date') }}</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" name="pay_date" id="pay_date"
                                    placeholder="{{ trans('navmenu.pick_date') }}" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label">{{ trans('navmenu.amount_paid') }} <span
                                    style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" step="any" name="amount" required
                                placeholder="{{ trans('navmenu.hnt_amount_paid') }}" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label">{{ trans('navmenu.pay_mode') }} <span
                                    style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-control" name="pay_mode" onchange="detailUpdate(this)" required>
                                <option value="Cash">{{ trans('navmenu.cash') }}</option>
                                @if ($shop->subscription_type_id == 2)
                                    <option value="Cheque">{{ trans('navmenu.cheque') }}</option>
                                @endif
                                <option value="Bank">{{ trans('navmenu.bank') }}</option>
                                <option value="Mobile Money">{{ trans('navmenu.mobilemoney') }}</option>
                            </select>
                        </div>

                        @if ($shop->subscription_type_id == 2)
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
                                        @foreach ($bdetails as $detail)
                                            <option value="{{ $detail->id }}">{{ $detail->bank_name }} -
                                                {{ $detail->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6" id="cheque" style="display: none;">
                                    <label class="form-label">Cheque Number</label>
                                    <input id="name" type="text" name="cheque_no"
                                        placeholder="Please enter Cheque Number" class="form-control">
                                </div>

                                <div class="form-group col-md-6" id="expire" style="display: none;">
                                    <label class="form-label">Expire Date</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input id="name" type="text" name="expire_date"
                                            placeholder="Please enter Expire Date" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group col-md-6" id="slip" style="display: none;">
                                    <label class="form-label">Credit Card/Bank Slip Number</label>
                                    <input id="name" type="text" name="slip_no"
                                        placeholder="Please enter Credit Card/Bank Slip number" class="form-control">
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
                            <label class="form-label">{{ trans('navmenu.comments') }}</label>
                            <textarea class="form-control" name="comments" placeholder="Enter Comments (Optional)...."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning"
                            data-bs-dismiss="modal">{{ trans('navmenu.btn_cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ trans('navmenu.btn_save') }}</button>
                    </div>
                </form>
            </div>

            {{-- <form class="form" method="POST" action="{{ route('sale-payments.store') }}">
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

                        <div class="form-group col-md-4">
                            <label class="label-control">{{ trans('navmenu.pay_mode') }} <span
                                    style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-control" name="pay_mode" onchange="detailUpdate(this)" required>
                                <option value="Cash">{{ trans('navmenu.cash') }}</option>
                                @if ($shop->subscription_type_id == 2)
                                    <option value="Cheque">{{ trans('navmenu.cheque') }}</option>
                                @endif
                                <option value="Bank">{{ trans('navmenu.bank') }}</option>
                                <option value="Mobile Money">{{ trans('navmenu.mobilemoney') }}</option>
                            </select>
                        </div>

                        @if ($shop->subscription_type_id == 2)
                            <div id="bankdetail" style="display: none;">
                                <div class="form-group col-md-4" id="deposit_mode" style="display: none;">
                                    <label class="label-control">Deposit Mode</label>
                                    <select name="deposit_mode" class="form-control">
                                        <option>Direct Deposit</option>
                                        <option>Bank Transfer</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="label-control">Bank Name </label>
                                    <select name="bank_name" class="form-control">

                                        <option value="">Select Bank Account</option>
                                        @foreach ($bdetails as $detail)
                                            <option value="{{ $detail->id }}">{{ $detail->bank_name }} -
                                                {{ $detail->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4" id="cheque" style="display: none;">
                                    <label class="label-control">Cheque Number</label>
                                    <input id="name" type="text" name="cheque_no"
                                        placeholder="Please enter Cheque Number" class="form-control">
                                </div>

                                <div class="form-group col-md-4" id="expire" style="display: none;">
                                    <label class="label-control">Expire Date</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input id="name" type="text" name="expire_date"
                                            placeholder="Please enter Expire Date" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group col-md-6" id="slip" style="display: none;">
                                    <label class="label-control">Credit Card/Bank Slip Number</label>
                                    <input id="name" type="text" name="slip_no"
                                        placeholder="Please enter Credit Card/Bank Slip number" class="form-control">
                                </div>
                            </div>
                            <div id="mobaccount" style="display: none;">
                                <div class="form-group col-md-4">
                                    <label class="label-control">Mobile Money Operator </label>
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
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn btn-success">{{ trans('navmenu.btn_save') }}</button>
                        <button type="button" class="btn btn-orange"
                            data-dismiss="modal">{{ trans('navmenu.btn_cancel') }}</button>
                    </div>
                </form> --}}
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
