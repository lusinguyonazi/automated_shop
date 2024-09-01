@extends('layouts.app')
<script type="text/javascript">
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
            document.getElementById('delete-form-cashout-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function confirmDeleteCashin(id) {
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
            document.getElementById('delete-form-cashin-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }


    function confirmDeleteTrans(id) {
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
            document.getElementById('delete-form-' + id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function wegBorrow(elem) {
        var x = document.getElementById("sel_customer");
        if(elem.value !== "No") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    }

    function weg(elem) {
        var x = document.getElementById("date_field");
        if(elem.value !== "auto") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
            $("#out_date").val('');
        }
    }

    function wegIn(elem) {
        var x = document.getElementById("indate_field");
        if(elem.value !== "auto") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
            $("#in_date").val('');
        }
    }

    function wegactx(elem) {
        var x = document.getElementById("tx_date_field");
        if(elem.value !== "auto") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
            $("#date").val('');
        }
    }

</script>

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3"></div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{url('home')}}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-xl-11 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr/>
            <form class="dashform row g-3" action="{{ url('filter-cash-flows')}}" method="POST">
                @csrf
                <div class="col-sm-6">
                   @if(Auth::user()->hasRole('manager')||Auth::user()->can('add-transaction')) <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#actxModal" data-bs-backdrop="static" data-bs-keyboard="false"><i class="bx bx-plus-circle"></i>{{trans('navmenu.new_transaction')}}</a>@endif
                    @if(Auth::user()->hasRole('manager')||Auth::user()->can('add-cashin')) <a href="#" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#cinModal" data-bs-backdrop="static" data-bs-keyboard="false"><i class="bx bx-plus-circle"></i>{{trans('navmenu.new_cash_in')}}</a>@endif
                    @if(Auth::user()->hasRole('manager')||Auth::user()->can('add-cashout')) <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#coutModal" data-bs-backdrop="static" data-bs-keyboard="false"><i class="bx bx-plus-circle"></i>{{trans('navmenu.new_cash_out')}}</a>@endif
                </div>
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                <!-- Date and time range -->
                @if(Auth::user()->hasRole('manager')||Auth::user()->can('view-transaction')||Auth::user()->can('edit-transaction')||Auth::user()->can('view-cashflow')||Auth::user()->can('edit-cashin')||Auth::user()->can('delete-cashin')||Auth::user()->can('view-cashout')||Auth::user()->can('edit-cashout')||Auth::user()->can('delete'))
                <div class="col-sm-6">
                    <div class="input-group">
                        <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                            <span><i class="bx bx-calendar"></i></span>
                            <i class="bx bx-caret-down"></i>
                        </button>
                    </div>
                </div>
                @endif
            </form>
            <hr>
            {{-- @if(Auth::user()->hasRole('manager')||Auth::user()->can('view-transaction')||Auth::user()->can('delete-transaction')||Auth::user()->can('edit-transaction')) --}}
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-success" role="tablist">
                        @if(Auth::user()->hasRole('manager')||Auth::user()->can('view-transaction')||Auth::user()->can('delete-transaction')||Auth::user()->can('edit-transaction')||Auth::user()->can('add-transaction'))    
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#transactions" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.accounts_transactions')}}</div>
                                </div>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasRole('manager')||Auth::user()->can('delete-cashin')||Auth::user()->can('view-cashin')||Auth::user()->can('edit-cashin')||Auth::user()->can('view-cashflow'))
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#cash-ins" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.cash_inflow')}}</div>
                                </div>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasRole('manager')||Auth::user()->can('delete-cashout')||Auth::user()->can('view-cashout')||Auth::user()->can('edit-cashout')||Auth::user()->can('add-cashout')||Auth::user()->can('view-cashflow'))
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#cash-outs" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.cash_outflow')}}</div>
                                </div>
                            </a>
                        </li>
                        @endif

                    </ul>
                    
                    
                    <div class="tab-content py-3">
                        @if(Auth::user()->hasRole('manager')||Auth::user()->can('view-transaction')||Auth::user()->can('delete-transaction')||Auth::user()->can('edit-transaction'))
                        <div class="tab-pane fade show active" id="transactions" role="tabpanel">
                            <table id="example2" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <!-- <th></th> -->
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>{{trans('navmenu.from')}}</th>
                                        <th>{{trans('navmenu.to')}}</th>
                                        <th>{{trans('navmenu.bank_name')}}</th>
                                        <th>{{trans('navmenu.amount')}}</th>
                                        <th>{{trans('navmenu.reason')}}</th>
                                        <th>{{trans('navmenu.created_at')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($acctransactions as $index => $trans)
                                    <tr>
                                        <!-- <td>{{$trans->id}}</td> -->
                                        <td>{{$trans->date}}</td>
                                        <td>{{$trans->from}}</td>
                                        <td>{{$trans->to}}</td>
                                        <td>
                                        @if(!is_null($trans->bank_detail_id))
                                            {{App\BankDetail::find($trans->bank_detail_id)->bank_name}}
                                        @else
                                        -
                                        @endif
                                        </td>
                                        <td>{{number_format($trans->amount, 2, '.', ',')}}</td>
                                        <td>{{$trans->reason}}</td>
                                        <td>{{$trans->created_at}}</td>
                                        <td>
                                            @if(Auth::user()->hasRole('manager')||Auth::user()->can('edit-transaction'))
                                            <a href="{{route('acc-transactions.edit', encrypt($trans->id))}}">
                                                <i class="bx bx-edit" style="color: blue;"></i>
                                            </a>
                                            @endif
                                            @if(Auth::user()->hasRole('manager')||Auth::user()->can('delete-transaction'))
                                            <form id="delete-form-{{$index}}" action="{{route('acc-transactions.destroy' , encrypt($trans->id) )}}" method="POST">
                                                @method('DELETE')
                                                 @csrf
                                                <a href="#" class="button" onclick="confirmDeleteTrans('{{$index}}')"><i class="bx bx-trash" style="color: red;"></i></a>
                                            </form>      
                                             @endif   
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                        @if(Auth::user()->hasRole('manager')||Auth::user()->can('add-cashin')||Auth::user()->can('edit-cashin')||Auth::user()->can('view-cashin')||Auth::user()->can('delete-cashin')||Auth::user()->can('view-cashflow'))
                        <div class="tab-pane fade" id="cash-ins" role="tabpanel">
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs py-3">
                                    <li class="nav-item"><a class="nav-link" href="#tab_1-in" data-bs-toggle="tab">{{trans('navmenu.cash_from_sales')}}</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#tab_2-in" data-bs-toggle="tab" onclick="return showInBtn()">{{trans('navmenu.other_source')}}</a></li>
                                    <li class="float-end"><a href="#" class="text-muted"><i class="bx bx-gear"></i></a></li>
                                </ul>
                                <div class="tab-content ">
                                    <div class="tab-pane active" id="tab_1-in">  
                                        <table id="example4" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>{{trans('navmenu.date')}}</th>
                                                    <th>{{trans('navmenu.account')}}</th>
                                                    <th>{{trans('navmenu.amount')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $total_sales = 0; ?>
                                                @foreach($salescashins as $index => $scin)
                                                <?php $total_sales += $scin->amount; ?>
                                                <tr>
                                                    <td>{{$scin->pay_date}}</td>
                                                    <td>
                                                       @if($scin->pay_mode == 'Cash')
                                                         @if(app()->getLocale() == 'en')
                                                           {{$scin->pay_mode}}
                                                         @else
                                                         {{trans('navmenu.cash')}}
                                                       @endif
                                                       @elseif($scin->pay_mode == 'Mobile Money')
                                                         @if(app()->getLocale() == 'en')
                                                           {{$scin->pay_mode}}
                                                         @else
                                                           {{trans('navmenu.mobilemoney')}}
                                                         @endif
                                                       @elseif($scin->pay_mode == 'Bank')
                                                         @if(app()->getLocale() == 'en')
                                                           {{$scin->pay_mode}}
                                                         @else
                                                           {{trans('navmenu.bank')}}
                                                         @endif                           
                                                       @endif
                                                    </td>
                                                    <td>{{number_format($scin->amount, 2,'.', ',')}}</td>   
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th></th>
                                                    <th>{{trans('navmenu.total')}}</th>
                                                    <th>{{number_format($total_sales)}}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="tab_2-in">
                                        <table id="example6" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>{{trans('navmenu.date')}}</th>
                                                    <th>{{trans('navmenu.account')}}</th>
                                                    <th>{{trans('navmenu.amount')}}</th>
                                                    <th>{{trans('navmenu.source')}}</th>
                                                    <th>{{trans('navmenu.created_at')}}</th>
                                                    <th>{{trans('navmenu.actions')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($cashins as $index => $cin)
                                                <tr>
                                                    <td>{{$cin->in_date}}</td>
                                                    <td>
                                                    @if($cin->account == 'Cash')
                                                       @if(app()->getLocale() == 'en')
                                                           {{$cin->account}}
                                                       @else
                                                           {{trans('navmenu.cash')}}
                                                       @endif
                                                    @elseif($cin->account == 'Mobile Money')
                                                       @if(app()->getLocale() == 'en')
                                                           {{$cin->account}}
                                                       @else
                                                           {{trans('navmenu.mobilemoney')}}
                                                       @endif
                                                    @elseif($cin->account == 'Bank')
                                                       @if(app()->getLocale() == 'en')
                                                           {{$cin->account}}
                                                       @else
                                                           {{trans('navmenu.bank')}}
                                                       @endif                           
                                                    @endif
                                                    </td>
                                                    <td>{{number_format($cin->amount, 2,'.', ',')}}</td>
                                                    <td>{{$cin->source}}</td>
                                                    <td>{{$cin->created_at}}</td>
                                                    <td>
                                                        @if(Auth::user()->hasRole('manager')||Auth::user()->can('edit-cashin'))
                                                        <a href="{{route('cash-ins.edit', encrypt($cin->id))}}">
                                                            <i class="bx bx-edit" style="color: blue;"></i>
                                                        </a>
                                                        @endif
                                                        @if(Auth::user()->hasRole('manager')||Auth::user()->can('delete-cashin'))
                                                        <form id="delete-form-cashin-{{$index}}" method="POST" action="{{route('cash-ins.destroy' , encrypt($cin->id) )}}" style="display: inline;">
                                                            @method('DELETE')
                                                            @csrf
                                                            <a href="#" class="button" onclick="confirmDeleteCashin('{{$index}}')"><i class="bx bx-trash" style="color: red;"></i></a>
                                                        </form>
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
                        @endif
                        <div class="tab-pane fade" id="cash-outs" role="tabpanel">
                            <div class="d-flex   px-1 py-3">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item"><a class="nav-link" href="#tab_1" data-bs-toggle="tab">{{trans('navmenu.operating_expense')}}</a></li>
                                    <li class="nav-item"><a  class="nav-link" href="#tab_2" data-bs-toggle="tab">{{trans('navmenu.stock_purchasing')}}</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#tab_3" data-bs-toggle="tab" onclick="return showOutBtn()">{{trans('navmenu.others')}}</a></li>
                                    <li class="float-end"><a href="#" class="text-muted"><i class="bx bx-gear"></i></a></li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab_1">
                                    <table id="example1" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <!-- <th></th> -->
                                                <th>{{trans('navmenu.date')}}</th>
                                                <th>{{trans('navmenu.amount')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $pout_total = 0; ?>
                                            @foreach($expcashouts as $index => $expout)
                                            <?php $pout_total += $expout->amount; ?>
                                            <tr>
                                                <td>{{$expout->pay_date}}</td>
                                                <td>{{number_format($expout->amount, 2, '.', ',')}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <!-- <th></th> -->
                                                <th>{{trans('navmenu.total')}}</th>
                                                <th>{{number_format($pout_total, 2, '.', ',')}}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="tab_2">
                                    <table id="example5" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <!-- <th></th> -->
                                                <th>{{trans('navmenu.date')}}</th>
                                                <th>{{trans('navmenu.amount')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $pout_total = 0; ?>
                                            @foreach($purchcashouts as $index => $pout)
                                            <?php $pout_total += $pout->amount; ?>
                                            <tr>
                                                <td>{{$pout->pay_date}}</td>
                                                <td>{{number_format($pout->amount, 2,'.', ',')}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <!-- <th></th> -->
                                                <th>{{trans('navmenu.total')}}</th>
                                                <th>{{number_format($pout_total, 2,'.', ',')}}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="tab_3">
                                    <table id="example3" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <!-- <th></th> -->
                                                <th>{{trans('navmenu.date')}}</th>
                                                <th>{{trans('navmenu.account')}}</th>
                                                <th>{{trans('navmenu.amount')}}</th>
                                                <th>{{trans('navmenu.reason')}}</th>
                                                <th>{{trans('navmenu.created_at')}}</th>
                                                <th>{{trans('navmenu.actions')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $tcout = 0; ?>
                                            @foreach($cashouts as $index => $cout)
                                            <?php $tcout += $cout->amount; ?>
                                            <tr>
                                                <!-- <td>{{$cout->id}}</td> -->
                                                <td>{{$cout->out_date}}</td>
                                                <td>
                                                @if($cout->account == 'Cash')
                                                    @if(app()->getLocale() == 'en')
                                                        {{$cout->account}}
                                                    @else
                                                        {{trans('navmenu.cash')}}
                                                    @endif
                                                @elseif($cout->account == 'Mobile Money')
                                                    @if(app()->getLocale() == 'en')
                                                        {{$cout->account}}
                                                    @else
                                                        {{trans('navmenu.mobilemoney')}}
                                                    @endif
                                                @elseif($cout->account == 'Bank')
                                                    @if(app()->getLocale() == 'en')
                                                        {{$cout->account}}
                                                    @else
                                                        {{trans('navmenu.bank')}}
                                                    @endif                           
                                                @endif
                                                </td>
                                                <td>{{number_format($cout->amount,2, '.', ',')}}</td>
                                                <td>{{$cout->reason}}</td>
                                                <td>{{$cout->created_at}}</td>
                                                <td>
                                                    @if(Auth::user()->hasRole('manager')||Auth::user()->can('view-cashout')||Auth::user()->can('delete-cashout')||Auth::user()->can('edit-cashout'))
                                                    <a href="{{route('cash-flows.show', encrypt($cout->id))}}">
                                                        <i class="bx bx-eye" style="color: blue;"></i>
                                                    </a>
                                                    @endif
                                                    @if(Auth::user()->hasRole('manager')||Auth::user()->can('edit-cashout'))
                                                    <a href="{{route('cash-flows.edit', encrypt($cout->id))}}">
                                                        <i class="bx bx-edit" style="color: blue;"></i>
                                                    </a>
                                                    @endif
                                                    @if(Auth::user()->hasRole('manager')||Auth::user()->can('delete-cashout'))
                                                    <form id="delete-form-cashout-{{$index}}" method="POST" action="{{route('cash-flows.destroy' , encrypt($cout->id) )}}" style="display: inline;">
                                                        @method('DELETE')
                                                        @csrf
                                                        <a href="#" class="button" onclick="confirmDelete('{{$index}}')"><i class="bx bx-trash" style="color: red;"></i></a>
                                                    </form>
                                                    @endif
                                                          
                                                   </td>
                                               </tr>
                                               @endforeach
                                           </tbody>
                                           <tfoot>
                                               <tr>
                                                   <!-- <th></th> -->
                                                   <th></th>
                                                   <th>{{trans('navmenu.total')}}</th>
                                                   <th>{{ number_format($tcout)}}</th>
                                                   <th></th>
                                                   <th></th>
                                                   <th></th>
                                               </tr>
                                           </tfoot>
                                       </table>
                                   </div>
                                   <!-- /.tab-pane -->
                               </div>
                           </div>
                        </div>
                    </div>
                </div>
                {{-- @endif --}}
            </div>
        </div>
    </div>
    <!-- Modal -->
<div class="modal fade" id="coutModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">  
                <h6 class="modal-title" id="myModalLabel">{{trans('navmenu.new_cash_out')}}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-bs-label="Close"></button>
            </div>
                <form method="POST" action="{{route('cash-flows.store')}}">
                    <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.is_borrowed')}}</label>
                            <select onchange="wegBorrow(this)" name="is_borrowed" class="form-select form-select-sm mb-3">
                                <option>No</option>
                                <option>Yes</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="sel_customer" style="display: none;">
                            <label class="form-label">{{trans('navmenu.customer')}}</label>
                            <select name="customer_id" class="form-select form-select-sm">
                                <option value="">{{trans('navmenu.select_customer')}}</option>
                                @foreach($customers as $customer)
                                <option value="{{$customer->id}}">{{$customer->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="register-username" class="label-control">Reason <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="register-username" type="text" name="reason" required placeholder="Please enter Reason" class="form-control form-control-sm mb-3">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Account <span style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-3" name="account" required style="width: 100%;">
                                <option value="">Select Account</option>
                                <option>Cash</option>
                                <option>Bank</option>
                                <option>Mobile Money</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Amount <span style="color: red; font-weight: bold;">*</span></label>
                            <input type="number" step="any" name="amount" placeholder="Please enter Amount" class="form-control form-control-sm mb-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.date')}}</label>
                            <select onchange="weg(this)" class="form-select form-select-sm mb-3">
                                <option value="auto">Auto</option>
                                <option value="manual">Manual</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="date_field" style="display: none;">
                            <label class="form-label">{{trans('navmenu.pick_date')}}</label>
                            <div class="inner-addon left-addon">
                                <i class="myaddon bx bx-calendar"></i>
                                <input type="text" name="out_date" id="out_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3">
                            </div> 
                        </div>  
                    </div>                
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn btn-success">Save</button>
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
    </div>
</div>

     <!-- Modal -->
<div class="modal fade" id="cinModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="myModalLabel">{{trans('navmenu.new_cash_in')}}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-bs-label="Close"></button>
            </div>
                <form method="POST" action="{{route('cash-ins.store')}}">
                    <div class="modal-body">
                    @csrf
                        <div class="col-md-12">
                            <label for="register-username" class="label-control">{{trans('navmenu.source')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="register-username" type="text" name="source" required placeholder="Please enter source of this fund" class="form-control form-control-sm mb-3">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Account <span style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-select form-select-sm mb-3" name="account" required style="width: 100%;">
                                    <option value="">Select Account</option>
                                    <option>Cash</option>
                                    <option>Bank</option>
                                    <option>Mobile Money</option>
                                </select>
                            </div>

                        
                            <div class="col-md-6">
                                <label class="form-label">Amount <span style="color: red; font-weight: bold;">*</span></label>
                                <input type="number" step="any" name="amount" placeholder="Please enter Amount" class="form-control form-control-sm mb-3" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">{{trans('navmenu.date')}}</label>
                                <select onchange="wegIn(this)" class="form-select form-select-sm mb-3">
                                    <option value="auto">Auto</option>
                                    <option value="manual">Manual</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="indate_field" style="display: none;">
                                <label class="form-label">{{trans('navmenu.pick_date')}}</label>
                                <div class="inner-addon left-addon">
                                    <i class="myaddon bx bx-calendar"></i>
                                    <input type="text" name="in_date" id="in_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3">
                                </div> 
                            </div>  
                        </div>                
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn btn-success">Save</button>
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="actxModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="myModalLabel">{{trans('navmenu.new_transaction')}}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-bs-label="Close"></button>
            </div>
                <form method="POST" action="{{route('acc-transactions.store')}}">
                    <div class="modal-body">
                    @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">{{trans('navmenu.from')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-select form-select-sm mb-3" name="from" required style="width: 100%;">
                                    <option value="">Select Account</option>
                                    <option>Cash</option>
                                    <option>Bank</option>
                                    <option>Mobile Money</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">{{trans('navmenu.to')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-control form-control-sm mb-3" name="to" required style="width: 100%;">
                                    <option value="">Select Account</option>
                                    <option>Cash</option>
                                    <option>Bank</option>
                                    <option>Mobile Money</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">{{trans('navmenu.bank_name')}}</label>
                                <select class="form-select form-select-sm mb-3" name="bank_detail_id" style="width: 100%;">
                                    <option value="">Select Account</option>
                                    @foreach($bdetails as $bdetail)
                                    <option value="{{$bdetail->id}}">{{$bdetail->bank_name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Amount <span style="color: red; font-weight: bold;">*</span></label>
                                <input type="number" name="amount" placeholder="Please enter Amount" class="form-control form-control-sm mb-3" required> 
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label for="register-username" class="label-control">Reason </label>
                            <input id="register-username" type="text" name="reason" required placeholder="Please enter Reason(Optional)" class="form-control form-control-sm mb-3">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">{{trans('navmenu.date')}}</label>
                                <select onchange="wegactx(this)" class="form-select form-select-sm mb-3">
                                    <option value="auto">Auto</option>
                                    <option value="manual">Manual</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="tx_date_field" style="display: none;">
                                <label class="form-label">{{trans('navmenu.pick_date')}}</label>
                                <div class="inner-addon left-addon">
                                    <i class="myaddon bx bx-calendar"></i>
                                    <input type="text" name="date" id="date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3">
                                </div>
                            </div>
                        </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn btn-success">Save</button>
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
    </div>
</div>
@endsection
   
<link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">

<script src="{{asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="out_date"]'),
                $cfdate = document.querySelector('[name="cf_date"]'),
                $indate = document.querySelector('[name="in_date"]'),
                $max = document.querySelector('[name="date"]');


            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $cfdate.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $indate.DatePickerX.init({
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