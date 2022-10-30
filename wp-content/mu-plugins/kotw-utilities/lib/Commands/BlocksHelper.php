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

use kotw\Logger;

class BlocksHelper {
	/**
	 * This creates a js and css directories in the assets' folder inside the current theme.
	 *
	 * @param $block_name
	 * @param $theme_dir
	 *
	 * @return bool
	 */
	public static function create_assets_directories( $block_name, $theme_dir ): bool {
		$js_blocks_dir   = $theme_dir . '/assets/src/js/blocks';
		$scss_blocks_dir = $theme_dir . '/assets/src/scss/blocks';

		// check if the js and scss directory exist, if not, create them.
		if ( ! file_exists( $js_blocks_dir ) ) {
			mkdir( $js_blocks_dir, 0777, true );
		}
		if ( ! file_exists( $scss_blocks_dir ) ) {
			mkdir( $scss_blocks_dir, 0777, true );
		}

		// create new directories with the block name.
		$js_block_dir   = $js_blocks_dir . '/' . $block_name;
		$scss_block_dir = $scss_blocks_dir . '/' . $block_name;

		// create new scss file inside the scss directory, and content to it.
		$scss_file = $scss_block_dir . '/index.scss';
		if ( ! file_exists( $scss_file ) ) {
			mkdir( $scss_block_dir, 0777, true );
			touch( $scss_file );
			$scss_content  = "@import '../../global/helper';\n";
			$scss_content .= 'section.kotw-block.block-' . $block_name . " {\n\n}";
			file_put_contents( $scss_file, $scss_content );
		}

		// create new js file inside the js directory, and content to it.
		$js_file = $js_block_dir . '/index.js';
		if ( ! file_exists( $js_file ) ) {
			mkdir( $js_block_dir, 0777, true );
			touch( $js_file );
			$js_content  = "//Import CSS\n";
			$js_content .= "import '../../../scss/blocks/$block_name/index.scss';\n\n\n\n\n\n\n\n";
			//$js_content .= window.addEventListener('DOMContentLoaded', () => {
			//
			//});

			$js_content .= "//Always load the JS, after the dom has been loaded.\n";
			$js_content .= "window.addEventListener('DOMContentLoaded', () => {\n";
			$js_content .= "\t// Your code here\n";
			$js_content .= "\t// Make sure to only call the block using the class '.block-$block_name', \n\t//to handle Multiple instances of the block on teh same page.\n";
			$js_content .= '});';

			file_put_contents( $js_file, $js_content );
		}

		// Check if any error  happened.
		if ( ! file_exists( $js_file ) || ! file_exists( $scss_file ) ) {
			return false;
		}

		// git add the two files.
		$git_add = "git add $js_file $scss_file";
		exec( $git_add );

		return true;

	}

	/**
	 * This creates twig template in the correct directory.
	 *
	 * @param $block_name
	 * @param $block_title
	 * @param $block_description
	 * @param $block_category
	 * @param $theme_dir
	 *
	 * @return bool
	 */
	public static function create_twig_template(
		$block_name,
		$block_title,
		$block_description,
		$block_category,
		$theme_dir
	): bool {
		$twig_blocks_dir = $theme_dir . '/views/blocks';

		// check if the twig directory exist, if not, create them.
		if ( ! file_exists( $twig_blocks_dir ) ) {
			mkdir( $twig_blocks_dir, 0777, true );
		}

		// create new twig file inside the twig directory, and content to it.
		$twig_file = $twig_blocks_dir . '/' . $block_name . '/template.twig';
		if ( ! file_exists( $twig_file ) ) {
			mkdir( $twig_blocks_dir . '/' . $block_name, 0777, true );
			touch( $twig_file );
			$twig_content  = '{#' . "\n";
			$twig_content .= "\t" . "Block Name: $block_name" . "\n";
			$twig_content .= "\t" . "Block Description: $block_description" . "\n";
			$twig_content .= "\t" . "Block Title: $block_title" . "\n";
			$twig_content .= "\t" . "Block Category: $block_category" . "\n";
			$twig_content .= "\t" . "Styles: assets/scss/blocks/$block_name/index.scss" . "\n";
			$twig_content .= "\t" . "Scripts: assets/js/blocks/$block_name/index.js" . "\n";
			$twig_content .= '#}';
			$twig_content .= "\n";
			$twig_content .= "<pre>{{dump(block)}}</pre>\n";
			$twig_content .= '<section id = "{{block.id}}" class="kotw-block block-' . $block_name . '"></section>';
			file_put_contents( $twig_file, $twig_content );
		}

		// check if any error happened.
		if ( ! file_exists( $twig_file ) ) {
			return false;
		}

		// git add the file.
		$git_add = "git add $twig_file";
		exec( $git_add );

		return true;
	}

