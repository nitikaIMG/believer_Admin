@extends('main')

@section('heading')
    Dashboard
@endsection('heading')
@section('card-heading-btn')
@if(Auth::user()->role == '1')
    <a  href="<?php echo action('YoutuberCardBonusController@give_youtuber_cardbonus') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase mr-2 text-primary float-right"  style="float: right;" data-toggle="tooltip" title="Give Youtuber Bonus Card Game"> Give Youtuber Bonus Card Game</a>
    <a  href="<?php echo action('CardProfitLossController@updatereport') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase mr-2 text-primary float-right"  style="float: right;" data-toggle="tooltip" title="Update Card Profit Loss"> Update Card Profit Loss</a>
    <a  href="<?php echo action('CronJobController@carddailyreport') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase mr-2 text-primary float-right"  style="float: right;" data-toggle="tooltip" title="Update Daily Profit Loss"> Update Daily Profit Loss</a>
@endif
    <a  href="<?php echo action('ResultController@gettransform') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase mr-2 text-primary float-right"  style="float: right;" data-toggle="tooltip" title="Selection Percent Refresh"><i class="far fa-undo"></i> <span class="pl-1">Selection Percent Refresh</span></a>
@endsection('card-heading-btn')
@section('content')
@include('alert_msg')
<div class="row">
    <div class="col-xl-4 col-md-4 mb-4">
        <!-- Dashboard info widget 1-->
        <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-primary h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="small font-weight-bold text-capitalize text-primary mb-1">Pending pan requests</div>
                        <div class="h5">{{$pan}}</div>
                        <div class="text-xs font-weight-bold d-inline-flex align-items-center">
                            <a href="{{ action('VerificationController@verifypan')}}" class="btn btn-primary text-uppercase btn-xs px-2">OPEN &nbsp; &nbsp; <i class="fad fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="ml-2 fs-50 position-relative">
                        <i class="fad fa-address-card"></i>
                        <i class="fas fa-info-circle position-absolute fs-22 top-8px right-n8px rounded-pill bg-white text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <div class="col-xl-4 col-md-4 mb-4">
        <!-- Dashboard info widget 2-->
        <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-orange h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="small font-weight-bold text-capitalize text-orange mb-1">Not Uploaded pan users</div>
                        <div class="h5">{{$pendingpanuser}}</div>
                        <div class="text-xs font-weight-bold text-danger d-inline-flex align-items-center">
                            <a href="{{ action('RegisteruserController@index','status=pan')}}" class="btn btn-orange text-uppercase btn-xs px-2">approval &nbsp; &nbsp; <i class="fad fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="ml-2 fs-50 position-relative">
                        <i class="fad fa-address-card"></i>
                        <i class="fas fa-question-circle position-absolute fs-22 top-8px right-n8px rounded-pill bg-white text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4 mb-4">
        <!-- Dashboard info widget 3-->
        <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-success h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="small font-weight-bold text-capitalize text-success mb-1">Verified pan requests</div>
                        <div class="h5">{{$panapproved}}</div>
                        <div class="text-xs font-weight-bold text-success d-inline-flex align-items-center">
                            <a href="{{ action('VerificationController@verifypan','status=1')}}" class="btn btn-success text-uppercase btn-xs px-2">OPEN &nbsp; &nbsp; <i class="fad fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="ml-2 fs-50 position-relative">
                        <i class="fad fa-address-card"></i>
                        <i class="fas fa-check-circle position-absolute fs-22 top-8px right-n8px rounded-pill bg-white text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(Auth::user()->role == '1')
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4 p-3">
            <div class="card-header py-2 px-3 bg-light rounded-20 shadow-sm d-flex justify-content-between"><span class="text-secondary">Total amount received</span><span class="text-primary">₹{{ $amt_recevied }}</span></div>
            <div class="card-body px-1 pb-0">
                <div class="chart-area">
                    <canvas id="myAreaChart1" width="100%" height="30"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4 p-3">
            <div class="card-header py-2 px-3 bg-light rounded-20 shadow-sm d-flex justify-content-between"><span class="text-secondary">Total withdrawal amount</span><span class="text-primary">₹{{ $totalwithdraw }}</span></div>
            <div class="card-body px-1 pb-0">
                <div class="chart-bar">
                    <canvas id="myBarChart1" width="100%" height="30"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row dashboard_row_1 mt-4">
    <div class="col-lg-3 col-md-6">
        <div class="card bg-danger text-white mb-4 p-1">
            <div class="row mx-0 rounded overflow-hidden bg-white position-relative">
                <div class="card-body col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <div class="text-danger small font-weight-bold"> Live Matches</div>
                            <div class="text-lg font-weight-bold mt-2 text-black">{{ $totallivematches }}</div>
                        </div>
    
                        <i class="fad fa-dot-circle fs-45 text-danger"></i>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex align-items-center justify-content-between border-0 col-12 p-0">
                    <a class="small text-dark d-flex justify-content-between px-3 pb-3 w-100 text-decoration-none text-uppercase font-weight-bold" href="{{ action('MatchController@allmatches','status=live')}}"><span>View</span><span><i class="fad fa-chevron-right"></i></span></a>
                    <!-- <div class="small text-white"><svg class="svg-inline--fa fa-angle-right fa-w-8" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg=""><path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path></svg><i class="fas fa-angle-right"></i></div> -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card bg-primary text-white mb-4 p-1">
            <div class="row mx-0 rounded overflow-hidden bg-white position-relative">
                <div class="card-body col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <div class="text-primary small font-weight-bold"> Launched matches</div>
                            <div class="text-lg font-weight-bold mt-2 text-black">{{ $launchmatches }}</div>
                        </div>
    
                        <i class="fad fa-rocket-launch fs-45 text-primary"></i>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex align-items-center justify-content-between border-0 col-12 p-0">
                    <a class="small text-dark d-flex justify-content-between px-3 pb-3 w-100 text-decoration-none text-uppercase font-weight-bold" href="{{ action('MatchController@allmatches','status=launched')}}"><span>View</span><span><i class="fad fa-chevron-right"></i></span></a>
                    <!-- <div class="small text-white"><svg class="svg-inline--fa fa-angle-right fa-w-8" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg=""><path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path></svg><i class="fas fa-angle-right"></i></div> -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card bg-indigo text-white mb-4 p-1">
            <div class="row mx-0 rounded overflow-hidden bg-white position-relative">
                <div class="card-body col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <div class="text-indigo small font-weight-bold"> Number of matches</div>
                            <div class="text-lg font-weight-bold mt-2 text-black">{{ $matches }}</div>
                        </div>
    
                        <i class="fad fa-tally fs-45 text-indigo"></i>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex align-items-center justify-content-between border-0 col-12 p-0">
                    <a class="small text-dark d-flex justify-content-between px-3 pb-3 w-100 text-decoration-none text-uppercase font-weight-bold" href="{{ action('MatchController@allmatches')}}"><span>View</span><span><i class="fad fa-chevron-right"></i></span></a>
                    <!-- <div class="small text-white"><svg class="svg-inline--fa fa-angle-right fa-w-8" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg=""><path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path></svg><i class="fas fa-angle-right"></i></div> -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card bg-success text-white mb-4 p-1">
            <div class="row mx-0 rounded overflow-hidden bg-white position-relative">
                <div class="card-body col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <div class="text-success small font-weight-bold"> Completed matches</div>
                            <div class="text-lg font-weight-bold mt-2 text-black">{{ $completedmatches }}</div>
                        </div>
    
                        <i class="fad fa-cricket fs-45 text-success"></i>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex align-items-center justify-content-between border-0 col-12 p-0">
                    <a class="small text-dark d-flex justify-content-between px-3 pb-3 w-100 text-decoration-none text-uppercase font-weight-bold" href="{{ action('MatchController@allmatches','status=complete')}}"><span>View</span><span><i class="fad fa-chevron-right"></i></span></a>
                    <!-- <div class="small text-white"><svg class="svg-inline--fa fa-angle-right fa-w-8" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg=""><path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path></svg><i class="fas fa-angle-right"></i></div> -->
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row dashboard_row_2 mt-4">
    <div class="col-xl-4 col-md-4 mb-4">
        <!-- Dashboard info widget 1-->
        <div class="card h-100 border-0 request-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="small font-weight-bold text-capitalize text-primary mb-1">Pending bank requests</div>
                        <div class="h5">{{$bank}}</div>
                        <div class="text-xs font-weight-bold d-inline-flex align-items-center">
                            <a href="{{ action('VerificationController@verifybankaccount','status=0')}}" class="btn btn-primary text-uppercase btn-xs px-2">OPEN &nbsp; &nbsp; <i class="fad fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="ml-2 fs-50 position-relative">
                        <i class="fad fa-university"></i>
                        <i class="fas fa-info-circle position-absolute fs-22 top-8px right-n8px rounded-pill bg-white text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4 mb-4">
        <!-- Dashboard info widget 2-->
        <div class="card h-100 border-0 pending-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="small font-weight-bold text-capitalize text-orange mb-1">Not uploaded Bank Users</div>
                        <div class="h5">{{$pendingbankuser}}</div>
                        <div class="text-xs font-weight-bold text-danger d-inline-flex align-items-center">
                            <a href="{{ action('RegisteruserController@index','status=bank')}}" class="btn btn-orange text-uppercase btn-xs px-2">approval &nbsp; &nbsp; <i class="fad fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="ml-2 fs-50 position-relative">
                        <i class="fad fa-university"></i>
                        <i class="fas fa-question-circle position-absolute fs-22 top-8px right-n8px rounded-pill bg-white text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4 mb-4">
        <!-- Dashboard info widget 3-->
        <div class="card h-100 border-0 verified-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="small font-weight-bold text-capitalize text-success mb-1">Verified Bank requests</div>
                        <div class="h5">{{$bankapproved}}</div>
                        <div class="text-xs font-weight-bold text-success d-inline-flex align-items-center">
                            <a href="{{ action('VerificationController@verifybankaccount','status=1')}}" class="btn btn-success text-uppercase btn-xs px-2">OPEN &nbsp; &nbsp; <i class="fad fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="ml-2 fs-50 position-relative">
                        <i class="fad fa-university"></i>
                        <i class="fas fa-check-circle position-absolute fs-22 top-8px right-n8px rounded-pill bg-white text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- @if(Auth::user()->role == '1') --}}
