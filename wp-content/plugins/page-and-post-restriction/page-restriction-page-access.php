<?php

require_once 'page-restriction-menu-settings.php';
require_once 'page-restriction-save.php';
require_once 'page-restriction-utility.php';

function papr_page_access() {

    $results_per_page         =     get_option('papr_results_per_page');
    $allowed_roles            =     get_option('papr_allowed_roles_for_pages');
    $allowed_redirect_pages   =     get_option('papr_allowed_redirect_for_pages');
    $default_role_parent      =     get_option('papr_default_role_parent');
    $unrestricted_pages       =     get_option('papr_login_unrestricted_pages');
    $default_login_toggle     =     get_option('papr_access_for_only_loggedin');

    $results_per_page         =     $results_per_page != '' ? $results_per_page : 10;
    $allowed_roles            =     $allowed_roles != '' ? $allowed_roles : array();
    $allowed_redirect_pages   =     $allowed_redirect_pages != '' ? $allowed_redirect_pages : array();
    $default_role_parent      =     $default_role_parent != '' ? $default_role_parent : array();
    $unrestricted_pages       =     $unrestricted_pages ? $unrestricted_pages : array();
    $default_login_toggle     =     $default_login_toggle != '' ? $default_login_toggle : "";

    $mo_page_search_value = "";
    if (array_key_exists('search', $_GET)) {
        $mo_page_search_value = sanitize_text_field($_GET['search']);
    }

?>


    <div class="rounded bg-white papr-shadow p-4 ms-4 mt-4">
        <h4 class="papr-form-head">Give Access to Pages based on Roles and Login Status</h4>
        
        <?php papr_toggle_all_pages(); ?>
        <?php papr_restriction_behaviour_pages(); ?>
        <hr class="mt-4"/>
        
        <h5 class="papr-form-head papr-form-head-bar mt-5">Page Restrictions
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
            <?php papr_dropdown($results_per_page, 'page'); ?>
            <?php papr_search_box('page',$mo_page_search_value); ?>
            <form name="f" method="post" action="" id="blockedpagesform" class="mt-4">
		        <?php wp_nonce_field("papr_restrict_pages_roles_login"); ?>
                <input type="hidden" name="option" value="papr_restrict_pages_roles_login" form="blockedpagesform" />
                <input type="submit" class="papr-btn-cstm rounded" value="Save Configuration" form="blockedpagesform">
                <?php

                $total_pages = papr_get_page_post_count($mo_page_search_value,'page');
                $number_of_pages_in_pagination = ceil($total_pages / $results_per_page);
                $current_page = papr_get_current_page($number_of_pages_in_pagination);
                $pagination = papr_get_paginated_pages_post($mo_page_search_value, $results_per_page, $current_page,'page');
                
                $link = 'admin.php?page=page_restriction&curr=';
                if($mo_page_search_value!=''){
                    $link = 'admin.php?page=page_restriction&search='.$mo_page_search_value.'&curr=';
                }
                papr_pagination_button($number_of_pages_in_pagination, $total_pages, $current_page, $link, 'top');
                ?>
        </div>

        <table id="reports_table" class="wp-list-table widefat fixed table-view-list pages">
                <thead> <?php papr_display_head_foot_of_table('Page'); ?></thead>
                <tbody>
                <?php
                if(count($pagination)==0 && $mo_page_search_value !=''){
                    echo '<tr><td><b>No Results</b></td><td></td><td></td><td></td></tr>';
                } else {
                    papr_display_filtered_pages($mo_page_search_value, $pagination, $current_page, $allowed_roles, $allowed_redirect_pages, $default_role_parent, $unrestricted_pages, $default_login_toggle);
                }
                ?>
                </tbody>
                <tfoot> <?php papr_display_head_foot_of_table('Page'); ?> </tfoot>
            </table>

        <div class="tablenav bottom mt-3 pb-5">
            <input type="submit" class="papr-btn-cstm rounded mt-2" value="Save Configuration" form="blockedpagesform">
            <?php papr_pagination_button($number_of_pages_in_pagination, $total_pages, $current_page, $link, 'bottom'); ?>
        </div>
        </form>

        <?php
        $mo_roles = array();
        global $wp_roles;
        $roles = $wp_roles->roles;
        foreach ($roles as $key => $val){
            $mo_roles[] = $key;
        }
        ?>
        <script>
            var closebtns = document.getElementsByClassName("close");
            var i;

            for (var i = 0; i < closebtns.length; i++) {
                closebtns[i].addEventListener("click", function() {
                    this.parentElement.style.display = 'none';
                });
            }

            function hide_show_child(x) {
                var valid = "child_list_" + x;
                var value = document.getElementById(valid).value;

                var valid1 = "child_list_toggle_" + x;
                var value1 = document.getElementById(valid1).value;


                text = value.slice(1, -1);
                var myArray = text.split(",");
                for (var i = 0; i < myArray.length; i++) {

                    child_img_id = "toggle_image_" + myArray[i];
                    var myElement = document.getElementById(child_img_id);

                    if (value1 == 'show') {
                        document.getElementById(myArray[i]).style.display = 'none';
                        if (myElement) {
                            myElement.style.transform = 'rotate(-90deg)';
                        }
                    } else {
                        document.getElementById(myArray[i]).style.display = '';
                        if (myElement) {
                            myElement.style.transform = 'rotate(0deg)';
                        }
                    }
                }

                img_id = "toggle_image_" + x;
                var img = document.getElementById(img_id);

                if (value1 == 'show') {
                    document.getElementById(valid1).value = "hide";
                    img.style.transform = 'rotate(-90deg)';
                } else {
                    document.getElementById(valid1).value = "show";
                    img.style.transform = 'rotate(0deg)';
                }
            }

            function disable_enable_child(x) {
                var valid = "child_list_" + x;
                var value = document.getElementById(valid).value;

                var hidden_child_id =  "child_list_hidden_" + x;
                var hidden_child_id_value = document.getElementById(hidden_child_id).value;
                
                var parent_id = "mo_page_roles_" + x;
                var parent_id_role_value = document.getElementById(parent_id).value;

                var parent_id_private = "mo_page_login_valid_" + x;

                if(document.getElementsByClassName(parent_id_private)[0].checked == true){
                    var parent_id_private_value = 1;
                } else {
                    var parent_id_private_value = 2;
                }

                text = value.slice(1, -1);
                var myArray = text.split(",");
                
                for (var i = 0; i < myArray.length; i++) {
                    var child_id = "mo_page_roles_" + myArray[i];
                    var child_id_private = "mo_page_login_valid_" + myArray[i];
                    var child_id_default_role_valid = "mo_page_default_role_valid_" + myArray[i];
                    
                    if(hidden_child_id_value==1){
                        document.getElementById(child_id).disabled = false;
                        document.getElementsByClassName(child_id_private)[0].disabled = false;
                        
                        if( jQuery('#'+child_id_default_role_valid).length ) {
                            document.getElementById(child_id_default_role_valid).disabled = false;
                        }
                    }

                    else{
                        document.getElementById(child_id).disabled = true;
                        document.getElementById(child_id).value = parent_id_role_value;

                        if(parent_id_private_value==1){
                            document.getElementsByClassName(child_id_private)[0].checked = true;
                        } else {
                            document.getElementsByClassName(child_id_private)[0].checked = false;
                        }

                        document.getElementsByClassName(child_id_private)[0].disabled = true;

                        if( jQuery('#'+child_id_default_role_valid).length ) {
                            document.getElementById(child_id_default_role_valid).disabled = true;
                        }
                    }
                }

                if(hidden_child_id_value==1){
                    document.getElementById(hidden_child_id).value = 2;
                } else {
                    document.getElementById(hidden_child_id).value = 1;
                }
            }

            function apply_to_child(x){
                var default_role = 'mo_page_default_role_valid_' + x;
                var elementExists = document.getElementById(default_role);

                if(elementExists){
                    if(document.getElementById(default_role).checked){
                        var valid = "child_list_" + x;
                        var value = document.getElementById(valid).value;

                        var parent_id_private = "mo_page_login_valid_" + x;

                        if(document.getElementsByClassName(parent_id_private)[0].checked == true){
                            var parent_id_private_value = 1;
                        } else {
                            var parent_id_private_value = 2;
                        }

                        text = value.slice(1, -1);
                        var myArray = text.split(",");
                        
                        if(parent_id_private_value==1){
                            for (var i = 0; i < myArray.length; i++) {
                                var child_id_private = "mo_page_login_valid_" + myArray[i];
                                document.getElementsByClassName(child_id_private)[0].checked = true;
                            }
                        }

                        if(parent_id_private_value==2){
                            for (var i = 0; i < myArray.length; i++) {
                                var child_id_private = "mo_page_login_valid_" + myArray[i];
                                document.getElementsByClassName(child_id_private)[0].checked = false;
                            }
                        }
                    }
                }
            }

            function apply_roles_to_child(event, x){
                // if(event.key==='Enter'){
                //     event.preventDefault();
                //     return false;
                // }
                
                var default_role = 'mo_page_default_role_valid_' + x;
                var elementExists = document.getElementById(default_role);

                if(elementExists){
                    if(document.getElementById(default_role).checked){
                        var valid = "child_list_" + x;
                        var value = document.getElementById(valid).value;

                        var parent_id_roles = "mo_page_roles_" + x;
                        var parent_id_role_value = document.getElementById(parent_id_roles).value;
                        
                        text = value.slice(1, -1);
                        var myArray = text.split(",");
                        
                        for (var i = 0; i < myArray.length; i++) {
                            var child_id_role = "mo_page_roles_" + myArray[i];
                            document.getElementById(child_id_role).value = parent_id_role_value;
                        }
                    }
                }
                //auto assign the checkbox of make private pages checked while entering a role
                auto_private_when_roles_assigned(x,'page');
            }

            jQuery(".roles_multi").chosen({no_results_text: "Oops, nothing found!"}); 
            
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

            var page_selector_up = document.getElementById("current-page-selector");
            var page_selector_down = document.getElementById("current-page-selector-1");
            var page_search_link = document.getElementById("mo_page_search");
            var link = 'admin.php?page=page_restriction&curr=';

            page_selector_up.addEventListener("keyup", function(event) {
                if (event.keyCode === 13) {
                    page_selector_up_value = document.getElementById("current-page-selector").value;
                    var page_link = link.concat(page_selector_up_value);
                    window.open(page_link, "_self");
                }
            });

            page_selector_down.addEventListener("keyup", function(event) {
                if (event.keyCode === 13) {
                    page_selector_down_value = document.getElementById("current-page-selector-1").value;
                    var page_link = link.concat(page_selector_down_value);
                    window.open(page_link, "_self");
                }
            });

            page_search_link.addEventListener("keyup", function(event) {
                if (event.keyCode === 13) {
                    page_search_link_value = document.getElementById("mo_page_search").value;
                    var search_link = 'admin.php?page=page_restriction&search=';
                    var page_link = search_link.concat(page_search_link_value);
                    window.open(page_link, "_self");
                }
            });
        </script>
    </div>
<?php
}

function papr_display_filtered_pages($mo_page_search_value,$pagination, $current_page,$allowed_roles, $allowed_redirect_pages, $default_role_parent, $unrestricted_pages, $default_login_toggle) {
    $row_no = 1;

    if ($current_page == 1 && $mo_page_search_value=='') {
        $color = '#f1f6ff';
        papr_display_pages($mo_page_search_value, 0, $color, $allowed_roles, $allowed_redirect_pages, $default_role_parent, $unrestricted_pages, $default_login_toggle);
        $row_no = 2;
    }

    foreach ($pagination as $page) {
        $color = '#f1f6ff';
        if ($row_no % 2 == 0)
            $color = '#fff';

        papr_display_pages($mo_page_search_value, $page, $color, $allowed_roles, $allowed_redirect_pages, $default_role_parent, $unrestricted_pages, $default_login_toggle);
        
        if ($mo_page_search_value=='') {
            $children = get_pages(array('child_of' => $page->ID));
            if (count($children) > 0) {
                foreach ($children as $child) {
                    papr_display_pages($mo_page_search_value, $child, $color, $allowed_roles, $allowed_redirect_pages, $default_role_parent, $unrestricted_pages, $default_login_toggle);
                }
            }
        }
        
        $row_no++;
    }
}
?>