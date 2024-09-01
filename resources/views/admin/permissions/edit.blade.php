@extends('layouts.adm')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('admin/home') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $page }}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">

        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-0 text-uppercase text-center mb-3 pt-2">EDIT Permission Details</h6>
                    <form class="form" method="post"
                        action="{{ route('permissions.update', Crypt::encrypt($permission->id)) }}" validate>
                        {{ csrf_field() }}
                        @csrf
                        @method('PUT')
                        <div class="form-body">
                            <div class="form-group">
                                <label class="py-2" for="userinput8">Permission Name</label>
                                <input value="{{ $permission->name }}" class="form-control form-control-sm border-primary"
                                    type="text" name="name" placeholder="Enter Permission Name" id="userinput8"
                                    required>
                            </div>
                            <div class="form-group">
                                <label class="py-2" for="userinput8">Permission Display Name</label>
                                <input value="{{ $permission->display_name }}"
                                    class="form-control form-control-sm border-primary" type="text" name="display_name"
                                    placeholder="Enter Permission Display Name" id="userinput8" required>
                            </div>
                            <div class="form-group">
                                <label class="py-2" for="userinput8">Permission Description</label>
                                <textarea name="description" class="form-control form-control-sm" placeholder="Enter permission description">
                                    {{ $permission->description }}
                                </textarea>
                            </div>
                            <div class="form-actions pt-3 right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-check2"></i> Save
                                </button>
                                <button type="reset" class="btn btn-warning">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
