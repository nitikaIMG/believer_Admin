@extends('main')

@section('heading')
    Offer Manager
@endsection('heading')

@section('sub-heading')
    Add New Offer
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('OffersController@getOffers') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary float-right"><i class="fad fa-eye"></i>&nbsp; View All Offers</a>
@endsection('card-heading-btn')

@section('content')


<div class="card">
    <div class="card-header">Add New Offer</div>
      <form id="demo-form2" data-parsley-validate="" action="<?php echo action('OffersController@addOffer')?>" method="post" class="card-body" enctype="multipart/form-data" autocomplete="">
       {{csrf_field()}}

        @include('alert_msg')

        <div class="sbp-preview">
            <div class="sbp-preview-content">
                <div class="row mx-0">
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            <label for="title">Title*</label>
                            <input class="form-control form-control-solid" id="first-name" type="text" placeholder="Title" id= 'title' name="title" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            <label for="description">Description*</label>
                            <input class="form-control form-control-solid" id="description" type="text" placeholder="Description" name="description" required>
                        </div>
                    </div>
                     <div class="col-md-6">
                        <div class="form-group my-3">
                            <label for="offercode">Offer Code*</label>
                             <input type="text" value="" placeholder="Enter Offer Code" class="form-control form-control-solid" id="offercode" name="offercode" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-4">
                            <label for="prize_type" class="mb-3">Prize Type*</label><br>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input checked required="" type="radio" id="prize_type" name="prize_type" class="custom-control-input" value="realcash" required>
                                <label class="custom-control-label fs-15" for="prize_type">Real Cash</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="prize_type2" name="prize_type" class="custom-control-input" value="bonus" required>
                                <label required="" class="custom-control-label fs-15" for="prize_type2">Bonus</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            <label for="minamount">Minimum Amount*</label>
                            <input class="form-control form-control-solid" id="minamount" onkeypress='return isNumberKey(event)' type="text" placeholder="Enter Minimum Amount" name="minamount" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            <label for="maxamount">Maximum Amount*</label>
                            <input class="form-control form-control-solid" id="maxamount" onkeypress='return isNumberKey(event)' type="text" placeholder="Enter Maximum Amount" name="maxamount" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-4">
                            <label for="bonus">Bonus*</label>
                            <input class="form-control form-control-solid" id="bonus" onkeypress='return isNumberKey(event)' type="text" placeholder="Enter bonus" name="bonus" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-4">
                            <label for="bonus_type" class="mb-3">Bonus Type*</label><br>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input checked required="" type="radio" id="bonus_type" name="bonus_type" class="custom-control-input" value="rs" required>
                                <label class="custom-control-label fs-15" for="bonus_type">Rupees</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="bonus_type2" name="bonus_type" class="custom-control-input" value="per" required>
                                <label required="" class="custom-control-label fs-15" for="bonus_type2">Percentage</label>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-6">
                        <div class="form-group my-4">
                            <label for="start_date">Start Date*</label>
                            <input class="form-control form-control-solid noAutoComplete" id="start_date" type="text" placeholder="Enter Start Date" name="start_date" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-4">
                            <label for="expire_date">Expire Date*</label>
                            <input class="form-control form-control-solid noAutoComplete" id="expire_date" type="text" placeholder="Enter expire date" name="expire_date" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-4">
                            <label for="user_time">Max Used*</label>
                            <input class="form-control form-control-solid" id="user_time" type="text" onkeypress='return isNumberKey(event)' placeholder="Enter Max used time" name="user_time" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-4">
                            <label for="amt_limit">Max Amount Limit*</label>
                            <input class="form-control form-control-solid " id="amt_limit" type="text" onkeypress='return isNumberKey(event)' placeholder="Enter max amount limit" name="amt_limit" required>
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
