<?php
/**
 * Create AJAX response for new user.
 * 
 * Collect email and login name from register.php AJAX request.
 * Check email is valid email format and if not return message, success.
 * If email is already registered return message, success.
 * If login name is not 6-12 characters or already in use return message, success.
 * If new login successful, send email and return message, success. 
 * 
 * @package ncgap
 */

$email = $_POST['email'];
$login = $_POST['login'];

//echo json_encode(array("message"=>$email,"success"=>false)); die();

$dbname = "pgsql:dbname=ncgap host=127.0.0.1";
$dbuser = "postgres";
$dbpass = "";

$dbh = new PDO($dbname, $dbuser, $dbpass);

//check that email is valid
$email = trim($email);
if (!preg_match("/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/", $email)) {
	$message = "email address entered is not valid, please enter a valid address";
	echo json_encode(array("message"=>$message,"success"=>false)); die();

}

//check whether email is used, if so resend login, else continue creating new login
$query1 = "select  login, passcode from ncgap_users where email = :email";
$stmt1 = $dbh->prepare($query1);
$stmt1->bindParam(":email", $email);
$stmt1->execute();
$result = $stmt1->fetchAll();

if (strlen($result[0]['passcode']) != 0) {
	$login = $result[0]['login'];
	$passwd = $result[0]['passcode'];
}else{

	//check that login name is valid
	$login = trim($login);
	if (strlen($login) <6 || strlen($login) > 12){
		$message = "must create login with length 6-12";
		echo json_encode(array("message"=>$message,"success"=>false)); die();
	}

	//check that login name in not already in use
	$query2 = "select count(*) from ncgap_users where login = :login";
	$stmt2 = $dbh->prepare($query2);
	$stmt2->bindParam(':login', $login);
	$stmt2->execute();
	$results2 = $stmt2->fetchAll();
	if ($results2[0]['count'] != 0) {
		$message = "login name already in use, please select another";
		echo json_encode(array("message"=>$message,"success"=>false)); die();
	}

	//create initial passcode and insert user into database
	$passwd = rand(100000, 999999);
	$query3 = "insert into ncgap_users (email, login, passcode, add_date) values (:email, :login, :passwd, CURRENT_TIMESTAMP)";
	$stmt3 = $dbh->prepare($query3);
	$stmt3->bindParam(':email', $email);
	$stmt3->bindParam(':login', $login);
	$stmt3->bindParam(':passwd', $passwd);
	$stmt3->execute();
}

//send email
$headers = 'From: BaSIC_WebMaster@ncsu.edu' . "\r\n" .
    'Reply-To: BaSIC_WebMaster@ncsu.edu';
$message = "your login name is ".$login."\nyour passcode is ".$passwd;
mail($email, 'login name for www.gapserve.ncsu.edu', $message, $headers);

//reuturn success to register.php
$message = "login success";
echo json_encode(array("message"=>$message,"success"=>true));

?>