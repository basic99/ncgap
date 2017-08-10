<?php

//$ret =  json_encode(array("mapname"=>"hello world"));
//date_default_timezone_set("America/New_York");

//connect string and $mspath should be set here
require('nc_config.php');
pg_connect($pg_connect);


ini_set("display_errors", 0);
ini_set("error_log", "/var/www/html/ncgap/logs/php-error.log");

error_log("running map_ajax.php");

//set mapfile and load mapscript if not already loaded

//process ajax input data
$extent_raw = $_POST['extent'];
$zoom = $_POST['zoom'];
$layer = $_POST['layers'];
$win_w = $_POST['winw'];
$win_h = $_POST['winh'];
$click_x = $_POST['clickx'];
$click_y = $_POST['clicky'];
$county_aoi = $_POST['county_aoi'];
$owner_aoi = $_POST['owner_aoi'];
$manage_aoi = $_POST['manage_aoi'];
$basin_aoi = $_POST['basin_aoi'];
$sub_basin_aoi = $_POST['sub_basin_aoi'];
$bird_consv_aoi = $_POST['bird_consv_aoi'];
$ecosys_aoi = $_POST['ecosys_aoi'];
$job_id = $_POST['job_id'];

//echo $job_id; die();
//echo json_encode(array("mapname"=>$county_aoi,"extent"=>$new_extent, "refname"=>$refurl)); die();
$post = print_r($_POST, true);
$logfileptr = fopen("/var/log/weblog/ncgap", "a");
fprintf($logfileptr, "\n\n\n%s   %s\nInput\n%s ", date('l dS \of F Y h:i:s A'), __FILE__, $post);
fclose($logfileptr);

$click_point = ms_newPointObj();
$click_point->setXY($click_x, $click_y);

//save extent to rect
$old_extent =  ms_newRectObj();
$extent = explode(" ", $extent_raw);
$old_extent->setextent($extent[0], $extent[1], $extent[2], $extent[3]);


/*
$logfileptr = fopen("/data2/weblogs/ncgap", "a");
fprintf($logfileptr, "test point in file%s in line %s\n ",  __FILE__, __LINE__);
fclose($logfileptr);
*/

//check that script is still running after mapobj creation
$query = "insert into check_mapobj(job_id ) values ( $job_id )";
pg_query($query);

//create mapobj
$mapfile = "/var/www/html/ncgap/ncgap.map";
$map = ms_newMapObj($mapfile);

//check that script is still running after mapobj creation
$query = "delete from check_mapobj where job_id = $job_id";
pg_query($query);


$logfileptr = fopen("/var/log/weblog/ncgap", "a");
fprintf($logfileptr, "test point in file%s in line %s\n ",  __FILE__, __LINE__);
fclose($logfileptr);


