<?php

$url = '<INSERT_GOOGLE_SHEET_URL_HERE>';
$file= file_get_contents($url);
$json = json_decode($file);
$rows = $json->{'feed'}->{'entry'};
foreach($rows as $row) {
  echo '<p>';
  $title = $row->{'gsx$title'}->{'$t'};
  $author = $row->{'gsx$author'}->{'$t'};
  $review = $row->{'gsx$review'}->{'$t'};
  echo $title . ' by ' . $author . '<br>' . $review;
  echo '</p>';
}

?>
