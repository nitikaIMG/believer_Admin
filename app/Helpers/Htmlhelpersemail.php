<?php
namespace App\Helpers;

class Htmlhelpersemail
{
    public static function emailotp($code, $userteam)
    {
        $html = '';
        $html .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
			<head>
			<!--[if gte mso 9]>
			<xml>
			<o:OfficeDocumentSettings>
			<o:AllowPNG/>
			<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
			</xml>
			<![endif]-->
			<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
			<meta http-equiv="X-UA-Compatible" content="IE=edge" />
			<meta name="format-detection" content="date=no" />
			<meta name="format-detection" content="address=no" />
			<meta name="format-detection" content="telephone=no" />
			<meta name="x-apple-disable-message-reformatting" />
			<!--[if !mso]><!-->
			<link href="https://fonts.googleapis.com/css?family=PT+Sans:400,400i,700,700i&display=swap" rel="stylesheet" />
			<!--<![endif]-->
			<title>Believer11 -OTP</title>
			<!--[if gte mso 9]>
			<style type="text/css" media="all">
			sup { font-size: 100% !important; }
			</style>
			<![endif]-->
			<!-- body, html, table, thead, tbody, tr, td, div, a, span { font-family: Arial, sans-serif !important; } -->


			<style type="text/css" media="screen">
			body { padding:0 !important; margin:0 auto !important; display:block !important; min-width:100% !important; width:100% !important; background:#e0f8ff; -webkit-text-size-adjust:none }
			a { color:#f3189e; text-decoration:none }
			p { padding:0 !important; margin:0 !important }
			img { margin: 0 !important; -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ }

			a[x-apple-data-detectors] { color: inherit !important; text-decoration: inherit !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; }

			.btn-16 a { display: block; padding: 15px 35px; text-decoration: none; }
			.btn-20 a { display: block; padding: 15px 35px; text-decoration: none; }

			.l-white a { color: #ffffff; }
			.l-black a { color: #282828; }
			.l-pink a { color: #f3189e; }
			.l-grey a { color: #6e6e6e; }
			.l-purple a { color: #9128df; }

			.gradient { background: linear-gradient(to right, #019173 0%,#0CB0E0 100%); }

			.btn-secondary { border-radius: 10px; background: linear-gradient(to right, #019173 0%,#0CB0E0 100%); }


			/* Mobile styles */
			@media only screen and (max-device-width: 480px), only screen and (max-width: 480px) {
			.mpx-10 { padding-left: 10px !important; padding-right: 10px !important; }

			.mpx-15 { padding-left: 15px !important; padding-right: 15px !important; }

			u + .body .gwfw { width:100% !important; width:100vw !important; }

			.td,
			.m-shell { width: 100% !important; min-width: 100% !important; }

			.mt-left { text-align: left !important; }
			.mt-center { text-align: center !important; }
			.mt-right { text-align: right !important; }

			.me-left { margin-right: auto !important; }
			.me-center { margin: 0 auto !important; }
			.me-right { margin-left: auto !important; }

			.mh-auto { height: auto !important; }
			.mw-auto { width: auto !important; }

			.fluid-img img { width: 100% !important; max-width: 100% !important; height: auto !important; }

			.column,
			.column-top,
			.column-dir-top { float: left !important; width: 100% !important; display: block !important; }

			.m-hide { display: none !important; width: 0 !important; height: 0 !important; font-size: 0 !important; line-height: 0 !important; min-height: 0 !important; }
			.m-block { display: block !important; }

			.mw-15 { width: 15px !important; }

			.mw-2p { width: 2% !important; }
			.mw-32p { width: 32% !important; }
			.mw-49p { width: 49% !important; }
			.mw-50p { width: 50% !important; }
			.mw-100p { width: 100% !important; }

			.mmt-0 { margin-top: 0 !important; }
			}

			</style>
			</head>
			<body class="body" style="padding:0 !important; margin:0 auto !important; display:block !important; min-width:100% !important; width:100% !important; background:#e0f8ff; -webkit-text-size-adjust:none;">
			<center>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 0; padding: 0; width: 100%; height: 100%;" bgcolor="#e0f8ff" class="gwfw">
			<tr>
			<td style="margin: 0; padding: 0; width: 100%; height: 100%;" align="center" valign="top">
			<table width="600" border="0" cellspacing="0" cellpadding="0" class="m-shell">
			<tr>
			<td class="td" style="width:600px; min-width:600px; font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="mpx-10">

			<!-- Container -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="gradient pt-10" style="border-radius: 10px 10px 0 0; padding-top: 10px;" bgcolor="#f3189e">
			    <table width="100%" border="0" cellspacing="0" cellpadding="0" >
			        <tr>
			            <td style="border-radius: 10px 10px 0 0;" bgcolor="#ffffff">
			                <!-- Logo -->
			                <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                    <tr>
			                        <td class="img-center p-30 px-15" style="font-size:0pt; line-height:0pt; text-align:center; padding: 30px; padding-left: 15px; padding-right: 15px;">
			                            <a href="#" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/Logo.png" width="auto" height="43" border="0" alt="" /></a>
			                        </td>
			                    </tr>
			                </table>
			                <!-- Logo -->

			                <!-- Main -->
			                <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                    <tr>
			                        <td class="px-50 mpx-15" style="padding-left: 50px; padding-right: 50px;">
			                            <!-- Section - Intro -->
			                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                                <tr>
			                                    <td class="pb-50" style="padding-bottom: 50px;">
			                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">

			                                            <tr>
			                                                <td class="title-36 a-center pb-15" style="font-size:16px; line-height:20px; color:#282828; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; text-align:left; padding-bottom: 15px;">
			                                                    <strong>Dear</strong>
			                                                    <br/>
			                                                    <span class="c-purple" style="color:#9128df;">' . $userteam . ',</span>
			                                                    <br/><br/>
			                                                 <p align="justify">Your OTP (One Time Password) is given below,
			                                                    Please do not share this OTP with anyone. Believer11  never asks for OTP, CVV, PIN, Card Number over phone calls or messages.</p>
			                                                </td>
			                                            </tr>
			                                            <tr>
			                                                <td class="pb-30" style="padding-bottom: 30px;">
			                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                                                        <tr>
			<td class="title-22 a-center py-20 px-50 mpx-15" style="border-radius: 10px; border: 1px dashed #b4b4d4; font-size:22px; line-height:26px; color:#282828; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; text-align:center; padding-top: 20px; padding-bottom: 20px; padding-left: 50px; padding-right: 50px;" bgcolor="#e0f8ff">
			                                                                <strong>OTP : <span class="c-purple" style="color:#9128df;">' . $code . '</span></strong>
			                                                            </td>
			                                                        </tr>
			                                                    </table>
			                                                </td>
			                                            </tr>
			                                            <tr>
			                                                <td align="center">
			                                                    <!-- Button -->
			                                                    <table border="0" cellspacing="0" cellpadding="0" style="min-width: 200px;">

			</table>
			<!-- END Button -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			<!-- END Section - Intro -->
			</td>
			</tr>
			</table>
			<!-- END Main -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			<!-- END Container -->

			<!-- Footer -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="p-50 mpx-15" bgcolor="#019173" style="border-radius: 0 0 10px 10px; padding: 50px;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td align="center" class="pb-20" style="padding-bottom: 20px;">
			<!-- Socials -->
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.facebook.com/Believer11 " target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_facebook.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			    <td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://twitter.com/Believer11 " target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_twitter.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			<td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.instagram.com/Believer11 /" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_instagram.png" width="34" height="34" border="0" alt="" /></a>
			</td>

			<td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.pinterest.com/Believer11 /" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_pinterest.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			</tr>
			</table>
			<!-- END Socials -->
			</td>
			</tr>
			<tr>
			<td class="text-14 lh-24 a-center c-white l-white pb-20" style="font-size:14px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 24px; text-align:center; color:#ffffff;">
			<a href="tel:+17384796719" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">(+91) 9431763858</span></a> <a href="tel:+13697181973" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"></a>
			<br />
			<a href="mailto:care@Believer11 .com" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">care@Believer11 .com</span></a> - <a href="https://www.Believer11 .com" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">www.Believer11 .com</span></a>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table><!-- END Footer -->

			<!-- Bottom -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="text-12 lh-22 a-center c-grey- l-grey py-20" style="font-size:12px; color:#6e6e6e; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 22px; text-align:center; padding-top: 20px; padding-bottom: 20px;">This is system generated email, please do not reply.
			    </td>
			</tr>
			</table>											<!-- END Bottom -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</center>
			</body>
			</html>
				';
        return $html;
    }

    public static function panapprove_email($teamname)
    {
        $html = '';
        $html .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
			<head>
			<!--[if gte mso 9]>
			<xml>
			<o:OfficeDocumentSettings>
			<o:AllowPNG/>
			<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
			</xml>
			<![endif]-->
			<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
			<meta http-equiv="X-UA-Compatible" content="IE=edge" />
			<meta name="format-detection" content="date=no" />
			<meta name="format-detection" content="address=no" />
			<meta name="format-detection" content="telephone=no" />
			<meta name="x-apple-disable-message-reformatting" />
			<!--[if !mso]><!-->
			<link href="https://fonts.googleapis.com/css?family=PT+Sans:400,400i,700,700i&display=swap" rel="stylesheet" />
			<!--<![endif]-->
			<title>Believer11 -OTP</title>
			<!--[if gte mso 9]>
			<style type="text/css" media="all">
			sup { font-size: 100% !important; }
			</style>
			<![endif]-->
			<!-- body, html, table, thead, tbody, tr, td, div, a, span { font-family: Arial, sans-serif !important; } -->


			<style type="text/css" media="screen">
			body { padding:0 !important; margin:0 auto !important; display:block !important; min-width:100% !important; width:100% !important; background:#e0f8ff; -webkit-text-size-adjust:none }
			a { color:#f3189e; text-decoration:none }
			p { padding:0 !important; margin:0 !important }
			img { margin: 0 !important; -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ }

			a[x-apple-data-detectors] { color: inherit !important; text-decoration: inherit !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; }

			.btn-16 a { display: block; padding: 15px 35px; text-decoration: none; }
			.btn-20 a { display: block; padding: 15px 35px; text-decoration: none; }

			.l-white a { color: #ffffff; }
			.l-black a { color: #282828; }
			.l-pink a { color: #f3189e; }
			.l-grey a { color: #6e6e6e; }
			.l-purple a { color: #9128df; }

			.gradient { background: linear-gradient(to right, #019173 0%,#0CB0E0 100%); }

			.btn-secondary { border-radius: 10px; background: linear-gradient(to right, #019173 0%,#0CB0E0 100%); }


			/* Mobile styles */
			@media only screen and (max-device-width: 480px), only screen and (max-width: 480px) {
			.mpx-10 { padding-left: 10px !important; padding-right: 10px !important; }

			.mpx-15 { padding-left: 15px !important; padding-right: 15px !important; }

			u + .body .gwfw { width:100% !important; width:100vw !important; }

			.td,
			.m-shell { width: 100% !important; min-width: 100% !important; }

			.mt-left { text-align: left !important; }
			.mt-center { text-align: center !important; }
			.mt-right { text-align: right !important; }

			.me-left { margin-right: auto !important; }
			.me-center { margin: 0 auto !important; }
			.me-right { margin-left: auto !important; }

			.mh-auto { height: auto !important; }
			.mw-auto { width: auto !important; }

			.fluid-img img { width: 100% !important; max-width: 100% !important; height: auto !important; }

			.column,
			.column-top,
			.column-dir-top { float: left !important; width: 100% !important; display: block !important; }

			.m-hide { display: none !important; width: 0 !important; height: 0 !important; font-size: 0 !important; line-height: 0 !important; min-height: 0 !important; }
			.m-block { display: block !important; }

			.mw-15 { width: 15px !important; }

			.mw-2p { width: 2% !important; }
			.mw-32p { width: 32% !important; }
			.mw-49p { width: 49% !important; }
			.mw-50p { width: 50% !important; }
			.mw-100p { width: 100% !important; }

			.mmt-0 { margin-top: 0 !important; }
			}

			</style>
			</head>
			<body class="body" style="padding:0 !important; margin:0 auto !important; display:block !important; min-width:100% !important; width:100% !important; background:#e0f8ff; -webkit-text-size-adjust:none;">
			<center>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 0; padding: 0; width: 100%; height: 100%;" bgcolor="#e0f8ff" class="gwfw">
			<tr>
			<td style="margin: 0; padding: 0; width: 100%; height: 100%;" align="center" valign="top">
			<table width="600" border="0" cellspacing="0" cellpadding="0" class="m-shell">
			<tr>
			<td class="td" style="width:600px; min-width:600px; font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="mpx-10">

			<!-- Container -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="gradient pt-10" style="border-radius: 10px 10px 0 0; padding-top: 10px;" bgcolor="#f3189e">
			    <table width="100%" border="0" cellspacing="0" cellpadding="0" >
			        <tr>
			            <td style="border-radius: 10px 10px 0 0;" bgcolor="#ffffff">
			                <!-- Logo -->
			                <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                    <tr>
			                        <td class="img-center p-30 px-15" style="font-size:0pt; line-height:0pt; text-align:center; padding: 30px; padding-left: 15px; padding-right: 15px;">
			                            <a href="#" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/Logo.png" width="auto" height="43" border="0" alt="" /></a>
			                        </td>
			                    </tr>
			                </table>
			                <!-- Logo -->

			                <!-- Main -->
			                <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                    <tr>
			                        <td class="px-50 mpx-15" style="padding-left: 50px; padding-right: 50px;">
			                            <!-- Section - Intro -->
			                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                                <tr>
			                                    <td class="pb-50" style="padding-bottom: 50px;">
			                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">

			                                            <tr>
			                                                <td class="title-36 a-center pb-15" style="font-size:16px; line-height:20px; color:#282828; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; text-align:left; padding-bottom: 15px;">
			                                                    <strong>Dear</strong>
			                                                    <br/>
			                                                    <span class="c-purple" style="color:#9128df;">' . $teamname . ',</span>
			                                                </td>
			                                            </tr>
			                                            <tr>
			                                                <td class="py-35 px-50 mpx-15" style="border-radius: 10px; padding-top: 35px; padding-bottom: 35px; padding-left: 50px; padding-right: 50px;" bgcolor="#f4ecfa">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tbody><tr>
			<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: -90px;">
			<tbody><tr>
			<td class="img-center pb-20" style="font-size:0pt; line-height:0pt; text-align:center; padding-bottom: 20px;">
			</td>
			</tr>
			</tbody></table>
			</td>
			</tr>
			<tr>
			<td class="text-18 a-center c-purple pb-15" style="font-size:18px; line-height:22px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; text-align:center; color:#9128df; padding-bottom: 15px;">
			<strong>Congratulations!</strong>
			</td>
			</tr>
			<tr>
			<td class="text-16 lh-26 a-center c-black" style="font-size:16px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 26px; text-align:center; color:#282828;">
			<em>"Your permanent account number (PAN) has been successfully verified. Please verify your Bank Details for activation of withdrawals."</em>
			</td>
			</tr>
			</tbody></table>
			</td>
			                                            </tr>
			                                            <tr>
			                                                <td align="center">
			                                                    <!-- Button -->
			                                                    <table border="0" cellspacing="0" cellpadding="0" style="min-width: 200px;">

			</table>
			<!-- END Button -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			<!-- END Section - Intro -->
			</td>
			</tr>
			</table>
			<!-- END Main -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			<!-- END Container -->

			<!-- Footer -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="p-50 mpx-15" bgcolor="#019173" style="border-radius: 0 0 10px 10px; padding: 50px;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td align="center" class="pb-20" style="padding-bottom: 20px;">
			<!-- Socials -->
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.facebook.com/Believer11 " target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_facebook.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			    <td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://twitter.com/Believer11 " target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_twitter.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			<td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.instagram.com/Believer11 /" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_instagram.png" width="34" height="34" border="0" alt="" /></a>
			</td>

			<td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.pinterest.com/Believer11 /" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_pinterest.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			</tr>
			</table>
			<!-- END Socials -->
			</td>
			</tr>
			<tr>
			<td class="text-14 lh-24 a-center c-white l-white pb-20" style="font-size:14px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 24px; text-align:center; color:#ffffff;">
			<a href="tel:+17384796719" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">(+91) 9431763858</span></a> <a href="tel:+13697181973" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"></a>
			<br />
			<a href="mailto:care@Believer11 .com" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">care@Believer11 .com</span></a> - <a href="https://www.Believer11 .com" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">www.Believer11 .com</span></a>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table><!-- END Footer -->

			<!-- Bottom -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="text-12 lh-22 a-center c-grey- l-grey py-20" style="font-size:12px; color:#6e6e6e; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 22px; text-align:center; padding-top: 20px; padding-bottom: 20px;">This is system generated email, please do not reply.
			    </td>
			</tr>
			</table>											<!-- END Bottom -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</center>
			</body>
			</html>
				';
        return $html;
    }

    public static function panreject_email($teamname)
    {
        $html = '';
        $html .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
			<head>
			<!--[if gte mso 9]>
			<xml>
			<o:OfficeDocumentSettings>
			<o:AllowPNG/>
			<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
			</xml>
			<![endif]-->
			<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
			<meta http-equiv="X-UA-Compatible" content="IE=edge" />
			<meta name="format-detection" content="date=no" />
			<meta name="format-detection" content="address=no" />
			<meta name="format-detection" content="telephone=no" />
			<meta name="x-apple-disable-message-reformatting" />
			<!--[if !mso]><!-->
			<link href="https://fonts.googleapis.com/css?family=PT+Sans:400,400i,700,700i&display=swap" rel="stylesheet" />
			<!--<![endif]-->
			<title>Believer11 -OTP</title>
			<!--[if gte mso 9]>
			<style type="text/css" media="all">
			sup { font-size: 100% !important; }
			</style>
			<![endif]-->
			<!-- body, html, table, thead, tbody, tr, td, div, a, span { font-family: Arial, sans-serif !important; } -->


			<style type="text/css" media="screen">
			body { padding:0 !important; margin:0 auto !important; display:block !important; min-width:100% !important; width:100% !important; background:#e0f8ff; -webkit-text-size-adjust:none }
			a { color:#f3189e; text-decoration:none }
			p { padding:0 !important; margin:0 !important }
			img { margin: 0 !important; -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ }

			a[x-apple-data-detectors] { color: inherit !important; text-decoration: inherit !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; }

			.btn-16 a { display: block; padding: 15px 35px; text-decoration: none; }
			.btn-20 a { display: block; padding: 15px 35px; text-decoration: none; }

			.l-white a { color: #ffffff; }
			.l-black a { color: #282828; }
			.l-pink a { color: #f3189e; }
			.l-grey a { color: #6e6e6e; }
			.l-purple a { color: #9128df; }

			.gradient { background: linear-gradient(to right, #019173 0%,#0CB0E0 100%); }

			.btn-secondary { border-radius: 10px; background: linear-gradient(to right, #019173 0%,#0CB0E0 100%); }


			/* Mobile styles */
			@media only screen and (max-device-width: 480px), only screen and (max-width: 480px) {
			.mpx-10 { padding-left: 10px !important; padding-right: 10px !important; }

			.mpx-15 { padding-left: 15px !important; padding-right: 15px !important; }

			u + .body .gwfw { width:100% !important; width:100vw !important; }

			.td,
			.m-shell { width: 100% !important; min-width: 100% !important; }

			.mt-left { text-align: left !important; }
			.mt-center { text-align: center !important; }
			.mt-right { text-align: right !important; }

			.me-left { margin-right: auto !important; }
			.me-center { margin: 0 auto !important; }
			.me-right { margin-left: auto !important; }

			.mh-auto { height: auto !important; }
			.mw-auto { width: auto !important; }

			.fluid-img img { width: 100% !important; max-width: 100% !important; height: auto !important; }

			.column,
			.column-top,
			.column-dir-top { float: left !important; width: 100% !important; display: block !important; }

			.m-hide { display: none !important; width: 0 !important; height: 0 !important; font-size: 0 !important; line-height: 0 !important; min-height: 0 !important; }
			.m-block { display: block !important; }

			.mw-15 { width: 15px !important; }

			.mw-2p { width: 2% !important; }
			.mw-32p { width: 32% !important; }
			.mw-49p { width: 49% !important; }
			.mw-50p { width: 50% !important; }
			.mw-100p { width: 100% !important; }

			.mmt-0 { margin-top: 0 !important; }
			}

			</style>
			</head>
			<body class="body" style="padding:0 !important; margin:0 auto !important; display:block !important; min-width:100% !important; width:100% !important; background:#e0f8ff; -webkit-text-size-adjust:none;">
			<center>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 0; padding: 0; width: 100%; height: 100%;" bgcolor="#e0f8ff" class="gwfw">
			<tr>
			<td style="margin: 0; padding: 0; width: 100%; height: 100%;" align="center" valign="top">
			<table width="600" border="0" cellspacing="0" cellpadding="0" class="m-shell">
			<tr>
			<td class="td" style="width:600px; min-width:600px; font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="mpx-10">

			<!-- Container -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="gradient pt-10" style="border-radius: 10px 10px 0 0; padding-top: 10px;" bgcolor="#f3189e">
			    <table width="100%" border="0" cellspacing="0" cellpadding="0" >
			        <tr>
			            <td style="border-radius: 10px 10px 0 0;" bgcolor="#ffffff">
			                <!-- Logo -->
			                <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                    <tr>
			                        <td class="img-center p-30 px-15" style="font-size:0pt; line-height:0pt; text-align:center; padding: 30px; padding-left: 15px; padding-right: 15px;">
			                            <a href="#" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/Logo.png" width="auto" height="43" border="0" alt="" /></a>
			                        </td>
			                    </tr>
			                </table>
			                <!-- Logo -->

			                <!-- Main -->
			                <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                    <tr>
			                        <td class="px-50 mpx-15" style="padding-left: 50px; padding-right: 50px;">
			                            <!-- Section - Intro -->
			                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                                <tr>
			                                    <td class="pb-50" style="padding-bottom: 50px;">
			                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">

			                                            <tr>
			                                                <td class="title-36 a-center pb-15" style="font-size:16px; line-height:20px; color:#282828; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; text-align:left; padding-bottom: 15px;">
			                                                    <strong>Dear</strong>
			                                                    <br/>
			                                                    <span class="c-purple" style="color:#9128df;">' . $teamname . ',</span>
			                                                </td>
			                                            </tr>
			                                            <tr>
			                                                <td class="py-35 px-50 mpx-15" style="border-radius: 10px; padding-top: 35px; padding-bottom: 35px; padding-left: 50px; padding-right: 50px;" bgcolor="#f4ecfa">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tbody><tr>
			<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: -90px;">
			<tbody><tr>
			<td class="img-center pb-20" style="font-size:0pt; line-height:0pt; text-align:center; padding-bottom: 20px;">
			</td>
			</tr>
			</tbody></table>
			</td>
			</tr>
			<tr>
			<td class="text-18 a-center c-purple pb-15" style="font-size:18px; line-height:22px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; text-align:center; color:#e91e63; padding-bottom: 15px;">
			<strong>Oh-no!</strong>
			</td>
			</tr>
			<tr>
			<td class="text-16 lh-26 a-center c-black" style="font-size:16px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 26px; text-align:center; color:#f44336;">
			<em>"Your permanent account number (PAN) verification has been failed. Please retry."</em>
			</td>
			</tr>
			</tbody></table>
			</td>
			                                            </tr>
			                                            <tr>
			                                                <td align="center">
			                                                    <!-- Button -->
			                                                    <table border="0" cellspacing="0" cellpadding="0" style="min-width: 200px;">

			</table>
			<!-- END Button -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			<!-- END Section - Intro -->
			</td>
			</tr>
			</table>
			<!-- END Main -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			<!-- END Container -->

			<!-- Footer -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="p-50 mpx-15" bgcolor="#019173" style="border-radius: 0 0 10px 10px; padding: 50px;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td align="center" class="pb-20" style="padding-bottom: 20px;">
			<!-- Socials -->
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.facebook.com/Believer11 " target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_facebook.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			    <td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://twitter.com/Believer11 " target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_twitter.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			<td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.instagram.com/Believer11 /" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_instagram.png" width="34" height="34" border="0" alt="" /></a>
			</td>

			<td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.pinterest.com/Believer11 /" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_pinterest.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			</tr>
			</table>
			<!-- END Socials -->
			</td>
			</tr>
			<tr>
			<td class="text-14 lh-24 a-center c-white l-white pb-20" style="font-size:14px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 24px; text-align:center; color:#ffffff;">
			<a href="tel:+17384796719" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">(+91) 9431763858</span></a> <a href="tel:+13697181973" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"></a>
			<br />
			<a href="mailto:care@Believer11 .com" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">care@Believer11 .com</span></a> - <a href="https://www.Believer11 .com" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">www.Believer11 .com</span></a>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table><!-- END Footer -->

			<!-- Bottom -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="text-12 lh-22 a-center c-grey- l-grey py-20" style="font-size:12px; color:#6e6e6e; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 22px; text-align:center; padding-top: 20px; padding-bottom: 20px;">This is system generated email, please do not reply.
			    </td>
			</tr>
			</table>											<!-- END Bottom -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</center>
			</body>
			</html>
				';
        return $html;
    }

    public static function bankapprove_email($teamname)
    {
        $html = '';
        $html .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
			<head>
			<!--[if gte mso 9]>
			<xml>
			<o:OfficeDocumentSettings>
			<o:AllowPNG/>
			<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
			</xml>
			<![endif]-->
			<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
			<meta http-equiv="X-UA-Compatible" content="IE=edge" />
			<meta name="format-detection" content="date=no" />
			<meta name="format-detection" content="address=no" />
			<meta name="format-detection" content="telephone=no" />
			<meta name="x-apple-disable-message-reformatting" />
			<!--[if !mso]><!-->
			<link href="https://fonts.googleapis.com/css?family=PT+Sans:400,400i,700,700i&display=swap" rel="stylesheet" />
			<!--<![endif]-->
			<title>Believer11 -OTP</title>
			<!--[if gte mso 9]>
			<style type="text/css" media="all">
			sup { font-size: 100% !important; }
			</style>
			<![endif]-->
			<!-- body, html, table, thead, tbody, tr, td, div, a, span { font-family: Arial, sans-serif !important; } -->


			<style type="text/css" media="screen">
			body { padding:0 !important; margin:0 auto !important; display:block !important; min-width:100% !important; width:100% !important; background:#e0f8ff; -webkit-text-size-adjust:none }
			a { color:#f3189e; text-decoration:none }
			p { padding:0 !important; margin:0 !important }
			img { margin: 0 !important; -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ }

			a[x-apple-data-detectors] { color: inherit !important; text-decoration: inherit !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; }

			.btn-16 a { display: block; padding: 15px 35px; text-decoration: none; }
			.btn-20 a { display: block; padding: 15px 35px; text-decoration: none; }

			.l-white a { color: #ffffff; }
			.l-black a { color: #282828; }
			.l-pink a { color: #f3189e; }
			.l-grey a { color: #6e6e6e; }
			.l-purple a { color: #9128df; }

			.gradient { background: linear-gradient(to right, #019173 0%,#0CB0E0 100%); }

			.btn-secondary { border-radius: 10px; background: linear-gradient(to right, #019173 0%,#0CB0E0 100%); }


			/* Mobile styles */
			@media only screen and (max-device-width: 480px), only screen and (max-width: 480px) {
			.mpx-10 { padding-left: 10px !important; padding-right: 10px !important; }

			.mpx-15 { padding-left: 15px !important; padding-right: 15px !important; }

			u + .body .gwfw { width:100% !important; width:100vw !important; }

			.td,
			.m-shell { width: 100% !important; min-width: 100% !important; }

			.mt-left { text-align: left !important; }
			.mt-center { text-align: center !important; }
			.mt-right { text-align: right !important; }

			.me-left { margin-right: auto !important; }
			.me-center { margin: 0 auto !important; }
			.me-right { margin-left: auto !important; }

			.mh-auto { height: auto !important; }
			.mw-auto { width: auto !important; }

			.fluid-img img { width: 100% !important; max-width: 100% !important; height: auto !important; }

			.column,
			.column-top,
			.column-dir-top { float: left !important; width: 100% !important; display: block !important; }

			.m-hide { display: none !important; width: 0 !important; height: 0 !important; font-size: 0 !important; line-height: 0 !important; min-height: 0 !important; }
			.m-block { display: block !important; }

			.mw-15 { width: 15px !important; }

			.mw-2p { width: 2% !important; }
			.mw-32p { width: 32% !important; }
			.mw-49p { width: 49% !important; }
			.mw-50p { width: 50% !important; }
			.mw-100p { width: 100% !important; }

			.mmt-0 { margin-top: 0 !important; }
			}

			</style>
			</head>
			<body class="body" style="padding:0 !important; margin:0 auto !important; display:block !important; min-width:100% !important; width:100% !important; background:#e0f8ff; -webkit-text-size-adjust:none;">
			<center>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 0; padding: 0; width: 100%; height: 100%;" bgcolor="#e0f8ff" class="gwfw">
			<tr>
			<td style="margin: 0; padding: 0; width: 100%; height: 100%;" align="center" valign="top">
			<table width="600" border="0" cellspacing="0" cellpadding="0" class="m-shell">
			<tr>
			<td class="td" style="width:600px; min-width:600px; font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="mpx-10">

			<!-- Container -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="gradient pt-10" style="border-radius: 10px 10px 0 0; padding-top: 10px;" bgcolor="#f3189e">
			    <table width="100%" border="0" cellspacing="0" cellpadding="0" >
			        <tr>
			            <td style="border-radius: 10px 10px 0 0;" bgcolor="#ffffff">
			                <!-- Logo -->
			                <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                    <tr>
			                        <td class="img-center p-30 px-15" style="font-size:0pt; line-height:0pt; text-align:center; padding: 30px; padding-left: 15px; padding-right: 15px;">
			                            <a href="#" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/Logo.png" width="auto" height="43" border="0" alt="" /></a>
			                        </td>
			                    </tr>
			                </table>
			                <!-- Logo -->

			                <!-- Main -->
			                <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                    <tr>
			                        <td class="px-50 mpx-15" style="padding-left: 50px; padding-right: 50px;">
			                            <!-- Section - Intro -->
			                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                                <tr>
			                                    <td class="pb-50" style="padding-bottom: 50px;">
			                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">

			                                            <tr>
			                                                <td class="title-36 a-center pb-15" style="font-size:16px; line-height:20px; color:#282828; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; text-align:left; padding-bottom: 15px;">
			                                                    <strong>Dear</strong>
			                                                    <br/>
			                                                    <span class="c-purple" style="color:#9128df;">' . $teamname . ',</span>
			                                                </td>
			                                            </tr>
			                                            <tr>
			                                                <td class="py-35 px-50 mpx-15" style="border-radius: 10px; padding-top: 35px; padding-bottom: 35px; padding-left: 50px; padding-right: 50px;" bgcolor="#f4ecfa">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tbody><tr>
			<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: -90px;">
			<tbody><tr>
			<td class="img-center pb-20" style="font-size:0pt; line-height:0pt; text-align:center; padding-bottom: 20px;">
			</td>
			</tr>
			</tbody></table>
			</td>
			</tr>
			<tr>
			<td class="text-18 a-center c-purple pb-15" style="font-size:18px; line-height:22px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; text-align:center; color:#9128df; padding-bottom: 15px;">
			<strong>Congratulations!</strong>
			</td>
			</tr>
			<tr>
			<td class="text-16 lh-26 a-center c-black" style="font-size:16px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 26px; text-align:center; color:#282828;">
			<em>"Your Bank account details have been successfully verified. Wohooo!!! You may now withdraw your winnings."</em>
			</td>
			</tr>
			</tbody></table>
			</td>
			                                            </tr>
			                                            <tr>
			                                                <td align="center">
			                                                    <!-- Button -->
			                                                    <table border="0" cellspacing="0" cellpadding="0" style="min-width: 200px;">

			</table>
			<!-- END Button -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			<!-- END Section - Intro -->
			</td>
			</tr>
			</table>
			<!-- END Main -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			<!-- END Container -->

			<!-- Footer -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="p-50 mpx-15" bgcolor="#019173" style="border-radius: 0 0 10px 10px; padding: 50px;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td align="center" class="pb-20" style="padding-bottom: 20px;">
			<!-- Socials -->
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.facebook.com/Believer11 " target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_facebook.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			    <td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://twitter.com/Believer11 " target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_twitter.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			<td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.instagram.com/Believer11 /" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_instagram.png" width="34" height="34" border="0" alt="" /></a>
			</td>

			<td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.pinterest.com/Believer11 /" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_pinterest.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			</tr>
			</table>
			<!-- END Socials -->
			</td>
			</tr>
			<tr>
			<td class="text-14 lh-24 a-center c-white l-white pb-20" style="font-size:14px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 24px; text-align:center; color:#ffffff;">
			<a href="tel:+17384796719" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">(+91) 9431763858</span></a> <a href="tel:+13697181973" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"></a>
			<br />
			<a href="mailto:care@Believer11 .com" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">care@Believer11 .com</span></a> - <a href="https://www.Believer11 .com" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">www.Believer11 .com</span></a>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table><!-- END Footer -->

			<!-- Bottom -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="text-12 lh-22 a-center c-grey- l-grey py-20" style="font-size:12px; color:#6e6e6e; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 22px; text-align:center; padding-top: 20px; padding-bottom: 20px;">This is system generated email, please do not reply.
			    </td>
			</tr>
			</table>											<!-- END Bottom -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</center>
			</body>
			</html>
				';
        return $html;
    }

    public static function bankrejected_email($teamname)
    {
        $html = '';
        $html .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
			<head>
			<!--[if gte mso 9]>
			<xml>
			<o:OfficeDocumentSettings>
			<o:AllowPNG/>
			<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
			</xml>
			<![endif]-->
			<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
			<meta http-equiv="X-UA-Compatible" content="IE=edge" />
			<meta name="format-detection" content="date=no" />
			<meta name="format-detection" content="address=no" />
			<meta name="format-detection" content="telephone=no" />
			<meta name="x-apple-disable-message-reformatting" />
			<!--[if !mso]><!-->
			<link href="https://fonts.googleapis.com/css?family=PT+Sans:400,400i,700,700i&display=swap" rel="stylesheet" />
			<!--<![endif]-->
			<title>Believer11 -OTP</title>
			<!--[if gte mso 9]>
			<style type="text/css" media="all">
			sup { font-size: 100% !important; }
			</style>
			<![endif]-->
			<!-- body, html, table, thead, tbody, tr, td, div, a, span { font-family: Arial, sans-serif !important; } -->


			<style type="text/css" media="screen">
			body { padding:0 !important; margin:0 auto !important; display:block !important; min-width:100% !important; width:100% !important; background:#e0f8ff; -webkit-text-size-adjust:none }
			a { color:#f3189e; text-decoration:none }
			p { padding:0 !important; margin:0 !important }
			img { margin: 0 !important; -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ }

			a[x-apple-data-detectors] { color: inherit !important; text-decoration: inherit !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; }

			.btn-16 a { display: block; padding: 15px 35px; text-decoration: none; }
			.btn-20 a { display: block; padding: 15px 35px; text-decoration: none; }

			.l-white a { color: #ffffff; }
			.l-black a { color: #282828; }
			.l-pink a { color: #f3189e; }
			.l-grey a { color: #6e6e6e; }
			.l-purple a { color: #9128df; }

			.gradient { background: linear-gradient(to right, #019173 0%,#0CB0E0 100%); }

			.btn-secondary { border-radius: 10px; background: linear-gradient(to right, #019173 0%,#0CB0E0 100%); }


			/* Mobile styles */
			@media only screen and (max-device-width: 480px), only screen and (max-width: 480px) {
			.mpx-10 { padding-left: 10px !important; padding-right: 10px !important; }

			.mpx-15 { padding-left: 15px !important; padding-right: 15px !important; }

			u + .body .gwfw { width:100% !important; width:100vw !important; }

			.td,
			.m-shell { width: 100% !important; min-width: 100% !important; }

			.mt-left { text-align: left !important; }
			.mt-center { text-align: center !important; }
			.mt-right { text-align: right !important; }

			.me-left { margin-right: auto !important; }
			.me-center { margin: 0 auto !important; }
			.me-right { margin-left: auto !important; }

			.mh-auto { height: auto !important; }
			.mw-auto { width: auto !important; }

			.fluid-img img { width: 100% !important; max-width: 100% !important; height: auto !important; }

			.column,
			.column-top,
			.column-dir-top { float: left !important; width: 100% !important; display: block !important; }

			.m-hide { display: none !important; width: 0 !important; height: 0 !important; font-size: 0 !important; line-height: 0 !important; min-height: 0 !important; }
			.m-block { display: block !important; }

			.mw-15 { width: 15px !important; }

			.mw-2p { width: 2% !important; }
			.mw-32p { width: 32% !important; }
			.mw-49p { width: 49% !important; }
			.mw-50p { width: 50% !important; }
			.mw-100p { width: 100% !important; }

			.mmt-0 { margin-top: 0 !important; }
			}

			</style>
			</head>
			<body class="body" style="padding:0 !important; margin:0 auto !important; display:block !important; min-width:100% !important; width:100% !important; background:#e0f8ff; -webkit-text-size-adjust:none;">
			<center>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 0; padding: 0; width: 100%; height: 100%;" bgcolor="#e0f8ff" class="gwfw">
			<tr>
			<td style="margin: 0; padding: 0; width: 100%; height: 100%;" align="center" valign="top">
			<table width="600" border="0" cellspacing="0" cellpadding="0" class="m-shell">
			<tr>
			<td class="td" style="width:600px; min-width:600px; font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="mpx-10">

			<!-- Container -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="gradient pt-10" style="border-radius: 10px 10px 0 0; padding-top: 10px;" bgcolor="#f3189e">
			    <table width="100%" border="0" cellspacing="0" cellpadding="0" >
			        <tr>
			            <td style="border-radius: 10px 10px 0 0;" bgcolor="#ffffff">
			                <!-- Logo -->
			                <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                    <tr>
			                        <td class="img-center p-30 px-15" style="font-size:0pt; line-height:0pt; text-align:center; padding: 30px; padding-left: 15px; padding-right: 15px;">
			                            <a href="#" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/Logo.png" width="auto" height="43" border="0" alt="" /></a>
			                        </td>
			                    </tr>
			                </table>
			                <!-- Logo -->

			                <!-- Main -->
			                <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                    <tr>
			                        <td class="px-50 mpx-15" style="padding-left: 50px; padding-right: 50px;">
			                            <!-- Section - Intro -->
			                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                                <tr>
			                                    <td class="pb-50" style="padding-bottom: 50px;">
			                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">

			                                            <tr>
			                                                <td class="title-36 a-center pb-15" style="font-size:16px; line-height:20px; color:#282828; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; text-align:left; padding-bottom: 15px;">
			                                                    <strong>Dear</strong>
			                                                    <br/>
			                                                    <span class="c-purple" style="color:#9128df;">' . $teamname . ',</span>
			                                                </td>
			                                            </tr>
			                                            <tr>
			                                                <td class="py-35 px-50 mpx-15" style="border-radius: 10px; padding-top: 35px; padding-bottom: 35px; padding-left: 50px; padding-right: 50px;" bgcolor="#f4ecfa">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tbody><tr>
			<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: -90px;">
			<tbody><tr>
			<td class="img-center pb-20" style="font-size:0pt; line-height:0pt; text-align:center; padding-bottom: 20px;">
			</td>
			</tr>
			</tbody></table>
			</td>
			</tr>
			<tr>
			<td class="text-18 a-center c-purple pb-15" style="font-size:18px; line-height:22px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; text-align:center; color:#e91e63; padding-bottom: 15px;">
			<strong>Oh-no!</strong>
			</td>
			</tr>
			<tr>
			<td class="text-16 lh-26 a-center c-black" style="font-size:16px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 26px; text-align:center; color:#f44336;">
			<em>"Your Bank Account verification has been failed. Please retry."</em>
			</td>
			</tr>
			</tbody></table>
			</td>
			                                            </tr>
			                                            <tr>
			                                                <td align="center">
			                                                    <!-- Button -->
			                                                    <table border="0" cellspacing="0" cellpadding="0" style="min-width: 200px;">

			</table>
			<!-- END Button -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			<!-- END Section - Intro -->
			</td>
			</tr>
			</table>
			<!-- END Main -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			<!-- END Container -->

			<!-- Footer -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="p-50 mpx-15" bgcolor="#019173" style="border-radius: 0 0 10px 10px; padding: 50px;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td align="center" class="pb-20" style="padding-bottom: 20px;">
			<!-- Socials -->
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.facebook.com/Believer11 " target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_facebook.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			    <td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://twitter.com/Believer11 " target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_twitter.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			<td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.instagram.com/Believer11 /" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_instagram.png" width="34" height="34" border="0" alt="" /></a>
			</td>

			<td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.pinterest.com/Believer11 /" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_pinterest.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			</tr>
			</table>
			<!-- END Socials -->
			</td>
			</tr>
			<tr>
			<td class="text-14 lh-24 a-center c-white l-white pb-20" style="font-size:14px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 24px; text-align:center; color:#ffffff;">
			<a href="tel:+17384796719" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">(+91) 9431763858</span></a> <a href="tel:+13697181973" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"></a>
			<br />
			<a href="mailto:care@Believer11 .com" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">care@Believer11 .com</span></a> - <a href="https://www.Believer11 .com" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">www.Believer11 .com</span></a>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table><!-- END Footer -->

			<!-- Bottom -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="text-12 lh-22 a-center c-grey- l-grey py-20" style="font-size:12px; color:#6e6e6e; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 22px; text-align:center; padding-top: 20px; padding-bottom: 20px;">This is system generated email, please do not reply.
			    </td>
			</tr>
			</table>											<!-- END Bottom -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</center>
			</body>
			</html>
				';
        return $html;
    }

    public static function helpdesk_email($input)
    {
        $html = '';
        $html .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
			<head>
			<!--[if gte mso 9]>
			<xml>
			<o:OfficeDocumentSettings>
			<o:AllowPNG/>
			<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
			</xml>
			<![endif]-->
			<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
			<meta http-equiv="X-UA-Compatible" content="IE=edge" />
			<meta name="format-detection" content="date=no" />
			<meta name="format-detection" content="address=no" />
			<meta name="format-detection" content="telephone=no" />
			<meta name="x-apple-disable-message-reformatting" />
			<!--[if !mso]><!-->
			<link href="https://fonts.googleapis.com/css?family=PT+Sans:400,400i,700,700i&display=swap" rel="stylesheet" />
			<!--<![endif]-->
			<title>Believer11 -OTP</title>
			<!--[if gte mso 9]>
			<style type="text/css" media="all">
			sup { font-size: 100% !important; }
			</style>
			<![endif]-->
			<!-- body, html, table, thead, tbody, tr, td, div, a, span { font-family: Arial, sans-serif !important; } -->


			<style type="text/css" media="screen">
			body { padding:0 !important; margin:0 auto !important; display:block !important; min-width:100% !important; width:100% !important; background:#e0f8ff; -webkit-text-size-adjust:none }
			a { color:#f3189e; text-decoration:none }
			p { padding:0 !important; margin:0 !important }
			img { margin: 0 !important; -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ }

			a[x-apple-data-detectors] { color: inherit !important; text-decoration: inherit !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; }

			.btn-16 a { display: block; padding: 15px 35px; text-decoration: none; }
			.btn-20 a { display: block; padding: 15px 35px; text-decoration: none; }

			.l-white a { color: #ffffff; }
			.l-black a { color: #282828; }
			.l-pink a { color: #f3189e; }
			.l-grey a { color: #6e6e6e; }
			.l-purple a { color: #9128df; }

			.gradient { background: linear-gradient(to right, #019173 0%,#0CB0E0 100%); }

			.btn-secondary { border-radius: 10px; background: linear-gradient(to right, #019173 0%,#0CB0E0 100%); }


			/* Mobile styles */
			@media only screen and (max-device-width: 480px), only screen and (max-width: 480px) {
			.mpx-10 { padding-left: 10px !important; padding-right: 10px !important; }

			.mpx-15 { padding-left: 15px !important; padding-right: 15px !important; }

			u + .body .gwfw { width:100% !important; width:100vw !important; }

			.td,
			.m-shell { width: 100% !important; min-width: 100% !important; }

			.mt-left { text-align: left !important; }
			.mt-center { text-align: center !important; }
			.mt-right { text-align: right !important; }

			.me-left { margin-right: auto !important; }
			.me-center { margin: 0 auto !important; }
			.me-right { margin-left: auto !important; }

			.mh-auto { height: auto !important; }
			.mw-auto { width: auto !important; }

			.fluid-img img { width: 100% !important; max-width: 100% !important; height: auto !important; }

			.column,
			.column-top,
			.column-dir-top { float: left !important; width: 100% !important; display: block !important; }

			.m-hide { display: none !important; width: 0 !important; height: 0 !important; font-size: 0 !important; line-height: 0 !important; min-height: 0 !important; }
			.m-block { display: block !important; }

			.mw-15 { width: 15px !important; }

			.mw-2p { width: 2% !important; }
			.mw-32p { width: 32% !important; }
			.mw-49p { width: 49% !important; }
			.mw-50p { width: 50% !important; }
			.mw-100p { width: 100% !important; }

			.mmt-0 { margin-top: 0 !important; }
			}

			</style>
			</head>
			<body class="body" style="padding:0 !important; margin:0 auto !important; display:block !important; min-width:100% !important; width:100% !important; background:#e0f8ff; -webkit-text-size-adjust:none;">
			<center>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 0; padding: 0; width: 100%; height: 100%;" bgcolor="#e0f8ff" class="gwfw">
			<tr>
			<td style="margin: 0; padding: 0; width: 100%; height: 100%;" align="center" valign="top">
			<table width="600" border="0" cellspacing="0" cellpadding="0" class="m-shell">
			<tr>
			<td class="td" style="width:600px; min-width:600px; font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="mpx-10">

			<!-- Container -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="gradient pt-10" style="border-radius: 10px 10px 0 0; padding-top: 10px;" bgcolor="#f3189e">
			    <table width="100%" border="0" cellspacing="0" cellpadding="0" >
			        <tr>
			            <td style="border-radius: 10px 10px 0 0;" bgcolor="#ffffff">
			                <!-- Logo -->
			                <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                    <tr>
			                        <td class="img-center p-30 px-15" style="font-size:0pt; line-height:0pt; text-align:center; padding: 30px; padding-left: 15px; padding-right: 15px;">
			                            <a href="#" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/Logo.png" width="auto" height="43" border="0" alt="" /></a>
			                        </td>
			                    </tr>
			                </table>
			                <!-- Logo -->

			                <!-- Main -->
			                <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                    <tr>
			                        <td class="px-50 mpx-15" style="padding-left: 50px; padding-right: 50px;">
			                            <!-- Section - Intro -->
			                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                                <tr>
			                                    <td class="pb-50" style="padding-bottom: 50px;">
			                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">

			                                            <tr>
			                                                <td class="title-36 a-center pb-15" style="font-size:16px; line-height:20px; color:#282828; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; text-align:left; padding-bottom: 15px;">
			                                                    <strong>Name: &nbsp;&nbsp;</strong>
			    <span class="c-purple" style="color:#9128df;">' . $input['fname'] . '</span>
			                                                    <br/><br/>
			                                                    <strong>Email: &nbsp;&nbsp;</strong>
			    <span class="c-purple" style="color:#9128df;">' . $input['email'] . '</span>
			                                                    <br/><br/>
			                                                    <strong>Contact Number: &nbsp;&nbsp;</strong>
			    <span class="c-purple" style="color:#9128df;">' . $input['contact'] . '</span>
			                                                    <br/><br/>
			                                                    <strong>Reason of Enquiry: &nbsp;&nbsp;</strong>
			    <span class="c-purple" style="color:#9128df;">' . $input['reason'] . '</span>
			                                                </td>
			                                            </tr>
			                                            <tr>
			                                                <td class="py-35 px-50 mpx-15" style="border-radius: 10px; padding-top: 35px; padding-bottom: 35px; padding-left: 50px; padding-right: 50px;" bgcolor="#f4ecfa">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tbody><tr>
			<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: -90px;">
			<tbody><tr>
			<td class="img-center pb-20" style="font-size:0pt; line-height:0pt; text-align:center; padding-bottom: 20px;">
			</td>
			</tr>
			</tbody></table>
			</td>
			</tr>
			<tr>
			<td class="text-18 a-center c-purple pb-15" style="font-size:18px; line-height:22px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; text-align:center; color:#9128df; padding-bottom: 15px;">
			<strong>Message</strong>
			</td>
			</tr>
			<tr>
			<td class="text-16 lh-26 a-center c-black" style="font-size:16px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 26px; text-align:center; color:#282828;">
			<em>"' . $input['message'] . '"</em>
			</td>
			</tr>
			</tbody></table>
			</td>
			                                            </tr>
			                                            <tr>
			                                                <td align="center">
			                                                    <!-- Button -->
			                                                    <table border="0" cellspacing="0" cellpadding="0" style="min-width: 200px;">

			</table>
			<!-- END Button -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			<!-- END Section - Intro -->
			</td>
			</tr>
			</table>
			<!-- END Main -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			<!-- END Container -->

			<!-- Footer -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="p-50 mpx-15" bgcolor="#019173" style="border-radius: 0 0 10px 10px; padding: 50px;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td align="center" class="pb-20" style="padding-bottom: 20px;">
			<!-- Socials -->
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.facebook.com/Believer11 " target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_facebook.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			    <td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://twitter.com/Believer11 " target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_twitter.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			<td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.instagram.com/Believer11 /" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_instagram.png" width="34" height="34" border="0" alt="" /></a>
			</td>

			<td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.pinterest.com/Believer11 /" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_pinterest.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			</tr>
			</table>
			<!-- END Socials -->
			</td>
			</tr>
			<tr>
			<td class="text-14 lh-24 a-center c-white l-white pb-20" style="font-size:14px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 24px; text-align:center; color:#ffffff;">
			<a href="tel:+17384796719" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">(+91) 9431763858</span></a> <a href="tel:+13697181973" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"></a>
			<br />
			<a href="mailto:care@Believer11 .com" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">care@Believer11 .com</span></a> - <a href="https://www.Believer11 .com" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">www.Believer11 .com</span></a>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table><!-- END Footer -->

			<!-- Bottom -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="text-12 lh-22 a-center c-grey- l-grey py-20" style="font-size:12px; color:#6e6e6e; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 22px; text-align:center; padding-top: 20px; padding-bottom: 20px;">This is system generated email, please do not reply.
			    </td>
			</tr>
			</table>											<!-- END Bottom -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</center>
			</body>
			</html>
				';
        return $html;
    }

    public static function withdrawApprove_email($teamname, $amt)
    {
        $html = '';
        $html .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
			<head>
			<!--[if gte mso 9]>
			<xml>
			<o:OfficeDocumentSettings>
			<o:AllowPNG/>
			<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
			</xml>
			<![endif]-->
			<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
			<meta http-equiv="X-UA-Compatible" content="IE=edge" />
			<meta name="format-detection" content="date=no" />
			<meta name="format-detection" content="address=no" />
			<meta name="format-detection" content="telephone=no" />
			<meta name="x-apple-disable-message-reformatting" />
			<!--[if !mso]><!-->
			<link href="https://fonts.googleapis.com/css?family=PT+Sans:400,400i,700,700i&display=swap" rel="stylesheet" />
			<!--<![endif]-->
			<title>Believer11 -OTP</title>
			<!--[if gte mso 9]>
			<style type="text/css" media="all">
			sup { font-size: 100% !important; }
			</style>
			<![endif]-->
			<!-- body, html, table, thead, tbody, tr, td, div, a, span { font-family: Arial, sans-serif !important; } -->


			<style type="text/css" media="screen">
			body { padding:0 !important; margin:0 auto !important; display:block !important; min-width:100% !important; width:100% !important; background:#e0f8ff; -webkit-text-size-adjust:none }
			a { color:#f3189e; text-decoration:none }
			p { padding:0 !important; margin:0 !important }
			img { margin: 0 !important; -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ }

			a[x-apple-data-detectors] { color: inherit !important; text-decoration: inherit !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; }

			.btn-16 a { display: block; padding: 15px 35px; text-decoration: none; }
			.btn-20 a { display: block; padding: 15px 35px; text-decoration: none; }

			.l-white a { color: #ffffff; }
			.l-black a { color: #282828; }
			.l-pink a { color: #f3189e; }
			.l-grey a { color: #6e6e6e; }
			.l-purple a { color: #9128df; }

			.gradient { background: linear-gradient(to right, #019173 0%,#0CB0E0 100%); }

			.btn-secondary { border-radius: 10px; background: linear-gradient(to right, #019173 0%,#0CB0E0 100%); }


			/* Mobile styles */
			@media only screen and (max-device-width: 480px), only screen and (max-width: 480px) {
			.mpx-10 { padding-left: 10px !important; padding-right: 10px !important; }

			.mpx-15 { padding-left: 15px !important; padding-right: 15px !important; }

			u + .body .gwfw { width:100% !important; width:100vw !important; }

			.td,
			.m-shell { width: 100% !important; min-width: 100% !important; }

			.mt-left { text-align: left !important; }
			.mt-center { text-align: center !important; }
			.mt-right { text-align: right !important; }

			.me-left { margin-right: auto !important; }
			.me-center { margin: 0 auto !important; }
			.me-right { margin-left: auto !important; }

			.mh-auto { height: auto !important; }
			.mw-auto { width: auto !important; }

			.fluid-img img { width: 100% !important; max-width: 100% !important; height: auto !important; }

			.column,
			.column-top,
			.column-dir-top { float: left !important; width: 100% !important; display: block !important; }

			.m-hide { display: none !important; width: 0 !important; height: 0 !important; font-size: 0 !important; line-height: 0 !important; min-height: 0 !important; }
			.m-block { display: block !important; }

			.mw-15 { width: 15px !important; }

			.mw-2p { width: 2% !important; }
			.mw-32p { width: 32% !important; }
			.mw-49p { width: 49% !important; }
			.mw-50p { width: 50% !important; }
			.mw-100p { width: 100% !important; }

			.mmt-0 { margin-top: 0 !important; }
			}

			</style>
			</head>
			<body class="body" style="padding:0 !important; margin:0 auto !important; display:block !important; min-width:100% !important; width:100% !important; background:#e0f8ff; -webkit-text-size-adjust:none;">
			<center>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 0; padding: 0; width: 100%; height: 100%;" bgcolor="#e0f8ff" class="gwfw">
			<tr>
			<td style="margin: 0; padding: 0; width: 100%; height: 100%;" align="center" valign="top">
			<table width="600" border="0" cellspacing="0" cellpadding="0" class="m-shell">
			<tr>
			<td class="td" style="width:600px; min-width:600px; font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="mpx-10">

			<!-- Container -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="gradient pt-10" style="border-radius: 10px 10px 0 0; padding-top: 10px;" bgcolor="#f3189e">
			    <table width="100%" border="0" cellspacing="0" cellpadding="0" >
			        <tr>
			            <td style="border-radius: 10px 10px 0 0;" bgcolor="#ffffff">
			                <!-- Logo -->
			                <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                    <tr>
			                        <td class="img-center p-30 px-15" style="font-size:0pt; line-height:0pt; text-align:center; padding: 30px; padding-left: 15px; padding-right: 15px;">
			                            <a href="#" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/Logo.png" width="auto" height="43" border="0" alt="" /></a>
			                        </td>
			                    </tr>
			                </table>
			                <!-- Logo -->

			                <!-- Main -->
			                <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                    <tr>
			                        <td class="px-50 mpx-15" style="padding-left: 50px; padding-right: 50px;">
			                            <!-- Section - Intro -->
			                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                                <tr>
			                                    <td class="pb-50" style="padding-bottom: 50px;">
			                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">

			                                            <tr>
			                                                <td class="title-36 a-center pb-15" style="font-size:16px; line-height:20px; color:#282828; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; text-align:left; padding-bottom: 15px;">
			                                                    <strong>Dear</strong>
			                                                    <br/>
			                                                    <span class="c-purple" style="color:#9128df;">' . $teamname . ',</span>
			                                                </td>
			                                            </tr>
			                                            <tr>
			                                                <td class="py-35 px-50 mpx-15" style="border-radius: 10px; padding-top: 35px; padding-bottom: 35px; padding-left: 50px; padding-right: 50px;" bgcolor="#f4ecfa">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tbody><tr>
			<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: -90px;">
			<tbody><tr>
			<td class="img-center pb-20" style="font-size:0pt; line-height:0pt; text-align:center; padding-bottom: 20px;">
			</td>
			</tr>
			</tbody></table>
			</td>
			</tr>
			<tr>
			<td class="text-18 a-center c-purple pb-15" style="font-size:18px; line-height:22px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; text-align:center; color:#9128df; padding-bottom: 15px;">
			<strong>Congratulations!</strong>
			</td>
			</tr>
			<tr>
			<td class="text-16 lh-26 a-center c-black" style="font-size:16px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 26px; text-align:center; color:#282828;">
			<em>"Withdrawal of Rs. ' . $amt . ' has been approved."</em>
			</td>
			</tr>
			</tbody></table>
			</td>
			                                            </tr>
			                                            <tr>
			                                                <td align="center">
			                                                    <!-- Button -->
			                                                    <table border="0" cellspacing="0" cellpadding="0" style="min-width: 200px;">

			</table>
			<!-- END Button -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			<!-- END Section - Intro -->
			</td>
			</tr>
			</table>
			<!-- END Main -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			<!-- END Container -->

			<!-- Footer -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="p-50 mpx-15" bgcolor="#019173" style="border-radius: 0 0 10px 10px; padding: 50px;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td align="center" class="pb-20" style="padding-bottom: 20px;">
			<!-- Socials -->
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.facebook.com/Believer11 " target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_facebook.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			    <td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://twitter.com/Believer11 " target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_twitter.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			<td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.instagram.com/Believer11 /" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_instagram.png" width="34" height="34" border="0" alt="" /></a>
			</td>

			<td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.pinterest.com/Believer11 /" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_pinterest.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			</tr>
			</table>
			<!-- END Socials -->
			</td>
			</tr>
			<tr>
			<td class="text-14 lh-24 a-center c-white l-white pb-20" style="font-size:14px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 24px; text-align:center; color:#ffffff;">
			<a href="tel:+17384796719" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">(+91) 9431763858</span></a> <a href="tel:+13697181973" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"></a>
			<br />
			<a href="mailto:care@Believer11 .com" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">care@Believer11 .com</span></a> - <a href="https://www.Believer11 .com" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">www.Believer11 .com</span></a>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table><!-- END Footer -->

			<!-- Bottom -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="text-12 lh-22 a-center c-grey- l-grey py-20" style="font-size:12px; color:#6e6e6e; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 22px; text-align:center; padding-top: 20px; padding-bottom: 20px;">This is system generated email, please do not reply.
			    </td>
			</tr>
			</table>											<!-- END Bottom -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</center>
			</body>
			</html>
				';
        return $html;
    }

    public static function withdrawRejected_email($teamname, $amt)
    {
        $html = '';
        $html .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
			<head>
			<!--[if gte mso 9]>
			<xml>
			<o:OfficeDocumentSettings>
			<o:AllowPNG/>
			<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
			</xml>
			<![endif]-->
			<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
			<meta http-equiv="X-UA-Compatible" content="IE=edge" />
			<meta name="format-detection" content="date=no" />
			<meta name="format-detection" content="address=no" />
			<meta name="format-detection" content="telephone=no" />
			<meta name="x-apple-disable-message-reformatting" />
			<!--[if !mso]><!-->
			<link href="https://fonts.googleapis.com/css?family=PT+Sans:400,400i,700,700i&display=swap" rel="stylesheet" />
			<!--<![endif]-->
			<title>Believer11 -OTP</title>
			<!--[if gte mso 9]>
			<style type="text/css" media="all">
			sup { font-size: 100% !important; }
			</style>
			<![endif]-->
			<!-- body, html, table, thead, tbody, tr, td, div, a, span { font-family: Arial, sans-serif !important; } -->


			<style type="text/css" media="screen">
			body { padding:0 !important; margin:0 auto !important; display:block !important; min-width:100% !important; width:100% !important; background:#e0f8ff; -webkit-text-size-adjust:none }
			a { color:#f3189e; text-decoration:none }
			p { padding:0 !important; margin:0 !important }
			img { margin: 0 !important; -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ }

			a[x-apple-data-detectors] { color: inherit !important; text-decoration: inherit !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; }

			.btn-16 a { display: block; padding: 15px 35px; text-decoration: none; }
			.btn-20 a { display: block; padding: 15px 35px; text-decoration: none; }

			.l-white a { color: #ffffff; }
			.l-black a { color: #282828; }
			.l-pink a { color: #f3189e; }
			.l-grey a { color: #6e6e6e; }
			.l-purple a { color: #9128df; }

			.gradient { background: linear-gradient(to right, #019173 0%,#0CB0E0 100%); }

			.btn-secondary { border-radius: 10px; background: linear-gradient(to right, #019173 0%,#0CB0E0 100%); }


			/* Mobile styles */
			@media only screen and (max-device-width: 480px), only screen and (max-width: 480px) {
			.mpx-10 { padding-left: 10px !important; padding-right: 10px !important; }

			.mpx-15 { padding-left: 15px !important; padding-right: 15px !important; }

			u + .body .gwfw { width:100% !important; width:100vw !important; }

			.td,
			.m-shell { width: 100% !important; min-width: 100% !important; }

			.mt-left { text-align: left !important; }
			.mt-center { text-align: center !important; }
			.mt-right { text-align: right !important; }

			.me-left { margin-right: auto !important; }
			.me-center { margin: 0 auto !important; }
			.me-right { margin-left: auto !important; }

			.mh-auto { height: auto !important; }
			.mw-auto { width: auto !important; }

			.fluid-img img { width: 100% !important; max-width: 100% !important; height: auto !important; }

			.column,
			.column-top,
			.column-dir-top { float: left !important; width: 100% !important; display: block !important; }

			.m-hide { display: none !important; width: 0 !important; height: 0 !important; font-size: 0 !important; line-height: 0 !important; min-height: 0 !important; }
			.m-block { display: block !important; }

			.mw-15 { width: 15px !important; }

			.mw-2p { width: 2% !important; }
			.mw-32p { width: 32% !important; }
			.mw-49p { width: 49% !important; }
			.mw-50p { width: 50% !important; }
			.mw-100p { width: 100% !important; }

			.mmt-0 { margin-top: 0 !important; }
			}

			</style>
			</head>
			<body class="body" style="padding:0 !important; margin:0 auto !important; display:block !important; min-width:100% !important; width:100% !important; background:#e0f8ff; -webkit-text-size-adjust:none;">
			<center>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 0; padding: 0; width: 100%; height: 100%;" bgcolor="#e0f8ff" class="gwfw">
			<tr>
			<td style="margin: 0; padding: 0; width: 100%; height: 100%;" align="center" valign="top">
			<table width="600" border="0" cellspacing="0" cellpadding="0" class="m-shell">
			<tr>
			<td class="td" style="width:600px; min-width:600px; font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="mpx-10">

			<!-- Container -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="gradient pt-10" style="border-radius: 10px 10px 0 0; padding-top: 10px;" bgcolor="#f3189e">
			    <table width="100%" border="0" cellspacing="0" cellpadding="0" >
			        <tr>
			            <td style="border-radius: 10px 10px 0 0;" bgcolor="#ffffff">
			                <!-- Logo -->
			                <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                    <tr>
			                        <td class="img-center p-30 px-15" style="font-size:0pt; line-height:0pt; text-align:center; padding: 30px; padding-left: 15px; padding-right: 15px;">
			                            <a href="#" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/Logo.png" width="auto" height="43" border="0" alt="" /></a>
			                        </td>
			                    </tr>
			                </table>
			                <!-- Logo -->

			                <!-- Main -->
			                <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                    <tr>
			                        <td class="px-50 mpx-15" style="padding-left: 50px; padding-right: 50px;">
			                            <!-- Section - Intro -->
			                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                                <tr>
			                                    <td class="pb-50" style="padding-bottom: 50px;">
			                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">

			                                            <tr>
			                                                <td class="title-36 a-center pb-15" style="font-size:16px; line-height:20px; color:#282828; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; text-align:left; padding-bottom: 15px;">
			                                                    <strong>Dear</strong>
			                                                    <br/>
			                                                    <span class="c-purple" style="color:#9128df;">' . $teamname . ',</span>
			                                                </td>
			                                            </tr>
			                                            <tr>
			                                                <td class="py-35 px-50 mpx-15" style="border-radius: 10px; padding-top: 35px; padding-bottom: 35px; padding-left: 50px; padding-right: 50px;" bgcolor="#f4ecfa">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tbody><tr>
			<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: -90px;">
			<tbody><tr>
			<td class="img-center pb-20" style="font-size:0pt; line-height:0pt; text-align:center; padding-bottom: 20px;">
			</td>
			</tr>
			</tbody></table>
			</td>
			</tr>
			<tr>
			<td class="text-18 a-center c-purple pb-15" style="font-size:18px; line-height:22px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; text-align:center; color:#e91e63; padding-bottom: 15px;">
			<strong>Oh-no!</strong>
			</td>
			</tr>
			<tr>
			<td class="text-16 lh-26 a-center c-black" style="font-size:16px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 26px; text-align:center; color:#f44336;">
			<em>"Your request for withdrawal of Rs. ' . $amt . ' has been rejected. Please Retry!!"</em>
			</td>
			</tr>
			</tbody></table>
			</td>
			                                            </tr>
			                                            <tr>
			                                                <td align="center">
			                                                    <!-- Button -->
			                                                    <table border="0" cellspacing="0" cellpadding="0" style="min-width: 200px;">

			</table>
			<!-- END Button -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			<!-- END Section - Intro -->
			</td>
			</tr>
			</table>
			<!-- END Main -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			<!-- END Container -->

			<!-- Footer -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="p-50 mpx-15" bgcolor="#019173" style="border-radius: 0 0 10px 10px; padding: 50px;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td align="center" class="pb-20" style="padding-bottom: 20px;">
			<!-- Socials -->
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.facebook.com/Believer11 " target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_facebook.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			    <td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://twitter.com/Believer11 " target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_twitter.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			<td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.instagram.com/Believer11 /" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_instagram.png" width="34" height="34" border="0" alt="" /></a>
			</td>

			<td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.pinterest.com/Believer11 /" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_pinterest.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			</tr>
			</table>
			<!-- END Socials -->
			</td>
			</tr>
			<tr>
			<td class="text-14 lh-24 a-center c-white l-white pb-20" style="font-size:14px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 24px; text-align:center; color:#ffffff;">
			<a href="tel:+17384796719" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">(+91) 9431763858</span></a> <a href="tel:+13697181973" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"></a>
			<br />
			<a href="mailto:care@Believer11 .com" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">care@Believer11 .com</span></a> - <a href="https://www.Believer11 .com" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">www.Believer11 .com</span></a>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table><!-- END Footer -->

			<!-- Bottom -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="text-12 lh-22 a-center c-grey- l-grey py-20" style="font-size:12px; color:#6e6e6e; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 22px; text-align:center; padding-top: 20px; padding-bottom: 20px;">This is system generated email, please do not reply.
			    </td>
			</tr>
			</table>											<!-- END Bottom -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</center>
			</body>
			</html>
				';
        return $html;
    }

    public static function dynamic_email($content)
    {
        $html = '';
        $html .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
			<head>
			<!--[if gte mso 9]>
			<xml>
			<o:OfficeDocumentSettings>
			<o:AllowPNG/>
			<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
			</xml>
			<![endif]-->
			<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
			<meta http-equiv="X-UA-Compatible" content="IE=edge" />
			<meta name="format-detection" content="date=no" />
			<meta name="format-detection" content="address=no" />
			<meta name="format-detection" content="telephone=no" />
			<meta name="x-apple-disable-message-reformatting" />
			<!--[if !mso]><!-->
			<link href="https://fonts.googleapis.com/css?family=PT+Sans:400,400i,700,700i&display=swap" rel="stylesheet" />
			<!--<![endif]-->
			<title>Believer11 -OTP</title>
			<!--[if gte mso 9]>
			<style type="text/css" media="all">
			sup { font-size: 100% !important; }
			</style>
			<![endif]-->
			<!-- body, html, table, thead, tbody, tr, td, div, a, span { font-family: Arial, sans-serif !important; } -->


			<style type="text/css" media="screen">
			body { padding:0 !important; margin:0 auto !important; display:block !important; min-width:100% !important; width:100% !important; background:#e0f8ff; -webkit-text-size-adjust:none }
			a { color:#f3189e; text-decoration:none }
			p { padding:0 !important; margin:0 !important }
			img { margin: 0 !important; -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ }

			a[x-apple-data-detectors] { color: inherit !important; text-decoration: inherit !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; }

			.btn-16 a { display: block; padding: 15px 35px; text-decoration: none; }
			.btn-20 a { display: block; padding: 15px 35px; text-decoration: none; }

			.l-white a { color: #ffffff; }
			.l-black a { color: #282828; }
			.l-pink a { color: #f3189e; }
			.l-grey a { color: #6e6e6e; }
			.l-purple a { color: #9128df; }

			.gradient { background: linear-gradient(to right, #019173 0%,#0CB0E0 100%); }

			.btn-secondary { border-radius: 10px; background: linear-gradient(to right, #019173 0%,#0CB0E0 100%); }


			/* Mobile styles */
			@media only screen and (max-device-width: 480px), only screen and (max-width: 480px) {
			.mpx-10 { padding-left: 10px !important; padding-right: 10px !important; }

			.mpx-15 { padding-left: 15px !important; padding-right: 15px !important; }

			u + .body .gwfw { width:100% !important; width:100vw !important; }

			.td,
			.m-shell { width: 100% !important; min-width: 100% !important; }

			.mt-left { text-align: left !important; }
			.mt-center { text-align: center !important; }
			.mt-right { text-align: right !important; }

			.me-left { margin-right: auto !important; }
			.me-center { margin: 0 auto !important; }
			.me-right { margin-left: auto !important; }

			.mh-auto { height: auto !important; }
			.mw-auto { width: auto !important; }

			.fluid-img img { width: 100% !important; max-width: 100% !important; height: auto !important; }

			.column,
			.column-top,
			.column-dir-top { float: left !important; width: 100% !important; display: block !important; }

			.m-hide { display: none !important; width: 0 !important; height: 0 !important; font-size: 0 !important; line-height: 0 !important; min-height: 0 !important; }
			.m-block { display: block !important; }

			.mw-15 { width: 15px !important; }

			.mw-2p { width: 2% !important; }
			.mw-32p { width: 32% !important; }
			.mw-49p { width: 49% !important; }
			.mw-50p { width: 50% !important; }
			.mw-100p { width: 100% !important; }

			.mmt-0 { margin-top: 0 !important; }
			}

			</style>
			</head>
			<body class="body" style="padding:0 !important; margin:0 auto !important; display:block !important; min-width:100% !important; width:100% !important; background:#e0f8ff; -webkit-text-size-adjust:none;">
			<center>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 0; padding: 0; width: 100%; height: 100%;" bgcolor="#e0f8ff" class="gwfw">
			<tr>
			<td style="margin: 0; padding: 0; width: 100%; height: 100%;" align="center" valign="top">
			<table width="600" border="0" cellspacing="0" cellpadding="0" class="m-shell">
			<tr>
			<td class="td" style="width:600px; min-width:600px; font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="mpx-10">

			<!-- Container -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="gradient pt-10" style="border-radius: 10px 10px 0 0; padding-top: 10px;" bgcolor="#f3189e">
			    <table width="100%" border="0" cellspacing="0" cellpadding="0" >
			        <tr>
			            <td style="border-radius: 10px 10px 0 0;" bgcolor="#ffffff">
			                <!-- Logo -->
			                <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                    <tr>
			                        <td class="img-center p-30 px-15" style="font-size:0pt; line-height:0pt; text-align:center; padding: 30px; padding-left: 15px; padding-right: 15px;">
			                            <a href="#" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/Logo.png" width="auto" height="43" border="0" alt="" /></a>
			                        </td>
			                    </tr>
			                </table>
			                <!-- Logo -->

			                <!-- Main -->
			                <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                    <tr>
			                        <td class="px-50 mpx-15" style="padding-left: 50px; padding-right: 50px;">
			                            <!-- Section - Intro -->
			                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
			                                <tr>
			                                    <td class="pb-50" style="padding-bottom: 50px;">
			                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">

			                                            <tr>
			                                                <td class="title-36 a-center pb-15" style="font-size:16px; line-height:20px; color:#282828; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; text-align:left; padding-bottom: 15px;">
			                                                    <strong>Dear</strong>
			                                                    <br/>
			                                                    <span class="c-purple" style="color:#9128df;">User,</span>
			                                                </td>
			                                            </tr>
			                                            <tr>
			                                                <td class="py-35 px-50 mpx-15" style="border-radius: 10px; padding-top: 35px; padding-bottom: 35px; padding-left: 50px; padding-right: 50px;" bgcolor="#f4ecfa">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tbody><tr>
			<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: -90px;">
			<tbody><tr>
			<td class="img-center pb-20" style="font-size:0pt; line-height:0pt; text-align:center; padding-bottom: 20px;">
			</td>
			</tr>
			</tbody></table>
			</td>
			</tr>
			<tr>

			</tr>
			<tr>
			<td class="text-16 lh-26 a-center c-black" style="font-size:16px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 26px; text-align:center; color:#282828;">
			<em>"' . $content . '"</em>
			</td>
			</tr>
			</tbody></table>
			</td>
			                                            </tr>
			                                            <tr>
			                                                <td align="center">
			                                                    <!-- Button -->
			                                                    <table border="0" cellspacing="0" cellpadding="0" style="min-width: 200px;">

			</table>
			<!-- END Button -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			<!-- END Section - Intro -->
			</td>
			</tr>
			</table>
			<!-- END Main -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			<!-- END Container -->

			<!-- Footer -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="p-50 mpx-15" bgcolor="#019173" style="border-radius: 0 0 10px 10px; padding: 50px;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td align="center" class="pb-20" style="padding-bottom: 20px;">
			<!-- Socials -->
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.facebook.com/Believer11 " target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_facebook.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			    <td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://twitter.com/Believer11 " target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_twitter.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			<td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.instagram.com/Believer11 /" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_instagram.png" width="34" height="34" border="0" alt="" /></a>
			</td>

			<td class="img" width="15" style="font-size:0pt; line-height:0pt; text-align:left;"></td>
			<td class="img" width="34" style="font-size:0pt; line-height:0pt; text-align:left;">
			<a href="https://www.pinterest.com/Believer11 /" target="_blank"><img src="https://Believer11 .com/wp-content/uploads/2021/07/ico_pinterest.png" width="34" height="34" border="0" alt="" /></a>
			</td>
			</tr>
			</table>
			<!-- END Socials -->
			</td>
			</tr>
			<tr>
			<td class="text-14 lh-24 a-center c-white l-white pb-20" style="font-size:14px; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 24px; text-align:center; color:#ffffff;">
			<a href="tel:+17384796719" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">(+91) 9431763858</span></a> <a href="tel:+13697181973" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"></a>
			<br />
			<a href="mailto:care@Believer11 .com" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">care@Believer11 .com</span></a> - <a href="https://www.Believer11 .com" target="_blank" class="link c-white" style="text-decoration:none; color:#ffffff;"><span class="link c-white" style="text-decoration:none; color:#ffffff;">www.Believer11 .com</span></a>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table><!-- END Footer -->

			<!-- Bottom -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td class="text-12 lh-22 a-center c-grey- l-grey py-20" style="font-size:12px; color:#6e6e6e; font-family:\'PT Sans\', Arial, sans-serif; min-width:auto !important; line-height: 22px; text-align:center; padding-top: 20px; padding-bottom: 20px;">This is system generated email, please do not reply.
			    </td>
			</tr>
			</table>											<!-- END Bottom -->
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</center>
			</body>
			</html>
				';
        return $html;
    }
}
