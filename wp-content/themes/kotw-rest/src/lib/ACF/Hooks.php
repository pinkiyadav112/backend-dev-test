<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    store.kotwrest.com
 * @subpackage kotwrest\ACF
 */

namespace KotwRest\ACF;

class Hooks {

	public function __construct() {
		// Load custom ACF fields' directories for parent and child themes.
		add_filter( 'acf/settings/load_json', array( $this, 'acf_load_json' ), 9999, 1 );
	}

	/**
	 * Register the ACF fields directories of the parent theme and the child theme, if it exists.
	 */
	public function acf_load_json( $paths ) {
		$parent_theme = get_template_directory() . '/acf-json';
		$child_theme  = get_stylesheet_directory() . '/acf-json';

		if ( $parent_theme ) {
			$paths[] = $parent_theme;
		}

		if ( $child_theme ) {
			$paths[] = $child_theme;
		}

		return $paths;
	}
}
