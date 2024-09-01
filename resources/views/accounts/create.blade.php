@extends('layouts.app')

@section('content')
    <div class="box box-success">
        <div class="card radius-6">
            <form class="form-validate" method="POST" action="{{ route('user-profile.store') }}">
                <div class="card-body">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 pt-3">
                            <label for="register-username" class="label-control">{{ trans('navmenu.first_name') }}</label>
                            <input id="register-username" type="text" name="first_name" required
                                placeholder="{{ trans('navmenu.hnt_first_name') }}" class="form-control">
                        </div>
                        <div class="col-md-6 pt-3">
                            <label for="register-username" class="label-control">{{ trans('navmenu.last_name') }}</label>
                            <input id="register-username" type="text" name="last_name" required
                                placeholder="{{ trans('navmenu.hnt_last_name') }}" class="form-control">
                        </div>

                        <div class="col-md-6 pt-3">
                            <label class="control-label">{{ trans('navmenu.country') }}</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-flag"></i></div>
                                <select class="form-control select2" name="phone_country" id="sel_ctr">
                                    <option value="TZ">Tanzania</option>
                                </select>
                                <img class="loading" src="{{ asset('img/ajax-loader.gif') }}"
                                    style="display:none; position: absolute;" />
                            </div>
                        </div>

                        <div class="col-md-6 pt-3">
                            <label for="register-phone" class="label-control">{{ trans('navmenu.mobile') }}</label>
                            <input id="register-phone" type="tel" name="phone" required
                                placeholder="{{ trans('navmenu.hnt_mobile_number') }} Eg 0789XXXXXX" class="form-control"
                                data-inputmask='"mask": "9999999999"' data-mask>
                        </div>

                        <div class="col-md-6 pt-3">
                            <label for="register-email" class="label-control">{{ trans('navmenu.email_address') }}</label>
                            <input id="register-email" type="email" name="email"
                                placeholder="{{ trans('navmenu.hnt_email_address') }}" class="form-control">
                        </div>


                        <div class="col-md-6 pt-3">
                            {{-- <div class="col-sm-6"> --}}
                            <label class="form-label">{{ trans('navmenu.role') }}</label>
                            <select name="role" class="form-control">
                                {{-- <option value="{{$user->roles[0]['name']}}">{{$user->roles[0]['display_name']}}</option> --}}
                                <option value="">------</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                @endforeach
                            </select>
                            {{-- </div> --}}
                        </div>






                        <div class="col-md-6 pt-3">
                            <label for="register-password" class="label-control">{{ trans('navmenu.password') }} </label>
                            <input id="password" type="password" name="password" required
                                placeholder="{{ trans('navmenu.hnt_password') }}" class="form-control">
                        </div>

                        <div class="col-md-12 pt-3">
                            <button type="submit" class="btn btn btn-success">{{ trans('navmenu.btn_save') }}</button>
                            <a href="javascript:history.back()"
                                class="btn btn-warning">{{ trans('navmenu.btn_cancel') }}</a>

                               
                        </div>

                    </div>
                </div>
                {{-- <div class="form-group col-md-5 d-flex justify-content-between mt-3">
	                <button type="submit" class="btn btn btn-success btn-lg">{{trans('navmenu.btn_save')}}</button>
	                <a href="javascript:history.back()" class="btn btn-danger btn-lg">{{trans('navmenu.btn_cancel')}}</a> --}}
                {{-- <a href="{{ url('/user-profile')}}"><button type="button" class="btn btn-orange" data-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button></a> --}}
                {{-- </div> --}}
            </form>
        </div>
    </div>
@endsection
