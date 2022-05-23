<?php
/**
 * EXIF Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * EXIF class wrapper for lsolesen\pel EXIF package
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.3.9
 */
class Page_Generator_Pro_Exif {

	/**
	 * Holds the base object.
	 *
	 * @since   2.3.9
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Constructor
	 *
	 * @since   2.3.9
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

	}

	/**
	 * Writes EXIF data to the given image path and file, preserving existing EXIF data
	 *
	 * @since   2.3.9
	 *
	 * @param   int     $image_id       WordPress Media Library Attachment ID.
	 * @param   string  $description    Image Description.
	 * @param   string  $comment        Image Comment.
	 * @param   decimal $latitude       Latitude.
	 * @param   decimal $longitude      Longitude.
	 *
	 * @throws  Exception               Error.
	 *
	 * @return  mixed                       WP_Error | true
	 */
	public function write( $image_id, $description = false, $comment = false, $latitude = false, $longitude = false ) {

		// If all of our attributes are empty or false, don't write anything.
		// This retains the original EXIF metadata on the image.
		if ( ! $description && ! $comment && ! $latitude && ! $longitude ) {
			return true;
		}

		// Get image.
		$image = get_attached_file( $image_id );

		// Bail if image doesn't exist.
		if ( ! file_exists( $image ) ) {
			return new WP_Error(
				'page_generator_pro_exif_write_error',
				sprintf(
					/* translators: Image path and filename */
					__( 'EXIF: %s does not exist on the server.', 'page-generator-pro' ),
					$image
				)
			);
		}

		// Silently exit if the file type isn't supported, as we don't need to do anything.
		$file_type = wp_check_filetype( $image );
		if ( ! in_array( $file_type['type'], $this->get_supported_file_types(), true ) ) {
			return true;
		}

		try {
			// Read Exif data.
			$data = new lsolesen\pel\PelDataWindow( file_get_contents( $image ) ); // phpcs:ignore

			// Determine if it's JPEG or TIFF data.
			if ( lsolesen\pel\PelJpeg::isValid( $data ) ) {
				$jpeg = new lsolesen\pel\PelJpeg();
				$file = $jpeg;
				$jpeg->load( $data );

				// Get Exif data.
				$exif = $jpeg->getExif();

				// If no Exif data exists, create an empty structure we can use.
				if ( $exif === null ) {
					// Create and add empty Exif data to the image.
					$exif = new lsolesen\pel\PelExif();
					$jpeg->setExif( $exif );

					// Create and add TIFF data to the Exif data.
					// Exif data is stored in a TIFF format.
					$tiff = new lsolesen\pel\PelTiff();
					$exif->setTiff( $tiff );
				} else {
					// Use existing data.
					$tiff = $exif->getTiff();
				}
			} elseif ( lsolesen\pel\PelTiff::isValid( $data ) ) {
				$tiff = new lsolesen\pel\PelTiff();
				$file = $tiff;
				$tiff->load( $data );
			} else {
				// Something went wrong.
				throw new Exception( __( 'Unrecoginzed image format.', 'page-generator-pro' ) );
			}

			// Bail if no TIFF data.
			if ( ! isset( $tiff ) || is_null( $tiff ) ) {
				throw new Exception( __( 'Unrecoginzed image format.', 'page-generator-pro' ) );
			}

			// See https://www.media.mit.edu/pia/Research/deepview/exif.html#ExifTags for IDF0 and IDF1 structure.

			// Get root IFD, called IFD0 (main image).
			// If it doesn't exist, create it.
			$ifd0 = $tiff->getIfd();
			if ( $ifd0 === null ) {
				$ifd0 = new lsolesen\pel\PelIfd( lsolesen\pel\PelIfd::IFD0 );
				$tiff->setIfd( $ifd0 );
			}

			// Get EXIF sub IFD.
			// If it doesn't exist, create it.
			$subifd = $ifd0->getSubIfd( lsolesen\pel\PelIfd::EXIF );
			if ( $subifd === null ) {
				$ifd0->addSubIfd( new lsolesen\pel\PelIfd( lsolesen\pel\PelIfd::EXIF ) );
				$subifd = $ifd0->getSubIfd( lsolesen\pel\PelIfd::EXIF );
			}

			// Description.
			if ( $description ) {
				$this->set_description( $description, $ifd0 );
			}

			// User Comment.
			if ( $comment ) {
				$this->set_comment( $comment, $subifd );
			}

			// Latitude and Longitude.
			if ( $latitude || $longitude ) {
				$this->set_latitude_longitude( $latitude, $longitude, $ifd0 );
			}

			// Save changes to image file.
			return $file->saveFile( $image );
		} catch ( Exception $e ) {
			return new WP_Error(
				'page_generator_pro_exif_write_error',
				sprintf(
					/* translators: Error message */
					__( 'EXIF: Error when writing data: %s', 'page-generator-pro' ),
					$e->getMessage()
				)
			);
		}
	}

