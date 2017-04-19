<?php

class Server
{
    public function message() {
        return Array(
            "hello" => "world"
        )
    }
}

$server = new Server();
$command = isset($_REQUEST['command']) ? $_REQUEST['command'] : null;
if (!$command || !method_exists($server, $command)) {
    header("HTTP/1.1 404 Not Found");
    header('Content-type: application/json; charset=UTF-8');
    
    echo json_encode(['error' => 'Unknown command']);
    exit();
}
$result = $server->$command();
