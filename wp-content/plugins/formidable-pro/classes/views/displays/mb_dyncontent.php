<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

_deprecated_file( basename( __FILE__ ), '4.09', null, 'This file can be found in formidable-views/classes/views/displays/mb_dyncontent.php' );
?>
<div class="nav-menus-php hide-if-no-js ">
<div class="wrap">
	<ul class="nav-tab-wrapper frm_form_nav">
		<li>
			<a href="#frm_content_box" class="nav-tab-active" id="frm_listing_tab" data-one="<?php esc_attr_e( 'Detail Page', 'formidable-pro' ); ?>" data-label="<?php esc_attr_e( 'Listing Page', 'formidable-pro' ); ?>">
				<?php echo ( $post->frm_show_count == 'one' ) ? esc_html__( 'Detail Page', 'formidable-pro' ) : esc_html__( 'Listing Page', 'formidable-pro' ); ?>
			</a>
		</li>
		<li class="hide_dyncontent <?php echo esc_attr( $use_dynamic_content ? '' : 'frm_hidden' ); ?>">
			<a href="#frm_dyncontent_box">
				<?php esc_html_e( 'Detail Page', 'formidable-pro' ); ?>
			</a>
		</li>
	</ul>
	<p class="nav-menu-content frm_content_box hide_single_content <?php echo ( $post->frm_show_count == 'one' ) ? 'frm_hidden' : ''; ?>">
		<?php esc_html_e( 'This page lists multiple entries. Link to a single entry/detail page using [detaillink]', 'formidable-pro' ); ?>
	</p>
	<p class="nav-menu-content frm_dyncontent_box frm_hidden">
		<?php esc_html_e( 'This is the detail page for a single entry in this form', 'formidable-pro' ); ?>
	</p>
</div>
</div>

<div class="nav-menu-content" id="frm_content_box">
<p class="hide_single_content <?php echo ( $post->frm_show_count == 'one' ) ? 'frm_hidden' : ''; ?>">
<label><?php esc_html_e( 'Before Content', 'formidable-pro' ); ?>
	<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e( 'This content will not be repeated. This would be a good place to put any HTML table tags.', 'formidable-pro' ) ?>" ></span> (<?php esc_html_e( 'optional', 'formidable-pro' ); ?>)
	<textarea id="before_content" name="options[before_content]" rows="3" class="frm_98_width"><?php echo FrmAppHelper::esc_textarea( $post->frm_before_content ) ?></textarea>
</label>
</p>


<div>
	<label><?php esc_html_e( 'Content', 'formidable-pro' ); ?>
		<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e( 'The HTML for your page. If \'All Entries\' is selected above, this content will be repeated for each entry. The field ID and Key work synonymously, although there are times one choice may be better. If you are panning to copy your view settings to other blogs, use the Key since they will be copied and the ids may differ from blog to blog.', 'formidable-pro' ) ?>"></span>
	</label>


	<p style="float:right;margin:0;">
		<label for="options_no_rt">
			<input type="checkbox" id="options_no_rt" name="options[no_rt]" value="1" <?php checked( $post->frm_no_rt, 1 ); ?> /> 
			<?php esc_html_e( 'Disable visual editor for this view', 'formidable-pro' ); ?>
		</label>
		<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e( 'It is recommended to check this box if you include a <table> tag in the Before Content box. If you are editing a view and notice the visual tab is selected and your table HTML is missing, you can switch to the HTML tab, go up to your url in your browser and hit enter to reload the page. As long as the settings have not been saved, your old HTML will be back to way it was before loading it in the visual tab.', 'formidable-pro' ) ?>"></span>
	</p>
<div class="clear"></div>

<div id="<?php echo ( ! $post->frm_no_rt && user_can_richedit() ) ? 'postdivrich' : 'postdiv'; ?>" class="postarea frm_full_rte">
<?php wp_editor( $post->post_content, 'content', $editor_args ); ?>
</div>
</div>


<p class="hide_single_content <?php echo ( $post->frm_show_count == 'one' ) ? 'frm_hidden' : ''; ?>">
	<label><?php esc_html_e( 'After Content', 'formidable-pro' ); ?>
		<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e( 'This content will not be repeated. This would be a good place to close any HTML tags from the Before Content field.', 'formidable-pro' ) ?>" ></span>
		(<?php esc_html_e( 'optional', 'formidable-pro' ); ?>)
		<textarea id="after_content" name="options[after_content]" rows="3" class="frm_98_width"><?php echo FrmAppHelper::esc_textarea( $post->frm_after_content ) ?></textarea>
	</label>
</p>

</div>


<div class="nav-menu-content hide-if-js" id="frm_dyncontent_box">
    <div class="hide_dyncontent <?php echo esc_attr( $use_dynamic_content ? '' : 'frm_hidden' ); ?>">
		<label><?php esc_html_e( 'Dynamic Content', 'formidable-pro' ); ?>
			<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php printf( esc_html__( 'The HTML for the entry on the dynamic page. This content will NOT be repeated, and will only show when the %1$s is clicked.', 'formidable-pro' ), '[detaillink]' ); ?>" ></span>
		</label>
        <?php wp_editor( $post->frm_dyncontent, 'dyncontent', $editor_args ); ?>
    </div>
</div>

<?php
if ( ! shortcode_exists( 'frm-export-view' ) && strpos( $post->frm_before_content, '<table' ) !== false ) {
	// Show a message for exporting views.
	FrmAppController::include_upgrade_overlay();
	$data = array(
		'data-upgrade' => 'Exporting Views to CSV',
		'data-medium'  => 'view-export',
	);

	$upgrading = FrmProAddonsController::install_link( 'export-view' );
	if ( isset( $upgrading['url'] ) ) {
		$data['data-oneclick'] = json_encode( $upgrading );
	}

	?>
	<p class="frmcenter">
		<a href="javascript:void(0)" class="frm_pro_tip" <?php echo FrmAppHelper::array_to_html_params( $data ); // WPCS: XSS ok. ?>>
			Want to export Views from the front-end?<br/>
			<span class="frm-tip-cta">Get the Export Views to CSV add-on.</span>
		</a>
	</p>
	<?php
}
