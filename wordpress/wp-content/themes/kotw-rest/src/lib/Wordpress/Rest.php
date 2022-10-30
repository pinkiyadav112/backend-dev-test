<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    kotwrest.com
 * @subpackage kotwrest
 */

namespace kotwrest\Wordpress;

class Rest {
	public function __construct() {
		// Update headers using wp_headers filter.
		add_filter( 'wp_headers', array( $this, 'add_headers' ), 0, 1 );
	}

	/**
	 * Filter the WordPress headers to fix CORS issue.
	 *
	 * @param $headers
	 *
	 * @return array
	 */
	public function add_headers( $headers ): array {
		if ( defined( 'KOTWREST_ALLOWED_DOMAINS' ) ) {
			foreach ( KOTWREST_PARENT_ALLOWED_DOMAINS as $domain ) {
				$headers[] = 'Access-Control-Allow-Origin: ' . $domain;
			}
		}

		return $headers;
	}
}
