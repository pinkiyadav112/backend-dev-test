<?php
/**
 * All custom redirects rules should be added here.
 *
 * @author     Kings Of The Web
 * @year       2022
 * @package    store.kotwrest.com
 * @subpackage kotwrest\Wordpress
 */

namespace kotwrest\Wordpress;

class Redirects {

	public function __construct() {
	}


	/**
	 * Main redirect method that should be called for invoking a redirect.
	 *
	 * @param $source
	 * @param $destination
	 *
	 * @return void
	 */
	public static function redirect( $source, $destination ): void {
		preg_match( $source, $_SERVER['REQUEST_URI'], $m );
		if ( count( $m ) > 0 ) {
			wp_safe_redirect( $destination );
		}
	}
}
