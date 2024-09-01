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
                                <div class="tab-title">{{trans('navmenu.raw_materials')}}</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab_2-2" role="tab" aria-selected="false">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='bx bx-export font-18 me-1'></i>
                                </div>
                                <div class="tab-title">{{trans('navmenu.new_raw_material')}}</div>
                            </div>
                        </a>
                    </li> 
                    <li class="nav-item" role="presentation">
                        <a class="nav-link"  href="{{url('rm-purchases')}}" >
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='bx bx-list-check font-18 me-1'></i>
                                </div>
                                <div class="tab-title">{{trans('navmenu.purchase_of_rm')}}</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link"  href="{{url('rm-uses')}}" role="tab">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='bx bx-list-minus font-18 me-1'></i>
                                </div>
                                <div class="tab-title">{{trans('navmenu.use_of_rm')}}</div>
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
                                        <th></th>
                                        <th>{{trans('navmenu.material_name')}}</th>
                                        <th>{{trans('navmenu.basic_unit')}}</th>
                                        <th>{{trans('navmenu.in_stock')}}</th>
                                        <th>
                                        {{trans('navmenu.date_registered')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($materials as $index => $material)
                                    <tr>
                                        <td>{{$material->id}}</td>
                                        <td><a href="{{route('raw-materials.show', encrypt($material->id))}}">{{$material->name}}</a></td>
                                        <td style="text-align: center;">{{$material->basic_unit}}</td>
                                        <td style="text-align: center;">{{number_format($material->pivot->in_store)}}</td>
                                        <td>{{$material->pivot->created_at}}</td>
                                        <td>
                                            <a href="{{route('raw-materials.edit', encrypt($material->id))}}"><i class="bx bx-edit" style="color: blue;"></i></a>
                                            <form id="delete-form-{{$index}}" method="POST" action="{{route('raw-materials.destroy' , encrypt($material->id))}}" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#"  onclick="confirmDelete('{{$index}}')"><i class="bx bx-trash" style="color: red;"></i>
                                                </a> 
                                            </form>     
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <form id="frm-example" action="{{url('delete-multiple-materials')}}" method="POST">
                            @csrf
                                 <button id="submitButton" class="btn btn-danger btn-sm">{{trans('navmenu.delete_selected')}}</button>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab_2-2" role="tabpanel">
                        <div class="row">
                            <form class="form" method="POST" action="{{route('raw-materials.store')}}">
                                @csrf
                                <div class="row ">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="form-label">{{trans('navmenu.material_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                                            
                                            <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_product_name')}}" class="form-control form-control-sm mb-3">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="form-label">{{trans('navmenu.basic_unit')}} <span style="color: red; font-weight: bold;">*</span></label>
                                            <select class="form-control form-select-sm mb-3" name="basic_unit" required style="width: 100%;">
                                                @foreach($units as $key => $unit)
                                                <option value="{{$key}}">{{$unit}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="form-label">{{trans('navmenu.current_stock')}}</label>
                                            <input id="qty" type="number" min="0" name="qty" step="any" placeholder="{{trans('navmenu.hnt_current_stock')}}" class="form-control form-control-sm mb-3">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="form-label">{{trans('navmenu.buying_per_unit')}}</label>
                                            <input id="unit_price" type="number" min="0" step="any" name="unit_cost" placeholder="{{trans('navmenu.hnt_buying_price')}}" class="form-control form-control-sm mb-3">
                                        </div>
                                    </div>
                                    {{--<div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="form-label">{{trans('navmenu.rm_type')}}</label>
                                            <select name="type" class="form-control form-select-sm mb-3" required>
                                                <option value="Direct Raw Materials">{{trans('navmenu.direct_rm')}}</option>
                                                <option value="Indirect Raw Materials">{{trans('navmenu.indirect_rm')}}</option>
                                            </select>
                                        </div>
                                    </div> --}}
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="form-label">{{trans('navmenu.description')}}</label>
                                            <textarea name="description" class="form-control form-control-sm mb-3" placeholder="{{trans('navmenu.hnt_product_desc')}}"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group float-end">
                                            <button type="submit" class="btn btn-success">{{trans('navmenu.btn_save')}}</button>
                                            <button type="reset" class="btn btn-warning">{{trans('navmenu.btn_reset')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


