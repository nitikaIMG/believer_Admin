@extends('main')

@section('heading')
    Contest Manager
@endsection('heading')

@section('sub-heading')
   Edit custom contest
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('ContestController@create_custom_contest') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary text-uppercase" data-toggle="tooltip" title="View All Custom Contests"><i class="fas fa-eye"></i>&nbsp; View</a>
@endsection('card-heading-btn')

@section('content')

<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">Edit Custom Contest</div>
            <div class="card-body">
            <?php $id = $challenge->id; ?>
         <form action="<?php echo action('ContestController@editcustomcontest',base64_encode(serialize($id))) ?>" method="post" id="j-forms">
          {{csrf_field()}}
                            
            @include('alert_msg')
                    
            <div class="sbp-preview">
                <div class="sbp-preview-content">
                    <div class="row mx-0">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="label-control text-bold">Match Name*</label>
                                <select name="contest_cat" class="form-control form-control-solid p-1 selectpicker show-tick" data-container="body" data-live-search="true" title="Select Match" data-hide-disabled="true" required="">
                                <option>Select Match</option>
                            <?php
                                if(!empty($findmatchnames->toarray())){
                                foreach($findmatchnames as $matches){

                                ?>
                                <option value="<?php echo $matches->matchkey; ?>" <?php if($matches->matchkey == $challenge->matchkey){ echo 'Selected';} ?>> {{$matches->name}}</option>
                                <?php
                                }
                            }
                            ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-3">
                               <label class="label-control text-bold">Fantasy Type*</label>
                                <select name="fantasy_type" class="form-control form-control-solid p-1 selectpicker show-tick" required="" id="fantasy_type">
                                <option value="">Select Type</option>
                                    <option value="Cricket" <?php if($challenge->fantasy_type == 'Cricket'){ echo 'Selected'; } ?>>Cricket</option>
                                    <option value="Duo" <?php if($challenge->fantasy_type == 'Duo'){ echo 'Selected'; } ?>>Duo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="duotype" style="display: none">
                            <div class="form-group my-3">
                               <label class="label-control text-bold">Duo Type*</label>
                                <select name="duotype" class="form-control form-control-solid p-1 selectpicker show-tick" id="duotype">
                                <option value="">Select Type</option>
                                    <option value="batsman" <?php if($challenge->duotype == 'batsman'){ echo 'Selected'; } ?>>Batsman</option>
                                    <option value="bowler" <?php if($challenge->duotype == 'bowler'){ echo 'Selected'; } ?>>Bowler</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-12 my-md-3">
                            <div class="form-group" id="contestamo_contest">
                                <label class="label-control text-bold">Contest Amount Type*</label><br>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" name="contest_type" checked="" value="Amount" <?php if($challenge->contest_type == 'Amount'){ echo 'checked'; } ?> id="customRadio1" class="custom-control-input">
                                    <label class="custom-control-label fs-15" for="customRadio1">Amount Based</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" name="contest_type" value="Percentage" <?php if($challenge->contest_type == 'Percentage'){ echo 'checked'; } ?> id="customRadio2" class="custom-control-input">
                                    <label class="custom-control-label fs-15" for="customRadio2">Percentage based</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12 my-md-3">
                            <div class="form-group"  id="pricecard_contest">
                            <label class="label-control text-bold" >Contest Price Card Type*</label><br>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" name="pricecard_type" value="Amount" <?php if($challenge->pricecard_type == 'Amount'){ echo 'checked'; } ?> id="customRadio11" class="custom-control-input">
                                <label class="custom-control-label fs-15" for="customRadio11">Amount Based</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" name="pricecard_type" value="Percentage" <?php if($challenge->pricecard_type == 'Percentage'){ echo 'checked'; } ?> id="customRadio12" class="custom-control-input">
                                <label class="custom-control-label fs-15" for="customRadio12">Percentage based</label>
                            </div>
                        </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                            <label class="label-control text-bold">Contest Category*</label>
                            <select name="contest_cat" class="p-1 form-control form-control-solid selectpicker show-tick" data-container="body" data-live-search="true" title="Select Contest Category" data-hide-disabled="true" required="" id="contest_cat">
                            <option>Select Contest Category</option>
                            @foreach($contest_cat as $value)
                            <option value="<?php echo $value->id; ?>" <?php if($value->id == $challenge->contest_cat){ echo 'Selected';} ?>><?php echo $value->name; ?></option>
                            @endforeach
                            </select>
                            </div>
                        </div>
                        <div class="col-6" id="win_0">
                            <div class="form-group">
                            <label class="label-control text-bold">Entry Fee*</label>
                                <input type="number" id="first-name" min="0" class="form-control form-control-solid" placeholder="Enter Entry Fee" name="entryfee" value="{{$challenge->entryfee}}" required="" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-6" id="win_0">
                            <div class="form-group">
                            <label class="label-control text-bold">Offer Entry Fee*</label>
                                <input type="number" id="first-name" min="0" class="form-control form-control-solid" placeholder="Enter Offer Entry Fee" name="offerentryfee" value="{{$challenge->offerentryfee}}" required="" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-6" id="win_1">
                            <div class="form-group">
                                <label class="label-control text-bold">Winning Amount*</label>
                                <input type="number" min="0" class="form-control form-control-solid" placeholder="Enter Winning Amount"  name="win_amount" required="" value="{{$challenge->win_amount}}" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-sm-6">
                        <div class="form-group" id="max_user">
                            <label class="label-control text-bold">Maximum number of users*</label>
                            <input type="number"  min="0" class="form-control form-control-solid" placeholder="Enter Maximum Number Of Users"  name="maximum_user" value="{{$challenge->maximum_user}}" autocomplete="off">
                        </div>
                        </div>

                        <div class="col-sm-12">
                        <div class="form-group"  id="pert" style="display: none;">
                            <label class="label-control text-bold">Percentage of users who will win (In case of percentage)*</label>
                            <input type="number" disabled="" id="ch1" class="form-control form-control-solid"  min="0" placeholder="Enter percentage of users winning this challenge"  name="winning_percentage" required="" value="{{$challenge->winning_percentage}}" autocomplete="off">
                        </div>
                        </div>

                        <div class="col-md-3 col-sm-6 col-12 mt-md-3 ">
                        <div class="form-group my-3" id="me">
                            <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" class="custom-control-input sel_all" id="select_all" name="multi_entry" value="1" <?php if($challenge->multi_entry == '1'){ echo 'checked';} ?>>
                                <label class="custom-control-label fs-15" for="select_all">Multi Entry</label>
                            </div>
                        </div>
                        </div>

                        <div class="col-md-3 col-sm-6 col-12 mt-md-3">
                        <div class="form-group my-3" id="cc">
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" class="custom-control-input sel_all" id="select_all1" name="confirmed_challenge" value="1" <?php if($challenge->confirmed_challenge == '1'){ echo 'checked';} ?>>
                                <label class="custom-control-label fs-15" for="select_all1">Is Confirmed</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-12 mt-md-3">
                        <div class="form-group my-3">
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" class="custom-control-input sel_all" id="select_all2" name="is_running" value="1" <?php if($challenge->is_running == '1'){ echo 'checked';} ?>>
                                <label class="custom-control-label fs-15" for="select_all2">Is Running</label>
                            </div>
                        </div>
                    </div>

                        <div class="col-md-3 col-sm-6 col-12 mt-md-3">
                        <div class="form-group my-3">
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" class="custom-control-input sel_all" id="select_all3" name="is_bonus" value="1" <?php if($challenge->is_bonus == '1'){ echo 'checked';} ?>>
                                <label class="custom-control-label fs-15" for="select_all3">Is Bonus Allowed</label>
                            </div>
                        </div>
                    </div>

                        <div class="col-sm-12">
                        <div class="form-group" id="bonuspercentage" style="display:none;">
                            <label class="label-control text-bold">Bonus Percentage (No need to enter % here. Just enter number here)*</label>
                            <input disabled="" id="ch2" type="text" class="form-control form-control-solid"  name="bonus_percentage"  value="{{$challenge->bonus_percentage}}"  placeholder="Enter Bonus Percentage" required="">
                        </div>
                        </div>
                        <div class="col-12 text-right mt-4 mb-2">
                            <button class="btn btn-sm btn-sm btn-warning"  onclick="window.location.href=window.location.href"><i class="fa fa-undo">&nbsp;</i>Reset</button>
                            <button class="btn btn-sm btn-sm btn-success" type="submit"><i class="far fa-check-circle"></i>&nbsp; Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
 if($('input[name=is_bonus]').prop('checked')==true){

    $("input[name=bonus_percentage]").prop('disabled', false);
    $("#bonuspercentage").show();
  }
  if($('input[name=is_bonus]').prop('checked')==false){
      $('input[name=bonus_percentage]').val('');
    $("input[name=bonus_percentage]").prop('disabled', true);
    $("#bonuspercentage").hide();
  }
