<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    mazencanada.com
 * @subpackage kotwrest\Wordpress\Blocks
 */

namespace KotwRest\Blocks;

use kotw\Blocks\InitRest as InitBlocks;


class Init {

	public function __construct() {
		self::register();
	}

	/**
	 * Get all available blocks in the theme's directory and registers them.
	 *
	 * @return void
	 */
	public static function register(): void {
		$theme_dir = get_template_directory();

		// Get all first level directories in the blocks' folder.
		$blocks = glob( $theme_dir . '/blocks/*', GLOB_ONLYDIR );

		$blocks_array = array();
		foreach ( $blocks as $block ) {
			$blocks_array[] = basename( $block );
		}

		// Init the blocks, with the theme's available blocks.
		new InitBlocks( $blocks_array, $theme_dir );

	}
}
