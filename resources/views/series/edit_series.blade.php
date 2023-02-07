@extends('main')

@section('heading')
    Series Manager
@endsection('heading')

@section('sub-heading')
    Edit Series
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('SeriesController@index') ?>" class="btn btn-sm btn-sm rounded-pill btn-light font-weight-bold text-primary float-right"><i class="fad fa-eye"></i>&nbsp; View All Series</a>
@endsection('card-heading-btn')

@section('content')

@include('alert_msg')

<div class="card">
    <div class="card-header">Edit Series</div>
    <?php $series_id = $data->id; ?>
      <form class="card-body" action="<?php echo action('SeriesController@edit',base64_encode(serialize($series_id)))?>" method="post">
    	{{csrf_field()}}

        @if ($errors->any())
           <div class="alert alert-danger">
               <ul>
                   @foreach ($errors->all() as $error)
                       <li>{{ $error }}</li>
                   @endforeach
               </ul>
           </div>
        @endif
        <div class="sbp-preview">
            <div class="sbp-preview-content">
                <div class="row mx-0">
                    <div class="col-12">
                        <div class="form-group my-3">
                            <label for="job-title">Series Name*</label>
                            <input class="form-control form-control-solid" id="job-title" type="text" placeholder="Enter Job Title" name="seriesname" required value="{{$data->name}}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            <label for="example-text-input">Start Date*</label>
                            <input class="form-control form-control-solid datetimepickerget" id="example-text-input" type="text" placeholder="Enter Start Date*" name="startdate" required datetimepickerget value="{{$data->start_date}}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            <label for="example-text-input">End Date*</label>
                            <input class="form-control form-control-solid datetimepickerget" id="example-text-input" type="text" placeholder="Enter End Date" name="enddate" required datetimepickerget value="{{$data->end_date}}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">Has Leaderboard</label>
                            <select name="has_leaderboard" id="has_leaderboard" required class="form-control selectpicker show-tick" data-width="full" data-container="body" data-live-search="true">
                                <!-- <option value="">Select Has Leaderboard</option> -->
                                <option value="yes"
                                    @if($data->has_leaderboard == 'yes')
                                        selected
                                    @endif
                                >Yes</option>
                                <option value="no"
                                    @if($data->has_leaderboard == 'no')
                                        selected
                                    @endif
                                >No</option>
                            </select>
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
@endsection('content')
