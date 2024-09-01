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
                    <form id="frm-example" action="{{url('delete-multiple-dnotes')}}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table id="del-multiple" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                <thead style="font-weight: bold; font-size: 14;">
                                    <tr>
                                        <th>#</th>
                                        <th>{{trans('navmenu.delivery_note_no')}}</th>
                                        <th>{{trans('navmenu.customer_name')}}</th>
                                        <th>{{trans('navmenu.comments')}}</th>
                                        <th>{{trans('navmenu.created_at')}}</th>
                                        <th>{{trans('navmenu.last_updated')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dnotes as $index => $dnote)
                                    <tr>
                                        <td>{{$dnote->id}}</td>
                                        <td> {{ sprintf('%04d', $dnote->note_no)}}</td>
                                        <td><a href="{{ route('delivery-notes.show', encrypt($dnote->id)) }}">{{$dnote->name}}</a></td>
                                        <td>{{$dnote->comments}}</td>
                                        <td>{{$dnote->created_at}}</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $dnote->updated_at)->diffForHumans() }}</td>
                                        <td>
                                            <a href="{{ route('delivery-notes.show', encrypt($dnote->id)) }}">
                                                <i class="bx bx-detail"></i>
                                            </a> | 
                                            <a href="{{ route('delivery-notes.edit', encrypt($dnote->id)) }}">
                                                <i class="bx bx-edit" style="color: blue;"></i>
                                            </a> |
                                            <form id="delete-form-{{$index}}" method="POST" action="{{ route('delivery-notes.destroy', encrypt($dnote->id))}}" style="display: inline;">
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
                        <button id="submitButton" class="btn btn-danger">{{trans('navmenu.delete_selected')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection