<?php
/**
 * Render the "Become a Merchant" CTA.
 *
 * @package OurPass
 */

$ourpasswc_setting_ourpass_onboarding_url = OURPASSWC_ONBOARDING_URL;

$cta_context = ! empty( $args['context'] ) ? $args['context'] : 'empty';

$cta_message = __('The first step to integrating OurPass Checkout with your WooCommerce store is to become a merchant with OurPass. If you already have a merchant account, you can enter your App Information in the App Info tab.');

if ( 'tab-ourpass_app_info' === $cta_context ) {
	$cta_message = __('The first step to integrating OurPass Checkout with your WooCommerce store is to become a merchant with OurPass. If you already have a merchant account, you can enter your App Information below.');
}
?>

<div class="ourpass-notice ourpass-notice-success">
	<h2><?php esc_html_e( 'Welcome to OurPass!'); ?></h2>
	<p><?php echo esc_html( $cta_message ); ?></p>
	<p>
		<a href="<?php echo esc_url( $ourpasswc_setting_ourpass_onboarding_url ); ?>" class="button button-primary" target="_blank" rel="noopener">
			<?php esc_html_e( 'Become a merchant on OurPass'); ?> →
		</a>
	</p>
</div>
