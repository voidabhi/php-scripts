
<?php

class WunderList
{
    protected $client_id;
    protected $client_secret;
    protected $redirect_url;
    protected $state;
    protected $access_token;
    protected $header = array();
    
    public function __construct($client_id, $client_secret, $redirect_url, $state, $access_token='')
    {
        $this->client_id     = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_url  = $redirect_url;
        $this->state         = $state;
        $this->access_token  = $access_token;
        
        if($this->access_token == ''){
            $this->setAccessToken(); 
        }
        
        $this->header = array(
            "X-Client-ID: {$this->client_id}",
            "X-Access-Token: {$this->access_token}"
       );
    }
    
    private function setAccessToken()
    {
        $code  = isset($_GET['code']) ? $_GET['code'] : '';
        $state = isset($_GET['state']) ? $_GET['state'] : '';
        
        if($code == ''){
            $params = array(
                'client_id' => $this->client_id,
                'state'     => $this->state
            );
            header("Location: https://www.wunderlist.com/oauth/authorize?redirect_uri={$this->redirect_url}&".http_build_query($params));
            exit();
        }
        else{
            if($state != $this->state){
                exit('Failed');
            }
            
            $token_url = 'https://www.wunderlist.com/oauth/access_token';
            $params = array(
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'code' => $code
            );
            $response = $this->request($token_url, 'POST', $params, false);
            $this->access_token = $response['access_token'];
        }
    }
    
    public function request($url,$method,$params=array(),$json=true)
    {
        $header = $this->header;
        
        if($json){
            $header[] = "Content-Type: application/json; charset=utf-8";
            $data = json_encode($params);
        }
        else{
            $header[] = "Content-Type: application/x-www-form-urlencoded";
            $data = http_build_query($params);
        }
        $header[] = "Content-Length: ".strlen($data);
        
        $context = array(
            "http" => array(
                "method"  => $method,
                "header"  => implode("\r\n", $header),
                "content" => $data
            )
        );
        $result = file_get_contents($url, false, stream_context_create($context));
        return json_decode($result, true);
    }
}
