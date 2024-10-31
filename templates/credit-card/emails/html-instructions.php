<?php
/**
 * Credit Card - HTML email instructions.
 *
 * @author  PagSeguro Internacional
 * @package PagSeguro_Internacional__Woocommerce/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<h2><?php _e( 'Payment', 'wc-pagseguro-internacional' ); ?></h2>

<p class="order_details"><?php echo esc_html(sprintf( __( 'Payment successfully made using credit card in %s.', 'wc-pagseguro-internacional' ), '<strong>' . $installments . 'x</strong>' )); ?></p>
