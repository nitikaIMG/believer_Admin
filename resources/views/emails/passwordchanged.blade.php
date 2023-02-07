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
				<p style="margin: 10px;font-size:30px;color:#2995E2;font-weight:700;font-size:25px">Hi <?php echo $name; ?></p>
				<p style="margin: 10px;
font-size: 13px;
color: #333;
font-weight: 500;
line-height: 27px;">  Password of your <?php echo Helpers::projectName();?>'s account was recently changed.  </p>
			
			</div>
		</div>
	</div>
	<?php echo Htmlhelpers::emailfooter();?>
	</div>
</body>
</html>