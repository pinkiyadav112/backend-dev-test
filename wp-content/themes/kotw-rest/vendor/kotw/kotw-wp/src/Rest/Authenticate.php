<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    english.codedegree.com
 * @subpackage kotw\Rest
 */

namespace KotwWP\Rest;


use kotw\Logger;

class Authenticate {


	/**
	 * This authenticates the login_sessions.
	 *
	 * @return bool
	 */
	public static function session_headers( $headers ) {
		new Logger( __CLASS__, $headers, '$headers' );


		$authorization_token = $headers['X-KOTW-SESSION-TOKEN'] ?? $headers['x-kotw-session-token'] ?? $headers['Authorization'] ?? $headers['authorization'];



		if ( ! $authorization_token ) {
			return false;
		}

		// explode $token by '__user_id__'.
		$token         = explode( '__user_id__', $authorization_token );
		$session_token = $token[0];
		$user_id       = $token[1];

		new Logger( __CLASS__, $user_id, 'user_id' );
		new Logger( __CLASS__, $session_token, '$session_token' );
	}
}
