<html>
<head>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
</head>
	<body style="margin:0px;font-family: 'Open Sans', sans-serif;">
		<div style="width:600px;margin:auto;">
			<div style="background:url('<?php echo URL::asset('images/emails/activate-bg.jpg')?>');background-size:cover;padding:5%;">
				<div style="background:rgba(255,255,255,0.9);text-align:center;padding: 1% 2% 4%;">
					<?php echo Htmlhelpers::emailheader();?>
					<div style="width:100%;display:inline-block;border-top: 1px solid #ddd;">
						<div style="width:20%;margin:auto; margin-top:3%;">
							<img src="<?php echo URL::asset('images/emails/done-img.png')?>" style="width:100%;height:auto;"></img>
						</div>
						<div style="width:100%;float:left;border-top:1px solid #ddd;">
							<p style="margin: 10px;font-size:20px;color:#333;font-weight:600;"><?php echo $name;?></p>
							<?php
								if($type=='member'){
							?>
							<p style="margin: 10px;font-size:14px;color:#333;font-weight:500;">We've received your question, thanks! </p>
							<p style="margin: 10px;font-size:14px;color:#333;font-weight:500;">You must be wondering what's next? Your question was sent to hundreds of experts to provide you with a high quality answer in the shortest amount of time as possible. As soon as your question is answered, we'll notify you! </p>
							<?php } else{ ?>
							<p style="margin: 10px;font-size:14px;color:#333;font-weight:500;">We've received new question!</p>
							<p style="margin: 10px;font-size:14px;color:#333;font-weight:500;">Someone asked a new question related to the service you offered. Response On his question soon. </p>
							<?php } ?>
							<a href="<?php echo action('QuestionController@singlequestion',$slug)?>" target="_blank" style="font-weight:700;text-decoration:none;color:#fff;background:linear-gradient(#126DAD, #2995E2);padding:10px 20px;margin: 2% 0%;display: inline-block;">
							View Question</a>
						</div>
					</div>
				</div>
			</div>
			<?php echo Htmlhelpers::emailfooter();?>
		</div>
	</body>
</html>