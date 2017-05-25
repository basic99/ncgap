
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>single species</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="../styles/custom-theme/jquery-ui-1.8.6.custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
<script type="text/javascript" src="../javascript/jquery-ui-1.8.6.custom.min.js" ></script>
<style type="text/css">
/* <![CDATA[ */

ul {/*border: solid green 1px;*/
padding: 0px;
margin: 0px;}
li {list-style: none;
/*border: solid blue 1px;*/
font-size: 14px;
padding-bottom: 12px;
padding-top: 2px;
}
#cont {width: 520px;
/*border: solid black 1px;*/
margin: 0px auto 0px;
font-size: 11px;
}
.lbl {font-size: 16px;}
body {margin: 0px;}
#spname {width: 350px;}

#col1 {width: 235px;
/*border: solid red 1px;*/
height: 105px;
float: left;
}
#col2 {width: 280px;
/*border: solid red 1px;*/
height: 105px;
float: right;
}
#sprep {clear: both;
margin: 0px 70px 0px;
}
#col1 button {
	float: right;
   clear: both;
	margin: 2px;}
#col2 button {
	float: right;
   clear: both;
	margin: 2px;}
/* ]]> */
</style>
<script language="javascript" type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	pred_dist();
	$("button").button();
	$("#pred").click(function(event){
		event.preventDefault();
		predicted();
	});
	$("#lc").click(function(event){
		event.preventDefault();
		lc_report();
	});
	$("#own").click(function(event){
		event.preventDefault();
		owner_report();
	});
	$("#stat").click(function(event){
		event.preventDefault();
		status_report();
	});
	$("#man").click(function(event){
		event.preventDefault();
		manage_report();
	});
	$("#sprep").click(function(event){
		event.preventDefault();
		species_report();
	});
	$("#liststat").click(function(event){
		event.preventDefault();
		list_stat();
	});
	});

function hab_leg(){
	parent.controls.show_lgnd();
	parent.controls.location.hash = '#lcov';
}
function sta_leg(){
	parent.controls.show_lgnd();
	parent.controls.location.hash = '#status';
}
function man_leg(){
	parent.controls.show_lgnd();
	parent.controls.location.hash = '#manage';
}
function own_leg(){
	parent.controls.show_lgnd();
	parent.controls.location.hash = '#own';
}
function ran_leg(){
	parent.controls.show_lgnd();
	parent.controls.location.hash = '#range';
}
function pred_dist(){
	var strelcode = document.forms.fm1.strelcode.value;
	if (strelcode.length == 0){
		parent.map.document.forms.ajaxform.species_layer.value = 'none';
	}else{
		parent.map.document.forms.ajaxform.strelcode.value = strelcode;
		parent.map.document.forms.fm5.strelcode.value = strelcode;
		parent.map.document.forms.ajaxform.species_layer.value = 'predicted';
		parent.map.document.forms.fm5.species_layer.value = 'predicted';
		parent.map.document.getElementById('zoom_ajax').value = '1';
		parent.map.send_ajax();
	}
}
function predicted(){
	var strelcode = document.forms.fm1.strelcode.value;
	if (strelcode.length == 0){alert('must select a species');
	}else{
		window.open("","report","toolbar=no,menubar=no,scrollbars,resizable");
		parent.map.document.forms.fm2.strelcode.value = strelcode;
		var species = document.forms.fm1.species.value;
		parent.map.document.forms.fm2.species.value = species;
		parent.map.document.forms.fm2.target = 'report';
		parent.map.document.forms.fm2.report.value = 'predicted';
		parent.map.document.forms.fm2.submit();
	}
}
function lc_report(){
	var strelcode = document.forms.fm1.strelcode.value;
	if (strelcode.length == 0){alert('must select a species');
	}else{
		window.open("","report","toolbar=no,menubar=no,scrollbars,resizable");
		parent.map.document.forms.fm2.strelcode.value = strelcode;
		var species = document.forms.fm1.species.value;
		parent.map.document.forms.fm2.species.value = species;
		parent.map.document.forms.fm2.target = 'report';
		parent.map.document.forms.fm2.report.value = 'landcover_sp';
		parent.map.document.forms.fm2.submit();
	}
}
function manage_report(){
	var strelcode = document.forms.fm1.strelcode.value;
	if (strelcode.length == 0){alert('must select a species');
	}else{
		window.open("","report","toolbar=no,menubar=no,scrollbars,resizable");
		parent.map.document.forms.fm2.strelcode.value = strelcode;
		var species = document.forms.fm1.species.value;
		parent.map.document.forms.fm2.species.value = species;
		parent.map.document.forms.fm2.target = 'report';
		parent.map.document.forms.fm2.report.value = 'management_sp';
		parent.map.document.forms.fm2.submit();
	}
}
function owner_report(){

	var strelcode = document.forms.fm1.strelcode.value;
	if (strelcode.length == 0){alert('must select a species');
	}else{
		window.open("","report","toolbar=no,menubar=no,scrollbars,resizable");
		parent.map.document.forms.fm2.strelcode.value = strelcode;
		var species = document.forms.fm1.species.value;
		parent.map.document.forms.fm2.species.value = species;
		parent.map.document.forms.fm2.target = 'report';
		parent.map.document.forms.fm2.report.value = 'owner_sp';
		parent.map.document.forms.fm2.submit();
	}
}
function status_report(){

	var strelcode = document.forms.fm1.strelcode.value;
	if (strelcode.length == 0){alert('must select a species');
	}else{
		window.open("","report","toolbar=no,menubar=no,scrollbars,resizable");
		parent.map.document.forms.fm2.strelcode.value = strelcode;
		var species = document.forms.fm1.species.value;
		parent.map.document.forms.fm2.species.value = species;
		parent.map.document.forms.fm2.target = 'report';
		parent.map.document.forms.fm2.report.value = 'status_sp';
		parent.map.document.forms.fm2.submit();
	}
}
function lc_map(){	
	var strelcode = document.forms.fm1.strelcode.value;
	if (strelcode.length == 0){alert('must select a species');
	}else{
		parent.map.document.forms.ajaxform.strelcode.value = strelcode;
		parent.map.document.forms.fm5.strelcode.value = strelcode;
		parent.map.document.forms.ajaxform.species_layer.value = 'habitat';
		parent.map.document.forms.fm5.species_layer.value = 'habitat';
		parent.map.document.getElementById('zoom_ajax').value = '1';
		parent.map.send_ajax();
	}
}
function ownership_map(){
	var strelcode = document.forms.fm1.strelcode.value;
	if (strelcode.length == 0){alert('must select a species');
	}else{
		parent.map.document.forms.ajaxform.strelcode.value = strelcode;
		parent.map.document.forms.fm5.strelcode.value = strelcode;
		parent.map.document.forms.ajaxform.species_layer.value = 'ownership';
		parent.map.document.forms.fm5.species_layer.value = 'ownership';
		parent.map.document.getElementById('zoom_ajax').value = '1';
		parent.map.send_ajax();
	}
}
function status_map(){
	var strelcode = document.forms.fm1.strelcode.value;
	if (strelcode.length == 0){alert('must select a species');
	}else{
		parent.map.document.forms.ajaxform.strelcode.value = strelcode;
		parent.map.document.forms.fm5.strelcode.value = strelcode;
		parent.map.document.forms.ajaxform.species_layer.value = 'status';
		parent.map.document.forms.fm5.species_layer.value = 'status';
		parent.map.document.getElementById('zoom_ajax').value = '1';
		parent.map.send_ajax();
	}
}
function manage_map(){
	var strelcode = document.forms.fm1.strelcode.value;
	if (strelcode.length == 0){alert('must select a species');
	}else{
		parent.map.document.forms.ajaxform.strelcode.value = strelcode;
		parent.map.document.forms.fm5.strelcode.value = strelcode;
		parent.map.document.forms.ajaxform.species_layer.value = 'manage';
		parent.map.document.forms.fm5.species_layer.value = 'manage';
		parent.map.document.getElementById('zoom_ajax').value = '1';
		parent.map.send_ajax();
	}
}
function range(){
	var strelcode = document.forms.fm1.strelcode.value;
	if (strelcode.length == 0){alert('must select a species');
	}else{
		parent.map.document.forms.ajaxform.strelcode.value = strelcode;
		parent.map.document.forms.fm5.strelcode.value = strelcode;
		parent.map.document.forms.ajaxform.species_layer.value = 'range';
		parent.map.document.forms.fm5.species_layer.value = 'range';
		parent.map.document.getElementById('zoom_ajax').value = '1';
		parent.map.send_ajax();
	}
}
function list_stat(){
	document.forms[0].action = "../NC_ListStatus.php";
	window.open('','liststat','toolbar=no,menubar=no,resizable,height=450,width=450');
	document.forms[0].target = "liststat";
	document.forms[0].submit();
}
function species_report(){
	var host = window.location.host;
	var page = document.forms[0].strelcode.value+".html";
	var url = 'http://'+host+"/nc_sppreport/"+page;
	window.open(url,'','toolbar=no,menubar=no,resizable,scrollbars=yes');
}
/* ]]> */
</script>
</head>
<body onload="">
<?php

