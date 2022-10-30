<?php
/**
 *
 */

namespace kotw\Blocks;

class Custom {

	public function __construct() {
		// Register the kotw group.
		add_filter( 'block_categories_all', array( $this, 'register_blocks_category' ), 10, 2 );

		// Enqueue the global kotwBlocks JS helper methods and globals.
		add_action( 'wp_enqueue_scripts', array( $this, 'kotw_global_js_functions' ) );
	}

	/**
	 * This registers all customizations for the KOTW blocks category.
	 *
	 * @param $block_categories
	 * @param $editor_context
	 *
	 * @return mixed
	 */
	public function register_blocks_category( $block_categories, $editor_context ) {
		if ( ! empty( $editor_context->post ) ) {
			$block_categories[] = array(
				'slug'  => 'kotw',
				'title' => __( 'Kotw Blocks', 'kotw' ),
				'icon'  => null,
			);
		}

		return $block_categories;
	}

	public function kotw_global_js_functions() {
		wp_enqueue_script(
			'kotw-blocks',
			KotwUtilitiesPluginUrl . '/inc/blocks/block/js/functions.js',
			array(),
			'1.0.0',
			false
		);
		// get all registered blocks with WordPress.
		$blocks = \WP_Block_Type_Registry::get_instance()->get_all_registered();

		// get all blocks under category kotw, and return their names.
		$kotw_blocks = array_filter(
			$blocks,
			function ( $block ) {
				return $block->category === 'kotw';
			}
		);

		// Add this to the global objectk KOTW.blocks
		wp_localize_script(
			'kotw-blocks',
			'KOTW',
			array(
				'blocks' => $kotw_blocks,
			)
		);
	}
}
