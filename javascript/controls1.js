
//function polls checked layer selections, puts result on parent.map 
function add_owner(){
	var length = document.forms[0].owner_aoi.length;
	var previous = "";
	document.forms[0].steward[0].checked = true;
	document.forms[0].owner_tab2.checked = true;
	for (var i=0;  i<length; i++){
		if(document.forms[0].owner_aoi[i].checked){
			var selected = document.forms[0].owner_aoi[i].value;
			if (previous.length == 0){
				previous = selected;
			}else{
				previous = previous + ":" + selected;
			}
		}
	}
	parent.map.document.getElementById('query_layer').value = 'ownd';
	parent.map.set_query();
	parent.map.document.getElementById('owner_ajax').value = previous;
	parent.map.document.getElementById('owner_aoi').value = previous;
	parent.map.document.getElementById('owner_pdf').value = previous;
	loadlayers();
}

function add_manage(){
	var length = document.forms[0].manage_aoi.length;
	var previous = "";
	document.forms[0].steward[1].checked = true;
	document.forms[0].manage_tab2.checked = true;
	for (var i=0;  i<length; i++){
		if(document.forms[0].manage_aoi[i].checked){
			var selected = document.forms[0].manage_aoi[i].value;
			if (previous.length == 0){
				previous = selected;
			}else{
				previous = previous + ":" + selected;
			}
		}
	}
	parent.map.document.getElementById('query_layer').value = 'mand';
	parent.map.set_query();
	parent.map.document.getElementById('manage_ajax').value = previous;
	parent.map.document.getElementById('manage_aoi').value = previous;
	parent.map.document.getElementById('manage_pdf').value = previous;
	loadlayers();
}

function add_status(){
	var length = document.forms[0].status.length;

}

function add_county(){
	var length = document.forms[0].county_aoi.length;
	var previous = "";
	document.forms[0].counties.checked = true;
	document.forms[0].county_tab2.checked = true;
	for (var i=0;  i<length; i++){
		if(document.forms[0].county_aoi[i].checked){
			var selected = document.forms[0].county_aoi[i].value;
			if (previous.length == 0){
				previous = selected;
			}else{
				previous = previous + ":" + selected;
			}
		}
	}
	parent.map.document.getElementById('query_layer').value = 'county';
	parent.map.set_query();
	parent.map.document.getElementById('county_ajax').value = previous;
	parent.map.document.getElementById('county_aoi').value = previous;
	parent.map.document.getElementById('county_pdf').value = previous;
	loadlayers();
}
function add_topo(){
	var length = document.forms[0].topo.length;

}

function add_basin(){
	var length = document.forms[0].basin_aoi.length;
	var previous = "";
	document.forms[0].basins_river.checked = true;
	document.forms[0].basin_tab2.checked = true;
	for (var i=0;  i<length; i++){
		if(document.forms[0].basin_aoi[i].checked){
			var selected = document.forms[0].basin_aoi[i].value;
			if (previous.length == 0){
				previous = selected;
			}else{
				previous = previous + ":" + selected;
			}
		}
	}
	parent.map.document.getElementById('query_layer').value = 'basin';
	parent.map.set_query();
	parent.map.document.getElementById('basin_ajax').value = previous;
	parent.map.document.getElementById('basin_aoi').value = previous;
	parent.map.document.getElementById('basin_pdf').value = previous;
	loadlayers();
}
function add_sub_basin(){
	//alert('hello');
	var length = document.forms[0].sub_basin_aoi.length;
	var previous = "";
	document.forms[0].sub_basins.checked = true;
	document.forms[0].sub_basin_tab2.checked = true;
	//alert('hello');
	for (var i=0;  i<length; i++){
		if(document.forms[0].sub_basin_aoi[i].checked){
			var selected = document.forms[0].sub_basin_aoi[i].value;
			if (previous.length == 0){
				previous = selected;
			}else{
				previous = previous + ":" + selected;
			}
		}
	}
	parent.map.document.getElementById('query_layer').value = 'sub_bas';
	parent.map.set_query();
	parent.map.document.getElementById('sub_basin_ajax').value = previous;
	parent.map.document.getElementById('sub_basin_aoi').value = previous;
	parent.map.document.getElementById('sub_basin_pdf').value = previous;
	loadlayers();

}

