<?php

namespace App\Helpers;

use Config;
use Redirect;
use Session;
use Input;
use HTML;
use URL;
use DB;
use Firebase;
use Push;
use Mail;
use App\Mail\SendMailable;
use Response;
use Image;
use Swift_SmtpTransport;
use Swift_Mailer;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\BlockController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\RegisteruserController;
use App\Http\Controllers\FeedController;
use Twilio\Rest\Client;
use Illuminate\Http\Request;

include(app_path() . '/sendnotification/firebase.php');
include(app_path() . '/sendnotification/push.php');
class Helpers
{
	public static function mailSmtpSend($datamessage)
	{
		// echo "<pre>";print_r($datamessage);die;
		Mail::to($datamessage['email'])->send(new SendMailable($datamessage['content'], $datamessage['subject']));
	}
	public static function projectName()
	{
		return Helpers::settings()->project_name ?? '';
	}
	//search by value
	public static function searchByValue($products, $field, $value)
	{
		foreach ($products as $key => $product) {
			if ($product[$field] === $value)
				return $key;
		}
		return false;
	}
	public static function actionName()
	{
		$routeArray = app('request')->route()->getAction();
		$controllerAction = class_basename($routeArray['controller']);
		list($controller, $action) = explode('@', $controllerAction);
		return $action;
	}

	// public static function sendTextSmsNew($txtmsg,$mobile){
	// 	$mobileNumber = str_replace('$$',',',$mobile);


	// 	// $mobile=str_replace('$$',',',$mobile);
	// 	  //       $txtmsg=rawurlencode($txtmsg);
	// 	  //       $url="http://sms.bulksmsserviceproviders.com/api/send_http.php?authkey=7ba51931f6156e34325316f07bf951ca&mobiles=$mobile&message=$txtmsg&sender=IMGERP&route=B";
	// 	  //       // echo "<pre>";print_r($url);die;
	// 	  //       $ch = curl_init();
	// 	  //       curl_setopt($ch, CURLOPT_URL, $url);
	// 	  //       curl_setopt($ch, CURLOPT_HEADER, 0);
	// 	  //       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// 	  //       curl_exec($ch);
	// 	  //       curl_close($ch);
	// 	// Authorisation details.
	// 		$username = "Believer11@gmail.com";
	// 		$hash = "e45c9e172d45252334d1eba3bad36b19967f2ec4f6d65a032f63259dcfe03dab";

	// 		// Config variables. Consult http://api.textlocal.in/docs for more info.
	// 		// $test = "0";

	// 		// $numbers = implode(',', $mobileNumber);
	// 		// Data for text message. This is the text message data.
	// 		$sender = "GLSPOT"; // This is who the message appears to be from.
	// 		$numbers = "91"+$mobileNumber; // A single number or a comma-seperated list of numbers
	// 		// $message = rawurlencode($txtmsg);
	// 		// 612 chars or less
	// 		// A single number or a comma-seperated list of numbers
	// 		$message = urlencode($txtmsg);
	// 		// $message = $txtmsg;
	// 		// dump($message);
	// 		$data = "username=".$username."&hash=".$hash."&message=".$message."&sender=".$sender."&numbers=".$numbers;
	// 		dd($message);
	// 		$ch = curl_init('http://api.textlocal.in/send/?');
	// 		curl_setopt($ch, CURLOPT_POST, true);
	// 		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	// 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// 		$result = curl_exec($ch); // This is the result from the API
	// 		dd($result);
	// 		curl_close($ch);
	// }

	public static function sendTextSmsNew($txtmsg, $mobile)
	{
		$mobileNumber = str_replace('$$', ',', $mobile);


		$mobile = str_replace('$$', ',', $mobile);
		$txtmsg = rawurlencode($txtmsg);
		// $url = "http://sms.bulksmsserviceproviders.com/api/send_http.php?authkey=372fe6a668dba5541dab0c63b21c48fe&mobiles=$mobile&message=$txtmsg&sender=Bliver&route=B";
		// echo "<pre>";print_r($url);die;
		$url="http://sms.bulksmsserviceproviders.com/api/send_http.php?authkey=718d1067e29c468eb61abf38d1f402cd&mobiles=$mobile&message=$txtmsg&sender=FANBOX&route=B";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_exec($ch);
		curl_close($ch);
	}


	/* get url function to get the main url */
	public static function geturl()
	{
		return asset('');
	}

	/* get the access rules for header */
	public static function accessrules()
	{
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Credentials: true");
		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
		header('Access-Control-Max-Age: 1000');
		header('Access-Control-Allow-Headers: Authorization');
		header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
	}

	/* to set the timezone  */
	public static function timezone()
	{
		date_default_timezone_set('Asia/Kolkata');
	}
	/* to get the status code of the api */
	public static function _getStatusCodeMessage($status)
	{
		$code = [
			200 => 'OK',
			400 => 'Bad Request',
			401 => 'Unauthorized Request',
			403 => 'Forbidden',
			404 => 'Not Found',
			500 => 'Internal Server Error',
			501 => 'Not Implemented'
		];
		return (isset($code[$status])) ? $code[$status] : "";
	}
	/* to set the header of the api for particular status */
	public static function setHeader($status)
	{
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Access-Control-Allow-Origin, Authorization, Pragma, Expires, Cache-Control');
		header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
		header('Access-Control-Allow-Credentials: true');
		$status_header = 'HTTP/1.1 ' . $status . ' ' . Helpers::_getStatusCodeMessage($status);
		header($status_header);
	}
	/* that function is used to check the authentication */
	public static function isAuthorize($request)
	{

		if ($request->header('authorization')) {
			$auth_key = $request->header('authorization');
			if (isset($auth_key) && $auth_key != "") {
				$dataa = explode(" ", $auth_key);
				if (isset($dataa[1])) {
					$main_key = $dataa[1];
				} else {
					$main_key = $auth_key;
				}
				$model = DB::table('registerusers')->where('auth_key', $main_key)->first();
				if ($model) {
					return $model;
				} else {
					Helpers::setHeader(401);
					$json['success'] = false;
					$json['message'] = 'You cannot access this page';
					echo json_encode($json, 401);
					die;
				}
			} else {
				Helpers::setHeader(401);
				$json['success'] = false;
				$json['message'] = 'You cannot access this page';
				echo json_encode($json, 401);
				die;
			}
		} else {
			Helpers::setHeader(401);
			$json['success'] = false;
			$json['message'] = 'You cannot access this page';
			echo json_encode($json, 401);
			die;
		}
	}
	public static function imageSingleUpload($file, $destinationPath, $fileName)
	{
		$filename = $file->getClientOriginalName();
		$extension = $file->getClientOriginalExtension();
		$ext = array("jpg", "jpeg", "png", "gif", "bmp", "JPG");

		if (!in_array($extension, $ext)) {
			return false;
		}
		$newfilename = $filename;
		if (file_exists($destinationPath . '/' . $newfilename)) {
			$info = pathinfo($newfilename);
			$imageNamee = $info['filename'] . '.' . $fileName;
			$newfilename = $imageNamee . '.' . $extension;
		}
		$resi = $destinationPath . '/' . $newfilename;
		$upload_success = $file->move($destinationPath, $newfilename);
		$wid = 500;
		$resizeimage = Helpers::resize_image($resi, $wid);
		$resizeimage = Helpers::compress_image($resi, 100);

		return $newfilename;
	}

