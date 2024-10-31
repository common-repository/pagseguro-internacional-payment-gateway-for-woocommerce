<?php
/**
 * Plugin Name: PagSeguro Internacional Payment Gateway for WooCommerce
 * Description: PagSeguro Internacional Payment Gateway for WooCommerce is made for merchants looking for a boost on sales and conversions in Latin America, as well as Portugal, Spain and Turkey by accepting over 140 local payments options directly in your e-commerce.
 * Plugin URI: https://wordpress.org/plugins/pagseguro-internacional-payment-for-woocommerce/
 * Text Domain: wc-pagseguro-internacional
 * Version: 1.0.0
 * Author: PagSeguro Internacional
 * Author URI: https://pagseguro.com/
 * Network: true
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /lang
 * WC requires at least: 3.0.0
 * WC tested up to: 7.1
*/

if (!defined('ABSPATH')) {

	exit;

} // end if;

if (!class_exists('PagSeguro_Internacional__WooCommerce')) {

	/**
	 * WooCommerce PagSeguro Internacional main class.
	 */
	class PagSeguro_Internacional__WooCommerce {

		/**
		 * API Client Name.
		 *
		 * @var string
		 */

		const CLIENT_NAME = 'pagseguro-internacional';

		/**
		 * CLient Version.
		 */
		const CLIENT_VERSION = '2.0.0';

		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Initialize the plugin actions.
		 */
		public function __construct() {

			if (!defined('WC_PGI_PLUGIN_FILE')) {

				define('WC_PGI_PLUGIN_FILE', __FILE__ );

			} // end if;

			/**
			 * Load plugin text domain.
			 */
			add_action('init', array( $this, 'load_plugin_textdomain'));

			include_once 'inc/class-wc-pagseguro-internacional.php';

			new WC_PagSeguro_Internacional_();

		} // end __construct.

		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {

			/**
			 * If the single instance hasn't been set, set it now.
			 */
			if (null == self::$instance) {

				self::$instance = new self;

			} // end if;

			return self::$instance;

		} // end get_instance;

		/**
		 * Get templates path.
		 *
		 * @return string
		 */
		public static function get_templates_path() {

			return plugin_dir_path( __FILE__ ) . 'templates/';

		} // end get_templates_path;

		/**
		 * Get assets path.
		 *
		 * @param bool $url
		 * @return string
		 */
		public static function get_assets_path($url = false) {

			$path = plugin_dir_path( __FILE__ ) . 'assets/';

			if ($url) {

				$path = plugins_url( 'assets', __FILE__ );

			} // end if;

			return $path;

		} // end get_assets_path;

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @return void
		 */
		public function load_plugin_textdomain() {

			load_plugin_textdomain('wc-pagseguro-internacional', false, dirname(plugin_basename(__FILE__)) . '/lang/');

		} // load_plugin_textdomain;

		/**
		 * Get log.
		 *
		 * @return string
		 */
		public static function get_log_view($gateway_id) {

			if (defined('WC_VERSION') && version_compare(WC_VERSION, '2.2', '>=') ) {

				return '<a href="' . esc_url( admin_url( 'admin.php?page=wc-status&tab=logs&log_file=' . esc_attr($gateway_id) . '-' . sanitize_file_name( wp_hash( $gateway_id ) ) . '.log' ) ) . '">' . __( 'System status &gt; logs', 'wc-pagseguro-internacional' ) . '</a>';

			} // end if;

			return '<code>woocommerce/logs/' . esc_attr( $gateway_id ) . '-' . sanitize_file_name(wp_hash($gateway_id)) . '.txt</code>';

		} // end get_log_view;

	} // end WC_PagSeguro_Internacional_;

	add_action('plugins_loaded', array('PagSeguro_Internacional__WooCommerce','get_instance'));

} // end if;
