<?php

require_once 'page-restriction-menu-settings.php';

function papr_dropdown($results_per_page, $type, $post_type = '')
{
    $dropdown_type = 'papr_'.$type.'_per_page';
    if($type == 'page') {
        $dropdown_type = 'papr_results_per_page';
    }
?>

    <form id="<?php esc_attr_e($dropdown_type); ?>" name="<?php esc_attr_e($dropdown_type); ?>" method="post" action="">
        <input type="hidden" name="option" value="<?php echo esc_attr($dropdown_type); ?>">
        <?php wp_nonce_field($dropdown_type); ?>
        <div class="row align-items-center">
            <div class="col-md-3">
                <h6>Number of items per page:</h6>
            </div>
            <div class="col-md-3">
                <select name="<?php echo esc_attr($dropdown_type); ?>" id="results_per_page" onChange="document.getElementById('<?php echo esc_js($dropdown_type); ?>').submit()" style="width:60px;">
                    <?php
                    for ($i = 1; $i <= 5; $i++) {
                        $value = $i * 10;
                        echo '<option value="' . esc_attr($value) . '"';
                        if ($results_per_page == $value) {
                            echo ' selected ';
                        }
                        echo '>' . esc_html($value) . '</option>';
                    }
                    ?>
                </select>
            </div>
    </form>
            <?php if ( $type == 'post') { ?>
            <div class="col-md-3">
                <h6> Please Select type of post:</h6>
            </div>
            <div class="col-md-3"> <?php papr_post_type_dropdown( $post_type ); ?> </div>
            <?php
            } ?>
        </div>
        <br>
<?php
}

function papr_post_type_dropdown ($post_type) {
    ?>
    <form id="papr_post_type" name="papr_post_type" method="post" action="">
        <input type="hidden" name="option" value="papr_post_type">
        <?php wp_nonce_field('papr_post_type'); ?>
        <select name="papr_post_type" id ="post_type" onChange="document.getElementById('papr_post_type').submit()" style="width:150px;">
            <?php
            $default = "post";
            echo '<option value="'.esc_attr($default).'"';
            echo '>Default Post</option>';

            $custom_post_types = papr_get_custom_post_types();
            foreach ( $custom_post_types as $post_key ) {
                $post_type_obj = get_post_type_object( $post_key );
                $post_label = $post_type_obj->labels->name;
                echo '<option value="'.esc_attr($post_key).'"';
                if($post_type === $post_key)
                {echo ' selected ';}
                echo '>'.esc_html($post_label).'</option>';
            }
            ?>
        </select>
    </form>
    <?php
}

