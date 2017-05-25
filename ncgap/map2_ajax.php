<?php
/**
 * map 2 ajax response page
 * 
 * 
 * 
 * 
 * 
 * 
 */



//date_default_timezone_set("America/New_York");
require('nc_aoi_class.php');
@session_start();


require('nc_define_aoi.php');
require('nc_config.php');
pg_connect($pg_connect);

//click points for navigation
$click_x = $_POST['clickx'];
$click_y = $_POST['clicky'];
//click points  for custom aoi
$posix = $_POST['posi_x'];
$posiy = $_POST['posi_y'];

$extent = $_POST['extent'];
$win_w = $_POST['winw'];
$win_h = $_POST['winh'];
$layer = $_POST['layers'];

//flag that causes extent to be calculated from AOI class instead of previous extent
$zoom_aoi = $_POST['zoomaoi'];
//zoom
$zoom = $_POST['zoom'];
//name of current AOI
$aoi_name = $_POST['aoi_name'];
//name of AOI to be created from previous saved AOI
$aoi_name_saved = $_POST['aoi_name_saved'];
//for new AOI tells how to create
$type = $_POST['type'];

//ogc_fid for predefined aoi
$owner_aoi = $_POST['owner_aoi'];
$manage_aoi = $_POST['manage_aoi'];
$status_aoi = $_POST['status_aoi'];
$county_aoi = $_POST['county_aoi'];
$topo_aoi = $_POST['topo_aoi'];
$basin_aoi = $_POST['basin_aoi'];
$sub_basin_aoi = $_POST['sub_basin_aoi'];
$bird_consv_aoi = $_POST['bird_consv_aoi'];
$ecosys_aoi = $_POST['ecosys_aoi'];
$file_shp = $_POST['shapefile'];

$strelcode = $_POST['strelcode'];
$species_layer = $_POST['species_layer'];
$species_layer_prev = $_POST['species_layer_prev'];
$map_species = $_POST['map_species'];
$richness_species = stripslashes($_POST['richness_species']);
$job_id = $_POST['job_id'];


$post = print_r($_POST, true);
$logfileptr = fopen("/var/log/weblog/ncgap", "a");
fprintf($logfileptr, "\n\n\n%s   %s\nInput\n%s ", date('l dS \of F Y h:i:s A'), __FILE__, $post);
fclose($logfileptr);

$click_point = ms_newPointObj();
$click_point->setXY($click_x, $click_y);

//check that script is still running after mapobj creation
$query = "insert into check_mapobj(job_id ) values ( $job_id );";
pg_query($query);

//create mapobj
$mapfile = "../ncgap.map";
$map = ms_newMapObj($mapfile);

//check that script is still running after mapobj creation
$query = "delete from check_mapobj where job_id = $job_id";
pg_query($query);

//if AOI is undefined then create it in postgis and create new AOI object else get aoi from form variable
if (strlen($aoi_name) ==0){
	//create aoi name
	$now = localtime(time(),1);
	$aoi_name = "aoi".$now['tm_yday'].rand(0,9999999);
	if ($type == 'custom'){
		get_custom_aoi($aoi_name, $posix, $posiy, $extent, $win_w, $win_h );
	}elseif($type == 'predefined'){
		$aoi_predefined['owner_aoi'] = $owner_aoi;
		$aoi_predefined['manage_aoi'] = $manage_aoi;
		$aoi_predefined['county_aoi'] = $county_aoi;
		$aoi_predefined['basin_aoi'] = $basin_aoi;
		$aoi_predefined['sub_basin_aoi'] = $sub_basin_aoi;
		$aoi_predefined['bcr_aoi'] = $bird_consv_aoi;
		$aoi_predefined['ecosys_aoi'] = $ecosys_aoi;
		$aoi_predef_save = pg_escape_string(serialize($aoi_predefined));
		$query = "update aoi set aoi_data = '{$aoi_predef_save}' where name = '{$aoi_name}'";
		get_predefined_aoi($aoi_name, $owner_aoi, $manage_aoi, $status_aoi, $county_aoi, $topo_aoi, $basin_aoi, $sub_basin_aoi, $bird_consv_aoi, $ecosys_aoi);
		pg_query($query);
	}elseif($type == 'uploaded') {
		//echo json_encode(array("mapname"=>$type,"extent"=>$extent, "refname"=>$file_shp)); die();
		get_uploaded_aoi($aoi_name, $file_shp);
	}elseif ($type == 'saved_aoi'){
		$aoi_name = $aoi_name_saved;
		$query = "select description from aoi where name = '{$aoi_name}'";
		$result = pg_query($query);
		$row = pg_fetch_array($result);
		$aoi_desc = $row['description'];
	}
	$new_page = true;
	$_SESSION[$aoi_name] = new nc_aoi_class($aoi_name);
}else{
	$new_page = false;
}
$nc_aoi_class = $_SESSION[$aoi_name];