$species = $_POST['species'];
$selected = $species[0];

require('nc_config.php');
pg_connect($pg_connect);

$sqlval = pg_escape_string($selected);
$query = "select * from info_spp where strscomnam  = '{$sqlval}'";
$result = pg_query($query);
$row = pg_fetch_array($result);
$strelcode = $row['strelcode'];
$scname = ucfirst($row['strgname']);
$comname = strtolower($selected);
?>

<div id="cont">
<span class="lbl">Current Species:</span> <input type="text" name="species" id="spname"  readonly="readonly" value="<?php echo $comname.'/'.$scname; ?>"/>
<div id="col1">
<button id="pred">Calculate</button>
<button id="lc">Calculate</button>
<ul>
<li>
<input type="radio" name="functions" value="dist" onclick="pred_dist();" checked="checked" /><span >Predicted Dist.</span>
</li>
<li>
<input type="radio" name="functions" value="veg" onclick="lc_map();"/> <a href="javascript:hab_leg();">Habitat Types</a>
</li>
<li>
<input type="radio" name="functions" value="range" onclick="range();" /> <a href="javascript:ran_leg();">Hexagonal&nbsp;Range&nbsp;Maps</a>
</li>
</ul>
</div>

<div id="col2">
<button id="own">Calculate</button>
<button id="stat">Calculate</button>
<button id="man">Calculate</button>
<ul>
<li><input type="radio" name="functions" value="owner" onclick="ownership_map();"/> <a href="javascript:own_leg();">Ownership of Habitat</a></li>
<li><input type="radio" name="functions" value="protection" onclick="status_map();"/> <a href="javascript:sta_leg();">Protection of Habitat</a></li>
<li><input type="radio" name="functions" value="management" onclick="manage_map();"/><a href="javascript:man_leg();">Management of Habitat</a></li>
</ul>
</div>
<button id="sprep">Species Report</button>
<button id="liststat">Listing Status</button>
</div>


<form action="" method="post" name="fm1">
<input type="hidden" name="strelcode" value="<?php echo $strelcode; ?>"/> 
<input type="hidden" name="species" value="<?php echo $selected; ?>"/>   
</form>
</body>
</html>
