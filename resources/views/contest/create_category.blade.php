@extends('main')

@section('heading')
    Contest Manager
@endsection('heading')

@section('sub-heading')
    Create new contest category
@endsection('sub-heading')


@section('card-heading-btn')
<a  href="<?php echo action('ContestController@view_contest_category') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary text-uppercase"><i class="fad fa-eye"></i>&nbsp; View All Contest Categories</a>
@endsection('card-heading-btn')

@section('content')

@include('alert_msg')

<div class="card">
    <div class="card-header">Add New Contest Category</div>
    	{{ Form::open(array('action' => 'ContestController@create_category', 'method' => 'post','id' => 'j-forms','class'=>'card-body', 'enctype'=>'multipart/form-data' ))}}
    	{{csrf_field()}}

        <div class="sbp-preview">
            <div class="sbp-preview-content">
                <div class="row mx-0">

                    <div class="col-6">
                        <div class="form-group my-3">
                           {{ Form::label('Contest Category Name*','Contest Category Name*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('name',null,array('value'=>'','required'=>'','placeholder'=>'Enter Category Name','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            {{ Form::label('Sub Title*','Sub Title*',array('class'=>'control-label text-bold'))}}
                           {{ Form::text('sub_title',null,array('value'=>'','required'=>'','placeholder'=>'Enter Sub Title','class' => 'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <div class="col-6">
            
                        <div class="avatar-upload col-3 position-relative">
                        <div class="avatar-edit position-absolute right-0px z-index-1 top-2px">
                            <input type='file' name="image" id="img" accept=".png"  class="imageUpload d-none"/>
                            <label class="d-grid w-40px h-40px mb-0 rounded-pill bg-white text-success fs-20 shadow pointer font-weight-normal align-items-center justify-content-center" for="img"><i class="fad fa-pencil"></i></label>
                        </div>
                        <div class="avatar-preview w-100px h-100px position-relative rounded-pill shadow">
                            <div class="w-100 h-100 rounded-pill" id="img-imagePreview" style="background-image: url({{ asset('public/logo.png') }})">
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="col-12 text-right mt-4 mb-2">
	                    <button type="reset" class="btn btn-sm btn-warning" onclick="window.location.href=window.location.href"><i class="fa fa-undo"></i>&nbsp;Reset</a>
                        <button type="submit" class=" btn btn-sm btn-success float-right ml-1"><i class="far fa-check-circle"></i>&nbsp;   Submit</button>
	                </div>
                </div>
            </div>
        </div>
    {{ Form::open() }}
</div>
@endsection('content')
