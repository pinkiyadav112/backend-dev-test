<?php
/**
 *
 * Register all related ACF functionality here.
 *
 * @author     Kings Of The Web
 * @year       2022
 * @package    store.kotwrest.com
 * @subpackage kotwrest
 */

namespace KotwRest\ACF;

class OptionsPages {

	public function __construct() {
		if ( function_exists( 'acf_add_options_page' ) ) {
			add_action( 'acf/init', array( $this, 'site_options' ) );
		}
	}


	/**
	 * Register an ACF option page.
	 *
	 * @option_page Site General Settings
	 * @return void
	 */
	public function site_options(): void {

		// Add parent.
		$parent = acf_add_options_page(
			array(
				'page_title'      => 'Site Options',
				'menu_title'      => 'Site Options',
				'menu_slug'       => 'theme_options',
				'capability'      => 'edit_posts',
				'position'        => '',
				'parent_slug'     => '',
				'icon_url'        => '',
				'redirect'        => true,
				'post_id'         => 'options',
				'autoload'        => false,
				'update_button'   => 'Update',
				'updated_message' => 'Options Updated',
			)
		);

		$child = acf_add_options_page(
			array(
				'page_title'  => __( 'Site Options' ),
				'menu_title'  => __( 'Site Options' ),
				'parent_slug' => $parent['menu_slug'],
			)
		);
	}
}
