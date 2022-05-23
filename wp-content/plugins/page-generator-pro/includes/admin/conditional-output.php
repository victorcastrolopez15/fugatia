<?php
/**
 * Conditional Output Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Parse conditional @if statements in a string, to determine the
 * required output.
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 3.2.8
 */
class Page_Generator_Pro_Conditional_Output {

	/**
	 * Holds the base object.
	 *
	 * @since   3.2.8
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Constructor.
	 *
	 * @since   3.2.8
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

	}

	/**
	 * Parses conditional statements which may be in the supplied text
	 *
	 * @since   3.2.8
	 *
	 * @param   string $text   Text.
	 * @return  string          Text with conditional statements evaluated/processed
	 */
	public function process( $text ) {

		// Bail if no directives exist.
		if ( strpos( $text, '@if' ) === false ) {
			return $text;
		}

		// Parse #p# and #s# not inside a #section#.
		$processed_text = preg_replace_callback(
			'/\@if\((.*?)\)(.*?)\@endif/si',
			array( $this, 'parse_if_statement' ),
			$text
		);

		// Return.
		return $this->format_and_return( $text, $processed_text );

	}

	/**
	 * Parses an individual @if statement
	 *
	 * Paragraphs are shuffled at random within each section.
	 *
	 * @since   3.2.8
	 *
	 * @param   array $matches    preg_match_all matches.
	 *                                [0] full statement, including @if, condition and text.
	 *                                [1] condition within @if statement.
	 *                                [2] text within statement.
	 * @return  string              Content
	 */
	private function parse_if_statement( $matches ) {

		// Define pass and fail values to return based on the result of performing the comparison.
		if ( strpos( $matches[2], '@else' ) !== false ) {
			list( $pass, $fail ) = explode( '@else', $matches[2] );
		} else {
			$pass = $matches[2];
			$fail = '';
		}

		// Get comparison operator.
		$comparison_operator = $this->get_comparison_operator( $matches[1] );

		// Test comparison.
		if ( ! $this->perform_comparison( $comparison_operator, $matches[1] ) ) {
			return trim( $fail );
		}

		return trim( $pass );

	}

	/**
	 * Returns the comparison operator detected within the given condition
	 *
	 * @since   3.2.8
	 *
	 * @param   string $condition  Condition (value1==value2, value1!=value2 etc).
	 * @return  mixed               Comparison Operator (false = no supported comparison operator found)
	 */
	private function get_comparison_operator( $condition ) {

		// Iterate through comparison operators until one is found in the condition.
		foreach ( array_keys( $this->base->get_class( 'common' )->get_comparison_operators() ) as $comparison_operator ) {
			if ( strpos( $condition, $comparison_operator ) !== false ) {
				return $comparison_operator;
			}
		}

		// If here, no comparison operator was found.
		return false;

	}

	/**
	 * Performs the comparison
	 *
	 * @since   3.2.8
	 *
	 * @param   string $comparison_operator    Comparison Operator (e.g. ==).
	 * @param   string $statement              Statement, including comparison operator (e.g. 1 == 2).
	 * @return  bool                            Comparison Passed
	 */
	private function perform_comparison( $comparison_operator, $statement ) {

		$left  = '';
		$right = '';
		if ( ! empty( $comparison_operator ) ) {
			list( $left, $right ) = explode( $comparison_operator, $statement );
		} else {
			$left = $statement;
		}

		// Trim strings.
		$left  = trim( $left );
		$right = trim( $right );

		// Perform comparison.
		switch ( $comparison_operator ) {
			case '==':
			case '=':
				return ( $left == $right ); // phpcs:ignore
			case '!=':
				return ( $left != $right ); // phpcs:ignore
			case '>':
				return ( $left > $right );
			case '>=':
				return ( $left >= $right );
			case '<':
				return ( $left < $right );
			case '<=':
				return ( $left <= $right );
			case 'LIKE':
				if ( stripos( $left, $right ) === false ) {
					return false;
				}
				return true;
			case 'NOT LIKE':
				if ( stripos( $left, $right ) === false ) {
					return true;
				}
				return false;
			default:
				return ! empty( $left );
		}

	}

	/**
	 * Sanity checks the processed output, and performs some formatting to it
	 *
	 * @since   3.2.8
	 *
	 * @param   string $original_text   Original Block Spintax.
	 * @param   string $processed_text  Processed Block Spintax.
	 * @return  string                  Spun Result
	 */
	private function format_and_return( $original_text, $processed_text ) {

		// Trim.
		$processed_text = trim( $processed_text );

		// If the processed text is the same as the original text, just return the original text.
		if ( $processed_text === $original_text ) {
			return $original_text;
		}

		// Strip any double spaces.
		$processed_text = str_replace( '  ', ' ', $processed_text );

		// Strip any spaces immediately after a paragraph tag.
		$processed_text = str_replace( '<p> ', '<p>', $processed_text );

		// Return.
		return $processed_text;

	}

}
