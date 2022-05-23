<?php
/**
 * Cornerstone Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Registers shortcodes as Elements for Pro and X Themes.
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.6.0
 */
class Page_Generator_Pro_Cornerstone {

	/**
	 * Holds the base object.
	 *
	 * @since   2.6.0
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Constructor
	 *
	 * @since   2.6.0
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

		add_action( 'cs_register_elements', array( $this, 'add_elements' ) );
		add_filter( 'page_generator_pro_generate_set_post_meta__cornerstone_data', array( $this, 'wp_slash_page_builder_meta_on_generation' ) );
		add_filter( 'page_generator_pro_generate_content_settings', array( $this, 'convert_elements_to_class_raw_content_elements' ), 10, 1 );
		add_action( 'page_generator_pro_generate_content_finished', array( $this, 'rebuild_post_content' ), 10, 1 );

	}

	/**
	 * Registers Plugin Shortcodes as Elements, so that they can be used in the Cornerstone Editor
	 *
	 * @since   2.6.0
	 */
	public function add_elements() {

		// Bail if Cornerstone isn't available.
		if ( ! function_exists( 'cs_register_element' ) ) {
			return;
		}

		// Get shortcodes, depending on whether we're editing a Content Group or Post.
		$shortcodes = $this->base->get_class( 'shortcode' )->get_shortcodes();

		// Bail if no shortcodes are available.
		if ( ! is_array( $shortcodes ) || count( $shortcodes ) === 0 ) {
			return;
		}

		// Iterate through shortcodes, registering them.
		foreach ( $shortcodes as $shortcode => $shortcode_properties ) {
			// Skip if no tabs or fields.
			if ( ! $shortcode_properties['tabs'] ) {
				continue;
			}
			if ( ! $shortcode_properties['fields'] ) {
				continue;
			}

			// Register Element.
			cs_register_element(
				$this->base->plugin->name . '-' . $shortcode,
				array(
					'title'       => $this->base->plugin->displayName . ': ' . $shortcode_properties['title'],
					'icon'        => $shortcode_properties['icon'],
					'values'      => $this->get_element_values( $shortcode_properties['default_values'] ),
					'control_nav' => $this->get_element_control_nav( $shortcode, $shortcode_properties['title'], $shortcode_properties['tabs'] ),
					'controls'    => $this->get_element_controls( $shortcode, $shortcode_properties['tabs'], $shortcode_properties['fields'], $shortcode_properties['default_values'] ),
					'render'      => array( $this, 'render_element' ),
				)
			);
		}

	}

	/**
	 * Returns a cs_regsiter_element() compatible array of values that the element supports
	 *
	 * @since   2.6.0
	 *
	 * @param   array $default_values   Key / Values.
	 * @return  array                   Element Values
	 */
	private function get_element_values( $default_values ) {

		$values = array();

		foreach ( $default_values as $key => $value ) {
			// Convert false to null, so 'false' doesn't output on text fields.
			if ( ! $value ) {
				$value = null;
			}

			$values[ $key ] = cs_value( $value, 'attr', false );
		}

		return $values;

	}

	/**
	 * Returns the element's control navigation, which are the shortcode's tabs
	 *
	 * @since   2.6.0
	 *
	 * @param   string $shortcode          Programmatic Shortcode Name.
	 * @param   string $shortcode_name     Shortcode Title.
	 * @param   array  $tabs               Shortcode UI Tabs.
	 * @return  array                       Control Navigation Tabs
	 */
	private function get_element_control_nav( $shortcode, $shortcode_name, $tabs ) {

		$control_nav = array(
			$shortcode => $shortcode_name,
		);

		foreach ( $tabs as $tab_name => $tab_properties ) {
			$control_nav[ $shortcode . ':' . $tab_name ] = $tab_properties['label'];
		}

		return $control_nav;

	}

	/**
	 * Returns the element's controls, which are the shortcode's fields, for the Cornerstone Builder
	 *
	 * @since   2.6.0
	 *
	 * @param   string $shortcode          Programmatic Shortcode Name.
	 * @param   array  $tabs               Shortcode Tabs.
	 * @param   array  $fields             Shortcode Fields.
	 * @param   array  $default_values     Shortcode Default Values.
	 * @return  array                       Element Builder Controls
	 */
	private function get_element_controls( $shortcode, $tabs, $fields, $default_values ) {

		$controls = array();

		foreach ( $tabs as $tab_name => $tab_properties ) {

			// Build this group's controls.
			$group_controls = array();
			foreach ( $tab_properties['fields'] as $field_name ) {
				// Skip if the field doesn't exist.
				if ( ! isset( $fields[ $field_name ] ) ) {
					continue;
				}

				// Define default value.
				$default_value = ( isset( $default_values[ $field_name ] ) ? $default_values[ $field_name ] : '' );

				// Add Element Control to this Group (Tab)'s Controls.
				$group_controls[] = $this->get_element_control( $field_name, $fields[ $field_name ] );
			}

			// Add this group and its controls.
			$controls[] = array(
				'type'     => 'group',
				'label'    => $tab_properties['label'],
				'group'    => $shortcode . ':' . $tab_name,
				'controls' => $group_controls,
			);

		}

		return $controls;

	}

