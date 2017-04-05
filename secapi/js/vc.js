// TODO:
//   - using something different from iframe to insert...
//   - fetch query + catalogue entry
//   - order by X in chart cache...

var types = [];

function mksse_init(element){    
    jQuery('#'+element).append('<div id="mksse_message"> </div>');
    jQuery('#'+element).append('<div id="mksse_spreadsheet"> </div>');
    jQuery('#'+element).append('<div id="mksse_mappings"> </div>');
    if (spreadsheet.url == "") {
	mksse_displayCSVForm();
    }
    else {
	spreadsheet.load(false, function(){
		mksse_updateSpreadsheet();
		mksse_displayMessage(0);
	    }, function (){
		spreadsheet.url="";
		alert("Something went wrong - are you sure this is a valid spreadsheet? Try again...");		
	    });
    }
    jQuery.ajax({
	url: "https://data.mksmart.org/entity/",
	success: function(data){
	    types=data.subresources;
	},
	dataType: "json"
    });                      

}

function mksse_displayCSVForm(){
    jQuery('#mksse_message').html(
	'<input class="form-control" type="text" id="mksse_urlinput" placeholder="url of spreadsheet file"/>'
    );
    jQuery(document).ready(function () {
           jQuery('#mksse_urlinput').change(function () { mksse_actions.urlProvided(); });
       });
}

function mksse_displayMappings(e){
    var s='<strong>Current mappings</strong><br/><ol>';
    for (var m in spreadsheet.mappings){
	s+='<li>column '+spreadsheet.mappings[m].column+' - ';
	if (spreadsheet.mappings[m].mappingtype=='mainid'){
	    s+='main object type - '+spreadsheet.mappings[m].type+'</li>';
	} else {
	    s+='relation '+spreadsheet.mappings[m].relation+
		' <a class="removebutton" href="javascript:removeMapping('+m+');">X</a></li>';
	}
    }
    s+='</ol>';
    e.html(s);
}

var currentphase;
var currentmappingtype;
var graphs; 

var filtersshow = false;
var multicolumn = false;

function removeMapping(ind){
    spreadsheet.mappings.splice(ind, 1);
    mksse_displayMappings(jQuery("#mksse_mappings"));
}

function matchACDim(level, q){
    var res = [];
    for (var rec in acdims[level]){
	var crec = acdims[level][rec].replace(/global:/g, "").replace(/_/g, " ");
	if (crec.indexOf(q)!=-1) { res.push(crec); }
    }
    return res;
}

function matchFilters(q){
    if (typeof(localStorage) !== "undefined") {
	var res = [];	
	var a = localStorage.secapi_filters.split("_xXXx_");
	    for (var rec in a){
		if (a[rec].indexOf(q)!=-1) { res.push(a[rec]); }
	    }
	return res;
    }
    return [];
}

function getRelation(){
  var rel1 = jQuery('#relationtext_1').val().toLowerCase();
  var rel2 = jQuery('#relationtext_2').val().toLowerCase();
  var rel3 = jQuery('#relationtext_3').val().toLowerCase();
  var rel4 = jQuery('#relationtext_4').val().toLowerCase();
  if (rel1 && rel1!=""){
      var res = rel1;
      if (acdims[0].indexOf(rel1)==-1) acdims[0].push(rel1);
      if (rel2 && rel2!=""){
	  res += "."+rel2;
	  if (acdims[1].indexOf(rel2)==-1) acdims[1].push(rel2);
	  if (rel3 && rel3!=""){
	      res += "."+rel3;
	      if (acdims[2].indexOf(rel3)==-1) acdims[2].push(rel3);
	      if (rel4 && rel4!=""){
		  res += "."+rel4;
		  if (acdims[3] && acdims[3].indexOf(rel4)==-1) acdims[3].push(rel4);
	      }
	  }
      }
      return res;
  } else return "unknown";

}

var lastfocusrel = null; 

