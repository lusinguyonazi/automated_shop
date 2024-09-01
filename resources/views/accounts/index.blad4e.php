@extends('layouts.app')

<script>
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
        document.getElementById('delete-form-' + id).submit();
        Swal.fire(
          "{{trans('navmenu.deleted')}}",
          "{{trans('navmenu.cancelled')}}",
          'success'
        )
      }
    })
  }

  function confirmShopDelete(id) {
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
        document.getElementById('delete-form-shop-' + id).submit();
        Swal.fire(
          "{{trans('navmenu.deleted')}}",
          "{{trans('navmenu.cancelled')}}",
          'success'
        )
      }
    })
  }
</script>
@section('content')

<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
  <div class="breadcrumb-title pe-3">@if(app()->getLocale() == 'en'){{$title}}@else{{$title_sw}}@endif</div>
  <div class="ps-3">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0 p-0">
        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-user"></i></a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
      </ol>
    </nav>
  </div>
  <div class="ms-auto">
    <div class="btn-group">
      <button type="button" class="btn btn-primary">Settings</button>
      <button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
        <span class="visually-hidden">Toggle Dropdown</span>
      </button>
      <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end"> <a class="dropdown-item" href="javascript:;">Action</a>
        <a class="dropdown-item" href="javascript:;">Another action</a>
        <a class="dropdown-item" href="javascript:;">Something else here</a>
        <div class="dropdown-divider"></div> <a class="dropdown-item" href="javascript:;">Separated link</a>
      </div>
    </div>
  </div>
</div>
<!--end breadcrumb-->

