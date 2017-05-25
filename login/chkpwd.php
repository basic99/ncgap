<?php
/**
 * Create AJAX response for login attempt.
 * 
 * Get name and password and query table for correct entry.
 * Return message and success as json.
 * 
 * 
 * @package ncgap
 */
session_start();

//get username and password submitted by form
$login = trim($_POST['username']);
$passwd = trim($_POST['password']);
//echo json_encode(array("name"=>$login,"pass"=>$passwd)); die();

//check that username and password are proper format
if (strlen($login) < 13  && strlen($login) > 5 && strlen($passwd) < 13 && strlen($passwd) > 5){

	$dsn = "pgsql:dbname=ncgap;host=127.0.0.1";
	$user = "postgres";
	$password ="";
	$dbh = new PDO($dsn, $user, $password);

	$query = "select count(*) from ncgap_users where login = :login and passcode = :passcode";
	$stmt = $dbh->prepare($query);
	$stmt->bindParam(':login', $login);
	$stmt->bindParam(':passcode', $passwd);
	$stmt->execute();
	$result = $stmt->fetchAll();

	//query returns number of rows matching username and password, if zero login failed
	if ($result[0]['count'] != 0 ){

		//if login successful update session variables with current user info
		$query2 = "select email from ncgap_users where login = :login";
		$stmt2 = $dbh->prepare($query2);
		$stmt2->bindParam(":login", $login);
		$stmt2->execute();
		$result2 = $stmt2->fetchAll();

		$_SESSION['email'] = $result2[0]['email'];
		$_SESSION['username'] = $login;
		
		echo json_encode(array("message"=>"login successful","success"=>true));

	} else {
		echo json_encode(array("message"=>"login incorrect, please try again","success"=>false));

	}
} else {
	echo json_encode(array("message"=>"login incorrect, please try again","success"=>false));

}

?>