function mksse_displayMessage(phase){
    var e = jQuery("#mksse_message");    
    var em = jQuery("#mksse_mappings");    
    switch(phase){
	case 0: // row, tab and filters
	var sh = '<div id="mksse_startline">'+
	    'Starting Line: <input id="startinglineinput" type="number" value="'+
	    spreadsheet.startingline+'" />'
	    +'Tab: <input id="tabinput" type="number" value="'+
	    spreadsheet.tab+'" />'
	    +'<button id="filtergo" type="button" class="button-primary">Go</button>';
//	    '<button id="filterbutton" type="button" class="button">Show filters</button>';
	sh += '<div id="mksse_filters">'+
	    'Keep rows matching: <input type="text" class="typeahead" id="filtersin" placeholder="keep rows matching... (comma separated)" value="'+spreadsheet.filtersin+'" /><br/>'+
	    'Remove rows matching: <input type="text" class="typeahead" id="filtersout" placeholder="remove rows matching... (comma separated)" value="'+spreadsheet.filtersout+'" />'+
	    '</div></div>';
	e.html(sh);
        jQuery('#startinglineinput').change(function () {mksse_actions.startinglinechanged() });
        jQuery('#startinglineinput').keypress(function (e) { if (e.which == 13) {e.preventDefault();}});
        jQuery('#tabinput').change(function () {mksse_actions.tabchanged() });
        jQuery('#tabinput').keypress(function (e) { if (e.which == 13) {e.preventDefault();}});
        jQuery('#filtersin').change(function () {mksse_actions.filtersinchanged() });
        jQuery('#filtersin').keypress(function (e) { if (e.which == 13) {e.preventDefault();}});
        jQuery('#filtersout').change(function () {mksse_actions.filtersoutchanged() });
        jQuery('#filtersout').keypress(function (e) { if (e.which == 13) {e.preventDefault();}});
        jQuery('#filtersin').focusout(function () {mksse_actions.filtersinchanged() });
        jQuery('#filtersout').focusout(function () {mksse_actions.filtersoutchanged() });
	if (filtersshow){
	    jQuery("#filterbutton").css("display", "none");
	    jQuery("#mksse_filters").css("display", "block");
	}
	jQuery('#filtersin').typeahead({
	    hint: true,
	    highlight: true,
	    minLength: 1
	},
	{
	    name: 'filtersin',
            limit: 10,
	    source: function (q,cb) {return cb(matchFilters(q)); }
	});
	jQuery('#filtersout').typeahead({
	    hint: true,
	    highlight: true,
	    minLength: 1
	},
	{
	    name: 'filtersout',
            limit: 10,
	    source: function (q,cb) {return cb(matchFilters(q)); }
	});
	jQuery("#filterbutton").on('click', function(){
	    filtersshow=true;
	    jQuery("#filterbutton").css("display", "none");
	    jQuery("#mksse_filters").css("display", "block");
	})
	jQuery("#filtergo").on('click', function(){ mksse_displayMessage(1); })
	break;

	case 1: // main id instruction
	e.html('<p class="instructiontext">Please choose the column that represents the identifier of the main object represented in each line - it might be a name or a code that is characteristic of the object (the name of an estate, of a school, etc.</p>');
	break;

	case 2: // type of main object
	e.html('<p class="instructiontext">Column "'+spreadsheet.summary.columns[currentcolumn]+'" is the main identifier. Please choose its type.</p>');	    
	types.sort();
	var s='<select id="typelist">';
	for (var t in types){
		s+='<option value="'+types[t]+'">'+types[t]+'</option>';
	}
	e.append(s+'</select>');
	e.append('<button id="maintypego" type="button" class="button-primary">Go</button>');
	jQuery("#maintypego").on('click', function(){ 
	    var newmapping = {
		mappingtype: "mainid",
		column: currentcolumn,
		type: jQuery('#typelist').val()
		};
	    spreadsheet.mappings.push(newmapping);
	    mksse_displayMessage(3); 
	})
	// should be able to try to link things... or even recommend it... based on links
	// add javascript function
	break;

	case 3: // other column instruction
	e.html('<p class="instructiontext">Please choose another column that represents an attribute of the main object.</p>');
	mksse_displayMappings(em);
	break;

	case 4: // other column mappingtype
	e.html('<p class="instructiontext">Is this attribute another object or a value?</p>');
	e.append('<div class="ovbuttons">');
	e.append('<button id="objectbutton" type="button" class="button">object</button>');
	e.append('<button id="valuebutton" type="button" class="button-primary">value</button>');
	e.append('</div>');
	jQuery("#objectbutton").on('click', function(){ 
	    currentmappingtype = "object";
	    mksse_displayMessage(5);
	});
	jQuery("#valuebutton").on('click', function(){ 
	    currentmappingtype = "value";
	    mksse_displayMessage(6);
	});
	mksse_displayMappings(em);
	break;

	case 5: // other column type of object
	e.html('<p class="instructiontext">Please choose the type of the object in Column "'+spreadsheet.summary.columns[currentcolumn]+'".</p>');	    
	var s='<select id="typelist" class="form-control">';
	for (var t in types){
		s+='<option value="'+types[t]+'">'+types[t]+'</option>';
	}
	e.append(s+'</select>');
	e.append('<button id="attrtypego" type="button" class="button-primary">Go</button>');
	jQuery("#attrtypego").on('click', function(){ 
	    currenttype =  jQuery('#typelist').val();
	    mksse_displayMessage(6); 
	})
	mksse_displayMappings(em);
	break;

	case 6: // other column config of literal value
	mksse_displayMessage(7);
	mksse_displayMappings(em);
	break;

	case 7: // relation
	multicolumn = false;
	e.html('<p class="instructiontext">Please indicate the relation between the main object and this attribute. Use 1 or more of the text field to represent different levels of the relation. Generally, the first level represents a category, the second the name of the actual attribute being looked at, and the third, a qualifier (e.g. the year). For example, to represent the relation "population in 2014", write "population > number of usual residents > 2014".</p>');
	e.append('<div id="multiplecolumnconfig">'+
	'<button id="multiplecolumnbut" type="button" class="button">Switch to multi-column relation</button>'+
	'<div id="multiplecolumnconfig_sub">'+
	'From selected column <span id="mcolumnstart"></span> '+
	'To column: <input type="text" class="mcolumntext" id="mcolumnend" /> (click on column to choose)'+	
	'</div>'+
	'</div>');
	jQuery("#multiplecolumnbut").on('click', function(){ 
	    if (!multicolumn){
		multicolumn = true;
		jQuery("#multiplecolumnconfig_sub").css("display", "block");
		jQuery("#mcolumnstart").html(currentcolumn+': "'+spreadsheet.summary.columns[currentcolumn]+'"');
		jQuery("#mcolumnend").val(currentcolumn);
	    }  else {
		multicolumn = false;
		jQuery("#multiplecolumnconfig_sub").css("display", "none");
	    }
	});
	e.append('<div class="relentries">');
	e.append('<button id="addheading" type="button" class="button">Copy column heading</button><br/>');
	e.append('<input type="text" class="relationtext typeahead" id="relationtext_1" placeholder="first level, generally a category like population" />');
	e.append('<input type="text" class="relationtext" id="relationtext_2" placeholder="second level, generally the relation" />');
	e.append('<input type="text" class="relationtext" id="relationtext_3" placeholder="third level, generally a qualifier (e.g. the year)" />');
	e.append('<input type="text" class="relationtext" id="relationtext_4" placeholder="fourth level"/>');
	e.append('<br/><button id="goandagain" type="button" class="button">Do another</button>');
	e.append('<button id="goandfinish" type="button" class="button-primary">Finish</button>');
	e.append('</div>');
	jQuery('#relationtext_1').typeahead({
	    hint: true,
	    highlight: true,
	    minLength: 1
	},
	{
	    name: 'dimensions_level_1',
            limit: 10,
	    source: function (q,cb) {return cb(matchACDim(0,q)); }
	});
	jQuery('#relationtext_2').typeahead({
	    hint: true,
	    highlight: true,
	    minLength: 1
	},
	{
	    name: 'dimensions_level_2',
            limit: 10,
	    source: function (q,cb) {return cb(matchACDim(1,q)); }
	});
	jQuery('#relationtext_3').typeahead({
	    hint: true,
	    highlight: true,
	    minLength: 1
	},
	{
	    name: 'dimensions_level_3',
            limit: 10,
	    source: function (q,cb) {return cb(matchACDim(2,q)); }
	});
	jQuery('#relationtext_4').typeahead({
	    hint: true,
	    highlight: true,
	    minLength: 1
	},
	{
	    name: 'dimensions_level_4',
            limit: 10,
	    source: function (q,cb) {return cb(matchACDim(3,q)); }
	});
	jQuery('#relationtext_1').on('focusin', function(){lastfocusrel = jQuery('#relationtext_1');});
	jQuery('#relationtext_2').on('focusin', function(){lastfocusrel = jQuery('#relationtext_2');});
	jQuery('#relationtext_3').on('focusin', function(){lastfocusrel = jQuery('#relationtext_3');});
	jQuery('#relationtext_4').on('focusin', function(){lastfocusrel = jQuery('#relationtext_4');});
        jQuery('#relationtext_1').keypress(function (e) { if (e.which == 13) {e.preventDefault();}});
        jQuery('#relationtext_2').keypress(function (e) { if (e.which == 13) {e.preventDefault();}});
        jQuery('#relationtext_3').keypress(function (e) { if (e.which == 13) {e.preventDefault();}});
        jQuery('#relationtext_4').keypress(function (e) { if (e.which == 13) {e.preventDefault();}});
	jQuery("#addheading").on('click', function(){
            var $txt = lastfocusrel;
            var caretPos = $txt[0].selectionStart;
            var textAreaTxt = $txt.val();
	    var txtToAdd = "[HEADING]";
	    if (!multicolumn)
		txtToAdd = spreadsheet.summary.columns[currentcolumn];
	    $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );	    
	});
	jQuery("#goandagain").on('click', function(){ 
	    if (multicolumn){
		generateMCMappings();
	    } else {
		var newmapping = {
		    mappingtype: currentmappingtype,
		    column: currentcolumn,
		    relation: getRelation()
		};
		if (currentmappingtype == "object") newmapping.type = currenttype;
		spreadsheet.mappings.push(newmapping);
	    }	    
	    mksse_displayMessage(3); 
	});
	jQuery("#goandfinish").on('click', function(){ 
	    if (multicolumn){
		generateMCMappings();
	    } else {
		var newmapping = {
		    mappingtype: currentmappingtype,
		    column: currentcolumn,
		    relation: getRelation()		
		};
		if (currentmappingtype == "object") newmapping.type = currenttype;
		spreadsheet.mappings.push(newmapping);
	    }
	    mksse_displayMessage(8);
	});
	mksse_displayMappings(em);
	break;

    case 8: // finish	
	e.html('<p>Below are the charts created (they might take time to load). Please select the ones you would like to insert in the post.</p>');
	spreadsheet.execute(function(data){
        graphs = data.graphs;
	for(var graph in graphs){
	    var charturl = 'http://mkinsight.org/wp-content/themes/mkinsight/mkio2/singlegraph.php?type='+graphs[graph].type;
	    var dims = "";
	    for(var dim in graphs[graph].dimensions){
		charturl += '&l'+(parseInt(dim)+1)+'=global:'+
		    urify(graphs[graph].dimensions[dim]);
		if (parseInt(dim)!==0) dims += '.';
	        dims += "global:"+urify(graphs[graph].dimensions[dim]);	
	    }
	    e.append('<iframe src="'+charturl+'" width="100%" height="500" ></iframe>');
	    e.append('<button id="insertbutton_'+graph+'" type="button" class="button">Insert in post</button>');
	    jQuery("#insertbutton_"+graph).click({type: graphs[graph].type, dim:dims}, function(event){ 
		    if (jQuery('#content').is(':visible'))
			jQuery('#content').val(jQuery('#content').val()+'\n\n[mkichart type="'+event.data.type+'" dim="'+event.data.dim+'"]');
		    else {
			if (!tinyMCE.activeEditor)
			    alert("please switch to text mode first.");
			else {
			    tinyMCE.activeEditor.setContent(tinyMCE.activeEditor.getContent()+'\n\n[mkichart type="'+event.data.type+'" dim="'+event.data.dim+'"]');
			}
		    }
		})
	}
	}, function(){
	    alert("there was an error");
	});	
	break;
	mksse_displayMappings(em);
    }
    currentphase = phase;
}

