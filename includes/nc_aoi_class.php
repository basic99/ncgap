<?php
/**
 * file contains one class definition
 *
 * @package ncgap
 */


require('nc_config.php');
$ncdbcon = pg_connect($pg_connect);

/**
 * File contains configuration information to use GRASS
 */

require("nc_config.php");
putenv("GISBASE={$GISBASE}");
putenv("GISRC={$GISRC}");
putenv("PATH={$PATH}");

/**
 * Main class used for GRASS calculations
 *
 * This class has a constructor that takes as a parameter an AOI name.
 * The constructor calculates the bounding box and imports a mask into GRASS
 * Various functions that depend on the AOI can then be called
 *
 */

class nc_aoi_class{
	/**
	 * Name of AOI, corresponds to name in table aoi.
	 *
	 * @var string
	 */
	private $aoi_name;
	/**
	 * Name of file that contains a blank mask that is used to burn an AOI with gdal_rasterize.
	 *
	 * @var string
	 */
	private $mask_name;

	/**
	 * Minimun x value of AOI, calculated with PostGIS.
	 *
	 * @var float
	 */
	private $min_x;
	/**
	 * Minimun y value of AOI, calculated with PostGIS.
	 *
	 * @var float
	 */
	private $min_y;
	/**
	 * Maximum x value of AOI, calculated with PostGIS.
	 *
	 * @var float
	 */
	private $max_x;
	/**
	 * Maximum y value of AOI, calculated with PostGIS.
	 *
	 * @var float
	 */
	private $max_y;

	/**
	 * Create new nc_aoi_class object from row in table aoi.
	 *
	 * Use parameter to select row in table aoi that has AOI geometry. Use spatial SQL query to calculate bounding box of AOI.
	 * Run gdal_translate to create blank the size of bounding box.
	 * Run gdal_rasterize to burn AOI into blank. Create GRASS command using r.in.gdal and run command to import AOI mask into GRASS.
	 *
	 * @param string $a
	 */



