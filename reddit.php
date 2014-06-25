<?php

class Reddit {
	private static $user_agent = 'Reddy https://github.com/voidabhi/reddy';

	protected static function make_request($endpoint, $params) {
	
		$request_url = self::build_request_url( $endpoint ) . '?' . http_build_query( $params );

		// init - opening curl connection
		$curl = curl_init();
		
		// setting curl config
		curl_setopt( $curl, CURLOPT_URL, $request_url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 5 );
		//curl_setopt( $curl, CURLOPT_USERAGENT, self::$user_agent );

		// executing curl request
		$response = curl_exec($curl);
		
		// clising curl connection
		curl_close($curl);

		return $response;
	}

	private static function build_request_url($endpoint) {
		return 'http://www.reddit.com' . $endpoint . '.json';
	}
}