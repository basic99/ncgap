<?php 
/**
 * Web site login page.
 * 
 * If $_SESSION['username'] is  visitor load login screen, else load page that shows user already logged in.
 * When user clicks login button, AJAX request is sent to chkpwd.php. 
 * AJAX response is displayed in div id="somecontent". 
 * If successful login set username on main map page and reload this page.
 * 
 * @package ncgap
 */
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="StyleSheet" href="../styles/popups.css" type="text/css" />
<link rel="stylesheet" href="../styles/custom-theme/jquery-ui-1.8.6.custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
<script type="text/javascript" src="../javascript/jquery-ui-1.8.6.custom.min.js" ></script>
<style type="text/css">
/* <![CDATA[ */
#somecontent {color: red;}
.ui-widget {font-size: 11px;}
button {width: 100px;
margin: 10px;}
/* ]]> */
</style>
<script language="javascript" type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
		  $("button").button();
   $("#logcancel").click(function(evt) {
		  evt.preventDefault();
		  window.close();
		  });
	$("crlogin").click(function(evt) {
		  evt.preventDefault();
		  document.forms.fm2.submit;
		  });
	$("passwdsbmt").click(function(evt) {
		  evt.preventDefault();
		  document.forms.fm3.submit();
		  });
	$('#senddata').click(function(evt){
      evt.preventDefault();
		var user = $('#usr').val();
		var pswd = $('#pswd').val();
		$.post("chkpwd.php", { username: user, password: pswd },
		function(data){
			//alert(data.message + data.success); 
			$('#somecontent').html(data.message);
			if(data.success){				
				opener.document.getElementById('visitor').value = user;
				window.location.reload();
			}			
		}, "json");
	});
});

/* ]]> */
</script>
</head>
<body>
<?php


//set username from session variable
$username = $_SESSION['username'];

if (!strcmp($username, "visitor")) {

?>

<h4> Please  login.</h4>

<form action="login.php" target="_self" method="post">
<table>
<tr>
<td>username: &nbsp;&nbsp;</td><td><input type="text" name="username" id="usr"/></td>
</tr>
<tr>
<td>password: </td><td><input type="password" name="password" id="pswd"/></td>
</tr>
<!--
<tr>
<td><input type="button" onclick="javascript:window.close();" value="cancel" /></td>
<td><input type="button" id="senddata" value="login"/></td>
</tr>
-->
</table>
<button id="logcancel">Cancel</button>
<button id="senddata">Submit</button>
</form>

<div id="somecontent" >

</div>

<h4>Not registered or forgot your login and passcode? </h4>
<form name="fm2" action="register.php" method="get" target="_self">
		  <!--
<input type="submit" value="create login"/>-->
<button id="crlogin">Create login</button>
</form>

<?php
} else {
	printf("<h4>User successfully logged in as: %s</h4>", $_SESSION['username']);

?>
<button   onclick="javascript:window.close();"  >close</button>
<h4>Change password? </h4>

<form name="fm3" action="password.php" method="get" target="_self" >
		  <button id="passwdsbmt">Change</button>
		  <!--
<input type="submit" value="change" />-->
</form>
<?php
}
?>
</body>
</html>
