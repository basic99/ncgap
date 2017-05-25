<?php
/**
 * file contains one class definition
 * 
 * @package ncgap
 */




$ncdbcon = pg_connect("host=localhost dbname=ncgap user=postgres");

//function to that does work of construction to assign class values
 function create($aoi_predefined, $aoi_name){
    
    global $ncdbcon;

    $key_gapown = explode(":", $aoi_predefined['owner_aoi']);
    $key_gapman = explode(":", $aoi_predefined['manage_aoi']);
    $key_county = explode(":", $aoi_predefined['county_aoi']);
    $key_basin = explode(":", $aoi_predefined['basin_aoi']);
    $key_sub_basin = explode(":", $aoi_predefined['sub_basin_aoi']);
    $key_bcr = explode(":", $aoi_predefined['bcr_aoi']);

    if (strlen($key_gapown[0] == 0)) unset($key_gapown);
    if (strlen($key_gapman[0] == 0)) unset($key_gapman);
    if (strlen($key_county[0] == 0)) unset($key_county);
    if (strlen($key_basin[0] == 0)) unset($key_basin);
    if (strlen($key_sub_basin[0] == 0)) unset($key_sub_basin);
    if (strlen($key_bcr[0] == 0)) unset($key_bcr);

    //calcuate ranges from tables for predefined aoi
    if ($aoi_predefined['ecosys_aoi'] == 1) {
       $query = "select ogc_fid from nc_range";
       $result=pg_query($ncdbcon, $query) or die('failed spatial query to database');
       $i=0;
       while(($row = pg_fetch_array($result)) !== FALSE){
          $range[$i++] = $row[0];
       }
    }elseif (isset($key_gapown) || isset($key_gapman) || isset($key_county) || isset($key_basin) ||isset($key_sub_basin) || isset($key_bcr)){

       $j=0;

       for ($i=0; $i<sizeof($key_county); $i++){
          $query = "select nc_range_ogc_fid from range_from_aoi where counties_ogc_fid  = {$key_county[$i]}";
          $results = pg_query($ncdbcon, $query);
          while($row = pg_fetch_array($results)){
             $range[$j++] = $row['nc_range_ogc_fid'];
          }
       }
       for ($i=0; $i<sizeof($key_basin); $i++){
          $query = "select nc_range_ogc_fid from range_from_aoi where basins_river_ogc_fid  = {$key_basin[$i]}";
          $results = pg_query($ncdbcon, $query);
          while($row = pg_fetch_array($results)){
             $range[$j++] = $row['nc_range_ogc_fid'];
          }
       }
       for ($i=0; $i<sizeof($key_gapown); $i++){
          $query = "select nc_range_ogc_fid from range_from_aoi where nc_owner_ogc_fid  = {$key_gapown[$i]}";
          $results = pg_query($ncdbcon, $query);
          while($row = pg_fetch_array($results)){
             $range[$j++] = $row['nc_range_ogc_fid'];
          }
       }
       for ($i=0; $i<sizeof($key_gapman); $i++){
          $query = "select nc_range_ogc_fid from range_from_aoi where nc_manage_ogc_fid  = {$key_gapman[$i]}";
          $results = pg_query($ncdbcon, $query);
          while($row = pg_fetch_array($results)){
             $range[$j++] = $row['nc_range_ogc_fid'];
          }
       }
       for ($i=0; $i<sizeof($key_sub_basin); $i++){
          $query = "select nc_range_ogc_fid from range_from_aoi where nc_sub_basins_ogc_fid  = {$key_sub_basin[$i]}";
          $results = pg_query($ncdbcon, $query);
          while($row = pg_fetch_array($results)){
             $range[$j++] = $row['nc_range_ogc_fid'];
          }
       }
       for ($i=0; $i<sizeof($key_bcr); $i++){
          $query = "select nc_range_ogc_fid from range_from_aoi where nc_bcr_ogc_fid  = {$key_bcr[$i]}";
          $results = pg_query($ncdbcon, $query);
          while($row = pg_fetch_array($results)){
             $range[$j++] = $row['nc_range_ogc_fid'];
          }
       }
    }
    //else calculate from geometry
    else{
       $query2 = "select ogc_fid from nc_range where intersects(
     (select wkb_geometry from aoi where name = '{$aoi_name}'),
     nc_range.wkb_geometry)";
       $result=pg_query($ncdbcon, $query2) or die('failed spatial query to database');
       $i=0;
       while(($row = pg_fetch_array($result)) !== FALSE){
          $range[$i++] = $row[0];
       }
    }

    //get strelcodes and store as key in associative array with key as strelcode and value 0
    //loop through strelcodes and ranges to find species in aoi and store in array
    $query = "select strelcode from info_spp";
    $result = pg_query($ncdbcon, $query);
    while(($row = pg_fetch_array($result)) !== FALSE){
       $strelcodes[$row[0]] = 0;
    }
    foreach ($strelcodes as $k=>$v) {
       foreach ($range as $rngval) {
          $query = "select {$k} from nc_range where ogc_fid = {$rngval}";
          $result = pg_query($ncdbcon, $query);
          $row = pg_fetch_array($result);
          if(($row[0]!=0) && ($row[0]!=4) && ($row[0]!=5)){
             $strelcodes[$k]=1;
             break;
          }
       }
    }

    //loop through strelcodes to calculate numbers of species for protection status in range
    $all_species =$fed_species =$state_species =$gap_species =$ns_global_species=
    $ns_state_species =$pif_species = 0;
    $query = "select strelcode, strusesa, strsprot, gap_p_all2, strgrank2, strsrank2, intpif from info_spp";
    $result = pg_query($ncdbcon, $query);
    while($row = pg_fetch_array($result)){
       if ($strelcodes[$row['strelcode']] == 1){
          $all_species++;
          if ($row['strusesa'] !== NULL) $fed_species++;
          if ($row['strsprot'] !== NULL) $state_species++;
          if ($row['gap_p_all2'] !== NULL) $gap_species++;
          if ($row['strgrank2'] !== NULL) $ns_global_species++;
          if ($row['strsrank2'] !== NULL) $ns_state_species++;
          if ($row['intpif'] != 0) $pif_species++;
       }
    }
    
    $result = array();
    $result['range'] = $range;
    $result['strelcodes'] = $strelcodes;
    $result['fed_species'] = $fed_species;
    $result['state_species'] = $state_species;
    $result['gap_species'] = $gap_species;
    $result['ns_global'] = $ns_global_species;
    $result['ns_state'] = $ns_state_species;
    $result['pif'] = $pif_species;
    $result['all'] = $all_species;

    return $result;
 
 }// end function create

