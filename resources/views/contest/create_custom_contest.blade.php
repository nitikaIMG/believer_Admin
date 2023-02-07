@extends('main')

@section('heading')
    Contest Manager
@endsection('heading')

@section('sub-heading')
    Add custom contest
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('ContestController@create_custom_contest') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary text-uppercase" data-toggle='tooltip' title='View All Custom Contests'><i class="fad fa-eye"></i>&nbsp; View</a>
@endsection('card-heading-btn')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">Custom Contest</div>
                {{ Form::open(array('action' => 'ContestController@create_custom', 'method' => 'post','id' => 'j-forms','class'=>'card-body', 'enctype'=>'multipart/form-data' ))}}
                {{csrf_field()}}

                @include('alert_msg')
        
                <div class="sbp-preview">
                    <div class="sbp-preview-content">
                        <div class="row mx-0">

                            <div class="col-md-12">
                                <div class="form-group">
                                <label class="label-control text-bold">Match Name*</label>
                                <select name="matchkey" class="p-1 form-control form-control-solid selectpicker show-tick" data-container="body" data-live-search="true" title="Select Match" data-hide-disabled="true" required="">
                                <option value="">Select Match</option>
                                    <?php
                                        if(!empty($findalllistmatches->toarray())){
                                        foreach($findalllistmatches as $matches){

                                        ?>
                                        <option value="<?php echo $matches->matchkey; ?>"> {{$matches->name}}</option>
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
                                        <option value="Cricket">Cricket</option>
                                        <option value="Duo">Duo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12" id="duotype" style="display: none">
                                <div class="form-group my-3">
                                   <label class="label-control text-bold">Duo Type*</label>
                                    <select name="duotype" class="form-control form-control-solid p-1 selectpicker show-tick" required="" id="duotype">
                                    <option value="">Select Type</option>
                                        <option value="batsman">Batsman</option>
                                        <option value="bowler">Bowler</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group my-3" id="contestamo_contest">
                                    <label class="label-control text-bold fs-13">Contest Amount Type*</label><br>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" name="contest_type" checked="" value="Amount" id="customRadio1" class="custom-control-input">
                                        <label class="custom-control-label fs-15" for="customRadio1">Amount Based</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" name="contest_type" value="Percentage" id="customRadio2" class="custom-control-input">
                                        <label class="custom-control-label fs-15" for="customRadio2">Percentage based</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group my-3"  id="pricecard_contest">
                                <label class="label-control text-bold fs-13" >Contest Price Card Type*</label><br>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" name="pricecard_type" checked="" value="Amount" id="customRadio11" checked class="custom-control-input">
                                    <label class="custom-control-label fs-15" for="customRadio11">Amount Based</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" name="pricecard_type" value="Percentage" id="customRadio12" class="custom-control-input">
                                    <label class="custom-control-label fs-15" for="customRadio12">Percentage based</label>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-12">
                                <div class="form-group my-3">
                                <label class="label-control text-bold">Contest Category*</label>
                                <select name="contest_cat" class="p-1 form-control form-control-solid selectpicker show-tick" data-container="body" data-live-search="true" title="Select Contest Category" data-hide-disabled="true" required="" id="contest_cat">
                                <option value="">Select Contest Category</option>
                                @foreach($contest_cat as $value)
                                    <option value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
                                @endforeach
                                </select>
                                </div>
                            </div>
                            <div class="col-md-6" id="win_0">
                                <div class="form-group">
                                <label class="label-control text-bold">Entry Fee*</label>
                                <input type="number" id="first-name" min="0" class="form-control form-control-solid" placeholder="Enter Entry Fee" name="entryfee" value="" required="" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6" id="win_0">
                                <div class="form-group">
                                <label class="label-control text-bold">Offer Entry Fee*</label>
                                <input type="number" id="first-name" min="0" class="form-control form-control-solid" placeholder="Enter Offer Entry Fee" name="offerentryfee" value="" required="" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6" id="win_1">
                                <div class="form-group">
                                <label class="label-control text-bold">Winning Amount*</label>
                                <input type="number" min="0" class="form-control form-control-solid" placeholder="Enter Winning Amount"  name="win_amount" required="" value="" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="max_user">
                                <label class="label-control text-bold">Maximum number of users*</label>
                                <input type="number" id="ch33" min="0" class="form-control form-control-solid" placeholder="Enter Maximum Number Of Users"  name="maximum_user" value="0" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group my-3" id="pert" style="display:none;">
                                <label class="label-control text-bold">Percentage of users who will win (In case of percentage)*</label>
                                <input type="number" disabled="" id="ch1" class="form-control form-control-solid"  min="0" placeholder="Enter percentage of users winning this challenge"  name="winning_percentage" required="" value="" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-12 mt-3" id="team_limit" style="display:none">
                                <div class="form-group">
                                    <label class="label-control text-bold">Team Limit</label>
                                    <input disabled="" id="ch21" type="number" class="form-control form-control-solid"  name="team_limit" value="11" placeholder="Enter Team Limit" required="">
                                </div>
                            </div>
                        <div class="col-lg-3 col-md-6 col-12">
                          <div class="form-group my-3" id="me">
                              <div class="custom-control custom-checkbox custom-control-inline">
                                 <input type="checkbox" class="custom-control-input sel_all" id="select_all" name="multi_entry" value="1">
                                  <label class="custom-control-label fs-15" for="select_all">Multi Entry</label>
                              </div>
                          </div>
                        </div>

                        <div class="col-lg-3 col-md-6 col-12"  id="cc">
                            <div class="form-group my-3">
                               <div class="custom-control custom-checkbox custom-control-inline">
                                   <input type="checkbox" class="custom-control-input sel_all" id="select_all1" name="confirmed_challenge" value="1">
                                    <label class="custom-control-label fs-15" for="select_all1">Is Confirmed</label>
                                 </div>
                            </div>
                        </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="form-group my-3">
                           <div class="custom-control custom-checkbox custom-control-inline">
                               <input type="checkbox" class="custom-control-input sel_all" id="select_all2" name="is_running" value="1">
                                <label class="custom-control-label fs-15" for="select_all2">Is Running</label>
                             </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="form-group my-3">
                           <div class="custom-control custom-checkbox custom-control-inline">
                               <input type="checkbox" class="custom-control-input sel_all" id="select_all3" name="is_bonus" value="1">
                                <label class="custom-control-label fs-15" for="select_all3">Is Bonus Allowed</label>
                             </div>
                        </div>
                    </div>

                            <div class="col-sm-12">
                            <div class="form-group" id="bonuspercentage" style="display:none;">
                                <label class="label-control text-bold">Bonus Percentage (No need to enter % here. Just enter number here)*</label>
                                <input disabled="" id="ch2" type="text" class="form-control form-control-solid"  name="bonus_percentage" value="" placeholder="Enter Bonus Percentage" required="">
                            </div>
                            </div>
                            <div class="col-md-12 text-right mt-4 mb-2">
                                <a href="<?php echo action('ContestController@create_custom') ?>" class="btn btn-sm text-uppercase btn-warning"><i class="far fa-undo"></i>&nbsp; Reset</a>
                                <button type="submit" class=" btn btn-sm text-uppercase btn-success ml-1"><i class="far fa-check-circle" ></i>&nbsp; Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            {{ Form::open() }}
        </div>
    </div>
</div>

<script>
$('input[name=is_bonus]').change(function() {
  if($('input[name=is_bonus]').prop('checked')==true){
    $('input[name=bonus_percentage]').val('');
    $("input[name=bonus_percentage]").prop('disabled', false);
    $("#bonuspercentage").show();
  $("#multientryteam").show();
  }
  if($('input[name=is_bonus]').prop('checked')==false){
    $("input[name=bonus_percentage]").prop('disabled', true);
    $("#bonuspercentage").hide();
  $("#multientryteam").hide();
  }

});
$("input[name=contest_type]").change(function () {
    var a=$('input[name=contest_type]:checked').val();
  if ($('input[name=contest_type]:checked').val() == "Amount") {
      $('#pert').hide();
      $('#max_user').show();
      $("#ch1").prop('disabled', true);
      $("#ch33").prop('disabled', false);
  }
  if ($('input[name=contest_type]:checked').val() == "Percentage") {
      $('#pert').show();
      $('#max_user').hide();
      $("#ch1").prop('disabled', false);
      $("#ch33").prop('disabled', true);
  }
});
$('input[name=multi_entry]').change(function() { 
  if($('input[name=multi_entry]').prop('checked')==true){
    $("input[name=team_limit]").prop('disabled', false);
    $("#team_limit").stop().slideDown();
  }
  if($('input[name=multi_entry]').prop('checked')==false){
    $("input[name=team_limit]").prop('disabled', true);
    $("#team_limit").stop().slideUp();
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
</script>
<script type="text/javascript">
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
$("input[name=pricecard_type]").change(function () {
  if ($('input[name=pricecard_type]:checked').val() == "Percentage") {
      $('#cc').hide();
      $('input[name=confirmed_challenge]').prop('checked',true);
  }
  else{
    $('#cc').show();
    $('input[name=confirmed_challenge]').prop('checked',false);
  }
});

</script>
@endsection('content')
