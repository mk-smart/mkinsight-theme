<?php

  // TODO: change the label to only fragment...

require_once("ecapi.php");

$type = 'place';
if (isset($_GET['type'])){
   $type = $_GET['type'];
}

$attr = 'demographics:population-2011';
if (isset($_GET['attr'])){
   $attr = $_GET['attr'];
}

$instances = getInstancesOfType($type, false);

$data = getValues($type, $instances, $attr, false);
updateDataCache($type, $attr);

echo $data;

function updateDataCache($type, $attr){
  // call the update cache service
}

?>
