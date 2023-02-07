@extends('main')

@section('heading')
    Addcash bonus Manager
@endsection('heading')

@section('sub-heading')
    View Addcash bonus
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('AddcashController@viewaddcashbonus') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary float-right"><i class="fad fa-eye"></i>&nbsp; View All Addcash bonus</a>
@endsection('card-heading-btn')

@section('content')


<div class="card">
    <div class="card-header">Add New Addcash bonus</div>
      <form id="demo-form2" data-parsley-validate="" action="<?php echo action('AddcashController@addcash_bonus')?>" method="post" class="card-body" enctype="multipart/form-data" autocomplete="">
       {{csrf_field()}}

        @include('alert_msg')

        <div class="sbp-preview">
            <div class="sbp-preview-content">
                <div class="row mx-0">
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            <label for="title">Amount Range*</label>
                            <input class="form-control form-control-solid" id="first-name" type="text" placeholder="Enter Amount Range" id= 'title' name="amt_range" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            <label for="description">Percentage*</label>
                            <input class="form-control form-control-solid" id="description" type="text" placeholder="Percentage" name="percentage" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-4">
                            <label for="user_time">Max Used</label>
                            <input class="form-control form-control-solid" id="max_used" type="text" onkeypress='return isNumberKey(event)' placeholder="Enter Max used time" name="max_used">
                        </div>
                    </div>
                    <div class="col-md-12 text-right mt-4 mb-2">
                      <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                  </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
$(function() {
    $('.noAutoComplete').attr('autocomplete', 'off');
});
</script>
@endsection