function papr_search_box($type,$mo_search_value)
{
    $temp = 'papr_search_'.$type;
    ?>
    
    <form id="<?php echo esc_attr($temp); ?>" name="<?php echo esc_attr($temp); ?>" method="post" action="">
    <input type="hidden" name="option" value="<?php echo esc_attr($temp); ?>">
    <?php wp_nonce_field($temp); 
        $textbox_id = 'mo_'.$type.'_search';
    ?>
    <div id="search_page_post">
        <h6>Search <?php echo esc_html(ucfirst(strtolower($type))); ?>:</h6>
        <div style="display:flex;align-items:center;">
            <div id="search" style="display:flex;align-items-center;" onmouseover = "document.getElementById('cross-img-<?php echo esc_js($type);?>').style.display='block'; document.getElementById('cross-img-<?php echo esc_js($type);?>').style.cursor='pointer';" onmouseout = "document.getElementById('cross-img-<?php echo esc_js($type);?>').style.display = 'none'">
                <div id="text">
                    <input type="text" name="<?php echo esc_attr($textbox_id); ?>" id="<?php echo esc_attr($textbox_id); ?>" style="width: 270px; margin-bottom:5px; padding-right:33px;"
                        value="<?php echo esc_attr($mo_search_value) ?>" placeholder="Search">
                    
                    <div id="image">
                        <svg id ="cross-img-<?php echo esc_attr($type);?>" style="margin-top:-2.15rem;position:absolute;left:17rem;display:none;" xmlns="http://www.w3.org/2000/svg"
                             
                            width="24" height="24" fill="currentColor" class="align-middle text-danger rotate-45" viewBox="0 0 14 14">
                            <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </div>
                </div>
            </div>
            </form>
            <div style="margin-left:1rem;margin-bottom:0.4rem">
                <button type="button" id = "<?php echo esc_attr($temp); ?>_button" class="papr-btn-cstm rounded"  style="padding:0.17rem 1.5rem !important"
                    >Search
                </button>
            </div>
        </div>
    </div>
    </br>

    <script>
        jQuery(document).ready(function() {
            var page_ele = document.getElementById("mo_page_search");
            if(page_ele){
                var search_page = document.getElementById('mo_page_search').value;
                if(search_page!=''){
                    jQuery('html, body').animate({
                        scrollTop: jQuery("#mo_page_search").offset().top
                    }, 0);
                }
            }

            var post_ele = document.getElementById("mo_post_search");
            if(post_ele){
                var search_post = document.getElementById('mo_post_search').value;
                if(search_post!=''){
                    jQuery('html, body').animate({
                        scrollTop: jQuery("#mo_post_search").offset().top
                    }, 0);
                }
            }
        });

        jQuery('#cross-img-page').click(function(e) {
            link = 'admin.php?page=page_restriction';
            window.open(link, "_self");
        });

        jQuery('#cross-img-post').click(function(e) {
            link = 'admin.php?page=page_restriction&tab=post_access';
            window.open(link, "_self");
        });
        
        jQuery('#papr_search_page_button').click(function(e) { 
            var search = document.getElementById('mo_page_search').value;
            link = 'admin.php?page=page_restriction';
            if(search != ''){
                link = 'admin.php?page=page_restriction&search='.concat(search);
            }
            window.open(link, "_self");
        });

        jQuery('#papr_search_post_button').click(function(e) {
            var search = document.getElementById('mo_post_search').value;
            link = 'admin.php?page=page_restriction&tab=post_access';
            if(search != ''){
                link = 'admin.php?page=page_restriction&tab=post_access&search='.concat(search);
            }
            window.open(link, "_self");
        });
    </script>
    <?php
}

function papr_pagination_button($number_of_pages_in_pagination, $total_pages, $current_page, $link, $page_button)
{
    $current_page_next = $current_page + 1;
    $current_page_prev = $current_page - 1;

    $next_page = $link . $current_page_next;
    $prev_page = $link . $current_page_prev;
    $last_page = $link . $number_of_pages_in_pagination;
    $first_page = $link . '1';

    if (!array_key_exists('tab', $_GET)) {
        if(array_key_exists('page', $_GET)!='papr_custom_roles_sub_menu'){
            $total_pages = $total_pages + 1;            //for home page
        }
    }
?>

    <div class="tablenav-pages mt-2">
        <span class="displaying-num"><?php echo esc_html($total_pages) ?> items</span>
        <span class="pagination-links">

            <?php
            if ($current_page == 1) {
                $first_page_link = 'disabled';
                $prev_page_link = 'disabled';
            } else {
                $first_page_link = 'href="' . esc_url($first_page) . '"';
                $prev_page_link = 'href="' . esc_url($prev_page) . '"';
            }
            ?>

            <a class="first-page rounded papr-pagination-btn" <?php echo $first_page_link; ?>>
                <span class="screen-reader-text">First page</span>
                <span aria-hidden="true">«</span>
            </a>
            <a class="prev-page rounded papr-pagination-btn" <?php echo $prev_page_link; ?>>
                <span class="screen-reader-text">Previous page</span>
                <span aria-hidden="true">‹</span>
            </a>

            <span class="paging-input">
                <label for="current-page-selector" class="screen-reader-text">Current Page</label>

                <?php
                if ($page_button == 'top')
                    $current_page_selector = 'current-page-selector';
                else
                    $current_page_selector = 'current-page-selector-1';
                ?>

                <input class="current-page" id="<?php echo esc_attr($current_page_selector); ?>" type="text" name="paged" value="<?php echo esc_attr($current_page) ?>" size="1" aria-describedby="table-paging" form="papr_current_page">
                <span class="tablenav-paging-text"> of
                    <span class="total-pages"><?php echo esc_html($number_of_pages_in_pagination) ?></span>
                </span>
            </span>

            <?php
            if ($current_page == $number_of_pages_in_pagination) {
                $next_page_link = 'disabled';
                $last_page_link = 'disabled';
            } else {
                $next_page_link = 'href="' . esc_url($next_page) . '"';
                $last_page_link = 'href="' . esc_url($last_page) . '"';
            }
            ?>

            <a class="next-page rounded papr-pagination-btn" <?php echo $next_page_link; ?>>
                <span class="screen-reader-text">Next page</span>
                <span aria-hidden="true">›</span>
            </a>
            <a class="last-page rounded papr-pagination-btn" <?php echo $last_page_link; ?>>
                <span class="screen-reader-text">Last page</span>
                <span aria-hidden="true">»</span>
            </a>
        </span>
    </div>
<?php
}

