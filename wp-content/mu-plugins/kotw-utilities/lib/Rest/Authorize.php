<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    english.codedegree.com
 * @subpackage kotw\Rest
 */

namespace kotw\Rest;

use kotw\Rest\Authorize\Token;
use \WP_REST_Request as WP_REST_Request;
use \WP_User as WP_User;
use kotw\Rest\Helper as KotwRestHelper;

class Authorize {

	/**
	 * This authorizes the cookie_string passed with the request against a specific user id.
	 *
	 * Scenarios to authorize the request as valid:
	 *  1. The cookie_string is a valid logged_in cookie for a specific WP User and the user_id is that specific user.
	 *
	 * @param $request  WP_REST_Request (required)  The request object.
	 * @param $user_id  int|null        (optional)  The WordPress user id. If not provided, then the user_id passed to                                                      the request will be used.
	 *
	 * @return false
	 */
	public static function authorize_cookie_string( $request, int $user_id = null ): bool {
		// Required parameter.
		if ( ! $request instanceof WP_REST_Request ) {
			return false;
		}

		// If user_id exists in $request,
		$user_id = $user_id ?? $request->get_param( 'user_id' );

		// check if $user_id is a valid WP user.
		$user = get_user_by( 'id', $user_id );
		if ( ! $user instanceof WP_User ) {
			return false;
		}

		// Is there a cookie_string added as a get or post parameter?
		$cookie_string = $request->get_param( 'cookie_string' );
		if ( ! $cookie_string ) {
			return false;
		}

		$user_from_cookie = KotwRestHelper::cookie_to_user( $cookie_string );

		// Is this a valid WP User?
		if ( ! $user_from_cookie instanceof WP_User ) {
			return false;
		}

		// Is the user_id from the cookie the same as the user_id passed as a parameter?
		if ( $user_from_cookie->ID !== $user->ID ) {
			return false;
		}

		return true;

	}


	/**
	 * This authorizes the user's bearer token passed with the headers against their user ID.
	 *
	 * @param $headers
	 *
	 * @return false|int
	 */
	public static function user_token( $headers ): bool|int {

		if ( ! is_array( $headers ) ) {
			return false;
		}

		$session_token = Token::parse_session_token( $headers );
		$user_id       = Token::parse_user_id( $headers );

		// get the user from the user id.
		$user = get_user_by( 'id', $user_id );
		if ( ! $user instanceof WP_User ) {
			return false;
		}

		// get the login_sessions for the user.
		$login_sessions = get_user_meta( $user->ID, 'session_tokens', false );
		$login_sessions = is_array( $login_sessions ) && count( $login_sessions ) > 0 ? $login_sessions[0] : null;

		if ( ! is_array( $login_sessions ) ) {
			return false;
		}

		$login_sessions_tokens = array_keys( $login_sessions );

		// if the session token is not in the login_sessions array, then return false.
		if ( ! in_array( $session_token, $login_sessions_tokens ) ) {
			return false;
		}

		return $user->ID;
	}

}
