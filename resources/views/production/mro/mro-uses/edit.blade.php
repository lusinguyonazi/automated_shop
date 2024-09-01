@extends('layouts.prod')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('/prod-home')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="col-md-9 mx-auto">
        <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
        <hr>

        <div class="card radius-6">
            <div class="card-body">
                <form class="form" method="POST" action="{{route('mro-uses.update', encrypt($mro_use->id))}}">
                    @csrf
                    {{ method_field('PUT') }}
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.batch_no')}}</label>
                                    <input id="name" type="text" name="prod_batch" class="form-control form-control-sm mb-4" value="{{$mro_use->prod_batch}}">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                               <label class="form-label">{{trans('navmenu.total_cost')}}</label>
                                    <input id="name" type="text" name="total_cost"  class="form-control form-control-sm mb-4" value="{{$mro_use->total_cost}}" readonly="">
                            </div>
                        </div>

                         <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.date')}}</label>
                                    <input id="date" type="text" placeholder="{{trans('navmenu.pick_date')}}" name="date" class="form-control form-control-sm mb-4" value="{{$mro_use->date}}" required="">
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group float-end">
                                <button type="submit" class="btn btn-success">{{trans('navmenu.btn_save')}}</button>
                                <button type="reset" class="btn btn-secondary">{{trans('navmenu.btn_reset')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

<link rel="stylesheet" href="../../css/DatePickerX.css">

<script src="../../js/DatePickerX.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $max = document.querySelector('[name="date"]');

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                // minDate    : new Date(),
                maxDate    : new Date()
            });

        });
    </script>


