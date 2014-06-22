<?php

class Reddit {
	private static $user_agent = 'SnapCorgi http://github.com/pcrumm/snapcorgi';

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

class Corgi extends Reddit {

	public static function fetch_corgi() {
		$reddit_corgis = self::fetch_corgi_post();
		return self::fetch_first_image_from_posts( json_decode( $reddit_corgis, true ) );
	}

	private static function fetch_corgi_post() {
		$params = array(
			't' 	=> 'day',
			'limit'	=> 5 // in case the first isn't a jpg
		);

		return self::make_request( '/r/corgi/top', $params );
	}

	private static function fetch_first_image_from_posts( $corgis ) {
		if(isset($corgis['data']['children']))
		{
			foreach ( $corgis['data']['children'] as $corgi ) {
				// We only want images, so we cheat and look for imgur links ending in .jpg
				if ( '.jpg' == substr( $corgi['data']['url'], -4) && strpos( $corgi['data']['url'] , 'imgur' ) )
					return $corgi['data']['url'];
			}

			return false;
		}
	}
}

$img = Corgi::fetch_corgi();
print '<img src='.$img.'/>';