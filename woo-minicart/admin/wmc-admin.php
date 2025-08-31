<?php
/**
 * Admin settings page for Minicart for WooCommerce.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! current_user_can( 'activate_plugins' ) ) {
	wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'woo-minicart' ) );
}

/**
 * Save options.
 */
if ( ! empty( $_POST ) && check_admin_referer( 'wmc-afs', 'wmc-admin-nonce' ) ) {
	$posted = wp_unslash( $_POST );

	$data = array(
		'enable-minicart'   => isset( $posted['enable-minicart'] ) ? '1' : '0',
		'minicart-icon'     => isset( $posted['minicart-icon'] ) ? sanitize_text_field( $posted['minicart-icon'] ) : 'wmc-icon-1',
		'minicart-position' => isset( $posted['minicart-position'] ) ? sanitize_text_field( $posted['minicart-position'] ) : 'wmc-top-right',
		'wmc-offset'        => isset( $posted['wmc-offset'] ) ? absint( $posted['wmc-offset'] ) : 150,
	);

	update_option( 'wmc_options', $data );
}

$current_options = (array) get_option( 'wmc_options', array(
	'enable-minicart'   => '1',
	'minicart-icon'     => 'wmc-icon-1',
	'minicart-position' => 'wmc-top-right',
	'wmc-offset'        => 150,
) );