	public static function imageUpload($file, $destinationPath, $fileName)
	{
		$array = array();
		foreach ($file as $fileimage) {
			$filename = $fileimage->getClientOriginalName();
			$extension = $fileimage->getClientOriginalExtension();
			$ext = array('jpg', 'JPG', 'jpeg', 'gif', 'png');
			if (!in_array($extension, $ext)) {
				return false;
			}
			$newfilename = $fileName . '.' . $extension;
			if (file_exists($destinationPath . '/' . $newfilename)) {
				$info = pathinfo($newfilename);
				$imageNamee = $info['filename'] . '-' . rand(100, 999);
				$newfilename = $imageNamee . "." . $info['extension'];
			}
			$array[] = $newfilename;
			$upload_success = $fileimage->move($destinationPath, $newfilename);
			$resi = $destinationPath . '/' . $newfilename;
			/*$resizeimage=Helpers::resize_image($resi);
					$resizeimage=Helpers::compress_image($resi,100);*/
		}
		$imageNames = implode('{$}', $array);
		return $imageNames;
	}

	public static function compress_image($destination_url, $quality)
	{
		$sizeee = filesize($destination_url);
		if ($sizeee > 1000) {
			$info = getimagesize($destination_url);
			if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($destination_url);
			elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($destination_url);
			elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($destination_url);
			imagejpeg($image, $destination_url, $quality);
		}
		return $destination_url;
	}