	/**
	 * Returns the given field's element control for the Cornerstone Builder
	 *
	 * @since   2.6.0
	 *
	 * @param   string $field_name     Field Name.
	 * @param   array  $field          Field.
	 * @return  array                   Element Control
	 */
	private function get_element_control( $field_name, $field ) {

		// Start building the Element Control.
		$element_control = array(
			'key'   => $field_name,
			'label' => $field['label'],
		);

		// If a condition is present, add it.
		if ( isset( $field['condition'] ) ) {
			$element_control = array_merge(
				$element_control,
				array(
					'condition' => array(
						'key'   => $field['condition']['key'],
						'value' => $field['condition']['value'],
						'op'    => $field['condition']['comparison'],
					),
				)
			);
		}

		// Depending on the field type, define additional control field attributes.
		switch ( $field['type'] ) {

			/**
			 * Repeater
			 */
			case 'repeater':
				// @TODO.
				$element_control = array_merge(
					$element_control,
					array(
						'type' => 'text',
					)
				);
				break;

			/**
			 * Select
			 */
			case 'select':
				// Build field options.
				$field_options = array();
				foreach ( $field['values'] as $value => $label ) {
					$field_options[] = array(
						'value' => $value,
						'label' => $label,
					);
				}

				$element_control = array_merge(
					$element_control,
					array(
						'type'    => 'select',
						'options' => array(
							'choices' => $field_options,
						),
					)
				);
				break;

			/**
			 * Select Multiple
			 */
			case 'select_multiple':
				// @TODO.
				$element_control = array_merge(
					$element_control,
					array(
						'type' => 'text',
					)
				);
				break;

			/**
			 * Text Multiple
			 */
			case 'text_multiple':
				// @TODO.
				$element_control = array_merge(
					$element_control,
					array(
						'type' => 'text',
					)
				);
				break;

			/**
			 * Toggle (Yes/No)
			 */
			case 'toggle':
				$element_control = array_merge(
					$element_control,
					array(
						'type'    => 'choose-single',
						'options' => array(
							'choices' => array(
								array(
									'value' => 0,
									'label' => __( 'No', 'page-generator-pro' ),
								),
								array(
									'value' => 1,
									'label' => __( 'Yes', 'page-generator-pro' ),
								),
							),
						),
					)
				);
				break;

			/**
			 * Number
			 */
			case 'number':
				$element_control = array_merge(
					$element_control,
					array(
						'type' => $field['type'],
						'min'  => $field['min'],
						'max'  => $field['max'],
						'step' => ( isset( $field['step'] ) ? $field['step'] : '' ),
					)
				);
				break;

			/**
			 * Autocomplete
			 */
			case 'autocomplete':
				$element_control = array_merge(
					$element_control,
					array(
						'type' => 'text',
					)
				);
				break;

			/**
			 * Text
			 */
			default:
				$element_control = array_merge(
					$element_control,
					array(
						'type' => $field['type'],
					)
				);
				break;

		}

		return $element_control;

	}

	/**
	 * Renders the shortcode syntax
	 *
	 * @since   2.6.0
	 *
	 * @param   array $atts   Attributes.
	 */
	public function render_element( $atts ) {

		// Get shortcode name.
		$shortcode_name = str_replace( $this->base->plugin->name . '-', '', $atts['_type'] );

		// Get shortcode.
		$shortcode = $this->base->get_class( 'shortcode' )->get_shortcode( $shortcode_name );

		// Bail if shortcode doesn't exist.
		if ( ! $shortcode ) {
			return false;
		}

		// Build shortcode markup.
		$html = $this->build_shortcode( $shortcode, $atts );

		// Output shortcode.
		ob_start();
		echo '<div  class="x-raw-content" >' . $html . '</div>'; // phpcs:ignore
		return ob_get_clean();

	}

	/**
	 * Builds the shortcode syntax
	 *
	 * @since   2.6.0
	 *
	 * @param   array $shortcode   Shortcode Properties.
	 * @param   array $atts        Cornerstone Block Attributes.
	 * @return  string
	 */
	private function build_shortcode( $shortcode, $atts ) {

		$html = '[' . $atts['_type'];
		foreach ( $shortcode['fields'] as $field_name => $field ) {
			if ( ! isset( $atts[ $field_name ] ) ) {
				continue;
			}

			$html .= ' ' . $field_name . '="' . $atts[ $field_name ] . '"';
		}
		$html .= ']';

		return $html;

	}

