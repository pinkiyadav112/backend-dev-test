<?php
/**
 * Returns true or false based on the auth token provided in the headers.
 *
 * @author     Kings Of The Web
 * @endpoint   /wp-json/kotwrest/user/is-logged
 **
 */

namespace KotwRest\Endpoints\User;

use kotw\Rest\Authorize;
use kotw\Rest\Endpoint;
use WP_REST_Request as WP_REST_Request;

class IsLogged extends Endpoint {

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
	 * This initializes the endpoint's data.
	 * @return array
	 */
	public static function init(): array {
		parent::init();
		self::$public_access      = true;
		self::$same_domain_access = true;
		self::$allowed_user_roles = array( 'administrator', 'developer' );
		self::$allowed_domains    = array( 'localhost' );

		return array(
			'kotwrest',
			'user/is-logged',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'callback' ),
				'permission_callback' => array( __CLASS__, 'permission_callback' ),
			),
		);
	}


	/**
	 *  The main callback for this endpoint.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return array
	 */
	public static function callback( WP_REST_Request $request ) {

		return self::handle_success(
			array(
				'is_logged' => true,
			)
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
