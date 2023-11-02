<?php
do_action( 'ump_admin_after_top_menu_add_ons' );
$pluginSlug = $data['plugin_slug'];
?>
<form action="" method="post">
	<div class="ihc-stuffbox">
		<h3 class="ihc-h3">Extended Shortcodes</h3>
		<div class="inside">

			<div class="iump-form-line">
				<h2><?php esc_html_e('Activate/Hold ', 'extended-shortcodes-for-ultimate-membership-pro');?> Extended Shortcodes <?php esc_html_e(' Integration', 'extended-shortcodes-for-ultimate-membership-pro');?></h2>
                <p><?php esc_html_e('Activate this feature in order to extend the functionality provided based on additional dedicated Shortcodes ', 'extended-shortcodes-for-ultimate-membership-pro'); ?></p>

                <label class="iump_label_shiwtch iump_checkbox">
					<?php $checked = ($data[ $pluginSlug . '-enabled']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#<?php esc_html_e($pluginSlug . '-enabled');?>');" <?php esc_html_e($checked);?> />
					<div class="switch"></div>
				</label>
				<input type="hidden" name="<?php esc_html_e($pluginSlug . '-enabled');?>" value="<?php esc_html_e($data[ $pluginSlug . '-enabled']);?>" id="<?php esc_html_e($pluginSlug . '-enabled');?>" />

            </div>
            <div class="iump-form-line">
            	<!-- Add your extra settings and options here -->
							<?php if ( $data['shortcodes'] ):?>
                          <table class="wp-list-table widefat fixed tags ihc-admin-tables">
								<thead>
										 <tr>
											<th><?php esc_html_e('Shortcode', 'extended-shortcodes-for-ultimate-membership-pro');?></th>
                                            <th><?php esc_html_e('What it does', 'extended-shortcodes-for-ultimate-membership-pro');?></th>
                                             <th><?php esc_html_e('Arguments available', 'extended-shortcodes-for-ultimate-membership-pro');?></th>
                                         </tr>
                                </thead>
								<tbody>
									<?php if ( $data['shortcodes'] ):?>
											<?php foreach ( $data['shortcodes'] as $shortcodeDetails ):?>
													<tr class="alternate">
															<?php
																	$attributes = '';
																	foreach ( $shortcodeDetails['attributes'] as $attr ){
																			$attributes .= " $attr='' ";

																	}
															?>
															<?php if ( $shortcodeDetails['single'] ):?>
																	<td><?php esc_html_e('[' . $shortcodeDetails['shortcode'] . ' ' . $attributes . ']');?></td>
															<?php else :?>
																	<td><?php esc_html_e('[' . $shortcodeDetails['shortcode'] . ' ' . $attributes . '] content here [/' . $shortcodeDetails['shortcode'] . ']');?></td>
															<?php endif;?>

		                          <td><?php esc_html_e($shortcodeDetails['what_it_does'], 'extended-shortcodes-for-ultimate-membership-pro');?></td>
		                        	<td>
																	<?php if ( $shortcodeDetails['attributes'] ):?>
																			<?php foreach ( $shortcodeDetails['attributes'] as $attribute ):?>
																					<div><?php esc_html_e($attribute);?></div>
																			<?php endforeach;?>
																	<?php endif;?>
															</td>
													</tr>
											<?php endforeach;?>
									<?php endif;?>
                                   </tbody>
                              </table>
							<?php endif;?>
            </div>

 			<div class="iump-form-line">
            </div>
            <div class="ihc-wrapp-submit-bttn iump_submit">
				<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'extended-shortcodes-for-ultimate-membership-pro');?>" name="ihc_save" class="button button-primary button-large" />
			</div>

		</div>
	</div>

</form>
<?php
