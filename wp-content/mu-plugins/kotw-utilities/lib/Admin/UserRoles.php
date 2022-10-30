<?php

namespace kotw\Admin;

use kotw\Logger;

/**
 * This class is mainly used to register custom user roles
 * added by kotw-utilities, and to define their capabilities.
 *
 * This is also added to
 */
class UserRoles {

	/**
	 * @var array[]
	 */
	public array $add_roles;

	/**
	 * @var array
	 */
	public array $remove_roles;

	public function __construct() {

		$this->add_roles    = array();
		$this->remove_roles = array();

		// Add developer
		$this->add_role(
			array(
				'developer' => array(
					'name'         => 'Developer',
					'copy'         => 'editor',
					'capabilities' => array( // Read: https://wordpress.org/support/article/roles-and-capabilities
						'list_users'         => 1,
						'manage_options'     => 1,
						'switch_themes'      => 1,
						'edit_plugins'       => 1,
						'activate_plugins'   => 1,
						'edit_dashboard'     => 1,
						'edit_theme_options' => 1,

					),
				),
			)
		);

		// Remove contributor.
		$this->remove_role( 'contributor' );

		// Remove Author.
		$this->remove_role( 'author' );
	}

	/**
	 * This should register the user roles in the DB.
	 *
	 * Fires with the admin_init action hook, but checks a variable in the db
	 * before firing. So this is a pseudo implementation of the activation hook
	 * for plugins.
	 *
	 *
	 * @return void
	 */
	public function run() {
		// Get total number of roles that will need to be added/removed.
		$user_roles_number = (int) ( count( $this->add_roles ) + count( $this->remove_roles ) );
		add_action(
			'admin_init',
			function () use ( $user_roles_number ) {
				// Exit if this number hasn't been changed recently.
				if ( (int) get_option( 'kotw_current_user_roles' ) === $user_roles_number ) {
					return;
				}

				global $wp_roles;
				$this->add_roles = array_merge( $this->add_roles, $wp_roles->roles );

				// Add roles.
				foreach ( $this->add_roles as $role => $role_array ) {
					if ( is_array( $role_array ) && isset( $role_array['name'] ) !== null ) {
						$capabilities = $role_array['capabilities'];
						if ( isset( $role_array['copy'] ) ) {
							$wordpress_role = $wp_roles->get_role( $role_array['copy'] );
							$capabilities   = array_merge( $capabilities, $wordpress_role->capabilities );
						}
						add_role(
							$role,
							$role_array['name'],
							$capabilities,
						);

					}
				}
				// Remove roles.
				foreach ( $this->remove_roles as $role ) {
					remove_role( $role );
				}

				update_option( 'kotw_current_user_roles', $user_roles_number );
			}
		);
	}

	/**
	 * Adds specific role to the site.
	 * @return void
	 */
	public function add_role( $user_role ) {
		$keys                        = array_keys( $user_role );
		$this->add_roles[ $keys[0] ] = $user_role[ $keys[0] ];
	}

	/**
	 * Remove specific role from the site.
	 * @return void
	 */
	public function remove_role( $user_role_key ) {
		$this->remove_roles[] = $user_role_key;
	}

	/**
	 * Get array of all roles per the site.
	 * @return array[]
	 */
	public function get_roles(): array {
		global $wp_roles;

		return $wp_roles->roles;
	}
}
