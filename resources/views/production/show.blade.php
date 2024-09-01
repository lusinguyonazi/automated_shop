@extends('layouts.prod')

@section('content')

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
    <div class="col">
        <div class="card radius-10 ">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">{{trans('navmenu.mro_cost')}}</p>
                        <h4 class="my-1">{{number_format($mrouse->sum('total_cost'))}} </h4>
                    </div>
                    <div class="widgets-icons bg-light-success text-success ms-auto">
                        <i class="bx bx-money"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card radius-10 ">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">{{trans('navmenu.rm_cost')}}</p>
                        <h4 class="my-1">{{number_format($rmuse->sum('total_cost'))}}</h4>
                    </div>
                    <div class="widgets-icons bg-light-primary text-primary ms-auto">
                        <i class="bx bxs-box"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card radius-10 ">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">{{trans('navmenu.pm_cost')}}</p>
                        <h4 class="my-1">{{ number_format($pmuse->sum('total_cost'))}}</h4>
                    </div>
                    <div class="widgets-icons bg-light-dark text-dark ms-auto">
                        <i class="bx bxs-alarm-exclamation"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
       <div class="card radius-10 ">
           <div class="card-body">
               <div class="d-flex align-items-center">
                   <div>
                       <p class="mb-0 text-secondary">{{trans('navmenu.batch_no')}}</p>
                       <h4 class="my-1">{{$prod_cost->prod_batch}}
                       </h4>
                   </div>
                   <div class="widgets-icons bg-light-danger text-danger ms-auto">
                       <i class="bx bxs-trash"></i>
                   </div>
               </div>
               <div class="text-center">
                  <span class="">{{date('d M, Y', strtotime($prod_cost->date))}}</span>
               </div>
           </div>
       </div>
    </div>
</div>

<div class="row pb-8" >
    <div class="col-md-12">
        <div class="card radius-6">
            <div class="card-header" >
                <div class="card-title">{{trans('navmenu.product_list')}}</div> 
            </div>
            <div class="card-body"> 
                <div class="row">
                    <div class="col-md-6" >
                        <h4>{{trans('navmenu.total_production_volume')}} : 
                        <span>{{$prod_cost->total_prod_qty}}</span>
                        </h4>
                    </div>  
                    <div class="col-md-6" >
                        <h4>Total Cost of Production 
                            <span > : {{number_format($mrouse->sum('total_cost')+ 
                                    $rmuse->sum('total_cost')+ $pmuse->sum('total_cost')
                                    )}}
                            </span>
                        </h4>
                    </div>    
                </div>            
                <div>
                    <table class="table">
                        <thead>
                            <th>#</th>
                            <th>{{trans('navmenu.product_name')}}</th>
                            <th>{{trans('navmenu.ratio')}}</th>
                            <th>{{trans('navmenu.unit_packed')}}</th>
                            <th>{{trans('navmenu.quantity')}}</th>
                            <th>{{trans('navmenu.unit_cost')}}</th>
                            <th>{{trans('navmenu.profit_margin')}}</th>
                            <th>{{trans('navmenu.total') }}</th>
                            <th>{{trans('navmenu.product_price')}}</th>
                            <th>{{trans('navmenu.total') }}</th>
                        </thead>
                        <tbody>
                            <?php $total_margin = 0; $total_price = 0; ?>
                             @foreach($prod_cost_items as $ky => $prod_cost_item)
                             <?php 
                                $total_margin += $prod_cost_item->profit_margin*$prod_cost_item->quantity;
                                $total_price += $prod_cost_item->selling_price*$prod_cost_item->quantity; 
                             ?>
                            <tr>
                            <td>{{$ky+1}}</td>
                            <td>{{$prod_cost_item->name}}</td>
                            <td>{{number_format((($prod_cost_item->unit_packed * $prod_cost_item->quantity )/$prod_cost->total_prod_qty)*100 , 2, '.' , '')}} %</td>
                            <td>{{$prod_cost_item->unit_packed}}</td>
                            <td>{{$prod_cost_item->quantity}}</td>
                            <td>{{$prod_cost_item->cost_per_unit}}</td>
                            <td>{{$prod_cost_item->profit_margin}}</td>
                            <td>{{number_format($prod_cost_item->profit_margin*$prod_cost_item->quantity, 2, '.', ',')}}</td>
                            <td>{{$prod_cost_item->selling_price}}</td>
                            <td>{{number_format($prod_cost_item->selling_price*$prod_cost_item->quantity, 2, '.', ',')}}</td>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <th></th>
                            <th><b>{{trans('navmenu.total')}}</b></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><b>{{number_format($total_margin, 2, '.', ',') }}</b></th>
                            <th></th>
                            <th><b>{{number_format($total_price, 2, '.', ',') }}</b></th>
                        </tfoot>
                    </table> 
                </div>
            </div>
        </div>
    </div>    
