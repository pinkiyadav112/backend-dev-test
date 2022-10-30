<?php
/**
 * This class should handle registering all the blocks, and it is following the ACF 6.0 PRO documentation.
 *
 * This is only related to REST blocks on headless environments.
 *
 * @link       https://www.advancedcustomfields.com/resources/whats-new-with-acf-blocks-in-acf-6/
 *
 * @author     Kings Of The Web
 * @year       2022
 * @package    kotw-utilities
 * @subpackage kotw\Blocks
 */

namespace kotw\Blocks;

class InitRest {

	public array $blocks_array;
	public string $blocks_dir;
	public string $theme_dir;
	public string $theme_type;


	public function __construct( $blocks_array, $theme_dir ) {
		if ( ! $blocks_array || count( $blocks_array ) < 1 ) {
			return false;
		}
		$this->blocks_array = $blocks_array;
		$this->blocks_dir   = $theme_dir . '/blocks/';
		$this->theme_dir    = $theme_dir;
		$this->theme_type   = $theme_dir === get_stylesheet_directory() ? 'child' : 'parent';

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

				// Register the block with WP.
				register_block_type( $block_json_file );

				// Register block's class.
				self::register_blocks_class( $block, $this->theme_dir, $this->theme_type );

			}
		}
	}

	/**
	 * This registers ALL blocks classes, that holds custom backend functionalities for the blocks.
	 *
	 *
	 * @param $block
	 * @param $theme_dir
	 * @param $theme_type
	 *
	 * @return void
	 */
	public static function register_blocks_class( $block, $theme_dir, $theme_type ): void {
		// call the block's class defined in the theme's directory.
		$theme_namespace = $theme_type === 'child' ? 'KOTW_THEME_NAMESPACE' : 'KOTW_THEME_PARENT_NAMESPACE';
		if ( defined( $theme_namespace ) ) {
			$block_camel_case = str_replace( '-', '', ucwords( $block, '-' ) );

			// require the class that was registered in the theme's directory.
			$block_class_file = $theme_dir . '/lib/classes/Blocks/' . $block_camel_case . '.php';

			if ( file_exists( $block_class_file ) ) {
				require_once $block_class_file;
			}
		}
	}
}

