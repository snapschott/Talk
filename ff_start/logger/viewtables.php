<?php
require_once("mylib.php");

$db = open_db();
echo_users($db);
echo_triples($db);

function echo_triples($db){
		date_default_timezone_set('America/New_York');
		$html = "<h2>All Triples</h2>";
		$html .= "<table border='1'>";
		$html .= "<tr><td>id</td><td>subject</td><td>predicate</td><td>value</td><td>timestamp</td></tr>";
		$queryString = "SELECT * FROM Triple";
		echo ("\$queryString=$queryString<br>");
		$result = $db->query($queryString);
		$allResults = $result->fetchAll();
		foreach($allResults as $row){
			$id = $row['id'];
			$subject = $row['subject'];
			$predicate = $row['predicate'];
			$value = $row['value'];
			$timestamp = $row['timestamp'];
			$timestamp = date("F j, Y, g:i a", $timestamp);
			$html .= "<tr><td>$id</td><td>$subject</td><td>$predicate</td><td>$value</td><td>$timestamp</td></tr>";;
		}
		$html .= "</table>";
		echo $html;
	}
	
	function echo_users($db){
		$html = "<h2>All Users</h2>";
		$html .= "<table border='1'>";
		$html .= "<tr><td>id</td><td>username</td><td>key</td></tr>";
		$queryString = "SELECT * FROM AuthKey";
		echo ("\$queryString=$queryString<br>");
		$result = $db->query($queryString);
		$allResults = $result->fetchAll();
		foreach($allResults as $row){
			$id = $row['id'];
			$username = $row['username'];
			$key = $row['key'];
			$html .= "<tr><td>$id</td><td>$username</td><td>$key</td></tr>";;
		}
		$html .= "</table>";
		echo $html;
	}
	
?>