@extends('layouts.auth')

@section('content')
    <div class="section-authentication-signin d-flex align-items-center justify-content-center my-5 my-lg-5">
        <div class="container-fluid" style="padding-top: 30px;">
            <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
                <div class="col-md-3 mx-auto">
                    <div class="border p-2 rounded radius-30">
                        <div class="text-center">
                            <h6 class="">Sign in</h6>
                        </div>
                        <div class="login-separater text-center mb-4"> 
                            <span>SIGN IN WITH EMAIL OR PHONE NUMBER</span>
                            <hr/>
                        </div>
                        <div class="form-body">
                            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="col-md-12">
                                    <div class="inner-addon left-addon">
                                        <i class="myaddon bx bx-envelope"></i>
                                        <input type="text" name="email" class="form-control form-control-sm mb-3 @error('email') is-invalid @enderror" id="inputEmailAddress" placeholder="{{trans('navmenu.email_mobile')}}" value="{{old('email')}}" required>
                                    </div>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please provide your email address or Phone number.</div>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <div class="input-group" id="show_hide_password">
                                        <div class="inner-addon left-addon" style="width: 87%;">
                                            <i class="myaddon bx bx-key"></i>
                                            <input type="password" name="password" class="form-control form-control-sm mb-3 border-end-0 @error('password') is-invalid @enderror"  id="inputChoosePassword" placeholder="{{trans('navmenu.login_password')}}"
                                            required> 
                                        </div>
                                        <a href="javascript:;" class="p-1"><i class='myaddon bx bx-hide' style="font-size: 20px;"></i></a>
                                        <div class="valid-feedback">Looks good!</div>
                                        <div class="invalid-feedback">Please provide your Password.</div>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked"  name="remember" id="remember" value="{{ old('remember') ? 'checked' : '' }}">
                                        <label class="form-check-label" for="flexSwitchCheckChecked">Remember Me</label>
                                    </div>
                                </div>
                                <div class="col-md-12 text-center"> 
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}">{{trans('navmenu.forgot_pass')}}</a>
                                    @endif
                                </div>
                                <div class="col-12">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-sm"><i class="bx bxs-lock-open"></i>Sign in</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="text-center">
                            <br>
                            <p>{{trans('navmenu.dont_have_account')}} <a href="{{ route('register') }}">{{trans('navmenu.signup')}}</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!--end row-->
        </div>
    </div>
@endsection
