<?php

namespace kotw\Blocks;

class Example extends Block {

	public static string $name;
	public static string $slug;
	public static string $version;
	public static array $js_dependencies;
	public static string $path;

	public static function init() {
		self::$name            = 'Example';
		self::$slug            = 'example';
		self::$version         = '1.0.0';
		self::$js_dependencies = array(); // Add here JS dependencies if needed. I hope you will not have to add jquery!
		self::$path            = dirname( __file__ );
	}

	public static function get_template( $settings, $content, $is_preview = false ) {
		self::init();
		parent::template( $settings, $content, __CLASS__, $is_preview );
	}

	/**
	 *  This is the main method used for enqueueing assets.
	 * @return void
	 */
	public static function enqueue_assets() {
		self::init();
		parent::enqueue( self::$path, __CLASS__ );
	}

	/**
	 * This when you edit your context.
	 * Add any custom context here.
	 * And keep in mind that you should be relying mostly on
	 * the fields coming from ACF,But this is reserved for
	 * the unique cases, where you might need custom variables
	 * to be added to this block.
	 *
	 *
	 * **Do not forget to return the $context back!**
	 *
	 * @return array
	 */
	public static function custom_context( $context ): array {
		$context['customContextVariable'] = time();
		return $context;
	}

	/**
	 * Add here any extra scripts you want to enqueue.
	 * You mostly should only be enqueueing external
	 * libraries here, if needed. But you still can rely
	 * on webpack to do that.So you should have a very good reason,
	 * of why you decided to enqueue external scripts here!
	 * @return void
	 */
	public static function custom_assets() {
		// Add custom assets here, by calling wp_enqueue_script and wp_enqueue_style
	}
}
