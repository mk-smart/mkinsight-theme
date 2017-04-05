<?php

  // TODO: parse the jsonP (split with . I think...)
  // TODO: fix the URIs that have a '&' in (namespace) and '-'

function getInstancesOfType($type, $cachebypass){
  if (file_exists("cache/".tfn($type)) && $cachebypass!==true){
    $output = file_get_contents("cache/".tfn($type));
    error_log("got instances from cache");
    return json_decode($output)->instances;
  }
  $ecapi_base = "https://data.beta.mksmart.org/entity/";
  error_log("calling  ".$ecapi_base.$type);
  $ch = curl_init(); 
  curl_setopt($ch, CURLOPT_URL, $ecapi_base.$type); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  $output = curl_exec($ch); 
  curl_close($ch);      	
  file_put_contents("cache/".tfn($type), $output);
  return json_decode($output)->instances;	
 }

function getValues($type, $instances, $attr, $cachebypass){
  if (file_exists("cache/".tfn($type.'_'.$attr)) && $cachebypass!==true){
    $output = file_get_contents("cache/".tfn($type.'_'.$attr));
    error_log("got data from cache");
    return $output;
  }
  $data = array();
  $attar = explode('.', $attr);    
  foreach($instances as $i){
    error_log("calling  ".$i);
    if (file_exists("cache/".tfn($i))){
      $output = file_get_contents("cache/".tfn($i));
      error_log("got $i from cache");
    } else {
      $ch = curl_init(); 
      curl_setopt($ch, CURLOPT_URL, $i); 
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      $output = curl_exec($ch); 
      curl_close($ch);      	
      file_put_contents("cache/".tfn($i), $output);      
    }
    $data[$i]=json_decode($output);
    foreach ($attar as $at){
      if (isset($data[$i]->{"$at"}))
	$data[$i] = $data[$i]->{"$at"}[0];
      else 
	$data[$i] = "";  
    }
  }
  $count=0;
  $r = array();
  ksort($data);
  foreach($data as $e=>$v){
    $dp = new StdClass();
    $dp->x = $count;
    $dp->y = floatval($v);
    $dp->label=str_replace("_", " ", fragment($e));
    $dp->entity=$e;
    $r[]=$dp;
    $count++;
  }
  $result = json_encode($r);
  file_put_contents("cache/".tfn($type.'_'.$attr), $result); 
  return $result;
}

function getValuesMap($type, $instances, $attr, $cachebypass){
  if (file_exists("cache/ma-".tfn($type.'_'.$attr)) && $cachebypass!==true){
    $output = file_get_contents("cache/map-".tfn($type.'_'.$attr));
    error_log("got data from cache");
    return $output;
  }
  $data = array();
  $attar = explode('.', $attr);    
  foreach($instances as $i){
    error_log("calling  ".$i);
    if (file_exists("cache/".tfn($i))){
      $output = file_get_contents("cache/".tfn($i));
      error_log("got $i from cache");
    } else {
      $ch = curl_init(); 
      curl_setopt($ch, CURLOPT_URL, $i); 
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      $output = curl_exec($ch); 
      curl_close($ch);      	
      file_put_contents("cache/".tfn($i), $output);      
    }
    $data[$i]=array();
    $data[$i]['value']=json_decode($output);
    $data[$i]['lat']=$data[$i]['value']->{"geo:lat"};
    $data[$i]['long']=$data[$i]['value']->{"geo:long"};
    foreach ($attar as $at){
      if (isset($data[$i]['value']->{"$at"}))
	$data[$i]['value'] = $data[$i]['value']->{"$at"}[0];
      else 
	$data[$i]['value'] = "";  
    }
  }
  $result = json_encode($data);
  file_put_contents("cache/map-".tfn($type.'_'.$attr), $result);
  return $result;
}

function tfn($s){
  return str_replace(":", "__", str_replace("/", "--", $s));
}

function fragment($uri){
  return substr($uri, strrpos($uri, '/')+1);
}

?>
