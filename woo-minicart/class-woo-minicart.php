<?php
/**
 * Main class for Minicart for WooCommerce.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WMC_Main_Class {

	/**
	 * @var array Plugin options.
	 */
	public $wmc_options = array();

	/**
	 * @var string Plugin version.
	 */
	public $plugin_version = '1.0';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->plugin_version = defined( 'WOO_MINICART_VERSION' ) ? WOO_MINICART_VERSION : '1.0';
		$this->wmc_options    = (array) get_option( 'wmc_options', array() );

		add_action( 'admin_menu', array( $this, 'wmc_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		if ( isset( $this->wmc_options['enable-minicart'] ) && (int) $this->wmc_options['enable-minicart'] === 1 ) {
			add_action( 'wp_footer', array( $this, 'templates' ) );
			add_action( 'wp_head', array( $this, 'dynamic_css' ) );
		}

		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'wmc_fragments' ), 30, 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_shortcode( 'woo-minicart', array( $this, 'woo_minicart_shortcode' ) );
	}

	/**
	 * Frontend scripts & styles.
	 */
	public function scripts() {
		wp_enqueue_style(
			'wmc-template1',
			plugins_url( '/assets/css/wmc-default-template.css', __FILE__ ),
			array(),
			$this->plugin_version
		);

		wp_enqueue_script(
			'wmc-js',
			plugins_url( '/assets/js/woo-minicart.js', __FILE__ ),
			array( 'jquery' ),
			$this->plugin_version,
			true
		);
	}

	/**
	 * Admin scripts & styles.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function admin_scripts( $hook ) {
		if ( 'toplevel_page_woo-minicart' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'wmc-admin-css',
			plugins_url( '/assets/css/admin.css', __FILE__ ),
			array(),
			$this->plugin_version
		);

		wp_enqueue_script(
			'wmc-admin-js',
			plugins_url( '/assets/js/admin.js', __FILE__ ),
			array( 'jquery' ),
			$this->plugin_version,
			true
		);
	}

	/**
	 * Output templates in footer (not on cart/checkout).
	 */
	public function templates() {
		if ( ! function_exists( 'is_cart' ) || ( ! is_cart() && ! is_checkout() ) ) {
			$file = plugin_dir_path( __FILE__ ) . 'frontend/wmc-default-template.php';
			if ( file_exists( $file ) ) {
				require $file;
			}
		}
	}

	/**
	 * Ajax fragments for minicart.
	 *
	 * @param array $fragments Fragments.
	 * @return array
	 */
	public function wmc_fragments( $fragments ) {
		ob_start();
		$file = plugin_dir_path( __FILE__ ) . 'frontend/wmc-content.php';
		if ( file_exists( $file ) ) {
			require $file;
		}
		$fragments['div.wmc-content'] = ob_get_clean();

		$count = function_exists( 'WC' ) && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
		$fragments['span.wmc-count']  = '<span class="wmc-count">' . esc_html( (string) $count ) . '</span>';

		return $fragments;
	}

	/**
	 * Shortcode output (not on cart/checkout).
	 *
	 * @return string
	 */
	public function woo_minicart_shortcode() {
		if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() ) ) {
			return '';
		}

		ob_start();
		$file = plugin_dir_path( __FILE__ ) . 'frontend/wmc-shortcode-template.php';
		if ( file_exists( $file ) ) {
			require $file;
		}
		return (string) ob_get_clean();
	}

	/**
	 * Inline dynamic CSS for position/offset.
	 */
	public function dynamic_css() {
		$position   = isset( $this->wmc_options['minicart-position'] ) ? (string) $this->wmc_options['minicart-position'] : 'wmc-top-right';
		$wmc_offset = isset( $this->wmc_options['wmc-offset'] ) ? (int) $this->wmc_options['wmc-offset'] : 150;
		?>
		<style type="text/css">
			<?php if ( 'wmc-top-left' === $position ) : ?>
				.wmc-cart-wrapper { left: 50px; top: <?php echo esc_html( (string) $wmc_offset ); ?>px; }
				.wmc-cart { left: 10px; }
			<?php elseif ( 'wmc-top-right' === $position ) : ?>
				.wmc-cart-wrapper { right: 50px; top: <?php echo esc_html( (string) $wmc_offset ); ?>px; }
				.wmc-cart { right: 10px; }
			<?php elseif ( 'wmc-bottom-left' === $position ) : ?>
				.wmc-cart-wrapper { left: 50px; bottom: 100px; }
				.wmc-cart { left: 10px; }
				.wmc-content { position: fixed; bottom: 100px; top: unset; right: unset; }
			<?php elseif ( 'wmc-bottom-right' === $position ) : ?>
				.wmc-cart-wrapper { right: 50px; bottom: 100px; }
				.wmc-cart { right: 10px; }
				.wmc-content { position: fixed; bottom: 100px; top: unset; }
			<?php endif; ?>
		</style>
		<?php
	}

	/**
	 * Register admin menu.
	 */
	public function wmc_admin_menu() {
		add_menu_page(
			__( 'Minicart Options', 'woo-minicart' ),
			__( 'Woo Minicart', 'woo-minicart' ),
			'manage_options',
			'woo-minicart',
			array( $this, 'wmc_admin_menu_callback' ),
			'dashicons-admin-generic',
			59
		);
	}

	/**
	 * Admin page callback.
	 */
	public function wmc_admin_menu_callback() {
		$file = plugin_dir_path( __FILE__ ) . 'admin/wmc-admin.php';
		if ( file_exists( $file ) ) {
			include $file;
		}
	}
}