	/**
	 * Adds slashes to Cornerstone's Page Builder Meta immediately before it's
	 * copied to the Generated Page.
	 *
	 * @since   2.6.1
	 *
	 * @param   string $value      Cornerstone Page Builder Data.
	 * @return  string              Cornerstone Page Builder Data
	 */
	public function wp_slash_page_builder_meta_on_generation( $value ) {

		return wp_slash( $value );

	}

	/**
	 * If the given Content Group's content contains Cornerstone Elements registered by this Plugin, converts them
	 * to Classic Raw Content Elements so subsequent generation routines can parse them.
	 *
	 * @since   2.6.0
	 *
	 * @param   array $settings       Group Settings.
	 * @return  array                   Group Settings.
	 */
	public function convert_elements_to_class_raw_content_elements( $settings ) {

		// Bail if no Cornerstone Data exists.
		if ( ! isset( $settings['post_meta']['_cornerstone_data'] ) ) {
			return $settings;
		}

		// Get shortcodes.
		$shortcodes = $this->base->get_class( 'shortcode' )->get_shortcodes();

		// Bail if no shortcodes are available.
		if ( ! is_array( $shortcodes ) || count( $shortcodes ) === 0 ) {
			return $settings;
		}

		// Get Cornerstone Data as array.
		$data = json_decode( $settings['post_meta']['_cornerstone_data'], true );

		// Bail if the Cornerstone Data couldn't be JSON decoded.
		if ( ! $data ) {
			return $settings;
		}

		// Iterate through Shortcodes.
		foreach ( $shortcodes as $shortcode_name => $shortcode_properties ) {
			// Recursively itereate through the array, replacing any elements of type matching this shortcode
			// to Raw Content Elements.
			$this->recursively_replace_elements_by_type( $data, $this->base->plugin->name . '-' . $shortcode_name, $shortcode_properties );
		}

		// Convert back to a JSON string.
		// JSON_UNESCAPED_SLASHES prevents URLs from having unecessary slashes added to them; we only want quotations in values to be slashed.
		$settings['post_meta']['_cornerstone_data'] = json_encode( $data, JSON_UNESCAPED_SLASHES ); // phpcs:ignore

		// Return.
		return $settings;

	}

	/**
	 * Recursively iterate through Cornerstone Data, replacing any Elements whose type matches the
	 * given Shortcode Name with a Classic Raw Content Element.
	 *
	 * No return value is needed, as $arr is passed by reference.
	 *
	 * @since   2.6.0
	 *
	 * @param   array  $arr                    Cornerstone Data or Sub-Data.
	 * @param   string $shortcode_name         Shortcode Name to replace.
	 * @param   array  $shortcode_properties   Shortcode Properties.
	 */
	private function recursively_replace_elements_by_type( &$arr, $shortcode_name, $shortcode_properties ) {

		// Iterate through array.
		foreach ( $arr as $key => &$item ) {

			// If modules exist, call this function again against the modules (going down one level).
			if ( count( $item['_modules'] ) ) {
				$this->recursively_replace_elements_by_type( $item['_modules'], $shortcode_name, $shortcode_properties );
			}

			// If the item's type matches our shortcode name, replace it with a Classic Raw Content Element
			// comprising of the Shortcode's syntax.
			if ( $item['_type'] === $shortcode_name ) {
				$item = array(
					// We process shortcodes now, because slashing/unslashing breaks JSON conformity later on
					// if we process it through generate.php.
					'content'  => do_shortcode( $this->build_shortcode( $shortcode_properties, $item ) ),
					'id'       => '',
					'class'    => '',
					'style'    => '',
					'_type'    => 'classic:raw-content',
					'_region'  => 'content',
					'_modules' => array(),
				);
			}
		}

	}

	/**
	 * Loads the generated Page into Cornerstone's Content class, and saves it, to ensure
	 * that the post_content is rebuilt to reflect any replaced elements
	 *
	 * @since   2.6.0
	 *
	 * @param   int $post_id        Generated Post ID.
	 */
	public function rebuild_post_content( $post_id ) {

		// Rebuild the Post Content to reflect Cornerstone Data.
		// @TODO Throws warnings about settings.
		if ( class_exists( 'Cornerstone_Content' ) ) {
			$cornerstone = new Cornerstone_Content( $post_id );
			$cornerstone->save();
			unset( $cornerstone );
		}

	}

}
