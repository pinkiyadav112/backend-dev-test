<?php

namespace kotw\Custom;

/**
 * Taxonomy
 * Creates a new taxonomy with all the textdomain strings needed.
 */
class Taxonomy {

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
	 * @var array $support
	 * Supports Array
	 */
	public array $supports;


	/**
	 * @var Boolean $hierarchical
	 *
	 */
	public bool $hierarchical;


	/**
	 * @var array $rewrite
	 *
	 */
	public array $rewrite;

	/**
	 * @var Boolean $available_in_rest
	 */
	public bool $available_in_rest;

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
	 * @param $supports
	 * @param bool $hierarchical
	 * @oaran $rewrite
	 */
	public function __construct(
		$name,
		$singular,
		$plural,
		$supports,
		bool $hierarchical = false,
		$rest = false,
		$rewrite = array(),
		string $menu_name = ''
	) {
		$this->name              = $name;
		$this->singular          = $singular;
		$this->plural            = $plural;
		$this->supports          = $supports;
		$this->hierarchical      = $hierarchical;
		$this->available_in_rest = $rest;
		$this->rewrite           = $rewrite;
		$this->menu_name         = $menu_name ?? $this->plural;

		add_action( 'init', array( $this, 'register_tax' ), 0 );

	}


	/**
	 * register_post_type.
	 */
	//@codingStandardsIgnoreStart
	public function register_tax() {
		$labels = array(
			'name'                       => __( $this->plural, 'kotw' ),
			'singular_name'              => __( $this->singular, 'kotw' ),
			'menu_name'                  => __( $this->menu_name, 'kotw' ),
			'all_items'                  => __( 'All ' . $this->plural, 'kotw' ),
			'parent_item'                => __( 'Parent ' . $this->singular, 'kotw' ),
			'parent_item_colon'          => __( 'Parent ' . $this->singular . ':', 'kotw' ),
			'new_item_name'              => __( 'New ' . $this->singular . ' Name', 'kotw' ),
			'add_new_item'               => __( 'Add New ' . $this->singular, 'kotw' ),
			'edit_item'                  => __( 'Edit ' . $this->singular, 'kotw' ),
			'update_item'                => __( 'Update ' . $this->singular, 'kotw' ),
			'view_item'                  => __( 'View ' . $this->singular, 'kotw' ),
			'separate_items_with_commas' => __( 'Separate ' . $this->plural . ' with commas', 'kotw' ),
			'add_or_remove_items'        => __( 'Add or remove ' . $this->plural, 'kotw' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'kotw' ),
			'popular_items'              => __( 'Popular ' . $this->plural, 'kotw' ),
			'search_items'               => __( 'Search ' . $this->plural, 'kotw' ),
			'not_found'                  => __( 'Not Found', 'kotw' ),
			'no_terms'                   => __( 'No ' . $this->plural, 'kotw' ),
			'items_list'                 => $this->plural . __( ' list', 'kotw' ),
			'items_list_navigation'      => $this->plural . __( ' list navigation', 'kotw' ),
		);
		$args   = array(
			'labels'            => $labels,
			'hierarchical'      => $this->hierarchical,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'show_in_rest'      => $this->available_in_rest,
			'rewrite'           => $this->rewrite,
		);
		register_taxonomy( $this->name, $this->supports, $args );
	}
}
