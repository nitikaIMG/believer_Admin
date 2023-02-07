@extends('main')

@section('heading')
    Offer Manager
@endsection('heading')

@section('sub-heading')
    Edit Offer
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('OffersController@getOffers') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary float-right"><i class="fad fa-eye"></i>&nbsp; View All Offers</a>
<a  href="<?php echo action('OffersController@addOffer') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary float-right"><i class="fa fa-plus"></i>&nbsp; Add New Offer</a>
@endsection('card-heading-btn')

@section('content')

<div class="card">
    <div class="card-header">Edit Offer</div>
    <?php $offer_id = $ofers->id; ?>
      <form class="card-body" action="<?php echo action('OffersController@editoffer',base64_encode(serialize($offer_id)))?>" method="post">
      {{csrf_field()}}

        @include('alert_msg')

        <div class="sbp-preview">
            <div class="sbp-preview-content">
                <div class="row mx-0">
                    <div class="col-6">
                        <div class="form-group my-3">
                           <label for="title">Title*</label>
                            <input class="form-control form-control-solid" id="title" type="text" placeholder="Enter Job Title" name="title" required value="{{$ofers->title}}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            <label for="description">Description*</label>
                            <input class="form-control form-control-solid" id="description" type="text" placeholder="Description*" name="description" required datetimepickerget value="{{$ofers->description}}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            <label for="offercode">Offer Code*</label>
                            <input class="form-control form-control-solid" id="offercode" type="text" placeholder="Enter Offer Code" name="offercode" required value="{{$ofers->offercode}}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-4">
                            <label for="prize_type" class="mb-3">Prize Type*</label><br>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input checked required="" type="radio" id="prize_type" name="prize_type" class="custom-control-input" value="realcash" required <?php if($ofers->prize_type=='realcash'){ echo 'checked';}?>>
                                <label class="custom-control-label fs-15" for="prize_type">Real Cash</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="prize_type2" name="prize_type" class="custom-control-input" value="bonus" required <?php if($ofers->prize_type=='bonus'){ echo 'checked';}?> >
                                <label required="" class="custom-control-label fs-15" for="prize_type2">Bonus</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            <label for="minamount">Minimum Amount*</label>
                            <input class="form-control form-control-solid" id="minamount" type="text" placeholder="Enter Minimum Amount" name="minamount" onkeypress='return isNumberKey(event)' required value="{{$ofers->minamount}}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            <label for="maxamount">Maximum Amount*</label>
                            <input class="form-control form-control-solid" id="maxamount" type="text" placeholder="Enter Maximum Amount" name="maxamount" required value="{{$ofers->maxamount}}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            <label for="bonus">Bonus*</label>
                            <input class="form-control form-control-solid" id="bonus" type="text" placeholder="Enter bonus" name="bonus" onkeypress='return isNumberKey(event)' required value="{{$ofers->bonus}}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-4">
                            <label for="bonus_type" class="mb-3">Bonus Type*</label><br>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input checked required="" type="radio" id="bonus_type" name="bonus_type" class="custom-control-input" value="rs" required <?php if($ofers->bonus_type=='rs'){ echo 'checked';}?>>
                                <label class="custom-control-label fs-15" for="bonus_type">Rupees</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="bonus_type2" name="bonus_type" class="custom-control-input" value="per" required <?php if($ofers->bonus_type=='per'){echo 'checked';}?>>
                                <label required="" class="custom-control-label fs-15" for="bonus_type2">Percentage</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            <label for="start_date">Start Date*</label>
                            <input class="form-control form-control-solid" id="start_date" type="text" placeholder="Enter Start Date" name="start_date" required value="{{$ofers->start_date}}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            <label for="expire_date">Expire Date*</label>
                            <input class="form-control form-control-solid" id="expire_date" type="text" placeholder="Enter Expire Date" name="expire_date" required value="{{$ofers->expire_date}}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            <label for="user_time">Max Used*</label>
                            <input class="form-control form-control-solid" id="user_time" type="text" placeholder="Enter Max Used" name="user_time" required  value="{{$ofers->user_time}}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            <label for="offercode">Max Amount Limit*</label>
                            <input class="form-control form-control-solid" id="amt_limit" type="text" placeholder="Enter max amount limit" name="amt_limit" required  value="{{$ofers->amt_limit}}" autocomplete="off">
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
