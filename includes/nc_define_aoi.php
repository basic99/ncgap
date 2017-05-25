<?php

/**
 * File contains functions to create AOI.
 * 
 * 
 * @package ncgap
 */

$ncdbcon = pg_connect("host=localhost dbname=ncgap user=postgres");

/**
 * Insert new row in table aoi from click points.
 * 
 * Use input data to calculate click points in meters.
 * Create  insert command  using the PostGIS GeometryFromText and intersection functions that calculates intersection of click points with ecosystem boundary.
 * Run command.
 *
 * @param string $aoi_name AOI name
 * @param string $result_x comma separated click x points in px
 * @param stringe $result_y comma separated click y points in px
 * @param string $result_ext map extent in meters
 * @param integer $size_w window width in px
 * @param integer $size_h window height in px
 */

function get_custom_aoi($aoi_name, $result_x, $result_y, $result_ext, $size_w, $size_h ){

	global $ncdbcon;

	//put results into arrays
	$click_x_vals = explode(",", $result_x);
	$click_y_vals = explode(",", $result_y);
	$mapext = explode(" ", $result_ext);

	//convert extent arrays to variables
	$minx = $mapext[0];
	$miny = $mapext[1];
	$maxx = $mapext[2];
	$maxy = $mapext[3];
	$extx = $maxx - $minx;
	$exty = $maxy - $miny;

	//calculate x values of map co-ords
	$i=0;
	foreach($click_x_vals as $click_x_val){
		$x[$i++] = (($click_x_val/$size_w)*$extx+$minx);
	}

	//calculate y values of map co-ords
	$i=0;
	foreach($click_y_vals as $click_y_val){
		$y[$i++] = ((($size_h - $click_y_val)/$size_h)*$exty+$miny);
	}

	//create query to make aoi
	$query_values = "";
	for($i=0; $i<count($x); $i++){
		$query_values = $query_values."$x[$i] $y[$i], ";
	}

	$query_values = $query_values."$x[0] $y[0]";
	//$query = "insert into aoi(wkb_geometry, name) values
	// (GeometryFromText('MULTIPOLYGON((($query_values)))', 32119), '{$aoi_name}')";
	$query = "insert into aoi(wkb_geometry, name) values
     ((select multi(intersection(GeometryFromText('MULTIPOLYGON((($query_values)))', 32119),wkb_geometry)) from nc_aoi where name = 'all'), '{$aoi_name}')";
	//echo $query;
	pg_query($ncdbcon, $query);


}

/**
 * Insert new row in table aoi from selections of predefined AOI.
 * 
 * If one area selected update geometry column and AOI name in table aoi with values. If more than one area selected, then second, third ... areas update
 * values using the PostGIS geomunion function.
 *
 * @param string $aoi_name
 * @param string $owner_aoi colon separated keys of selected areas
 * @param string $manage_aoi colon separated keys of selected areas
 * @param string $status_aoi colon separated keys of selected areas
 * @param string $county_aoi colon separated keys of selected areas
 * @param string $topo_aoi colon separated keys of selected areas
 * @param string $basin_aoi colon separated keys of selected areas
 * @param string $sub_basin_aoi colon separated keys of selected areas
 * @param string $bird_consv_aoi colon separated keys of selected areas
 */

