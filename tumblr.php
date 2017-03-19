
<?php
session_start();
require_once 'vendor/autoload.php';
$consumerKey = 'doTy109KhSbt4QKzdYWDns7lIWMhiehHKJ29NNsc6VS0CImPUU';
$consumerSecret = 'AsPvCGXe13Ydvcovu8uCyX4WhNk7chLoGM7bNMpH7hjQgA8FhI';
$tmpToken = isset($_SESSION['tmp_oauth_token'])? $_SESSION['tmp_oauth_token'] : null;
$tmpTokenSecret = isset($_SESSION['tmp_oauth_token_secret'])? $_SESSION['tmp_oauth_token_secret'] : null;
$client = new Tumblr\API\Client($consumerKey, $consumerSecret, $tmpToken, $tmpTokenSecret);
// Change the base url
$requestHandler = $client->getRequestHandler();
$requestHandler->setBaseUrl('https://www.tumblr.com/');
if (!empty($_GET['oauth_verifier'])) {
    // exchange the verifier for the keys
    $verifier = trim($_GET['oauth_verifier']);
    $resp = $requestHandler->request('POST', 'oauth/access_token', array('oauth_verifier' => $verifier));
    $out = (string) $resp->body;
    $data = array();
    parse_str($out, $data);
    unset($_SESSION['tmp_oauth_token']);
    unset($_SESSION['tmp_oauth_token_secret']);
    $_SESSION['Tumblr_oauth_token'] = $data['oauth_token'];
    $_SESSION['Tumblr_oauth_token_secret'] = $data['oauth_token_secret'];
}
if (empty($_SESSION['Tumblr_oauth_token']) || empty($_SESSION['Tumblr_oauth_token_secret'])) {
    // start the old gal up
    $callbackUrl = 'http://tmp.my/tumblr.php-master';
    $resp = $requestHandler->request('POST', 'oauth/request_token', array(
            'oauth_callback' => $callbackUrl
        ));
    // Get the result
    $result = (string) $resp->body;
    parse_str($result, $keys);
    $_SESSION['tmp_oauth_token'] = $keys['oauth_token'];
    $_SESSION['tmp_oauth_token_secret'] = $keys['oauth_token_secret'];
    $url = 'https://www.tumblr.com/oauth/authorize?oauth_token=' . $keys['oauth_token'];
    echo '<a href="'.$url.'">Connect Tumblr</a>';
    exit;
}
$client = new Tumblr\API\Client(
    $consumerKey,
    $consumerSecret,
    $_SESSION['Tumblr_oauth_token'],
    $_SESSION['Tumblr_oauth_token_secret']
);
$clientInfo = $client->getUserInfo();
$blogs = !empty($clientInfo->user->blogs)? $clientInfo->user->blogs : null;
foreach ($blogs as $blog) {
    $client->createPost($blog->name, array(
            'type' => 'text',
            'body' => 'This is Sparta!!!!!!!!!!'
        ));
}
exit;
