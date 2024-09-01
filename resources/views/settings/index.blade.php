@extends('layouts.app')
<script type="text/javascript">
    function showHideForm(elem) {
        var newform = document.getElementById('add-currency');
        var curlist = document.getElementById('currencies');
        var newbtn = document.getElementById('new-btn');
        if (elem == 'show') {
            newform.style.display = 'block';
            newbtn.style.display = 'none';
            curlist.style.display = 'none';
        }else{
            newform.style.display = 'none';
            newbtn.style.display = 'block';
            curlist.style.display = 'block';
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
        <div class="col-xl-8 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.business_settings')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <div>
                        <h5 class="card-title">Currencies <a href="#" onclick="showHideForm('show')" class="btn btn-success btn-sm float-end" id="new-btn" style="margin: 2px;"><i class="bx bxs-plus-square"></i> Add Currency</a></h5>
                    </div>
                    <hr/>
                    <div id="add-currency" style="display: none;">
                        <form class="row g-3" method="POST" action="{{ url('set-currency') }}">
                            @csrf
                            <div class="col-md-6">
                                <label>{{trans('navmenu.currency')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-select select2" name="code" onchange="this.form.submit()" required style="width: 100%;">
                                    <option value="">Select Currency</option>
                                    @foreach($list as $key => $name)
                                    <option value="{{$key}}">{{$name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-warning btn-sm float-end" onclick="showHideForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
                            </div>
                        </form>
                    </div>
                    <div class="row row-cols-auto g-3" id="currencies">
                        @foreach($shopcurrencies as $sc)
                        @if($sc->is_default)
                        <div class="col">
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-primary btn-sm">{{$sc->code}} (Default)</button>
                            </div>
                        </div>
                        @else
                        <div class="col">
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-secondary btn-sm">{{$sc->code}}</button>
                                <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">  <span class="visually-hidden">Toggle Dropdown</span></button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ url('make-default-currency/'.encrypt($sc->id))}}">Make Default</a></li>
                                    <li><a class="dropdown-item" href="{{ url('rem-currency/'.encrypt($sc->id))}}">Remove Currency</a></li>
                                </ul>
                            </div>
                        </div>
                        @endif
                        @endforeach    
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form class="row g-3" action="{{url('edit-settings')}}" method="POST">
                        <!-- Horizontal Form -->
                        @csrf
                        <input type="hidden" name="id" value="{{$settings->id}}">
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.allow_multi_currency')}}</label>
                            <select name="allow_multi_currency" class="form-select form-select-sm mb-3" onchange="this.form.submit()">
                                @if($settings->allow_multi_currency)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.enable_cpos')}}</label>
                            <select name="enable_cpos" class="form-select form-select-sm mb-3 ">
                                @if($settings->enable_cpos)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        @if($shop->business_type_id != 3)
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.allow_sp_less_bp')}}</label>
                            <select name="allow_sp_less_bp" class="form-select form-select-sm mb-3 ">
                                @if($settings->allow_sp_less_bp)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.always_sell_old_first')}}</label>
                            <select name="always_sell_old" class="form-select form-select-sm mb-3 ">
                                @if($settings->always_sell_old)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.is_retail_with_wholesale')}}</label>
                            <select name="retail_with_wholesale" class="form-select form-select-sm mb-3 ">
                                @if($settings->retail_with_wholesale)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.sp_mindays')}}</label>
                            <input type="number" name="sp_mindays" class="form-control form-control-sm mb-3" value="{{$settings->sp_mindays}}">
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.is_categorized')}}</label>
                            <select name="is_categorized" class="form-select form-select-sm mb-3 ">
                                @if($settings->is_categorized)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.allow_unit_discount')}}</label>
                            <select name="allow_unit_discount" class="form-select form-select-sm mb-3 ">
                                @if($settings->allow_unit_discount)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.allow_exp_date')}}</label>
                            <select name="enable_exp_date" class="form-select form-select-sm mb-3 ">
                                @if($settings->enable_exp_date)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        @endif

                        @if($shop->business_type_id == 3)
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.is_service_per_device')}}</label>
                            <select name="is_service_per_device" class="form-select form-select-sm mb-3">
                                @if($settings->is_service_per_device)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        @endif

                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.show_discounts')}}</label>
                            <select name="show_discounts" class="form-select form-select-sm mb-3">
                                @if($settings->show_discounts)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.show_bd')}}</label>
                            <select name="show_bd" class="form-select form-select-sm mb-3">
                                @if($settings->show_bd)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.show_end_note')}}</label>
                            <select name="show_end_note" class="form-select form-select-sm mb-3">
                                @if($settings->show_end_note)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.invoice_title_position')}}</label>
                            <select name="invoice_title_position" class="form-select form-select-sm mb-3">
                                @if($settings->invoice_title_position == 'right')
                                <option value="right">Title Right</option>
                                <option value="top">Title Top</option>                
                                @else
                                <option value="top">Title Top</option>  
                                <option value="right">Title Right</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.is_vat_registered')}}</label>
                            <select name="is_vat_registered" class="form-select form-select-sm mb-3">
                                @if($settings->is_vat_registered)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        @if($settings->is_vat_registered)
                        <div class="form-group  col-md-6">
                            <label for="tax_rate" class="form-label">{{trans('navmenu.tax_rate')}}(%)</label>

                            <!-- <div class="col-md-7"> -->
                            <input type="tel" class="form-control form-control-sm mb-3" id="tax_rate" name="tax_rate" value="{{$settings->tax_rate}}" placeholder="{{trans('navmenu.hnt_tax_rate')}}">
                            <!-- </div> -->
                        </div>
                        @endif 

                        <div class="form-group  col-md-6">
                            <label class="form-label">{{trans('navmenu.allow_to_estmate_wht')}}</label>
                            <select name="estimate_withholding_tax" class="form-select form-select-sm mb-3">
                                @if($settings->estimate_withholding_tax)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>

                        @if($shop->business_type_id != 3)
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.enable_barcode')}}</label>
                            <select name="use_barcode" class="form-select form-select-sm mb-3">
                                @if($settings->use_barcode)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.generate_barcode')}}</label>
                            <select name="generate_barcode" class="form-select form-select-sm mb-3">
                                @if($settings->generate_barcode)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        @endif

                        <div class="col-sm-6">
                            <label for="phone" class="form-label">{{trans('navmenu.inv_no_type')}}</label>

                            <!-- <div class="col-md-7"> -->
                            <select name="inv_no_type" class="form-select form-select-sm mb-3">
                                @if($settings->inv_no_type === 'Automatic')
                                <option>Automatic</option>
                                <option>Manual</option>
                                @else
                                <option>Manual</option>
                                <option>Automatic</option>
                                @endif
                            </select>
                            <!-- </div> -->
                        </div>

                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-primary btn-sm">{{trans('navmenu.btn_save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-4 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.barcode_settings')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <form class="form" method="POST" action="{{ url('update-bsettings')}}">
                        @csrf
                        <div class="form-body">
                            <div class="form-group col-md-12 text-center">
                                <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($code, $bsetting->code_type, $bsetting->width, $bsetting->height, [0, 0, 0], $bsetting->showcode)}}" alt="barcode" />
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">{{trans('navmenu.barcode_type')}} </label>
                                <select class="form-select form-select-sm mb-3" name="code_type">
                                    <option>{{$bsetting->code_type}}</option>
                                    <option value="">{{trans('navmenu.select_barcode_type')}}</option>
                                    <option>C39</option>
                                    <option>C39+</option>
                                    <option>C39E</option>
                                    <option>C39E+</option>
                                    <option>I25</option>
                                    <option>I25+</option>
                                    <option>C128</option>
                                    <option>C128A</option>
                                    <option>C128B</option>
                                    <option>EAN8</option>
                                    <option>EAN13</option>
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">{{trans('navmenu.barcode_number_length')}}</label>
                                <input type="number" name="code_length" value="{{$bsetting->code_length}}" class="form-control form-control-sm mb-3">
                            </div>

                            <div class="form-group col-md-12">
                                <label class="form-label">{{trans('navmenu.barcode_width')}}</label>
                                <input type="number" min="1" max="2" name="width" value="{{$bsetting->width}}" class="form-control form-control-sm mb-3">
                            </div>

                            <div class="form-group col-md-12">
                                <label class="form-label">{{trans('navmenu.barcode_height')}}</label>
                                <input type="number" name="height" value="{{$bsetting->height}}" class="form-control form-control-sm mb-3">
                            </div>
                            <div class="form-group">
                                <div class="col-md-6"> 
                                    @if($bsetting->showcode)
                                    <div class="checkbox icheck">
                                        <label>
                                            <input type="checkbox" name="showcode" value="1" checked> {{trans('navmenu.show_code')}}
                                        </label>
                                    </div>
                                    @else
                                    <div class="checkbox icheck">
                                        <label>
                                            <input type="checkbox" name="showcode" value="1"> {{trans('navmenu.show_code')}}
                                        </label>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary btn-sm">{{trans('navmenu.btn_save')}}</button>
                                </div>
                            </div>
                        </div>
                      </form>
                </div>
            </div>
            
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.change_btype')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <form class="form" method="POST" action="{{url('change-btype')}}">
                        @csrf
                        <div class="form-group">
                            <label for="inputName" class="form-label">{{trans('navmenu.business_type')}}</label>
                            <select name="business_type_id"  onchange='if(this.value != 0) { this.form.submit(); }' class="form-select form-select-sm mb-3">
                              <option value="{{$btype->id}}">{{$btype->type}}</option>
                              <option value="0">{{trans('navmenu.select_business_type')}}</option>
                              @foreach($btypes as $key => $type)
                              <option value="{{$type->id}}">{{$type->id}}. {{$type->type}}</option>
                              @endforeach
                            </select>
                        </div> 
                    </form>
                </div>
            </div>

            <h6 class="mb-0 text-uppercase">{{trans('navmenu.btype_desc')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    @foreach($btypes as $type)
                      <div class="col-md-12">
                        @if(app()->getLocale() == 'en')
                        <h6 class="mb-0 text-uppercase">{{$type->type}}</h6>
                        <p>{{$type->description}}</p>
                        @else
                        <h6 class="mb-0 text-uppercase">{{$type->type_sw}}</h6>
                        <p>{{$type->description_sw}}</p>
                        @endif
                      </div>
                      @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection