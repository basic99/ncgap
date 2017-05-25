<?php
require('nc_range_class.php');
session_start();		  
require('nc_config.php');
pg_connect($pg_connect);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="../styles/aqtree3clickable.css" />
<link rel="stylesheet" href="../styles/custom-theme/jquery-ui-1.8.6.custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
<script type="text/javascript" src="../javascript/jquery-ui-1.8.6.custom.min.js" ></script>
<script type="text/javascript" src="../javascript/aqtree3clickable.js"></script>
<script type="text/javascript" src="../javascript/controls_tab1.js"></script>
<script type="text/javascript" src="../javascript/controls234.js"></script>

<style type="text/css">
/* <![CDATA[ */

body {padding: 0px; margin: 2px;}
#tabs {font-size: 11px; width: 315px;}
#tabs-1 { width: 270px; font-size: 16px;}
#tabs-2{ width: 270px; font-size: 11px;}
#tabs-2 td{  font-size: 14px;
		  text-align: center;}
#tabs-3 {overflow: scroll; width: 270px; font-size: 16px;}
button {margin: 10px 0px 0px 100px; width: 100px;}
span.desc {font-size: 16px; line-height: 2;}
h2 {text-align: center;}
/* ]]> */
</style>
<script language="javascript" type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
load_selections();		  
$("#tabs").tabs();
$("#opentab").click();
$("button").button();
var win_h = $(window).height();
$("#tabs-1,#tabs-2,#tabs-3").height(win_h - 78);

$("#sub").click(function(evt) {
	document.forms[1].submit();	  
});

});
/* ]]> */
</script>
</head>
<body>
		  
<div id="tabs">
<ul>
<li><a href="#tabs-1">View Layers</a></li>
<li><a id="opentab" href="#tabs-2">Select Species</a></li>
<li><a id="legendtab" href="#tabs-3">Legends</a></li>
</ul>
<div id="tabs-1">
		  <form>
<ul class="aqtree3clickable">
<li class="aq3open"><a href="#" class="no_link">Foreground</a>
<ul>
<li><input type="checkbox" name="basins_river"  onclick="loadlayers();" /><a>River Basins</a></li>
<li><input type="checkbox" name="sub_basins"  onclick="loadlayers();" /><a>Sub Basins</a></li>
<li><input type="checkbox" name="bird_consv"  onclick="loadlayers();" /><a>Bird Conservation</a></li>
<li><input type="checkbox" name="cities"  onclick="loadlayers();" /><a>Cities</a></li>
<li><input type="checkbox" name="counties" checked="checked" onclick="loadlayers();" /><a>Counties</a></li>
<li><input type="checkbox" name="hydro"  onclick="loadlayers();" /><a>Rivers</a></li>
<li><input type="checkbox" name="interstate"  onclick="loadlayers();" /><a>Interstates</a></li>
<li><input type="checkbox" name="roads"  onclick="loadlayers();" /><a>Roads</a></li>
<li><input type="checkbox" name="topo_24000"  onclick="loadlayers();" /><a>Topo Maps(1:24000)</a></li>
</ul>
</li>
<li><a href="#" class="no_link">Stewardship</a>
<ul>
<li><input type="radio" name="steward" value="gapown"  onclick="loadlayers();" /><a href="#own" onclick="show_lgnd();">Ownership</a></li>
<li><input type="radio" name="steward" value="gapman"  onclick="loadlayers();" /><a href="#manage" onclick="show_lgnd();">Management</a></li>
<li><input type="radio" name="steward" value="gapsta"  onclick="loadlayers();" /><a href="#status" onclick="show_lgnd();" >Status</a></li>
<li><input type="radio" name="steward" value="none" checked="checked" onclick="loadlayers();" /><a>none</a></li>
</ul>
</li>
<li><a href="#" class="no_link">Background</a>
<ul>
<li><input type="radio" name="background" value="landcover"  onclick="loadlayers();" /><a href="#lcov" onclick="show_lgnd();">Land Cover</a></li>
<li><input type="radio" name="background" value="elevation" checked="checked" onclick="loadlayers();" /><a href="#elev" onclick="show_lgnd();">Elevation</a></li>
<li><input type="radio" name="background" value="none"  onclick="loadlayers();" /><a>none</a></li>
</ul>
</li>
</ul>
</form>
</div>

<div id="tabs-2">
<?php
$aoi_name = $_POST['aoi_name'];
$type = $_POST['type'];
$owner_aoi = $_POST['owner'];
$manage_aoi = $_POST['manage'];
$county_aoi = $_POST['county'];
$basin_aoi = $_POST['basin'];
$sub_basin_aoi = $_POST['sub_basin'];
$bird_consv_aoi = $_POST['bcr'];

//var_dump($_POST);

//$rclass_ser = $_POST['rclass'];

if (!isset($_SESSION["range".$aoi_name]) ){

	$_SESSION["range".$aoi_name] = new nc_range_class($aoi_name);
}
$rclass = $_SESSION["range".$aoi_name];
?>

