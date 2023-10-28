<?php
require_once 'inc/properties.php';
require_once 'inc/utils.php';
// Generate a hash and a fingerprint
$hash = generateHash();
$fingerprint = generateFingerPrint($hash);
?>
<!DOCTYPE html>
<html>   
<head>
	<meta charset="utf-8" />
	<title>Flappy Twitter</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<!-- Load the jQuery library -->
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>-->
	<!-- Load the Phaser library -->
	<script type="text/javascript" src="js/phaser.min.js"></script>
	<!-- Load the jCryption library -->
	<script type="text/javascript" src="js/jquery.jcryption.3.0.js"></script>
	<!-- Custom scripts -->
	<script type="text/javascript">
		window.hash = "<?php echo $hash; ?>";
		window.fingerprint = "<?php echo $fingerprint; ?>";
		window.sp = "<?php echo STRING_SEPARATOR_IN_AJAX_CALL; ?>";
	</script>
	<script type="text/javascript" src="js/main.js"></script>
	<!-- Custom style -->
	<style type="text/css">
		#game_wrap {position:relative; margin:50px auto 0 auto; width:400px;}
		#game_div,
		#game_form {position:absolute; top:0; left:0;}
		#game_div {margin:0 !important;}
		#game_form label,
		#game_form input {display:block; font-size:20px; font-family:Arial, sans-serif;}
		#game_form label {margin:170px 0 0 85px; font-size:20px; color:#fff;}
		#game_form input {margin:20px 0 0 100px; width:200px; color:#333;}
	</style>
</head>

<body>
	<div id="game_wrap">
		<div id="game_div"></div>
		<div id="game_form" style="display:none;">
			<label for="formUsername">Write down your username</label>
			<input type="text" id="formUsername" value="" autocomplete="off" />
		</div>
	</div>
</body>
</html>