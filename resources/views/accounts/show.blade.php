@extends('layouts.app')

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                document.getElementById('delete-form-' + id).submit();
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
                    'success'
                )
            }
        })
    }

    function confirmShopDelete(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                document.getElementById('delete-form-shop-' + id).submit();
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
                    'success'
                )
            }
        })
    }
</script>
@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">
            @if (app()->getLocale() == 'en')
                {{ $title }}@else{{ $title_sw }}
            @endif
        </div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-user"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $page }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-4">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex flex-column align-items-start text-center">
                        <ul class="list-group list-group-unbordered">
                            <li></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                <b>{{ trans('navmenu.name') }}</b> <span class="float-end">{{ $user->first_name }}
                                    {{ $user->last_name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                <b>{{ trans('navmenu.mobile') }}</b> <span class="float-end">{{ $user->phone }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                <b>{{ trans('navmenu.email') }}</b> <a class="float-end">{{ $user->email }}</a>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                <b>{{ trans('navmenu.date_registered') }}</b> <span
                                    class="float-end">{{ $user->created_at->toDayDateTimeString() }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                <b>{{ trans('navmenu.user_role') }}</b> <span
                                    class="float-end">{{ $user->roles[0]['display_name'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                <b>{{ trans('navmenu.user_shops') }}</b>
                                <span class="float-end">
                                    @foreach ($usershops as $shop)
                                        {{ $shop->name }},
                                    @endforeach
                                </span>
                            </li>
                        </ul>
                    </div>
                    <div class="pt-3">
                        <form action="{{ url('assign-business') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <label for="user-id"
                                class="form-label bold"><b>{{ trans('navmenu.assign_business') }}</b></label>
                            <select name="shop_id" class="form-control" required
                                onchange='if(this.value != 0) { this.form.submit(); }'>
                                <option value="0">------</option>
                                @foreach ($shops as $shop)
                                    <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <div>
                        <form action="{{ url('detach-business') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <label for="shop_id" class="form-label"><b>{{ trans('navmenu.detach_business') }}</b></label>
                            <select name="shop_id" class="form-control col" required
                                onchange='if(this.value != 0) { this.form.submit(); }'>
                                <option value="0">------</option>
                                @foreach ($shops as $shop)
                                    <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <div>
                        <form action="{{ url('change-user-role') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <label for="shop_id" class="form-label"><b>{{ trans('navmenu.change_user_role') }}</b></label>
                            <select name="role" class="form-control" required
                                onchange='if(this.value != 0) { this.form.submit(); }'>
                                <option value="{{ $user->roles[0]['name'] }}">{{ $user->roles[0]['display_name'] }}
                                </option>
                                <option value="">------</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->display_name }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <div class="col-md-8">
            <div class="main-body">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">

                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card">

                        </div>
                    </div>
                    <div class="row">
                        <div class="d-lg-flex align-items-center mb-4 gap-3">
                            <div class="position-relative">
                                <h6 class="mb-0 text-uppercase" id="list-title">{{ trans('navmenu.user_permissions') }}
                                </h6>
                            </div>
                            <div class="ms-auto">
                                <div class="d-flex justify-content-start">
                                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                                        data-bs-target="#exampleSmallModal" style="margin: 2px;"><i
                                            class="bx bxs-plus-square"></i> Assign Permissions</button>
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#exampleMediumModal" style="margin: 2px;"><i
                                            class="bx bxs-x-square"></i> Revoke Permissions</button>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row row-cols-1 row-cols-sm-4 row-cols-lg-4 row-cols-xl-3 g-3">
                            @if (!empty($user_permissions))
                                @foreach ($user_permissions as $v)
                                    <div class="col" tabindex="1">
                                        <div
                                            class="d-flex align-items-center theme-icons shadow-sm p-2 cursor-pointer rounded">
                                            <div class="font-22 text-primary"> <i class="fadeIn animated bx bx-pencil"></i>
                                            </div>
                                            <div class="ms-2">{{ $v->display_name }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="exampleSmallModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Select Permissions to add to {{ $user->first_name }}
                            ({{ $user->roles[0]['display_name'] }})</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form class="row g-3 needs-validation" novalidate method="POST"
                        action="{{ url('add-permission') }}">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <div class="row">
                                <label for="validationCustom02" class="form-label col-md-12">Select Permissions</label>
                                @foreach ($permissions as $value)
                                    <label class="col-md-3"
                                        style="padding-bottom: 5px;">{{ Form::checkbox('permission[]', $value->name, in_array($value->id, $currPermissions) ? true : false, ['class' => 'name']) }}
                                        {{ $value->display_name }}</label>
                                @endforeach
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please select Permissions to assign.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="exampleMediumModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Select Permissions to Revoke from {{ $user->first_name }}
                            ({{ $user->roles[0]['display_name'] }})</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form class="row g-3 needs-validation" novalidate method="POST"
                        action="{{ url('remove-permission') }}">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <div class="row">
                                <label for="validationCustom02" class="form-label col-md-12">Select Permissions</label>
                                @foreach ($user_permissions as $permission)
                                    <label class="col-md-6"
                                        style="padding-bottom: 5px;">{{ Form::checkbox('permission[]', $permission->id, false, ['class' => 'name']) }}
                                        {{ $permission->display_name }}</label>
                                @endforeach
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please select Permissions to assign.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Revoke</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>











    @endsection
