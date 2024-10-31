<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PagSeguro Internacional Payment E Wallet Gateway class.
 *
 * Extended by individual payment gateways to handle payments.
 *
 * @class   WC_PagSeguro_Internacional__E_Wallet_Gateway
 * @extends WC_Payment_Gateway
 * @version 2.0.0
 * @author  PagSeguro Internacional
 */
class WC_PagSeguro_Internacional__E_Wallet_Gateway extends WC_Payment_Gateway {

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {

		global $woocommerce;

		$this->id                   = 'pagseguro-internacional-e-wallet';
		$this->icon                 = apply_filters('pagseguro_internacional_woocommerce_e_wallet_icon', '');
		$this->method_title         = __('PagSeguro Internacional - e-Wallet – Direct Checkout', 'wc-pagseguro-internacional' );
		$this->method_description   = __('Accept e-wallet payments in Brazil without redirections.', 'wc-pagseguro-internacional' );
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
		$this->ignore_due_email = $this->get_option('ignore_due_email');
		$this->deadline         = $this->get_option('deadline');
		$this->send_only_total  = $this->get_option('send_only_total', 'no');
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

		$this->api = new WC_PagSeguro_Internacional__API($this, 'e-wallet', $this->sandbox, $this->prefix);

		/**
		 * Actions
		 */
		add_action('woocommerce_api_wc_pagseguro_internacional_e_wallet_gateway', array($this, 'notification_handler'));

		add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

		add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));

		//add_action('woocommerce_email_after_order_table', array($this, 'email_instructions'), 10, 3);

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
			'enabled'     => array(
				'title'   => __( 'Enable/Disable', 'wc-pagseguro-internacional' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable e-wallet payments with PagSeguro Internacional', 'wc-pagseguro-internacional' ),
				'default' => 'no'
			),
			'title' => array(
				'title'       => __('Title', 'wc-pagseguro-internacional'),
				'type'        => 'text',
				'description' => __( 'Payment method title seen on the checkout page.', 'wc-pagseguro-internacional'),
				'default'     => __( 'PagSeguro Internacional Wallets', 'wc-pagseguro-internacional' )
			),
			'description' => array(
				'title'       => __('Description', 'wc-pagseguro-internacional' ),
				'type'        => 'textarea',
				'description' => __( 'Payment method description seen on the checkout page.', 'wc-pagseguro-internacional'),
				'default'     => __( 'Pay with e-wallets', 'wc-pagseguro-internacional')
			),
			'integration' => array(
				'title'       => __('Integration settings', 'wc-pagseguro-internacional'),
				'type'        => 'title',
				'description' => ''
			),
			'merchantid' => array(
				'title'             => __('MerchantID', 'wc-pagseguro-internacional'),
				'type'              => 'text',
				'description'       => sprintf(__('Your PagSeguro Internacional account\'s unique store ID, found in %s.', 'wc-pagseguro-internacional' ), '<a href="https://myaccount.boacompra.com/" target="_blank">' . __( 'My Account', 'wc-pagseguro-internacional' ) . '</a>'),
				'default'           => '',
				'custom_attributes' => array(
					'required' => 'required'
				)
			),
			'secretkey' => array(
				'title'             => __('SecretKey', 'wc-pagseguro-internacional'),
				'type'              => 'text',
				'description'       => sprintf(__('SecretKey can be found/created in %s.', 'wc-pagseguro-internacional'), '<a href="https://myaccount.boacompra.com/" target="_blank">' . __('My Account', 'wc-pagseguro-internacional') . '</a>'),
				'default'           => '',
				'custom_attributes' => array(
					'required' => 'required'
				)
			),
			'behavior' => array(
				'title'       => __('Integration behavior', 'wc-pagseguro-internacional'),
				'type'        => 'title',
				'description' => ''
			),
			'invoice_prefix' => array(
				'title'       => __('Invoice Prefix', 'wc-pagseguro-internacional'),
				'type'        => 'text',
				'description'        => __('Please enter a prefix for your invoice code.', 'wc-pagseguro-internacional'),
				'placeholder' => '',
				'default'     => 'WC',
				'placeholder' => 'WC',
			),
			'environment' => array(
				'title'       => __('PagSeguro Internacional sandbox', 'wc-pagseguro-internacional'),
				'type'        => 'checkbox',
				'label'       => __('Enable PagSeguro Internacional sandbox', 'wc-pagseguro-internacional'),
				'default'     => 'no',
				'description' => __('Used to test payments.', 'wc-pagseguro-internacional')
			),
			'debug' => array(
				'title'       => __('Debugging', 'wc-pagseguro-internacional'),
				'type'        => 'checkbox',
				'label'       => __('Enable logging', 'wc-pagseguro-internacional'),
				'default'     => 'no',
				'description' => sprintf(__( 'Log PagSeguro Internacional events, such as API requests, for debugging purposes. The log can be found in %s.', 'wc-pagseguro-internacional' ), PagSeguro_Internacional__WooCommerce::get_log_view($this->id))
			)
		);

	} // end init_form_fields;

	/**
	 * Payment Fields.
	 *
	 * @return void
	 */
	public function payment_fields() {

		wp_enqueue_style('wc-pagseguro-internacional-e-wallet-css', plugins_url('assets/css/e-wallet.css', PagSeguro_Internacional__WooCommerce::get_assets_path()));

		wp_enqueue_script('wc-pagseguro-internacional-e-wallet-js', plugins_url('assets/js/e-wallet.js', PagSeguro_Internacional__WooCommerce::get_assets_path()), array('jquery'), PagSeguro_Internacional__Woocommerce::CLIENT_VERSION, true);

		wc_get_template(
			'e-wallet/payment-form.php',
			array(
				'wallets' => array(
					'pagseguro' => array(
						'title' => __('PagSeguro', 'wc-pagseguro-internacional'),
						'img'   => PagSeguro_Internacional__WooCommerce::get_assets_path(true) . '/img/pagseguro.png'
					),
					'paypal'    => array(
						'title' => __('PayPal', 'wc-pagseguro-internacional'),
						'img'   => PagSeguro_Internacional__WooCommerce::get_assets_path(true) . '/img/paypal.png'
					),
				)
			),
			'woocommerce/boacompra/',
			PagSeguro_Internacional__Woocommerce::get_templates_path()
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
			'e-wallet/payment-instructions.php',
			array(),
			'woocommerce/boacompra/',
			PagSeguro_Internacional__WooCommerce::get_templates_path()
		);

	} // end thankyou_page;


	/**
	 * Add content to the WC emails.
	 *
	 * @param  object $order         Order object.
	 * @param  bool   $sent_to_admin Send to admin.
	 * @param  bool   $plain_text    Plain text or HTML.
	 * @return string                Payment instructions.
	 */
	public function email_instructions($order, $sent_to_admin, $plain_text = false) {

		if ($sent_to_admin || ! in_array($order->get_status(), array('processing', 'on-hold')) || $this->id !== $order->get_payment_method()) {

			return;

		} // end if;

		$data = get_post_meta($order->get_id(), '_pagseguro_internacional_wc_transaction_data', true);

		if (isset($data['payment-url'])) {

			if ($plain_text) {

				wc_get_template(
					'e-wallet/emails/plain-instructions.php',
					array(
						'e_wallet_url' => $data['payment-url']
					),
					'woocommerce/boacompra/',
					PagSeguro_Internacional__WooCommerce::get_templates_path()
				);

			} else {

				wc_get_template(
					'e-wallet/emails/html-instructions.php',
					array(
						'e_wallet_url' => $data['payment-url']
					),
					'woocommerce/boacompra/',
					PagSeguro_Internacional__WooCommerce::get_templates_path()
				);

			} // end if;

		} // end if;

	} // end email_instructions;

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

		return new \WP_Error('error', __('Refunds for this payment method are not allowed.', 'wc-pagseguro-internacional'));

	} // end process_refund;

} // end WC_PagSeguro_Internacional__E_Wallet_Gateway;
