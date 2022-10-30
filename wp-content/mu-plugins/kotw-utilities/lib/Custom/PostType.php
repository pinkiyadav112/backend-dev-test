<?php

namespace kotw\Custom;

/**
 * PostType
 * Creates a new post_type with all the textdomain strings needed.
 */
class PostType {


	/**
	 * @var String $name
	 * Post type name
	 */
	public string $name;

	/**
	 * @var String $singular
	 * Singular label
	 */
	public string $singular;

	/**
	 * @var String $plural
	 * Plural Label
	 */
	public string $plural;

	/**
	 * @var Array $tax_array
	 * Taxonomy array
	 */
	public array $tax_array;

	/**
	 * @var Array $support
	 * Supports Array
	 */
	public array $supports;

	/**
	 * @var string $icon
	 */
	public string $icon;

	/**
	 * @var boolean $available_in_rest
	 */
	public bool $available_in_rest;

	/**
	 * @var array $rewrite
	 */
	public array $rewrite;

	/**
	 * This is the menu name in the WordPress dashboard.
	 * @var string
	 */
	public string $menu_name;

	/**
	 * KOTW_Custom_Post constructor.
	 *
	 * @param $name
	 * @param $singular
	 * @param $plural
	 * @param $tax_array
	 * @param $supports
	 * @param string $icon
	 * @param bool $available_in_rest
	 * @param array $rewrite
	 * @param string $menu_name
	 */
	public function __construct(
		$name,
		$singular,
		$plural,
		$tax_array,
		$supports,
		string $icon = 'dashicons-admin-pos',
		bool $available_in_rest = false,
		array $rewrite = array(),
		string $menu_name = ''
	) {
		$this->name              = $name;
		$this->singular          = $singular;
		$this->plural            = $plural;
		$this->tax_array         = $tax_array;
		$this->supports          = $supports;
		$this->icon              = $icon;
		$this->available_in_rest = $available_in_rest;
		$this->rewrite           = $rewrite;
		$this->menu_name         = $menu_name ?? $this->plural;

		add_action( 'init', array( $this, 'register_post_type' ), 0 );

	}


	/**
	 * @codingStandardsIgnoreStart
	 * register_post_type.
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => __( $this->singular, 'kotw' ),
			'singular_name'         => __( $this->singular, 'kotw' ),
			'menu_name'             => __( $this->menu_name, 'kotw' ),
			'name_admin_bar'        => __( $this->singular, 'kotw' ),
			'archives'              => $this->singular . __( ' Archives', 'kotw' ),
			'attributes'            => $this->singular . __( ' Attributes', 'kotw' ),
			'parent_item_colon'     => __( 'Parent ', 'kotw' ) . $this->singular . ':',
			'all_items'             => __( 'All ', 'kotw' ) . $this->plural,
			'add_new_item'          => __( 'Add New ', 'kotw' ) . $this->singular,
			'add_new'               => __( 'Add New', 'kotw' ),
			'new_item'              => __( 'New ', 'kotw' ) . $this->singular,
			'edit_item'             => __( 'Edit ', 'kotw' ) . $this->singular,
			'update_item'           => __( 'Update ', 'kotw' ) . $this->singular,
			'view_item'             => __( 'View ', 'kotw' ) . $this->singular,
			'view_items'            => __( 'View ', 'kotw' ) . $this->plural,
			'search_items'          => __( 'Search ', 'kotw' ) . $this->plural,
			'not_found'             => __( 'Not found', 'kotw' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'kotw' ),
			'featured_image'        => __( 'Featured Image', 'kotw' ),
			'set_featured_image'    => __( 'Set featured image', 'kotw' ),
			'remove_featured_image' => __( 'Remove featured image', 'kotw' ),
			'use_featured_image'    => __( 'Use as featured image', 'kotw' ),
			'insert_into_item'      => __( 'Insert into ', 'kotw' ) . $this->singular,
			'uploaded_to_this_item' => __( 'Uploaded to this ', 'kotw' ) . $this->singular,
			'items_list'            => $this->plural . __( ' list', 'kotw' ),
			'items_list_navigation' => $this->plural . __( ' list navigation', 'kotw' ),
			'filter_items_list'     => __( 'Filter ', 'kotw' ) . $this->plural . __( ' list', 'kotw' ),
		);
		$args   = array(
			'label'               => __( $this->singular, 'kotw' ),
			'description'         => $this->singular . __( ' Description', 'kotw' ),
			'labels'              => $labels,
			'supports'            => $this->supports,
			'taxonomies'          => $this->tax_array,
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => $this->icon,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'show_in_rest'        => $this->available_in_rest,
			'rewrite'             => $this->rewrite,

		);
		register_post_type( $this->name, $args );
	}
}
