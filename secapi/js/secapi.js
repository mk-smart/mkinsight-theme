
// define javascript class for spreadsheet
spreadsheet = {
   url: "",
   summary:  {},
   startingline: 1,
   tab: 0,
   filtersin: '',
   filtersout: '',
   mappings: [],
   load: function(withfilter, success, failure){
       console.log("Loading "+this.url);
       var url = "/wp-content/themes/mkinsight/secapi/services/loadSpreadsheet.php";
       jQuery.ajax({
	   type: "POST",
	   url: url,
		   data: {url: this.url, fl: this.startingline, 
			  nl: 10, tab: this.tab, 
			  fin: this.filtersin, fout: this.filtersout},
	   success: function(data){
	       if (data.error) {
		   failure();
		   return;
	       }
	       spreadsheet.summary=data;
	       console.log(this.summary);
	       success();
	   },
	   dataType: "json"
       });
       // the summary should include guessed info about:
       //     type (number or string)
       //     potential id columns ids (with unique values)
   },
    execute: function(success, failure){
	console.log("Executing "+this.url);
       var url = "/wp-content/themes/mkinsight/secapi/services/executeMapping.php";
	jQuery.ajax({
	   type: "POST",
		    url: url,
		    data: {
		    url: this.url, 
			fl: this.startingline, 
			filters: JSON.stringify(this.filters),
			mappings: JSON.stringify(this.mappings),
			fin: this.filtersin, fout: this.filtersout,
			tab: this.tab
	   },
	   success: function(data){
	       if (data.error) {
		   failure();
		   return;
	       }	       
	       console.log("I'm back");
	       success(data);
	   },
	   dataType: "json"
       });
    }     
}


