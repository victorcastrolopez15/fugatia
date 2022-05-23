<?php
/**
 * Wikipedia API Class.
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Fetch content from Wikipedia
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.2.7
 */
class Page_Generator_Pro_Wikipedia {

	/**
	 * Holds the base object.
	 *
	 * @since   2.2.7
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Holds fetched Wikipedia images in single request cycle
	 *
	 * @since   2.2.7
	 *
	 * @var     array
	 */
	private $image_cache = array();

	/**
	 * Holds fetched Wikipedia articles in single request cycle
	 *
	 * @since   2.2.7
	 *
	 * @var     array
	 */
	private $page_cache = array();

	/**
	 * Holds the URL used to fetch content from
	 *
	 * @since   2.7.7
	 *
	 * @var     string
	 */
	private $url = '';

	/**
	 * Returns the URL that Wikipedia Content was successfully fetched from
	 *
	 * @since   2.7.7
	 *
	 * @return  string  Wikipedia URL
	 */
	public function get_url() {

		return $this->url;

	}

	/**
	 * Returns all images for the given Wikipedia Term
	 *
	 * If $term is an array, iterates through the Terms until images are returned
	 *
	 * @since   3.1.7
	 *
	 * @param   string $term       Term.
	 * @param   string $language   Language.
	 */
	public function get_images( $term, $language = 'en' ) {

		// Sanitize term.
		$term = $this->sanitize_term( $term );

		// If images already exist in cache, return them now.
		if ( isset( $this->image_cache[ $term . '-' . $language ] ) ) {
			return $this->image_cache[ $term . '-' . $language ];
		}

		// Query API.
		$result = $this->request(
			array(
				'page' => $term,
				'prop' => 'images',
			),
			$language
		);

		// Bail if an error occured.
		if ( is_wp_error( $result ) ) {
			return new WP_Error(
				'page_generator_pro_wikipedia_get_images_error',
				sprintf(
					/* translators: %1$s: Error message, %2$s: Search Term */
					__( 'Wikipedia Image: get_images(): %1$s for the Term %2$s', 'page-generator-pro' ),
					$result->get_error_message(),
					$term
				)
			);
		}

		// If no images exist, bail.
		if ( ! count( $result->images ) ) {
			return new WP_Error(
				'page_generator_pro_wikipedia_get_images_error',
				sprintf(
					/* translators: Search Term */
					__( 'Wikipedia Image: get_images(): No images found for the Term %s', 'page-generator-pro' ),
					$term
				)
			);
		}

		// Define the terms, of which at least one must be in an image's filename.
		$filename_terms = explode( ',', $term );

		// Build images array, removing some images that probably won't be relevant.
		$images = array();
		foreach ( $result->images as $image ) {
			// Skip anything that isn't a JPEG.
			// This avoids returning SVG icons that have no relevance to the Term.
			if ( stripos( $image, '.jpg' ) === false ) {
				continue;
			}

			// Iterate through terms, of which at least one must be in the image's filename.
			$image_matches = false;
			foreach ( $filename_terms as $filename_term ) {
				if ( stripos( $image, $filename_term ) === false ) {
					continue;
				}

				$image_matches = true;
			}

			// Skip if this image doesn't match the term.
			if ( ! $image_matches ) {
				continue;
			}

			// Get image URL.
			$image_url = $this->get_image_url( $image );

			// Skip if false.
			if ( ! $image_url ) {
				continue;
			}

			// Add to images array.
			$images[] = array(
				'url'             => $image_url,
				'title'           => str_replace( '_', ' ', $image ),

				// Credits.
				'source'          => $image_url,
				'creator'         => __( 'Wikipedia', 'page-generator-pro' ),
				'creator_url'     => false,
				'license'         => false,
				'license_version' => false,
				'license_url'     => false,
			);
		}

		// If no images exist, bail.
		if ( ! count( $images ) ) {
			return new WP_Error(
				'page_generator_pro_wikipedia_get_images_error',
				sprintf(
					/* translators: Search Term */
					__( 'Wikipedia Image: get_images(): Images exist on the Wikipedia page for the Term %s, but none of the image filenames were relevent to the Term.', 'page-generator-pro' ),
					$term
				)
			);
		}

		// Add to cache.
		$this->image_cache[ $term . '-' . $language ] = $images;

		// Return images for this Term.
		return $this->image_cache[ $term . '-' . $language ];

	}

