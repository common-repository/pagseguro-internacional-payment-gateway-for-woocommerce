<?php

if (!defined( 'ABSPATH')) {

	exit;

} // end if;

/**
 * PagSeguro Internacional Payment Bank Slip Addons Gateway class.
 *
 * Integration with WooCommerce Subscriptions.
 *
 * @class   WC_PagSeguro_Internacional__Bank_Slip_Addons_Gateway
 * @extends WC_PagSeguro_Internacional__Bank_Slip_Gateway
 *
 * @version 2.0.0
 * @author  PagSeguro Internacional
 */
class WC_PagSeguro_Internacional__Bank_Slip_Addons_Gateway extends WC_PagSeguro_Internacional__Bank_Slip_Gateway {

	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct();

		if ( class_exists( 'WC_Subscriptions_Order' ) ) {

			add_action('woocommerce_scheduled_subscription_payment_' . $this->id, array($this, 'scheduled_subscription_payment'), 10, 2);

		} // end if;

		add_action('woocommerce_api_wc_pagseguro_internacional_bank_slip_addons_gateway', array( $this->api, 'notification_handler' ));

	} // end __construct;

	/**
	 * Process the payment.
	 *
	 * @param  int $order_id
	 * @return array
	 */
	public function process_payment($order_id) {

		/**
		 * Processing subscription.
		 */
		if ($this->api->order_contains_subscription($order_id)) {

			return $this->process_subscription($order_id);

		} else {

			return parent::process_payment($order_id);

		} // end if;

	} // end process_payment;

	/**
	 * Process the subscription.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Order $order
	 * @return array
	 */
	protected function process_subscription($order_id) {

		try {

			$order = new WC_Order( $order_id );

			$payment_response = $this->process_subscription_payment( $order, $order->get_total() );

			if ( isset( $payment_response ) && is_wp_error( $payment_response ) ) {
				throw new Exception( $payment_response->get_error_message() );
			} else {
				// Remove cart
				$this->api->empty_card();

				// Return thank you page redirect
				return array(
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order )
				);
			}

		} catch ( Exception $e ) {
			$this->api->add_error( '<strong>' . esc_attr( $this->title ) . '</strong>: ' . $e->getMessage() );

			return array(
				'result'   => 'fail',
				'redirect' => ''
			);
		}
	}

	/**
	 * Process subscription payment.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_order $order
	 * @param int      $amount (default: 0)
	 * @return bool|WP_Error
	 */
	public function process_subscription_payment($order = '', $amount = 0) {

		if (0 == $amount) {

			/**
			 * Payment Complete
			 */
			$order->payment_complete();

			return true;

		} // end if;

		if ($this->debug == 'yes') {

			$this->log->add($this->id, 'Processing a subscription payment for order ' . $order->get_order_number());

		} // end if;

		$charge = $this->api->process_payment($order);

		if (isset($charge['errors']) && !empty($charge['errors'])) {

			return new WP_Error( 'pagseguro_internacional_subscription_error', $charge['errors']);

		} // end if;

		update_post_meta($order->get_id(), '_pagseguro_internacional_wc_transaction_data', $charge['transactions']);

		update_post_meta($order->get_id(), '_pagseguro_internacional_wc_transaction_id', $charge['transactions']['code']);

		update_post_meta( $order->get_id(), __('PagSeguro Internacional Bank Slip: URL', 'wc-pagseguro-internacional'), $charge['transactions']['payment-url']);

		$order_note = __('PagSeguro Internacional: The customer generated a bank slip. Awaiting payment confirmation.', 'wc-pagseguro-internacional');

		if ($order->get_status() == 'pending') {

			$order->update_status( 'on-hold', $order_note);

		} else {

			$order->add_order_note($order_note);

		} // end if;

		return true;

	} // end process_subscription_payment;

	/**
	 * Scheduled subscription payment.
	 *
	 * @since 2.0.0
	 *
	 * @param float $amount_to_charge The amount to charge.
	 * @param WC_Order $renewal_order A WC_Order object created to record the renewal payment.
	 */
	public function scheduled_subscription_payment($amount_to_charge, $renewal_order) {

		$result = $this->process_subscription_payment($renewal_order, $amount_to_charge);

		if (is_wp_error($result) ) {

			$renewal_order->update_status( 'failed', $result->get_error_message() );

		} // end if;

	} // end scheduled_subscription_payment;

	/**
	 * Update subscription status.
	 *
	 * @since 2.0.0
	 *
	 * @param int    $order_id
	 * @param string $invoice_status
	 * @return bool
	 */
	protected function update_subscription_status($order_id, $invoice_status) {

		$order = new WC_Order($order_id);

		$invoice_status = strtolower($invoice_status);

		$order_updated = false;

		if ($invoice_status == 'paid' || $invoice_status == 'PAID') {

			$order->add_order_note( __( 'PagSeguro Internacional: Subscription paid successfully.', 'wc-pagseguro-internacional' ) );

			/**
			 * // Payment complete
			 */
			$order->payment_complete();

			$order_updated = true;

		} elseif ($invoice_status == 'pending' || $invoice_status == 'PENDING') {

			$order->add_order_note(__('PagSeguro Internacional: Subscription payment failed.', 'wc-pagseguro-internacional'));

			WC_Subscriptions_Manager::process_subscription_payment_failure_on_order($order);

			$order_updated = true;

		} // end if;

		/**
		 * Allow custom actions when update the order status.
		 */
		do_action( 'pagseguro_internacional_woocommerce_update_order_status', $order, $invoice_status, $order_updated );

	} // end update_subscription_status;

	/**
	 * Notification handler.
	 */
	public function notification_handler() {

		@ob_clean();

		if (isset($_REQUEST['transactions']) || isset($_REQUEST['transactions'])) {
			global $wpdb;

			header( 'HTTP/1.1 200 OK' );

			$invoice_id = sanitize_text_field( $_REQUEST['data']['id'] );
			$order_id   = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_transaction_id' AND meta_value = '%s'", $invoice_id ) );
			$order_id   = intval( $order_id );

			if ( $order_id ) {
				$invoice_status = $this->api->get_invoice_status( $invoice_id );

				if ( $invoice_status ) {
					if ( $this->api->order_contains_subscription( $order_id ) ) {
						$this->update_subscription_status( $order_id, $invoice_status );
						exit();
					} else {
						$this->api->update_order_status( $order_id, $invoice_status );
						exit();
					}
				}
			}
		}

		wp_die( __( 'The request failed!', 'wc-pagseguro-internacional' ), __( 'The request failed!', 'wc-pagseguro-internacional' ), array( 'response' => 200 ) );
	}
}
