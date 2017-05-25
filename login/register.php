<?php
/**
 * Collect username and email and send AJAX request to createuser.php.
 * 
 * Send AJAX request to createuser.php when submit button is clicked. 
 * Display message in div id="somecontent".
 * 
 * @package ncgap
 */

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Online GAP register</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
<link rel="stylesheet" href="../styles/custom-theme/jquery-ui-1.8.6.custom.css" />
<script type="text/javascript" src="../javascript/jquery-ui-1.8.6.custom.min.js" ></script>
<link rel="StyleSheet" href="../styles/popups.css" type="text/css" />
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
		var name = $('#name').val();
		var mail = $('#mail').val();
		$.post("createuser.php", { login: name, email: mail },
		function(data){
			//alert(data.message + data.success);
			$('#somecontent').html(data.message);
			if(data.success){
				//opener.document.getElementById('visitor').value = user;
				window.location = "mail.php";
			}
		}, "json");
	});
});
/* ]]> */
</script>
</head>
<body>
<p>In order to register for the site please select a login name which is  6-12 characters long. 
Also enter your email address and a copy of your login and passcode will be mailed to you. 
You can change the passcode later after you log into the site if you like.
Note that if you have previously registered with an email address, then it will ignore the entry in the login name 
field and send a copy of the current login and passcode.</p>

<form action="" method="post">
<table>
<tr>
<td>login name: </td>
<td><input type="text" size="30" name="login" id="name"/></td>
</tr>
<tr>
<td>e-mail address: </td>
<td><input type="text" size="30" name="email" id="mail" /></td>
</tr>

<tr>
<td align="center">
<button   onclick="javascript:window.close();"  >close</button>
<!--
<img id="b01" src="/graphics/ncgap/b16_up.png" alt="button" onclick="window.close();" 
onmousedown="document.getElementById('b01').src = '/graphics/ncgap/b16_dn.png'"
onmouseup="document.getElementById('b01').src = '/graphics/ncgap/b16_up.png'"/>
-->
</td>
<td align="center">
<button    id="b02">submit</button>
<!--
<img id="b02" src="/graphics/ncgap/b17_up.png" alt="button"
onmousedown="document.getElementById('b02').src = '/graphics/ncgap/b17_dn.png'"
onmouseup="document.getElementById('b02').src = '/graphics/ncgap/b17_up.png'"/>
_-->
</td>
</tr>
</table>

<div id="somecontent" >

</div>
</form>
</body>
</html>
