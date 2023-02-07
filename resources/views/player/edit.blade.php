@extends('main')

@section('heading')
    Player Manager
@endsection('heading')

@section('sub-heading')
    Edit Player
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('PlayerController@view_player') ?>" class="btn btn-sm  btn-sm rounded-pill btn-light font-weight-bold text-primary float-right"><i class="fad fa-eye"></i>&nbsp; View All Players</a>
@endsection('card-heading-btn')

@section('content')

<div class="card">
    <div class="card-header">Edit Player</div>
      <form class="card-body" action="<?php echo action('PlayerController@edit_player',base64_encode(serialize($player->id)))?>" enctype='multipart/form-data' method="post">
    	{{csrf_field()}}

        @include('alert_msg')


        <div class="sbp-preview">
            <div class="sbp-preview-content">
                <div class="row mx-0">
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            {{ Form::label('Player key*','Player key*',array('class'=>'control-label text-bold','for'=>'disabledTextInput'))}}<br>
                            {{ Form::text('players_key',$player->players_key, array('value'=>'$player->players_key','class'=>'form-control form-control-solid', 'id'=>'disabledTextInput','disabled'=>'disabled'))}}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            {{ Form::label('Player Name*','Player Name*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('player_name',$player->player_name, array('value'=>'','required'=>'','placeholder'=>'Enter player name here','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            {{ Form::label('Batting Style','Batting Style',array('class'=>'control-label form-label'))}}
                            {{ Form::text('battingstyle',$player->battingstyle, array('value'=>'','placeholder'=>'Enter player batting style here','class'=>'form-control form-control-solid form-input1'))}}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            {{ Form::label('Bowling style','Bowling style',array('class'=>'control-label form-label'))}}
                            {{ Form::text('bowlingstyle',$player->bowlingstyle, array('value'=>'','placeholder'=>'Enter player bowling style here','class'=>'form-control form-control-solid form-input1'))}}
                        </div>
                    </div> 
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            {{ Form::label('Date of Birth*','Date of Birth*',array('class'=>'control-label text-bold'))}}
                            <?php if($player->dob!="0000-00-00"){
                                ?>
                            {{ Form::text('dob',$player->dob,array('id'=>'dob-date','value'=>'2004-06-14 0:0:0','required'=>'','autocomplete'=>'off','placeholder'=>'Enter date of birth','class'=>'form-control form-control-solid','pattern'=>'/([0-2][0-9]{3})\-([0-1][0-9])\-([0-3][0-9])T([0-5][0-9])\:([0-5][0-9])\:([0-5][0-9])(Z|([\-\+]([0-1][0-9])\:00))/'))}}
                            <?php
                            } else{
                            ?>
                            {{ Form::date('dob',null,array('value'=>'2004-06-14','required'=>'','placeholder'=>'Enter date of birth','class'=>'form-control form-control-solid form-control form-control-solid-rounded','pattern'=>'/([0-2][0-9]{3})\-([0-1][0-9])\-([0-3][0-9])T([0-5][0-9])\:([0-5][0-9])\:([0-5][0-9])(Z|([\-\+]([0-1][0-9])\:00))/'))}}
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            {{ Form::label('Country*','Country*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('country',$player->country,array('value'=>'','required'=>'','placeholder'=>'Enter country','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            {{ Form::label('Credit*','Credit*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('credit',$player->credit,array('value'=>'','required'=>'','placeholder'=>'enter player credit here','onkeypress'=>'return isNumberKey(event)','class'=>'form-control form-control-solid', 'autocomplete' => 'off'))}}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <?php $f_type = 'Cricket';
                        if($f_type=='Cricket'){?>
                            <div class="form-group my-3">
                                {{ Form::label('Select Role*','Select Role*',array('class'=>'control-label text-bold'))}}
                                <select class="form-control form-control-solid p-1 selectpicker show-tick" data-container="body" data-live-search="true" title="Select Theme" data-hide-disabled="true" name="role">
                                <option disabled selected value="">Select Role</option>
                                <option value="batsman" <?php if($player->role=='batsman'){ echo 'selected'; }?>>Batsman</option>
                                <option value="bowler" <?php if($player->role=='bowler'){ echo 'selected'; }?>>Bowler</option>
                                <option value="allrounder" <?php if($player->role=='allrounder'){ echo 'selected'; }?>>All rounder</option>
                                <option value="keeper" <?php if($player->role=='keeper'){ echo 'selected'; }?>>Wicket Keeper</option>
                                    </select>
                            </div>
                            <?php }else{ ?>
                            <div class="form-group my-3">
                                {{ Form::label('Select Role*','Select Role*',array('class'=>'control-label text-bold'))}}
                                <select class="form-control form-control-solid p-1 selectpicker show-tick" data-container="body" data-live-search="true" title="Select Theme" data-hide-disabled="true" name="role">
                                <option disabled selected value="">Select Role</option>
                                <option value="goalkeeper" <?php if($player->role=='goalkeeper'){ echo 'selected'; }?>>Goal Keeper</option>
                                <option value="defender" <?php if($player->role=='defender'){ echo 'selected'; }?>>Defender</option>
                                <option value="midfielder" <?php if($player->role=='midfielder'){ echo 'selected'; }?>>Mid Fielder</option>
                                <option value="striker" <?php if($player->role=='striker'){ echo 'selected'; }?>>Striker</option>
                                    </select>
                            </div>
                            <?php } ?>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="row mx-0 justify-content-center ">


                        <?php

                            $input = ['image'];
                            $defaultLogo = ['player_image.png'];
                            $labels = ['Player image'];

                            for($i=0; $i<count($input);$i++){?>

                            <?php $setting2= $input[$i];?>
                            <?php $defaultImages= $defaultLogo[$i];?>

                            <?php
                                if( !empty($player->$setting2) ) {
                                    $image = $player->$setting2;
                                } else {
                                    $image = $defaultLogo[$i];
                                }

                            ?>


                            <div class="col-md col-sm-4 col-6">
                                <div class="form-group">
                                    <div class="row justify-content-center py-0">
                                        <h1 class="fs-14 font-weight-bold text-center mt-3 col-12">{{ $labels[$i] }}</h1>
                                        <div class="avatar-upload col-auto position-relative">
                                            <div class="avatar-edit position-absolute right-0px z-index-1 top-2px">
                                                <input type='file' name="{{ $input[$i] }}" id="{{ $input[$i] }}" accept=".png"  class="imageUpload d-none"/>
                                                <label class="d-grid w-40px h-40px mb-0 rounded-pill bg-white text-success fs-20 shadow pointer font-weight-normal align-items-center justify-content-center" for="{{ $input[$i] }}"><i class="fad fa-pencil"></i></label>
                                            </div>
                                            <div class="avatar-preview w-100px h-100px position-relative rounded-pill shadow">
                                                <?php
                                                    if( @GetImageSize( asset('public/'. $image) ) ){
                                                        $img =  asset('public/'. $image);
                                                    } else {
                                                        $img = asset('public/team_image.png');
                                                    }
                                                ?>

                                                <div class="w-100 h-100 rounded-pill" id="{{ $input[$i] }}-imagePreview" style="background-image: url({{ $img }});">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php }?>

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

<script>
    $(function(){
	var container = $('.container'), inputFile = $('#file'), img, btn, txt = 'Browse', txtAfter = 'Browse another pic';

	if(!container.find('#upload').length){
		container.find('.input').append('<input type="button" value="'+txt+'" id="upload">');
		btn = $('#upload');
		container.prepend('<img src="" class="hidden" alt="Uploaded file" id="uploadImg" width="100">');
		img = $('#uploadImg');
	}

	btn.on('click', function(){
		img.animate({opacity: 0}, 300);
		inputFile.click();
	});

	inputFile.on('change', function(e){
		container.find('label').html( inputFile.val() );

		var i = 0;
		for(i; i < e.originalEvent.srcElement.files.length; i++) {
			var file = e.originalEvent.srcElement.files[i],
				reader = new FileReader();

			reader.onloadend = function(){
				img.attr('src', reader.result).animate({opacity: 1}, 700);
			}
			reader.readAsDataURL(file);
			img.removeClass('hidden');
		}

		btn.val( txtAfter );
	});
});
</script>

<script>
$(document).on('input', 'input[name="credit"]', function () {
    if( $(this).val() > 15 ) {
        Swal.fire('Player credit must be less than or equal to 15');

        $(this).val('');
    }
});
</script>

@endsection('content')
