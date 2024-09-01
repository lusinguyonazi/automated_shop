@extends('layouts.app')
<script type="text/javascript">
    function confirmDelete(id){
        document.getElementById('delete-form-'+id).submit();
    }
</script>
@section('content')

    <div class="row" ng-controller="SearchItemCtrl">
        <div class="col-md-12">
          
            <div class="card radius-10">

                <!-- /.box-header -->
                <div class="card-body">
                    <form class="form" name="orderform" method="POST" action="{{ route('purchase-orders.update', encrypt($porder->id))}}">
                        @csrf
                        {{ method_field('PATCH') }}
                                                
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="supplier_id" class="form-label">{{trans('navmenu.supplier')}}</label>
                                <select name="supplier_id" id="supplier" required class="form-control form-control-sm mb-3" onchange="changeSupplier(this)">
                                    @if(!is_null(App\Supplier::find($porder->supplier_id)))
                                    <option value="{{$porder->supplier_id}}">{{App\Supplier::find($porder->supplier_id)->name}}</option>
                                    @else
                                    <option>{{trans('navmenu.unknown')}}</option>
                                    @endif
                                    @foreach($suppliers as $supplier)
                                    <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status" class="form-label">{{trans('navmenu.status')}}</label>
                                <select name="status" id="status" required class="form-control form-control-sm mb-3" onchange="changeSupplier(this)">
                                    @foreach($statuses as $status)
                                    @if($porder->status == $status['value'])
                                    <option selected>{{$status['value']}}</option>
                                    @else
                                    <option>{{$status['value']}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="comments" class="form-label">{{trans('navmenu.comments')}}</label>
                                <textarea  class="form-control form-control-sm mb-3" name="comments" id="comments" rows="1">{{$porder->comments}}</textarea>
                            </div>
                        </div>

                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_save')}}</button>
                                        <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>      
    </div>
@endsection