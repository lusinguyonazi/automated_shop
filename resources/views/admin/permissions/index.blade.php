@extends('layouts.adm')

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
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-0 text-uppercase text-center mb-3 pt-2">Add New Permission</h6>
                    <form class="form" method="post" action="{{ route('permissions.store') }}" validate>
                        {{csrf_field()}}
                        <div class="form-body">
                            <div class="form-group">
                                <label class="py-2" for="userinput8">Permission Name</label>
                                <input class="form-control form-control-sm border-primary" type="text" name="name" placeholder="Enter Permission Name" id="userinput8" required>
                            </div>
                            <div class="form-group">
                                <label class="py-2" for="userinput8">Permission Display Name</label>
                                <input class="form-control form-control-sm border-primary" type="text" name="display_name" placeholder="Enter Permission Display Name" id="userinput8" required>
                            </div>
                            <div class="form-group">
                                <label class="py-2" for="userinput8">Permission Description</label>
                                <textarea name="description" class="form-control form-control-sm" placeholder="Enter permission description"></textarea>
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
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-0 text-uppercase mb-3 pt-2 text-center">{{$title}}</h6>
                    <table class="table table-responsive table-striped" style="width: 100%;">
                        <thead style="font-weight: bold; font-size: 14;">
                            <tr>
                                <th style="width: 10px;">#</th>
                                <th>Name</th>
                                <th>Gaurd</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $key => $permission)
                            <tr>
                                <td>{{ $key+1  }}</td>
                                <td>{{ $permission->display_name }}</td>
                                <td>{{ $permission->guard_name }} </td>
                                <td>{{ $permission->description }}</td>
                                <td>
                                    <a  href="{{  route('permissions.edit', Crypt::encrypt($permission->id)) }}">
                                        <i class="bx bx-edit"></i>
                                    </a> |
                                    <a href="{{ url('admin/permissions/destroy', Crypt::encrypt($permission->id)) }}" onclick="return confirm('Are you sure you want to delete this record?.')">
                                        <i class="bx bx-trash text-danger"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div>
                        {{ $permissions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
