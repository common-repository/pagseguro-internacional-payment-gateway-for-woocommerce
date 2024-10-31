<?php
/**
 * E=Wallet - Checkout form.
 *
 * @author  PagSeguro Internacional
 * @package PagSeguro_Internacional__Woocommerce/Templates
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<fieldset id="pagseguro_internacional_wallets">

	<div id="pagseguro_internacional_wallets_div" class="form-row pagseguro-internacional-d-flex pagseguro-internacional-d-flex-inline">

			<?php foreach($wallets as $wallet_key => $wallet_value) : ?>

				<div class="">

					<label id="pagseguro_internacional_wallet_<?php echo esc_html($wallet_key); ?>_label" for='pagseguro_internacional_wallet_<?php echo esc_html($wallet_key); ?>'>

						<img id="pagseguro_internacional_wallet_<?php echo esc_html($wallet_key); ?>_img" class="wallet-options-img" src="<?php echo esc_html($wallet_value['img']); ?>">

						<input id="pagseguro_internacional_wallet_<?php echo esc_html($wallet_key); ?>" name="pagseguro_internacional_wallet" class='wallet-options-input' type='radio' value='<?php echo esc_html($wallet_key); ?>'/>

					</label>

				</div>

			<?php endforeach; ?>

	</div>

</fieldset>
