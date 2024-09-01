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
            <form class="dashform row g-3" action="{{ url('admin/reset-password') }}" method="get">
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
                                    <table id="example6" class="table table-responsive table-striped display nowrap"
                                        style="width: 100%;">
                                        <thead style="font-weight: bold; font-size: 14;">
                                            <tr>
                                                <th style="width: 10px;">#</th>
                                                <th>Phone number</th>
                                                <th>Reset Code</th>
                                                <td>Expired</td>
                                                <th>Requested At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($passcodes as $key => $ucode)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $ucode->phone }} </td>
                                                    <td>{{ $ucode->code }} </td>
                                                    <td>{{ $ucode->is_expired }} </td>
                                                    <td>{{ $ucode->created_at }} </td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot style="font-weight: bold; font-size: 14;">
                                            <tr>
                                                <th style="width: 10px;">#</th>
                                                <th>Phone number</th>
                                                <th>Reset Code</th>
                                                <td>Expired</td>
                                                <th>Requested At</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <div class="mt-3">
                                        {{ $passcodes->links() }}
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
