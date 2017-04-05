<?php 
session_start();
require_once("twitteroauth/twitteroauth.php"); //Path to twitteroauth library

#$search = "%23kmiou OR %23ou_ceremonies";
$search = "#OUDigitalInnovation";
$notweets = 500;
$consumerkey = "r3lKSB3KgVrbRzI0BkamhNWRd";
$consumersecret = "WBzxsgM3rTTJSBKbf3bCTMtK0csMscLOfBt24trPB91jfmRCOY";
$accesstoken = "246184280-uofLEYBw9KRFMPdqqlXBk47phw6G3SIYXFjJkiNU";
$accesstokensecret = "h3u3VTAAQxyICUfQSFtz0fJkKO2Q2PJ05LgY6DS3UduzH";
 
function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
  $tconnection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
  return $tconnection;
}
 
$tconnection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
$search = str_replace("#", "%23", $search);
$tweets = $tconnection->get("https://api.twitter.com/1.1/search/tweets.json?q=".$search."&count=".$notweets);
 
//Check twitter response for errors.
if ( isset( $tweets->errors[0]->code )) {
    // If errors exist, print the first error for a simple notification.
    echo "Error encountered: ".$tweets->errors[0]->message." Response code:" .$tweets->errors[0]->code;
} else {
    // No errors exist. Write tweets to json/txt file.
    $file = "diw-tweets.txt";
    //$file = "cache/tweets.txt";
    $fh = fopen($file, 'w') or die("can't open file");
    fwrite($fh, json_encode($tweets));
    fclose($fh);
}


$file_new = array_unique(file('diw-tweets.txt'));
$file_history = array_unique(file('diw-tweets-history.txt')); // Note : I supposed array_unique here too
$file_history_to_write = fopen('diw-tweets-history.txt', 'w');
foreach ($file_new as $line_unique) {
     if (!in_array($line_unique, $file_history)) {
         fwrite($file_history_to_write, $line_unique);
     }
}
fclose($file_history_to_write);
?>