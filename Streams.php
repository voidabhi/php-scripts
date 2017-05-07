
<?php
include "github-creds.php"; // sets $access_token
ini_set('user_agent', "PHP"); // github requires this
$api = 'https://api.github.com';
$url = $api . '/gists'; // no user info because we're sending auth
// prepare the body data 
$data = json_encode(array(
    'description' => 'Inspiring Poetry',
    'public' => 'true',
    'files' => array(
        'poem.txt' => array(
            'content' => 'If I had the time, I\'d make a rhyme'
        )
    )
)); 
// set up the request context
$options = ["http" => [
    "method" => "POST",
    "header" => ["Authorization: token " . $access_token,
        "Content-Type: application/json"],
    "content" => $data
    ]];
$context = stream_context_create($options);
// make the request
$response = file_get_contents($url, false, $context);
