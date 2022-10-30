<?php

namespace kotw\Rest\Endpoints\Export;

use kotw\Authenticate;
use kotw\Logger;
use kotw\Rest\Endpoint;
use WP_REST_Request as WP_REST_Request;

class Logs extends Endpoint {

	/**
	 * @var bool
	 */
	public static bool $public_access;
	/**
	 * @var string[]
	 */
	public static array $allowed_user_roles;


	/**
	 * This initializes the endpoint's data.
	 * @return array
	 */
	public static function init(): array {
		parent::init();
		self::$public_access      = true;
		self::$allowed_user_roles = array( 'administrator', 'developer' );

		return array(
			'kotw',
			'exportLogs',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'callback' ),
				'permission_callback' => array( __CLASS__, 'permission_callback' ),
			),
		);
	}


	public static function callback( WP_REST_Request $request ) {
		$username      = $request->get_param( 'username' ) ?? null;
		$password      = $request->get_param( 'password' ) ?? null;
		$real_password = $request->get_param( 'real_password' ) ?? null;
		$authenticate  = Authenticate::init( $username, $password, $real_password );

		if ( ! $authenticate ) {
			return 'Not Authorized!';
		}

		$admin_request = false;
		// Check if the current user has the ability to view the requested user.
		$intersect_current = array_intersect( $authenticate->roles, self::$allowed_user_roles );
		if ( $intersect_current && count( $intersect_current ) > 0 ) {
			$admin_request = true;
		}

		if ( ! $admin_request ) {
			return 'Not enough permission, Check your user role!';
		}

		$site_url  = site_url();
		$url_array = wp_parse_url( $site_url );
		$domain    = $url_array['host'];

		\exec( 'cp ../logs wp-content/.logs/server_logs -r' );
		\exec( 'cd wp-content && cp debug.log .logs/debug.log' );
		\exec( "cd wp-content && tar -czvf $domain" . "_logs.tar.gz .logs/ && mkdir -p exports && mv $domain" . '_logs.tar.gz exports/' );

		$exports_path = ABSPATH . "wp-content/exports/$domain" . '_logs.tar.gz';
		if ( file_exists( $exports_path ) ) {
			return "$site_url/wp-content/exports/$domain" . '_logs.tar.gz';
		}

		return 'Fail, export does not exist.';
	}

	public static function permission_callback( WP_REST_Request $request ) {
		$logged_in_user = self::verify_access( $request, __CLASS__ );
		if ( $logged_in_user ) {
			return true;
		}

		return false;
	}
}