	public function __construct($a) {


		global $ncdbcon;

		ini_set("error_log", "/var/www/html/ncgap/logs/php-error.log");
		//assign parameter class variable
		$this->aoi_name = $a;

		//get max extents of aoi

		$query_minx = "select x(pointn(exteriorring(envelope(wkb_geometry)), 1)) from aoi where name='{$this->aoi_name}'";
		$query_miny = "select y(pointn(exteriorring(envelope(wkb_geometry)), 1)) from aoi where name='{$this->aoi_name}'";
		$query_maxx = "select x(pointn(exteriorring(envelope(wkb_geometry)), 3)) from aoi where name='{$this->aoi_name}'";
		$query_maxy = "select y(pointn(exteriorring(envelope(wkb_geometry)), 3)) from aoi where name='{$this->aoi_name}'";

		$result = pg_query($ncdbcon, $query_minx);
		$row = pg_fetch_array($result);
		$this->min_x = $row[0];
		// $min_x = $row[0]-10000;
		$min_x = $row[0];

		$result = pg_query($ncdbcon, $query_miny);
		$row = pg_fetch_array($result);
		$this->min_y = $row[0];
		// $min_y = $row[0] - 10000;
		$min_y = $row[0];

		$result = pg_query($ncdbcon, $query_maxx);
		$row = pg_fetch_array($result);
		$this->max_x = $row[0];
		//$max_x = $row[0] + 10000;
		$max_x = $row[0];

		$result = pg_query($ncdbcon, $query_maxy);
		$row = pg_fetch_array($result);
		$this->max_y = $row[0];
		// $max_y = $row[0] + 10000;
		$max_y = $row[0] ;

		//check if can use mask already in GRASS
		$query = "select ogc_fid, aoi_data from aoi where name='{$this->aoi_name}'";
		$result = pg_query($ncdbcon, $query);
		$row = pg_fetch_array($result);
		if (!empty($row['aoi_data'])) {
			$aoi_data = unserialize($row['aoi_data']);

			if ($aoi_data['ecosys_aoi'] == 1) {
				$this->mask_name = 'ecosys';
				return;
			}
			switch ($aoi_data['basin_aoi']){
				case "1":
					$this->mask_name = 'Broad_NC_SC_';
					return;
				case "2":
					$this->mask_name = 'Cape_Fear_NC_';
					return;
				case "3":
					$this->mask_name = 'Catawba_NC_SC_';
					return;
				case "4":
					$this->mask_name = 'Chowan_NC_VA_';
					return;
				case "5":
					$this->mask_name = 'French_Broad_NC_TN_';
					return;
				case "6":
					$this->mask_name = 'Hiwassee_GA_NC_';
					return;
				case "7":
					$this->mask_name = 'Little_Tennessee_GA_NC_TN_';
					return;
				case "8":
					$this->mask_name = 'Lumber_NC_SC_';
					return;
				case "9":
					$this->mask_name = 'Neuse_NC_';
					return;
				case "10":
					$this->mask_name = 'New_NC_VA_';
					return;
				case "11":
					$this->mask_name = 'Pasquotank_NC_VA_';
					return;
				case "12":
					$this->mask_name = 'Roanoke_NC_VA_';
					return;
				case "13":
					$this->mask_name = 'Savannah_GA_NC_SC_';
					return;
				case "14":
					$this->mask_name = 'Tar-Pamlico_NC_';
					return;
				case "15":
					$this->mask_name = 'Watauga_NC_TN_';
					return;
				case "16":
					$this->mask_name = 'Yadkin_NC_SC_';
					return;

			}

			switch ($aoi_data['bcr_aoi']) {
				case "1":
					$this->mask_name = 'SOUTHEASTERN_COASTAL_PLAIN';
					return;
				case "2":
					$this->mask_name = 'APPALACHIAN_MOUNTAINS';
					return;
				case "3":
					$this->mask_name = 'PIEDMONT';
					return;
			}
		}

		//create name for mask
		$blank_file = aoi.rand(0,999999);
		$blank = "/pub/server_temp/".$blank_file;
		$this->mask_name = $blank_file;

		//copy blank file to rectangle of AOI
		$gdal_cmd1 = "/usr/local/bin/gdal_translate -of GTiff -projwin {$min_x} {$max_y} {$max_x} {$min_y} /var/www/html/data/ncgap/nc_blank_b {$blank} &>/dev/null";
		//echo $gdal_cmd1;
		system($gdal_cmd1);

		//burn aoi into blank file
		$gdal_cmd = "/usr/local/bin/gdal_rasterize -burn 1 -sql \"SELECT AsText(wkb_geometry) FROM  aoi  where aoi.name='{$this->aoi_name}' \"   PG:\"host=localhost port=5432 dbname=ncgap user=postgres\"  {$blank} &>/dev/null";
		//echo $gdal_cmd; //die();
		system($gdal_cmd);

		//import mask into GRASS
		$grass_cmd=<<<GRASS_SCRIPT
g.region -d &>/dev/null
r.in.gdal input={$blank} output={$blank_file}a &>/dev/null
cat /var/www/html/ncgap/grass/mask_recl | r.reclass input={$blank_file}a output={$blank_file} &>/dev/null
GRASS_SCRIPT;
		//echo $grass_cmd."<br>";
		error_log($grass_cmd);
		system($grass_cmd);

	}
	// function for testing only
	public function show_vars(){
		echo $this->aoi_name."<br>";
		echo $this->mask_name."<br>";
		echo $this->min_x."<br>";
		echo $this->min_y."<br>";
		echo $this->max_y."<br>";
		echo $this->max_x."<br>";

	}

	// getter functions for max extent of AOI
	public function get_minx(){
		return $this->min_x;
	}

	public function get_maxx(){
		return $this->max_x;
	}

	public function get_miny(){
		return $this->min_y;
	}

	public function get_maxy(){
		return $this->max_y;
	}