<div class="row">
  <div class="col-md-3">
    <div class="card radius-10">
      <div class="card-body">
        <div class="d-flex flex-column align-items-center text-center">
          <img src="{{ asset('assets/images/user.jpg') }}" alt="Admin" class="rounded-circle p-1 bg-primary" width="110">
          <div class="mt-3">
            <h4>{{Auth::user()->first_name}} {{Auth::user()->last_name}}</h4>
            <p class="text-secondary mb-1">{{Auth::user()->roles[0]['display_name']}}</p>
          </div>
        </div>
        <ul class="list-group list-group-flush">
          <li></li>
          <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
            <h6 class="mb-0">Mobile</h6>
            <span class="text-secondary">{{Auth::user()->phone}}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
            <h6 class="mb-0">E-Mail</h6>
            <span class="text-secondary">{{Auth::user()->email}}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
            <h6 class="mb-0">Shops</h6>
            <span class="text-secondary">{{Auth::user()->shops()->count()}}</span>
          </li>
          <li></li>
        </ul>

        <div>
          <a href="{{route('user-profile.edit', encrypt(Auth::user()->id))}}" class="btn btn-primary col-12"><i class="bx bx-edit"></i><b> {{trans('navmenu.edit_profile')}}</b></a>
        </div>


        <div class="pt-2">
          <a href="{{url('change-password')}}" class="btn btn-warning col-12"><i class="bx bx-key"></i><b> {{trans('navmenu.change_password')}}</b></a>
        </div>


      </div>
    </div>
  </div>
  <div class="col-md-9">
    <div class="card radius-6 " style="height:100%">
      <div class="card-body ">
        <div class="float-end">
          <a href="#" data-bs-toggle="modal" data-bs-target="#userModal" class="btn btn-success btn-sm"><i class="bx bx-user-plus"></i>{{trans('navmenu.new_seller')}}</a>
        </div>
        <div class="d-flex align-items-start  px-1 py-1">
          <ul class="nav nav-tabs nav-success" role="tablist">
            <li class="nav-item" role="presentation">
              <a class="nav-link active" data-bs-toggle="tab" href="#user" role="tab" aria-selected="false">
                <div class="d-flex align-items-center">
                  <div class="tab-title">{{trans('navmenu.business_users')}}</div>
                </div>
              </a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link" data-bs-toggle="tab" href="#add-shop" role="tab" aria-selected="false">
                <div class="d-flex align-items-center">
                  <div class="tab-title">{{trans('navmenu.add_business')}}</div>
                </div>
              </a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link" data-bs-toggle="tab" href="#myshops" role="tab" aria-selected="false">
                <div class="d-flex align-items-center">
                  <div class="tab-title">{{trans('navmenu.my_businesses')}}</div>
                </div>
              </a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link" data-bs-toggle="tab" href="#payments" role="tab" aria-selected="false">
                <div class="d-flex align-items-center">
                  <div class="tab-title">{{trans('navmenu.payment_history')}}</div>
                </div>
              </a>
            </li>
          </ul>
        </div>

        <div class="row">
          <div class="tab-content">
            <div class="active tab-pane pt-4" id="user">
              <table id="example1" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                <thead>
                  <th>#</th>
                  <th>{{trans('navmenu.name')}}</th>
                  <th>{{trans('navmenu.mobile')}}</th>
                  <th>{{trans('navmenu.date_registered')}}</th>
                  <th>{{trans('navmenu.actions')}}</th>
                </thead>
                <tbody>
                  @foreach($users as $key => $user)
                  <tr>
                    <td>{{$key+1}}</td>
                    @if($user->id === Auth::user()->id)
                    <td>{{$user->first_name}} {{$user->last_name}}</td>
                    @else
                    <td><a href="{{route('user-profile.show', encrypt($user->id))}}">{{$user->first_name}} {{$user->last_name}}</a></td>
                    @endif
                    <td>{{$user->phone}}</td>
                    <td>{{$user->created_at}}</td>
                    <td>
                      @if($user->roles[0]['name'] != 'manager')
                      <a href="{{route('user-profile.edit', encrypt($user->id))}}"><i class="bx bx-edit"></i></a>
                      <form id="delete-form-{{$key}}" method="POST" action="{{route('user-profile.destroy' , encrypt($user->id))}}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <a href="javascript:;" onclick="return confirmDelete({{$key}})"><i class="bx bx-trash" style="color: red;"></i></a>
                      </form>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="tab-pane pt-4" id="add-shop">
              <form method="POST" action="{{url('add-shop')}}">
                @csrf
                <div class="row">
                  <div class="col">
                    <label class="form-lable">{{trans('navmenu.business_name')}}</label>
                    <input type="text" name="shop_name" class="form-control" id="inputName" placeholder="{{trans('navmenu.hnt_business_name')}}" required>
                  </div>

                  <div class=" col">
                    <label class="form-label">{{trans('navmenu.subscription_type')}}</label>
                    <select name="subscription_type_id" required id="stype" class="form-control">
                      @foreach($stypes as $key => $stype)
                      <option value="{{$stype->id}}">{{$stype->title}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <label class="form-lable">{{trans('navmenu.business_type')}}</label>
                    <select name="business_type_id" id="btype" class="form-control" required>
                      <option value="0">{{trans('navmenu.select_business_type')}}</option>
                      @foreach($btypes as $key => $type)
                      <option value="{{$type->id}}">{{$type->type}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col">
                    <label class="form-label">{{trans('navmenu.business_sub_type')}}</label>
                    <select name="business_sub_type_id" id="sub_type" class="form-control" required>
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <button type="submit" class="btn btn-danger">{{trans('navmenu.btn_submit')}}</button>
                  </div>
                </div>
              </form>
            </div>

            <div class="tab-pane pt-4" id="myshops">
              <table id="example" class="table table-responsive table-striped display nowrap " style="width: 100%;">
                <thead style="font-weight: bold; font-size: 14;">
                  <tr>
                    <th style="width: 10px;">#</th>
                    <th>{{trans('navmenu.business_name')}}</th>
                    <td>{{trans('navmenu.street')}}</td>
                    <th>{{trans('navmenu.district')}}</th>
                    <th>{{trans('navmenu.city')}}</th>
                    <th>{{trans('navmenu.date_registered')}}</th>
                    <th>{{trans('navmenu.actions')}}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($shops as $index => $shop)
                  <tr>
                    <td>{{ $key+1  }}</td>
                    <td><a href="{{route('shops.show' , encrypt($shop->id))}}">{{ $shop->name }}</a></td>
                    <td>{{ $shop->street }}</td>
                    <td>{{ $shop->district }} </td>
                    <td>{{ $shop->city }} </td>
                    <td>{{ $shop->created_at}} </td>
                    <td>
                      <form id="delete-form-shop-{{$key}}" method="POST" action="route('shops.destroy' , encrypt($user->id))" style="display: inline;">
                        <a href="javascript:;" onclick="return confirmShopDelete({{$index}})"><i class="bx bx-trash" style="color: red;"></i></a>
                      </form>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="tab-pane pt-4" id="payments">
              <table id="example2" class="table table-responsive table-striped display nowrap " style="width: 100%;">
                <thead style="font-weight: bold; font-size: 14;">
                  <tr>
                    <th style="width: 10px;">#</th>
                    <th>{{trans('navmenu.payment_date')}}</th>
                    <td>{{trans('navmenu.receipt_no')}}</td>
                    <td>{{trans('navmenu.pay_number')}}</td>
                    <th>{{trans('navmenu.amount')}}</th>
                    <th>{{trans('navmenu.duration')}}</th>
                    <th>{{trans('navmenu.activated_by')}}</th>
                    <th>{{trans('navmenu.activated_at')}}</th>
                    <th>{{trans('navmenu.expire_date')}}</th>
                    <th>{{trans('navmenu.is_expired')}}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($payments as $key => $payment)
                  <tr>
                    <td> {{ $key+1  }}</td>
                    <td>{{ $payment->created_at}} </td>
                    <td><a href="{{url('view-receipt/'.$payment->id)}}">{{ $payment->transaction_id }}</a> </td>
                    <td> {{ $payment->phone }} </td>
                    <td>{{ $payment->amount_paid }}</td>
                    <td>{{ $payment->period }} </td>
                    <td> {{ $payment->first_name}} {{ $payment->last_name}}</td>
                    <td> {{ $payment->activation_time }}</td>
                    <td>{{ $payment->expire_date }} </td>
                    @if($payment->is_expired)
                    <td>YES </td>
                    @else
                    <td>NO</td>
                    @endif
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
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="myModalLabel">{{trans('navmenu.new_seller')}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

          </div>
          <form class="form-validate" method="POST" action="{{route('user-profile.store')}}">
            <div class="modal-body">
              @csrf
              <div class="row">
                <div class=" col-md-6 pt-3">
                  <label for="register-username" class="form-label">{{trans('navmenu.first_name')}}</label>
                  <input id="register-username" type="text" name="first_name" required placeholder="{{trans('navmenu.hnt_first_name')}}" class="form-control">
                </div>
                <div class=" col-md-6 pt-3">
                  <label for="register-username" class="form-label">{{trans('navmenu.last_name')}}</label>
                  <input id="register-username" type="text" name="last_name" required placeholder="{{trans('navmenu.hnt_last_name')}}" class="form-control">
                </div>
                <div class=" col-md-6 pt-3">
                  <label class="form-label">{{trans('navmenu.country')}}</label>
                  <div class="input-group">
                    <div class="input-group-text"><i class="bx bx-flag"></i></div>
                    <select class="form-control " name="phone_country" id="sel_ctr">
                      <option value="TZ">Tanzania</option>
                    </select>
                  </div>
                </div>
                <div class=" col-md-6 pt-3">
                  <label for="register-phone" class="form-label">{{trans('navmenu.mobile')}}</label>
                  <input id="register-phone" type="tel" name="phone" required placeholder="{{trans('navmenu.hnt_mobile_number')}}" class="form-control" data-inputmask='"mask": "9999999999"' data-mask>
                </div>
                <div class=" col-md-6 pt-3">
                  <label for="register-email" class="form-label">{{trans('navmenu.email_address')}}</label>
                  <input id="register-email" type="email" name="email" placeholder="{{trans('navmenu.hnt_email_address')}}" class="form-control">
                </div>
                <div class=" col-md-6 pt-3">
                  <label for="register-password" class="form-label">{{trans('navmenu.password')}} </label>
                  <input id="password" type="password" name="password" required placeholder="{{trans('navmenu.hnt_password')}}" class="form-control">
                </div>
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