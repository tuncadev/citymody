<?php
/**
* Plugin Name: Page Restriction WordPress (WP) - Protect WP Pages/Post
* Description: This plugin allows restriction over users based on their roles and whether they are logged in or not.
* Version: 1.3.4
* Author: miniOrange
* Author URI: https://miniorange.com
* License: MIT/Expat
* License URI: https://docs.miniorange.com/mit-license
*/

require_once('page-restriction-save.php');
require_once('feedback-form.php');
require_once('page-restriction-menu-settings.php');
include_once 'page-restriction-utility.php';
require_once('page-restriction-custom-roles-sub-menu.php');

class page_and_post_restriction_add_on {

    function __construct(){
        update_option('papr_host_name','https://login.xecurify.com');
        add_action( 'admin_menu', array( $this, 'papr_menu'),11 );
        add_action( 'admin_init', 'papr_save_setting', 1, 0 );
        add_action( 'admin_enqueue_scripts', array( $this, 'papr_plugin_settings_script') );
	    register_deactivation_hook(__FILE__, array( $this, 'papr_deactivate'));
        add_action( 'save_post', array($this, 'papr_save_meta_box_info'),10,3);
        add_action('wp',array($this, 'papr_initialize_page_restrict'),0);
        add_action('add_meta_boxes',array($this, 'papr_add_custom_meta_box'));
        add_action('admin_footer', array( $this, 'papr_feedback_request' ) );
        add_filter('manage_page_posts_columns', array( $this, 'papr_page_add_column') );
        add_filter('manage_post_posts_columns', array( $this, 'papr_post_add_column') );
        add_action('manage_page_posts_custom_column', array( $this, 'papr_page_custom_columns'), 10, 2);
        add_action('manage_post_posts_custom_column', array( $this, 'papr_post_custom_columns'), 10, 2);
        add_action('quick_edit_custom_box', array( $this, 'papr_display_custom_quickedit_fields'),10,2);
        add_shortcode('restrict_content', array($this, 'papr_restrict_content'));
	    add_action('plugin_action_links_'.plugin_basename(__FILE__), array($this,'papr_add_plugin_settings'));
    }

    function papr_menu() {
        add_menu_page('Page and Post Restriction','Page Restriction', 'administrator', 'page_restriction','papr_page_restriction',plugin_dir_url(__FILE__) . 'includes/images/miniorange.png');
        add_submenu_page( 'page_restriction', 'Custom Roles', 'Roles and Capabilities','administrator','papr_custom_roles_sub_menu', 'papr_custom_roles_sub_menu');
    }

    function papr_add_plugin_settings($links) {
	    $links = array_merge( array(
		    '<a href="' . esc_url( admin_url( 'admin.php?page=page_restriction' ) ) . '">' . __( 'Settings' ) . '</a>'
	    ), $links );
	    return $links;
    }

    function papr_feedback_request() {
        papr_display_feedback_form();
    }

    function papr_deactivate() {
        wp_redirect('plugins.php');
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
        delete_option('papr_roles_per_page');
    }

    static function papr_plugin_settings_script($page) {
        if($page == 'toplevel_page_page_restriction' || $page == 'page-restriction_page_papr_custom_roles_sub_menu'){
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-autocomplete');
            wp_enqueue_script('papr_admin_settings_phone_script', plugins_url('includes/js/phone.js', __FILE__));
            wp_enqueue_style('papr_admin_bootstrap_settings_script', plugins_url('includes/js/bootstrap/bootstrap.min.js', __FILE__));
            wp_enqueue_style('papr_admin_bootstrap_settings_script', plugins_url('includes/js/bootstrap/popper.min.js', __FILE__));
            wp_enqueue_style('papr_admin_settings_phone_style', plugins_url('includes/css/phone.min.css', __FILE__));
            wp_enqueue_style('papr_admin_bootstrap_settings_style', plugins_url('includes/css/bootstrap/bootstrap.min.css', __FILE__));
            wp_enqueue_style('papr_admin_settings_style', plugins_url('includes/css/papr_settings_style.min.css', __FILE__), array(), '1.3.4', 'all');
            wp_enqueue_script('papr_auto_assign_private_script', plugins_url('includes/js/papr_role_assigned.js', __FILE__));
            wp_enqueue_script('papr_roles_show_dropdown_script', plugins_url('includes/js/papr_role_dropdown.js', __FILE__));
            wp_enqueue_style('papr_roles_show_dropdown_style', 'https://cdn.jsdelivr.net/gh/harvesthq/chosen@gh-pages/chosen.min.css');
       }
        
        else if($page == 'edit.php'){ 
            wp_enqueue_script( 'populatequickedit', plugins_url('includes/js/page-restriction-quick-edit.js', __FILE__), array( 'jquery' ) );
        }

        else {
            return;
        }
    }

