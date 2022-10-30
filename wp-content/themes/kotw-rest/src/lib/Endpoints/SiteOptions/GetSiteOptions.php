<?php
/**
 * @author     Kings Of The Web
 * @endpoint   /wp-json/kotwrest/get-site-options/<group>
 *
 * @params     language GET string (optional) - sets the languages for the current request.
 *
 */

namespace KotwRest\Endpoints\SiteOptions;

use kotw\Rest\Endpoint;
use WP_REST_Request as WP_REST_Request;
use WP_REST_Response;

class GetSiteOptions extends Endpoint {

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
			'get-site-options/(?P<group>[a-zA-Z0-9-]+)',
			array(
				'methods'             => 'GET',
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
	 * @return array|WP_REST_Response
	 */
	public static function callback( WP_REST_Request $request ) {

		$group_name = $request->get_param( 'group' );

		// Cookies should always be provided.
		if ( ! $group_name ) {
			return self::handle_error( 'No page ID was provided.', 400 );
		}

		// Grab all acf fields in options page with the provided ID.
		$acf_fields = get_fields( 'options_' . $group_name );

		// If no fields were found, return an error.
		if ( $acf_fields ) {
			return self::handle_success( $acf_fields );
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
			return true;
		}

		return false;
	}
}
