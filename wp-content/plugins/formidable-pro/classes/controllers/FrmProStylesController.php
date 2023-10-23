<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProStylesController extends FrmStylesController {

	/**
	 * @return void
	 */
	public static function load_pro_hooks() {
		if ( ! FrmAppHelper::is_admin_page( 'formidable-styles' ) && ! FrmAppHelper::is_admin_page( 'formidable-styles2' ) ) {
			return;
		}

		// Filters.
		add_filter( 'frm_style_head', 'FrmProStylesController::maybe_new_style' );
		add_filter( 'frm_style_action_route', 'FrmProStylesController::pro_route' );
		add_filter( 'frm_saved_form_style_id', 'FrmProStylesController::maybe_import_style_template' );

		// Actions.
		add_action( 'frm_sample_style_form', 'FrmProStylesController::append_style_form' );
		add_action( 'admin_enqueue_scripts', 'FrmProAppController::load_style_manager_js_assets' );
		add_action( 'frm_style_settings_input_atts', 'FrmProStylesController::echo_style_settings_input_atts' );
		add_action( 'frm_style_settings_general_section_after_background', 'FrmProStylesController::echo_bg_image_settings', 10 );
		add_action( 'frm_style_settings_general_section_after_background', 'FrmProStylesController::echo_additional_background_image_settings', 20 );
		add_action( 'frm_style_preview_after_toggle', 'FrmProStylesController::preview_after_toggle' );

		self::prevent_wordpress_datepicker_localization();
		self::prevent_additional_datepicker_scripts();
		self::admin_css();
	}

	/**
	 * Disable the back end datepicker localization in the styler preview.
	 * When this loads, datepicker show with different settings than they do in the front end.
	 * The first day of the week for example may show as "M" instead of "Su".
	 *
	 * @since 6.0
	 *
	 * @return void
	 */
	private static function prevent_wordpress_datepicker_localization() {
		remove_action( 'admin_enqueue_scripts', 'wp_localize_jquery_ui_datepicker', 1000 );
	}

	/**
	 * Make sure that additional datepicker scipts don't load to avoid a "Uncaught ReferenceError: frmProForm is not defined" error.
	 *
	 * @since 6.0
	 *
	 * @return void
	 */
	private static function prevent_additional_datepicker_scripts() {
		add_action(
			'admin_print_footer_scripts',
			function() {
				remove_action( 'frm_date_field_js', 'FrmProFieldsController::date_field_js' );
			},
			30
		);
	}

	/**
	 * @return void
	 */
	private static function admin_css() {
		if ( FrmAppHelper::doing_ajax() ) {
			return;
		}

		$version = FrmAppHelper::plugin_version();
		wp_register_style( 'formidable-pro-style-settings', FrmProAppHelper::plugin_url() . '/css/settings/style-settings.css', array(), $version );
		wp_enqueue_style( 'formidable-pro-style-settings' );
	}

	/**
	 * Add additional style settings for Pro fields.
	 *
	 * @param array $boxes
	 * @return array
	 */
	public static function add_style_boxes( $boxes ) {
		$add_boxes = array(
			'section-fields'  => __( 'Section Fields', 'formidable-pro' ),
			'repeater-fields' => __( 'Repeater Fields', 'formidable-pro' ),
			'date-fields'     => __( 'Date Fields', 'formidable-pro' ),
			'toggle-fields'   => __( 'Toggle Fields', 'formidable-pro' ),
			'slider-fields'   => __( 'Slider Fields', 'formidable-pro' ),
			'progress-bars'   => __( 'Progress Bars &amp; Rootline', 'formidable-pro' ),
		);
		$boxes     = array_merge( $boxes, $add_boxes );

		foreach ( $add_boxes as $label => $name ) {
			add_filter( 'frm_style_settings_' . $label, 'FrmProStylesController::style_box_file' );
		}

		return $boxes;
	}

	/**
	 * @since 3.01.01
	 *
	 * @param string $f
	 * @return string
	 */
	public static function style_box_file( $f ) {
		$path = explode( '/views/styles/', $f );
		return self::view_folder() . '/' . $path[1];
	}

	/**
	 * @since 3.03
	 *
	 * @return array
	 */
	public static function jquery_themes( $selected_style = 'none' ) {
		$themes = self::get_date_themes( $selected_style );

		/**
		 * @param array $themes
		 */
		return apply_filters( 'frm_jquery_themes', $themes );
	}

	/**
	 * @since 3.03
	 *
	 * @return array
	 */
	private static function get_date_themes( $selected_style = 'none' ) {
		if ( self::use_default_style( $selected_style ) ) {
			return array(
				'ui-lightness' => 'Default',
			);
		}

		$themes = array(
			'ui-lightness'  => 'Default',
			'ui-darkness'   => 'UI Darkness',
			'smoothness'    => 'Smoothness',
			'start'         => 'Start',
			'redmond'       => 'Redmond',
			'sunny'         => 'Sunny',
			'overcast'      => 'Overcast',
			'le-frog'       => 'Le Frog',
			'flick'         => 'Flick',
			'pepper-grinder' => 'Pepper Grinder',
			'eggplant'      => 'Eggplant',
			'dark-hive'     => 'Dark Hive',
			'cupertino'     => 'Cupertino',
			'south-street'  => 'South Street',
			'blitzer'       => 'Blitzer',
			'humanity'      => 'Humanity',
			'hot-sneaks'    => 'Hot Sneaks',
			'excite-bike'   => 'Excite Bike',
			'vader'         => 'Vader',
			'dot-luv'       => 'Dot Luv',
			'mint-choc'     => 'Mint Choc',
			'black-tie'     => 'Black Tie',
			'trontastic'    => 'Trontastic',
			'swanky-purse'  => 'Swanky Purse',
			'-1'            => 'None',
		);

		return $themes;
	}

	/**
	 * @since 3.03
	 */
	public static function jquery_css_url( $theme_css ) {
		if ( $theme_css == -1 ) {
			return;
		}

		if ( self::use_default_style( $theme_css ) ) {
			$css_file = FrmProAppHelper::plugin_url() . '/css/ui-lightness/jquery-ui.css';
		} elseif ( preg_match( '/^http.?:\/\/.*\..*$/', $theme_css ) ) {
			$css_file = $theme_css;
		} else {
			$uploads = FrmStylesHelper::get_upload_base();
			$file_path = '/formidable/css/' . $theme_css . '/jquery-ui.css';
			if ( file_exists( $uploads['basedir'] . $file_path ) ) {
				$css_file = $uploads['baseurl'] . $file_path;
			} else {
				$css_file = FrmProAppHelper::jquery_ui_base_url() . '/themes/' . $theme_css . '/jquery-ui.min.css';
			}
		}

		return $css_file;
	}

	/**
	 * @since 3.03
	 *
	 * @return bool
	 */
	private static function use_default_style( $selected ) {
		return empty( $selected ) || 'ui-lightness' === $selected;
	}

	/**
	 * Load jQuery UI CSS. This is important for datepicker styling.
	 *
	 * @since 3.03
	 *
	 * @return void
	 */
	public static function enqueue_jquery_css() {
		$form       = self::get_form_for_page();
		$theme_css  = FrmStylesController::get_style_val( 'theme_css', $form );
		$action     = FrmAppHelper::get_param( 'frm_action', '', 'get', 'sanitize_text_field' );
		$is_builder = FrmAppHelper::is_admin_page( 'formidable' ) && $action !== 'settings' && ! FrmAppHelper::is_admin_page( 'formidable-styles' );

		if ( $theme_css != -1 && ! $is_builder ) {
			// Without this line, datepickers load without proper styling in the Form Scheduling settings when you set the form status dropdown to "Schedule".
			wp_enqueue_style( 'jquery-theme', self::jquery_css_url( $theme_css ), array(), FrmAppHelper::plugin_version() );
		}
	}

	/**
	 * @since 3.03
	 *
	 * @return string
	 */
	private static function get_form_for_page() {
		global $frm_vars;
		$form_id = 'default';
		if ( ! empty( $frm_vars['forms_loaded'] ) ) {
			foreach ( $frm_vars['forms_loaded'] as $form ) {
				if ( is_object( $form ) ) {
					$form_id = $form->id;
					break;
				}
			}
		}
		return $form_id;
	}

	/**
	 * @param array $atts
	 * @return void
	 */
	public static function append_style_form( $atts ) {
		$style     = $atts['style'];
		$pos_class = $atts['pos_class'];
		include self::view_folder() . '/_sample_form.php';
	}

	public static function maybe_new_style( $style ) {
		$action = FrmAppHelper::get_param( 'frm_action', '', 'get', 'sanitize_title' );
		if ( 'new_style' === $action ) {
			$style = self::new_style( 'style' );
		} else if ( 'duplicate' === $action ) {
			$style = self::duplicate( 'style' );
		}
		return $style;
	}

	/**
	 * Handle the new style route. This doesn't create a new style, just an object with all defaults and no ID to use in the styler.
	 *
	 * @param string $return $return If 'style', the style will be returned and nothing will be echoed. Another value will return nothing, and will render the styler.
	 * @return stdClass|void
	 */
	public static function new_style( $return = '' ) {
		$frm_style = new FrmStyle();
		$style     = $frm_style->get_new();

		if ( 'style' === $return ) {
			// Return style object for header css link.
			return $style;
		}

		self::load_styler( $style ); // As of 6.0 load_style does not actually use any parameters. This is only passed for legacy versions.
	}

	/**
	 * Handle the duplicate route. This doesn't actually duplicate a style, it just creates an unsaved copy to use in the styler.
	 *
	 * @param string $return If 'style', the style will be returned and nothing will be echoed. Another value will return nothing, and will render the styler.
	 *                       'style' is used from the frm_style_head hook to copy the CSS for the target style we're copying.
	 * @return stdClass|void
	 */
	public static function duplicate( $return = '' ) {
		$style_id = FrmAppHelper::get_param( 'style_id', 0, 'get', 'absint' );

		if ( ! $style_id ) {
			self::new_style( $return );
			return;
		}

		$frm_style = new FrmProStyle();
		$style     = $frm_style->duplicate( $style_id );

		if ( 'style' === $return ) {
			// return style object for header css link
			return $style;
		}

		self::load_styler( $style ); // As of 6.0 load_style does not actually use any parameters. This is passed for legacy versions.
	}

	/**
	 * Destroy style by ID via an AJAX action.
	 *
	 * @return void
	 */
	public static function destroy() {
		$permission_error = FrmAppHelper::permission_nonce_error( 'frm_edit_forms', 'nonce', 'frm_ajax' );
		if ( $permission_error !== false ) {
			$data = array(
				'message' => __( 'Unable to delete style', 'formidable-pro' ),
			);
			wp_send_json_error( $data, 403 );
			die();
		}

		$id = FrmAppHelper::get_post_param( 'id', 0, 'absint' );
		if ( ! $id ) {
			$data = array(
				'message' => __( 'Missing style ID', 'formidable-pro' ),
			);
			wp_send_json_error( $data, 400 );
			die();
		}

		$frm_style = new FrmStyle();
		$deleted   = $frm_style->destroy( $id );

		if ( ! $deleted ) {
			$data = array(
				'message' => __( 'Style not deleted', 'formidable-pro' ),
			);
			wp_send_json_error( $data, 400 );
			die();
		}

		$frm_style->save_settings(); // Sync the CSS and transient after deleting the style.

		$data = array(
			'message' => __( 'Your styling settings have been deleted.', 'formidable-pro' ),
		);
		wp_send_json_success( $data );
		die();
	}

	public static function pro_route( $action ) {
		switch ( $action ) {
			case 'new_style':
			case 'duplicate':
				add_filter( 'frm_style_stop_action_route', '__return_true' );
				return self::$action();
		}
	}

	/**
	 * @param array $args {
	 *     @type array $defaults
	 * }
	 * @return void
	 */
	public static function include_front_css( $args ) {
		$defaults  = $args['defaults'];
		$important = self::is_important( $defaults );
		$vars      = self::css_vars();

		self::maybe_include_icon_font_css();
		include FrmProAppHelper::plugin_path() . '/css/pro_fields.css.php';
		include FrmProAppHelper::plugin_path() . '/css/chosen.css.php';
		include FrmProAppHelper::plugin_path() . '/css/dropzone.css';
		include FrmProAppHelper::plugin_path() . '/css/progress.css.php';
	}

	/**
	 * Encodes image as a base64 data.
	 *
	 * @since 6.4.3
	 *
	 * @param string $file File path.
	 * @param string $mime_type
	 * @return string
	 */
	public static function base64_encode_image( $file, $mime_type = '' ) {
		if ( ! $file || ! file_exists( $file ) ) {
			return '';
		}

		if ( ! $mime_type ) {
			$mime_type = FrmProAppHelper::get_mime_content_type( $file );
			if ( ! $mime_type ) {
				return '';
			}
		}

		$file_content = file_get_contents( $file );
		if ( ! $file_content ) {
			return '';
		}

		return 'data:' . $mime_type . ';base64,' . base64_encode( $file_content );
	}

	/**
	 * Maybe read the font icons CSS when including additional CSS for the front end.
	 * As of v6.4 this is now only required for old versions of the signatures add on.
	 * Signatures v3.0.4 no longer requires the font icons either.
	 *
	 * @return void
	 */
	private static function maybe_include_icon_font_css() {
		$signature_add_on_is_active = class_exists( 'FrmSigAppHelper', false );
		if ( ! $signature_add_on_is_active ) {
			return;
		}

		if ( is_callable( 'FrmSigAppHelper::get_svg_icon' ) ) {
			// This is no longer required in newer versions of the signatures add on.
			return;
		}

		readfile( FrmAppHelper::plugin_path() . '/css/font_icons.css' );
	}

	/**
	 * @since 3.01.01
	 */
	public static function add_defaults( $settings ) {
		self::set_toggle_slider_colors( $settings );
		self::set_toggle_date_colors( $settings );
		self::set_bg_image_settings( $settings );
		return $settings;
	}

	/**
	 * @since 3.01.01
	 */
	public static function override_defaults( $settings ) {
		if ( ! isset( $settings['toggle_on_color'] ) && isset( $settings['progress_active_bg_color'] ) ) {
			self::set_toggle_slider_colors( $settings );
		}

		if ( ! isset( $settings['date_head_bg_color'] ) && isset( $settings['progress_active_bg_color'] ) ) {
			self::set_toggle_date_colors( $settings );
		}

		return $settings;
	}

	/**
	 * @since 3.01.01
	 */
	private static function set_toggle_slider_colors( &$settings ) {
		$settings['toggle_font_size'] = $settings['font_size'];
		$settings['toggle_on_color']  = $settings['progress_active_bg_color'];
		$settings['toggle_off_color'] = $settings['progress_bg_color'];

		$settings['slider_font_size'] = '24px';
		$settings['slider_color']     = $settings['progress_active_bg_color'];
		$settings['slider_bar_color'] = $settings['progress_active_bg_color'];
	}

	/**
	 * @since 3.03
	 */
	private static function set_toggle_date_colors( &$settings ) {
		$settings['date_head_bg_color'] = $settings['progress_active_bg_color'];
		$settings['date_head_color']    = $settings['progress_active_color'];
		$settings['date_band_color']    = FrmStylesHelper::adjust_brightness( $settings['progress_active_bg_color'], -50 );
	}

	/**
	 * @since 5.0.08
	 */
	private static function set_bg_image_settings( &$settings ) {
		$settings['bg_image_id']      = '';
		$settings['bg_image_opacity'] = '100%';
	}

	/**
	 * This CSS is only loaded with the ajax call.
	 *
	 * @since 3.0
	 * @return void
	 */
	public static function include_pro_fields_ajax_css() {
		header( 'Content-type: text/css' );

		$defaults  = self::get_default_style();
		$important = self::is_important( $defaults );

		$vars = self::css_vars();
		if ( is_callable( 'FrmStylesHelper::get_css_vars' ) ) {
			$vars = FrmStylesHelper::get_css_vars( array_keys( $defaults ) );
		}

		include FrmProAppHelper::plugin_path() . '/css/pro_fields.css.php';
	}

	/**
	 * @param array $settings
	 * @return void
	 */
	public static function output_single_style( $settings ) {
		$important = self::is_important( $settings );

		// calculate the top position based on field padding
		$top_pad    = explode( ' ', $settings['field_pad'] );
		$top_pad    = reset( $top_pad ); // the top padding is listed first
		$pad_unit   = preg_replace( '/[0-9]+/', '', $top_pad ); //px, em, rem...
		$top_margin = (int) str_replace( $pad_unit, '', $top_pad ) / 2;
		$defaults   = self::get_default_style();
		$vars       = self::css_vars();

		list( $bg_image_url, $bg_image_opacity ) = self::get_bg_image_vars( $settings );

		include FrmProAppHelper::plugin_path() . '/css/single-style.css.php';
	}

	/**
	 * Get variables for background image URL and opacity to use in the stylesheet CSS.
	 *
	 * @param array $settings {
	 *     @type string|null $bg_image_id
	 *     @type string|null $bg_image_opacity Supports '50%' percent values as well as '0.5' float values.
	 *                                         An empty string will be treating as a 1 (full opacity).
	 *                                         A '0' has no opacity so it will not be visible.
	 *                                         Any unexpected negative value will use 0 instead.
	 * }
	 * @return array<string|float|false>
	 */
	private static function get_bg_image_vars( $settings ) {
		$bg_image_url     = false;
		$bg_image_opacity = false;

		if ( empty( $settings['bg_image_id'] ) ) {
			return array( $bg_image_url, $bg_image_opacity );
		}

		$bg_image_url = wp_get_attachment_url( $settings['bg_image_id'] );
		if ( false === $bg_image_url || ( empty( $settings['bg_image_opacity'] ) && '0' !== $settings['bg_image_opacity'] ) ) {
			return array( $bg_image_url, $bg_image_opacity );
		}

		$bg_image_opacity     = $settings['bg_image_opacity'];
		$has_trailing_percent = '%' === $bg_image_opacity[ strlen( $bg_image_opacity ) - 1 ];

		if ( $has_trailing_percent ) {
			// Remove a trailing % and use the whole value.
			$bg_image_opacity = substr( $bg_image_opacity, 0, strlen( $bg_image_opacity ) - 1 );

			// Divide the percent value by 100.
			if ( is_numeric( $bg_image_opacity ) ) {
				$bg_image_opacity = floatval( $bg_image_opacity ) / 100;
			}
		}

		if ( is_numeric( $bg_image_opacity ) ) {
			if ( $bg_image_opacity < 0 ) {
				$bg_image_opacity = 0;
			}
			if ( ! is_float( $bg_image_opacity ) ) {
				$bg_image_opacity = floatval( $bg_image_opacity );
			}
		} else {
			$bg_image_opacity = false;
		}

		return array( $bg_image_url, $bg_image_opacity );
	}

	/**
	 * @since 4.05
	 *
	 * @return array
	 */
	private static function get_default_style() {
		$frm_style     = new FrmStyle();
		$default_style = $frm_style->get_default_style();
		if ( is_admin() && ! empty( $_POST ) && isset( $_POST['action'] ) && $_POST['action'] === 'frm_change_styling' ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			// Reset to prevent posted values from being used on styler page.
			$_POST['action'] = '';
		}
		return FrmStylesHelper::get_settings_for_output( $default_style );
	}

	/**
	 * This is here for version mismatch. It can be removed later.
	 *
	 * @since 4.05
	 *
	 * @return array
	 */
	private static function css_vars() {
		if ( is_callable( 'FrmStylesHelper::get_css_vars' ) ) {
			return array();
		}

		$vars = array( 'progress_color', 'progress_bg_color', 'progress_active_bg_color', 'progress_border_size', 'border_color', 'border_radius', 'field_border_width', 'field_border_style', 'field_font_size', 'field_margin', 'text_color', 'field_pad', 'bg_color', 'submit_text_color', 'submit_font_size', 'border_color_error', 'text_color_error', 'bg_color_error', 'description_color', 'slider_font_size', 'slider_bar_color', 'section_border_width', 'section_border_style', 'section_border_color', 'section_font_size', 'section_bg_color', 'section_color', 'section_weight', 'toggle_off_color', 'toggle_on_color', 'toggle_font_size', 'check_weight', 'check_label_color' );
		return array_unique( $vars );
	}

	/**
	 * @return string
	 */
	private static function view_folder() {
		return FrmProAppHelper::plugin_path() . '/classes/views/styles';
	}

	/**
	 * @param array $settings
	 * @return string
	 */
	private static function is_important( $settings ) {
		return ! empty( $settings['important_style'] ) ? ' !important' : '';
	}

	/**
	 * Called when the frm_style_settings_general_section_after_background action is triggered the first time in the pro plugin.
	 *
	 * @since 5.0.08
	 *
	 * @param array $args with keys 'frm_style', 'style'.
	 */
	public static function echo_bg_image_settings( $args ) {
		$style = $args['style'];

		if ( ! empty( $style->post_content['bg_image_id'] ) ) {
			$bg_image_id       = absint( $style->post_content['bg_image_id'] );
			$bg_image          = wp_get_attachment_image( $bg_image_id );
			$bg_image_filepath = get_attached_file( $bg_image_id );
			$bg_image_filename = basename( $bg_image_filepath );
		} else {
			$bg_image_id       = 0;
			$bg_image          = '<img src="" class="frm_hidden" />';
			$bg_image_filepath = '';
			$bg_image_filename = '';
		}

		include self::view_folder() . '/_bg-image.php';
	}

	/**
	 * Called when the frm_style_settings_general_section_after_background action is triggered the second time in the pro plugin.
	 *
	 * @since 5.0.08
	 *
	 * @param array $args with keys 'frm_style', 'style'.
	 */
	public static function echo_additional_background_image_settings( $args ) {
		$style            = $args['style'];
		$hidden           = empty( $style->post_content['bg_image_id'] );
		$class            = $hidden ? 'frm_hidden ' : '';
		$class           .= 'frm_bg_image_additional_settings';
		$bg_image_opacity = isset( $style->post_content['bg_image_opacity'] ) ? $style->post_content['bg_image_opacity'] : '100%';
		include self::view_folder() . '/_bg-image-settings.php';
	}

	/**
	 * Called when the frm_style_settings_input_atts action is triggered.
	 *
	 * @since 5.0.08
	 */
	public static function echo_style_settings_input_atts( $key ) {
		if ( self::is_colorpicker( $key ) ) {
			// Support alpha in color pickers in pro style settings.
			self::echo_alpha_colorpicker_atts();
		}
	}

	/**
	 * Determine if input is a colorpicker type based on key name.
	 *
	 * @since 5.0.08
	 *
	 * @param string $key
	 * @return bool
	 */
	private static function is_colorpicker( $key ) {
		if ( in_array( $key, array( 'error_bg', 'error_border', 'error_text' ), true ) ) {
			return true;
		}
		return '_color' === substr( $key, -6 ) || '_color_error' === substr( $key, -12 ) || '_color_active' === substr( $key, -13 ) || '_color_disabled' === substr( $key, -15 );
	}

	/**
	 * @since 5.0.08
	 */
	private static function echo_alpha_colorpicker_atts() {
		echo 'data-alpha-color-type="rgba" data-alpha-enabled="true"';
	}

	/**
	 * Initialize Pro features for the Style page (including New Style).
	 *
	 * @since 6.0
	 *
	 * @param array $args {
	 *     @type stdClass $form
	 * }
	 * @return void
	 */
	public static function before_render_style_page( $args ) {
		$form           = $args['form'];
		$preview_helper = new FrmProStylesPreviewHelper();

		$preview_helper->adjust_form_for_preview();
		self::force_jquery_css_for_styler( $form );

		add_filter( 'frm_style_card_params', 'FrmProStylesController::filter_style_card_params', 10, 2 );
	}

	/**
	 * Add additional params for the style card HTML element.
	 * This includes data-duplicate-url, which is required for duplicating a style which is only supported in Pro.
	 *
	 * @since 6.0
	 *
	 * @param array $params
	 * @param array $args
	 * @return array
	 */
	public static function filter_style_card_params( $params, $args ) {
		$style                        = $args['style'];
		$params['data-duplicate-url'] = esc_url( admin_url( 'admin.php?page=formidable-styles&frm_action=duplicate&style_id=' . $style->ID ) );
		return $params;
	}

	/**
	 * Force jQuery UI CSS for the styler page so the datepicker sample doesn't look broken.
	 *
	 * @since 6.0
	 *
	 * @param stdClass $form
	 * @return void
	 */
	private static function force_jquery_css_for_styler( $form ) {
		// Temporarily set the form to forms_loaded array so self::get_form_for_page works. Then unset it.
		global $frm_vars;
		$temporary_key                              = 'styler';
		$frm_vars['forms_loaded'][ $temporary_key ] = $form;
		self::enqueue_jquery_css();
		unset( $frm_vars['forms_loaded'][ $temporary_key ] );
	}

	/**
	 * Initialize Dropzone so file fields in the preview don't just appear as type="file" input fields.
	 *
	 * @since 6.0
	 *
	 * @return void
	 */
	private static function initialize_dropzone() {
		FrmProAppController::register_js( 'dropzone', FrmProAppController::get_dropzone_js_details() );
		wp_enqueue_script( 'dropzone' );
	}

	/**
	 * Handle routing for frm_set_style_as_default AJAX action.
	 *
	 * @since 6.0
	 *
	 * @return void
	 */
	public static function set_style_as_default() {
		global $wpdb;

		$permission_error = FrmAppHelper::permission_nonce_error( 'frm_edit_forms', 'nonce', 'frm_ajax' );
		if ( $permission_error !== false ) {
			$data = array(
				'message' => __( 'Unable to set style as default', 'formidable-pro' ),
			);
			wp_send_json_error( $data, 403 );
			die();
		}

		$style_id = FrmAppHelper::get_post_param( 'style_id', 0, 'absint' );
		if ( ! $style_id ) {
			$data = array(
				'message' => __( 'Missing target style ID', 'formidable-pro' ),
			);
			wp_send_json_error( $data, 400 );
			die();
		}

		$post = get_post( $style_id );
		if ( ! $post || $post->post_type !== FrmStylesController::$post_type ) {
			$data = array(
				'message' => __( 'Invalid target style ID', 'formidable-pro' ),
			);
			wp_send_json_error( $data, 400 );
			die();
		}

		// Unset previous defaults.
		$wpdb->update(
			$wpdb->posts,
			array(
				'menu_order' => 0,
			),
			array(
				'menu_order' => 1,
				'post_type'  => FrmStylesController::$post_type,
			)
		);

		// Set the new default.
		$wpdb->update(
			$wpdb->posts,
			array(
				'menu_order' => 1,
			),
			array(
				'ID' => $post->ID,
			)
		);

		$frm_style = new FrmStyle();
		$frm_style->save_settings(); // Sync the CSS file and transients after setting the default style.

		$data = array();
		wp_send_json_success( $data );
	}

	/**
	 * @since 6.0
	 *
	 * @param WP_Post  $active_style
	 *
	 * @return array<WP_Post>
	 */
	public static function get_styles_for_styler( $active_style ) {
		return self::move_active_style_to_top( FrmStylesController::get_style_opts(), $active_style );
	}

	/**
	 * @since 6.0
	 *
	 * @param int $form_id
	 * @return WP_Post
	 */
	public static function get_active_style_for_form( $form_id ) {
		self::maybe_add_conversational_form_style_filter( $form_id );

		$form         = FrmForm::getOne( $form_id );
		$active_style = FrmStylesController::get_form_style( $form );
		if ( is_null( $active_style ) ) {
			$frm_style    = new FrmStyle( 'default' );
			$active_style = $frm_style->get_one();
		}
		return $active_style;
	}

	/**
	 * Add the conversational form object filter so that the conversational "lines" style gets properly filtered in the styler preview.
	 *
	 * @since 6.0
	 *
	 * @param int $form_id
	 * @return void
	 */
	private static function maybe_add_conversational_form_style_filter( $form_id ) {
		if ( ! is_callable( 'FrmChatAppController::add_form_object_filter' ) ) {
			return;
		}

		FrmChatAppController::add_form_object_filter( compact( 'form_id' ) );
	}

	/**
	 * The active style should be the first in the list so move it there.
	 *
	 * @since 6.0
	 *
	 * @param array<WP_Post> $styles
	 * @param WP_Post        $active_style
	 * @return array
	 */
	private static function move_active_style_to_top( $styles, $active_style ) {
		foreach ( $styles as $key => $style ) {
			if ( $style->ID === $active_style->ID ) {
				unset( $styles[ $key ] );
				break;
			}
		}

		array_unshift( $styles, $active_style );
		return $styles;
	}

	/**
	 * Get any notes to display in the visual styler preview.
	 *
	 * @since 6.0
	 *
	 * @return array<string>
	 */
	public static function get_notes_for_styler_preview() {
		global $frm_vars;

		$notes                   = array();
		$includes_multiple_pages = ! empty( $frm_vars['next_page'] );
		$includes_dropzone       = ! empty( $frm_vars['dropzone_loaded'] ); // Check for dropzone before loading the scripts. This isn't used in a note.

		if ( in_array( FrmAppHelper::simple_get( 'frm_action' ), array( 'new_style', 'duplicate' ), true ) ) {
			$notes[] = __( 'This style does not yet exist in the database. Click Update to create this new style.', 'formidable-pro' );
		}

		if ( $includes_multiple_pages ) {
			$notes[] = __( 'Fields from all pages are shown.', 'formidable-pro' );
		}

		if ( $includes_dropzone ) {
			self::initialize_dropzone();
		}

		wp_dequeue_script( 'frmdates' ); // Dequeues the datepicker add on scripts as they aren't necessary for the visual styler.
		wp_dequeue_script( 'formidable-stripe' );

		return $notes;
	}

	/**
	 * @since 6.0
	 *
	 * @return string|false
	 */
	public static function get_disabled_javascript_features() {
		global $frm_vars;

		$includes_conditional_logic   = ! empty( $frm_vars['rules'] );
		$includes_lookup_fields       = ! empty( $frm_vars['lookup_fields'] );
		$includes_dynamic_fields      = ! empty( $frm_vars['dep_dynamic_fields'] );
		$includes_calculations        = ! empty( $frm_vars['calc_fields'] );
		$includes_chosen_autocomplete = ! empty( $frm_vars['chosen_loaded'] );

		// Group together all of these features into a single list.
		$disabled_features = array();
		if ( $includes_conditional_logic ) {
			$disabled_features[] = __( 'conditionally hidden fields', 'formidable-pro' );
		}
		if ( $includes_lookup_fields ) {
			$disabled_features[] = __( 'lookup data', 'formidable-pro' );
		}
		if ( $includes_dynamic_fields ) {
			$disabled_features[] = __( 'dynamic field data', 'formidable-pro' );
		}
		if ( $includes_calculations ) {
			$disabled_features[] = __( 'calculations', 'formidable-pro' );
		}
		if ( $includes_chosen_autocomplete ) {
			$disabled_features[] = __( 'autocomplete', 'formidable-pro' );
		}

		if ( $disabled_features ) {
			return sprintf(
				// translators: %s: List of disabled features.
				__( 'The following Pro features are disabled: %s.', 'formidable-pro' ),
				implode( ', ', $disabled_features )
			);
		}

		return false;
	}

	/**
	 * Get the XML template file style data for the styler preview.
	 *
	 * @since 6.0
	 *
	 * @return void
	 */
	public static function preview_style_template() {
		$permission_error = FrmAppHelper::permission_nonce_error( 'frm_edit_forms', 'nonce', 'frm_ajax' );
		if ( $permission_error !== false ) {
			$data = array(
				'message' => __( 'Unable to preview style template', 'formidable-pro' ),
			);
			wp_send_json_error( $data, 403 );
			die();
		}

		if ( ! class_exists( 'FrmStyleApi' ) ) {
			// Lite is not up to date.
			wp_die( 0 );
		}

		$template_key = FrmAppHelper::get_post_param( 'template_key', '', 'sanitize_key' );
		if ( ! $template_key ) {
			$data = array(
				'message' => __( 'Template key not specified', 'formidable-pro' ),
			);
			wp_send_json_error( $data, 400 );
			die();
		}

		$template = self::get_template_match_from_api( $template_key );
		if ( ! is_array( $template ) || empty( $template['url'] ) ) {
			$data = array(
				'message' => __( 'Template did not match with API', 'formidable-pro' ),
			);
			wp_send_json_error( $data, 400 );
			die();
		}

		$transient_key = 'frm_style_template_' . $template['slug'];
		$transient     = get_transient( $transient_key );
		if ( is_array( $transient ) ) {
			$data = array(
				'settings' => $transient,
			);
			wp_send_json_success( $data );
			die();
		}

		$xml_url  = $template['url'];
		$response = wp_remote_get( $xml_url );
		$body     = wp_remote_retrieve_body( $response );
		$xml      = simplexml_load_string( $body );

		if ( ! isset( $xml->view ) || 'frm_styles' !== (string) $xml->view[0]->post_type ) {
			$data = array(
				'message' => __( 'Unable to successfully install template from API data.', 'formidable-pro' ),
			);
			wp_send_json_error( $data, 400 );
			die();
		}

		$encoded_content = (string) $xml->view[0]->content;
		$settings        = FrmAppHelper::maybe_json_decode( $encoded_content, false );

		if ( ! is_array( $settings ) ) {
			$data = array(
				'message' => __( 'API XML data is not formatted as expected and is not possible to preview.', 'formidable-pro' ),
			);
			wp_send_json_error( $data, 400 );
			die();
		}

		$frm_style      = new FrmStyle();
		$default_values = $frm_style->get_defaults();
		$settings       = $frm_style->override_defaults( $settings );
		$settings       = wp_parse_args( $settings, $default_values );

		// Keep the XML post content as a transient for a day so preview data can load quicker.
		set_transient( $transient_key, $settings, DAY_IN_SECONDS );

		$data = array(
			'settings' => $settings,
		);
		wp_send_json_success( $data );
	}

	/**
	 * Add an apply style button after the toggle button in the template preview.
	 *
	 * @since 6.0
	 *
	 * @return void
	 */
	public static function preview_after_toggle( $view ) {
		if ( 'list' !== $view ) {
			// This button is only required for the list view.
			return;
		}
		// This button has no text on load because the text is set dynamically when a style card is clicked.
		// It may be "Apply style", or "Install and apply style" if the card selected is a template.
		?>
		<a href="#" id="frm_apply_style" class="frm_floating_style_button button frm-button-secondary frm-with-icon frm_hidden" tabindex="0" role="button">
			<?php FrmAppHelper::icon_by_class( 'frmfont frm_save_icon', array( 'echo' => true ) ); ?> <span class="frm-apply-button-text"></span>
		</a>
		<?php
	}

	/**
	 * If $_POST['style_id'] is a template key, import the template and filter the style ID when assigning a style to a form.
	 *
	 * @since 6.0
	 *
	 * @param int $style_id
	 * @return int
	 */
	public static function maybe_import_style_template( $style_id ) {
		if ( $style_id ) {
			return $style_id;
		}

		$template_key = FrmAppHelper::get_post_param( 'style_id', '', 'sanitize_key' );
		if ( ! $template_key ) {
			return 0;
		}

		$template = self::get_template_match_from_api( $template_key );
		if ( ! is_array( $template ) || empty( $template['url'] ) ) {
			return 0; // Use 0 to flag the style ID input as invalid.
		}

		$xml = self::download_and_prepare_xml( $template['url'] );
		if ( false === $xml ) {
			return 0;
		}

		self::maybe_set_style_key( $xml );
		$imported = FrmXMLHelper::import_xml_now( $xml, true );

		if ( empty( $imported['posts'] ) ) {
			return 0;
		}

		// Make sure the new CSS loads after we sync a new style template.
		// Otherwise the preview may show without any styling until you refresh again.
		echo '<link href="' . esc_url( admin_url( 'admin-ajax.php?action=frmpro_css' ) ) . '" type="text/css" rel="Stylesheet" class="frm-custom-theme" />';

		$style_id = reset( $imported['posts'] );
		return $style_id;
	}

	/**
	 * Make sure the style post name is unique when we import a style template so we never update an existing one.
	 *
	 * @since 6.0
	 *
	 * @param SimpleXMLElement $xml
	 * @return void
	 */
	private static function maybe_set_style_key( $xml ) {
		if ( ! isset( $xml->view ) || empty( $xml->view->post_name ) ) {
			return;
		}

		$xml->view->post_name = FrmAppHelper::get_unique_key( (string) $xml->view->post_name, 'posts', 'post_name' );
	}

	/**
	 * Check the Style API for a template match by key.
	 * If there is a match we import the XML based on the url from our API data.
	 *
	 * @since 6.0
	 *
	 * @param string $template_key
	 * @return array|false
	 */
	private static function get_template_match_from_api( $template_key ) {
		$api      = new FrmStyleApi();
		$info     = $api->get_api_info();
		$template = false;

		foreach ( $info as $key => $style ) {
			if ( ! is_numeric( $key ) || ! is_array( $style ) || ! array_key_exists( 'slug', $style ) ) {
				continue;
			}

			if ( $template_key !== $style['slug'] ) {
				continue;
			}

			$template = $style;
			break;
		}

		return $template;
	}

	/**
	 * @since 6.0
	 *
	 * @param string $url
	 * @return SimpleXMLElement|false
	 */
	private static function download_and_prepare_xml( $url ) {
		$response = wp_remote_get( $url );
		$body     = wp_remote_retrieve_body( $response );
		$xml      = simplexml_load_string( $body );

		if ( false === $xml || empty( $xml->view ) || FrmStylesController::$post_type !== (string) $xml->view->post_type ) {
			return false;
		}

		return $xml;
	}

	/**
	 * @deprecated 4.0
	 */
	public static function style_switcher( $style, $styles ) {
		_deprecated_function( __METHOD__, '4.0', 'FrmProStylesController::add_new_button' );
		self::add_new_button( $style );
	}

	/**
	 * @codeCoverageIgnore
	 */
	public static function section_fields_file() {
		_deprecated_function( __METHOD__, '3.01.01', 'FrmProStylesController::style_box_file' );
		return self::view_folder() . '/_section-fields.php';
	}

	/**
	 * @codeCoverageIgnore
	 */
	public static function date_settings_file() {
		_deprecated_function( __METHOD__, '3.01.01', 'FrmProStylesController::style_box_file' );
		return self::view_folder() . '/_date-fields.php';
	}

	/**
	 * @codeCoverageIgnore
	 */
	public static function progress_settings_file() {
		_deprecated_function( __METHOD__, '3.01.01', 'FrmProStylesController::style_box_file' );
		return self::view_folder() . '/_progress-bars.php';
	}

	/**
	 * @codeCoverageIgnore
	 */
	public static function get_datepicker_names( $jquery_themes ) {
		_deprecated_function( __METHOD__, '3.03' );
		$alt_img_name = array();
		$theme_names  = array_keys( $jquery_themes );
		$theme_names  = array_combine( $theme_names, $theme_names );
		$alt_img_name = array_merge( $theme_names, $alt_img_name );
		$alt_img_name['-1'] = '';

		return $alt_img_name;
	}

	/**
	 * @since 4.0
	 * @deprecated 6.0
	 *
	 * @param array $atts
	 * @return void
	 */
	public static function style_dropdown( $atts ) {
		_deprecated_function( __METHOD__, '6.0' );
	}

	/**
	 * @since 4.0
	 * @deprecated 6.0
	 *
	 * @param WP_Post $style
	 * @return void
	 */
	public static function add_new_button( $style ) {
		_deprecated_function( __METHOD__, '6.0' );
	}
}
