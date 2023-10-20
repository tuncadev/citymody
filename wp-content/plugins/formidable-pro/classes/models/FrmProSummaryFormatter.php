<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 4.03
 */
class FrmProSummaryFormatter {

	protected $form = null;

	protected $form_pages = null;

	protected $entry = null;

	/**
	 * @var FrmProSummaryValues
	 */
	protected $summary_values;

	protected $atts;

	/**
	 * @param int   $form_id
	 * @param array $summary_atts
	 */
	public function __construct( $form_id, $summary_atts ) {
		$this->init_form( $form_id );

		if ( $this->form === null || $this->form === false ) {
			return;
		}

		$this->init_summary_values( $summary_atts );
		$this->init_entry();
	}

	/**
	 * Set the form property
	 *
	 * @param int $form_id
	 */
	protected function init_form( $form_id ) {
		$this->form = FrmForm::getOne( $form_id );
	}

	protected function init_summary_values( $atts ) {
		$this->atts           = $this->prepare_summary_attributes( $atts );
		$this->summary_values = new FrmProSummaryValues( $this->form->id, $this->atts );
	}

	protected function prepare_summary_attributes( $atts ) {
		$defaults = array(
			'excluded_ids'   => array(),
			'excluded_types' => array(),
		);

		return wp_parse_args( $atts, $defaults );
	}

	private function init_entry() {
		$this->entry = $this->summary_values->get_fake_entry();
		$this->entry->form_id = $this->form->id;
	}

	private function get_page_link( $page_num ) {
		if ( ! isset( $this->form_pages ) ) {
			$this->form_pages = FrmProPageField::get_form_pages( $this->form );
			if ( empty( $this->form_pages ) ) {
				// very unlikely though
				return '';
			}
		}

		$page_data  = $this->form_pages['page_array'][ $page_num ];
		$data_page  = $page_data['data-page'];
		$data_field = $page_data['data-field'];
		$link_text = __( 'Edit', 'formidable-pro' );

		$class = $page_data['class'] . ' frm_page_' . $page_num;

		$link = sprintf(
			'<button type="button" data-page="%2$s" class="frm-edit-page-btn %3$s" data-field="%4$s">%5$s <span>%1$s</span></button>',
			esc_html( $link_text ),
			esc_attr( $data_page ),
			esc_attr( $class ),
			esc_attr( $data_field ),
			'<svg class="frm-icon frm-icon-pencil"><use xlink:href="#frm_pencil_icon"/></svg>'
		);

		return $link;
	}

	private function svg() { ?>
		<svg aria-hidden="true" style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
			<defs>
				<symbol id="frm_pencil_icon" viewBox="0 0 20 20">
					<path d="M19.7 2.2l-.8-1.1c-.6-.6-1.5-1-2.5-1S14.6.5 14 1L1.7 13.4a1 1 0 0 0-.3.4l-1.4 5a.9.9 0 0 0 0 .5 1 1 0 0 0 1.2.6l5-1.3.4-.3L18.9 6a3.5 3.5 0 0 0 .7-3.8zm-6.8 2.6L15.2 7l-8.6 8.7-2.4-2.4zm-10.7 13l1-3.3L5.4 17zM18 4.2l-.4.5L16.3 6 14 3.7l1.3-1.3A1.7 1.7 0 0 1 18 3.6l-.1.6z"/>
				</symbol>
			</defs>
		</svg>
	<?php
	}

	public function get_formatted_entry_values() {
		if ( ! $this->form ) {
			return '';
		}

		$content = '';

		$this->add_field_values_to_content( $content );

		return $content;
	}

	protected function add_field_values_to_content( &$content ) {
		$break_before_summary = $this->summary_values->get_break_before_summary();
		$breaks_found         = 0;

		$include_fields = array();

		$this->svg();

		foreach ( $this->summary_values->get_field_values() as $field_id => $field_value ) {

			if ( 'break' == $field_value->get_field_type() ) {
				if ( $field_value->get_field_attr( 'form_id' ) == $break_before_summary->form_id ) {
					// this is a break in the main form, so set currentpage & begin a new pg
					$this->summary_page( $content, ++$breaks_found, $include_fields );

					if ( $field_value->get_field_attr( 'field_order' ) == $break_before_summary->field_order ) {
						break;
					}
				}

				$include_fields = array();

				// we don't render a cell for page breaks
				continue;
			}

			if ( $field_value->get_field_attr( 'form_id' ) != $break_before_summary->form_id ) {
				// this is an inner field (in an embedded form or a repeater);
				// continue, the parent will handle its display more properly
				continue;
			}

			$include_fields[] = $field_id;
		}
	}

	protected function summary_page( &$content, $page_num, $include_fields ) {
		if ( empty( $include_fields ) ) {
			return;
		}

		$content .= '<div class="frm-summary-page-wrapper" data-pagenum="' . esc_attr( $page_num ) . '">';
		$content .= $this->get_page_link( $page_num );

		$this_page = FrmEntriesController::show_entry_shortcode(
			array(
				'entry'          => $this->entry,
				'fields'         => $this->summary_values->get_fields(),
				'inline_style'   => 0,
				'include_extras' => $this->include_extras(),
				'include_fields' => $include_fields,
				'class'          => 'frm-line-table',
				'show_image'     => true,
				'size'           => 'thumbnail',
				'summary'        => true,
				'exclude_fields' => $this->atts['excluded_ids'],
			)
		);

		$content .= $this_page;
		$content .= '</div>';
	}

	/**
	 * Only include the field types that are set to be included.
	 *
	 * @since 4.03.02
	 */
	private function include_extras() {
		$included = array( 'section', 'html', 'password' );
		$excluded = $this->atts['excluded_types'];
		$included = array_diff( $included, $excluded );
		return implode( ', ', $included );
	}

	/**
	 * Check if a field should be included in the content
	 *
	 * @param $field FrmProFieldSummaryValue
	 *
	 * @return bool
	 */
	protected function include_field_in_content( $field ) {
		$include = ! FrmField::is_no_save_field( $field->type );

		return apply_filters( 'frm_include_field_in_content', $include, $field );
	}
}
