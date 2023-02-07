<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

use Illuminate\Support\Facades\Log;
LOG::info(url()->current());

// Auth::routes();

Route::prefix('my-admin')->group(function () {
    Auth::routes([
        'register' => false, // Register Routes...
        'reset' => false, // Reset Password Routes...
        'verify' => false, // Email Verification Routes...
    ]);
});

Route::get('/home', function () {
    return redirect('/my-admin');
});

Route::get('/', function () {
    return redirect('/my-admin');
});


// Card Game Api
Route::any('/api/refundOpenedContest', 'api\CardApiController@refundOpenedContest');
Route::any('/api/getAllcardContests', 'api\CardApiController@getAllcardContests');
Route::any('/api/createmycardteam', 'api\CardApiController@createmycardteam');
Route::any('/api/cardleauges', 'api\CardApiController@cardleauges');
Route::any('/api/cardleaugesteams', 'api\CardApiController@cardleaugesteams');
Route::any('/api/cardsteamplayers', 'api\CardApiController@cardsteamplayers');
Route::any('/api/cardplayersdata', 'api\CardApiController@cardplayersdata');
Route::any('/api/getcardUsableBalance', 'api\CardApiController@getcardUsableBalance');
Route::any('/api/joincardleauge', 'api\CardApiController@joincardleauge');
Route::any('/api/getjoineduserschallenge', 'api\CardApiController@getjoineduserschallenge');
Route::any('/api/refundcard_amount', 'api\CardApiController@refundcard_amount');
Route::any('/api/updateTossStatus', 'api\CardApiController@updateTossStatus');
Route::any('/api/updatesocketid', 'api\CardApiController@updatesocketid');
Route::any('/api/getsocketuserid', 'api\CardApiController@getsocketuserid');
Route::any('/api/refundcard_amountcontest', 'api\CardApiController@refundcard_amountcontest');
Route::any('/api/finalcardresult', 'api\CardApiController@finalcardresult');
Route::any('/api/getusergamedata', 'api\CardApiController@getusergamedata');
Route::any('/api/getusercarddata', 'api\CardApiController@getusercarddata');
Route::any('/api/cardjoinedmatches', 'api\CardApiController@cardjoinedmatches');
Route::any('/api/recentjoined', 'api\CardApiController@recentjoined');

Route::any('/api/series_leaderboard','api\MatchApiController@series_leaderboard');
// Route::any('/api/series_leaderboard1','api\MatchApiController@series_leaderboard');

Route::any('/api/series_leaderboard_match_wise/{matchkey}','api\MatchApiController@series_leaderboard_match_wise');
Route::any('/getwinamount', 'CronJobController@getWon');
Route::any('/api/popup_notify', 'api\UserApiController@popup_notify');

Route::any('/api/check_cashfree_payout_status', 'api\CashfreeApiController@check_cashfree_payout_status');
Route::any('/setReminderNotification','CronJobController@setReminderNotification');
Route::any('/api/webhook_detail', 'api\UserApiController@webhook_detail');
Route::any('/api/setMatchReminder', 'api\MatchApiController@setMatchReminder');
Route::any('/api/webhook_detail_payu', 'api\UserApiController@webhook_detail_payu');
Route::any('/api/paykun_webhook', 'api\UserApiController@paykun_webhook');
Route::any('/api/paytm_webhook', 'api\UserApiController@paytm_webhook');
Route::any('/api/helpdeskmaiL', 'api\UserApiController@helpdeskmaiL');
Route::any('/api/getPaytmCheckSum', 'api\UserApiController@getPaytmCheckSum');
Route::any('/api/getnews', 'api\UserApiController@getnews');
Route::any('/api/tempregisteruser1', 'api\UserApiController@tempregisteruser1');
Route::any('/api/webhook', 'api\UserApiController@webhook');
Route::any('/updatereport', 'ProfitLossController@updatereport');
Route::any('/dailyreport', 'CronJobController@dailyreport');
Route::any('/api/paytmwebhook', 'api\UserApiController@webhook');
Route::any('/api/getversion', 'api\UserApiController@getversion');
Route::any('/gettransform', 'ResultController@gettransform');
Route::any('/update_results_of_matches', 'ResultController@update_results_of_matches');
Route::any('/updateJoinedusers', 'api\ContestApiController@updateJoinedusers');
Route::any('/showplaying/{matchkey}', 'ResultController@showplaying');
Route::any('/player_point/{matchkey}/{format}', 'ResultController@player_point');
Route::any('/refund_amount', 'ResultController@refund_amount');
Route::any('/PermanentJoincontest', 'PdfController@PermanentJoincontest');
Route::any('/createpdfnew/{id}', 'PdfController@createpdfnew');
Route::any('/getPdfDownload', 'PdfController@getPdfDownload');
Route::any('/youtubernotification', 'CronJobController@youtubernotification');
Route::any('/uploadExcelll', 'CronJobController@uploadExcelll');
Route::any('/changeprice/{cid}', 'CronJobController@changeprice');
Route::any('/changePricecard/{cid}', 'CronJobController@changePricecard');
Route::any('/getallmatchestocompress', 'CronJobController@getallmatchestocompress');
Route::any('/changeduopricecard', 'CronJobController@changeduopricecard');

Route::any('/api/paytmstatus', 'api\UserApiController@paytmstatus');
//API Routes

/* -------- UserApiController ------------ */

Route::any('/api/affiliate_program', 'api\UserApiController@affiliate_program');
Route::any('/api/invoicetransaction', 'api\UserApiController@invoicetransaction');
Route::any('/api/getmainbanner', 'api\UserApiController@getmainbanner');
Route::any('/api/getnews', 'api\UserApiController@getnews');

