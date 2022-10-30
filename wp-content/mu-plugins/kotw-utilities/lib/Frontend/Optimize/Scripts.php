<?php

namespace kotw\Frontend\Optimize;

use WP_Error as WP_Error;

defined( 'ABSPATH' ) || die( '' );

class Scripts {
	/**
	 * Holds the configurations for the optimizations
	 *
	 * @var array
	 * @access private
	 */
	private $optimize = array();

	/**
	 * Constructor
	 *
	 * @param array $optimize The optimalizations to conduct
	 */
	public function __construct( array $optimizations = array() ) {

		$defaults = array(
			'defer_CSS'                => false,
			'defer_JS'                 => false,
			'disable_comments'         => false,
			'disable_block_styling'    => false,
			'disable_embed'            => false,
			'disable_emoji'            => true,
			'disable_jquery'           => false,
			'disable_jquery_migrate'   => true,
			'jquery_to_footer'         => true,
			'limit_comments_JS'        => true,
			'remove_comments_style'    => true,
			'remove_wp_assets_version' => true,
		);

		$this->optimize = wp_parse_args( $optimizations, $defaults );
		$this->optimize();

	}

	/**
	 * Hit it! Runs eachs of the functions if enabled
	 */
	private function optimize(): void {
		foreach ( $this->optimize as $key => $value ) {
			if ( $value === true && method_exists( $this, $key ) ) {
				$this->$key();
			}
		}
	}


	/**
	 * Defers all CSS using loadCSS from the Filament Group. Thanks dudes and dudettes!
	 */
	private function defer_CSS(): void {

		// Rewrite our object context
		$object = $this;

		// Dequeue our CSS and save our styles. Please note - this function removes conditional styles for older browsers
		add_action(
			'wp_enqueue_scripts',
			function () use ( $object ) {

				// Bail out if we are uzing the customizer preview
				if ( is_customize_preview() ) {
					return;
				}

				global $wp_styles;

				// Save the queued styles
				foreach ( $wp_styles->queue as $style ) {
					$object->styles[] = $wp_styles->registered[ $style ];
					$dependencies     = $wp_styles->registered[ $style ]->deps;

					if ( ! $dependencies ) {
						continue;
					}

					// Add dependencies, but only if they are not included yet
					foreach ( $dependencies as $dependency ) {
						$object->styles[] = $wp_styles->registered[ $dependency ];
					}
				}

				// Remove duplicate values because of the dependencies
				$object->styles = array_unique( $object->styles, SORT_REGULAR );

				// Dequeue styles and their dependencies except for conditionals
				foreach ( $object->styles as $style ) {
					wp_dequeue_style( $style->handle );
				}

			},
			9999
		);

		// Load our CSS using loadCSS
		add_action(
			'wp_head',
			function () use ( $object ) {

				// Bail out if we are using the customizer preview
				if ( is_customize_preview() ) {
					return;
				}

				$output = '<script>function loadCSS(a,b,c,d){"use strict";var e=window.document.createElement("link"),f=b||window.document.getElementsByTagName("script")[0],g=window.document.styleSheets;return e.rel="stylesheet",e.href=a,e.media="only x",d&&(e.onload=d),f.parentNode.insertBefore(e,f),e.onloadcssdefined=function(b){for(var c,d=0;d<g.length;d++)g[d].href&&g[d].href.indexOf(a)>-1&&(c=!0);c?b():setTimeout(function(){e.onloadcssdefined(b)})},e.onloadcssdefined(function(){e.media=c||"all"}),e}';
				foreach ( $object->styles as $style ) {
					if ( isset( $style->extra['conditional'] ) ) {
						continue;
					}

					// Load local assets
					if ( strpos( $style->src, 'http' ) === false ) {
						$style->src = site_url() . $style->src;
					}
					$output .= 'loadCSS("' . $style->src . '", "", "' . $style->args . '");';
				}
				$output .= '</script>';

				echo $output;

			},
			9999
		);

	}

	/**
	 * Defers all JS
	 */
	private function defer_JS(): void {

		// Defered JS breaks the customizer or the Gutenberg Editor, hence we skip it here
		if ( is_customize_preview() || is_admin() ) {
			return;
		}

		add_filter(
			'script_loader_tag',
			function ( $tag ) {
				return str_replace( ' src', ' defer="defer" src', $tag );
			},
			10,
			1
		);
	}

	/**
	 * Disables block styling
	 */
	private function disable_block_styling(): void {
		add_action(
			'wp_enqueue_scripts',
			function () {
				wp_dequeue_style( 'wp-block-library' );
				wp_dequeue_style( 'wp-block-library-theme' );
				wp_dequeue_style( 'wc-block-style' );
			},
			100
		);
	}

