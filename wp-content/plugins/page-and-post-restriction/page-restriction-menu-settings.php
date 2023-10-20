<?php

include_once 'page-restriction-premium-plan.php';
include_once 'page-restriction-page-access.php';
include_once 'page-restriction-post-access.php';
include_once 'page-restriction-custom-roles.php';
include_once 'page-restriction-tag-access.php';
include_once 'page-restriction-category-access.php';
include_once 'page-restriction-utility.php';

/* Main Page Restriction Function which is called on addition of Menu and Submenu pages.
	Function to display the correct page content based on the active tab. */
function papr_page_restriction()
{
    $current_tab = "";
    if (array_key_exists('tab', $_GET)) {
        $current_tab = sanitize_text_field($_GET['tab']);
    } ?>

    <div class="papr-bg-main papr-margin-left">
        <?php papr_nav_tab($current_tab); ?>
        <div class="d-flex">
            <div class="col-md-9">
                <?php
                papr_message_success_fail();
                switch ($current_tab) {
                    case 'custom_restriction':
                        papr_custom_restrict();
                        break;
                    case 'account_setup':
                        papr_show_customer_page();
                        break;
                    case 'premium_plan':
                        papr_show_premium_plans();
                        break;
                    case 'post_access':
                        papr_post_access();
                        break;
                    case 'category_access':
                        papr_category_access();
                        break;
                    case 'tag_access':
                        papr_tag_access();
                        break;
                    default:
                        papr_page_access();
                        break;
                }
                ?>
                <?php
                if ($current_tab == '' || $current_tab == 'post_access' || $current_tab == 'category_access' || $current_tab == 'tag_access' ) { ?>
                    <div class="rounded bg-white papr-shadow p-4 mt-4 ms-4 mb-4" id="restricted_behaviour">
                        <h4 class="papr-form-head">Restricted Content Behaviour
                            <div class="papr-info-global ms-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                </svg>
                                <p class="papr-info-text-global">
                                    Choose what visitors will see if they don't have permission to view the restricted content.<a href="https://plugins.miniorange.com/guide-to-restrict-content-by-user-roles-in-wordpress#stepf" target="_blank" >Click here to know more</a> 
                                </p>
                            </div>
                        </h4>
                        <div class="papr-bg-cstm p-3 rounded mt-4">
                            <b><u> Note:</u> </b>In the free version of the plugin, the users would be shown a default error message
                            <b>"You are not authorized to access this, please login" </b>
                        </div>
                        <div class="d-flex mt-4">
                            <div class="papr-prem-info col-md-6 me-3">
                                <div class="papr-prem-icn" style="margin-left:20px;"><img src="<?php echo esc_url(plugin_dir_url(__FILE__)) ?>includes/images/lock.png" width="35px" />
                                    <p class="papr-prem-info-text">Available in <b>Paid</b> versions of the plugin. <a href=<?php echo esc_url(admin_url('admin.php?page=page_restriction&tab=premium_plan')); ?> class="text-warning">Click here to upgrade</a></p>
                                </div><?php papr_display_options_log(); ?>
                            </div>
                            <div class="papr-prem-info col-md-6 ">
                                <div class="papr-prem-icn papr-prem-icn-log" style="margin-left:20px;"><img src="<?php echo esc_url(plugin_dir_url(__FILE__)) ?>includes/images/lock.png" width="35px" />
                                    <p class="papr-prem-info-text">Available in <b>Paid</b> versions of the plugin. <a href=<?php echo esc_url(admin_url('admin.php?page=page_restriction&tab=premium_plan')); ?> class="text-warning">Click here to upgrade</a></p>
                                </div> <?php papr_display_options_page(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3"></div>
                <?php
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

function papr_set_active_tab($current_tab, $tab_name)
{
    if ($current_tab == $tab_name) {
        return 'nav-tab-active';
    }
    return;
}

function papr_nav_tab($current_tab)
{
?>
    <div class="wrap shadow-cstm p-3 me-0 mt-0 mo-saml-margin-left bg-white">
        <div class="row align-items-center">
            <div class="col-md-5 h3 ps-3 d-flex align-items-center">
                <img src="<?php echo esc_url(plugin_dir_url(__FILE__)) ?>includes/images/miniorange-logo.png" alt="" width="50px" class="me-2">
                <span>Page and Post Restriction</span>
            </div>
            <div class="col-md-7 d-flex align-items-center justify-content-end">
                <a id="license_upgrade" class="me-3 text-white ps-5 pe-5 pt-3 pb-3 papr-prem-btn" href="admin.php?page=page_restriction&tab=premium_plan">Premium Plans</a>
                <a class="me-3 text-white ps-5 pe-5 pt-3 pb-3 papr-prem-btn" target="_blank" href="https://plugins.miniorange.com/wordpress-page-restriction-by-user-roles/">Setup Guidelines</a>
                <a class="text-white ps-5 pe-5 pt-3 pb-3 papr-prem-btn" target="_blank" href="https://blog.miniorange.com/wordpress-page-post-restriction-addon/">Know More</a>
            </div>
        </div>
    </div>
    <div class="nav-tab-wrapper papr-bg-main">
        <a class="nav-tab papr-nav-tab ms-3 <?php echo esc_attr(papr_set_active_tab($current_tab, '')); ?>" href="admin.php?page=page_restriction"> Page Access
        </a>
        <a class="nav-tab papr-nav-tab <?php echo esc_attr(papr_set_active_tab($current_tab, 'post_access')); ?>" href="admin.php?page=page_restriction&tab=post_access"> Post Access
        </a>
        <a class="nav-tab papr-nav-tab <?php echo esc_attr(papr_set_active_tab($current_tab, 'custom_restriction')); ?>" href="admin.php?page=page_restriction&tab=custom_restriction"> Block Access
        </a>
        <a class="nav-tab papr-nav-tab <?php echo esc_attr(papr_set_active_tab($current_tab, 'tag_access')); ?>" href="admin.php?page=page_restriction&tab=tag_access"> Tag Access
        </a>
        <a class="nav-tab papr-nav-tab <?php echo esc_attr(papr_set_active_tab($current_tab, 'category_access')); ?>" href="admin.php?page=page_restriction&tab=category_access"> Category Access
        </a>
        <a class="nav-tab papr-nav-tab <?php echo esc_attr(papr_set_active_tab($current_tab, 'custom_role')); ?>" href="admin.php?page=papr_custom_roles_sub_menu"> Roles and Capabilities
        </a>
        <a class="nav-tab papr-nav-tab <?php echo esc_attr(papr_set_active_tab($current_tab, 'account_setup')); ?>" href="admin.php?page=page_restriction&tab=account_setup"> Account Setup
        </a>
        <a class="nav-tab papr-nav-tab <?php echo esc_attr(papr_set_active_tab($current_tab, 'premium_plan')); ?>" href="admin.php?page=page_restriction&tab=premium_plan"> Licensing Plans
        </a>
    </div>
<?php
}

function papr_custom_restrict()
{
?>
    <div class="rounded bg-white papr-shadow p-4 mt-4 ms-4">
        <h4 class="papr-form-head">Give Access to Blocks in a page to only Logged in Users</h4>
        <div class="papr-bg-cstm p-3 rounded mt-4">
            <h5>Use the shortcode [restrict_content] [/restrict_content] to restrict any content between it.</h5>
            <p class="mt-3">
                If you want to <b>allow only logged in users to view specific content on a page</b>, then you can use the shortcode [restrict_content] to do so.<br>
                Content between the opening tag [restrict_content] and closing tag [restrict_content] will not be visible to users who are not logged in. Users can view such content only after the user is logged in.
            </p>
        </div>
        <br>

        <div class="row">
            <div class="col-md-6">
                <h6>Page Content</h6>
                <textarea id="papr-block-textarea" placeholder="Enter your content here to see how the shortcode would work.."
                          class="papr-editor-block"><?php echo "Hi there, sentence 1 is not restricted. \r\n[restrict_content] Hi, this sentence is restricted. [/restrict_content] \r\nLast sentence is also not restricted." ?>
                </textarea>
            </div>
            <div class="col-md-6">
                <h6>Content shown to a user who is not logged-in</h6>
                <textarea id="papr-block-preview" class="papr-editor-block" readonly><?php echo "Hi there, sentence 1 is not restricted. Last sentence is also not restricted." ?>
                </textarea>
            </div>
        </div>

            <div style="text-align: center"><button class="papr-btn-cstm rounded mt-3" onclick="papr_block_preview()">Preview</button></div>
            <br>
        <div class="papr-prem-info">
            <div class="papr-prem-icn" style="margin-left:31em;"><img src="<?php echo esc_url(plugin_dir_url(__FILE__)) ?>includes/images/lock.png" width="35px" />
                <p class="papr-prem-info-text">Available in <b>Paid</b> versions of the plugin. <a href="<?php echo esc_url(admin_url('admin.php?page=page_restriction&tab=premium_plan')); ?>">Click here to upgrade</a></p>
            </div>
            <div class="row">
                <div class="col-md-4"><h6>Enter Custom Error message: </h6></div>
                <div class="col-md-8"><input style="width:90%" type="text" placeholder="Eg: You are not allowed to view this content!" disabled></div>
            </div>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-8"><p><b>Note:</b> This error message would be shown to logged out users in place of the restricted content.</p></div>
            </div>
        </div>
    </div>

    <script>
        function papr_block_preview() {
            let text = document.getElementById("papr-block-textarea").value;

            if(text.includes("[restrict_content]") && text.includes("[/restrict_content]")) {

                const start_shortcode = text.indexOf('[restrict_content]');
                const end_shortcode = text.indexOf('[/restrict_content]') + 19;

                const before_restriction = text.substring(0, start_shortcode);
                const after_restriction = text.substring(end_shortcode);
                document.getElementById("papr-block-preview").innerText = before_restriction + after_restriction;
            }
            else {
                document.getElementById("papr-block-preview").innerText = text;
            }

        }
    </script>
<?php
}

function papr_support_page_restriction()
{
    $admin_email = get_option("papr_admin_email");
    $admin_email = $admin_email!=""? $admin_email: "";
    
    ?>
    <div class="rounded bg-white papr-shadow p-4 ms-3 mb-4 me-3 mt-4">
        <div>
            <h4>Support/Contact Us</h4>
            <hr>
            <p>Need any help? We can help you with configuring your Page Restriction Plugin. Just send us a query and we will get back to you soon.</p>
            <form method="post" action="">
                <?php wp_nonce_field("papr_contact_us_query_option"); ?>
                <input type="hidden" name="option" value="papr_contact_us_query_option">
                <table style="width:100%">
                    <tr>
                        <td>
                            <input class="w-100" type="email" required name="papr_contact_us_email" value="<?php echo esc_attr($admin_email) ?>" placeholder="Enter your email">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="tel" class="w-100 mt-4" id="contact_us_phone" pattern="[\+]\d{11,14}|[\+]\d{1,4}[\s]\d{9,10}" name="papr_contact_us_phone" value="<?php echo esc_attr(get_option('papr_admin_phone')); ?>" placeholder="Enter your phone">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <textarea class="w-100 mt-4" placeholder="Write your query here" onkeypress="papr_valid_query(this)" onkeyup="papr_valid_query(this)" onblur="papr_valid_query(this)" required name="papr_contact_us_query" rows="4" style="resize: vertical;"></textarea>
                        </td>
                    </tr>
                </table>
                <div style="text-align:center;">
                    <input type="submit" name="submit" class="papr-btn-cstm rounded mt-3" />
                </div>
            </form>
            <br>
        </div>
    </div>
    <script>
        jQuery("#contact_us_phone").intlTelInput();
        jQuery("#phone_contact").intlTelInput();

        function papr_valid_query(f) {
            !(/^[a-zA-Z?,.\(\)\/@ 0-9]*$/).test(f.value) ? f.value = f.value.replace(
                /[^a-zA-Z?,.\(\)\/@ 0-9]/, '') : null;
        }
    </script>
<?php
}

function papr_show_customer_page()
{
    if (papr_is_customer_registered()) {
        papr_show_customer_details();
    } else {
        if (get_option('papr_verify_customer') == 'true') {
            papr_show_verify_password_page();
        } else {
            papr_show_new_registration_page();
        }
    }
}

//Display options for the customer to choose from when user is restricted
function papr_display_options_page()
{
?>
    <h4 class="papr-form-head">Restrict Options before Login
       
    </h4>
    <div class="pt-3">
        <table>
            <tr style="margin-bottom:7%;">
                <input style="cursor: not-allowed;" type="radio" name="mo_display" disabled>
                <b>Redirect to Login Page</b>
                <br><b>Note </b>: Enabling this option will <i><b>Redirect</b></i> the restricted users to WP Login Page. </p>
            </tr>
            <tr>
                <td width="60%" style="padding-bottom:6px;">
                    <input style="cursor: not-allowed;" type="radio" name="mo_display" value="mo_display_page" disabled>
                    <b>Redirect to Page Link</b>
                </td>
            </tr>
            <tr>
                <td colsapn="2">
                    <input type="url" style="width:90%;cursor: not-allowed;" name="mo_display_page_url" id="mo_page_url" disabled placeholder="Enter URL of the page">
                <td>
                    <button style="cursor: not-allowed; width: 180px;" class="btn papr-btn-cstm rounded" disabled value = "" placeholder = " Enter URL of the page">
                        Save & Test URL 
                    </button>
                </td>
                </td>
            </tr>
            <tr class="mt-3">
                <td colspan="3">
                    <p class="papr-bg-cstm p-3 h6 mt-3"><b>Note </b>: Enabling this option will <i><b>Redirect</b></i> the restricted users to the given page URL.<br>
                        <font color="#8b008b">Please provide a valid URL and make sure that the given page is not Restricted</font>
                    </p>
                </td>
            </tr>

            <tr>
                <td style="padding-bottom:6px;" width="70%">
                    <input style="cursor: not-allowed;" type="radio" name="mo_display" value="mo_display_message" disabled>
                    <b>Message on display</b><br>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" style="width:90%;cursor:not-allowed;" name="mo_display_message_text" disabled="" value="" placeholder="You are not authorized to access this, please login." />
                <td>
                    <button style="cursor: not-allowed;" class="btn papr-btn-cstm rounded" disabled> Save & Preview</button>
                </td>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <b>Note </b>: Enabling this option will display the configured message to the restricted users.
                </td>
            </tr>
            <tr style="margin-bottom:7%;">
                <input style="cursor: not-allowed;" type="radio" name="mo_display" disabled>
                <b>Single Sign On </b>
                <br>
                <b>Note </b>: Enabling this option will <i><b>Redirect</b></i> the restricted users to IDP login page.</p>

            </tr>
        </table>
        <br><br>
    </div>

<?php
}

function papr_display_options_log()
{
?>

    <h4 class="papr-form-head">Restrict Options after Login</h4>
    <div class="pt-3">
        <input type="hidden" style="cursor: not-allowed;" name="option" value="mo_display_option_login_log">
        <table>
            <tr>
                <td colspan="2" style="padding-bottom:6px">
                    <input style="cursor: not-allowed;" type="radio" name="mo_display" value="mo_display_page_log" disabled>
                    <b>Redirect to Page Link</b>
                </td>
            </tr>
            <tr>
                <td width="80%" colspan="2">
                    <input type="url" name="mo_display_page_url_log" style="width:90%;cursor:not-allowed;" id="mo_page_url" disabled placeholder="Enter URL of the page" />
                <td><b>
                        <button style="cursor: not-allowed;width:180px;" class="btn papr-btn-cstm rounded" disabled>Save & Test URL</button>
                </td>
                </td>
            </tr>

            <tr>
                <td colspan="3">
                    <br><b>Note </b>: Enabling this option will <i><b>Redirect</b></i> the restricted users to the given page URL.
                    <br>
                    <font color="#8b008b">Please provide a valid URL and make sure that the given page is not Restricted</font>
                </td>
            </tr>
            <tr>
                <td>
                    <br>
                </td>
            </tr>
            <tr>
                <td style="padding-bottom:6px">
                    <input style="cursor: not-allowed;" type="radio" name="mo_display" value="mo_display_message_log" disabled>
                    <b>Message on display</b>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="text" name="mo_display_message_text_log" style="width:90%; cursor:not-allowed;" disabled value="" placeholder="You are not authorized to access this, please login.">
                <td>
                    <button style="cursor: not-allowed;" class="btn papr-btn-cstm rounded" disabled>Save & Preview</button>
                </td>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <br>
                    <b>Note </b>: Enabling this option will display the configured message to the restricted users.
                </td>
            </tr>
        </table>
        <br><br>
    </div>

<?php
}

function papr_is_customer_registered() {

    $email       = get_option('papr_admin_email');
    $customerKey = get_option('papr_admin_customer_key');
    if (!$email || !$customerKey || !is_numeric(trim($customerKey)))
        return 0;
    return 1;
}

function papr_show_customer_details()
{
?>
    <div class="rounded bg-white papr-shadow p-4 ms-4 mt-4">
        <h2>Thank you for registering with miniOrange.</h2>
        <div style="padding: 10px;">
            <table border="1" style="background-color:#FFFFFF; border:1px solid #CCCCCC; border-collapse: collapse; margin-bottom:15px; width:85%">
                <tr>
                    <td style="width:45%; padding: 10px;">miniOrange Account Email</td>
                    <td style="width:55%; padding: 10px;"><?php echo esc_attr(get_option('papr_admin_email')); ?></td>
                </tr>
                <tr>
                    <td style="width:45%; padding: 10px;">Customer ID</td>
                    <td style="width:55%; padding: 10px;"><?php echo esc_attr(get_option('papr_admin_customer_key')) ?></td>
                </tr>
            </table>

            <table>
                <tr>
                    <td>
                        <form name="f1" method="post" action="" id="papr_goto_login_form">
                            <?php wp_nonce_field("papr_change_miniorange"); ?>
                            <input type="hidden" value="papr_change_miniorange" name="option" />
                            <input type="submit" value="Change Email Address" class="btn papr-btn-cstm rounded" />
                        </form>
                    </td>
                    <td>
                        <a href="<?php echo esc_url(admin_url("admin.php?page=page_restriction&tab=premium_plan")); ?>"><input type="button" class="btn papr-btn-cstm rounded" value="Check Premium Plans" /></a>
                    </td>
                </tr>
            </table>
        </div>
        <br>
    </div>

<?php
}

function papr_show_new_registration_page()
{
    update_option('papr_new_registration', 'true');
?>
    <div class="rounded bg-white papr-shadow p-4 ms-4 mt-4">
        <form name="f" method="post" action="">
            <?php wp_nonce_field("papr_register_customer"); ?>
            <input type="hidden" name="option" value="papr_register_customer">
            <h4 class="papr-form-head">Register with miniOrange</h4>

            <div id="help_register_desc" class="papr-bg-cstm p-3 rounded mt-4 text-center">
                <h5>Why should I register?</h5>
                <p class="papr-p-text">You should register so that in case you need help, we can help you with step by step instructions. <b>You will also need a miniOrange account to upgrade to the premium version of the plugin.</b> We do not store any information except the email that you will use to register with us.</p>
            </div>
            <div class="row mt-4">
                <div class="col-md-3">
                    <h6>Email :</h6>
                </div>
                <div class="col-md-6">
                    <input class="mo_saml_table_textbox" style="width:100%" type="email" name="email" required placeholder="person@example.com" value="<?php echo (get_option('papr_admin_email') == '') ? esc_attr(get_option('admin_email')) : esc_attr(get_option('papr_admin_email')); ?>" />
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-3">
                    <h6>Password :</h6>
                </div>
                <div class="col-md-6">
                    <input class="mo_saml_table_textbox" style="width:100%" required type="password" name="password" placeholder="Choose your password (Min. length 6)" minlength="6" pattern="^[(\w)*(!@#$.%^&*-_)*]+$" title="Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*) should be present." />
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-3">
                    <h6>Confirm Password :</h6>
                </div>
                <div class="col-md-6">
                    <input class="mo_saml_table_textbox" style="width:100%" required type="password" name="confirmPassword" placeholder="Confirm your password" minlength="6" pattern="^[(\w)*(!@#$.%^&*-_)*]+$" title="Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*) should be present.">
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-3"></div>
                <div class="col-md-9">
                    <input type="submit" name="submit" value="Register" class="btn papr-btn-cstm rounded" />
                    <input type="button" name="papr_goto_login" id="papr_goto_login" value="Already have an account?" class="btn papr-btn-cstm rounded" />
                </div>
            </div>
        </form>
    </div>
    <form name="f1" method="post" action="" id="papr_goto_login_form">
        <?php wp_nonce_field("papr_goto_login"); ?>
        <input type="hidden" name="option" value="papr_goto_login" />
    </form>

    <script>
        jQuery('#papr_goto_login').click(function() {
            jQuery('#papr_goto_login_form').submit();
        });
    </script>
<?php
}

function papr_show_verify_password_page()
{
?>
    <form name="f" method="post" action="">
        <?php wp_nonce_field("papr_verify_customer"); ?>
        <input type="hidden" name="option" value="papr_verify_customer">
        <div class="rounded bg-white papr-shadow p-4 mt-4 ms-4">
            <h4 class="papr-form-head">Login with miniOrange</h4>

            <div class="papr-bg-cstm p-3 rounded mt-4 text-center">
                <p class="papr-p-text">
                    <b>It seems you already have an account with miniOrange. Please enter your miniOrange email and password.
                        <br><a target="_blank" href="https://login.xecurify.com/moas/idp/resetpassword">Click here if you forgot your password?</a>
                    </b>
                </p>
            </div>
            <div class="row mt-4">
                <div class="col-md-3">
                    <h6>Email :</h6>
                </div>
                <div class="col-md-6">
                    <input class="mo_saml_table_textbox w-75" type="email" name="email" required placeholder="person@example.com" value="<?php echo esc_attr(get_option('papr_admin_email')); ?>" />
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-3">
                    <h6>Password :</h6>
                </div>
                <div class="col-md-6">
                    <input class="mo_saml_table_textbox w-75" required type="password" name="password" placeholder="Enter your password" minlength="6" pattern="^[(\w)*(!@#$.%^&*-_)*]+$" title="Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*) should be present.">
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-3"></div>
                <div class="col-md-9">
                    <input type="submit" name="submit" value="Login" class="btn papr-btn-cstm rounded" />
                    <input type="button" name="papr_goback" id="papr_goback" value="Sign Up" class="btn papr-btn-cstm rounded" />
                </div>
            </div>

        </div>
    </form>

    <form name="f" method="post" action="" id="papr_goback_form">
        <?php wp_nonce_field("papr_go_back") ?>
        <input type="hidden" name="option" value="papr_go_back" />
    </form>
    <form name="f" method="post" action="" id="papr_forgotpassword_form">
        <?php wp_nonce_field("papr_forgot_password_form_option"); ?>
        <input type="hidden" name="option" value="papr_forgot_password_form_option" />
    </form>
    <script>
        jQuery('#papr_goback').click(function() {
            jQuery('#papr_goback_form').submit();
        });
        jQuery("a[href=\"#papr_forgot_password_link\"]").click(function() {
            jQuery('#papr_forgotpassword_form').submit();
        });
    </script>
<?php
}

function papr_in_array($var,$arr){
    return is_array($arr) && in_array($var,$arr);
}

?>