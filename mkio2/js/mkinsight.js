
// TODO: Saving for logged in people
// TODO: highlights and removal accross charts

var currentpage = [];

function showChartPage(page){
  currentpage=page;
  updateDisplayForS();
}

function updateDisplayForS(){
    jQuery("#resultpanel").html("");
    for(var i in currentpage){
	displaySection(i);
    }
    displayAddSectionDiv();
}

function displaySection(num){
    var st='<div class="mkio_section" id="mkio_section_'+num+'">';
    st += getSectionContent(num);
    st+='<div>';
    jQuery("#resultpanel").append(st);
    addOperations(num);
}

function getSectionContent(num){
    var sec = currentpage[num];
    if (sec.type=="title"){
	return "<h2>"+sec.text+"</h2>";
    } else if (sec.type=="text"){
	return "<p>"+sec.text+"</p>";
    } else if (sec.type=="chart"){
	dimsparam='';
	dims = sec.dims.split('.');
	for (var i in dims){
	    dimsparam += '&l'+(parseInt(i)+1)+'='+dims[i];
	}
	return '<iframe src="http://mkinsight.org/wp-content/themes/mkinsight/mkio2/singlegraph.php?type='+sec.ctype+'&title='+escape(sec.title)+dimsparam+'" width="100%" height="550" frameborder="0" class="iframe-class"></iframe>';
    } else return "error in getting section content";
}

function displayAddSectionDiv(){
    var st ='<div id="mkio_add_buttons" class="no-print">';
    st+='<a class="mkio_add_button" href="javascript:addTitleSection();"><img src="http://mkinsight.org/wp-content/themes/mkinsight/mkio2/imgs/add_title.png" alt="add title" title="add title" class="chart_button"/></a> ';
    st+='<a class="mkio_add_button" href="javascript:addTextSection();"><img src="http://mkinsight.org/wp-content/themes/mkinsight/mkio2/imgs/add_text.png" alt="add text" title="add text" class="chart_button"/></a> ';
    st+='<a class="mkio_add_button" href="javascript:addChartSection();"><img src="http://mkinsight.org/wp-content/themes/mkinsight/mkio2/imgs/add_chart.png" alt="add chart" title="add chart" class="chart_button"/></a> ';
    st+='<div id="mkio_save"><a href="javascript:pdfSave()">print or save page as pdf</a></div>';
//    if (typeof(pageid) !== 'undefined'){
//	st+='<div id="mkio_email"><a href="mailto:?Subject=Data page from MK Insight: '+window.location.href+'">email page</a></div>';
//    }
    st+="</div>";
    jQuery("#resultpanel").append(st);
}

function addTitleSection(){
    jQuery("#mkio_add_buttons").remove();
    var num = currentpage.length
    var section = {"type":"title", "text":""};
    currentpage.push(section);
    var st='<div class="mkio_section" id="mkio_section_'+num+'">';
    st+='</div>';
    jQuery("#resultpanel").append(st);
    displaySectionForm(num);
}

function addTextSection(){
    jQuery("#mkio_add_buttons").remove();
    var num = currentpage.length
    var section = {"type":"text", "text":""};
    currentpage.push(section);
    var st='<div class="mkio_section" id="mkio_section_'+num+'">';
    st+='</div>';
    jQuery("#resultpanel").append(st);
    displaySectionForm(num);
}

function addChartSection(){
    jQuery("#mkio_add_buttons").remove();
    var num = currentpage.length        
    var section = {"type":"chart", "ctype":"", "dims":"", "title":""};
    currentpage.push(section);
    var st='<div class="mkio_section" id="mkio_section_'+num+'">';
    st+='<div>';
    jQuery("#resultpanel").append(st);
    displaySectionForm(num);
}
    
function displaySectionForm(num){
    var st = "";
    if (currentpage[num].type=="chart"){	
	st+= '<input type="text" placeholder="Title of the chart" id="input_section_'+num+'" value="'+currentpage[num].title+'"/>';
	st+= '<div class="mkio_tandd" style="margin-stop: 10px;><div class="mkio_type_div">'+showTypesForS(num)+'</div><div id="mkio_dims_'+num+'" style="margin-top: 10px; margin-bottom: 10px;"><span class="chartformlabel" style="padding-right: 10px; font-size: 120%; font-weight: bold">Dimensions: </span></div></div>';
	st+= '<a class="mkio_close_button" id="mkio_close_'+num+'">done</a>';
	st+='</div>';
    } else if (currentpage[num].type=="title"){	
	st+= '<input type="text" placeholder="Write the title here" id="input_section_'+num+'" value="'+currentpage[num].text+'"/>';
	st+= '<a class="mkio_close_button" id="mkio_close_'+num+'">done</a>';
    } else if (currentpage[num].type=="text"){	
	st+= '<textarea placeholder="Write the text here" cols="40" rows="5" id="input_section_'+num+'">'+currentpage[num].text+'</textarea>';
	st+= '<a class="mkio_close_button" id="mkio_close_'+num+'">done</a>';
    }
    
    jQuery("#mkio_section_"+num).html(st);

    if (currentpage[num].type=="chart"){	
	jQuery('#input_section_'+num).change(function () {
	    currentpage[num].title=escapeHtml(jQuery("#input_section_"+num).val());
	});
	jQuery('#typedropdown_'+num).change(function () {
	    currentpage[num].ctype = jQuery('#typedropdown_'+num).val();
	    currentpage[num].dims = "";	    
	    removeChartConfigForS(num);
	    addChartConfigForS(1, dimensions[currentpage[num].ctype], num);
	}); 
    } else {
	jQuery('#input_section_'+num).change(function () {
	    currentpage[num].text=escapeHtml(jQuery("#input_section_"+num).val());
	});
    }
    setCloseEvent(num);  
}

