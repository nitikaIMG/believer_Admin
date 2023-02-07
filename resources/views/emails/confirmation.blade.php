<?php echo Helpers::mailheader();?>
<?php echo Helpers::mailbody('<p><strong>Dear Challenger </strong></p><p>Please <strong>use OTP  '.$rand.'. </strong>  to verify your email address.</p>');
?>
<?php echo Helpers::mailfooter();?>