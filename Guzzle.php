 <?php
  require_once 'vendor/autoload.php';
  use Guzzle\Http\Client;
  
  $user = '<<user>>';
  $password = '<<password>>';
  $url = '<<baseurl>>/rest/v10/';
  
  $client = new Client($url);
 
  $request = $client->post('oauth2/token', null, array(
      'grant_type' => 'password',
      'client_id' => 'sugar',
      'username' => $user,
      'password' => $password,
  ));
  $results = $request->send()->json();
  $token = $results['access_token'];
 
  $request = $client->get('me');
  $request->setHeader('OAuth-Token', $token);
 
  $currentUser = $request->send()->json();
 
  var_dump($currentUser);
