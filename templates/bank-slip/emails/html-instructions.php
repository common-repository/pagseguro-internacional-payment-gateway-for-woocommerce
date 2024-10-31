<?php
/**
 * Bank Slip - HTML email instructions.
 *
 * @author  PagSeguro Internacional
 * @package PagSeguro_Internacional__Woocommerce/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<h2><?php _e('Payment', 'wc-pagseguro-internacional'); ?></h2>

<p class="order_details">

	<?php if ($tax_message === 'yes') : ?>

		<br />

		<?php _e('Some payment methods may incur administrative fees.', 'wc-pagseguro-internacional' ); ?>

	<?php endif; ?>

	<?php _e('Use the link below to view your bank slip. You can print and pay it on your internet banking or in a lottery retailer.', 'wc-pagseguro-internacional'); ?>

	<br />

	<a class="button" href="<?php echo esc_url($bank_slip_url); ?>" target="_blank"><?php _e('Pay the bank slip', 'wc-pagseguro-internacional'); ?></a>

	<br />

	<?php _e('After we receive the bank slip payment confirmation, your order will be processed.', 'wc-pagseguro-internacional'); ?>

</p>
