<?php
/**
 * Dsiplay listing status
 * 
 * Query info_spp for species listing status.
 * 
 * @package ncgap
 */
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Listing Status</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="StyleSheet" href="styles/popups.css" type="text/css" />
<style type="text/css">
/* <![CDATA[ */

td {width: 200px;}
h3 {text-align: left;}
/* ]]> */
</style>
<script language="javascript" type="text/javascript">
/* <![CDATA[ */
function set_view(){
	var taxclass = document.getElementById('taxclas').value;
	//alert(taxclass);
	if(taxclass != 'AVES'){
		document.getElementById('pif').style.display = 'none';
	}
}
/* ]]> */
</script>
</head>
<body onload="set_view();">
<?php
require('nc_config.php');
pg_connect($pg_connect);

$strelcode = $_POST['strelcode'];
$species = stripslashes($_POST['species']);

$query = "select * from info_spp where strelcode = '{$strelcode}'";

//echo $query;

$result = pg_query($query);
$row = pg_fetch_array($result);
//var_dump($row);

?>
<input type="hidden" id="taxclas" value="<?php echo $row['strtaxclas']; ?>" />
<h3><?php echo $row['strscomnam']; ?><br /><i><?php echo $row['strgname']; ?></i></h3>
<hr />
<table>
<tr>
<td><a href="/listcodes/FederalStatusCodes.html" target="fedcodes" onclick="window.open('', 'fedcodes', 'menubar=no,height=200,width=520')"><b>Federal Status</b></a></td>
<td>
<?php 
if(strlen($row['strusesa']) == 0) {
	echo "---";
}else{
	echo $row['strusesa'];
}
?>
</td>
<tr>
<td><a href="/listcodes/NCStateStatusCodes.html" target="statecodes" onclick="window.open('', 'statecodes', 'menubar=no,height=300,width=520')"><b>NC State Status</b></a></td>
<td>
<?php //echo $row['strsprot']; 
if(strlen($row['strsprot']) == 0) {
	echo "---";
}else{
	echo $row['strsprot'];
}

?>
</td>
</tr>

<tr>
<td colspan="2"><a href="http://www.natureserve.org/explorer/ranking.htm" target="nserv" onclick="window.open('', 'nserv', 'menubar=no,scrollbars=yes,width=800')"><b>Nature Serve Rank</b></a></td>
</tr>
<tr>
<td>&nbsp;&nbsp;Global Rank</td>
<td>
<?php //echo $row['strgrank']; 
if(strlen($row['strgrank']) == 0) {
	echo "---";
}else{
	echo $row['strgrank'];
}
?>
</td>

</tr>
<tr>
<td>&nbsp;&nbsp;NC State Rank</td>
<td>
<?php //echo $row['strsrank']; 
if(strlen($row['strsrank']) == 0) {
	echo "---";
}else{
	echo $row['strsrank'];
}
?>

</td>
</tr>

<tr>
<td><a href="/listcodes/NCGAPStatusCodes.html" target="gapcodes" onclick="window.open('', 'gapcodes', 'menubar=no,height=200,width=720')"><b>GAP Status</b></a></td>
<td>
<?php //echo $row['gap_p_all2']; 
if(strlen($row['gap_p_all2']) == 0) {
	echo "---";
}else{
	echo $row['gap_p_all2'];
}
?>
</td>
</tr>

</table>

<div id="pif">
<table>
<tr><td colspan="2"><a href="/listcodes/NCPIFStatusCodes.html" target="pifcodes" onclick="window.open('', 'pifcodes', 'menubar=no,height=200,width=720')"><b>Partners-In-Flight</b></a></td></tr>

<tr>
<td>&nbsp;&nbsp;S. Atl. Coastal Plain</td>
<td>
<?php //echo $row['intpif_03']; 
if(strlen($row['intpif_03']) == 0) {
	echo "---";
}else{
	echo $row['intpif_03'];
}
?>
</td>
</tr>

<tr>
<td>&nbsp;&nbsp;Mid Atl. Piedmont</td>

<td>
<?php //echo $row['intpif_10']; 
if(strlen($row['intpif_10']) == 0) {
	echo "---";
}else{
	echo $row['intpif_10'];
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;Southern Piedmont</td>
<td>
<?php //echo $row['intpif_11']; 
if(strlen($row['intpif_11']) == 0) {
	echo "---";
}else{
	echo $row['intpif_11'];
}
?>
</td>
</tr>

<tr>
<td>&nbsp;&nbsp;Mid Atl. Ridge &amp; Valley</td>
<td>
<?php //echo $row['intpif_12']; 
if(strlen($row['intpif_12']) == 0) {
	echo "---";
}else{
	echo $row['intpif_12'];
}
?>
</td>
</tr>

<tr>
<td>&nbsp;&nbsp;S. Blue Ridge</td>
<td>
<?php //echo $row['intpif_23']; 
if(strlen($row['intpif_23']) == 0) {
	echo "---";
}else{
	echo $row['intpif_23'];
}
?>
</td>
</tr>

<tr>
<td>&nbsp;&nbsp;Mid Atl. Coastal Plain</td>
<td>
<?php //echo $row['intpif_44'];
if(strlen($row['intpif_44']) == 0) {
	echo "---";
}else{
	echo $row['intpif_44'];
}
?>
</td>
</tr>

</table>
</div>

</body>
</html>
