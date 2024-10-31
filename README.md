# PagSeguro International Payment Gateway for WooCommerce

## Compatibility

- Compatible with WooCommerce 5+;.
- Compatible with WordPress 5+;
- Tested and developed based on PagSeguro’s API;
- Compatible with PHP 5.4.x to 7;
- Mandatory use of SSL certificate with TLS 1.2 protocol;

## Additional Plugins

- For stores transacting in Brazil and using Direct API payment methods (credit cards, boleto, and e-wallets), it's mandatory to install Brazilian Market on WooCommerce to add in your WC store some extra fields end-users must fill in with information to local regulatory authorities.
- For stores transacting in different countries, we recommend installing WooCommerce - Country Based Payments to configure the methods displayed in each country from your WC Settings.
- To set up a multi-currency store, we recommend installing Multi-Currency for WooCommerce;
- To offer Recurring Payments through Boleto - Direct Checkout in Brazil, you should install Woocommerce Subscriptions. More details about the integration with our plugin to follow in this document.

## Installation

By accessing the Plugins directory (wp-admin/plugin-install), you can upload the plugin files or search for our plugin PagSeguro International Payment Gateway for WooCommerce.

If you choose to search, please write "PagSeguro International Payment Gateway for WooCommerce" in the box at the right-hand corner and click "Install now". In the plugins area of WordPress, activate the PagSeguro International Payment Gateway for WooCommerce module.

If you choose to upload our package, please use the "Add new plugin" tool (wp-admin/plugins.php). Then, in the plugins area of WordPress, activate the PagSeguro International for WooCommerce module.


## Settings

---
#### 1 - Activation

##### MerchantID & SecretKey

Getting your MerchantID & SecretKey is the first step to create a functional integration. After registering and formalizing your contract with PagSeguro, you will receive a SecretKey, which will be used to reference your account and validate the processed payments.

Your PagSeguro MerchantID & SecretKey can be found in MyAccount. With the data in hand, please access Payments in Woocommerce Settings (wp-admin/admin.php?page=wc-settings&tab=checkout), select the payment method you wish to use and configure the respective fields as indicated below.

WooCommerce -> Settings -> Payments ->

![image](https://user-images.githubusercontent.com/5360720/117220507-c0442080-addd-11eb-806f-082736a30ec7.png "Configuração - Ativação, MerchantID & SecretKey")

##### Sandbox

A sandbox is an isolated testing environment that enables users to run programs or execute files without affecting the actual application, system, or platform in which they run.

All payment methods on our plugin have a sandbox environment so you can test the integration.
To do this, you must activate the sandbox option of the desired method, as shown below:

WooCommerce -> Settings -> Payments -> Select the method and click ‘manage’ -> enable PagSeguro sandbox

![image](https://user-images.githubusercontent.com/5360720/117220603-ee296500-addd-11eb-90cd-9a9ec225203e.png "Configuração - Ativar Sandbox")

You can change the transaction status in PagSeguro's Sandbox environment through the page: https://billing-partner.boacompra.com/ - Please use the same email and password from MyAccount.

To find the transaction with the ID Order, you need to add the prefix configured in the "Invoice Prefix", concatenated with the Woocommerce order number.

Note: After testing the plugin, you should remember to disable the sandbox environment, otherwise your WooCommerce store won’t run our plugin.

##### Invoice Prefix

You can set a prefix to differentiate the ID of your PagSeguro invoices:

![image](https://user-images.githubusercontent.com/5360720/117226730-a9a4c600-adeb-11eb-8989-37ee149ceb50.png "Configuração do Boleto Bancário")

##### Logs

Enable the option for the module to record everything that is sent and received between your store and PagSeguro:

To view the logs, click the Logs link or go to "WooCommerce> System Status> Logs> select log pagseguro-payment-xxxxx.log" and click "View" to review details of what has been sent and received between your store and PagSeguro.

---

#### 2 - Payment Method

Our plugin provides a total of 5 payment method groups, distributed among 5 different gateways, all with their own individual settings.

You should also enable on ‘WWCBP’ the countries you want to accept the payments methods:


- CREDIT CARD - DIRECT CHECKOUT (ONLY FOR BRAZIL)

Customers pay with a credit card directly on your e-commerce checkout without redirections.

Set the maximum number of installments accepted by the store at Credit Card Direct Checkout Settings. Select between 1 and 12 installments.

Note: The interest rate may vary depending on the store's billing ceiling or your contractual negotiation with PagSeguro.

The installments are shown after entering the card number, since it depends on the card's flag to inform the installment rate.

- BOLETO BANCÁRIO - DIRECT CHECKOUT (ONLY FOR BRAZIL)

After clicking on "Place order", the customer is taken to the Thank You page with information about the boleto (Bank Slip Barcode).

By default, when issuing a Boleto Bancário, we show a message referring to the standard billing fee of R$1.50 for issuance.

If you do not want this message to appear at checkout, email and thank you page, just uncheck the option at Boleto Bancário Settings.

- E-WALLETS - DIRECT CHECKOUT (ONLY FOR BRAZIL)

The e-wallet options available are PagSeguro and PayPal. When enabling this payment method, both e-wallets appear to the end-user:

- PAGSEGURO PAYMENT PAGE

After clicking on "Place order", your customer will be redirected to PagSeguro Payment Page to choose among all the payment methods you have made available to him. PagSeguro handles all payment information collection, sensitive data protection, and transaction security.

To enable different payment groups, please access PagSeguro Payment Page Settings:

You can check all available payment methods for your country here.

- PAGSEGURO PAYMENT PAGE – PIX

After clicking on "Place order", your customer will be redirected to PagSeguro Payment Page, but only see the PIX option enabled:

---
#### 3 - Refunds

You can offer a partial or total refund to your end-user.

To make a refund request, please access the desired order at WooCommerce Orders and click on the "Refund" button. Then, set the "Refunded Total" and click the "Reimbursement R$X,XX by Payment via PagSeguro" button. The module will transmit the request to PagSeguro in real-time.

If the refund cannot be completed automatically, it means PagSeguro does not allow it to be refundable. Please check the methods that are refunded here.

#### 4 - Recurring Payments through Boleto Direct Checkout and Woocommerce Subscriptions

We are integrated with Woocommerce Subscriptions so you can offer recurring payments to your customer through Boleto Bancário in Brazil. If you want this extra service offered by WooCommerce, you should purchase and install Woocommerce Subscription, then process the payment with PagSeguro.

#### 5 - Translations

The language shown in the plugin is the one settled in WordPress General Settings. Our plugin supports English, Spanish, and Portuguese.

---

## CHANGELOG

**2.0.0**

- Complete refactoring of the plugin structure.
- Payment options into individual gateways.
- Added support for Boleto Bancário subscriptions.
- Added support for Multicurrency and Multilanguage.
- Added support for the PIX payment method.

**1.0.0**

- The first version of the PagSeguro module.
