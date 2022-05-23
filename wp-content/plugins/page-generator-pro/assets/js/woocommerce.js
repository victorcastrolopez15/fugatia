/**
 * WooCommerce Integration
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

jQuery( document ).ready(
	function( $ ) {

		// Remove .wc_input_price and .wc_input_decimal classes from WooCommerce Product data meta box fields,
		// so that WooCommerce field validation on prices/numbers isn't triggered, therefore allowing
		// keywords to be inserted into price fields.
		$( 'input.wc_input_price' ).removeClass( 'wc_input_price' );
		$( 'input.wc_input_decimal' ).removeClass( 'wc_input_decimal' );

	}
);
