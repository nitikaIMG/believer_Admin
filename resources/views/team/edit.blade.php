@extends('main')

@section('heading')
    Team Manager
@endsection('heading')

@section('sub-heading')
    Edit Team
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('TeamController@view_team') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary float-right"><i class="fad fa-eye"></i>&nbsp; View All Teams</a>
@endsection('card-heading-btn')

@section('content')
<div class="card">
    <div class="card-header">Edit Team</div>
      <form class="card-body" action="<?php echo action('TeamController@edit_team',base64_encode(serialize($data->id)))?>" enctype='multipart/form-data' method="post">
    	{{csrf_field()}}
        @include('alert_msg')

        {{ Form::hidden('id',$data->id, array('value'=>''))}}

        <div class="sbp-preview">
            <div class="sbp-preview-content">
                <div class="row mx-0">
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            {{ Form::label('Team Name*','Team Name*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('team',$data->team, array('value'=>'','required'=>'','placeholder'=>'Enter Team Name','class'=>'form-control form-control-solid')) }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            {{ Form::label('Team Short Name*','Team Short Name*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('short_name',$data->short_name, array('value'=>'','required'=>'','placeholder'=>'Enter Team Short Name','class'=>'form-control form-control-solid')) }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            {{ Form::label('Team Key*','Team Key*',array('class'=>'control-label text-bold')) }}<br>
                            <input type="text" name="" id="" value="{{ $data->team_key }}" class="form-control form-control-solid" readonly disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            {{ Form::label('Team Color*','Team Color*',array('class'=>'control-label text-bold')) }} {{$data->color}}<br>
                            <input type="color" class="form-control form-control-solid" placeholder="Team Color" name="color" value="{{$data->color}}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row mx-0 justify-content-center ">


                        <?php

                            $input = ['logo'];
                            $defaultLogo = ['team_image.png'];
                            $labels = ['Team Logo'];

                            for($i=0; $i<count($input);$i++){?>

                            <?php $setting2= $input[$i];?>
                            <?php $defaultImages= $defaultLogo[$i];?>

                            <?php
                                if( !empty($data->$setting2) ) {
                                    $image = $data->$setting2;
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


@endsection('content')
