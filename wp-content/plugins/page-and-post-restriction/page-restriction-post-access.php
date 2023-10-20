<?php

require_once 'page-restriction-menu-settings.php';
require_once 'page-restriction-page-access.php';
require_once 'page-restriction-utility.php';

function papr_post_access() {
    
    $results_per_page               =  get_option('papr_post_per_page');
    $allowed_roles                  =  get_option('papr_allowed_roles_for_posts');
    $allowed_redirect_post          =  get_option('papr_allowed_redirect_for_posts');
    $access_for_only_loggedin_post  =  get_option('papr_access_for_only_loggedin_posts');
    $unrestricted_posts             =  get_option('papr_login_unrestricted_posts');
    $post_type                      =  get_option('papr_post_type');

    $results_per_page               =  $results_per_page != '' ? $results_per_page : 10;
    $allowed_roles                  =  $allowed_roles != '' ? $allowed_roles : array();
    $allowed_redirect_post          =  $allowed_redirect_post != '' ? $allowed_redirect_post : array();
    $access_for_only_loggedin_post  =  $access_for_only_loggedin_post != '' ? $access_for_only_loggedin_post : "";
    $unrestricted_posts             =  $unrestricted_posts ? $unrestricted_posts : array();
    $post_type                      =  $post_type != '' ? $post_type : 'post';

    $mo_post_search_value = "";
    if (array_key_exists('search', $_GET)) {
        $mo_post_search_value = sanitize_text_field($_GET['search']);
    }

    $post_array_type = get_post_types();

    if(empty($post_array_type[$post_type])){
        $post_type = 'post';
        update_option('papr_post_type',$post_type);
    }

    ?>

    <div class="rounded bg-white papr-shadow p-4 ms-4 mt-4">
        <h4 class="papr-form-head">Give Access to Posts based on Roles and Login Status</h4>
        <?php papr_post_toggle_all_pages(); ?>
        <?php papr_restriction_behaviour_pages(); ?>
        <hr class="mt-4"/>
        <h5 class="papr-form-head papr-form-head-bar mt-5">Post Restrictions
        <div class="papr-info-global ms-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
            </svg>
            <p class="papr-info-text-global">
                Specify which pages would be <b>accessible to only Logged In users</b> OR which <b>user roles should be able to access</b> the page in the table below.
            </p>
        </div>
        </h5>
        <div class="tablenav top mt-4">
            <?php papr_dropdown($results_per_page, 'post', $post_type); ?>
            <?php papr_search_box('post',$mo_post_search_value); ?>
                
            <form name="f" method="post" action="" id="blockedpagesform">
            <?php wp_nonce_field("papr_restrict_post_roles_login"); ?>
                <input type="hidden" name="option" value="papr_restrict_post_roles_login" form="blockedpagesform" />
                <input type="submit" class="papr-btn-cstm rounded" value="Save Configuration" form="blockedpagesform">

                <?php

                $total_post = papr_get_page_post_count($mo_post_search_value, $post_type);
                $number_of_pages_in_pagination = ceil($total_post / $results_per_page);
                $current_page = papr_get_current_page($number_of_pages_in_pagination);
                $pagination = papr_get_paginated_pages_post($mo_post_search_value, $results_per_page, $current_page, $post_type);

                $link = 'admin.php?page=page_restriction&tab=post_access&curr=';
                if($mo_post_search_value!=''){
                    $link = 'admin.php?page=page_restriction&tab=post_access&search='.$mo_post_search_value.'&curr=';
                }
                
                papr_pagination_button($number_of_pages_in_pagination, $total_post, $current_page, $link, 'top');
                ?>
        </div>

            <table id="reports_table" class="wp-list-table widefat fixed striped table-view-list pages" style="width:99%;">
                <thead><?php papr_display_head_foot_of_table('Post'); ?></thead>
                <tbody style="width:100%;">
                    <?php
                    if(count($pagination)==0){
                        echo '<tr><td><b>No Results</b></td><td></td><td></td></tr>';
                    }
                    else {
                        foreach ($pagination as $post) {
                            papr_post_display_pages($post, $allowed_roles, $allowed_redirect_post, $unrestricted_posts, $access_for_only_loggedin_post,$post_type);
                        }
                    } ?>
                </tbody>
                <thead><?php papr_display_head_foot_of_table('Post'); ?></thead>
            </table>

                <div class="tablenav bottom">
                    <input type="submit" class="papr-btn-cstm rounded" value="Save Configuration" form="blockedpagesform">
                    <?php
                    papr_pagination_button($number_of_pages_in_pagination, $total_post, $current_page, $link, 'bottom');
                    $mo_roles = array();
                    global $wp_roles;
                    $roles = $wp_roles->roles;
                    foreach ($roles as $key => $val){
                        $mo_roles[] = $key;
                    }
                    ?>

                    <script>
                        jQuery(".roles_multi").chosen({no_results_text: "Oops, nothing found!"}); 

                        var post_selector_up = document.getElementById("current-page-selector");
                        var post_selector_down = document.getElementById("current-page-selector-1");
                        var post_search_link = document.getElementById("mo_post_search");
                        var link = 'admin.php?page=page_restriction&tab=post_access&curr=';

                        post_selector_up.addEventListener("keyup", function(event) {
                            if (event.keyCode === 13) {
                                post_selector_up_value = document.getElementById("current-page-selector").value;
                                var page_link = link.concat(post_selector_up_value);
                                window.open(page_link, "_self");
                            }
                        });

                        post_selector_down.addEventListener("keyup", function(event) {
                            if (event.keyCode === 13) {
                                post_selector_down_value = document.getElementById("current-page-selector-1").value;
                                var page_link = link.concat(post_selector_down_value);
                                window.open(page_link, "_self");
                            }
                        });

                        post_search_link.addEventListener("keyup", function(event) {
                            if (event.keyCode === 13) {
                                post_search_link_value = document.getElementById("mo_post_search").value;
                                var search_link = 'admin.php?page=page_restriction&tab=post_access&search=';
                                var post_link = search_link.concat(post_search_link_value);
                                window.open(post_link, "_self");
                            }
                        });

                        var closebtns = document.getElementsByClassName("close");
                        var i;

                        for (i = 0; i < closebtns.length; i++) {
                            closebtns[i].addEventListener("click", function() {
                                this.parentElement.style.display = 'none';
                            });
                        }

                        jQuery(function() {

                            function split(val) {
                                return val.split(/;\s*/);
                            }

                            function extractLast(term) {
                                return split(term).pop();
                            }

                            var mo_roles = <?php echo json_encode($mo_roles); ?>;

                            jQuery(".mo_roles_suggest")
                                .on("keydown", function(event) {
                                    if (event.keyCode === jQuery.ui.keyCode.TAB && jQuery(this).autocomplete("instance").menu.active) {
                                        event.preventDefault();
                                    }
                                })
                                .autocomplete({
                                    minLength: 0,
                                    source: function(request, response) {
                                        response(jQuery.ui.autocomplete.filter(mo_roles, extractLast(request.term)));
                                    },
                                    focus: function() {
                                        return false;
                                    },
                                    select: function(event, ui) {
                                        var terms = split(this.value);
                                        terms.pop();
                                        terms.push(ui.item.value);
                                        terms.push("");
                                        this.value = terms.join(";");
                                        return false;
                                    }
                                });
                        });
                    </script>
                </div>
            </form>
        <br><br>
    </div>
<?php
}

