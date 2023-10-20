<?php

class papr_custom_roles_constants {
    public static function papr_custom_roles_constants_names() {

        $papr_capabilites_category = array();

        $papr_capabilites_category['general']           =  'dashicons dashicons-wordpress';
        $papr_capabilites_category['post']              =  'dashicons dashicons-admin-post';
        $papr_capabilites_category['page']              =  'dashicons dashicons-admin-page';
        $papr_capabilites_category['media']             =  'dashicons dashicons-admin-media';
        $papr_capabilites_category['taxonomies']        =  'dashicons dashicons-tag';
        $papr_capabilites_category['appearance']        =  'dashicons dashicons-admin-appearance';
        $papr_capabilites_category['plugins']           =  'dashicons dashicons-admin-plugins';
        $papr_capabilites_category['users']             =  'dashicons dashicons-admin-users';
        $papr_capabilites_category['custom']            =  'dashicons dashicons-admin-generic';
        $papr_capabilites_category['all']               =  'dashicons dashicons-plus';

        return $papr_capabilites_category;
    }

    public static function papr_custom_roles_capabilities_constants() {

        $papr_capabilites_category_array = array();

        $papr_capabilites_category_array['general'] = array('edit_dashboard','edit_files',
            'export','import','manage_links','manage_options','moderate_comments','read',
            'unfiltered_html','update_core');
        
        $papr_capabilites_category_array['post'] = array('delete_others_posts','delete_posts',
        'delete_private_posts','delete_published_posts','edit_others_posts','edit_posts',
        'edit_private_posts','edit_published_posts','publish_posts','read_private_posts');
        
        $papr_capabilites_category_array['page'] = array('delete_others_pages','delete_pages',
        'delete_private_pages','delete_published_pages','edit_others_pages','edit_pages',
        'edit_private_pages','edit_published_pages','publish_pages','read_private_pages');

        $papr_capabilites_category_array['media'] = array('upload_files','unfiltered_upload');

        $papr_capabilites_category_array['taxonomies'] = array('manage_categories');

        $papr_capabilites_category_array['appearance'] = array('delete_themes',
        'edit_theme_options','edit_themes','install_themes','switch_themes','update_themes');

        $papr_capabilites_category_array['plugins'] = array('activate_plugins','delete_plugins',
        'edit_plugins','install_plugins','update_plugins');

        $papr_capabilites_category_array['users'] = array('create_roles','create_users',
        'delete_roles','delete_users','edit_roles','edit_users','list_roles','list_users',
        'promote_users','remove_users');

        $papr_capabilites_category_array['custom'] = array('restrict_content');

        return $papr_capabilites_category_array;
    }
}
?>
