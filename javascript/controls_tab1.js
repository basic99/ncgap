function loadlayers(){
	
	if(document.forms[0].background[0].checked) var layer=document.forms[0].background[0].value;
	if(document.forms[0].background[1].checked) var layer=document.forms[0].background[1].value;
	if(document.forms[0].background[2].checked) var layer=document.forms[0].background[2].value;
	if(document.forms[0].steward[0].checked)  layer=layer+" ownership";
	if(document.forms[0].steward[1].checked)  layer=layer+" management";
	if(document.forms[0].steward[2].checked)  layer=layer+" status";
	if(document.forms[0].basins_river.checked) layer=layer + " basins_river";
	if(document.forms[0].sub_basins.checked) layer=layer + " sub_basins";
	if(document.forms[0].bird_consv.checked) layer=layer + " bird_consv";
	if(document.forms[0].cities.checked) layer=layer + " cities";
	if(document.forms[0].counties.checked) layer=layer + " counties";
	if(document.forms[0].hydro.checked) layer=layer + " hydro";
	if(document.forms[0].interstate.checked) layer=layer + " interstate";
	if(document.forms[0].roads.checked) layer=layer + " roads";
	if(document.forms[0].topo_24000.checked) layer=layer + " topo_24000";
	/*if(document.forms[0].owner_tab2){
	if(document.forms[0].owner_tab2.checked) layer=layer + " ownership";
	if(document.forms[0].manage_tab2.checked) layer=layer + " management";
	if(document.forms[0].county_tab2.checked) layer=layer + " counties";
	if(document.forms[0].basin_tab2.checked) layer=layer + " basins_river";
	}*/

	parent.map.document.getElementById('layers_ajax').value = layer;
	parent.map.document.getElementById('layers_pdf').value = layer;
	if(parent.map.document.getElementById('layers')){
		parent.map.document.getElementById('layers').value = layer;
	}
	if(parent.map.document.getElementById('layers_zoom')){
		parent.map.document.getElementById('layers_zoom').value = layer;
	}
	//parent.map.document.getElementById('layers_pdf').value = layer;
	parent.map.document.getElementById('zoom_ajax').value = '1';
	//parent.map.clkcntr();
	parent.map.send_ajax();
}

function show_lgnd(){
		  $("#legendtab").click();
}