$('input[name=is_bonus]').change(function() {
  if($('input[name=is_bonus]').prop('checked')==true){
    $('input[name=bonus_percentage]').val('');
    $("input[name=bonus_percentage]").prop('disabled', false);
    $("#bonuspercentage").show();
  }
  if($('input[name=is_bonus]').prop('checked')==false){
    $("input[name=bonus_percentage]").prop('disabled', true);
    $("#bonuspercentage").hide();
  }

});
  if ($('input[name=contest_type]:checked').val() == "Amount") {
      $('#pert').hide();
      $('#max_user').show();
      $("#ch1").prop('disabled', true);
  }
  if ($('input[name=contest_type]:checked').val() == "Percentage") {
      $('#pert').show();
      $('#max_user').hide();
      $("#ch1").prop('disabled', false);
  }
$("input[name=contest_type]").change(function () {
  if ($('input[name=contest_type]:checked').val() == "Amount") {
      $('#pert').hide();
      $('#max_user').show();
      $("#ch1").prop('disabled', true);
  }
  if ($('input[name=contest_type]:checked').val() == "Percentage") {
      $('#pert').show();
      $('#max_user').hide();
      $("#ch1").prop('disabled', false);
  }
});

$("#fantasy_type").change(function(){
  if ($("#fantasy_type").val() == "Duo") {
      $('#contestamo_contest').hide();
      $('#pricecard_contest').hide();
      $('#me').hide();
      $('#cc').hide();
      $("#team_limit").stop().slideUp();
      $('#duotype').show();
  }
  if ($("#fantasy_type").val() == "Cricket") {
    $('#contestamo_contest').show();
      $('#pricecard_contest').show();
      $('#me').show();
      $('#cc').show();
      $('#duotype').hide();
  }
});
$(document).ready(function() {
    if ($("#fantasy_type").val() == "Duo") {
      $('#contestamo_contest').hide();
      $('#pricecard_contest').hide();
      $('#me').hide();
      $('#cc').hide();
      $("#team_limit").stop().slideUp();
      $('#duotype').show();
  }
  if ($("#fantasy_type").val() == "Cricket") {
      $('#contestamo_contest').show();
      $('#pricecard_contest').show();
      $('#me').show();
      $('#cc').show();
      $('#duotype').hide();
  }
});
</script>
<script type="text/javascript">
 if ($('input[name=contest_type]:checked').val() == "Amount") {
      $('#pert').hide();
      $('#max_user').show();
      $("#ch1").prop('disabled', true);
  }
  if ($('input[name=contest_type]:checked').val() == "Percentage") {
      $('#pert').show();
      $('#max_user').hide();
      $("#ch1").prop('disabled', false);
  }
