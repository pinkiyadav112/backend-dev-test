<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    english.codedegree.com
 * @subpackage codedegree\Blocks
 */

namespace kotw\Blocks;

use Timber\Timber;

class Render {

	/**
	 * This renders the correct twig template for the incoming block.
	 *
	 * @param $args
	 *
	 * @return bool|string
	 */
	public static function template( $args ) {

		if ( ! is_array( $args ) ) {
			return false;
		}
		if ( ! isset( $args['name'] ) ) {
			return false;
		}

		// remove CODEDEGREE_BLOCKS_NAMESPACE and ACF_BLOCKS_NAMESPACE from $args['name'] ONLY if they are prefixes.
		$block_name = $args['name'];
		$namespaces = array( KOTW_BLOCKS_NAMESPACE . '/', ACF_BLOCKS_NAMESPACE . '/' );
		foreach ( $namespaces as $namespace ) {
			if ( strpos( $block_name, $namespace ) === 0 ) {
				// remove it from the beginning of the string.
				$block_name = substr( $block_name, strlen( $namespace ) );
			}
		}

		$context = Timber::context();

		// Pass the block data to the context.
		$context['block'] = array(
			'id'    => $args['id'],
			'title' => $args['title'],
			'data'  => get_fields(),
		);

		Timber::render( "views/blocks/$block_name/template.twig", $context, true );

	}

	/**
	 * This registers the styles and scripts per this block, to be used later in blocks.json using the handle.
	 *
	 * All assets starts with prefix `block-` to avoid conflicts with other assets.
	 *
	 * @param $block_name
	 *
	 * @return void
	 */
	public static function register_assets( $block_name ): void {
		$assets_dir    = get_template_directory_uri() . '/assets/dist/blocks/';
		$theme_version = wp_get_theme()->get( 'Version' );
		add_action(
			'wp_enqueue_scripts',
			function () use ( $block_name, $assets_dir, $theme_version ) {
				wp_register_style(
					$block_name,
					$assets_dir . $block_name . '/index.css',
					array(),
					$theme_version,
					'all'
				);
			}
		);
		add_action(
			'wp_enqueue_scripts',
			function () use ( $block_name, $assets_dir, $theme_version ) {
				wp_register_script(
					$block_name,
					$assets_dir . $block_name . '/index.js',
					array(),
					$theme_version,
					true
				);
			}
		);
	}
}
