<?php

$mapfile = "../ncgap.map";

require('nc_config.php');
pg_connect($pg_connect);

/**
 * function to convert clickpoint to map co-ords
 *
 * @param integer $width
 * @param integer $height
 * @param object $point
 * @param object $ext
 * @return array
 */

function img2map($width, $height, $point, $ext){
	if ($point->x && $point->y){
		$dpp_x = ($ext->maxx -$ext->minx)/$width;
		$dpp_y = ($ext->maxy -$ext->miny)/$height;
		$p[0] = $ext->minx + $dpp_x*$point->x;
		$p[1] = $ext->maxy - $dpp_y*$point->y;
	}
	return $p;
}

//get form variables
$win_w = $_POST['win_w'];
$win_h = $_POST['win_h'];

//img_x and img_y from click points on image name = "img" in map.php
$click_x =$_POST['img_x'];
$click_y = $_POST['img_y'] - 68;
$extent_raw = $_POST['extent'];
$query_layer = $_POST['query_layer'];


//create click obj
$click_point = ms_newPointObj();
$click_point->setXY($click_x, $click_y);
//echo "<h3>query results for layer {$query_layer} </h3>";

//save extent to object
$extent = explode(" ", $extent_raw);
$old_extent =  ms_newRectObj();
$old_extent->setextent($extent[0], $extent[1], $extent[2], $extent[3]);

//create map object
$map = ms_newMapObj($mapfile);
$map->setSize($win_w, $win_h);
list($qx, $qy) = img2map($map->width, $map->height, $click_point, $old_extent);
$qpoint = ms_newPointObj();
$qpoint->setXY($qx,$qy);

if(preg_match("/maname|mgrinst1|mand|ownd|stat_d/", $query_layer)){
	@$qlayer = $map->getLayerByName('nc_steward');
	@$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	@$result = $qlayer->getResult(0);
	@$result = $result->shapeindex;
	if($result > 0){
		$query = "select {$query_layer} from nc_steward where ogc_fid = '{$result}'";
		$result2 = pg_query($query);
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = 'n/a';
	}
}
if(preg_match("/basin/", $query_layer)){
	@$qlayer = $map->getLayerByName('basins_river');
	@$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	@$result = $qlayer->getResult(0);
	@$result = $result->shapeindex;
	if($result > 0){
		$query = "select basin_name from basins_river where ogc_fid = '{$result}'";
		$result2 = pg_query($query);
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = 'n/a';
	}
}
if(preg_match("/sub_bas/", $query_layer)){
	@$qlayer = $map->getLayerByName('subwtsh');
	@$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	@$result = $qlayer->getResult(0);
	@$result = $result->shapeindex;
	if ($result > 0){
		$query = "select hu_8_name from nc_sub_basins where ogc_fid = '{$result}'";
		$result2 = pg_query($query);
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = 'n/a';
	}
}
if(preg_match("/bird_consv/", $query_layer)){
	@$qlayer = $map->getLayerByName('bird_consv');
	@$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	@$result = $qlayer->getResult(0);
	@$result = $result->shapeindex;
	if ($result > 0){
		$query = "select bcr_name from  nc_bcr where ogc_fid = '{$result}'";
		$result2 = pg_query($query);
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = 'n/a';
	}
}
if(preg_match("/city/", $query_layer)){
	@$qlayer = $map->getLayerByName('cities');
	@$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	@$result = $qlayer->getResult(0);
	@$result = $result->shapeindex;
	if ($result > 0){
		$query = "select mb_name from cities where ogc_fid = '{$result}'";
		$result2 = pg_query($query);
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = 'n/a';
	}
}
if(preg_match("/county/", $query_layer)){
	@$qlayer = $map->getLayerByName('counties');
	@$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	@$result = $qlayer->getResult(0);
	@$result = $result->shapeindex;
	if($result > 0){
		$query = "select co_name  from counties where ogc_fid = '{$result}'";
		$result2 = pg_query($query);
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = 'n/a';
	}
}
if(preg_match("/water/", $query_layer)){
	@$qlayer = $map->getLayerByName('hydro_poly');
	@$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	@$result = $qlayer->getResult(0);
	@$result = $result->shapeindex;
	if ($result > 0){
		$query = "select waterbody  from hydro_poly where ogc_fid = '{$result}'";
		$result2 = pg_query($query);
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = 'n/a';
	}
}
if(preg_match("/topo/", $query_layer)){
	@$qlayer = $map->getLayerByName('topo_24000');
	@$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	@$result = $qlayer->getResult(0);
	@$result = $result->shapeindex;
	if ($result > 0){
		$query = "select quad_name from topo_24000 where ogc_fid = '{$result}'";
		$result2 = pg_query($query);
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = 'n/a';
	}
}
if(preg_match("/landcover/", $query_layer)){
	@$qlayer = $map->getLayerByName('landcover');
	@$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	@$qlayer->open();
	@$items = $qlayer->getItems(); //not required, use with var_dump($items);
	@$shape = $qlayer->getShape(0, 0);
	@$x = $shape->values['value_0'];
	@$qlayer->close();
	if ($x > 0){
		$query = "select description from lcov_desc where cat_num = {$x}";
		$result2 = pg_query($query);
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = 'n/a';
	}
}

echo json_encode(array("result"=>$msg)); 
ob_flush();
flush();

?>