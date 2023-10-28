<?php

class jCryptionUtil {
    
	private $descriptorspec = array(
								   0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
								   1 => array("pipe", "w")  // stdout is a pipe that the child will write to
								);						
	private $public_key_file = "rsa_1024_pub.pem";
	private $private_key_file = "rsa_1024_priv.pem";
    
    public function  __construct($action = ''){
		// Session
		if(session_id() == '') session_start();
	}
	
	public function getPublicKey(){
		// Build the array and print it
		$public_key_path = dirname(__FILE__)."/".$this->public_key_file;
		$public_key = file_get_contents($public_key_path);
		$arrOutput = array(
			"publickey" => $public_key
		);
		return json_encode($arrOutput);
	}
	
	public function handshake($post_key){
		$descriptorspec = $this->descriptorspec;
		// Decrypt the client's request
		$private_key_path = dirname(__FILE__)."/".$this->private_key_file;
		$cmd = sprintf("openssl rsautl -decrypt -inkey " . $private_key_path);
		$process = proc_open($cmd, $descriptorspec, $pipes);
		if(is_resource($process)){
		    fwrite($pipes[0], base64_decode($post_key));
		    fclose($pipes[0]);

		    $key = stream_get_contents($pipes[1]);
		    fclose($pipes[1]);
		    proc_close($process);
		}
		// Save the AES key into the session
		$_SESSION["key"] = $key;
		
		// JSON encode the challenge
		$cmd = sprintf("openssl enc -aes-256-cbc -pass pass:'$key' -a -e");
		$process = proc_open($cmd, $descriptorspec, $pipes);
		if(is_resource($process)){
		    fwrite($pipes[0], $key);
		    fclose($pipes[0]);

		    // we have to trim all newlines and whitespaces by ourself
		    $challenge = trim(str_replace("\n", "", stream_get_contents($pipes[1])));
		    fclose($pipes[1]);
		    proc_close($process);
		}

		return json_encode(array("challenge" =>  $challenge));
		//echo json_encode(array("challenge" => AesCtr::encrypt($key, $key, 256)));
	}
	
	public function getData($post_data){
		$key = $_SESSION["key"];

		$descriptorspec = $this->descriptorspec;
		
		// Decrypt the client's request and send it to the clients(uncrypted)
		$cmd = sprintf("openssl enc -aes-256-cbc -pass pass:'$key' -d");
		$process = proc_open($cmd, $descriptorspec, $pipes);
		if (is_resource($process)) {
			fwrite($pipes[0], base64_decode($post_data));
			fclose($pipes[0]);

			$data = stream_get_contents($pipes[1]);
			fclose($pipes[1]);
			proc_close($process);
		}
		
		if(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return json_encode(array("data" => $data));
		} else {
			parse_str($data, $output);
			return $output;
		}
	}
	
}	
?>