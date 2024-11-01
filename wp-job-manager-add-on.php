<?php

/*
Plugin Name: WP All Import - WP Job Manager Add-On
Plugin URI: https://www.wpallimport.com/
Description: Supporting imports into the WP Job Manager plugin.
Version: 1.2.1
Author: Soflyy
*/

if ( ! class_exists( 'WPAI_WP_Job_Manager_Add_On' ) ) {

    final class WPAI_WP_Job_Manager_Add_On {

        protected static $instance;

        protected $add_on;

        static public function get_instance() {
            if ( self::$instance == NULL ) {
                self::$instance = new self();
            }
            
            return self::$instance;
        }

        protected function __construct() {
            $this->constants();
            $this->includes();

            // Important: Preserve 'wpjm_addon' for backwards compatibility
            $this->add_on = new RapidAddon( 'WP Job Manager Add-On', 'wpjm_addon' );

            add_action( 'init', array( $this, 'init' ) );
        }

        public function init() {
            // Helper functions to get post type and other things
            $helper = new WPAI_WP_Job_Manager_Add_On_Helper();
            $this->post_type = $helper->get_post_type();

            // Prevent WP Job Manager from removing location data after importing
            add_action( 'pmxi_before_post_import', array( $helper, 'wpai_wp_jobm_addon_ensure_location_data_is_imported' ), 10, 1 );
            
			// Importing 'Jobs'
			$this->job_fields();

            $this->add_on->set_import_function( array( $this, 'import' ) );

            $this->add_on->run( array(
                'plugins'       => array( 'wp-job-manager/wp-job-manager.php' ),
                'post_types'    => array( 'job_listing' )
            ) );

            $notice_message = 'The WP Job Manager Add-On requires WP All Import <a href="https://www.wpallimport.com/order-now/?utm_source=free-plugin&utm_medium=dot-org&utm_campaign=wpjm" target="_blank">Pro</a> or <a href="https://wordpress.org/plugins/wp-all-import" target="_blank">Free</a>, and the <a href="https://wordpress.org/plugins/wp-job-manager/">WP Job Manager</a> plugin.';

            $this->add_on->admin_notice( $notice_message, array( 'plugins' => array( 'wp-job-manager/wp-job-manager.php' ) ) );
        }

        public function get_add_on() {
            return $this->add_on;
        }

        public function job_fields() {
            $fields = new WPAI_WP_Job_Manager_Jobs_Field_Factory( $this->add_on );

            $fields->add_field( 'job_location' );
            $fields->add_field( 'job_details' );
        }
        
        public function import( $post_id, $data, $import_options, $article ) {
            $importer = new WPAI_WP_Job_Manager_Add_On_Importer( $this->add_on, $this->post_type );
            $importer->import( $post_id, $data, $import_options, $article );
        }

        public function includes() {
            include WPAI_WPJOBM_PLUGIN_DIR_PATH . 'rapid-addon.php';
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            include_once( WPAI_WPJOBM_PLUGIN_DIR_PATH . 'classes/class-field-factory-jobs.php' );            
            include_once( WPAI_WPJOBM_PLUGIN_DIR_PATH . 'classes/class-helper.php' ); 
            include_once( WPAI_WPJOBM_PLUGIN_DIR_PATH . 'classes/class-importer.php' );
            include_once( WPAI_WPJOBM_PLUGIN_DIR_PATH . 'classes/class-importer-jobs.php' );
            include_once( WPAI_WPJOBM_PLUGIN_DIR_PATH . 'classes/class-importer-jobs-location.php' );
        }

        public function constants() {
            if ( ! defined( 'WPAI_WPJOBM_PLUGIN_DIR_PATH' ) ) {
                // Dir path
                define( 'WPAI_WPJOBM_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
            }
            
            if ( ! defined( 'WPAI_WPJOBM_ROOT_DIR' ) ) {
                // Root directory for the plugin.
                define( 'WPAI_WPJOBM_ROOT_DIR', str_replace( '\\', '/', dirname( __FILE__ ) ) );
            }

            if ( ! defined( 'WPAI_WPJOBM_PLUGIN_PATH' ) ) {
                // Path to the main plugin file.
                define( 'WPAI_WPJOBM_PLUGIN_PATH', WPAI_WPJOBM_ROOT_DIR . '/' . basename( __FILE__ ) );
            }
        }
    }

    WPAI_WP_Job_Manager_Add_On::get_instance();
}