function papr_post_display_pages($post, $allowed_roles, $allowed_redirect_post, $unrestricted_posts, $access_for_only_loggedin_post,$post_type) {
    $disabled = '';
    if($post_type != 'post')
        $disabled = 'disabled';

    $postid = $post->ID;
?>
    <tr id="<?php echo esc_attr($post->ID) ?>" <?php
        if($post_type != 'post'){ echo ' class="papr-prem-info"'; } ?> >
        <td>
            <a href="<?php echo esc_url($post->guid) ?>" target="_blank"> <?php echo esc_html($post->post_title) ?>&nbsp;
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5z"></path>
                    <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0v-5z"></path>
                </svg>
            </a>
        </td>
        <?php
        $mo_post_roles_value = array();
        if (!empty($allowed_roles[$post->ID])) {
            $mo_post_roles_value = $allowed_roles[$post->ID];
        }
        $mo_post_val=implode(";",$mo_post_roles_value);
        ?>
        <td>
        <input type="hidden" name="mo_hidden_post_roles_<?php echo esc_attr($postid) ?>[]" id="mo_hidden_post_roles_<?php echo esc_attr($postid) ?>" value="<?php echo esc_attr($mo_post_val) ?>">
        <select multiple class="roles_multi w-75" name="mo_post_roles_<?php echo esc_attr($postid) ?>[]" id="mo_post_roles_<?php echo esc_attr($postid) ?>" value="<?php echo esc_attr($mo_post_val) ?>" onchange="auto_private_when_roles_assigned(<?php echo esc_js($postid); ?>,'post')">
                <?php
                papr_display_roles($mo_post_roles_value);
                ?>
        </select>
        </td>

        <?php
        $mo_post_login_check = "";
        $mo_post_login_check_value = "";

        if (!empty($allowed_redirect_post[$post->ID])) {
            $mo_post_login_check_value = $allowed_redirect_post[$post->ID];
        }

        if ($mo_post_login_check_value == 1 || $mo_post_login_check_value == 'on' || $mo_post_login_check_value == 'true' || $access_for_only_loggedin_post == 1) {
            if($post_type == 'post')
                $mo_post_login_check = 'checked';
        }

        if ($access_for_only_loggedin_post == 1 && !empty($unrestricted_posts[$postid])) {
            $mo_post_login_check = '';
        }

        ?>
			<th scope="row" class="check-column">
			<label class="screen-reader-text" for="cb-select-3"></label>
			<input style="text-align:center; margin-left: 88px;" id="cb-select-3" name="mo_post_login_<?php echo esc_attr($post->ID) ?>" class="mo_post_login_valid_<?php echo esc_attr($post->ID) ?>" <?php echo esc_attr($disabled) ?> type="checkbox" <?php echo esc_attr($mo_post_login_check) ?> >
	    <?php
        if($post_type != 'post') { ?>
		    <div class="papr-prem-icn papr-prem-icn-log" style="margin-left:130px;"><img src="<?php echo esc_url(plugin_dir_url(__FILE__)) ?>includes/images/lock.png" width="35px" />
                <p class="papr-prem-info-text">Available in <b>Paid</b> versions of the plugin. <a href="<?php echo esc_url(admin_url('admin.php?page=page_restriction&tab=premium_plan')) ?>" class="text-warning">Click here to upgrade</a></p>
           </div>
        <?php
	    }
        ?>
			<div class="locked-indicator">
			<span class="locked-indicator-icon" aria-hidden="true"></span>
			<span class="screen-reader-text"></span>
			</div>
            </th>
    </tr>
    <?php
}