	/**
	 * Returns an array of file types that support EXIF metadata
	 *
	 * @since   2.5.0
	 *
	 * @return  array   Supported File Types
	 */
	private function get_supported_file_types() {

		$file_types = array(
			'image/jpeg',
			'image/tiff',
		);

		return $file_types;

	}

	/**
	 * Set Camera Make EXIF data
	 *
	 * @since   2.5.0
	 *
	 * @param   string $value  Camera Make.
	 * @param   object $ifd0   EXIF Data Object.
	 */
	private function set_camera_make( $value, $ifd0 ) {

		$existing = $ifd0->getEntry( lsolesen\pel\PelTag::MAKE );
		if ( $existing === null ) {
			$entry = new lsolesen\pel\PelEntryAscii( lsolesen\pel\PelTag::MAKE, $value );
			$ifd0->addEntry( $entry );
		} else {
			$existing->setValue( $value );
		}

	}

	/**
	 * Set Camera Model EXIF data
	 *
	 * @since   2.5.0
	 *
	 * @param   string $value  Camera Model.
	 * @param   object $ifd0   EXIF Data Object.
	 */
	private function set_camera_model( $value, $ifd0 ) {

		$existing = $ifd0->getEntry( lsolesen\pel\PelTag::MODEL );
		if ( $existing === null ) {
			$ifd0->addEntry( new lsolesen\pel\PelEntryAscii( lsolesen\pel\PelTag::MODEL, $value ) );
		} else {
			$existing->setValue( $value );
		}

	}

	/**
	 * Set Created Timestamp EXIF data
	 *
	 * @since   2.5.0
	 *
	 * @param   integer $value      Created Timestamp.
	 * @param   object  $subifd     EXIF SubIFD Data Object.
	 */
	private function set_created_timestamp( $value, $subifd ) {

		$existing = $subifd->getEntry( lsolesen\pel\PelTag::DATE_TIME_ORIGINAL );
		if ( $existing === null ) {
			$subifd->addEntry( new lsolesen\pel\PelEntryTime( lsolesen\pel\PelTag::DATE_TIME_ORIGINAL, $value ) );
		} else {
			$existing->setValue( $value );
		}

	}

	/**
	 * Set Author EXIF data
	 *
	 * @since   2.5.0
	 *
	 * @param   string $value  Author.
	 * @param   object $ifd0   EXIF Data Object.
	 */
	private function set_author( $value, $ifd0 ) {

		$existing = $ifd0->getEntry( lsolesen\pel\PelTag::ARTIST );
		if ( $existing === null ) {
			$ifd0->addEntry( new lsolesen\pel\PelEntryAscii( lsolesen\pel\PelTag::ARTIST, $value ) );
		} else {
			$existing->setValue( $value );
		}

	}

	/**
	 * Set Comment EXIF data
	 *
	 * @since   2.5.0
	 *
	 * @param   string $value      Comment.
	 * @param   object $subifd     EXIF SubIFD Data Object.
	 */
	private function set_comment( $value, $subifd ) {

		$existing = $subifd->getEntry( lsolesen\pel\PelTag::USER_COMMENT );
		if ( $existing === null ) {
			$subifd->addEntry( new lsolesen\pel\PelEntryUserComment( $value, 'Unicode' ) );
		} else {
			$existing->setValue( $value, 'Unicode' );
		}

	}

