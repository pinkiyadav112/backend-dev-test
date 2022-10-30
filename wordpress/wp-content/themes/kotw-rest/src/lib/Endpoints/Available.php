<?php
/**
 * This endpoint retrieves available endpoints, you need to be logged in to view this endpoint.
 *
 * @author     Kings Of The Web
 * @endpoint   /wp-json/kotwreset/get-menus
 *
 */

namespace KotwRest\Endpoints;

use kotw\Rest\Authorize;
use kotw\Rest\Endpoint;
use WP_REST_Request as WP_REST_Request;
use WP_REST_Response;

class Available extends Endpoint {

	public static bool $public_access;
	/**
	 * @var string[]
	 */
	public static array $allowed_user_roles;
	/**
	 * @var string[]
	 */
	public static array $allowed_domains;

	/**
	 * @var bool
	 */
	public static bool $same_domain_access;
	/**
	 * @var string|void
	 */
	public static $site_url;

	/**
	 * This initializes the endpoint's data.
	 * @return array
	 */
	public static function init(): array {
		parent::init();
		self::$public_access      = true;
		self::$same_domain_access = true;
		self::$allowed_user_roles = array( 'administrator', 'developer', 'subscriber' );
		self::$allowed_domains    = array();
		self::$site_url           = site_url();

		return array(
			'kotwrest',
			'available',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'callback' ),
				'permission_callback' => array( __CLASS__, 'permission_callback' ),
			),
		);
	}


	/**
	 *  The main callback for this endpoint, that should return an array of menus
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|array
	 */
	public static function callback( WP_REST_Request $request ) {

		// get all available endpoints under the kotwrest namespace.
		$endpoints = rest_get_server()->get_routes();
		$available = array();
		$rest_url  = get_rest_url();
		$rest_url  = rtrim( $rest_url, '/' );

		foreach ( $endpoints as $endpoint => $data ) {
			if ( strpos( $endpoint, 'kotwrest' ) !== false ) {
				// remove the first sla
				$available[] = $rest_url . $endpoint;
			}
		}

		if ( count( $available ) > 0 ) {
			// remove the first item (the route itself)
			array_shift( $available );

			return self::handle_success( $available );
		}

		return self::handle_error(
			'Something wrong happened.',
			400
		);

	}


	/**
	 * This function is called before the callback, and it validates the request.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return bool
	 */
	public static function permission_callback( WP_REST_Request $request ): bool {
		// Returns a user, if current it is a valid request.
		$user_array = self::verify_access( $request, __CLASS__ );
		if ( $user_array ) {
			return Authorize::user_token( $request->get_headers() );
		}

		return false;
	}
}
