<?php

require_once('couchdb.class.php');

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
    if(preg_match('/'.$f.'/', $v)===1) {
         return true;
    }
  }
  return false;
}

function spreadsheettoRDF($data, $mappings, $graph, $fl, $tab, $fin, $fout){
    include("config.php");
    $graphdata = array();
    $mmapping = getMainMapping($mappings);
    if ($mmapping==null) {
	return null;
    }
    $done=false;
    $triples = array();
    for($i = $fl+1; !$done; $i++){    
	if(isset($data[$i])){	
	    $toremove = false;
            $tokeep   = false;    
	    foreach(array_values($data[$i]) as $col){
	       if (outremove($col, $fout)){	          
	          $toremove=true;
	       }
	       if (!inremove($col, $fin)){
	          $tokeep=true;
	       }
	    }
	    if (!$toremove && $tokeep){ 
	    $cell = getColInLine($data[$i], $mmapping->column);	    
	    if(strcmp($cell, "")!=0){			        
		$muri = $baseNS.$mmapping->type.'/'.urify($cell);		
		$triple= array();
		$triple[] = $muri;
		$triple[] = "http://www.w3.org/1999/02/22-rdf-syntax-ns#type";
		$triple[] = $baseNS."type/".$mmapping->type;
		$triples[] = $triple;
		foreach($mappings as $ma){
		    if (strcmp($ma->mappingtype,"value")==0){
			$rels = explode('.', $ma->relation);			
			$prev=$muri;
			for($j = 0; $j < count($rels); $j++){
			    $triple = array();
			    $triple[] = $prev;
			    $triple[] = $baseNS.'prop/'.urify($rels[$j]);
			    if ($j==count($rels)-1){
				$val = getColInLine($data[$i], $ma->column);
				if (!is_numeric($val)){
				  if (is_numeric(str_replace(",", "", $val))){
				    $val = str_replace(",", "", $val);
				  }
				}
				if (is_numeric($val)){
				    $triple[] = $val;
				    $gd = new stdClass(0);
				    $gd->x=$i-$fl-1;
				    $gd->y=floatval($val);
				    $gd->label=$cell;
				    $gd->entity="https://data.mksmart.org/entity/".$mmapping->type.'/'.urify($cell);
				    if (!isset($graphdata[$ma->relation])) 
				      $graphdata[$ma->relation] = array();
				    $graphdata[$ma->relation][] = $gd;
				}
				else {
				    $triple[] = '"'.$val.'"';
				}
			    }
			    else {
				$triple[] = $prev.'.'.urify($rels[$j]);
				$prev=$prev.'.'.urify($rels[$j]);
			    }
			    $triples[] = $triple;
			}
		    } else if (strcmp($ma->mappingtype,"object")==0){
		    }
		}
	    }
	}
	}
	else $done=true;	
    }
    foreach($graphdata as $srels=>$gd){
      $gds = json_encode($gd);
      $attr = "global:".urify($srels);
      $attr=str_replace(".", ".global:", $attr);
	file_put_contents("../../mkio2/cache/".tfn($mmapping->type.'_'.$attr), $gds);
    }
    include("config.php"); 
    grantAccess($graph, $ecapiKey, $ecapiSKey, $ecapiODKey);
    $chunk = array();
    $count = 0;
    foreach ($triples as $triple){
	$chunk[]=$triple;
	$count++;
	if ($count==$chunkSize){
	    $code = writeTriplesToECAPI($graph, $chunk);
	    if ($code!=200 && $code!=201) return "error with some triples - code ".$code;
	    $count=0;
	    $chunk = array();
	}
    }
// clearing graph in ECAPI
//    $code = clearGraph($graph);
//    if ($code !== 200) return "error with clearing the graph - code ".$code;
    $code = writeTriplesToECAPI($graph, $chunk);
    if ($code !== 200 && $code!==201) return "error with some triples - code ".$code;
    return "created ".count($triples)." triples";
}

// TODO: check HTTP code and return the right thing...
function writeTriplesToECAPI($graph, $triples){
    include("config.php");
    $url = 'https://data.beta.mksmart.org/dataset/'.$graph."?key=".$ecapiKey;
    $data = "";
    $count = 0;
    foreach($triples as $triple){
	$data = $data."<".$triple[0]."> <".$triple[1]."> ";
	if (is_numeric($triple[2])){
	    $data = $data.$triple[2];
	} else if (startsWith($triple[2], "http")){
	    $data = $data.'<'.$triple[2].'>';
	}
	else {
	    $data = $data.'"'.str_replace("&", "and", trim($triple[2], '"')).'"';
	}
	$count++;	
	if ($count==count($triples)) $data = $data." \n";
	else $data = $data." . \n";
    }
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, 1);
    curl_setopt($ch,CURLOPT_POSTFIELDS, "data=".$data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    $info = curl_getinfo($ch);    
    if($info["http_code"] !== 200 && $info["http_code"]!==201) {print_r($info);
				    print_r($data);
				    }
    curl_close($ch);
    return $info["http_code"];
}

function clearGraph($graph){
    include("config.php");
    $url = 'https://data.beta.mksmart.org/dataset/'.$graph."/clear?key=".$ecapiKey;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, 1);