function generateMCMappings(){
    var endcol = jQuery("#mcolumnend").val();
    for(var col = parseInt(currentcolumn); col <= parseInt(endcol); col++){
	console.log("mapping for col "+col);
	var newmapping = {
	    mappingtype: currentmappingtype,
	    column: col,
	    relation: getRelation().replace("[heading]", spreadsheet.summary.columns[col].toLowerCase())
	};
	if (currentmappingtype == "object") newmapping.type = currenttype;
	spreadsheet.mappings.push(newmapping);
    }
}



function mksse_updateSpreadsheet(){
    var e = jQuery("#mksse_spreadsheet");    
    e.html("");
    console.log(spreadsheet.summary);
    e.append("<table><tr>");
    for(var c in spreadsheet.summary.columns){
	e.append('<th onclick="mksse_actions.columnclicked(\''+c+'\')">'+spreadsheet.summary.columns[c]+"</th>");
    }
    e.append("</tr>");
    for(var c in spreadsheet.summary.data){
	e.append("<tr>");
	for(var d in spreadsheet.summary.data[c]){
	    e.append('<td onclick="mksse_actions.columnclicked(\''+d+'\')">'+spreadsheet.summary.data[c][d]+"</td>");
	}
	e.append("</tr>");
    }
    e.append("<tr>");
    for(var c in spreadsheet.summary.columns){
    	e.append('<td onclick="mksse_actions.columnclicked(\''+c+'\')">...</td>');
    }
    e.append("</tr>");
    e.append("</table>");    
}

