@extends('layouts.prod')
<script type="text/javascript">
    function wegDam(elem) {
      var x = document.getElementById("dam_date_field");
      if(elem.value !== "auto") {
        x.style.display = "block";
      } else {
        x.style.display = "none";
        $("#datepicker").val('');
      }
    }

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

    function confirmDeleteDamage(id) {
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
            document.getElementById('delete-form-damage-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function confirmDeleteRmUse(id){
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
            document.getElementById('delete-form-rm-use-'+id).submit();
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
        <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
        <hr>

        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">{{trans('navmenu.in_stock')}}</p>
                                <h4 class="my-1">@if(is_numeric( $material->pivot->in_store) && floor( $material->pivot->in_store ) != $material->pivot->in_store) {{$material->pivot->in_store}} @else {{$material->pivot->in_store}} @endif</h4>
                            </div>
                            <div class="widgets-icons bg-light-primary text-primary ms-auto"><i class="bx bxs-box"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">{{trans('navmenu.unit_cost')}}</p>
                                <h4 class="my-1">{{number_format($material->pivot->unit_cost, 2, '.', ',')}}</h4>
                            </div>
                            <div class="widgets-icons bg-light-success text-success ms-auto"><i class="bx bx-money"></i></div>
                        </div>
                       {{-- <div class="text-center">
                            <button type="button" class=" font-13 btn btn-info"  data-bs-toggle="modal" data-bs-target="#buyingModal">{{trans('navmenu.new_buying_price')}}</button>
                        </div> --}}
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">{{trans('navmenu.reorder_point')}}</p>
                                <h4 class="my-1">{{number_format($material->pivot->reorder_point)}}</h4>
                            </div>
                        <div class="widgets-icons bg-light-dark text-dark ms-auto"><i class="bx bxs-alarm-exclamation"></i></div>
                        </div>
                        <div class="text-center">
                            <button type="button" class=" font-13 btn btn-dark"  data-bs-toggle="modal" data-bs-target="#reorderModal">{{trans('navmenu.new_reorder_point')}}</button>
                        </div>
                    </div>
                </div>
            </div>

             <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">
                                    {{trans('navmenu.damaged')}}</p>
                                <h4 class="my-1">
                                        {{$t_dam}}
                                </h4>
                            </div>
                            <div class="widgets-icons bg-light-danger text-danger ms-auto"><i class="bx bxs-trash"></i></div>
                        </div>
                        <div class="text-center">
                            <button type="button" class="mb-0 font-13  btn btn-danger" data-bs-toggle="modal" data-bs-target="#damageModal">
                                    {{trans('navmenu.new_damage')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @if(!is_null($material->pivot->description))
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">{{trans('navmenu.description')}}</p>
                                <p class="mb-0 font-18 text-success" >@if($$material->pivot->description != 'null'){{$$material->pivot->description}}@endif </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

      <!-- =========================================================== -->
        <div class="row">
            <div class="col-md-9">
                <div class="card radius-10">
                    <div class="card-body">
                        <div class="mb-3"><h5><i class="bx bx-list"></i>{{trans('navmenu.stock_history')}}</h5></div>
                        <ul class="nav nav-tabs nav-success " role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#tab_1-1" class="nav-link active" role="tab" aria-selected="true" data-bs-toggle="tab">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon">
                                            <div class="tab-title font-15">{{trans('navmenu.stock_purchases')}}</div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tab_3-3" class="nav-link " role="tab" aria-selected="true" data-bs-toggle="tab">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon">
                                            <div class="tab-title font-15"> {{trans('navmenu.rm_uses')}}  
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tab_2-2" class="nav-link " role="tab" aria-selected="true" data-bs-toggle="tab">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon">
                                            <div class="tab-title font-15"> {{trans('navmenu.damaged')}}  
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        
                        <div class="tab-content py-3">
                            <div class="tab-pane fade show active" id="tab_1-1" role="tabpanel">
                                 <table id="example1" class="table table-striped table-bordered display nowrap" style="width: 100%; font-size: 14px;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <th>#</th>
                                        <th>{{trans('navmenu.quantity')}}</th>
                                        <th>{{trans('navmenu.unit_cost')}}</th>
                                        <th>{{trans('navmenu.source')}}</th>
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </thead>
                                    <tbody>
                                        @foreach($rmitems  as $index => $rmitem)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td>
                                                @if(is_numeric( $rmitem->qty) && floor( $rmitem->qty) != $rmitem->quantity) {{$rmitem->qty}} @else {{number_format($rmitem->qty)}} @endif
                                            </td>
                                            <td>{{number_format($rmitem->unit_cost)}}</td>
                                            <td>@if(is_null($rmitem->sp_name)) Unknown @else{{$rmitem->sp_name}} @endif</td>
                                            <td>{{$rmitem->date}}</td>
                                            <td>
                                                <a href="{{route('rm-items.edit', encrypt($rmitem->id))}}">
                                                    <i class="bx bx-edit" style="color: blue;"></i>
                                                </a>
                                                <form id="delete-form-{{$index}}" method="POST" action="{{route('rm-items.destroy' , encrypt($rmitem->id))}}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                   <a href="#" onclick="confirmDelete('{{$index}}')">
                                                        <i class="bx bx-trash" style="color: red;"></i>
                                                    </a> 
                                                </form>
                                                
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="tab-pane fade " id="tab_2-2" role="tabpanel">
                                <table id="example4" class="table table-striped table-bordered display nowrap" style="width: 100%; font-size: 14px;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <th>#</th>
                                        <th>{{trans('navmenu.quantity')}}</th>
                                        <th>{{trans('navmenu.damage_cause')}}</th>
                                        <th>{{trans('navmenu.damage_date')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </thead>
                                    <tbody>
                                        @foreach($damages as $index => $damage)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td>
                                                @if(is_numeric( $damage->quantity ) && floor( $damage->quantity ) != $damage->quantity) {{$damage->quantity}} @else {{number_format($damage->quantity)}} @endif
                                                    </td>
                                            <td>{{$damage->reason}}</td>
                                            <td>{{$damage->created_at}}</td>
                                            <td>
                                                <a href="{{route('rm-damages.edit', encrypt($damage->id))}}">
                                                    <i class="bx bx-edit" style="color: blue;"></i>
                                                </a>
                                                <form id="delete-form-damage-{{$index}}" method="POST" action="{{route('rm-damages.destroy' , encrypt($damage->id))}}" style="display:inline">
                                                    @csrf
                                                    @method('DELETE')
                                                   <a href="#" onclick="confirmDeleteDamage('{{$index}}')">
                                                    <i class="bx bx-trash" style="color: red;"></i>
                                                </a> 
                                                </form>
                                                
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> 
                            <div class="tab-pane fade " id="tab_3-3" role="tabpanel">
                                <table id="example4" class="table table-striped table-bordered display nowrap" style="width: 100%; font-size: 14px;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <th>#</th>
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>{{trans('navmenu.quantity')}}</th>
                                        <th>{{trans('navmenu.batch_no')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </thead>
                                    <tbody>
                                        @foreach($rm_uses as $index => $rm_use)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td>{{$rm_use->date}}</td>
                                            <td>{{$rm_use->quantity}}</td>
                                            <td><a href="{{route('rm-uses.show' , encrypt($rm_use->rm_use_id))}}">{{$rm_use->prod_batch}}</a></td>
                                            <td>
                                                <a href="{{route('rm-uses.edit', encrypt($rm_use->id))}}">
                                                    <i class="bx bx-edit" style="color: blue;"></i>
                                                </a>
                                                <form id="delete-form-rm-use-{{$index}}" method="POST" action="{{route('rm-uses.destroy' , encrypt($rm_use->id))}}" style="display:inline">
                                                    @csrf
                                                    @method('DELETE')
                                                   <a href="#" onclick="confirmDeleteRmUse('{{$index}}')">
                                                    <i class="bx bx-trash" style="color: red;"></i>
                                                </a> 
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
            <div class="col-md-3">
                <div class="card radius-10">
                    <div class="card-body">
                        <h6 class="mb-0 text-uppercase text-center">Summary</h6>
                        <hr>
                        <table class="table table-striped" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <th>{{trans('navmenu.purchased')}}</th>
                                    <td style="text-align: left;"><b>{{$rmitems->sum('qty')+0}}</b></td>
                                </tr>
                                <tr>
                                    <th>{{trans('navmenu.used')}}</th>
                                    <td style="text-align: left;"><b>{{$rm_uses->sum('quantity')+0}}</b></td>
                                </tr>
                                <tr>
                                    <th>{{trans('navmenu.damaged')}}</th>
                                    <td style="text-align: left;"><b>{{$t_dam+0}}</b></td>
                                </tr>
                                <tr>
                                    <th>{{trans('navmenu.in_stock')}}</th>
                                    <td style="text-align: left;"><b>@if(is_numeric( $material->pivot->in_store) && floor( $material->pivot->in_store ) != $material->pivot->in_store) {{$material->pivot->in_store}} @else {{$material->pivot->in_store}} @endif</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
      


<!-- Modal -->
<div class="modal fade" id="buyingModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.new_buying_price')}} </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        <form class="form-horizontal" method="POST" action="{{url('rm-new-buy-price')}}">
            <div class="modal-body">
                @csrf
                <input type="hidden" name="raw_material_id" value="{{$material->id}}">
                <div class="form-group">
                    <label for="register-username" class="col-sm-6 form-label">{{trans('navmenu.buying_per_unit')}}</label>
                    <div class="col-sm-6">
                        <input id="register-username" type="number" min="0" step="any" name="buying_per_unit" required placeholder="{{trans('navmenu.hnt_buying_price')}}" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_save')}}</button>
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="reorderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">{{trans('navmenu.new_reorder_point')}} </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        <form class="form-horizontal" method="POST" action="{{url('new-rm-reorder-point')}}">
            <div class="modal-body">
                @csrf
                <input type="hidden" name="raw_material_id" value="{{$material->id}}">
                <div class="col-sm-12">
                    <label for="register-username" class="form-label">{{trans('navmenu.reorder_point')}}</label>
                    <input id="register-username" type="number" min="0" name="reorder_point" required placeholder="{{trans('navmenu.hnt_reorder_point')}}" class="form-control form-control-sm">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_save')}}</button>
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="damageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">{{trans('navmenu.new_damage')}}</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        <form class="form-horizontal" method="POST" action="{{route('rm-damages.store')}}">
            <div class="modal-body">
                @csrf
                <div class="row">
                    <input type="hidden" name="raw_material_id" value="{{$material->id}}">
                    
                    <div class="col-sm-12">
                        <label for="register-username" class="form-label">{{trans('navmenu.quantity')}}<span style="color: red;"> *</span></label> 
                        <input id="damaged" type="number" min="0" step="any" name="quantity" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-3">
                    </div>
                    <div class="col-sm-12">
                        <label class="form-label">{{trans('navmenu.date')}}</label>
                        <select onchange="wegDam(this)" class="form-control form-select-sm mb-3">
                            <option value="auto">Auto</option>
                            <option value="manual">Manual</option>
                        </select>
                    </div>
                    <div class="col-sm-12" id="dam_date_field" style="display: none;">
                        <label class="form-label">{{trans('navmenu.pick_date')}}</label> 
                        <div class="input-group date">
                            <div class="inner-addon left-addon">
                                <i class="myaddon bx bx-calendar"></i>
                            </div>
                            <input type="text" name="dam_date" id="dam_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3">
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <label class="form-label">{{trans('navmenu.damage_cause')}}<span style="color: red;"> *</span></label>
                        <textarea name="reason" placeholder="{{trans('navmenu.hnt_damage_cause')}}" class="form-control form-control-sm mb-3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_save')}}</button>
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
            </div>
        </form>
        </div>
    </div>
</div>

    
@endsection 


<link rel="stylesheet" href="../css/DatePickerX.css">

<script src="../js/DatePickerX.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            // var $min = document.querySelector('[name="mnf_date"]'),
            //     $max = document.querySelector('[name="exp_date"]'),
              var  $dam = document.querySelector('[name="dam_date"]');

            // $min.DatePickerX.init({
            //     mondayFirst: true,
            //     // minDate    : new Date(),
            //     format     : 'yyyy-mm-dd',
            //     maxDate    :  new Date(),
            // });

            // $max.DatePickerX.init({
            //     mondayFirst: true,
            //     format     : 'yyyy-mm-dd',
            //     minDate    : $min,
            //     // maxDate    : new Date()
            // });

            $dam.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                maxDate    :  new Date(),
            });

        });
    </script>

    <script>

        function ImagetoPrint(source)
        {
            return "<html><head><scri"+"pt>function step1(){\n" +
                    "setTimeout('step2()', 10);}\n" +
                    "function step2(){window.print();window.close()}\n" +
                    "</scri" + "pt></head><body onload='step1()'>\n" +
                    "<img src='" + source + "' /></body></html>";
        }

        function PrintImage(source)
        {
            var Pagelink = "about:blank";
            var pwa = window.open(Pagelink, "_new");
            pwa.document.open();
            pwa.document.write(ImagetoPrint(source));
            pwa.document.close();
        }

    </script>
