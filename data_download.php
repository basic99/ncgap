<?php
/**
 * Display small map of AOI and selections for data download
 *
 * Collect $_POST data submitted from form fm6 in map2.php or form fm4 in select_species.php and $_SESSION data.
 * Use this to create a map with red bounding box around AOI.
 * Use database query for richness species or $rclass->get_species_dnld to display species to select depending on whether in single or multiple mode.
 *
 * @package ncgap
 */
require('nc_range_class.php');
require('nc_aoi_class.php');
require('nc_config.php');
session_start();
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Data download</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="StyleSheet" href="styles/popups.css" type="text/css" />
<link rel="stylesheet" href="styles/custom-theme/jquery-ui-1.8.6.custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
<script type="text/javascript" src="javascript/jquery-ui-1.8.6.custom.min.js" ></script>
<style type="text/css">
/* <![CDATA[ */
body {font-family: sans-serif;}
#selects {position: absolute; left: 500px; top: 50px;}
img {position: fixed; left: 50px; top: 50px;}
.none {visibility: hidden;}
.hdr {font-size: 1.2em; text-align: center;}
#b01 {position: fixed; left: 130px; top: 500px; }
#b02 {position: fixed; left: 250px; top: 500px; }
.ui-widget {font-size: 11px;}
button {width: 100px;
}
/* ]]> */
</style>
<script language="javascript" type="text/javascript">
/* <![CDATA[ */
$(document).ready(function() {
	$("button").button();
	//check_pd();
	 $("#b01").click(function(evt) {
         evt.preventDefault();
			document.forms[0].reset();
      });
	  $("#b02").click(function(evt) {
         evt.preventDefault();
			document.forms[0].submit();
      });
	check_pd(); //put this last as it will not always run
});
function check_pd(){
	var strelcode = document.forms[0].strelcode.value;
	document.forms[0].strpds.value = strelcode;
	var length = document.forms[0].pds.length;
	for (var i=0;  i<length; i++){
		if(strelcode == document.forms[0].pds[i].value){
			document.forms[0].pds[i].checked = 'true';
		}
	}
}

//function poll(){
//	var length = document.forms[0].pds.length;
//	var previous = "";
//	for (var i=0;  i<length; i++){
//		if(document.forms[0].pds[i].checked){
//			var selected = document.forms[0].pds[i].value;
//			if (previous.length == 0){
//				previous = selected;
//			}else{
//				previous = previous + ":" + selected;
//			}
//		}
//	}
//	document.forms[0].strpds.value = previous;
//}

function poll(){
	if(document.forms[0].pds.length){
        var length = document.forms[0].pds.length;
        var previous = "";
        for (var i=0;  i<length; i++){
             console.log(i);
           if(document.forms[0].pds[i].checked){
              var selected = document.forms[0].pds[i].value;
              if (previous.length == 0){
                 previous = selected;
              }else{
                 previous = previous + ":" + selected;
              }
           }
        }
   } else {
        var previous = document.forms[0].pds.value;
   }


	document.forms[0].strpds.value = previous;
}
/* ]]> */
</script>
</head>
<body onload="">

<?php

if (($_SESSION['username']) == 'visitor' ){
	die('<h4>Must log in to use this feature.</h4>');
}
//get post data
$avian = $_POST['avian'];
$mammal = $_POST['mammal'];
$reptile = $_POST['reptile'];
$amphibian = $_POST['amphibian'];
$aoi_name = $_POST['aoi_name'];
$strelcode = $_POST['strelcode'];
$richness_species = $_POST['richness_species'];
$richness_map = $_POST['richness_map'];
$search = $_POST['search'];
//var_dump($_POST);


//get range and aoi instances
$rclass = $_SESSION["range".$aoi_name];
$nc_aoi_class = $_SESSION[$aoi_name];

