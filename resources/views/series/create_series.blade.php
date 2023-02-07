@extends('main')

@section('heading')
    Series Manager
@endsection('heading')

@section('sub-heading')
    Create New Series
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('SeriesController@index') ?>" class="btn btn-sm  btn-sm rounded-pill btn-light font-weight-bold text-primary float-right"><i class="fad fa-eye"></i>&nbsp; View All Series</a>
@endsection('card-heading-btn')

@section('content')


<div class="card">
    <div class="card-header">Create Series</div>
    	{{ Form::open(array('url' => 'my-admin/create_series', 'method' => 'post','id' => 'j-forms','class'=>'card-body' ))}}
    	{{csrf_field()}}

        
        @include('alert_msg')

        <div class="sbp-preview">
            <div class="sbp-preview-content p-2">
                <div class="row mx-0">
                    <div class="col-12">
                        <div class="form-group my-3">
                            <label for="job-title">Series Name*</label>
                            <input class="form-control form-control-solid" id="job-title" type="text" placeholder="Enter Series Title" name="seriesname" required>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="form-group my-3">
                            <label for="example-datetime-local-input">Start Date*</label>
                            <input class="form-control form-control-solid" type="text" placeholder="Enter Start Date"  id="example-datetime-local-input" autocomplete="off" value="" name="startdate" required >
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="form-group my-3">
                            <label for="example-datetime-local-input2">End Date*</label>
                            <input class="form-control form-control-solid" type="text" placeholder="Enter End Date" value="" name="enddate" autocomplete="off" id="example-datetime-local-input2" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">Has Leaderboard</label>
                            <select name="has_leaderboard" id="has_leaderboard" required class="form-control selectpicker show-tick" data-width="full" data-container="body" data-live-search="true">
                                <!-- <option value="">Select Has Leaderboard</option> -->
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 text-right mt-4 mb-2">
	                    <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
	                </div>
                </div>
            </div>
        </div>
    {{ Form::open() }}
</div>
@endsection('content')