//    curl_setopt($ch,CURLOPT_POSTFIELDS, "data=".$data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    $info = curl_getinfo($ch);    
    if($info["http_code"] !== 200 && $info["http_code"]!==201) {print_r($info);
				    print_r($data);
				    }
    curl_close($ch);
    return $info["http_code"];
}

// TODO: check HTTP code and return the right thing...
function grantAccess($graph, $key, $skey, $odkey){
    // give write access
    $url = 'https://data.beta.mksmart.org/dataset/'.$graph."/grant/?key=".$skey;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, 2);
    curl_setopt($ch,CURLOPT_POSTFIELDS, "ukey=".$key."&right=write");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
//    echo "Error - ".curl_error($ch)."\n";
//    echo "write grant results = ".$result."\n";
//    print_r(curl_getinfo($ch));
    curl_close($ch);
    // give open read access
    $url = 'https://data.beta.mksmart.org/dataset/'.$graph."/grant/?key=".$skey;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, 2);
    curl_setopt($ch,CURLOPT_POSTFIELDS, "ukey=".$odkey."&right=read");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
//    echo "Error - ".curl_error($ch)."\n";
//    echo "read grant results = ".$result."\n";
//    print_r(curl_getinfo($ch)->http_code);
    curl_close($ch);
}

function mappingtoECAPI($mappings, $graph){
    include("config.php");
    $res = new stdClass();
    $mm = getMainMapping($mappings);
    $type = $mm->type;
    $res->type = "provider-spec";
    $res->{"http://rdfs.org/ns/void#sparqlEndpoint"} = $sparqlendpoint;
    $res->{"mks:graph"} = 'urn:dataset/'.$graph.'/graph';
    $res->{"mks:types"} = new stdClass();
    $res->{"mks:types"}->{"type/global:id/".$mm->type} = new stdClass();
    $res->{"mks:types"}->{"type/global:id/".$mm->type}->localise = 'function localise(stype,authority,uidcat,uid){return "'.$baseNS.$mm->type.'/"+uid.toLowerCase();}';
    $level = 1;
    $maxlevel = 1;
    $query = "{{";
    $nm = 0;    
    foreach($mappings as $m){
	if (strcmp($m->mappingtype,"value")===0){
	    $rels = explode('.', $m->relation);
	    $prev = "[LURI]";
	    if ($level<count($rels)) $level = count($rels);
	    if ($level > $maxlevel) $maxlevel = $level;
	    $nb = 1;
	    foreach($rels as $rel){
		$query .= ' <'.$prev.'> <'.$baseNS.'prop/'.urify($rel).'> ?o'.$nb.' .';
		$query .= 'bind(uri("'.$baseNS.'prop/'.urify($rel).'") as ?p'.$nb.') .';
		$prev = $prev.'.'.urify($rel);
		$nb++;
	    }
	} else if (strcmp($m->mappingtype,"object")===0) {
	
	}		   
	$nm++;
	if ($nm!==count($mappings) && strcmp($m->mappingtype,"mainid")!==0){
	    $query .= "} UNION {";
	}
    }
    $query .= "}}";
    $vars = "";
    for ($nvars = 0; $nvars < $maxlevel; $nvars++){
	$vars .= "?p".($nvars + 1)." ?o".($nvars+1)." ";
    }
    $query = 'select '.$vars.' where {graph <urn:dataset/'.$graph.'/graph> '.'{'.$query.'}}';
    $res->{"mks:types"}->{"type/global:id/".$mm->type}->query_text = $query;
    // print_r($res);
    // write to ECAPI
    // create the fetch queryc
    sendToCouchDB($graph, $res);
    return $res;
}

function mappingtoMKIOConfig($mapping){
  $res = array();
  $mm = getMainMapping($mapping);
  $type = $mm->type;
  foreach($mapping as $m){
    if (strcmp($m->mappingtype,"value")===0){
      $graph = new stdClass();
      $graph->type=$type;
      $rels = explode('.', $m->relation);
      $graph->dimensions = $rels;
      $res[]=$graph;
    }
  }
  return $res;
}

function urify($s){
  return str_replace("/", "--", str_replace("+", "plus", str_replace("\n", "", str_replace('(', '-', str_replace(')', '-', str_replace("&", "and", str_replace(" ", "_", strtoLower($s))))))));
}

function getMainMapping($m){
    foreach($m as $ma){
	if(strcmp($ma->mappingtype, "mainid")===0){
	    return $ma;
	}
    }
    return null;
}

function getColInLine($line, $col){
  $vals = array_values($line);
  return $vals[$col];
}

function startsWith($haystack, $needle) {
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function sendToCouchDB($id, $body){
    include('config.php');
    $cb = new couchdb($couchdburl, $couchdb, $couchdbuser, $couchdbpw);
    $docjson = $cb->getDoc($id);
    if (isset($docjson->_rev)){
	$body->_rev = $docjson->_rev;
    }
    $jsons = json_encode($body);
    $r = $cb->saveDoc($id, $jsons);
    //    print_r($r);
    // also update namespaces... if needed for object...
}

function tfn($s){
  return str_replace(":", "__", str_replace("/", "--", $s));
}