	/**
	 * Main function to fetch HTML from the given Term's Wikipedia Page
	 *
	 * @since   2.2.7
	 *
	 * @param   string $term                   Term / URL.
	 * @param   bool   $use_similar_page       Use a similar Page if the Term's Page cannot be found.
	 * @param   array  $sections               Section(s) to fetch.
	 * @param   string $language               Language.
	 * @param   mixed  $elements               Element (string) or Elements (array).
	 * @param   bool   $remove_links           Remove Links.
	 * @return  mixed                           WP_Error | string
	 */
	public function get_page_sections( $term, $use_similar_page = false, $sections = false, $language = 'en', $elements = 'paragraphs', $remove_links = true ) {

		// Sanitize term.
		$term = $this->sanitize_term( $term );

		// If the page doesn't exist in cache, fetch it now.
		if ( ! isset( $this->page_cache[ $term . '-' . $language ] ) ) {
			// Get Wikipedia Page.
			$page = $this->get_page( $term, $language );

			// Bail if an error occured.
			if ( is_wp_error( $page ) ) {
				return $page;
			}

			// If the term is ambiguous, and could refer to one of several articles on Wikipedia, either fetch
			// that article or bail, depending on the $use_similar_page setting.
			if ( $this->is_disambiguation_page( $page ) ) {
				if ( ! $use_similar_page ) {
					return new WP_Error(
						'page_generator_pro_get_page_sections_ambiguous_term',
						sprintf(
							/* translators: Search Term */
							__( 'The Term "%s" is ambiguous and could relate to one of several articles available on Wikipedia.  To use one of these similar articles, set use_similar_page=1 in your shortcode.', 'page-generator-pro' ),
							$term
						)
					);
				}

				// Get similar term that has a full Wikipedia Page.
				$term = $this->get_similar_term( $page );

				// Get entire similar Page.
				$page = $this->get_page( $term, $language );
				if ( is_wp_error( $page ) ) {
					return $page;
				}
			}

			// Get content.
			$content = $this->get_content( $page );

			if ( is_wp_error( $content ) ) {
				return $content;
			}

			// Build cache.
			$cache = array(
				'content' => $content,
				'url'     => $this->url,
			);

			// Get TOC Headings and Keys.
			$headings = $this->get_headings( $page );
			if ( $headings ) {
				$cache['headings']      = $headings;
				$cache['headings_keys'] = array_keys( $headings );
			}

			// Store in cache.
			$this->page_cache[ $term . '-' . $language ] = $cache;

			// Cleanup unused vars.
			unset( $content, $headings );
		}

		// If no sections are specified, return the summary.
		if ( ! $sections || is_array( $sections ) && count( $sections ) === 0 ) {
			$return_elements = $this->get_elements( $this->page_cache[ $term . '-' . $language ]['content'], $term, false, 'toc', $elements, $remove_links );

			// If no elements found, bail.
			if ( ! count( $return_elements ) ) {
				return new WP_Error(
					'page_generator_pro_wikipedia_get_page_sections_no_elements_found',
					sprintf(
						/* translators: List of HTML elements */
						__( 'No elements could be found in the summary section matching %s', 'page-generator-pro' ),
						implode( ',', $elements )
					)
				);
			}

			return $return_elements;
		}

		// If no headings could be found, return the summary.
		if ( ! isset( $this->page_cache[ $term . '-' . $language ]['headings'] ) || ! $this->page_cache[ $term . '-' . $language ]['headings'] ) {
			$return_elements = $this->get_elements( $this->page_cache[ $term . '-' . $language ]['content'], $term, false, 'toc', $elements, $remove_links );

			// If no elements found, bail.
			if ( count( $return_elements ) === 0 ) {
				return new WP_Error(
					'page_generator_pro_wikipedia_get_page_sections_no_elements_found',
					sprintf(
						/* translators: List of HTML elements */
						__( 'No headings could be found, and no elements could be found in the summary section matching %s', 'page-generator-pro' ),
						implode( ',', $elements )
					)
				);
			}

			return $return_elements;
		}

		// Iterate through each section, fetching elements.
		$return_elements = array();
		foreach ( $sections as $section ) {
			unset( $result );

			switch ( $section ) {
				case 'summary':
					$result = $this->get_elements( $this->page_cache[ $term . '-' . $language ]['content'], $term, false, 'toc', $elements, $remove_links );
					break;

				default:
					// Get index of this section from the array of headings.
					$index = $this->get_heading_index( $this->page_cache[ $term . '-' . $language ]['headings'], $section );

					// If no index could be found, skip this section.
					if ( $index === false ) {
						break;
					}

					// Based on the index of this heading, define the start and end heading keys (IDs).
					$start_heading = $this->page_cache[ $term . '-' . $language ]['headings_keys'][ $index ];

					// If this section is the last heading, there isn't a 'next' heading that we can use
					// to determine the end of the content, so we use the navbox instead.
					if ( ! isset( $this->page_cache[ $term . '-' . $language ]['headings_keys'][ $index + 1 ] ) ) {
						$end_heading = 'navbox';
					} else {
						$end_heading = $this->page_cache[ $term . '-' . $language ]['headings_keys'][ $index + 1 ];
					}

					// Extract elements.
					$result = $this->get_elements( $this->page_cache[ $term . '-' . $language ]['content'], $term, $start_heading, $end_heading, $elements, $remove_links );
					break;
			}

			// Skip if no content found.
			if ( ! isset( $result ) || count( $result ) === 0 ) {
				continue;
			}

			// Add the results (elements) to the main array.
			$return_elements = array_merge( $return_elements, $result );
		}

		// If no elements found, bail.
		if ( count( $return_elements ) === 0 ) {
			return new WP_Error(
				'page_generator_pro_wikipedia_get_page_sections_no_content_found',
				sprintf(
					/* translators: %1$s: List of sections, %2$s: List of HTML elements */
					__( 'No content could be found in the sections %1$s for the elements %2$s', 'page-generator-pro' ),
					implode( ', ', $sections ),
					implode( ', ', $elements )
				)
			);
		}

		// Return elements.
		return $return_elements;

	}

