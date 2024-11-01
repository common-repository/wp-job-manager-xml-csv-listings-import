<?php
if ( ! class_exists( 'WPAI_WP_Job_Manager_Add_On_Helper' ) ) {
    
    class WPAI_WP_Job_Manager_Add_On_Helper {
        public function get_theme_version() {
            $version = false;
            $theme   = wp_get_theme();
            
            if ( ! empty( $theme->parent() ) ) {
                $version = $theme->parent()->get( 'Version' );
            } else {
                $version = $theme->get( 'Version' );
            }

            return $version;
        }

        public function get_post_type() {
            global $argv;
            $import_id = false;
            /**
            * Show fields based on post type
            **/
        
            $custom_type = false;

            if ( ! empty( $argv ) ) {
                if ( isset( $argv[3] ) ) {
                    $import_id = $argv[3];
                }
            }
        
            if ( ! $import_id ) {
                // Get import ID from URL or set to 'new'
                if ( isset( $_GET['import_id'] ) ) {
                    $import_id = $_GET['import_id'];
                } elseif ( isset( $_GET['id'] ) ) {
                    $import_id = $_GET['id'];
                }
            
                if ( empty( $import_id ) ) {
                    $import_id = 'new';
                }
            }
        
            // Declaring $wpdb as global to access database
            global $wpdb;
        
            // Get values from import data table
            $imports_table = $wpdb->prefix . 'pmxi_imports';
        
            // Get import session from database based on import ID or 'new'
            $import_options = $wpdb->get_row( $wpdb->prepare("SELECT options FROM $imports_table WHERE id = %d", $import_id), ARRAY_A );
        
            // If this is an existing import load the custom post type from the array
            if ( ! empty($import_options) )	{
                $import_options_arr = unserialize($import_options['options']);
                $custom_type = $import_options_arr['custom_type'];
            } else {
                // If this is a new import get the custom post type data from the current session
                $import_options = $wpdb->get_row( $wpdb->prepare("SELECT option_name, option_value FROM $wpdb->options WHERE option_name = %s", '_wpallimport_session_' . $import_id . '_'), ARRAY_A );				
                $import_options_arr = empty($import_options) ? array() : unserialize($import_options['option_value']);
                $custom_type = empty($import_options_arr['custom_type']) ? '' : $import_options_arr['custom_type'];		
            }
            return $custom_type;
        }
        
        public function get_user( $data, $return_field ) {
            if ( $user = get_user_by( 'login', $data ) ) {
                return $user->$return_field;
            } elseif ( $user = get_user_by( 'email', $data ) ) {
                return $user->$return_field;
            } elseif ( $user = get_user_by( 'ID', $data ) ) {
                return $user->$return_field;
            } else {
                return FALSE;
            }
        }
        
        public function update_meta( $id, $field, $data, $type = 'post' ) {
            $enable_logs_settings = array( 
                'enable'                 => true, 
                'include_empty_updates'  => false,
                'include_failed_updates' => false 
            );

            $enable_logs = apply_filters( 'wpai_wp_jobm_addon_enable_logs', $enable_logs_settings );

            if ( $type == 'post' ) {

                $update = update_post_meta( $id, $field, $data );
                $data = maybe_serialize( $data );
                $data = wp_strip_all_tags( $data );

                if ( $update !== false ) {
                    if ( $enable_logs['enable'] === true ) {
                        if ( $enable_logs['include_empty_updates'] === false ) {
                            if ( $field !== '' && $data !== '' ) {                                
                                $this->log( '<strong>WP Job Manager Add-On:</strong> Successfully imported value "<em>' . $data . '</em>" into field: <em>' . $field . '</em>.' );
                            }
                        } else {
                            $this->log( '<strong>WP Job Manager Add-On:</strong> Successfully imported value "<em>' . $data . '</em>" into field: <em>' . $field . '</em>.' );
                        }
                    }
                } else {
                    if ( $enable_logs['enable'] === true && $enable_logs['include_failed_updates'] === true )  {
                        $this->log( '<strong>WP Job Manager Add-On:</strong> failed to import value "<em>' . $data . '</em>" into field: <em>' . $field . '</em>.' );
                    }
                }
            }
        }

        public function log( $message = 'empty' ){

            if ( $message !== 'empty' ) {            
                $logger = function( $m = '' ) {
                    $date    = date('H:i:s');
                    $m = str_replace( '%', '%%', $m );
                    printf( "<div class='progress-msg'>[%s] $m</div>\n", $date ); 
                    flush(); 
                };
                call_user_func( $logger, $message );
            }

        }
        
        public function wpai_wp_jobm_addon_ensure_location_data_is_imported( $import_id ) {
    
			$import = new PMXI_Import_Record();
			$import_object = $import->getById( $import_id );
			$post_type = $import_object->options['custom_type'];

			if ( $post_type == 'job_listing' ) {
                remove_all_actions( 'job_manager_job_location_edited' );
			}
        }

        public function get_job_manager_add_on_google_api_key() {
            return get_option('job_manager_google_maps_api_key');
        }

        public function google_api_constants() {
            if ( ! defined ( 'GOOGLE_FOR_WORK_AUTH_ERROR_MESSAGE' ) ) {
                define( 'GOOGLE_FOR_WORK_AUTH_ERROR_MESSAGE', 'Unable to authenticate the request.' );
            }
        }
    }
}