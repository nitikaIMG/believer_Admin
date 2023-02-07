@extends('main')

@section('heading')
    Player Manager
@endsection('heading')

@section('sub-heading')
    Edit Player
@endsection('sub-heading')

@section('content')
<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          <a  href="<?php echo action('PlayerController@view_player') ?>" class="btn btn-sm btn-info float-right"><i class="fad fa-eye"></i> View All Players</a>
        </div>
      </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Edit Player</div>
      <form class="card-body" action="<?php echo action('PlayerController@edit_player',base64_encode(serialize($player->id)))?>" enctype='multipart/form-data' method="post">
    	{{csrf_field()}}

        @include('alert_msg')

        <div class="sbp-preview">
            <div class="sbp-preview-content">
                <div class="row mx-0">
                    <div class="col-6">
                        <div class="form-group my-3">
                            {{ Form::label('Player key*','Player key*',array('class'=>'control-label text-bold','for'=>'disabledTextInput'))}}<br>
                            {{ Form::text('players_key',$player->players_key, array('value'=>'$player->players_key','class'=>'form-control form-control-solid', 'id'=>'disabledTextInput','disabled'=>'disabled'))}}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            {{ Form::label('Player Name*','Player Name*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('player_name',$player->player_name, array('value'=>'','required'=>'','placeholder'=>'Enter player name here','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            {{ Form::label('Batting Style*','Batting Style*',array('class'=>'control-label form-label'))}}
                            {{ Form::text('battingstyle',$player->battingstyle, array('value'=>'','placeholder'=>'Enter player batting style here','class'=>'form-control form-control-solid form-input1'))}}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            {{ Form::label('Bowling style*','Bowling style*',array('class'=>'control-label form-label'))}}
                            {{ Form::text('bowlingstyle',$player->bowlingstyle, array('value'=>'','placeholder'=>'Enter player bowling style here','class'=>'form-control form-control-solid form-input1'))}}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            {{ Form::label('Date of Birth*','Date of Birth*',array('class'=>'control-label text-bold'))}}
                            <?php if($player->dob!="0000-00-00"){
                                ?>
                            {{ Form::text('dob',date('M/d/Y',strtotime($player->dob)),array('value'=>'','required'=>'','placeholder'=>'Enter date of birth','class'=>'form-control form-control-solid datepicker'))}}
                            <?php
                            } else{
                            ?>
                            {{ Form::date('dob',null,array('value'=>'','required'=>'','placeholder'=>'Enter date of birth','class'=>'form-control form-control-solid form-control form-control-solid-rounded'))}}
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            {{ Form::label('Country*','Country*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('country',$player->country,array('value'=>'','required'=>'','placeholder'=>'Enter country','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            {{ Form::label('Credit*','Credit*',array('class'=>'control-label text-bold'))}}
                            {{ Form::text('credit',$player->credit,array('value'=>'','required'=>'','placeholder'=>'enter player credit here','onkeypress'=>'return isNumberKey(event)','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>

                    <div class="col-6">
                        <?php $f_type = 'Cricket';
                        if($f_type=='Cricket'){?>
                            <div class="form-group">
                                {{ Form::label('Select Role*','Select Role*',array('class'=>'control-label text-bold'))}}
                                <select class="p-1 form-control form-control-solid" name="role">
                                <option disabled selected value="">Select Role</option>
                                <option value="batsman" <?php if($player->role=='batsman'){ echo 'selected'; }?>>Batsman</option>
                                <option value="bowler" <?php if($player->role=='bowler'){ echo 'selected'; }?>>Bowler</option>
                                <option value="allrounder" <?php if($player->role=='allrounder'){ echo 'selected'; }?>>All rounder</option>
                                <option value="keeper" <?php if($player->role=='keeper'){ echo 'selected'; }?>>Wicket Keeper</option>
                                    </select>
                            </div>
                            <?php }else{ ?>
                            <div class="form-group">
                                {{ Form::label('Select Role*','Select Role*',array('class'=>'control-label text-bold'))}}
                                <select class="p-1 form-control form-control-solid" name="role">
                                <option disabled selected value="">Select Role</option>
                                <option value="goalkeeper" <?php if($player->role=='goalkeeper'){ echo 'selected'; }?>>Goal Keeper</option>
                                <option value="defender" <?php if($player->role=='defender'){ echo 'selected'; }?>>Defender</option>
                                <option value="midfielder" <?php if($player->role=='midfielder'){ echo 'selected'; }?>>Mid Fielder</option>
                                <option value="striker" <?php if($player->role=='striker'){ echo 'selected'; }?>>Striker</option>
                                    </select>
                            </div>
                            <?php } ?>
                    </div>
                    <div class="col-6">
                        {{ Form::label('Image*','Image*',array('class'=>'control-label text-bold'))}}
                        {{ Form::file('image',null,array('value'=>'','required'=>'','placeholder'=>'enter your image','onkeypress'=>'return isNumberKey(event)'))}}
                        <?php if($player->image==''){
                        ?>
                        <img src="<?php echo asset('public/'.auth()->user()->settings()->logo ?? '');?>" style='width:100px;height:100px;'>
                        <?php }else{
                        ?>
                        <img src="<?php echo asset('public/'.$player->image);?>" style='width:100px;height:100px;' onerror="this.src='{{ asset('public/'.auth()->user()->settings()->logo ?? '')}}'">
                        <?php } ?>
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
