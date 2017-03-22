<?php
/*
Create table as:-
    create table data (id integer primary key, message varchar)
Then to see the update on browser, insert new message:-
    insert into data (message) values ('hello');
    ...
    insert into data (message) values ('second');
*/
$db = new SQLite3('data.db');
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache'); // recommended to prevent caching of event data.
function send_msg($id, $msg) {
    echo "id: $id" . PHP_EOL;
    echo "data: $msg" . PHP_EOL;
    echo PHP_EOL;
    ob_flush();
    flush();
}
// in the event of client reconnect, it will send Last-Event-ID in the headers
// this only evaluated during the first request and subsequent reconnect from client
$last_event_id = floatval(isset($_SERVER["HTTP_LAST_EVENT_ID"]) ? $_SERVER["HTTP_LAST_EVENT_ID"] : False);
if ($last_event_id == 0) {
    $last_event_id = floatval(isset($_GET["lastEventId"]) ? $_GET["lastEventId"] : False);
}
// also keep our own last id for normal updates but favor last_event_id if it exists
// since on each reconnect, this value will lost
$last_id = 0;
while (1) {
    error_log('$last_id:' . $last_id, 4);
    error_log('$last_event_id:' . $last_event_id, 4);
    $id  = $last_event_id != False ? $last_event_id : $last_id;
    $stm = $db->prepare("SELECT id, message FROM data WHERE id > :id");
    $stm->bindValue('id', $id);
    $results = $stm->execute();
    if ($results) {
        while ($row = $results->fetchArray()) {
            if ($row) {
                send_msg($row['id'], $row['message']);
                $last_id = $row['id'];
            }
        }
    }
    sleep(5);
}