	public static function resize_image($destination_url, $wid)
	{
		$info = getimagesize($destination_url);
		if ($info['mime'] == 'image/jpeg' || $info['mime'] == 'image/jpg') {
			$src = imagecreatefromjpeg($destination_url);
		} else if ($info['mime'] == 'image/png') {
			$src = imagecreatefrompng($destination_url);
		} else {
			$src = imagecreatefromgif($destination_url);
		}
		list($width, $height) = getimagesize($destination_url);
		if ($width > $wid) {
			$newwidth = $wid;
			$newheight = ($height / $width) * $newwidth;
			$tmp = imagecreatetruecolor($newwidth, $newheight);
			imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
			imagejpeg($tmp, $destination_url, 100);
			imagedestroy($src);
			imagedestroy($tmp);
		}
		return $destination_url;
	}
	public static function sortBySubArrayValue(&$array, $key, $dir = 'asc')
	{

		$sorter = array();
		$rebuilt = array();

		//make sure we start at the beginning of $array
		reset($array);

		//loop through the $array and store the $key's value
		foreach ($array as $ii => $value) {
			$sorter[$ii] = $value[$key];
		}

		//sort the built array of key values
		if ($dir == 'asc') asort($sorter);
		if ($dir == 'desc') arsort($sorter);

		//build the returning array and add the other values associated with the key
		foreach ($sorter as $ii => $value) {
			$rebuilt[$ii] = $array[$ii];
		}

		//assign the rebuilt array to $array
		$array = $rebuilt;
	}
	public static function allmatchformats()
	{
		$formats = array();
		$format['t10'] = 't10';
		$format['t20'] = 't20';
		$format['test'] = 'test';
		$format['one-day'] = 'one-day';
		$format['100-ball'] = '100-ball';
		return $format;
	}
	public static function mailheader()
	{
		$geturl = Helpers::geturl();
		$mail = '
		';
		return $mail;
	}
	public static function mailbody($content)
	{
		$mail = '<div style="margin:0;padding:0;min-width:100%;background-color:#e5e5e5;font-family:Arial,Helvetica;font-size:16px">
					<div style="display:none;font-size:1px;color:#333333;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden"> Top the leaderboard! </div>
					<div style="padding:0px 0 0px 0">
					<div style="width:100%;background-color:#ff5722;padding:0px;border-bottom:1px solid #ddd">
						<div style="margin:0 auto;max-width:580px;padding:0px">
						<table width="100%" style="border-spacing:0">
							<tbody>
							<tr>
								<td style="padding:15px 0px 15px 20px" align="left">
								<div>
									<a href="" style="color:#ffffff;text-decoration:none" target="_blank" data-saferedirecturl="">' . (Helpers::settings()->project_name ?? '') . '</a>
								</div>
								</td>

							</tr>
							</tbody>
						</table>
						</div>
					</div>
			  <div style="padding:20px 5px 0 5px">
				<div style="margin:0 auto;max-width:600px;padding:0px;border-radius:4px">
				  <table width="100%" style="border-spacing:0;background-color:#fff">
					<tbody>
					  ' . $content . '
					  <tr>
						<td style="padding:20px 20px 0px 20px" align="center">
						  <a href="https://' . (Helpers::settings()->project_name ?? '') . '.com" style="text-decoration:none;font-family:Montserrat,Droid Sans,Lucida Sans Unicode,Lucida Grande,Arial Black,Arial,Helvetica;font-size:13px;color:#ffffff;font-weight:bold;line-height:20px;padding:12px 0;background-color:#3b5998;border-radius:25px;display:block;max-width:278px;width:100%" target="_blank" data-saferedirecturl="">
						   GO TO WEBSITE
						  </a>
						</td>
					  </tr>
  					</tbody>
				  </table>
				  <table width="100%" style="border-spacing:0;background:#f0f0f0">
					<tbody>
					  <tr>
						<td style="padding:0px 20px 20px 20px;font-family:Arial,Helvetica;font-size:15px;color:#262626;font-weight:normal;line-height:1.4" align="center">
						  <table style="border-spacing:0" width="100%">
							<tbody>
							  <tr>
								<td style="font-family:Arial,Helvetica;font-size:15px;color:#262626;font-weight:normal;line-height:1.4;padding:40px 0 10px 0" align="center">
								  <img style="width:48px" src="https://ci6.googleusercontent.com/proxy/XIPwsY2CXhV2aPrhA7Z0ameW6FXhCxq7rJofeNEdKzgVmaSM6faMlHAnOVHgw7U8FXXaIrjlukd1S-aQBJPfIvOmvhcMFdWQtJf7MMNp8gsWwrjZpr-fySh3HnmMp9Wg93t6hofP18r68Sn-a7Bq=s0-d-e1-ft#https://d13ir53smqqeyp.cloudfront.net/contain/newsletter/template-04-03-16/icon-rupee.png" alt="" class="CToWUd">
								</td>
							  </tr>
							  <tr>
								<td style="font-family:Montserrat,Droid Sans,Lucida Sans Unicode,Lucida Grande,Helvetica,Georgia,Arial;font-size:14px;color:#2c2c2c;font-weight:normal;line-height:22px;padding:0px" align="center">Invite your friends &amp; earn as they play!</td>
							  </tr>

							</tbody>
						  </table>
						</td>
					  </tr>
					  <tr>
						<td style="padding:0px 0px 20px 0px" align="left">
						  <table style="border-spacing:0" width="100%">
							<tbody>
							 <tr>
								<td style="padding:0px 0px 20px 0px;font-family:Montserrat,Arial,Helvetica;font-size:12px;color:#888888;font-weight:normal;line-height:1" align="center"><a style="padding:5px 10px;color:#888888" href="" target="_blank" data-saferedirecturl="">Contact Us</a>| <a style="padding:5px 10px;color:#888888" href="" target="_blank" data-saferedirecturl="">FairPlay</a>| <a style="padding:5px 10px;color:#888888" href="" target="_blank" data-saferedirecturl="">Email Preferences</a></td>
							  </tr>
							  <tr>
								<td style="padding:0px 20px 0px 20px;font-family:Montserrat,Arial,Helvetica;font-size:11px;color:#999999;font-weight:normal;line-height:16px" align="center">
								  <table>
									<tbody><tr>
									  <td style="padding:0 10px"><a href="" target="_blank" data-saferedirecturl=""><img src="https://ci4.googleusercontent.com/proxy/Gang5ywdmie7fUKT3_eSMNvBLZaNI-k4CiQSK5M-ZM3HCO0MpJZVnSpMSJzhWk7a6fpAFvJzSSoR7CffAiMXCgqc_85HMY7aOzrw9okzM-eGCxSmWk1SX7KSWWQpsjB4K5J5AgxUnlcpMEJTaoJhho09=s0-d-e1-ft#https://d13ir53smqqeyp.cloudfront.net/contain/newsletter/template-04-03-16/facebook_new1.png" width="36" alt="" class="CToWUd"></a></td>
									  <td style="padding:0 10px"><a href="" target="_blank" data-saferedirecturl=""><img src="https://ci3.googleusercontent.com/proxy/JCOPAWdLjOG4LlvLPAlg7wH3mD15OyGuZK0SRvnm1rhFRkRYUAUfnMFLXOmGYPDoc7hi55X6p8zfTC0n67bf0ZVmIYyc8XBeGW6V9mdM-CQ8joI6wplUDcF64KTyfZfTwfvNZx1sftICr95OlcgmjZQ=s0-d-e1-ft#https://d13ir53smqqeyp.cloudfront.net/contain/newsletter/template-04-03-16/twitter_new1.png" width="36" alt="" class="CToWUd"></a></td>
									  <td style="padding:0 10px"><a href="" target="_blank" data-saferedirecturl=""><img src="https://ci3.googleusercontent.com/proxy/y8C9hKN6EkfZnMIRbiU7tOFaVId4PvGXDqHhsL9BmvMT4NjOAVuYuqAQzuZE32tEnpX218-kakLZDfJJSyx3So1FtBbR_O3oFu5nKKy_nVvl1TN_wK1iTjdBReG-kitooVKa4-R2Qhkn4wrx_V4UYC9R1Q=s0-d-e1-ft#https://d13ir53smqqeyp.cloudfront.net/contain/newsletter/template-04-03-16/instagram_new1.png" width="36" alt="" class="CToWUd"></a></td>
									  <td style="padding:0 10px"><a href="" target="_blank" data-saferedirecturl=""><img src="https://ci4.googleusercontent.com/proxy/jYZPkRXopWQzrr2XQgmnC7Vbur4HIljcwUq1YlxZ33acdJDou7S9z9roDvS5Jkznt6Jeyv6fQl8WMH0KK-HFb7r7U9rThSKcsh7oafw_g08QicK_h-HSSwpmJl_rHB3l3lZ96Fez2luVDM5CsPq7knA=s0-d-e1-ft#https://d13ir53smqqeyp.cloudfront.net/contain/newsletter/template-04-03-16/youtube_new1.png" width="36" alt="" class="CToWUd"></a></td>
									</tr>
								  </tbody></table>
								</td>
							  </tr>
							  <tr>
								<td style="padding:20px 20px 0px 20px;font-family:Montserrat,Arial,Helvetica;font-size:11px;color:#999999;font-weight:normal;line-height:16px" align="center"><span class="il">' . (Helpers::settings()->project_name ?? '') . '</span> Fantasy Pvt. Ltd. </td>
							  </tr>
							 </tbody>
						  </table>
						</td>
					  </tr>
					</tbody>
				  </table>
				</div>
			  </div>
			</div>
		</div>';
		return $mail;
	}
	public static function mailfooter()
	{
		$mail = '<div>
		<img src="https://ci3.googleusercontent.com/proxy/umBdvXUjq17C7Ah8QC92nPc_Zc2l9j1ldK8SxuqWO8iBHdjinWUyvS5lIZIVtJtu9797cgcP-xevQ-Z7o-qKz6ROjzjVJ7onPFpnVA4LLeMX3w-CayzC1YMDt4zCnEOlN85o8EWnpz3RSbje-NzlAloR1FAcypvp7ED9qdUJJ-IyRg_nUtGGKpYIFA32oAl8c6kOEnui6vYf6Uui1qo1Pr23uUWVCfTtaC43rwMkmL2ew-yXdbeXHdPLPjQH9l8b8NiKfAYS7MFnszxzzbXXill8l7RKu-s-X7DS_9Dk9ZdbbDiGK03CwxHRgq559Iu55Ttbo47765RqQIJ-kQMn16MYeEm-A5XIGTGmE0MXmGTyobPQL8nYoWZjDfbc5413aq0xcSY1U_wG8PXcbtQDX4ntGLglC9_nQrZn4g63PRmnZtkCi9IEjwPpsYLva9Od9SK7SkKsbpppeij4AjRbM69TyigCLSFaMI_moQn_GMQC7wdk2fS-jZ9uq65Eo1RJ4fKjuDI5MQ2c-XEbpszgG8MgFX_fCPJY1lCdMER2QxUy4P1dmUdAepys4iJ_b-8102YxVPJpz1Uz-_8X9cyBb7hxjfDfiWvPky_i401XczFTxoiwQ5I3uYSkwQ1LYNLir0eVuPf_2B8EwW78xE9oRDtl=s0-d-e1-ft#http://email.' . (Helpers::settings()->project_name ?? '') . '.com/wf/open?upn=5F-2BQJKy1fxWNQUL1awoE5RAuXCnn-2B3E7raKXhSDsJrAO4eo23-2FGuE9jjMJckMGOY9c01PKIUoian0V5pXFwlLfv3MfGt2-2BQ0zIU2gs8BmtBPDqZ1N1im7iQyn69dQtBemMifSCJamjygvwezy3xdXaPeSRXLRLImQ1xrRaA8wg1ZMoijvAgdfBNXJ4e9-2BuKFUHLT7b6dEfDB6ziVSAFppnm-2B155TW-2FpiiPG-2B3LwJEvlcKnkFt8ZeRZTNNwLh5q0O9RkQFBPfOsSVfdouFLVfmkCLfAvr1l6pxmZtqGrC7R7Ye-2BHaBxGdYLPRV4ded770glAyOtvpL1kzo7YR9-2Fl7MRPsqWDPUz1EKdK-2BVMdROdlh1WcicUwXj-2BoSfbFgajg-2FvhcAPCJRzn43lNOItynlQA-3D-3D" alt="" width="1" height="1" border="0" style="height:1px!important;width:1px!important;border-width:0!important;margin-top:0!important;margin-bottom:0!important;margin-right:0!important;margin-left:0!important;padding-top:0!important;padding-bottom:0!important;padding-right:0!important;padding-left:0!important" class="CToWUd"><div class="yj6qo"></div><div class="adL">
		</div></div><div class="adL">
		</div></div>';
		return $mail;
	}
	public static function mailsentFormat($email, $subject, $mailmessage)
	{
		// echo "<pre>";print_r($email);die;
		Mail::to($email)->send(new SendMailable($mailmessage, $subject));
	}
	public static function sendmultiplenotification($title, $message, $include_image, $users)
	{

		// require_once('./sendnotification/firebase.php');
		// require_once('./sendnotification/push.php');
		if (!empty($users)) {
			$regarray = array();
			$findappids = DB::table('androidappid')->whereIn('user_id', $users)->get();
			if (!empty($findappids)) {
				foreach ($findappids as $app) {
					$regarray[] = $app->appkey;
				}
			}
			$firebase = new Firebase();
			$push = new Push();
			$payload = array();
			$payload['team'] = 'India';
			$payload['score'] = '5.6';
			$push->setTitle($title);
			$push->setMessage($message);
			$push->setIsBackground(FALSE);
			$push->setNotificationType('');
			$json = $push->getPush();
			// echo "<pre>";print_r($json);die;
			$response = $firebase->sendMultiple($regarray, $json);
			// dd($response);
		}
	}
	public static function sendmultiplenotificationagain($title, $message, $include_image, $users)
	{
		$firebase = new Firebase();
		$push = new Push();
		$payload = array();
		$payload['team'] = 'India';
		$payload['score'] = '5.6';
		$push->setTitle($title);
		$push->setMessage($message);
		$push_type = 'topic';
		if ($include_image != "") {
			$push->setImage('http://api.androidhive.info/images/minion.jpg');
		} else {
			$push->setImage('');
		}
		$push->setIsBackground(FALSE);
		$push->setPayload($payload);
		$push->setNotificationType('');
		$json = '';
		$response = '';

		if ($push_type == 'topic') {
			$json = $push->getPush();
			$response = $firebase->sendToTopic('All', $json);
		} else if ($push_type == 'individual') {
			$json = $push->getPush();
			$response = $firebase->send($app->appkey, $json);
		}
	}
	public static function sendnotification($title, $message, $include_image, $regId)
	{
		if ($regId != "") {
			$findappids = DB::table('androidappid')->where('user_id', $regId)->get();
			if (!empty($findappids)) {
				foreach ($findappids as $app) {
					$firebase = new Firebase();
					$push = new Push();
					$payload = array();
					$payload['team'] = 'India';
					$payload['score'] = '5.6';
					$push->setTitle($title);
					$push->setMessage($message);
					$push_type = 'individual';
					if ($include_image != "") {
						$push->setImage('http://api.androidhive.info/images/minion.jpg');
					} else {
						$push->setImage('');
					}
					$push->setIsBackground(FALSE);
					$push->setPayload($payload);
					$push->setNotificationType('');
					$json = '';
					$response = '';

					if ($push_type == 'topic') {
						$json = $push->getPush();
						$response = $firebase->sendToTopic('global', $json);
					} else if ($push_type == 'individual') {
						$json = $push->getPush();
						$response = $firebase->send($app->appkey, $json);
					}
				}
			}
		}
	}