</div>

<!-- =========================================================== -->

<div class="row">
    <div class="col-md-12">
        <div class="card radius-10">
            <div class="card-body">   
                <ul class="nav nav-tabs nav-success " role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#tab_1-1" class="nav-link active" role="tab" aria-selected="true" data-bs-toggle="tab">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon">
                                    <div class="tab-title font-15">{{trans('navmenu.mro_items')}}</div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#tab_2-2" class="nav-link " role="tab" aria-selected="true" data-bs-toggle="tab">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon">
                                    <div class="tab-title font-15"> 
                                        {{trans('navmenu.raw_materials')}}  
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#tab_3-3" class="nav-link " role="tab" aria-selected="true" data-bs-toggle="tab">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon">
                                    <div class="tab-title font-15">
                                        {{trans('navmenu.packing_materials')}} 
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
                    
                <div class="tab-content py-3">
                    <div class="tab-pane fade show active" id="tab_1-1" role="tabpanel">
                        <table id="mro_used" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <th>#</th>
                                <th>{{trans('navmenu.mro_name')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                <th style="text-align: center;">UOM</th>
                                <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                            </thead>
                            <tbody>
                            @foreach($mros as  $mro)
                                @foreach($mro as $key => $mroitem)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$mroitem->name}}</td>
                                        <td style="text-align: center;">{{$mroitem->quantity}}</td>
                                        <td><span style="color: gray; text-align: center;">
                                                {{$mroitem->basic_unit}}
                                            </span>
                                        </td>
                                        <td style="text-align: center;">{{number_format($mroitem->unit_cost)}}</td>
                                        <td style="text-align: center;">{{number_format($mroitem->total)}}</td>
                                        <td style="text-align: center;">{{$mroitem->date}}</td>
                                    </tr>
                                @endforeach
                            @endforeach 
                            </tbody>
                        </table>
                    </div>
                        
                    <div class="tab-pane fade " id="tab_2-2" role="tabpanel">
                        <table id="rm_used" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <th>#</th>
                                <th>{{trans('navmenu.material_name')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                <th style="text-align: center;">UOM</th>
                                <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                            </thead>
                            <tbody>
                            @foreach($rms as  $rm)
                                @foreach($rm as $ref => $rmitem)
                                    <tr>
                                        <td>{{$ref+1}}</td>
                                        <td>{{$rmitem->name}}</td>
                                        <td style="text-align: center;">{{$rmitem->quantity}}</td>
                                        <td><span style="color: gray; text-align: center;">
                                            {{$rmitem->basic_unit}}
                                            </span>
                                        </td>
                                        <td style="text-align: center;">{{number_format($rmitem->unit_cost)}}</td>
                                        <td style="text-align: center;">{{number_format($rmitem->total)}}</td>
                                        <td style="text-align: center;">{{$rmitem->date}}</td>
                                    </tr>
                                @endforeach
                            @endforeach 
                            </tbody>
                        </table>
                    </div>

                    <div class="tab-pane fade " id="tab_3-3" role="tabpanel">
                        <table id="pm_used" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <th>#</th>
                                <th>{{trans('navmenu.packing_name')}}</th>
                                <th>{{trans('navmenu.product_packed')}}</th>
                                <th>{{trans('navmenu.product_basic_unit')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                <th style="text-align: center;">UOM</th>
                                <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                            </thead>
                            <tbody>
                            @foreach($pms as  $pmv)
                                @foreach($pmv as $index => $pm )
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$pm->name}}</td>
                                        <td>{{$pm->product_name}}</td>
                                        <td>{{$pm->basic_unit}}</td>
                                        <td>{{$pm->quantity}}</td>
                                        <td>{{$pm->package_unit}}</td>
                                        <td>{{$pm->unit_cost}}</td>
                                        <td>{{$pm->total}}</td>
                                        <td>{{$pm->date}}</td>
                                    </tr> 
                                @endforeach
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