function get_predefined_aoi($aoi_name, $owner_aoi, $manage_aoi, $status_aoi, $county_aoi, $topo_aoi, $basin_aoi, $sub_basin_aoi, $bird_consv_aoi, $ecosys_aoi){

	global $ncdbcon;

	$key_gapown = explode(":", $owner_aoi);
	$key_gapman = explode(":", $manage_aoi);
	$key_gapsta = explode(":", $status_aoi);
	$key_county = explode(":", $county_aoi);
	$key_bcr = explode(":", $topo_aoi);
	$key_basin = explode(":", $basin_aoi);
	$key_sub_basin = explode(":", $sub_basin_aoi);
	$key_bcr = explode(":", $bird_consv_aoi);

	if ($ecosys_aoi == 1) {
		$query = "insert into aoi(name, wkb_geometry) values ('{$aoi_name}',
		(select multi(wkb_geometry) from nc_aoi where ogc_fid = 2))";
		pg_query($ncdbcon, $query);
		return "<p>created aoi named ".$aoi_name."</p>";
	}

	$feature_count = 0;
	$query = "insert into aoi(name) values ('{$aoi_name}')";
	pg_query($ncdbcon, $query);

	if(strlen($key_gapsta[0]) != 0){
		for ($i = 0; $i < count($key_gapsta); $i++){
			if ($feature_count == 0) {
				$query2 = "update aoi set wkb_geometry =
            (select multi(wkb_geometry) from nc_status where ogc_fid = '{$key_gapsta[$i]}')
	         where name = '{$aoi_name}'";
				// echo $query2."\n";
				pg_query($ncdbcon, $query2);
			}else {
				$query3 = "update aoi set wkb_geometry =
            (select multi(geomunion(aoi.wkb_geometry, nc_status.wkb_geometry)) from aoi, nc_status  
            where aoi.name = '{$aoi_name}' and nc_status.ogc_fid = '{$key_gapsta[$i]}')
	         where aoi.name = '{$aoi_name}'";
				//echo $query3."\n";
				pg_query($ncdbcon, $query3);
			}
			$feature_count++;
		}
	}

	if(strlen($key_gapown[0]) != 0){
		for ($i = 0; $i < count($key_gapown); $i++){
			if ($feature_count == 0) {
				$query2 = "update aoi set wkb_geometry =
            (select multi(wkb_geometry) from nc_owner where ogc_fid = '{$key_gapown[$i]}')
             where name = '{$aoi_name}'";
				// echo $query2."\n";
				pg_query($ncdbcon, $query2);
			}else {
				$query3 = "update aoi set wkb_geometry =
            (select multi(geomunion(aoi.wkb_geometry, nc_owner.wkb_geometry)) from aoi, nc_owner 
            where aoi.name = '{$aoi_name}' and nc_owner.ogc_fid = '{$key_gapown[$i]}')
            where aoi.name = '{$aoi_name}'";
				//  echo $query3."\n";
				pg_query($ncdbcon, $query3);
			}
			$feature_count++;
		}
	}

	if(strlen($key_gapman[0]) != 0){
		for ($i = 0; $i < count($key_gapman); $i++){
			if ($feature_count == 0) {
				$query2 = "update aoi set wkb_geometry =
            (select multi(wkb_geometry) from nc_manage where ogc_fid = '{$key_gapman[$i]}') 
            where name = '{$aoi_name}'";            
				pg_query($ncdbcon, $query2);
			}else {
				$query3 = "update aoi set wkb_geometry =
            (select multi(geomunion(aoi.wkb_geometry, nc_manage.wkb_geometry)) from aoi, nc_manage 
            where aoi.name = '{$aoi_name}' and nc_manage.ogc_fid = '{$key_gapman[$i]}') 
            where aoi.name = '{$aoi_name}'";
				// echo $query3;
				pg_query($ncdbcon, $query3);
			}
			$feature_count++;
		}
	}
	if(strlen($key_county[0]) != 0){
		for ($i = 0; $i < count($key_county); $i++){
			if ($feature_count == 0) {
				$query2 = "update aoi set wkb_geometry =
            (select multi(wkb_geometry) from counties where ogc_fid = '{$key_county[$i]}')
	         where name = '{$aoi_name}'";
				//echo $query2."\n";
				pg_query($ncdbcon, $query2);
			}else {
				$query3 = "update aoi set wkb_geometry =
            (select multi(geomunion(aoi.wkb_geometry, counties.wkb_geometry)) from aoi, counties  
            where aoi.name = '{$aoi_name}' and counties.ogc_fid = '{$key_county[$i]}')
	         where aoi.name = '{$aoi_name}'";
				//echo $query3."\n";
				pg_query($ncdbcon, $query3);
			}
			$feature_count++;
		}
	}
	if(strlen($key_bcr[0]) != 0){
		for ($i = 0; $i < count($key_bcr); $i++){
			if ($feature_count == 0) {
				$query2 = "update aoi set wkb_geometry =
	(select multi(wkb_geometry) from nc_bcr where ogc_fid = '{$key_bcr[$i]}')
	where name = '{$aoi_name}'";
				// echo $query2."\n";
				pg_query($ncdbcon, $query2);
			}else {
				$query3 = "update aoi set wkb_geometry =
	(select multi(geomunion(aoi.wkb_geometry, nc_bcr.wkb_geometry)) from aoi, nc_bcr
	where aoi.name = '{$aoi_name}' and nc_bcr.ogc_fid = '{$key_bcr[$i]}')
	where aoi.name = '{$aoi_name}'";
				//echo $query3."\n";
				pg_query($ncdbcon, $query3);
			}
			$feature_count++;
		}
	}
	if(strlen($key_basin[0]) != 0){
		for ($i = 0; $i < count($key_basin); $i++){
			if ($feature_count == 0) {
				$query2 = "update aoi set wkb_geometry =
            (select multi(wkb_geometry) from basins_river where ogc_fid = '{$key_basin[$i]}')
	         where name = '{$aoi_name}'";
				// echo $query2."\n";
				pg_query($ncdbcon, $query2);
			}else {
				$query3 = "update aoi set wkb_geometry =
            (select multi(geomunion(aoi.wkb_geometry, basins_river.wkb_geometry)) from aoi,  basins_river 
            where aoi.name = '{$aoi_name}' and basins_river.ogc_fid = '{$key_basin[$i]}')
	         where aoi.name = '{$aoi_name}'";
				//echo $query3."\n";
				pg_query($ncdbcon, $query3);
			}
			$feature_count++;
		}
	}
	if(strlen($key_sub_basin[0]) != 0){
		for ($i = 0; $i < count($key_sub_basin); $i++){
			if ($feature_count == 0) {
				$query2 = "update aoi set wkb_geometry =
            (select multi(wkb_geometry) from nc_sub_basins where ogc_fid = '{$key_sub_basin[$i]}')
	         where name = '{$aoi_name}'";
				// echo $query2."\n";
				pg_query($ncdbcon, $query2);
			}else {
				$query3 = "update aoi set wkb_geometry =
            (select multi(geomunion(aoi.wkb_geometry, nc_sub_basins.wkb_geometry)) from aoi,  nc_sub_basins 
            where aoi.name = '{$aoi_name}' and nc_sub_basins.ogc_fid = '{$key_sub_basin[$i]}')
	         where aoi.name = '{$aoi_name}'";
				//echo $query3."\n";
				pg_query($ncdbcon, $query3);
			}
			$feature_count++;
		}
	}
	//return "<p>created aoi named ".$aoi_name."</p>";
}

