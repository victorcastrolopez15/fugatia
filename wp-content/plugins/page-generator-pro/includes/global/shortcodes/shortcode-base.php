<?php
/**
 * Shortcode Base Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Base class for a Dynamic Element, providing several shared functions:
 * - registering a block/shortcode,
 * - parsing attributes, filling in defaults and casting variables,
 * - importing an image at random from a set
 * - producing <img> tag HTML markup
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 3.6.3
 */
class Page_Generator_Pro_Shortcode_Base {

	/**
	 * Holds the base object.
	 *
	 * @since   3.6.3
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Registers this shortcode / block in Page Generator Pro
	 *
	 * @since   3.6.3
	 *
	 * @param   array $shortcodes     Shortcodes.
	 * @return  array                   Shortcodes
	 */
	public function add_shortcode( $shortcodes ) {

		// Add this shortcode to the array of registered shortcodes.
		$shortcodes[ $this->get_name() ] = array_merge(
			$this->get_overview(),
			array(
				'name'           => $this->get_name(),
				'fields'         => $this->get_fields(),
				'attributes'     => $this->get_attributes(),
				'supports'       => $this->get_supports(),
				'tabs'           => $this->get_tabs(),
				'default_values' => $this->get_default_values(),
			)
		);

		// Return.
		return $shortcodes;

	}

	/**
	 * Returns this block's supported built-in Attributes for Gutenberg.
	 *
	 * @since   3.6.3
	 *
	 * @return  array   Supports
	 */
	public function get_supports() {

		return array(
			'className' => true,
		);

	}

	/**
	 * Returns the given shortcode / block's field's Default Value
	 *
	 * @since   3.6.3
	 *
	 * @param   string $field  Field.
	 * @return  string          Value
	 */
	public function get_default_value( $field ) {

		$defaults = $this->get_default_values();
		if ( isset( $defaults[ $field ] ) ) {
			return $defaults[ $field ];
		}

		return '';
	}

	/**
	 * Performs several transformation on a block's attributes, including:
	 * - sanitization
	 * - adding attributes with default values are missing but registered by the block
	 * - cast attribute values based on their defined type
	 *
	 * These steps are performed because the attributes may be defined by a shortcode,
	 * block or third party widget/page builder's block, each of which handle attributes
	 * slightly differently.
	 *
	 * Returns a standardised attributes array.
	 *
	 * @since   3.6.3
	 *
	 * @param   array $atts   Declared attributes.
	 * @return  array           All attributes, standardised.
	 */
	public function parse_atts( $atts ) {

		// Parse shortcode attributes, defining fallback defaults if required.
		$atts = shortcode_atts(
			$this->get_default_values(),
			$this->sanitize_atts( $atts ),
			$this->base->plugin->name . '-' . $this->get_name()
		);

		// Iterate through attributes, casting them based on their attribute definition.
		$atts_definitions = $this->get_attributes();
		foreach ( $atts as $att => $value ) {
			// Skip if no definition exists for this attribute.
			if ( ! array_key_exists( $att, $atts_definitions ) ) {
				continue;
			}

			// Skip if no type exists for this attribute.
			if ( ! array_key_exists( 'type', $atts_definitions[ $att ] ) ) {
				continue;
			}

			// Cast attribute's value(s), depending on the attribute's type.
			switch ( $atts_definitions[ $att ]['type'] ) {
				case 'number':
					$atts[ $att ] = (int) $value;
					break;

				case 'boolean':
					$atts[ $att ] = (bool) $value;
					break;

				case 'array':
					// If the value isn't an array, convert it to an array using the field's delimiter
					// as the separator.
					if ( ! is_array( $value ) ) {
						$atts[ $att ] = explode( $atts_definitions[ $att ]['delimiter'], $value );
					}
					break;
			}
		}

		return $atts;

	}

	/**
	 * Removes any HTML that might be wrongly included in the shorcode attribute's values
	 * due to e.g. copy and pasting from Documentation or other examples.
	 *
	 * @since   3.6.3
	 *
	 * @param   array $atts   Shortcode Attributes.
	 * @return  array           Shortcode Attributes
	 */
	private function sanitize_atts( $atts ) {

		if ( ! is_array( $atts ) ) {
			return $atts;
		}

		foreach ( $atts as $key => $value ) {
			if ( is_array( $value ) ) {
				continue;
			}

			$atts[ $key ] = wp_strip_all_tags( $value );
		}

		return $atts;

	}

	/**
	 * Chooses an image at random from the given array of images, returning it.
	 *
	 * EXIF data is then stored against the imported image, before the Image ID
	 * is returned
	 *
	 * @since   2.9.9
	 *
	 * @param   array $images     Images.
	 * @return  array               Image
	 */
	public function choose_random_image( $images ) {

		// Pick an image at random from the resultset.
		if ( count( $images ) === 1 ) {
			$image_index = 0;
		} else {
			$image_index = wp_rand( 0, ( count( $images ) - 1 ) );
		}

		// Return image.
		return $images[ $image_index ];

	}

