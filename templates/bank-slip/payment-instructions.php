<?php
/**
 * Bank Slip - Payment instructions.
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

	<span class="woocommerce-table woocommerce-table--order-details shop_table order_details">

		<div class="woocommerce-column woocommerce-column--1 pagseguro_internacional_bank_slip_barcode">

			<h3 class="woocommerce-column__title"><?php _e('Bank Slip Barcode', 'wc-pagseguro-internacional'); ?></h3>

			<div class="">

				<svg id="bank_slip_barcode" jsbarcode-format="code39" jsbarcode-value="<?php echo esc_html($bank_slip_barcode); ?>" jsbarcode-textmargin="0" jsbarcode-displayvalue="false" ></svg>

			</div>

		</div>

		<div class="woocommerce-column woocommerce-column--1 pagseguro_internacional_bank_slip_line_div">

			<input type="text" value="<?php echo esc_html($bank_slip_line); ?>" disabled="disabled" name="pagseguro_internacional_bank_slip_line" class="pagseguro_internacional_bank_slip_line_input"/>

			<button class="pagseguro_internacional_bank_slip_line_button" id="pagseguro_internacional_bank_slip_line_button" data-clipboard-text='<?php echo esc_html($bank_slip_line); ?>'><?php _e('Copy Code', 'wc-pagseguro-internacional'); ?></button><br />

		</div>

		<div class="woocommerce-column woocommerce-column--1 pagseguro_internacional_bank_slip_pay_div">

			<a class="button pagseguro_internacional_bank_slip_pay_button" href="<?php echo esc_url($bank_slip_url); ?>" target="_blank">

				<?php _e( 'Pay the bank slip', 'wc-pagseguro-internacional' ); ?>

			</a>

			<?php _e( 'Please click in the following button to view your bank slip.', 'wc-pagseguro-internacional' ); ?><br /><?php _e( 'You can print and pay it on your internet banking or in a lottery retailer.', 'wc-pagseguro-internacional' ); ?><br /><?php _e( 'After we receive the bank slip payment confirmation, your order will be processed.', 'wc-pagseguro-internacional' ); ?>

		</div>

	</span>

</div>
