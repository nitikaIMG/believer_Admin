@extends('main')

@section('heading')
    Sub Admin Manager
@endsection('heading')

@section('sub-heading')
    View Permissions
@endsection('sub-heading')

@section('content')

<div class="card">
    <div class="card-header">
        <div class="row w-100 align-items-center mx-0">
            <div class="col-md col-12 mb-md-0 mb-2 text-md-left text-center">View Permissions</div>
            <div class="col-md-auto col-12 px-md-3 px-0 text-center">
                    <a href="{{ action('SubAdminController@view_sub_admin') }}" class="btn btn-secondary text-uppercase btn-sm rounded-pill" data-toggle="tooltip" font-weight-600 title="Return Back"><i class="far fa-undo-alt"></i>&nbsp; Return Back</a>
            </div>
        </div>
    </div>
        <div class="card-body">
            <div class="sbp-preview">
                <div class="sbp-preview-content p-3">

                    @foreach($permissions as $permission)
                        <div class="row mx-0">
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Settings Manager</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        $f1= explode(',',$permissions->permissions);
                                        if(in_array('SettingsController@ui_settings',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> UI Settings</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> UI Settings</div>';
                                        };
                                        if(in_array('SettingsController@facebook_settings',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Facebook Settings</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Facebook Settings</div>';
                                        };
                                        if(in_array('SettingsController@google_settings',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Google Settings</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Google Settings</div>';
                                        };
                                        if(in_array('SettingsController@alert_settings',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Alert Settings</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Alert Settings</div>';
                                        };
                                        if(in_array('SettingsController@payment_gateway_settings',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Payment Gateway Settings</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Payment Gateway Settings</div>';
                                        };
                                        if(in_array('SettingsController@reset_admin_theme',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Reset Admin Theme</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Reset Admin Theme</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Series Manager</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        $f1= explode(',',$permissions->permissions);
                                        if(in_array('SeriesController@create',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Add</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Add</div>';
                                        };
                                        if(in_array('SeriesController@edit',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Edit</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Edit</div>';
                                        };
                                        if(in_array('SeriesController@index',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> View</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> View</div>';
                                        };
                                        if(in_array('SeriesController@updateseriesstatus',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Update Series status</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Update Series status</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Teams Manager</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        if(in_array('TeamController@edit_team',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Edit</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Edit</div>';
                                        };
                                        if(in_array('TeamController@view_team',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> View</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> View</div>';
                                        };
                                        if(in_array('TeamController@downloadteamdata',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Download Team Data</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Download Team Data</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Players manager</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        if(in_array('PlayerController@edit_player',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Edit</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Edit</div>';
                                        };
                                        if(in_array('PlayerController@view_player',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> View</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> View</div>';
                                        };
                                        if(in_array('PlayerController@saveplayerroles',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Update Credit</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Update Credit</div>';
                                        };
                                        if(in_array('PlayerController@addplayermanually',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Add manually</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Add manually</div>';
                                        };
                                        if(in_array('PlayerController@downloadallplayerdetails',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Download players data</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Download players data</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Playing XI manager</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        if(in_array('PlayingController@updateplaying11',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> View</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> View</div>';
                                        };
                                        if(in_array('PlayingController@match_player1',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Players Select / Launch Playing11</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Players Select / Launch Playing11</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Upcoming Matches</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        if(in_array('MatchController@upcoming_matches',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> View</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> View</div>';
                                        };
                                        if(in_array('MatchController@editmatch',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Edit matches & series</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Edit matches & series</div>';
                                        };
                                        if(in_array('MatchController@importsquad',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Import players</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Import players</div>';
                                        };
                                        if(in_array('MatchController@playerroles',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Launch match</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Launch match</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> All Matches</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        if(in_array('MatchController@allmatches',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> View</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> View</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> User Manager</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        if(in_array('RegisteruserController@index',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> All Users</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> All Users</div>';
                                        };
                                        if(in_array('RegisteruserController@getuserdetails',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Users details</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Users details</div>';
                                        };
                                        if(in_array('RegisteruserController@edituserdetails',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Edit details</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Edit details</div>';
                                        };
                                        if(in_array('RegisteruserController@update_userdetails',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Update details</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Update details</div>';
                                        };
                                        if(in_array('RegisteruserController@updateuserstatus',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Users status</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Users status</div>';
                                        };
                                        if(in_array('RegisteruserController@downloadalluserdetails',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Download users</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Download users</div>';
                                        };
                                        if(in_array('RegisteruserController@downloadalluserstransaction',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Download users transaction</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Download users transaction</div>';
                                        };
                                        if(in_array('RegisteruserController@viewtransactions',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Users transactions</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Users transactions</div>';
                                        };
                                        if(in_array('RegisteruserController@userswallet',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> User Wallet</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> User Wallet</div>';
                                        };
                                        if(in_array('AdminwalletController@adminwallet',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Admin Wallet</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Admin Wallet</div>';
                                        };
                                        if(in_array('AdminwalletController@giveadminwallet',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Admin add / delete money to / from wallet</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Admin add / delete money to / from wallet</div>';
                                        };
                                    ?>
                                    </div>
                                </div>
                                <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                    <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Verification Manager</label>
                                    <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        if(in_array('VerificationController@verifypan',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Verify Pan</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Verify Pan</div>';
                                        };
                                        if(in_array('VerificationController@viewpandetails',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> View Pan detail</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> View Pan detail</div>';
                                        };
                                        if(in_array('VerificationController@updatepantatus',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Edit Pan detail</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Edit Pan detail</div>';
                                        };
                                        if(in_array('VerificationController@verifybankaccount',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Verify Bank</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Verify Bank</div>';
                                        };
                                        if(in_array('VerificationController@viewbankdetails',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> View Bank detail</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> View Bank detail</div>';
                                        };
                                        if(in_array('VerificationController@editbankdetails',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Edit Bank detail</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Edit Bank detail</div>';
                                        };
                                        if(in_array('VerificationController@withdraw_amount',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Withdraw Requests</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Withdraw Requests</div>';
                                        };
                                        if(in_array('VerificationController@downloadwithdrawaldata',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Download Withdraw Requests</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Download Withdraw Requests</div>';
                                        };
                                        if(in_array('VerificationController@approve',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Approve and Cancel withdraw</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Approve and Cancel withdraw</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Received fund</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        if(in_array('FundController@card',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> View Received Fund</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> View Received Fund</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Notifications</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        if(in_array('NotificationController@pushnotifications',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Android Notifications</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Android Notifications</div>';
                                        };
                                        if(in_array('NotificationController@emailnotifications',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Email Notifications</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Email Notifications</div>';
                                        };
                                        if(in_array('NotificationController@smsnotifications',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> SMS Notifications</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> SMS Notifications</div>';
                                        };
                                    ?>
                                </div>
                            </div>

                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Contest Full Detail</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        if(in_array('ContestFullDetailController@fulldetail1',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> View</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> View</div>';
                                        };
                                        if(in_array('ContestFullDetailController@allcontests',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> All Contest</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> All Contest</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Contest Category</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        if(in_array('ContestController@create_category',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Add</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Add</div>';
                                        };
                                        if(in_array('ContestController@edit_contest_category',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Edit</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Edit</div>';
                                        };
                                        if(in_array('ContestController@view_contest_category',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> View</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> View</div>';
                                        };
                                        if(in_array('ContestController@delete_contest_category',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Delete</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Delete</div>';
                                        };
                                        if(in_array('ContestController@view_search_contest_category',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Search</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Search</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Custom Contest</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        if(in_array('ContestController@create_custom',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Add</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Add</div>';
                                        };
                                        if(in_array('ContestController@editcustomcontest',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Edit</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Edit</div>';
                                        };
                                        if(in_array('ContestController@create_custom_contest',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> View</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> View</div>';
                                        };
                                        if(in_array('ContestController@delete_customcontest',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Delete</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Delete</div>';
                                        };
                                        if(in_array('ContestController@makeConfirmed',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Make Confirmed</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Make Confirmed</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Global Contest</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php  
                                        if(in_array('ContestController@create_global',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Add</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Add</div>';
                                        };
                                        if(in_array('ContestController@editglobalcontest',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Edit</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Edit</div>';
                                        };
                                        if(in_array('ContestController@global_index',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> View</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> View</div>';
                                        };
                                        if(in_array('ContestController@globalcat_muldelete',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Multi delete</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Multi delete</div>';
                                        };
                                        if(in_array('ContestController@delete_global_contest',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Delete</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Delete</div>';
                                        };
                                        if(in_array('ContestController@addpricecard',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Price Card</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Price Card</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Import Leagues in contest</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        if(in_array('ContestController@importdata',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Import Contest</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Import Contest</div>';
                                        };
                                        if(in_array('ContestController@contestcancel',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Cancel Contest</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Cancel Contest</div>';
                                        };
                                        if(in_array('ContestController@addmatchpricecard',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Add/Edit Price Cards</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Add/Edit Price Cards</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Results</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        if(in_array('ResultController@match_result',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> View Result</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> View Result</div>';
                                        };
                                        if(in_array('ResultController@updatepoints',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Update Points</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Update Points</div>';
                                        };
                                        if(in_array('ResultController@refund_amount',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Refund</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Refund</div>';
                                        };
                                        if(in_array('ResultController@updatematchfinalstatus',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Update Match Final Status</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Update Match Final Status</div>';
                                        };
                                        if(in_array('ResultController@match_points',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Is Reviewed or Declare Winner</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Is Reviewed or Declare Winner</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Banner</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        if(in_array('SidebannerController@view_sidebanner',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> View</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> View</div>';
                                        };
                                        if(in_array('SidebannerController@add_sidebanner',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Add</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Add</div>';
                                        };
                                        if(in_array('SidebannerController@update_sidebanner',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Edit</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Edit</div>';
                                        };
                                        if(in_array('SidebannerController@delete_sidebanner',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Delete</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Delete</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> General Tabs Manager</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        if(in_array('GeneralTabsController@index', $f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Update Bonus</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Update Bonus</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Add Point Manager</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        if(in_array('AddpointController@add_pointt', $f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Add/View</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Add/View</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Offers Manager</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        if(in_array('OffersController@getOffers', $f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> View</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> View</div>';
                                        };
                                        if(in_array('OffersController@addOffer', $f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Add</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Add</div>';
                                        };
                                        if(in_array('OffersController@editoffer', $f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Edit</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Edit</div>';
                                        };
                                        if(in_array('OffersController@deleteoffer', $f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Delete</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Delete</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Youtuber Manager</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        if(in_array('YoutuberController@view_youtuber',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> View</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> View</div>';
                                        };
                                        if(in_array('YoutuberController@add_youtuber',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Add</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Add</div>';
                                        };
                                        if(in_array('YoutuberController@edit_youtuber',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Edit</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Edit</div>';
                                        };
                                        if(in_array('YoutuberController@delete_youtuber',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Delete</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Delete</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 align-self-stretch align-items-center h-revert my-0 pb-5">
                                <label class="control-label text-bold text-dark fs-md-15 fs-14"><i class="fad fa-shield-alt text-success"></i> Point System Manager</label>
                                <div class="row mx-0 shadow rounded h-100 py-2 align-content-start mb-n4">
                                    <?php
                                        $f1= explode(',',$permissions->permissions);
                                        if(in_array('PointSystemController@point_system',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> View Point System</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> View Point System</div>';
                                        };
                                        if(in_array('PointSystemController@update_point_system',$f1)){
                                            echo '<div class="col-12 mb-1 text-dark fs-13"><i class="fas fa-check-circle text-success"></i> Update Point System</div>';
                                        }
                                        else {
                                            echo '<div class="col-12 mb-1 fs-13"><i class="fas fa-times-circle text-danger"></i> Update Point System</div>';
                                        };
                                    ?>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection