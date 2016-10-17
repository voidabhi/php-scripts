<?php
require_once __DIR__.'/vendor/autoload.php';
use Aws\Lambda\LambdaClient;
$client = LambdaClient::factory([
    'version' => 'latest',
    'region'  => 'eu-west-1',
]);
$result = $client->invoke([
    'FunctionName' => 'hello-world',
]);
echo json_decode((string) $result->get('Payload'));
