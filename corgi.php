<?php

class Corgi extends Reddit {

	public static function fetch_corgi() {
		$reddit_corgis = self::fetch_corgi_post();
		return self::fetch_first_image_from_posts( json_decode( $reddit_corgis, true ) );
	}

	// to fetch top corgi posts
	private static function fetch_corgi_post() {
		$params = array(
			't' 	=> 'day',
			'limit'	=> 5 // in case the first isn't a jpg
		);

		return self::make_request( '/r/corgi/top', $params );
	}

	// fetch top image
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