/**
 * Class is used to calculate total species in AOI, and what class and listing status.
 *
 */

class nc_range_class
{
	/**
	 * Array contains elcodes as key and 0 or 1 as value depending on if predicted in AOI.
	 *
	 * @var array
	 */
	private $strelcodes;

	/**
	 * Array contains primary keys (ogc_fid) to table nc_range of hexagons that overlap AOI.
	 *
	 * @var array
	 */
	private $range;

	/**
	 * Array with keys indicating listing status, e.g. federal, state, gap and value number of species in AOI.
	 *
	 * @var array
	 */
	public $num_species;

	/**
	 * Array with keys of class name and value of number of that class and listing status in AOI.
	 *
	 * @var array
	 */
	private $tot_class;

	/**
	 * SQL command for select from info_spp to calculate number per class.
	 *
	 * @var string
	 */
	private $query;

	/**
	 * From geometry of AOI create array $strelcodes with elcodes as key and 0 or 1 as value depending if species is predicted.
	 * 
	 * Called from controls3.php. Entry aoi_data from table aoi contains seralized array of primary keys used for predefined aoi.
	 * Use these keys to look up precalculated range overlaps of range hexagons and predefined aoi and save in array $range 
	 * primary keys of overlapping hexagons. For custom AOI calculate overlap of AOI with range hexagons using PostGIS intersects function.
	 * Create array $strelcodes from table info_spp and $range array with elcode as key and 0 or 1 as value depending if species is in AOI.
	 * Use $strelcodes and table info_spp to create array $num_species that has numbers of species by listing status. 
	 *
	 * @param string $aoi_name
	 */

