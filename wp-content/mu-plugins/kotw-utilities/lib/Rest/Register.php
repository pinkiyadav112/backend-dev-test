<?php

namespace kotw\Rest;

class Register {

	public function __construct( $endpoints ) {
		foreach ( $endpoints as $endpoint ) {
			add_action(
				'rest_api_init',
				function () use ( $endpoint ) {
					register_rest_route(
						$endpoint[0], // namespace
						$endpoint[1], // route
						$endpoint[2] // args_array
					);
				}
			);
		}
	}
}