/**
 * Insert into table aoi new row with geometry of user uploaded shapefile.
 * 
 * Use ogr2ogr command to load shapefile into table aoi_upload. 
 * This can create multiple rows for multipolygons. Insert new row in table aoi with AOI name.
 * Select all rows created in table aoi_upload, loop thru and update entry in table aoi with first row, 
 * and then others with geomunion. Update row with intersection of current row and ecosystem boundary.
 *
 * @param string $aoi_name
 * @param string $file_shp 
 */

function get_uploaded_aoi($aoi_name, $file_shp){

	global $ncdbcon;

	//clean temp table
	$query = "delete from aoi_upload where name is null";
	pg_query($ncdbcon, $query);

	//upload file to temp table and give all rows aoi name
	$gdal_cmd = "/usr/local/bin/ogr2ogr -update -append  -f PostgreSQL  PG:'dbname=ncgap user=postgres host=localhost'  {$file_shp} -t_srs 'epsg:32119'  -nln aoi_upload -nlt MULTIPOLYGON &>/dev/null";
	exec($gdal_cmd);
	$query2 = "update aoi_upload set name = '{$aoi_name}' where name is null";
	pg_query($ncdbcon, $query2);

	//create union of temp rows  into aoi table
	$feature_count = $row_count = 0;
	$query = "insert into aoi(name) values ('{$aoi_name}')";
	pg_query($ncdbcon, $query);
	$query = "select ogc_fid from aoi_upload where name = '{$aoi_name}'";
	$result =  pg_query($ncdbcon, $query);
	while($row = pg_fetch_array($result)){
		$key_upload[$row_count++] = $row[0];
	}
	//var_dump($key_upload);

	for ($i = 0; $i < count($key_upload); $i++){
		if ($feature_count == 0) {
			$query2 = "update aoi set wkb_geometry =
            (select multi(wkb_geometry) from aoi_upload where ogc_fid = '{$key_upload[$i]}')
	         where name = '{$aoi_name}'";
			//echo $query2."\n";
			pg_query($ncdbcon, $query2);
		}else {
			$query3 = "update aoi set wkb_geometry =
            (select multi(geomunion(aoi.wkb_geometry, aoi_upload.wkb_geometry)) from aoi,  aoi_upload 
            where aoi.name = '{$aoi_name}' and aoi_upload.ogc_fid = '{$key_upload[$i]}')
	         where aoi.name = '{$aoi_name}'";
			//echo $query3."\n";
			pg_query($ncdbcon, $query3);
		}
		$feature_count++;
	}
	//cut to ecosystem boundary
	$query = "update aoi set wkb_geometry = (select multi(intersection(aoi.wkb_geometry, nc_aoi.wkb_geometry)) from nc_aoi, aoi
	    where nc_aoi.name = 'all' and aoi.name = '{$aoi_name}') where aoi.name = '{$aoi_name}'";
	pg_query($ncdbcon, $query);


	//cleanup temp table
	$query = "delete from aoi_upload where name = '{$aoi_name}'";
	pg_query($ncdbcon, $query);
}

?>