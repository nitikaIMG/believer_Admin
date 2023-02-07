@extends('main')

@section('heading')
    Contest Manager
@endsection('heading')

@section('sub-heading')
    Add Card contest
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('ContestCardController@card_index') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary text-uppercase"><i class="fad fa-eye"></i>&nbsp; View All Card Contests</a>
@endsection('card-heading-btn')

@section('content')

@include('alert_msg')

<div class="card">
    <div class="card-header">Card Contest</div>
    	{{ Form::open(array('action' => 'ContestCardController@create_card', 'method' => 'post','id' => 'j-forms','class'=>'card-body', 'enctype'=>'multipart/form-data' ))}}
    	{{csrf_field()}}

        <div class="sbp-preview">
            <div class="sbp-preview-content py-2">
                <div class="row mx-0">
                    <div class="col-md-6" id="win_0">
                        <div class="form-group my-3">
                           <label class="label-control text-bold">Entry Fee*</label>
                            <input type="number" id="first-name" min="0" class="form-control form-control-solid" placeholder="Enter Entry Fee" name="entryfee" value="" required="" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-6" id="win_0">
                        <div class="form-group my-3">
                           <label class="label-control text-bold">Offer Entry Fee*</label>
                            <input type="number" id="first-name" min="0" class="form-control form-control-solid" placeholder="Enter Offer Entry Fee" name="offerentryfee" value="" required="" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-6" id="win_1">
                        <div class="form-group my-3">
                           <label class="label-control text-bold">Winning Amount*</label>
                           <input type="number" min="0" class="form-control form-control-solid" placeholder="Enter Winning Amount"  name="win_amount" required="" value="" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group my-3">
                           <div class="custom-control custom-checkbox custom-control-inline">
                               <input type="checkbox" class="custom-control-input sel_all" id="select_all3" name="is_bonus" value="1">
                                <label class="custom-control-label" for="select_all3">Is Bonus Allowed</label>
                             </div>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="form-group d-none" id="bonuspercentage">
                          <label class="label-control text-bold">Bonus Percentage (No need to enter % here. Just enter number here)*</label>
                           <input disabled="" id="ch2" type="text" class="form-control form-control-solid"  name="bonus_percentage" value="" placeholder="Enter Bonus Percentage"required="">
                      </div>
                    </div>
                    <div class="col-12 text-right mt-md-4 mb-md-2">
	                    <button type="reset" class="btn btn-sm text-uppercase btn-warning" onclick="window.location.href=window.location.href"><i class="far fa-undo"></i>&nbsp; Reset</a>
                        <button type="submit" class=" btn btn-sm text-uppercase btn-success float-right ml-1"><i class="far fa-check-circle"></i>&nbsp; Submit</button>
	                </div>
                </div>
            </div>
        </div>
    {{ Form::open() }}
</div>
<script>
$('input[name=is_bonus]').change(function() {
  if($('input[name=is_bonus]').prop('checked')==true){
    $('input[name=bonus_percentage]').val('');
    $("input[name=bonus_percentage]").prop('disabled', false);
    $("#bonuspercentage").removeClass('d-none');
  $("#multientryteam").show();
  }
  if($('input[name=is_bonus]').prop('checked')==false){
    $("input[name=bonus_percentage]").prop('disabled', true);
    $("#bonuspercentage").addClass('d-none');
  $("#multientryteam").hide();
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
$(window).ready(function() {
    var fantasy_type = $('#fantasy_type').val();
    get_contest_category(fantasy_type);
});
</script>
@endsection('content')
