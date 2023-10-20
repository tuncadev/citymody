<?php

require_once('page-restriction-menu-settings.php');
include_once 'page-restriction-utility.php';

function papr_custom_roles_sub_menu()
{
        global $wp_roles;
        $papr_custom_roles = $wp_roles->roles;

        $current_tab = "";
        if (array_key_exists('tab', $_GET)) {
            $current_tab = sanitize_text_field( $_GET['tab']);
        } ?>
        <?php papr_custom_roles_nav_tab($current_tab); ?>
        <div class="papr-bg-main papr-margin-left">
            <div class="d-flex">
                <div class="col-md-9">
                    <?php
                        papr_message_success_fail();
                        switch ($current_tab) {
                            case 'create_role':
                                if(array_key_exists('clone', $_GET)){
                                    $current_edit_role = sanitize_text_field($_GET['clone']);
                                    papr_custom_roles::create_clone_roles($current_edit_role,$papr_custom_roles);
                                } else {
                                    papr_custom_roles::create_edit_roles('',$papr_custom_roles);
                                }
                                break;

                            default:
                                if (array_key_exists('current_edit_role', $_GET)) {
                                    $current_edit_role = sanitize_text_field($_GET['current_edit_role']);
                                    if(empty($papr_custom_roles[$current_edit_role])){
                                        papr_custom_roles::edit_delete_roles();
                                    } else {
                                        papr_custom_roles::create_edit_roles($current_edit_role,$papr_custom_roles);
                                    }
                                }
                                else {
                                    papr_custom_roles::edit_delete_roles();
                                }
                                break;
                        }
                    ?>
                </div>
                <div class="col-md-3 papr_support_col ps-0 pe-0">
                    <?php papr_support_page_restriction(); ?>
                </div>
            </div>

            <div class="row">
            </div>
        </div>
        <?php
}

function papr_custom_roles_nav_tab($current_tab)
{
    ?>
    <div class="papr-bg-main papr-margin-left">
        <div class="wrap shadow-cstm p-3 me-0 mt-0 mo-saml-margin-left bg-white" id="nav-market">
            <div class="row align-items-center">
                <div class="col-md-5 h3 ps-3 d-flex align-items-center">
                    <img src="<?php echo esc_url(plugin_dir_url(__FILE__)) ?>includes/images/miniorange-logo.png" alt="" width="50px" class="me-2">
                    <span>Page and Post Restriction</span>
                </div>
                <div class="col-md-3 text-center">
                    <a id="license_upgrade" class="text-white ps-4 pe-4 pt-2 pb-2 papr-prem-btn" href="admin.php?page=page_restriction&tab=premium_plan">Premium Plans | Upgrade Now</a>
                </div>
                <div class="col-md-4 d-flex align-items-center justify-content-end">
                    <a class="me-3 text-white ps-5 pe-5 pt-3 pb-3 papr-prem-btn" target="_blank" href="https://faq.miniorange.com/kb/saml-single-sign-on/">FAQs</a>
                    <a class="text-white ps-5 pe-5 pt-3 pb-3 papr-prem-btn" target="_blank" href="https://plugins.miniorange.com/wordpress-page-restriction">Know More</a>
                </div>
            </div>
        </div>
        <div class="nav-tab-wrapper papr-bg-main" id="nav-role">
            <a class="nav-tab papr-nav-tab <?php echo esc_attr(papr_set_active_tab($current_tab, '')); ?>" href="admin.php?page=papr_custom_roles_sub_menu">Roles
            </a>
            <a class="nav-tab papr-nav-tab ms-3 <?php echo esc_attr(papr_set_active_tab($current_tab, 'create_role')); ?>" href="admin.php?page=papr_custom_roles_sub_menu&tab=create_role"> Add New Role
            </a>
        </div>
    </div>
    <?php
}

?>