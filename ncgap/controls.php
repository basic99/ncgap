<?php
require('nc_config.php');
pg_connect($pg_connect);

require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Cache');
try{
   $frontendOptions = array(
      'lifetime' => 604800, // cache lifetime forever
      'automatic_serialization' => true
   );
   $backendOptions = array(
       'cache_dir' => '../../temp/' // Directory where to put the cache files
   );
   // getting a Zend_Cache_Core object
   $cache = Zend_Cache::factory('Output',
                                'File',
                                $frontendOptions,
                                $backendOptions);
} catch(Exception $e) {
  echo $e->getMessage();
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>controls</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="../styles/aqtree3clickable.css" />
<link rel="stylesheet" href="../styles/custom-theme/jquery-ui-1.8.6.custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
<script type="text/javascript" src="../javascript/jquery-ui-1.8.6.custom.min.js" ></script>
<script type="text/javascript" src="../javascript/aqtree3clickable.js"></script>
<script type="text/javascript" src="../javascript/controls_tab1.js"></script>
<script type="text/javascript" src="../javascript/set_tabs.js"></script>
<script type="text/javascript" src="../javascript/controls1.js"></script>

<style type="text/css">
/* <![CDATA[ */
body {padding: 0px; margin: 2px;}
#tabs {font-size: 11px; width: 315px;}
#tabs-1, #tabs-3{overflow: scroll;  width: 270px; font-size: 16px;}
#tabs-2 {overflow: scroll;  width: 298px; font-size: 16px; }
#tabs-2cont {padding-bottom: 0px;}

#pre_btns {font-size: 11px; padding-bottom: 20px;}
#cust  {font-size: 11px; }

#tabs-2 ul {width: 500px;  padding-right: 20px;}
p {font-size: 16px;}
button {width: 90px;}

/* ]]> */
</style>


<script language="javascript" type="text/javascript">
/* <![CDATA[ */
$(function() {
   $( "#tabs" ).tabs();
   $("button").button();

   var win_h = $(window).height();
   $("#tabs-1,#tabs-3").height(win_h - 78);
   $("#tabs-2").height(win_h - 104);

   $("#aoi_reset").click(function(evt) {
      evt.preventDefault();
      pre_reset();
   });
   $("#aoi_submit").click(function(evt) {
      evt.preventDefault();
      aoi_pre_sub();
   });
   $("#aoi_custom").click(function(evt) {
      evt.preventDefault();
      document.getElementById('cont2').style.display = 'none';
      document.getElementById('cust').style.display = 'block';
      cust_start();
   });
   $("#cust_rst").click(function(evt) {
      evt.preventDefault();
      cust_reset();
   });
   $("#cust_sbmt").click(function(evt) {
      evt.preventDefault();
      aoi_cust_sub();
   });
   $("#predef").click(function(evt) {
      evt.preventDefault();
      document.getElementById('cont2').style.display = 'block';
      document.getElementById('cust').style.display = 'none';
      //set_tab2();
      pre_start();

   });
});

/* ]]> */
</script>


</head>

<body >
<!--
<div id="super">
-->
<div id="tabs">
<ul>
<li><a href="#tabs-1">View Layers</a></li>
<li><a href="#tabs-2cont">Define AOI</a></li>
<li><a id="legendtab" href="#tabs-3">Legends</a></li>
</ul>
<form>
<div id="tabs-1">
       <ul class="aqtree3clickable">
<li class="aq3open"><a href="#" class="no_link">Foreground</a>
<ul>
<li><input type="checkbox" name="basins_river"  onclick="loadlayers();" /><a>River Basins</a></li>
<li><input type="checkbox" name="sub_basins"  onclick="loadlayers();" /><a>Sub Basins</a></li>
<li><input type="checkbox" name="bird_consv"  onclick="loadlayers();" /><a>Bird Conservation</a></li>
<li><input type="checkbox" name="cities"  onclick="loadlayers();" /><a>Cities</a></li>
<li><input type="checkbox" name="counties" checked="checked" onclick="loadlayers();" /><a>Counties</a></li>
<li><input type="checkbox" name="hydro"  onclick="loadlayers();" /><a>Rivers</a></li>
<li><input type="checkbox" name="interstate"  onclick="loadlayers();" /><a>Interstates</a></li>
<li><input type="checkbox" name="roads"  onclick="loadlayers();" /><a>Roads</a></li>
<li><input type="checkbox" name="topo_24000"  onclick="loadlayers();" /><a>Topo Maps(1:24000)</a></li>
</ul>
</li>
<li><a href="#" class="no_link">Stewardship</a>
<ul>
<li><input type="radio" name="steward" value="gapown"  onclick="loadlayers();" /><a href="#own" onclick="show_lgnd();">Ownership</a></li>
<li><input type="radio" name="steward" value="gapman"  onclick="loadlayers();" /><a href="#manage" onclick="show_lgnd();">Management</a></li>
<li><input type="radio" name="steward" value="gapsta"  onclick="loadlayers();" /><a href="#status" onclick="show_lgnd();" >Status</a></li>
<li><input type="radio" name="steward" value="none" checked="checked" onclick="loadlayers();" /><a>none</a></li>
</ul>
</li>
<li><a href="#" class="no_link">Background</a>
<ul>
<li><input type="radio" name="background" value="landcover"  onclick="loadlayers();" /><a href="#lcov" onclick="show_lgnd();">Land Cover</a></li>
<li><input type="radio" name="background" value="elevation" checked="checked" onclick="loadlayers();" /><a href="#elev" onclick="show_lgnd();">Elevation</a></li>
<li><input type="radio" name="background" value="none"  onclick="loadlayers();" /><a>none</a></li>
</ul>
</li>
</ul>
</div>

<div id="tabs-2cont">
<div id="cont2">
<div id="pre_btns" >

<button id="aoi_reset">&nbsp;Reset&nbsp;&nbsp;</button>
<button id="aoi_submit">Submit</button>
<button id="aoi_custom">Custom</button>

</div>

<?php
// start caching
// if(!$cache->start('mypage')) {
?>

<div id="tabs-2">

<input type="checkbox" name="ecosys" style="margin-left:4px;" onclick="add_ecosys()"/><span>full extent</span>
<ul class="aqtree3clickable">

<li><a href="#" class="no_link">ownership</a>
<ul>
<li><input type="checkbox"  name="owner_tab2" onclick="show_owner();"  />
<a class="lnk1">Show this layer</a></li>
<?php
$query = "select ownc, ownd, ogc_fid  from nc_owner order by ownc";
$result = pg_query($query);
while ($row = pg_fetch_array($result)){
	echo "<li><input type='checkbox'  name='owner_aoi' value=\"{$row['ogc_fid']}\" onclick='add_owner();' class='lnk2'/><a>{$row['ownd']}</a></li>";
}
?>
</ul>
</li>

<li><a href="#" class="no_link">management</a>
<ul>
<li><input type="checkbox" name="manage_tab2"  onclick="show_manage();" />
<a class="lnk1">Show this layer</a></li>
<?php
$query = "select manc, mand, ogc_fid from nc_manage order by manc";
$result = pg_query($query);
while ($row = pg_fetch_array($result)){
	echo "<li><input type='checkbox' name='manage_aoi' value=\"{$row['ogc_fid']}\" onclick='add_manage();' class='lnk2'/><a>{$row['mand']}</a></li>";
}
?>
</ul>
</li>

<li><a href="#" class="no_link">county</a>
<ul>
<li><input type="checkbox" name="county_tab2"  onclick="show_county();" />
<a class="lnk1" >Show this layer</a></li>
<?php
$query = "select co_name, ogc_fid from counties order by co_name";
$result = pg_query($query);
while ($row = pg_fetch_array($result)){
	echo "<li><input type='checkbox' name='county_aoi' value=\"{$row['ogc_fid']}\" onclick='add_county();' class='lnk2'/><a>{$row['co_name']}</a></li>";
}
?>
</ul>
</li>

<li><a href="#" class="no_link">river basin</a>
<ul>
<li><input type="checkbox" name="basin_tab2"  onclick="show_basin();" />
<a class="lnk1" >Show this layer</a></li>
<?php
$query = "select basin_name, ogc_fid from basins_river order by basin_name";
$result = pg_query($query);
while ($row = pg_fetch_array($result)){
	echo "<li><input type='checkbox' name='basin_aoi' value=\"{$row['ogc_fid']}\" onclick='add_basin();'  class='lnk2'/><a>{$row['basin_name']}</a></li>";
}
?>
</ul>
</li>

<li><a href="#" class="no_link">sub basin</a>
<ul>
<li><input type="checkbox" name="sub_basin_tab2"  onclick="show_sub_basin();" />
<a class="lnk1" >Show this layer</a></li>
<?php
$query = "select hu_8_name, ogc_fid from nc_sub_basins order by hu_8_name";
$result = pg_query($query);
while ($row = pg_fetch_array($result)){
	echo "<li><input type='checkbox' name='sub_basin_aoi' value=\"{$row['ogc_fid']}\" onclick='add_sub_basin();'  class='lnk2'/><a>{$row['hu_8_name']}</a></li>";
}
?>
</ul>
</li>

<li><a href="#" class="no_link">bird conservation region</a>
<ul>
<li><input type="checkbox" name="bcr_tab2"  onclick="show_bcr();" />
<a class="lnk1" >Show this layer</a></li>
<?php
$query = "select  bcr_name, ogc_fid from nc_bcr order by bcr_name";
$result = pg_query($query);
while ($row = pg_fetch_array($result)){
	echo "<li><input type='checkbox' name='bcr_aoi' value=\"{$row['ogc_fid']}\" onclick='add_bcr();'  class='lnk2'/><a>{$row['bcr_name']}</a></li>";
}
?>
</ul>
</li>

</ul>
</div>

<?php
   //end caching
    // $cache->end(); // the output is saved and sent to the browser
}
?>

