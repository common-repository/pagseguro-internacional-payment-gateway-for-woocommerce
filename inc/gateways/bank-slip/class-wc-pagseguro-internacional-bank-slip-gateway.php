<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PagSeguro Internacional Payment Bank Slip Gateway class.
 *
 * Extended by individual payment gateways to handle payments.
 *
 * @class   WC_PagSeguro_Internacional__Bank_Slip_Gateway
 * @extends WC_Payment_Gateway
 * @version 2.0.0
 * @author  PagSeguro Internacional
 */
class WC_PagSeguro_Internacional__Bank_Slip_Gateway extends WC_Payment_Gateway {

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {

		global $woocommerce;

		$this->id                   = 'pagseguro-internacional-bank-slip';
		$this->icon                 = apply_filters('pagseguro_internacional_woocommerce_bank_slip_icon', '');
		$this->method_title         = __('PagSeguro Internacional - Boleto – Direct Checkout', 'wc-pagseguro-internacional' );
		$this->method_description   = __('Accept Boleto payments in Brazil without redirections.', 'wc-pagseguro-internacional' );
		$this->has_fields           = true;
		$this->view_transaction_url = 'https://billing-partner.boacompra.com/transactions_test.php/%s';
		$this->supports             = array(
			'subscriptions',
			'products',
			'subscription_cancellation',
			'subscription_reactivation',
			'subscription_suspension',
			'subscription_amount_changes',
			'subscription_payment_method_change',
			'subscription_payment_method_change_customer',
			'subscription_payment_method_change_admin',
			'subscription_date_changes',
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
		$this->tax_message      = $this->get_option('tax_message', 'no');
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

		$this->api = new WC_PagSeguro_Internacional__API($this, 'bank-slip', $this->sandbox, $this->prefix);

		/**
		 * Actions
		 */
		add_action('woocommerce_api_wc_pagseguro_internacional_bank_slip_gateway', array($this, 'notification_handler'));

		add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

		add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));

		add_action('woocommerce_email_after_order_table', array($this, 'email_instructions'), 10, 3);

		if ($this->settings['enabled'] === 'yes') {

			add_action('admin_notices', array($this, 'dependencies_notices'));

		} // end if;

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
	 * Dependecie notice.
	 *
	 * @return mixed.
	 */
	public function dependencies_notices() {

		if (!class_exists('Extra_Checkout_Fields_For_Brazil')) {

			require_once dirname(WC_PGI_PLUGIN_FILE) . '/views/html-notice-ecfb-missing.php';

		} // end if;

	} // end if;

