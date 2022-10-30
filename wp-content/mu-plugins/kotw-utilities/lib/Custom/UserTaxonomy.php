<?php
/*
 * Copyright (c) 2022. Property of Kings Of The Web
 */

namespace kotw\Custom;

/**
 * This class adds support to user taxonomies, in WordPress.
 * The registered taxonomy still gets saved in wp_terms, but the admin UI is manipulated,
 * to show the taxonomy in the user interface.
 *
 * User taxonomies can be registered using the kotw\Custom\Taxonomy class, by passing the "user" to the $supports array:
 *
 * Example: new Taxonomy("user-group", array("user"), "User Group", "User Groups", "dashicons-groups", true);
 *
 * Credits go to: https://wordpress.org/plugins/user-taxonomies/
 *
 *
 * @year       2022
 * @package    kotw
 * @subpackage kotw\Custom
 */
class UserTaxonomy {
	private static array $taxonomies = array();

	/**
	 * Register all the hooks and filters we can in advance
	 * Some will need to be registered later on, as they require knowledge of the taxonomy name
	 */
	public function __construct() {
		// Taxonomies
		add_action( 'registered_taxonomy', array( $this, 'registered_taxonomy' ), 10, 3 );

		// Menus
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_filter( 'parent_file', array( $this, 'parent_menu' ) );

		// User Profiles
		add_filter( 'sanitize_user', array( $this, 'restrict_username' ) );

		// Add new taxonomy to User columns
		add_filter( 'manage_users_columns', array( $this, 'manage_users_columns' ) );
		add_filter( 'manage_users_custom_column', array( $this, 'manage_users_custom_column' ), 10, 3 );

	}

	/**
	 * This is our way into manipulating registered taxonomies
	 * It's fired at the end of the register_taxonomy function
	 *
	 * @param string $taxonomy     - The name of the taxonomy being registered
	 * @param $object - The object type the taxonomy is for; Example: array("user")
	 * @param array $args          - The user supplied + default arguments for registering the taxonomy
	 */
	public function registered_taxonomy( string $taxonomy, $object, array $args ): void {
		global $wp_taxonomies;

		// Only modify user taxonomies, everything else can stay as is
		if ( is_array( $object ) ) {
			if ( ! in_array( 'user', $object, true ) ) {
				return;
			}
		} elseif ( $object !== 'user' ) {
			return;
		}

		// We're given an array, but expected to work with an object later on
		$args = (object) $args;

		// Register any hooks/filters that rely on knowing the taxonomy now
		add_filter( "manage_edit-{$taxonomy}_columns", array( $this, 'set_user_column' ) );
		add_action( "manage_{$taxonomy}_custom_column", array( $this, 'set_user_column_values' ), 10, 3 );

		// Set the callback to update the count if not already set
		if ( empty( $args->update_count_callback ) ) {
			$args->update_count_callback = array( $this, 'update_count' );
		}

		$wp_taxonomies[ $taxonomy ]    = $args;
		self::$taxonomies[ $taxonomy ] = $args;
	}

	/**
	 * We need to manually update the number of users for a taxonomy term
	 *
	 * @param array $terms     - List of Term taxonomy IDs
	 * @param object $taxonomy - Current taxonomy object of terms
	 *
	 * @see _update_post_term_count()
	 */
	public function update_count( array $terms, object $taxonomy ): void {
		global $wpdb;

		foreach ( (array) $terms as $term ) {
			$count = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d",
					$term
				)
			);

