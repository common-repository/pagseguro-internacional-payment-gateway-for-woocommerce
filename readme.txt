=== PagSeguro International Payment Gateway for WooCommerce ===
Contributors: pluginspagseguro, braising
Tags: woocommerce, pagseguro, payment, Pagseguro pagamentos, wc pagseguro, gateway de pagamento brasil, payment gateways
Requires at least: 6.0
Tested up to: 6.1
Requires PHP: 5.6
Stable tag: 5.6.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
PagSeguro International Payment Gateway for WooCommerce allows merchants to accept over 140 Latin American payment methods directly on your website, thus helping boost sales and conversions in the region.

Our features provide the best payment experience for your customers and include installments and refunds. They are all available by following a few installation steps and do not require technical knowledge.

Main advantages:
- Coverage: Our solution includes 17 countries in Latin America, including the main markets such as Brazil, Mexico, Peru, Colombia, Chile and Argentina. We also cover Portugal, Spain, Turkey, Greece and Romania.
- Local payment options: Over 140 payment methods are available by installing our plugin, including local credit/debit cards, cash-based payments (Boleto, OXXO, RapiPago and more), bank transfers, PIX in Brazil, and e-wallets.
- Integrations to fit your exact business demands: By selecting PagSeguro Payment Page, the customer will be redirected to PagSeguro’s payment page to complete a purchase. PagSeguro handles all payment information collection, sensitive data protection, and transaction security. By selecting a payment method with Direct Checkout (credit cards, boleto and e-wallets), you will have complete control over your payment page. Customers pay directly on your interface without redirections.
- Robust risk analysis: Reduce your risk of fraud and chargebacks with our machine learning system, built by a team of experts on the LATAM market.
- Easy set-up: All functionalities are available with a straightforward installation of our plugin.


Features available:
- Multicurrency and multilanguage: Our plugin supports multicurrency and multilingual plugins, so checkout will be adapted to display the selected language and currency chosen by the end-user.
- Installments: Offer customers the flexibility of splitting up payment purchases with the guarantee that your company still receives the money all at once, with no extra fees.
- Refunds API: We help avoid chargeback disputes by offering refunds in an integrated solution, where all the processes with the customer are done automatically. Our API supports total or partial refunds for processed payments.
- Recurring payments with Boleto: We are integrated with Woocommerce Subscriptions so you can offer recurring payments to your customers through Boleto Bancário in Brazil.
- Responsive checkout: Provide your clients with a user-friendly experience by using a checkout page adaptable to multiple devices and screen sizes.
- Sandbox environment for testing.
- Log and debug options.

About PagSeguro

PagSeguro (NYSE: PAGS) provides innovative payment solutions, automating payments, sales, and wire transfers to boost businesses anywhere, in a simple and secure way. Part of the UOL Group – the leader in Brazilian internet –, PagSeguro acts as an issuer, an acquirer, and a provider of digital accounts, besides offering complete solutions for online and in-person payments.

The company also has the most complete payment methods coverage in Brazil and 16 other Latin American countries, besides Portugal, Spain, Turkey, Greece, and Romania, allowing merchants worldwide to process and collect more than 140 local payment methods and local currencies. It also provides instant single or mass cross-border payouts to Brazil.

By partnering with PagSeguro, you’ll get the following benefits:
- No local entities required: One simple integration covers all markets without opening multiple bank accounts.
- No cross-border surcharges: All details on transactions and charges are available on our platform, so you don\'t have to worry about additional undisclosed fees.
- Local customer support 24/7: Our support team speaks your customers\' language (Portuguese, Spanish, English, and Turkish) and is available every day to ensure they are thoroughly looked after.
- Account management support: A dedicated regionalized account manager will be assigned to you so that we can ensure all your unique needs and business requirements are met.
- Robust risk analysis: Reduce your risk of fraud and chargebacks with our machine learning system, which we combine with a team full of experts on the LATAM market.

Security
All sensitive information will be tokenized and saved on PagSeguro’s environment. PagSeguro is PCI-DSS compliance certified, which means we follow global standards for security to assure trustworthy shopping experiences to your customers.

= Compatibility & Requirements =
- Compatible with WooCommerce 5+;
- Compatible with WordPress 5+;
- Tested and developed based on PagSeguro\'s API
- Compatible with PHP 5.4.x to 7;
- Mandatory use of SSL certificate with TLS 1.2 protocol;
- Pages must be served over HTTPS


= Further information =
You can reach out to one of our payments specialists by accessing our website.

= Start selling now =
1. Create an account: Access our website and one of our business executives will contact you directly.
2. Download and install our plugin: Your MerchantID and SecretKey should be available at your account. By following the step-by-step installation, you can set the plugin up by yourself.
3. Sell to Latin America and get paid anywhere in the world: Select which payment methods are available at checkout and start selling.


== Installation ==

= Compatibility =
- Compatible with WooCommerce 5+;
- Compatible with WordPress 5+;
- Tested and developed based on PagSeguro’s API
- Compatible with PHP 5.4.x to 7;
- Mandatory use of SSL certificate with TLS 1.2 protocol;


= Additional Plugins =

- For stores transacting in Brazil and using Direct API payment methods (credit cards, boleto, and e-wallets), it\'s mandatory to install Brazilian Market on WooCommerce to add in your WC store some extra fields end-users must fill in with information to local regulatory authorities.
- For stores transacting in different countries, we recommend installing WooCommerce - Country Based Payments to configure the methods displayed in each country from your WC Settings.
- To set up a multi-currency store, we recommend installing Multi-Currency for WooCommerce;
-  To offer Recurring Payments through Boleto - Direct Checkout in Brazil, you should install Woocommerce Subscriptions. More details about the integration with our plugin to follow in this document.