</div>

</form>

<div id="cust" style="display: none;">
   <button id="cust_rst">Reset</button>
   <button id="cust_sbmt">Submit</button>
   <button id="predef">Predefined</button>


<p>Click on the map to locate the starting point. Move the cursor to the second point and click again.
Continue in this fashion until the polygon describes the AOI. To start over click reset, or to submit AOI click submit. </p>

<p>Create an AOI by <a href="javascript:upload();">uploading</a> a user Shapefile.</p>
</div>
</div>
<div id="tabs-3">

<h4><a href="#lcov">GAP Land Cover</a></h4>
<h4><a href="#owner">Ownership (Stewardship)</a></h4>
<h4><a href="#manage">Management (Stewardship)</a></h4>
<h4><a href="#status">GAP Status (Stewardship)</a></h4>


<a name="elev"></a><br /><br />
<h4>Elevation (meters)</h4>
<img src="/graphics/ncgap/nc_elev_legend.png" alt="elevation legend" /><br /><br />


<a name="lcov"></a><br /><br />
<h4>GAP Land Cover</h4>
<img src="/graphics/ncgap/nc_lc_legend_1.png" alt="cover legend" /><br />
<img src="/graphics/ncgap/nc_lc_legend_2.png" alt="cover legend" /><br />
<img src="/graphics/ncgap/nc_lc_legend_3.png" alt="cover legend" /><br />

<a name="own"></a><br /><br />
<h4>Ownership (Stewardship)</h4>
<img src="/graphics/ncgap/nc_leg_owner.png" alt="ownership legend" /><br />

<a name="manage"></a><br /><br />
<h4>Management (Stewardship)</h4>
<img src="/graphics/ncgap/nc_leg_manage.png" alt="manage legend" /><br />

<a name="status"></a><br /><br />
<h4>GAP Status (Stewardship)</h4>
<img src="/graphics/ncgap/nc_leg_status.png" alt="status legend" /><br />
</div>
</div>

</body>
</html>
