<?php
if ( ! class_exists( 'WPAI_WP_Job_Manager_Jobs_Field_Factory' ) ) {	
    class WPAI_WP_Job_Manager_Jobs_Field_Factory {
        
        protected $add_on;
        
        public function __construct( RapidAddon $addon_object ) {
            $this->add_on = $addon_object;
        }
        
        public function add_field( $field_type ) {
            switch ( $field_type ) {
                case 'job_location':
                    $this->job_location();
                    break;

                case 'job_details':
                    $this->job_details();
                    break;
                
                default:
                    break;
            }
        }

        public function job_location() {
            $this->add_on->add_field(
                '_job_location',
                'Location',
                'radio',
                array(
                    'search_by_address' => array(
                        'Search by Address',
                        $this->add_on->add_options(
                            $this->add_on->add_field(
                                'job_address',
                                'Job Address',
                                'text'
                            ),
                            'Google Geocode API Settings',
                            array(
                                $this->add_on->add_field(
                                    'address_geocode',
                                    'Request Method',
                                    'radio',
                                    array(
                                        'address_google_developers' => array(
                                            'Google Developers API Key - <a href="https://developers.google.com/maps/documentation/geocoding/#api_key">Get free API key</a>',
                                            $this->add_on->add_field(
                                                'address_google_developers_api_key', 
                                                'API Key', 
                                                'text'
                                            ),
                                            'Up to 2,500 requests per day and 5 requests per second.'
                                        ),
                                        'address_google_for_work' => array(
                                            'Google for Work Client ID & Digital Signature - <a href="https://developers.google.com/maps/documentation/business">Sign up for Google for Work</a>',
                                            $this->add_on->add_field(
                                                'address_google_for_work_client_id', 
                                                'Google for Work Client ID', 
                                                'text'
                                            ), 
                                            $this->add_on->add_field(
                                                'address_google_for_work_digital_signature', 
                                                'Google for Work Digital Signature', 
                                                'text'
                                            ),
                                            'Up to 100,000 requests per day and 10 requests per second'
                                        )
                                    ) // end Request Method options array
                                ), // end Request Method nested radio field 

                            ) // end Google Geocode API Settings fields
                        ) // end Google Gecode API Settings options panel
                    ), // end Search by Address radio field
                    'search_by_coordinates' => array(
                        'Enter Coordinates',
                        $this->add_on->add_field(
                            'job_lat', 
                            'Latitude', 
                            'text', 
                            null, 
                            'Example: 34.0194543'
                        ),
                        $this->add_on->add_options( 
                            $this->add_on->add_field(
                                'job_lng', 
                                'Longitude', 
                                'text', 
                                null, 
                                'Example: -118.4911912'
                            ), 
                            'Google Geocode API Settings', 
                            array(
                                $this->add_on->add_field(
                                    'coord_geocode',
                                    'Request Method',
                                    'radio',
                                    array(
                                        'coord_google_developers' => array(
                                            'Google Developers API Key - <a href="https://developers.google.com/maps/documentation/geocoding/#api_key">Get free API key</a>',
                                            $this->add_on->add_field(
                                                'coord_google_developers_api_key', 
                                                'API Key', 
                                                'text'
                                            ),
                                            'Up to 2,500 requests per day and 5 requests per second.'
                                        ),
                                        'coord_google_for_work' => array(
                                            'Google for Work Client ID & Digital Signature - <a href="https://developers.google.com/maps/documentation/business">Sign up for Google for Work</a>',
                                            $this->add_on->add_field(
                                                'coord_google_for_work_client_id', 
                                                'Google for Work Client ID', 
                                                'text'
                                            ), 
                                            $this->add_on->add_field(
                                                'coord_google_for_work_digital_signature', 
                                                'Google for Work Digital Signature', 
                                                'text'
                                            ),
                                            'Up to 100,000 requests per day and 10 requests per second'
                                        )
                                    ) // end Geocode API options array
                                ), // end Geocode nested radio field 
                            ) // end Geocode settings
                        ) // end coordinates Option panel
                    ) // end Search by Coordinates radio field
                ),
                'Leave this blank if the location is not important.' // end Job Location radio field
            );
        }
        
        public function job_details() {
            $this->add_on->add_field( '_company_name', 'Company Name', 'text' );
            $this->add_on->add_field( '_company_tagline', 'Company Tagline', 'text' );
            $this->add_on->add_field( '_application', 'Application email/URL', 'text', null, 'This field is required for the "application" area to appear beneath the listing.' );
            $this->add_on->add_field( '_company_website', 'Company Website', 'text' );
            $this->add_on->add_field( '_company_twitter', 'Company Twitter', 'text' );

            $this->add_on->disable_default_images();
            $this->add_on->add_field( 'company_featured_image', 'Company Logo', 'image' );

            $this->add_on->add_field( 'video_type', 'Company Video', 'radio',
                array(
                    'external' => array(
                        'Externally Hosted',
                        $this->add_on->add_field( '_company_video_url', 'Video URL', 'text')
                    ),
                    'local' => array(
                        'Locally Hosted',
                        $this->add_on->add_field( '_company_video_id', 'Upload Video', 'file')
                    )
                )
            );

            $this->add_on->add_field( '_job_expires', 'Listing Expiry Date', 'text', null, 'Use any format supported by the PHP <b>strtotime</b> function. That means pretty much any human-readable date will work.');

            $this->add_on->add_field( '_filled', 'Filled', 'radio', 
                array(
                    '0' => 'No',
                    '1' => 'Yes'
                ),
                'Filled listings will no longer accept applications.'
            );

            $this->add_on->add_field( '_featured', 'Featured Listing', 'radio', 
                array(
                    '0' => 'No',
                    '1' => 'Yes'
                ),
                'Featured listings will be sticky during searches, and can be styled differently.'
            );
        }
    }
}
