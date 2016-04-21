<?php
	// Title: fetchtriples.php
	// Author: Tony Jefferson
	// Last Modified: 5/5/2013
	require_once("mylib.php");
	define("DEFAULT_LIMIT","20");
	
	$queryString = prepare_query();
	//echo ("\$queryString=$queryString<br>");
	query_DB($queryString);
	
	function prepare_query(){
		$subject = (array_key_exists('s',$_GET)) ?  sanitize_string($_GET['s']) : NULL;
		$predicate = (array_key_exists('p',$_GET)) ?  sanitize_string($_GET['p']) : NULL;
		$limit = (array_key_exists('limit',$_GET)) ?  sanitize_string($_GET['limit']) : DEFAULT_LIMIT;
		$limit = floor($limit);
		if ($limit < 1 || !is_numeric($limit)) $limit = DEFAULT_LIMIT;
		
		
		if($subject && $predicate){
			$queryString = "SELECT * FROM Triple WHERE subject = '$subject' AND predicate  = '$predicate' ORDER BY timestamp DESC";
		} 
		elseif ($subject){
			$queryString = "SELECT * FROM Triple WHERE subject = '$subject' ORDER BY timestamp DESC";
		}
		elseif ($predicate){
			$queryString = "SELECT * FROM Triple WHERE predicate = '$predicate' ORDER BY timestamp DESC";
		} else {
			$queryString = "SELECT * FROM Triple ORDER BY timestamp DESC";
		}
		
		$queryString .= " LIMIT $limit";
		
		return $queryString;
	}
	
	function query_DB($queryString){
		date_default_timezone_set('America/New_York');
		$db = open_db();
		$result = $db->query($queryString);
		$allRows = $result->fetchAll();
		$numFound = count($allRows);
		
		$results = array();
		// Loop through result set
		foreach ($allRows as $row){
			// one way to build an array
			$filteredRow['id'] = $row['id'];
			$filteredRow['subject'] = $row['subject'];
			$filteredRow['predicate'] = $row['predicate'];
			$filteredRow['value'] = json_decode($row['value']); // NOTE: in SQLite, all single quotes are escaped with '' - see here for reason: http://php.net/manual/en/security.magicquotes.php 
			$filteredRow['timestamp'] = date("F j, Y, g:i a", $row['timestamp']);
			$results[]=$filteredRow;
		} // end loop
		
		// another way to build an array
		$array = array('status'=>'success', 'numFound'=>$numFound ,'results'=>$results);
		// convert array to JSON string
		$json = json_encode($array);
		header("content-type: application/json");
		echo $json;
	}



?>