    function papr_restrict_content($attr, $content = '') {
        if(!is_user_logged_in())
            return '';
        return '<p>'.$content.'</p>';
    }

    function papr_page_add_column( $column_array ) {
        $column_array['Allowed_Roles'] = 'Roles who have view access';
        $column_array['Private'] = 'Page restrcited from non-logged in users';
        return $column_array;
    }

    function papr_post_add_column( $column_array ) {
        $column_array['Allowed_Roles'] = 'Roles who have view access';
        $column_array['Private'] = 'Post restrcited from non-logged in users';
        return $column_array;
    }

    function papr_page_custom_columns($column, $post_id){
        $allowed_roles         = get_option('papr_allowed_roles_for_pages');
        $allowed_roles         = $allowed_roles != '' ? $allowed_roles : array();
        
        $allowed_redirect_pages = get_option('papr_allowed_redirect_for_pages');
        $allowed_redirect_pages = $allowed_redirect_pages != '' ? $allowed_redirect_pages : array();
        
        switch ($column) {
            case 'Allowed_Roles':
                if(isset($allowed_roles[$post_id])){
                    $len=0;
                    if(is_countable($allowed_roles[$post_id]))
                        $len = count($allowed_roles[$post_id]);
                    foreach($allowed_roles[$post_id] as $keys => $allowed_role){
                        echo esc_html__($allowed_role);
                        if($keys!=$len-1){
                            echo ';';
                        }
                    }
                }
                break;
            case 'Private':
                if(isset($allowed_redirect_pages[$post_id])){
                    echo esc_html__("Yes");
                } else {
                    echo esc_html__("No");
                }
            default:
                break;
        }
    }

    function papr_post_custom_columns($column, $post_id){
        $allowed_roles         = get_option('papr_allowed_roles_for_posts');
        $allowed_roles         = $allowed_roles != '' ? $allowed_roles : array();
        
        $allowed_redirect_post = get_option('papr_allowed_redirect_for_posts');
        $allowed_redirect_post = $allowed_redirect_post != '' ? $allowed_redirect_post : array();
        
        switch ($column) {
            case 'Allowed_Roles':
                if(isset($allowed_roles[$post_id])){
                    $len=0;
                    if(is_countable($allowed_roles[$post_id]))
                        $len = count($allowed_roles[$post_id]);
                    foreach($allowed_roles[$post_id] as $keys => $allowed_role){
                        echo esc_html__($allowed_role);
                        if($keys!=$len-1){
                            echo ';';
                        }
                    }
                }
                break;
            case 'Private':
                if(isset($allowed_redirect_post[$post_id])){
                    echo esc_html__("Yes");
                } else {
                    echo esc_html__("No");
                }
                break;
            default:
                break;
        }
    }
    
