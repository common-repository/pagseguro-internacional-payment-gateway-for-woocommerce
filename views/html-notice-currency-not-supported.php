<?php
/**
 * Admin View: Notice - Currency not supported.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="error">
	<p><strong><?php _e( 'PagSeguro Internacional disabled', 'wc-pagseguro-internacional' ); ?></strong>: <?php printf( __( 'Currency <code>%s</code> is not supported. WooCommerce PagSeguro Internacional only works with Brazilian real (BRL).', 'wc-pagseguro-internacional' ), get_woocommerce_currency() ); ?>
	</p>
</div>