			do_action( 'edit_term_taxonomy', $term, $taxonomy );
			$wpdb->update( $wpdb->term_taxonomy, compact( 'count' ), array( 'term_taxonomy_id' => $term ) );
			do_action( 'edited_term_taxonomy', $term, $taxonomy );
		}
	}

	/**
	 * Add each of the taxonomies to the Users menu
	 * They will behave in the same was as post taxonomies under the Posts menu item
	 * Taxonomies will appear in alphabetical order
	 */
	public function admin_menu(): void {
		// Put the taxonomies in alphabetical order
		$taxonomies = self::$taxonomies;
		ksort( $taxonomies );

		foreach ( $taxonomies as $key => $taxonomy ) {
			add_users_page(
				$taxonomy->labels->menu_name,
				$taxonomy->labels->menu_name,
				$taxonomy->cap->manage_terms,
				"edit-tags.php?taxonomy={$key}"
			);
		}
	}

	/**
	 * Fix a bug with highlighting the parent menu item
	 * By default, when on the edit taxonomy page for a user taxonomy, the Posts tab is highlighted
	 * This will correct that bug
	 */
	function parent_menu( $parent = '' ) {
		global $pagenow;

		// If we're editing one of the user taxonomies
		// We must be within the users menu, so highlight that
		if ( ! empty( $_GET['taxonomy'] ) && $pagenow == 'edit-tags.php' && isset( self::$taxonomies[ $_GET['taxonomy'] ] ) ) {
			$parent = 'users.php';
		}

		return $parent;
	}

	/**
	 * Correct the column names for user taxonomies
	 * Need to replace "Posts" with "Users"
	 */
	public function set_user_column( $columns ) {
		unset( $columns['posts'] );
		$columns['users'] = __( 'Users' );

		return $columns;
	}

	/**
	 * Set values for custom columns in user taxonomies
	 */
	public function set_user_column_values( $display, $column, $term_id ): void {
		if ( 'users' === $column ) {
			$term = get_term( $term_id, $_GET['taxonomy'] );
			echo $term->count;
		}
	}

	/**
	 * Add the taxonomies to the user view/edit screen
	 *
	 * @param Object $user - The user of the view/edit screen
	 */
	public function user_profile( $user ) {
		// Using output buffering as we need to make sure we have something before outputting the header
		// But we can't rely on the number of taxonomies, as capabilities may vary
		ob_start();

		foreach ( self::$taxonomies as $key => $taxonomy ) :
			// Check the current user can assign terms for this taxonomy
			if ( ! current_user_can( $taxonomy->cap->assign_terms ) ) {
				continue;
			}

			// Get all the terms in this taxonomy
			$terms = get_terms( $key, array( 'hide_empty' => false ) );
			?>
			<table class="form-table">
				<tr>
					<th><label for=""><?php _e( "Select {$taxonomy->labels->singular_name}" ); ?></label></th>
					<td>
						<?php if ( ! empty( $terms ) ) : ?>
							<?php foreach ( $terms as $term ) : ?>
								<input type="radio" name="<?php echo $key; ?>"
								id="<?php echo "{$key}-{$term->slug}"; ?>                                    									   value="<?php echo $term->slug; ?>"
								<?php
								checked(
									true,
									is_object_in_term( $user->ID, $key, $term )
								);
								?>
								/>
								<label for="<?php echo "{$key}-{$term->slug}"; ?>"><?php echo $term->name; ?></label>
								<br/>
							<?php endforeach; // Terms ?>
						<?php else : ?>
							<?php _e( "There are no {$taxonomy->labels->name} available." ); ?>
						<?php endif ?>
					</td>
				</tr>
			</table>
			<?php
		endforeach; // Taxonomies

		// Output the above if we have anything, with a heading
		$output = ob_get_clean();
		if ( ! empty( $output ) ) {
			echo '<h3>', __( 'Taxonomies' ), '</h3>';
			echo $output;
		}
	}

	/**
	 * Save the custom user taxonomies when saving a users profile
	 *
	 * @param Integer $user_id - The ID of the user to update
	 */
	public function save_profile( $user_id ) {
		foreach ( self::$taxonomies as $key => $taxonomy ) {
			// Check the current user can edit this user and assign terms for this taxonomy
			if ( ! current_user_can( 'edit_user', $user_id ) && current_user_can( $taxonomy->cap->assign_terms ) ) {
				return false;
			}

			// Save the data
			$term = esc_attr( $_POST[ $key ] );
			wp_set_object_terms( $user_id, array( $term ), $key, false );
			clean_object_term_cache( $user_id, $key );
		}
	}

	/**
	 * Usernames can't match any of our user taxonomies
	 * As otherwise it will cause a URL conflict
	 * This method prevents that happening
	 */
	public function restrict_username( $username ) {
		if ( isset( self::$taxonomies[ $username ] ) ) {
			return '';
		}

		return $username;
	}

	/**
	 * Add the user taxonomies to the users search page.
	 *
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function manage_users_columns( $columns ) {
		//foreach of all self::$taxonomies add them to the $columns array
		foreach ( self::$taxonomies as $key => $taxonomy ) {
			$columns[ $key ] = $taxonomy->labels->singular_name;
		}

		return $columns;
	}

	/**
	 * Show the list of user taxonomies in the user search columns.
	 *
	 * @param $val
	 * @param $column_name
	 * @param $user_id
	 *
	 * @return string|void
	 */
	public function manage_users_custom_column( $val, $column_name, $user_id ) {
		// loop through all self::$taxonomies and check if the column name matches the key
		foreach ( self::$taxonomies as $key => $taxonomy ) {
			// if it matches, get the terms for the user
			if ( $column_name === $key ) {
				$terms      = get_terms( $key, array( 'hide_empty' => false ) );
				$user_terms = array();
				// loop through the terms and check if the user has the term
				foreach ( $terms as $term ) {
					if ( is_object_in_term( $user_id, $key, $term ) ) {
						// if they do, return the name of the term
						$user_terms[] = $term->name;
					}
				}

				return implode( ',', $user_terms );
			}
		}
	}
}
