<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

if ( empty( $tags ) ) {
	echo '<p class="frm_no_bottom_margin">' . esc_html__( 'No options found.', 'formidable-pro' ) . '</p>';
	return;
}

FrmAppHelper::show_search_box(
	array(
		'input_id'    => 'default-value-field',
		'placeholder' => __( 'Search Terms', 'formidable-pro' ),
		'tosearch'    => 'search-terms',
	)
);

?>

<ul class="frm_code_list frm-full-hover frm-short-list">
	<?php
	foreach ( $tags as $tag => $label ) {
		?>
		<li class="search-terms">
			<a href="javascript:void(0)" data-code="<?php echo esc_attr( $tag ); ?>" class="show_dyn_default_value frm_insert_code" data-shortcode="0">
				<?php echo esc_html( $label ); ?>
				<span><?php echo esc_html( $tag ); ?></span>
			</a>
		</li>
		<?php
		unset( $tag, $label );
	}
	?>
</ul>
<p class="howto">
	<?php esc_html_e( 'Click smart value to dynamically populate this field. Smart values are not used when editing entries.', 'formidable-pro' ); ?>
</p>
