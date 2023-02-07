<html>
<head>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet"> 
<style>
</style>
</head>
	<body style="margin:0px;font-family: 'Open Sans', sans-serif;">
		<div style="width:600px;margin:auto;">
			<div style="background:url('<?php echo URL::asset('images/emails/activate-bg.jpg')?>');background-size:cover;padding:5%;">
				<div style="background:rgba(255,255,255,0.9);text-align:center;padding: 1% 2% 4%;">
					<?php echo Htmlhelpers::emailheader();?>
					<div style="width:100%;display:inline-block;border-top: 1px solid #ddd;">
						<h1 style="color:#2995E2;">Account Created Sucessfully</h1>
						<div style="width:20%;margin:auto; margin-top:3%;">
						<img src="<?php echo URL::asset('images/emails/done-img.png')?>" style="width:100%;height:auto;"></img>
						</div>
						<h2 style="color:#425666;">Welcome to <?php echo Helpers::projectname();?></h2>
						<div style="width:100%;float:left;border-top:1px solid #ddd;">
							<p style="color:#259AE5;font-size:17px;line-height:25px;">Join a community of passionate experts and thought-leaders who are being sought out by fellow entrepreneurs.</p>
						</div>
					</div>
				</div>
			</div>
			<?php echo Htmlhelpers::emailfooter();?>
		</div>
	</body>
</html>