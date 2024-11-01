<?php
if ( ! class_exists( 'WPAI_WP_Job_Manager_Job_Importer' ) ) {
    class WPAI_WP_Job_Manager_Job_Importer extends WPAI_WP_Job_Manager_Add_On_Importer {

		protected $add_on;
		public $helper;

        public function __construct( RapidAddon $addon_object ) {
			$this->add_on = $addon_object;
			$this->helper = new WPAI_WP_Job_Manager_Add_On_Helper();
        }

        public function import_job_details( $post_id, $data, $import_options, $article ) {
            // Import text fields

            $fields = array(
                '_company_name',
                '_company_tagline',
                '_application',
                '_company_website',
                '_company_twitter',
                '_filled',
                '_featured'
            );

            foreach ( $fields as $field ) {

		        if ( empty( $article['ID'] ) || $this->add_on->can_update_meta( $field, $import_options ) ) {

                    $this->helper->update_meta( $post_id, $field, $data[ $field ], 'post' );

		        }

            }

            // If it's a featured job listing, set "menu_order" to "-1"

            $menu_order = $data [ '_featured' ] ? -1 : 0;
            wp_update_post( [ 'ID' => $post_id, 'menu_order' => $menu_order ] );

            // Import company logo

            $field = 'company_featured_image';

            if ( empty( $article['ID'] ) or $this->add_on->can_update_image( $import_options ) ) {
                $attachment_id = $data[$field]['attachment_id'];
                set_post_thumbnail( $post_id, $attachment_id );
            }
            
            // Import company video

            if ( empty( $article['ID'] ) or $this->add_on->can_update_meta( '_company_video', $import_options ) ) {
                if ( $data['video_type'] == 'external' ) {
                    $this->helper->update_meta( $post_id, '_company_video', $data['_company_video_url'] );
                } elseif ( $data['video_type'] == 'local' ) {
                    $attachment_id = $data['_company_video_id']['attachment_id'];
                    $url = wp_get_attachment_url( $attachment_id );
                    $this->helper->update_meta( $post_id, '_company_video', $url );
                }
            }

            // Import listing expiration date

            $field = '_job_expires';

            $date = $data[$field];
            
            $duration = get_option('job_manager_submission_duration');
            
            if ( empty( $article['ID'] ) or ( $this->add_on->can_update_meta( $field, $import_options ) ) ) {
                
                $job_status = $article['post_status'];

                if ( !empty( $date ) ) {
                
                    $date = strtotime( $date );

                    $date = date( 'Y-m-d', $date );

                    $this->helper->update_meta( $post_id, $field, $date );

                    // Set job listing as 'expired' if the date is older than today

                    $today_date = date( 'Y-m-d', current_time( 'timestamp' ) );
                    $job_status = $date < $today_date ? 'expired' : $article['post_status'];
                
                } elseif ( !empty( $duration ) ) {
                    
                    $date = strtotime( 'now + '. $duration .' day' );

                    $date = date( 'Y-m-d', $date );

                    $this->helper->update_meta( $post_id, $field, $date );
                    
                } else {
                    
                    delete_post_meta( $post_id, $field );
                    
                }

                wp_update_post( [ 'ID' => $post_id, 'post_status' => $job_status ] );

            }
        }

        public function import_job_location( $post_id, $data, $import_options, $article ) {
            $location_importer = new WPAI_WP_Job_Manager_Job_Location_Importer( $this->add_on );
            $location_importer->import( $post_id, $data, $import_options, $article );
        }
    }
}
