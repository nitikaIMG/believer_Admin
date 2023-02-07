@extends('main')

@section('heading')
    User Manager
@endsection('heading')

@section('sub-heading')
    Edit User Details
@endsection('sub-heading')


@section('card-heading-btn')
<a  href="<?php echo action('RegisteruserController@index') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary text-uppercase"><i class="fad fa-eye"></i>&nbsp; View All Users</a>
@endsection('card-heading-btn')

@section('content')

@include('alert_msg')

<div class="card">
    <div class="card-header">Edit User Details</div>
      <form class="card-body" action="<?php echo action('RegisteruserController@edituserdetails',base64_encode(serialize($user->id)))?>" enctype='multipart/form-data' method="post">
        {{csrf_field()}}
        {{ Form::hidden('id',$user->id, array('value'=>''))}}


        <div class="sbp-preview">
            <div class="sbp-preview-content">
                <div class="row mx-0">
                    <div class="col-6">
                        <div class="form-group my-3">
                            {{ Form::label('User Name*','User Name*',array('class'=>'control-label text-bold'))}}</br>
                            {{ Form::text('username',$user->username,array('value'=>'','required'=>'','placeholder'=>'Enter name','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">

                            {{ Form::label('Gender*','Gender*',array('class'=>'control-label text-bold'))}}
                            <br/>
                            <div class="custom-control custom-radio custom-control-inline">
                              <input type="radio" id="customRadio1" name="gender" value="male" <?php if($user->gender=='male'){ echo 'checked';}?> required class="custom-control-input">
                              <label class="custom-control-label" for="customRadio1">Male</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                              <input type="radio" id="customRadio2" name="gender" class="custom-control-input" value="female" <?php if($user->gender=='female'){ echo 'checked';}?>>
                              <label class="custom-control-label" for="customRadio2">Female</label>
                            </div>

                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            {{ Form::label('Email*','Email*',array('class'=>'control-label text-bold'))}}
                            {{ Form::email('email',$user->email, array('value'=>'','required'=>'','placeholder'=>'Enter Email','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            {{ Form::label('Mobile*','Mobile*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('mobile',$user->mobile, array('value'=>'','required'=>'','placeholder'=>'Enter Mobile','class'=>'form-control form-control-solid','onkeypress'=>'return isNumberKey(event)','pattern'=>'[789][0-9]{9}','minlength'=>'10','maxlength'=>'10'))}}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            {{ Form::label('Address*','Address*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('address',$user->address, array('value'=>'','placeholder'=>'Enter Address','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group my-3">
                            {{ Form::label('D.O.B.*','D.O.B.*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('dob',$user->dob, array('value'=>'','required'=>'','placeholder'=>'Enter D.O.B.','class'=>'form-control form-control-solid datetimepickerget', "data-date-format" => "yyyy-mm-dd"))}}
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group my-3">
                            {{ Form::label('City*','City*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('city',$user->city, array('value'=>'','placeholder'=>'Enter City','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            {{ Form::label('State*','State*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('state',$user->state, array('value'=>'','required'=>'','placeholder'=>'Enter State','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group my-3">
                            {{ Form::label('Pincode*','Pincode*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('pincode',$user->pincode, array('value'=>'','required'=>'','placeholder'=>'Enter Pincode','class'=>'form-control form-control-solid','onkeypress'=>'return isNumberKey(event)','pattern'=>'[0-9]{6}'))}}
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group my-3">
                           {{ Form::label('Team*','Team*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('team',$user->team, array('value'=>'','required'=>'','placeholder'=>'Enter Team','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                           {{ Form::label('Refercode*','Refercode*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('refer_code',$user->refer_code, array('value'=>'','required'=>'','placeholder'=>'Refercode','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>

                    <div class="row mx-0 justify-content-center ">


                    <?php

                        $input = ['image'];
                        $labels = ['Image'];

                        for($i=0; $i<count($input);$i++){?>
                        <?php $contests= $input[$i];?>


                        <div class="col-md col-sm-4 col-6">
                            <div class="form-group">
                                <div class="row justify-content-center py-0">
                                    <h1 class="fs-14 font-weight-bold text-center mt-5 col-12">{{ $labels[$i] }}</h1>
                                    <div class="avatar-upload col-auto position-relative">
                                        <div class="avatar-edit position-absolute right-0px z-index-1 top-2px">
                                            <input type='file' name="image" id="{{ $input[$i] }}" accept=".png"  class="imageUpload d-none"/>
                                            <label class="d-grid w-40px h-40px mb-0 rounded-pill bg-white text-success fs-20 shadow pointer font-weight-normal align-items-center justify-content-center" for="{{ $input[$i] }}"><i class="fad fa-pencil"></i></label>
                                        </div>
                                        <div class="avatar-preview w-100px h-100px position-relative rounded-pill shadow">

                                            <?php

                                                if( @GetImageSize( asset($user->image) ) ){
                                                    $img =  asset($user->image);
                                                } else {
                                                    $img = asset('public/user_image.png');
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


                    <div class="col-12 text-right mt-4 mb-2">
	                    <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
	                </div>
                </div>
            </div>
        </div>
     </form>
</div>
@endsection('content')