= Installation =

By accessing the Plugins directory (wp-admin/plugin-install), you can upload the plugin files or search for our plugin PagSeguro International Payment Gateway for WooCommerce.

If you choose to search, please write \"PagSeguro International Payment Gateway for WooCommerce\" in the box at the right-hand corner and click \"Install now\". In the plugins area of WordPress, activate the PagSeguro International Payment Gateway for WooCommerce module.

If you choose to upload our package, please use the \"Add new plugin\" tool (wp-admin/plugins.php). Then, in the plugins area of WordPress, activate the PagSeguro International for WooCommerce module.


= Settings =

= 1 - Activation =

- MerchantID & SecretKey
Getting your MerchantID & SecretKey is the first step to create a functional integration. After registering and formalizing your contract with PagSeguro, you will receive a SecretKey, which will be used to reference your account and validate the processed payments.

Your PagSeguro MerchantID & SecretKey can be found in MyAccount. With the data in hand, please access Payments in Woocommerce Settings (wp-admin/admin.php?page=wc-settings&tab=checkout), select the payment method you wish to use and configure the respective fields as indicated below.

WooCommerce -> Settings -> Payments ->


- Sandbox
A sandbox is an isolated testing environment that enables users to run programs or execute files without affecting the actual application, system, or platform in which they run.

All payment methods on our plugin have a sandbox environment so you can test the integration.
To do this, you must activate the sandbox option of the desired method, as shown below:

WooCommerce -> Settings -> Payments -> Select the method and click ‘manage’ -> enable PagSeguro sandbox

You can change the transaction status in PagSeguro\'s Sandbox environment through the page:
https://billing-partner.boacompra.com/ - Please use the same email and password from MyAccount.

 * To find the transaction with the ID Order, you need to add the prefix configured in the \"Invoice Prefix\", concatenated with the Woocommerce order number.

Note: After testing the plugin, you should remember to disable the sandbox environment, otherwise your WooCommerce store won’t run our plugin.


- Invoice Prefix
You can set a prefix to differentiate the ID of your PagSeguro invoices:


- Logs
Enable the option for the module to record everything that is sent and received between your store and PagSeguro:

To view the logs, click the Logs link or go to \"WooCommerce> System Status> Logs> select log pagseguro-payment-xxxxx.log\" and click \"View\" to review details of what has been sent and received between your store and PagSeguro.


= 2 – Payment Method =
Our plugin provides a total of 5 payment method groups, distributed among 5 different gateways, all with their own individual settings.

 You should also enable on ‘WWCBP’ the countries you want to accept the payments methods:


- CREDIT CARD - DIRECT CHECKOUT (ONLY FOR BRAZIL)
Customers pay with a credit card directly on your e-commerce checkout without redirections.

Set the maximum number of installments accepted by the store at Credit Card Direct Checkout Settings. Select between 1 and 12 installments.

Note: The interest rate may vary depending on the store\'s billing ceiling or your contractual negotiation with PagSeguro.

The installments are shown after entering the card number, since it depends on the card\'s flag to inform the installment rate.


- BOLETO BANCÁRIO - DIRECT CHECKOUT (ONLY FOR BRAZIL)
After clicking on \"Place order\", the customer is taken to the Thank You page with information about the boleto (Bank Slip Barcode).


By default, when issuing a Boleto Bancário, we show a message referring to the standard billing fee of R$1.50 for issuance.

If you do not want this message to appear at checkout, email and thank you page, just uncheck the option at Boleto Bancário Settings.


- E-WALLETS - DIRECT CHECKOUT (ONLY FOR BRAZIL)

The e-wallet options available are PagSeguro and PayPal. When enabling this payment method, both e-wallets appear to the end-user:


- PAGSEGURO PAYMENT PAGE

After clicking on \"Place order\", your customer will be redirected to PagSeguro Payment Page to choose among all the payment methods you have made available to him. PagSeguro handles all payment information collection, sensitive data protection, and transaction security.

To enable different payment groups, please access PagSeguro Payment Page Settings:

You can check all available payment methods for your country here.


- PAGSEGURO PAYMENT PAGE – PIX

After clicking on \"Place order\", your customer will be redirected to PagSeguro Payment Page, but only see the PIX option enabled:


= 3 - Refunds =
You can offer a partial or total refund to your end-user.
To make a refund request, please access the desired order at WooCommerce Orders and click on the \"Refund\" button. Then, set the \"Refunded Total\" and click the \"Reimbursement R$X,XX by Payment via PagSeguro\" button. The module will transmit the request to PagSeguro in real-time.
If the refund cannot be completed automatically, it means PagSeguro does not allow it to be refundable. Please check the methods that are refunded here.


= 4 – Recurring Payments through Boleto Direct Checkout and Woocommerce Subscriptions =
We are integrated with Woocommerce Subscriptions so you can offer recurring payments to your customer through Boleto Bancário in Brazil. If you want this extra service offered by WooCommerce, you should purchase and install Woocommerce Subscription, then process the payment with PagSeguro.


= 5 – Translations =
The language shown in the plugin is the one settled in WordPress General Settings. Our plugin supports English, Spanish, and Portuguese.


= CHANGELOG =
2.0.0
- Complete refactoring of the plugin structure.
- Payment options into individual gateways.
- Added support for Boleto Bancário subscriptions.
- Added support for Multicurrency and Multilanguage
- Added support for the PIX payment method
1.0.0
- The first version of the PagSeguro module.