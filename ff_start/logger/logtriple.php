<?php
	// Title: logtriple.php
	// Author: Tony Jefferson
	// Version: 0.13
	// Last Modified: 5/5/2013
	
	require_once("mylib.php");

	/* test with: 
	logtriple.php?s=testuser&k=testkey&p=message&v={"username":"testuser","message":"Hi!!"}
	logtriple.php?s=acjvks&k=123456&p=message&v={"username":"acjvks","message":"Hello!"}
	logtriple.php?s=acjvks&k=123456&p=location&v={"username":"acjvks","location":{"latitude":"43.082634","longitude":"-77.68004"}}
	*/

	$db = open_db();
	log_to_db($db);
	

	function log_to_db($db){
		$ERROR_MSG = 'usage logger.php?s=subject&p=predicate&v=value&k={property1:value1,property2:value2,...}';
		// Uncomment to quickly clear out all Triple records
		//$db->query("DELETE FROM Triple"); // delete all records from Triple table
		
		// grab values from query string
		$subject = (array_key_exists('s',$_GET)) ?  sanitize_string($_GET['s']) : show_error($ERROR_MSG);
		$predicate = (array_key_exists('p',$_GET)) ?  sanitize_string($_GET['p']) : show_error($ERROR_MSG);
		$value = (array_key_exists('v',$_GET)) ? $_GET['v'] : show_error($ERROR_MSG);
		$key = (array_key_exists('k',$_GET)) ?  sanitize_string($_GET['k']) : show_error($ERROR_MSG);
		$timestamp = time();
		
		// Check to see if user is authorized
		$queryString = "SELECT * FROM AuthKey WHERE username=? AND key=?";
		$statement = $db->prepare($queryString);
		$statement->setFetchMode(PDO::FETCH_ASSOC); 
		if($statement && $statement->execute(array($subject,$key))){
			$allRows = $statement->fetchAll();
			if (count($allRows) == 0){
				show_error("Bad username or key!");
				die;
			}
		}
	

		// convert passed in JSON string into a PHP associative array
		$json = json_decode($value,true);
		//print_r($json);
		if(! $json)  show_error("parameter 'v'($value) is not valid JSON!");
		
		// get IP address of user
		$ip = $_SERVER['REMOTE_ADDR'];
		
		// add ip address to PHP associative array
		$json['ip'] = $ip;
		
		// add timestamp address to PHP associative array
		$json['timestamp'] = $timestamp;
		
		// convert PHP associative array back into JSON string
		$value = json_encode($json);
		
		// insert value into DB
		$queryString =  ("INSERT INTO Triple (id, subject, predicate, value, timestamp) VALUES (NULL, ?,?,?,?)");
		$statement = $db->prepare($queryString);
		$statement->setFetchMode(PDO::FETCH_ASSOC); 
		if($statement && $statement->execute(array($subject, $predicate, $value, $timestamp))){
			$lastID = $db->lastInsertId();
			if ($lastID){
				$array = array("status"=>"success","description"=>"lastInsertId=" . $lastID, "ip"=> $ip);
				echo_response($array);
			} else {
				$array = array("status"=>"error","description"=>"Triple insert failed");
				echo_response($array);
			}
		} else {
			$array = array("status"=>"error","description"=>"Triple insert failed - problem with \$statement");
				echo_response($array);
		}
			
	}
	
	function show_error($msg){
		$array = array("status"=>"error","description"=>$msg);
		echo_response($array);
	}

	function echo_response($array){
		$json = json_encode($array);
		header("content-type: application/json");
		echo $json;
		die;
	
	}

?>