// Convenience vars.
$checked_enable       = ( isset( $current_options['enable-minicart'] ) && (int) $current_options['enable-minicart'] === 1 );
$current_icon         = isset( $current_options['minicart-icon'] ) ? $current_options['minicart-icon'] : 'wmc-icon-1';
$current_position     = isset( $current_options['minicart-position'] ) ? $current_options['minicart-position'] : 'wmc-top-right';
$current_offset       = isset( $current_options['wmc-offset'] ) ? (int) $current_options['wmc-offset'] : 150;
$graphics_base        = trailingslashit( plugin_dir_url( dirname( __FILE__ ) ) ) . 'assets/graphics/';
$positions            = array(
	'wmc-top-left'     => __( 'Top Left', 'woo-minicart' ),
	'wmc-top-right'    => __( 'Top Right', 'woo-minicart' ),
	'wmc-bottom-left'  => __( 'Bottom Left', 'woo-minicart' ),
	'wmc-bottom-right' => __( 'Bottom Right', 'woo-minicart' ),
);
$icon_files           = array(
	'wmc-icon-1' => 'wmc-icon-1.png',
	'wmc-icon-2' => 'wmc-icon-2.png',
	'wmc-icon-3' => 'wmc-icon-3.png',
	'wmc-icon-4' => 'wmc-icon-4.png',
	'wmc-icon-5' => 'wmc-icon-5.png',
);
?>
<div class="wrap wmc-wrap">
	<h1 class="hidden-h1"></h1>

	<?php if ( isset( $_POST['wmc_option_submit'] ) ) : ?>
		<div class="notice notice-success"><p><strong><?php esc_html_e( 'Settings saved.', 'woo-minicart' ); ?></strong></p></div>
	<?php endif; ?>

	<div class="wmc-admin-page-title">
		<h1 class="wmc-admin-title"><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<span class="wmc-version"><?php echo esc_html( $this->plugin_version ); ?></span>
	</div>

	<form method="post" class="options-form">
		<?php wp_nonce_field( 'wmc-afs', 'wmc-admin-nonce' ); ?>

		<div class="block">
			<fieldset>
				<legend class="screen-reader-text"><span><?php esc_html_e( 'Enable Floating Minicart', 'woo-minicart' ); ?></span></legend>
				<label for="enable-minicart">
					<input id="enable-minicart" name="enable-minicart" type="checkbox" <?php checked( $checked_enable ); ?> />
					<span><?php esc_html_e( 'Enable Floating Minicart', 'woo-minicart' ); ?></span>
				</label>
			</fieldset>
		</div>

		<div class="block">
			<h3><?php esc_html_e( 'Floating Minicart Position', 'woo-minicart' ); ?></h3>
			<fieldset>
				<legend class="screen-reader-text"><span><?php esc_html_e( 'Minicart Position', 'woo-minicart' ); ?></span></legend>
				<?php foreach ( $positions as $value => $label ) : ?>
					<label>
						<input type="radio" name="minicart-position" value="<?php echo esc_attr( $value ); ?>" <?php checked( $current_position, $value ); ?> />
						<span><?php echo esc_html( $label ); ?></span>
					</label>
				<?php endforeach; ?>
			</fieldset>
		</div>

		<div class="block">
			<h3><?php esc_html_e( 'Offset', 'woo-minicart' ); ?></h3>
			<p><?php esc_html_e( 'Position from top, only applicable if Minicart position is either Top Left or Top Right.', 'woo-minicart' ); ?></p>
			<label for="wmc-offset">
				<input id="wmc-offset" type="number" name="wmc-offset" value="<?php echo esc_attr( (string) $current_offset ); ?>" /> px
			</label>
		</div>

		<h3><?php esc_html_e( 'Minicart Icon', 'woo-minicart' ); ?></h3>

		<div class="block">
			<fieldset>
				<legend class="screen-reader-text"><span><?php esc_html_e( 'Cart Icons', 'woo-minicart' ); ?></span></legend>

				<?php foreach ( $icon_files as $icon_key => $file ) : ?>
					<label>
						<input type="radio" name="minicart-icon" value="<?php echo esc_attr( $icon_key ); ?>" data-class="<?php echo esc_attr( str_replace( 'wmc-icon-', 'minicart-', $icon_key ) ); ?>" <?php checked( $current_icon, $icon_key ); ?> />
						<img class="<?php echo esc_attr( str_replace( 'wmc-icon-', 'minicart-', $icon_key ) ); ?> <?php echo esc_attr( $current_icon === $icon_key ? 'cart-active' : '' ); ?>"
							src="<?php echo esc_url( $graphics_base . $file ); ?>" alt="<?php echo esc_attr__( 'Mini Cart', 'woo-minicart' ); ?>" width="30" height="30">
					</label>
				<?php endforeach; ?>
			</fieldset>

			<br>

			<fieldset class="pro-only">
				<label>
					<strong><?php esc_html_e( 'Custom Cart Icon URL', 'woo-minicart' ); ?></strong>
					(<a href="https://eshaldevs.com/product/minicart-for-woocommerce-pro/" target="_blank" class="pro-only-link" rel="noopener noreferrer">Pro</a> <?php esc_html_e( 'only', 'woo-minicart' ); ?>)
					<input type="url" readonly="readonly">
				</label>
			</fieldset>
		</div>

		<div class="block">
			<h3><?php esc_html_e( 'Shortcode', 'woo-minicart' ); ?></h3>
			<p>
				<?php
				// translators: shortcode to render the minicart.
				echo wp_kses_post( sprintf( __( '<strong style="font-size:18px;">%s</strong> Use this shortcode to display minicart anywhere.', 'woo-minicart' ), '[woo-minicart]' ) );
				?>
			</p>
		</div>

		<div class="block wmc-styling">
			<h3><?php esc_html_e( 'Style', 'woo-minicart' ); ?></h3>

			<label class="pro-only">
				<span><strong><?php esc_html_e( 'Minicart Count Background color', 'woo-minicart' ); ?></strong> (<a href="https://eshaldevs.com/product/minicart-for-woocommerce-pro/" target="_blank" class="pro-only-link" rel="noopener noreferrer">Pro</a> <?php esc_html_e( 'only', 'woo-minicart' ); ?>)</span>
				<input type="text" readonly="readonly">
			</label><br>

			<label class="pro-only">
				<span><strong><?php esc_html_e( 'Minicart Count Text color', 'woo-minicart' ); ?></strong> (<a href="https://eshaldevs.com/product/minicart-for-woocommerce-pro/" target="_blank" class="pro-only-link" rel="noopener noreferrer">Pro</a> <?php esc_html_e( 'only', 'woo-minicart' ); ?>)</span>
				<input type="text" readonly="readonly">
			</label><br>

			<label class="pro-only">
				<span><strong><?php esc_html_e( 'Minicart Header Background Color', 'woo-minicart' ); ?></strong> (<a href="https://eshaldevs.com/product/minicart-for-woocommerce-pro/" target="_blank" class="pro-only-link" rel="noopener noreferrer">Pro</a> <?php esc_html_e( 'only', 'woo-minicart' ); ?>)</span>
				<input type="text" readonly="readonly">
			</label><br>

			<label class="pro-only">
				<span><strong><?php esc_html_e( 'Minicart Header Text Color', 'woo-minicart' ); ?></strong> (<a href="https://eshaldevs.com/product/minicart-for-woocommerce-pro/" target="_blank" class="pro-only-link" rel="noopener noreferrer">Pro</a> <?php esc_html_e( 'only', 'woo-minicart' ); ?>)</span>
				<input type="text" readonly="readonly">
			</label><br>

			<label class="pro-only">
				<span><strong><?php esc_html_e( 'Minicart View Cart Button Background Color', 'woo-minicart' ); ?></strong> (<a href="https://eshaldevs.com/product/minicart-for-woocommerce-pro/" target="_blank" class="pro-only-link" rel="noopener noreferrer">Pro</a> <?php esc_html_e( 'only', 'woo-minicart' ); ?>)</span>
				<input type="text" readonly="readonly">
			</label><br>

			<label class="pro-only">
				<span><strong><?php esc_html_e( 'Minicart View Cart Button Text Color', 'woo-minicart' ); ?></strong> (<a href="https://eshaldevs.com/product/minicart-for-woocommerce-pro/" target="_blank" class="pro-only-link" rel="noopener noreferrer">Pro</a> <?php esc_html_e( 'only', 'woo-minicart' ); ?>)</span>
				<input type="text" readonly="readonly">
			</label><br>

			<label class="pro-only">
				<span><strong><?php esc_html_e( 'Minicart View Cart Button Hover Background Color', 'woo-minicart' ); ?></strong> (<a href="https://eshaldevs.com/product/minicart-for-woocommerce-pro/" target="_blank" class="pro-only-link" rel="noopener noreferrer">Pro</a> <?php esc_html_e( 'only', 'woo-minicart' ); ?>)</span>
				<input type="text" readonly="readonly">
			</label><br>

			<label class="pro-only">
				<span><strong><?php esc_html_e( 'Minicart View Cart Button Hover Text Color', 'woo-minicart' ); ?></strong> (<a href="https://eshaldevs.com/product/minicart-for-woocommerce-pro/" target="_blank" class="pro-only-link" rel="noopener noreferrer">Pro</a> <?php esc_html_e( 'only', 'woo-minicart' ); ?>)</span>
				<input type="text" readonly="readonly">
			</label><br>

			<label class="pro-only">
				<span><strong><?php esc_html_e( 'Minicart Checkout Button Background Color', 'woo-minicart' ); ?></strong> (<a href="https://eshaldevs.com/product/minicart-for-woocommerce-pro/" target="_blank" class="pro-only-link" rel="noopener noreferrer">Pro</a> <?php esc_html_e( 'only', 'woo-minicart' ); ?>)</span>
				<input type="text" readonly="readonly">
			</label><br>

			<label class="pro-only">
				<span><strong><?php esc_html_e( 'Minicart Checkout Button Text Color', 'woo-minicart' ); ?></strong> (<a href="https://eshaldevs.com/product/minicart-for-woocommerce-pro/" target="_blank" class="pro-only-link" rel="noopener noreferrer">Pro</a> <?php esc_html_e( 'only', 'woo-minicart' ); ?>)</span>
				<input type="text" readonly="readonly">
			</label><br>

			<label class="pro-only">
				<span><strong><?php esc_html_e( 'Minicart Checkout Button Hover Background Color', 'woo-minicart' ); ?></strong> (<a href="https://eshaldevs.com/product/minicart-for-woocommerce-pro/" target="_blank" class="pro-only-link" rel="noopener noreferrer">Pro</a> <?php esc_html_e( 'only', 'woo-minicart' ); ?>)</span>
				<input type="text" readonly="readonly">
			</label><br>

			<label class="pro-only">
				<span><strong><?php esc_html_e( 'Minicart Checkout Button Hover Text Color', 'woo-minicart' ); ?></strong> (<a href="https://eshaldevs.com/product/minicart-for-woocommerce-pro/" target="_blank" class="pro-only-link" rel="noopener noreferrer">Pro</a> <?php esc_html_e( 'only', 'woo-minicart' ); ?>)</span>
				<input type="text" readonly="readonly">
			</label>
		</div>

		<input class="button-primary wmc-submit" type="submit" name="wmc_option_submit" value="<?php echo esc_attr__( 'Save Changes', 'woo-minicart' ); ?>" />
	</form>

	<?php
	$pro_notice = __(
		'<h3><a class="pro-only-link" href="https://eshaldevs.com/product/minicart-for-woocommerce-pro/" target="_blank" rel="noopener noreferrer">Go Pro</a></h3><h4>What is included in Pro version?</h4><ol class="pro-details-list"><li>More Minicart Icons</li><li>Custom Minicart Icon</li><li>Custom Styling</li><li>Preferred Support</li><li>Lifetime Updates</li></ol><h2>Interested in Pro Version?</h2><h4><a href="https://eshaldevs.com/product/minicart-for-woocommerce-pro/" target="_blank" rel="noopener noreferrer">Click here</a> to get pro version now.</h4>',
		'woo-minicart'
	);
	?>
	<div class="pro-notice">
		<p><strong><?php esc_html_e( 'The plugin may need some CSS styling to adjust with your site on desktop as well as on mobile. I will do that for you without any additional cost with pro version. Just purchase pro version and send me email.', 'woo-minicart' ); ?></strong></p>

		<?php echo wp_kses_post( $pro_notice ); ?>

		<div class="review-request">
			<h3 style="margin-top: 50px;"><?php esc_html_e( 'Rate this Plugin', 'woo-minicart' ); ?></h3>
			<p><?php echo wp_kses_post( __( 'If you have a moment, I would very much appreciate if you could quickly rate the plugin on <a href="https://wordpress.org/support/plugin/woo-minicart/reviews/#new-post" target="_blank" rel="noopener noreferrer">wordpress.org</a>, just to help us spread the word.', 'woo-minicart' ) ); ?></p>
		</div>

		<h4 class="wmc-contact-info">
			<?php
			echo wp_kses_post(
				sprintf(
					/* translators: %s: support email address */
					__( 'In case of any problem, question, idea or any WordPress related work, reach me at <a href="mailto:%1$s">%1$s</a>', 'woo-minicart' ),
					'info@eshaldevs.com'
				)
			);
			?>
		</h4>
	</div>
</div>