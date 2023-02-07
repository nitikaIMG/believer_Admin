@extends('main')

@section('heading')
    News Manager
@endsection('heading')

@section('sub-heading')
    Edit News
@endsection('sub-heading')

@section('card-heading-btn')
    <a href="<?php echo action('NewsController@view_news'); ?>"
        class="btn btn-sm btn-light font-weight-bold text-uppercase mr-2 text-primary float-right" data-toggle="tooltip"
        title="View All News"><i class="fas fa-eye"></i>&nbsp; View</a>
    <a href="<?php echo action('NewsController@news'); ?>"
        class="btn btn-sm btn-light font-weight-bold text-uppercase mr-2 text-primary float-right" data-toggle="tooltip"
        title="Add New News"><i class="fas fa-plus"></i>&nbsp; Add</a>
@endsection('card-heading-btn')

@section('content')

    <div class="card">
        <div class="card-header">Edit News Slider</div>
        <?php $sidebanner_id = $sidebanner->id; ?>
        <form class="card-body"
            action="<?php echo action('NewsController@update_news', base64_encode(serialize($sidebanner_id))); ?>"
            method="post" enctype="multipart/form-data">
            {{ csrf_field() }}

            @include('alert_msg')

            <div class="sbp-preview">
                <div class="sbp-preview-content">
                    <div class="row mx-0">
                        <div class="col-12">
                            <div class="form-group my-3">
                                <label for="job-title">Type*</label>
                                <select name="type" class="form-control form-control-solid selectpicker show-tick"
                                    data-container="body" data-live-search="true" title="Select Theme"
                                    data-hide-disabled="true"  onchange="show_hide_link(this)" id="select_type">
                                    <option value="">Select Type</option>
                                    <option value="Tranding" <?php if ($sidebanner->type == 'Tranding') {
                                        echo 'selected';
                                        } ?>>Tranding</option>
                                    <?php
                                    /*<option <?php if($sidebanner->type == 'web'){ echo 'selected'; }?> value="web">Web</option>*/
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group my-3">
                                <label for="type">Title*</label>
                                <input name="title" class="form-control form-control-solid " value="{{$sidebanner->title}}" type="text" placeholder="Enter News Title">
                            
                            </div>
                        </div>
                        <div class="col-12">
                            <?php

                                $input = ['image'];
                                $defaultLogo = ['logo.png'];
                                $labels = ['Image'];

                                for($i=0; $i<count($input);$i++){?>

                                <?php $setting2= $input[$i];?>
                                <?php $defaultImages= $defaultLogo[$i];?>

                                <?php
                                    if( !empty($sidebanner->$setting2) ) {
                                        $image = $sidebanner->$setting2;
                                    } else {
                                        $image = $defaultLogo[$i];
                                    }

                                ?>


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
                                                        $img = asset('public/logo.png');
                                                    }
                                                ?>

                                                <div class="w-100 h-100 rounded-pill" id="{{ $input[$i] }}-imagePreview" style="background-image: url({{ $img }});">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php }?>

                        </div>
                        <div class="col-12">
                            <div class="form-group my-3">
                                <label for="firt-name">Description*</label>
                                <textarea required="required" name="description" class="form-control form-control-solid ckeditor" type="text" placeholder="Enter points" onkeypress='return isNumberKey(event)'>{{$sidebanner->description}}</textarea>
                            </div>
                        </div>
                <div class="col-12 text-right mt-4 mb-2">
                    <button class="btn btn-sm btn-success text-uppercase"><i
                            class="far fa-check-circle"></i>&nbsp;Submit</button>
                </div>
            </div>
    </form>
    </div>

<script>
    function show_hide_link(element) {
        if(element.value == 'others') {
            $('#link').show();
            $('#matchdata').hide();
        } else {
            $('#link').hide();
            if(element.value=="match"){
                $('#matchdata').show();
            }else{
                $('#matchdata').hide();
            }
        }        
    }

    if($("#select_type").val()=='match'){
        $('#matchdata').show();
    }
    
</script>
@endsection('content')