	/**
	 * Imports the given third party image into WordPress with any supplied metadata attributes.
	 *
	 * EXIF data is then stored against the imported image, before the Image ID
	 * is returned
	 *
	 * @since   2.8.7
	 *
	 * @param   array $image      Image.
	 * @param   array $atts       Shortcode Attributes.
	 * @return  mixed               WP_Error | Image ID
	 */
	public function import( $image, $atts ) {

		// Import the image.
		$image_id = $this->base->get_class( 'import' )->import_remote_image(
			$image['url'],
			0,
			$this->base->get_class( 'shortcode' )->get_group_id(),
			$this->base->get_class( 'shortcode' )->get_index(),
			$atts['filename'],
			( ! $atts['title'] ? $image['title'] : $atts['title'] ), // title.
			( ! $atts['caption'] ? $image['title'] : $atts['caption'] ), // caption.
			( ! $atts['alt_tag'] ? $image['title'] : $atts['alt_tag'] ), // alt_tag.
			( ! $atts['description'] ? $image['title'] : $atts['description'] ) // description.
		);

		// Bail if an error occured.
		if ( is_wp_error( $image_id ) ) {
			return $image_id;
		}

		// Store EXIF Data in Image.
		$this->base->get_class( 'exif' )->write(
			$image_id,
			$atts['exif_description'],
			$atts['exif_comments'],
			$atts['exif_latitude'],
			$atts['exif_longitude']
		);

		// Return Image ID.
		return $image_id;

	}

	/**
	 * Chooses an image at random from the given array of images, importing it
	 * into WordPress with any supplied metadata attributes.
	 *
	 * EXIF data is then stored against the imported image, before the Image ID
	 * is returned
	 *
	 * @since   2.8.7
	 *
	 * @param   mixed $image_id   Attachment ID (false = remote image URL).
	 * @param   array $atts       Shortcode Attributes.
	 * @param   mixed $image      Third Party Image (false = not a third party image).
	 * @return  string              <img> HTML markup
	 */
	public function get_image_html( $image_id, $atts, $image = false ) {

		// If an Image ID is specified, get HTML image tag, with the image matching the given WordPress registered image size.
		if ( $image_id ) {
			$html = wp_get_attachment_image( $image_id, $atts['size'] );
		} else {
			// Build the image tag manually.
			$image_atts = array(
				'src' => $image['url'],
			);
			if ( $atts['alt_tag'] ) {
				$image_atts['alt'] = $atts['alt_tag'];
			}
			if ( $atts['title'] ) {
				$image_atts['title'] = $atts['title'];
			}

			// Build <img> string.
			$html = '<img';
			foreach ( $image_atts as $att => $value ) {
				$html .= ' ' . $att . '="' . $value . '"';
			}
			$html .= ' />';
		}

		// If a link is specified, wrap the image in the link now.
		if ( ! empty( $atts['link_href'] ) ) {
			$link = '<a href="' . $atts['link_href'] . '"';

			// Add title, if specified.
			if ( ! empty( $atts['link_title'] ) ) {
				$link .= ' title="' . $atts['link_title'] . '"';
			}

			// Add rel attribute, if specified.
			if ( ! empty( $atts['link_rel'] ) ) {
				$link .= ' rel="' . $atts['link_rel'] . '"';
			}

			// Add target, if specified.
			if ( ! empty( $atts['link_target'] ) ) {
				$link .= ' target="' . $atts['link_target'] . '"';
			}

			$link .= '>';

			$html = $link . $html . '</a>';
		}

		// If attribution is enabled, show it now.
		if ( isset( $atts['attribution'] ) && $atts['attribution'] && $image ) {
			$attribution = $this->get_image_attribution( $image );
			if ( ! empty( $attribution ) ) {
				$html = '<figure>' . $html . '<figcaption>' . $attribution . '</figcaption></figure>';
			}
		}

		/**
		 * Filter the image HTML output, before returning.
		 *
		 * @since   2.8.7
		 *
		 * @param   string  $html       HTML Output.
		 * @param   array   $atts       Shortcode Attributes.
		 * @param   int     $image_id   WordPress Media Library Image ID.
		 * @param   array   $image      Third Party Image Data.
		 */
		$html = apply_filters( 'page_generator_pro_shortcode_image_get_image_html', $html, $atts, $image_id, $image );

		// Return.
		return $html;

	}

	/**
	 * Returns an attribution string for the given image
	 *
	 * @since   2.9.9
	 *
	 * @param   array $image  Image.
	 * @return  string          Image Attribution
	 */
	public function get_image_attribution( $image ) {

		// Return full attribution if available.
		if ( $image['license'] && $image['license_url'] && $image['license_version'] ) {
			return sprintf(
				/* translators: %1$s: Link to Image Source, %2$s: Link to Image Creator, %3$s: Link to Image License */
				__( '%1$s by %2$s, licensed under %3$s', 'page-generator-pro' ),
				'<a href="' . $image['source'] . '" target="_blank" rel="nofollow noopener">' . __( 'Image', 'page-generator-pro' ) . '</a>',
				'<a href="' . $image['creator_url'] . '" target="_blank" rel="nofollow noopener">' . $image['creator'] . '</a>',
				'<a href="' . $image['license_url'] . '" target="_blank" rel="nofollow noopener">' . $image['license'] . ' ' . $image['license_version'] . '</a>'
			);
		}

		// Return basic attribution.
		return sprintf(
			/* translators: %1$s: Link to Image Source, %2$s: Link to Image Creator */
			__( '%1$s by %2$s', 'page-generator-pro' ),
			'<a href="' . $image['source'] . '" target="_blank" rel="nofollow noopener">' . __( 'Image', 'page-generator-pro' ) . '</a>',
			'<a href="' . $image['creator_url'] . '" target="_blank" rel="nofollow noopener">' . $image['creator'] . '</a>'
		);

	}

}
