<?php
//copy file to nc_config.php and make changes as necessary

//location of GRASS raster data for webserver
$grass_raster = "/pub/grass/n_carolina/webserv/cellhd/";

//location of GRASS raster data for permanent
$grass_raster_perm = "/pub/grass/n_carolina/PERMANENT/cellhd/";

$GISBASE = "/usr/local/grass-6.4.6";

$GISRC = "/var/www/html/ncgap/grassrc";

$PATH = "/usr/local/grass-6.4.6/bin:/usr/local/grass-6.4.6/scripts:/usr/local/bin:/usr/bin:/bin";

$mspath = "/pub/server_temp/";

$pg_connect = "host=localhost dbname=ncgap user=postgres";

ini_set("log_errors", 1);
?>