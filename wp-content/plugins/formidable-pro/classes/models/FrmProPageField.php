<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProPageField {

	/**
	 * Hooks a function that shows progress/rootline bar into the appropriate action based on form setting.
	 *
	 * @param object $form
	 */
	public static function add_pagination_hook( $form ) {
		if ( empty( $form->options['pagination_position'] ) || FrmAppHelper::is_admin_page( 'formidable-entries' ) ) {
			// show progress/rootline just below the form title by default, and regardless of the setting for entry add/edit pages.
			add_action( 'frm_after_title', 'FrmProPageField::page_navigation' );
			return;
		}

		$form_position = $form->options['pagination_position'];

		switch ( $form_position ) {
			case 'above_title':
				add_action( 'frm_before_title', 'FrmProPageField::page_navigation' );
				break;
			case 'below_submit':
				add_action( 'frm_after_submit_btn', 'FrmProPageField::page_navigation' );
				break;
			case 'above_submit':
				add_action( 'frm_before_submit_btn', 'FrmProPageField::page_navigation' );
				break;
		}
	}

	public static function page_navigation( $atts ) {
		$setting_action = array(
			''             => 'frm_after_title',
			'above_title'  => 'frm_before_title',
			'below_submit' => 'frm_after_submit_btn',
			'above_submit' => 'frm_before_submit_btn',
		);

		$atts = shortcode_atts( array( 'id' => false, 'form' => false ), $atts );
		$form = $atts['form'];

		if ( FrmAppHelper::is_admin_page( 'formidable-entries' ) ) {
			// show progress/rootline on default position for entry add/edit pages.
			$form->options['pagination_position'] = '';
		}

		if ( ! is_object( $form ) ) {
			$form = FrmForm::getOne( $atts['id'] );
			$atts['id'] = $form->id;
		}

		$position = isset( $form->options['pagination_position'] ) ? $form->options['pagination_position'] : '';
		if ( current_action() !== $setting_action[ $position ] ) {
			return;
		}

		$show_progress = FrmForm::get_option( array( 'form' => $form, 'option' => 'rootline', 'default' => '' ) );
		if ( empty( $show_progress ) ) {
			return;
		}

		$page_breaks = FrmProFormsHelper::has_field( 'break', $form->id, false );
		if ( ! $page_breaks ) {
			return;
		}

		$page_array = self::get_pages_array( $page_breaks, $form );

		self::show_progress( compact( 'page_array', 'form' ) );
	}

	private static function get_pages_array( $page_breaks, $form ) {
		global $frm_vars;
		$page_order = isset( $frm_vars['prev_page'][ $form->id ] ) ? $frm_vars['prev_page'][ $form->id ] : 0;

		$page_number = 1;
		$current_page = 0;
		$field_id = 0;
		$page_array = array();

		foreach ( $page_breaks as $page_break ) {
			if ( FrmProFieldsHelper::is_field_hidden( $page_break, wp_unslash( $_POST ) ) ) {
				continue;
			}

			if ( $page_break->field_order <= $page_order ) {
				// going back
				$page_array[ $page_number ] = array(
					'data-page'  => $page_break->field_order,
					'class'      => 'frm_page_back',
					'formnovalidate' => 'formnovalidate',
					'data-field' => $field_id,
				);
			} elseif ( $page_break->field_order > $page_order && $current_page == 0 ) {
				// show current page
				$page_array[ $page_number ] = array(
					'data-page'  => '',
					'class'      => '',
					'aria-disabled'   => 'true',
					'data-field' => $field_id,
				);
				$current_page = $page_number;
			}

			$field_id = $page_break->id;
			$page_number++;

			if ( $page_break->field_order > $page_order ) {
				// going forward
				$page_array[ $page_number ] = array(
					'data-page'  => $page_break->field_order,
					'class'      => 'frm_page_skip',
					'data-field' => $field_id,
				);
			}
		}

		if ( $current_page == 0 ) {
			// show current page if last
			$page_array[ $page_number ] = array(
				'data-page'     => '',
				'class'         => '',
				'aria-disabled' => 'true',
				'data-field'    => $field_id,
			);
		}

		self::add_titles_to_array( $form, $page_array );

		return apply_filters( 'frm_rootline_pages', $page_array, compact( 'page_breaks', 'form', 'current_page', 'page_order' ) );
	}

	/**
	 * Show the rootline or progress element.
	 *
	 * @param array $args
	 * @return void
	 */
	private static function show_progress( $args ) {
		$hide_lines   = FrmForm::get_option( array( 'form' => $args['form'], 'option' => 'rootline_lines_off', 'default' => 0 ) );
		$show_titles  = FrmForm::get_option( array( 'form' => $args['form'], 'option' => 'rootline_titles_on', 'default' => 0 ) );
		$hide_numbers = FrmForm::get_option( array( 'form' => $args['form'], 'option' => 'rootline_numbers_off', 'default' => 0 ) );
		$type         = FrmForm::get_option( array( 'form' => $args['form'], 'option' => 'rootline', 'default' => '' ) );

		$title_atts = compact( 'show_titles', 'type' );

		$page_count = count( $args['page_array'] );

		$classes = array( 'frm_page_bar', 'frm_rootline_' . $page_count, 'frm_' . $type, 'frm_' . $type . '_line' );
		$classes[] = $hide_numbers ? 'frm_no_numbers' : '';
		$classes[] = $hide_lines ? '' : 'frm_show_lines';
		$classes[] = $show_titles ? 'frm_show_titles' : '';
		$current_page = array_filter(
			$args['page_array'],
			function( $page ) {
				return isset( $page['aria-disabled'] );
			}
		);

		$keys = array_keys( $current_page );
		$current_page = reset( $keys );

		$output = '<div class="frm_rootline_group" role="group" tabindex="0" aria-label="' . sprintf( __( 'Page %1$s of %2$s', 'formidable-pro' ), $current_page, $page_count ) . '" >';
		$output .= '<ul class="' . esc_attr( implode( ' ', $classes ) ) . '">';

		$current_page = 0;
		$page_numbers     = array_keys( $args['page_array'] );
		$last_page_number = $page_numbers[ count( $page_numbers ) - 1 ];

		foreach ( $args['page_array'] as $page_number => $page ) {
			if ( $last_page_number === $page_number && count( $page_numbers ) > 3 && $type === 'rootline' ) {
				// Add a ... option before the last option.
				$output .= self::get_rootline_more_option();
			}

			$page['class'] .= ' frm_page_' . $page_number;
			$current_class  = ( isset( $page['aria-disabled'] ) ) ? ' frm_current_page' : '';
			$output        .= '<li class="frm_rootline_single' . $current_class . '">';

			$title_atts['title'] = $page['aria-label'];
			$title_atts['position'] = 'before';
			$output .= self::maybe_get_progress_title( $title_atts );

			$output .= '<input type="button" value="' . esc_attr( $page_number ) . '" ';
			foreach ( $page as $key => $attr ) {
				$output .= $key . '="' . esc_attr( $attr ) . '" ';
			}
			$output .= ' />';

			$title_atts['position'] = 'after';
			$output .= self::maybe_get_progress_title( $title_atts );

			$output .= '</li>';

			if ( isset( $page['aria-disabled'] ) ) {
				$current_page = $page_number;
			}
		}
		$output .= '</ul>';

		if ( ! $hide_numbers && $type === 'progress' ) {
			$percent_complete = self::percent_complete( $current_page, $args['page_array'] );
			$output .= '<div class="frm_percent_complete">' . sprintf( __( '%s Complete', 'formidable-pro' ), $percent_complete ) . '</div>';
			$output .= '<div class="frm_pages_complete">' . self::pages_complete( $current_page, $args['page_array'] ) . '</div>';
		}
		$output .= '<div class="frm_clearfix"></div>';
		$output .= '</div>';

		echo $output;
		self::maybe_load_style();
	}

	/**
	 * @since 6.0
	 *
	 * @return string
	 */
	private static function get_rootline_more_option() {
		ob_start();
		?>
		<li class="frm_rootline_single frm_rootline_show_hidden_steps_btn frm_hidden">
			<input type="button" class="frm_rootline_show_more_btn" value="···" />
			<ul class="frm_rootline_hidden_steps frm_hidden"></ul>
		</li>
		<?php
		$html = ob_get_clean();
		return $html;
	}

	private static function maybe_get_progress_title( $atts ) {
		$title = '';
		if ( $atts['show_titles'] ) {

			$show_before = ( $atts['type'] == 'progress' && $atts['position'] == 'before' );
			$show_after = ( $atts['type'] == 'rootline' && $atts['position'] == 'after' );
			if ( $show_before || $show_after ) {
				$title = self::get_progress_title( $atts['title'] );
			}
		}
		return $title;
	}

	private static function get_progress_title( $title ) {
		return '<span class="frm_rootline_title">' . strip_tags( $title ) . '</span>';
	}

	private static function get_title_for_page( $atts ) {
		$field_id = $atts['page']['data-field'];
		$default_title = sprintf( __( 'Page %d', 'formidable-pro' ), $atts['page_number'] );
		$title = isset( $atts['page_titles'][ $field_id ] ) ? $atts['page_titles'][ $field_id ] : $default_title;

		return $title;
	}

	private static function add_titles_to_array( $form, &$page_array ) {
		$page_titles = FrmForm::get_option(
			array(
				'form'    => $form,
				'option'  => 'rootline_titles',
				'default' => array(),
			)
		);

		foreach ( $page_array as $page_number => $page ) {
			$page_array[ $page_number ]['aria-label'] = self::get_title_for_page(
				array(
					'page'        => $page,
					'page_number' => $page_number,
					'page_titles' => $page_titles,
				)
			);
		}
	}

	private static function pages_complete( $current_page, $page_array ) {
		return sprintf( __( '%1$d of %2$d', 'formidable-pro' ), $current_page, count( $page_array ) );
	}

	private static function percent_complete( $current_page, $page_array ) {
		$percent = ( ( $current_page - 1 ) / count( $page_array ) ) * 100;
		return round( $percent ) . '%';
	}

	/**
	 * Load progress bar style for the admin entry page
	 */
	private static function maybe_load_style() {
		if ( ! FrmAppHelper::is_admin_page('formidable-entries') ) {
			return;
		}

		$frm_style = new FrmStyle();
		$default_style = $frm_style->get_default_style();
		$defaults = FrmStylesHelper::get_settings_for_output( $default_style );

		echo '<style type="text/css">';
		include( FrmProAppHelper::plugin_path() . '/css/progress.css.php' );
		echo '</style>';
	}

	/**
	 * @since 4.03
	 *
	 * @return array
	 */
	public static function get_form_pages( $form ) {
		if ( ! is_object( $form ) ) {
			$form = FrmForm::getOne( $form );
		}

		$page_breaks = FrmProFormsHelper::has_field( 'break', $form->id, false );
		if ( empty( $page_breaks ) ) {
			return array();
		}

		$rootline = FrmForm::get_option(
			array(
				'form'    => $form,
				'option'  => 'rootline',
				'default' => '',
			)
		);

		$page_array = self::get_pages_array( $page_breaks, $form );

		return compact( 'rootline', 'page_array' );
	}
}
