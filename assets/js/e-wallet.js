(function( $ ) {
	'use strict';

	$(function() {

    $('form.checkout').on('click', '#pagseguro_internacional_wallet_pagseguro_label', function() {

      $('#pagseguro_internacional_wallet_pagseguro_img').addClass('wallet-options-img-disable');

      $('#pagseguro_internacional_wallet_paypal_img').removeClass('wallet-options-img-disable');

		});

    $('form.checkout').on('click', '#pagseguro_internacional_wallet_paypal_label', function() {

      $('#pagseguro_internacional_wallet_paypal_img').addClass('wallet-options-img-disable');

      $('#pagseguro_internacional_wallet_pagseguro_img').removeClass('wallet-options-img-disable');

		});

  });

}( jQuery ));
