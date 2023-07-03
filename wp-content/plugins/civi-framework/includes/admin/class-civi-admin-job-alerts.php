<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Civi_Admin_Job_Alerts')) {
    /**
     * Class Civi_Admin_Jobs
     */
    class Civi_Admin_Job_Alerts
    {

        /**
         * Register custom columns
         * @param $columns
         * @return array
         */
        public function register_custom_column_titles($columns)
        {
            $columns['title'] = esc_html__('Title', 'civi-framework');
            $columns['email'] = esc_html__('Email', 'civi-framework');
            $columns['location'] = esc_html__('Location', 'civi-framework');
            $columns['cat'] =  esc_html__('Categories', 'civi-framework');
            $columns['experience'] = esc_html__('Experience', 'civi-framework');
            $columns['frequency'] = esc_html__('Frequency', 'civi-framework');
            $new_columns = array();
            $custom_order = array('title', 'email', 'location', 'cat', 'experience', 'frequency');
            foreach ($custom_order as $colname) {
                $new_columns[$colname] = $columns[$colname];
            }
            return $new_columns;
        }
        /**
         * Display custom column for jobs
         * @param $column
         */
        public function display_custom_column($column)
        {
            global $post;
            $post_id = $post->ID;
            switch ($column) {
                case 'email':
                    echo get_post_meta( $post_id, CIVI_METABOX_PREFIX . 'job_alerts_email', true );
                    break;
                case 'location':
					$location = get_term_by('id', intval(get_post_meta( $post_id, CIVI_METABOX_PREFIX . 'job_alerts_location', true )), 'jobs-location');
					if( $location ) {
						echo $location->name;
					} else {
						echo '-';
					}
                    break;
                case 'cat':
					$categories = get_term_by('id', intval(get_post_meta( $post_id, CIVI_METABOX_PREFIX . 'job_alerts_categories', true )), 'jobs-categories');
					if( $categories ) {
						echo $categories->name;
					} else {
						echo '-';
					}
                    break;
                case 'experience':
					$experience = get_term_by('id', intval(get_post_meta( $post_id, CIVI_METABOX_PREFIX . 'job_alerts_experience', true )), 'jobs-experience');
					if( $experience ) {
						echo $experience->name;
					} else {
						echo '-';
					}
                    break;
				case 'frequency':
					if( get_post_meta( $post_id, CIVI_METABOX_PREFIX . 'job_alerts_frequency', true ) ) {
						echo get_post_meta( $post_id, CIVI_METABOX_PREFIX . 'job_alerts_frequency', true );
					} else {
						echo '-';
					}
					break;
            }
        }

        /**
         * sortable_columns
         * @param $columns
         * @return mixed
         */
        public function sortable_columns($columns){
            $columns['title'] = 'title';
            $columns['email'] = 'email';
            $columns['location'] = 'location';
            $columns['cat'] = 'cat';
            $columns['experience'] = 'experience';
            $columns['frequency'] = 'frequency';
            return $columns;
        }
    }
}
