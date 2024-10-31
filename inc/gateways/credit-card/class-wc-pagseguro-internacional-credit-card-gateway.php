<?php
if (!defined('ABSPATH')) {

	exit;

} // end if;

/**
 * PagSeguro Internacional Payment Credit Card Gateway class.
 *
 * Extended by individual payment gateways to handle payments.
 *
 * @class   PagSeguro_Internacional__Woocommerce_Credit_Card_Gateway
 * @extends WC_Payment_Gateway
 */
class WC_PagSeguro_Internacional__Credit_Card_Gateway extends WC_Payment_Gateway {

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {

		global $woocommerce;

		$this->id                   = 'pagseguro-internacional-credit-card';
		$this->icon                 = apply_filters('pagseguro_internacional_woocommerce_credit_card_icon', '');
		$this->method_title         = __('PagSeguro Internacional - Credit card – Direct Checkout', 'wc-pagseguro-internacional');
		$this->method_description   = __('Accept credit card payments in Brazil without redirections.', 'wc-pagseguro-internacional');
		$this->has_fields           = true;
		$this->view_transaction_url = 'https://billing-partner.boacompra.com/transactions_test.php/%s';
		$this->supports             = array(
			'subscriptions',
			'products',
			'subscription_cancellation',
			'subscription_reactivation',
			'subscription_suspension',
			'subscription_amount_changes',
			'subscription_payment_method_change', // Subscriptions 1.n compatibility.
			'subscription_payment_method_change_customer',
			'subscription_payment_method_change_admin',
			'subscription_date_changes',
			'multiple_subscriptions',
			'refunds',
		);

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Options.
		$this->title                = $this->get_option('title');
		$this->description          = $this->get_option('description');
		$this->merchant_id          = $this->get_option('merchantid');
		$this->secret_key           = $this->get_option('secretkey');
		$this->installments         = $this->get_option('max_installments');
		$this->send_only_total      = $this->get_option('send_only_total', 'no');
		$this->prefix               = $this->get_option('invoice_prefix', 'wc');
		$this->sandbox              = $this->get_option('environment', 'no');
		$this->debug                = $this->get_option('debug');

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

		$this->api = new WC_PagSeguro_Internacional__API($this, 'credit-card', $this->sandbox, $this->prefix);

		/**
		 * Actions
		 */

		add_action('wp_ajax_pagseguro_internacional_woocommerce_credit_card_token', array($this, 'credit_card_token'));

		add_action('woocommerce_api_wc_pagseguro_internacional_credit_card_gateway', array($this, 'notification_handler'));

		add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

		add_action('woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page'));

		add_action('woocommerce_email_after_order_table', array( $this, 'email_instructions'), 10, 3 );

		//add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'), 10 );

