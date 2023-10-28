<?php
require_once dirname(__FILE__).'/properties.php';
require_once dirname(__FILE__).'/utils.php';

class DBUtil {
    
    private $mysqli = '';
	
	public $username = "";
	public $user_id = "";
	public $user_best_score = 0;
	public $user_last_score = 0;
    
    public function  __construct(){
		$this->connect();
	}
	
	private function connect(){
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		if(mysqli_connect_errno()){
			printf("DB Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
		// Add the Arabic Language Support
		$mysqli->query("SET NAMES 'utf8'");
		$mysqli->query("SET CHARACTER SET utf8");
		$this->mysqli = $mysqli;
	}
	
	public function handleUser($username){
		// Get the MySQLi object
		$mysqli = $this->mysqli;
		if(empty($mysqli)){
			$this->connect();
			$mysqli = $this->mysqli;
		}
		if(!empty($username)){
			// Do the query
			$user_id = '';
			$username = $mysqli->real_escape_string($username);
			$query = "SELECT id, username FROM `user` WHERE username='" . $username . "' LIMIT 1";
			$result = $mysqli->query($query);
			if(!empty($result)){
				if($result->num_rows == 0) {
					if($stmt = $mysqli->prepare("INSERT INTO `user` (username) VALUES(?)")) {
						$stmt->bind_param('s', $username);
						$stmt->execute();
						$user_id = $mysqli->insert_id;
					}
				} else if($result->num_rows == 1) {
					while($row = $result->fetch_assoc()){
						$user_id = $row['id'];
						$username = $row['username'];
					}
				}
			}
			mysqli_close($mysqli);
			$this->mysqli = null;
			
			// Save the data
			$this->user_id = $user_id;
			$this->username = $username;
			
			// Get the user's best score
			$this->getUserBestScore();
			
			// Get the user's last score
			$this->getUserLastScore();
			
			// Save the data in the session
			$_SESSION['username'] = $this->username;
			$_SESSION['user_id'] = $this->user_id;
			$_SESSION['user_best_score'] = $this->user_best_score;
			$_SESSION['user_last_score'] = $this->user_last_score;
			
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Add score
	 */
	public function addScore($user_id, $score){
		$result = false;
		// Get the MySQLi object
		$mysqli = $this->mysqli;
		if(empty($mysqli)){
			$this->connect();
			$mysqli = $this->mysqli;
		}
		// Do the query
		if(!empty($score) && !empty($user_id)){
			$score = $mysqli->real_escape_string($score);
			$stmt = $mysqli->prepare("INSERT INTO `score`(score, user_id) VALUES(?, ?)");
			if($stmt){
				$stmt->bind_param('ii', $score, $user_id);
				$stmt->execute();
				if(empty($mysqli->error)){
					$result = $stmt->insert_id;
				}
			}
		}
		mysqli_close($mysqli);
		$this->mysqli = null;
		return $result;
	}
	
	/**
	 * Get the user's best score
	 */
	private function getUserBestScore(){
		// Get the MySQLi object
		$mysqli = $this->mysqli;
		if(empty($mysqli)){
			$this->connect();
			$mysqli = $this->mysqli;
		}
		// Do the query
		if(!empty($this->user_id)){
			$query = "SELECT MAX(s.score) as `user_best_score`";
			$query .= "FROM `score` AS s ";
			$query .= "LEFT JOIN user AS u ON u.id=s.user_id ";
			$query .= "WHERE s.user_id='" . $this->user_id . "' AND s.is_deleted=0 AND u.is_deleted=0";
			$result = $mysqli->query($query);
			if(!empty($result) && $result->num_rows > 0){
				while($row = $result->fetch_assoc()){
					$this->user_best_score = $row['user_best_score'];
				}
			}
		}
		mysqli_close($mysqli);
		$this->mysqli = null;
	}
	
	/**
	 * Get the user's last score
	 */
	private function getUserLastScore(){
		// Get the MySQLi object
		$mysqli = $this->mysqli;
		if(empty($mysqli)){
			$this->connect();
			$mysqli = $this->mysqli;
		}
		// Do the query
		if(!empty($this->user_id)){
			$query = "SELECT s.score as `user_last_score`";
			$query .= "FROM `score` AS s ";
			$query .= "LEFT JOIN user AS u ON u.id=s.user_id ";
			$query .= "WHERE s.user_id='" . $this->user_id . "' AND s.is_deleted=0 AND u.is_deleted=0 ORDER BY s.created_date DESC LIMIT 1";
			$result = $mysqli->query($query);
			if(!empty($result) && $result->num_rows > 0){
				while($row = $result->fetch_assoc()){
					$this->user_last_score = $row['user_last_score'];
				}
			}
		}
		mysqli_close($mysqli);
		$this->mysqli = null;
	}
	
}
?>