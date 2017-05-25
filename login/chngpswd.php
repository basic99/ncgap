<?php
/**
 * Create AJAX response for password change.
 * 
 * Query correct password and update table with new password if correct.
 * Return json with message and success.
 * 
 * 
 * @package ncgap
 */
session_start();

$oldpass = $_POST['oldpass'];
$newpass = $_POST['newpass'];
$email = $_SESSION['email'];
$login = $_SESSION['username'];

//echo json_encode(array("message"=>$oldpass,"success"=>false)); die();

if (strlen($newpass) < 6  || strlen($newpass)  > 12)  {
	$message = "new password must be 6-12 characters long";
	echo json_encode(array("message"=>$message,"success"=>false)); die();
}

$dsn = "pgsql:dbname=ncgap;host=127.0.0.1";
$user = "postgres";
$password ="";
$dbh = new PDO($dsn, $user, $password);

$query = "select count(*) from ncgap_users where login = :login and passcode = :passcode";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':login', $_SESSION['username']);
$stmt->bindParam(':passcode', $oldpass);
$stmt->execute();
$result = $stmt->fetchAll();

if ($result[0]['count'] != 0 ){
	$query2 = "update ncgap_users set passcode = :newpass where login = :login";
	$stmt2 = $dbh->prepare($query2);
	$stmt2->bindParam(':newpass', $newpass);
	$stmt2->bindParam(':login', $_SESSION['username']);
	if ($stmt2->execute()) {
		$message = "your login name is ".$login."\nyour new passcode is ".$newpass;
		//mail($email, 'password change for www5.basic.ncsu.edu', $message);
      //send email
      $headers = 'From: BaSIC_WebMaster@ncsu.edu' . "\r\n" .
         'Reply-To: BaSIC_WebMaster@ncsu.edu';
      //$message = "your login name is ".$login."\nyour passcode is ".$passwd;
      mail($email, 'password change for www.gapserve.ncsu.edu', $message, $headers);

		$message2 = "password successfully updated";
		echo json_encode(array("message"=>$message2,"success"=>true)); die();

	} else {
		$message = "database error, please try again";
		echo json_encode(array("message"=>$message,"success"=>false)); die();
	}

} else {
	$message = "error entering old password";
	echo json_encode(array("message"=>$message,"success"=>false)); die();
}




?>