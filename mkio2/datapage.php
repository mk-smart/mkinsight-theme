<div id="debug" style="display: block"> </div>
<div class="row">
   <div id="resultpanel" class="col-md-12" >       
   </div>
</div>
   <script>
   <?php 

// get id from parameters and load report if available
// or (if user logged in) add id javascript
// if user is creator of report, allow modifs     

$jsonDesc = array();

// getting types...   
$types = array();
$files = scandir('/var/www/html/wp-content/themes/mkinsight/mkio2/cache/');
$dimensions = array();
foreach($files as $file){
    if (!startsWith($file, "http") && !startsWith($file, '.') && !startsWith($file, 'map') &&strpos($file, "__")!==false){
	$fn = str_replace("__", ":", $file);
	$afn = explode("_", $fn);
	if (!in_array ($afn[0], $types)) $types[] = $afn[0];
	if (!isset($dimensions[$afn[0]])) { $dimensions[$afn[0]] = array(); }
	$arr = &$dimensions[$afn[0]];
	$dims = substr($fn, strpos($fn,"_")+1);
	$aafn = explode(".", $dims);
	foreach($aafn as $elem){
	    if (!isset($arr[$elem])) { $arr[$elem] = array(); }
	    $arr = &$arr[$elem];
	}
    }
}

// print_r($dimensions);
echo 'var types      = '.json_encode($types).     ';'."\n";
echo 'var dimensions = '.json_encode($dimensions).';'."\n";
echo 'var page = '.json_encode($jsonDesc).';'."\n";

// can save here???
// create a directory for each user and put it there...
// ...

if (is_user_logged_in()){
   if (isset($_GET['id'])){
      echo 'var pageid = "'.$_GET['id'].'";'."\n";
   }  else {
      global $current_user;
      get_currentuserinfo();
      $pageid = md5("salt1".$current_user->user_login.rand(1,999999)."salt2");
      echo 'var pageid = "'.$pageid.'";'."\n";
      echo 'history.pushState({}, null,  window.location.href+"?id='.$pageid.'");';
   }
}

    ?>

// showTypes(types, dimensions);

showChartPage(page);

</script>

