<?php
/**
 *
 * This adds Lottie animations support to the blocks.
 *
 * @author     Kings Of The Web
 * @year       2022
 * @package    english.codedegree.com
 * @subpackage kotw\Frontend
 */

namespace kotw\Blocks;

class Lottie {

	public string $lottie_files_dir;
	public string $lottie_files_dir_theme;

	public function __construct() {
		$this->lottie_files_dir       = KotwUtilitiesPluginPath . '/assets/lottie-files/';
		$this->lottie_files_dir_theme = get_stylesheet_directory() . '/assets/src/lottie-files/';
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_lottie_scripts' ) );
	}

	/**
	 * This enqueues the lottie scripts.
	 *
	 * @return void
	 */
	public function enqueue_lottie_scripts() {
		// enqueue lottie scripts.
		wp_enqueue_script(
			'lottie',
			'https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.7.4/lottie.min.js',
			array(),
			'5.7.4',
			false
		);
	}


	/**
	 * This builds the lottie parameters.
	 *
	 * @return false|string
	 */
	public function build_the_html( $args ) {
		if ( ! $args || ! is_array( $args ) || ! isset( $args['name'] ) ) {
			return false;
		}
		$animation_name = $args['name'];
		$animation_json = null;

		// check if it exists in kotw-utilities plugin, or the active theme, OR exit if nothing was found!
		$kotw_utilities_files = array(
			$this->lottie_files_dir . $animation_name . '/animation.json',
			$this->lottie_files_dir . $animation_name . '/data.json',
			$this->lottie_files_dir . $animation_name . '/file.json',
			$this->lottie_files_dir . $animation_name . '/' . $animation_name . '.json',

		);
		$active_theme_files = array(
			$this->lottie_files_dir_theme . $animation_name . '/animation.json',
			$this->lottie_files_dir_theme . $animation_name . '/data.json',
			$this->lottie_files_dir_theme . $animation_name . '/file.json',
			$this->lottie_files_dir_theme . $animation_name . '/' . $animation_name . '.json',

		);

		// check if any of active theme files exist.
		foreach ( $active_theme_files as $file ) {
			if ( file_exists( $file ) ) {
				$animation_json = $file;
				break;
			}
		}

		// If you were not successful, then check if any of the active theme files exist.
		if ( ! $animation_json ) {
			// check if any of the active theme files exist.
			foreach ( $kotw_utilities_files as $file ) {
				if ( file_exists( $file ) ) {
					$animation_json = $file;
					break;
				}
			}
		}

		// if you were not successful, then exit.
		if ( ! $animation_json ) {
			return false;
		}

		// get the url of $animation_json.
		$animation_json = file_get_contents( $animation_json );

		// Build the html
		ob_start();
		?>
		<div id="kotw-animation-<?php echo $args['name'] . '-' . $args['id']; ?>"></div>
		<script>

			(function () {
				let animationData = `<?php echo $animation_json; ?>`;
				if (!animationData) {
					return false;
				}
				// Check if the JSON is parseable
				try {
					animationData = JSON.parse(animationData);
				} catch (e) {
					console.warn('Bad json');
				}

				// fix the height of the container.
				const container = document.querySelector('#kotw-animation-<?php echo $args['name'] . '-' . $args['id']; ?>');

				lottie.loadAnimation({
					container: container,
					renderer: 'canvas',
					loop: <?php echo $args['loop']; ?>,
					autoplay: <?php echo $args['auto']; ?>,
					animationData: animationData,
					rendererSettings: {
						preserveAspectRatio: 'xMidYMid meet',
						imagePreserveAspectRatio: 'xMidYMid meet',
						progressiveLoad: true,
						clearCanvas: true,
					},
				});
			})();

		</script>
		<?php
		return ob_get_clean();
	}
}
