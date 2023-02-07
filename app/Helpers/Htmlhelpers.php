<?php
namespace App\Helpers;
Use Config;
Use Redirect;
Use Session;
Use Input;
Use HTML;
Use URL;
Use DB;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\RegisteruserController;
use App\Http\Controllers\FeedController;
class Htmlhelpers
{
	public static function logincontent(){
		$html='<div class="login-form" id="loginform">
				<div class="login-left">
					<div class="text-center">
						<a class="loginLogo"><img src="'.URL::asset('public/logo.png').'" alt="optional logo"> 
							<span>MiM Essay</span>
						</a>
						<p>Join a community of passionate experts and thought-leaders who are being sought out by fellow entrepreneurs.</p>
					</div>
					<div class="social-icons">
						<ul>
							<li>
								<p><span class="btn-round">or</span></p>
							</li>
							<li>
								<p>
									<a href="'.action('RegisteruserController@redirectToProvider','facebook').'">
										<span class="fa fa-facebook facebook-before"></span>
										<button class="facebook" id="fbcontent"><span> Login Via Facebook</span></button>
									</a>
								</p>
							</li>
							<li>
								<p>
									<a href="'.action('RegisteruserController@redirectToProvider','twitter').'">
										<span class="fa fa-twitter twitter-before"></span>
										<button class="twitter" id="twittercontent"><span>Login Via Twitter</span></button>
									</a>
								</p>
							</li>
							<li>
								<p>
									<a href="'.action('RegisteruserController@redirectToProvider','google').'">
										<span class="fa fa-google-plus google-before"></span>
										<button class="google" id="googleconetnt"><span>Login Via Google Plus</span></button>
									</a>
								</p>
							</li>
							<li>
								<p>
									<a href="'.action('RegisteruserController@redirectToProvider','linkedin').'">
										<span class="fa fa-linkedin linkedin-before"></span>
										<button class="linkedin" id="linkedincontent"><span>Login Via Linkedin</span></button>
									</a>
								</p>
							</li>
						</ul>
					</div>
				</div>
				<div class="login-right">
					<div class="sap_tabs">
						<div id="horizontalTab" style="display: block; width: 100%; margin: 0px;">
							<ul class="resp-tabs-list">';
								$functionvarlogin = "'login'";
								$functionvarregister = "'register'";
								$html.='<li class="resp-tab-item" aria-controls="tab_item-0" role="tab" onclick="changecontent('.$functionvarlogin.')"><span>Login</span></li><li class="resp-tab-item" aria-controls="tab_item-1" role="tab" onclick="changecontent('.$functionvarregister.')"><span>Sign up</span></li>
							</ul>				  	 
							<div class="resp-tabs-container">
								<div class="tab-1 resp-tab-content pAddT60" aria-labelledby="tab_item-0">
									<div class="login-top">
									<p id="login_error" class="errorshows" style="display:none;">
											</p>
										<form>
											<input type="text" id="loginemail" class="email" placeholder="Email" required=""/>
											<input type="password" id="loginpassword" class="password" placeholder="Password" required=""/>	
										<div class="login-text">
											<ul><li><a href="'.action('RegisteruserController@forgetpassword').'">Forgot password?</a></li>
										</div>
											<div class="submit">
													<input type="button" class="submitinput" value="Login" onclick="login();"/>
											</div>	
										</form>
										<div class="inner_under">
											<section class="alreadymember">
												<p>
													<a aria-controls="tab_item-1" role="tab">Do not have a Mim-essay account yet? Sign Up</a>
												</p>
											</section>
											<section class="terms">
												<p>
													By signing up, you agree to our <a href="#" target="_blank" class="external">Terms of Use</a> and <a href="#" target="_blank" class="external">Privacy Policy</a>, and you confirm that you are 18 years old or over.
												</p>
											</section>
										</div>
									</div>
								</div>
								<div class="tab-1 resp-tab-content" aria-labelledby="tab_item-1">
									<div class="login-top sign-top">
										<form>
											<p id="register_error" class="errorshows" style="display:none;">
											</p>
											<input type="text" id="fusername" class="name active" placeholder="First Name" required=""/>
											<input type="text" id="lusername" class="name" placeholder="Last Name" required=""/>
											<input type="text" id="useremail" class="email" placeholder="Email" required=""/>
											<input type="password" class="password" id="userpassword"  placeholder="Password" required=""/>
											<div class="submit">
												<input type="button" class="submitinput" value="SiGNUP" onclick="regsiteruser();"/>
											</div>
										</form>
										<div class="inner_under">
											<section class="alreadymember">
												<p>
													<a aria-controls="tab_item-0" role="tab">Already a Mim essay member? Log In</a>
												</p>
											</section>
											<section class="terms">
												<p>
													By signing up, you agree to our <a href="#" target="_blank" class="external">Terms of Use</a> and <a href="#" target="_blank" class="external">Privacy Policy</a>, and you confirm that you are 18 years old or over.
												</p>
											</section>
										</div>
									</div>
									</div>
							</div>							
						</div>	
					</div>
				</div>
				<div class="clear"> </div>
			</div>
		</div>
	';
	return $html;
	}
	public static function emailfooter(){
			$html='<table border="0" cellpadding="0" cellspacing="0" style="width:100%;height:auto;margin:auto;background: #fff;">	
			<tr style="background:#E6EBEE;">
				<td>
					<table style="width:100%;margin:auto;">
						<tr>
							<td style="padding:25px;color:#777;text-align:center;background:#E6EBEE;font-size:14px;">
								<p>All rights are reserved &copy; 2017 '.Helpers::projectname().'.</p>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>';
		return $html;
	}
	public static function emailheader(){
		$html='<div style="width:100%;float:left;">
		    	<div style="width:62%;float:left;">
					<div style="width:21%;float:left;">
						<a href="'.action('MainController@home').'" target="_blank">
						<img src="'.URL::asset('public/logo.png').'" style="width:49%;height:auto;padding-top: 10px;"></a>
					</div>
					<div style="width:78%;float:left;padding-top:0%;text-align:left;">
						<h3 style="font-size:1.1em;text-decoration:none;color:#259AE5;font-weight: 600;margin:2% 0px;">'.Helpers::projectname().'</h3>
						<p style="font-size: 11px;margin:1% 0%;">Startup Advice from World Class Experts</p>
					</div>
					</div>
					<div style="width:38%;float:left;text-align:right;padding-top: 3%;">
						<a href="#"><img style="height:22px;" src="'.URL::asset('images/emails/twitter-rc-lg.png').'"></a>
						<a href="#"><img style="height:22px;" src="'.URL::asset('images/emails/facebook-rc-lg.png').'"></a>
						<a href="#"><img style="height:22px;" src="'.URL::asset('images/emails/google-rc-lg.png').'"></a>
					</div>
			</div>';
			return $html;
	}
	public static function profileheader($getusersession){
		$htmlcontent="";
		$ratings = Htmlhelpers::findrating($getusersession->id);
		$exploderating=explode('$$$',$ratings);
		$htmlcontent.='<div class="container-fluid paddingZ">
        <div class="container contaSppw paddingZ">
            <div id="profile-page-header" class="card">
                <div class="card-image waves-effect waves-block waves-light">
                    <img class="activator" src="'.URL::asset('images/user-profile-bg.jpg').'" alt="user background">
                </div>
				<div class="container"  style="padding-bottom:15px;">
				<div class="card-content">
					<div class="row">
						<div class="col-md-1" style="width:auto !important">';
							if($getusersession->image!=""){
								$htmlcontent.='<img src="'.$getusersession->image.'" alt="profile image" class="circle z-depth-2 responsive-img activator" style="width:100px;height:100px">';
								}else{
									$htmlcontent.='<img src="'.URL::asset('images/user-icon.png').'" alt="profile image" class="circle z-depth-2 responsive-img activator" style="width:100px;height:100px">';
								}
						$htmlcontent.='</div>
                        <div class="col-md-4  col-sm-3 col-xs-12">
                            <a href="'.action('RegisteruserController@myaccount').'"><h4 class="card-title profilenameclr">'.ucwords($getusersession->fname).' '. ucwords($getusersession->lname).'</h4></a>';
							if($getusersession->location!=""){
								$htmlcontent.=' <p class="medium-small" style="color: #03A9F4 !important;
								font-size: 12px;"><i class="fa fa-map-marker"></i> '. ucwords($getusersession->location).'</p>';
							}
							if($getusersession->shortbio!=""){
								$htmlcontent.='<p class="medium-small grey-text" style="font-size:12px;margin-top:8px">'.ucwords($getusersession->shortbio).'</p>
								';
							}
							if($getusersession->usertype=="consultant"){
								$htmlcontent.='<p class="medium-small grey-text" style="margin-top:8px">
								<span style="margin-right:15px;"><i class="fa fa-star-o" style="color: #1198DC;font-size: 18px;"></i> '.$exploderating[0].' </span>
								<span style="margin-right:15px;"><i class="fa fa-phone" style="color: #1198DC;font-size: 18px;"></i>  100</span>
								<span style="margin-right:15px;"><i class="fa fa-comment-o" style="color: #1198DC;font-size: 18px;"></i> '.$exploderating[1].' </span>
								</p>';
							}
						$htmlcontent.=' </div>';
					   if($getusersession->usertype=="consultant"){
                       $htmlcontent.=' 
						   <div class="col-md-3 col-sm-4 col-xs-12 pull-right">
						   <h3 class="text-center" style="margin-bottom:0px;"><i class="fa fa-inr"></i> '.$getusersession->hourlyrate.'</h3>
						   <p class="text-center">Per Minute</p>
						  <div class="col-md-12 col-sm-12 col-xs-12 text-center sideListing  paddingZ"><a href="'.action('RegisteruserController@consultantprofile',$getusersession->unique_id).'"><span>View Your Profile</span></a></div>
							';
						   }
						$htmlcontent.='</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
';
return $htmlcontent;
	}
	public static function profilesidebar($getusersession){
		$htmlcontent='<div class="col-md-4 col-xs-12 col-sm-4 paddXsZ">';
				$getactionname = Helpers::actionName();
					if($getactionname!='expert'){
						if($getusersession->usertype=='user'){
							$htmlcontent.='<div class="card light-blue z-depth-1">
							<div class="card-content white-text">
								<span class="card-title">Be an expert!</span>
								<p>There is a world full of business owners and entrepreneurs out there who are seeking your valued advice and feedback. </p>
								<a style="background:#FFFFFF;padding:10px;float:left;margin-bottom:15px;margin-top:10px;cursor:pointer;" href="'.action('RegisteruserController@expert').'"> Apply to be an expert </a>
							</div>
						</div>';
						}else{
							$htmlcontent.='<div class="card light-blue z-depth-1">
							<div class="card-content white-text">
								<span class="card-title"><i class="fa fa-file-o"></i> Add Your Post! </span>
								<p>Add expertise listings to your profile to make it easier for others to find you. Create a listing to be found in search.</p>
								<a style="background:#FFFFFF;padding:10px;float:left;margin-bottom:15px;margin-top:10px;cursor:pointer;" href="'.action('RegisteruserController@addnewpost').'"><i class="fa fa-plus-circle"></i> Create your listing </a>
							</div>
						</div>';
						}
					}
					$htmlcontent.='<ul id="profile-page-about-details" class="collection z-depth-1">
                        <li class="collection-item">
                            <div class="row">
                                <div class="col-md-5 col-sm-5 col-xs-6 grey-text darken-1"><i class="fa fa-user fa-fw"></i> Full Name:- </div>
                                <div class="col-md-7 col-sm-7 col-xs-6 grey-text text-darken-4 right-align">'.ucwords($getusersession->fname).' '.ucwords($getusersession->lname).'</div>
                            </div>
                        </li>';
						if($getusersession->number!=""){
								$htmlcontent.='<li class="collection-item">
									<div class="row">
										<div class="col-md-5 col-sm-5 col-xs-6 grey-text darken-1"><i class="fa fa-mobile fa-fw"></i> Number:- </div>
										<div class="col-md-7 col-sm-7 col-xs-6 grey-text text-darken-4 right-align">+'.$getusersession->countrycode.$getusersession->number.' </div>
									</div>
								</li>';
						}
						if($getusersession->education!=""){
							$htmlcontent.='<li class="collection-item">
									<div class="row">
										<div class="col-md-5 col-sm-5 col-xs-6 grey-text darken-1"><i class="fa fa-graduation-cap fa-fw"></i> Education:- </div>
										<div class="col-md-7 col-sm-7 col-xs-6 grey-text text-darken-4 right-align">'.ucwords($getusersession->education).' </div>
									</div>
								</li>';
						}
						$htmlcontent.='<li class="collection-item">
									<div class="row">
										<div class="col-md-5 col-sm-5 col-xs-6 grey-text darken-1"><i class="fa fa-check-circle fa-fw"></i> Verification:- </div>
										<div class="col-md-7 col-sm-7 col-xs-6 grey-text text-darken-4 right-align">';
										if($getusersession->fbid==""){
										 $htmlcontent.='<i class="fa fa-facebook fa-fw verifyicons"></i>';
										}else{
											$htmlcontent.='<i class="fa fa-facebook fa-fw succverifyiconsfb"></i>';
										}
										if($getusersession->twitterid==""){
										 $htmlcontent.='<i class="fa fa-twitter fa-fw verifyicons"></i>';
										}else{
											$htmlcontent.='<i class="fa fa-twitter fa-fw succverifyiconstwitter"></i>';
										}
										if($getusersession->linkedinid==""){
										 $htmlcontent.='<i class="fa fa-linkedin fa-fw verifyicons"></i>';
										}else{
											$htmlcontent.='<i class="fa fa-linkedin fa-fw succverifyiconslinkedin"></i>';
										}
										if($getusersession->phoneverification=="no"){
										 $htmlcontent.='<i class="fa fa-phone fa-fw verifyicons"></i>';
										}else{
											$htmlcontent.='<i class="fa fa-phone fa-fw succverifyicons"></i>';
										}
										$htmlcontent.='</div>
									</div>
								</li>';
						if($getusersession->miniresume!=""){
								$htmlcontent.='<li class="collection-item">
									<div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12 grey-text darken-1"><i class="fa fa-file-o fa-fw"></i> &nbsp; Mini Resume:- </div>
										<div class="col-md-12 col-sm-12 col-xs-12 grey-text text-darken-4" style="text-aign:justify;margin-top:10px">'.ucwords($getusersession->miniresume).'</div>
									</div>
								</li>';
						}
						
					$htmlcontent.='</ul>';
					$getactionname = Helpers::actionName();
						if($getactionname!='expert'){
							if($getusersession->usertype=='consultant'){
							$htmlcontent.='<div class="card light-blue z-depth-1">
								<div class="card-content white-text">
									<span class="card-title">Too Busy or On Vacation ?</span>
									<p>Convert to Member so you would not appear in search and would not get any new calls. You can revert back any time. </p>
									<a style="background:#FFFFFF;padding:10px;float:left;margin-bottom:15px;margin-top:10px;cursor:pointer;" href="'.action('RegisteruserController@coverttomember').'"> Convert to member </a>
								</div>
							</div>';
						}
						}
				$htmlcontent.='</div>';
				return $htmlcontent;
	}
	public static function expertslisting($allposts,$findtagname=null){
		$htmlcontent="";
		if($findtagname==""){
			$findtagname="";
		}
		$loginid="";
		if(Session::has('mimessay_reg_user')){
			$getloginsession = Session::get('mimessay_reg_user');
			$loginid = $getloginsession->id;
		}
		foreach($allposts as $posts){
		$htmlcontent.='<div class="col-xs-12 col-sm-12 col-md-12 paddingZ answerQuuetion">
						  <div class="row">
							<a style="text-decoration: none!important;">
								<div class="col-xs-12 col-sm-8 col-md-9 listinSecSpeNine">
									<div class="col-xs-12 col-sm-12 col-md-12 paddingZ">
										<div class="expertise_image">';
											if($posts->image!=""){
												$htmlcontent.='<a href="'.action('RegisteruserController@consultantprofile',$posts->unique_id).'"><img src="'.URL::asset('images/s_img_new.php').'?image='.$posts->image.'&width=173&height=173&zc=1" class="img-responsive"></a>';
											}else{
												$htmlcontent.='<a href="'.action('RegisteruserController@consultantprofile',$posts->unique_id).'"><img src="'.URL::asset('images/s_img_new.php').'?image='.URL::asset('images/user-icon.png').'&width=173&height=173&zc=1" class="img-responsive"></a>';
											}
											$htmlcontent.='</div>
											<div class="expertise_content">
												<h3 class="exprtTitle"><a href="'.action('RegisteruserController@consultantprofile',$posts->unique_id).'">'.ucwords($posts->fname).' '. ucwords($posts->lname).'</a></h3>
													<h4 class="exprtTitleHd"><i class="fa fa-map-marker"></i> '.ucwords($posts->location).'</h4>
													<h4>
														<small>';
														if($posts->education!=""){
															$htmlcontent.='<strong>'.ucwords($posts->education).'</strong>';
														}
														$htmlcontent.='<em> • </em>';
														if($posts->expertise!=""){
														$htmlcontent.='<span>'.ucwords($posts->expertise).'</span>';
														}							
														$htmlcontent.='</small>
														';
																										  
														$htmlcontent.='</small>
													</h4>
												<p>'.substr($posts->miniresume,0,150).'</p>';
												$findservices = DB::table('tags')->join('posttags','posttags.tagid','=','tags.id')->join('posts','posts.id','=','posttags.postid')->join('registerusers','registerusers.id','=','posts.userid')->where('registerusers.id',$posts->id)->select('tags.name','tags.id','tags.slug')->groupBy('tags.id')->orderBy('tags.name','ASC')->get();
												if(!empty($findservices)){
													foreach($findservices as $servicetags){
														if($findtagname==$servicetags->name){
															$htmlcontent.='<a class="serviceifferedtags activeserviceioff"> '.ucwords($servicetags->name).' </a>';
														}else{
															$htmlcontent.='<a class="serviceifferedtags"  href="'.action('PostController@searchexperts',$servicetags->slug).'"> '.ucwords($servicetags->name).' </a>';
														}
															
													}
												}
												
												
											$htmlcontent.='</div>
										</div>
									</div>
									<div class="col-xs-12 col-sm-4 col-md-3  sideListing">
										<div class="sideListingqw requestcalsection">
											<div class="col-md-12 col-sm-12 col-xs-12 text-center paddingZ">
												<h4><i class="fa fa-inr"></i> '.$posts->hourlyrate.'</h4>
												<p>Per Minute</p>';
												$ratings = Htmlhelpers::findrating($posts->id);
												$exploderating=explode('$$$',$ratings);
												$htmlcontent.=Htmlhelpers::getRating($exploderating[0]);
											$htmlcontent.='
											<div class="reviewCount">'.' ('.$exploderating[1].')'.'</div>
											</div>';
											if($loginid!=""){
												if($loginid!=$posts->userid){
											}
											$htmlcontent.='<div class="col-md-12 col-sm-12 col-xs-12 text-center paddingZ">';
											$htmlcontent.='<a href="'.action('PostController@requestacall',base64_encode(serialize($posts->userid))).'"><span>Request a Call</span></a>
											';
											}
											$htmlcontent.='</div>
										</div>
									</div>
								</a>
							</div>
						</div>
					';
		}
		return $htmlcontent;
	}
	public static function noresults(){
		$htmlcontent='<h1 class="text-center" style="padding-top:50px;"> Sorry No Result Found</h1>';
		return $htmlcontent;
	}
	public static function postslisting($allposts){
		$htmlcontent="";
		$loginid="";
		if(Session::has('mimessay_reg_user')){
			$getloginsession = Session::get('mimessay_reg_user');
			$loginid = $getloginsession->id;
		}
		foreach($allposts as $posts){
		$htmlcontent.='<div class="col-xs-12 col-sm-12 col-md-12 paddingZ answerQuuetion">
						<div class="row">
							<a href="'.action('PostController@singlepost',$posts->slug).'" style="text-decoration: none!important;">
								<div class="col-xs-12 col-sm-8 col-md-9 listinSecSpeNine">
									<div class="col-xs-12 col-sm-12 col-md-12 paddingZ">
										<div class="expertise_image">';
											if($posts->image!=""){
												$htmlcontent.='<img src="'.URL::asset('images/s_img_new.php').'?image='.URL::asset('uploads/posts/'.$posts->image).'&width=173&height=173&zc=1" class="img-responsive">';
											}else{
												
												$htmlcontent.='<img src="'.URL::asset('images/s_img_new.php').'?image='.URL::asset('images/noimage.png').'&width=173&height=173&zc=1" class="img-responsive">';
											}
											$htmlcontent.='</div>
											<div class="expertise_content">
												<h3>'.ucfirst($posts->title).'</h3>
												<h4>
													<small>
														<strong>'.ucwords($posts->userfname).' '. ucwords($posts->userlname).'</strong>
														<em>•</em>
														<span>'.ucwords($posts->location).'</span>													  
													</small>
												</h4>
												<p>'.substr($posts->description,0,300).'</p>';
												// $findservices = DB::table('servicesoffered')->join('services','services.id','=','servicesoffered.serviceid')->where('userid',$posts->regid)->select('services.name','services.id')->orderBy('name','ASC')->get();
												// if(!empty($findservices)){
													// foreach($findservices as $servicetags){
														// $htmlcontent.='<a class="serviceifferedtags"> '.ucwords($servicetags->name).' </a>';	
													// }
												// }
												$posttags = $posts->tagsid;
												if($posttags!=""){
													$arrayposttags = array_filter(explode('$$',$posttags));
													if(!empty($arrayposttags)){
														foreach($arrayposttags as $arrtag){
															$servicetags = DB::table('tags')->where('id',$arrtag)->select('name','slug')->first();
															$htmlcontent.='<a class="serviceifferedtags" href="'.action('PostController@searchexperts',$servicetags->slug).'"> '.ucwords($servicetags->name).' </a>';	
														}
													}
												}
											$ratings = Htmlhelpers::findrating($posts->userid);
											$exploderating=explode('$$$',$ratings);
											$htmlcontent.='</div>
										</div>
									</div>
									<div class="col-xs-12 col-sm-4 col-md-3  sideListing">
										<div class="sideListingqw requestcalsection">
											<div class="col-md-12 col-sm-12 col-xs-12 text-center paddingZ">
												<h4><i class="fa fa-inr"></i> '.$posts->hourlyrate.'</h4>
												<p>Per Minute</p>';
											$htmlcontent.=Htmlhelpers::getRating($exploderating[0]);
											$htmlcontent.=' ('.$exploderating[1].')</div>';
											if($loginid!=""){
												if($loginid!=$posts->userid){
													$htmlcontent.='<div class="col-md-12 col-sm-12 col-xs-12 text-center paddingZ">';
													$htmlcontent.='<a href="'.action('PostController@requestacall',base64_encode(serialize($posts->id))).'"><span>Request a Call</span></a></div>';
												}
											}else{
												$htmlcontent.='<div class="col-md-12 col-sm-12 col-xs-12 text-center paddingZ">';
												$htmlcontent.='<a href="'.action('PostController@requestacall',base64_encode(serialize($posts->id))).'"><span>Request a Call</span></a></div>';
											}
										$htmlcontent.='
										</div>
									</div>
								</a>
							</div>
						</div>
					';
			}
		return $htmlcontent;
	}
	public static function  bannercontent($contentget=null){
		$html='<div class="container-fluid paddingZ position_ralative_banner">
				<img class="activator" src="'.URL::asset('images/aim-banner.jpg').'" alt="Consultants Background">
				<div class="container position_ralative">
					<div class="col-xs-12 col-md-12 col-sm-12 bannerType paddingZ" style="bottom: 45px;">
						<div class="col-xs-12 col-md-12 col-sm-12">
							<h1 class="pageheadCate text-center"><span>';
							if($contentget!=""){
								$html.=$contentget;
							}else{
								$html.='Log In/Register';
							}
							$html.='</span></h1>
					
						</div>
					</div>
				</div>
			</div>';
		return $html;
	}
	public static function expertinfosidebar($getuserifno){
		$content='<div class="col-md-12 col-sm-12 col-xs-12 text-center sideListing roFialg">
				<div class="proImaage" style="width:100%;margin:10px 0px;">';
					if($getuserifno->image!=""){
						$content.='<img src="'.URL::asset('images/s_img_new.php').'?image='.$getuserifno->image.'&width=80&height=80&zc=1" style="border-radius:50%;">';
					} else {
						$content.='<img class="activator" src="'.URL::asset('images/user-icon.png').'" alt="user background" style="margin-top:0px;border-radius:50%;width:100px;">';
					}
				$content.='</div>
				<div class="proCont" style="text-align:center;">
					<h3><a href="'.action('RegisteruserController@consultantprofile',$getuserifno->unique_id).'">'.ucwords($getuserifno->fname).' '.ucwords($getuserifno->lname).'</a></h3>
					<h4><i class="fa fa-map-marker" style="color:#0085C8;"></i> '.ucwords($getuserifno->location).'</h4>
					<p>'.$getuserifno->miniresume.'</p>
				</div>
				<div class="propara">
					<p></p>
				</div>
			</div>';
			return $content;
	}
	public static function commonmailformat($cont,$name){
		$emailcontent='<html>
						<head>
						<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
						</head>
							<body style="margin:0px;font-family: "Open Sans", sans-serif;">
								<div style="width:600px;margin:auto;">
									<div style="background:url('.URL::asset('images/emails/activate-bg.jpg').');background-size:cover;padding:5%;">
										<div style="background:rgba(255,255,255,0.9);text-align:center;padding: 1% 2% 4%;">';
										$emailcontent.= Htmlhelpers::emailheader();
											$emailcontent.='<div style="width:100%;display:inline-block;border-top: 1px solid #ddd;">
												<div style="width:20%;margin:auto; margin-top:3%;">
													<img src="'.URL::asset('images/emails/done-img.png').'" style="width:100%;height:auto;"></img>
												</div>
												<div style="width:100%;float:left;border-top:1px solid #ddd;">';
												if($name!=""){
													$emailcontent.='<p style="margin: 10px;font-size:20px;color:#333;font-weight:600;">'.$name.'</p>';
												}
													$emailcontent.=$cont;
													$emailcontent.='</div>
												</div>
											</div>
										</div>';
										$emailcontent.= Htmlhelpers::emailfooter();
									$emailcontent.='</div>
								</body>
							</html>';
					return $emailcontent;
	}
	public static function mainmailformat($content,$name=null){
		$emailcontent='
		<div style="width:600px;margin:auto;">
			<div style="background:url('.URL::asset('images/emails/activate-bg.jpg').');background-size:cover;padding:5%;float:left;">
				<div style="background:rgba(255,255,255,0.9);text-align:center;padding: 1% 7% 4%;float:left;">';
						$emailcontent.= Htmlhelpers::emailheader();
						$emailcontent.='<div style="width:100%;float: left;border-top:1px solid #ddd;padding-bottom:2%;">';
						if($name!=""){
							$emailcontent.='<p style="margin: 10px;font-size:20px;color:#333;font-weight:700;margin-top: 20px;">Dear '.ucwords($name).'</p>';
						}
						$emailcontent.=$content;
					$emailcontent.='</div>
			    </div>
			</div>';
			$emailcontent.= Htmlhelpers::emailfooter();
		$emailcontent.='</div>';
		return $emailcontent;
	}
	public static function mailsentFormat($email,$subject,$mailmessage){
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers.= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From:  MIM Essay <imgglobaldev@gmail.com>'. " \r\n";
		// mail($email,$subject,$mailmessage,$headers,"-f imgglobaldev@gmail.com");
	}
	public static function getRating($rate)
	{
		// echo $rate;die;
		if($rate==0)
		{
			$rating='<i class="fa fa-star-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>';
		} elseif(0.5 >= $rate){
			$rating='<i class="fa fa-star-half-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>';
		} elseif(0.5 < $rate && 1.0 >= $rate){
			$rating='<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>';
		} elseif(1.0 < $rate && 1.5 >= $rate){
			$rating='<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star-half-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>';
		} elseif(1.5 < $rate && 2.0 >= $rate){ 
			$rating='<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star" style="color:#E09C19;"></i>		
			<i class="fa fa-star-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>	';		
		} elseif(2.0 < $rate && 2.5 >= $rate){
			$rating='<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star-half-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>	';
		} elseif(2.5 < $rate && 3.0 >= $rate){
			$rating='<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>	';
		} elseif(3.0 < $rate && 3.5 >= $rate){
			$rating='<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star-half-o" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>';	
		} elseif(3.5 < $rate && 4.0 >= $rate){
			$rating='<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star-o" style="color:#E09C19;"></i>';
		} elseif(4.0 < $rate && 4.5 >= $rate){
			$rating='<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star-half-o" style="color:#E09C19;"></i>';
		} elseif(4.5 < $rate && 5.0 >= $rate){
			$rating='<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star" style="color:#E09C19;"></i>
			<i class="fa fa-star" style="color:#E09C19;"></i>';
		} 
		return $rating;
	}
	public static function findrating($id=null){
		if($id==""){
			return 0;
		}
		
		$ratings = DB::table('reviews')->select('rating')->where('consultantid',$id)->get();
		$counts = count($ratings);
		$ratecount = 0;
		if(!empty($ratings)){
			foreach($ratings as $rate){
				$ratecount+=$rate->rating;
			}
		}
		if($ratecount!=0){
			$finalrating = number_format($ratecount/$counts,1);
		}else{
			$finalrating = 0;
		}
		return $finalrating.'$$$'.$counts;
	}
	public static function reviewList($findreviews){
		$htmlcontent="";
		if(!empty($findreviews)){
			foreach($findreviews as $revie){
		$htmlcontent.='<div class="col-md-12 col-xs-12 col-sm-12 paddingZ everySec">
							<div class="col-md-1 col-sm-1 col-xs-4 allSnlyef">';
								if($revie->image!=""){
									$htmlcontent.='<img src="'.$revie->image.'" class="img-responsive" style="width:60px;height:60px;border-radius:50%;">';
								}else{
									$htmlcontent.='<img src="'.URL::asset('images/s_img_new.php').'?image='.URL::asset('images/noimage.png').'&width=60&height=60&zc=1" class="img-responsive">';
								}
								$htmlcontent.='</div>
								<div class="allSnlyCon col-md-11 col-sm-11 col-xs-12">
									<div class="col-md-12 col-xs-12 col-sm-12 paddingZ ">
										<div class="col-md-6 col-xs-6 col-sm-6 paddingZ ">
											<h3 style="font-size: 12px;"><i class="fa fa-clock-o" style="font-size: 12px;"></i> '.date('M d,Y',strtotime($revie->created_at)).'</h3>
										</div>
										<div class="col-md-6 col-xs-6 col-sm-6 paddingZ ">
											<h3 class="text-right">';
												$htmlcontent.=Htmlhelpers::getRating($revie->rating);
											$htmlcontent.='</h3>
										</div>
									</div>
									<div class="col-md-12 col-xs-12 col-sm-12 paddingZ ">
										<p style="color:#333;">'.$revie->review.'</p>
									</div>
									<p>'.ucwords($revie->fname).' '.ucwords($revie->lname).'</p>
								</div>
							</div>';
			}
		}
		return $htmlcontent;
	}
	public static function getUserImage($fname,$lname,$image,$width,$height,$style=null,$class=null){
		if($image!=""){
			$imagecontent='<img src="'.URL::asset('images/s_img_new.php').'?image='.$image.'&width='.$width.'&height='.$height.'&zc=1" style="'.$style.'" class="img-responsive '.$class.'">';
		}else{
			$firstletter = substr($fname,0,1);
			$secondletter = substr($lname,0,1);
			$imagecontent = '<div class="user-profile-image extra-small user-profile-imageinitials" style="'.$style.'">'.ucwords($firstletter).' '.ucwords($secondletter).'</div>';
		}
		return $imagecontent;
	}
	public static function getMsgSection($message){
		$cntent='<div class="col-md-12 col-sm-12" style="margin-top:10px;">
					<div class="col-md-1 col-sm-1 col-xs-4 allSnlyef">';
						$width='35';$height = '35';
						$cntent.=Htmlhelpers::getUserImage($message->r2fname,$message->r2lname,$message->r2image,$width,$height);
						$cntent.='</div>
					<div class="col-md-11 col-sm-11 col-xs-6" style="background:#ddd;padding:7px 15px;border-radius:5px;">'.$message->message.'</div>
				</div>
				<div class="col-md-12 col-sm-12 col-xs-12" style="">
					<p style="pull-right;text-align:right;width:100%;padding:0px 15px;font-size:11px;text-transform:uppercase;">'.date('d M, Y',strtotime($message->created_at)).'  '.date('g:i a',strtotime($message->created_at)).'</p>
				</div>';
		return $cntent;
	}
	public static function getcountriescode($code=null){
		$htmlcontent='
		<select name="countryCode" id="countrycodevalue" style="width:100%;">
			<option data-countryCode="DZ" value="213">Algeria (+213)</option>
			<option data-countryCode="AD" value="376">Andorra (+376)</option>
			<option data-countryCode="AO" value="244">Angola (+244)</option>
			<option data-countryCode="AI" value="1264">Anguilla (+1264)</option>
			<option data-countryCode="AG" value="1268">Antigua &amp; Barbuda (+1268)</option>
			<option data-countryCode="AR" value="54">Argentina (+54)</option>
			<option data-countryCode="AM" value="374">Armenia (+374)</option>
			<option data-countryCode="AW" value="297">Aruba (+297)</option>
			<option data-countryCode="AU" value="61">Australia (+61)</option>
			<option data-countryCode="AT" value="43">Austria (+43)</option>
			<option data-countryCode="AZ" value="994">Azerbaijan (+994)</option>
			<option data-countryCode="BS" value="1242">Bahamas (+1242)</option>
			<option data-countryCode="BH" value="973">Bahrain (+973)</option>
			<option data-countryCode="BD" value="880">Bangladesh (+880)</option>
			<option data-countryCode="BB" value="1246">Barbados (+1246)</option>
			<option data-countryCode="BY" value="375">Belarus (+375)</option>
			<option data-countryCode="BE" value="32">Belgium (+32)</option>
			<option data-countryCode="BZ" value="501">Belize (+501)</option>
			<option data-countryCode="BJ" value="229">Benin (+229)</option>
			<option data-countryCode="BM" value="1441">Bermuda (+1441)</option>
			<option data-countryCode="BT" value="975">Bhutan (+975)</option>
			<option data-countryCode="BO" value="591">Bolivia (+591)</option>
			<option data-countryCode="BA" value="387">Bosnia Herzegovina (+387)</option>
			<option data-countryCode="BW" value="267">Botswana (+267)</option>
			<option data-countryCode="BR" value="55">Brazil (+55)</option>
			<option data-countryCode="BN" value="673">Brunei (+673)</option>
			<option data-countryCode="BG" value="359">Bulgaria (+359)</option>
			<option data-countryCode="BF" value="226">Burkina Faso (+226)</option>
			<option data-countryCode="BI" value="257">Burundi (+257)</option>
			<option data-countryCode="KH" value="855">Cambodia (+855)</option>
			<option data-countryCode="CM" value="237">Cameroon (+237)</option>
			<option data-countryCode="CA" value="1">Canada (+1)</option>
			<option data-countryCode="CV" value="238">Cape Verde Islands (+238)</option>
			<option data-countryCode="KY" value="1345">Cayman Islands (+1345)</option>
			<option data-countryCode="CF" value="236">Central African Republic (+236)</option>
			<option data-countryCode="CL" value="56">Chile (+56)</option>
			<option data-countryCode="CN" value="86">China (+86)</option>
			<option data-countryCode="CO" value="57">Colombia (+57)</option>
			<option data-countryCode="KM" value="269">Comoros (+269)</option>
			<option data-countryCode="CG" value="242">Congo (+242)</option>
			<option data-countryCode="CK" value="682">Cook Islands (+682)</option>
			<option data-countryCode="CR" value="506">Costa Rica (+506)</option>
			<option data-countryCode="HR" value="385">Croatia (+385)</option>
			<option data-countryCode="CU" value="53">Cuba (+53)</option>
			<option data-countryCode="CY" value="90392">Cyprus North (+90392)</option>
			<option data-countryCode="CY" value="357">Cyprus South (+357)</option>
			<option data-countryCode="CZ" value="42">Czech Republic (+42)</option>
			<option data-countryCode="DK" value="45">Denmark (+45)</option>
			<option data-countryCode="DJ" value="253">Djibouti (+253)</option>
			<option data-countryCode="DM" value="1809">Dominica (+1809)</option>
			<option data-countryCode="DO" value="1809">Dominican Republic (+1809)</option>
			<option data-countryCode="EC" value="593">Ecuador (+593)</option>
			<option data-countryCode="EG" value="20">Egypt (+20)</option>
			<option data-countryCode="SV" value="503">El Salvador (+503)</option>
			<option data-countryCode="GQ" value="240">Equatorial Guinea (+240)</option>
			<option data-countryCode="ER" value="291">Eritrea (+291)</option>
			<option data-countryCode="EE" value="372">Estonia (+372)</option>
			<option data-countryCode="ET" value="251">Ethiopia (+251)</option>
			<option data-countryCode="FK" value="500">Falkland Islands (+500)</option>
			<option data-countryCode="FO" value="298">Faroe Islands (+298)</option>
			<option data-countryCode="FJ" value="679">Fiji (+679)</option>
			<option data-countryCode="FI" value="358">Finland (+358)</option>
			<option data-countryCode="FR" value="33">France (+33)</option>
			<option data-countryCode="GF" value="594">French Guiana (+594)</option>
			<option data-countryCode="PF" value="689">French Polynesia (+689)</option>
			<option data-countryCode="GA" value="241">Gabon (+241)</option>
			<option data-countryCode="GM" value="220">Gambia (+220)</option>
			<option data-countryCode="GE" value="7880">Georgia (+7880)</option>
			<option data-countryCode="DE" value="49">Germany (+49)</option>
			<option data-countryCode="GH" value="233">Ghana (+233)</option>
			<option data-countryCode="GI" value="350">Gibraltar (+350)</option>
			<option data-countryCode="GR" value="30">Greece (+30)</option>
			<option data-countryCode="GL" value="299">Greenland (+299)</option>
			<option data-countryCode="GD" value="1473">Grenada (+1473)</option>
			<option data-countryCode="GP" value="590">Guadeloupe (+590)</option>
			<option data-countryCode="GU" value="671">Guam (+671)</option>
			<option data-countryCode="GT" value="502">Guatemala (+502)</option>
			<option data-countryCode="GN" value="224">Guinea (+224)</option>
			<option data-countryCode="GW" value="245">Guinea - Bissau (+245)</option>
			<option data-countryCode="GY" value="592">Guyana (+592)</option>
			<option data-countryCode="HT" value="509">Haiti (+509)</option>
			<option data-countryCode="HN" value="504">Honduras (+504)</option>
			<option data-countryCode="HK" value="852">Hong Kong (+852)</option>
			<option data-countryCode="HU" value="36">Hungary (+36)</option>
			<option data-countryCode="IS" value="354">Iceland (+354)</option>
			<option data-countryCode="IN" value="91" selected="">India (+91)</option>
			<option data-countryCode="ID" value="62">Indonesia (+62)</option>
			<option data-countryCode="IR" value="98">Iran (+98)</option>
			<option data-countryCode="IQ" value="964">Iraq (+964)</option>
			<option data-countryCode="IE" value="353">Ireland (+353)</option>
			<option data-countryCode="IL" value="972">Israel (+972)</option>
			<option data-countryCode="IT" value="39">Italy (+39)</option>
			<option data-countryCode="JM" value="1876">Jamaica (+1876)</option>
			<option data-countryCode="JP" value="81">Japan (+81)</option>
			<option data-countryCode="JO" value="962">Jordan (+962)</option>
			<option data-countryCode="KZ" value="7">Kazakhstan (+7)</option>
			<option data-countryCode="KE" value="254">Kenya (+254)</option>
			<option data-countryCode="KI" value="686">Kiribati (+686)</option>
			<option data-countryCode="KP" value="850">Korea North (+850)</option>
			<option data-countryCode="KR" value="82">Korea South (+82)</option>
			<option data-countryCode="KW" value="965">Kuwait (+965)</option>
			<option data-countryCode="KG" value="996">Kyrgyzstan (+996)</option>
			<option data-countryCode="LA" value="856">Laos (+856)</option>
			<option data-countryCode="LV" value="371">Latvia (+371)</option>
			<option data-countryCode="LB" value="961">Lebanon (+961)</option>
			<option data-countryCode="LS" value="266">Lesotho (+266)</option>
			<option data-countryCode="LR" value="231">Liberia (+231)</option>
			<option data-countryCode="LY" value="218">Libya (+218)</option>
			<option data-countryCode="LI" value="417">Liechtenstein (+417)</option>
			<option data-countryCode="LT" value="370">Lithuania (+370)</option>
			<option data-countryCode="LU" value="352">Luxembourg (+352)</option>
			<option data-countryCode="MO" value="853">Macao (+853)</option>
			<option data-countryCode="MK" value="389">Macedonia (+389)</option>
			<option data-countryCode="MG" value="261">Madagascar (+261)</option>
			<option data-countryCode="MW" value="265">Malawi (+265)</option>
			<option data-countryCode="MY" value="60">Malaysia (+60)</option>
			<option data-countryCode="MV" value="960">Maldives (+960)</option>
			<option data-countryCode="ML" value="223">Mali (+223)</option>
			<option data-countryCode="MT" value="356">Malta (+356)</option>
			<option data-countryCode="MH" value="692">Marshall Islands (+692)</option>
			<option data-countryCode="MQ" value="596">Martinique (+596)</option>
			<option data-countryCode="MR" value="222">Mauritania (+222)</option>
			<option data-countryCode="YT" value="269">Mayotte (+269)</option>
			<option data-countryCode="MX" value="52">Mexico (+52)</option>
			<option data-countryCode="FM" value="691">Micronesia (+691)</option>
			<option data-countryCode="MD" value="373">Moldova (+373)</option>
			<option data-countryCode="MC" value="377">Monaco (+377)</option>
			<option data-countryCode="MN" value="976">Mongolia (+976)</option>
			<option data-countryCode="MS" value="1664">Montserrat (+1664)</option>
			<option data-countryCode="MA" value="212">Morocco (+212)</option>
			<option data-countryCode="MZ" value="258">Mozambique (+258)</option>
			<option data-countryCode="MN" value="95">Myanmar (+95)</option>
			<option data-countryCode="NA" value="264">Namibia (+264)</option>
			<option data-countryCode="NR" value="674">Nauru (+674)</option>
			<option data-countryCode="NP" value="977">Nepal (+977)</option>
			<option data-countryCode="NL" value="31">Netherlands (+31)</option>
			<option data-countryCode="NC" value="687">New Caledonia (+687)</option>
			<option data-countryCode="NZ" value="64">New Zealand (+64)</option>
			<option data-countryCode="NI" value="505">Nicaragua (+505)</option>
			<option data-countryCode="NE" value="227">Niger (+227)</option>
			<option data-countryCode="NG" value="234">Nigeria (+234)</option>
			<option data-countryCode="NU" value="683">Niue (+683)</option>
			<option data-countryCode="NF" value="672">Norfolk Islands (+672)</option>
			<option data-countryCode="NP" value="670">Northern Marianas (+670)</option>
			<option data-countryCode="NO" value="47">Norway (+47)</option>
			<option data-countryCode="OM" value="968">Oman (+968)</option>
			<option data-countryCode="PW" value="680">Palau (+680)</option>
			<option data-countryCode="PA" value="507">Panama (+507)</option>
			<option data-countryCode="PG" value="675">Papua New Guinea (+675)</option>
			<option data-countryCode="PY" value="595">Paraguay (+595)</option>
			<option data-countryCode="PE" value="51">Peru (+51)</option>
			<option data-countryCode="PH" value="63">Philippines (+63)</option>
			<option data-countryCode="PL" value="48">Poland (+48)</option>
			<option data-countryCode="PT" value="351">Portugal (+351)</option>
			<option data-countryCode="PR" value="1787">Puerto Rico (+1787)</option>
			<option data-countryCode="QA" value="974">Qatar (+974)</option>
			<option data-countryCode="RE" value="262">Reunion (+262)</option>
			<option data-countryCode="RO" value="40">Romania (+40)</option>
			<option data-countryCode="RU" value="7">Russia (+7)</option>
			<option data-countryCode="RW" value="250">Rwanda (+250)</option>
			<option data-countryCode="SM" value="378">San Marino (+378)</option>
			<option data-countryCode="ST" value="239">Sao Tome &amp; Principe (+239)</option>
			<option data-countryCode="SA" value="966">Saudi Arabia (+966)</option>
			<option data-countryCode="SN" value="221">Senegal (+221)</option>
			<option data-countryCode="CS" value="381">Serbia (+381)</option>
			<option data-countryCode="SC" value="248">Seychelles (+248)</option>
			<option data-countryCode="SL" value="232">Sierra Leone (+232)</option>
			<option data-countryCode="SG" value="65">Singapore (+65)</option>
			<option data-countryCode="SK" value="421">Slovak Republic (+421)</option>
			<option data-countryCode="SI" value="386">Slovenia (+386)</option>
			<option data-countryCode="SB" value="677">Solomon Islands (+677)</option>
			<option data-countryCode="SO" value="252">Somalia (+252)</option>
			<option data-countryCode="ZA" value="27">South Africa (+27)</option>
			<option data-countryCode="ES" value="34">Spain (+34)</option>
			<option data-countryCode="LK" value="94">Sri Lanka (+94)</option>
			<option data-countryCode="SH" value="290">St. Helena (+290)</option>
			<option data-countryCode="KN" value="1869">St. Kitts (+1869)</option>
			<option data-countryCode="SC" value="1758">St. Lucia (+1758)</option>
			<option data-countryCode="SD" value="249">Sudan (+249)</option>
			<option data-countryCode="SR" value="597">Suriname (+597)</option>
			<option data-countryCode="SZ" value="268">Swaziland (+268)</option>
			<option data-countryCode="SE" value="46">Sweden (+46)</option>
			<option data-countryCode="CH" value="41">Switzerland (+41)</option>
			<option data-countryCode="SI" value="963">Syria (+963)</option>
			<option data-countryCode="TW" value="886">Taiwan (+886)</option>
			<option data-countryCode="TJ" value="7">Tajikstan (+7)</option>
			<option data-countryCode="TH" value="66">Thailand (+66)</option>
			<option data-countryCode="TG" value="228">Togo (+228)</option>
			<option data-countryCode="TO" value="676">Tonga (+676)</option>
			<option data-countryCode="TT" value="1868">Trinidad &amp; Tobago (+1868)</option>
			<option data-countryCode="TN" value="216">Tunisia (+216)</option>
			<option data-countryCode="TR" value="90">Turkey (+90)</option>
			<option data-countryCode="TM" value="7">Turkmenistan (+7)</option>
			<option data-countryCode="TM" value="993">Turkmenistan (+993)</option>
			<option data-countryCode="TC" value="1649">Turks &amp; Caicos Islands (+1649)</option>
			<option data-countryCode="TV" value="688">Tuvalu (+688)</option>
			<option data-countryCode="UG" value="256">Uganda (+256)</option>
			<option data-countryCode="GB" value="44">UK (+44)</option>
			<option data-countryCode="UA" value="380">Ukraine (+380)</option>
			<option data-countryCode="AE" value="971">United Arab Emirates (+971)</option>
			<option data-countryCode="UY" value="598">Uruguay (+598)</option>
			<option data-countryCode="US" value="1">USA (+1)</option>
			<option data-countryCode="UZ" value="7">Uzbekistan (+7)</option>
			<option data-countryCode="VU" value="678">Vanuatu (+678)</option>
			<option data-countryCode="VA" value="379">Vatican City (+379)</option>
			<option data-countryCode="VE" value="58">Venezuela (+58)</option>
			<option data-countryCode="VN" value="84">Vietnam (+84)</option>
			<option data-countryCode="VG" value="84">Virgin Islands - British (+1284)</option>
			<option data-countryCode="VI" value="84">Virgin Islands - US (+1340)</option>
			<option data-countryCode="WF" value="681">Wallis &amp; Futuna (+681)</option>
			<option data-countryCode="YE" value="969">Yemen (North)(+969)</option>
			<option data-countryCode="YE" value="967">Yemen (South)(+967)</option>
			<option data-countryCode="ZM" value="260">Zambia (+260)</option>
			<option data-countryCode="ZW" value="263">Zimbabwe (+263)</option>
		</select>';
		return $htmlcontent;
	}
}
?>