function papr_get_page_post_count($mo_page_post_search_value,$type) {
    if($mo_page_post_search_value==''){
	    if($type=='page'){
            $all_parent_pages = array(
                'post_parent' => 0,
                'numberposts' => -1,
                'post_type' => $type
            );
            $total_pages_post = get_posts($all_parent_pages);
        }

        else {
            $total_pages_post_count = wp_count_posts($type)->publish; // @TODO: Find an alternative
            return $total_pages_post_count;
        }
    }

    else {
	    global $wpdb;
	    $total_pages_post = $wpdb->get_results("SELECT * FROM wp_posts WHERE (post_title LIKE '%{$mo_page_post_search_value}%') AND post_type='$type'");
    }
    
    $total_pages_post_count = count($total_pages_post);
    return $total_pages_post_count;
}

function papr_get_paginated_pages_post($mo_page_post_search_value, $results_per_page, $current_page,$type) {
	if ($mo_page_post_search_value == '') {
		$required_pages_post = array(
			'post_parent' => 0,
			'posts_per_page' => $results_per_page,
			'post_type' => $type,
			'paged' => $current_page,
			'orderby' => 'publish_date',
			'order' => 'ASC',
		);
		$pagination = get_posts($required_pages_post);
	}
	else {
	    global $wpdb;
		$skip = ($current_page-1)*$results_per_page;
		$pagination = $wpdb->get_results(" SELECT * FROM wp_posts 
                        WHERE post_title LIKE '%$mo_page_post_search_value%' AND post_type='$type' AND post_status='publish' 
                        ORDER BY post_title 
                        limit $results_per_page OFFSET $skip ");
    }
	return $pagination;
}

function papr_display_head_foot_of_table($type)
{
    ?>
    <tr>
   <td style="width:200px;padding-left:50px;" id="cb" class="manage-column column-cb"><?php echo esc_html($type) ?></td>

    <td style="width:250px;">Enter Roles who can view this <?php echo esc_html($type) ?>
        <div class="papr-info-global ms-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
            </svg>
            <p class="papr-info-text-global">
                Only the roles that are entered in the input box will have access to the <?php echo esc_html($type) ?> <b>(By default all <?php echo esc_html($type) ?> are accessible to all users irrespective of their roles)</b>.
            </p>
        </div>
    </td>
    <?php
    if ($type == 'Page') { ?>
        <td style="width:170px;text-align:center;">Auto-assign Parent Configuration to Child Pages</td>
    <?php
    } ?>
    <td style="width:120px;text-align:center;" id="cb" class="manage-column column-cb check-column">Make <?php echo esc_html($type) ?> Private
        <br>
        <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
        <input id="cb-select-all-1" type="checkbox" style="margin:5px 0;">
    </td>
    </tr>
<?php
}

function papr_get_current_page($number_of_pages_in_pagination)
{
    $current_page = 1;
    if (isset($_REQUEST['curr']) && ($_REQUEST['curr'] > 1)) {
        $current_page = $_REQUEST['curr'];
        if ($current_page > $number_of_pages_in_pagination)
            $current_page = $number_of_pages_in_pagination;
    }

    return $current_page;
}

function papr_display_pages($mo_page_search_value, $page, $color, $allowed_roles, $allowed_redirect_pages, $default_role_parent, $unrestricted_pages, $default_login_toggle)
{
    $children = array();
    if (!is_object($page)) {
        if ($page == 0)
            $pageid = 0;
    } else {
        $pageid = $page->ID;
        $children = get_pages(array('child_of' => $pageid));
    }
    ?>
    <tr id="<?php echo esc_attr($pageid) ?>" style="background-color:<?php echo esc_attr($color) ?>">

        <td class="d-inline-flex">
            <?php
            if ($pageid == 0) { ?>
                <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p><a href="<?php echo esc_url(get_home_url()) ?>" target="_blank" class="d-flex align-items-center">Home Page&nbsp; <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5z"></path>
                <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0v-5z"></path>
                </svg></a>
                <?php
            } else {
                if($mo_page_search_value=='') {
                    $ancestor_array = get_post_ancestors($pageid);
                    $total_ancestor = count($ancestor_array);
                    for ($i = 0; $i < $total_ancestor; $i++)
                        echo '&nbsp&nbsp&nbsp&nbsp';

                    if (count($children) > 0) {
                        $child_with_id = array();
                        foreach ($children as $child)
                            array_push($child_with_id, $child->ID);
                        $parsed_child = json_encode($child_with_id);
                    ?>
                        <a class="me-2" onclick="hide_show_child(<?php echo esc_js($pageid) ?>)">
                            <img id="toggle_image_<?php echo esc_attr($pageid) ?>" src="<?php echo esc_url(plugin_dir_url(__FILE__)) ?>.'/includes/images/collapse.png'">
                        </a>
                        <input type=hidden id="child_list_<?php echo esc_attr($pageid) ?>" value="<?php echo esc_attr($parsed_child) ?>">
                        <input type=hidden id="child_list_toggle_<?php echo esc_attr($pageid) ?>" value="show">
                    <?php
                    }
                    else { ?>
                        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
                    <?php }
                    echo " ";
                }
                $page_link = get_site_url() . '/' . $page->post_title . '/';
                ?>
                <a href="<?php echo esc_url($page->guid) ?>" target="_blank" class="d-flex align-items-center"><?php echo esc_html($page->post_title) ?> &nbsp; <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5z"></path>
                <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0v-5z"></path>
              </svg></a>
              <?php
            }
            ?>
        </td>

        <?php
        $mo_page_roles_value = array();
        if ($pageid == 0) {
            if (!empty($allowed_roles['mo_page_0'])) {
                $mo_page_roles_value = $allowed_roles['mo_page_0'];
            }
        } else {
            if (!empty($allowed_roles[$pageid]))
                $mo_page_roles_value = $allowed_roles[$pageid];
        }
        $page_role_login_disable = '';  
        $page_role_login_disable_check = '';  
        $parent_id = (int)wp_get_post_parent_id( $pageid );
        
        if (!empty($default_role_parent[$parent_id]) && $parent_id != 0)
            $page_role_login_disable_check = $default_role_parent[$parent_id];

        if ($page_role_login_disable_check == 1 || $page_role_login_disable_check == 'on' || $page_role_login_disable_check == 'true')
            $page_role_login_disable = 'disabled';
	$mo_page_val=implode(";",$mo_page_roles_value);
        ?>
        <td>
        <input type="hidden" name="mo_hidden_page_roles_<?php echo esc_attr($pageid) ?>[]" id="mo_hidden_page_roles_<?php echo esc_attr($pageid) ?>" value="<?php echo esc_attr($mo_page_val) ?>">
            <select multiple class="roles_multi w-100" name="mo_page_roles_<?php echo esc_attr($pageid) ?>[]" id="mo_page_roles_<?php echo esc_attr($pageid) ?>" value="<?php echo esc_attr($mo_page_val) ?>" <?php echo esc_attr($page_role_login_disable); ?> onChange ="apply_roles_to_child(event, <?php echo esc_js($pageid); ?>)">
                <?php
                papr_display_roles($mo_page_roles_value);
                ?>
            </select>
        </td>
        <?php
        if($mo_page_search_value==''){
            ?>
            <td style="border-right-style: none;text-align:center;" scope="row" class="check-column-1">
                <?php
                if (count($children) > 0) {
                    $mo_page_default_check = "";
                    $mo_page_default_check_value = "";
                    $hidden_value = 2;

                    if (!empty($default_role_parent[$pageid]))
                        $mo_page_default_check_value = $default_role_parent[$pageid];

                    if ($mo_page_default_check_value == 1 || $mo_page_default_check_value == 'on' || $mo_page_default_check_value == 'true') {
                        $mo_page_default_check = 'checked';
                        $hidden_value = 1;
                    }
                    $page_role_login_disable = '';
                    $page_role_login_disable_check = '';
                    $parent_id = (int)wp_get_post_parent_id( $pageid );

                    if (!empty($default_role_parent[$parent_id]) && $parent_id != 0)
                        $page_role_login_disable_check = $default_role_parent[$parent_id];

                    if ($page_role_login_disable_check == 1 || $page_role_login_disable_check == 'on' || $page_role_login_disable_check == 'true')
                        $page_role_login_disable = 'disabled';
                    ?>
                    <input type=hidden id="child_list_hidden_<?php echo esc_attr($pageid) ?>" value="<?php echo esc_attr($hidden_value) ?>">
                    <input id="mo_page_default_role_valid_<?Php echo esc_attr($pageid) ?>" name="mo_page_default_role_<?Php echo esc_attr($pageid) ?>" type="checkbox" <?php echo esc_attr($mo_page_default_check) ?> <?php echo esc_attr($page_role_login_disable) ?> onChange="disable_enable_child(<?php echo esc_js($pageid); ?>)" >
                    <?php
                } ?>
            </td>
            <?php
        } else{
            echo '<td></td>';
        }
        $mo_page_login_check_value = "";
        $mo_page_login_check = "";

        if (!empty($allowed_redirect_pages[$pageid]))
            $mo_page_login_check_value = $allowed_redirect_pages[$pageid];

        if ($mo_page_login_check_value == 1 || $mo_page_login_check_value == 'on' || $mo_page_login_check_value == 'true' || $default_login_toggle == 1)
            $mo_page_login_check = 'checked';

        if ($default_login_toggle == 1 && !empty($unrestricted_pages[$pageid]))
            $mo_page_login_check = '';
        ?>

        <td style="width:200px; text-align:center; padding-left:8px;" scope="row" class="check-column">
            <label class="screen-reader-text" for="cb-select-3"></label>
            <input id="cb-select-3" class ="mo_page_login_valid_<?php echo esc_attr($pageid) ?>" name="mo_page_login_<?php echo esc_attr($pageid) ?>" type="checkbox" <?php echo esc_attr($mo_page_login_check) ?> <?php echo esc_attr($page_role_login_disable); ?>
            onChange="apply_to_child(<?php echo esc_js($pageid); ?>)">

            <div class="locked-indicator">
                <span class="locked-indicator-icon" aria-hidden="true"></span>
                <span class="screen-reader-text"></span>
            </div>
        </td>
    </tr>
    <?php
}

function papr_toggle_all_pages() {
?>
    <div class="mt-5">
        <h5 class="papr-form-head papr-form-head-bar">Global Settings for all Pages
        <div class="papr-info-global ms-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
            </svg>
            <p class="papr-info-text-global"> These settings would be <b>applied to all pages</b> on this WordPress site.</p>
        </div>
        </h5>
        <form id="papr_access_for_only_loggedin" name="papr_access_for_only_loggedin" method="post" class="mt-4">
            <?php wp_nonce_field('papr_access_for_only_loggedin'); ?>
            <div class="row">
                <div class="col-md-6">
                    <h6>Make all Pages Private
                    <div class="papr-info-global ms-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                        </svg>
                        <p class="papr-info-text-global">
                            Enable this toggle to <b>allow only logged in users</b> to access the pages of this WordPress site.
                        </p>
                    </div>
                    </h6>
                </div>
                <div class="col-md-4">
                    <input type="hidden" name="option" value="papr_access_for_only_loggedin">
                    <label class="switch">
                        <input type="checkbox" id="logged_in" name="papr_access_for_only_loggedin"
                        <?php
                        if (get_site_option('papr_access_for_only_loggedin') == 1)
                            echo ' checked ';
                        ?>
                        onChange="document.getElementById('papr_access_for_only_loggedin').submit()">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </form>
        <form id="papr_default_role_parent_page_toggle" name="papr_default_role_parent_page_toggle" method="post" class="mt-4">
            <?php wp_nonce_field('papr_default_role_parent_page_toggle'); ?>
            <div class="row">
                <div class="col-md-6">
                    <h6>Auto-assign Parent Configurations to all Child Pages
                    <div class="papr-info-global ms-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                        </svg>
                        <p class="papr-info-text-global">
                            Enable this toggle to apply the restrictions of the parent pages to their respective child pages.
                        </p>
                    </div>
                    </h6>
                </div>
                <div class="col-md-4">
                    <input type="hidden" name="option" value="papr_default_role_parent_page_toggle">
                    <label class="switch">
                        <input type="checkbox" id="default_role_toggle" name="papr_default_role_parent_page_toggle"
                        <?php
                        if (get_site_option('papr_default_role_parent_page_toggle') == 1)
                            echo 'checked ';
                        ?>
                        onChange = "document.getElementById('papr_default_role_parent_page_toggle').submit()">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </form>
    </div>
<?php
}

function papr_restriction_behaviour_pages(){
    ?>
            </br>
            <div class="row">
                <div class="col-md-6">
                    <h6>Configure the Restriction Behavior
                    <div class="papr-info-global ms-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                        </svg>
                        <p class="papr-info-text-global">
                            Configure what the restricted users should be shown when a restricted page/post is accessed.
                        </p>
                    </div>
                    </h6>
                </div>
                <div class="col-md-4">
                    <a href="#restricted_behaviour" style="color:#fff; text-decoration:none;"><button class="papr-btn-cstm" style="border-radius: 2.25rem;">Click here!</button></a>
                </div>
            </div>
    <?php
}

function papr_display_roles($mo_type_roles_value){
    global $wp_roles;
    $roles = $wp_roles->roles;
    foreach ($roles as $key => $val) {
        $role_exist = -1;
        if(is_array($mo_type_roles_value)){
            $role_exist = array_search($key, $mo_type_roles_value);
            $role_exist++; // to make work in php7.4
        }
        echo '<option value="' . esc_attr($key) . '"';
            if ($role_exist>=0 && $role_exist!='') {
                echo ' selected ';
            } 
        echo '>' . esc_html($key) . '</option>';
    }
}

function papr_add_query_arg($url){
	if(strpos($url, 'premium_plan') !== false){
		$url = str_replace('premium_plan', 'account_setup', $url);
	}
	return $url;
}

function papr_check_option_admin_referer($option_name){
	return (isset($_POST['option']) and $_POST['option']==$option_name and check_admin_referer($option_name));
}

function papr_message_success_fail(){
    $papr_message_success_fail = get_option('papr_message_success_fail');
    $style = '';
    if($papr_message_success_fail == 'success'){
        $style = 'border-left-color: green ; background-color:#90ee9096 !important; overflow:auto;';
    } else if($papr_message_success_fail == 'error') {
        $style = 'border-left-color: red ; background-color:#ff7f7f63 !important; overflow:auto;';
    }

    $papr_message = get_option('papr_message');
    if($papr_message != ''){
        echo '
            <div class="rounded bg-white papr-shadow p-2 pt-1 mt-4 ms-4" style="border-left-style: solid;'.$style.'">
                '.$papr_message.'
            </div>';
    }
    delete_option('papr_message');
    delete_option('papr_message_success_fail');
}

?>