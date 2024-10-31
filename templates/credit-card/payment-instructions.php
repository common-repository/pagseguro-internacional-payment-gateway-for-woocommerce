<?php
/**
 * Credit Card - Payment instructions.
 *
 * @author  PagSeguro Internacional
 * @package PagSeguro_Internacional__Woocommerce/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="woocommerce-message">
	<span><?php echo esc_html(sprintf( __( 'Payment successfully made using credit card in %s.', 'wc-pagseguro-internacional' ), '<strong>' . $installments . 'x</strong>' )); ?></span>
</div>
