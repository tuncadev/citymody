<?php
	$posible_values = array('all'=>esc_html__('All', 'ihc'), 'reg'=>esc_html__('Registered Users', 'ihc'), 'unreg'=>esc_html__('Unregistered Users', 'ihc') );

	$levels = \Indeed\Ihc\Db\Memberships::getAll();
	if($levels){
		foreach($levels as $id=>$level){
			$posible_values[$id] = $level['name'];
		}
	}
	$pages = ihc_get_all_pages();//getting pages
$subtab = isset( $_GET['subtab'] ) ? sanitize_text_field($_GET['subtab']) : 'post_types';
?>
<div class="ihc-subtab-menu">
	<a class="ihc-subtab-menu-item <?php echo ( $subtab === 'post_types' ) ? 'ihc-subtab-selected' : '';?>" href="<?php echo esc_url($url.'&tab='.$tab.'&subtab=post_types');?>"><?php esc_html_e('All Posts', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ($subtab === 'cats') ? 'ihc-subtab-selected' : '';?>" href="<?php echo esc_url($url.'&tab='.$tab.'&subtab=cats');?>"><?php esc_html_e('All Posts based on Categories', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ($subtab === 'files') ? 'ihc-subtab-selected' : '';?>" href="<?php echo esc_url($url.'&tab='.$tab.'&subtab=files');?>"><?php esc_html_e('Specific Files', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ($subtab === 'entire_url') ? 'ihc-subtab-selected' : '';?>" href="<?php echo esc_url($url.'&tab='.$tab.'&subtab=entire_url');?>"><?php esc_html_e('Specific URL', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ($subtab === 'keyword') ? 'ihc-subtab-selected' : '';?>" href="<?php echo esc_url($url.'&tab='.$tab.'&subtab=keyword');?>"><?php esc_html_e('All URLs (based on Keywords)', 'ihc');?></a>
	<div class="ihc-clear"></div>
</div>

<?php
	echo ihc_inside_dashboard_error_license();
	echo ihc_check_default_pages_set();//set default pages message
	echo ihc_check_payment_gateways();
	echo ihc_is_curl_enable();
	do_action( "ihc_admin_dashboard_after_top_menu" );
?>
<div class="iump-wrapper">
<div class="iump-page-title">Ultimate Membership Pro -
							<span class="second-text">
								<?php esc_html_e('Access Rules', 'ihc');?>
							</span>
</div>
<form method="post"  id="block_url_form">

	<input type="hidden" name="ihc_admin_block_url_nonce" value="<?php echo wp_create_nonce( 'ihc_admin_block_url_nonce' );?>" />

	<?php
		$subtab = isset($_REQUEST['subtab']) ? sanitize_text_field($_REQUEST['subtab']) : 'post_types';
		switch ($subtab):
			case 'entire_url':
				ihc_save_block_urls();//save/update block url
				ihc_delete_block_urls();//delete block url
			?>
			<div class="ihc-stuffbox">
				<h3><?php esc_html_e('Add new Restriction', 'ihc');?></h3>
				<div class="inside">
						<div class="iump-form-line">
							<h2><?php esc_html_e('Restrict Based on URL Path', 'ihc');?></h2>
							<p><?php esc_html_e('You may restrict any URL running through your WordPress website even if not about a static Post or Page.', 'ihc');?></p>
						</div>
						<div class="iump-form-line">
							<div class="row">
                	<div class="col-xs-8">
                             <div class="input-group">
                                <span class="input-group-addon"><?php esc_html_e('Full URL', 'ihc');?></span>
                                <input class="ihc-block-url-full-url form-control" type="text"  value="" name="ihc_block_url_entire-url" placeholder="<?php esc_html_e('copy the entire Link from your browser', 'ihc');?>">
                             </div>
                     </div>
                 </div>
						</div>

						<div class="iump-form-line iump-special-line">

							<div class="iump-form-line">
								<?php
									$type_values = array(
															'show' =>esc_html__('Show Only', 'ihc'),
															'block' =>esc_html__('Block Only', 'ihc')

									);

								?>
								<h4><?php esc_html_e('Restriction type:', 'ihc'); ?></h4>
								<select name="block_or_show">
									<?php foreach ($type_values as $k=>$v):?>
										<option value="<?php echo esc_attr($k);?>"><?php echo esc_html($v);?></option>
									<?php endforeach;?>
								</select>
							</div>

							<div class="iump-form-line">
								<h4><?php esc_html_e('Target Members:', 'ihc');?></h4>
								<select id="ihc-change-target-user-set" onChange="ihcWriteTagValue(this, '#ihc_block_url_entire-target_users', '#ihc_tags_field1', 'ihc_select_tag_' );" class="ihc-block-url-select">
									<option value="-1" selected>...</option>
									<?php
										foreach($posible_values as $k=>$v){
										?>
											<option value="<?php echo esc_attr($k);?>"><?php echo esc_html($v);?></option>
										<?php
										}
									?>
								</select>
								<input type="hidden" value="" name="ihc_block_url_entire-target_users" id="ihc_block_url_entire-target_users" />
								<div id="ihc_tags_field1"></div>
							</div>
						</div>

						<div class="iump-form-line">
							<h4><?php esc_html_e('Redirect after', 'ihc');?></h4>
							<p><?php esc_html_e('If access is restrict choose where members will be redirected. If no specific option is selected Default Redirect Page will be used.', 'ihc');?></p>
							<select name="ihc_block_url_entire-redirect">
								<option value="-1" selected >...</option>
								<?php
									$pages = $pages + ihc_get_redirect_links_as_arr_for_select();
									if ($pages){
										foreach ($pages as $k=>$v){
											?>
												<option value="<?php echo esc_attr($k);?>" ><?php echo esc_html($v);?></option>
											<?php
										}
									}
								?>
							</select>
						</div>

					<input type="hidden" value="" name="delete_block_url" id="delete_block_url" />

					<div class="ihc-wrapp-submit-bttn">
						<input type="submit" value="<?php esc_html_e('Add New Rule', 'ihc');?>" name="ihc_save_block_url" class="button button-primary button-large ihc_submit_bttn" />
					</div>
				</div>
			</div>
			<?php
				$data = get_option('ihc_block_url_entire');
				if ($data && count($data)){
					?>
					<div class="ihc-dashboard-form-wrap">
					<table class="wp-list-table widefat fixed tags ihc-admin-tables ihc-block-url-table">
						<thead>
						<tr>
							<th class="manage-column"><?php esc_html_e('Target URL', 'ihc');?></th>
							<th class="manage-column"><?php esc_html_e('Type', 'ihc');?></th>
							<th class="manage-column"><?php esc_html_e('Target Users', 'ihc');?></th>
							<th class="manage-column"><?php esc_html_e('Redirect To', 'ihc');?></th>
							<th class="manage-column ihc-small-status-col"><?php esc_html_e('Delete', 'ihc');?></th>
						</tr>
						</thead>
					<?php
						$i = 1;
						foreach ($data as $key=>$arr){
						?>
						<tr class="<?php if ($i%2==0){
							 echo 'alternate';
						}
						?>">
							<td><?php echo esc_url($arr['url']);?></td>
							<td><?php
								$print_type = isset($arr['block_or_show']) ? $arr['block_or_show'] : 'block';
								echo ucfirst($print_type);
							?></td>
							<td>
								<?php
									if ($arr['target_users']){
										$levels = explode(',', $arr['target_users']);
										if ($levels && count($levels)){
											$extra_class = ($print_type=='block') ? 'ihc-expired-level' : '';
											foreach ($levels as $val){
												$print_type_user = '';
												if ($val!='reg' && $val!='unreg' && $val!='all'){
													$temp_data = ihc_get_level_by_id($val);
													if (!empty($temp_data['name'])){
														$print_type_user = $temp_data['name'];
													}
												} else {
													$print_type_user = $val;
												}
												if (empty($print_type_user)){
													$print_type_user =esc_html__('Deleted Level', 'ihc');
												}
												?>
												<div class="level-type-list <?php echo esc_attr($extra_class);?>"><?php echo esc_html($print_type_user);?></div>
												<?php
											}
										}
									}
								?>
							</td>
							<td class="ihc-block-url-redirect">
								<?php
									if ($arr['redirect']!=-1){
										$redirect_link = ihc_get_redirect_link_by_label($arr['redirect']);
										if ($redirect_link){
											echo esc_url($redirect_link);
										} else {
											echo get_the_title($arr['redirect']);
										}
									} else {
										echo '-';
									}
								?>
							</td>
							<td align="center">
								<i class="fa-ihc ihc-icon-remove-e ihc-js-admin-block-url-delete-block-url" data-key="<?php echo esc_attr($key);?>" ></i>
							</td>
						</tr>
						<?php
						$i++;
						}
						?>
					</table>
					</div>
					<?php
				}
			break;
		case 'keyword':
				ihc_save_block_urls();//save/update block url
				ihc_delete_block_urls();//delete block url
			?>
				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Add new Restriction', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line">
							<h2><?php esc_html_e('Restrict any URL based on Keywords', 'ihc');?></h2>
							<p><?php esc_html_e('You may restrict multiple URLs running through your WordPress website based on specific keyword found inside the Link. Avoid to common keywords that may restrict more links and is expected.', 'ihc');?></p>
						</div>
						<div class="iump-form-line">
							<div class="row">
									<div class="col-xs-4">
														 <div class="input-group">
																<span class="input-group-addon"><?php esc_html_e('Keyword', 'ihc');?></span>
																<input class="form-control" type="text" value="" name="ihc_block_url_word-url">
														 </div>
										 </div>
								 </div>
						</div>


							<div class="iump-form-line iump-special-line">
								<div class="iump-form-line">
									<?php
										$type_values = array(
																'show' =>esc_html__('Show Only', 'ihc'),
																'block' =>esc_html__('Block Only', 'ihc')

										);


									?>
									<h4><?php esc_html_e('Restriction type:', 'ihc');?></h4>
									<select name="block_or_show">
										<?php foreach ($type_values as $k=>$v):?>
											<option value="<?php echo esc_attr($k);?>"><?php echo esc_html($v);?></option>
										<?php endforeach;?>
									</select>
								</div>

								<div class="iump-form-line">
									<h4><?php esc_html_e('Target Members:', 'ihc');?></h4>
									<select id="ihc-change-target-user-set-regex" onChange="ihcWriteTagValue(this, '#ihc_block_url_word-target_users', '#ihc_tags_field2', 'ihc_select_tag_regex_' );" class="ihc-block-url-select">
										<option value="-1" selected>...</option>
										<?php
											foreach($posible_values as $k=>$v){
											?>
												<option value="<?php echo esc_attr($k);?>"><?php echo esc_html($v);?></option>
											<?php
											}
										?>
									</select>
									<input type="hidden" value="" name="ihc_block_url_word-target_users" id="ihc_block_url_word-target_users" />
									<div id="ihc_tags_field2"></div>
								</div>
							</div>

							<div class="iump-form-line">
								<h4><?php esc_html_e('Redirect after', 'ihc');?></h4>
								<p><?php esc_html_e('If access is restrict choose where members will be redirected. If no specific option is selected Default Redirect Page will be used.', 'ihc');?></p>
								<select name="ihc_block_url_word-redirect">
									<option value="-1" selected >...</option>
									<?php
										$pages = $pages + ihc_get_redirect_links_as_arr_for_select();
										if ($pages){
											foreach($pages as $k=>$v){
												?>
													<option value="<?php echo esc_attr($k);?>"><?php echo esc_html($v);?></option>
												<?php
											}
										}
									?>
								</select>
							</div>
							<input type="hidden" value="" name="delete_block_regex" id="delete_block_regex" />
						<div class="ihc-wrapp-submit-bttn">
							<input type="submit" value="<?php esc_html_e('Add New Rule', 'ihc');?>" name="ihc_save_block_url" class="button button-primary button-large ihc_submit_bttn" />
						</div>
					</div>
				</div>
		<?php
				$data = get_option('ihc_block_url_word');
				if ($data && count($data)){
					?>
						<div class="ihc-dashboard-form-wrap">
						<table class="wp-list-table widefat fixed tags ihc-admin-tables ihc-block-url-table" >
							<thead>
							<tr>
								<th class="manage-column"><?php esc_html_e('Target URL That Contains', 'ihc');?></th>
								<th class="manage-column"><?php esc_html_e('Type', 'ihc');?></th>
								<th class="manage-column"><?php esc_html_e('Target Users', 'ihc');?></th>
								<th class="manage-column"><?php esc_html_e('Redirect To', 'ihc');?></th>
								<th class="manage-column ihc-small-status-col"><?php esc_html_e('Delete', 'ihc');?></th>
							</tr>
							</thead>
						<?php
							$i = 1;
							foreach ($data as $key=>$arr){
							?>
								<tr class="<?php if ($i%2==0){
									 echo 'alternate';
								}
								?>">
									<td><?php echo esc_url($arr['url']);?></td>
									<td><?php
										$print_type = isset($arr['block_or_show']) ? $arr['block_or_show'] : 'block';
										echo ucfirst($print_type);
									?></td>
									<td>
										<?php
											if ($arr['target_users']){
												$levels = explode(',', $arr['target_users']);
												if ($levels && count($levels)){
													$extra_class = ($print_type=='block') ? 'ihc-expired-level' : '';
													foreach ($levels as $val){
														$print_type_user = '';
														if ($val!='reg' && $val!='unreg' && $val!='all'){
															$temp_data = ihc_get_level_by_id($val);
															if (!empty($temp_data['name'])){
																$print_type_user = $temp_data['name'];
															}
														} else {
															$print_type_user = $val;
														}
														if (empty($print_type_user)){
															$print_type_user =esc_html__('Deleted Level', 'ihc');
														}
														?>
														<div class="level-type-list <?php echo esc_attr($extra_class);?>"><?php echo esc_html($print_type_user);?></div>
														<?php
													}
												}
											}
										?>
									</td>
									<td class="ihc-block-url-redirect">
										<?php
											if ($arr['redirect']!=-1){
												$redirect_link = ihc_get_redirect_link_by_label($arr['redirect']);
												if ($redirect_link){
													echo esc_url($redirect_link);
												} else {
													echo get_the_title($arr['redirect']);
												}
											} else {
												echo '-';
											}
										?>
									</td>
									<td align="center">
										<i class="fa-ihc ihc-icon-remove-e ihc-js-admin-block-regex" data-key="<?php echo esc_attr($key);?>" ></i>
									</td>
								</tr>
							<?php
							$i++;
							}
							?>
						</table>
						</div>
			<?php
				}
			break;
		case 'post_types':

			if (isset($_POST['delete_block']) && $_POST['delete_block']!='' && !empty($_POST['ihc_admin_block_url_nonce']) && wp_verify_nonce( sanitize_text_field($_POST['ihc_admin_block_url_nonce']), 'ihc_admin_block_url_nonce' ) ){
				/// ======================== DELETE
				ihc_delete_block_group('ihc_block_posts_by_type', sanitize_text_field($_POST['delete_block']));
			}
			if ( !empty($_POST['ihc_save']) && !empty($_POST['ihc_admin_block_url_nonce']) && wp_verify_nonce( sanitize_text_field($_POST['ihc_admin_block_url_nonce']), 'ihc_admin_block_url_nonce' ) ){
				/// ========================= ADD NEW
				unset($_POST['ihc_save']);
				ihc_save_block_group('ihc_block_posts_by_type', indeed_sanitize_array($_POST), sanitize_text_field($_POST['post_type']));
			}
			?>
			<form method="post" >

				<input type="hidden" name="ihc_admin_block_url_nonce" value="<?php echo wp_create_nonce( 'ihc_admin_block_url_nonce' );?>" />

				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Block All Posts By Type', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line">
							<h4><?php esc_html_e('Custom Post Type', 'ihc');?></h4>
							<p><?php esc_html_e('Choose one of existent custom post types registered into your WordPress website.', 'ihc');?></p>
							<select name="post_type">
							<?php
								global $wp_post_types;
								$post_types = ihc_get_all_post_types();
								foreach ($post_types as $key):
									if (isset($wp_post_types[$key])){
										$obj = $wp_post_types[$key];
										$label =  $obj->labels->name;
									} else {
										$label = ucfirst($key);
									}
							?>
								<option value="<?php echo esc_attr($key);?>"><?php echo esc_html($label) . ' (' . esc_html($key) . ')';?></option>
							<?php
								endforeach;
							?>
							</select>
						</div>

						<div class="iump-form-line">
							<label><?php esc_html_e('Except: ', 'ihc');?></label>
							<input type="text" name="except" value="" />
							<p><i><?php esc_html_e('Write post IDs separated by comma. ex.: 30, 55, 102');?></i></p>
						</div>

						<div class="iump-form-line iump-special-line">
							<div class="iump-form-line">
								<?php
									$type_values = array(
															'show' =>esc_html__('Show Only', 'ihc'),
															'block' =>esc_html__('Block Only', 'ihc')

									);

								?>
								<h4><?php esc_html_e('Restriction type:', 'ihc');?></h4>
								<select name="block_or_show">
									<?php foreach ($type_values as $k=>$v):?>
										<option value="<?php echo esc_attr($k);?>"><?php echo esc_html($v);?></option>
									<?php endforeach;?>
								</select>
							</div>

							<div class="iump-form-line">
								<h4><?php esc_html_e('Target Members:', 'ihc');?></h4>
								<select id="ihc-change-target-user-set-regex" onChange="ihcWriteTagValue(this, '#target_users', '#ihc_tags_field2', 'ihc_select_tag_regex_' );" class="ihc-block-url-select">
									<option value="-1" selected>...</option>
									<?php
										foreach($posible_values as $k=>$v){
										?>
											<option value="<?php echo esc_attr($k);?>"><?php echo esc_html($v);?></option>
										<?php
										}
									?>
								</select>
								<input type="hidden" value="" name="target_users" id="target_users" />
								<div id="ihc_tags_field2"></div>
							</div>

						</div>


						<div class="iump-form-line"><h4><?php esc_html_e('Redirect after', 'ihc');?></h4>
						<p><?php esc_html_e('If access is restrict choose where members will be redirected. If no specific option is selected Default Redirect Page will be used.', 'ihc');?></p>
							<select name="redirect">
								<option value="-1" selected >...</option>
								<?php
									$pages = $pages + ihc_get_redirect_links_as_arr_for_select();
									if ($pages){
										foreach($pages as $k=>$v){
											?>
												<option value="<?php echo esc_attr($k);?>"><?php echo esc_html($v);?></option>
										<?php
										}
									}
								?>
							</select>
						</div>

						<div class="ihc-wrapp-submit-bttn">
							<input type="submit" value="<?php esc_html_e('Add New Rule', 'ihc');?>" name="ihc_save" class="button button-primary button-large ihc_submit_bttn">
						</div>
					</div>
				</div>

			</form>
			<?php
				$data = get_option('ihc_block_posts_by_type');
				if ($data && count($data)){
					?>
						<form method="post"  id="delete_block_form">
							<input type="hidden" name="ihc_admin_block_url_nonce" value="<?php echo wp_create_nonce( 'ihc_admin_block_url_nonce' );?>" />
							<input type="hidden" value="" name="delete_block" id="delete_block" />
						</form>
						<div class="ihc-dashboard-form-wrap">
						<table class="wp-list-table widefat fixed tags ihc-admin-tables ihc-block-url-table" >
							<thead>
							<tr>
								<th class="manage-column"><?php esc_html_e('Target Post Type', 'ihc');?></th>
								<th class="manage-column"><?php esc_html_e('Type', 'ihc');?></th>
								<th class="manage-column"><?php esc_html_e('Target Users', 'ihc');?></th>
								<th class="manage-column"><?php esc_html_e('Except', 'ihc');?></th>
								<th class="manage-column"><?php esc_html_e('Redirect To', 'ihc');?></th>
								<th class="manage-column ihc-small-status-col"><?php esc_html_e('Delete', 'ihc');?></th>
							</tr>
							</thead>
						<?php
							$i = 1;
							foreach ($data as $key=>$arr){
							?>
								<tr class="<?php if ($i%2==0){
									 echo 'alternate';
								}
								?>">
									<td><?php echo esc_html($arr['post_type']);?></td>
									<td><?php
										$print_type = isset($arr['block_or_show']) ? $arr['block_or_show'] : 'block';
										echo ucfirst($print_type);
									?></td>
									<td>
										<?php
											if ($arr['target_users']){
												$levels = explode(',', $arr['target_users']);
												if ($levels && count($levels)){
													$extra_class = ($print_type=='block') ? 'ihc-expired-level' : '';
													foreach ($levels as $val){
														$print_type_user = '';
														if ($val!='reg' && $val!='unreg' && $val!='all'){
															$temp_data = ihc_get_level_by_id($val);
															if (!empty($temp_data['name'])){
																$print_type_user = $temp_data['name'];
															}
														} else {
															$print_type_user = $val;
														}
														if (empty($print_type_user)){
															$print_type_user =esc_html__('Deleted Level', 'ihc');
														}
														?>
														<div class="level-type-list <?php echo esc_attr($extra_class);?>"><?php echo esc_html($print_type_user);?></div>
														<?php
													}
												}
											}
										?>
									</td>
									<td><?php
										if (empty($arr['except'])){
											echo '-';
										} else {
											echo esc_html($arr['except']);
										}
									?></td>
									<td class="ihc-block-url-redirect">
										<?php
											if ($arr['redirect']!=-1){
												$redirect_link = ihc_get_redirect_link_by_label($arr['redirect']);
												if ($redirect_link){
													echo esc_url($redirect_link);
												} else {
													echo get_the_title($arr['redirect']);
												}
											} else {
												echo '-';
											}
										?>
									</td>
									<td align="center">
										<i class="fa-ihc ihc-icon-remove-e ihc-js-admin-delete-block-url-block" data-key="<?php echo esc_attr($key);?>" ></i>
									</td>
								</tr>
							<?php
							$i++;
							}
							?>
						</table>
						</div>
		<?php }
		break;
	case 'cats':
			if (isset($_POST['delete_block']) && $_POST['delete_block']!='' && !empty($_POST['ihc_admin_block_url_nonce']) && wp_verify_nonce( sanitize_text_field($_POST['ihc_admin_block_url_nonce']), 'ihc_admin_block_url_nonce' ) ){
				/// ======================== DELETE
				ihc_delete_block_group('ihc_block_cats_by_name', sanitize_text_field($_POST['delete_block']));
			}
			if (!empty($_POST['ihc_save']) && !empty($_POST['ihc_admin_block_url_nonce']) && wp_verify_nonce( sanitize_text_field($_POST['ihc_admin_block_url_nonce']), 'ihc_admin_block_url_nonce' ) ){
				/// ========================= ADD NEW
				unset($_POST['ihc_save']);
				ihc_save_block_group('ihc_block_cats_by_name', indeed_sanitize_array($_POST), sanitize_text_field($_POST['cat_id']) );
			}
			?>
			<form method="post" >

				<input type="hidden" name="ihc_admin_block_url_nonce" value="<?php echo wp_create_nonce( 'ihc_admin_block_url_nonce' );?>" />

				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Block All Posts By Category Name', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line">
						  <h2><?php esc_html_e('Restrict all Posts from certain Category', 'ihc');?></h2>
						  <p><?php esc_html_e('Set mass restriction over all Posts from a certain Category. This option is available for any type of Posts, from WordPress Posts to Products. If you wish to restrict Category page, you may use Full URL section.', 'ihc');?></p>
						</div>
						<div class="iump-form-line">
							<h4><?php esc_html_e('Category:', 'ihc');?></h4>
							<select name="cat_id">
							<?php
								$terms = ihc_get_all_terms_with_names();
								foreach ($terms as $key=>$label):
							?>
								<option value="<?php echo esc_attr($key);?>"><?php echo esc_html($label);?></option>
							<?php
								endforeach;
							?>
							</select>
						</div>

						<div class="iump-form-line">
							<label><?php esc_html_e('Except: ', 'ihc');?></label>
							<input type="text" name="except" value="" />
							<p><i><?php esc_html_e('Write post IDs separated by comma. ex.: 30, 55, 102');?></i></p>
						</div>

						<div class="iump-form-line iump-special-line">
							<div class="iump-form-line">
								<?php
									$type_values = array(
															'show' =>esc_html__('Show Only', 'ihc'),
															'block' =>esc_html__('Block Only', 'ihc')

									);

								?>
								<h4><?php esc_html_e('Restriction type:', 'ihc');?></h4>
								<select name="block_or_show">
									<?php foreach ($type_values as $k=>$v):?>
										<option value="<?php echo esc_attr($k);?>"><?php echo esc_html($v);?></option>
									<?php endforeach;?>
								</select>
							</div>
							<div class="iump-form-line">
								<h4><?php esc_html_e('Target Members:', 'ihc');?></h4>
								<select id="ihc-change-target-user-set-regex" onChange="ihcWriteTagValue(this, '#target_users', '#ihc_tags_field2', 'ihc_select_tag_regex_' );" class="ihc-block-url-select">
									<option value="-1" selected>...</option>
									<?php
										foreach($posible_values as $k=>$v){
										?>
											<option value="<?php echo esc_attr($k);?>"><?php echo esc_html($v);?></option>
										<?php
										}
									?>
								</select>
								<input type="hidden" value="" name="target_users" id="target_users" />
								<div id="ihc_tags_field2"></div>
							</div>
						</div>

						<div class="iump-form-line"><h4><?php esc_html_e('Redirect after', 'ihc');?></h4>
						<p><?php esc_html_e('If access is restrict choose where members will be redirected. If no specific option is selected Default Redirect Page will be used.', 'ihc');?></p>
							<select name="redirect">
								<option value="-1" selected >...</option>
								<?php
									$pages = $pages + ihc_get_redirect_links_as_arr_for_select();
									if ($pages){
										foreach($pages as $k=>$v){
											?>
												<option value="<?php echo esc_attr($k);?>"><?php echo esc_html($v);?></option>
										<?php
										}
									}
								?>
							</select>
						</div>

						<div class="ihc-wrapp-submit-bttn">
							<input type="submit" value="<?php esc_html_e('Add New Rule', 'ihc');?>" name="ihc_save" class="button button-primary button-large ihc_submit_bttn">
						</div>
					</div>
				</div>

			</form>
			<?php
				$data = get_option('ihc_block_cats_by_name');
				if ($data && count($data)){
					?>
						<form method="post"  id="delete_block_form">
							<input type="hidden" name="ihc_admin_block_url_nonce" value="<?php echo wp_create_nonce( 'ihc_admin_block_url_nonce' );?>" />
							<input type="hidden" value="" name="delete_block" id="delete_block" />
						</form>
						<div class="ihc-dashboard-form-wrap">
						<table class="wp-list-table widefat fixed tags ihc-admin-tables ihc-block-url-table" >
							<thead>
							<tr>
								<th class="manage-column"><?php esc_html_e('Target Category Name', 'ihc');?></th>
								<th class="manage-column"><?php esc_html_e('Type', 'ihc');?></th>
								<th class="manage-column"><?php esc_html_e('Target Users', 'ihc');?></th>
								<th class="manage-column"><?php esc_html_e('Except', 'ihc');?></th>
								<th class="manage-column"><?php esc_html_e('Redirect To', 'ihc');?></th>
								<th class="manage-column ihc-small-status-col"><?php esc_html_e('Delete', 'ihc');?></th>
							</tr>
							</thead>
						<?php
							$i = 1;
							foreach ($data as $key=>$arr){
							?>
								<tr class="<?php if ($i%2==0){
									 echo 'alternate';
								}
								?>">
									<td><?php
										$k = $arr['cat_id'];
										if (!empty($terms[$k])){
											echo esc_html($terms[$k]);
										}
									?></td>
									<td><?php
										$print_type = isset($arr['block_or_show']) ? $arr['block_or_show'] : 'block';
										echo ucfirst($print_type);
									?></td>
									<td>
										<?php
											if ($arr['target_users']){
												$levels = explode(',', $arr['target_users']);
												if ($levels && count($levels)){
													$extra_class = ($print_type=='block') ? 'ihc-expired-level' : '';
													foreach ($levels as $val){
														$print_type_user = '';
														if ($val!='reg' && $val!='unreg' && $val!='all'){
															$temp_data = ihc_get_level_by_id($val);
															if (!empty($temp_data['name'])){
																$print_type_user = $temp_data['name'];
															}
														} else {
															$print_type_user = $val;
														}
														if (empty($print_type_user)){
															$print_type_user =esc_html__('Deleted Level', 'ihc');
														}
														?>
														<div class="level-type-list <?php echo esc_attr($extra_class);?>"><?php echo esc_html($print_type_user);?></div>
														<?php
													}
												}
											}
										?>
									</td>
									<td><?php
										if (empty($arr['except'])){
											echo '-';
										} else {
											echo esc_html($arr['except']);
										}
									?></td>
									<td class="ihc-block-url-redirect">
										<?php
											if ($arr['redirect']!=-1){
												$redirect_link = ihc_get_redirect_link_by_label($arr['redirect']);
												if ($redirect_link){
													echo esc_url($redirect_link);
												} else {
													echo get_the_title($arr['redirect']);
												}
											} else {
												echo '-';
											}
										?>
									</td>
									<td align="center">
										<i class="fa-ihc ihc-icon-remove-e ihc-js-admin-delete-block-url-redirect" data-key="<?php echo esc_attr($key);?>" ></i>
									</td>
								</tr>
							<?php
							$i++;
							}
							?>
						</table>
						</div>
		<?php }

		break;
	case 'files':
			if (isset($_POST['delete_block']) && $_POST['delete_block']!=''){
				/// ======================== DELETE
				ihc_delete_block_group('ihc_block_files_by_url', sanitize_text_field($_POST['delete_block']));
			}
			if (!empty($_POST['ihc_save']) && !empty($_POST['ihc_admin_block_url_nonce']) && wp_verify_nonce( sanitize_text_field($_POST['ihc_admin_block_url_nonce']), 'ihc_admin_block_url_nonce' ) ){
				/// ========================= ADD NEW
				unset($_POST['ihc_save']);
				ihc_save_block_group('ihc_block_files_by_url', indeed_sanitize_array($_POST), sanitize_text_field($_POST['file_url']) );
				ihc_do_write_into_htaccess();
			}
			?>
			<form method="post" >

				<input type="hidden" name="ihc_admin_block_url_nonce" value="<?php echo wp_create_nonce( 'ihc_admin_block_url_nonce' );?>" />

				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Block Files By URL', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line">
						  <h2><?php esc_html_e('Restrict Physical Files stored on your WordPress', 'ihc');?></h2>
						  <p><?php esc_html_e('Restriction rule is applied only on additional media files stored inside  your WordPress with mp3|mp4|avi|pdf|zip|rar|doc|gz|tar|docx|xls|xlsx|PDF extension. ', 'ihc');?></p>
						</div>
						<div class="iump-form-line">
						  <div class="row">
						      <div class="col-xs-8">
						                 <div class="input-group">
						                    <span class="input-group-addon"><?php esc_html_e('Full File Link', 'ihc');?></span>
						                    <input class="ihc-block-url-file-url form-control" type="text"  value="" name="file_url" placeholder="<?php esc_html_e('copy the entire File Link from your browser', 'ihc');?>">
						                 </div>
						         </div>
						     </div>
						</div>

						<div class="iump-form-line iump-special-line">

							<div class="iump-form-line">
								<?php
									$type_values = array(
															'show' =>esc_html__('Show Only', 'ihc'),
															'block' =>esc_html__('Block Only', 'ihc')

									);

								?>
								<h4><?php esc_html_e('Restriction type:', 'ihc');?></h4>
								<select name="block_or_show">
									<?php foreach ($type_values as $k=>$v):?>
										<option value="<?php echo esc_attr($k);?>"><?php echo esc_html($v);?></option>
									<?php endforeach;?>
								</select>
							</div>

							<div class="iump-form-line">
								<h4><?php esc_html_e('Target Members:', 'ihc');?></h4>
								<select id="ihc-change-target-user-set-regex" onChange="ihcWriteTagValue(this, '#target_users', '#ihc_tags_field2', 'ihc_select_tag_regex_' );" class="ihc-block-url-select">
									<option value="-1" selected>...</option>
									<?php
										foreach($posible_values as $k=>$v){
										?>
											<option value="<?php echo esc_attr($k);?>"><?php echo esc_html($v);?></option>
										<?php
										}
									?>
								</select>
								<input type="hidden" value="" name="target_users" id="target_users" />
								<div id="ihc_tags_field2"></div>
							</div>

						</div>

						<div class="iump-form-line">

							<h4><?php esc_html_e('Redirect after', 'ihc');?></h4>
							<p><?php esc_html_e('If access is restrict choose where members will be redirected. If no specific option is selected Default Redirect Page will be used.', 'ihc');?></p>
							<select name="redirect">
								<option value="-1" selected >...</option>
								<?php
									$pages = $pages + ihc_get_redirect_links_as_arr_for_select();
									if ($pages){
										foreach($pages as $k=>$v){
											?>
												<option value="<?php echo esc_attr($k);?>"><?php echo esc_html($v);?></option>
										<?php
										}
									}
								?>
							</select>
						</div>

						<div class="ihc-wrapp-submit-bttn">
							<input type="submit" value="<?php esc_html_e('Add New Rule', 'ihc');?>" name="ihc_save" class="button button-primary button-large ihc_submit_bttn">
						</div>
					</div>
				</div>

			</form>
			<?php
				$data = get_option('ihc_block_files_by_url');
				if ($data && count($data)){
					?>
						<form method="post"  id="delete_block_form">
							<input type="hidden" value="" name="delete_block" id="delete_block" />
						</form>
						<div class="ihc-dashboard-form-wrap">
						<table class="wp-list-table widefat fixed tags ihc-admin-tables ihc-block-url-table" >
							<thead>
							<tr>
								<th class="manage-column"><?php esc_html_e('Target File URL', 'ihc');?></th>
								<th class="manage-column"><?php esc_html_e('Type', 'ihc');?></th>
								<th class="manage-column"><?php esc_html_e('Target Users', 'ihc');?></th>
								<th class="manage-column"><?php esc_html_e('Redirect To', 'ihc');?></th>
								<th class="manage-column ihc-small-status-col"><?php esc_html_e('Delete', 'ihc');?></th>
							</tr>
							</thead>
						<?php
							$i = 1;
							foreach ($data as $key=>$arr){
							?>
								<tr class="<?php if ($i%2==0){
									 echo 'alternate';
								}
								?>">
									<td><?php echo esc_url($arr['file_url']);?></td>
									<td><?php
										$print_type = isset($arr['block_or_show']) ? $arr['block_or_show'] : 'block';
										echo ucfirst($print_type);
									?></td>
									<td>
										<?php
											if ($arr['target_users']){
												$levels = explode(',', $arr['target_users']);
												if ($levels && count($levels)){
													$extra_class = ($print_type=='block') ? 'ihc-expired-level' : '';
													foreach ($levels as $val){
														$print_type_user = '';
														if ($val!='reg' && $val!='unreg' && $val!='all'){
															$temp_data = ihc_get_level_by_id($val);
															if (!empty($temp_data['name'])){
																$print_type_user = $temp_data['name'];
															}
														} else {
															$print_type_user = $val;
														}
														if (empty($print_type_user)){
															$print_type_user =esc_html__('Deleted Level', 'ihc');
														}
														?>
														<div class="level-type-list <?php echo esc_attr($extra_class);?>"><?php echo esc_html($print_type_user);?></div>
														<?php
													}
												}
											}
										?>
									</td>
									<td class="ihc-block-url-redirect">
										<?php
											if ($arr['redirect']!=-1){
												$redirect_link = ihc_get_redirect_link_by_label($arr['redirect']);
												if ($redirect_link){
													echo esc_url($redirect_link);
												} else {
													echo get_the_title($arr['redirect']);
												}
											} else {
												echo '-';
											}
										?>
									</td>
									<td align="center">
										<i class="fa-ihc ihc-icon-remove-e ihc-js-delete-block-url-redirect" data-key="<?php echo esc_attr($key);?>" ></i>
									</td>
								</tr>
							<?php
							$i++;
							}
							?>
						</table>
						</div>
			<?php }
		break;
endswitch;
?>
</div>
<?php
