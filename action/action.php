<?php
require_once '../inc/properties.php';
require_once '../inc/utils.php';
require_once '../inc/dbObj.php';

if(isset($_POST['action']) && !empty($_POST['action'])){
	$action = $_POST['action'];
	switch($action){
		case "hu":
			// Handle User
			$result = array();
			$hash = isset($_POST['h']) ? $_POST['h'] : '';
			$fingerprint = isset($_POST['f']) ? $_POST['f'] : '';
			$username = isset($_POST['u']) ? $_POST['u'] : '';
			// Sanitize
			$hash = trim($hash);
			$fingerprint = trim($fingerprint);
			// Validation
			if(!preg_match('/[a-z0-9]/i', $hash)) $hash = '';
			if(!preg_match('/[a-z0-9]/i', $fingerprint)) $fingerprint = '';
			if(!preg_match('/[a-zA-Z0-9]/i', $username)) $username = '';
			// Checkpoint
			if(!empty($hash) && !empty($fingerprint) && !empty($username)){
				$checkpoint = checkFingerprint($hash, $fingerprint);
				if($checkpoint === true){
					$db = new DBUtil();
					$query_result = $db->handleUser($username);
					if($query_result){
						$user_best_score = $db->user_best_score;
						$user_last_score = $db->user_last_score;
						// Sanitize data
						if($user_best_score == null){
							$user_best_score = 0;
						}
						// Build the array
						$result = array(
										"user_best_score"=>$user_best_score,
										"user_last_score"=>$user_last_score
								  ); 
					}
				}
			}
			echo json_encode($result);
		break;
		case 'ss':
			// Save the score in the database
			$result = 0;
			$hash = isset($_POST['h']) ? $_POST['h'] : '';
			$fingerprint = isset($_POST['f']) ? $_POST['f'] : '';
			$username = isset($_POST['u']) ? $_POST['u'] : '';
			$score = isset($_POST['s']) ? $_POST['s'] : '';
			// Sanitize
			$hash = trim($hash);
			$fingerprint = trim($fingerprint);
			$score = trim($score);
			// Validation
			if(!preg_match('/[a-z0-9]/i', $hash)) $hash = '';
			if(!preg_match('/[a-z0-9]/i', $fingerprint)) $fingerprint = '';
			if(!preg_match('/[a-zA-Z0-9]/i', $username)) $username = '';
			if(!filter_var($score, FILTER_VALIDATE_INT)) $score = 0;
			// Checkpoint
			if(!empty($hash) && !empty($fingerprint) && !empty($username)){
				$checkpoint = checkFingerprint($hash, $fingerprint);
				if($checkpoint === true){
					// Check identity
					$session_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
					if(empty($session_user_id)){
						$db = new DBUtil();
						$db->handleUser($username);
						$session_user_id = $db->user_id;
					}
					// Add the score
					$db = new DBUtil();
					$score_result = $db->addScore($session_user_id, $score);
					if($score_result != false){
						$result = 1;
					}
				}
			}
			echo $result;
		break;
		default:
		break;
	}
}
?>