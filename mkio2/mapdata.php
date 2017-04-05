<?php

  // TODO: change the label to only fragment...

require_once("ecapi.php");

$type = 'lsoa';
if (isset($_GET['type'])){
   $type = $_GET['type'];
}

$attr = 'global:TotalResidents';
if (isset($_GET['attr'])){
   $attr = $_GET['attr'];
}

$instances = getInstancesOfType($type, false);

$data = getValuesMap($type, $instances, $attr, false);

echo $data;

?>
