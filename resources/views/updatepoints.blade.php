@extends('main')

@section('heading')
    Point
@endsection('heading')

@section('sub-heading')
    Add Point
@endsection('sub-heading')

@section('content')


<div class="card">
    <div class="card-header">Add Point</div>
       <form enctype="multipart/form-data" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" action="<?php echo action('AddpointController@add_pointt')?>" method="post">
      {{csrf_field()}}


        @include('alert_msg')


        <div class="sbp-preview">
            <div class="sbp-preview-content">
                <div class="row mx-0">
                    <div class="col-12">
                        <div class="form-group my-3">
                            <label for="firt-name">Points*</label>
                             <textarea required="required" name="updation_points" class="form-control form-control-solid ckeditor" type="text" placeholder="Enter points" onkeypress='return isNumberKey(event)'><?php if(!empty($version->updation_points)){ echo $version->updation_points; } ?></textarea>
                        </div>
                    </div>
                    <div class="col-12 text-right mt-4 mb-2">
                      <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                  </div>
                </div>
            </div>
        </div>
     </form>
</div>

@endsection('content')
