<?php
/**
 * Minicart shortcode template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bail if running in admin.
if ( is_admin() ) {
	return;
}

// Options.
$wmc_options   = (array) get_option( 'wmc_options', array() );
$minicart_icon = isset( $wmc_options['minicart-icon'] ) ? (string) $wmc_options['minicart-icon'] : 'wmc-icon-1';

// Resolve icon URL.
$base_url = trailingslashit( plugin_dir_url( dirname( __FILE__ ) ) );
$icons    = array(
	'wmc-icon-1' => 'assets/graphics/wmc-icon-1.png',
	'wmc-icon-2' => 'assets/graphics/wmc-icon-2.png',
	'wmc-icon-3' => 'assets/graphics/wmc-icon-3.png',
	'wmc-icon-4' => 'assets/graphics/wmc-icon-4.png',
	'wmc-icon-5' => 'assets/graphics/wmc-icon-5.png',
);

$icon_url = isset( $icons[ $minicart_icon ] ) ? $base_url . $icons[ $minicart_icon ] : $base_url . $icons['wmc-icon-1'];

if ( 'wmc-icon-custom' === $minicart_icon ) {
	$pro_opts = (array) get_option( 'wmc_pro_options', array() );
	if ( ! empty( $pro_opts['custom-cart-icon'] ) ) {
		$icon_url = esc_url( $pro_opts['custom-cart-icon'] );
	}
}

// Cart count (safe if WC not loaded yet).
$cart_count = ( function_exists( 'WC' ) && WC()->cart ) ? (int) WC()->cart->get_cart_contents_count() : 0;
?>
<div class="wmc-cart-wrapper shortcode-wrapper">
	<a class="wmc-cart" href="javascript:void(0)" aria-label="<?php echo esc_attr__( 'Open mini cart', 'woo-minicart' ); ?>">
		<img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr__( 'Mini Cart', 'woo-minicart' ); ?>" width="50" height="50">
		<span class="wmc-count"><?php echo esc_html( (string) $cart_count ); ?></span>
	</a>
	<?php
		$template = plugin_dir_path( __FILE__ ) . 'wmc-content.php';
		if ( file_exists( $template ) ) {
			include $template;
		}
	?>
</div>