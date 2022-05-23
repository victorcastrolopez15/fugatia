<?php
/**
 * Frontend Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Registers Plugin specific filters for a Post/Page's Title,
 * Excerpt and Content.
 *
 * Other classes/functions hook into these e.g. block spintax
 * if dynamic / on the fly frontend block spintax processing is enabled.
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 3.2.9
 */
class Page_Generator_Pro_Frontend {

	/**
	 * Holds the base object.
	 *
	 * @since   3.2.9
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Constructor
	 *
	 * @since   3.2.9
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

		// Hook into WordPress Post filters, which expose our own Plugin filters that can be used.
		add_filter( 'wp_title', array( $this, 'filter_site_title' ), 10, 3 );
		add_filter( 'the_title', array( $this, 'filter_post_title' ), 10, 2 );
		add_filter( 'get_the_excerpt', array( $this, 'filter_post_excerpt' ) );
		add_filter( 'the_content', array( $this, 'filter_post_content' ) );

	}

	/**
	 * Modifies the Site Title immediately prior to it being output
	 *
	 * @since   3.2.9
	 *
	 * @param   string $site_title     Site Title.
	 * @param   string $sep            Title Separator.
	 * @param   string $sep_location   Location of Title Separator (left | right).
	 * @return  string                  Site Title
	 */
	public function filter_site_title( $site_title, $sep, $sep_location ) {

		/**
		 * Modifies the Site Title immediately prior to it being output
		 *
		 * @param   string  $site_title     Site Title.
		 * @param   string  $sep            Title Separator.
		 * @param   string  $sep_location   Location of Title Separator (left | right).
		 */
		$result = apply_filters( 'page_generator_pro_frontend_filter_site_title', $site_title, $sep, $sep_location );

		// Return.
		return $this->return( $result, $site_title );

	}

	/**
	 * Modifies the Post Title immediately prior to it being output
	 *
	 * @since   3.2.9
	 *
	 * @param   string $post_title     Post Title.
	 * @param   int    $post_id        Post ID.
	 * @return  string                  Post Title
	 */
	public function filter_post_title( $post_title, $post_id = false ) {

		// Bail if no Post ID.
		if ( ! $post_id ) {
			return $post_title;
		}

		/**
		 * Modifies the Post Title immediately prior to it being output
		 *
		 * @since   3.2.9
		 *
		 * @param   string  $post_title     Post Title.
		 * @param   int     $post_id        Post ID.
		 */
		$result = apply_filters( 'page_generator_pro_frontend_filter_post_title', $post_title, $post_id );

		// Return.
		return $this->return( $result, $post_title );

	}

	/**
	 * Modifies the Post Excerpt immediately prior to it being output
	 *
	 * @since   3.2.9
	 *
	 * @param   string $post_excerpt   Post Excerpt.
	 * @return  string                  Post Excerpt
	 */
	public function filter_post_excerpt( $post_excerpt ) {

		/**
		 * Modifies the Post Excerpt immediately prior to it being output
		 *
		 * @since   3.2.9
		 *
		 * @param   string  $post_excerpt   Post Excerpt.
		 * @param   int     $post_id        Post ID.
		 */
		$result = apply_filters( 'page_generator_pro_frontend_filter_post_excerpt', $post_excerpt );

		// Return.
		return $this->return( $result, $post_excerpt );

	}

	/**
	 * Modifies the Post Content immediately prior to it being output
	 *
	 * @since   3.2.9
	 *
	 * @param   string $post_content   Post Content.
	 * @return  string                  Post Content
	 */
	public function filter_post_content( $post_content ) {

		global $post;

		$post_id = ( isset( $post ) ? $post->ID : 0 );

		// Bail if no Post ID.
		if ( ! $post_id ) {
			return $post_content;
		}

		/**
		 * Modifies the Post Content immediately prior to it being output
		 *
		 * @since   3.2.9
		 *
		 * @param   string  $post_content   Post Content.
		 * @param   int     $post_id        Post ID.
		 */
		$result = apply_filters( 'page_generator_pro_frontend_filter_post_content', $post_content, $post_id );

		// Return.
		return $this->return( $result, $post_content );

	}

	/**
	 * Checks if the result is a WP_Error, returning the original text if so.
	 * Otherwise returns the result which will be a filtered string
	 *
	 * @since   3.2.9
	 *
	 * @param   mixed  $result         Result (WP_Error | string).
	 * @param   string $original_text  Original Text String.
	 * @return  string                  Original Text or Result
	 */
	private function return( $result, $original_text ) {

		if ( is_wp_error( $result ) ) {
			return $original_text;
		}

		return $result;

	}

}
