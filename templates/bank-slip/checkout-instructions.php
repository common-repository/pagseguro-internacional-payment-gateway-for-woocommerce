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

<div id="pagseguro-internacional-bank-slip-instructions">

	<p>

		<?php echo __( 'After clicking on "Place order", you will have access to the bank slip, which you can print and pay on your internet banking or in a lottery retailer.', 'wc-pagseguro-internacional'); ?><br />

		<?php echo __( 'Note: The order will be confirmed only after the payment approval.', 'wc-pagseguro-internacional'); ?>

		<?php if ($tax_message && $tax_message === 'yes') : ?>

			<br />

			<strong><?php _e( 'Tax', 'wc-pagseguro-internacional' ); ?>:</strong> <?php _e('R$ 1,50 (rate applied to cover management risk costs of the payment method).', 'wc-pagseguro-internacional' ); ?>

		<?php endif; ?>

	</p>

</div>