	public static function sendnotificationind($title, $message, $include_image, $regId)
	{
		if ($regId != "") {
			$findappids = DB::table('androidappid')->where('user_id', $regId)->first();
			if (!empty($findappids)) {
				// foreach($findappids as $app){
				$firebase = new Firebase();
				$push = new Push();
				$payload = array();
				$payload['team'] = 'India';
				$payload['score'] = '5.6';
				$push->setTitle($title);
				$push->setMessage($message);
				$push_type = 'individual';
				if ($include_image != "") {
					$push->setImage('http://api.androidhive.info/images/minion.jpg');
				} else {
					$push->setImage('');
				}
				$push->setIsBackground(FALSE);
				$push->setPayload($payload);
				$push->setNotificationType('');
				$json = '';
				$response = '';

				if ($push_type == 'topic') {
					$json = $push->getPush();
					$response = $firebase->sendToTopic('global', $json);
				} else if ($push_type == 'individual') {
					$json = $push->getPush();
					$response = $firebase->send($findappids->appkey, $json);
				}

				// }
			}
		}
	}

	public static function getnewmail($email)
	{
		if (strpos($email, '@gmail.com') !== false) {
			$wordbreak = explode('@gmail.com', $email);
			$word1 = str_replace('.', '', $wordbreak[0]);
			$email = $word1 . '@gmail.com';
		}
		return $email;
	}
	public static function checkEmail($email)
	{
		if (strpos($email, '@') !== false) {
			$split = explode('@', $email);
			return (strpos($split['1'], '.') !== false ? true : false);
		} else {
			return false;
		}
	}
	public static function Mailbody1($content)
	{
		$content = '<table class="m_-7802618208648170374body-wrap" width="100%" cellspacing="0" cellpadding="0" style="margin:0;padding:0;width:100%;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif">
            <tbody><tr style="margin:0;padding:0">
                <td style="margin:0;padding:0"></td>
                <td class="m_-7802618208648170374containear" bgcolor="#F6F6F6" style="margin:0 auto!important;padding:0;background:#f6f6f6;display:block!important;max-width:800px!important;clear:both!important">
                    <br style="margin:0;padding:0">
                    <div class="m_-7802618208648170374content" style="margin:0 auto 15px;padding:0;max-width:600px;border-radius:3px;display:block;background:#fff">
                        <div class="m_-7802618208648170374mailer-header" style="margin:0;padding:0 15px">

                            <table class="m_-7802618208648170374head-wrap" cellspacing="0" cellpadding="0" style="margin:0;padding:0;width:100%">
                                <tbody><tr style="margin:0;padding:0">
                                    <td style="margin:0;padding:0"></td>
                                    <td class="m_-7802618208648170374header m_-7802618208648170374container" bgcolor="#fff" style="margin:0 auto!important;background:#fff;border-bottom:1px solid #e9e9e9;display:block!important;max-width:800px!important;clear:both!important">
                                        <table width="100%" cellspacing="0" cellpadding="0" style="margin:0;padding:0">
                                            <tbody><tr style="margin:0;padding:0">
                                                <td valign="top" align="center" width="100" style="margin:0;padding:0">
                                                    <img alt="' . (Helpers::settings()->project_name ?? '') . '" style="width:40px;padding:0;max-width:100%; padding: 5px;" src="' . (Helpers::settings()->site_url ?? '') . '.com' . '/include/glsports/public/logo.png" class="CToWUd">
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                    <td style="margin:0;padding:0"></td>
                                </tr>
                            </tbody></table>

                        </div>'
			. $content .
			'<br style="margin:0;padding:0">

                        <div class="m_-7802618208648170374primary-footer" style="margin:0;padding:0">

                            <table class="m_-7802618208648170374social" width="100%" cellspacing="0" cellpadding="0" style="padding:0 15px;border-top:1px solid #e9e9e9;border-bottom:1px solid #e9e9e9;margin:0">
                                <tbody><tr style="margin:0;padding:0">
                                    <td style="margin:0;padding:0">

                                        <table align="left" class="m_-7802618208648170374column" cellspacing="0" cellpadding="0" style="margin:0;padding:15px 0;max-width:112px">
                                            <tbody><tr style="margin:0;padding:0">
                                                <td style="margin:0;padding:0">
                                                    <table class="m_-7802618208648170374app-links" border="0" cellspacing="0" cellpadding="0" style="font-family:Arial,Helvetica,sans-serif;font-size:11px;margin:0;padding:0">
                                                        <tbody style="margin:0;padding:0">
                                                            <tr style="margin:0;padding:0">
                                                                <td style="margin:0;padding:0">Get the App:&nbsp;&nbsp;</td>
                                                                <td class="m_-7802618208648170374icon" style="margin:0;padding:0;padding-right:5px">
                                                                    <a  style="margin:0;padding:0;color:#2ba6cb;display:block" target="_blank" >
                                                                        <img style="height:15px;width:auto;margin:0;padding:0;max-width:100%;max-height:15px!important" src="' . (Helpers::settings()->site_url ?? '') . '.com' . '/include/glsports/public/logo.png" class="CToWUd">
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody></table>


                                        <table align="right" class="m_-7802618208648170374column" cellspacing="0" cellpadding="0" style="margin:0;padding:15px 0;max-width:142px">
                                            <tbody><tr style="margin:0;padding:0">
                                                <td style="margin:0;padding:0">
                                                    <table class="m_-7802618208648170374social-links" border="0" cellspacing="0" cellpadding="0" style="font-family:Arial,Helvetica,sans-serif;font-size:11px;margin:0;padding:0">
                                                        <tbody style="margin:0;padding:0">
                                                            <tr style="margin:0;padding:0">
                                                                <td style="margin:0;padding:0;text-align:left">Follow us:&nbsp;&nbsp;</td>
                                                                <td class="m_-7802618208648170374icon" style="margin:0;padding:0;padding-right:5px">
                                                                    <a href="https://www.facebook.com/" style="margin:0;padding:0;color:#2ba6cb;display:block" target="_blank" >
                                                                        <img style="height:15px;width:auto;margin:0;padding:0;max-width:100%;max-height:15px!important" src="' . (Helpers::settings()->site_url ?? '') . '.com' . '/include/glsports/public/fb.png" alt="Facebook" border="0" class="CToWUd"></a>
                                                                </td>
                                                                <td class="m_-7802618208648170374icon" style="margin:0;padding:0;padding-right:5px">
                                                                    <a href="https://twitter.com" style="margin:0;padding:0;color:#2ba6cb;display:block" target="_blank">
                                                                        <img style="height:15px;width:auto;margin:0;padding:0;max-width:100%;max-height:15px!important" src="' . (Helpers::settings()->site_url ?? '') . '.com' . '/include/glsports/public/tw.png" alt="Twitter" border="0" class="CToWUd"></a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody></table>

                                    </td>
                                </tr>
                            </tbody></table>
                        </div>

                        <div class="m_-7802618208648170374secondary-footer" style="margin:0;padding:0 15px;background:#fbfbfb">
                            <br style="margin:0;padding:0">
                            <table width="100%" class="m_-7802618208648170374column" cellspacing="0" cellpadding="0" style="margin:0;padding:0">
                                <tbody><tr style="margin:0;padding:0">
                                    <td style="margin:0;padding:0">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#9b9b9b;margin:0;padding:0">
                                            <tbody style="margin:0;padding:0">
                                                <tr style="margin:0;padding:0">
                                                    <td align="center" style="margin:0;padding:0">
                                                        © 2019-' . (Helpers::settings()->project_name ?? '') . '.com. All rights reserved.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody></table><br style="margin:0;padding:0">
                        </div>
                    </div>

                </td>
                <td style="margin:0;padding:0"></td>
            </tr>
        </tbody></table>';
		return $content;
	}
	public static function invoice($email, $amount, $tranid)
	{

		$rand = rand(000000, 999999);
		$html = '';
		$html .= '<div class=""><div class="aHl"></div><div id=":76" tabindex="-1"></div><div id=":2z" class="ii gt"><div id=":3l" class="a3s aXjCH "><div dir="ltr"><div class="adM"><br></div><div class="gmail_quote"><div dir="ltr"><div class="gmail_quote"><div class="adM"><br></div><u></u>
            <div style="margin:0;padding:0;min-width:100%;font-family:Arial,Helvetica;font-size:16px">
                <div style="padding:0px">
                  <div style="margin:0 auto;max-width:600px;padding:20px 20px;box-sizing:border-box;border-radius:4px;border: 1px solid silver;">
                    <table style="border-spacing:0;background-color:#fff;font-family:Helvetica,Georgia,Arial" width="100%">
                      <tbody>
                      <tr>
                          <td colspan="2" style="font-size:20px;font-weight:bold;color:#282828">
                            Invoice
                          </td>
                        </tr>
                        <tr>
                          <td style="border-bottom:solid 4px #e8e8e8;padding:24px 0px">
                            <table width="100%">
                              <tbody><tr>
                                <td>
                                  <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">' . $email . '</div>
                                  <div style="font-size:14px;color:#282828;line-height:16px;margin-bottom:2px"></div>
                                </td>
                                <td align="right">
                                  <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">BHAGWATI SOFTECH</div>
                                  <div style="font-size:14px;color:#282828;line-height:16px;margin-bottom:2px"><span style="color:#9b9b9b">Date:</span> ' . date('d M Y') . '</div>
                                  <div style="font-size:14px;color:#282828;line-height:16px;margin-bottom:2px"><span style="color:#9b9b9b">Invoice No:</span> ' . $rand . '</div>
                                  <div style="font-size:14px;color:#282828;line-height:16px;margin-bottom:2px"><span style="color:#9b9b9b">Registration No.:</span> 08CHTPS9593N1ZR</div>
                                   <div style="font-size:14px;color:#282828;line-height:16px;margin-bottom:2px"><span style="color:#9b9b9b">BRN:</span> 8005220005002190</div>
                                </td>
                              </tr>
                            </tbody></table>
                          </td>
                        </tr>
                        <tr>
                          <td style="border-bottom:dashed 1px #e8e8e8;padding:22px 0px 12px 0px">
                            <table width="100%">
                              <tbody><tr>
                                <td>
                                  <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">Description*</div>
                                </td>
                                <td align="right">
                                  <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">Amount (INR)</div>
                                </td>
                              </tr>
                            </tbody></table>
                          </td>
                        </tr>
                        <tr>
                          <td style="border-bottom:solid 1px #7b7a7a;padding:10px 0px">
                            <table width="100%">
                              <tbody><tr>
                                <td>
                                  <div style="font-size:14px;color:#282828;line-height:16px;margin-bottom:2px;padding: 5px 1px;">Other on-line contents n.e.c.</div>
                                  <div style="font-size:14px;color:#282828;line-height:16px;margin-bottom:2px">Transaction ID: <span style="float: right;">' . $tranid . '</span></div>
                                </td>
                              </tr>
                            </tbody></table>
                          </td>
                        </tr>

                        <tr>
                          <td style="border-bottom:solid 4px #e8e8e8;padding:30px 0px 24px 0px">
                            <table width="100%">
                              <tbody><tr>
                                <td>
                                  <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">Total Taxable Value - Platform Fee*</div>
                                </td>
                                <td align="right">
                                  <div style="font-size:14px;color:#282828;line-height:16px;margin-bottom:2px">₹' . $amount . '</div>
                                </td>
                              </tr>
                            </tbody></table>
                          </td>
                        </tr>
                        <tr>
                          <td style="border-bottom:dashed 1px #7b7a7a;padding:15px 0px">
                            <table width="100%">
                              <tbody><tr>
                                <td>
                                  <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">Total</div>
                                </td>
                                <td align="right">
                                  <div style="font-size:14px;color:#282828;line-height:16px;margin-bottom:2px">₹' . $amount . '</div>
                                </td>
                              </tr>
                            </tbody></table>
                          </td>
                        </tr>
                        <tr>
                          <td style="padding:15px 0px 0px">
                            <table width="100%">
                              <tbody><tr>
                                <td style="border-bottom:solid 1px #e8e8e8;padding-bottom:12px">
                                  <div style="font-size:12px;color:#7b7a7a;font-weight:normal;line-height:16px;margin-bottom:2px">Terms &amp; Conditions:</div>
                                  <div style="font-size:12px;color:#7b7a7a;font-weight:normal;line-height:16px">Refer to <a style="text-decoration:none;color:#3759a5" href="https://' . (Helpers::settings()->project_name ?? '') . '.com/terms-conditions" target="_blank">https://www.' . (Helpers::settings()->project_name ?? '') . '.com/tf/cricket/termsandconditions</a></div>
                                </td>
                              </tr>
                            </tbody></table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            <div class="yj6qo"></div><div class="adL">
            </div></div><div class="adL">
            </div></div></div><div class="adL">
            </div></div></div><div class="adL">
            </div></div></div><div id=":71" class="ii gt" style="display:none"><div id=":72" class="a3s aXjCH undefined"></div></div><div class="hi"></div></div>';
		return $html;
		die;
	}

