<?php

require_once 'page-restriction-menu-settings.php';
require_once 'page-restriction-save.php';
require_once 'page-restriction-custom-roles-constants.php';
include_once 'page-restriction-utility.php';

class papr_custom_roles {   

    public static function create_edit_roles($current_edit_role,$papr_custom_roles) {
        $papr_current_role_capabilty = array();
        $disabled = '';

        if(!empty($papr_custom_roles[$current_edit_role])){
            $papr_current_role_capabilty = $papr_custom_roles[$current_edit_role]['capabilities'];
            $disabled = 'readonly';
        }
        
        papr_custom_roles::roles_form($current_edit_role, $papr_current_role_capabilty, $disabled);
    }

    public static function create_clone_roles($current_edit_role,$papr_custom_roles) {
        $papr_current_role_capabilty = array();
        $disabled = '';

        if ($current_edit_role!='') {
            if(substr($current_edit_role, -6)=='_clone'){
                $current_edit_role_pre_clone = substr($current_edit_role, 0, -6);
                if(!empty($papr_custom_roles[$current_edit_role_pre_clone])){
                    $papr_current_role_capabilty = $papr_custom_roles[$current_edit_role_pre_clone]['capabilities'];
                }
            }
        }
        
        papr_custom_roles::roles_form($current_edit_role, $papr_current_role_capabilty, $disabled);
    }

