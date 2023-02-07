@extends('main')
<?php
use App\Helpers\Helpers;
?>
@section('heading')
    Match Manager
@endsection('heading')

@section('sub-heading')
    Edit Match
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('MatchController@upcoming_matches') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary float-right" data-toggle="tooltip" title="View All Upcoming Matches"><i class="fas fa-eye"></i>&nbsp; View</a>
@endsection('card-heading-btn')

@section('content')

<div class="card">
    <div class="card-header">Edit Existing Match</div>
      <form class="card-body" action="<?php echo action('MatchController@editmatch',$findmatchdetails->matchkey)?>" enctype='multipart/form-data' method="post">
    	{{csrf_field()}}

        @include('alert_msg')

        <div class="sbp-preview"> 
            <div class="sbp-preview-content py-2">
                <div class="row mx-0">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="hidden" name="fantasy_type" value="{{$f_type}}">
                            {{ Form::label('Match Name*','Match Name*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('name', ucwords($findmatchdetails->name), array('value'=>'','required'=>'','placeholder'=>'Match Name','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('Match Start Date*','Match Start Date*',array('class'=>'control-label text-bold'))}}
                            <input class="form-control form-control-solid datetimepickerget" name='start_date' type="text" value="{{$findmatchdetails->start_date}}" id="start_date" required placeholder="Enter Match Start Date">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('Team1*','Team1*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('team1',ucwords($findmatchdetails->team1name),array('value'=>'','required'=>'','placeholder'=>'Team1','disabled'=>'','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('Team2*','Team2*',array('class'=>'control-label text-bold'))}}
                             {{ Form::text('team2',ucwords($findmatchdetails->team2name),array('value'=>'','required'=>'','placeholder'=>'Team2','disabled'=>'','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <?php $f_type = 'Cricket';
                    if($f_type=='Cricket'){?>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('Match Format*','Match Format*',array('class'=>'control-label text-bold'))}}
                            <select class="form-control form-control-solid p-1 selectpicker show-tick" data-container="body" data-live-search="true" title="Select Match Format" data-hide-disabled="true" name="format"  required="">
                            <option value="" > Select match format </option>
                            <?php
                                $matchformat = Helpers::allmatchformats();
                                if(!empty($matchformat)){
                                    foreach($matchformat as $matchf=>$matchvalue){
                                        ?>
                                        <option value="<?php echo $matchf?>" <?php if($findmatchdetails->format==$matchf){ echo 'selected'; }?>><?php echo ucwords($matchvalue);?> </option>
                                        <?php
                                    }
                                }
                            ?>
                         </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('Match Order*','Match Order*',array('class'=>'control-label text-bold'))}}
                             {{ Form::text('tbl_order',ucwords($findmatchdetails->tbl_order),array('value'=>'','placeholder'=>'Match Order','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <?php }?>
                    <div class="col-sm-12">
                    <div class="form-group">
                        {{ Form::label('Series*','Series*',array('class'=>'control-label text-bold'))}}
                    <select class="form-control form-control-solid p-1 selectpicker show-tick" data-container="body" data-live-search="true" title="Select Series" data-hide-disabled="true"  name="series" required="">
                    <option value="" disabled>Select Series</option>
                    <?php
                         if(!empty($findmatchseries)){
                         foreach($findmatchseries as $matcseries){
                      ?>
                       <option value="<?php echo $matcseries->id?>"  <?php if($matcseries->id==$findmatchdetails->series){ echo 'selected'; }?>><?php echo ucwords($matcseries->name);?></option>
                      <?php
                        }
                      }
                    ?>
                  </select>
                </div>
              </div>

                    <div class="col-md-12 text-right mt-md-4 mb-md-2">
                      <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                      <a onclick="window.location.href=window.location.href" class="btn btn-sm btn-warning text-uppercase"><i class="fa fa-undo" ></i>&nbsp; Reset</a>
	                </div>
                </div>
            </div>
        </div>
     </form>
</div>
@endsection('content')
