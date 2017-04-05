<?php

// Based on http://wiki.apache.org/couchdb/Getting_started_with_PHP

class couchdb {
    function couchdb($cburl, $db, $user, $pass) {
       $this->cburl = $cburl;
       $this->db    = $db;       
       $this->user  = $user;
       $this->pass  = $pass;
    } 
   
   function getDoc($id) {
     $ch = curl_init(); 
     curl_setopt($ch, CURLOPT_URL, $this->cburl.'/'.$this->db.'/'.urlencode($id));
     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		 'Content-type: application/json',
		 'Accept: */*'
     )); 
    curl_setopt($ch, CURLOPT_USERPWD, $this->user.':'.$this->pass); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response);
   }

   function saveDoc($id, $jsons){
      $ch = curl_init(); 
      curl_setopt($ch, CURLOPT_URL, $this->cburl.'/'.$this->db.'/'.urlencode($id));
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); 
      curl_setopt($ch, CURLOPT_POSTFIELDS, $jsons);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		 'Content-type: application/json',
		 'Accept: */*'
      )); 
      curl_setopt($ch, CURLOPT_USERPWD, $this->user.':'.$this->pass); 
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      $response = curl_exec($ch); 
      // print_r(curl_getinfo($ch));
      curl_close($ch);
      return $response;
   } 
}

?>