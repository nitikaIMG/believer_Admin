@extends('main')

@section('heading')
    Match Manager
@endsection('heading')

@section('sub-heading')
    Launch match
@endsection('sub-heading')

<style>
    .text-13px {
        font-size: 13px !important;
    }
</style>

@section('card-heading-btn')
<?php $mkey = base64_encode(serialize($findmatchdetails->matchkey));
$team1 =  base64_encode(serialize($findmatchdetails->team1id));
$team2 =  base64_encode(serialize($findmatchdetails->team2id)); ?>
<?php if(($findmatchdetails->name == 'TBC vs TBC') || ($findmatchdetails->name == 'TBC Vs TBC') || ($findmatchdetails->name == 'tbc vs tbc') || ($findmatchdetails->name == 'Tbc vs Tbc'|| ($findmatchdetails->name == 'TBC A Vs TBC B'))){ ?>
    <a href="<?php echo action('MatchController@importteam',$findmatchdetails->matchkey)?>" class="btn text-uppercase font-weight-bold btn-sm btn-primary"><i class="fad fa-users"></i>&nbsp; Import Team</a>
<?php } ?>
<?php if($findmatchdetails->launch_status=='launched' && $findmatchdetails->second_inning_status==0){ ?>
<a  href="<?php echo action('MatchController@secondinninglaunch',[$findmatchdetails->matchkey])?>" class="btn text-uppercase font-weight-bold btn-sm btn-success "><i class="fad fa-rocket"></i>&nbsp; Second Inning Launch</a>
<?php }?>


<?php $f_type = $fantasy_type;
if($f_type=='Cricket'){?>
<?php if($findmatchdetails->launch_status!='launched'){ ?>

<div class="text-right">
<a  href="<?php echo action('MatchController@viewmatchdetails',$findmatchdetails->matchkey)?>" class="btn-sm ml-auto btn text-uppercase font-weight-bold btn-info"><i class="fad fa-download"></i>&nbsp; Import Player</a>
<a  href="<?php echo action('MatchController@launch',[$findmatchdetails->matchkey, 'fantasy_type' => $f_type])?>" class="btn text-uppercase font-weight-bold btn-sm btn-danger "><i class="fad fa-rocket"></i>&nbsp; Launch Match</a>

</div>
<?php } else{?>
    <div class="float-right">
        <a  href="<?php echo action('MatchController@unlaunch',$findmatchdetails->matchkey)?>" class="btn text-uppercase font-weight-bold btn-danger btn-sm"><i class="fad fa-times-circle"></i>&nbsp; Unlaunch Match</a>
    </div>

<?php  }?>
<?php  }else{?>
<?php if($findmatchdetails->launch_status!='launched'){ ?>

    <div>
        <p> To launch this match click here </p>
        <a href="<?php echo action('FootballMatchController@GetMatchPlayers',$findmatchdetails->matchkey)?>" class="btn text-uppercase font-weight-bold btn-sm btn-info"><i class="fad fa-download"></i>&nbsp; Import Player</a>
        <a href="<?php echo action('FootballMatchController@launch',[$findmatchdetails->matchkey, 'fantasy_type' => $f_type])?>" class="btn text-uppercase font-weight-bold btn-sm btn-danger"><i class="fad fa-rocket"></i>&nbsp; Launch Match</a>
    </div>
<?php } else{?>
<div class="float-right">
<a  href="<?php echo action('MatchController@unlaunch',$findmatchdetails->matchkey)?>" class="btn text-uppercase font-weight-bold btn-sm btn-danger"><i class="far fa-check-circle"></i>&nbsp; Unlaunch Match</a>
</div>

<?php  }?>
<?php  }?>
@endsection('card-heading-btn')

@section('content')

@include('alert_msg')