	public static function generateInvoice($fullname, $amount, $tranid, $peruserplatformfees, $taxablevalue, $state)
	{
		//
		$cgst = 0;
		$sgst = 0;
		$igst = 0;
		$ttltax = 0;
		if ($state == 'Rajasthan') {
			$cgst = round($taxablevalue / 2, 2);
			$sgst = round($taxablevalue / 2, 2);
		} else {
			$igst = round($taxablevalue, 2);
		}
		$ttltax = $peruserplatformfees + $cgst + $sgst + $igst;
		// echo "<pre>";print_r($ttltax);die;
		$rand = rand(000000, 999999);

		$content = '<div style="margin:0;padding:0;min-width:100%;font-family:Arial,Helvetica;font-size:16px">
	    <div style="padding:0px 0 0px 0">
	      <div style="width:100%;background-color:#fff;padding:0px">
	        <div style="margin:0 auto;max-width:600px;padding:0px 20px;box-sizing:border-box">
	          <table width="100%" style="border-spacing:0;border-bottom:solid 4px #e8e8e8">
	            <tbody>
	              <tr>
	                <td style="font-size:20px;font-weight:bold;color:#282828">
	                  Acknowledgement
	                </td>
	                <td style="padding:24px 0px 24px 0px;font-family:KohinoorBangla-Regular,Droid Sans,Lucida Sans Unicode,Lucida Grande,Helvetica,Georgia,Arial;font-size:12px;color:#ffffff" align="right">
	                  <div>
	                    <a href="https://' . (Helpers::settings()->project_name ?? '') . '.com"><img src="https://' . (Helpers::settings()->project_name ?? '') . '.com/' . (Helpers::settings()->project_name ?? '') . '/public/logo.png" alt="' . (Helpers::settings()->project_name ?? '') . '" width="65" style="outline:none;display:block" class="CToWUd"></a>
	                  </div>
	                </td>
	              </tr>
	            </tbody>
	          </table>
	        </div>
	      </div>
	      <div style="padding:0px">
	        <div style="margin:0 auto;max-width:600px;padding:0px 20px;box-sizing:border-box;border-radius:4px">
	          <table width="100%" style="border-spacing:0;background-color:#fff;font-family:"Helvetica",Georgia,Arial">
	            <tbody>
	              <tr>
	                <td style="border-bottom:solid 4px #e8e8e8;padding:24px 0px">
	                  <table width="100%">
	                    <tbody><tr>
	                      <td>
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px"><p>' . $fullname . '</p></div>
	                        <div style="font-size:14px;color:#282828;line-height:16px;margin-bottom:2px"></div>
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px"><p>' . $state . '</p></div>
	                        <div style="font-size:14px;color:#282828;line-height:16px;margin-bottom:2px"></div>
	                      </td>
	                      <td align="right">
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">BHAGWATI SOFTECH</div>
	                        <div style="font-size:14px;color:#282828;line-height:16px;margin-bottom:2px"><span style="color:#9b9b9b">Date:</span> ' . date('d M Y') . '</div>
	                        <div style="font-size:14px;color:#282828;line-height:16px"><span style="color:#9b9b9b">GSTIN:</span> 08CHTPS9593N1ZR</div>
	                      </td>
	                    </tr>
	                  </tbody></table>
	                </td>
	              </tr>
	              <tr>
	                <td style="border-bottom:dashed 1px #e8e8e8;padding:22px 0px 12px 0px">
	                  <table width="100%">
	                    <tbody><tr>
	                      <td>
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">Description*</div>
	                      </td>
	                      <td align="right">
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">Amount (INR)</div>
	                      </td>
	                    </tr>
	                  </tbody></table>
	                </td>
	              </tr>
	              <tr>
	                <td style="border-bottom:solid 1px #7b7a7a;padding:10px 0px">
	                  <table width="100%">
	                    <tbody><tr>
	                      <td>
	                        <div style="font-size:14px;color:#282828;line-height:16px;margin-bottom:2px">Transaction ID: <span style="float: right;">' . $tranid . '</span></div>
	                      </td>
	                    </tr>
	                  </tbody></table>
	                </td>
	              </tr>

	              <tr>
	                <td style="border-bottom:solid 4px #e8e8e8;padding:10px 0px 24px 0px">
	                  <table width="100%">
	                    <tbody><tr>
	                      <td>
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">Total</div>
	                      </td>
	                      <td align="right">
	                        <div style="font-size:14px;color:#282828;line-height:16px;margin-bottom:2px">₹' . $amount . '</div>
	                      </td>
	                    </tr>
	                  </tbody></table>
	                </td>
	              </tr>

	              <tr>
	                <td style="padding:10px 0px 0px">
	                  <table width="100%">
	                    <tbody><tr>
	                      <td style="border-bottom:solid 1px #e8e8e8;padding-bottom:12px">
	                        <div style="font-size:12px;color:#7b7a7a;font-weight:normal;line-height:16px;margin-bottom:2px">Terms &amp; Conditions:</div>
	                        <div style="font-size:12px;color:#7b7a7a;font-weight:normal;line-height:16px">Refer to <a style="text-decoration:none;color:#3759a5" href="https://' . (Helpers::settings()->project_name ?? '') . '.com/terms-conditions">https://www.<span class="il">' . (Helpers::settings()->project_name ?? '') . '</span>.com/tf/<wbr>cricket/termsandconditions</a></div>
	                      </td>
	                    </tr>
	                  </tbody></table>
	                </td>
	              </tr>

	              <tr>
	                <td style="padding:10px 0px">
	                  <table width="100%">
	                    <tbody><tr>
	                      <td style="padding-bottom:12px;font-size:12px;color:#7b7a7a;line-height:16px">
	                        <div style="margin-bottom:2px"><strong>Office Address:</strong>408, FLOOR NO.3, PRATAP NAGAR EXT., MURLIPURA,Jaipur, Rajasthan, 302039</div>
	                        <div style="font-style:oblique">This is a computer generated acknowledgement. No signature required</div>
	                      </td>
	                    </tr>
	                  </tbody></table>
	                </td>
	              </tr>
	            </tbody>
	          </table>
	        </div>
	      </div>
	    </div>

	    <div></div>


	    <div style="padding:0px 0 0px 0">
	      <div style="width:100%;background-color:#fff;padding:0px">
	        <div style="margin:0 auto;max-width:600px;padding:0px 20px;box-sizing:border-box">
	          <table width="100%" style="border-spacing:0;border-bottom:solid 4px #e8e8e8">
	            <tbody>
	              <tr>
	                <td style="font-size:20px;font-weight:bold;color:#282828">
	                  Tax <span class="il">Invoice</span>
	                </td>
	                <td style="padding:24px 0px 24px 0px;font-family:KohinoorBangla-Regular,Droid Sans,Lucida Sans Unicode,Lucida Grande,Helvetica,Georgia,Arial;font-size:12px;color:#ffffff" align="right">
	                  <div>
	                    <a href="https://' . (Helpers::settings()->project_name ?? '') . '.com"><img src="https://' . (Helpers::settings()->project_name ?? '') . '.com/' . (Helpers::settings()->project_name ?? '') . '/public/logo.png" alt="' . (Helpers::settings()->project_name ?? '') . '" width="65" style="outline:none;display:block" class="CToWUd"></a>
	                  </div>
	                </td>
	              </tr>
	            </tbody>
	          </table>
	        </div>
	      </div>
	      <div style="padding:0px">
	        <div style="margin:0 auto;max-width:600px;padding:0px 20px;box-sizing:border-box;border-radius:4px">
	          <table width="100%" style="border-spacing:0;background-color:#fff;font-family:"Helvetica",Georgia,Arial">
	            <tbody>
	              <tr>
	                <td style="border-bottom:solid 4px #e8e8e8;padding:24px 0px">
	                  <table width="100%">
	                    <tbody><tr>
	                      <td>
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px"><p>' . $fullname . '</p></div>
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px"><p>' . $state . '</p></div>
	                        <div style="font-size:14px;color:#282828;line-height:16px;margin-bottom:2px"></div>
	                      </td>
	                      <td align="right">
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">BHAGWATI SOFTECH</div>
	                        <div style="font-size:14px;color:#282828;line-height:16px;margin-bottom:2px"><span style="color:#9b9b9b">Date:</span> ' . date('d M Y') . '</div>
	                        <div style="font-size:14px;color:#282828;line-height:16px"><span style="color:#9b9b9b"><span class="il">Invoice</span> No:</span> ' . $rand . '</div>
	                        <div style="font-size:14px;color:#282828;line-height:16px"><span style="color:#9b9b9b">GSTIN:</span> 08CHTPS9593N1ZR</div>
	                        <div style="font-size:14px;color:#282828;line-height:16px"><span style="color:#9b9b9b">Place of Supply :</span>  Rajasthan</div>
	                      </td>
	                    </tr>
	                  </tbody></table>
	                </td>
	              </tr>
	              <tr>
	                <td style="border-bottom:dashed 1px #e8e8e8;padding:22px 0px 12px 0px">
	                  <table width="100%">
	                    <tbody><tr>
	                      <td>
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">Description*</div>
	                      </td>
	                      <td align="right">
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">Amount (INR)</div>
	                      </td>
	                    </tr>
	                  </tbody></table>
	                </td>
	              </tr>
	              <tr>
	                <td style="border-bottom:solid 1px #7b7a7a;padding:10px 0px">
	                  <table width="100%">
	                    <tbody><tr>
	                      <td>
	                        <div style="font-size:14px;color:#282828;line-height:16px;margin-bottom:2px">Transaction ID: ' . $tranid . '</div>
	                      </td>
	                    </tr>
	                  </tbody></table>
	                </td>
	              </tr>
	              <tr>
	                <td style="border-bottom:solid 1px #7b7a7a;padding:10px 0px">
	                  <table width="100%">
	                    <tbody><tr>
	                      <td>
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">Total Taxable Value - Platform Fee*</div>
	                      </td>
	                      <td align="right">
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">₹' . $peruserplatformfees . '</div>
	                      </td>
	                    </tr>
	                  </tbody></table>
	                </td>
	              </tr>
	              <tr>
	                <td style="border-bottom:solid 1px #e8e8e8;padding:10px 0px">
	                  <table width="100%">
	                    <tbody><tr>
	                      <td>
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">SGST @9%</div>
	                      </td>
	                      <td align="right">
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">₹' . $sgst . '</div>
	                      </td>
	                    </tr>
	                  </tbody></table>
	                </td>
	              </tr>
	              <tr>
	                <td style="border-bottom:solid 1px #e8e8e8;padding:10px 0px">
	                  <table width="100%">
	                    <tbody><tr>
	                      <td>
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">CGST @9%</div>
	                      </td>
	                      <td align="right">
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">₹' . $cgst . '</div>
	                      </td>
	                    </tr>
	                  </tbody></table>
	                </td>
	              </tr>
	              <tr>
	                <td style="border-bottom:solid 1px #7b7a7a;padding:10px 0px">
	                  <table width="100%">
	                    <tbody><tr>
	                      <td>
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">IGST @18%</div>
	                      </td>
	                      <td align="right">
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">₹' . $igst . '</div>
	                      </td>
	                    </tr>
	                  </tbody></table>
	                </td>
	              </tr>

	              <tr>
	                <td style="border-bottom:solid 4px #e8e8e8;padding:10px 0px 24px 0px">
	                  <table width="100%">
	                    <tbody><tr>
	                      <td>
	                        <div style="font-size:14px;color:#282828;font-weight:bold;line-height:16px;margin-bottom:2px">Total</div>
	                      </td>
	                      <td align="right">
	                        <div style="font-size:14px;color:#282828;line-height:16px;margin-bottom:2px">₹' . $ttltax . '</div>
	                      </td>
	                    </tr>
	                  </tbody></table>
	                </td>
	              </tr>
	              <tr>
	                <td style="padding:10px 0px 0px">
	                  <table width="100%">
	                    <tbody><tr>
	                      <td style="border-bottom:solid 1px #e8e8e8;padding-bottom:12px">
	                        <div style="font-size:12px;color:#7b7a7a;font-weight:normal;line-height:16px;margin-bottom:2px">Terms &amp; Conditions:</div>
	                        <div style="font-size:12px;color:#7b7a7a;font-weight:normal;line-height:16px">Refer to <a style="text-decoration:none;color:#3759a5" href="https://' . (Helpers::settings()->project_name ?? '') . '.com/terms-conditions">https://www.<span class="il">' . (Helpers::settings()->project_name ?? '') . '</span>.com/tf/<wbr>cricket/termsandconditions</a></div>
	                      </td>
	                    </tr>
	                  </tbody></table>
	                </td>
	              </tr>

	              <tr>
	                <td style="font-size:14px;font-style:oblique;color:#282828;line-height:normal;padding:11px 0px 0px">
	                  Tax payable under Reverse Charge : No
	                </td>
	              </tr>

	              <tr>
	                <td style="font-size:14px;font-style:oblique;color:#282828;line-height:normal;padding:11px 0px 0px">
	                  * Incase of Inter-state supply IGST will be applicable. Within State supplies are liable for CGST &amp; SGST.
	                </td>
	              </tr>

	              <tr>
	                <td style="padding:10px 0px">
	                  <table width="100%">
	                    <tbody><tr>
	                      <td style="padding-bottom:12px;font-size:12px;color:#7b7a7a;line-height:16px">
	                        <div style="margin-bottom:2px"><strong>Office Address:</strong>408, FLOOR NO.3, PRATAP NAGAR EXT., MURLIPURA, Jaipur, Rajasthan, 302039</div>
	                        <div style="font-style:oblique">This is a computer generated acknowledgement. No signature required</div>
	                      </td>
	                    </tr>
	                  </tbody></table>
	                </td>
	              </tr>
	            </tbody>
	          </table>


	        </div>
	      </div>
	    </div>';
		return $content;
		die;
	}

