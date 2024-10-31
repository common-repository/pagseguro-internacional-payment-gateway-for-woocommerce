(function( $ ) {
	'use strict';

	$( function() {

		let pagseguro_internacional_barcode = $('#bank_slip_barcode');

		if (pagseguro_internacional_barcode.length) {

			JsBarcode('#bank_slip_barcode', pagseguro_internacional_bank_slip.bank_slip_barcode, {
				fontSize: 40,
				background: "#000000",
				lineColor: "#ffffff",
				margin: 40,
			}).init();

		  $('#bank_slip_barcode').css('max-width', '100%');

		} // end if;

	});

}( jQuery ));
