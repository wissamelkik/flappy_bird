<?php
require_once 'properties.php';

function getUserIPAddress(){
	$ip = '';
	if(!empty($_SERVER['HTTP_CLIENT_IP'])){
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

function generateHash(){
	return sha1(md5(uniqid(rand(), true)));
}

function generateFingerPrint($hash){
	if(!empty($hash)){
		$ip = getUserIPAddress();
		$str = $hash . ENCRYPTION_SALT . $ip;
		return md5($str);
	}
}

function checkFingerprint($hash, $fingerprint){
	if(!empty($hash) && !empty($fingerprint)){
		// Generate a new fingerprint
		$new_fingerprint = generateFingerPrint($hash);
		if($new_fingerprint == $fingerprint){
			return true;
		}
	}
	return false;
}
?>