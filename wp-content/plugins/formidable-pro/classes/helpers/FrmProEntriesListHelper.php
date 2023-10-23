<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProEntriesListHelper extends FrmEntriesListHelper {

	public function get_bulk_actions() {
		$actions = array(
			'bulk_delete' => __( 'Delete', 'formidable-pro' ),
		);

		if ( ! current_user_can('frm_delete_entries') ) {
			unset($actions['bulk_delete']);
		}

		//$actions['bulk_export'] = __( 'Export to XML', 'formidable-pro' );
		if ( $this->params['form'] ) {
			$actions['bulk_csv'] = __( 'Export to CSV', 'formidable-pro' );
		}

		return $actions;
	}

	protected function extra_tablenav( $which ) {
		parent::extra_tablenav( $which );
		$is_footer    = ( $which !== 'top' );
		$entries_args = array(
			'entries_count'                    => $this->total_items,
			'bulk_delete_confirmation_message' => $this->confirm_bulk_delete(),
		);
		FrmProEntriesHelper::before_table( $is_footer, $this->params['form'], $entries_args );
	}

	public function search_box( $text, $input_id ) {
		if ( ! $this->has_items() && ! isset( $_REQUEST['s'] ) ) {
			return;
		}

		if ( isset( $this->params['form'] ) ) {
			$form = FrmForm::getOne( $this->params['form'] );
		} else {
			$form = FrmForm::get_published_forms( array(), 1 );
		}

		if ( ! $form ) {
			return;
		}

		$field_list = FrmField::getAll( array( 'fi.form_id' => $form->id, 'fi.type not' => FrmField::no_save_fields() ), 'field_order' );

		$fid = isset( $_REQUEST['fid'] ) ? sanitize_title( stripslashes( $_REQUEST['fid'] ) ) : '';
		$input_id = $input_id . '-search-input';
		$search_str = isset( $_REQUEST['s'] ) ? sanitize_text_field( stripslashes( $_REQUEST['s'] ) ) : '';

		foreach ( array( 'orderby', 'order' ) as $get_var ) {
			if ( ! empty( $_REQUEST[ $get_var ] ) ) {
				echo '<input type="hidden" name="' . esc_attr( $get_var ) . '" value="' . esc_attr( $_REQUEST[ $get_var ] ) . '" />';
			}
		}

		$options = self::get_entry_search_options( $field_list );
?>
<div class="frm-search">
	<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_attr( $text ); ?>:</label>
	<?php FrmProAppHelper::icon_by_class( 'frm_icon_font frm_search_icon' ); ?>
	<input type="text" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php echo esc_attr( $search_str ); ?>" class="frm-search-input" />
	<?php
	if ( empty( $field_list ) ) {
			submit_button( $text, 'button', false, false, array( 'id' => 'search-submit' ) );
			echo '</div>';
			return;
	}
	?>
	<select name="fid" class="hide-if-js">
		<?php
		foreach ( $options as $v => $opt ) {
			?>
			<option value="<?php echo esc_attr( $v ); ?>" <?php selected( $fid, $v ); ?>>
				<?php echo esc_html( $opt ); ?>
			</option>
			<?php
		}
		?>
	</select>

	<div class="button dropdown hide-if-no-js" id="search-submit">
		<a href="#" id="frm-fid-search" class="frm-dropdown-toggle" data-toggle="dropdown">
			<?php esc_html_e( 'Search', 'formidable-pro' ); ?>
			<b class="caret"></b>
		</a>
		<ul class="frm-dropdown-menu <?php echo esc_attr( is_rtl() ? 'dropdown-menu-left' : 'dropdown-menu-right' ); ?>" id="frm-fid-search-menu" role="menu" aria-labelledby="frm-fid-search">
			<?php
			foreach ( $options as $v => $opt ) {
				?>
			<li>
				<a href="#" id="fid-<?php echo esc_attr( $v ); ?>">
					<?php echo esc_html( $opt ); ?>
				</a>
			</li>
				<?php
			}
			?>
		</ul>
	</div>
		<?php
		submit_button( $text, 'button hide-if-js', false, false, array( 'id' => 'search-submit' ) );
		?>

</div>
<?php
	}

	/**
	 * @since 4.04.02
	 */
	private static function get_entry_search_options( $field_list ) {
		$options = array(
			''           => '&mdash; ' . __( 'All Fields', 'formidable-pro' ) . ' &mdash;',
			'created_at' => __( 'Entry creation date', 'formidable-pro' ),
			'id'         => __( 'Entry ID', 'formidable-pro' ),
		);

		foreach ( $field_list as $f ) {
			$value = ( $f->type == 'user_id' ) ? 'user_id' : $f->id;
			$options[ $value ] = FrmAppHelper::truncate( $f->name, 30 );
		}

		return apply_filters( 'frm_admin_search_options', $options, compact( 'field_list' ) );
	}
}