		add_action('woocommerce_api_wc_pagseguro_internacional_installments', array($this, 'get_pagseguro_internacional_installments'));

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
				'title'   => __('Enable/Disable', 'wc-pagseguro-internacional' ),
				'type'    => 'checkbox',
				'label'   => __('Enable credit card payments with PagSeguro Internacional', 'wc-pagseguro-internacional' ),
				'default' => 'no'
			),
			'title' => array(
				'title'       => __('Title', 'wc-pagseguro-internacional' ),
				'type'        => 'text',
				'description' => __('Payment method title seen on the checkout page.', 'wc-pagseguro-internacional' ),
				'default'     => __('Credit card', 'wc-pagseguro-internacional' )
			),
			'description' => array(
				'title'       => __('Description', 'wc-pagseguro-internacional'),
				'type'        => 'textarea',
				'description' => __('Payment method description seen on the checkout page.', 'wc-pagseguro-internacional'),
				'default'     => __('Pay with credit card', 'wc-pagseguro-internacional')
			),
			'integration' => array(
				'title'       => __('Integration settings', 'wc-pagseguro-internacional'),
				'type'        => 'title',
				'description' => ''
			),
			'merchantid' => array(
				'title'             => __('MerchantID', 'wc-pagseguro-internacional'),
				'type'              => 'text',
				'description'       => sprintf(__('Your PagSeguro Internacional account\'s unique store ID, found in %s.', 'wc-pagseguro-internacional' ), '<a href="https://myaccount.boacompra.com/" target="_blank">' . __('My Account', 'wc-pagseguro-internacional') . '</a>' ),
				'default'           => '',
				'custom_attributes' => array(
					'required' => 'required'
				)
			),
			'secretkey' => array(
				'title'             => __('SecretKey', 'wc-pagseguro-internacional'),
				'type'              => 'text',
				'description'       => sprintf(__('SecretKey can be found/created in %s.', 'wc-pagseguro-internacional' ), '<a href="https://myaccount.boacompra.com/" target="_blank">' . __( 'My Account', 'wc-pagseguro-internacional') . '</a>'),
				'default'           => '',
				'custom_attributes' => array(
					'required' => 'required'
				)
			),
			'payment' => array(
				'title'       => __('Payment options', 'wc-pagseguro-internacional' ),
				'type'        => 'title',
				'description' => ''
			),
			'max_installments' => array(
				'title'             => __('Installments limit', 'wc-pagseguro-internacional' ),
				'type'              => 'number',
				'description'       => __('The maximum number of installments allowed for credit card payments. This can\'t be greater than the setting allowed in your PagSeguro Internacional account.', 'wc-pagseguro-internacional' ),
				'default'           => '1',
				'custom_attributes' => array(
					'step' => '1',
					'min'  => '1',
					'max'  => '12'
				)
			),
			'behavior' => array(
				'title'       => __( 'Integration behavior', 'wc-pagseguro-internacional' ),
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
			'send_only_total' => array(
				'title'   => __( 'Send only the order total', 'wc-pagseguro-internacional' ),
				'type'    => 'checkbox',
				'label'   => __( 'When enabled, the customer only gets the order total, not the list of purchased items.', 'wc-pagseguro-internacional' ),
				'default' => 'no'
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
	 * Call plugin scripts in front-end.
	 *
	 * @return void
	 */
	public function frontend_scripts() {

		if (is_checkout() &&  $this->enabled == 'yes') {

			wp_enqueue_style('wc-pagseguro-internacional-credit-card-css', plugins_url('assets/css/credit-card.css', PagSeguro_Internacional__WooCommerce::get_assets_path()));

			wp_enqueue_script('wc-pagseguro-internacional-js', $this->api->get_js_url(), array(), null, true);

			wp_enqueue_script('wc-pagseguro-internacional-credit-card-js', plugins_url('assets/js/credit-card.js', PagSeguro_Internacional__WooCommerce::get_assets_path()), array('jquery'), PagSeguro_Internacional__Woocommerce::CLIENT_VERSION, true);

			wp_enqueue_script('wc-pagseguro-internacional-mask', plugins_url('assets/js/jquery.mask.js', PagSeguro_Internacional__WooCommerce::get_assets_path()), array('jquery'), '', true);

			wp_enqueue_script('wc-pagseguro-internacional-credit-card-js', plugins_url('assets/js/credit-card.js', PagSeguro_Internacional__WooCommerce::get_assets_path()), array('jquery', 'wc-pagseguro-internacional-mask', 'wc-pagseguro-internacional-credit-card-js', 'wc-pagseguro-internacional-js'), PagSeguro_Internacional__Woocommerce::CLIENT_VERSION, true);

		} // end if;

	} // end frontend_scripts;

	/**
	 * Payment Fields
	 *
	 * @return void
	 */
	public function payment_fields() {

		$this->frontend_scripts();

		wp_enqueue_script('wc-credit-card-form');

		if ($description = $this->get_description()) {

			echo esc_html($description);

		} // end if;

		/**
		 * Get order total.
		 */
		if (method_exists($this, 'get_order_total')) {

			$order_total = $this->get_order_total();

		} else {

			$order_total = $this->api->get_order_total();

		} // end if;

		wp_localize_script(
			'wc-pagseguro-internacional-credit-card-js',
			'pagseguro_internacional_wc_credit_card_params',
			array(
				'ajaxurl'     => get_site_url() . '/wc-api/wc_pagseguro_internacional_installments',
				'masks'       => array(
					'pagseguro_internacional_card_expiry' => '00/0000',
					'pagseguro_internacional_card_number' => '0000 0000 0000 0000',
					'pagseguro_internacional_card_cvv'    => '0000'
				),
				'order_total' => $order_total,
				'currency'    => '',
				'i18n'        => array(
					'total'     => __('Total', 'wc-pagseguro-internacional'),
				),
				'sandbox'     => $this->sandbox
			)
		);

		wc_get_template(
			'credit-card/payment-form.php',
			array(
				'order_total'          => $order_total,
				'max_installments'     => intval($this->installments),
			),
			'woocommerce/boacompra/',
			PagSeguro_Internacional__Woocommerce::get_templates_path()
		);

	} // end payment_fields;

	/**
	 * Process the payment and return the result.
	 *
	 * @param  int $order_id Order ID.
	 * @return array         Redirect.
	 */
	public function process_payment($order_id) {

		return $this->api->process_payment($order_id);

	} // end process_payment;

	/**
	 * Thank You page message.
	 *
	 * @param  int    $order_id Order ID.
	 *
	 * @return string
	 */
	public function thankyou_page($order_id) {

		$order = wc_get_order($order_id);

		$data = $order->get_meta('_pagseguro_internacional_wc_transaction_data');

		if (isset( $data['installments'] ) && $order->has_status( 'processing' ) ) {
			wc_get_template(
				'credit-card/payment-instructions.php',
				array(
					'installments' => $data['installments']
				),
				'woocommerce/boacompra/',
				PagSeguro_Internacional__Woocommerce::get_templates_path()
			);
		}
	}

	/**
	 * Add content to the WC emails.
	 *
	 * @param  object $order         Order object.
	 * @param  bool   $sent_to_admin Send to admin.
	 * @param  bool   $plain_text    Plain text or HTML.
	 *
	 * @return string                Payment instructions.
	 */
	public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		// WooCommerce 3.0 or later.
		if ( is_callable( array( $order, 'get_meta' ) ) ) {
			if ( $sent_to_admin || ! $order->has_status( array( 'processing', 'on-hold' ) ) || $this->id !== $order->get_payment_method() ) {
				return;
			}

			$data = $order->get_meta( '_pagseguro_internacional_wc_transaction_data' );
		} else {
			if ( $sent_to_admin || ! $order->has_status( array( 'processing', 'on-hold' ) ) || $this->id !== $order->get_payment_method() ) {
				return;
			}

			$data = get_post_meta( $order->get_id(), '_pagseguro_internacional_wc_transaction_data', true );
		}

		if ( isset( $data['installments'] ) ) {
			if ( $plain_text ) {
				wc_get_template(
					'credit-card/emails/plain-instructions.php',
					array(
						'installments' => $data['installments']
					),
					'woocommerce/boacompra/',
					PagSeguro_Internacional__Woocommerce::get_templates_path()
				);
			} else {
				wc_get_template(
					'credit-card/emails/html-instructions.php',
					array(
						'installments' => $data['installments']
					),
					'woocommerce/boacompra/',
					PagSeguro_Internacional__Woocommerce::get_templates_path()
				);
			}
		}
	}

	/**
	 * Handles the API Notification.
	 *
	 * @return void
	 */
	public function notification_handler() {

		$this->api->notification_handler();

	} // end notification_handler;

	/**
	 * Process refund.
	 *
	 * If the gateway declares 'refunds' support, this will allow it to refund.
	 * a passed in amount.
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
	 * Undocumented function
	 *
	 * @return void
	 */
	public function get_pagseguro_internacional_installments() {

		$bin = substr(wcbc_clean_input_values(wcbc_request('card_number', '')), 0, 6);

		$country = wcbc_request('country', wc_get_base_location()['country']);

		$amount = wcbc_request('amount', '');

		$amount = number_format($amount, 2, '.', '');

		$currency = get_woocommerce_currency();

		$payload = array(
			'bin'      => wcbc_clean_input_values($bin),
			'country'  => $country,
			'amount'   => $amount,
			'currency' => $currency,
		);

		return $this->api->get_pagseguro_internacional_installments($payload);

	} // end get_pagseguro_internacional_installments;

} // end WC_PagSeguro_Internacional__Credit_Card_Gateway;