	/**
	 * Set Copyright EXIF data
	 *
	 * @since   2.5.0
	 *
	 * @param   string $value      Copyright.
	 * @param   object $ifd0       EXIF Data Object.
	 */
	private function set_copyright( $value, $ifd0 ) {

		$existing = $ifd0->getEntry( lsolesen\pel\PelTag::COPYRIGHT );
		if ( $existing === null ) {
			$ifd0->addEntry( new lsolesen\pel\PelEntryCopyright( lsolesen\pel\PelTag::COPYRIGHT, $value ) );
		} else {
			$existing->setValue( $value );
		}

	}

	/**
	 * Set Description EXIF data
	 *
	 * @since   2.5.0
	 *
	 * @param   string $value      Description.
	 * @param   object $ifd0       EXIF Data Object.
	 */
	private function set_description( $value, $ifd0 ) {

		$existing = $ifd0->getEntry( lsolesen\pel\PelTag::IMAGE_DESCRIPTION );
		if ( $existing === null ) {
			$ifd0->addEntry( new lsolesen\pel\PelEntryAscii( lsolesen\pel\PelTag::IMAGE_DESCRIPTION, $value ) );
		} else {
			$existing->setValue( $value );
		}

	}

	/**
	 * Set Latitude and Longitude EXIF data
	 *
	 * @since   2.3.9
	 *
	 * @param   decimal $latitude   Latitude.
	 * @param   decimal $longitude  Longitude.
	 * @param   object  $ifd0       EXIF Data Object.
	 */
	private function set_latitude_longitude( $latitude, $longitude, $ifd0 ) {

		// Create a sub-IFD for holding GPS information. GPS data must be below the first IFD.
		$gps_ifd = new lsolesen\pel\PelIfd( lsolesen\pel\PelIfd::GPS );
		$ifd0->addSubIfd( $gps_ifd );

		$gps_ifd->addEntry( new lsolesen\pel\PelEntryByte( lsolesen\pel\PelTag::GPS_VERSION_ID, 2, 2, 0, 0 ) );

		// Convert Latitude from 12.34 to 12 20' 42".
		list( $hours, $minutes, $seconds ) = $this->convert_decimal_to_degrees_minutes_seconds( $latitude );

		// Interpret a negative latitude as being south.
		$latitude_ref = ( $latitude < 0 ) ? 'S' : 'N';

		// Write latitude.
		$gps_ifd->addEntry( new lsolesen\pel\PelEntryAscii( lsolesen\pel\PelTag::GPS_LATITUDE_REF, $latitude_ref ) );
		$gps_ifd->addEntry( new lsolesen\pel\PelEntryRational( lsolesen\pel\PelTag::GPS_LATITUDE, $hours, $minutes, $seconds ) );

		// Convert Longitude from 12.34 to 12 20' 42".
		list( $hours, $minutes, $seconds) = $this->convert_decimal_to_degrees_minutes_seconds( $longitude );

		// Interpret a negative longitude as being west.
		$longitude_ref = ( $longitude < 0 ) ? 'W' : 'E';

		// Write longitude.
		$gps_ifd->addEntry( new lsolesen\pel\PelEntryAscii( lsolesen\pel\PelTag::GPS_LONGITUDE_REF, $longitude_ref ) );
		$gps_ifd->addEntry( new lsolesen\pel\PelEntryRational( lsolesen\pel\PelTag::GPS_LONGITUDE, $hours, $minutes, $seconds ) );

	}

	/**
	 * Set Title EXIF data
	 *
	 * @since   2.5.0
	 *
	 * @param   string $value      Title.
	 * @param   object $ifd0       EXIF Data Object.
	 */
	private function set_title( $value, $ifd0 ) {

		$existing = $ifd0->getEntry( lsolesen\pel\PelTag::XP_TITLE );
		if ( $existing === null ) {
			$ifd0->addEntry( new lsolesen\pel\PelEntryWindowsString( lsolesen\pel\PelTag::XP_TITLE, $value ) );
		} else {
			$existing->setValue( $value );
		}

	}

	/**
	 * Converts a fraction to a decimal
	 *
	 * @since   2.5.0
	 *
	 * @param   string $fraction   Fraction.
	 * @return  float               Decimal
	 */
	public function fraction_to_decimal( $fraction ) {

		if ( false === strpos( $fraction, '/' ) ) {
			return $fraction;
		}

		list( $n, $d ) = explode( '/', $fraction );
		if ( ! empty( $d ) ) {
			return round( ( $n / $d ), 2 );
		}

		return $fraction;

	}

