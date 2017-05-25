<?php
/**
 * Template for nc_config.php
 * 
 * This file contains configuration needed to run GRASS and access GRASS rasters. 
 * Update values and copy to nc_config.php.
 * 
 * @package ncgap
 */

//copy file to nc_config.php and make changes as necessary

//location of GRASS raster data for webserver
$grass_raster = "/data/n_carolina/webserv/cellhd/";

//location of GRASS raster data for permanent
$grass_raster_perm = "/data/n_carolina/PERMANENT/cellhd/";

$GISBASE = "/usr/local/grass-6.1.cvs";

$GISRC = "/var/www/html/ncgap/grassrc";

$PATH = "/usr/local/grass-6.1.cvs/bin:/usr/local/grass-6.1.cvs/scripts:/usr/local/bin:/usr/bin:/bin";
?>