<?php

require_once("mapping_fct.php");

if (!isset($_POST['url'])){
   echo '{error: "no URL passed"}'."\n"; http_response_code(400); die();
} 
$url   = $_POST['url'];
$graph = md5($url);

$firstline=1;
if (isset($_POST['fl'])) $firstline = intval($_POST['fl']);

$tab=0;
if (isset($_POST['tab'])) $tab = intval($_POST['tab']);

if (!isset($_POST['mappings'])){
    echo '{error: "no mapping passed"}'."\n"; http_response_code(400); die();
}
$mapping = json_decode($_POST['mappings']);


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

$rdf    = spreadsheettoRDF($sheetData, $mapping, $graph, $firstline, $tab, $filtersin, $filtersout);
$query  = mappingtoECAPI($mapping, $graph);
$config = mappingtoMKIOConfig($mapping);

$res = new stdClass();
$res->rdf    = json_encode($rdf);
$res->query  = json_encode($query);
$res->graphs = $config;
echo json_encode($res);
