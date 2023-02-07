@extends('main')

@section('heading')
    Sub Admin Manager
@endsection('heading')

@section('sub-heading')
    Add Sub Admin
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('SubAdminController@view_sub_admin') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary float-right" data-toggle="tooltip" title="View All Sub admin"><i class="fa fa-eye"></i>&nbsp; View</a>
@endsection('card-heading-btn')

@section('content')

@include('alert_msg')

<div class="card">
    <div class="card-header">Add Sub Admin</div>
    <div class="card-body">
        <div class="sbp-preview">
            <div class="sbp-preview-content p-4">
                {{ Form::open(array('url' => action('SubAdminController@add_sub_admin'), 'method' => 'post','id' => 'j-forms','class'=>'j-forms row mx-0' ))}}

                    {{csrf_field()}}
                    
                    <div class="form-group col-md-6 col-12">
                        <label class='control-label text-bold' for="first-name">Name<span>*</span></label>
                        <input name="name" class="form-control form-control-solid" type="text" placeholder="Please enter name" required="">
                    </div>   
                    
                    <div class="form-group col-md-6 col-12">
                        <label class="control-label text-bold" for="first-name">Email<span class="">*</span></label>
                        <input name="email" class="form-control form-control-solid" type="email" placeholder="Please enter email" required="">
                    </div>   
                    
                    <div class="form-group col-md-6 col-12">
                        <label class="control-label text-bold" for="first-name">Mobile<span class="">*</span></label>
                        <input name="mobile" class="form-control form-control-solid" type="text" 
                        placeholder="Please enter Mobile" id="mobile"
                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                        maxlength="10" pattern="[1-9]{1}[0-9]{9}">
                    </div>   

                    <div class="form-group col-md-6 col-12">
                        <label class="control-label text-bold" for="first-name">Password<span class="">*</span></label>
                        <input name="password" class="form-control form-control-solid" type="text" placeholder="Please enter password" required="">
                    </div>
 

                    <div class="form-group col-12 mt-2">
                                    <label class="control-label text-2x" for="first-name">Permissions<span class="required">*</span></label>
                            <div class="row">





                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                

                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> Series Manager</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallseries" name="selectall" id="selectallseries">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallseries">Select all permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallseries").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallseries').each(function(){
                                                                        $(".selectallseries").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallseries').each(function(){
                                                                        $(".selectallseries").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>

                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallseries" id="series-create" value="SeriesController@create" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="series-create">Create</label>
                                                </div>

                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallseries" id="series-edit" value="SeriesController@edit" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="series-edit">Edit</label>
                                                </div>

                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallseries" id="series-index" value="SeriesController@index,SeriesController@series_datatable,MatchController@importseriesdata" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="series-index">View</label>
                                                </div>

                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallseries" id="series-updateseriesstatus" value="SeriesController@updateseriesstatus" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="series-updateseriesstatus">Update Series status</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> Teams Manager</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallteam" name="selectall" id="selectallteam">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallteam">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallteam").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallteam').each(function(){
                                                                        $(".selectallteam").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallteam').each(function(){
                                                                        $(".selectallteam").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallteam" id="team-edit_team" value="TeamController@edit_team" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="team-edit_team">Edit</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallteam" id="team-view_team" value="TeamController@view_team,TeamController@view_team_datatable" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="team-view_team">View</label>
                                                </div>

                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallteam" id="team-download-team-data" value="TeamController@downloadteamdata" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="team-download-team-data">Download Team Data</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> Player Manager</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallplayer" name="selectall" id="selectallplayer">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallplayer">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallplayer").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallplayer').each(function(){
                                                                        $(".selectallplayer").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallplayer').each(function(){
                                                                        $(".selectallplayer").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallplayer" id="player-edit_player" value="PlayerController@edit_player" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="player-edit_player">Edit</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallplayer" id="player-view_player" value="PlayerController@view_player,PlayerController@view_player_datatable" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="player-view_player">View</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallplayer" id="player-saveplayerroles" value="PlayerController@saveplayerroles" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="player-saveplayerroles">Update Credit</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallplayer" id="player-addplayermanually" value="PlayerController@addplayermanually" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="player-addplayermanually">Add manually</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallplayer" id="player-downloadallplayerdetails" value="PlayerController@downloadallplayerdetails" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="player-downloadallplayerdetails">Download player data</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> Upcoming Matches</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallupc_match" name="selectall" id="selectallupc_match">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallupc_match">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallupc_match").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallupc_match').each(function(){
                                                                        $(".selectallupc_match").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallupc_match').each(function(){
                                                                        $(".selectallupc_match").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallupc_match" id="upc_match-upcoming_matches" value="MatchController@upcoming_matches,MatchController@upcoming_matches_datatable" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="upc_match-upcoming_matches">View</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallupc_match" id="upc_match-editmatch" value="MatchController@editmatch" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="upc_match-editmatch">Edit matches &amp; series</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallupc_match" id="upc_match-importsquad" value="MatchController@importsquad" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="upc_match-importsquad">Import players</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallupc_match" id="upc_match-playerroles" value="MatchController@playerroles,MatchController@viewmatchdetails,MatchController@launchmatch,MatchController@unlaunch,MatchController@secondinninglaunch,MatchController@updatelogo,MatchController@importdatafromapi,MatchController@launch,MatchController@deleteplayer,MatchController@DownloadPlayer,MatchController@DownloadPlayer2,MatchController@UploadPlayer,MatchController@UploadPlayer2" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="upc_match-playerroles">Launch match</label>
                                                </div> 
                                                
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> All Matches</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallmatches" name="selectall" id="selectallmatches">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallmatches">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallmatches").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallmatches').each(function(){
                                                                        $(".selectallmatches").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallmatches').each(function(){
                                                                        $(".selectallmatches").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallmatches" id="selectallmatches-allmatches" value="MatchController@allmatches,MatchController@allmatches_datatable" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallmatches-allmatches">View</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> Update Playing XI</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallplaying_x1" name="selectall" id="selectallplaying_x1">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallplaying_x1">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallplaying_x1").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallplaying_x1').each(function(){
                                                                        $(".selectallplaying_x1").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallplaying_x1').each(function(){
                                                                        $(".selectallplaying_x1").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallplaying_x1" id="selectallplaying_x1-updateplaying11" value="PlayingController@updateplaying11" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallplaying_x1-updateplaying11">View</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallplaying_x1" id="selectallplaying_x1-match_player1" value="PlayingController@match_player1,PlayingController@match_player2,PlayingController@upp1,PlayingController@upp2,PlayingController@launchplaying" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallplaying_x1-match_player1">Players Select / Launch Playing11</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> User Manager</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallusermanager" name="selectall" id="selectallusermanager">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallusermanager">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallusermanager").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallusermanager').each(function(){
                                                                        $(".selectallusermanager").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallusermanager').each(function(){
                                                                        $(".selectallusermanager").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallusermanager" id="selectallusermanager-index" value="RegisteruserController@index,RegisteruserController@view_users_datatable" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallusermanager-index">All Users</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallusermanager" id="selectallusermanager-getuserdetails" value="RegisteruserController@getuserdetails" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallusermanager-getuserdetails">Users details</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallusermanager" id="selectallusermanager-edituserdetails" value="RegisteruserController@edituserdetails" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallusermanager-edituserdetails">Edit user details</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallusermanager" id="selectallusermanager-update_userdetails" value="RegisteruserController@update_userdetails" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallusermanager-update_userdetails">Update user details</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallusermanager" id="selectallusermanager-updateuserstatus" value="RegisteruserController@updateuserstatus" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallusermanager-updateuserstatus">Users status</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallusermanager" id="selectallusermanager-downloadallusers" value="RegisteruserController@downloadalluserdetails" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallusermanager-downloadallusers">Download user</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallusermanager" id="selectallusermanager-downloadalluserstransaction" value="RegisteruserController@downloadalluserstransaction" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallusermanager-downloadalluserstransaction">Download user transaction</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallusermanager" id="selectallusermanager-viewtransactions" value="RegisteruserController@viewtransactions,RegisteruserController@viewtransactions_table" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallusermanager-viewtransactions">Users transactions</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallusermanager" id="selectallusermanager-userswallet" value="RegisteruserController@userswallet,RegisteruserController@userswallet_table" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallusermanager-userswallet">User Wallet</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallusermanager" id="selectallusermanager-adminwallet" value="AdminwalletController@adminwallet,AdminwalletController@wallet_list" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallusermanager-adminwallet">Admin Wallet</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallusermanager" id="selectallusermanager-giveadminwallet" value="AdminwalletController@giveadminwallet,AdminwalletController@searchadminwallet,AdminwalletController@deductmoneyinwallet,AdminwalletController@addmoneyinwallet,AdminwalletController@details" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallusermanager-giveadminwallet">Admin add/deduct money to/from user</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> Verify Manager</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallverifymngr" name="selectall" id="selectallverifymngr">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallverifymngr">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallverifymngr").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallverifymngr').each(function(){
                                                                        $(".selectallverifymngr").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallverifymngr').each(function(){
                                                                        $(".selectallverifymngr").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallverifymngr" id="selectallverifymngr-verifypan" value="VerificationController@verifypan,VerificationController@verifypan_table" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallverifymngr-verifypan">Verify Pan</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallverifymngr" id="selectallverifymngr-viewpandetails" value="VerificationController@viewpandetails" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallverifymngr-viewpandetails">View Pan detail</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallverifymngr" id="selectallverifymngr-editpandetails" value="VerificationController@editpandetails,VerificationController@updatepantatus" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallverifymngr-editpandetails">Edit Pan detail</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallverifymngr" id="selectallverifymngr-verifybankaccount" value="VerificationController@verifybankaccount,VerificationController@verifybankaccount_table" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallverifymngr-verifybankaccount">Verify Bank</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallverifymngr" id="selectallverifymngr-viewbankdetails" value="VerificationController@viewbankdetails" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallverifymngr-viewbankdetails">View Bank detail</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallverifymngr" id="selectallverifymngr-editbankdetails" value="VerificationController@editbankdetails,VerificationController@updatebanktatus" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallverifymngr-editbankdetails">Edit Bank detail</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallverifymngr" id="selectallverifymngr-withdraw_amount" value="VerificationController@withdraw_amount,VerificationController@withdrawl_amount_table" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallverifymngr-withdraw_amount">Withdraw Request</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallverifymngr" id="selectallverifymngr-downloadwithdrawaldata" value="VerificationController@downloadwithdrawaldata" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallverifymngr-downloadwithdrawaldata">Download Withdraw Request</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallverifymngr" id="selectallverifymngr-approve" value="VerificationController@approve,VerificationController@withdrawl_amount_table" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallverifymngr-approve">Approve and Cancel withdraw</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>

                                    

                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> Notifications</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallnotification" name="selectall" id="selectallnotification">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallnotification">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallnotification").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallnotification').each(function(){
                                                                        $(".selectallnotification").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallnotification').each(function(){
                                                                        $(".selectallnotification").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallnotification" id="selectallnotification-pushnotifications" value="NotificationController@pushnotifications,NotificationController@getusers,NotificationController@addmodule,NotificationController@import" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallnotification-pushnotifications">Android Notifications</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallnotification" id="selectallnotification-emailnotification" value="NotificationController@emailnotifications, NotificationController@getusers,NotificationController@addmodule,NotificationController@import" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallnotification-emailnotification">Email Notifications</label>
                                                </div>
                                                
                                                
                                            </div>
                                        </div>
                                    </div>
                                
                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> Contest Full Detail</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallcontestfdetail" name="selectall" id="selectallcontestfdetail">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallcontestfdetail">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallcontestfdetail").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallcontestfdetail').each(function(){
                                                                        $(".selectallcontestfdetail").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallcontestfdetail').each(function(){
                                                                        $(".selectallcontestfdetail").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallcontestfdetail" id="selectallcontestfdetail-fulldetail" value="ContestFullDetailController@fulldetail1" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallcontestfdetail-fulldetail">View</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallcontestfdetail" id="selectallcontestfdetail-allcontests" value="ContestFullDetailController@allcontests,ContestFullDetailController@allusers,ContestFullDetailController@user_team,ContestFullDetailController@viewjoinusers_datatable" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallcontestfdetail-allcontests">All Contest</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                
                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> Contest Category Managers</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallccmanger" name="selectall" id="selectallccmanger">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallccmanger">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallccmanger").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallccmanger').each(function(){
                                                                        $(".selectallccmanger").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallccmanger').each(function(){
                                                                        $(".selectallccmanger").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallccmanger" id="selectallccmanger-create_category" value="ContestController@create_category" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallccmanger-create_category">Add</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallccmanger" id="selectallccmanger-edit_contest_category" value="ContestController@edit_contest_category" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallccmanger-edit_contest_category">Edit</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallccmanger" id="selectallccmanger-view_contest_category" value="ContestController@view_contest_category" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallccmanger-view_contest_category">View</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallccmanger" id="selectallccmanger-delete_contest_category" value="ContestController@delete_contest_category" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallccmanger-delete_contest_category">Delete</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallccmanger" id="selectallccmanger-view_search_contest_category" value="ContestController@view_search_contest_category" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallccmanger-view_search_contest_category">Search</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> Global Contest</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallgcontest" name="selectall" id="selectallgcontest">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallgcontest">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallgcontest").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallgcontest').each(function(){
                                                                        $(".selectallgcontest").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallgcontest').each(function(){
                                                                        $(".selectallgcontest").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallgcontest" id="selectallgcontest-create_global" value="ContestController@create_global,ContestController@addpricecard" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallgcontest-create_global">Add</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallgcontest" id="selectallgcontest-editglobalcontest" value="ContestController@editglobalcontest" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallgcontest-editglobalcontest">Edit</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallgcontest" id="selectallgcontest-global_index" value="ContestController@global_index,ContestController@global_index_datatable" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallgcontest-global_index">View</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallgcontest" id="selectallgcontest-globalcat_muldelete" value="ContestController@globalcat_muldelete" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallgcontest-globalcat_muldelete">Multi Delete</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallgcontest" id="selectallgcontest-delete_global_contest" value="ContestController@delete_global_contest" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallgcontest-delete_global_contest">Delete</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallgcontest" id="selectallgcontest-addpricecard" value="ContestController@addpricecard,ContestController@deletematchpricecard" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallgcontest-addpricecard">Price Card</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> Custom Contest</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallccontest" name="selectall" id="selectallccontest">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallccontest">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallccontest").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallccontest').each(function(){
                                                                        $(".selectallccontest").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallccontest').each(function(){
                                                                        $(".selectallccontest").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallccontest" id="selectallccontest-create_custom" value="ContestController@create_custom,ContestController@selectglobalcontest,ContestController@selectglobcontest_datatable,ContestController@multiselect_globalcat" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallccontest-create_custom">Add</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallccontest" id="selectallccontest-editcustomcontest" value="ContestController@editcustomcontest" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallccontest-editcustomcontest">Edit</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallccontest" id="selectallccontest-custom_index" value="ContestController@create_custom_contest" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallccontest-custom_index">View</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallccontest" id="selectallccontest-delete_customcontest" value="ContestController@delete_customcontest" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallccontest-delete_customcontest">Delete</label>
                                                </div>

                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallccontest" id="selectallccontest-makeConfirmed" value="ContestController@makeConfirmed" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallccontest-makeConfirmed">Make Confirmed</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> Import League In Contest</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallimp_league" name="selectall" id="selectallimp_league">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallimp_league">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallimp_league").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallimp_league').each(function(){
                                                                        $(".selectallimp_league").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallimp_league').each(function(){
                                                                        $(".selectallimp_league").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallimp_league" id="selectallimp_league-importdata" value="ContestController@importdata" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallimp_league-importdata">Import Contest</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallimp_league" id="selectallimp_league-contestcancel" value="ContestController@contestcancel" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallimp_league-contestcancel">Cancel Contest</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallimp_league" id="selectallimp_league-addmatchpricecard" value="ContestController@addmatchpricecard,ContestController@deletepricecard" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallimp_league-addmatchpricecard">Add/Edit Price Cards</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> Results</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallresultcontroller" name="selectall" id="selectallresultcontroller">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallresultcontroller">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallresultcontroller").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallresultcontroller').each(function(){
                                                                        $(".selectallresultcontroller").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallresultcontroller').each(function(){
                                                                        $(".selectallresultcontroller").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallresultcontroller" id="selectallresultcontroller-match_result" value="ResultController@match_result,ResultController@match_detail" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallresultcontroller-match_result">View Result</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallresultcontroller" id="selectallresultcontroller-updatepoints" value="ResultController@updatepoints,ResultController@batting_points,ResultController@bowling_points,ResultController@fielding_points,ResultController@team_points,ResultController@match_score,ResultController@select_join_person,YoutuberBonusController@give_youtuber_bonus,ProfitLossController@updatereport" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallresultcontroller-updatepoints">Update point</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallresultcontroller" id="selectallresultcontroller-refund_allamount" value="ResultController@refund_allamount,ResultController@refund_amount,ResultController@refundprocess" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallresultcontroller-refund_allamount">Refund amount</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallresultcontroller" id="selectallresultcontroller-updatematchfinalstatus" value="ResultController@updatematchfinalstatus,ResultController@updatescores,ResultController@userpoints" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallresultcontroller-updatematchfinalstatus">Update Match Final Status</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallresultcontroller" id="selectallresultcontroller-match_points" value="ResultController@match_points,ResultController@viewwinners,ResultController@distribute_winning_amount,ResultController@refund_allamount" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallresultcontroller-match_points">Is Reviewed or Declare Winner</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                
                                
                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> Banner</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallbanner" name="selectall" id="selectallbanner">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallbanner">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallbanner").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallbanner').each(function(){
                                                                        $(".selectallbanner").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallbanner').each(function(){
                                                                        $(".selectallbanner").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallbanner" id="selectallbanner-view_sidebanner" value="SidebannerController@view_sidebanner,SidebannerController@view_sidebanner_table,SidebannerController@show_sidebanner" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallbanner-view_sidebanner">View</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallbanner" id="selectallbanner-add_sidebanner" value="SidebannerController@add_sidebanner,SidebannerController@sidebanner" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallbanner-add_sidebanner">Add</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallbanner" id="selectallbanner-edit_sidebanner" value="SidebannerController@edit_sidebanner,SidebannerController@update_sidebanner" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallbanner-edit_sidebanner">Edit</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallbanner" id="selectallbanner-delete_sidebanner" value="SidebannerController@delete_sidebanner" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallbanner-delete_sidebanner">Delete</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                

                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                        <div class="form-group col-md-12 mb-0">
                                            <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> General Tabs Manager</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallgen_tabs" name="selectall" id="selectallgen_tabs">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallgen_tabs">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallgen_tabs").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallgen_tabs').each(function(){
                                                                        $(".selectallgen_tabs").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallgen_tabs').each(function(){
                                                                        $(".selectallgen_tabs").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallgen_tabs" id="selectallgen_tabs-index" value="GeneralTabsController@index,GeneralTabsController@viewrefer,GeneralTabsController@delete,GeneralTabsController@deleterefer" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallgen_tabs-index">Update Bonus</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                            <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> Add point Manager</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectalladdpoint" name="selectall" id="selectalladdpoint">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectalladdpoint">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectalladdpoint").click(function(){
                                                                if(this.checked){
                                                                    $('.selectalladdpoint').each(function(){
                                                                        $(".selectalladdpoint").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectalladdpoint').each(function(){
                                                                        $(".selectalladdpoint").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectalladdpoint" id="selectalladdpoint-add_pointt" value="AddpointController@pointt,AddpointController@add_pointt" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectalladdpoint-add_pointt">Add / View</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> Offers Manager</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallcoupon" name="selectall" id="selectallcoupon">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallcoupon">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallcoupon").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallcoupon').each(function(){
                                                                        $(".selectallcoupon").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallcoupon').each(function(){
                                                                        $(".selectallcoupon").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallcoupon" id="selectallcoupon-popular" value="OffersController@popular,OffersController@getOffers" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallcoupon-popular">View</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallcoupon" id="selectallcoupon-addOffer" value="OffersController@addOffer" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallcoupon-addOffer">Add</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallcoupon" id="selectallcoupon-editoffer" value="OffersController@editoffer" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallcoupon-editoffer">Edit</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallcoupon" id="selectallcoupon-deleteoffer" value="OffersController@deleteoffer" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="selectallcoupon-deleteoffer">Delete</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> Youtuber Manager</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input youtbes" disabled name="selectall" id="youtbes">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="youtbes">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#youtbes").click(function(){
                                                                if(this.checked){
                                                                    $('.youtbes').each(function(){
                                                                        $(".youtbes").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.youtbes').each(function(){
                                                                        $(".youtbes").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input youtbes" id="youtbes-view_youtuber" value="YoutuberController@view_youtuber,YoutuberController@view_youtuber_dt" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="youtbes-view_youtuber">View</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input youtbes" id="youtbes-createyoutuber" value="YoutuberController@add_youtuber" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="youtbes-createyoutuber">Add</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input youtbes" id="youtbes-edityoutuber" value="YoutuberController@edit_youtuber" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="youtbes-edityoutuber">Edit</label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input youtbes" id="youtbes-delete_youtuber" value="YoutuberController@delete_youtuber" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="youtbes-delete_youtuber">Delete</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> Duo Manager</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallduo" name="selectall" id="selectallduo">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallduo">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallduo").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallduo').each(function(){
                                                                        $(".selectallduo").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallduo').each(function(){
                                                                        $(".selectallduo").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallduo" id="duo_per" value="MatchController@updateduoplayer" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="duo_per">Duo Permission</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-12 my-2">
                                        <div class="row shadow bg-white rounded-10 py-3 px-2 mx-0 h-100">
                                            <div class="form-group col-md-12 mb-0">
                                                <label class="control-label text-primary"><i class="fad fa-shield-alt"></i> Card Manager</label>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallcard" name="selectall" id="selectallcard">
                                                    <label class="custom-control-label fs-14 text-uppercase font-weight-900" for="selectallcard">Select all Permissions</label>
                                                    <script>
                                                        $(document).ready(function(){
                                                            $("#selectallcard").click(function(){
                                                                if(this.checked){
                                                                    $('.selectallcard').each(function(){
                                                                        $(".selectallcard").prop('checked', true);
                                                                    })
                                                                }else{
                                                                    $('.selectallcard').each(function(){
                                                                        $(".selectallcard").prop('checked', false);
                                                                    })
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input selectallcard" id="card_per" value="CardController@importcarddatafromapi,CardController@importplayerdata,CardController@index,CardController@series_carddatatable,CardController@edit,CardController@view_team,CardController@view_team_datatable,CardController@edit_team,CardController@view_player,CardController@edit_player,CardController@get_cardteams,CardController@savecardplayerroles,CardController@view_player_datatable,CardController@view_contestresult,CardController@view_contestresult_datatable,CardController@view_challengeresult,ContestCardController@create_card,ContestCardController@card_index,ContestCardController@card_index_datatable,ContestCardController@editcardcontest,ContestCardController@delete_card_contest,ContestCardController@cardcat_muldelete" name="permissions[]">
                                                    <label class="custom-control-label fs-14 font-weight-normal" for="card_per">Card Permission</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>   
                        </div>
                    
                    <div class="col-12 text-right mt-4 mb-2">
                        <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
@endsection