function add_bcr(){
	var length = document.forms[0].bcr_aoi.length;
	var previous = "";
	document.forms[0].bird_consv.checked = true;
	//document.forms[0].sub_basin_tab2.checked = true;
	for (var i=0;  i<length; i++){
		if(document.forms[0].bcr_aoi[i].checked){
			var selected = document.forms[0].bcr_aoi[i].value;
			if (previous.length == 0){
				previous = selected;
			}else{
				previous = previous + ":" + selected;
			}
		}
	}
	parent.map.document.getElementById('query_layer').value = 'bird_consv';
	parent.map.set_query();
	parent.map.document.getElementById('bird_consv_ajax').value = previous;
	parent.map.document.getElementById('bird_consv_aoi').value = previous;
	parent.map.document.getElementById('bird_consv_pdf').value = previous;
	loadlayers();
}

function add_ecosys(){
	if(document.forms[0].ecosys.checked){
		parent.map.document.getElementById('ecosys_aoi').value = "1";
		parent.map.document.getElementById('ecosys_ajax').value = "1";
	}else{
		parent.map.document.getElementById('ecosys_aoi').value = "";
		parent.map.document.getElementById('ecosys_ajax').value = "";
	}
	loadlayers();
}

function pre_reset(){
	for( var i = 0; i < document.forms[0].owner_aoi.length; i++) document.forms[0].owner_aoi[i].checked = false;
	for( var i = 0; i < document.forms[0].manage_aoi.length; i++) document.forms[0].manage_aoi[i].checked = false;
	for( var i = 0; i < document.forms[0].county_aoi.length; i++) document.forms[0].county_aoi[i].checked = false;
	for( var i = 0; i < document.forms[0].basin_aoi.length; i++) document.forms[0].basin_aoi[i].checked = false;
	for( var i = 0; i < document.forms[0].sub_basin_aoi.length; i++) document.forms[0].sub_basin_aoi[i].checked = false;
	for( var i = 0; i < document.forms[0].bcr_aoi.length; i++) document.forms[0].bcr_aoi[i].checked = false;
	parent.map.clear_aois();

}

function aoi_pre_sub(){
	if(parent.map.document.forms[0].owner.value == '' &&
	parent.map.document.forms[0].manage.value == '' &&
	parent.map.document.forms[0].county.value == '' &&
	parent.map.document.forms[0].sub_basin.value == '' &&
	parent.map.document.forms[0].basin.value == '' &&
	parent.map.document.forms[0].ecosys.value == '' &&
	parent.map.document.forms[0].bird_consv.value == ''){
		alert('must select AOI before submitting')
	} else {
		parent.map.document.getElementById('aoi_type').value = 'predefined';
		parent.map.document.getElementById('zoom').value = '1';
		parent.map.document.getElementById('mode').value = "pan";
		parent.map.document.getElementById('fm1').submit();
		//parent.gears = window.open("../gears.html","","width=400,height=200, top=200, left=200");
		//  alert('hello');
	}
}

function aoi_cust_sub(){
	if((parent.map.posix.length < 3)){
		alert('must select AOI before submitting')
	} else {
		parent.map.document.getElementById('aoi_type').value = 'custom';
		parent.map.document.getElementById('click_val_x').value = parent.map.posix;
		parent.map.document.getElementById('click_val_y').value = parent.map.posiy;
		parent.map.document.getElementById('zoom').value = '1';
		parent.map.document.getElementById('mode').value = "pan";
		parent.map.document.getElementById('fm1').submit();
		//parent.gears = window.open("../gears.html","","width=400,height=200, top=200, left=200");

	}
}

//this function  makes  myMap display to draw custom aoi
function cust_start(){
	parent.map.draw();
}

function pre_start(){
	cust_reset();
	parent.map.pan();
}