<div class="row dashboard_row_2">
    <div class="col-xl-4 col-md-4 mb-4">
        <!-- Dashboard info widget 2-->
        <div class="card h-100 border-0 pending-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="small font-weight-bold text-capitalize text-orange mb-1">Pending withdrawal request</div>
                        <div class="h5">{{ $withdraw }}</div>
                        <div class="text-xs font-weight-bold text-danger d-inline-flex align-items-center">
                            <a href="{{ action('VerificationController@withdraw_amount','status=0')}}" class="btn btn-orange text-uppercase btn-xs px-2">approval &nbsp; &nbsp; <i class="fad fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="ml-2 fs-50 position-relative">
                        <i class="fad fa-sack-dollar"></i>
                        <i class="fas fa-question-circle position-absolute fs-22 top-8px right-n8px rounded-pill bg-white text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4 mb-4">
        <!-- Dashboard info widget 3-->
        <div class="card h-100 border-0 verified-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="small font-weight-bold text-capitalize text-success mb-1">Verified withdrawal request</div>
                        <div class="h5">{{ $withdrawapprove }}</div>
                        <div class="text-xs font-weight-bold text-success d-inline-flex align-items-center">
                            <a href="{{ action('VerificationController@withdraw_amount','status=1')}}" class="btn btn-success text-uppercase btn-xs px-2">OPEN &nbsp; &nbsp; <i class="fad fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="ml-2 fs-50 position-relative">
                        <i class="fad fa-sack-dollar"></i>
                        <i class="fas fa-check-circle position-absolute fs-22 top-8px right-n8px rounded-pill bg-white text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4 mb-4">
        <!-- Dashboard info widget 1-->
        <div class="card h-100 border-0 request-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="small font-weight-bold text-capitalize text-primary mb-1">Total withdrawal amount</div>
                        <div class="h5">{{ $totalwithdraw }}</div>
                        <div class="text-xs font-weight-bold d-inline-flex align-items-center">
                            <a href="{{ action('VerificationController@withdraw_amount')}}?status=1" class="btn btn-primary text-uppercase btn-xs px-2">OPEN &nbsp; &nbsp; <i class="fad fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="ml-2 fs-50 position-relative">
                        <i class="fad fa-sack-dollar text-orange"></i>
                        <i class="fas fa-check-circle position-absolute fs-22 top-8px right-n8px rounded-pill bg-white text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- @endif --}}


