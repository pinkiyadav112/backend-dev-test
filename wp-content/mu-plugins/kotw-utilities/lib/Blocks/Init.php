<?php
/**
 * This class should handle registerting all the blocks, and it is following the ACF 6.0 PRO documentation.
 *
 * @link       https://www.advancedcustomfields.com/resources/whats-new-with-acf-blocks-in-acf-6/
 *
 * @author     Kings Of The Web
 * @year       2022
 * @package    kotw-utilities
 * @subpackage kotw\Blocks
 */

namespace kotw\Blocks;

class Init {

	public array $blocks_array;
	public string $blocks_dir;

	public function __construct( $blocks_array ) {
		if ( ! $blocks_array || count( $blocks_array ) < 1 ) {
			return false;
		}
		$this->blocks_array = $blocks_array;
		$this->blocks_dir   = get_template_directory() . '/blocks/';

		// This has to be registered early, and before ACF.
		add_action( 'init', array( $this, 'register_blocks' ), 5 );
	}

	/**
	 * Register all blocks added to the active theme here.
	 *
	 * @return void
	 */
	public function register_blocks(): void {
		foreach ( $this->blocks_array as $block ) {
			$block_json_file = $this->blocks_dir . $block . '/block.json';
			if ( file_exists( $block_json_file ) ) {

				// Register the assets for this block.
				Render::register_assets( $block );

				// Register the block with WP.
				register_block_type( $block_json_file );

				// Register block's class.
				self::register_blocks_class( $block );

			}
		}
	}

	/**
	 * This registers ALL blocks classes, that holds custom backend functionalities for the blocks.
	 *
	 * @return void
	 */
	public static function register_blocks_class( $block ) {
		// call the block's class defined in the theme's directory.
		if ( defined( 'KOTW_THEME_NAMESPACE' ) ) {
			$block_camel_case = str_replace( '-', '', ucwords( $block, '-' ) );

			// require the class that was registered in the theme's directory.
			$block_class_file = get_template_directory() . '/lib/classes/Timber/Context/Blocks/' . $block_camel_case . '.php';

			if ( file_exists( $block_class_file ) ) {
				require_once $block_class_file;
			}
		}
	}
}