function papr_post_toggle_all_pages() {
        ?>
        <div class="mt-5">
            <h5 class="papr-form-head papr-form-head-bar">Global Settings for all Posts
            <div class="ms-2 papr-info-global">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                </svg>
                <p class="papr-info-text-global">These settings would be <b>applied to all posts</b> on this WordPress site.</p>
            </div>
            </h5>
            <form id="papr_access_for_only_loggedin_posts" name="papr_access_for_only_loggedin_posts" method="post" action="" class="mt-4">
                <?php wp_nonce_field('papr_access_for_only_loggedin_posts'); ?>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Make all Posts accessible to only Logged In users
                        <div class="ms-2 papr-info-global">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                            </svg>
                            <p class="papr-info-text-global">Enable this toggle to <b>allow only logged in users</b> to access the default posts of this WordPress site.</p>
                        </div>
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <label class="switch">
                            <input type="checkbox" id="logged_in_post" name="papr_access_for_only_loggedin_posts" onChange="document.getElementById('papr_access_for_only_loggedin_posts').submit()"
                            <?php
                            if (get_site_option('papr_access_for_only_loggedin_posts') == 1)
                                echo ' checked ';
                            ?>/>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <input type="hidden" name="option" value="papr_access_for_only_loggedin_posts">
            </form>
        </div>
    <?php
}

function papr_get_custom_post_types() {
	$args = array(
		'public'   => true,
		'_builtin' => false
	);
	
	$output = 'names'; // names or objects, note names is the default
	$operator = 'and'; // 'and' or 'or'
	 
	$post_types = get_post_types( $args, $output, $operator ); 
	return $post_types;
}
?>