	/**
	 * Converts the given latitude degrees, minutes, seconds and reference to a latitude
	 *
	 * @since   2.5.0
	 *
	 * @param   string $degrees    Degrees.
	 * @param   string $minutes    Minutes.
	 * @param   string $seconds    Seconds.
	 * @param   string $ref        Reference.
	 * @return  decimal             Latitude
	 */
	private function convert_latitude_degrees_minutes_seconds_to_decimal( $degrees, $minutes, $seconds, $ref ) {

		// Get latitude.
		$latitude = $this->convert_degrees_minutes_seconds_to_decimal( $degrees, $minutes, $seconds );

		// Return a negative value if the ref is south.
		if ( $ref === 'S' ) {
			return -$latitude;
		}

		// Return.
		return $latitude;

	}

	/**
	 * Converts the given longitude degrees, minutes, seconds and reference to a longitude
	 *
	 * @since   2.5.0
	 *
	 * @param   string $degrees    Degrees.
	 * @param   string $minutes    Minutes.
	 * @param   string $seconds    Seconds.
	 * @param   string $ref        Reference.
	 * @return  decimal             Latitude
	 */
	private function convert_longitude_degrees_minutes_seconds_to_decimal( $degrees, $minutes, $seconds, $ref ) {

		// Get longitude.
		$longitude = $this->convert_degrees_minutes_seconds_to_decimal( $degrees, $minutes, $seconds );

		// Return a negative value if the ref is south.
		if ( $ref === 'W' ) {
			return -$longitude;
		}

		// Return.
		return $longitude;

	}

	/**
	 * Converts the given degrees, minutes and seconds to a decimal for latitude/longitude.
	 *
	 * @since   2.5.0
	 *
	 * @param   string $degrees    Degrees.
	 * @param   string $minutes    Minutes.
	 * @param   string $seconds    Seconds.
	 * @return  decimal             Latitude / Longitude
	 */
	private function convert_degrees_minutes_seconds_to_decimal( $degrees, $minutes, $seconds ) {

		// Degrees: convert to decimal from fraction.
		$degrees_parts = explode( '/', $degrees );
		if ( ! empty( $degrees_parts[1] ) ) {
			$degrees = $degrees_parts[0] / $degrees_parts[1];
		}

		// Minutes: convert to decimal from fraction.
		$minutes_parts = explode( '/', $minutes );
		if ( ! empty( $minutes_parts[1] ) ) {
			$minutes = $minutes_parts[0] / $minutes_parts[1];
		}

		// Seconds: convert to decimal from fraction.
		$seconds_parts = explode( '/', $seconds );
		if ( ! empty( $seconds_parts[1] ) ) {
			$seconds = $seconds_parts[0] / $seconds_parts[1];
		}

		// Return decimal.
		return $degrees + ( $minutes / 60 ) + ( $seconds / 3600 );

	}

	/**
	 * Converts a given latitude or longitude to degrees, minutes and seconds
	 *
	 * @since   2.5.0
	 *
	 * @param   decimal $degree     Latitude / Longitude.
	 * @return  mixed                   false | array
	 */
	private function convert_decimal_to_degrees_minutes_seconds( $degree ) {

		// Bail if the latitude/longitude isn't in a valid range.
		if ( $degree > 180 || $degree < - 180 ) {
			return false;
		}

		// Make sure number is positive.
		$degree = abs( $degree );

		// Total number of seconds.
		$seconds = $degree * 3600;

		// Whole degrees.
		$degrees = floor( $degree );

		// Subtract number of seconds taken by degrees.
		$seconds -= $degrees * 3600;

		// Number of whole minutes.
		$minutes = floor( $seconds / 60 );

		// Subtract number of seconds taken by minutes.
		$seconds -= $minutes * 60;

		// Round seconds to 1/100th second precision.
		$seconds = round( $seconds * 100, 0 );

		// Return.
		return array(
			array(
				$degrees,
				1,
			),
			array(
				$minutes,
				1,
			),
			array(
				$seconds,
				100,
			),
		);

	}

}
