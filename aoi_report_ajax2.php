<?php
/**
 * File queries table nc_reports  for AJAX request of aoi_reports.php, and returns success and report as JSON.
 * 
 * @package ncgap
 */
$reportid = $_POST['reportid'];
$now = time();

require('nc_config.php');
$ncdbcon = pg_connect($pg_connect);


$query = "select report from nc_reports where reportid = {$reportid}";
$result = pg_query($ncdbcon, $query);
if($row = pg_fetch_array($result))	{
	$report = $row['report'];
	$status = true;
} else {
	$status = false;
}

echo json_encode(array("time"=>$now, "status"=>$status, "rep"=>$report));die();

?>