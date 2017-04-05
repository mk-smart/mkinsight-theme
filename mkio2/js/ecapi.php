<?php

$ecapi_base = "https://data.beta.mksmart.org/entity/";

function getInstancesOfType($type){
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $ecapi_base.$type); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        curl_close($ch);      	
	return json_decode($output);
}



?>