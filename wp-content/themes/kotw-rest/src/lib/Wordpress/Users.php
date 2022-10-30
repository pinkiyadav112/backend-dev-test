<?php
/**
 * Custom functions for handling users in WordPress.
 *
 *
 * @author     Kings Of The Web
 * @year       2022
 * @package    redesign.codedegree.com
 * @subpackage KotwRest\Wordpress
 */

namespace KotwRest\Wordpress;

class Users {

	public function __construct() {
		// set the last login time for a user.
		add_action( 'wp_login', array( $this, 'set_last_login' ), 10, 1 );

		// set the last logout time for a user.
		add_action( 'wp_logout', array( $this, 'set_last_logout' ), 10, 1 );
	}

	/**
	 * This sets the last login time for a user.
	 *
	 * @param $user_id
	 *
	 * @return void
	 */
	public static function set_last_login( $user_id ): void {
		$login_info = array(
			'time'  => date( 'Y-m-d H:i:s', time() ),
			'ip'    => $_SERVER['REMOTE_ADDR'],
			'agent' => $_SERVER['HTTP_USER_AGENT'],
		);
		update_user_meta( $user_id, '_kotw_last_login', $login_info );
	}

	/**
	 * This sets the last logout time for a user.
	 *
	 * @param $user_id
	 *
	 * @return void
	 */
	public static function set_last_logout( $user_id ): void {
		$logout_info = array(
			'time'  => date( 'Y-m-d H:i:s', time() ),
			'ip'    => $_SERVER['REMOTE_ADDR'],
			'agent' => $_SERVER['HTTP_USER_AGENT'],
		);
		update_user_meta( $user_id, '_kotw_last_logout', $logout_info );
	}


}
