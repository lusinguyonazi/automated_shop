@extends('layouts.app')
<script>
    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_delete')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            alert(id)
            document.getElementById('delete-form-'+id).submit();
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
                <div class="card-header">
                    <div class="table-responsive">
                        <table id="example1" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                            <thead style="font-weight: bold; font-size: 14;">
                                <tr>
                                    <th>#</th>
                                    <th>Invoice No.</th>
                                    <th>Credit Note No.</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Created At</th>
                                    <th>Last updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cnotes as $index => $cnote)
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td> {{ sprintf('%06d', $cnote->inv_no)}}</td>
                                    <td><a href="{{ route('credit-notes.show', Crypt::encrypt($cnote->id)) }}"> {{ sprintf('%04d', $cnote->credit_note_no)}}</a></td>
                                    <td>{{$cnote->name}}</td>
                                    <td>{{number_format($cnote->amount)}}</td>
                                    <td>{{$cnote->due_date}}</td>
                                    <td>{{$cnote->created_at}}</td>
                                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $cnote->updated_at)->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('credit-notes.edit', encrypt($cnote->id)) }}"><i class="bx bx-edit" style="color: blue;"></i></a> |
                                        <form id="delete-form-{{$index}}" method="POST" action="{{ route('credit-notes.destroy', encrypt($cnote->id))}}" style="display: inline;">
                                            @csrf
                                            @method("DELETE")
                                            <a href="#" onclick="confirmDelete('<?php echo $index; ?>')"><i class="bx bx-trash" style="color: red;"></i></a>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>  
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection