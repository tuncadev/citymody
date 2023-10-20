<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 6.0
 */
class FrmProStylesPreviewHelper {

	/**
	 * Modify various form behaviours for the styler preview, mostly focused on making sure that fields are all visible at load.
	 *
	 * @return void
	 */
	public function adjust_form_for_preview() {
		$this->prevent_pro_form_scripts_from_registering();
		$this->prevent_draft_entries_from_loading();
		$this->force_all_fields_on_page();
		$this->open_collapsible_sections();
		$this->clear_form_open_status();
		$this->change_lookup_field_markup();
		$this->hide_summary_fields();
		$this->show_other_inputs();
		$this->disable_conversational_forms_in_style_preview();
		$this->prevent_stripe_from_loading_in_style_preview();
		$this->prevent_payment_gateway_fields_from_rendering();
	}

	/**
	 * Avoid loading the scripts for the form preview.
	 * This includes the footer_js.php file that loads scripts for calculations, conditional logic, etc.
	 *
	 * @since 6.0
	 *
	 * @return void
	 */
	private function prevent_pro_form_scripts_from_registering() {
		remove_action( 'frm_before_get_form', 'FrmProAppController::register_scripts' );
		remove_action( 'admin_footer', 'FrmProFormsController::enqueue_footer_js', 19 );
		remove_action( 'admin_print_footer_scripts', 'FrmProFormsController::footer_js', 40 );
		self::remove_tinymce_init_callback();

		// These are loaded on admin pages but are not necessary for the visual styler so dequeue them.
		wp_dequeue_script( 'frm-surveys-admin' );
		wp_dequeue_script( 'frm-quizzes-form-action' );
	}

	/**
	 * We do not want a saved draft entry appearing in the styler preview.
	 * The filters below disable an edited entry from being loaded, and forces a new form to load instead.
	 *
	 * @since 6.0
	 *
	 * @return void
	 */
	private function prevent_draft_entries_from_loading() {
		remove_action( 'frm_display_form_action', 'FrmProEntriesController::edit_update_form' );
		remove_filter( 'frm_continue_to_new', 'FrmProEntriesController::maybe_editing' );
		add_filter( 'frm_continue_to_new', '__return_true', 20 );
	}

	/**
	 * Unset frmProForm.changeRte function that is here by default.
	 * When Pro scripts are not loaded TinyMCE triggers a "TypeError: callback is undefined" error on init.
	 *
	 * @since 6.0
	 *
	 * @return void
	 */
	private function remove_tinymce_init_callback() {
		add_filter(
			'frm_rte_options',
			function( $options ) {
				$options['tinymce']['init_instance_callback'] = '';
				return $options;
			}
		);
	}

	/**
	 * Normally we only load the fields for a single page at a time.
	 * In the visual styler, we load every field in the form instead.
	 *
	 * @since 6.0
	 *
	 * @return void
	 */
	private function force_all_fields_on_page() {
		add_filter( 'frm_hide_fields_on_other_pages', '__return_false' );
	}

	/**
	 * Make sure collapsible sections are not all in the default closed state.
	 * As scripts are not loaded, trying to toggle a collapsible section does nothing.
	 *
	 * @since 6.0
	 *
	 * @return void
	 */
	private function open_collapsible_sections() {
		add_filter( 'frm_section_is_open', '__return_true' );
	}

	/**
	 * Unset a form's status setting so the preview doesn't display the form as closed.
	 *
	 * @since 6.0
	 *
	 * @return void
	 */
	private function clear_form_open_status() {
		add_filter(
			'frm_form_object',
			function( $form ) {
				$form->options['open_status'] = '';
				return $form;
			}
		);
	}

