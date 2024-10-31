<?php
/**
 * Credit Card - Plain email instructions.
 *
 * @author  PagSeguro Internacional
 * @package PagSeguro_Internacional__Woocommerce/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

_e( 'Payment', 'wc-pagseguro-internacional' );

echo esc_html("\n\n");

echo esc_html(sprintf( __( 'Payment successfully made using credit card in %s.', 'wc-pagseguro-internacional' ), $installments . 'x' ));

echo esc_html("\n\n****************************************************\n\n");