<div class="row dashboard_row_1 mt-4">
    <div class="col-lg-4 col-md-6">
        <div class="card bg-primary text-white mb-4 p-1">
            <div class="row mx-0 rounded overflow-hidden bg-white position-relative">
                <div class="card-body col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <div class="text-primary small font-weight-bold">No. of user register</div>
                            <div class="text-lg font-weight-bold mt-2 text-black">{{$users}}</div>
                        </div>
    
                        <i class="fad fa-user-circle fs-45 text-primary"></i>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex align-items-center justify-content-between border-0 col-12 p-0">
                    <a class="small text-dark d-flex justify-content-between px-3 pb-3 w-100 text-decoration-none text-uppercase" href="{{ action('RegisteruserController@index')}}"><span>View</span><span><i class="fad fa-chevron-right"></i></span></a>
                    <!-- <div class="small text-white"><svg class="svg-inline--fa fa-angle-right fa-w-8" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg=""><path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path></svg><i class="fas fa-angle-right"></i></div> -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="card bg-success text-white mb-4 p-1">
            <div class="row mx-0 rounded overflow-hidden bg-white position-relative">
                <div class="card-body col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <div class="text-success small font-weight-bold">No. of active users</div>
                            <div class="text-lg font-weight-bold mt-2 text-black">{{$active_users}}</div>
                        </div>
    
                        <i class="fad fa-users fs-45 text-success"></i>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex align-items-center justify-content-between border-0 col-12 p-0">
                    <a class="small text-dark d-flex justify-content-between px-3 pb-3 w-100 text-decoration-none text-uppercase invisible" href="javascript:void(0);"><span>View</span><span><i class="fad fa-chevron-right"></i></span></a>
                    <!-- <div class="small text-white"><svg class="svg-inline--fa fa-angle-right fa-w-8" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg=""><path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path></svg><i class="fas fa-angle-right"></i></div> -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="card bg-orange text-white mb-4 p-1">
            <div class="row mx-0 rounded overflow-hidden bg-white position-relative">
                <div class="card-body col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <div class="text-orange small font-weight-bold">Pending Winner Declare</div>
                            <div class="text-lg font-weight-bold mt-2 text-black">{{ $pendingwinnerdeclare }}</div>
                        </div>
    
                        <i class="fad fa-trophy fs-45 text-orange"></i>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex align-items-center justify-content-between border-0 col-12 p-0">
                    <a class="small text-dark d-flex justify-content-between px-3 pb-3 w-100 text-decoration-none text-uppercase" href="{{ action('ResultController@match_result')}}"><span>View</span><span><i class="fad fa-chevron-right"></i></span></a>
                    <!-- <div class="small text-white"><svg class="svg-inline--fa fa-angle-right fa-w-8" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg=""><path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path></svg><i class="fas fa-angle-right"></i></div> -->
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6">
        <div class="card bg-indigo text-white mb-4 p-1">
            <div class="row mx-0 rounded overflow-hidden bg-white position-relative">
                <div class="card-body col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <div class="text-indigo small font-weight-bold">Total number of teams</div>
                            <div class="text-lg font-weight-bold mt-2 text-black">{{ $teams }}</div>
                        </div>
    
                        <i class="fad fa-user fs-45 text-indigo"></i>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex align-items-center justify-content-between border-0 col-12 p-0">
                    <a class="small text-dark d-flex justify-content-between px-3 pb-3 w-100 text-decoration-none text-uppercase" href="{{ action('TeamController@view_team')}}"><span>View</span><span><i class="fad fa-chevron-right"></i></span></a>
                    <!-- <div class="small text-white"><svg class="svg-inline--fa fa-angle-right fa-w-8" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg=""><path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path></svg><i class="fas fa-angle-right"></i></div> -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="card bg-blue text-white mb-4 p-1">
            <div class="row mx-0 rounded overflow-hidden bg-white position-relative">
                <div class="card-body col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <div class="text-blue small font-weight-bold">Total number of players</div>
                            <div class="text-lg font-weight-bold mt-2 text-black">{{ $players }}</div>
                        </div>
    
                        <i class="fad fa-users fs-45 text-blue"></i>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex align-items-center justify-content-between border-0 col-12 p-0">
                    <a class="small text-dark d-flex justify-content-between px-3 pb-3 w-100 text-decoration-none text-uppercase" href="{{ action('PlayerController@view_player')}}"><span>View</span><span><i class="fad fa-chevron-right"></i></span></a>
                    <!-- <div class="small text-white"><svg class="svg-inline--fa fa-angle-right fa-w-8" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg=""><path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path></svg><i class="fas fa-angle-right"></i></div> -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="card bg-red text-white mb-4 p-1">
            <div class="row mx-0 rounded overflow-hidden bg-white position-relative">
                <div class="card-body col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <div class="text-red small font-weight-bold">Global Contest</div>
                            <div class="text-lg font-weight-bold mt-2 text-black">{{ $leauges }}</div>
                        </div>
    
                        <i class="fad fa-globe fs-45 text-red"></i>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex align-items-center justify-content-between border-0 col-12 p-0">
                    <a class="small text-dark d-flex justify-content-between px-3 pb-3 w-100 text-decoration-none text-uppercase" href="{{ action('ContestController@global_index')}}"><span>View</span><span><i class="fad fa-chevron-right"></i></span></a>
                    <!-- <div class="small text-white"><svg class="svg-inline--fa fa-angle-right fa-w-8" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg=""><path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path></svg><i class="fas fa-angle-right"></i></div> -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    showGraph();
    showBar();
});


