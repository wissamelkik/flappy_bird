<?php
require_once dirname(__FILE__).'/../inc/jcryptionUtil.php';

// Manage requests
if(isset($_GET["gpk"]) && $_GET["gpk"] == true){
	$obj = new jCryptionUtil();
	$result = $obj->getPublicKey();
	echo $result;
} else if(isset($_GET["hs"]) && $_GET["hs"] == true){
	$obj = new jCryptionUtil();
	$key = isset($_POST['key']) ? $_POST['key'] : '';
	$result = $obj->handshake($key);
	echo $result;
} else if(isset($_POST['gd'])){
	$obj = new jCryptionUtil();
	$gd = isset($_POST['gd']) ? $_POST['gd'] : '';
	$result = $obj->getData($gd);
	echo $result;
} else {
	// Error
}
?>