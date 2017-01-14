<?php

<?php
class API {
    const OK = 200;
    const CREATED = 201;
    const NO_CONTENT = 204;
    const NOT_MODIFIED = 304;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const PAYMENT_REQUIRED = 402;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const NOT_ACCEPTABLE = 406;
    const GONE = 410;
    const LENGTH_REQUIRED = 411;
    const PRECONDITION_FAILED = 412;
    const UNPROCESSABLE_ENTITY = 422;
    const TOO_MANY_REQUESTS = 429;
    const INTERNAL_SERVER_ERROR = 500;
    const SERVICE_UNAVAILABLE = 503;
    public static function get($code = null){
        $options = array(
            self::OK => __('OK'),
            self::CREATED => __('Created'),
            self::NO_CONTENT => __('No Content'),
            self::NOT_MODIFIED => __('Not Modified'),
            self::BAD_REQUEST => __('Bad Request'),
            self::UNAUTHORIZED => __('Unauthorized'),
            self::PAYMENT_REQUIRED => __('Payment Required'),
            self::FORBIDDEN => __('Forbidden'),
            self::NOT_FOUND => __('Not Found'),
            self::METHOD_NOT_ALLOWED => __('Method Not Allowed'),
            self::NOT_ACCEPTABLE => __('Not Acceptable'),
            self::GONE => __('Gone'),
            self::LENGTH_REQUIRED => __('Length Required'),
            self::PRECONDITION_FAILED => __('Precondition Failed'),
            self::UNPROCESSABLE_ENTITY => __('Unprocessable Entity'),
            self::TOO_MANY_REQUESTS => __('Too Many Requests'),
            self::INTERNAL_SERVER_ERROR => __('Internal Server Error'),
            self::SERVICE_UNAVAILABLE => __('Service Unavailable'),
        );
        if(!is_null($code)){
            if(array_key_exists($code, $options)){
                return array('status' => $options[$code], 'code' => (string)$code);
            }else{
                return array('status' => 'Internal Service Error', 'code' => (string)42);
            }
        }
        return $options;
    }
    protected static function enum($value, $options, $default = '') {
        if ($value !== null) {
            if (array_key_exists($value, $options)) {
                return $options[$value];
            }
            return $default;
        }
        return $options;
    }
    public static function response($code, $data = array(), $app = null){
        $status = self::get($code);
        $code = $status['code'];
        if(is_null($app)){
            //Create a response
            $response = new \Phalcon\Http\Response();
        }else{
            $response = $app->response;
        }
        if($data !== true){
            $data = array_merge($status, $data);
            $response->setJsonContent($data);
        }else{
            $response->setJsonContent($status);
        }
        $content = $response->getContent();
        $response->setContentType('application/json', 'UTF-8')
            ->setStatusCode($code, $status['status'])
            ->setRawHeader("HTTP/1.0 $code" . $status['status'])
            ->setRawHeader("Content-Length: " . strlen($content));
        if($response->isSent()){
            return $response;
        }else{
            return $response->send();
        }
    }
    public static function status($value = null) {
        return self::enum($value, self::get());
    }
}
