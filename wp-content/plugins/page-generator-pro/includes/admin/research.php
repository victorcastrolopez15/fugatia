<?php
/**
 * Research Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Interacts with third party APIs to build
 * content based on a given topic, and check the status
 * of a research request.
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.8.9
 */
class Page_Generator_Pro_Research {

	/**
	 * Holds the base object.
	 *
	 * @since   2.8.9
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Constructor.
	 *
	 * @since   2.8.9
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

	}

	/**
	 * Return available research providers supported by this class.
	 *
	 * @since   2.8.9
	 *
	 * @return  array   Spintax Service Providers
	 */
	public function get_providers() {

		$providers = array(
			'ai_writer'  => __( 'AI Writer', 'page-generator-pro' ),
			'contentbot' => __( 'ContentBot', 'page-generator-pro' ),
		);

		/**
		 * Defines the available research providers supported by this Plugin
		 *
		 * @since   2.8.9
		 *
		 * @param   array   $providers  Research Service Providers.
		 */
		$providers = apply_filters( 'page_generator_pro_research_get_providers', $providers );

		// Return filtered results.
		return $providers;

	}

	/**
	 * Researches the given topic, sending the request to the configured third party
	 * service to return content later on.
	 *
	 * @since   2.8.9
	 *
	 * @param   string $topic  Topic.
	 * @return  mixed           WP_Error | string
	 */
	public function research( $topic ) {

		// Get research provider.
		$provider = $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-research', 'provider' );

		// Depending on the provider, perform spinning now.
		switch ( $provider ) {

			/**
			 * AI Writer
			 */
			case 'ai_writer':
				$result = $this->research_ai_writer( $topic );
				break;

			/**
			 * ContentBot
			 */
			case 'contentbot':
				$result = $this->research_contentbot( $topic );
				break;

			default:
				/**
				 * Filter to add spintax to content for a third party spintax provider.
				 *
				 * @since   2.2.9
				 *
				 * @param   string  $content                Content.
				 * @param   array   $protected_words        Protected Words.
				 */
				$result = apply_filters(
					'page_generator_pro_research_research_' . $provider,
					new WP_Error(
						'page_generator_pro_research_error',
						__( 'No Research provider was configured in the Plugin\'s Settings', 'page-generator-pro' )
					)
				);
				break;

		}

		return $result;

	}

	/**
	 * Returns the status of an existing research request.
	 *
	 * @since   2.8.9
	 *
	 * @param   string $id     ID of existing research request.
	 * @return  mixed           WP_Error | string
	 */
	public function get_status( $id ) {

		// Get research provider.
		$provider = $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-research', 'provider' );

		// Depending on the provider, perform spinning now.
		switch ( $provider ) {

			/**
			 * AI Writer
			 */
			case 'ai_writer':
				$result = $this->get_status_ai_writer( $id );
				break;

			default:
				/**
				 * Filter to add spintax to content for a third party spintax provider.
				 *
				 * @since   2.2.9
				 *
				 * @param   string  $content                Content.
				 * @param   array   $protected_words        Protected Words.
				 */
				$result = apply_filters(
					'page_generator_pro_research_get_status_' . $provider,
					new WP_Error(
						'page_generator_pro_research_get_status_error',
						__( 'No Research provider was configured in the Plugin\'s Settings', 'page-generator-pro' )
					)
				);
				break;

		}

		return $result;

	}

	/**
	 * Sends the topic to AI Writer's research endpoint, for AI Writer to build content
	 * and return it later on asynchronously
	 *
	 * @since   2.8.9
	 *
	 * @param   string $topic            Topic.
	 * @return  mixed                     WP_Error | string
	 */
	private function research_ai_writer( $topic ) {

		// Get API key.
		$api_key = $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-research', 'ai_writer_api_key', false );

		// Bail if no API Key defined.
		if ( ! $api_key ) {
			return new WP_Error(
				'page_generator_pro_research_process_ai_writer_error',
				__( 'No API key was configured in the Plugin\'s Settings', 'page-generator-pro' )
			);
		}

		// Set API Key.
		$this->base->get_class( 'ai_writer' )->set_api_key( $api_key );

		// Send request.
		$result = $this->base->get_class( 'ai_writer' )->put_research_request( $topic );

		// If an error occured, bail.
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Return data of request ID and estimated time needed.
		return array(
			'id'             => $result->id,
			'estimated_time' => $result->estimated_time_needed,
			'message'        => sprintf(
				/* translators: Calculated human readable duration/time */
				__( 'Estimated time for completion is %s. Please wait whilst AI Writer completes this process.', 'page-generator-pro' ),
				human_readable_duration( gmdate( 'i:s', $result->estimated_time_needed ) )
			),
		);

	}

	/**
	 * Returns the status of a previously researched topic from AI Writer
	 *
	 * @since   2.8.9
	 *
	 * @param   string $id     ID.
	 * @return  mixed           WP_Error | string
	 */
	private function get_status_ai_writer( $id ) {

		// Get API key.
		$api_key = $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-research', 'ai_writer_api_key', false );

		// Bail if no API Key defined.
		if ( ! $api_key ) {
			return new WP_Error(
				'page_generator_pro_research_get_status_ai_writer_error',
				__( 'No API key was configured in the Plugin\'s Settings', 'page-generator-pro' )
			);
		}

		// Set API Key.
		$this->base->get_class( 'ai_writer' )->set_api_key( $api_key );

		// Send request.
		$result = $this->base->get_class( 'ai_writer' )->get_research_result( $id );

		// If an error occured, bail.
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Define default status array.
		$status = array(
			'id'        => $result->request->id,
			'completed' => $result->request->done,
			'content'   => '',
			'message'   => '',
		);

		// If the research isn't completed, return.
		if ( ! $status['completed'] ) {
			$status['message'] = sprintf(
				/* translators: Calculated human readable duration/time */
				__( 'Estimated time for completion is %s. Please wait whilst AI Writer completes this process.', 'page-generator-pro' ),
				human_readable_duration( gmdate( 'i:s', $result->request->estimated_time_needed ) )
			);
			return $status;
		}

		// Build paragraphs.
		$paragraphs = array();
		foreach ( $result->result->article as $index => $paragraph ) {
			$paragraphs[] = $paragraph->paragraph_text;
		}
		$status['content'] = $paragraphs;
		$status['message'] = __( 'Research completed successfully.', 'page-generator-pro' );

		// Return.
		return $status;

	}

	/**
	 * Sends the topic to ContentBot's research endpoint, for ContentBot to build content
	 * and return the result.
	 *
	 * @since   3.5.5
	 *
	 * @param   string $topic            Topic.
	 * @return  mixed                     WP_Error | string
	 */
	public function research_contentbot( $topic ) {

		// Get API key.
		$api_key = $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-research', 'contentbot_api_key', false );

		// Bail if no API Key defined.
		if ( ! $api_key ) {
			return new WP_Error(
				'page_generator_pro_research_process_contentbot_error',
				__( 'No API key was configured in the Plugin\'s Settings', 'page-generator-pro' )
			);
		}

		// Set API Key.
		$this->base->get_class( 'contentbot' )->set_api_key( $api_key );

		// Send request.
		$result = $this->base->get_class( 'contentbot' )->get_topic_content( $topic );

		// If an error occured, bail.
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Return data.
		return array(
			'id'        => 0, // ContentBot doesn't use an ID.
			'completed' => true, // We get an immediate result, so return it.
			'content'   => $result,
			'message'   => __( 'Research completed successfully.', 'page-generator-pro' ),
		);

	}

}