    function papr_display_custom_quickedit_fields( $column_name, $post_type ) {

        wp_nonce_field( plugin_basename( __FILE__ ), 'book_edit_nonce' );

        if($column_name == 'Allowed_Roles'){
            ?>
            <fieldset class="inline-edit-col-right inline-edit-book">
                <h3>miniOrange Page and Post Restriction Configuration</h3>
            <?php
        }
        ?>    
            <div class="inline-edit-col column-<?php echo esc_attr($column_name); ?>">
                <label class="inline-edit-group">
                <?php
                switch ( $column_name ) {
                    case 'Private':
                        ?><label class="title">Private</label><input name="Private" type="checkbox" /><?php
                        break;
                    
                    case 'Allowed_Roles':
                        ?><label class="title">Allowed Roles</label><input name="Allowed_Roles" type="text"/><?php
                        break;    
                }
                ?>
                </label>
            </div>
        <?php
        if($column_name == 'Private'){
            ?>
            </fieldset>
        <?php
        }
    }
    
    function papr_add_custom_meta_box($post_type) {

    	global $pagenow;
        $papr_metabox_allowed_roles = get_option('papr_allowed_metabox_roles');
        if(empty($papr_metabox_allowed_roles))
            $papr_metabox_allowed_roles = 'Editor; Author;';
        if($papr_metabox_allowed_roles == 'papr_no_roles')
            $papr_metabox_allowed_roles = '';
        $metabox_roles_array = explode(';', $papr_metabox_allowed_roles);
        $current_user = wp_get_current_user();
        $user_roles = $current_user->roles;

    	if(in_array( $pagenow, array('post-new.php') )){
    		if($post_type == 'page' && get_option('papr_select_all_pages') == 'checked'){

    			$pages_for_loggedin_users = get_option('papr_allowed_redirect_for_pages');
                $pages_for_loggedin_users = $pages_for_loggedin_users!='' ? $pages_for_loggedin_users : array();
    			
                $pages_for_loggedin_users[get_the_ID()]=true;
    			update_option('papr_allowed_redirect_for_pages',$pages_for_loggedin_users);

    		} else if(get_option('papr_select_all_posts') == 'checked'){

    			$pages_for_loggedin_users = get_option('papr_allowed_redirect_for_posts');
                $pages_for_loggedin_users = $pages_for_loggedin_users!='' ? $pages_for_loggedin_users : array();
    			
                $pages_for_loggedin_users[get_the_ID()]=true;
    			update_option('papr_allowed_redirect_for_posts',$pages_for_loggedin_users);
            }
    	}

        if(is_array($user_roles))
            if(empty(array_intersect($metabox_roles_array, $user_roles)) && (!papr_in_array('administrator', $user_roles)))
                return;

        $type = get_post_type_object( $post_type );
        add_meta_box("demo-meta-box", "Page Restrict Access", array($this, "papr_meta_box"), $post_type, "side", "high", null);
    }

    /*If the user is not logged in then it checks if the page or post are retricted to logged in user only or not. */
    function papr_restrict_logged_in_users($page_post_id){
    	$restricted_pages = get_option( 'papr_allowed_redirect_for_pages' );
    	$restricted_posts = get_option( 'papr_allowed_redirect_for_posts' );
        
        $default_login_toggle = get_option('papr_access_for_only_loggedin');
        $unrestricted_pages   = get_option( 'papr_login_unrestricted_pages' );
    	
        $default_login_toggle_posts = get_option('papr_access_for_only_loggedin_posts');
        $unrestricted_posts         = get_option( 'papr_login_unrestricted_posts' );
        

        $restricted_pages           = $restricted_pages!='' ? $restricted_pages : array();
        $restricted_posts           = $restricted_posts!='' ? $restricted_posts : array();
        
        $default_login_toggle       = $default_login_toggle!='' ? $default_login_toggle : "";
        $unrestricted_pages         = $unrestricted_pages!='' ? $unrestricted_pages : array();
        
        $default_login_toggle_posts = $default_login_toggle_posts!='' ? $default_login_toggle_posts : "";
        $unrestricted_posts         = $unrestricted_posts!='' ? $unrestricted_posts : array();

        //Settings when global toggle is all and fe pages/posts has unticked checkbox
        if( is_page() && ($default_login_toggle==1) && empty($unrestricted_pages[$page_post_id]) ) {
            $papr_message_text = __('You are not authorized to access this, please login', 'page-and-post-restriction');;
    	    wp_die( $papr_message_text );
        }

        if( is_single() && ($default_login_toggle_posts==1) && empty( $unrestricted_posts[$page_post_id]) ) {
            $papr_message_text = __('You are not authorized to access this, please login', 'page-and-post-restriction');;
    	    wp_die( $papr_message_text );
        }

        //Added condition for front page restriction
    	if ( (is_page() && !empty($restricted_pages[$page_post_id])) || (is_front_page() && !empty($restricted_pages[get_option('page_on_front')])) || (is_single() && !empty( $restricted_posts[$page_post_id])) ) {
    	    $papr_message_text = __('You are not authorized to access this, please login', 'page-and-post-restriction');;
    	    wp_die( $papr_message_text );
    	}
    }

