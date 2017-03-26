<?php
$m = new MongoClient();
 
// select a database
$db = $m->seq;
// select a collection (analogous to a relational database's table)
$collection = $db->counters;
$user_collection = $db->user;
 
 
 
/******************Function to auto increment seq******************/
function getNextSequence($name){
global $collection;
 
$retval = $collection->findAndModify(
     array('_id' => $name),
     array('$inc' => array("seq" => 1)),
     null,
     array(
        "new" => true,
    )
);
return $retval['seq'];
}
/********************Example Usage**********************************/
$db_array=array('_id' => getNextSequence("userid"), 'name' => 'debojit');
 
$user_collection->insert($db_array);
