<?php

use Firebase\JWT\JWT;
class DwJwt {
    public static $_data = [];
    /**
     * Get decode variable
     * @param type $var
     * @return type
     */
    public static function get($var) {
        return isset(self::$_data[$var]) ? self::$_data[$var] : NULL;
    }
    /**
     *
     * @param type $token
     * @return type
     */
    public static function decode($token) {
        try {
            $payload        = JWT::decode($token, JWT_KEY, array('HS256'));
            self::$_data    = (array) $payload;
            return $payload;
        } catch (Exception $e) {
            self::$_data    = [];
            return [];
        }
    }
    /**
     *
     * @param array $token
     * @return type
     */
    public static function encode($token) {
        return JWT::encode($token, JWT_KEY, 'HS256');
    }
}