Route::any('/api/getaffliateleaderboard', 'api\UserApiController@getaffliateleaderboard');
Route::any('/api/tempregisteruser', 'api\UserApiController@tempregisteruser');
Route::any('/api/resendotp', 'api\UserApiController@resendotp');
Route::any('/api/registerusers', 'api\UserApiController@registerusers');
Route::any('/api/loginuser', 'api\UserApiController@loginuser');
Route::any('/api/loginotp', 'api\UserApiController@loginotp');
Route::any('/api/userfulldetails', 'api\UserApiController@userfulldetails');
Route::any('/api/imageUploadUser', 'api\UserApiController@imageUploadUser');
Route::any('/api/editprofile', 'api\UserApiController@editprofile');
Route::any('/api/matchCodeForReset', 'api\UserApiController@matchCodeForReset');
Route::any('/api/resetpassword', 'api\UserApiController@resetpassword');
Route::any('/api/checkforrefer', 'api\UserApiController@checkforrefer');
Route::any('/api/forgotpassword', 'api\UserApiController@forgotpassword');
Route::any('/api/changepassword', 'api\UserApiController@changepassword');
Route::any('/api/registerprocess', 'api\UserApiController@registerprocess');
Route::any('/api/socialauthentication', 'api\UserApiController@socialauthentication');
Route::any('/api/allverify', 'api\UserApiController@allverify');
Route::any('/api/panrequest', 'api\UserApiController@panrequest');
Route::any('/api/getpandetails', 'api\UserApiController@seepandetails');
Route::any('/api/bankrequest', 'api\UserApiController@bankrequest');
Route::any('/api/seebankdetails', 'api\UserApiController@seebankdetails');
Route::any('/api/verifyMobileNumber', 'api\UserApiController@verifyMobileNumber');
Route::any('/api/verifyCode', 'api\UserApiController@verifyCode');
Route::any('/api/verifyEmail', 'api\UserApiController@verifyEmail');
Route::any('/api/requestaddcash', 'api\UserApiController@requestaddcash');
Route::any('/api/addcash1', 'api\UserApiController@addcash1');
Route::any('/api/affiliate_details', 'api\UserApiController@affiliate_details');

Route::any('/api/getnotification', 'api\UserApiController@getnotification');
Route::any('/api/logout', 'api\UserApiController@logoutuser');
Route::any('/api/getallnotifications', 'api\UserApiController@getallnotifications');
Route::any('/api/invitepage', 'api\UserApiController@invitepage');
//transaction
Route::any('/api/mytransactions', 'api\UserApiController@mytransactions');
Route::any('/api/detailsTransactions', 'api\UserApiController@detailsTransactions');
Route::any('/api/mywalletdetails', 'api\UserApiController@mywalletdetails');
Route::any('/api/getbalance', 'api\UserApiController@getbalance');
Route::any('/api/request_withdrow', 'api\UserApiController@request_withdrow');

# payout cashfree for withdraw
Route::any('/api/request_withdrow_payout', 'api\UserApiController@request_withdrow_payoutt');
# payout cashfree for withdraw

# webhook for payout cashfree for withdraw
Route::any('/api/cashfree_payout_webhook','api\UserApiController@cashfree_payout_webhook');

Route::any('/api/mywithdrawlist', 'api\UserApiController@mywithdrawlist');

Route::any('/api/sendlinktouser', 'api\UserApiController@sendlinktouser');

Route::any('/api/paymentstatus', 'api\UserApiController@paymentstatus');
Route::any('/api/getreferuser', 'api\UserApiController@getreferuser');

/* -------- SeriesApiController ------------ */
Route::any('/api/getallseries', 'api\SeriesApiController@getallseries');

/* -------- MatchApiController ------------ */
Route::any('/api/version', 'AddpointController@version');
// match api routes
Route::any('/api/getmatchlist', 'api\MatchApiController@getmatchlist');
Route::any('/api/getmatchlistagain', 'api\MatchApiController@getmatchlistagain');
Route::any('/api/secondinningmatchlist', 'api\MatchApiController@secondinningmatchlist');
Route::any('/api/getmatchdetails', 'api\MatchApiController@getmatchdetails');
Route::any('/api/matchlivedata', 'api\MatchApiController@matchlivedata');
Route::any('/api/matchlivedatas', 'api\MatchApiController@matchlivedatas');
//to get live score
Route::any('/api/livescores', 'api\MatchApiController@livescores');
Route::any('/api/getlivescores', 'api\MatchApiController@getlivescores');

Route::any('/api/fantasyscorecards', 'api\MatchApiController@fantasyscorecards');

Route::any('/api/getjointeamplayers', 'api\MatchApiController@getjointeamplayers');
Route::any('/api/dreamteam', 'api\MatchApiController@dreamteam');

Route::any('/api/teamcompare', 'api\MatchApiController@teamcompare');

// player api routes
Route::any('/api/getallplayers', 'api\MatchApiController@getallplayers');
Route::any('/api/getPlayerInfo', 'api\MatchApiController@getPlayerInfo');
Route::any('/api/allmatchplayers', 'api\MatchApiController@allmatchplayers');

Route::any('/api/getuserjoined', 'api\MatchApiController@getuserjoined');

Route::any('/api/getleaderboard', 'api\MatchApiController@getleaderboard');
Route::any('/api/getleaderboardbyuser', 'api\MatchApiController@getleaderboardbyuser');

Route::any('/api/getleaderboard_challenge', 'api\MatchApiController@getleaderboard_challenge');

