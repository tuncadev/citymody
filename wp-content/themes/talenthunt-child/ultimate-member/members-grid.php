<?php if ( ! defined( 'ABSPATH' ) ) exit;

$unique_hash = substr( md5( $args['form_id'] ), 10, 5 ); ?>

<script type="text/template" id="tmpl-um-member-grid-<?php echo esc_attr( $unique_hash ) ?>">
<div class="um-members-grid-wrapper">
	<div class="um-members um-members-list">

		<# if ( data.length > 0 ) { #>
			<# _.each( data, function( user, key, list ) { #>

				<div class="um-member um-role-{{{user.role}}} {{{user.account_status}}} <?php if ( $cover_photos ) { echo 'with-cover'; } ?>">

					<span class="um-member-status {{{user.account_status}}}">
						{{{user.account_status_name}}}
					</span>

					<?php if ( $cover_photos ) { ?>
						<div class="um-member-cover" data-ratio="<?php echo esc_attr( UM()->options()->get( 'profile_cover_ratio' ) ); ?>">
							<div class="um-member-cover-e">
								<a href="{{{user.profile_url}}}" title="{{{user.display_name}}}">
									{{{user.cover_photo}}}
								</a>
							</div>
						</div>
					<?php }

					if ( $profile_photo ) { ?>
					<?php $value = get_theme_mod( 'member_profile_photo_grayscale_checkbox_setting', false ); ?>
						<div class="um-member-photo radius-<?php echo esc_attr( UM()->options()->get( 'profile_photocorner' ) ); ?> <?php echo ( $value ) ? 'um-member-photo-grayscale' : '' ?>">
							
							<a href="{{{user.profile_url}}}" title="{{{user.display_name}}}">
								{{{user.avatar}}}
								<?php do_action( 'um_members_in_profile_photo_tmpl', $args ); ?>
							</a>
					

				<?php $value = get_theme_mod( 'member_card_hover_checkbox_setting', false ); ?>
					<?php do_action( 'um_members_after_user_name_tmpl', $args ); ?>
						{{{user.hook_after_user_name}}}
				<div class="<?php echo ( $value ) ? 'um-member-card-wrap-off' : 'um-member-card-wrap-on' ?> ">
					

					<div class="um-member-card <?php if ( ! $profile_photo ) { echo 'no-photo'; } ?>">

						<div class="<?php if ( $show_tagline && ! empty( $tagline_fields ) && is_array( $tagline_fields ) ) { echo 'um-member-meta-main'; } ?>" >

						<?php if ( $show_tagline && ! empty( $tagline_fields ) && is_array( $tagline_fields ) ) {

							foreach ( $tagline_fields as $key ) {
								if ( empty( $key ) ) {
									continue;
								} ?>

								<# if ( typeof user['<?php echo $key; ?>'] !== 'undefined' ) { #>
									<div class="um-member-tagline um-member-tagline-<?php echo esc_attr( $key ); ?>"
									     data-key="<?php echo esc_attr( $key ); ?>">
										{{{user['<?php echo $key; ?>']}}}
									</div>
								<# } #>

							<?php }
						}

						if ( $show_userinfo ) { ?>

							<# var $show_block = false; #>

							<?php foreach ( $reveal_fields as $k => $key ) {
								if ( empty( $key ) ) {
									unset( $reveal_fields[ $k ] );
								} ?>
								<# if ( typeof user['<?php echo $key; ?>'] !== 'undefined' ) {
									$show_block = true;
								} #>
							<?php }

							if ( $show_social ) { ?>
								<# if ( ! $show_block ) { #>
									<# $show_block = user.social_urls #>
								<# } #>
							<?php } ?>

							<# if ( $show_block ) { #>
								
									<?php if ( $userinfo_animate ) { ?>
										<div class="um-member-more">
											<a href="javascript:void(0);"><i class="um-faicon-angle-down"></i></a>
										</div>
									<?php } ?>

									<div class="um-member-meta <?php if ( ! $userinfo_animate ) { echo 'no-animate'; } ?>">

										<?php foreach ( $reveal_fields as $key ) { ?>

											<# if ( typeof user['<?php echo $key; ?>'] !== 'undefined' ) { #>
												<div class="um-member-metaline um-member-metaline-<?php echo $key; ?>">
													<strong>{{{user['label_<?php echo $key;?>']}}}:</strong> {{{user['<?php echo $key;?>']}}}
												</div>
											<# } #>

										<?php }

										if ( $show_social ) { ?>
											<div class="um-member-connect">
												{{{user.social_urls}}}
											</div>
										<?php } ?>
									</div>

									<?php if ( $userinfo_animate ) { ?>
										<div class="um-member-less">
											<a href="javascript:void(0);"><i class="um-faicon-angle-up"></i></a>
										</div>
									<?php } ?>
							
							<# } #>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>

			
					<?php
						// please use for buttons priority > 100
						do_action( 'um_members_just_after_name_tmpl', $args ); ?>
						{{{user.hook_just_after_name}}}


						<# if ( user.can_edit ) { #>
							<div class="um-members-edit-btn">
								<a href="{{{user.edit_profile_url}}}" class="um-edit-profile-btn um-button um-alt">
									<?php _e( 'Edit profile','ultimate-member' ) ?>
								</a>
							</div>
						<# } #>


						<?php if ( $show_name ) { ?>
							<div class="um-member-name">
								<h3><a href="{{{user.profile_url}}}" title="{{{user.display_name}}}">
									{{{user.display_name_html}}}
								</a></h3>
							</div> 
						<?php } ?>

			</div>


		<?php } ?>

			<# }); #>
		<# } else { #>



			<div class="um-members-none">
				<p><?php echo $no_users; ?></p>
			</div>

		<# } #>

		<div class="um-clear"></div>

		</div>
	</div>
</script>