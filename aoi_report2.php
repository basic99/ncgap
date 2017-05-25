<?php 
/**
 * Generates GRASS report
 * 
 * Uses $_POST and $_SESSION input to determine proper report to create.
 * Calls method of nc_aoi_class to create GRASS report.
 * 
 * @package ncgap
 * @deprecated  replaced by aoi_report3.php
 * 
 */

/**
 * import class definition before session_start
 * 
 */
require('nc_aoi_class.php');
session_start();
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>AOI GRASS Report</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
/* <![CDATA[ */
@media print{
  .prn {display: none; }
}
  
body {font-family: sans-serif;}
/* ]]> */
</style>
<script language="javascript" type="text/javascript">
/* <![CDATA[ */
function spreadsheet(){
	//window.open("aoi_report_ss.php","ssrept","height=100,width=300")
	var pretag = document.getElementsByTagName("pre");
	var content = pretag[0].innerHTML;
	document.forms[0].content.value = content;
	document.forms[0].submit();
}
/* ]]> */
</script>
</head>
<body>

<?php
$report = $_POST['report'];
$strelcode = $_POST['strelcode'];
$species = stripslashes($_POST['species']);
$aoi_name = $_POST['aoi_name'];
$a = $_SESSION[$aoi_name];



if ($report == 'landcover'){
	echo "<h1>AOI Land Cover Report</h1>";
	$a->aoi_landcover();
}

if ($report == 'management') {
	echo "<h1>AOI Management Report</h1>";
	$a->aoi_management();
}

if ($report == 'owner') {
	echo "<h1>AOI Ownership Report</h1>";
	$a->aoi_ownership();
}

if ($report == 'status') {
	echo "<h1>AOI GAP Status Report</h1>";
	$a->aoi_status();
}

if ($report == 'status_sp') {
	echo "<h1>Species GAP Status Report</h1>";
	echo "<h3>{$species}</h3>";
	$a->species_status($strelcode);
}

if ($report == 'landcover_sp') {
	echo "<h1>Species Land Cover Report</h1>";
	echo "<h3>{$species}</h3>";
	$a->species_landcover($strelcode);
}

if ($report == 'management_sp') {
	echo "<h1>Species Management Report</h1>";
	echo "<h3>{$species}</h3>";
	$a->species_management($strelcode);
}

if ($report == 'owner_sp') {
	echo "<h1>Species Ownership Report</h1>";
	echo "<h3>{$species}</h3>";
	$a->species_ownership($strelcode);
}
if ($report == 'predicted') {
	echo "<h1>Predicted Distribution Report</h1>";
	echo "<h3>{$species}</h3>";
	$a->predicted($strelcode);
}
if ($report == 'richness_report') {
	echo "<h1>Richness Report</h1>";
	$a->richnessreport($species);
}

?>

<img src="/graphics/ncgap/b21_up.png" alt="b21" id="b21" class="prn" onclick="window.print();" 
   onmousedown="document.getElementById('b21').src='/graphics/ncgap/b21_dn.png';"
   onmouseup="document.getElementById('b21').src='/graphics/ncgap/b21_up.png';"/>

<img src="/graphics/ncgap/b22_up.png" alt="b22" id="b22" class="prn" onclick="spreadsheet();" 
   onmousedown="document.getElementById('b22').src='/graphics/ncgap/b22_dn.png';"
   onmouseup="document.getElementById('b22').src='/graphics/ncgap/b22_up.png';"/>



<form action="aoi_report_ss.php" target="_self" method="post">
<input  type="hidden" name="aoi_name" value="<?php echo $aoi_name; ?>" /> 
<input type="hidden" name="report" value="<?php echo $report; ?>" />
<input type="hidden" name="strelcode" value="<?php echo $strelcode ?>" />
<input type="hidden" name="species" value="<?php echo $species ?>" />
<input type="hidden" name="ss" value="ss" />
<input type="hidden" name="content"  />
</form>

</body>
</html>
