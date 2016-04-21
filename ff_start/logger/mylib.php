<?php
	define("PATH_TO_DB","triples.sqlite3");
	
	function open_db(){
		if(file_exists(PATH_TO_DB)){
			// open DB
			$db = new PDO('sqlite:' . PATH_TO_DB);
		} else {
			try{
				// create the database
				$db = new PDO('sqlite:' . PATH_TO_DB);
		
				
			
				// create the 'AuthKey' table
				$db->exec("CREATE TABLE AuthKey (id INTEGER PRIMARY KEY, username, key)");  
			 
				// insert test data into the table
				$db->exec("INSERT INTO AuthKey (id, username, key) VALUES ('1', 'testuser', 'testkey')");
				$db->exec("INSERT INTO AuthKey (id, username, key) VALUES ('2', 'acjvks', '123456')");
				
				// create the 'Triple' table
				$db->exec("CREATE TABLE Triple (id INTEGER PRIMARY KEY, subject, predicate, value, timestamp)");
				
				// insert test data into the table
				$db->exec("INSERT INTO Triple (id, subject, predicate, value, timestamp) VALUES ('1', 'testuser', 'defaultpredicate','{\"someKey\":\"someValue\"}','123456')");
				$db->exec("INSERT INTO Triple (id, subject, predicate, value, timestamp) VALUES ('2', 'testuser', 'message','{\"username\":\"testuser\",\"message\":\"Default Hello world!\",\"ip\":\"74.67.160.35\",\"timestamp\":\"1234567\"}','1234567')");  
				$db->exec("INSERT INTO Triple (id, subject, predicate, value, timestamp) VALUES ('3', 'testuser', 'location','{\"username\":\"testuser\",\"location\":{\"latitude\":\"43.08464\",\"longitude\":\"-77.680145\"},\"ip\":\"74.67.160.35\",\"timestamp\":\"12345678\"}','12345678')");  
				
				echo "Created DB<br>";
			}
			catch(PDOException $e){
				echo 'Exception : ' . $e->getMessage();
			}
		}
		return $db;
	}
	
	
	function sanitize_string($str){
		$str = urldecode($str); // %20 to space, %7E to ~, etc...
		$str = trim($str); // get rid of leading and trailing spaces
		$str = strip_tags($str); // get rid of HTML and PHP tags
		$str = addslashes($str); // add backslashes to (i.e. escape) quotes
		return $str;
	}
	

?>