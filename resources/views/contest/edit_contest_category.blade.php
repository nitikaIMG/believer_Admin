@extends('main')

@section('heading')
    Contest Manager
@endsection('heading')

@section('sub-heading')
   Edit Contest Category
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('ContestController@view_contest_category') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary text-uppercase"><i class="fad fa-eye"></i>&nbsp;View All Contest Categories</a>
@endsection('card-heading-btn')

@section('content')

<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">Edit Contest Category</div>
            <div class="card-body">
            <form action="<?php echo action('ContestController@edit_contest_category',base64_encode(serialize($contest->id)))?>" enctype='multipart/form-data' method="post">
                {{csrf_field()}}

            @include('alert_msg')
        
                <div class="sbp-preview">
                    <div class="sbp-preview-content">
                        <div class="row mx-0">
                            <div class="col-md-6">
                                <div class="form-group my-3">
                                    {{ Form::label('Contest Category Name*','Contest Category Name*',array('class'=>'control-label text-bold'))}}
                                    {{ Form::text('name',$contest->name,array('value'=>'','required'=>'','placeholder'=>'Enter Category Name','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group my-3">
                                    {{ Form::label('Sub Title*','Sub Title*',array('class'=>'control-label text-bold'))}}
                                {{ Form::text('sub_title',$contest->sub_title,array('value'=>'','required'=>'','placeholder'=>'Enter Sub Title','class' => 'form-control form-control-solid'))}}
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

                                                if( @GetImageSize( asset('public/'. $contest->image) ) ){
                                                    $img =  asset('public/'. $contest->image);
                                                } else {
                                                    $img = asset('public/logo.png');
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
                                <button type="reset" class="btn btn-sm btn-warning" onclick="window.location.href=window.location.href"><i class="fa fa-undo"></i>&nbsp;Reset</a>
                                <button type="submit" class=" btn btn-sm btn-success float-right ml-1"><i class="far fa-check-circle"></i>&nbsp;   Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
@endsection('content')