	function __construct($aoi_name)
	{
		
      //get predefined AOI data from database
      global $ncdbcon;
		$query = "select aoi_data from aoi where name = '{$aoi_name}'";
		$result = pg_query($ncdbcon, $query);
		$row = pg_fetch_array($result);
		$aoi_predefined = unserialize($row['aoi_data']);
      
      //check of AOI is predefined if so set is_predefined to true to submit function to zend cache
      $is_predefined = false;
      if($aoi_predefined){
         foreach($aoi_predefined as $v){
            if(strlen($v) != 0){$is_predefined = true; break;}
         }
      }
      
      //use zend cache to cache results for function create
		require_once 'Zend/Loader.php';
      Zend_Loader::loadClass('Zend_Cache');
      try{
         $frontendOptions = array(
            'lifetime' => null, // cache lifetime no expiration 
            'automatic_serialization' => true
         );
         $backendOptions = array(
             'cache_dir' => '../../temp/' // Directory where to put the cache files
         );
         // getting a Zend_Cache_Core object
         $cache = Zend_Cache::factory('Function',
                                      'File',
                                      $frontendOptions,
                                      $backendOptions);
      } catch(Exception $e) {
        echo $e->getMessage();
      }
      
      //call create function
      if($is_predefined){
         //submit to zend cache
         $result = $cache->call('create', array($aoi_predefined, "dummy"));
      } else {
         //submit as function not to zend cache for custon AOI
         $result = create($aoi_predefined, $aoi_name);
      }
     
      
      //assign class variable from preceeding calculations
		$this->range = $result['range'];
		$this->strelcodes = $result['strelcodes'];
		$this->num_species['fed_species'] = $result['fed_species'];
		$this->num_species['state_species'] = $result['state_species'];
		$this->num_species['gap_species'] = $result['gap_species'];
		$this->num_species['ns_global_species'] = $result['ns_global'];
		$this->num_species['ns_state_species'] = $result['nc_state'];
		$this->num_species['pif_species'] = $result['pif'];
		$this->num_species['all_species'] = $result['all'];

	}
	////////////////////////////////////////////////////////////////////////////////
	////////////end constructor
	//////////////////////////////////////////////////////////////////////////////////

	/**
	 * Create SQL query, and calculate numbers of species in AOI for each class.
	 * 
	 * Function called by controls4.php with data submitted from controls3.php to select listing status to display.
	 * Create select statement from info_spp, and add where statements according to selections. 
	 * Run command and loop thru results to get total in each class for AOI. 
	 * Save results in array $tot_class  and return.
	 * 
	 * @param string $species
	 * @param string $sel
	 * @param string $fed
	 * @param string $state
	 * @param string $gap
	 * @param string $nsglobal
	 * @param string $nsstate
	 * @param string $pif
	 * @return array
	 */