    public static function roles_form($current_edit_role, $papr_current_role_capabilty, $disabled){
        ?>
        <div class="rounded bg-white papr-shadow p-4 ms-4 mt-4">
            <div class="row">
                <div class="col-md-12">
                    <h4>
                        <?php
                        if($disabled == ''){
                            echo 'Add new role and define it\'s capabilities';
                        } else {
                            echo 'Edit role and it\'s capabilities';
                        }
                        ?>
                    </h4>
                </div>
            </div>
            <div class="form-head"></div>
            <?php
                papr_custom_roles::clone_roles_form();
            ?>
            <h5 class="papr-form-head papr-form-head-bar mt-5">
                
                <?php
                    if($disabled == ''){
                        echo 'Add New Roles';
                    } else {
                        echo 'Edit Roles : <b style="color:#00008B;">'.$current_edit_role.'</b>';
                    }
                    
                    if($disabled == ''){
                        ?>
                        <div class="papr-info-global ms-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                            </svg>
                            <p class="papr-info-text-global">
                                Just turn on check boxes of capabilities you wish to add to the new role you are creating and then click <b style="color:red">Save Configuration</b> button to save your changes.
                            </p>
                        </div>
                        <?php
                    }
                ?>
            </h5>
            <?php
                if($disabled == ''){
                    $form_name = "papr_custom_create_roles";
                } else {
                    $form_name = "papr_custom_edit_roles";
                }
                $button_value = 'Save Configuration';
                global $wp_roles;
                $roles = $wp_roles->roles;
                $roles_with_name = array();
                foreach($roles as $key=>$value){
                    array_push($roles_with_name, $key);
                }
                $parsed_roles = json_encode($roles_with_name);
            ?>
            <input type="hidden" name="option" id="roles_valid_hidden_input" value='<?php echo esc_attr($parsed_roles); ?>'>
            <form id="<?php echo esc_attr($form_name); ?>" name="<?php echo esc_attr($form_name); ?>" method="post" action="">
                <input type="hidden" name="option" value="<?php echo esc_attr($form_name); ?>">
                <?php
                    wp_nonce_field($form_name);
                    $role_button_disable = "";
                    if($disabled == '') {
                        ?>
                        <input required type="text" name="custom_role_name" id="custom_role_name" value="<?php echo esc_attr($current_edit_role);?>" 
                        onkeyup="validate_role_name();"
                        style="width: 270px; margin-bottom:5px;" placeholder="Enter Role Name">
                        <?php
                        
                        if(!empty($roles[$current_edit_role])) {
                            ?>
                            <p id="role_name_error" style="color:red">This role name already exist</p>
                            <?php
                            $role_button_disable = "disabled";
                        } else {
                            ?>
                            <p id="role_name_error" style="color:red"></p>
                            <?php
                        }
                    } else {
                        ?>
                        <input type="hidden" name="custom_role_name" id="custom_role_name" value="<?php echo esc_attr($current_edit_role);?>">
                        <?php
                    }
                ?>
                <br>
                <b>Note : </b>Please Select the Capabilites you want to assign to the Role before you Save and add/edit the role.
                </br></br>
                <input type='submit' value="<?php echo esc_attr($button_value); ?>" class="papr-btn-cstm-role-create role_submit_button" style="border-radius: 2.25rem;" <?php echo esc_attr($role_button_disable); ?>>
                <div>
                    </br></br>
                    <div class="flex-container-custom-role">
                        <div class="display_custom_role_nav">
                            <?php
                                papr_custom_roles::display_custom_role_nav();
                            ?>
                        </div>
                            
                        <div id="capability-table" class="display_custom_role_table_edit">
                            <?php
                                papr_custom_roles::display_custom_role_table_edit($papr_current_role_capabilty,$current_edit_role);
                            ?>
                        </div>
                    </div>
                    </br>
                </div>
                <input type='submit' value="<?php echo esc_attr($button_value); ?>" class="papr-btn-cstm-role-create role_submit_button" style="border-radius: 2.25rem;" <?php echo esc_attr($role_button_disable); ?>>
            </form>
        </div>
        </br>
        <script>
            var roles_array = document.getElementById('roles_valid_hidden_input').value;
            text = roles_array.slice(1, -1);
            var roles_array = text.split(",");
            for (var i = 0; i < roles_array.length; i++) {
                roles_array[i] = roles_array[i].slice(1, -1);
            }

            function validate_role_name(){
                var format = /[!@#$%^&*()+\-=\[\]{};':"\\|,.<>\/?]+/;
                var role_name = document.getElementById('custom_role_name').value;
                let index = roles_array.indexOf(role_name);
                if(index != -1) {
                    document.getElementById("role_name_error").innerHTML = "This role name already exist";
                    document.getElementsByClassName("role_submit_button")[0].disabled = true;
                    document.getElementsByClassName("role_submit_button")[1].disabled = true;
                } else if(format.test(role_name)){
                    document.getElementById("role_name_error").innerHTML = "Special characters in the role name not allowed.";
                    document.getElementsByClassName("role_submit_button")[0].disabled = true;
                    document.getElementsByClassName("role_submit_button")[1].disabled = true;
                } else {
                    document.getElementById("role_name_error").innerHTML = " ";
                    document.getElementsByClassName("role_submit_button")[0].disabled = false;
                    document.getElementsByClassName("role_submit_button")[1].disabled = false;
                }
            }
        </script>
        <?php
    }

    public static function display_custom_role_nav(){
        ?>

        <ul class="papr-tab-nav">
            <?php
            $papr_capabilites_category = papr_custom_roles_constants::papr_custom_roles_constants_names();
            foreach($papr_capabilites_category as $key=>$value){
                ?>
                <li class="papr-tab-title">
                    <?php
                        $key_js_function  = "papr-tab-".$key;
                        $key_up = str_replace('_', ' ', $key);
                        $key_up = ucwords($key_up);

                        if($key != 'all') {
                            $background_color = '#fff';
                            if($key=='general') {
                                $background_color = '#f1f6ff';
                            }
                            ?>
                                <a onclick="hide_function('<?php echo esc_js($key_js_function); ?>');" class="roles_buttons">
                                    <div class="div_button" id="<?php echo esc_attr($key_js_function); ?>_button" style="width:170px;height:40px; padding-left:10px; padding-top:5px; background-color:<?php echo $background_color; ?>;">
                                        <i style="vertical-align:middle !important;" class="<?php echo esc_attr($value); ?>"></i>
                                        <span class="label"><?php echo esc_html($key_up); ?></span>
                                    </div>
                                </a>
                            <?php
                        } else {
                            ?>
                                <a onclick="show_all_function();" class="roles_buttons" >
                                    <div class="div_button" id="<?php echo esc_attr($key_js_function); ?>_button" style="width:170px;height:40px; padding-left:10px; padding-top:5px; background-color:#fff;">
                                            <i style="vertical-align:middle !important;" class="<?php echo esc_attr($value); ?>"></i>
                                            <span class="label"><?php echo esc_html($key_up); ?></span>
                                    </div>
                                </a>
                            <?php
                        }
                    ?>
                </li>
                <?php
            }
            ?>
        </ul>
        <script>
            function hide_function(id) {
                var divs = document.getElementsByClassName('hide-category-div');
                var i;
                for (i = 0; i < divs.length; i++) {
                    divs[i].style.display = 'none';
                }

                var divid = document.getElementsByClassName(id);
                var i;
                for (i = 0; i < divid.length; i++) {
                    divid[i].style.display = '';
                }

                var div_button = document.getElementsByClassName('div_button');
                var i;
                for (i = 0; i < div_button.length; i++) {
                    div_button[i].style.backgroundColor = '#fff';
                }

                var id_button = id + '_button';
                document.getElementById(id_button).style.backgroundColor = '#f1f6ff';
            }

            function show_all_function() {
                var divs = document.getElementsByClassName('hide-category-div');
                var i;
                for (i = 0; i < divs.length; i++) {
                    divs[i].style.display = '';
                }

                var div_button = document.getElementsByClassName('div_button');
                var i;
                for (i = 0; i < div_button.length; i++) {
                    div_button[i].style.backgroundColor = '#fff';
                }

                var id_button = 'papr-tab-all_button';
                document.getElementById(id_button).style.backgroundColor = "#f1f6ff";
            }
        </script>
        <?php                     
    }

    public static function display_custom_role_table_edit($papr_current_role_capabilty,$current_edit_role){
        ?>
        <table class="role_table_width">
                <tr style="display:; border-bottom:1px solid lightblue; height:40px;">
                    <td style="width:30rem !important;"><b>Capability</b></td>
                    <td style="width:5rem !important; text-align:center;"><b>Grant</b></td>
                </tr>
            
                <?php
                    $papr_capabilites_category_array = papr_custom_roles_constants::papr_custom_roles_capabilities_constants();
                                               
                    foreach($papr_capabilites_category_array as $group_name=>$capability){
                        if($group_name=='general'){
                            $display = 'display:;';
                        } else {
                            $display = 'display:none;';
                        }
                        $papr_capabilites_category_array_name = $papr_capabilites_category_array[$group_name];
                        foreach($papr_capabilites_category_array_name as $key){
                            $capability_name = str_replace('_', ' ', $key);
                            $grant_checked = '';
                            if(!empty($papr_current_role_capabilty[$key])){
                                if($papr_current_role_capabilty[$key]==1){
                                    $grant_checked = 'checked';
                                }
                            }

                            if($key=='read' && $current_edit_role==''){
                                $grant_checked = 'checked';
                            }
                            
                            ?>
                                <tr class="hide-category-div papr-tab-<?php echo esc_attr($group_name); ?>" style="<?php echo esc_attr($display); ?> border-bottom:0.01rem solid lightblue; height:40px;">
                                    <td style="width:30rem !important;"><?php echo esc_html(ucwords($capability_name)); ?></td>
                                    <td style="width:5rem !important; text-align:center;"><input type="checkbox" name="<?php echo esc_attr($key)?>" id="<?php echo esc_attr($key)?>" <?php echo esc_attr($grant_checked);?>/></td>
                                </tr>
                            <?php
                        }
                    }
                ?>

                <tr style="height:40px;">
                    <td style="width:30rem !important;"><b>Capability</b></td>
                    <td style="width:5rem !important; text-align:center;"><b>Grant</b></td>
                </tr>
            
        </table>
        <?php
    }

    public static function edit_delete_roles() {
        global $wp_roles;
        $roles = $wp_roles->roles;
        $total_roles = count($roles);
        $roles_per_page = get_option('papr_roles_per_page');
	    $roles_per_page = $roles_per_page != '' ? $roles_per_page : 10;
        $number_of_pages_in_pagination = ceil($total_roles / $roles_per_page);;
        
        $current_page = papr_get_current_page($number_of_pages_in_pagination);
        $link = get_admin_url().'admin.php?page=papr_custom_roles_sub_menu&curr=';
        $offset = ($current_page - 1) * $roles_per_page;
        $paginated_roles = array_slice($roles, $offset, $roles_per_page);
        ?>
        
        <div class="rounded bg-white papr-shadow p-4 ms-4 mt-4">
            <div class="row">
                <div class="col-md-6">
                    <h4>Edit or Delete Custom Roles</h4>
                </div>
                <div class="col-md-6 text-end">
                    <a href="admin.php?page=page_restriction" class="papr-btn-cstm rounded"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
                        </svg>&nbsp; Back to Restriction Settings
                    </a>
                </div>
            </div>
            <div class="form-head"></div>
            </br>
            <div class="">
                <?php
                    papr_custom_roles::roles_result_per_page($roles_per_page);
                ?>
                <div style="text-align: right;">
                <?php papr_pagination_button($number_of_pages_in_pagination, $total_roles, $current_page, $link, 'top'); ?>
                </div>
            </div>
            </br>

            <form id="papr_delete_custom_roles" name="papr_delete_custom_roles" method="post" action="">
                <input type="hidden" name="option" value="papr_delete_custom_roles">
                <input type="hidden" name="role_delete" id="role_delete" value="">
                <?php wp_nonce_field("papr_delete_custom_roles"); ?>
            </form>
            <table class="wp-list-table widefat fixed table-view-list pages">
                <thead>
                    <tr>
                        <th style="width:500px;">Role</th>
                        <th style="text-align:center;">Edit Role</th>
                        <th style="text-align:center;">Number of Users</th>
                    </tr>
                </thead>
                <tbody>
                <?php

                if(count($roles)!=0){
                    papr_custom_roles::display_paginated_roles($paginated_roles);
                } else {
                    $color = 'f1f6ff';
                    ?>
                    <tr style="background-color:#<?php echo esc_attr($color); ?>;">
                        <td><b>No Roles added so far.</b></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td style="width:500px;">Role</td>
                        <td style="text-align:center;">Edit Role</td>
                        <td style="text-align:center;">Number of Users</td>
                    </tr>
                </tfoot>
            </table>
            <div style="text-align: right;">
                <?php papr_pagination_button($number_of_pages_in_pagination, $total_roles, $current_page, $link, 'bottom'); ?>
            </div>
        </div>
        </br>
        <script>
            var page_selector_up = document.getElementById("current-page-selector");
            var page_selector_down = document.getElementById("current-page-selector-1");
            var link = 'admin.php?page=papr_custom_roles_sub_menu&curr=';

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
        </script>
        <?php
        papr_custom_roles::delete_role_modal();
    }

    public static function delete_role_modal(){
        wp_enqueue_style( 'papr_admin_plugin_feedback_style', plugins_url( '/includes/css/papr_feedback_style.min.css', __FILE__ ) );
        ?>
        <div id="papr_delete_role_modal" class="mo_papr_modal_role">
            <div class="mo_papr_delete_role_content">
                <h6 style="margin: 2%; word-break:break-word;">Are you sure you want to delete <span  id="delete_role_warning" style="color:red"></span> Role ?</h6>
                <li>If the user who is part of only this role would have his role as none once you delete this role.</li>
                <li>If you create new role by same name afterwards that user would get back this role again.</li>
                </br>
                <div class="mo_papr_feedback_footer">
                    <input type="button" class="papr-btn-cstm rounded" value="Delete Role" onclick="delete_role();"/>
                    <input type="button" class="papr-btn-cstm rounded" value="Cancel" onclick="role_modal_close();"/>
                </div>
            </div>
        </div>
        <script>
                function delete_role(){
                    document.getElementById('papr_delete_custom_roles').submit();
                    role_modal_close();
                }

                function role_modal_close(){
                    var mo_modal = document.getElementById('papr_delete_role_modal');
                    var span = document.getElementsByClassName("papr_close")[0];
                    mo_modal.style.display = "none";
                }
        </script>
        <?php
    }

    public static function display_paginated_roles($paginated_roles){
        $row_no = 1;
        $user = wp_get_current_user();
        $current_user_roles = $user->roles;
        $current_user_roles=array_flip($current_user_roles);
        foreach($paginated_roles as $key=>$value){
            $edit_link = admin_url( '/admin.php?page=papr_custom_roles_sub_menu&current_edit_role='.$key );
            $clone_link = admin_url( '/admin.php?page=papr_custom_roles_sub_menu&tab=create_role&clone='.$key.'_clone' );
            $users_link = admin_url( 'users.php?role='.$key );
            $color = 'f1f6ff';
            if($row_no%2==0){
                $color = 'fff';
            }
            $row_no++;
            ?>
            <tr style="background-color:#<?php echo esc_attr($color); ?>;">
                <td>
                    <b><?php echo esc_html($key);?></b>
                    <?php
                    if(!empty($current_user_roles[$key])){
                        ?>
                        - Your Role
                        <?php
                        if(get_option('default_role')==$key){
                            ?>
                            , Default Role
                            <?php
                        }
                    } else {
                        if(get_option('default_role')==$key){
                            ?>
                            - Default Role
                            <?php
                        }
                    }
                    ?>
                    <div style="margin-top:5px;">
                        <ul class="role_usability">
                            <li><a href="<?php echo esc_url($clone_link);?>">Clone </a></li>
                            <li>| </li>
                            <li><a href="<?php echo esc_url($users_link);?>">Users </a></li>
                            <?php 
                            if(get_option('default_role')==$key){
                                $change_default = admin_url( '/options-general.php#default_role' );
                                ?>
                                <li>| </li>
                                <li><a href="<?php echo esc_url($change_default);?>">Change Default</a></li>
                            <?php
                            } else if(empty($current_user_roles[$key])){
                                ?>
                                <li>| </li>
                                <li style="color:red;" id="delete_<?php echo esc_attr($key);?>"><a onclick="papr_delete_role('<?php echo esc_js($key); ?>');" onmouseover = "document.getElementById('delete_<?php echo esc_js($key);?>').style.cursor='pointer';"
                                >Delete</a></li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                </td>
                <td style="text-align:center;">
                    <a href="<?php echo esc_url($edit_link);?>"><button class="papr-btn-cstm">Edit </button></a>
                </td>
                <td style="text-align:center;">
                    <?php
                        $users_args = array(
                            'role' => $key,
                        );
                        $users_with_role = get_users($users_args);
                        echo esc_html(count($users_with_role));
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
        <script>
            function papr_delete_role(role){
                document.getElementById("delete_role_warning").textContent = role;
                var mo_modal = document.getElementById('papr_delete_role_modal');
                mo_modal.style.display = "block";
                document.getElementById('role_delete').value = role;
            }
        </script>
        <?php
    }

    public static function roles_result_per_page($roles_per_page){
        ?>
        <form id="roles_per_page" name="roles_per_page" method="post" action="">
        <input type="hidden" name="option" value="roles_per_page">
        <?php wp_nonce_field('roles_per_page'); ?>
        <div class="row align-items-center">
            <h6 style="margin-right:10px; padding-left:15px;">Number of items per page:</h6>
            <div>
                <select name="roles_per_page" onChange="document.getElementById('roles_per_page').submit()" style="width:60px;">
                    <?php
                    for ($i = 1; $i <= 5; $i++) {
                        $value = $i * 10;
                        echo '<option value="' . esc_attr($value) . '"';
                        if ($roles_per_page == $value) {
                            echo ' selected ';
                        }
                        echo '>' . esc_html($value) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        </form>
        <?php
    }

    public static function clone_roles_form(){
        if(!array_key_exists('clone', $_GET) && isset($_GET['tab'])){
            echo '</br>';
            global $wp_roles;
            $papr_custom_roles = $wp_roles->roles;
            $clone_link = admin_url( '/admin.php?page=papr_custom_roles_sub_menu&tab=create_role&clone=');

            $papr_custom_roles_first_key = $papr_custom_roles;
            reset($papr_custom_roles_first_key);
            $first_key = key($papr_custom_roles_first_key);
            $clone_link_admin = $clone_link.$first_key.'_clone';

            ?>
            If you want to clone a role please select the role you want to clone from dropdown and click on Clone.
            <br><br>    
            Clone From : &nbsp&nbsp&nbsp
            
            <select onchange="cloneThisRole(this.value,'<?php echo esc_js($clone_link); ?>')" style="height:25px;">
                <?php
                foreach($papr_custom_roles as $key=>$value){
                    ?>
                    <option value="<?php echo esc_attr($key);?>"><?php echo esc_attr($key);?></option>
                    <?php
                }
                ?>
            </select>
            &nbsp&nbsp
            <a href="<?php echo esc_url($clone_link_admin); ?>" id="clone_role_link">
                <button class="papr-btn-cstm rounded">
                    Clone
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5z"></path>
                        <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0v-5z"></path>
                    </svg>
                </button>
            </a>
            </br>
            <script>
                function cloneThisRole(role_id,clone_link) {
                    var link = clone_link + role_id + '_clone';
                    document.getElementById('clone_role_link').href = link;
                }
            </script>
            <?php
        }
    }
}
?>