<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3 font-weight-700 text-secondary fs-18">Match info</div>
        <div class="card-body p-3">
            <div class="row">
                <div class="col-6">

            <div class="sbp-preview position-relative h-100">

                <div class="row p-3">
                    <!---------- Match Details ------------------->
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12 border-bottom">
                                        <div class="row my-2">
                                            <div class="col"><strong>Match Name :- </strong></div>
                                            <div class="col-auto text-black font-weight-bold">{{ ucwords($findmatchdetails->name) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 border-bottom">
                                        <div class="row my-2">
                                            <div class="col"><strong>Team 1 Display Name :- </strong></div>
                                            <div class="col-auto text-warning font-weight-bold">{{ ucwords($findmatchdetails->team1team) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 border-bottom">
                                        <div class="row my-2">
                                            <div class="col"><strong>Team 2 Display Name :- </strong></div>
                                            <div class="col-auto text-warning font-weight-bold">{{ ucwords($findmatchdetails->team2team) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 border-bottom">
                                        <div class="row my-2">
                                            <div class="col"><strong>Date and time :- </strong></div>
                                            <div class="col-auto text-black font-weight-bold">
                                                <span class="text-success">{{ date('d F,',strtotime($findmatchdetails->start_date)) }}</span>
                                                <span class="text-info">{{ date(' Y ',strtotime($findmatchdetails->start_date)) }}</span>
                                                <span class="text-primary">{{ date(' h:i a',strtotime($findmatchdetails->start_date)) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row my-2">
                                            <div class="col"><strong>Format :- </strong></div>
                                            <div class="col-auto text-black font-weight-bold">{{ strtoupper($findmatchdetails->format) }} Match</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
                </div>
                <div class="col-6">

            <div class="sbp-preview position-relative h-100">

                <div class="row p-3 h-100 align-items-center">

                    <!----------- Team1 section ---------------------->
                    <div class="col-md-6 text-center border-right border-warning">
                        <div class="row h-100">
                            <div class="col-md-12 col-sm-12">
                                <div class="row justify-content-center">
                                    <div class="col-auto">
                                        @if($findmatchdetails->team1logo!="")
                                            <img src="{{ URL::asset($findmatchdetails->team1logo) }}" class="w-100px h-100px rounded-pill shadow" onerror="this.src='{{ URL::asset('public/team_image.png') }}'">
                                            <a class="btn-sm btn text-uppercase font-weight-bold btn-primary position-absolute bottom-5px right-5px w-35px h-35px rounded-pill shadow p-0 d-grid align-items-center justify-content-center fs-19" data-toggle="modal" data-target="#team1modallogo"><i class="fas fa-redo-alt position-relative top-1px"></i></a>
                                        @else
                                            <img src="{{ URL::asset('public/team_image.png') }}" class="w-100px h-100px rounded-pill shadow">
                                            <a class="btn-sm btn text-uppercase font-weight-bold btn-success position-absolute bottom-5px right-5px w-35px h-35px rounded-pill shadow p-0 d-grid align-items-center justify-content-center fs-19" data-toggle="modal" data-target="#team1modallogo"><i class="fas fa-plus-circle position-relative top-1px"></i></a>
                                        @endif
                                    </div>
                                </div>
                                <h5 class="mt-4"><strong> {{ ucwords($findmatchdetails->team1team) }} </strong></h5>




                                <!-- Modal -->
                                <div class="modal fade" id="team1modallogo" tabindex="-1" aria-labelledby="team1modallogoLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                            <h5 class="modal-title" id="team1modallogoLabel">Update logo of team {{ ucwords($findmatchdetails->team1team) }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            </div>
                                            <form action="{{ action('MatchController@updatelogo',$findmatchdetails->team1) }}" method="post" enctype="multipart/form-data">
                                            {{csrf_field()}}
                                            <div class="modal-body">
                                                
                                            <div class="col-md-12">
                                                <div class="row mx-0 justify-content-center ">


                                                <?php

                                                    $input = ['image'];
                                                    $defaultLogo = ['team_image.png'];
                                                    $labels = ['Team Logo'];

                                                    for($i=0; $i<count($input);$i++){?>

                                                    <?php $setting2= $input[$i];?>
                                                    <?php $defaultImages= $defaultLogo[$i];?>

                                                    <?php
                                                        if( !empty($findmatchdetails->team1logo) ) {
                                                            $image = $findmatchdetails->team1logo;
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

                                                                            if( @GetImageSize( asset($image) ) ){
                                                                                $img =  asset( 'public/'.$image);
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
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn text-uppercase btn-sm btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn text-uppercase btn-sm btn-primary">Update Logo</button>
                                            </div>
                                        </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 text-center">
                        <div class="row h-100">
                            <div class="col-md-12 col-sm-12">
                                <div class="row justify-content-center">
                                    <div class="col-auto">
                                        @if($findmatchdetails->team2logo!="")
                                            <img src="{{ URL::asset($findmatchdetails->team2logo) }}" class="w-100px h-100px rounded-pill shadow" onerror="this.src='{{ URL::asset('public/team_image.png') }}'">
                                            <a class="btn-sm btn text-uppercase font-weight-bold btn-primary position-absolute bottom-5px right-5px w-35px h-35px rounded-pill shadow p-0 d-grid align-items-center justify-content-center fs-19" data-toggle="modal" data-target="#team2modallogo"><i class="fas fa-redo-alt position-relative top-1px"></i></a>
                                        @else
                                            <img src="{{ URL::asset('public/team_image.png') }}" class="w-100px h-100px rounded-pill shadow">
                                            <a class="btn-sm btn text-uppercase font-weight-bold btn-success position-absolute bottom-5px right-5px w-35px h-35px rounded-pill shadow p-0 d-grid align-items-center justify-content-center fs-19" data-toggle="modal" data-target="#team2modallogo"><i class="fas fa-plus-circle position-relative top-1px"></i></a>
                                        @endif
                                    </div>
                                </div>

                                <h5 class="mt-4"><strong> {{ ucwords($findmatchdetails->team2team) }} </strong></h5>



                                <!-- Modal -->
                                <div class="modal fade" id="team2modallogo" tabindex="-1" aria-labelledby="team2modallogoLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h5 class="modal-title" id="team2modallogoLabel">Update logo of team {{ ucwords($findmatchdetails->team2team) }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>
                                        <form action="{{ action('MatchController@updatelogo',$findmatchdetails->team2) }}" method="post" enctype="multipart/form-data">
                                            {{csrf_field()}}
                                        
                                        <div class="modal-body">
                                                
                                            <div class="col-md-12">
                                                <div class="row mx-0 justify-content-center ">


                                                <?php

                                                    $input = ['image'];
                                                    $defaultLogo = ['team_image.png'];
                                                    $labels = ['Team Logo'];

                                                    for($i=0; $i<count($input);$i++){?>

                                                    <?php $setting2= $input[$i];?>
                                                    <?php $defaultImages= $defaultLogo[$i];?>

                                                    <?php
                                                        if( !empty($findmatchdetails->team2logo) ) {
                                                            $image = $findmatchdetails->team2logo;
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
                                                                        <input type='file' name="{{ $input[$i] }}" id="{{ $input[$i] }}1" accept=".png"  class="imageUpload d-none"/>
                                                                        <label class="d-grid w-40px h-40px mb-0 rounded-pill bg-white text-success fs-20 shadow pointer font-weight-normal align-items-center justify-content-center" for="{{ $input[$i] }}1"><i class="fad fa-pencil"></i></label>
                                                                    </div>
                                                                    <div class="avatar-preview w-100px h-100px position-relative rounded-pill shadow">

                                                                        <?php

                                                                            if( @GetImageSize( asset($image) ) ){
                                                                                $img =  asset($image);
                                                                            } else {
                                                                                $img = asset('public/team_image.png');
                                                                            }
                                                                        ?>

                                                                        <div class="w-100 h-100 rounded-pill" id="{{ $input[$i] }}1-imagePreview" style="background-image: url({{ $img }});">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                <?php }?>

                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn text-uppercase btn-sm btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn text-uppercase btn-sm btn-primary">Update Logo</button>
                                        </div>
                                    </form>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                </div>

            </div>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>




<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">

                    <div class="col-md-6">

                        <?php if($findmatchdetails->launch_status!='launched'){ ?>
                        <?php if($f_type=='Cricket'){?>
                            

                        <?php }else{ ?>
                            

                        <?php } }?>

                        <table class="table table-striped table-bordered dataTable no-footer text-nowrap text-13px d-table" role="grid" aria-describedby="datatable_info" id="dataTable1" >
                            <thead>
                                <tr role="row">
                                    <th data-toggle="tooltip" title="Serial Number">#</th>
                                    <th data-toggle="tooltip" title="Select Duo Player">Duo</th>
                                    <th data-toggle="tooltip" title="Player Name">Player Name</th>
                                    <th data-toggle="tooltip" title="Player Role">Role</th>
                                    <th data-toggle="tooltip" title="Credit">Cr</th>
                                    <?php if($findmatchdetails->launch_status!='launched'){ ?>
                                    <th>Action</th>
                                    <?php }?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $player1sno=0;?>
                                <?php foreach($findplayer1details as $p1){
                                $player1sno++;
                                $i = 1;
                                ?>
                                <tr role="row">
                                    <td><?php echo $player1sno; ?></td>
                                    <?php if($findmatchdetails->launch_status=='pending'){   ?>
                                    <td><input value="1" name="forduo" type="checkbox" <?php if($p1->forduo==1) {echo 'checked' ;}?> onchange="update_duo('<?php echo $p1->matchkey; ?>','<?php echo $p1->id; ?>','forduo',this.value);"></td>
                                    <?php }else{ ?>
                                    <td><input value="1" name="forduo" type="checkbox" disabled <?php if($p1->forduo==1) {echo 'checked' ;}?>></td>  
                                    <?php } ?>
                                    <td class="us_name"><?php echo ucwords($p1->name);?></td>
                                    <td><a data-toggle="modal" data-target="#player1modal<?php echo $player1sno?>" class="text-decoration-none text-primary pointer"><?php echo ucwords($p1->role);?></a></td>
                                    <td><a data-toggle="modal" data-target="#player1modal<?php echo $player1sno?>" class="text-decoration-none text-warning pointer"><?php if($p1->credit==""){ echo 0;}else{ echo $p1->credit; } ?></a></td>
                                <?php if($findmatchdetails->launch_status=='pending'){   ?>
                                <td class="text-center">
                                    <a data-toggle="modal" class="btn btn-xs btn-danger text-uppercase" onclick="delete_confirmation('<?php echo action('MatchController@deleteplayer',[base64_encode(serialize($p1->id)),base64_encode(serialize($p1->matchkey))])?>')"><i class="far fa-trash-alt"></i></a>
                                </td><?php } ?>
                                </tr>


                                <?php if($findmatchdetails->launch_status!='launched'){ ?>


                                    <!-- Modal -->
                                    <div class="modal fade" id="player1modal<?php echo $player1sno;?>" tabindex="-1" aria-labelledby="player1modalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="player1modalLabel">Change player role and credit</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="<?php echo action('MatchController@playerroles',$p1->id)?>" method="post" id="p1{{$player1sno}}" enctype="multipart/form-data">
                                                    {{csrf_field()}}
                                                    <div class="form-group">
                                                        <input type="text" class="form-control form-control-solid" name="name" value="<?php echo ucwords($p1->name); ?>" placeholder="Enter Player name here">
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control form-control-solid"  onkeyup="Swal.fire(this.value.count('.'));" onkeypress="return isNumberKey(event)" name="credit" placeholder="Enter player credits" value="<?php echo $p1->credit; ?>">
                                                    </div>
                                                    <div class="form-group">
                                                    @if($f_type=='Cricket')
                                                        <select class="form-control form-control-solid" name="role">
                                                            <option value=""></option>
                                                            <option value="batsman" <?php if($p1->role=='batsman'){ echo 'selected'; }?>>Batsman</option>
                                                            <option value="bowler" <?php if($p1->role=='bowler'){ echo 'selected'; }?>>Bowler</option>
                                                            <option value="allrounder" <?php if($p1->role=='allrounder'){ echo 'selected'; }?>>All rounder</option>
                                                            <option value="keeper" <?php if($p1->role=='keeper'){ echo 'selected'; }?>>Wicket Keeper</option>
                                                        </select>
                                                    @else
                                                    <select class="form-control form-control-solid" name="role">
                                                        <option disabled selected value="">Select Role</option>
                                                        <option value="goalkeeper" <?php if($p1->role=='goalkeeper'){ echo 'selected'; }?>>Goal Keeper</option>
                                                        <option value="defender" <?php if($p1->role=='defender'){ echo 'selected'; }?>>Defender</option>
                                                        <option value="midfielder" <?php if($p1->role=='midfielder'){ echo 'selected'; }?>>Mid Fielder</option>
                                                        <option value="striker" <?php if($p1->role=='striker'){ echo 'selected'; }?>>Striker</option>
                                                    </select>
                                                    @endif
                                                    </div>
                                                            
                                                    <div class="form-group">
                                                            
                                                        <div class="col-md-12">
                                                            <div class="row mx-0 justify-content-center ">


                                                            <?php

                                                                $input = ['image'];
                                                                $defaultLogo = ['team_image.png'];
                                                                $labels = ['Player Image'];

                                                                for($i=0; $i<count($input);$i++){?>

                                                                <?php $setting2= $input[$i];?>
                                                                <?php $defaultImages= $defaultLogo[$i];?>

                                                                <?php
                                                                    if( !empty($p1->image) ) {
                                                                        $image = $p1->image;
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
                                                                                    <input type='file' name="{{ $input[$i] }}" id="{{ $input[$i] }}<?php echo $player1sno;?>id" accept=".png"  class="imageUpload d-none"/>
                                                                                    <label class="d-grid w-40px h-40px mb-0 rounded-pill bg-white text-success fs-20 shadow pointer font-weight-normal align-items-center justify-content-center" for="{{ $input[$i] }}<?php echo $player1sno;?>id"><i class="fad fa-pencil"></i></label>
                                                                                </div>
                                                                                <div class="avatar-preview w-100px h-100px position-relative rounded-pill shadow">

                                                                                    <?php

                                                                                        if( @GetImageSize( asset($image) ) ){
                                                                                            $img =  asset($image);
                                                                                        } else {
                                                                                            $img = asset('player_image.png');
                                                                                        }
                                                                                    ?>

                                                                                    <div class="w-100 h-100 rounded-pill" id="{{ $input[$i] }}<?php echo $player1sno;?>id-imagePreview" style="background-image: url({{ $img }});">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            <?php }?>

                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="checkbox" class="text-left">
                                                        <div class="custom-control custom-checkbox my-1 mr-sm-2">
                                                          <input value="global" name="global" type="checkbox" class="custom-control-input" id="customControlInline{{$player1sno}}">
                                                          <label class="custom-control-label text-uppercase" for="customControlInline{{$player1sno}}">Set as global</label>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn text-uppercase btn-sm btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn text-uppercase btn-sm btn-primary" form="p1{{$player1sno}}">Save changes</button>
                                            </div>
                                        </div>
                                        </div>
                                    </div>



                                <?php } ?>
                                <?php } ?>
                            </tbody>
                        </table>

                        @if($f_type=='Cricket')
                            <div id="Ctrl_SlctCriteriaTeam1" class="col-md-12 col-sm-12 mt-1 p-0">
                                <table class="table table-striped table-bordered no-footer w-100 d-table">
                                    <tbody>
                                        <tr>
                                            <th colspan="5" class="text-center">Selection Criteria</th>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>WK</td>
                                            <td>Bat</td>
                                            <td>AR</td>
                                            <td>Bowl</td>
                                        </tr>
                                        <tr>
                                            <th data-toggle="tooltip" title="Minimum">Min</th>
                                            <td>1</td>
                                            <td>3</td>
                                            <td>1</td>
                                            <td>3</td>
                                        </tr>
                                        <tr>
                                            <th data-toggle="tooltip" title="Maximum">Max</th>
                                            <td>4</td>
                                            <td>6</td>
                                            <td>4</td>
                                            <td>5</td>
                                        </tr>
                                        <tr>
                                            <th>Your Selection</th>
                                                @if($wk1<1)
                                                    <td class="font-weight-bold text-white bg-danger" title="Criteria fulfill for wicket-Keeper">{{ $wk1 }}</td>
                                                @else
                                                    <td class="font-weight-bold text-white bg-success" title="Criteria fulfill for wicket-Keeper">{{ $wk1 }}</td>
                                                @endif
                                                @if($batsman1<3)
                                                    <td class="font-weight-bold text-white bg-danger" title="Please select atleast three Batsman">{{ $batsman1 }}</td>
                                                @else
                                                    <td class="font-weight-bold text-white bg-success" title="Please select atleast three Batsman">{{ $batsman1 }}</td>
                                                @endif
                                                @if($allrounder1<1)
                                                    <td class="font-weight-bold text-white bg-danger" title="Please select atleast one All-Rounders">{{ $allrounder1 }}</td>
                                                @else
                                                    <td class="font-weight-bold text-white bg-success" title="Please select atleast one All-Rounders">{{ $allrounder1 }}</td>
                                                @endif
                                                @if($bowlers1<1)
                                                    <td class="font-weight-bold text-white bg-success" title="Please select atleast three Bowlers">{{ $bowlers1 }}</td>
                                                @else
                                                    <td class="font-weight-bold text-white bg-success" title="Please select atleast three Bowlers">{{ $bowlers1 }}</td>
                                                @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div id="Ctrl_SlctCriteriaTeam1" class="col-md-12 col-sm-12 mt-1 p-0">
                                <table class="table table-striped table-bordered dataTable no-footer w-100 d-table">
                                    <tbody>
                                        <tr>
                                            <th colspan="5" class="text-center">Selection Criteria</th>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>GK</td>
                                            <td>DEF</td>
                                            <td>MID</td>
                                            <td>ST</td>
                                        </tr>
                                        <tr>
                                            <th>Min</th>
                                            <td>1</td>
                                            <td>3</td>
                                            <td>1</td>
                                            <td>3</td>
                                        </tr>
                                        <tr>
                                            <th>Max</th>
                                            <td>1</td>
                                            <td>5</td>
                                            <td>3</td>
                                            <td>5</td>
                                        </tr>
                                        <tr>
                                            <th>Your Selection</th>
                                                @if($goalkeeper1<1)
                                                    <td class="font-weight-bold text-white bg-danger" title="Criteria fulfill for Goalkeeper1">{{ $goalkeeper1 }}</td>
                                                @else
                                                    <td class="font-weight-bold text-white bg-success" title="Criteria fulfill for Goalkeeper1">{{ $goalkeeper1 }}</td>
                                                @endif
                                                @if($defender1<3)
                                                    <td class="font-weight-bold text-white bg-danger" title="Please select atleast three Defender">{{ $defender1 }}</td>
                                                @else
                                                    <td class="font-weight-bold text-white bg-success" title="Please select atleast three Defender">{{ $defender1 }}</td>
                                                @endif
                                                @if($midfielder1<3)
                                                    <td class="font-weight-bold text-white bg-danger" title="Please select atleast one Midfielder">{{ $midfielder1 }}</td>
                                                @else
                                                    <td class="font-weight-bold text-white bg-success" title="Please select atleast one Midfielder">{{ $midfielder1 }}</td>
                                                @endif
                                                @if($striker1<2)
                                                    <td class="font-weight-bold text-white bg-success" title="Please select atleast three Striker">{{ $striker1 }}</td>
                                                @else
                                                    <td class="font-weight-bold text-white bg-success" title="Please select atleast three Striker">{{ $striker1 }}</td>
                                                @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <?php if($findmatchdetails->launch_status!='launched'){ ?>
                        

                        <?php } ?>
                        <table class="table table-striped table-bordered dataTable no-footer text-nowrap text-13px d-table" id="dataTable2">
                            <thead>
                                <tr role="row">
                                <th data-toggle="tooltip" title="Serial Number">#</th>
                                <th data-toggle="tooltip" title="Select Duo Player">Duo</th>
                                <th data-toggle="tooltip" title="Player Name">Player Name</th>
                                <th data-toggle="tooltip" title="Role">Role</th>
                                <th data-toggle="tooltip" title="Credit">Cr</th>
                                <?php if($findmatchdetails->launch_status!='launched'){ ?>
                                <th>Action</th>
                                <?php }?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $player2sno=0;?>
                                <?php foreach($findplayer2details as $p2){
                                $player2sno++;
                                $j =1;
                                ?>
                                <tr role="row">
                                <td><?php echo $player2sno; ?></td>
                                <?php if($findmatchdetails->launch_status=='pending'){   ?>
                                    <td><input value="1" name="forduo" type="checkbox" <?php if($p2->forduo==1){ echo 'checked' ;}?> onchange="update_duo('<?php echo $p2->matchkey; ?>','<?php echo $p2->id; ?>','forduo',this.value);"></td>
                                    <?php }else{ ?>
                                        <td><input value="1" name="forduo" type="checkbox" disabled <?php if($p2->forduo==1){ echo 'checked' ;}?>></td>  
                                    <?php } ?>
                                
                                <td class="us_name"><?php echo ucwords($p2->name); ?></td>
                                <td><a data-toggle="modal" data-target="#player2modal<?php echo $player2sno?>" class="text-decoration-none text-primary pointer"><?php echo ucwords($p2->role);?></a></td>
                                <td><a data-toggle="modal" data-target="#player2modal<?php echo $player2sno?>" class="text-decoration-none text-warning pointer"><?php if($p2->credit==""){ echo 0;}else{ echo $p2->credit; } ?></a></td>
                                <?php if($findmatchdetails->launch_status=='pending'){   ?>
                                <td class="text-center">
                                    <a data-toggle="modal" class="btn btn-xs btn-danger text-uppercase" href=""  onclick="delete_confirmation('<?php echo action('MatchController@deleteplayer',[base64_encode(serialize($p2->id)),base64_encode(serialize($p2->matchkey))])?>')"><i class="far fa-trash-alt"></i></a>
                                </td><?php
                                }
                                ?>
                                </tr>


                                <?php if($findmatchdetails->launch_status!='launched'){ ?>




                                    <!-- Modal -->
                                    <div class="modal fade" id="player2modal<?php echo $player2sno;?>" tabindex="-1" aria-labelledby="player1modalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="player1modalLabel">Change player role and credit</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="<?php echo action('MatchController@playerroles',$p2->id)?>" method="post" id="p2{{$player2sno}}" enctype="multipart/form-data">
                                                    {{csrf_field()}}
                                                    <div class="form-group">
                                                        <input type="text" class="form-control form-control-solid" name="name" value="<?php echo ucwords($p2->name); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control form-control-solid" onkeypress="return isNumberKey(event)" name="credit" placeholder="Enter player credits" value="<?php echo $p2->credit; ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        @if($f_type=='Cricket')
                                                            <select class="form-control form-control-solid" name="role">
                                                                <option value=""></option>
                                                                <option value="batsman" <?php if($p2->role=='batsman'){ echo 'selected'; }?>>Batsman</option>
                                                                <option value="bowler" <?php if($p2->role=='bowler'){ echo 'selected'; }?>>Bowler</option>
                                                                <option value="allrounder" <?php if($p2->role=='allrounder'){ echo 'selected'; }?>>All rounder</option>
                                                                <option value="keeper" <?php if($p2->role=='keeper'){ echo 'selected'; }?>>Wicket Keeper</option>
                                                            </select>
                                                        @else
                                                        <select class="form-control form-control-solid" name="role">
                                                            <option disabled selected value="">Select Role</option>
                                                            <option value="goalkeeper" <?php if($p2->role=='goalkeeper'){ echo 'selected'; }?>>Goal Keeper</option>
                                                            <option value="defender" <?php if($p2->role=='defender'){ echo 'selected'; }?>>Defender</option>
                                                            <option value="midfielder" <?php if($p2->role=='midfielder'){ echo 'selected'; }?>>Mid Fielder</option>
                                                            <option value="striker" <?php if($p2->role=='striker'){ echo 'selected'; }?>>Striker</option>
                                                        </select>
                                                        @endif
                                                    </div>
                                                    <div class="form-group">
                                                            
                                                        <div class="col-md-12">
                                                            <div class="row mx-0 justify-content-center ">


                                                            <?php

                                                                $input = ['image'];
                                                                $defaultLogo = ['team_image.png'];
                                                                $labels = ['Player Image'];

                                                                for($i=0; $i<count($input);$i++){?>

                                                                <?php $setting2= $input[$i];?>
                                                                <?php $defaultImages= $defaultLogo[$i];?>

                                                                <?php
                                                                    if( !empty($p2->image) ) {
                                                                        $image = $p2->image;
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
                                                                                    <input type='file' name="{{ $input[$i] }}" id="{{ $input[$i] }}<?php echo $player2sno;?>id1" accept=".png"  class="imageUpload d-none"/>
                                                                                    <label class="d-grid w-40px h-40px mb-0 rounded-pill bg-white text-success fs-20 shadow pointer font-weight-normal align-items-center justify-content-center" for="{{ $input[$i] }}<?php echo $player2sno;?>id1"><i class="fad fa-pencil"></i></label>
                                                                                </div>
                                                                                <div class="avatar-preview w-100px h-100px position-relative rounded-pill shadow">

                                                                                    <?php

                                                                                        if( @GetImageSize( asset($image) ) ){
                                                                                            $img =  asset($image);
                                                                                        } else {
                                                                                            $img = asset('player_image.png');
                                                                                        }
                                                                                    ?>

                                                                                    <div class="w-100 h-100 rounded-pill" id="{{ $input[$i] }}<?php echo $player2sno;?>id1-imagePreview" style="background-image: url({{ $img }});">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            <?php }?>

                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="checkbox" class="text-left">
                                                        <div class="custom-control custom-checkbox my-1 mr-sm-2">
                                                          <input value="global" name="global" type="checkbox" class="custom-control-input" id="customControlInline2{{$player2sno}}">
                                                          <label class="custom-control-label text-uppercase" for="customControlInline2{{$player2sno}}">Set as global</label>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn text-uppercase btn-sm btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn text-uppercase btn-sm btn-primary" form="p2{{$player2sno}}">Save changes</button>
                                            </div>
                                        </div>
                                        </div>
                                    </div>






                                <?php } ?>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php if($f_type=='Cricket'){?>
                            <div id="Ctrl_SlctCriteriaTeam1" class="col-md-12 col-sm-12 mt-2 p-0">
                                <table class="table table-striped table-bordered no-footer w-100 d-table">
                                    <tbody>
                                        <tr>
                                            <th colspan="5" style="text-align:center">Selection Criteria</th>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>WK</td>
                                            <td>Bat</td>
                                            <td>AR</td>
                                            <td>Bowl</td>
                                        </tr>
                                        <tr>
                                            <th>Min</th>
                                            <td>1</td>
                                            <td>3</td>
                                            <td>1</td>
                                            <td>3</td>
                                        </tr>
                                        <tr>
                                            <th>Max</th>
                                            <td>4</td>
                                            <td>6</td>
                                            <td>4</td>
                                            <td>5</td>
                                        </tr>
                                        <tr>
                            <th>Your Selection</th>
                            <?php
                                    if($wk2<1){
                                ?>
                                <td class="font-weight-bold text-white bg-danger" title="Criteria fulfill for wicket-Keeper"><?php echo $wk2;?></td>
                                <?php } else{
                                    ?>
                                <td class="font-weight-bold text-white bg-success" title="Criteria fulfill for wicket-Keeper"><?php echo $wk2;?></td>
                                <?php } ?>
                                <?php
                                    if($batsman2<3){
                                ?>
                                <td class="font-weight-bold text-white bg-danger" title="Please select atleast three Batsman"><?php echo $batsman2;?></td>
                                <?php } else{ ?>
                                <td class="font-weight-bold text-white bg-success" title="Please select atleast three Batsman"><?php echo $batsman2;?></td>
                                <?php } ?>
                                <?php
                                    if($allrounder2<1){
                                ?>
                                <td class="font-weight-bold text-white bg-danger" title="Please select atleast one All-Rounders"><?php echo $allrounder2;?></td>
                                <?php } else{ ?>
                                <td class="font-weight-bold text-white bg-success" title="Please select atleast one All-Rounders"><?php echo $allrounder2;?></td>
                                <?php } ?>
                                @if($bowlers2<1)
                                    <td class="font-weight-bold text-white bg-success" title="Please select atleast three Bowlers">{{ $bowlers2 }}</td>
                                @else
                                    <td class="font-weight-bold text-white bg-success" title="Please select atleast three Bowlers">{{ $bowlers2 }}</td>
                                @endif
                            </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php }else{?>
                            <div id="Ctrl_SlctCriteriaTeam1" class="col-md-12 col-sm-12 mt-2 p-0">
                                <table style="width:100%;" class="table table-striped table-bordered dataTable no-footer fs-12 d-table">
                                    <tbody>
                                        <tr>
                                            <th colspan="5" style="text-align:center">Selection Criteria</th>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>GK</td>
                                            <td>DEF</td>
                                            <td>MID</td>
                                            <td>ST</td>
                                        </tr>
                                        <tr>
                                            <th>Min</th>
                                            <td>1</td>
                                            <td>4</td>
                                            <td>4</td>
                                            <td>2</td>
                                        </tr>
                                        <tr>
                                            <th>Max</th>
                                            <td>1</td>
                                            <td>4</td>
                                            <td>4</td>
                                            <td>4</td>
                                        </tr>
                                        <tr>
                                            <th>Your Selection</th>
                                                <?php
                                                    if($goalkeeper2<1){
                                                ?>
                                                <td class="font-weight-bold text-white bg-danger" title="Criteria fulfill for Goalkeeper"><?php echo $goalkeeper2;?></td>
                                                <?php } else{
                                                    ?>
                                                <td class="font-weight-bold text-white bg-success" title="Criteria fulfill for Goalkeeper"><?php echo $goalkeeper2;?></td>
                                                <?php } ?>
                                                <?php
                                                    if($defender2<3){
                                                ?>
                                                <td class="font-weight-bold text-white bg-danger" title="Please select atleast three Defender"><?php echo $defender2;?></td>
                                                <?php } else{ ?>
                                                <td class="font-weight-bold text-white bg-success" title="Please select atleast three Defender"><?php echo $defender2;?></td>
                                                <?php } ?>
                                                <?php
                                                    if($midfielder2<3){
                                                ?>
                                                <td class="font-weight-bold text-white bg-danger" title="Please select atleast one Midfielder"><?php echo $midfielder2;?></td>
                                                <?php } else{ ?>
                                                <td class="font-weight-bold text-white bg-success" title="Please select atleast one Midfielder"><?php echo $midfielder2;?></td>
                                                <?php } ?>
                                                <?php
                                                    if($striker2<3){
                                                ?>
                                                <td class="font-weight-bold text-white bg-success" title="Please select atleast three Striker"><?php echo $striker2;?></td>
                                                <?php } else{ ?>
                                                <td class="font-weight-bold text-white bg-success" title="Please select atleast three Striker"><?php echo $striker2;?></td>
                                                <?php } ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php }?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(function(){
    var container = $('.container'), inputFile = $('#file'), img, btn, txt = 'Browse', txtAfter = 'Browse another pic';
           
	$(document).on('click', '.browse_btn', function(e){
        var browse_btn = $(this);

        var input_file_tag = $(this).attr('id').replace('upload_', '');

        $("#"+input_file_tag+"").trigger('click');
        
        var inputFile = $("#"+input_file_tag+"");

        var img = $('#uploadImg_' + input_file_tag);

        img.animate({opacity: 0}, 300);
        
        inputFile.on('change', function (e) {

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
            
            $(browse_btn).val( txtAfter );
        });

	});
});
</script>


<script>
$('#dataTable1, #dataTable2').dataTable({
    "paging": false,
    "searching": false,
    "LengthChange": false,
    "Filter": false,
    "Info": false,
    "showNEntries" : false
});
</script>

<script>
$(document).on('input', 'input[name="credit"]', function () {
    if( $(this).val() > 15 ) {
        Swal.fire('Player credit must be less than or equal to 15');

        $(this).val('');
    }
});

function update_duo(matchkey,playerid,field,value){
  $.ajax({
     type:'POST',
     url:'<?php echo asset('my-admin/updateduoplayer');?>',
     data:'_token=<?php echo csrf_token();?>&matchkey='+matchkey+'&playerid='+playerid+'&field='+field+'&value='+value,
     success:function(data){
       if(data==2){
        alert('You Can Select Only 5 Batsman Or Keeper')
       }
       if(data==3){
        alert('You Can Select Only 5 Bowler Or Allrounder')
       }
    }
  });
}
</script>

<script>
function delete_confirmation(url) {
  // sweet alert
    const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-sm btn-success ml-2',
        cancelButton: 'btn btn-sm btn-danger'
    },
    buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'success',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'No, cancel!',
    reverseButtons: true
    }).then((result) => {
    if (result.isConfirmed) {

      swalWithBootstrapButtons.fire(
                'Deleted!',
                'Player Deleted successfully.',
                'success'
                );

      window.location.href = url;

        
    } else if (
        /* Read more about handling dismissals below */
        result.dismiss === Swal.DismissReason.cancel
    ) {
        swalWithBootstrapButtons.fire(
        'Cancelled',
        'Cancelled successfully :)',
        'error'
        );
        return false;
    }
  })
}
</script>
@endsection('content')