	public static function multid_sort($arr, $index)
	{
		$b = array();
		$c = array();

		foreach ($arr as $key => $value) {
			$b[$key] = $value[$index];
		}
		arsort($b);
		foreach ($b as $key => $value) {
			$c[] = $arr[$key];
		}
		return $c;
	}

	public static function logoUpload($file, $destinationPath, $filename)
	{
		$extension = $file->getClientOriginalExtension();
		$ext = array("png", "PNG", "jpg", 'jpeg', 'JPG', "JPEG");

		if (!in_array($extension, $ext)) {
			return false;
		}

		$resi = $destinationPath . '/' . $filename;
		$upload_success = $file->move($destinationPath, $filename);

		return $filename;
	}

	# project settings : project name, app id etc.
	# @param optional : type = website
	public static function settings($type = '')
	{

		$settings = DB::table('settings');

		if (!empty($type)) {
			$settings = $settings->where('type', $type);
		}

		$settings = $settings->pluck('value', 'name')
			->toArray();

		if (!empty($settings)) {
			return (object) $settings;
		} else {
			return false;
		}
	}

	# Entry in transactions
	# @param : array of transactions fields
	public static function transaction_entry(array $transactions)
	{
		DB::connection('mysql2')->table('transactions')->insert($transactions);
	}
	public static function HelpdeskMail($content)
	{
		$content = '<table class="m_-7802618208648170374body-wrap" width="100%" cellspacing="0" cellpadding="0" style="margin:0;padding:0;width:100%;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif">
            <tbody><tr style="margin:0;padding:0">
                <td style="margin:0;padding:0"></td>
                <td class="m_-7802618208648170374containear" bgcolor="#F6F6F6" style="margin:0 auto!important;padding:0;background:#f6f6f6;display:block!important;max-width:800px!important;clear:both!important">
                    <br style="margin:0;padding:0">
                    <div class="m_-7802618208648170374content" style="margin:0 auto 15px;padding:0;max-width:600px;border-radius:3px;display:block;background:#fff">
                        <div class="m_-7802618208648170374mailer-header" style="margin:0;padding:0 15px">

                            <table class="m_-7802618208648170374head-wrap" cellspacing="0" cellpadding="0" style="margin:0;padding:0;width:100%">
                                <tbody><tr style="margin:0;padding:0">
                                    <td style="margin:0;padding:0"></td>
                                    <td class="m_-7802618208648170374header m_-7802618208648170374container" bgcolor="#fff" style="margin:0 auto!important;background:#fff;border-bottom:1px solid #e9e9e9;display:block!important;max-width:800px!important;clear:both!important">
                                        <table width="100%" cellspacing="0" cellpadding="0" style="margin:0;padding:0">
                                            <tbody><tr style="margin:0;padding:0">
                                                <td valign="top" align="center" width="100" style="margin:0;padding:0">
                                                    ' . (Helpers::settings()->project_name ?? '') . '
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                    <td style="margin:0;padding:0"></td>
                                </tr>
                            </tbody></table>

                        </div>'
			. $content .
			'<br style="margin:0;padding:0">

                        <div class="m_-7802618208648170374primary-footer" style="margin:0;padding:0">

                            <table class="m_-7802618208648170374social" width="100%" cellspacing="0" cellpadding="0" style="padding:0 15px;border-top:1px solid #e9e9e9;border-bottom:1px solid #e9e9e9;margin:0">
                                <tbody><tr style="margin:0;padding:0">
                                    <td style="margin:0;padding:0">

                                        <table align="left" class="m_-7802618208648170374column" cellspacing="0" cellpadding="0" style="margin:0;padding:15px 0;max-width:112px">
                                            <tbody><tr style="margin:0;padding:0">
                                                <td style="margin:0;padding:0">
                                                    <table class="m_-7802618208648170374app-links" border="0" cellspacing="0" cellpadding="0" style="font-family:Arial,Helvetica,sans-serif;font-size:11px;margin:0;padding:0">
                                                        <tbody style="margin:0;padding:0">
                                                            <tr style="margin:0;padding:0">
                                                                <td style="margin:0;padding:0">Get the App:&nbsp;&nbsp;</td>
                                                                <td class="m_-7802618208648170374icon" style="margin:0;padding:0;padding-right:5px">
                                                                    <a  style="margin:0;padding:0;color:#2ba6cb;display:block" target="_blank" >
                                                                        <img style="height:15px;width:auto;margin:0;padding:0;max-width:100%;max-height:15px!important" src="' . (Helpers::settings()->site_url ?? '') . '/supervised/public/logo.png" class="CToWUd">
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>
                        </div>

                        <div class="m_-7802618208648170374secondary-footer" style="margin:0;padding:0 15px;background:#fbfbfb">
                            <br style="margin:0;padding:0">
                            <table width="100%" class="m_-7802618208648170374column" cellspacing="0" cellpadding="0" style="margin:0;padding:0">
                                <tbody><tr style="margin:0;padding:0">
                                    <td style="margin:0;padding:0">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#9b9b9b;margin:0;padding:0">
                                            <tbody style="margin:0;padding:0">
                                                <tr style="margin:0;padding:0">
                                                    <td align="center" style="margin:0;padding:0">
                                                        © 2019-' . (Helpers::settings()->site_url ?? '') . '. All rights reserved.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody></table><br style="margin:0;padding:0">
                        </div>
                    </div>

                </td>
                <td style="margin:0;padding:0"></td>
            </tr>
        </tbody></table>';
		return $content;
	}

	public static function PanCardVerfy(Request $request)
	{
		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => "https://gamma.cashfree.com/verification",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "{\"pan\":\".$request->get('pannumber').\"}",
			CURLOPT_HTTPHEADER => [
				"Accept: application/json",
				"x-client-id: CF14892DK1NHY04Y2AAMA2",
				"x-client-secret: 1144e06b4317a76e8dcf92670d8cc69821b130a9",
				"Content-Type: application/json"
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return $err;
		} else {
			return $response;
		}
	}

	public static function BankVerfy(Request $request)
	{
		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => "https://payout-api.cashfree.com/payout/v1.2/validation/bankDetails?bankAccount=" . $request->get('accno') . "&ifsc=" . $request->get('ifsc'),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => [
				"Accept: application/json"
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			return $response;
		}
	}
}
