<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<div id="form_reports_page" class="frm_wrap frm_charts">
	<?php
	FrmAppHelper::get_admin_header( array(
		'label' => __( 'Reports', 'formidable-pro' ),
		'form'  => $form,
	) );

	$class = 'odd';
	$time_data = isset( $data['time'] ) ? $data['time'] : '';
	?>
	<div class="frm-inner-content wrap">
		<h2><?php esc_html_e( 'Reports', 'formidable-pro' ); ?></h2>
		<form method="POST" class="frm-report-filter frm-flex-justify tablenav">
			<div class="frm_form_field">
				<label for="frm_stats_date_range" class="frm_primary_label"><?php esc_html_e( 'Date range', 'formidable-pro' ); ?></label>
				<select id="frm_stats_date_range" name="date_range">
					<?php
					foreach ( $date_range_options as $val => $label ) {
						?>
						<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $val, $selected_date_range ); ?>><?php echo esc_html( $label ); ?></option>
						<?php
					}
					?>
				</select>
			</div>
			<div class="frm_form_field frm_stats_date_wrapper frm_invisible">
				<label for="frm_stats_start_date" class="frm_primary_label"><?php esc_html_e( 'Start date', 'formidable-pro' ); ?></label>
				<input name="start_date" id="frm_stats_start_date" type="date" value="<?php echo $start_date ? esc_attr( gmdate( 'Y-m-d', strtotime( $start_date ) ) ) : ''; ?>" disabled />
			</div>
			<div class="frm_form_field frm_stats_date_wrapper frm_invisible">
				<label for="frm_stats_end_date" class="frm_primary_label"><?php esc_html_e( 'End date', 'formidable-pro' ); ?></label>
				<input name="end_date" id="frm_stats_end_date" type="date" value="<?php echo $end_date ? esc_attr( gmdate( 'Y-m-d', strtotime( $end_date ) ) ) : ''; ?>" disabled />
			</div>
			<div>
				<br>
				<button class="frm-button-secondary frm-button-sm" type="submit">
					<?php esc_html_e( 'Apply', 'formidable-pro' ); ?>
				</button>
			</div>
		</form>

		<div class="frmcenter">
		<div class="postbox">
			<div class="inside">
				<h3><?php esc_html_e( 'Submissions', 'formidable-pro' ); ?></h3>
				<b><?php echo count( $entries ); ?></b>
			</div>
		</div>
		<?php if ( isset( $submitted_user_ids ) ) { ?>
			<div class="postbox">
				<div class="inside">
					<h3><?php esc_html_e( 'Users Submitted', 'formidable-pro' ); ?></h3>
					<b><?php echo count( $submitted_user_ids ); ?> (<?php echo round( ( count( $submitted_user_ids ) / count( $user_ids ) ) * 100, 2 ); ?>%)</b>
				</div>
			</div>
		<?php } ?>
		<div class="clear"></div>
		</div>

		<div class="frm-inline-pro-tip">
			<?php if ( $time_data ) { ?>
				<h3><?php esc_html_e( 'Responses Over Time', 'formidable-pro' ); ?></h3>
			<?php } ?>

			<a class="frm-pro-tip frm-pro-tip-end" href="https://formidableforms.com/knowledgebase/graphs/" target="_blank">
				<span class="frm-pro-tip-text"><?php esc_html_e( 'Pro Tip: Add graphs like this on a page', 'formidable-pro' ); ?></span>
				<?php FrmAppHelper::icon_by_class( 'frmfont frm_external_link_icon' ); ?>
			</a>
		</div>

		<?php
		if ( $time_data ) {
			echo $data['time'];
		}

		foreach ( $fields as $field ) {
			if ( ! isset( $data[ $field->id ] ) ) {
                continue;
            }

			$post_boxes = self::get_field_boxes( compact( 'field', 'entries' ) );
			if ( empty( $post_boxes ) ) {
				continue;
			}
            ?>
			<div class="frm_report_box pg_<?php echo esc_attr( $class ); ?>" data-ftype="<?php echo esc_attr( $field->type ); ?>">
				<h3>
					<?php echo esc_html( $field->name ); ?>
				</h3>
				<?php echo $data[ $field->id ]; ?>

				<?php if ( isset( $data[ $field->id . '_table' ] ) ) { ?>
					<br/>
					<?php echo $data[ $field->id . '_table' ]; ?>
				<?php } ?>

				<div class="frmcenter" style="margin-top:20px;">
				<?php foreach ( $post_boxes as $box ) { ?>
				<div class="postbox">
					<div class="inside">
						<h3><?php echo esc_html( $box['label'] ); ?></h3>
						<?php echo esc_html( $box['stat'] ); ?>
					</div>
				</div>
				<?php } ?>

				<?php
				/**
				 * Fires after the field report.
				 *
				 * @since 5.0.02
				 *
				 * @param array $args The arguments. Contains `field`..
				 */
				do_action( 'frm_pro_after_field_report', compact( 'field' ) );
				?>
			</div>

            <div class="clear"></div>
            </div>
        <?php
			$class = ( $class == 'odd' ) ? 'even' : 'odd';
            unset($field);
        }

        if ( isset($data['month']) ) {
            echo $data['month'];
        }
?>
	</div>
</div>
