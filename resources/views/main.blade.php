<!DOCTYPE html>
<html lang="en">

<head>
    <?php
        use App\Helpers\Helpers;
    ?>

    <!-- Page expire error -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content=""/>
    <meta name="author" content=""/>
    <title>{{ Helpers::settings()->project_name ?? '' }}</title>
    <link href="{{ asset('public/css/bootstrap.css') }}" rel="stylesheet" />
    <link href="{{ asset('public/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="{{ asset('public/favicon.png') }}" />
    <link href="{{ asset('public/css/bijarniadream.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('public/css/bootstrap-select.css') }}" rel="stylesheet" />
    <link href="{{ asset('public/css/style.css') }}" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="{{ asset('public/js/jquery.dataTables.min.js') }}"></script>
    <link href="{{ asset('public/css/theme1.css') }}" rel="stylesheet" />
    <link href="{{ asset('public/css/jquery.datetimepicker.css') }}" rel="stylesheet" />
    <link rel='stylesheet' href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css'>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.1/datatables.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.3.6/dist/sweetalert2.all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.3.6/dist/sweetalert2.css">

    <style>

    /*--------------------------------------------------------------
    # preloader_admin
    --------------------------------------------------------------*/
    #preloader_admin {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      z-index: 9999;
      overflow: hidden;
      background: #FAFAFA;
      display: block;
    }

    main {
        position: relative;
    }

    #preloader_admin:before {
      content: "";
      position: fixed;
      top: calc(50% - 30px);
      left: calc(50% - 30px);
      border: 6px solid #106eea;
      border-top-color: #e2eefd;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      -webkit-animation: animate-preloader_admin 1s linear infinite;
      animation: animate-preloader_admin 1s linear infinite;
    }

    @-webkit-keyframes animate-preloader_admin {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
      }
    }

    @keyframes animate-preloader_admin {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
      }
    }
    </style>

    @include('css_color')

</head>

