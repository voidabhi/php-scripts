
<?php
require('simpleCache.php'); 
$cache = new SimpleCache();
$minx = $_GET['minx'];
$maxx = $_GET['maxx'];
$miny = $_GET['miny'];
$maxy = $_GET['maxy'];
//FLICKR
$flickr_api = 'YOUR FLICKR API KEY';
$flickr_url = 'http://api.flickr.com/services/rest?api_key='.$flickr_api.'&format=json&method=flickr.photos.search&extras=geo%2Curl_s%2C&per_page=20&page=1&bbox='.$minx.','.$miny.','.$maxx.','.$maxy.'&jsoncallback=flickr';
$flickr_file = 'flickr'.$minx.'-'.$miny;
$flickr = $cache->get_data($flickr_file, $flickr_url);
//PANORAIMO
$panoraimo_url = 'http://www.panoramio.com/map/get_panoramas?order=popularity&set=full&from=0&to=50&size=thumbnail&minx='.$miny.'&miny='.$minx.'&maxx='.$maxy.'&maxy='.$maxx.'&callback=panoraimo';
$panoraimo_file = 'panoraimo'.$minx.'-'.$miny;
$panoraimo = $cache->get_data($panoraimo_file, $panoraimo_url);
//show result
echo $flickr.';'.$panoraimo;
?>