$("input[name=contest_type]").change(function () {
  if ($('input[name=contest_type]:checked').val() == "Amount") {
      $('#pert').hide();
      $('#max_user').show();
      $("#ch1").prop('disabled', true);
  }
  if ($('input[name=contest_type]:checked').val() == "Percentage") {
      $('#pert').show();
      $('#max_user').hide();
      $("#ch1").prop('disabled', false);
  }
});
</script>
<script type="text/javascript">
  if ($('input[name=contest_type]:checked').val() == "Amount") {
      $('#pricecard_contest').show();
      document.getElementById("pricecard_contest").style.display = "block";
  }
  if ($('input[name=contest_type]:checked').val() == "Percentage") {
      $('#pricecard_contest').hide();
      document.getElementById("pricecard_contest").style.display = "none";
  }
  $("input[name=contest_type]").change(function () {
  if ($('input[name=contest_type]:checked').val() == "Amount") {
      $('#pricecard_contest').show();
      document.getElementById("pricecard_contest").style.display = "block";
  }
  if ($('input[name=contest_type]:checked').val() == "Percentage") {
      $('#pricecard_contest').hide();
      document.getElementById("pricecard_contest").style.display = "none";
  }
});
</script>
<script>
    function check(){
        $('#checks').html('*sorry you can not edit this!');
        $('#checks').delay(3200).fadeOut(300);

    }
</script>
@endsection('content')
