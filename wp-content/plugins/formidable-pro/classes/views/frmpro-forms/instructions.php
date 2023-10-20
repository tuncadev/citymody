<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

FrmAppHelper::show_search_box(
	array(
		'input_id'    => 'default-value-field',
		'placeholder' => __( 'Search Smart Tags', 'formidable-pro' ),
		'tosearch'    => 'search-smart-tags',
	)
);
?>

<div id="frm-dynamic-values">
	<ul class="frm_code_list frm-full-hover frm-short-list">
		<?php
		foreach ( $tags as $tag => $label ) {
			$title = '';
			if ( is_array( $label ) ) {
				$title = isset( $label['title'] ) ? $label['title'] : '';
				$label = isset( $label['label'] ) ? $label['label'] : reset( $label );
			}

			?>
			<li class="search-smart-tags">
				<a href="javascript:void(0)" data-code="<?php echo esc_attr( $tag ); ?>" class="show_dyn_default_value frm_insert_code
					<?php
					if ( ! empty( $title ) ) {
						echo ' frm_help" title="' . esc_attr( $title );
					}
					?>">
					<?php echo esc_html( $label ); ?>
					<span>[<?php echo esc_html( $tag ); ?>]</span>
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
</div>
