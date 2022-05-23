/**
 * Elementor
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

var pageGeneratorProElementor = elementor.modules.controls.BaseData.extend(
	{

		/**
		 * Initialize selectize, autocomplete and conditional fields
		 */
		onReady: function() {

			setTimeout(
				function() {
					// Initialize autocomplete instances.
					wp_zinc_autocomplete_initialize();
				},
				1000
			);

		}

	}
);

// Bind to controls.
elementor.addControlView( 'page-generator-pro-autocomplete', pageGeneratorProElementor );
