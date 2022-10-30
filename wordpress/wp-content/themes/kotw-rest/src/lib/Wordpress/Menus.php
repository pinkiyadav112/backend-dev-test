<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    mazencanada.com
 * @subpackage KotwRest\Wordpress
 */

namespace KotwRest\Wordpress;

class Menus {

	public static array $menus;

	public function __construct() {
		self::$menus = array(
			'primary-header' => esc_html__( 'Primary Header', 'kotwrest' ),
			'primary-footer' => esc_html__( 'Primary Footer', 'kotwrest' ),
		);

		add_action( 'init', array( __CLASS__, 'register_menus' ) );

	}

	/**
	 * This registers the menus by calling the init hook.
	 * @return void
	 */
	public static function register_menus() {
		register_nav_menus( self::$menus );
	}

}