<body class="nav-fixed">

  <div id="preloader_admin">
  </div>

    <?php

        #User subadmin Permissions - date: 27 dec

        $r1 = Route::getCurrentRoute()->getAction();
        $r2 = Route::currentRouteAction();
        $r3 = Route::currentRouteName();

        $r4 = explode('@',$r2);

        $permissions_string = Auth::user()->permissions;
        $permissions_array = explode(',', $permissions_string);

        #end subadmin Permissions work
    ?>

    <nav class="topnav navbar navbar-expand shadow navbar-light bg-white" id="sidenavAccordion">
        <a class="navbar-brand" href="{{ asset('/my-admin')}}">  
   
        @if(empty(Helpers::settings()->project_name_or_logo) or Helpers::settings()->project_name_or_logo == 'logo' or Helpers::settings()->project_name_or_logo == 'both')
        <span>
        <img class="img-fluid h-60px d-none d-sm-inline-block" src="{{ asset('public/logo.png') }}" onerror="this.src='{{ asset('public/logo.png')}}'" />
        <img class="img-fluid h-60px d-inline-block d-sm-none" src="{{ asset('public/logo.png') }}" onerror="this.src='{{ asset('public/logo.png')}}'" />
        </span>
        @endif
        
        @if(empty(Helpers::settings()->project_name_or_logo) or Helpers::settings()->project_name_or_logo == 'project_name' or Helpers::settings()->project_name_or_logo == 'both')   
        <span class="ml-1 d-none d-sm-inline-block text-black">
        {{ Helpers::settings()->project_name ?? '' }}
        </span>
        <span class="ml-1 d-inline-block d-sm-none">
        {{ Helpers::settings()->short_name ?? '' }}
        @endif
        
        </span>

        </a>
        
        <button class="btn btn-sm btn-icon order-1 order-lg-0 mr-lg-2" id="sidebarToggle">
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="18" viewBox="0 0 27.623 18" class="injectable">
                <g transform="translate(-599 -99)">
                    <path d="M624.811,134.423h-24A1.817,1.817,0,0,1,599,132.611h0a1.817,1.817,0,0,1,1.811-1.811h24a1.817,1.817,0,0,1,1.811,1.811h0A1.817,1.817,0,0,1,624.811,134.423Z" transform="translate(0 -24.6)" fill="#134ee6"></path>
                    <path d="M618.019,166.123H600.811A1.817,1.817,0,0,1,599,164.311h0a1.817,1.817,0,0,1,1.811-1.811h17.208a1.817,1.817,0,0,1,1.811,1.811h0A1.817,1.817,0,0,1,618.019,166.123Z" transform="translate(0 -49.123)" fill="#134ee6"></path>
                    <path d="M613.491,102.623H600.811A1.817,1.817,0,0,1,599,100.811h0A1.817,1.817,0,0,1,600.811,99h12.679a1.817,1.817,0,0,1,1.811,1.811h0A1.817,1.817,0,0,1,613.491,102.623Z" fill="#134ee6"></path>
                </g>
            </svg>
        </button>
        <ul class="navbar-nav align-items-center ml-auto">
            <li class="nav-item dropdown no-caret mr-3 dropdown-user">
                <a class="btn btn-sm btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownUserImage" href="javascript:void(0);" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img class="img-fluid" src="{{ asset('public/'.auth()->user()->image)}}" onerror="this.src='{{ asset('public/logo.png')}}'"  /></a>
                <div class="dropdown-menu dropdown-menu-right border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownUserImage">
                    <h6 class="dropdown-header d-flex align-items-center">
                        <img class="dropdown-user-img" src="{{ asset('public/'.auth()->user()->image)}}" onerror="this.src='{{ asset('public/logo.png')}}'" >
                            <div class="dropdown-user-details">
                                <div class="dropdown-user-details-name"><?php echo auth()->user()->name; ?></div>
                                <div class="dropdown-user-details-email"><?php echo auth()->user()->email; ?></div>
                            </div>
                        </h6>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ asset('public/my-admin/admin_profie')}}">
                        <div class="dropdown-item-icon"><i class="fad fa-user"></i></div>
                        Profile
                    </a>
                    <a class="dropdown-item" href="{{ asset('public/my-admin/admin_change_password')}}">
                        <div class="dropdown-item-icon"><i class="fad fa-redo"></i></div>
                        Change Password
                    </a>
                    <a class="dropdown-item" href="{{route('logout')}}"
                        onclick="event.preventDefault();document.getElementById('logout').submit()">
                        <div class="dropdown-item-icon"><i class="fad fa-sign-out-alt"></i></div>
                        Logout
                    </a>
                    <form
                        action="{{route('logout')}}"
                        method="post"
                        id="logout">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sidenav shadow-right sidenav-light">
                <div class="sidenav-menu">

                    <div class="nav accordion" id="accordionSidenav">
                            <div class="sidenav-menu-heading"></div>
                        <a class="nav-link" href="{{ asset('/') }}">
                            <div class="nav-link-icon"><i class="fad fa-home-heart"></i></div> Dashboard
                        </a>

                        <div class="sidenav-menu-heading">Modules</div>

                        <!-- @if( preg_match('/SettingsController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-ui-settings" aria-expanded="false" aria-controls="collapse-ui-settings">
                            <div class="nav-link-icon"><i class="fad fa-layer-group"></i></div>
                            Settings
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-ui-settings" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                
                                @if( in_array("SettingsController@ui_settings" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="{{ action('SettingsController@ui_settings') }}">UI Settings</a>
                                @endif

                                @if( in_array("SettingsController@facebook_settings" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="{{ action('SettingsController@facebook_settings') }}">Facebook Settings</a>
                                @endif

                                @if( in_array("SettingsController@google_settings" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="{{ action('SettingsController@google_settings') }}">Google Settings</a>
                                @endif

                                @if( in_array("SettingsController@alert_settings" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="{{ action('SettingsController@alert_settings') }}">Alert Settings</a>
                                @endif

                                @if( in_array("SettingsController@payment_gateway_settings" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="{{ action('SettingsController@payment_gateway_settings') }}">Payment Gateway Settings</a>
                                @endif
                            </nav>
                        </div>
                        @endif -->

                        @if( preg_match('/SeriesController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-series-manager" aria-expanded="false" aria-controls="collapse-series-manager">
                            <div class="nav-link-icon"><i class="fad fa-layer-plus"></i></div>
                            Series Manager
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-series-manager" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                
                                @if( in_array("SeriesController@index" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')                                        
                                <a class="nav-link" href="<?php echo action('SeriesController@index')?>">View Series</a>
                                @endif
                            </nav>
                        </div>
                        @endif

                        @if( preg_match('/ContestFullDetailController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-leaderboard-s-manager" aria-expanded="false" aria-controls="collapse-leaderboard-s-manager">
                            <div class="nav-link-icon"><i class="fad fa-layer-plus"></i></div>
                            Leaderboard Manager
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-leaderboard-s-manager" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                
                                @if( in_array("ContestFullDetailController@leaderboard" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')                                        
                                <a class="nav-link" href="<?php echo action('ContestFullDetailController@leaderboard')?>">View Leaderboard Series</a>
                                @endif
                            </nav>
                        </div>
                        @endif

                        @if( preg_match('/TeamController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-team-manager" aria-expanded="false" aria-controls="collapse-team-manager">
                            <div class="nav-link-icon"><i class="fad fa-users"></i></div>
                            Team Manager
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-team-manager" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                @if( in_array("TeamController@view_team" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')                                        
                                <a class="nav-link" href="<?php echo action('TeamController@view_team')?>">View All Teams</a>
                                @endif
                            </nav>
                        </div>
                        @endif

                        @if( preg_match('/PlayerController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-player-manager" aria-expanded="false" aria-controls="collapse-player-manager">
                            <div class="nav-link-icon"><i class="fad fa-running"></i></div>
                            Player Manager
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-player-manager" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                @if( in_array("PlayerController@view_player" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('PlayerController@view_player')?>">View All Players</a>
                                @endif
                            </nav>
                        </div>
                        @endif

                        @if( preg_match('/MatchController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-match-manager" aria-expanded="false" aria-controls="collapse-match-manager">
                            <div class="nav-link-icon"><i class="fad fa-gamepad"></i></div>
                           Match Manager
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-match-manager" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                @if( in_array("MatchController@upcoming_matches" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('MatchController@upcoming_matches')?>">All Upcoming Matches</a>
                                @endif
                                @if( in_array("MatchController@allmatches" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')                                        
                                <a class="nav-link" href="<?php echo action('MatchController@allmatches')?>">View All Matches</a>
                                @endif
                                @if( in_array("PlayingController@updateplaying11" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('PlayingController@updateplaying11')?>">Update Playing XI</a>
                                @endif
                            </nav>
                        </div>
                        @endif

                        @if( preg_match('/CardController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-card-manager" aria-expanded="false" aria-controls="collapse-card-manager">
                            <div class="nav-link-icon"><i class="fad fa-gamepad"></i></div>
                           Card Game Manager
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-card-manager" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                @if( in_array("CardController@index" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                    <a class="nav-link" href="<?php echo action('CardController@index')?>">Card Series</a>
                                @endif

                                @if( in_array("CardController@view_team" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                    <a class="nav-link" href="<?php echo action('CardController@view_team')?>">Card Teams</a>
                                @endif

                                @if( in_array("CardController@view_player" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                    <a class="nav-link" href="<?php echo action('CardController@view_player')?>">Card Players</a>
                                @endif

                                @if( in_array("CardController@view_contestresult" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                    <a class="nav-link" href="<?php echo action('CardController@view_contestresult')?>">Card Results</a>
                                @endif
                            </nav>
                        </div>
                        @endif

                        @if( preg_match('/ContestController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-Contest-manager" aria-expanded="false" aria-controls="collapse-Contest-manager">
                            <div class="nav-link-icon"><i class="fad fa-trophy"></i></div>
                           Contest Manager
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-Contest-manager" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                @if( in_array("ContestController@view_contest_category" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('ContestController@view_contest_category')?>">View All Contest Category</a>
                                @endif
                                @if( in_array("ContestController@global_index" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('ContestController@global_index')?>">View All Global Contests</a>
                                @endif
                                @if( in_array("ContestController@create_custom_contest" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')                                        
                                <a class="nav-link" href="<?php echo action('ContestController@create_custom_contest')?>">View All Custom Contests</a>
                                @endif
                                @if( in_array("ContestCardController@card_index" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('ContestCardController@card_index')?>">View All Card Contests</a>
                                @endif
                            </nav>
                        </div>
                        @endif

                        @if( preg_match('/RegisteruserController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-User-manager" aria-expanded="false" aria-controls="collapse-User-manager">
                            <div class="nav-link-icon"><i class="fas fa-users"></i></div>
                           User Manager
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-User-manager" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                @if( in_array("RegisteruserController@index" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('RegisteruserController@index')?>">View All Registered Users</a>
                                @endif
                                @if( in_array("RegisteruserController@userswallet" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('RegisteruserController@userswallet')?>">View Wallet Report</a>
                                @endif
                                @if( in_array("AdminwalletController@adminwallet" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('AdminwalletController@adminwallet')?>">Admin Wallet</a>
                                @endif
                            </nav>
                        </div>
                        @endif

                        @if( preg_match('/VerificationController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-Verification-manager" aria-expanded="false" aria-controls="collapse-Verification-manager">
                            <div class="nav-link-icon"><i class="fad fa-copy"></i></div>
                           Verification Manager
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-Verification-manager" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                @if( in_array("VerificationController@verifypan" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('VerificationController@verifypan')?>">View All Pan card Requests</a>
                                @endif
                                @if( in_array("VerificationController@verifybankaccount" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('VerificationController@verifybankaccount')?>">View all Bank Account Requests</a>
                                @endif
                                @if( in_array("VerificationController@withdraw_amount" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('VerificationController@withdraw_amount')?>">View all Withdrawal Account Requests</a>
                                @endif
                            </nav>
                        </div>
                        @endif

                        @if( preg_match('/ResultController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-result-manager" aria-expanded="false" aria-controls="collapse-result-manager">
                            <div class="nav-link-icon"><i class="fad fa-registered"></i></div>
                           Result Manager
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-result-manager" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                @if( in_array("ResultController@match_result" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('ResultController@match_result')?>">Match Result</a>
                                @endif
                            </nav>
                        </div>
                        @endif

                        @if( preg_match('/ContestFullDetailController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-fullseriesdetail-manager" aria-expanded="false" aria-controls="collapse-fullseriesdetail-manager">
                            <div class="nav-link-icon"><i class="fad fa-sliders-h"></i></div>
                            Full Series Detail
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-fullseriesdetail-manager" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                @if( in_array("ContestFullDetailController@fulldetail1" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('ContestFullDetailController@fulldetail1')?>">Series Detail</a>
                                @endif
                            </nav>
                        </div>
                        @endif

                        

                        @if( preg_match('/NotificationController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-notification-manager" aria-expanded="false" aria-controls="collapse-notification-manager">
                            <div class="nav-link-icon"><i class="fad fa-bell"></i></div>
                            Notifications
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-notification-manager" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                @if( in_array("NotificationController@pushnotifications" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('NotificationController@pushnotifications')?>">Send Android App Notifications</a>
                                @endif
                                @if( in_array("NotificationController@emailnotifications" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('NotificationController@emailnotifications')?>">Send Email Notifications</a>
                                @endif
                                @if( in_array("NotificationController@smsnotifications" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <!-- <a class="nav-link" href="<?php //echo action('NotificationController@smsnotifications')?>">Send SMS Notifications</a> -->
                                @endif
                            </nav>
                        </div>
                        @endif

                        @if( preg_match('/SidebannerController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-banner-manager" aria-expanded="false" aria-controls="collapse-banner-manager">
                            <div class="nav-link-icon"><i class="fad fa-tablet-alt"></i></div>
                            Banner Manager
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-banner-manager" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                @if( in_array("SidebannerController@sidebanner" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('SidebannerController@sidebanner')?>">Add New Banner</a>
                                @endif
                                @if( in_array("SidebannerController@view_sidebanner" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('SidebannerController@view_sidebanner')?>">View All Banners</a>
                                @endif
                            </nav>
                        </div>
                        @endif

                        @if( preg_match('/GeneralTabsController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-general-manager" aria-expanded="false" aria-controls="collapse-general-manager">
                            <div class="nav-link-icon"><i class="fad fa-th"></i></div>
                            General Tabs
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-general-manager" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                @if( in_array("GeneralTabsController@index" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('GeneralTabsController@index')?>">View All General Tabs</a>
                                @endif
                            </nav>
                        </div>
                        @endif
                        
                        @if( preg_match('/AddpointController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-point-manager" aria-expanded="false" aria-controls="collapse-point-manager">
                            <div class="nav-link-icon"><i class="fad fa-circle-notch"></i></div>
                           Point
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-point-manager" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                @if( in_array("AddpointController@pointt" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('AddpointController@pointt')?>">Add Point</a>
                                @endif
                            </nav>
                        </div>
                        @endif

                        @if( preg_match('/OffersController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                            <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-offer-manager" aria-expanded="false" aria-controls="collapse-series-manager">
                                <div class="nav-link-icon"><i class="fad fa-dollar-sign"></i></div>
                                Offer Manager
                                <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapse-offer-manager" data-parent="#accordionSidenav">
                                <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                    @if( in_array("OffersController@addOffer" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                    <a class="nav-link" href="<?php echo action('OffersController@addOffer')?>">Add Offer</a>
                                    @endif
                                    @if( in_array("OffersController@getOffers" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                    <a class="nav-link" href="<?php echo action('OffersController@getOffers')?>">View All Offers</a>
                                    @endif
                                </nav>
                            </div>
                        @endif
                        @if( preg_match('/YoutuberController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-youtube-manager" aria-expanded="false" aria-controls="collapse-youtube-manager">
                            <div class="nav-link-icon"><i class="fab fa-youtube"></i></div>
                            Youtuber Manager
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-youtube-manager" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                @if( in_array("YoutuberController@add_youtuber" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('YoutuberController@add_youtuber')?>">Add Youtuber</a>
                                @endif
                                
                                @if( in_array("YoutuberController@view_youtuber" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('YoutuberController@view_youtuber')?>">View Youtubers</a>
                                @endif
                            </nav>
                        </div>
                        @endif
                        
                        @if( preg_match('/PointSystemController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-pointsystem-manager" aria-expanded="false" aria-controls="collapse-pointsystem-manager">
                            <div class="nav-link-icon"><i class="fad fa-play"></i></div>
                            Point System Manager
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-pointsystem-manager" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                
                                @if( in_array("PointSystemController@point_system" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('PointSystemController@point_system')?>">Update Point System</a>
                                @endif
                                
                            </nav>
                        </div>
                        @endif 

                        @if( preg_match('/SubAdminController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-SubAdmin-manager" aria-expanded="false" aria-controls="collapse-SubAdmin-manager">
                            <div class="nav-link-icon"><i class="fad fa-user"></i></div>
                            Sub Admin Manager
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-SubAdmin-manager" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                
                                @if( in_array("SubAdminController@add_sub_admin" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('SubAdminController@add_sub_admin')?>">Add Sub Admin</a>
                                @endif

                                @if( in_array("SubAdminController@view_sub_admin" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('SubAdminController@view_sub_admin')?>">View Sub Admin</a> 
                                @endif
                            </nav>
                        </div>
                        @endif
                        @if( preg_match('/ProfitLossController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                            <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-profit-manager" aria-expanded="false" aria-controls="collapse-profit-manager">
                                <div class="nav-link-icon"><i class="fas fa-chart-line"></i></div>
                                Cricket Profit Loss Manager
                                <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapse-profit-manager" data-parent="#accordionSidenav">
                                <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                    
                                    @if( in_array("ProfitLossController@view_profit_loss" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                    <a class="nav-link" href="<?php echo action('ProfitLossController@view_profit_loss')?>">View Profit & Loss</a>
                                    @endif

                                    @if( in_array("ProfitLossController@view_daily_report" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                    <a class="nav-link" href="<?php echo action('ProfitLossController@view_daily_report')?>">View Daily Report</a>
                                    @endif
                                </nav>
                            </div>
                        @endif

                        @if( preg_match('/CardProfitLossController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                            <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-cardprofit-manager" aria-expanded="false" aria-controls="collapse-cardprofit-manager">
                                <div class="nav-link-icon"><i class="fas fa-chart-line"></i></div>
                                Card Profit Loss Manager
                                <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapse-cardprofit-manager" data-parent="#accordionSidenav">
                                <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                    
                                    @if( in_array("CardProfitLossController@view_profit_loss" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                    <a class="nav-link" href="<?php echo action('CardProfitLossController@view_profit_loss')?>">View Profit & Loss</a>
                                    @endif

                                    @if( in_array("CardProfitLossController@view_daily_report" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                    <a class="nav-link" href="<?php echo action('CardProfitLossController@view_daily_report')?>">View Daily Report</a>
                                    @endif
                                </nav>
                            </div>
                        @endif
                         
                        @if( preg_match('/PopupController/', $permissions_string) || Auth::user()->role == '1')
                            <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-popup-manager" aria-expanded="false" aria-controls="collapse-popup-manager">
                                <div class="nav-link-icon"><i class="fas fa-play"></i></div>
                                Popup Notification
                                <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapse-popup-manager" data-parent="#accordionSidenav">
                                <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                    @if( in_array("PopupController@popup" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                    <a class="nav-link" href="<?php echo action('PopupController@popup')?>">View Popup Notification</a>
                                    @endif
                                </nav>
                            </div>
                            
                        @endif

                         {{-- @if( preg_match('/NewsController/', $permissions_string) || Auth::user()->role == '1' || $permissions_string == '*')
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapse-banner-manager" aria-expanded="false" aria-controls="collapse-banner-manager">
                            <div class="nav-link-icon"><i class="fad fa-tablet-alt"></i></div>
                            News Manager
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapse-banner-manager" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                                @if( in_array("NewsController@news" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('NewsController@news')?>">Add New News</a>
                                @endif
                                @if( in_array("NewsController@view_news" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')
                                <a class="nav-link" href="<?php echo action('NewsController@view_news')?>">View All News</a>
                                @endif
                            </nav>
                        </div>
                        @endif  --}}

                    </div>

                </div>
                <div class="sidenav-footer">
                    <div class="sidenav-footer-content w-100">
                        
                        <div class="sidenav-footer-subtitle">Logged in as:</div>
                        <div class="sidenav-footer-title">
                            {{
                                auth()->user()->name
                            }}
                        </div>
                    </div>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                
                <div class="page-header pb-10 page-header-dark bg-gradient-primary-to-secondary">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="page-header-content">
                                    <h1 class="page-header-title fs-md-35 fs-20">
                                    
                                        <div class="page-header-icon"><i class="fad fa-at text-white"></i></div>
                                        <span class=" text-capitalize">
                                            @yield('heading')
                                        </span>
                                    </h1>
                                    <div class="page-header-subtitle fs-md-19 fs-14 text-capitalize">
                                        @yield('sub-heading')
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto mb-md-0 mb-3">
                                @yield('card-heading-btn')
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container-fluid mt-n10">
                    @yield('content')
                    
                </div>
            </main>

            
            <footer class="footer mt-auto footer-light">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6 small">Copyright &#xA9; Admin 2020</div>
                        <div class="col-md-6 text-md-right small">
                            <a href="javascript:void(0);">Privacy Policy</a> &#xB7;
                            <a href="javascript:void(0);">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
   
    <script>    

      $(window).on('load', function(){
    
         $('#preloader_admin').hide();
    
        })
    </script>
    
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.1/datatables.min.js"></script>
    <script src="{{ asset('public/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('public/js/scripts.js') }}"></script>
    <script src="{{ asset('public/js/jquery.datetimepicker.full.js') }}"></script>
    <script src="{{ asset('public/js/bootstrap-select.js') }}"></script>
    <script src="{{ asset('public/js/Chart.min.js') }}"></script>
    <script src="{{ asset('public/js/demo/chart-area-demo.js') }}"></script>
    <script src="{{ asset('public/js/demo/chart-bar-demo.js') }}"></script>
    <script src="{{ asset('public/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('public/js/demo/datatables-demo.js') }}"></script>
    <script src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js'></script>
    <script type="text/javascript" src="{{ asset('public/ckeditor/ckeditor.js') }}"></script>

    

           
    @include('other_js_scripts')                 

</body>

</html>
