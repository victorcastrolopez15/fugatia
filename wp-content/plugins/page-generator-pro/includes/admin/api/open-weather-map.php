<?php
/**
 * OpenWeatherMap API Class.
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Fetch weather data from OpenWeatherMap
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.4.8
 */
class Page_Generator_Pro_Open_Weather_Map extends Page_Generator_Pro_API {

	/**
	 * Holds the base object.
	 *
	 * @since   2.4.8
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Holds the API Key
	 *
	 * @since   2.4.8
	 *
	 * @var     string
	 */
	public $api_key = '44cd0f66dbf150164a4289bfc29fa565';

	/**
	 * Holds the API endpoint
	 *
	 * @since   2.4.8
	 *
	 * @var     string
	 */
	public $api_endpoint = 'http://api.openweathermap.org/';

	/**
	 * Constructor.
	 *
	 * @since   2.4.8
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

	}

	/**
	 * Returns the supported Forecast Types
	 *
	 * @since   2.5.1
	 *
	 * @return  array   Forecast Types
	 */
	public function get_forecast_types() {

		return array(
			13 => __( 'Small', 'page-generator-pro' ),
			16 => __( 'Medium', 'page-generator-pro' ),
			17 => __( 'Medium with Details', 'page-generator-pro' ),
			12 => __( 'Large', 'page-generator-pro' ),
			11 => __( 'Large with Details', 'page-generator-pro' ),
			18 => __( 'Banner', 'page-generator-pro' ),
			19 => __( 'Banner Alternative', 'page-generator-pro' ),
		);

	}

	/**
	 * Returns the supported Temperature Units
	 *
	 * @since   2.5.1
	 *
	 * @return  array   Tempoerature Units
	 */
	public function get_temperature_units() {

		return array(
			'imperial' => __( 'Imperial (Farenheight)', 'page-generator-pro' ),
			'metric'   => __( 'Metric (Celcius)', 'page-generator-pro' ),
		);

	}

	/**
	 * Returns the City ID for the given Location (City or ZIP Code) and Country,
	 * which can then be used for subsequent API queries or the JS widget.
	 *
	 * @since   2.4.8
	 *
	 * @param   string $location       Location (City, ZIP Code).
	 * @param   string $country_code   Country Code.
	 * @return  mixed                   WP_Error | int
	 */
	public function get_city_id( $location, $country_code ) {

		// Run the query.
		$results = $this->response(
			$this->get(
				'data/2.5/weather',
				array(
					'q'     => $location . ',' . $country_code,
					'APPID' => $this->api_key,
				)
			)
		);

		// Bail if an error occured.
		if ( is_wp_error( $results ) ) {
			return $results;
		}

		// Bail if no ID in the results.
		if ( ! isset( $results->id ) || empty( $results->id ) ) {
			return new WP_Error(
				'page_generator_pro_open_weather_map_error',
				sprintf(
					/* translators: Location and Country Code */
					__( 'OpenWeatherMap: No Location ID could be found for %s', 'page-generator-pro' ),
					$location . ',' . $country_code
				)
			);
		}

		// Return City ID.
		return $results->id;

	}

	/**
	 * Returns the City ID for the given ZIP Code and Country,
	 * which can then be used for subsequent API queries or the JS widget.
	 *
	 * This fetches the City Name for the ZIP Code, and then uses that
	 * in the usual get_city_id() call.
	 *
	 * @since   3.3.0
	 *
	 * @param   string $zip_code       ZIP Code.
	 * @param   string $country_code   Country Code.
	 * @return  mixed                   WP_Error | int
	 */
	public function get_city_id_by_zip_code( $zip_code, $country_code ) {

		// Run the query to fetch the City for the ZIP Code.
		$results = $this->response(
			$this->get(
				'data/2.5/weather',
				array(
					'zip'   => $zip_code . ',' . $country_code,
					'APPID' => $this->api_key,
				)
			)
		);

		// Bail if an error occured.
		if ( is_wp_error( $results ) ) {
			return $results;
		}

		// Bail if no name in the results.
		if ( ! isset( $results->name ) || empty( $results->name ) ) {
			return new WP_Error(
				'page_generator_pro_open_weather_map_error',
				sprintf(
					/* translators: Location and Country Code */
					__( 'OpenWeatherMap: City could not be established for ZIP Code %s', 'page-generator-pro' ),
					$zip_code . ',' . $country_code
				)
			);
		}

		// Return the resuts of get_city_id().
		return $this->get_city_id( $results->name, $country_code );

	}

	/**
	 * Inspects the response from the API call, returning an error
	 * or data
	 *
	 * @since   2.8.9
	 *
	 * @param   mixed $response   Response (WP_Error | object).
	 * @return  mixed               WP_Error | object
	 */
	private function response( $response ) {

		// If the response is an error, return it.
		if ( is_wp_error( $response ) ) {
			return new WP_Error(
				'page_generator_pro_open_weather_map_error',
				sprintf(
					/* translators: Error message */
					__( 'OpenWeatherMap: %s', 'page-generator-pro' ),
					$response->get_error_message()
				)
			);
		}

		// If the response contains an error message, return it.
		if ( isset( $response->message ) ) {
			return new WP_Error(
				'page_generator_pro_open_weather_map_error',
				sprintf(
					/* translators: Error message */
					__( 'OpenWeatherMap: %s', 'page-generator-pro' ),
					$response->message
				)
			);
		}

		// Return successful response data.
		return $response;

	}

}
