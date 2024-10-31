<?php
/**
 * Bank Slip - Plain email instructions.
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

_e( 'Use the link below to view your bank slip. You can print and pay it on your internet banking or in a lottery retailer.', 'wc-pagseguro-internacional' );

echo esc_html("\n");

echo esc_url($bank_slip_url);

echo esc_html("\n");

_e( 'After we receive the bank slip payment confirmation, your order will be processed.', 'wc-pagseguro-internacional' );

echo esc_html("\n\n****************************************************\n\n");
