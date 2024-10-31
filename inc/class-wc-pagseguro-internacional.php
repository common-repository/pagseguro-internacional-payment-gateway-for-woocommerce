<?php
/**
 * WooCommerce PagSeguro Internacional main class.
 */
class WC_PagSeguro_Internacional_ {

	/**
	 * Initialize the plugin actions.
	 */
	public function __construct() {

		/**
		 * Checks with WooCommerce and WooCommerce Extra Checkout Fields for Brazil is installed.
		 */
		if (class_exists('WC_Payment_Gateway')) {

			$this->includes();

			add_filter('woocommerce_payment_gateways', array( $this, 'add_gateway'));

			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'plugin_action_links'));

		} else {

			add_action('admin_notices', array( $this, 'dependencies_notices'));

		} // end if;

	} // end __construct;


	/**
	 * Include the main files.
	 *
	 * @return void
	 */
	private function includes() {

		include_once 'functions/helpers.php';

		include_once 'class-wc-pagseguro-internacional-api.php';

		include_once 'gateways/bank-slip/class-wc-pagseguro-internacional-bank-slip-gateway.php';

		include_once 'gateways/credit-card/class-wc-pagseguro-internacional-credit-card-gateway.php';

    	include_once 'gateways/e-wallet/class-wc-pagseguro-internacional-e-wallet-gateway.php';

		include_once 'gateways/redirect/class-wc-pagseguro-internacional-redirect-gateway.php';

		include_once 'gateways/pix/class-wc-pagseguro-internacional-pix-gateway.php';

		include_once 'admin-pages/class-wc-pagseguro-internacional-my-account.php';

		if ( class_exists('WC_Subscriptions_Order')) {

      		//include_once 'inc/class-wc-pagseguro-internacional-bank-slip-addons-gateway.php';

      		//include_once 'inc/class-wc-pagseguro-internacional-credit-card-addons-gateway.php';

		} // end if;

	} // end includes;

	/**
	 * Add the gateway to WooCommerce.
	 *
	 * @param  array $methods WooCommerce payment methods.
	 * @return array Payment methods with PagSeguro Internacional.
	 */
	public function add_gateway($methods) {

		$methods[] = 'WC_PagSeguro_Internacional__Credit_Card_Gateway';

		$methods[] = 'WC_PagSeguro_Internacional__E_Wallet_Gateway';

		$methods[] = 'WC_PagSeguro_Internacional__Redirect_Gateway';

		$methods[] = 'WC_PagSeguro_Internacional__Pix_Gateway';

		if (class_exists('WC_Subscriptions_Order')) {

			$methods[] = 'WC_PagSeguro_Internacional__Bank_Slip_Addons_Gateway';

		} else {

			$methods[] = 'WC_PagSeguro_Internacional__Bank_Slip_Gateway';

		} // end if;

		return $methods;

	} // end add_gateway;

	/**
	 * Dependencies notices.
	 */
	public function dependencies_notices() {

		if (!class_exists( 'WC_Payment_Gateway')) {

			require_once dirname(WC_PGI_PLUGIN_FILE) . '/views/html-notice-woocommerce-missing.php';

		} // end if;

	} // end if;

	/**
	 * Action links.
	 *
	 * @param  array $links
	 *
	 * @return array
	 */
	public function plugin_action_links($links) {

			$plugin_links = array();

			if (defined('WC_VERSION') && version_compare(WC_VERSION, '2.1', '>=')) {

				$settings_url = admin_url('admin.php?page=wc-settings&tab=checkout&section=');

			} else {

				$settings_url = admin_url('admin.php?page=woocommerce_settings&tab=payment_gateways&section=');

			} // end if;

			if (class_exists('WC_Subscriptions_Order') || class_exists('WC_Pre_Orders_Order')) {

        $credit_card = 'wc_pagseguro_internacional_credit_card_addons_gateway';

        $bank_slip   = 'wc_pagseguro_internacional_bank_slip_addons_gateway';


			} else  {

				$credit_card = 'wc_pagseguro_internacional_credit_card_Gateway';

				$bank_slip   = 'wc_pagseguro_internacional_bank_slip_gateway';

			} // end if.

			$plugin_links[] = '<a href="' . esc_url( $settings_url . $credit_card ) . '">' . __( 'Credit card settings', 'wc-pagseguro-internacional' ) . '</a>';

			$plugin_links[] = '<a href="' . esc_url( $settings_url . $bank_slip ) . '">' . __( 'Bank slip settings', 'wc-pagseguro-internacional' ) . '</a>';

			return array_merge( $plugin_links, $links );

		} // end if;

} // end WC_PagSeguro_Internacional_;