	/////////////////////////////////////////////////////////////////////////
	//functions that print reports for all AOI, not dependant on species
	//////////////////////////////////////////////////////////////////////////

	/**
	 * Create land cover report for AOI
	 *
	 * Create GRASS command using r.mapcalc, r.reclass, and r.report that generates land cover report for AOI.
	 * Run command.
	 *
	 */


	public function aoi_landcover(){
		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_lc = '{$this->mask_name}  * nc_lcov' &>/dev/null
cat /var/www/html/ncgap/grass/nc_lcov_recl | r.reclass input={$this->mask_name}calc_lc output={$this->mask_name}recl_lc &>/dev/null
r.report -n map={$this->mask_name}recl_lc units=a,h,p 2>/dev/null
GRASS_SCRIPT;
		return `$str`;
}

/**
	 * Create stewardship management report for AOI
	 *
	 * Create GRASS command using r.mapcalc, r.reclass, and r.report that generates management report for AOI.
	 * Run command.
	 *
	 */


public function aoi_management(){
	$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_man = '{$this->mask_name}  * nc_manage' &>/dev/null
cat /var/www/html/ncgap/grass/nc_man_recl | r.reclass input={$this->mask_name}calc_man output={$this->mask_name}recl_man &>/dev/null
r.report -n map={$this->mask_name}recl_man units=a,h,p 2>/dev/null
GRASS_SCRIPT;
	return `$str`;

}

/**
	 * Create stewardship ownership report for AOI
	 *
	 * Create GRASS command using r.mapcalc, r.reclass, and r.report that generates ownership report for AOI.
	 * Run command.
	 *
	 */

public function aoi_ownership(){
	$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_own = '{$this->mask_name}  * nc_owner' &>/dev/null
cat /var/www/html/ncgap/grass/nc_own_recl | r.reclass input={$this->mask_name}calc_own output={$this->mask_name}recl_own &>/dev/null
r.report -n map={$this->mask_name}recl_own units=a,h,p 2>/dev/null
GRASS_SCRIPT;
	return `$str`;

}

/**
	 * Create GAP status report for AOI
	 *
	 * Create GRASS command using r.mapcalc, r.reclass, and r.report that generates GAP status report for AOI.
	 * Run command.
	 *
	 */

public function aoi_status(){
	$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_stat = '{$this->mask_name}  * nc_status' &>/dev/null
cat /var/www/html/ncgap/grass/nc_sta_recl | r.reclass input={$this->mask_name}calc_stat output={$this->mask_name}recl_stat &>/dev/null
r.report -n map={$this->mask_name}recl_stat units=a,h,p  2>/dev/null
GRASS_SCRIPT;
	return `$str`;

}

/////////////////////////////////////////////////////////////////////////////
//functions that print reports for  AOI, are dependant on species
////////////////////////////////////////////////////////////////////////////

/**
	 * Create predicted distribution for species report in AOI
	 *
	 * Create GRASS command using r.mapcalc, r.reclass, and r.report that generates predicted distribution report for species in AOI.
	 * Run command.
	 *
	 * @param string $a same as strelcode from table info_spp
	 */

public function predicted($a){

	//convert strelcode to raster name
	$raster = "pd_".strtolower($a);
	$str=<<<GRASS_SCRIPT

g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_pred = '{$this->mask_name}  *{$raster}' &>/dev/null
cat /var/www/html/ncgap/grass/nc_pred_recl | r.reclass input={$this->mask_name}calc_pred output={$this->mask_name}recl_pred &>/dev/null
r.report -n map={$this->mask_name}recl_pred units=a,h,p 2>/dev/null
GRASS_SCRIPT;
	return `$str`;
}

/**
	 * Create GAP status for species report in AOI
	 *
	 * Create GRASS command using r.mapcalc, r.reclass, and r.report that generates GAP status report for species in AOI.
	 * Run command.
	 *
	 * @param string $a same as strelcode from table info_spp
	 */

public function species_status($a){

	//convert strelcode to raster name
	$raster = "pd_".strtolower($a);
	$str=<<<GRASS_SCRIPT

g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_stat_sp = '{$this->mask_name}  *{$raster}* nc_status' &>/dev/null
cat /var/www/html/ncgap/grass/nc_sta_recl | r.reclass input={$this->mask_name}calc_stat_sp output={$this->mask_name}recl_stat_sp &>/dev/null
r.report -n map={$this->mask_name}recl_stat_sp units=a,h,p 2>/dev/null
GRASS_SCRIPT;
	return `$str`;
}

/**
	 * Create stewardship ownership for species report in AOI
	 *
	 * Create GRASS command using r.mapcalc, r.reclass, and r.report that generates stewardship ownership report for species in AOI.
	 * Run command.
	 *
	 * @param string $a same as strelcode from table info_spp
	 */

public function species_ownership($a){

	//convert strelcode to raster name
	$raster = "pd_".strtolower($a);
	$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_own_sp = '{$this->mask_name}  *{$raster}* nc_owner' &>/dev/null
cat /var/www/html/ncgap/grass/nc_own_recl | r.reclass input={$this->mask_name}calc_own_sp output={$this->mask_name}recl_own_sp &>/dev/null
r.report -n map={$this->mask_name}recl_own_sp units=a,h,p 2>/dev/null
GRASS_SCRIPT;
	return `$str`;
}

/**
	 * Create stewardship management for species report in AOI
	 *
	 * Create GRASS command using r.mapcalc, r.reclass, and r.report that generates stewardship management report for species in AOI.
	 * Run command.
	 *
	 * @param string $a same as strelcode from table info_spp
	 */


public function species_management($a){

	//convert strelcode to raster name
	$raster = "pd_".strtolower($a);
	$str=<<<GRASS_SCRIPT

g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_man_sp = '{$this->mask_name}  *{$raster}*  nc_manage' &>/dev/null
cat /var/www/html/ncgap/grass/nc_man_recl | r.reclass input={$this->mask_name}calc_man_sp output={$this->mask_name}recl_man_sp &>/dev/null
r.report -n map={$this->mask_name}recl_man_sp units=a,h,p 2>/dev/null
GRASS_SCRIPT;
	return `$str`;
}

/**
	 * Create land cover for species report in AOI
	 *
	 * Create GRASS command using r.mapcalc, r.reclass, and r.report that generates land cover report for species in AOI.
	 * Run command.
	 *
	 * @param string $a same as strelcode from table info_spp
	 */

public function species_landcover($a){

	//convert strelcode to raster name
	$raster = "pd_".strtolower($a);
	$str=<<<GRASS_SCRIPT

g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_lc_sp = '{$this->mask_name}  *{$raster}*  nc_lcov' &>/dev/null
cat /var/www/html/ncgap/grass/nc_lcov_recl | r.reclass input={$this->mask_name}calc_lc_sp output={$this->mask_name}recl_lc_sp &>/dev/null
r.report -n map={$this->mask_name}recl_lc_sp units=a,h,p 2>/dev/null
GRASS_SCRIPT;
	return `$str`;
}

//////////////////////////////////////////////////////////////////////////////////////
//functions that return handle to map created for single species
//////////////////////////////////////////////////////////////////////////////////////

/**
	 * Create land cover for species map in AOI
	 *
	 * Create GRASS command using r.mapcalc and r.colors to generate map of land cover where species is predicted in AOI.
	 * Run command.
	 *
	 * @param string $a same as strelcode from table info_spp
	 * @return string name of map in GRASS
	 */

public function landcover_map($a){

	//convert strelcode to raster name
	$raster = "pd_".strtolower($a);

	//calculate 10% padding
	$x_pad = ($this->max_x - $this->min_x) * 0.1;
	$y_pad = ($this->max_y - $this->min_y) * 0.1;
	$max_x = $this->max_x + $x_pad;
	$min_x = $this->min_x - $x_pad;
	$max_y = $this->max_y + $y_pad;
	$min_y = $this->min_y - $y_pad;

	//create map name
	$map = "map".rand(0,999999);

	$str=<<<GRASS_SCRIPT
g.region n={$max_y} s={$min_y} w={$min_x} e={$max_x} &>/dev/null
r.mapcalc {$map} = '{$raster} *  nc_lcov_256' &>/dev/null
cat  /var/www/html/ncgap/grass/nc_lcov_colors | r.colors map={$map} color=rules &>/dev/null
GRASS_SCRIPT;
	system($str);
	return $map;
}

/**
	 * Create stewardship ownership for species map in AOI
	 *
	 * Create GRASS command using r.mapcalc and r.colors to generate map of stewardship ownership where species is predicted in AOI.
	 * Run command.
	 *
	 * @param string $a same as strelcode from table info_spp
	 * @return string name of map in GRASS
	 */

public function ownership_map($a){

	//convert strelcode to raster name
	$raster = "pd_".strtolower($a);

	//calculate 10% padding
	$x_pad = ($this->max_x - $this->min_x) * 0.1;
	$y_pad = ($this->max_y - $this->min_y) * 0.1;
	$max_x = $this->max_x + $x_pad;
	$min_x = $this->min_x - $x_pad;
	$max_y = $this->max_y + $y_pad;
	$min_y = $this->min_y - $y_pad;

	//create map name
	$map = "map".rand(0,999999);

	$str=<<<GRASS_SCRIPT
g.region n={$max_y} s={$min_y} w={$min_x} e={$max_x} &>/dev/null
r.mapcalc {$map} = '{$raster} *  nc_owner_256' &>/dev/null
cat  /var/www/html/ncgap/grass/nc_owner_color | r.colors map={$map} color=rules &>/dev/null
GRASS_SCRIPT;
	system($str);
	return $map;
}

/**
	 * Create GAP status  for species map in AOI
	 *
	 * Create GRASS command using r.mapcalc and r.colors to generate map of GAP status where species is predicted in AOI.
	 * Run command.
	 *
	 * @param string $a same as strelcode from table info_spp
	 * @return string name of map in GRASS
	 */

public function protection_map($a){

	//convert strelcode to raster name
	$raster = "pd_".strtolower($a);

	//calculate 10% padding
	$x_pad = ($this->max_x - $this->min_x) * 0.1;
	$y_pad = ($this->max_y - $this->min_y) * 0.1;
	$max_x = $this->max_x + $x_pad;
	$min_x = $this->min_x - $x_pad;
	$max_y = $this->max_y + $y_pad;
	$min_y = $this->min_y - $y_pad;

	//create map name
	$map = "map".rand(0,999999);

	$str=<<<GRASS_SCRIPT
g.region n={$max_y} s={$min_y} w={$min_x} e={$max_x} &>/dev/null
r.mapcalc {$map} = '{$raster} *  nc_status' &>/dev/null
cat  /var/www/html/ncgap/grass/nc_sta_color | r.colors map={$map} color=rules &>/dev/null
GRASS_SCRIPT;
	system($str);
	return $map;
}

/**
	 * Create stewardship management for species map in AOI
	 *
	 * Create GRASS command using r.mapcalc and r.colors to generate map of stewardship management where species is predicted in AOI.
	 * Run command.
	 *
	 * @param string $a same as strelcode from table info_spp
	 * @return string name of map in GRASS
	 */

public function management_map($a){

	//convert strelcode to raster name
	$raster = "pd_".strtolower($a);

	//calculate 10% padding
	$x_pad = ($this->max_x - $this->min_x) * 0.1;
	$y_pad = ($this->max_y - $this->min_y) * 0.1;
	$max_x = $this->max_x + $x_pad;
	$min_x = $this->min_x - $x_pad;
	$max_y = $this->max_y + $y_pad;
	$min_y = $this->min_y - $y_pad;

	//create map name
	$map = "map".rand(0,999999);

	$str=<<<GRASS_SCRIPT
g.region n={$max_y} s={$min_y} w={$min_x} e={$max_x} &>/dev/null
r.mapcalc {$map} = '{$raster} *  nc_manage_256' &>/dev/null
cat  /var/www/html/ncgap/grass/nc_manage_color | r.colors map={$map} color=rules &>/dev/null
GRASS_SCRIPT;
	system($str);
	return $map;
}

////////////////////////////////////////////////////////////////////////////
//function that returns handle to map created for richness
//accepts as parameter colon delimted species list
//richness report
///////////////////////////////////////////////////////////////////////////

/**
	 * Create richness map of selected species.
	 *
	 * Create GRASS command using r.mapcalc and r.colors to generate richness map of selected species in AOI.
	 * Run command.
	 *
	 * @param string $a colon separated list of species same as strscomnam from info_spp
	 * @return string name of map in GRASS
	 */

public function richness($a){
	global $ncdbcon;

	$species = explode(":", $a);
	for ($i=0; $i<sizeof($species); $i++){
		$species_esc = addslashes($species[$i]);
		$query = "select strelcode from info_spp where strscomnam  = '$species_esc'";
		$result = pg_query($ncdbcon, $query);
		$row = pg_fetch_array($result);
		$layers[$i] = "pd_".strtolower($row[0]);
	}
	$layer_str = implode(" + ", $layers);
	$rules_file = "/var/www/html/ncgap/grass/richness_rule";

	//calculate 10% padding
	$x_pad = ($this->max_x - $this->min_x) * 0.1;
	$y_pad = ($this->max_y - $this->min_y) * 0.1;
	$max_x = $this->max_x + $x_pad;
	$min_x = $this->min_x - $x_pad;
	$max_y = $this->max_y + $y_pad;
	$min_y = $this->min_y - $y_pad;

	//create map name
	$map = "map".rand(0,999999);
	$str=<<<GRASS_SCRIPT
g.region n={$max_y} s={$min_y} w={$min_x} e={$max_x} &>/dev/null
r.mapcalc  {$map} = '{$layer_str}' &>/dev/null
cat {$rules_file} | r.colors map={$map} color=rules &>/dev/null
GRASS_SCRIPT;
	system($str);
	return $map;
}

/**
	 * Create richness report for selected species in AOI.
	 *
	 * Create GRASS command using r.mapcalc and r.colors to generate richness report of selected species in AOI.
	 * Run command.
	 *
	 * @param string $a colon separated list of species same as strscomnam from info_spp
	 */

public function richnessreport($a){
	global $ncdbcon;
	$species = explode(":", $a);
	for ($i=0; $i<sizeof($species); $i++){
		$species_esc = addslashes($species[$i]);
		$query = "select strelcode from info_spp where strscomnam  = '$species_esc'";
		$result = pg_query($ncdbcon, $query);
		$row = pg_fetch_array($result);
		$layers[$i] = "pd_".strtolower($row[0]);
	}
	$layer_str = implode(" + ", $layers);
	//var_dump($layers);
	$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}richness_report = '{$this->mask_name}  *({$layer_str})' &>/dev/null
r.report -n map={$this->mask_name}richness_report units=a,h,p 2>/dev/null
GRASS_SCRIPT;
	return `$str`;

}

/**
	 * Save a copy of current richness map on server for data download.
	 *
	 * Create GRASS command with r.out.gdal to convert GRASS map to geotiff. Run command.
	 *
	 * @param string $a this value comes from the variable $map_species in map2.php which is the name of the displayed richness map
	 * @return string
	 */

public function richnessexport($a){
	$map = "richness".rand(0,9999999).".tif";
	$str=<<<GRASS_SCRIPT
r.out.gdal input={$a} format=GTiff type=Byte output=/pub/richness_export/{$map}
GRASS_SCRIPT;
	system($str);
	return $map;

}


}


?>