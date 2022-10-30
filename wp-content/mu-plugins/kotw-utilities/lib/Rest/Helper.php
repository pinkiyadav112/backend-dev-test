<?php
/*
 * Copyright (c) 2022. Property of Kings Of The Web
 *
 * Helper methods to be used for the endpoints API
 */

namespace kotw\Rest;

use Exception as Exception;
use kotw\Logger;
use WP_REST_Request as WP_REST_Request;
use WP_USER;

class Helper {


	/**
	 * Takes a WordPress REST Request Object and returns a user if it exists.
	 * This only works with the current user making the request.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return false|void
	 */
	public static function request_to_user( WP_REST_Request $request ) {
		try {
			$request_cookie = $request->get_header( 'cookie' );
			if ( ! $request_cookie ) {
				// User is not logged-in, and it is not open to the public >> return FALSE
				return false;
			}

			$user_id = wp_validate_auth_cookie( '', 'logged_in' );

			return get_user_by( 'id', $user_id );
		} catch ( Exception $e ) {
			new Logger( __CLASS__, $e, 'request_to_user' );
		}
	}


	/**
	 * This one validates a cookie string and returns a user  if it exists, or it returns false if it doesn't exist.
	 *
	 * @param $wordpress_cookie
	 *
	 * @return false|WP_User
	 */
	public static function cookie_to_user( $wordpress_cookie ) {
		try {
			if ( empty( $wordpress_cookie ) ) {
				return false;
			}

			$user_id = wp_validate_auth_cookie( $wordpress_cookie, 'logged_in' );

			return get_user_by( 'id', $user_id );
		} catch ( Exception $e ) {
			new Logger( __CLASS__, $e, 'cookie_to_user' );
		}
	}

}
