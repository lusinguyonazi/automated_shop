@extends('layouts.app')
    
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

    <div class="row">
        <div class="col-md-11 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr/>
            <div class="card">
                <div class="card-header" style="display :inline;">
                  	<a  class="btn btn-success btn-sm float-end" href="{{route('pro-invoices.create')}}" style="color: #fff; margin-left: 3px;"><i class="bx bx-edit"></i> New Profoma Invoice</a>
        		</div>
                <div class="card-body">
                	<table id="example1" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                  		<thead style="font-weight: bold; font-size: 14;">
                   			<tr>
                   				<th>#</th>
                     			<th>Order No.</th>
                     			<th>Customer</th>
                     			<th>Due Date</th>
                     			<th>Created At</th>
                     			<th>Last updated</th>
                      	      	<th>Status</th>
                     			<th>Actions</th>
                     		</tr>
                  		</thead>
                  		<tbody>
                  		    @foreach($invoices as $index => $invoice)
                            <tr>
                                <td>{{$index+1}}</td>
                                <td> {{ sprintf('%04d', $invoice->invoice_no)}} </td>
                                <td><a href="{{ route('pro-invoices.show', encrypt($invoice->id)) }}">{{$invoice->name}}</a></td>
			                    <td>{{$invoice->due_date}}</td>
			                    <td>{{$invoice->created_at}}</td>
			                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $invoice->updated_at)->diffForHumans() }}</td>
                                <td>{{$invoice->status}}</td>
                                <td>
                                    <a href="{{ route('pro-invoices.show', encrypt($invoice->id)) }}"><span class="lni lni-eye"></span></a>
                                    @if($invoice->status == 'Pending')
                                    | <a href="{{url('cancel-profoma/'.encrypt($invoice->id))}}" class="btn bg-yellow btn-flat" style="margin: 5px;"> Cancel</a> |
                                    <a href="{{url('create-invoice/'.encrypt($invoice->id))}}" style="margin: 5px; color: green;" title="Create Tax Invoice"><i class="bx bx-file"></i></a> |
                                    <a href="{{ route('pro-invoices.edit', encrypt($invoice->id)) }}"><i class="bx bx-edit" style="color: blue;"></i></a> |
                                    <a href="{{ url('pro-invoices/destroy/'.encrypt($invoice->id))}}" class="button" onclick="return confirm('Are you sure you want to delete this record')"><i class="bx bx-trash" style="color: red;"></i></a>
                                    @elseif($invoice->status == 'Cancelled')
                                    <a href="{{url('resume-profoma/'.encrypt($invoice->id))}}" class="btn bg-info btn-sm" style="margin: 5px;"> Resume</a>
                                    @endif
			                    </td>
                            </tr>
                            @endforeach
	                    </tbody>
	                </table>
	            </div>
	        </div>
        </div>
    </div>
@endsection
<link rel="stylesheet" href="css/DatePickerX.css">

<script src="js/DatePickerX.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="due_date"]'),
                $max = document.querySelector('[name="realDPX-max"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : $max
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                minDate    : $min,
                //maxDate    : new Date(2017, 4, 25)
            });

        });
    </script>