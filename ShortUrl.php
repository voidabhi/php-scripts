<?php
function shorten($url, $qr=NULL){
if(function_exists('curl_init')){
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, 'http://goo.gl/api/shorten');
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'security_token=null&url='.urlencode($url));
$results = curl_exec($ch);
$headerInfo = curl_getinfo($ch);
curl_close($ch);
if ($headerInfo['http_code'] === 201){ // HTTP Code 201 = Created
$results = json_decode($results);
if(isset($results->short_url)){
$qr = !is_null($qr)?'.qr':'';
return $results->short_url.$qr;
}
return FALSE;
}
return FALSE;
}
trigger_error("cURL required to shorten URLs.", E_USER_WARNING); // Show the user a neat error.
return FALSE;
}
// Example: Just the Short URL
echo shorten('http://www.google.com/');
// Example: Give the Short Code URL and image it.
$qrURL = shorten('http://www.google.com/', TRUE);
echo '<img src="'.$qrURL.'" />';
?>