	/**
	 * This creates the JSON file that will be used to initialize the block.
	 *
	 *
	 * @param $block_name
	 * @param $block_title
	 * @param $block_description
	 * @param $block_category
	 * @param $theme_dir
	 *
	 * @return bool
	 */
	public static function create_the_json_file(
		$block_name,
		$block_title,
		$block_description,
		$block_category,
		$theme_dir
	): bool {
		$blocks_dir = $theme_dir . '/blocks';

		// check if the block's directory exist, if not, create them.
		if ( ! file_exists( $blocks_dir ) ) {
			mkdir( $blocks_dir, 0777, true );
		}

		// create $block_name dir.
		$block_dir = $blocks_dir . '/' . $block_name;
		if ( ! file_exists( $block_dir ) ) {
			mkdir( $block_dir, 0777, true );
		}

		// create new file block.json inside the $block_name directory, and content to it.
		$block_json_file = $block_dir . '/block.json';

		if ( ! file_exists( $block_json_file ) ) {
			touch( $block_json_file );
			$args_array = array(
				'name'         => $block_category . '/' . $block_name,
				'title'        => $block_title,
				'description'  => $block_description,
				'category'     => $block_category,
				'icon'         => 'admin-comments',
				'script'       => $block_name,
				'style'        => $block_name,
				'editorScript' => $block_name,
				'editorStyle'  => $block_name,
				'keywords'     => array( $block_category, 'block', $block_name ),
				'apiVersion'   => 2,
				'acf'          => array(
					'mode'           => 'auto',
					'renderCallback' => 'kotw\Blocks\Render::template',
					'postTypes'      => array(
						'post',
						'page',
					),
				),

			);
			//encode the array into json.
			$json = json_encode( $args_array, JSON_PRETTY_PRINT );
			file_put_contents( $block_json_file, $json );

			// check if any error.
			if ( ! file_exists( $block_json_file ) ) {
				return false;
			}

			// git add the file.
			$git_add = "git add $block_json_file";
			exec( $git_add );

			return true;

		}

		return true;

	}


	/**
	 * This creates the JSON file that will be used to initialize the block.
	 *
	 *
	 * @param $block_name
	 * @param $block_title
	 * @param $block_description
	 * @param $block_category
	 * @param $theme_dir
	 *
	 * @return bool
	 */
	public static function create_the_json_file_rest(
		$block_name,
		$block_title,
		$block_description,
		$block_category,
		$theme_dir
	): bool {
		$blocks_dir = $theme_dir . '/blocks';

		// check if the block's directory exist, if not, create them.
		if ( ! file_exists( $blocks_dir ) ) {
			mkdir( $blocks_dir, 0777, true );
		}

		// create $block_name dir.
		$block_dir = $blocks_dir . '/' . $block_name;
		if ( ! file_exists( $block_dir ) ) {
			mkdir( $block_dir, 0777, true );
		}

		// create new file block.json inside the $block_name directory, and content to it.
		$block_json_file = $block_dir . '/block.json';

		if ( ! file_exists( $block_json_file ) ) {
			touch( $block_json_file );
			$args_array = array(
				'name'        => $block_category . '/' . $block_name,
				'title'       => $block_title,
				'description' => $block_description,
				'category'    => $block_category,
				'icon'        => 'admin-comments',
				'keywords'    => array( $block_category, 'block', $block_name ),
				'apiVersion'  => 2,
				'acf'         => array(
					'mode'           => 'edit',
					'renderCallback' => 'kotw\Blocks\RenderRest::template',
					'postTypes'      => array(
						'post',
						'page',
					),
				),

			);
			//encode the array into json.
			$json = json_encode( $args_array, JSON_PRETTY_PRINT );
			file_put_contents( $block_json_file, $json );

			// check if any error.
			if ( ! file_exists( $block_json_file ) ) {
				return false;
			}

			// git add the file.
			$git_add = "git add $block_json_file";
			exec( $git_add );

			return true;

		}

		return true;

	}

	/**
	 * This should create the block's ACF group.
	 *
	 * @param $block_name
	 * @param $block_title
	 * @param $theme_dir
	 *
	 * @return false
	 */
	public static function create_acf_group( $block_name, $block_title, $theme_dir ): bool {

		$acf_key   = 'group_' . substr( md5( $block_name ), 0, 13 );
		$acf_array = array(
			'key'                   => $acf_key,
			'title'                 => 'Block - ' . $block_title,
			'fields'                => array(),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'kotw/' . $block_name,
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'left',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
			'acfe_display_title'    => '',
			'acfe_autosync'         => array(
				'json',
			),
			'acfe_form'             => 0,
			'acfe_meta'             => '',
			'acfe_note'             => '',
		);

		$acf_array_json = json_encode( $acf_array, JSON_PRETTY_PRINT );
		$acf_file       = $theme_dir . '/acf-json/' . $acf_key . '.json';

		// create acf file.
		if ( ! file_exists( $acf_file ) ) {
			touch( $acf_file );
			file_put_contents( $acf_file, $acf_array_json );
		}

		// check if any error.
		if ( ! file_exists( $acf_file ) ) {
			return false;
		}

		// git add the file.
		$git_add = "git add $acf_file";
		exec( $git_add );

		return true;

	}


