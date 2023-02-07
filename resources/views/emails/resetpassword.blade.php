<html>
<head>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet"> 
</head>
<body style="margin:0px;font-family: 'Open Sans', sans-serif;">
	<div style="width:550px;margin:auto;">
		<div style="background:url('<?php echo URL::asset('images/emails/activate-bg.jpg')?>');background-size:cover;padding:5%;">
		<div style="background:rgba(255,255,255,0.9);text-align:center;padding: 1% 2% 4%;">
			<?php echo Htmlhelpers::emailheader();?>
			<div style="width:100%;display:inline-block;border-top: 1px solid #ddd;">
				<p style="margin: 10px;font-size:30px;color:#2995E2;font-weight:700;font-size:25px">Reset password</p>
				<p style="margin: 10px;
font-size: 13px;
color: #333;
font-weight: 500;
line-height: 27px;">  We have received you request to reset your password from <?php echo Helpers::projectName();?> aacount. You dont have to worry about your forgotten password. All you have to do is reset it by following link </p>
				<a href="<?php echo action('RegisteruserController@resetpassword',$token)?>" target="_blank" 
				style="font-weight:700;text-decoration:none;color:#fff;background:linear-gradient(#126DAD, #2995E2);padding:10px 20px;margin: 2% 0%;display: inline-block;">
			Click here</a>
			</div>
		</div>
	</div>
	<?php echo Htmlhelpers::emailfooter();?>
	</div>
</body>
</html>