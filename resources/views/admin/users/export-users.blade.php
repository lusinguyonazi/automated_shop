@extends('layouts.adm')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('admin/home') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $page }}</li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $title }} From <span class="text-primary">{{ $duration['from'] }}</span> To <span
                            class="text-primary">{{ $duration['to'] }} </span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-12 mx-auto">
            <form class="dashform row g-3" action="{{ url('admin/users') }}" method="get">
                @csrf
                <div class="col-md-3"></div>
                <div class="col-md-2"></div>
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                <!-- Date and time range -->
                <div class="col-md-7 mb-1">
                    <div class="input-group d-flex justify-content-end">
                        <input class="form-control form-control mx-3" account="text" style="width: 45px;" name="username"
                            placeholder="Enter Username" id="userinput8" required>
                        <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange"><span><i
                                    class="bx bx-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-body">

                    @include('admin.users.nav')

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab_1-0" role="tabpanel">
                            <div class="table-responsive">
                                {{-- <table id="example7" class="table table-striped display nowrap" style="width: 100%;"> --}}
                                <table class="table table-striped display nowrap" style="width: 100%; font-size: 12px;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th style="width: 10px;">#</th>
                                            <th>FirstName</th>
                                            <th>LastName</th>
                                            <th>Mobile Phone</th>
                                            <th>Email</th>
                                            <th>Country Code</th>
                                            <th>Dial Code</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users->chunk(200) as $user)
                                            @foreach ($user as $key => $detail)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $detail->first_name }}</td>
                                                    <td>{{ $detail->last_name }} </td>
                                                    <td>{{ $detail->phone }}</td>
                                                    <td>{{ $detail->email }} </td>
                                                    <td>{{ $detail->country_code }} </td>
                                                    <td>{{ $detail->dial_code }} </td>
                                                    <td>
                                                        {{-- <a href="{{ route('details.edit', $detail->id) }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a> |
                                                        <a href="{{ url('admin/payments/destroy', ['id' => $detail->id]) }}"
                                                            onclick="return confirm('Are you sure you want to delete this record')">
                                                            <i class="fa fa-trash"></i> --}}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="mt-3">
                                    {{ $users->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection