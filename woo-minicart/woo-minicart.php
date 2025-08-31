<?php
/**
 * Plugin Name:       Minicart for WooCommerce
 * Plugin URI:        https://eshaldevs.com/product/minicart-for-woocommerce-pro/
 * Description:       The simple plugin to add a Minicart to your WooCommerce website.
 * Version:           2.0.7
 * Author:            Ahmad Shyk
 * Author URI:        https://ahmadshyk.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-minicart
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin version.
 */
if ( ! defined( 'WOO_MINICART_VERSION' ) ) {
	define( 'WOO_MINICART_VERSION', '2.0.6' );
}

/**
 * Activation Hook.
 */
if ( ! function_exists( 'wmc_activate' ) ) {
	function wmc_activate() {
		$default = array(
			'enable-minicart'   => 1,
			'minicart-icon'     => 'wmc-icon-1',
			'minicart-position' => 'wmc-top-right',
			'wmc-offset'        => 150,
		);

		add_option( 'wmc_options', $default, '', 'yes' );
	}
}
register_activation_hook( __FILE__, 'wmc_activate' );

/**
 * Deactivation Hook.
 */
if ( ! function_exists( 'wmc_deactivate' ) ) {
	function wmc_deactivate() {
		// Intentionally left blank.
	}
}
register_deactivation_hook( __FILE__, 'wmc_deactivate' );

/**
 * Admin notice if WooCommerce is not active.
 */
if ( ! function_exists( 'wmc_no_woocommerce' ) ) {
	function wmc_no_woocommerce() { ?>
		<div class="notice notice-error">
			<p><?php esc_html_e( 'Minicart for WooCommerce is activated but not effective. It requires WooCommerce to work.', 'woo-minicart' ); ?></p>
		</div>
		<?php
	}
}

/**
 * Load plugin textdomain.
 */
if ( ! function_exists( 'wmc_load_textdomain' ) ) {
	function wmc_load_textdomain() {
		load_plugin_textdomain(
			'woo-minicart',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);
	}
}
add_action( 'init', 'wmc_load_textdomain' );

/**
 * Bootstrap: require main class if WooCommerce is active, else show notice.
 */
$wmc_active_plugins = (array) apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) );

if ( in_array( 'woocommerce/woocommerce.php', $wmc_active_plugins, true ) ) {
	require plugin_dir_path( __FILE__ ) . 'class-woo-minicart.php';
	if ( class_exists( 'WMC_Main_Class' ) ) {
		new WMC_Main_Class();
	}
} else {
	add_action( 'admin_notices', 'wmc_no_woocommerce' );
	unset( $wmc_active_plugins );
}

/**
 * Add "Settings" action link on the Plugins screen.
 *
 * @param string[] $links Existing action links.
 * @return string[]
 */
if ( ! function_exists( 'wmc_settings_link' ) ) {
	function wmc_settings_link( $links ) {
		$url           = admin_url( 'admin.php?page=woo-minicart' );
		$settings_link = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', 'woo-minicart' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
}

add_filter(
	'plugin_action_links_' . plugin_basename( __FILE__ ),
	'wmc_settings_link'
);