<?php
/**
 * This class handles the wpcli command for creating a new Gutenberg/acf block, in the active theme's directory.
 *
 * @author     Kings Of The Web
 * @year       2022
 * @package    english.codedegree.com
 * @subpackage kotw\commands
 */

namespace kotw\commands;

use \WP_CLI as WP_CLI;

class Blocks {
	/**
	 *
	 * ## OPTIONS
	 *
	 *
	 * [--name=<name>]
	 * : The name of the block.
	 *
	 * [--description=<description>]
	 * : The description of the block.
	 *
	 * [--theme=<theme>]
	 * : The theme to create the block in, by default it is set to the parent theme (this can be the child theme instead)
	 *
	 *
	 * @param $args
	 * @param $assoc_args
	 *
	 * @return void
	 *
	 *
	 * @subcommand create-block --name=<name> --description=<description>
	 *
	 * ## Usage
	 *
	 *      wp kotw create-block --name=landing-hero --title="Landing Hero" --category="kotw" --theme=parent
	 */
	public function create_block( $args, $assoc_args ): void {
		$block_name        = $assoc_args['name'];
		$block_description = $assoc_args['description'] ?? '';
		$theme_type        = $assoc_args['theme'] === 'parent' ? 'parent' : 'child';
		$theme_dir         = $theme_type === 'parent' ? get_template_directory() : get_stylesheet_directory();
		$block_category    = 'kotw';

		if ( ! $block_name ) {
			WP_CLI::error( 'Please provide a block name' );
		}

		// get all names of blocks registered in WP, and check if it already exists.
		$registered_blocks = \WP_Block_Type_Registry::get_instance()->get_all_registered();
		$registered_blocks = array_keys( $registered_blocks );
		$registered_blocks = preg_replace( '/(.*)\//', '', $registered_blocks );

		if ( in_array( $block_name, $registered_blocks ) ) {
			WP_CLI::error( 'Block name already exists' );
		}

		$block_title = ucwords( str_replace( '-', ' ', $block_name ) );

		// If description is not provided, use the block title as the description.
		if ( ! $block_description ) {
			$block_description = 'Block ' . $block_title;
		}

		$assets = BlocksHelper::create_assets_directories( $block_name, $theme_dir );
		if ( ! $assets ) {
			WP_CLI::error( 'Could not create assets directories' );
		}
		$twig = BlocksHelper::create_twig_template( $block_name, $block_title, $block_description, $block_category, $theme_dir );
		if ( ! $twig ) {
			WP_CLI::error( 'Could not create twig template' );
		}
		$json_file = BlocksHelper::create_the_json_file( $block_name, $block_title, $block_description, $block_category, $theme_dir );
		if ( ! $json_file ) {
			WP_CLI::error( 'Could not create json file' );
		}
		$acf_file = BlocksHelper::create_acf_group( $block_name, $block_title, $theme_dir );
		if ( ! $acf_file ) {
			WP_CLI::error( 'Could not create acf file' );
		}
		$php_class = BlocksHelper::create_php_class( $block_name, $theme_dir );
		if ( ! $php_class ) {
			WP_CLI::error( 'Could not create php class' );
		}

		WP_CLI::success( $block_name . ' block created successfully\n' );
		$acf_url = admin_url( 'edit.php?post_type=acf-field-group&post_status=sync' );
		WP_CLI::success( "Go to $acf_url, and sync the new Block's ACF group." );
	}

	/**
	 *
	 * This creates a new block, that is used solely for REST. This is used in a headless environment.
	 *
	 * ## OPTIONS
	 *
	 *
	 * [--name=<name>]
	 * : The name of the block.
	 *
	 * [--description=<description>]
	 * : The description of the block.
	 *
	 * [--theme=<theme>]
	 * : The theme to create the block in, by default it is set to the parent theme (this can be the child theme instead)
	 *
	 *
	 * @param $args
	 * @param $assoc_args
	 *
	 * @return void
	 *
	 *
	 * @subcommand create-block-rest --name=<name> --description=<description>
	 *
	 * ## Usage
	 *
	 *      wp kotw create-block-rest --name=landing-hero --title="Landing Hero" --category="kotw" --theme=parent
	 */
	public function create_block_rest( $args, $assoc_args ): void {
		$block_name        = $assoc_args['name'];
		$block_description = $assoc_args['description'] ?? '';
		$theme_type        = $assoc_args['theme'] === 'parent' ? 'parent' : 'child';
		$theme_dir         = $theme_type === 'parent' ? get_template_directory() : get_stylesheet_directory();
		$block_category    = 'kotw';

		if ( ! $block_name ) {
			WP_CLI::error( 'Please provide a block name' );
		}

		// get all names of blocks registered in WP, and check if it already exists.
		$registered_blocks = \WP_Block_Type_Registry::get_instance()->get_all_registered();
		$registered_blocks = array_keys( $registered_blocks );
		$registered_blocks = preg_replace( '/(.*)\//', '', $registered_blocks );

		if ( in_array( $block_name, $registered_blocks ) ) {
			WP_CLI::error( 'Block name already exists' );
		}

		$block_title = ucwords( str_replace( '-', ' ', $block_name ) );

		// If description is not provided, use the block title as the description.
		if ( ! $block_description ) {
			$block_description = 'Block ' . $block_title;
		}

		$json_file = BlocksHelper::create_the_json_file_rest( $block_name, $block_title, $block_description, $block_category, $theme_dir );
		if ( ! $json_file ) {
			WP_CLI::error( 'Could not create json file' );
		}
		$acf_file = BlocksHelper::create_acf_group( $block_name, $block_title, $theme_dir );
		if ( ! $acf_file ) {
			WP_CLI::error( 'Could not create acf file' );
		}
		$php_class = BlocksHelper::create_php_class_rest( $block_name, $theme_dir );
		if ( ! $php_class ) {
			WP_CLI::error( 'Could not create php class' );
		}

		WP_CLI::success( $block_name . ' block created successfully\n' );
		$acf_url = admin_url( 'edit.php?post_type=acf-field-group&post_status=sync' );
		WP_CLI::success( "Go to $acf_url, and sync the new Block's ACF group." );
	}

}