var currentcolumn = -1;

function storeFilter(f){
    if (typeof(localStorage) !== "undefined") {
	if (!localStorage.secapi_filters) localStorage.secapi_filters = "";
	if (localStorage.secapi_filters.indexOf(f)==-1) {
	    if (localStorage.secapi_filters!="") localStorage.secapi_filters+="_xXXx_";
	    localStorage.secapi_filters+=f;
	}
    }
}

var mksse_actions = {
    urlProvided: function(){
	spreadsheet.url=jQuery('#mksse_urlinput').val();
	spreadsheet.load(false, function(){
	    mksse_updateSpreadsheet();
	    mksse_displayMessage(0);
	}, function (){
	    spreadsheet.url="";
	    alert("Something went wrong - are you sure this is a valid spreadsheet? Try again...");		
	});
    },
    startinglinechanged: function(){
	spreadsheet.startingline = jQuery("#startinglineinput").val();
	spreadsheet.load(true, function(){
	    mksse_updateSpreadsheet();
	    mksse_displayMessage(0);
	}, function (){
	    spreadsheet.url="";
	    alert("Something went wrong - are you sure this is a valid spreadsheet? Try again...");		
	});
    },
    filtersinchanged: function(){
	spreadsheet.filtersin = jQuery("#filtersin").val();
	storeFilter(spreadsheet.filtersin);
	spreadsheet.load(true, function(){
	    mksse_updateSpreadsheet();
	    mksse_displayMessage(0);
	}, function (){
	    spreadsheet.url="";
	    alert("Something went wrong - are you sure this is a valid spreadsheet? Try again...");		
	});
    },
    filtersoutchanged: function(){
	spreadsheet.filtersout = jQuery("#filtersout").val();
	storeFilter(spreadsheet.filtersout);
	spreadsheet.load(true, function(){
	    mksse_updateSpreadsheet();
	    mksse_displayMessage(0);
	}, function (){
	    spreadsheet.url="";
	    alert("Something went wrong - are you sure this is a valid spreadsheet? Try again...");		
	});
    },
    tabchanged: function(){
	spreadsheet.tab = jQuery("#tabinput").val();
	spreadsheet.load(true, function(){
	    mksse_updateSpreadsheet();
	    mksse_displayMessage(0);
	}, function (){
	    spreadsheet.url="";
	    alert("Something went wrong - are you sure this is a valid spreadsheet? Try again...");		
	});
    },
    filterdone: function(){},
    columnclicked: function(column){
	switch(currentphase){
	    case 1:
	    currentcolumn = column;
	    mksse_displayMessage(2);
	    break;
	    case 3:
	    currentcolumn = column;
	    // short cutting choosing object or value
	    currentmappingtype = "value";
	    mksse_displayMessage(6);
	    break;
	    case 6:
	    jQuery("#mcolumnend").val(column);
	    break;
	    default: console.log("nothing to do "+currentphase);
	}
    }
}


    function urify(s){
	return s.replace(/ /g, '_');
    }