	/**
	 * This should create the block's php class in the theme's directory.
	 *
	 * @param $block_name
	 * @param $theme_dir
	 *
	 * @return bool
	 */
	public static function create_php_class( $block_name, $theme_dir ): bool {

		// Create the block's class to be defined in the theme's directory.
		$theme_type      = $theme_dir === get_template_directory() ? 'parent' : 'child';
		$theme_namespace = $theme_type === 'child' ? 'KOTW_THEME_NAMESPACE' : 'KOTW_THEME_PARENT_NAMESPACE';
		if ( defined( $theme_namespace ) ) {
			$block_camel_case = str_replace( '-', '', ucwords( $block_name, '-' ) );

			// require the class that was registered in the theme's directory.
			$block_class_file = $theme_dir . '/lib/classes/Timber/Context/Blocks/' . $block_camel_case . '.php';

			if ( ! file_exists( $block_class_file ) ) {
				// create the class's content.
				$block_class_content  = '<?php' . PHP_EOL;
				$block_class_content .= 'namespace ' . KOTW_THEME_NAMESPACE . '\Timber\Blocks;' . PHP_EOL . PHP_EOL;
				$block_class_content .= 'class ' . $block_camel_case . ' {' . PHP_EOL . PHP_EOL;
				$block_class_content .= "\tpublic string \$block_name;" . PHP_EOL . PHP_EOL;
				$block_class_content .= "\tpublic function __construct() {" . PHP_EOL;
				$block_class_content .= "\t\t\$this->block_name = '" . $block_name . "';" . PHP_EOL;
				$block_class_content .= "\t\tadd_filter( 'timber/context', array( \$this, 'context' ), 10, 1 );" . PHP_EOL . PHP_EOL;
				$block_class_content .= "\t\t// Add ajax hooks related to this block here." . PHP_EOL . PHP_EOL;
				$block_class_content .= "\t}" . PHP_EOL . PHP_EOL;
				$block_class_content .= "\t/**" . PHP_EOL;
				$block_class_content .= "\t * Custom context for " . $block_camel_case . ' block.' . PHP_EOL;
				$block_class_content .= "\t *" . PHP_EOL;
				$block_class_content .= "\t * @return void" . PHP_EOL;
				$block_class_content .= "\t */" . PHP_EOL;
				$block_class_content .= "\tpublic function context( \$context ) {" . PHP_EOL . PHP_EOL;
				$block_class_content .= "\t\treturn \$context;" . PHP_EOL;
				$block_class_content .= "\t}" . PHP_EOL;
				$block_class_content .= '}' . PHP_EOL . PHP_EOL;
				$block_class_content .= 'new ' . $block_camel_case . '();' . PHP_EOL;

				// create the file.
				touch( $block_class_file );
				file_put_contents( $block_class_file, $block_class_content );

			}

			// check if any error.
			if ( ! file_exists( $block_class_file ) ) {
				return false;
			}

			// git add the file.
			$git_add = "git add $block_class_file";
			exec( $git_add );

			return true;
		}

		return false;
	}


	/**
	 * This should create the block's php class in the theme's directory, for the REST block.
	 *
	 * @param $block_name
	 * @param $theme_dir
	 *
	 * @return bool
	 */
	public static function create_php_class_rest( $block_name, $theme_dir ): bool {

		// Create the block's class to be defined in the theme's directory.
		$theme_type      = $theme_dir === get_template_directory() ? 'parent' : 'child';
		$theme_namespace = $theme_type === 'child' ? 'KOTW_THEME_NAMESPACE' : 'KOTW_THEME_PARENT_NAMESPACE';

		if ( defined( $theme_namespace ) ) {
			$block_camel_case = str_replace( '-', '', ucwords( $block_name, '-' ) );

			// require the class that was registered in the theme's directory.
			$block_class_file = $theme_dir . '/src/lib/Blocks/' . $block_camel_case . '.php';

			if ( ! file_exists( $block_class_file ) ) {
				// create the class's content.
				$namespace            = $theme_type === 'child' ? KOTW_THEME_NAMESPACE : KOTW_THEME_PARENT_NAMESPACE;
				$block_class_content  = '<?php' . PHP_EOL;
				$block_class_content .= 'namespace ' . $namespace . '\Blocks;' . PHP_EOL . PHP_EOL;
				$block_class_content .= 'class ' . $block_camel_case . ' {' . PHP_EOL . PHP_EOL;
				$block_class_content .= "\tpublic string \$block_name;" . PHP_EOL . PHP_EOL;
				$block_class_content .= "\tpublic function __construct() {" . PHP_EOL;
				$block_class_content .= "\t\t\$this->block_name = '" . $block_name . "';" . PHP_EOL;
				$block_class_content .= "\t}" . PHP_EOL . PHP_EOL;
				$block_class_content .= '}' . PHP_EOL . PHP_EOL;
				$block_class_content .= 'new ' . $block_camel_case . '();' . PHP_EOL;

				// create the file.
				touch( $block_class_file );
				file_put_contents( $block_class_file, $block_class_content );

			}

			// check if any error.
			if ( ! file_exists( $block_class_file ) ) {
				return false;
			}

			// git add the file.
			$git_add = "git add $block_class_file";
			exec( $git_add );

			return true;
		}

		return false;
	}
}