//leaderboard
Route::any('/api/getSeriesWeek', 'api\MatchApiController@getSeriesWeek');
Route::any('/api/getSeriesWeeklyData', 'api\MatchApiController@getSeriesWeeklyData');
//team api routes
Route::any('/api/createmyteam', 'api\MatchApiController@createmyteam');
Route::any('/api/getMyTeams', 'api\MatchApiController@getMyTeams');
Route::any('/api/myteam', 'api\MatchApiController@myteam');
Route::any('/api/viewteam', 'api\MatchApiController@viewteam');
Route::any('/api/viewteamweb', 'api\MatchApiController@viewteamweb');

Route::any('api/getallfaq', 'FaqController@getallfaq');

/************ ContestApiController *****************/

Route::any('/api/getAllContests', 'api\ContestApiController@getAllContests');
Route::any('/api/investmentSummary', 'api\ContestApiController@investmentSummary');

Route::any('/api/getContestByCategory', 'api\ContestApiController@getContestByCategory');
Route::any('/api/joinbycode', 'api\ContestApiController@joinbycode');
Route::any('/api/getContests', 'api\ContestApiController@getContests');

Route::any('/api/myteam_joinedcontest', 'api\ContestApiController@myteam_joinedcontest');

// Leauges
Route::any('/api/leaugesdetails', 'api\ContestApiController@leaugesdetails');
Route::any('/api/joinleauge', 'api\ContestApiController@joinleauge');
Route::any('/api/joinduoleauge', 'api\ContestApiController@joinduoleauge');

Route::any('/api/myjoinedleauges', 'api\ContestApiController@myjoinedleauges');
Route::any('/api/switchteams', 'api\ContestApiController@switchteams');
Route::any('/api/singleswitchteams', 'api\ContestApiController@singleswitchteams');
Route::any('/api/getUsableBalance', 'api\ContestApiController@getUsableBalance');
Route::any('/api/joinedmatches', 'api\ContestApiController@joinedmatches');

Route::any('/api/create_private_contest', 'api\ContestApiController@create_private_contest');

Route::any('api/offerdeposits', 'OffersController@offerdeposits');
Route::post('api/checkpromocode', 'OffersController@checkpromocode');
Route::any('api/offerdepositsnew', 'OffersController@offerdepositsnew');

Route::any('/api/joinbycode_without_matchkey', 'api\ContestApiController@joinbycode_without_matchkey');

Route::any('/api/joinbycode2', 'api\ContestApiController@joinbycode2');


//For Duo Api
Route::any('/api/getDuoplayers', 'api\ContestApiController@getDuoplayers');

Route::any('/api/getduoallplayers', 'api\MatchApiController@getduoallplayers');

Route::any('/api/getduoleaderboard', 'api\MatchApiController@getduoleaderboard');
Route::any('/api/getliveduoleaderboard', 'api\MatchApiController@getliveduoleaderboard');


Route::auth();

Route::any('/my-admin/', 'DashboardController@index')->middleware('auth');
Route::any('/my-admin/giveinstantAmt', 'DashboardController@giveinstantAmt')->middleware('auth');

Route::any('/my-admin/total_amount_received_in_week', 'DashboardController@total_amount_received_in_week')->middleware('auth');
Route::any('/my-admin/total_amount_withdraw_in_week', 'DashboardController@total_amount_withdraw_in_week')->middleware('auth');

//admin profile
Route::any('/my-admin/admin_profie', 'HomeController@admin_profie')->middleware('auth');
Route::any('/my-admin/update_profile', 'HomeController@update_profile')->middleware('auth');

