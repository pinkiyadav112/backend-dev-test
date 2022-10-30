<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    english.codedegree.com
 * @subpackage kotw
 */

namespace kotw\Timber;

use kotw\Blocks\Lottie as LottieAnimation;
use kotw\Logger;
use Twig\Environment;
use Twig\TwigFunction;

class Functions {

	public function __construct() {
		add_filter( 'timber/twig', array( $this, 'functions' ), 10, 999 );
	}

	/**
	 * All global timber functions that can be used.
	 *
	 * @return Environment
	 */
	public function functions( Environment $twig ) {
		$filters = array(
			array(
				'key'      => 'render_animation',
				'callback' => array( $this, 'render_animation' ),
			),
		);

		foreach ( $filters as $filter ) {
			$twig->addFunction( new TwigFunction( $filter['key'], $filter['callback'] ) );
		}

		return $twig;
	}

	/**
	 * This renders a lottie animation that is saved either in kotw-utilities or the active theme.
	 *
	 * @param $timber_args
	 *
	 * @return string
	 */
	public function render_animation( $timber_args ): string {

		$lottie_animation = new LottieAnimation();
		$args             = array(
			'name' => $timber_args['name'],
			'id'   => $timber_args['id'],
			'loop' => $timber_args['loop'] ? 'true' : 'false',
			'auto' => $timber_args['auto'] ? 'true' : 'false',

		);

		new Logger( __CLASS__, $args, '$args' );

		return $lottie_animation->build_the_html( $args );
	}
}
