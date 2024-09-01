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
    <div class="row">
        <div class="col-xl-10 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-success" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#manage-returns" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">Manage Sales Returns</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#new-return" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">New Sales Return</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade" id="manage-returns" role="tabpanel">
                            <div class="table-responsive">
                                <table id="example" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th>#</th>
                                            <th>Customer</th>
                                            <th>Amount</th>
                                            <th>Discount</th>
                                            @if($settings->is_vat_registered)
                                            <th>Tax Amount</th>
                                            @endif
                                            <th>Created At</th>
                                            <th>Last updated</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sreturns as $index => $sreturn)
                                        <tr>
                                            <td>{{$index+1}}</td></td>
                                            <td><a href="{{ route('sales-returns.show', encrypt($sreturn->id)) }}">{{$sreturn->name}}</a></td>
                                            <td>{{number_format($sreturn->sale_return_amount)}}</td>
                                            <td>{{number_format($sreturn->sale_return_discount)}}</td>
                                            @if($settings->is_vat_registered)
                                            <td>{{number_format($sreturn->return_tax_amount)}}</td>
                                            @endif
                                            <td>{{$sreturn->created_at}}</td>
                                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sreturn->updated_at)->diffForHumans() }}</td>
                                            @if($sreturn->status == 'Paid')
                                            <td></td>
                                            @else
                                            <td>
                                                <a href="{{ route('sales-returns.edit', encrypt($sreturn->id)) }}"><i class="bx bx-edit" style="color: blue;"></i></a> |
                                                <form id="delete-form-{{$index}}" method="POST" action="{{ route('sales-returns.destroy', encrypt($sreturn->id))}}" style="display: inline;">
                                                    @csrf
                                                    @method("DELETE")
                                                    <a href="#" onclick="confirmDelete('<?php echo $index; ?>')"><i class="bx bx-trash" style="color: red;"></i></a>
                                                </form>
                                            </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade show active" id="new-return" role="tabpanel">
                            <form class="dashform row g-3" action="{{url('sale-returns')}}" method="POST">
                                @csrf
                                <div class="form-group col-sm-3">
                                    <label class="form-label">Pick Sale Date</label>
                                    <div class="input-group mb-1"> <span class="input-group-text" id="basic-addon1"><i class="bx bx-calendar"></i></span>
                                        <input type="text" name="sale_date" id="saledate" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" autocomplete="off">
                                    </div>
                                </div>
                            </form>
                            <form class="form" action="{{ route('sales-returns.store') }}" method="POST">
                                @csrf
                                <div class="col-md-12">
                                    <label class="control-label">Select Sale to Refund/Return</label>
                                    <select name="an_sale_id" class="form-control select2" onchange='if(this.value != 0) { this.form.submit(); }' required>
                                        <option value=""></option>
                                        @foreach($sales as $sale)
                                        <option value="{{$sale['id']}}">{{$sale['customer']}}({{$sale['date']}}) -> {{$sale['items']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

    <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">

    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $max = document.querySelector('[name="sale_date"]');
            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                // minDate    : new Date(),
                maxDate    : new Date()
            });
        });
    </script>