Route::group(['prefix' => 'my-admin', 'middleware' => ['auth', 'check-permissions']], function () {


    //CardGame Route

    Route::any('/importcarddatafromapi', 'CardController@importcarddatafromapi');
    Route::any('/importplayerdata/{id}', 'CardController@importplayerdata');
    Route::any('/view_cardseries', 'CardController@index');
    Route::any('/series_carddatatable', 'CardController@series_carddatatable');
    Route::any('/edit_cardseries/{id}', 'CardController@edit');

    Route::any('/view_cardteam', 'CardController@view_team');
    Route::any('/view_cardteam_datatable', 'CardController@view_team_datatable');
    Route::any('/edit_cardteam/{id}', 'CardController@edit_team');

    Route::any('/view_cardplayer', 'CardController@view_player');
    Route::any('/edit_cardplayer/{id}', 'CardController@edit_player');
    Route::any('/get_cardteams', 'CardController@get_cardteams');
    Route::any('/savecardplayerroles', 'CardController@savecardplayerroles');
    Route::any('/view_cardplayer_datatable', 'CardController@view_player_datatable');

    
    Route::any('/view_contestresult', 'CardController@view_contestresult');
    Route::any('/view_contestresult_datatable', 'CardController@view_contestresult_datatable');
    Route::any('/view_challengeresult/{id}', 'CardController@view_challengeresult');


    Route::any('/create_card', 'ContestCardController@create_card');
    Route::any('/card_contests', 'ContestCardController@card_index');
    Route::any('/card_index_datatable', 'ContestCardController@card_index_datatable');
    Route::any('/editcardcontest/{id}', 'ContestCardController@editcardcontest');
    Route::any('/delete_card_contest/{id}', 'ContestCardController@delete_card_contest');
    Route::any('/cardcat_muldelete', 'ContestCardController@cardcat_muldelete');

    Route::any('/give_youtuber_cardbonus', 'YoutuberCardBonusController@give_youtuber_cardbonus')->middleware('auth');

     # card profit & loss report
     Route::any('/carddailyreport', 'CronJobController@carddailyreport');
     Route::any('/cardupdatereport', 'CardProfitLossController@updatereport');
     Route::any('/view_card_profit_loss', 'CardProfitLossController@view_profit_loss')->middleware('auth');
     Route::any('/view_card_profit_loss_dt', 'CardProfitLossController@view_profit_loss_dt')->middleware('auth');
 
     Route::any('/view_card_daily_report', 'CardProfitLossController@view_daily_report')->middleware('auth');
     Route::any('/carddownloaddaywisereport', 'CardProfitLossController@downloaddaywisereport')->middleware('auth');
     Route::any('/view_card_daily_report_dt', 'CardProfitLossController@view_daily_report_dt')->middleware('auth');

    // Sub Admin Manager //
    Route::any('/view_sub_admin', 'SubAdminController@view_sub_admin');
    Route::any('/view_sub_admin_dt', 'SubAdminController@view_sub_admin_dt');
    Route::any('/add_sub_admin', 'SubAdminController@add_sub_admin');
    Route::any('/edit_sub_admin/{id}', 'SubAdminController@edit_sub_admin');
    Route::any('/delete_sub_admin/{id}', 'SubAdminController@delete_sub_admin');
    Route::any('/view_permissions/{id}', 'SubAdminController@view_permissions');

    // Series Manager
    Route::any('/create_series', 'SeriesController@create');
    Route::any('/view_series', 'SeriesController@index');
    Route::any('/series_datatable', 'SeriesController@series_datatable');
    Route::any('/edit_series/{id}', 'SeriesController@edit');
    Route::any('/updateseriesstatus/{id}/{status}', 'SeriesController@updateseriesstatus');
    // Route::any('/seriespricecard', 'SeriesController@seriespricecard');
    // Route::any('/addseriespricecard/{id}', 'SeriesController@addseriespricecard');
    // Route::any('/deleteseriespricecard/{id}', 'SeriesController@deleteseriespricecard');

    Route::any('/series/addpricecard/{id}','SeriesController@addpricecard');
    Route::any('/series/deletematchpricecard/{id}','SeriesController@deletematchpricecard');
    Route::any('/series/addmatchpricecard/{id}','SeriesController@addmatchpricecard ');
    Route::any('/series/deletepricecard/{id}','SeriesController@deletepricecard');
    Route::any('/series/addmatchpricecard/{id}','SeriesController@addmatchpricecard');

    //Match Manager

    Route::any('/updateduoplayer','MatchController@updateduoplayer');
    Route::any('/upcoming_series','MatchController@importseriesdata');
    Route::any('/importteam/{id}', 'MatchController@importteam');
    Route::any('/upcoming_matches', 'MatchController@upcoming_matches');
    Route::any('/unlaunch/{matchkey}', 'MatchController@unlaunch');
    Route::any('/upcoming_matches_datatable', 'MatchController@upcoming_matches_datatable');
    Route::any('/importdatafromapi', 'MatchController@importdatafromapi');
    Route::any('/updatelogo/{id}', 'MatchController@updatelogo');
    Route::any('/importsquad/{matchkey}', 'MatchController@importsquad');
    Route::any('/editmatch/{matchkey}', 'MatchController@editmatch');
    Route::any('/launchmatch/{matchkey}', 'MatchController@launchmatch');
    Route::any('/allmatches', 'MatchController@allmatches');
    Route::any('/allmatches_datatable', 'MatchController@allmatches_datatable');
    Route::any('/importsquad/{matchkey}', 'MatchController@importsquad');
    Route::any('/GetLocalseason', 'MatchController@getLocalseason');
    Route::any('/viewmatchdetails/{matchkey}', 'MatchController@viewmatchdetails');
    Route::any('/updatematchdetails/{matchkey}', 'MatchController@updatematchdetails');
    Route::any('/launch/{matchkey}', 'MatchController@launch');
    Route::any('/deleteplayer/{id}/{matchkey}', 'MatchController@deleteplayer');
    Route::any('/playerroles/{id}', 'MatchController@playerroles');
    Route::any('/match_muldelete', 'MatchController@match_muldelete');
    Route::any('/upcomingmatch_muldelete', 'MatchController@upcomingmatch_muldelete');

    //second inning work //
    Route::any('/secondinninglaunch/{matchkey}', 'MatchController@secondinninglaunch');

    //PlayingController
    Route::any('/updateplaying11', 'PlayingController@updateplaying11');
    Route::any('/match_player1/{matchkey}/{team}', 'PlayingController@match_player1');
    Route::any('/match_player2/{matchkey}/{team}', 'PlayingController@match_player2');
    Route::any('/upp1/{matchkey}/{team}', 'PlayingController@upp1');
    Route::any('/upp2/{matchkey}/{team}', 'PlayingController@upp2');
    Route::any('/launchplaying/{matchkey}', 'PlayingController@launchplaying');

    //Cricketapicontroller
    Route::any('/rules', 'Cricketapicontroller@accessrules');
    Route::any('/getaccesstoken', 'Cricketapicontroller@getaccesstoken');
    Route::any('/recentmatches', 'Cricketapicontroller@recentmatches');
    Route::any('/responseget', 'Cricketapicontroller@responseget');
    Route::any('/getscedulematches', 'Cricketapicontroller@getscedulematches');
    Route::any('/getmatchdetails/match_key', 'Cricketapicontroller@getmatchdetails');
    Route::any('/forfull_data/match_key', 'Cricketapicontroller@forfull_data');
    Route::any('/getplayerinfo/{playerkey}', 'Cricketapicontroller@getplayerinfo');
    Route::any('/recentseasons', 'Cricketapicontroller@recentseasons');
    Route::any('/sesaon/{sesaonkey}', 'Cricketapicontroller@seasonmatches');

    // Team Manager
    Route::any('/view_team', 'TeamController@view_team');
    Route::any('/add_team', 'TeamController@add_team');
    Route::any('/view_team_datatable', 'TeamController@view_team_datatable');
    Route::any('/edit_team/{id}', 'TeamController@edit_team');
    Route::any('/delete_team/{id}', 'TeamController@delete_team');
    Route::any('/team_muldelete', 'TeamController@team_muldelete');

    // Player Manager
    Route::any('/view_player', 'PlayerController@view_player');
    Route::any('/add_player', 'PlayerController@add_player');
    Route::any('/view_player_datatable', 'PlayerController@view_player_datatable');
    Route::any('/edit_player/{id}', 'PlayerController@edit_player');
    Route::any('/saveplayerroles', 'PlayerController@saveplayerroles');
    Route::any('/addplayermanually', 'PlayerController@addplayermanually');
    Route::any('/player_muldelete', 'PlayerController@player_muldelete');

    //Contest Manager

    //Contest category
    Route::any('/view_contest_category', 'ContestController@view_contest_category');
    Route::any('/create_category', 'ContestController@create_category');
    Route::any('/edit_contest_category/{id}', 'ContestController@edit_contest_category');
    Route::any('/delete_customcontest/{id}', 'ContestController@delete_customcontest');
    Route::any('/contestcancel/{id}', 'ContestController@contestcancel');
    Route::any('/makeConfirmed/{id}', 'ContestController@makeConfirmed');
    Route::any('/view_search_contest_category', 'ContestController@view_search_contest_category');

    //global and custom
    Route::any('/global_contests', 'ContestController@global_index');
    Route::any('/global_index_datatable', 'ContestController@global_index_datatable');
    Route::any('/custom_contests', 'ContestController@custom_index');
    Route::any('/create_global', 'ContestController@create_global');
    Route::any('/privatecontest', 'ContestController@privatecontest');
    Route::any('/viewprivatecontest', 'ContestController@viewprivatecontest');
    Route::any('/pricontest', 'ContestController@pricontest');
    Route::any('/submitprivatecontest', 'ContestController@submitprivatecontest');
    Route::any('/create_custom', 'ContestController@create_custom');
    Route::any('/create_custom_contest', 'ContestController@create_custom_contest');
    Route::any('/addpricecard/{id}', 'ContestController@addpricecard');
    Route::any('/deletematchpricecard/{id}', 'ContestController@deletematchpricecard');
    Route::any('/importdata/{id}', 'ContestController@importdata');
    Route::any('/addmatchpricecard/{id}', 'ContestController@addmatchpricecard ');
    Route::any('/deletepricecard/{id}', 'ContestController@deletepricecard');
    Route::any('/addmatchpricecard/{id}', 'ContestController@addmatchpricecard');
    Route::any('/delete_global_contest/{id}', 'ContestController@delete_global_contest');
    Route::any('/delete_contest_category/{id}', 'ContestController@delete_contest_category');
    Route::any('/contestcat_muldelete', 'ContestController@contestcat_muldelete');
    Route::any('/globalcat_muldelete', 'ContestController@globalcat_muldelete');
    Route::any('/editglobalcontest/{id}', 'ContestController@editglobalcontest');
    Route::any('/editcustomcontest/{id}', 'ContestController@editcustomcontest');
    Route::any('/selectglobalcontest/{id}', 'ContestController@selectglobalcontest');
    Route::any('/selectglobcontest_datatable', 'ContestController@selectglobcontest_datatable');
    Route::any('/multiselect_globalcat', 'ContestController@multiselect_globalcat');

    //Contest category
    Route::any('/view_contest_category', 'ContestController@view_contest_category');
    Route::any('/create_category', 'ContestController@create_category');
    Route::any('/edit_contest_category/{id}', 'ContestController@edit_contest_category');
    Route::any('/delete_customcontest/{id}', 'ContestController@delete_customcontest');
    Route::any('/contestcancel/{id}', 'ContestController@contestcancel');

    //User Manager
    Route::any('/view_all_users', 'RegisteruserController@index');
    Route::any('/view_users_datatable', 'RegisteruserController@view_users_datatable');
    Route::any('/view_all_refer/{id}', 'RegisteruserController@allrefer');
    Route::any('/emailverifymanually/{id}', 'RegisteruserController@emailverifymanually');
    Route::any('/view_refer_datatable', 'RegisteruserController@view_refer_datatable');
    Route::any('/viewtransactions/{id}', 'RegisteruserController@viewtransactions');
    Route::any('/viewtransactions_table/{id}', 'RegisteruserController@viewtransactions_table');
    Route::any('/updateuserstatus/{id}/{status}', 'RegisteruserController@updateuserstatus');
    Route::any('/youtuberstatus/{id}', 'RegisteruserController@youtuberstatus');
    Route::any('/users_muldelete', 'RegisteruserController@users_muldelete');
    Route::any('/userswallet', 'RegisteruserController@userswallet');
    Route::any('/userswallet_table', 'RegisteruserController@userswallet_table');
    Route::any('/details', 'RegisteruserController@details');
    Route::any('/downloaduserpdf', 'RegisteruserController@downloaduserpdf');
    Route::any('/getuserdetails/{id}', 'RegisteruserController@getuserdetails');
    Route::any('/edituserdetails/{id}', 'RegisteruserController@edituserdetails');

    Route::any('/update_withdraw', 'RegisteruserController@update_withdraw');

    //Verification manager

    //pan verification
    Route::any('/verifypan_table', 'VerificationController@verifypan_table');
    Route::any('/verifypan', 'VerificationController@verifypan');
    Route::any('/viewpandetails/{id}', 'VerificationController@viewpandetails');
    Route::any('/editpandetails/{id}', 'VerificationController@editpandetails');
    Route::any('/updatepantatus', 'VerificationController@updatepantatus');
    Route::any('/pancard_muldelete', 'VerificationController@pancard_muldelete');

    //bank verification
    Route::any('/verifybankaccount', 'VerificationController@verifybankaccount');
    Route::any('/verifybankaccount_table', 'VerificationController@verifybankaccount_table');
    Route::any('/viewbankdetails/{id}', 'VerificationController@viewbankdetails');
    Route::any('/editbankdetails/{id}', 'VerificationController@editbankdetails');
    Route::any('/updatebanktatus', 'VerificationController@updatebanktatus');
    Route::any('/bank_muldelete', 'VerificationController@bank_muldelete');

    //withdrawal reqest verification
    Route::any('/withdraw_amount', 'VerificationController@withdraw_amount');
    Route::any('/withdrawl_amount_table', 'VerificationController@withdrawl_amount_table');

    Route::any('/paytm_withdraw_amount', 'VerificationController@paytm_withdraw_amount');
    Route::any('/paytm_withdrawl_amount_table', 'VerificationController@paytm_withdrawl_amount_table');

    Route::any('/details/{$id}', 'VerificationController@details');
    Route::any('/approve', 'VerificationController@approve');
    Route::any('/remark', 'VerificationController@remark');
    Route::any('/withdraw_muldelete', 'VerificationController@withdraw_muldelete');
    Route::any('/downloadwithdrawalrequest', 'VerificationController@downloadwithdrawalrequest');
    Route::any('/downloadwithdrawaldata', 'VerificationController@downloadwithdrawaldata');
    Route::any('/downloadallpandetails', 'VerificationController@downloadallpandetails');
    Route::any('/downloadallbankdetails', 'VerificationController@downloadallbankdetails');

    //Result Manager
    Route::any('/match_result', 'ResultController@match_result');
    Route::any('/match_points/{matchkey}', 'ResultController@match_points');
    Route::any('/batting_points/{matchkey}', 'ResultController@batting_points');
    Route::any('/bowling_points/{matchkey}', 'ResultController@bowling_points');
    Route::any('/fielding_points/{matchkey}', 'ResultController@fielding_points');
    Route::any('/team_points/{matchkey}', 'ResultController@team_points');
    Route::any('/match_score/{matchkey}', 'ResultController@match_score');

    Route::any('/select_join_person', 'ResultController@select_join_person');
    Route::any('/updatepoints', 'ResultController@updatepoints');
    Route::any('/match_detail/{id}', 'ResultController@match_detail');
    Route::any('/join_users/{id}', 'ResultController@join_users');
    Route::any('/updatematchfinalstatus/{id}/{status}', 'ResultController@updatematchfinalstatus');
    Route::any('/player_points/{id}/{match}', 'ResultController@player_point');
    Route::any('/updatescores/{id}', 'ResultController@updatescores');
    Route::any('/viewwinners/{matchkey}', 'ResultController@viewwinners');
    Route::any('/downloadallwinnersdata/{matchkey}', 'ResultController@downloadallwinnersdata');

    Route::any('/refund_allamount/{id}', 'ResultController@refund_allamount');
    Route::any('/distribute_winning_amount/{id}', 'ResultController@distribute_winning_amount');
    Route::any('/updatepointss/{id}', 'ReportController@updatepointss');
    Route::any('/getscoresupdates/{matchkey}', 'ResultController@getscoresupdates');
    Route::any('/userpoints/{matchkey}', 'ResultController@userpoints');

    //FAQ Manager
    Route::any('/create_faq', 'FaqController@create_faq');
    Route::any('/view_faq', 'FaqController@index');
    Route::any('/faq_datatable', 'FaqController@faq_datatable');
    Route::any('/edit_faq/{id}', 'FaqController@edit_faq');
    Route::any('/deletefaq/{id}', 'FaqController@deletefaq');

    //Received Fund Manager
    Route::any('/fund_paytm', 'FundController@paytm');
    Route::any('/paytmtable', 'FundController@paytmtable');
    Route::any('/netbanking', 'FundController@netbanking');
    Route::any('/netbankingtable', 'FundController@netbankingtable');
    Route::any('/cashfree', 'FundController@card');
    Route::any('/cardtable', 'FundController@cardtable');
    Route::any('/upi', 'FundController@upi');
    Route::any('/upitable', 'FundController@upitable');
    Route::any('/downloadFundtransaction', 'FundController@downloadFundtransaction');
    // Route::any('/cashFree','FundController@cashFree');
    // Route::any('/cashFreetable','FundController@cashFreetable');

    //Notifications
    Route::any('/pushnotifications', 'NotificationController@pushnotifications');
    Route::any('/smsnotifications', 'NotificationController@smsnotifications');
    Route::any('/emailnotifications', 'NotificationController@emailnotifications');
    Route::any('/getusers', 'NotificationController@getusers');
    Route::any('/import', 'NotificationController@import');

    Route::any('/downloadalluserdetails', 'RegisteruserController@downloadalluserdetails');
    Route::any('/downloadalluserwallet', 'RegisteruserController@downloadalluserwallet');
    Route::any('/downloadalluserstransaction/{id}', 'RegisteruserController@downloadalluserstransaction');
    Route::any('/downloadallplayerdetails', 'PlayerController@downloadallplayerdetails');
    Route::any('/downloadteamdata', 'TeamController@downloadteamdata');

    /*------------------------------Admin Wallet Section----------------------------*/
    Route::any('/wallet-list', 'AdminwalletController@adminwallet');
    Route::any('/adminwallet-list', 'AdminwalletController@wallet_list');
    Route::any('/adminwallet', 'AdminwalletController@giveadminwallet');
    Route::any('/searchadminwallet', 'AdminwalletController@searchadminwallet');
    Route::any('/addmoneyinwallet', 'AdminwalletController@addmoneyinwallet');
    Route::any('/details/{id}', 'AdminwalletController@details');
    Route::any('/downloadalladminwalletlistdetails', 'AdminwalletController@downloadalladminwalletlistdetails');

    /*------------------------------Contest Full Detail Section----------------------------*/
    Route::any('/fulldetail', 'ContestFullDetailController@fulldetail1');
    Route::any('/allcontests/{matchkey}', 'ContestFullDetailController@allcontests');
    Route::any('/allusers/{challengeid}/{matchkey}', 'ContestFullDetailController@allusers');
    Route::any('/allwinners/{challengeid}/{matchkey}', 'ContestFullDetailController@allwinners');
    Route::any('/user_team/{teamid}/{matchkeyid}/{uid}', 'ContestFullDetailController@user_team');
    Route::any('/viewjoinusers_datatable', 'ContestFullDetailController@viewjoinusers_datatable');
    Route::any('/viewjoinwinners_datatable', 'ContestFullDetailController@viewjoinwinners_datatable');
    Route::any('/changeteam/{id}/{matchkeyid}/{Uid}', 'ContestFullDetailController@changeteam');
    Route::any('/changeteam_datatable', 'ContestFullDetailController@changeteam_datatable');
    Route::any('/update_change_team/{teamid}/{matchkeyid}/{Uid}', 'ContestFullDetailController@update_change_team');
    Route::any('/update_change_team2/{teamid}/{matchkeyid}/{Uid}', 'ContestFullDetailController@update_change_team2');
    Route::any('/update_changeteam_datatable', 'ContestFullDetailController@update_changeteam_datatable');

    //offers//
    Route::any('main-admin/addoffers', 'OffersController@addOffer')->middleware('auth');
    Route::any('main-admin/getOffers', 'OffersController@getOffers')->middleware('auth');
    Route::any('main-admin/popularoffers/{id}', 'OffersController@popular')->middleware('auth');
    Route::any('main-admin/editoffers/{id}', 'OffersController@editoffer')->middleware('auth');
    Route::any('main-admin/deleteoffers/{id}', 'OffersController@deleteoffer')->middleware('auth');

    //SideBanner
    Route::any('/sidebanner', 'SidebannerController@sidebanner')->middleware('auth');
    Route::any('/add_sidebanner', 'SidebannerController@add_sidebanner')->middleware('auth');
    Route::any('/edit_sidebanner/{id}', 'SidebannerController@edit_sidebanner')->middleware('auth');
    Route::any('/update_sidebanner/{id}', 'SidebannerController@update_sidebanner')->middleware('auth');
    Route::any('/view_sidebanner', 'SidebannerController@view_sidebanner')->middleware('auth');
    Route::any('/view_sidebanner_table', 'SidebannerController@view_sidebanner_table')->middleware('auth');
    Route::any('/delete_sidebanner/{id}', 'SidebannerController@delete_sidebanner')->middleware('auth');

    //News
    Route::any('/news', 'NewsController@news')->middleware('auth');
    Route::any('/add_news', 'NewsController@add_news')->middleware('auth');
    Route::any('/edit_news/{id}', 'NewsController@edit_news')->middleware('auth');
    Route::any('/update_news/{id}', 'NewsController@update_news')->middleware('auth');
    Route::any('/view_news', 'NewsController@view_news')->middleware('auth');
    Route::any('/view_news_table', 'NewsController@view_news_table')->middleware('auth');
    Route::any('/delete_news/{id}', 'NewsController@delete_news')->middleware('auth');

    //general tabs section
    Route::any('/general', 'GeneralTabsController@index');
    Route::any('/viewrefer', 'GeneralTabsController@viewrefer');
    Route::any('/general_delete/{id}', 'GeneralTabsController@delete');
    Route::any('/deleterefer/{id}', 'GeneralTabsController@deleterefer');

    Route::any('/points', 'AddpointController@pointt')->middleware('auth');
    Route::any('/add_points', 'AddpointController@add_pointt')->middleware('auth');

    //AddcashController
    Route::any('addcash_bonus', 'AddcashController@addcash_bonus');
    Route::any('viewaddcashbonus', 'AddcashController@viewaddcashbonus');
    Route::any('delleteaddcashbonus/{id}', 'AddcashController@delleteaddcashbonus');
    Route::any('editbonus/{id}', 'AddcashController@editbonus');

    //User Controller
    Route::any('/add_admin_teams/{id}', 'AutoTeamController@add_admin_teams')->middleware('auth');
    Route::any('/view_admin_user', 'AutoTeamController@view_admin_user')->middleware('auth');
    Route::any('/admin_user_datatable', 'AutoTeamController@admin_user_datatable')->middleware('auth');

    //Add Joined Users
    Route::any('/addjoinedusers/{id}', 'AutoTeamController@addjoinedusers');

    //Football section

    Route::any('/GetFootballMatch', 'FootballMatchController@GetFootballMatch');
    Route::any('/footballlaunchmatch/{id}', 'FootballMatchController@launchmatch');
    Route::any('/footballlaunch/{id}', 'FootballMatchController@launch');
    Route::any('/GetMatchPlayers/{id}', 'FootballMatchController@GetMatchPlayers');

    //update football secore
    Route::any('/updateFootballScore', 'FootballMatchController@updateFootballScore');

    // Youtuber Manager //
    Route::any('/view_youtuber', 'YoutuberController@view_youtuber')->middleware('auth');
    Route::any('/view_youtuber_dt', 'YoutuberController@view_youtuber_dt')->middleware('auth');
    Route::any('/add_youtuber', 'YoutuberController@add_youtuber')->middleware('auth');
    Route::any('/edit_youtuber/{id}', 'YoutuberController@edit_youtuber')->middleware('auth');
    Route::any('/delete_youtuber/{id}', 'YoutuberController@delete_youtuber')->middleware('auth');

    Route::any('/give_youtuber_bonus', 'YoutuberBonusController@give_youtuber_bonus')->middleware('auth');
    Route::any('/give_user_bonus', 'UserBonusController@give_user_bonus')->middleware('auth');

    Route::any('/check_mail/{email}', function ($email) {

        Mail::to($email)->send(new App\Mail\SendMailable('test'));

    });

    # profit & loss report
    Route::any('/view_profit_loss', 'ProfitLossController@view_profit_loss')->middleware('auth');
    Route::any('/view_profit_loss_dt', 'ProfitLossController@view_profit_loss_dt')->middleware('auth');

    Route::any('/view_daily_report', 'ProfitLossController@view_daily_report')->middleware('auth');
    Route::any('/downloaddaywisereport', 'ProfitLossController@downloaddaywisereport')->middleware('auth');
    Route::any('/view_daily_report_dt', 'ProfitLossController@view_daily_report_dt')->middleware('auth');

    # youtuber profit
    Route::any('/youtuber_bonus', 'ProfitLossController@youtuber_bonus')->middleware('auth');
    Route::any('/youtuber_bonus_dt', 'ProfitLossController@youtuber_bonus_dt')->middleware('auth');

    # youtuber profit detailed
    Route::any('/youtuber_bonus_detail', 'ProfitLossController@youtuber_bonus_detail')->middleware('auth');
    Route::any('/youtuber_bonus_detail_dt', 'ProfitLossController@youtuber_bonus_detail_dt')->middleware('auth');
    # profit & loss report

    // News Manager //
    Route::any('/news', 'NewsController@news')->middleware('auth');
    Route::any('/add_news', 'NewsController@add_news')->middleware('auth');
    Route::any('/edit_news/{id}', 'NewsController@edit_news')->middleware('auth');
    Route::any('/update_news/{id}', 'NewsController@update_news')->middleware('auth');
    Route::any('/view_news', 'NewsController@view_news')->middleware('auth');
    Route::any('/view_news_table', 'NewsController@view_news_table')->middleware('auth');
    Route::any('/delete_news/{id}', 'NewsController@delete_news')->middleware('auth');

    # ui settings
    // Route::get('/', 'HomeController@index');
    Route::any('/ui_settings', 'SettingsController@ui_settings');
    Route::any('/facebook_settings', 'SettingsController@facebook_settings');
    Route::any('/google_settings', 'SettingsController@google_settings');
    Route::any('/alert_settings', 'SettingsController@alert_settings');
    Route::any('/payment_gateway_settings', 'SettingsController@payment_gateway_settings');
    Route::any('/show_credentials_box', 'SettingsController@show_credentials_box');
    Route::any('/reset_admin_theme', 'SettingsController@reset_admin_theme');

    //admin profile
    Route::any('/admin_change_password', 'HomeController@admin_change_password')->middleware('auth');
    Route::any('/change_masterpassword', 'HomeController@change_masterpassword')->middleware('auth');

    # point system dynamic
    Route::any('/point_system', 'PointSystemController@point_system');

    Route::any('/update_point_system', 'PointSystemController@update_point_system');
    # point system dynamic

    //PopupController section
    Route::any('/add_popup', 'PopupController@add_popup');
    Route::any('/popup', 'PopupController@popup');
    Route::any('/view_popup_notification', 'PopupController@view_popup_notification');
    Route::any('/edit_popup_notification/{id}', 'PopupController@edit_popup_notification');
    Route::any('/delete_popup_notification/{id}', 'PopupController@delete_popup_notification');

    /*
    Deduct money from wallet
     */
    Route::any('/deductmoneyinwallet', 'AdminwalletController@deductmoneyinwallet');

     // leaderboard manager
  Route::any('/leaderboard','ContestFullDetailController@leaderboard');
  Route::any('/leaderboard_rank','ContestFullDetailController@leaderboard_rank');
  Route::any('/distribute_winning_amount_series_leaderboard/{id}','ContestFullDetailController@distribute_winning_amount_series_leaderboard');
  Route::any('/leaderboard_winning_rank','ContestFullDetailController@leaderboard_winning_rank');

});

Route::any('/error', function () {
    // DB::table('users')->whereId('46dfhsflhf')->first()->hello;

    abort(404);
});

Route::any('does_save_works_on_db', function () {
    $region = DB::table('region')->first();

    $region->region = 'No can\'t with db';

    $region = (array) $region;

    unset(
        $region['created_at'],
        $region['updated_at']
    );

    dd($region);

    DB::connection('mysql2')->table('region')->where('id', $region->id)->update(
        $region
    );
});
