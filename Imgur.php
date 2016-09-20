
<?php
// ID of gallery, you get it from URL.
$galleryId = 'nuAM3';
$url = 'http://imgur.com/ajaxalbums/getimages/'.$galleryId.'/hit.json?all=true';
$jsonContent = file_get_contents($url);
$data = json_decode($jsonContent, true);
if(!is_dir('images_'.$galleryId)) {
  mkdir('images_'.$galleryId);
}
foreach($data['data']['images'] as $image) {
  
  $name = $image['hash'].$image['ext'];
  
  $imageContent = file_get_contents('http://i.imgur.com/'.$name);
  
  file_put_contents('images_'.$galleryId.'/'.$name, $imageContent);
}
  
