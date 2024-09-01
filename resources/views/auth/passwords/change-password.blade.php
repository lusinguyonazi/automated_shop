@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center">
<div class="card align-items-center col-md-8">
  <div class="card-header"><h5>Hi {{Auth::user()->first_name}}, Fill Form Below To Change Your Password.</h5></div>
  <div class="card-body">
    
    
    @include('flash-message')
    <form action="{{ url('change-password') }}" method="post" role="form" class="contactForm">
        @csrf
          <div class="pt-2 row">
            <input id="curr_password" type="password" name="curr_password" data-rule="required" data-msg="Please enter your Current password" class="form-control" placeholder="Current Password">       
            <div class="validation" style="color: red;"></div>
          </div>

          <div class="pt-2 row">
            <input id="password" type="password" name="password" data-rule="required" data-msg="Please enter your New password" class="form-control" placeholder="New Password">       
            <div class="validation" style="color: red;"></div>
            @if ($errors->has('password'))
            <span class="help-block" style="color: red;">
              <strong>{{ $errors->first('password') }}</strong>
            </span>
            @endif
          </div>
          <div class="pt-2 row">
             <input id="confirm-password" type="password" name="password_confirmation" data-rule="required" data-msg="Please Re-enter your New password" placeholder="Re-Enter New password" class="form-control">
            <div class="validation" style="color: red;"></div>
          </div>

          <div class="pt-2">
            <div class="">
                <button type="submit" class="btn btn-primary">
                    {{ __('Change Password') }}
                </button>
            </div>

            <div class="col-md-12 pt-3">
              {{-- <button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_save')}}</button> --}}
              <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
          </div>
        </div>
          
    </form>
  </div>
  <!-- /.login-box-body -->
</div>
</div>
@endsection

<script type="text/javascript">
  $(".toggle-password").click(function() {

    $(this).toggleClass("bx-eye bx-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
          input.attr("type", "text");
        } else {
          input.attr("type", "password");
        }
      });
</script>
