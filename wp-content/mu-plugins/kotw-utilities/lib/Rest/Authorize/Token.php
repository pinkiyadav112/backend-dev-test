<?php
/**
 * A helper class for managing Tokens over REST API.
 *
 * @author     Kings Of The Web
 * @year       2022
 * @package    redesign.codedegree.com
 * @subpackage kotw\Rest\Authorize
 */

namespace kotw\Rest\Authorize;

class Token {

	public static string $token_separator = '__user_id__';

	/**
	 * This returns a user ID from headers array.
	 * NOTE: This does not validate the token.
	 *
	 * @param $headers
	 *
	 * @return bool|int
	 */
	public static function parse_user_id( $headers ): bool|int {

		if ( ! is_array( $headers ) ) {
			return false;
		}

		$token = self::parse_bearer_token( $headers );

		// Required parameter.
		if ( ! $token ) {
			return false;
		}

		// explode by self::$token_separator
		$token = explode( self::$token_separator, $token );

		return (int) $token[1] ?? false;
	}

	/**
	 * This returns a session_token from headers array.
	 *
	 * NOTE: This does not validate the token.
	 *
	 * @param $headers
	 *
	 * @return false|string
	 */
	public static function parse_session_token( $headers ): bool|string {
		if ( ! is_array( $headers ) ) {
			return false;
		}

		$token = self::parse_bearer_token( $headers );

		// explode by self::$token_separator
		$token = explode( self::$token_separator, $token );

		return $token[0] ?? false;
	}

	/**
	 * This parses the authorization bearer header to get the token.
	 *
	 * @param $headers  array  The headers array.
	 *
	 * @return false|string
	 */
	public static function parse_bearer_token( $headers ): bool|string {

		if ( ! is_array( $headers ) ) {
			return false;
		}

		$authorization_header = $headers['Authorization'] ?? $headers['authorization'] ?? null;
		if ( ! is_array( $authorization_header ) ) {
			return false;
		}

		$authorization_header = explode( ' ', $authorization_header[0] );
		if ( ! is_array( $authorization_header ) || ! isset( $authorization_header[1] ) ) {
			return false;
		}

		return $authorization_header[1];
	}

}