function cust_reset(){
	parent.map.posix.length = 0;
	parent.map.posiy.length = 0;
	parent.map.jg_box.clear();
}
function load_selections(){
	//alert('hello');
	var layers = parent.map.document.getElementById('layers').value;
	//alert(layers);
	if (layers.indexOf('basins_river') != -1){
		document.forms[0].basins_river.checked = true;
		document.forms[0].basin_tab2.checked = true;
	}else{
		document.forms[0].basins_river.checked = false;
		document.forms[0].basin_tab2.checked = false;
	}
	if (layers.indexOf('counties') != -1){
		document.forms[0].counties.checked = true;
		document.forms[0].county_tab2.checked = true;
	}else{
		document.forms[0].counties.checked = false;
		document.forms[0].county_tab2.checked = false;
	}
	if (layers.indexOf('sub_basins') != -1){
		document.forms[0].sub_basins.checked = true;
		document.forms[0].sub_basin_tab2.checked = true;
	}else{
		document.forms[0].sub_basins.checked = false;
		document.forms[0].sub_basin_tab2.checked = false;
	}
	if (layers.indexOf('bird_consv') != -1){
		document.forms[0].bird_consv.checked = true;
		document.forms[0].bcr_tab2.checked = true;
	}else{
		document.forms[0].bird_consv.checked = false;
		document.forms[0].bcr_tab2.checked = false;
	}
	if (layers.indexOf('cities') != -1){document.forms[0].cities.checked = true;}
	if (layers.indexOf('hydro') != -1){document.forms[0].hydro.checked = true;}
	if (layers.indexOf('interstate') != -1){document.forms[0].interstate.checked = true;}
	if (layers.indexOf('roads') != -1){document.forms[0].roads.checked = true;}
	if (layers.indexOf('topo_24000') != -1){document.forms[0].topo_24000.checked = true;}

	if (layers.indexOf('ownership') != -1){
		document.forms[0].steward[0].checked = true;
		document.forms[0].owner_tab2.checked = true;
		document.forms[0].manage_tab2.checked = false;
	}else  if (layers.indexOf('management') != -1){
		document.forms[0].steward[1].checked = true;
		document.forms[0].manage_tab2.checked = true;
		document.forms[0].owner_tab2.checked = false;
	}else if (layers.indexOf('status') != -1){
		document.forms[0].steward[2].checked = true;
	}else{
		document.forms[0].steward[3].checked = true;
		document.forms[0].owner_tab2.checked = false;
		document.forms[0].manage_tab2.checked = false;
	}

	if (layers.indexOf('landcover') != -1){
		document.forms[0].background[0].checked = true;
	}else if (layers.indexOf('elevation') != -1){
		document.forms[0].background[1].checked = true;
	}else{
		document.forms[0].background[2].checked = true;
	}

}
function show_owner(){
	if(document.forms[0].owner_tab2.checked){
		document.forms[0].steward[0].checked = true;
		parent.map.document.forms[0].query_layer.value = 'ownd';
	}else{
		document.forms[0].steward[0].checked = false;
	}
	loadlayers();
}
function show_manage(){
	//alert('hello');
	if(document.forms[0].manage_tab2.checked){
		document.forms[0].steward[1].checked = true;
		parent.map.document.forms[0].query_layer.value = 'mand';
	}else{
		document.forms[0].steward[1].checked = false;
	}
	loadlayers();
}
function show_county(){
	// alert('hello');
	if(document.forms[0].county_tab2.checked){
		document.forms[0].counties.checked = true;
		parent.map.document.forms[0].query_layer.value = 'county';
	}else{
		document.forms[0].counties.checked = false;
	}
	loadlayers();
}
function show_basin(){
	//alert('hello');
	if(document.forms[0].basin_tab2.checked){
		document.forms[0].basins_river.checked = true;
		parent.map.document.forms[0].query_layer.value = 'basin';
	}else{
		document.forms[0].basins_river.checked = false;
	}
	loadlayers();
}
function show_sub_basin(){
	//alert('hello');
	if(document.forms[0].sub_basin_tab2.checked){
		document.forms[0].sub_basins.checked = true;
		parent.map.document.forms[0].query_layer.value = 'sub_bas';
	}else{
		document.forms[0].sub_basins.checked = false;
	}
	loadlayers();
}
function show_bcr(){
	//alert('hello');
	if(document.forms[0].bcr_tab2.checked){
		document.forms[0].bird_consv.checked = true;
		parent.map.document.forms[0].query_layer.value = 'bird_consv';
	}else{
		document.forms[0].bird_consv.checked = false;
	}
	loadlayers();
}

function upload(){
	window.open("../upload.php","", "height=300,width=600")

}