/*
$logfileptr = fopen("/data2/weblogs/ncgap", "a");
fprintf($logfileptr, "test point in file%s in line %s\n ",  __FILE__, __LINE__);
fclose($logfileptr);
*/


$mapname = "map".rand(0,9999999).".png";
//$mspath = "/pub/server_temp/";
$maploc = "{$mspath}{$mapname}";

//get calculated maps for single species or richness from aoi_class, but first test to see if we can use previous map
if (preg_match("/habitat/", $species_layer)) {
	if ($species_layer != $species_layer_prev) {
		$map_species = $nc_aoi_class->landcover_map($strelcode);
	}
}
if (preg_match("/ownership/", $species_layer)) {
	if ($species_layer != $species_layer_prev) {
		$map_species = $nc_aoi_class->ownership_map($strelcode);
	}
}
if (preg_match("/status/", $species_layer)) {
	if ($species_layer != $species_layer_prev) {
		$map_species = $nc_aoi_class->protection_map($strelcode);
	}
}
if (preg_match("/manage/", $species_layer)) {
	if ($species_layer != $species_layer_prev) {
		$map_species = $nc_aoi_class->management_map($strelcode);
	}
}
if (preg_match("/richness/", $species_layer)) {
	if ($species_layer != $species_layer_prev) {
		$map_species = $nc_aoi_class->richness($richness_species);
	}
}

//convert strelcode to raster name
$raster = "pd_".strtolower($strelcode);

//set layers from controls
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

//set raster to display species maps
if(preg_match("/range/", $species_layer)){
	$this_layer = $map->getLayerByName('range');
	$this_layer->set('classitem', strtolower($strelcode));
	$this_layer->set('status', MS_ON);
	//turn off other rasters
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_OFF);
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_OFF);
}


if(preg_match("/habitat|ownership|status|manage|richness/", $species_layer)){
	$this_layer = $map->getLayerByName('mapcalc');
	$this_layer->set('data', $grass_raster.$map_species);
	$this_layer->set('status', MS_ON);
	//turn off other rasters
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_OFF);
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_OFF);
}


if(preg_match("/predicted/", $species_layer)){
	$this_layer = $map->getLayerByName('mapcalc');
	$this_layer->set('data', $grass_raster_perm.$raster);
	$this_layer->set('status', MS_ON);
	//turn off other rasters
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_OFF);
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_OFF);
}

$filter = "(name = '{$aoi_name}')";
$this_layer = $map->getLayerByName('aoi');
$this_layer->setFilter($filter);
$this_layer->set('status', MS_ON);

//calculate extent from class variables the first time or zoom to aoi, else use previous extent
$extent_obj =  ms_newRectObj();
if ($new_page  || $zoom_aoi) {
	$min_x = $nc_aoi_class->get_minx();
	$min_y = $nc_aoi_class->get_miny();
	$max_x = $nc_aoi_class->get_maxx();
	$max_y = $nc_aoi_class->get_maxy();
	$x_adj = ($max_x - $min_x)*0.1;
	$y_adj = ($max_y - $min_y)*0.1;
	$extent_obj->setExtent($min_x-$x_adj, $min_y-$y_adj, $max_x+$x_adj, $max_y+$y_adj);
}else {
	$mapext = explode(" ", $extent);
	$minx = $mapext[0];
	$miny = $mapext[1];
	$maxx = $mapext[2];
	$maxy = $mapext[3];
	$extent_obj->setExtent($minx, $miny, $maxx, $maxy);
}

$map->setSize($win_w, $win_h);
$map->zoompoint($zoom, $click_point, $win_w, $win_h, $extent_obj);


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

$ret = json_encode(array("mapname"=>$mapname,"extent"=>$new_extent, "refname"=>$refurl, "aoiname"=>$aoi_name, "mapspecies"=>$map_species));

$logfileptr = fopen("/var/log/weblog/ncgap", "a");
fprintf($logfileptr, "\n%s   %s\nOutput\n%s", date('l dS \of F Y h:i:s A'), __FILE__,  $ret);
fclose($logfileptr);

echo $ret;
?>