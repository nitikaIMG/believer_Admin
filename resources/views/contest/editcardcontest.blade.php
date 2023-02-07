@extends('main')

@section('heading')
    Contest Manager
@endsection('heading')

@section('sub-heading')
   Edit Card contest
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('ContestCardController@card_index') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary text-uppercase"><i class="fad fa-eye"></i>&nbsp; View All Card Contests</a>
@endsection('card-heading-btn')

@section('content')

<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">Card Contest</div>
            <div class="card-body">
            <form action="<?php echo action('ContestCardController@editcardcontest',base64_encode(serialize($challenge->id))) ?>" method="post">
                {{csrf_field()}}
                    
            @include('alert_msg')
                
            <div class="sbp-preview">
                <div class="sbp-preview-content">
                    <div class="row mx-0">
                        <div class="col-6" id="win_0">
                            <div class="form-group ">
                            <label class="label-control text-bold">Entry Fee*</label>
                            <input type="number" id="first-name" min="0" class="form-control form-control-solid" placeholder="Enter Entry Fee" name="entryfee"  value="{{$challenge->entryfee}}" required="" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-6" id="win_0">
                            <div class="form-group ">
                            <label class="label-control text-bold">Offer Entry Fee*</label>
                            <input type="number" id="first-name" min="0" class="form-control form-control-solid" placeholder="Enter Offer Entry Fee" name="offerentryfee"  value="{{$challenge->offerentryfee}}" required="" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-6" id="win_1">
                            <div class="form-group">
                                <label class="label-control text-bold">Winning Amount*</label>
                                <input type="number" min="0" class="form-control form-control-solid" placeholder="Enter Winning Amount"  name="win_amount" required="" value="{{$challenge->win_amount}}" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group my-3">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input sel_all" id="select_all3" name="is_bonus" value="1" <?php if($challenge->is_bonus == '1'){ echo 'checked';} ?>>
                                    <label class="custom-control-label" for="select_all3">Is Bonus Allowed</label>
                                </div>
                            </div>
                        </div>

                            <div class="col-12 ">
                            <div class="form-group" id="bonuspercentage" style="display: none;">
                                <label class="label-control text-bold">Bonus Percentage (No need to enter % here. Just enter number here)*</label>
                                <input disabled="" id="ch2" type="text" class="form-control form-control-solid"  name="bonus_percentage"  value="{{$challenge->bonus_percentage}}"  placeholder="Enter Bonus Percentage" style="color:black;">
                            </div>
                            </div>
                            <div class="col-12 text-right mt-4 mb-2">
                                <button type="reset" class="btn btn-sm btn-warning" onclick="window.location.href=window.location.href"><i class="far fa-undo"></i>&nbsp;Reset</button>
                                <button type="submit" class=" btn btn-sm btn-success ml-1"><i class="far fa-check-circle" ></i>&nbsp;   Submit</button>
                            </div>
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
      $("#ch233").prop('disabled', false);
  }
  if ($('input[name=contest_type]:checked').val() == "Percentage") {
      $('#pert').show();
      $('#max_user').hide();
      $("#ch1").prop('disabled', false);
      $("#ch233").prop('disabled', true);
  }
$("input[name=contest_type]").change(function () {
  if ($('input[name=contest_type]:checked').val() == "Amount") {
      $('#pert').hide();
      $('#max_user').show();
      $("#ch1").prop('disabled', true);
      $("#ch233").prop('disabled', false);
  }
  if ($('input[name=contest_type]:checked').val() == "Percentage") {
      $('#pert').show();
      $('#max_user').hide();
      $("#ch1").prop('disabled', false);
      $("#ch233").prop('disabled', true);
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

function get_contest_category(value){
    var fantasy_type = value;
    $.ajax({
    type:'POST',
    url:'<?php echo asset('my-admin/get_contest_category');?>',
    data:'_token=<?php echo csrf_token();?>&fantasy_type='+fantasy_type,
    success:function(data){
        $('#contest_cat').html('<option value="">Select Contest Category</option>');

        for(var i = 0; i < data.length; i++) {
            // alert(data[i]['id']);
            $('#contest_cat').append('<option value="'+data[i]['id']+'">'+data[i]['name']+'</option>');
        }

        // <option value="">Select Team</option>
    }
    });
}
</script>
@endsection('content')
