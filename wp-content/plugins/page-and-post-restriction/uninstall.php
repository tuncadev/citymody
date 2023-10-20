<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

delete_option('papr_admin_email');
delete_option('papr_admin_customer_key');
delete_option('papr_host_name');
delete_option('papr_new_registration');
delete_option('papr_admin_phone');
delete_option('papr_admin_password');
delete_option('papr_admin_customer_key');
delete_option('papr_admin_api_key');
delete_option('papr_customer_token');
delete_option('papr_message');
delete_option('papr_allowed_roles_for_pages');
delete_option('papr_restricted_pages');
delete_option('papr_allowed_roles_for_posts');
delete_option('papr_restricted_posts');
delete_option('papr_allowed_redirect_for_pages');
delete_option('papr_allowed_redirect_for_posts');
delete_option('papr_login_unrestricted_pages');
delete_option('papr_default_role_parent');
delete_option('papr_login_unrestricted_posts');
delete_option('papr_page_search_value');
delete_option('papr_post_search_value');
delete_option('papr_post_type');
delete_option('papr_default_role_parent_page_toggle');
delete_option('papr_access_for_only_loggedin');
delete_option('papr_access_for_only_loggedin_posts');
delete_option('papr_results_per_page');
delete_option('papr_post_per_page');
delete_option('papr_category_per_page');
delete_option('papr_guest_enabled');

?>