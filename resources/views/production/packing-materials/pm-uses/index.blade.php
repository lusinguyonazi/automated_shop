@extends('layouts.prod')
<script>
    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('delete-form-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function showModal(id) {
        $('#id_hide').val(id);
        $('#payModal').modal('show');
    }

</script>

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
    <div class="col-md-11 mx-auto">
        <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
        <hr>
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-success" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab_1-1" role="tab" aria-selected="true">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i>
                                </div>
                                <div class="tab-title">{{trans('navmenu.pm_utilized')}}</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link"  href="{{route('pm-uses.create')}}" role="tab" >
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='bx bx-export font-18 me-1'></i>
                                </div>
                                <div class="tab-title">  {{trans('navmenu.new_use_of_pm')}}</div>
                            </div>
                        </a>
                    </li>
                </ul>
                <div class="tab-content py-3">
                    <div class="tab-pane fade show active" id="tab_1-1" role="tabpanel">
                            <div class="table-responsive">
                                <table id="del-multiple" class="table table-striped table-bordered display nowrap" style="width: 100%; font-size: 14px;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th>S/NO</th>
                                            <th>{{trans('navmenu.date')}}</th>
                                            <th>{{trans('navmenu.batch_no')}}</th>
                                            <th>{{trans('navmenu.employee')}}</th>
                                            <th>{{trans('navmenu.cost_of_pm')}}</th>
                                            <th>{{trans('navmenu.created_at')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pmuses as $index => $pmuse)
                                        <tr>
                                            <td>{{$index +1}}</td>
                                            <td>{{date('d-m-Y', strtotime($pmuse->date))}}</td>
                                            <td>{{$pmuse->prod_batch}}</td>
                                            <td>{{$pmuse->first_name}} {{$pmuse->last_name}}</td>
                                            <td>{{number_format($pmuse->total_cost)}}</td>
                                            <td>{{$pmuse->created_at}}</td>
                                            <td>
                                         
                                                <a href="{{route('pm-uses.show', Crypt::encrypt($pmuse->id))}}">
                                                    <span class="lni lni-eye"></span>
                                                </a> |   
                                              <a href="{{route('pm-uses.edit', Crypt::encrypt($pmuse->id))}}">
                                                    <i class="bx bx-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form id="delete-form-{{$index}}" style="display: inline;" method="POST" action="{{route('pm-uses.destroy' , encrypt($pmuse->id))}}">
                                                    @csrf
                                                    @method('DELETE')
                                                      <a href="#" onclick="confirmDelete('{{$index}}')">
                                                    <i class="bx bx-trash" style="color: red;"></i></a>
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
    </div>
@endsection


