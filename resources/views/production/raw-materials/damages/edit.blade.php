@extends('layouts.prod')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('/home')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="col-md-10 mx-auto">
        <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
        <hr>
        <div class="card radius-6">
            <div class="card-header">
              <h3>{{$raw_material->name}}</h3>
            </div>
            <div class="card-body">
                <form class="form-validate" method="POST" action="{{ route('rm-damages.update', encrypt($rmdamage->id))}}">
                    @csrf
                    {{ method_field('PATCH') }} 
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.quantity')}}</label>
                                <input type="number" min="1" name="quantity" class="form-control form-control-sm" value="{{$rmdamage->quantity}}">
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.unit_cost')}}</label>
                                <input type="number" step="any" name="unit_cost" class="form-control form-control-sm" value="{{$rmdamage->unit_cost}}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.damage_cause')}}<span style="color: red;"> *</span></label>
                                <textarea name="reason" placeholder="{{trans('navmenu.hnt_damage_cause')}}" class="form-control" required>{{$rmdamage->reason}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row pt-3">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_save')}}</button>
                                <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
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
            var $min = document.querySelector('[name="pay_date"]'),
                $max = document.querySelector('[name="end_date"]');


            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : new Date(),
                // maxDate    : new Date()
            });

        });
    </script>


