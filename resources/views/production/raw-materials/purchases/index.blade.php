@extends('layouts.prod')
<script type="text/javascript">
    var currency = '';
    function wegCurr(elem) {
        var defc = "<?php echo $defcurr;?>";
        var rateMode = document.getElementById('ex-rate-mode');
        var rateModeCol = document.getElementById('rate-mode-col');
        var locale = document.getElementById('locale');
        if (elem.value != defc) {
            currency = elem.value;
            var option1 = document.createElement("option");
            option1.value = 'locale';
            option1.text = "1 "+defc+" Equals ? "+currency;
            rateMode.appendChild(option1);
            var option2 = document.createElement("option");
            option2.value = 'foreign';
            option2.text = "1 "+currency+" Equals ? "+defc;
            rateMode.appendChild(option2);
            rateModeCol.style.display = 'block';
            locale.style.display = 'block';
            document.getElementById('locale-label').innerHTML = 'Rate Amount in '+currency;
        }else{
            rateModeCol.style.display = 'none';
            locale.style.display = 'none';
        }
    }

    function wegRate(exrm) {
        var locale = document.getElementById('locale');
        var foreign = document.getElementById('foreign');
        if (exrm.value == 'locale') {
            locale.style.display = 'block';
            foreign.style.display = 'none';
        }else{
            locale.style.display = 'none';
            foreign.style.display = 'block';
        }
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
                    <li class="breadcrumb-item"><a href="{{ url('/prod-home')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="col-md-11 mx-auto">
        <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
        <hr>
        <div class="card">
            <div class="card-body">
               <div class="table-responsive">
                   <table id="del-multiple" class="table table-responsive table-striped display nowrap" style="width: 100%; font-size: 14px;">
                        <thead  style="font-weight: bold; font-size: 14;">
                            <tr>
                                <th>S/No</th>
                                <th>{{trans('navmenu.purchase_date')}}</th>
                                <th>{{trans('navmenu.supplier')}}</th>
                                @if($shop->subscription_type_id == 3 || $shop->subscription_type_id == 4)
                                <th>{{trans('navmenu.grn_no')}}</th>
                                @endif
                                <th>{{trans('navmenu.amount')}}</th>
                                <th>{{trans('navmenu.amount_paid')}}</th>
                                <th>{{trans('navmenu.unpaid')}}</th>
                                <th>{{trans('navmenu.created_at')}}</th>
                                <th>{{trans('navmenu.actions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rmpurchases as $index => $rmpurchase)
                            <tr>
                                <td>{{$index+1}}</td>
                                <td>{{date('d-m-Y', strtotime($rmpurchase->date))}}</td>
                                @if(!is_null($rmpurchase->supplier_id))
                                <td><a href="{{route('rm-purchases.show', encrypt($rmpurchase->id))}}">{{App\Models\Supplier::find($rmpurchase->supplier_id)->name}}</a></td>
                                @else
                                <td><a href="{{route('rm-purchases.show', encrypt($rmpurchase->id))}}">{{trans('navmenu.unknown')}}</a></td>
                                @endif
                                @if($shop->subscription_type_id == 3 || $shop->subscription_type_id == 4)
                                <td><a href="{{route('rm-purchase-grn', encrypt($rmpurchase->id))}}">{{ sprintf('%04d', $rmpurchase->grn_no)}}</a></td>
                                @endif
                                <td>{{number_format($rmpurchase->total_amount)}}</td>
                                <td>{{number_format($rmpurchase->amount_paid)}}</td>
                                <td>{{number_format($rmpurchase->total_amount-$rmpurchase->amount_paid)}}</td>
                                <td>{{$rmpurchase->created_at}}</td>
                                <td>
                                    @if($shop->subscription_type_id == 2)
                                    <a href="{{route('rm-purchases.show', encrypt($rmpurchase->id))}}">
                                        <span class="lni lni-eye"></span>
                                    </a> | @endif 
                                    @if($rmpurchase->amount_paid < $rmpurchase->total_amount)
                                    <a href="#" onclick="showModal('<?php echo $rmpurchase->id; ?>')" data-id="{{$rmpurchase->id}}"><i class="bx bx-money"></i></a>
                                     |@endif
                                    <a href="{{route('rm-purchases.edit', encrypt($rmpurchase->id))}}">
                                        <i class="bx bx-edit" style="color: blue;"></i>
                                    </a> | 
                                    <form id="delete-form-{{$index}}" method="POST" action="{{route('rm-purchases.destroy' , encrypt($rmpurchase->id))}}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <a href="#" onclick="confirmDelete('{{$index}}')">
                                            <i class="bx bx-trash" style="color: red;"></i>
                                        </a>   
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

<!-- Modal -->  
<div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.add_payment')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form" method="POST" action="{{route('rm-purchase-payments.store')}}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="rm_purchase_id" id="id_hide">
                        <div class="form-group col-md-4">
                            <label>{{trans('navmenu.pay_date')}}</label>
                            <div class="input-group date">
                                <div class="inner-addon left-addon">
                                    <i class="myaddon bx bx-calendar "></i>
                                </div>                                
                                <input type="text" name="pay_date" id="pay_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-select-sm mb-3" required>
                                    
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" name="amount" required placeholder="{{trans('navmenu.hnt_amount_paid')}}" class="form-control form-control-sm mb-3">
                        </div>

                        <div class="form-group col-md-4">
                            <label class="form-label">{{trans('navmenu.pay_mode')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-control form-select-sm mb-3" name="pay_mode" onchange="detailUpdate(this)" required>
                                <option value="Cash">{{trans('navmenu.cash')}}</option>
                                <option value="Cheque">{{trans('navmenu.cheque')}}</option>
                                <option value="Bank">{{trans('navmenu.bank')}}</option>
                                <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                            </select>
                        </div>
                       @if($settings->allow_multi_currency)
                            <div class="col-md-4">
                                <label class="form-label">{{trans('navmenu.currency')}}</label>
                                <select name="currency" id="currency" class="form-select form-select-sm mb-3" onchange="wegCurr(this)" required>
                                    @foreach($currencies as $curr)
                                    <option>{{$curr->code}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4" id="rate-mode-col" style="display: none;">
                                <label class="form-label">Exchange Rate Mode</label>
                                <select id="ex-rate-mode" name="ex_rate_mode"  class="form-select form-select-sm mb-3" onchange="wegRate(this)">
                                </select>
                            </div>
                            <div class="col-md-4" id="locale" style="display: none;">
                                <label class="form-label" id="locale-label"></label>
                                <input id="foreign-ex-rate" type="number" min="0" step="any" name="foreign_ex_rate" class="form-control form-control-sm mb-3">
                            </div>
                            <div class="col-md-4" id="foreign" style="display: none;">
                                <label class="form-label">Rate Amount in {{$defcurr}}</label>
                                <input id="local-ex-rate" type="number" min="0" step="any" name="local_ex_rate" class="form-control form-control-sm mb-3">
                            </div>
                        @endif
                        
                      {{--  @if($shop->subscription_type_id ==3 || $shop->subscription_type_id ==4)
                        <div id="bankdetail" style="display: none;">
                            <div class="form-group col-md-4" id="deposit_mode" style="display: none;">
                                <label class="form-label">Deposit Mode</label>
                                <select name="deposit_mode" class="form-control form-select-sm mb-3">
                                    <option>Direct Deposit</option>
                                    <option>Bank Transfer</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Bank Name </label>
                                <select name="bank_name" class="form-control form-select-sm mb-3">
                                    @foreach($bdetails as $detail)
                                    <option>{{$detail->bank_name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label class="form-label">Bank Branch </label>
                                <input id="name" type="text" name="bank_branch" placeholder="Please enter Bank Branch" class="form-control form-select-sm mb-3">
                            </div>

                            <div class="form-group col-md-4" id="cheque" style="display: none;">
                                <label class="form-label">Cheque Number</label>
                                <input id="name" type="text" name="cheque_no" placeholder="Please enter Cheque Number" class="form-control form-select-sm mb-3">
                            </div>

                            <div class="form-group col-md-4" id="expire" style="display: none;">
                                <label class="form-label">Expire Date</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div> 
                                    <input id="name" type="text" name="expire_date" placeholder="Please enter Expire Date" class="form-control form-select-sm mb-3">
                                </div>
                            </div>

                            <div class="form-group col-md-6" id="slip" style="display: none;">
                                <label class="form-label">Credit Card/Bank Slip Number</label>
                                <input id="name" type="text" name="slip_no" placeholder="Please enter Credit Card/Bank Slip number" class="form-control form-select-sm mb-3">
                            </div>
                        </div>
                        <div id="mobaccount" style="display: none;">
                            <div class="form-group col-md-4">
                                <label class="form-label">Mobile Money Operator </label>
                                <select class="form-control form-select-sm mb-3" name="operator">
                                    <option>AirtelMoney</option>
                                    <option>EzyPesa</option>
                                    <option>M-Pesa</option>
                                    <option>TigoPesa</option>
                                    <option>HaloPesa</option>
                                </select>
                            </div>
                        </div>
                        @endif --}}
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
 <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
<script>
    window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.getElementById('pay_date');


            $min.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

        });
    </script>