	function num_class($species, $sel, $fed, $state, $gap, $nsglobal, $nsstate, $pif){
		global $ncdbcon;

		$query = "select strtaxclas, strelcode, strscomnam, strgname from info_spp";
		$i=0;

		//modify query for and selections
		if ( $species ==='prot' && $sel === 'and'){

			//case fed selected
			if($fed == 'on'){
				$query = $query." where (strusesa is not null";
				$i++;
			}

			//case state selected
			if($state == 'on'){
				if($i==0) {
					$query = $query." where (strsprot is not null";
					$i++;
				}else{
					$query = $query." and strsprot is not null";
				}
			}

			//case gap selected
			if($gap == 'on'){
				if($i==0) {
					$query = $query." where (gap_p_all2 is not null";
					$i++;
				}else{
					$query = $query." and gap_p_all2 is not null";
				}
			}

			//case nsglobal selected
			if($nsglobal == 'on'){
				if($i==0) {
					$query = $query." where (strgrank2 is not null";
					$i++;
				}else{
					$query = $query." and strgrank2 is not null";
				}
			}

			//case nsstate selected
			if($nsstate == 'on'){
				if($i==0) {
					$query = $query." where (strsrank2 is not null";
					$i++;
				}else{
					$query = $query." and strsrank2 is not null";
				}
			}

			//case pif selected
			if($pif == 'on'){
				if($i==0) {
					$query = $query." where (intpif <> 0";
					$i++;
				}else{
					$query = $query." and intpif <> 0";
				}
			}
		}

		//modify query for or selections
		if ( $species ==='prot' && $sel === 'or'){
			//case fed selected
			if($fed == 'on'){
				$query = $query." where (strusesa is not null";
				$i++;
			}

			//case state selected
			if($state == 'on'){
				if($i==0) {
					$query = $query." where (strsprot is not null";
					$i++;
				}else{
					$query = $query." or strsprot is not null";
				}
			}

			//case gap selected
			if($gap == 'on'){
				if($i==0) {
					$query = $query." where (gap_p_all2 is not null";
					$i++;
				}else{
					$query = $query." or gap_p_all2 is not null";
				}
			}

			//case nsglobal selected
			if($nsglobal == 'on'){
				if($i==0) {
					$query = $query." where (strgrank2 is not null";
					$i++;
				}else{
					$query = $query." or strgrank2 is not null";
				}
			}

			//case nsstate selected
			if($nsstate == 'on'){
				if($i==0) {
					$query = $query." where (strsrank2 is not null";
					$i++;
				}else{
					$query = $query." or strsrank2 is not null";
				}
			}

			//case pif selected
			if($pif == 'on'){
				if($i==0) {
					$query = $query." where (intpif <> 0";
					$i++;
				}else{
					$query = $query." or intpif <> 0";
				}
			}
			//if($i>0)$query .=")";
		}
		  if($i>0)$query .=")";
		$avian = $mammal = $rept = $amph =  0;

		//get numbers for avian, mammal, rept and amph for all species
		$result = pg_query($ncdbcon, $query);
		while ($row = pg_fetch_array($result)){
			if ($this->strelcodes[$row['strelcode']] == 1){
				if($row['strtaxclas'] == 'AMPHIBIA') $amph++;
				if($row['strtaxclas'] == 'AVES') $avian++;
				if($row['strtaxclas'] == 'MAMMALIA') $mammal++;
				if($row['strtaxclas'] == 'REPTILIA') $rept++;

			}
		}
		// assign to class variable and return values as associative array
		$this->query = $query;
		$this->tot_class['amph'] = $amph;
		$this->tot_class['avian'] = $avian;
		$this->tot_class['mammal'] = $mammal;
		$this->tot_class['rept'] = $rept;
		return $this->tot_class;
	}
	/////////////////////////////////////////////////////////////////////////////////
	// end function num_class
	////////////////////////////////////////////////////////////////////////////////

	/**
	 * Get list of selected species for select box.
	 * 
	 * Use query saved as class variable,  $strelcodes , and $language to output options to 
	 * select element.
	 *
	 * @param string $avian
	 * @param string $mammal
	 * @param string $reptile
	 * @param string $amphibian
	 * @param string $language column in info_spp to use for display
	 */

	function get_species($avian, $mammal, $reptile, $amphibian, $language, $search){
		global $ncdbcon;
		$query = $this->query;
		if(strpos($query, "where") === false){
			$query .= " where strscomnam ilike '%{$search}%'";
			$query .= " or strgname ilike '%{$search}%'" ;
		} else {
			$query .= " and (strscomnam ilike '%{$search}%'";
			$query .= " or strgname ilike '%{$search}%')" ;
		}
		$strelcodes = $this->strelcodes;
		$result = pg_query($ncdbcon, $query);
		while (($row = pg_fetch_array($result))!==FALSE){
			switch ($language){
				case "strscomnam":
					$display = strtolower($row[$language]);
					break;
				case "strgname":
					$display = ucfirst($row[$language]);
					break;
			}
			if ($strelcodes[$row['strelcode']] == 1){
				if($row['strtaxclas'] == 'AMPHIBIA' && $amphibian == 'on') {
					echo "<option value=\"".$row['strscomnam']."\">".$display."</option>";
				}
				if($row['strtaxclas'] == 'AVES' && $avian == 'on'){
					echo "<option value=\"".$row['strscomnam']."\">".$display."</option>";
				}
				if($row['strtaxclas'] == 'MAMMALIA' && $mammal == 'on') {
					echo "<option value=\"".$row['strscomnam']."\">".$display."</option>";
				}
				if($row['strtaxclas'] == 'REPTILIA' && $reptile) {
					echo "<option value=\"".$row['strscomnam']."\">".$display."</option>";
				}
			}
		}
	}