function showGraph()
{
    var post_data = {
        _token: '{{ csrf_token() }}'
    }

    $.post("<?php echo action('DashboardController@total_amount_received_in_week'); ?>",
    post_data,
    function (data)
    {
        var labels = data[0];
        var amounts = data[1];

        var ctx = document.getElementById("myAreaChart1");
        var myLineChart = new Chart(ctx, {
            type: "line",
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Amount Received",
                        lineTension: 0.3,
                        backgroundColor: "rgba(0, 97, 22, 0.05)",
                        borderColor: getComputedStyle(document.documentElement)
    .getPropertyValue('--color-primary'),
                        pointRadius: 3,
                        pointBackgroundColor: getComputedStyle(document.documentElement)
    .getPropertyValue('--color-primary'),
                        pointBorderColor: getComputedStyle(document.documentElement)
    .getPropertyValue('--color-primary'),
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(0, 97, 242, 1)",
                        pointHoverBorderColor: "rgba(0, 97, 242, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: amounts,
                    },
                ],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0,
                    },
                },
                scales: {
                    xAxes: [
                        {
                            time: {
                                unit: "date",
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false,
                            },
                            ticks: {
                                maxTicksLimit: 7,
                            },
                        },
                    ],
                    yAxes: [
                        {
                            ticks: {
                                maxTicksLimit: 5,
                                padding: 10,
                                // Include a dollar sign in the ticks
                                callback: function (value, index, values) {
                                    return "" + number_format(value);
                                },
                            },
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2],
                            },
                        },
                    ],
                },
                legend: {
                    display: false,
                },
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: "#6e707e",
                    titleFontSize: 14,
                    borderColor: "#dddfeb",
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: "index",
                    caretPadding: 10,
                    callbacks: {
                        label: function (tooltipItem, chart) {
                            var datasetLabel =
                                chart.datasets[tooltipItem.datasetIndex].label || "";
                            return (
                                datasetLabel + ":" + number_format(tooltipItem.yLabel)
                            );
                        },
                    },
                },
            },
        });
    });
}
</script>