	/**
	 * Extracts the Term from a URL, if the Term is a URL, and sanitizes the Term
	 * to remove some accents that cause issues with Wikipedia
	 *
	 * @since   3.1.7
	 *
	 * @param   string $term   Term or Wikipedia URL.
	 * @return  string          Term
	 */
	private function sanitize_term( $term ) {

		// If the Term is a Wikipedia URL, extract the Term for the API call.
		if ( filter_var( $term, FILTER_VALIDATE_URL ) && strpos( $term, 'wikipedia.org' ) !== false ) {
			$url  = wp_parse_url( $term );
			$term = str_replace( '/', '', $url['path'] );
		}

		// Return sanitized term.
		return str_replace( ' ', '_', preg_replace( '/&([A-Za-z]{1,2})(grave|acute|circ|cedil|uml|lig);/', '$1', $term ) );

	}

	/**
	 * Sends a request to the Wikipedia API
	 *
	 * @since   3.1.7
	 *
	 * @param   array  $args       Request arguments.
	 * @param   string $language   Language.
	 * @return  mixed               WP_Error | object
	 */
	private function request( $args, $language ) {

		// Merge args.
		$args = array_merge(
			$args,
			array(
				'action'        => 'parse',
				'format'        => 'json',
				'formatversion' => 2,
			)
		);

		// Build API URL.
		$url = add_query_arg( $args, 'https://' . $language . '.wikipedia.org/w/api.php' );

		// Query API.
		// User-agent ensures we get all content.
		$response = wp_remote_get(
			$url,
			array(
				'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.131 Safari/537.36',
				'sslverify'  => false,
			)
		);

		// Bail if an error occured.
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Bail if HTTP response code isn't valid.
		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return new WP_Error( 'page_generator_pro_wikipedia_request', wp_remote_retrieve_response_code( $response ) );
		}

		// Fetch body and JSON decode.
		$body   = wp_remote_retrieve_body( $response );
		$result = json_decode( $body );

		// Bail if an error was received from Wikipedia.
		if ( isset( $result->error ) ) {
			return new WP_Error( 'page_generator_pro_wikipedia_request', $result->error->code . ': ' . $result->error->info );
		}

		// Bail if the expected data is missing.
		if ( ! isset( $result->{ $args['action'] } ) ) {
			return new WP_Error( 'page_generator_pro_wikipedia_get_page', __( 'No data was returned.', 'page-generator-pro' ) );
		}

