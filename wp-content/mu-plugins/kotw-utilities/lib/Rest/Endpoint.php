<?php
/**
 * This is the modal for new Endpoint.
 */

namespace kotw\Rest;

use kotw\Logger;
use Exception as Exception;
use WP_REST_Response as WP_REST_Response;

class Endpoint {

	/**
	 * @var false
	 */
	private static bool $public_access;
	/**
	 * @var string[]
	 */
	private static array $allowed_user_roles;
	/**
	 * @var string[]
	 */
	private static array $allowed_domains;
	/**
	 * @var false
	 */
	private static bool $same_domain_access;

	public static function init() {
		self::$public_access      = false;
		self::$same_domain_access = true;
		self::$allowed_user_roles = array( 'endpoint' );
		self::$allowed_domains    = array();
	}

	/**
	 * This handles all types of errors.
	 *
	 * @param string $message
	 * @param int $status
	 *
	 * @return WP_REST_Response
	 */
	public static function handle_error( string $message = 'Not Authorized', int $status = 401 ): WP_REST_Response {
		$return_message = array(
			'message' => $message,
			'data'    => array(
				'status' => $status,
			),
		);

		return new WP_REST_Response( $return_message, $status );
	}

	/**
	 * This handles all types of errors.
	 *
	 * @param array $response
	 * @param int $status
	 *
	 * @return array
	 */
	public static function handle_success( array $response = array(), int $status = 200 ): array {

		if ( count( $response ) > 0 ) {
			$response = self::snake_to_camel_recursion( $response );
		}

		return array(
			'message' => 'success',
			'data'    => array(
				'status'   => $status,
				'response' => $response,
			),
		);
	}

	/**
	 * Verifies the current request. It should verify user device, user IP, ..etc
	 *
	 * @param $request
	 */
	public static function verify_request( $request, $user ) {

		try {
			$request_ip        = $request->get_header( 'x_real_ip' );
			$request_os        = $request->get_header( 'sec_ch_ua_platform' );
			$request_browser   = $request->get_header( 'sec_ch_ua' );
			$request_useragent = $request->get_header( 'user_agent' );
			$request_host      = $request->get_header( 'host' );

			return true; // Returns true always by default.
		} catch ( Exception $e ) {
			new Logger( __CLASS__, $e, 'verify_request' );
		}

	}

	/**
	 * This verifies if the current visitor can access the current endpoint or not.
	 *
	 * @param $request
	 */
	public static function verify_access( $request, $class ) {

		try {

			$public_access      = $class::$public_access ?? self::$public_access;
			$same_domain_access = $class::$same_domain_access ?? self::$same_domain_access;
			$allowed_domains    = $class::$allowed_domains ?? self::$allowed_domains;
			$allowed_user_roles = $class::$allowed_user_roles ?? self::$allowed_user_roles;
			$self_request       = false; // Assume it is not a request from the same user to itself.
			$admin_request      = false; // Assume the admin is not requesting this user.
			$current_user       = false;
			$requested_user     = false;

			if ( $same_domain_access ) {
				$allowed_domains = array_merge( $allowed_domains, array( wp_parse_url( site_url() )['host'] ) );
			}

			if ( $public_access ) {
				return true; // If it is open to the public, then just return true.
			}

			// Check if referer exists.
			$request_referer = $request->get_header( 'referer' ) ?? $_SERVER['HTTP_ORIGIN'] ?? null;

			if ( ! $request_referer ) {

				// Check if cookie exists.
				$request_cookie = $request->get_header( 'cookie' );

				if ( ! $request_cookie ) {
					// User is not logged-in, and it is not open to the public >> return FALSE
					return false;
				}

				$user_id = wp_validate_auth_cookie( '', 'logged_in' );
				if ( ! $user_id ) {
					// This cookie is suspicious! >> return FALSE
					return false;
				}

				$requested_user = Helper::request_to_user( $request );
				// Treat it as it is coming from the current host (self request on the browser).
				$request_referer = $request->get_header( 'host' );
			}

			$parsed_url     = wp_parse_url( $request_referer );
			$referer_domain = $parsed_url['host'] ?? $parsed_url['path'];

			if ( ! in_array( $referer_domain, $allowed_domains, true ) ) {
				return false; // Domain is not allowed >> return FALSE
			}

			$authorize_cookie_string = Authorize::authorize_cookie_string( $request );

			// Is there a cookie_string added as a get parameter for local access?
			$cookie_string = $request->get_param( 'cookie_string' );
			if ( $cookie_string ) {

				$requested_user = Helper::cookie_to_user( $cookie_string );

				if ( ! $requested_user ) {
					return false; // ALERT! The cookie added as get parameter is a bad/stale cookie.
				}
				// This request is coming from localhost.
				if ( $referer_domain === 'localhost' ) {
					$self_request = true;
				}
			}

			$current_user = Helper::request_to_user( $request );

			// Check if the requested_user is the same as the current user.
			if ( $current_user && $requested_user->ID === $current_user->ID ) {
				$self_request = true;
			}

			// Check if the current user has the ability to view the requested user.
			$intersect_current = array_intersect( $current_user->roles, $allowed_user_roles );
			if ( $intersect_current && count( $intersect_current ) > 0 ) {
				$admin_request = true;
				// Is there an id get parameter added?
				$user_id = $request->get_param( 'user_id' );
				if ( $user_id ) {
					$requested_user = get_user_by( 'id', $user_id );
				}
			}

			if ( $self_request || $admin_request ) {

				// Verify if the current user's request is valid.
				// Maybe they are only allowed to use a single IP per session?
				self::verify_request( $request, $requested_user );

				return array(
					'current_user'   => $current_user,
					'requested_user' => $requested_user,
					'admin_request'  => $admin_request,
					'self_request'   => $self_request,
				);
			}

			return false;
		} catch ( Exception $e ) {
			new Logger( __CLASS__, $e, 'verify_access' );
		}
	}


	/**
	 * This verifies if the request's domain is part of the list of approved domains.
	 *
	 * @param $request
	 * @param $class
	 *
	 * @return bool
	 */
	public static function verify_domain_access( $request, $class ): bool {
		$request_referer = $request->get_header( 'referer' ) ?? $_SERVER['HTTP_ORIGIN'] ?? null;
		if ( ! $request_referer ) {
			return false;
		}

		$same_domain_access = $class::$same_domain_access ?? self::$same_domain_access;
		$allowed_domains    = $class::$allowed_domains ?? self::$allowed_domains;
		$parsed_url         = wp_parse_url( $request_referer );
		$referer_domain     = $parsed_url['host'] ?? $parsed_url['path'];

		if ( $same_domain_access ) {
			$allowed_domains = array_merge( $allowed_domains, array( wp_parse_url( site_url() )['host'] ) );
		}

		if ( ! in_array( $referer_domain, $allowed_domains, true ) ) {
			return false; // Domain is not allowed >> return FALSE
		}

		return true;
	}

	/**
	 * Transforms the key from snake to camelcase for more friendly JSON format.
	 *
	 * @param $array
	 *
	 * @return array
	 */
	public static function snake_to_camel_recursion( $array ): array {
		$new_array = array();
		foreach ( $array as $key => $value ) {
			$new_key = lcfirst( str_replace( ' ', '', ucwords( str_replace( '_', ' ', $key ) ) ) );
			if ( is_array( $value ) ) {
				$new_array[ $new_key ] = self::snake_to_camel_recursion( $value );
			} else {
				$new_array[ $new_key ] = $value;
			}
		}

		return $new_array;
	}

}