//get corners of aoi and add 10%
$min_x = $nc_aoi_class->get_minx();
$min_y = $nc_aoi_class->get_miny();
$max_x = $nc_aoi_class->get_maxx();
$max_y = $nc_aoi_class->get_maxy();
$x_adj = ($max_x - $min_x)*0.1;
$y_adj = ($max_y - $min_y)*0.1;
$min_x -= $x_adj;
$min_y -= $y_adj;
$max_x += $x_adj;
$max_y += $y_adj;



//create box
$aoi_extent = ms_newRectObj();
$aoi_extent->setextent($min_x, $min_y, $max_x, $max_y);
$extent_save = $min_x.":".$min_y.":".$max_x.":".$max_y;


$mapfile = "/var/www/html/ncgap/ncgap.map";


// draw elevation and states layers
$map = ms_newMapObj($mapfile);
$this_layer = $map->getLayerByName('elevation');
$this_layer->set('status', MS_ON);
$this_layer = $map->getLayerByName('counties');
$this_layer->set('status', MS_ON);

//draw aoi layer
$filter = "(name = '{$aoi_name}')";
$this_layer = $map->getLayerByName('aoi');
$this_layer->setFilter($filter);
$this_layer->set('status', MS_ON);

//creating main map
$mapname = "map".rand(0,9999999).".png";
$maploc = "{$mspath}{$mapname}";
$map->setSize(400, 400);
$mapimage = $map->draw();

//draw rectangle on image
$aoi_extent->draw($map, $map->getLayerByName('aoi'), $mapimage, 1, '' );

//save image to file
$mapimage->saveImage($maploc);

?>
<img alt="map" src="/server_temp/<?php  echo $mapname; ?>" />
<button id="b01">Reset</button>
<button id="b02">Submit</button>

<form action="data_dnld_submit.php" target="_self" method="post" >

<div id="selects">
<table>
<tr><td colspan="2" class="hdr">general layers</tr>
<tr>
<td><input type="checkbox" name="lcov" /></td><td>landcover</td>
</tr>
<tr>
<td><input type="checkbox" name="steward" /></td><td>stewardship(vector)</td>
</tr>
<?php
if (!empty($richness_map)) {
	$richness_export = $nc_aoi_class->richnessexport($richness_map);
?>

<tr>
<td><input type="checkbox" checked="checked" name="richness" /></td><td>richness map</td>
</tr>
<tr>
<td class="none">HHH</td><td class="none">HHHHHHH</td>
</tr>

<?php
pg_connect($pg_connect);


$species_arr = explode(":", $richness_species);
foreach ($species_arr as $a){
	$query = sprintf("select strelcode from info_spp where strscomnam = '%s'", pg_escape_string($a));
	$result = pg_query($query);
	$row = pg_fetch_array($result);
	printf("<tr><td><input type=checkbox onclick='poll();' name='pds' value='%s'/></td><td>%s</td></tr>",  $row['strelcode'], $a);
}
$richness_species = preg_replace("/:/","\n", $richness_species);
} else { ?>
<tr>
<td class="none">HHH</td><td class="none">HHHHHHH</td>
</tr>

<?php
if (isset($avian) && isset($mammal) && isset($reptile) && isset($amphibian)) {
	echo "<tr><td colspan=\"2\" class=\"hdr\">predicted distribution layers</tr>";
	$rclass->get_species_dnld($avian, $mammal, $reptile, $amphibian, $search);
}
}
?>
</table>
</div>
<input type="hidden" name="aoi_name" value="<?php echo $aoi_name; ?>" />
<input type="hidden" name="ext_save" value="<?php echo $extent_save; ?>" />
<input type="hidden" name="strelcode" value="<?php echo $strelcode; ?>" />
<input type="hidden" name="strpds"  />
<input type="hidden" name="r_export" value="<?php echo $richness_export; ?>" />
<input type="hidden" name="r_species" value="<?php echo $richness_species; ?>" />
</form>


</body>
</html>