//set layers
if(preg_match("/management/", $layer)){
	$this_layer = $map->getLayerByName('gapman');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('gapman');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/ownership/", $layer)){
	$this_layer = $map->getLayerByName('gapown');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('gapown');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/status/", $layer)){
	$this_layer = $map->getLayerByName('gapsta');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('gapsta');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/landcover/", $layer)){
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/elevation/", $layer)){
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/basins_river/", $layer)){
	$this_layer = $map->getLayerByName('basins_river');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('basins_river');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/sub_basins/", $layer)){
	$this_layer = $map->getLayerByName('subwtsh');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('subwtsh');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/bird_consv/", $layer)){
	$this_layer = $map->getLayerByName('bird_consv');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('bird_consv');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/cities/", $layer)){
	$this_layer = $map->getLayerByName('cities');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('cities');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/counties/", $layer)){
	$this_layer = $map->getLayerByName('counties');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('counties');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/hydro/", $layer)){
	$this_layer = $map->getLayerByName('hydro_line');
	$this_layer->set('status', MS_ON);
	//$this_layer = $map->getLayerByName('hydro_poly');
	//$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('hydro_line');
	$this_layer->set('status', MS_OFF);
	//$this_layer = $map->getLayerByName('hydro_poly');
	//$this_layer->set('status', MS_OFF);
}
if(preg_match("/interstate/", $layer)){
	$this_layer = $map->getLayerByName('interstate');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('interstate');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/roads/", $layer)){
	$this_layer = $map->getLayerByName('roads');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('roads');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/topo_24000/", $layer)){
	$this_layer = $map->getLayerByName('topo_24000');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('topo_24000');
	$this_layer->set('status', MS_OFF);
}
if (isset($owner_aoi) && !empty($owner_aoi)){
	$key_gapown = explode(":", $owner_aoi);
	$filter = "(ogc_fid = {$key_gapown[0]})";
	for($i=1; $i<count($key_gapown); $i++){
		$filter .= " or (ogc_fid = {$key_gapown[$i]})";
	}
	$this_layer = $map->getLayerByName('gapown_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($manage_aoi) && !empty($manage_aoi)){
	$key_gapman = explode(":", $manage_aoi);
	$filter = "(ogc_fid = {$key_gapman[0]})";
	for($i=1; $i<count($key_gapman); $i++){
		$filter .= " or (ogc_fid = {$key_gapman[$i]})";
	}
	$this_layer = $map->getLayerByName('gapman_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($status_aoi) && !empty($status_aoi)){
	$key_gapsta = explode(":", $status_aoi);
	$filter = "(ogc_fid = {$key_gapsta[0]})";
	for($i=1; $i<count($key_gapsta); $i++){
		$filter .= " or (ogc_fid = {$key_gapsta[$i]})";
	}
	$this_layer = $map->getLayerByName('gapstatus_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($county_aoi) && !empty($county_aoi)){
	$key_gap = explode(":", $county_aoi);
	$filter = "(ogc_fid = {$key_gap[0]})";
	for($i=1; $i<count($key_gap); $i++){
		$filter .= " or (ogc_fid = {$key_gap[$i]})";
	}
	$this_layer = $map->getLayerByName('counties_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($topo_aoi) && !empty($topo_aoi)){
	$key_gap = explode(":", $topo_aoi);
	$filter = "(ogc_fid = {$key_gap[0]})";
	for($i=1; $i<count($key_gap); $i++){
		$filter .= " or (ogc_fid = {$key_gap[$i]})";
	}
	$this_layer = $map->getLayerByName('topo_24000_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($basin_aoi) && !empty($basin_aoi)){
	$key_gap = explode(":", $basin_aoi);
	$filter = "(ogc_fid = {$key_gap[0]})";
	for($i=1; $i<count($key_gap); $i++){
		$filter .= " or (ogc_fid = {$key_gap[$i]})";
	}
	$this_layer = $map->getLayerByName('basins_river_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($sub_basin_aoi) && !empty($sub_basin_aoi)){
	$key_gap = explode(":", $sub_basin_aoi);
	$filter = "(ogc_fid = {$key_gap[0]})";
	for($i=1; $i<count($key_gap); $i++){
		$filter .= " or (ogc_fid = {$key_gap[$i]})";
	}
	$this_layer = $map->getLayerByName('sub_basins_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($bird_consv_aoi) && !empty($bird_consv_aoi)){
	$key_gap = explode(":", $bird_consv_aoi);
	$filter = "(ogc_fid = {$key_gap[0]})";
	for($i=1; $i<count($key_gap); $i++){
		$filter .= " or (ogc_fid = {$key_gap[$i]})";
	}
	$this_layer = $map->getLayerByName('bcr_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($ecosys_aoi) && !empty($ecosys_aoi)){
	$this_layer = $map->getLayerByName('ecosys_select');
	$this_layer->set('status', MS_ON);
}
//echo "test";die();
//creating main map
$mapname = "map".rand(0,9999999).".png";

$maploc = "{$mspath}{$mapname}";
$map->setSize($win_w, $win_h);

$map->zoompoint($zoom, $click_point, $win_w, $win_h, $old_extent);

//$map->outputformat->setOption("INTERLACE", "OFF");
$mapimage = $map->draw();

$mapimage->saveImage($maploc);

//create ref map
$refname="refmap".rand(0,9999999).".png";
$refurl="/server_temp/".$refname;
$refname = $mspath.$refname;
$refimage = $map->drawReferenceMap();
$refimage->saveImage($refname);

//get new extent
$new_extent = 	sprintf("%3.6f",$map->extent->minx)." ".
sprintf("%3.6f",$map->extent->miny)." ".
sprintf("%3.6f",$map->extent->maxx)." ".
sprintf("%3.6f",$map->extent->maxy);

$ret =  json_encode(array("mapname"=>$mapname,"extent"=>$new_extent, "refname"=>$refurl));

$logfileptr = fopen("/var/log/weblog/ncgap", "a");
fprintf($logfileptr, "%s   %s\nOutput\n%s\n", date('l dS \of F Y h:i:s A'), __FILE__,  $ret);
fclose($logfileptr);

echo $ret;

?>