<script>

function showBar()
{
    var post_data = {
        _token: '{{ csrf_token() }}'
    }

    $.post("<?php echo action('DashboardController@total_amount_withdraw_in_week'); ?>",
    post_data,
    function (data)
    {
        var labels = data[0];
        var amounts = data[1];

        // Bar Chart Example
        var ctx = document.getElementById("myBarChart1");
        var myBarChart = new Chart(ctx, {
            type: "bar",
            data: {
                labels: labels,
                datasets: [{
                    label: "Withdraw Amount",
                    backgroundColor: getComputedStyle(document.documentElement)
    .getPropertyValue('--color-primary'),
                    hoverBackgroundColor: "rgba(0, 97, 242, 0.9)",
                    borderColor: getComputedStyle(document.documentElement)
    .getPropertyValue('--color-primary'),
                    data: amounts,
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    xAxes: [{
                        time: {
                            unit: "month"
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 6
                        },
                        maxBarThickness: 25
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            max: {{$totalwithdraw}},
                            maxTicksLimit: 5,
                            padding: 10,
                            // Include a dollar sign in the ticks
                            callback: function(value, index, values) {
                                return "" + number_format(value);
                            }
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }]
                },
                legend: {
                    display: false
                },
                tooltips: {
                    titleMarginBottom: 10,
                    titleFontColor: "#6e707e",
                    titleFontSize: 14,
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: "#dddfeb",
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var datasetLabel =
                                chart.datasets[tooltipItem.datasetIndex].label || "";
                            return datasetLabel + ": " + number_format(tooltipItem.yLabel);
                        }
                    }
                }
            }
        });
    });
}
</script>
@endsection('content')
