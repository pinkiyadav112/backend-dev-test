<?php

namespace kotw\Rest\Endpoints;

use kotw\Logger;
use kotw\Rest\Endpoint;
use WP_REST_Request as WP_REST_Request;

class BoilerPlate extends Endpoint {

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
	 * This initializes the endpoint's data.
	 * @return array
	 */
	public static function init(): array {
		parent::init();
		self::$public_access = true;
		//self::$allowed_user_roles = array();
		//self::$allowed_domains    = array();

		return array(
			'kotw',
			'boilerplate',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'callback' ),
				'permission_callback' => array( __CLASS__, 'permission_callback' ),
			),
		);
	}


	public static function callback( WP_REST_Request $request ) {

		return array(
			'name' => 'Boilerplate',
		);
	}

	public static function permission_callback( WP_REST_Request $request ) {
		$logged_in_user = self::verify_access( $request, __CLASS__ );
		if ( $logged_in_user ) {
			return true;
		}

		return false;
	}
}