	/**
	 * Initialise Gateway Settings Form Fields.
	 *
	 * @return void.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'     => array(
				'title'   => __('Enable/Disable', 'wc-pagseguro-internacional'),
				'type'    => 'checkbox',
				'label'   => __('Enable bank slip payments with PagSeguro Internacional', 'wc-pagseguro-internacional'),
				'default' => 'no'
			),
			'title' => array(
				'title'       => __('Title', 'wc-pagseguro-internacional'),
				'type'        => 'text',
				'description' => __('Payment method title seen on the checkout page.', 'wc-pagseguro-internacional'),
				'default'     => __('Bank Slip', 'wc-pagseguro-internacional' )
			),
			'description' => array(
				'title'       => __('Description', 'wc-pagseguro-internacional' ),
				'type'        => 'textarea',
				'description' => __('Payment method description seen on the checkout page.', 'wc-pagseguro-internacional'),
				'default'     => __('Pay with Bank Slip', 'wc-pagseguro-internacional')
			),
			'integration' => array(
				'title'       => __('Integration settings', 'wc-pagseguro-internacional'),
				'type'        => 'title',
				'description' => ''
			),
			'merchantid' => array(
				'title'             => __('MerchantID', 'wc-pagseguro-internacional'),
				'type'              => 'text',
				'description'       => sprintf(__('Your PagSeguro Internacional account\'s unique store ID, found in %s.', 'wc-pagseguro-internacional'), '<a href="https://myaccount.boacompra.com/" target="_blank">' . __('My Account', 'wc-pagseguro-internacional') . '</a>'),
				'default'           => '',
				'custom_attributes' => array(
					'required' => 'required'
				)
			),
			'secretkey' => array(
				'title'             => __('SecretKey', 'wc-pagseguro-internacional'),
				'type'              => 'text',
				'description'       => sprintf( __('SecretKey can be found/created in %s.', 'wc-pagseguro-internacional'), '<a href="https://myaccount.boacompra.com/" target="_blank">' . __('My Account', 'wc-pagseguro-internacional') . '</a>' ),
				'default'           => '',
				'custom_attributes' => array(
					'required' => 'required'
				)
			),
			'behavior' => array(
				'title'       => __( 'Integration behavior', 'wc-pagseguro-internacional' ),
				'type'        => 'title',
				'description' => ''
			),
			'tax_message'    => array(
				'title'   => __('Banking Ticket Tax Message', 'wc-pagseguro-internacional' ),
				'type'    => 'checkbox',
				'label'   => __('Display a message alerting the customer that will be charged R$ 1,50 for payment by Banking Ticket', 'wc-pagseguro-internacional'),
				'default' => 'yes',
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
				'title'       => __( 'PagSeguro Internacional sandbox', 'wc-pagseguro-internacional' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable PagSeguro Internacional sandbox', 'wc-pagseguro-internacional' ),
				'default'     => 'no',
				'description' => __( 'Used to test payments.', 'wc-pagseguro-internacional' )
			),
			'debug' => array(
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
			'bank-slip/checkout-instructions.php',
			array(
				'tax_message' => $this->tax_message
			),
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

		$data = get_post_meta($order_id, '_pagseguro_internacional_wc_transaction_data', true);

		if (isset($data)) {

			wp_enqueue_style('wc-pagseguro-internacional-bank-slip-css', plugins_url('assets/css/bank-slip.css', PagSeguro_Internacional__WooCommerce::get_assets_path()));

			wp_enqueue_script('wc-pagseguro-internacional-barcode', plugins_url('assets/js/JsBarcode.all.min.js', PagSeguro_Internacional__WooCommerce::get_assets_path()) , array('jquery'), '3.11.3', true);

			wp_enqueue_script('wc-pagseguro-internacional-bank-slip', plugins_url('assets/js/bank-slip.js', PagSeguro_Internacional__WooCommerce::get_assets_path()), array('jquery', 'wc-pagseguro-internacional-barcode'), PagSeguro_Internacional__Woocommerce::CLIENT_VERSION, true);

			wp_localize_script('wc-pagseguro-internacional-bank-slip', 'pagseguro_internacional_bank_slip', array(
				'bank_slip_url'     => $data['payment-url'],
				'bank_slip_barcode' => $data['barcode-number'],
				'bank_slip_line'    => $data['digitable-line']
			));

			wc_get_template(
				'bank-slip/payment-instructions.php',
				array(
					'bank_slip_url'     => $data['payment-url'],
					'bank_slip_barcode' => $data['barcode-number'],
					'bank_slip_line'    => $data['digitable-line']
				),
				'woocommerce/boacompra/',
				PagSeguro_Internacional__WooCommerce::get_templates_path()
			);

		} // end if;

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
					'bank-slip/emails/plain-instructions.php',
					array(
						'bank_slip_url' => $data['payment-url'],
						'tax_message'   => $this->tax_message
					),
					'woocommerce/boacompra/',
					PagSeguro_Internacional__WooCommerce::get_templates_path()
				);

			} else {

				wc_get_template(
					'bank-slip/emails/html-instructions.php',
					array(
						'bank_slip_url' => $data['payment-url'],
						'tax_message'   => $this->tax_message
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

} // end WC_PagSeguro_Internacional__Bank_Slip_Gateway;