<form action="controls4.php" method="post" target="controls" id="fm2" >
<input  type="hidden" name="aoi_name" value="<?php echo $aoi_name; ?>" />
<table style="border-collapse:collapse;" id="cntrls3">

<tr>
<th></th><th style="width: 80px;">Number of Species</th><th colspan="2">Select Category</th>
</tr>

<tr>
<td style="width:15px;"><input type="radio" name="species" value="all" checked="checked" /></td>
<td style="border: solid black 1px; border-right: white;"><?php echo $rclass->num_species['all_species']; ?></td>
<td colspan="2" style="border: solid black 1px; border-left: white;" >all species in selection area</td>
</tr>

<tr><td colspan="4" style="height: 5px; border-right:  solid 1px white; "></td></tr>

<tr>
<td></td>
<td style="border: solid black 1px; border-right: white; border-bottom: white"><?php echo $rclass->num_species['fed_species']; ?></td>
<td style="border-top: solid black 1px;"><input type="checkbox" name="fed" onclick="categories();" /></td>
<td style="border: solid black 1px; border-bottom: white; border-left: white;"> federally listed species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['state_species']; ?></td>
<td><input type="checkbox" name="state" onclick="categories();"/></td>
<td style="border-right: solid black 1px;"> nc state listed species</td>
</tr>

<tr>
<td ></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['gap_species']; ?></td>
<td><input type="checkbox" name="gap" onclick="categories();"/></td>
<td style="border-right: solid black 1px;"> gap species of concern</td>
</tr>

<tr>
<td><input type="radio" name="species" value="prot" /></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['ns_global_species']; ?></td>
<td><input type="checkbox" name="nsglobal" onclick="categories();"/></td>
<td style="border-right: solid black 1px;"> natureserve global priority species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['ns_state_species']; ?></td>
<td><input type="checkbox" name="nsstate" onclick="categories();"/></td>
<td style="border-right: solid black 1px;">natureserve state priority species</td>
</tr>

<tr>
<td ></td>
<td style="border-left: solid black 1px; border-bottom:solid black 1px; "><?php echo $rclass->num_species['pif_species']; ?></td>
<td style="border-bottom:solid black 1px;" ><input type="checkbox" name="pif" onclick="categories();"/></td>
<td style="border-right: solid black 1px; border-bottom:solid black 1px;" > partners in flight species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><input type="radio" name="sel" value="and" /> </td>
<td colspan="2" style="border-right: solid black 1px;">AND Select only species in all checked categories</td>
</tr>

<tr>
<td ></td>
<td style="border: solid black 1px; border-top: white; border-right: white;"><input type="radio" name="sel" value="or" checked="checked" /></td>
<td colspan="2" style="border-bottom: solid black 1px; border-right: solid black 1px;"> OR Select species in any checked category </td>
</tr>

</table>
<button id="sub">Submit</button>
</form>
   <!--<INPUT type="button" value="submit" onclick="submit()">
<div style="margin: 5px; width: 100px;" onclick="document.forms[1].submit();" 
onmousedown="document.getElementById('btn04').src = '/graphics/ncgap/b04_dn.png';"
onmouseup="document.getElementById('btn04').src = '/graphics/ncgap/b04_up.png';" >
<img src="/graphics/ncgap/b04_up.png" id="btn04" alt="button"  />
</div>-->
</div>

<div id="tabs-3">
		            
<h4><a href="#lcov">GAP Land Cover</a></h4>
<h4><a href="#owner">Ownership (Stewardship)</a></h4>
<h4><a href="#manage">Management (Stewardship)</a></h4>
<h4><a href="#status">GAP Status (Stewardship)</a></h4>


<a name="elev"></a><br /><br />
<h4>Elevation (meters)</h4>
<img src="/graphics/ncgap/nc_elev_legend.png" alt="elevation legend" /><br /><br />


<a name="lcov"></a><br /><br />
<h4>GAP Land Cover</h4>
<img src="/graphics/ncgap/nc_lc_legend_1.png" alt="cover legend" /><br />
<img src="/graphics/ncgap/nc_lc_legend_2.png" alt="cover legend" /><br />
<img src="/graphics/ncgap/nc_lc_legend_3.png" alt="cover legend" /><br />

<a name="own"></a><br /><br />
<h4>Ownership (Stewardship)</h4>
<img src="/graphics/ncgap/nc_leg_owner.png" alt="ownership legend" /><br />

<a name="manage"></a><br /><br />
<h4>Management (Stewardship)</h4>
<img src="/graphics/ncgap/nc_leg_manage.png" alt="manage legend" /><br />

<a name="status"></a><br /><br />
<h4>GAP Status (Stewardship)</h4>
<img src="/graphics/ncgap/nc_leg_status.png" alt="status legend" /><br />
</div>
</div>

</body>
</html>