		// Return.
		return $result->{ $args['action'] };

	}


	/**
	 * Returns the full image URI for the given Wikipedia Image Filename
	 *
	 * @see     https://commons.wikimedia.org/wiki/Commons:FAQ#What_are_the_strangely_named_components_in_file_paths.3F
	 *
	 * @since   3.1.7
	 *
	 * @param   string $image_filename     Image Filename.
	 * @return  string                      Image URL
	 */
	private function get_image_url( $image_filename ) {

		// Bail if no image filename.
		if ( ! $image_filename || empty( $image_filename ) ) {
			return false;
		}

		// Replace spaces with underscores.
		$image_filename = str_replace( ' ', '_', $image_filename );

		// Get MD5 hash.
		$hash = md5( $image_filename );

		// Return URL per wikimedia format.
		return 'https://upload.wikimedia.org/wikipedia/commons/' . substr( $hash, 0, 1 ) . '/' . substr( $hash, 0, 2 ) . '/' . $image_filename;

	}

	/**
	 * Returns a DOMDocument representation of a Wikipedia Page's content
	 *
	 * @since   2.2.7
	 *
	 * @param   string $term       Term / URL.
	 * @param   string $language   Language.
	 * @return  mixed               WP_Error | array
	 */
	private function get_page( $term, $language ) {

		// Build API URL.
		$result = $this->request(
			array(
				'page' => $term,
				'prop' => 'text',
			),
			$language
		);

		// Bail if an error occured.
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Parse into DOMDocument.
		$dom                     = new DOMDocument();
		$dom->preserveWhiteSpace = false; // phpcs:ignore

		// Convert encoding to UTF-8 if php-mbstring is installed.
		if ( function_exists( 'mb_convert_encoding' ) ) {
			$html = mb_convert_encoding( $result->text, 'HTML-ENTITIES', 'UTF-8' );
		} else {
			// Cannot guarantee this works as mb_convert_encoding is not available.
			$html = $result->text;
		}
		@$dom->loadHTML( '<html><body>' . $html . '</body></html>' ); // phpcs:ignore

		// Store URL.
		$this->url = 'https://' . $language . '.wikipedia.org/wiki/' . $term;

		// Return.
		return $dom;

	}

	/**
	 * Flag to denote if the page is a disambiguation page, meaning that the term given
	 * is too ambiguous to determine which article to fetch
	 *
	 * @since   2.2.8
	 *
	 * @param   DOMDocument $dom    Wikipedia Page DOM.
	 * @return  bool                    Is Disambiguation Page
	 */
	private function is_disambiguation_page( $dom ) {

		// Check if a disambiguation element exists.
		$disambigbox = $dom->getElementById( 'disambigbox' );
		$disambig    = $dom->getElementById( 'disambig' );
		if ( is_null( $disambigbox ) && is_null( $disambig ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Returns the first similar term from the list of "may refer to" links
	 * where the given DOM represents a Wikipedia Disambiguation Page
	 *
	 * @since   2.2.8
	 *
	 * @param   DOMDocument $dom        Wikipedia Page DOM.
	 * @param   string      $language   Language.
	 * @return  mixed                       WP_Error | string
	 */
	private function get_similar_term( $dom, $language = 'en' ) { // phpcs:ignore

		// Sanity check that a disambiguation element exists.
		if ( ! $this->is_disambiguation_page( $dom ) ) {
			return new WP_Error(
				'page_generator_pro_wikipedia_get_similar_page_not_disambiguation',
				__( 'The given Page is not a disambiguation page, therefore no similar page can be fetched.', 'page-generator-pro' )
			);
		}

		// Get terms listed in the 'may refer to' part.
		$links = $this->get_similar_page_terms( $dom );
		if ( is_wp_error( $links ) ) {
			return $links;
		}

		// Return first link's term as a DOMDocument.
		return $links[0];

	}

	/**
	 * Returns an array of all Terms found in the Wikipedia article's
	 * 'may refer to' links.
	 *
	 * @since   2.2.8
	 *
	 * @param   DOMElement $content    Wikipedia Page Content.
	 * @return  mixed                   WP_Error | array
	 */
	private function get_similar_page_terms( $content ) {

		// Fetch list items in content.
		$similar_pages = $content->getElementsByTagName( 'li' );

		// Bail if no table of contents could be found.
		if ( ! $similar_pages->length ) {
			return new WP_Error(
				'page_generator_pro_wikipedia_get_similar_page_similar_pages_empty',
				__( 'There are no similar page links on this disambiguation page.', 'page-generator-pro' )
			);
		}

		// Extract link names and anchors.
		$terms = array();
		foreach ( $similar_pages as $similar_page ) {
			foreach ( $similar_page->childNodes as $link ) { // phpcs:ignore
				// Skip if not a link.
				if ( $link->nodeName != 'a' ) { // phpcs:ignore
					continue;
				}

				// Skip if the class name contains 'new' - there's no published article available.
				if ( strpos( $link->getAttribute( 'class' ), 'new' ) !== false ) {
					continue;
				}

				$terms[] = $link->nodeValue; // phpcs:ignore
			}
		}

		// Bail if no links found.
		if ( count( $terms ) === 0 ) {
			return new WP_Error(
				'page_generator_pro_wikipedia_get_similar_page_similar_pages_empty',
				__( 'There are no similar page terms on this disambiguation page.', 'page-generator-pro' )
			);
		}

		// Return terms.
		return $terms;

	}

	/**
	 * Returns the main content of the Wikipedia article
	 *
	 * @since   2.2.7
	 *
	 * @param   DOMDocument $dom    Wikipedia Page DOM.
	 * @return  mixed                   WP_Error | DOMElement (Article Content)
	 */
	private function get_content( $dom ) {

		// Iterate through content until we find the .mw-parser-output element.
		foreach ( $dom->getElementsByTagName( 'div' ) as $node ) {
			if ( $node->getAttribute( 'class' ) === 'mw-parser-output' ) {
				return $node;
			}
		}

		// If here, we couldn't find the .mw-parser-output element.
		return new WP_Error( 'page_generator_pro_wikipedia_get_content_mw_parser_output_class_missing', __( 'The mw-parser-output CSS class could not be found on the Wikipedia Page', 'page-generator-pro' ) );

	}

	/**
	 * Returns an array of all headings found in the Wikipedia article's
	 * Table of Contents
	 *
	 * @since   2.2.7
	 *
	 * @param   DOMDocument $dom    Wikipedia DOM.
	 * @return  mixed                   bool | array
	 */
	private function get_headings( $dom ) {

		// Get table of contents.
		$toc_element = $dom->getElementById( 'toc' );

		// Bail if the table of contents element could not be found.
		if ( is_null( $toc_element ) ) {
			return false;
		}

		// Get table of contents.
		$table_of_contents = $toc_element->getElementsByTagName( 'li' );

		// Bail if no table of contents could be found.
		if ( ! $table_of_contents->length ) {
			return false;
		}

		// Extract heading names and anchors.
		$headings = array();
		foreach ( $table_of_contents as $heading ) {
			// Skip if this is not a top level heading.
			if ( strpos( $heading->getAttribute( 'class' ), 'toclevel-1' ) === false ) {
				continue;
			}

			foreach ( $heading->childNodes as $link ) { // phpcs:ignore
				// Skip if not a link.
				if ( $link->nodeName != 'a' ) { // phpcs:ignore
					continue;
				}

				// Get heading text parts, so we just get the text, not the number.
				$heading_parts = $link->getElementsByTagName( 'span' );

				// If no heading parts found, just use the node as the text.
				if ( ! $heading_parts->length ) {
					$headings[ str_replace( '#', '', $link->getAttribute( 'href' ) ) ] = $link->nodeValue; // phpcs:ignore
					continue;
				}

				// Iterate through heading parts.
				foreach ( $heading_parts as $heading_part ) {
					$class = $heading_part->getAttribute( 'class' );
					if ( $class !== 'toctext' ) {
						continue;
					}

					// We found the heading text.
					$headings[ str_replace( '#', '', $link->getAttribute( 'href' ) ) ] = $heading_part->nodeValue; // phpcs:ignore
					continue;
				}
			}
		}

		// Return headings.
		return $headings;

	}

	/**
	 * Searches both keys and values for the given array of headings to find a heading
	 *
	 * @since   2.2.7
	 *
	 * @param   array  $headings   Headings.
	 * @param   string $search     Heading to search for.
	 * @return  mixed               false | index
	 */
	private function get_heading_index( $headings, $search ) {

		$search = strtolower( $search );

		$i = 0;
		foreach ( $headings as $heading => $label ) {
			if ( strtolower( $heading ) === $search ) {
				return $i;
			}

			if ( strtolower( $label ) === $search ) {
				return $i;
			}

			$i++;
		}

		return false;

	}

	/**
	 * Returns an array of specified elements between the given start and end element
	 *
	 * @since   2.2.7
	 *
	 * @param   DOMElement $content        Article Content Node.
	 * @param   string     $term           Term.
	 * @param   mixed      $start_element  false | string.
	 * @param   mixed      $end_element    false | string.
	 * @param   array      $elements       Elements to Return.
	 * @param   bool       $remove_links   Remove Links (default: true).
	 * @return  array                       Elements
	 */
	private function get_elements( $content, $term, $start_element = false, $end_element = false, $elements = 'paragraphs', $remove_links = true ) {

		// Define array to store elements in.
		$return_elements = array();

		// Flag to denote whether we should start collecting elements.
		$collect_elements = ( ! $start_element ? true : false );

		foreach ( $content->childNodes as $node ) { // phpcs:ignore
			if ( ! $node instanceof DOMElement ) {
				continue;
			}

			// Start collecting elements if we've not yet started and this element matches our start element selector.
			if ( $start_element !== false && $this->is_element( $node, $start_element ) ) {
				$collect_elements = true;
			}

			// Stop collecting elements if we've reached the end element.
			if ( $end_element !== false && $this->is_element( $node, $end_element ) ) {
				$collect_elements = false;
				break;
			}

			// Skip if we're not yet collecting elements.
			if ( ! $collect_elements ) {
				continue;
			}

			// Skip if not an element we want.
			if ( ! in_array( $node->tagName, $this->get_tags_by_elements( $elements ), true ) ) { // phpcs:ignore
				continue;
			}

			// Get text.
			$text = trim( $node->nodeValue ); // phpcs:ignore

			// Skip if empty.
			if ( empty( $text ) ) {
				continue;
			}

			// Skip if this entire elements's nodeValue matches the keyword.
			if ( strpos( $term, $text ) !== false ) {
				continue;
			}

			// Strip some child nodes that we don't want.
			$node = $this->remove_child_nodes( $node );

			// Save HTML of node so we get the entire markup for this element .
			$content = $node->ownerDocument->saveHTML( $node ); // phpcs:ignore

			// Skip if the elements starts with certain characters.
			if ( strpos( $content, '[[' ) !== false ) {
				continue;
			}

			// Remove footnotes.
			$content = preg_replace( '/\[[a-z0-9]+\]/', null, $content );

			// Remove links, if required.
			if ( $remove_links ) {
				$content = preg_replace( array( '"<a (.*?)>"', '"</a>"' ), array( '', '' ), $content );
			}

			// Remove some odd characters that may have been left behind, such as ().
			$content = str_replace( '()', '', $content );

			// Add elements to array.
			$return_elements[] = $content;
		}

		return $return_elements;

	}

	/**
	 * Returns an array of supported elements that can be fetched from
	 * a Wikipedia Article, with their values being label names
	 *
	 * @since   2.7.1
	 *
	 * @return  array   Supported Elements
	 */
	public function get_supported_elements() {

		return array(
			'paragraphs' => __( 'Paragraphs', 'page-generator-pro' ),
			'headings'   => __( 'Headings', 'page-generator-pro' ),
			'lists'      => __( 'Lists', 'page-generator-pro' ),
			'tables'     => __( 'Tables', 'page-generator-pro' ),
			'images'     => __( 'Images', 'page-generator-pro' ),
		);

	}

	/**
	 * Returns an array of supported elements that can be fetched from
	 * a Wikipedia Article, with their values being an array of HTML tags
	 *
	 * @since   2.7.1
	 *
	 * @return  array   Supported Elements
	 */
	public function get_supported_elements_tags() {

		return array(
			'paragraphs' => array( 'p' ),
			'headings'   => array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ),
			'lists'      => array( 'ol', 'ul' ),
			'tables'     => array( 'table' ),
			'images'     => array( 'img' ),
		);

	}

	/**
	 * Helper method to retrieve Wikipedia languages
	 *
	 * @since   3.1.7
	 *
	 * @return  array    Languages
	 */
	public function get_languages() {

		// Keys are Wikipedia subdomains e.g. ab.wikipedia.org.
		// Values are the language names in English.
		$languages = array(
			'ab'           => 'Abkhazian',
			'ace'          => 'Acehnese',
			'ady'          => 'Adyghe',
			'aa'           => 'Afar',
			'af'           => 'Afrikaans',
			'ak'           => 'Akan',
			'sq'           => 'Albanian',
			'als'          => 'Alemannic',
			'am'           => 'Amharic',
			'ang'          => 'Anglo-Saxon',
			'ar'           => 'Arabic',
			'an'           => 'Aragonese',
			'arc'          => 'Aramaic',
			'hy'           => 'Armenian',
			'roa-rup'      => 'Aromanian',
			'as'           => 'Assamese',
			'ast'          => 'Asturian',
			'av'           => 'Avar',
			'ay'           => 'Aymara',
			'az'           => 'Azerbaijani',
			'bm'           => 'Bambara',
			'bjn'          => 'Banjar',
			'map-bms'      => 'Banyumasan',
			'ba'           => 'Bashkir',
			'eu'           => 'Basque',
			'bar'          => 'Bavarian',
			'be'           => 'Belarusian',
			'be-tarask'    => 'Belarusian (Taraškievica)',
			'bn'           => 'Bengali',
			'bh'           => 'Bihari',
			'bpy'          => 'Bishnupriya Manipuri',
			'bi'           => 'Bislama',
			'bs'           => 'Bosnian',
			'br'           => 'Breton',
			'bug'          => 'Buginese',
			'bg'           => 'Bulgarian',
			'my'           => 'Burmese',
			'bxr'          => 'Buryat',
			'zh-yue'       => 'Cantonese',
			'ca'           => 'Catalan',
			'ceb'          => 'Cebuano',
			'bcl'          => 'Central Bicolano',
			'ch'           => 'Chamorro',
			'cbk-zam'      => 'Chavacano',
			'ce'           => 'Chechen',
			'chr'          => 'Cherokee',
			'chy'          => 'Cheyenne',
			'ny'           => 'Chichewa',
			'zh'           => 'Chinese',
			'cho'          => 'Choctaw',
			'cv'           => 'Chuvash',
			'zh-classical' => 'Classical Chinese',
			'kw'           => 'Cornish',
			'co'           => 'Corsican',
			'cr'           => 'Cree',
			'crh'          => 'Crimean Tatar',
			'hr'           => 'Croatian',
			'cs'           => 'Czech',
			'da'           => 'Danish',
			'dv'           => 'Divehi',
			'nl'           => 'Dutch',
			'nds-nl'       => 'Dutch Low Saxon',
			'dz'           => 'Dzongkha',
			'arz'          => 'Egyptian Arabic',
			'eml'          => 'Emilian-Romagnol',
			'en'           => 'English',
			'myv'          => 'Erzya',
			'eo'           => 'Esperanto',
			'et'           => 'Estonian',
			'ee'           => 'Ewe',
			'ext'          => 'Extremaduran',
			'fo'           => 'Faroese',
			'hif'          => 'Fiji Hindi',
			'fj'           => 'Fijian',
			'fi'           => 'Finnish',
			'frp'          => 'Franco-Provençal',
			'fr'           => 'French',
			'fur'          => 'Friulian',
			'ff'           => 'Fula',
			'gag'          => 'Gagauz',
			'gl'           => 'Galician',
			'gan'          => 'Gan',
			'ka'           => 'Georgian',
			'de'           => 'German',
			'glk'          => 'Gilaki',
			'gom'          => 'Goan Konkani',
			'got'          => 'Gothic',
			'el'           => 'Greek',
			'kl'           => 'Greenlandic',
			'gn'           => 'Guarani',
			'gu'           => 'Gujarati',
			'ht'           => 'Haitian',
			'hak'          => 'Hakka',
			'ha'           => 'Hausa',
			'haw'          => 'Hawaiian',
			'he'           => 'Hebrew',
			'hz'           => 'Herero',
			'mrj'          => 'Hill Mari',
			'hi'           => 'Hindi',
			'ho'           => 'Hiri Motu',
			'hu'           => 'Hungarian',
			'is'           => 'Icelandic',
			'io'           => 'Ido',
			'ig'           => 'Igbo',
			'ilo'          => 'Ilokano',
			'id'           => 'Indonesian',
			'ia'           => 'Interlingua',
			'ie'           => 'Interlingue',
			'iu'           => 'Inuktitut',
			'ik'           => 'Inupiak',
			'ga'           => 'Irish',
			'it'           => 'Italian',
			'jam'          => 'Jamaican Patois',
			'ja'           => 'Japanese',
			'jv'           => 'Javanese',
			'kbd'          => 'Kabardian',
			'kab'          => 'Kabyle',
			'xal'          => 'Kalmyk',
			'kn'           => 'Kannada',
			'kr'           => 'Kanuri',
			'pam'          => 'Kapampangan',
			'krc'          => 'Karachay-Balkar',
			'kaa'          => 'Karakalpak',
			'ks'           => 'Kashmiri',
			'csb'          => 'Kashubian',
			'kk'           => 'Kazakh',
			'km'           => 'Khmer',
			'ki'           => 'Kikuyu',
			'rw'           => 'Kinyarwanda',
			'ky'           => 'Kirghiz',
			'rn'           => 'Kirundi',
			'kv'           => 'Komi',
			'koi'          => 'Komi-Permyak',
			'kg'           => 'Kongo',
			'ko'           => 'Korean',
			'kj'           => 'Kuanyama',
			'ku'           => 'Kurdish (Kurmanji)',
			'ckb'          => 'Kurdish (Sorani)',
			'lad'          => 'Ladino',
			'lbe'          => 'Lak',
			'lo'           => 'Lao',
			'ltg'          => 'Latgalian',
			'la'           => 'Latin',
			'lv'           => 'Latvian',
			'lez'          => 'Lezgian',
			'lij'          => 'Ligurian',
			'li'           => 'Limburgish',
			'ln'           => 'Lingala',
			'lt'           => 'Lithuanian',
			'jbo'          => 'Lojban',
			'lmo'          => 'Lombard',
			'nds'          => 'Low Saxon',
			'dsb'          => 'Lower Sorbian',
			'lg'           => 'Luganda',
			'lb'           => 'Luxembourgish',
			'mk'           => 'Macedonian',
			'mai'          => 'Maithili',
			'mg'           => 'Malagasy',
			'ms'           => 'Malay',
			'ml'           => 'Malayalam',
			'mt'           => 'Maltese',
			'gv'           => 'Manx',
			'mi'           => 'Maori',
			'mr'           => 'Marathi',
			'mh'           => 'Marshallese',
			'mzn'          => 'Mazandarani',
			'mhr'          => 'Meadow Mari',
			'cdo'          => 'Min Dong',
			'zh-min-nan'   => 'Min Nan',
			'min'          => 'Minangkabau',
			'xmf'          => 'Mingrelian',
			'mwl'          => 'Mirandese',
			'mdf'          => 'Moksha',
			'mo'           => 'Moldovan',
			'mn'           => 'Mongolian',
			'mus'          => 'Muscogee',
			'nah'          => 'Nahuatl',
			'na'           => 'Nauruan',
			'nv'           => 'Navajo',
			'ng'           => 'Ndonga',
			'nap'          => 'Neapolitan',
			'ne'           => 'Nepali',
			'new'          => 'Newar',
			'pih'          => 'Norfolk',
			'nrm'          => 'Norman',
			'frr'          => 'North Frisian',
			'lrc'          => 'Northern Luri',
			'se'           => 'Northern Sami',
			'nso'          => 'Northern Sotho',
			'no'           => 'Norwegian (Bokmål)',
			'nn'           => 'Norwegian (Nynorsk)',
			'nov'          => 'Novial',
			'ii'           => 'Nuosu',
			'oc'           => 'Occitan',
			'cu'           => 'Old Church Slavonic',
			'or'           => 'Oriya',
			'om'           => 'Oromo',
			'os'           => 'Ossetian',
			'pfl'          => 'Palatinate German',
			'pi'           => 'Pali',
			'pag'          => 'Pangasinan',
			'pap'          => 'Papiamentu',
			'ps'           => 'Pashto',
			'pdc'          => 'Pennsylvania German',
			'fa'           => 'Persian',
			'pcd'          => 'Picard',
			'pms'          => 'Piedmontese',
			'pl'           => 'Polish',
			'pnt'          => 'Pontic',
			'pt'           => 'Portuguese',
			'pa'           => 'Punjabi',
			'qu'           => 'Quechua',
			'ksh'          => 'Ripuarian',
			'rmy'          => 'Romani',
			'ro'           => 'Romanian',
			'rm'           => 'Romansh',
			'ru'           => 'Russian',
			'rue'          => 'Rusyn',
			'sah'          => 'Sakha',
			'sm'           => 'Samoan',
			'bat-smg'      => 'Samogitian',
			'sg'           => 'Sango',
			'sa'           => 'Sanskrit',
			'sc'           => 'Sardinian',
			'stq'          => 'Saterland Frisian',
			'sco'          => 'Scots',
			'gd'           => 'Scottish Gaelic',
			'sr'           => 'Serbian',
			'sh'           => 'Serbo-Croatian',
			'st'           => 'Sesotho',
			'sn'           => 'Shona',
			'scn'          => 'Sicilian',
			'szl'          => 'Silesian',
			'simple'       => 'Simple English',
			'sd'           => 'Sindhi',
			'si'           => 'Sinhalese',
			'sk'           => 'Slovak',
			'sl'           => 'Slovenian',
			'so'           => 'Somali',
			'azb'          => 'Southern Azerbaijani',
			'es'           => 'Spanish',
			'srn'          => 'Sranan',
			'su'           => 'Sundanese',
			'sw'           => 'Swahili',
			'ss'           => 'Swati',
			'sv'           => 'Swedish',
			'tl'           => 'Tagalog',
			'ty'           => 'Tahitian',
			'tg'           => 'Tajik',
			'ta'           => 'Tamil',
			'roa-tara'     => 'Tarantino',
			'tt'           => 'Tatar',
			'te'           => 'Telugu',
			'tet'          => 'Tetum',
			'th'           => 'Thai',
			'bo'           => 'Tibetan',
			'ti'           => 'Tigrinya',
			'tpi'          => 'Tok Pisin',
			'to'           => 'Tongan',
			'ts'           => 'Tsonga',
			'tn'           => 'Tswana',
			'tum'          => 'Tumbuka',
			'tr'           => 'Turkish',
			'tk'           => 'Turkmen',
			'tyv'          => 'Tuvan',
			'tw'           => 'Twi',
			'udm'          => 'Udmurt',
			'uk'           => 'Ukrainian',
			'hsb'          => 'Upper Sorbian',
			'ur'           => 'Urdu',
			'ug'           => 'Uyghur',
			'uz'           => 'Uzbek',
			've'           => 'Venda',
			'vec'          => 'Venetian',
			'vep'          => 'Vepsian',
			'vi'           => 'Vietnamese',
			'vo'           => 'Volapük',
			'fiu-vro'      => 'Võro',
			'wa'           => 'Walloon',
			'war'          => 'Waray',
			'cy'           => 'Welsh',
			'vls'          => 'West Flemish',
			'fy'           => 'West Frisian',
			'pnb'          => 'Western Punjabi',
			'wo'           => 'Wolof',
			'wuu'          => 'Wu',
			'xh'           => 'Xhosa',
			'yi'           => 'Yiddish',
			'yo'           => 'Yoruba',
			'diq'          => 'Zazaki',
			'zea'          => 'Zeelandic',
			'za'           => 'Zhuang',
			'zu'           => 'Zulu',
		);

		/**
		 * Defines available Wikipedia languages.
		 *
		 * @since   3.1.7
		 *
		 * @param   array   $output_types   Output Types.
		 */
		$languages = apply_filters( 'page_generator_pro_wikipedia_get_languages', $languages );

		// Return filtered results.
		return $languages;

	}

	/**
	 * Returns an array of HTML tags (e.g. p,h1) for the given element names (e.g. paragraphs,headings)
	 *
	 * @since   2.7.1
	 *
	 * @param   mixed $elements   Elements (string|array).
	 * @return                      HTML Elements
	 */
	private function get_tags_by_elements( $elements ) {

		// Convert elements to an array if it's a string.
		if ( ! is_array( $elements ) ) {
			$elements = array( $elements );
		}

		// Get element names and their tags.
		$elements_tags = $this->get_supported_elements_tags();

		// Build array of HTML tags.
		$tags = array();
		foreach ( $elements as $element ) {
			// Skip if element isn't supported.
			if ( ! isset( $elements_tags[ $element ] ) ) {
				continue;
			}

			$tags = array_merge( $tags, $elements_tags[ $element ] );
		}

		return $tags;

	}

	/**
	 * Recursively iterates through the node to see if it, or any descendents,
	 * have an ID or class attribute matching the given search
	 *
	 * @since   2.2.7
	 *
	 * @param   DOMNode $node       Node.
	 * @param   string  $search     Search Class or ID.
	 * @return  bool                    Element matches Search by ID or class
	 */
	private function is_element( $node, $search ) {

		// Return true if the element's ID matches our search term.
		if ( $node->getAttribute( 'id' ) === $search ) {
			return true;
		}

		// Return true if a class name matches our search term.
		$classes = explode( ' ', $node->getAttribute( 'class' ) );
		if ( in_array( $search, $classes, true ) ) {
			return true;
		}

		// If children exist, iterate them now.
		if ( $node->childNodes ) { // phpcs:ignore
			foreach ( $node->childNodes as $child_node ) { // phpcs:ignore
				if ( ! $child_node instanceof DOMElement ) {
					continue;
				}

				if ( $this->is_element( $child_node, $search ) ) {
					return true;
				}
			}
		}

		return false;

	}

	/**
	 * Removes links, if specified, from the given node, as well as some predefined
	 * child nodes that we don't want, such as Wikipedia Edit Links.
	 *
	 * @since   2.7.1
	 *
	 * @param   DOMNode $node   Node.
	 * @return  DOMNode             Node
	 */
	private function remove_child_nodes( $node ) {

		// Define tags and CSS class combinations to remove.
		$tags = array(
			'sup'   => array(),
			'span'  => array(
				'mw-editsection',
				'rt-commentedText',
			),

			// Edit on Wikidata.
			'div'   => array(
				'wikidata-link',
			),

			'link'  => array(),
			'style' => array(),
		);

		// Iterate through tags.
		foreach ( $tags as $tag => $classes ) {
			$child_nodes = $node->getElementsByTagName( $tag );

			// If no child nodes matching the tag exist, bail.
			if ( ! $child_nodes->length ) {
				continue;
			}

			// Iterate through tags.
			foreach ( $child_nodes as $child_node ) {
				// If $classes is empty, remove the tag regardless of its CSS class.
				if ( empty( $classes ) ) {
					try {
						// Access this node's parent to then remove the child i.e. this node.
						// Better than $node->removeChild(), which may trigger a not found exception.
						$child_node->parentNode->removeChild( $child_node ); // phpcs:ignore
					} catch ( Exception $e ) { // phpcs:ignore
						// Continue on to the next node.
					}
					continue;
				}

				// Get CSS classes.
				$child_node_classes = $child_node->getAttribute( 'class' );

				// Skip if no classes.
				if ( empty( $child_node_classes ) ) {
					continue;
				}

				// Explode into an array so we can search for individual CSS classes.
				$child_node_classes = explode( ' ', $child_node_classes );

				// Iterate through classes that would require us to remove this child node.
				foreach ( $classes as $class ) {
					// Skip if this class doesn't exist in the child node's classes.
					if ( ! in_array( $class, $child_node_classes, true ) ) {
						continue;
					}

					// If here, we need to remove this child node.
					try {
						// Access this node's parent to then remove the child i.e. this node.
						// Better than $node->removeChild(), which may trigger a not found exception.
						$child_node->parentNode->removeChild( $child_node ); // phpcs:ignore
					} catch ( Exception $e ) { // phpcs:ignore
						// Continue on to the next node.
					}
				}
			}
		}

		return $node;

	}

}
