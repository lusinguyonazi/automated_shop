@extends('layouts.app')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('admin/home')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">

        </div>
    </div>
    <!--end breadcrumb-->
    <div class="col-xl-9 mx-auto">
        <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
        <hr/>
        <div class="card">
            <div class="card-body">
                <form class="row g-3" method="POST" action="{{route('expenses.update' , encrypt($expense->id))}}">
                    @csrf
                    @method('PATCH')
                    <div class="col-md-4">
                        <label class="form-label">{{trans('navmenu.expense_date')}}</label>
                        <div class="inner-addon left-addon">
                            <i class="myaddon bx bx-calendar"></i>
                            <input type="text" name="expense_date" id="expense_date" placeholder="{{trans('navmenu.pick_date')}}" value="{{date('Y-m-d', strtotime($expense->time_created))}}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Expense Category</label>
                        <select name="expense_category_id" class="form-select form-select-sm mb-1">
                            <option value="">None</option>
                            @foreach($expcategories as $expcat)
                            @if($expense->expense_category_id == $expcat->id)
                            <option value="{{$expcat->id}}" selected>{{$expcat->name}}</option>
                            @else
                            <option value="{{$expcat->id}}">{{$expcat->name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{trans('navmenu.expense_type')}}</label>
                        <input type="text" name="expense_type" value="{{$expense->expense_type}}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{trans('navmenu.amount')}}</label>
                        <input type="text" name="amount" class="form-control" value="{{$expense->amount}}">
                    </div>
                    <div class="col-md-4">
                        <label for="account" class="form-label">{{trans('navmenu.paid_from')}} <span  style="color: red; font-weight: bold;">*</span></label>
                        <select class="form-control" name="account" required>
                            <option value="{{$expense->account}}">{{$expense->account}}</option>
                            <option value="Cash">{{trans('navmenu.cash')}}</option>
                            <option value="Bank">{{trans('navmenu.bank')}}</option>
                            <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{trans('navmenu.no_days')}}</label>
                        <input type="number" name="no_days" class="form-control" value="{{$expense->no_days}}">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">{{trans('navmenu.description')}}</label>
                        <textarea name="description" class="form-control form-control-sm mb-1" placeholder="{{trans('navmenu.hnt_description')}}">{{$expense->description}}</textarea>
                    </div>
                    @if($settings->is_vat_registered)
                    <div class="col-md-4">
                        <label class="form-label">{{trans('navmenu.has_vat')}}</label>
                        <select name="has_vat" class="form-control">
                            @if($expense->exp_vat > 0)
                            <option value="yes">YES</option>
                            <option value="no">NO</option>
                            @else
                            <option value="no">NO</option>
                            <option value="yes">YES</option>
                            @endif
                        </select>
                    </div>
                    @endif
                    @if($settings->is_categorized)
                    <div class="col-md-4 pt-2">
                        <label class="form-label">{{trans('navmenu.category')}}</label>
                        <select name="category_id" id="category" class="form-control">
                            <option value="">{{trans('navmenu.all_categories')}}</option>
                            @if(!is_null($categories))
                            @foreach($categories as $key => $cat)
                            @if(!is_null($category) && $category->id === $cat->id)
                            <option selected value="{{$cat->id}}">{{$cat->name}}</option>
                            @else
                            <option value="{{$cat->id}}">{{$cat->name}}</option>
                            @endif
                            @endforeach
                            @endif
                        </select>
                    </div>
                    @endif                           
                        
                    @if($settings->estimate_withholding_tax)
                    <div class="col-md-4">
                        <label class="form-label">Is this Expense contains Withholding Tax</label>
                        <select onchange="yesnoCheck(this)" class="form-control">
                            <option value="no">NO</option>
                            <option value="yes">YES</option>
                        </select>
                    </div>
                    <div class="col-md-4" id="ifYes" style="display: none;">
                        <label>{{trans('navmenu.wht_rate')}} </label>
                        <input type='number' min="0" id='wtax_rate' name='wht_rate' class="form-control" placeholder="Please Enter the Rate(%) of Withholding Tax">
                    </div>
                    @endif

                    @if($settings->is_service_per_device)
                    @if(!is_null($dexpense))
                    <div class="col-md-4">
                        <label class="form-label">{{trans('navmenu.device_number')}}</label>
                        <select name="device_id" class="form-control">
                            <option value="{{App\Device::find($dexpense->device_id)->id}}">{{App\Models\Device::find($dexpense->device_id)->device_number}}</option>
                            @foreach($devices as $device)
                            <option value="{{$device->id}}">{{$device->device_number}}</option>
                            @endforeach
                        </select>
                    </div>                        
                    @else
                    <div class="col-md-4 pt-2">
                        <label class="form-label">{{trans('navmenu.device_number')}}</label>
                        <select name="device_id" class="form-control select2">
                            <option value="">{{trans('navmenu.select_device')}}</option>
                            @if(!is_null($devices))
                              @foreach($devices as $device)
                              <option value="{{$device->id}}">{{$device->device_number}}</option>
                              @endforeach
                            @endif
                        </select>
                    </div>
                    @endif
                    @endif
                    <div class="col-md-12">
                        <button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_save')}}</button>
                        <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
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
            var $min = document.querySelector('[name="expense_date"]');
            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : d,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>