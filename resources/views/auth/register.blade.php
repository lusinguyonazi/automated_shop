@extends('layouts.auth')

@section('content')
<div class="section-authentication-signin d-flex align-items-center justify-content-center my-5">
    <div class="container">
        <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-2">
            <div class="col mx-auto">
                <div class="border p-4 rounded">
                    <div class="text-center">
                        <h6 class="">Sign Up</h6>
                        <p>Already have an account? <a href="{{ route('login') }}">Sign in here</a></p>
                    </div>
                    <div class="login-separater text-center mb-4"> 
                        <span>REGISTER A NEW ACCOUNT</span>
                        <hr/>
                    </div>
                    <div class="form-body">
                        @if (isset($errors) && count($errors))
                            <ul>
                                @foreach($errors->all() as $error)
                                <li>
                                    <div class="alert alert-danger alert-block">
                                        <button type="button" class="close" data-dismiss="alert">×</button> 
                                        <strong>{{ $error }}</strong>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        @endif
                        <form id="register-form" class="row g-3 needs-validation" novalidate method="POST" action="{{ route('register') }}">
                            @csrf
                            <div id="smartwizard">
                                <ul class="nav">
                                    <li class="nav-item">
                                        <a class="nav-link" href="#step-1"> </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#step-2"></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#step-3"></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#step-4"></a>
                                    </li>
                                </ul>
                                <div class="tab-content" style="display: block; overflow-y: auto; scroll-behavior: inherit;">
                                    <div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
                                        <div class="row">
                                            <p class="col-sm-12 business">{{trans('navmenu.business_info')}}</p>
                                            <div class="col-sm-6">
                                                <label for="inputbName" class="form-label">{{trans('navmenu.business_name')}} <span style="color:red">*</span></label>
                                                <input id="shopname" type="text" name="shop_name"  data-rule="minlen:5" data-msg="{{trans('navmenu.hnt_enter_business_name')}}" class="form-control form-control-sm mb-3" placeholder="{{trans('navmenu.business_name')}}" value="{{old('shop_name')}}" required>

                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please your Business name.</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="inputsubscr" class="form-label">{{trans('navmenu.subscripty_type')}} <span style="color:red">*</span></label>
                                                <div class="input-group">
                                                    <select name="subscription_type_id" data-rule="required" data-msg="Please Choose your best Plan" id="stype" class="form-select form-select-sm mb-3" required>
                                                        @foreach(App\Models\SubscriptionType::all() as $key => $stype)
                                                        <option value="{{$stype->id}}">{{$stype->title}} Package</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="input-group-addon"><a href="{{url('prices')}}" title="Click here to View more Info about these versions"><i class="fa fa-info"></i></a></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="inputbType" class="form-label">{{trans('navmenu.business_type')}} <span style="color:red">*</span></label>
                                                <select name="business_type_id" id="btype" class="form-select form-select-sm mb-3" required>
                                                    <option value="">{{trans('navmenu.select_business_type')}}</option>
                                                    @foreach(App\Models\BusinessType::all() as $key => $type)
                                                    @if(app()->getLocale() == 'en')
                                                    <option value="{{$type->id}}">{{$type->id}}. {{$type->type}}</option>
                                                    @else
                                                    <option value="{{$type->id}}">{{$type->id}}. {{$type->type_sw}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>

                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please Select business type.</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="inputSubType" class="form-label">{{trans('navmenu.business_sub_type')}} <span style="color:red">*</span></label>
                                                <select name="business_sub_type_id" class="form-select form-select-sm mb-3" id="sub-type" required>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select your Business type.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="step-2" class="tab-pane" role="tabpanel" aria-labelledby="step-2">
                                        <div class="row">
                                            <p class="col-sm-12 personal">{{trans('navmenu.personal_info')}}</p>
                                            <div class="col-sm-6">
                                                <label for="inputFirstName" class="form-label">{{trans('navmenu.first_name')}} <span style="color:red">*</span></label>
                                                <input type="text" class="form-control form-control-sm mb-3" id="inputFirstName" name="first_name" value="{{ old('first_name') }}" placeholder="{{trans('navmenu.first_name')}}" required>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please enter your First name.</div>
                                                @error('first_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="inputLastName" class="form-label">{{trans('navmenu.last_name')}} <span style="color:red">*</span></label>
                                                <input type="text" class="form-control form-control-sm mb-3" id="inputLastName" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name" required>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please enter your last name.</div>
                                                @error('last_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>

                                            <div class="col-sm-6">
                                                <label for="inputEmailAddress" class="form-label">{{trans('navmenu.email_address')}} <span style="color:red">*</span></label>
                                                <input type="email" class="form-control form-control-sm mb-3" name="email" id="inputEmailAddress" value="{{ old('email') }}" placeholder="Email Address" required>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please provide a valid email address.</div>

                                                @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="inpuMobile" class="form-label">{{trans('navmenu.mobile')}} <span style="color:red">*</span></label>
                                                <input type="tel" class="form-control form-control-sm mb-3" id="inputPhoneNumber" name="phone" placeholder="Eg. 0789XXXXXX" value="{{old('phone')}}" required>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please provide a valid phone number.</div>
                                                @error('phone')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                            <input type="hidden" name="phone_country" id="countryCode">
                                            <input type="hidden" name="dial_code" id="dialCode">
                                            <input type="hidden" name="country" id="country">
                                        </div>
                                    </div>
                                    <div id="step-3" class="tab-pane" role="tabpanel" aria-labelledby="step-3">
                                        <div class="row">
                                            <p class="col-sm-12 password">{{trans('navmenu.password')}}</p>
                                            <div class="col-sm-6">
                                                <label for="inputChoosePassword" class="form-label">{{trans('navmenu.login_password')}} <span style="color:red">*</span></label>
                                                <div class="input-group" id="show_hide_password">
                                                    <input type="password" minlength="8" class="form-control form-control-sm mb-3 border-end-0" id="inputChoosePassword" name="password" placeholder="Enter Password" required> 
                                                    <a href="javascript:;" class="p-1"><i class='myaddon bx bx-hide' style="font-size: 20px;"></i></a>
                                                    <div class="valid-feedback">Looks good!</div>
                                                    <div class="invalid-feedback">Please provide a valid and Strong Password.</div>
                                                </div>
                                                @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="inputChoosePassword" class="form-label">{{trans('navmenu.retype_password')}} <span style="color:red">*</span></label>
                                                <div class="input-group" id="show_hide_password">
                                                    <input type="password" class="form-control form-control-sm mb-3 border-end-0" id="inputConfirmPassword" name="password_confirmation" placeholder="Enter Password" required> 
                                                    <a href="javascript:;" class="p-1"><i class='myaddon bx bx-hide' style="font-size: 20px;"></i></a>
                                                    <div class="valid-feedback">Looks good!</div>
                                                    <div class="invalid-feedback">Please provide a matched password.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="step-4" class="tab-pane" role="tabpanel" aria-labelledby="step-4">
                                        <div class="row">
                                            <p class="col-sm-12 agent">{{trans('navmenu.from_agent')}} </p>
                                            <div class="col-sm-6">
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="options" value="no" checked>
                                                        {{trans('navmenu.no')}}
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="options"  value="yes">
                                                        {{trans('navmenu.yes')}}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" id="ifYes" style="display: none;">
                                                <input id="agent_code" type="text" name="agent_code" class="form-control form-control-sm mb-3" data-msg="{{trans('navmenu.hnt_agent_code')}}" placeholder="{{trans('navmenu.hnt_agent_code')}}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <p class="col-sm-12 complete">{{trans('navmenu.agree_complete')}}</p>
                                            <div class="col-12">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" required>
                                                    <label class="form-check-label" for="flexSwitchCheckChecked">I read and agree to Terms & Conditions</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--end row-->        
    </div>
</div>
@endsection