	/**
	 * Force look up fields without required data to appear in the styler preview.
	 *
	 * @since 6.0
	 * @todo What about "text" "Single Line Text" type?
	 *
	 * @return void
	 */
	private function change_lookup_field_markup() {
		add_filter(
			'frm_before_replace_shortcodes',
			/**
			 * @param string         $html
			 * @param stdClass|array $field
			 * @return string
			 */
			function( $html, $field ) {
				if ( ! is_array( $field ) || 'lookup' !== $field['type'] || empty( $field['watch_lookup'] ) ) {
					// Skip any non-look up, or any look-up that isn't watching a lookup.
					return $html;
				}

				$options = FrmProLookupFieldsController::get_independent_lookup_field_options( $field );

				switch ( $field['data_type'] ) {
					case 'data':
						if ( empty( $field['value'] ) ) {
							// Show the first option as the value rather than showing nothing.
							$field['value'] = reset( $options );
						}
						break;

					case 'select':
					case 'radio':
					case 'checkbox':
						// Show all options, ignoring filtering from watched lookups.
						$field['options'] = $options;
						break;
				}

				ob_start();
				FrmProLookupFieldsController::get_front_end_lookup_field_html( $field, '', '' );
				$lookup_html = ob_get_clean();
				$html        = str_replace( '[input]', $lookup_html, $html );

				return $html;
			},
			10,
			2
		);
	}

	/**
	 * Since we do not have form data in the styler preview, there isn't anything to display for a summary field.
	 * Since summary fields are not a styling target, hide it from the preview so the field label doesn't appear.
	 *
	 * @since 6.0
	 *
	 * @return void
	 */
	private function hide_summary_fields() {
		$this->hide_field_type( 'summary' );
	}

	/**
	 * Hide all fields of a target type so they do not get included in the styler preview.
	 *
	 * @since 6.0
	 *
	 * @param string $target_field_type
	 * @return void
	 */
	private function hide_field_type( $target_field_type ) {
		add_filter(
			'frm_show_normal_field_type',
			/**
			 * @param bool   $show
			 * @param string $field_type
			 * @param string $target_field_type
			 * @return bool
			 */
			function( $show, $field_type ) use ( $target_field_type ) {
				if ( $target_field_type === $field_type ) {
					$show = false;
				}
				return $show;
			},
			10,
			2
		);
	}

	/**
	 * The other input is hidden by default.
	 * As the scripts are not loaded to toggle it on and off, just toggle it on so it can be previewed.
	 *
	 * @since 6.0
	 *
	 * @return void
	 */
	private function show_other_inputs() {
		add_filter(
			'frm_replace_shortcodes',
			/**
			 * Remove the frm_pos_none class from any frm_other_input inputs that match.
			 * This allows them to load visible as they are usually hidden by default.
			 *
			 * @param string         $html
			 * @param stdClass|array $field
			 * @return string
			 */
			function( $html, $field ) {
				if ( ! is_array( $field ) || empty( $field['other'] ) ) {
					return $html;
				}
				return str_replace( 'frm_other_input frm_pos_none', 'frm_other_input', $html );
			},
			10,
			2
		);
	}

	/**
	 * Prevent the style preview from showing a form as conversational.
	 *
	 * @since 6.0
	 *
	 * @return void
	 */
	private function disable_conversational_forms_in_style_preview() {
		add_filter(
			'frm_form_object',
			/**
			 * @param stdClass $form
			 * @return stdClass
			 */
			function( $form ) {
				$form->options['chat'] = 0;
				return $form;
			}
		);
	}

	/**
	 * @since 6.0
	 *
	 * @return void
	 */
	private function prevent_stripe_from_loading_in_style_preview() {
		remove_action( 'frm_entry_form', 'FrmStrpAuth::add_hidden_token_field' );
		remove_filter( 'frm_pro_show_card_callback', 'FrmStrpActionsController::show_card_callback' );
	}

	/**
	 * @since 6.0
	 *
	 * @return void
	 */
	private function prevent_payment_gateway_fields_from_rendering() {
		$this->hide_field_type( 'gateway' );
	}

}
