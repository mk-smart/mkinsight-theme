<?php

function isDataPage(){
   $id = get_the_ID();
   include('mkio2config.php');
   if ($id === $pageids['data']) return true;
   return false;
}

function isMapsPage(){
   $id = get_the_ID();
   include('mkio2config.php');
   if ($id === $pageids['maps']) return true;
   return false;
}


?>