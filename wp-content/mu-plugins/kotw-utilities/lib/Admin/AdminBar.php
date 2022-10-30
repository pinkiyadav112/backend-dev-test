<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    english.codedegree.com
 * @subpackage kotw\Admin
 */

namespace kotw\Admin;

class AdminBar {

	public function __construct() {
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_menu' ), 999 );
		add_action( 'init', array( $this, 'clear_cache_all' ) );
	}

	/**
	 * Add the main KOTW admin BAR menu
	 *
	 * @return void
	 */
	public function add_admin_bar_menu(): void {
		// add new button.
		global $wp_admin_bar;
		$wp_admin_bar->add_menu(
			array(
				'id'    => 'kotw',
				'title' => 'KOTW',
				'href'  => '#',
			)
		);

		// Add clear cache button.
		$this->add_clear_cache_button();

	}

	/**
	 * This adds the "Clear All Cache" button to Kotw Admin Bar menu.
	 *
	 * @return void
	 */
	public function add_clear_cache_button(): void {

		global $wp_admin_bar;
		$cache_flush_url = add_query_arg(
			array( 'kotw-clear-cache' => 'all' ),
			home_url( $_SERVER['REQUEST_URI'] )
		);
		$wp_admin_bar->add_menu(
			array(
				'id'     => 'kotw-clear-cache',
				'title'  => 'Clear All Cache',
				'parent' => 'kotw',
				'href'   => $cache_flush_url,
			)
		);
	}

	/**
	 * Clear all cache if kotw-clear-cache=all is set in the url.
	 *
	 * @return void
	 */
	public function clear_cache_all(): void {
		if ( ! isset( $_GET['kotw-clear-cache'] ) || ! $_GET['kotw-clear-cache'] == 'all' ) {
			return;
		}

		// Flush rewrite rules.
		flush_rewrite_rules();

		exec( 'wp transient delete --all' );
		exec( 'wp rewrite flush' );
		exec( 'wp cache flush' );
		exec( 'wp timber clear_cache' );

		wp_redirect( remove_query_arg( 'kotw-clear-cache' ) );
		exit;

	}

}
