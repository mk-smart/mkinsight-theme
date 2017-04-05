<?php

// TODO: if filters are passed, process them...

if (!isset($_POST['url'])){
   echo '{error: "no URL passed"}'."\n"; http_response_code(400); die();
} 
$url = $_POST['url'];

$firstline=1;
if (isset($_POST['fl'])) $firstline = intval($_POST['fl']);

$tab=0;
if (isset($_POST['tab'])) $tab = intval($_POST['tab']);

$nbline=10;
if (isset($_POST['nl'])) $nbline = intval($_POST['nl']);

$filtersin = "";
if (isset($_POST['fin'])) $filtersin = $_POST['fin'];

$filtersout = "";
if (isset($_POST['fout'])) $filtersout = $_POST['fout'];

file_put_contents('data/'.md5($url), file_get_contents($url));
include '../PHPExcel-1.8/Classes/PHPExcel/IOFactory.php';
try {
    $objPHPExcel = PHPExcel_IOFactory::load("data/".md5($url));
} catch(PHPExcel_Reader_Exception $e) {
    echo '{error: "impossible to read speadcheet file"}';
    http_response_code(400);
    die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
}

$objPHPExcel->setActiveSheetIndex($tab);
$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

$columns  = array();
$headings = array();
foreach ($sheetData[$firstline] as $ind => $heading){
    if ($heading==NULL) break;
    $columns[] = $ind;
    $headings[] = $heading;
}

$data = array();
$carryon=true;
$firstline=$firstline+1;
$count = 0;
while($carryon && $count < $nbline){
    $line = array();
    $carryon = false;
    $toremove = false;
    $keep = false;
    foreach($columns as $col){
	$val = $sheetData[$firstline][$col];
	if (!$val==NULL) $carryon=true;
	if (outremove($val, $filtersout)){
	  $toremove = true;
	  break;
	}
	if (!inremove($val, $filtersin)){
	    $keep = true;
	}
        $line[]=$val;
    }
    if ($carryon && $keep && !$toremove) $data[]=$line;
    $firstline=$firstline+1;    
    if (!$toremove && $keep) $count++;
}

$res = new stdClass();
$res->columns = $headings;
$res->data    = $data;

echo json_encode($res);

unlink("data/".md5($url));

function inremove($v, $fin){
  if (strcmp($fin, '')===0){return false;}
  $fins = explode(",", $fin);
  foreach($fins as $f){
    if(preg_match('/'.$f.'/', $v)===1) return false;
  }
  return true;
}

function outremove($v, $fout){
  if (strcmp($fout, '')===0) {return false;}
  $fouts = explode(",", $fout);
  foreach($fouts as $f){
    if(preg_match('/'.$f.'/', $v)===1) return true;
  }
  return false;
}