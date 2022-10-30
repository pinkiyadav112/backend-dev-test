<?php
spl_autoload_register(
	function ( $classname ) {

		// It has to be part of kotwBase namespace.
		$class     = preg_replace( array( '/kotw\\\\/i', '/\\\\/i' ), array( '', DIRECTORY_SEPARATOR ), $classname );
		$classpath = dirname( __FILE__ ) . '/lib/' . $class . '.php';

		if ( file_exists( $classpath ) ) {
			include_once $classpath;
		}

	}
);
