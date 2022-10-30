<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    mazencanada.com
 * @subpackage KotwRest\Endpoints
 */

namespace KotwRest\Endpoints;

use kotw\Rest\Register as RegisterEndpoints;
use kotwrest\Endpoints\Available as AvailableEndpoints;
use KotwRest\Endpoints\SiteOptions\GetSiteOptions as GetSiteOptionsEndpoint;
use KotwRest\Endpoints\Menus\GetMenus as GetMenusEndpoint;
use KotwRest\Endpoints\User\Login as LoginEndpoint;
use KotwRest\Endpoints\User\Logout as LogoutEndpoint;
use KotwRest\Endpoints\User\IsLogged as IsLoggedEndpoint;
use KotwRest\Endpoints\User\Register as RegisterEndpoint;
use KotwRest\Endpoints\User\Profile as GetUserProfileEndpoint;
use KotwRest\Endpoints\Pages\GetPage as GetPageEndpoint;
use KotwRest\Endpoints\Books\GetBook as GetBookEndpoint;

class Init {

	public function __construct() {
		self::register_global_endpoints();
		self::register_user_endpoints();
		self::register_content_endpoints();
		self::register_services_endpoints();
	}

	/**
	 * This registers all settings endpoints, for example:
	 *
	 *  - /wp-json/kotwrest/get-site-options/header
	 *
	 * @return void
	 */
	public static function register_global_endpoints(): void {
		new RegisterEndpoints(
			array(
				AvailableEndpoints::init(),
				GetSiteOptionsEndpoint::init(),
			)
		);

	}

	/**
	 * This registers all user endpoints, for example:
	 * - /wp-json/kotwrest/user/profile/<user_id>
	 * - /wp-json/kotwrest/user/login
	 *
	 * @return void
	 */
	public static function register_user_endpoints(): void {
		new RegisterEndpoints(
			array(
				GetUserProfileEndpoint::init(),
				LoginEndpoint::init(),
				LogoutEndpoint::init(),
				IsLoggedEndpoint::init(),
				RegisterEndpoint::init(),
			)
		);
	}

	/**
	 * All endpoints related to content, for example:
	 *
	 * - /wp-json/kotwrest/page/get-page/<slug>
	 *
	 * @return void
	 */
	public static function register_content_endpoints(): void {
		new RegisterEndpoints(
			array(
				GetMenusEndpoint::init(),
				GetPageEndpoint::init(),
				GetUserProfileEndpoint::init(),
				GetBookEndpoint::init(),
			)
		);
	}

	/**
	 * All endpoints related to 3rd party services, for example:
	 *
	 * - /wp-json/kotwrest/services/get-youtube-videos
	 * @return void
	 */
	public static function register_services_endpoints(): void {
		new RegisterEndpoints(
			array()
		);
	}


}