	/**
	 * Disables the support and appearance of comments
	 */
	private function disable_comments(): void {

		// by default, comments are closed.
		if ( is_admin() ) {
			update_option( 'default_comment_status', 'closed' );
		}

		// Closes plugins
		add_filter( 'comments_open', '__return_false', 20, 2 );
		add_filter( 'pings_open', '__return_false', 20, 2 );

		// Disables admin support for post types and menus
		add_action(
			'admin_init',
			function () {

				$post_types = get_post_types();

				foreach ( $post_types as $post_type ) {
					if ( post_type_supports( $post_type, 'comments' ) ) {
						remove_post_type_support( $post_type, 'comments' );
						remove_post_type_support( $post_type, 'trackbacks' );
					}
				}

			}
		);

		// Removes menu in left dashboard meun
		add_action(
			'admin_menu',
			function () {
				remove_menu_page( 'edit-comments.php' );
			}
		);

		// Removes comment menu from admin bar
		add_action(
			'wp_before_admin_bar_render',
			function () {
				global $wp_admin_bar;
				$wp_admin_bar->remove_menu( 'comments' );
			}
		);

	}


	/**
	 * Removes the Embed Javascript and References
	 */
	private function disable_embed(): void {

		add_action(
			'wp_enqueue_scripts',
			function () {
				wp_deregister_script( 'wp-embed' );
			},
			100
		);

		add_action(
			'init',
			function () {

				// Removes the oEmbed JavaScript.
				remove_action( 'wp_head', 'wp_oembed_add_host_js' );

				// Removes the oEmbed discovery links.
				remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

				// Remove the oEmbed route for the REST API epoint.
				remove_action( 'rest_api_init', 'wp_oembed_register_route' );

				// Disables oEmbed auto discovery.
				remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );

				// Turn off oEmbed auto discovery.
				add_filter( 'embed_oembed_discover', '__return_false' );

			}
		);

	}


	/**
	 * Removes WP Emoji
	 */
	private function disable_emoji(): void {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

		/**
		 * Removes Emoji from the TinyMCE Editor
		 *
		 * @param array $plugins The plugins hooked onto the TinyMCE Editor
		 */
		add_filter(
			'tiny_mce_plugins',
			function ( $plugins ) {
				if ( ! is_array( $plugins ) ) {
					return array();
				}

				return array_diff( $plugins, array( 'wpemoji' ) );
			},
			10,
			1
		);
	}


	/**
	 * Deregisters jQuery.
	 */
	private function disable_jquery(): void {
		add_action(
			'wp_enqueue_scripts',
			function () {
				wp_deregister_script( 'jquery' );
			},
			100
		);
	}

	/**
	 * Deregisters jQuery Migrate by removing the dependency.
	 */
	private function disable_jquery_migrate(): void {

		add_filter(
			'wp_default_scripts',
			function ( $scripts ) {
				if ( ! empty( $scripts->registered['jquery'] ) ) {
					$scripts->registered['jquery']->deps = array_diff(
						$scripts->registered['jquery']->deps,
						array( 'jquery-migrate' )
					);
				}
			}
		);

	}


	/**
	 * Puts jquery inside the footer
	 */
	private function jquery_to_footer(): void {
		add_action(
			'wp_enqueue_scripts',
			function () {
				wp_deregister_script( 'jquery' );
				wp_register_script( 'jquery', includes_url( '/js/jquery/jquery.js' ), false, null, true );
				wp_enqueue_script( 'jquery' );
			}
		);
	}

	/**
	 * Limits the comment reply JS to the places where it's needed
	 */
	private function limit_comments_JS(): void {

		add_action(
			'wp_print_scripts',
			function () {
				if ( is_singular() && ( get_option( 'thread_comments' ) === true ) && comments_open() && get_comments_number() ) {
					wp_enqueue_script( 'comment-reply' );
				} else {
					wp_dequeue_script( 'comment-reply' );
				}
			},
			100
		);

	}

	/**
	 * Removes the styling added to the header for recent comments
	 */
	private function remove_comments_style(): void {
		add_action(
			'widgets_init',
			function () {
				global $wp_widget_factory;
				remove_action(
					'wp_head',
					array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' )
				);
			}
		);
	}

	/**
	 * Remove WP version from css and js
	 */
	private function remove_wp_assets_version(): void {
		add_filter(
			'style_loader_src',
			function ( $src ) {
				if ( strpos( $src, 'ver=' ) ) {
					$src = remove_query_arg( 'ver', $src );
				}

				return $src;
			}
		);
		add_filter(
			'script_loader_src',
			function ( $src ) {
				if ( strpos( $src, 'ver=' ) ) {
					$src = remove_query_arg( 'ver', $src );
				}

				return $src;
			}
		);
	}

}
