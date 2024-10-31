<?php
/**
 * Credit Card - Checkout form.
 *
 * @author  PagSeguro Internacional
 * @package PagSeguro_Internacional__Woocommerce/Templates
 * @version 1.1.0
 */

if (!defined('ABSPATH')) {

	exit;

} // end if;
?>

<fieldset id="pagseguro_internacional_credit_card_fields">

	<input type="hidden" id="pagseguro_internacional_card_token" name="pagseguro_internacional_card_token" value=""/>

	<input type="hidden" id="pagseguro_internacional_card_brand" name="pagseguro_internacional_card_brand" value=""/>

	<div id="new-credit-card">

		<p class="form-row form-row-first pagseguro-internacional-credit-card-div">

			<label for="pagseguro_internacional_card_number">

				<?php _e('Card number', 'wc-pagseguro-internacional' ); ?> <span class="required">*</span>

			</label>

			<input id="pagseguro_internacional_card_number" class="input-text wc-credit-card-form-card-number" type="text" style="font-size: 1.5em; padding: 8px;" name="pagseguro_internacional_card_number" data-boacompra="number"/>

		</p>

		<p class="form-row form-row-last pagseguro-internacional-credit-card-div">

			<label for="pagseguro_internacional_card_holder_name">

				<?php _e('Name printed on card', 'wc-pagseguro-internacional'); ?> <span class="required">*</span>

			</label>

			<input id="pagseguro_internacional_card_holder_name" name="pagseguro_internacional_card_holder_name" class="input-text" type="text" autocomplete="off" style="font-size: 1.5em; padding: 8px;" data-boacompra="full_name" />

		</p>

		<div class="clear"></div>

		<p class="form-row form-row-first">

			<label for="pagseguro_internacional_card_expiry"><?php _e('Expiry date (MM/YYYY)', 'wc-pagseguro-internacional'); ?> <span class="required">*</span></label>

			<input id="pagseguro_internacional_card_expiry" type="text" name="pagseguro_internacional_card_expiry" class="input-text wc-credit-card-form-card-expiry" style="font-size: 1.5em; padding: 8px;" maxlength='10'/>

		</p>

		<p class="form-row form-row-last">

			<label for="pagseguro_internacional_card_cvv"><?php _e( 'Security code', 'wc-pagseguro-internacional' ); ?> <span class="required">*</span></label>

			<input id="pagseguro_internacional_card_cvv" name="pagseguro_internacional_card_cvv" class="input-text wc-credit-card-form-card-cvc" type="text" style="font-size: 1.5em; padding: 8px;" data-boacompra="verification_value" />

		</p>

		<div class="clear"></div>

	</div>

	<?php if ($max_installments > 1) : ?>

	<p class="form-row form-row-wide boacompra">

		<label for="pagseguro_internacional_card_installments"><?php _e('Installments', 'wc-pagseguro-internacional' ); ?> <span class="required">*</span></label>

		<select id="pagseguro_internacional_card_installments" name="pagseguro_internacional_card_installments" style="font-size: 1.5em; padding: 4px; width: 100%;">

			<option value=""><?php echo __('Type your card number', 'wc-pagseguro-internacional'); ?></option>

		</select>

	</p>

	<?php else : ?>

		<input type="hidden" value="1" id="pagseguro_internacional_card_installments" name="pagseguro_internacional_card_installments">

	<?php endif; ?>

	<div class="clear"></div>

</fieldset>