    /*If user is logged in then this function checks if the user is restricted to access any page or post*/
    function papr_restrict_by_role($page_post_id){
    	$allowed_roles_for_posts = get_option("papr_allowed_roles_for_posts");
        $allowed_roles_for_pages = get_option("papr_allowed_roles_for_pages");
        $restricted_pages = get_option('papr_restricted_pages');
        $restricted_posts = get_option('papr_restricted_posts');

        $allowed_roles_for_posts = $allowed_roles_for_posts!='' ? $allowed_roles_for_posts : array();
        $allowed_roles_for_pages = $allowed_roles_for_pages!='' ? $allowed_roles_for_pages : array();
        $restricted_pages = $restricted_pages!='' ? $restricted_pages : array();
        $restricted_posts = $restricted_posts!='' ? $restricted_posts : array();

        if(is_page($page_post_id)){
            $allowed_roles_for_types = $allowed_roles_for_pages;
        }
        else{
            $allowed_roles_for_types = $allowed_roles_for_posts;
        }

        if(!is_front_page() && empty($allowed_roles_for_pages['mo_page_0'])) {
            if (is_page($page_post_id)) {
                if (is_array($restricted_pages)) {
                    if (!papr_in_array($page_post_id, $restricted_pages)) {
                        if ($page_post_id !== 1)
                            return;
                    }
                }
            } else {
                if (is_array($restricted_posts)) {
                    if (!papr_in_array($page_post_id, $restricted_posts)) {
                        return;
                    }
                }
            }
        }

    	$current_user = wp_get_current_user();
        $user_roles = $current_user->roles;

        foreach ($user_roles as $key => $user_role) {
    		if(is_front_page()&&!empty($allowed_roles_for_pages['mo_page_0'])){
    			foreach($allowed_roles_for_pages['mo_page_0'] as $keys => $allowed_roles_for_page){
                    if( strripos($allowed_roles_for_page,$user_role) !== FALSE)
    			    	return;
                }
    		}
    		else if( !empty($allowed_roles_for_types[$page_post_id])){
                if(is_array($allowed_roles_for_types) ){
                    if(!empty( $allowed_roles_for_types[$page_post_id] )){
                        foreach($allowed_roles_for_types[$page_post_id] as $keys => $allowed_roles_for_type){
                            if( strripos($allowed_roles_for_type,$user_role) !== FALSE)
                                return;
                        }
                    }
                }
            }				
        }

    	//default access to all users
        if(is_array($allowed_roles_for_pages) || is_array($allowed_roles_for_posts)) {
            if(is_page()&& !empty( $allowed_roles_for_pages[$page_post_id])&&(count($allowed_roles_for_pages[$page_post_id])==0))
                return;
            elseif( (is_single()&& !empty($allowed_roles_for_posts[$page_post_id])) || (is_front_page() && !empty($allowed_roles_for_pages['mo_page_0'])) || (is_page() && !empty($allowed_roles_for_pages[$page_post_id])) ){
                $papr_message_text = __('You are not authorized to access this, please login', 'page-and-post-restriction');;
                wp_die( $papr_message_text );
            }
        }
    }

