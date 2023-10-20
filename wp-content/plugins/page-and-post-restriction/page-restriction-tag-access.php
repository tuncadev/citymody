<?php

require_once 'page-restriction-menu-settings.php';
require_once 'page-restriction-page-access.php';

function papr_tag_access()
{
    $results_per_page = get_option('papr_tag_per_page');

	$results_per_page = $results_per_page != '' ? $results_per_page : 10;
    
    ?>
	<div class="rounded bg-white papr-shadow p-4 mt-4 ms-4">
		<div>
			<h4 class="papr-form-head">Give Access to Tags based on Roles and Login Status</h4>
            <div class="papr-prem-info">
                <div class="papr-prem-icn papr-prem-cat-icn"><img src="https://img.icons8.com/color/48/000000/lock--v2.png" width="35px">
                    <p class="papr-prem-info-text">Available in <b>Paid</b> versions of the plugin. <a href="<?php echo esc_url(admin_url('admin.php?page=page_restriction&tab=premium_plan')) ?>" class="text-warning">Click here to upgrade</a></p>
                </div>
                <h5 class="papr-form-head papr-form-head-bar mt-2 mb-4">Tag Restrictions
                    <div class="papr-info-global ms-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                        </svg>
                        <p class="papr-info-text-global">
                            Specify which tags would be <b>accessible to only Logged In users</b> OR which <b>user roles should be able to access</b> the tag in the table below.
                        </p>
                    </div>
                </h5>
                <div class="mb-4"> <b>Note:</b> All the posts under a restricted tag would also be restricted.</div>

			    <?php papr_dropdown($results_per_page, 'tag'); ?>

                <div class="tablenav top mt-3">
                    <input type="submit" class="btn papr-btn-cstm rounded" value="Save Configuration" disabled>
                    <?php
                    $tags = get_tags(array("hide_empty" => 0));
                    $total_tag = count($tags);
                    $number_of_pages_in_pagination = ceil($total_tag / $results_per_page);

                    $current_page = papr_get_current_page($number_of_pages_in_pagination);

                    $offset = ($results_per_page * $current_page) - $results_per_page;
                    $tags = get_tags(array(
                        "hide_empty" => 0,
                        "type"  	=> "post",
                        "orderby"   => "name",
                        "order" 	=> "ASC",
                        'number' => $results_per_page,
                        'offset' => $offset
                    ));

                    $link = 'admin.php?page=page_restriction&tab=tag_access&curr=';
                    papr_pagination_button($number_of_pages_in_pagination, $total_tag, $current_page, $link, 'top');
                    ?>
                </div>

                <table id="reports_table" class="wp-list-table widefat fixed striped table-view-list pages">
                    <thead><?php papr_display_head_foot_of_table('tag'); ?><thead>
                        <tbody class="w-100">
                            <?php
                            foreach ($tags as $tag) {
                                papr_tag_display_pages($tag);
                            } ?>
                        </tbody>
                    <tfoot>
                        <?php papr_display_head_foot_of_table('tag'); ?>
                    </tfoot>
                </table>
                <div class="tablenav bottom mt-4">
                    <input type="submit" class="btn papr-btn-cstm rounded" value="Save Configuration" form="blockedpagesform" disabled>
                    <?php papr_pagination_button($number_of_pages_in_pagination, $total_tag, $current_page, $link, 'bottom'); ?>
                </div>
            </div>
		</div>
	</div>
    <script>
        var tag_selector_up = document.getElementById("current-page-selector");
        var tag_selector_down = document.getElementById("current-page-selector-1");
        var link = 'admin.php?page=page_restriction&tab=tag_access&curr=';

        tag_selector_up.addEventListener("keyup", function(event) {
            if (event.keyCode === 13) {
                tag_selector_up_value = document.getElementById("current-page-selector").value;
                var page_link = link.concat(tag_selector_up_value);
                window.open(page_link, "_self");
            }
        });

        tag_selector_down.addEventListener("keyup", function(event) {
            if (event.keyCode === 13) {
                tag_selector_down_value = document.getElementById("current-page-selector-1").value;
                var page_link = link.concat(tag_selector_down_value);
                window.open(page_link, "_self");
            }
        });
    </script>
<?php
}

function papr_tag_display_pages($tag) {
?>
	<tr id="<?php echo esc_attr($tag->term_id) ?>">
		<td>
			<a href="<?php echo esc_url(get_tag_link($tag)) ?>" target="_blank">
                <?php echo esc_html($tag->name) ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
					<path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5z"></path>
					<path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0v-5z"></path>
				</svg>
            </a>
		</td>
        <td>
            <input class="w-75" type="text" name="mo_tag_roles" id="mo_tag_roles" placeholder="Enter (;) separated Roles" autocomplete="off" disabled>
		</td>
        <th scope="row" class="check-column">
			<label class="screen-reader-text" for="cb-select-3"></label>
			<input style="margin-left: 105px;" id="cb-select-3" name="mo_tag_login" class="log_check" type="checkbox" disabled>
        </th>
    </tr>
    <?php
}
?>