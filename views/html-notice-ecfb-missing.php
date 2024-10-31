<?php
/**
 * Admin View: Notice - WooCommerce Extra Checkout Fields for Brazil missing.
 */

if (!defined('ABSPATH')) {

	exit;

} // end if;

$plugin_slug = 'woocommerce-extra-checkout-fields-for-brazil';

if (current_user_can('install_plugins')) {

	$url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $plugin_slug), 'install-plugin_' . $plugin_slug);

} else {

	$url = 'http://wordpress.org/plugins/' . $plugin_slug;

} // end if;

?>

<div class="error">

	<p></strong><?php printf(__( 'WooCommerce PagSeguro Internacional requires the latest version of %s to work!', 'wc-pagseguro-internacional'), '<a href="' . esc_url( $url ) . '">' . __('WooCommerce Extra Checkout Fields for Brazil', 'wc-pagseguro-internacional') . '</a>' ); ?></p>
</div>