    function papr_initialize_page_restrict(){
        $page_post_id = get_the_ID()?get_the_ID():0;
        $guest_user_logged_in = false;

        $type=get_post_type($page_post_id);

        if($type != 'page' && $type != 'post') {
            return;
        }

        if(!is_user_logged_in() and !$guest_user_logged_in)
    	    $this->papr_restrict_logged_in_users($page_post_id);
    	else
    		$this->papr_restrict_by_role($page_post_id);
    }

    public static function papr_meta_box($post ) {
        $type = '';
        if(is_object($post) && property_exists($post, 'post_type')){
            $type=$post->post_type;
        }
        
        if($type == 'page' || $type == 'post') {
            wp_nonce_field('my_meta_box_nonce','meta_box_nonce');
        }

        global $wp_roles;
        $wp_name_roles=($wp_roles->role_names);
        asort($wp_name_roles);
        if($type == 'page'){
            $role = maybe_unserialize(get_option('papr_allowed_roles_for_pages'));
            $role = $role!='' ? $role : array();

            $default_login_toggle = maybe_unserialize(get_option('papr_access_for_only_loggedin'));
            $default_login_toggle = $default_login_toggle!='' ? $default_login_toggle : "";
        } else if($type == 'post') {
            $role = maybe_unserialize(get_option('papr_allowed_roles_for_posts'));
            $role = $role!='' ? $role : array();

            $default_login_toggle = maybe_unserialize(get_option('papr_access_for_only_loggedin_posts'));
            $default_login_toggle = $default_login_toggle!='' ? $default_login_toggle : "";
        }
        else {
            $role = array();
            $default_login_toggle = '';
        }

        $roles=array();
        if(!empty($role[$post->ID])){
            $roles=$role[$post->ID];
        }

    $is_page_restrcited_for_loggedin_users = 'false';
    
    $disabled = '';
    
    if($type == 'page'){
        $pages_for_loggedin_users = maybe_unserialize(get_option('papr_allowed_redirect_for_pages'));
        $pages_for_loggedin_users = $pages_for_loggedin_users!='' ? $pages_for_loggedin_users : array();
    }
    else if($type == 'post'){
        $pages_for_loggedin_users = maybe_unserialize(get_option('papr_allowed_redirect_for_posts'));
        $pages_for_loggedin_users = $pages_for_loggedin_users!='' ? $pages_for_loggedin_users : array();
    }
    else{
        $disabled = 'disabled';
        $pages_for_loggedin_users=array();
    }

    if(!empty($pages_for_loggedin_users)){
        if(!empty( $pages_for_loggedin_users[$post->ID]) && $pages_for_loggedin_users[$post->ID]=='true'){
            $is_page_restrcited_for_loggedin_users = 'true';
        }
    }
    ?>
    <div class="row">
        <img src="<?php echo esc_url(plugin_dir_url(__FILE__)) ?>includes/images/miniorange-logo.png" alt="miniOrange Page and Post Restriction" width="35px">
        <h4 style="position:absolute;top:-0.6rem;left:4.2rem;">Page and Post Restriction</h4>
    </div>
    
    <p> 
        <?php 
            if($type != 'page' && $type != 'post') {
                ?>
                <p><b>
                This feature is available for Custom Post in Premium version of the Page and Post Restriction plugin.
                </b></p>
                <a href="admin.php?page=page_restriction&tab=premium_plan" target="_blank">Premium Plans</a>
                <?php
                echo '</br></br>';
            }
            esc_html_e( "Limit access to Logged in users.", 'mo-wpum' );
        ?>
        </p>
        <div class="page-restrict-loggedin-user-div">
            <input type="hidden" name="papr_metabox" value="true">
            <ul class="page-restrict-loggedin-user">
                <?php
                if($is_page_restrcited_for_loggedin_users=='true'){
                    $require_login = 'checked';
                } else {
                    $require_login = '';
                }
                global $pagenow;
                if(in_array( $pagenow, array('post-new.php') )){
                    if($default_login_toggle==1){
                        $require_login = 'checked';
                    }
                }
                ?>
                <input type="checkbox" name="restrict_page_access_loggedin_user" <?php echo esc_attr($require_login);?> value="true" <?php echo ($disabled); ?> />
                Require Login
            </ul>
        </div>

        <hr>
        <p>
            <?php esc_html_e( "Limit access to this post's content to users of the selected roles.", 'mo-wpum' );  ?>
        </p>

        <div class="role-list-wrap">

            <ul class="role-list">

                <?php foreach ( $wp_name_roles as $role => $name ) : ?>
                    <li>
                        <label>
                            <input type="checkbox" name="papr_access_role[]" <?php checked(papr_in_array( $role, $roles )  || papr_in_array($name, $roles)); ?> value="<?php echo esc_attr( $role ); ?>" <?php echo esc_attr($disabled); ?> />
                            <?php echo esc_html( translate_user_role( $name ) ); ?>
                        </label>
                    </li>
                <?php endforeach; ?>

            </ul>
        </div>
     <?php
    }

/* Function to save the meta box details during creation/editing */
    static function papr_save_meta_box_info($post_id , $post, $update) {
        if(!isset($_POST['papr_metabox']) && !isset($_POST['book_edit_nonce'])) return;

        $type = get_post_type($post);
        
        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        $default_role_parent     = get_option('papr_default_role_parent');
        $default_role_parent     = $default_role_parent!='' ? $default_role_parent : array();
        if($type=='page'){
            $allowed_roles          = maybe_unserialize(get_option('papr_allowed_roles_for_pages'));
            $restrictedposts        = get_option('papr_restricted_pages');
            $allowed_redirect_pages = get_option('papr_allowed_redirect_for_pages');
            $unrestricted_pages     = get_option( 'papr_login_unrestricted_pages' );
            
            $allowed_roles          = $allowed_roles!='' ? $allowed_roles : array();
            $restrictedposts        = $restrictedposts!='' ? $restrictedposts : array();
            $allowed_redirect_pages = $allowed_redirect_pages!='' ? $allowed_redirect_pages : array();
            $unrestricted_pages     = $unrestricted_pages!='' ? $unrestricted_pages : array();
            
        } else if($type=='post') {
            $allowed_roles          = maybe_unserialize(get_option('papr_allowed_roles_for_posts'));
            $restrictedposts        = get_option('papr_restricted_posts');
            $allowed_redirect_pages = get_option('papr_allowed_redirect_for_posts');
            $unrestricted_pages     = get_option( 'papr_login_unrestricted_posts' );
            
            $allowed_roles          = $allowed_roles!='' ? $allowed_roles : array();
            $restrictedposts        = $restrictedposts!='' ? $restrictedposts : array();
            $allowed_redirect_pages = $allowed_redirect_pages!='' ? $allowed_redirect_pages : array();
            $unrestricted_pages     = $unrestricted_pages!='' ? $unrestricted_pages : array();
        } else {
            return;
        }

        if(isset( $_POST['papr_access_role'] )){
            array_push($restrictedposts, $post_id);
            $new_roles=$_POST['papr_access_role'];
            foreach ($new_roles as $value) {	
                $value = sanitize_text_field( $value );
            }
            $allowed_roles[$post_id] = $new_roles;
        } else if (isset( $_POST['Allowed_Roles'] )) {
            array_push($restrictedposts, $post_id);
            $new_roles = sanitize_text_field($_POST['Allowed_Roles']);
            $allowed_roles[$post_id] = explode(";",$new_roles);
        } else {
            $restrictedpostsarray = $restrictedposts;
            if(is_array($restrictedpostsarray)){
                while(($i = array_search($post_id, $restrictedpostsarray)) !== false) {
                    unset($restrictedpostsarray[$i]);
                    unset($allowed_roles[$post_id]);
                }
            }
            $restrictedposts = $restrictedpostsarray;
        }
        if( isset($_POST['restrict_page_access_loggedin_user']) || isset($_POST['Private']) ) {
            $allowed_redirect_pages[$post_id]=true;
            unset($unrestricted_pages[$post_id]);
        } else{
            unset($allowed_redirect_pages[$post_id]);
            $unrestricted_pages[$post_id] = true;
        }

        $parent_id = wp_get_post_parent_id($post_id);
        if($type=='page' && $parent_id){
            if(!empty($default_role_parent[$parent_id]) && $default_role_parent[$parent_id]==true){
                $restrictedposts = get_option('papr_restricted_pages');
                $restrictedposts = $restrictedposts!='' ? $restrictedposts : array();

                if(papr_in_array($parent_id, $restrictedposts)){
                    array_push($restrictedposts, $post_id);
                    $allowed_roles = get_option('papr_allowed_roles_for_pages');
                    $allowed_roles = $allowed_roles!='' ? $allowed_roles : array();
                    $role_string = $allowed_roles[$parent_id];
                    $parent_allowed_roles = $role_string;
                    $allowed_roles[$post_id] = $parent_allowed_roles;
                    if($allowed_roles[$post_id] != ''){
                        if(!papr_in_array($post_id,$restrictedposts)){                    
                            array_push($restrictedposts, $post_id);
                        }
                    } else {
                        unset($restrictedposts[$post_id]);
                    }
                }
                if(papr_in_array($parent_id, $allowed_redirect_pages)){
                    $allowed_redirect_pages[$post_id]=true;
                    unset($unrestricted_pages[$post_id]);
                } else{
                    unset($allowed_redirect_pages[$post_id]);
                    $unrestricted_pages[$post_id] = true;
                }
            }
        }

        if($type=='page'){
            $default_role_toggle = get_option('papr_default_role_parent_page_toggle');
            $default_role_toggle = $default_role_toggle!='' ? $default_role_toggle : "";
            
            if(!empty( $default_role_parent[$post_id]) || $default_role_toggle==1) {
                if($post_id != 0){
                    $default_role_parent[$post_id]=true;
                    
                    $children = get_pages( array( 'child_of' => $post_id ) );
                
                    if(count($children)>0) {
                        
                        foreach($children as $child) {
                            $child_id = $child->ID;
                            
                            $allowed_roles[$child->ID] = $allowed_roles[$post_id];

                            if($allowed_roles[$child->ID] != ''){
                                if(!papr_in_array($child->ID,$restrictedposts)){                    
                                    array_push($restrictedposts, $child->ID);
                                }
                            } else {
                                unset($restrictedposts[$child->ID]);
                            }

                            if(!empty( $allowed_redirect_pages[$post_id])) {
                                if($allowed_redirect_pages[$post_id]==1 || $allowed_redirect_pages[$post_id]=='on' || $allowed_redirect_pages[$post_id]=='true'){
                                    $allowed_redirect_pages[$child->ID]=true;
                                    unset($unrestricted_pages[$child->ID]);
                                }
                            }
                            else {
                                unset($allowed_redirect_pages[$child->ID]);
                                $unrestricted_pages[$child->ID] = true;
                            }

                            $children_of_children = get_pages( array( 'child_of' => $child->ID ) );

                            if(count($children_of_children)>0){
                                $default_role_parent[$child->ID]=true;
                            }
                        }
                    }
                }        
            }
        }

        if($type=='page'){
            update_option('papr_default_role_parent', $default_role_parent);
            update_option('papr_login_unrestricted_pages',$unrestricted_pages);
            update_option('papr_restricted_pages', $restrictedposts);
            update_option('papr_allowed_roles_for_pages', $allowed_roles);
            update_option('papr_allowed_redirect_for_pages',$allowed_redirect_pages);
        }
        else {
            update_option('papr_restricted_posts', $restrictedposts);
            update_option('papr_allowed_roles_for_posts', $allowed_roles);
            update_option('papr_allowed_redirect_for_posts',$allowed_redirect_pages);
            update_option('papr_login_unrestricted_posts',$unrestricted_pages);
        }
    }
}
new page_and_post_restriction_add_on;
?>