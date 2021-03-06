//function puts checks in layer boxes to match visible layers
function load_selections(){
	var layers = parent.map.document.getElementById('layers_ajax').value;
	if (layers.indexOf('basins_river') != -1){document.forms[0].basins_river.checked = true;}
	if (layers.indexOf('cities') != -1){document.forms[0].cities.checked = true;}
	if (layers.indexOf('counties') != -1){document.forms[0].counties.checked = true;}
	if (layers.indexOf('hydro') != -1){document.forms[0].hydro.checked = true;}
	if (layers.indexOf('interstate') != -1){document.forms[0].interstate.checked = true;}
	if (layers.indexOf('roads') != -1){document.forms[0].roads.checked = true;}
	if (layers.indexOf('topo_24000') != -1){document.forms[0].topo_24000.checked = true;}
	if (layers.indexOf('sub_basins') != -1){document.forms[0].sub_basins.checked = true;}
	if (layers.indexOf('bird_consv') != -1){document.forms[0].bird_consv.checked = true;}

	if (layers.indexOf('ownership') != -1){
		document.forms[0].steward[0].checked = true;
	}else  if (layers.indexOf('management') != -1){
		document.forms[0].steward[1].checked = true;
	}else if (layers.indexOf('status') != -1){
		document.forms[0].steward[2].checked = true;
	}else{
		document.forms[0].steward[3].checked = true;
	}

	if (layers.indexOf('landcover') != -1){
		document.forms[0].background[0].checked = true;
	}else if (layers.indexOf('elevation') != -1){
		document.forms[0].background[1].checked = true;
	}else{
		document.forms[0].background[2].checked = true;
	}

}

// 4 functions to create AOI wide GRASS reports
function lc_report(){
	window.open("","report","toolbar=no,menubar=no,scrollbars,resizable");
	parent.map.document.forms.fm2.target = 'report';
	parent.map.document.forms.fm2.report.value = 'landcover';
	parent.map.document.forms.fm2.submit();
}

function manage_report(){
	window.open("","report","toolbar=no,menubar=no,scrollbars,resizable");
	parent.map.document.forms.fm2.target = 'report';
	parent.map.document.forms.fm2.report.value = 'management';
	parent.map.document.forms.fm2.submit();
}

function owner_report(){
	window.open("","report","toolbar=no,menubar=no,scrollbars,resizable");
	parent.map.document.forms.fm2.target = 'report';
	parent.map.document.forms.fm2.report.value = 'owner';
	parent.map.document.forms.fm2.submit();
}

function status_report(){
	window.open("","report","toolbar=no,menubar=no,scrollbars,resizable");
	parent.map.document.forms.fm2.target = 'report';
	parent.map.document.forms.fm2.report.value = 'status';
	parent.map.document.forms.fm2.submit();
}

function categories(){
	if(document.forms.fm2.fed.checked || document.forms.fm2.state.checked || document.forms.fm2.gap.checked ||
	document.forms.fm2.nsglobal.checked || document.forms.fm2.nsstate.checked || document.forms.fm2.pif.checked){
		document.forms.fm2.species[1].checked = true;
	}else{
		document.forms.fm2.species[0].checked = true;
	}
}

//function to go back from controls4.php to controls3.php
function change_categories(){
	parent.data.location = 'dummy.html';
	parent.functions.location = 'dummy.html';
	document.forms[1].action = 'controls3.php';
	document.forms[1].target = 'controls';
	document.forms[1].submit();
	parent.map.document.forms.fm1.species_layer.value = '';
	parent.map.document.forms.fm1.zoom.value = '1';
	parent.map.document.forms.fm1.submit();
}

//function to display proper page in frame functions
//called to change or initially when controls4.php is opened
function functions_action(){
	var self_loc = window.location.pathname;
	if(document.forms[1].mode[0].checked){
		parent.functions.location = self_loc.replace(/controls4.php/, "single.php");
	}
	if(document.forms[1].mode[1].checked){
		parent.functions.location = self_loc.replace(/controls4.php/, "multiple.php");
	}
}