function setCloseEvent(num){
    jQuery("#mkio_close_"+num).on('click', function(){
	var st = getSectionContent(num);
	jQuery('#mkio_section_'+num).html(st);
	addOperations(num);
	displayAddSectionDiv();
    });
}

function addOperations(num){
    var st = '<div class="operations no-print" id="mkio_ops_'+num+'">';
    st+= '<a href="javascript:edit('+num+');"><img src="http://mkinsight.org/wp-content/themes/mkinsight/mkio2/imgs/edit.png" alt="add title" title="edit" class="chart_section_button"/></a> ';
    st+= '<a href="javascript:moveUp('+num+');"><img src="http://mkinsight.org/wp-content/themes/mkinsight/mkio2/imgs/moveup.png" alt="move up" title="move up" class="chart_section_button"/></a> ';
    st+= '<a href="javascript:moveDown('+num+');"><img src="http://mkinsight.org/wp-content/themes/mkinsight/mkio2/imgs/movedown.png" alt="move down" title="movedown" class="chart_section_button"/></a> ';
    st+= '<a href="javascript:remove('+num+');"><img src="http://mkinsight.org/wp-content/themes/mkinsight/mkio2/imgs/remove.png" alt="remove" title="remove" class="chart_section_button"/></a> ';
    st+= '</div>';
    jQuery(st).insertBefore("#mkio_section_"+num);
}


function showTypesForS(num){   
    var s = '<span class="chartformlabel" style="padding-right: 10px;font-size: 120%; font-weight: bold;">Type: </span><select id="typedropdown_'+num+'" style="font-size: 120%;">'+
	'<option value="">---</option>';
    types.forEach(function (entry) {	
	if (entry && entry!="undefined")
	    s += '<option value="'+entry+'">'+entry+'</option>';
	});
    s += '</select>';
    return s;
}

function removeChartConfigForS(num){
    jQuery("#mkio_dims_"+num).html('<span class="chartformlabel" style="padding-right: 10px; font-size: 120%; font-weight: bold">Dimensions: </span>');
}

function addChartConfigForS(level, dims, num){
    if (dims.length!=0){
	var s = '<select style="font-size: 120%;" id="l'+level+'dropdown_'+num+'">'+
	    '<option value="">---</option>';
	for (var dim in dims){
	    s += '<option value="'+dim+'">'+dfragment(dim)+'</option>';
	}
	s+='</select>';
	jQuery("#mkio_dims_"+num).append(s);
	jQuery('#l'+level+'dropdown_'+num).change(function () {
	    var ndim = jQuery('#l'+level+'dropdown_'+num).val();
	    jQuery('#l'+level+'dropdown_'+num).attr("disabled", true); 
	    if (currentpage[num].dims=="") currentpage[num].dims=ndim; 
	    else currentpage[num].dims += '.'+ndim
	    addChartConfigForS(level+1, dims[ndim], num);
	});
    }
}

function dfragment(dim){
    if (!dim) return "hum...";    
    return dim.substring(dim.lastIndexOf(":")+1);
}

var entityMap = {
  "&": "&amp;",
  "<": "&lt;",
  ">": "&gt;",
  '"': '&quot;',
  "'": '&#39;',
  "/": '&#x2F;'
};

function escapeHtml(string) {
  return String(string).replace(/[&<>"'\/]/g, function (s) {
    return entityMap[s];
  });
}

function edit(num){
    jQuery("#mkio_ops_"+num).remove();
    jQuery("#mkio_add_buttons").remove();
    displaySectionForm(num);
}

function moveUp(num){
    if (num!=0){
	var prev = currentpage[num-1];
	currentpage[num-1]=currentpage[num];
	currentpage[num]=prev;
	updateDisplayForS();
    }
}

function moveDown(num){
    if (num!=currentpage.length-1){
	var suiv = currentpage[num+1];
	currentpage[num+1]=currentpage[num];
	currentpage[num]=suiv;
	updateDisplayForS();
    }
}

function remove(num){
    currentpage.splice(num, 1);
    updateDisplayForS();
}

function pdfSave(){
    window.print();
}
