<?php

if (!defined('ABSPATH')) {

	exit;

} // end if;

/**
 * PagSeguro Internacional Payment PIX Gateway class.
 *
 * Extended by individual payment gateways to handle payments.
 *
 * @class   WC_PagSeguro_Internacional__Pix_Gateway
 * @extends WC_Payment_Gateway
 * @version 2.0.0
 * @author  PagSeguro Internacional
 */
class WC_PagSeguro_Internacional__Pix_Gateway extends WC_Payment_Gateway {

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {

		global $woocommerce;

		$this->id                   = 'pagseguro-internacional-pix';
		$this->icon                 = apply_filters('pagseguro_internacional_woocommerce_redirect_icon', '');
		$this->method_title         = __('PagSeguro Internacional - PIX – Hosted Checkout ', 'wc-pagseguro-internacional' );
		$this->method_description   = __('Accept PIX in Brazil on PagSeguro Internacional’s payment page.', 'wc-pagseguro-internacional' );
		$this->has_fields           = true;
		$this->view_transaction_url = 'https://billing-partner.boacompra.com/transactions_test.php/%s';
		$this->supports             = array(
			'products',
			'refunds'
		);

		/**
		 * Load the form fields.
		 */
		$this->init_form_fields();

		/**
		 * Load the settings.
		 */
		$this->init_settings();

		/**
		 * Options.
		 */
		$this->title            = $this->get_option('title');
		$this->description      = $this->get_option('description');
		$this->merchant_id      = $this->get_option('merchantid');
		$this->secret_key       = $this->get_option('secretkey');
		$this->prefix           = $this->get_option('invoice_prefix', 'wc');
		$this->sandbox          = $this->get_option('environment', 'no');
		$this->debug            = $this->get_option('debug');

		/**
		 * Active logs.
		 */
		if ($this->debug === 'yes') {

			if (class_exists('WC_Logger')) {

				$this->log = new WC_Logger();

			} else {

				$this->log = $woocommerce->logger();

			} // end if;

		} // end if;

		$this->api = new WC_PagSeguro_Internacional__API($this, 'pix', $this->sandbox, $this->prefix);

		/**
		 * Actions
		 */
		add_action('woocommerce_api_wc_pagseguro_internacional_pix_gateway', array($this, 'notification_handler'));

		add_action('woocommerce_api_wc_pagseguro_internacional_hosted_pix_request', array($this, 'redirect_checkout'));

		add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

		add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));

	} // end __construct;

	/**
	 * Returns a value indicating the the Gateway is available or not.
	 *
	 * @return bool
	 */
	public function is_available() {

		// Test if is valid for use.
		$api = !empty($this->merchant_id) && !empty( $this->secret_key);

		$available = parent::is_available() && $api;

		return $available;

	} // end is_available;

	/**
	 * Initialise Gateway Settings Form Fields.
	 *
	 * @return void.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'         => array(
				'title'   => __('Enable/Disable', 'wc-pagseguro-internacional'),
				'type'    => 'checkbox',
				'label'   => __('Enable PIX payments with PagSeguro Internacional', 'wc-pagseguro-internacional'),
				'default' => 'no'
			),
			'title'           => array(
				'title'       => __('Title', 'wc-pagseguro-internacional'),
				'type'        => 'text',
				'description' => __('Payment method title seen on the checkout page.', 'wc-pagseguro-internacional'),
				'default'     => __('PIX', 'wc-pagseguro-internacional')
			),
			'description'     => array(
				'title'       => __('Description', 'wc-pagseguro-internacional' ),
				'type'        => 'textarea',
				'description' => __('Payment method description seen on the checkout page.', 'wc-pagseguro-internacional'),
				'default'     => __('Confirm your order to be redirected to the payment page.', 'wc-pagseguro-internacional')
			),
			'integration'    => array(
				'title'       => __('Integration settings', 'wc-pagseguro-internacional'),
				'type'        => 'title',
				'description' => ''
			),
			'merchantid'      => array(
				'title'             => __('MerchantID', 'wc-pagseguro-internacional'),
				'type'              => 'text',
				'description'       => sprintf(__( 'Your PagSeguro Internacional account\'s unique store ID, found in %s.', 'wc-pagseguro-internacional' ), '<a href="https://myaccount.boacompra.com/" target="_blank">' . __( 'My Account', 'wc-pagseguro-internacional' ) . '</a>'),
				'default'           => '',
				'custom_attributes' => array(
					'required' => 'required'
				)
			),
			'secretkey'      => array(
				'title'             => __('SecretKey', 'wc-pagseguro-internacional'),
				'type'              => 'text',
				'description'       => sprintf(__('SecretKey can be found/created in %s.', 'wc-pagseguro-internacional' ), '<a href="https://myaccount.boacompra.com/" target="_blank">' . __( 'My Account', 'wc-pagseguro-internacional' ) . '</a>'),
				'default'           => '',
				'custom_attributes' => array(
					'required' => 'required'
				)
			),
			'behavior'       => array(
				'title'       => __( 'Integration behavior', 'wc-pagseguro-internacional' ),
				'type'        => 'title',
				'description' => ''
			),
			'invoice_prefix' => array(
				'title'       => __('Invoice Prefix', 'wc-pagseguro-internacional'),
				'type'        => 'text',
				'description' => __('Please enter a prefix for your invoice code.', 'wc-pagseguro-internacional'),
				'placeholder' => '',
				'default'     => 'WC',
				'placeholder' => 'WC',
			),
			'environment'    => array(
				'title'       => __( 'PagSeguro Internacional sandbox', 'wc-pagseguro-internacional' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable PagSeguro Internacional sandbox', 'wc-pagseguro-internacional' ),
				'default'     => 'no',
				'description' => __( 'Used to test payments.', 'wc-pagseguro-internacional' )
			),
			'debug'         => array(
				'title'       => __( 'Debugging', 'wc-pagseguro-internacional' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'wc-pagseguro-internacional' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Log PagSeguro Internacional events, such as API requests, for debugging purposes. The log can be found in %s.', 'wc-pagseguro-internacional' ), PagSeguro_Internacional__WooCommerce::get_log_view($this->id))
			)
		);

	} // end init_form_fields;

	/**
	 * Payment fields.
	 */
	public function payment_fields() {

		if ($description = $this->get_description()) {

			echo esc_html($description);

		} // end if;

		wc_get_template(
			'pix/checkout-instructions.php',
			array(),
			'woocommerce/boacompra/',
			PagSeguro_Internacional__WooCommerce::get_templates_path()
		);

	} // end payment_fields;

	/**
	 * Process the payment and return the result.
	 *
	 * @param  int $order_id Order ID.
	 * @return array Redirect.
	 */
	public function process_payment($order_id) {

		return $this->api->process_payment($order_id);

	} // end process_payment;

	/**
	 * Thank You page message.
	 *
	 * @param  int $order_id Order ID.
	 * @return void.
	 */
	public function thankyou_page($order_id) {

		wc_get_template(
			'pix/payment-instructions.php',
			array(),
			'woocommerce/boacompra/',
			PagSeguro_Internacional__WooCommerce::get_templates_path()
		);

	} // end thankyou_page;

	/**
	 * Handles the notification posts from PagSeguro Internacional Gateway.
	 *
	 * @return void
	 */
	public function notification_handler() {

		$this->api->notification_handler();

	} // end notification_handler;

	/**
	 * Process refund.
	 *
	 * @param  int        $order_id Order ID.
	 * @param  float|null $amount Refund amount.
	 * @param  string     $reason Refund reason.
	 * @return boolean True or false based on success, or a WP_Error object.
	 */
	public function process_refund($order_id, $amount = null, $reason = '') {

		return $this->api->process_refund($order_id, $amount);

	} // end process_refund;

	/**
	 * Auto redirect to the PagSeguro_Internacional_ checkout page.
	 *
	 * @return void
	 */
	public function redirect_checkout() {

		$order_id = wcbc_request('oid', '');

		$order = wc_get_order($order_id);

		if ($order) {

			$return = $this->api->do_hosted_request($order);

			echo esc_url($return);

			die();

		} // end if;

	} // end redirect_checkout;

} // end WC_PagSeguro_Internacional__Pix_Gateway;
