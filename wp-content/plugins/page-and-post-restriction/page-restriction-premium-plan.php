<?php

require_once 'page-restriction-menu-settings.php';

function papr_show_premium_plans()
{
?>
    <div class="bg-white mt-4 ms-4 ps-0 mb-4 ps-3 rounded">
        <h2 class="text-center pt-4">Page and Post Restriction</h2>
        <h5 class="text-danger text-center">You are currently on the Free version of the plugin</h5>

        <input type="hidden" value="<?php echo esc_attr(papr_is_customer_registered()); ?>" id="papr_customer_registered">

        <div class="me-3">
            <table class="w-100 text-center mt-4 papr-license-plan">
                <tr class="papr-lic-head rounded">
                    <th class="h2 p-3">Choose Your Plan</th>
                    <th class="h2 p-3">Premium</th>
                    <th class="h2 p-3">Enterprise</th>
                </tr>
                <tr class="bg-white">
                    <td class="p-2"></td>
                    <td class="p-2 h2 papr-price">$149 <sup>*</sup></td>
                    <td class="p-2 h2 papr-price">$249 <sup>*</sup></td>
                </tr>
                <tr>
                    <td class="p-2">Restrict specific Pages/Posts</td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                </tr>
                <tr>
                    <td class="p-2">Restrict Complete Site</td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                </tr>
                <tr>
                    <td class="p-2">Restrict based on User's Login Status</td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                </tr>
                <tr>
                    <td class="p-2">Number of Restricted Pages/Posts</td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                </tr>
                <tr>
                    <td class="p-2">Error Message to Restricted User</td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                </tr>
                <tr>
                    <td class="p-2">Restrict Pages/Posts while Creating (Meta Box)</td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                </tr>
                <tr>
                    <td class="p-2">Restrict Category Page</td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                </tr>
                <tr>
                    <td class="p-2">Restrict posts of a particular Category</td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                </tr>
                <tr>
                    <td class="p-2">Restrict Category based on User Roles</td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                </tr>
                <tr>
                    <td class="p-2">Redirect Restricted User to a URL</td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                </tr>
                <tr>
                    <td class="p-2">Redirect Restricted User to WordPress Login Page</td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                </tr>
                <tr>
                    <td class="p-2">Restrict Entire Custom Post type</td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-x text-danger" viewBox="0 0 14 14">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                </tr>
                <tr>
                    <td class="p-2">Integration with SAML/OAuth SSO</td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-x text-danger" viewBox="0 0 14 14">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </td>
                    <td class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="align-middle text-success" viewBox="0 0 14 14">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </td>
                </tr>
                <tr>
                    <td class="p-2 pt-3"> <a href="https://www.miniorange.com/contact" class="btn papr-btn-cstm">Contact Us</a> </td>
                    <td class="p-2 pt-3"> <a onclick="papr_select_plan('wp_page_restriction_premium_plan')" class="btn papr-btn-cstm">Purchase Now</a> </td>
                    <td class="p-2 pt-3"> <a onclick="papr_select_plan('wp_page_restriction_enterprise_plan')" class="btn papr-btn-cstm">Purchase Now</a> </td>
                </tr>

            </table>
        </div>
    </div>
    <div class="bg-white mt-4 ms-4 ps-0 mb-4 ps-3 rounded">
        <h3 class="text-center pt-4">Customer Policy</h3>
        </br>
        <div class="ms-3">
            <p><b>Cost applicable for one instance only.</b></p>
        </div>
        <br>
        <div class="ms-3">
            <h6><u>Steps to Upgrade to Paid Plugin</u></h6>
            <ol style="font-size:13px;">
                <li>
                    Click on 'Purchase now' button of the required Licensing Plan. You will be redirected to Account Setup tab of the plugin.
                        Login or Signup with miniOrange to move forward with the purchase.
                </li>
                <li>
                    Once you are logged in, you can click on the 'Purchase Now' button again in the Licensing Plans tab of the plugin.
                        You will be redirected to miniOrange Login Console, where you need to enter your password for the miniOrange account.
                </li>
                <li>
                    You will be then redirected to the payment page. Enter you card details and complete the payment.
                        On successful payment completion, you will see the link to download the plugin.
                </li>
                <li>
                    Now navigate to 'Plugins > Installed Plugins' on the WordPress Dashboard. Click on "Add New" and upload the downloaded paid plugin zip.
                </li>
                <li>
                    From this point on, please do not update the paid plugin from the WordPress marketplace.
                </li>
            </ol>
        </div>
        <br>
        <div class="ms-3">
            <h6><u>10 Days Return Policy</u></h6>
            <p>At miniOrange, we want to ensure you are 100% happy with your purchase. If the paid plugin you purchased is not working as
                    advertised and you've attempted to resolve any issues with our support team which couldn't get resolved, we will refund the whole
                amount within 10 days of the purchase. <b>Please email us at samlsupport@xecurify.com for any queries</b> regarding the return policy.</p>
        </div>
        </br>
    </div>
    <form style="display:none;" id="paprloginform" action="https://login.xecurify.com/moas/login" target="_blank" method="post">
        <input type="email" name="username" value="<?php echo esc_attr(get_option('papr_admin_email')); ?>" />
        <input type="text" name="redirectUrl" value="https://login.xecurify.com/moas/initializepayment" />
        <input type="text" name="requestOrigin" id="requestOrigin" />
    </form>
    <a id="paprbacktoaccountsetup" style="display:none;" href="<?php echo esc_attr(papr_add_query_arg(htmlentities($_SERVER['REQUEST_URI']))); ?>"></a>
    <script>
        function papr_select_plan(planType) {
            jQuery('#requestOrigin').val(planType);
            if (jQuery('#papr_customer_registered').val() == 1) {
                jQuery('#paprloginform').submit();
            }
            else {
                location.href = jQuery('#paprbacktoaccountsetup').attr('href');
            }
        }
    </script>
<?php
}
?>