	/**
	 * Open file, write column headers and and output selected species as spreadsheet.
	 *
	 * @param string $avian
	 * @param string $mammal
	 * @param unstring $reptile
	 * @param string $amphibian
	 * @return string
	 */

	function get_species_ss($avian, $mammal, $reptile, $amphibian, $search){
		global $ncdbcon;
		$report_name = "report".rand(0,999999).".xls";

		//open file for writing and write column headers
		$handle = fopen("/pub/server_temp/{$report_name}", "w+");
		$somecontent = "elcode \t scientific name \t commom name \n";
		fwrite($handle, $somecontent);

		//run query and write data to file
		$query = $this->query;
		if(strpos($query, "where") === false){
			$query .= " where strscomnam ilike '%{$search}%'";
			$query .= " or strgname ilike '%{$search}%'" ;
		} else {
			$query .= " and (strscomnam ilike '%{$search}%'";
			$query .= " or strgname ilike '%{$search}%')" ;
		}
		$strelcodes = $this->strelcodes;
		$result = pg_query($ncdbcon, $query);

		while (($row = pg_fetch_array($result))!==FALSE){
			if ($strelcodes[$row['strelcode']] == 1){
				if($row['strtaxclas'] == 'AMPHIBIA' && $amphibian == 'on') {
					$somecontent = $row['strelcode']."\t".$row['strgname']."\t".$row['strscomnam']."\n";
					fwrite($handle, $somecontent);
				}
				if($row['strtaxclas'] == 'AVES' && $avian == 'on'){
					$somecontent = $row['strelcode']."\t".$row['strgname']."\t".$row['strscomnam']."\n";
					fwrite($handle, $somecontent);
				}
				if($row['strtaxclas'] == 'MAMMALIA' && $mammal == 'on') {
					$somecontent = $row['strelcode']."\t".$row['strgname']."\t".$row['strscomnam']."\n";
					fwrite($handle, $somecontent);
				}
				if($row['strtaxclas'] == 'REPTILIA' && $reptile) {
					$somecontent = $row['strelcode']."\t".$row['strgname']."\t".$row['strscomnam']."\n";
					fwrite($handle, $somecontent);
				}
			}
		}
		fclose($handle);
		return $report_name;
	}

	/**
	 * Display species on data_download.php.
	 * 
	 *
	 * @param string $avian
	 * @param string $mammal
	 * @param string $reptile
	 * @param ustring $amphibian
	 */

	//get list of selected species for select box
	function get_species_dnld($avian, $mammal, $reptile, $amphibian, $search){

		global $ncdbcon;
		$query = $this->query;
		if(strpos($query, "where") === false){
			$query .= " where strscomnam ilike '%{$search}%'";
			$query .= " or strgname ilike '%{$search}%'" ;
		} else {
			$query .= " and (strscomnam ilike '%{$search}%'";
			$query .= " or strgname ilike '%{$search}%')" ;
		}
		$strelcodes = $this->strelcodes;
		$result = pg_query($ncdbcon, $query);
		while (($row = pg_fetch_array($result))!==FALSE){
			if ($strelcodes[$row['strelcode']] == 1){
				if($row['strtaxclas'] == 'AMPHIBIA' && $amphibian == 'on') {
					echo "<tr><td><input type='checkbox' onclick='poll();' name='pds' value='".$row['strelcode']."' /></td><td>".$row['strscomnam']."</td></tr>";
				}
				if($row['strtaxclas'] == 'AVES' && $avian == 'on'){
					echo "<tr><td><input type='checkbox' onclick='poll();' name='pds' value='".$row['strelcode']."'/></td><td>".$row['strscomnam']."</td></tr>";
				}
				if($row['strtaxclas'] == 'MAMMALIA' && $mammal == 'on') {
					echo "<tr><td><input type='checkbox' onclick='poll();' name='pds' value='".$row['strelcode']."'/></td><td>".$row['strscomnam']."</td></tr>";
				}
				if($row['strtaxclas'] == 'REPTILIA' && $reptile) {
					echo "<tr><td><input type='checkbox' onclick='poll();' name='pds' value='".$row['strelcode']."'/></td><td>".$row['strscomnam']."</td></tr>";
				}
			}
		}
	}

	//tester function
	function test1(){
		var_dump($this->range);
		var_dump($this->strelcodes);
		var_dump($this->num_species);
	}
}

?>