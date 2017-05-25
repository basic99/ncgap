<?php
/**
 * create PDF using FPDF library
 * 
 * File gathers $_POST data submitted from form fm5 from map.php or map2.php.
 * Creates MapServer MapObj object, selects layers passed in form.
 * For 300 dpi map set map size 3000 x 1800, for 72 dpi set at 720 x 432.
 * Add legend pages, and create pdf.
 * 
 * @link http://www.fpdf.org/
 * @package ncgap
 */
set_time_limit(300);
require('fpdf.php');
require('nc_config.php');
/**
 * Configuration information to access GRASS data.
 */
require('nc_config.php');
//set mapfile and load mapscript if not already loaded
$mapfile = "ncgap.map";

if(!extension_loaded('MapScript')){
	dl("php_mapscript.so");
}

//get form variables
$win_w = $_POST['win_w'];
$win_h = $_POST['win_h'];
$extent_raw = $_POST['extent'];
$mode = $_POST['mode'];
$layer = $_POST['layers2'];
$owner_aoi = $_POST['owner'];
$manage_aoi = $_POST['manage'];
$status_aoi = $_POST['status'];
$county_aoi = $_POST['county'];
$topo_aoi = $_POST['topo'];
$basin_aoi = $_POST['basin'];
$sub_basin_aoi = $_POST['sub_basin'];
$bird_consv_aoi = $_POST['bird_consv'];

$desc = $_POST['desc'];
$dpi = $_POST['dpi'];

//type of map, eg predicted, managed, etc.
$species_layer = $_POST['species_layer'];
//strelcode, used to select correct range map
$strelcode = $_POST['strelcode'];

$map_species = $_POST['map_species'];
$aoi_name = $_POST['aoi_name'];

//var_dump($_POST); die();

//create click obj
$click_point = ms_newPointObj();
$click_x=$win_w/2;
$click_y=$win_h/2;
$click_point->setXY($click_x, $click_y);

//save extent to rect
$extent = explode(" ", $extent_raw);
$old_extent =  ms_newRectObj();
$old_extent->setextent($extent[0], $extent[1], $extent[2], $extent[3]);


//create map object
$map = ms_newMapObj($mapfile);

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
	$this_layer = $map->getLayerByName('hydro_poly');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('hydro_line');
	$this_layer->set('status', MS_OFF);
	$this_layer = $map->getLayerByName('hydro_poly');
	$this_layer->set('status', MS_OFF);
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

//convert strelcode to raster name
$raster = "pd_".strtolower($strelcode);

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

$filter = "(name = '{$aoi_name}')";
$this_layer = $map->getLayerByName('aoi');
$this_layer->setFilter($filter);
$this_layer->set('status', MS_ON);

//create map for pdf

$pdfmapname = "map".rand(0,9999999).".png";

$pdfmaploc = "{$mspath}{$pdfmapname}";
if ($dpi == 300) {
	$map->setSize(3000, 1800);
	$map->scalebar->set("width", 400);
	$map->scalebar->set("height", 15);
	$map->scalebar->label->set("size", "20");
	$map->getLayerByName('interstate')->getClass(0)->label->set("size", "20");
	$map->getLayerByName('hydro_line')->getClass(0)->label->set("size", "20");
	$map->getLayerByName('roads')->getClass(0)->label->set("size", "20");

} else {
	$map->setSize(720, 432);
}
$map->zoompoint(1, $click_point, $win_w, $win_h, $old_extent);
//$map->outputformat->setOption("INTERLACE", "OFF");
$pdfmapimage = $map->draw();
$pdfmapimage->saveImage($pdfmaploc);

//////////////////////////////////////////////////////////////

/**
 * Class extends FPDF by adding footer with logo
 * 
 *
 */

class PDF extends FPDF
{
	function Footer()
	{
		$this->Image('/var/www/html/graphics/ncgap/USGS_GAP_BaSIC_PDF_Logo_NC.png',0.5,7.5,0,0.5);
	}
}

//Instanciation of inherited class
$pdf=new PDF('L','in', 'Letter');
$pdf->SetFont('Arial','B',24);
$pdf->SetMargins(0.5,0.5);
$pdf->AddPage();


//print title
//$pdf->Cell(3);
$pdf->Cell(0,0,$desc,0,0);

//output map
$pdf->Image($mspath.$pdfmapname,0.5,1.25,10,6);

//add legends page if needed
if((preg_match("/landcover/", $layer)   && (strlen($species_layer) == 0)) ||  preg_match("/habitat/", $species_layer))
{
	$pdf->AddPage();
	$pdf->Cell(0,0,'GAP Land Cover',0,0);
	$pdf->Image('/var/www/html/graphics/ncgap/nc_lc_legend.png',0.5,1.75,10,0);
}


if(preg_match("/elevation/", $layer) && (strlen($species_layer) == 0)){
	$pdf->AddPage();
	$pdf->Cell(0,0,'Elevation (meters)',0,0);
	$pdf->Image('/var/www/html/graphics/ncgap/nc_elev_legend.png',0.5,1.25,0,6);
}

if(preg_match("/management/", $layer) || preg_match("/manage/", $species_layer) ){
	$pdf->AddPage();
	$pdf->Cell(0,0,'Management (Stewardship)',0,0);
	$pdf->Image('/var/www/html/graphics/ncgap/nc_leg_manage.png',0.5,1.25,0,5);
}
if(preg_match("/ownership/", $layer) || preg_match("/ownership/", $species_layer)){
	$pdf->AddPage();
	$pdf->Cell(0,0,'Ownership (Stewardship)',0,0);
	$pdf->Image('/var/www/html/graphics/ncgap/nc_leg_owner.png',0.5,1.25,0,5);
}
if(preg_match("/status/", $layer)  || preg_match("/status/", $species_layer)){
	$pdf->AddPage();
	$pdf->Cell(0,0,'GAP Status (Stewardship)',0,0);
	$pdf->Image('/var/www/html/graphics/ncgap/nc_leg_status.png',0.5,1.25,4,0);
}
if(preg_match("/range/", $species_layer)){
	$pdf->AddPage();
	$pdf->Cell(0,0,'Known range',0,0);
	$pdf->Image('/var/www/html/graphics/ncgap/nc_range_leg.png',0.5,1.25,1.8,0);
}
$file_name = "ncgap".rand(1,1000).".pdf";
$pdf->Output($file_name, I);
?>