<?php
//check the POST
$ips = $_POST["ips"];
if (empty($ips)) {
    echo 'Please provide ips.';
} else {
  
  //connect to mysql and select the maxmind database
  $con = mysql_connect("localhost","root","root");
  if (!$con){
    die('Could not connect: ' . mysql_error());
  }
  mysql_select_db("maxmind", $con);
  
  //blank array setup to store all lat and long coords
  $finalarray = array();
  
  //turn ip string into array so we can loop through it
  $iparray = explode(",", $ips);
  
  //loop through each ip
  foreach($iparray as $ip){
    //our mysql query
    $query = "SELECT latitude,longitude FROM location WHERE locid = ( SELECT locid FROM blocks WHERE INET_ATON('". $ip ."') BETWEEN startIPNum AND endIPNum);";
    $result = mysql_query($query);
    if ($result) {
      $row = mysql_fetch_row($result);
      
      //create an array of ip geo coords for one result
      $geoarray = array($row[0], $row[1]);
      
      //push array of individual geo coords into main geo location array above
      array_push($finalarray, $geoarray);
    }
  }
  
  //return the final multidimensional array as JSON
  $return = json_encode($finalarray);
  echo $return;
}
?>
