<?php
/**
 * Password change page uses AJAX.
 * 
 * Enter old pass and new pass, and create AJAX request to chngpswd.php when submit is clicked.
 * Display AJAX response in div id="somecontent".
 * 
 * @package ncgap
 */

session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Change Password</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
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
	$('#b02').click(function(event){
		event.preventDefault();
		var nps = $('#nps').val();
		var old = $('#ops').val();
		$.post("chngpswd.php", { oldpass: old, newpass: nps },
		function(data){
			//alert(data.message + data.success);
			$('#somecontent').html(data.message);
			if(data.success){
				//opener.document.getElementById('visitor').value = user;
				//window.location = "mail.php";
			}
		}, "json");
	});
});
/* ]]> */
</script>
</head>
<body>
<center>



<form action="password.php" method="post" target="_self" >
<h4>Password Change</h4>
<table>
<tr>
<td>Old password</td>
<td><input type="text" name="oldpass" id="ops"/></td>
</tr>
<tr>
<td>New password</td>
<td><input type="text" name="newpass" id="nps"/></td>
</tr>
<tr>
<td>
<button onclick="javascript:window.close();">cancel</button>
</td>
<td>
<button  id="b02">submit</button>
</td>
</tr>
</table>

</form>
<div id="somecontent" >

</div>

</center>



</body>
</html>
