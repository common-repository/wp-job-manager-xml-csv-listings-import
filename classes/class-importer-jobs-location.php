<?php
if ( ! class_exists( 'WPAI_WP_Job_Manager_Job_Location_Importer' ) ) {
    class WPAI_WP_Job_Manager_Job_Location_Importer extends WPAI_WP_Job_Manager_Add_On_Importer {
        
		protected $add_on;
		public $helper;

        public function __construct( RapidAddon $addon_object ) {
			$this->add_on = $addon_object;
			$this->helper = new WPAI_WP_Job_Manager_Add_On_Helper();

		}

        public function import( $post_id, $data, $import_options, $article ) {
			$field            = '_job_location';
			$address          = $data['job_address'];
			$lat              = $data['job_lat'];
			$long             = $data['job_lng'];
			$street_name      = null;
			$street_number    = null;
			$country          = null;
			$zip              = null;			
			$api_key          = null;
			$empty_api_key    = false;
			$geocoding_failed = false;
			$job_manager_api_key = $this->helper->get_job_manager_add_on_google_api_key();

			$this->helper->google_api_constants();

			//  Build search query
			if ( $data['_job_location'] == 'search_by_address' ) {

				$search = ( !empty( $address ) ? 'address=' . rawurlencode( $address ) : null );

			} else {

				$search = ( !empty( $lat ) && !empty( $long ) ? 'latlng=' . rawurlencode( $lat . ',' . $long ) : null );

			}

			// Build API key
			if ( $data['_job_location'] == 'search_by_address' ) {

				if ( $data['address_geocode'] == 'address_google_developers' ) {

					if ( !empty( $data['address_google_developers_api_key'] ) )  {
						$api_key = '&key=' . $data['address_google_developers_api_key'];
					} else {
						if ( $job_manager_api_key ) {
							$this->helper->log( '- Obtaining Google Maps API key from the WP Job Manager settings');
							$api_key = '&key=' . $job_manager_api_key;
						}
					}

				} elseif ( $data['address_geocode'] == 'address_google_for_work' && !empty( $data['address_google_for_work_client_id'] ) && !empty( $data['address_google_for_work_digital_signature'] ) ) {

					$api_key = '&client=' . $data['address_google_for_work_client_id'] . '&signature=' . $data['address_google_for_work_digital_signature'];

				}

			} else {

				if ( $data['coord_geocode'] == 'coord_google_developers' ) {

					if ( !empty( $data['coord_google_developers_api_key'] ) ) {
						$api_key = '&key=' . $data['coord_google_developers_api_key'];
					} else {
						if ( $job_manager_api_key ) {
							$this->helper->log( '- Obtaining Google Maps API key from the WP Job Manager settings');
							$api_key = '&key=' . $job_manager_api_key;
						}
					}

				} elseif ( $data['coord_geocode'] == 'coord_google_for_work' && !empty( $data['coord_google_for_work_client_id'] ) && !empty( $data['coord_google_for_work_digital_signature'] ) ) {

					$api_key = '&client=' . $data['coord_google_for_work_client_id'] . '&signature=' . $data['coord_google_for_work_digital_signature'];

				}

			}

			// Store _job_location value for later use

			if ( $data['_job_location'] == 'search_by_address' ) {
				$partial_address = $address;
			}

			// if all fields are updateable and $search has a value
			if ( empty( $article['ID'] ) or ( $this->can_update_meta( $field, $import_options ) && $this->can_update_meta( '_job_location', $import_options ) && !empty ( $search ) ) ) {

				$this->helper->log( 'Updating Map Location' );

				if ( empty( $api_key ) ) {
					// You can't use the geocoding API without an API key anymore. Fail.
					$this->fail( 'empty_api_key' );
					return;
				}

				// Build $request_url for API call
				$request_url = 'https://maps.googleapis.com/maps/api/geocode/json?' . $search . $api_key;

				$curl = curl_init();

				curl_setopt( $curl, CURLOPT_URL, $request_url );

				curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );

				$response = curl_exec( $curl );

				curl_close( $curl );

				if ( substr( $response, 0, strlen( GOOGLE_FOR_WORK_AUTH_ERROR_MESSAGE ) ) === GOOGLE_FOR_WORK_AUTH_ERROR_MESSAGE ) {
					$this->fail( 'google_for_work_auth_error' );
					return;
				}

				$json = $response;

				// Parse API response
				if ( !empty( $json ) ) {

					$details = json_decode( $json, true );

					if ( array_key_exists( 'status', $details ) ) {
						if ( $details['status'] == 'INVALID_REQUEST' || $details['status'] == 'ZERO_RESULTS' || $details['status'] == 'REQUEST_DENIED' || $details['status'] == 'OVER_QUERY_LIMIT' ) {
							// The request didn't work, go to fail.
							$this->fail( 'error', $details );
							return;
						}
					}
					
					$address_data = array(
						'street_number' => '',
						'route' => '',
						'locality' => '',
						'country_short_name' => '',
						'country_long_name' => '',
						'postal_code' => '',
						'administrative_area_level_1_short_name' => '',
						'administrative_area_level_1_long_name' => ''
					);

					if ( ! empty($details['results'][0]['address_components']) ){
		
						foreach ( $details['results'][0]['address_components'] as $type ) {
							// Went for type_name here to try to make the if statement a bit shorter,
							// and hopefully clearer as well
							$type_name = $type['types'][0];
							
							if ($type_name == 'administrative_area_level_1' || $type_name == 'administrative_area_level_2' || $type_name == 'country') {
								// short_name & long_name must be stored for these three field types, as
								// the short & long names are stored by WP Job Manager
								$address_data[ $type_name . '_short_name' ] = $type['short_name'];
								$address_data[ $type_name . '_long_name' ] = $type['long_name'];
							} else {
								// The rest of the data from Google Maps can be returned in long format,
								// as the other fields only store data in that format
								$address_data[ $type_name ] = $type['long_name'];
							}
						}
					}

					// It's a long list, but this is what WP Job Manager stores in the database
					$geo_status = ($details['status'] == 'ZERO_RESULTS') ? 0 : 1;
					$lat  = $details['results'][0]['geometry']['location']['lat'];
					$long = $details['results'][0]['geometry']['location']['lng'];
					$address = $details['results'][0]['formatted_address'];
					$street_number = $address_data['street_number'];
					$street = $address_data['route'];
					$city = $address_data['locality'];
					$country_short = $address_data['country_short_name'];
					$country_long = $address_data['country_long_name'];
					$zip = $address_data['postal_code'];
					
					// Important because the 'geolocation_state_short' & 'geolocation_state_long' fields
					// can get data from 'administrative_area_level_1' or 'administrative_area_level_2',
					// depending on the address that's provided
					$state_short = !empty( $address_data['administrative_area_level_1_short_name'] ) ? $address_data['administrative_area_level_1_short_name'] : $address_data['administrative_area_level_2_short_name'];
					
					$state_long = !empty( $address_data['administrative_area_level_1_long_name'] ) ? $address_data['administrative_area_level_1_long_name'] : $address_data['administrative_area_level_2_long_name'];

					if ( $data['_job_location'] == 'search_by_address' ) {

						$job_location = $partial_address;

					} else {

						// When geocoding from lat/long, use "City, State" format as the job location

						$job_location = $city . ', ' . $state_short;

					}

					// Update location fields
					$fields = array(
						'geolocation_lat' => $lat,
						'geolocation_long' => $long,
						'geolocation_formatted_address' => $address,
						'geolocation_street_number' => $street_number,
						'geolocation_street' => $street,
						'geolocation_city' => $city,
						'geolocation_state_short' => $state_short,
						'geolocation_state_long' => $state_long,
						'geolocation_postcode' => $zip,
						'geolocation_country_short' => $country_short,
						'geolocation_country_long' => $country_long,
						'_job_location' => $job_location
					);

					$this->helper->log( '- Got location data from Geocoding API: ' . $request_url );
					$serialized_geocoding_data = json_encode( $fields );
					$this->helper->log( '- Geocoding data received: ' . $serialized_geocoding_data );
					$this->helper->log( '- Updating latitude and longitude' );

					// Check if 'geolocated' field should be created or deleted
					if ($geo_status == '0') {
						delete_post_meta( $post_id, 'geolocated' );
					} elseif ($geo_status == '1') {
						$this->helper->update_meta( $post_id, 'geolocated', $geo_status );
					} else {
						// Do nothing, it's possible that we didn't get a response from the Google Maps API.
					}

					foreach ( $fields as $key => $value ) {

						if ( empty( $article['ID'] ) or $this->can_update_meta( $key, $import_options ) ) {
							$this->helper->update_meta( $post_id, $key, $value );
						}
					}
				}
			}
		}

        public function fail( $type, $details = array() ) {
            switch ( $type ) {
                case 'empty_api_key':
                    $this->helper->log( 'WARNING Geocoding failed because there is no API key in the import template.' );
                    break;

                case 'error':
                    $this->helper->log( 'WARNING Geocoding failed with status: ' . $details['status'] );
                    if ( array_key_exists( 'error_message', $details ) ) {
                        $this->helper->log( 'WARNING Geocoding error message: ' . $details['error_message'] );
                    }
					break;

				case 'google_for_work_auth_error':
					$this->helper->log( 'WARNING Geocoding failed because authentication details are invalid.' );
					break;
            }
        }
    }
}
