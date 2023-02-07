@extends('main')

@section('heading')
    Admin Profile
@endsection('heading')

@section('sub-heading')
    Update Admin Profile
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('HomeController@change_masterpassword') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary text-uppercase" ><i class="fad fa-key"></i>&nbsp; Change Master Password</a>
@endsection('card-heading-btn')

@section('content')

<div class="card">
    <div class="card-header">Update Admin Profile</div>
      {!! Form::open(array('method' =>'post', 'action' => 'HomeController@update_profile','files' => true,'id' => 'j-forms', 'class' => 'card-body','encrypt'=>'multipart/form-data')) !!}

          {{csrf_field()}}

        @include('alert_msg')
        
        <div class="sbp-preview">
            <div class="sbp-preview-content p-2">
                <div class="row mx-0">
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            <label for="name">Admin Name*</label>
                            <input class="form-control form-control-solid" id="name" type="text" placeholder="Enter Admin Name" name="name" required value="{{ucwords($profile->name)}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            {{ Form::label('Mobile Number*','Mobile Number*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('mobile',ucwords($profile->mobile),array('value'=>'','required'=>'','placeholder'=>'Enter Mobile Number','class'=>'form-control form-control-solid','autocomplete'=>'off','onkeypress'=>'return isNumberKey(event)','pattern'=>'[789][0-9]{9}','minlength'=>'10','maxlength'=>'10'))}}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            {{ Form::label('Email Address*','Email Address*',array('class'=>'control-label text-bold'))}}
                            {{ Form::email('email',ucwords($profile->email),array('value'=>'','required'=>'','placeholder'=>'Enter Email Address','class'=>'form-control form-control-solid ','autocomplete'=>'off'))}}
                        </div>
                    </div>
                     <div class="col-md-6">
                        <div class="form-group my-3">
                           <label for="input-9">Masterpassword</label>
                            <input type="password" placeholder = "Enter Master Password"  name="masterpassword" class="form-control form-control-solid" id="input-9" value="" required="">
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <?php

                        $input = ['image'];
                        $defaultLogo = ['logo.png'];
                        $labels = ['Image'];

                        for($i=0; $i<count($input);$i++){?>

                        <?php $setting2= $input[$i];?>
                        <?php $defaultImages= $defaultLogo[$i];?>

                        <?php
                            if( !empty($profile->image) ) {
                                $image = $profile->image;
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
                                        <?php
                                            if( @GetImageSize( asset('public/'. $profile->image) ) ){
                                                $img =  asset('public/'. $profile->image);
                                            } else {
                                                $img = asset('public/logo.png');
                                            }
                                        ?>
                                        <div class="avatar-preview w-100px h-100px position-relative rounded-pill shadow">
                                            <div class="w-100 h-100 rounded-pill" id="{{ $input[$i] }}-imagePreview" style="background-image: url({{ asset($img) }});">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php }?>
                    </div>
                    <div class="col-12 text-right mt-4 mb-2">
                      <button type="submit" class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                      <a href="{{ asset('my-admin/admin_profie')}}" class="btn btn-sm btn-warning text-uppercase"><i class="far fa-undo"></i>&nbsp;Reset</a>
                  </div>
                </div>
            </div>
        </div>
     {!! Form::close() !!}
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
