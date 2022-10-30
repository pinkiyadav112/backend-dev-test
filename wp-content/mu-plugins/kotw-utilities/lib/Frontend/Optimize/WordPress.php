<?php

namespace kotw\Frontend\Optimize;

use WP_Error as WP_Error;

defined( 'ABSPATH' ) || die( '' );

class WordPress {
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
	 * @param array $optimizations
	 */
	public function __construct( array $optimizations = array() ) {

		$defaults = array(
			'block_external_HTTP'             => false,
			'disable_feeds'                   => false,
			'disable_heartbeat'               => false,
			'disable_rest_api'                => false,
			'disable_RSD'                     => true,
			'disable_shortlinks'              => true,
			'disable_version_numbers'         => true,
			'disable_WLW_manifest'            => true,
			'disable_WP_version'              => true,
			'index_rel_link'                  => true,
			'parent_post_rel_link'            => true,
			'start_post_rel_link'             => true,
			'adjacent_posts_rel_link_wp_head' => true,
			'disable_XMLRPC'                  => true,
			'limit_revisions'                 => true,
			'slow_heartbeat'                  => true,
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
	 * Block plugins to connect to external http's
	 */
	private function block_external_HTTP(): void {
		if ( ! is_admin() ) {
			add_filter(
				'pre_http_request',
				function () {
					return new WP_Error( 'http_request_failed', __( 'Request blocked by WP Optimize.' ) );
				},
				100
			);
		}
	}


	/**
	 * Disables the access to Rest API
	 * Breaks a lot, so not really recommended to use.
	 */
	private function disable_rest_api(): void {

		// Remove the references to the JSON api
		remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
		remove_action( 'rest_api_init', 'wp_oembed_register_route' );
		add_filter( 'embed_oembed_discover', '__return_false' );
		remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );

		// Disable the API completely
		add_filter( 'json_enabled', '__return_false' );
		add_filter( 'json_jsonp_enabled', '__return_false' );
		add_filter( 'rest_enabled', '__return_false' );
		add_filter( 'rest_jsonp_enabled', '__return_false' );

	}


	/**
	 * Removes links to RSS feeds
	 */
	private function disable_feeds(): void {
		remove_action( 'wp_head', 'feed_links_extra', 3 );
		remove_action( 'wp_head', 'feed_links', 2 );
		add_action( 'do_feed', array( $this, 'disable_feeds_hook' ), 1 );
		add_action( 'do_feed_rdf', array( $this, 'disable_feeds_hook' ), 1 );
		add_action( 'do_feed_rss', array( $this, 'disable_feeds_hook' ), 1 );
		add_action( 'do_feed_rss2', array( $this, 'disable_feeds_hook' ), 1 );
		add_action( 'do_feed_atom', array( $this, 'disable_feeds_hook' ), 1 );
	}

	/**
	 * Removes the actual feed links
	 */
	public function disable_feeds_hook(): void {
		wp_die( '<p>' . esc_html__( 'Feed disabled by WP Optimize.' ) . '</p>' );
	}

	/**
	 * Removes the WP Heartbeat Api. Caution: this disables the autosave functionality
	 */
	private function disable_heartbeat(): void {
		add_action(
			'admin_enqueue_scripts',
			function () {
				wp_deregister_script( 'heartbeat' );
			}
		);
	}


	/**
	 * Disables RSD Links, used by pingbacks
	 */
	private function disable_RSD(): void {
		remove_action( 'wp_head', 'rsd_link' );
	}

	/**
	 * Removes the WP Shortlink
	 */
	private function disable_shortlinks(): void {
		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
	}


	/**
	 * Removes the version hook on scripts and styles
	 *
	 * @uses MT_WP_Optimize::no_scripts_styles_version_hook
	 */
	private function disable_version_numbers(): void {
		add_filter( 'style_loader_src', array( $this, 'disable_version_numbers_hook' ), 9999 );
		add_filter( 'script_loader_src', array( $this, 'disable_version_numbers_hook' ), 9999 );
	}

	/**
	 * Removes version numbers from scripts and styles.
	 * The absence of version numbers increases the likelyhood of scripts and styles being cached.
	 *
	 * @param string @target_url The url of the script
	 *
	 * @return string @target_url The modified target url
	 */
	public function disable_version_numbers_hook( string $target_url = '' ): string {

		if ( strpos( $target_url, 'ver=' ) ) {
			$target_url = remove_query_arg( 'ver', $target_url );
		}

		return $target_url;

	}

	/**
	 * Removes WLW manifest bloat
	 */
	private function disable_WLW_manifest(): void {
		remove_action( 'wp_head', 'wlwmanifest_link' );
	}

	/**
	 * Removes Index Rel Link
	 */
	private function index_rel_link(): void {
		remove_action( 'wp_head', 'index_rel_link' );
	}

	/**
	 * Removes Previous Link
	 */
	private function parent_post_rel_link(): void {
		remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	}

	/**
	 * Removes Previous Link
	 */
	private function start_post_rel_link(): void {
		remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	}

	/**
	 * Removes Previous Link
	 */
	private function adjacent_posts_rel_link_wp_head(): void {
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	}

	/**
	 * Removes the WP Version as generated by WP
	 */
	private function disable_WP_version(): void {
		remove_action( 'wp_head', 'wp_generator' );
		add_filter( 'the_generator', '__return_null' );
	}

	/**
	 * Disables XML RPC. Warning, makes some functions unavailable!
	 */
	private function disable_XMLRPC(): void {

		if ( is_admin() ) {
			update_option( 'default_ping_status', 'closed' ); // Might do something else here to reduce our queries
		}

		add_filter( 'xmlrpc_enabled', '__return_false' );
		add_filter( 'pre_update_option_enable_xmlrpc', '__return_false' );
		add_filter( 'pre_option_enable_xmlrpc', '__return_zero' );

		/**
		 * Unsets xmlrpc headers
		 *
		 * @param array $headers The array of wp headers
		 */
		add_filter(
			'wp_headers',
			function ( $headers ) {
				if ( isset( $headers['X-Pingback'] ) ) {
					unset( $headers['X-Pingback'] );
				}

				return $headers;
			},
			10,
			1
		);

		/**
		 * Unsets xmlr methods for pingbacks
		 *
		 * @param array $methods The array of xmlrpc methods
		 */
		add_filter(
			'xmlrpc_methods',
			function ( $methods ) {
				unset( $methods['pingback.ping'] );
				unset( $methods['pingback.extensions.getPingbacks'] );

				return $methods;
			},
			10,
			1
		);

	}


	/**
	 * Limits post revisions
	 */
	private function limit_revisions(): void {

		if ( defined( 'WP_POST_REVISIONS' ) && ( WP_POST_REVISIONS !== false ) ) {
			add_filter(
				'wp_revisions_to_keep',
				function ( $num, $post ) {
					return 5;
				},
				10,
				2
			);
		}

	}


	/**
	 * Slows heartbeat to 1 minute
	 */
	private function slow_heartbeat(): void {

		add_filter(
			'heartbeat_settings',
			function ( $settings ) {
				$settings['interval'] = 60;

				return $settings;
			}
		);

	}
}
