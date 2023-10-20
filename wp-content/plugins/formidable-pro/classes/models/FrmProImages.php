<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 4.06
 */
class FrmProImages {

	/**
	 * Checks if field has image options. This wraps FrmProImages:should_show_images() method.
	 *
	 * @param array $field Field data.
	 * @return bool
	 */
	public static function has_image_options( $field ) {
		return self::field_type_support_image_options( $field ) && self::should_show_images( $field );
	}

	/**
	 * @since 5.0.06
	 *
	 * @param array $field
	 * @return bool
	 */
	private static function field_type_support_image_options( $field ) {
		return FrmField::is_field_type( $field, 'radio' ) || FrmField::is_field_type( $field, 'checkbox' );
	}

	public static function has_images_options_in_html( $options ) {
		$options = is_array( $options ) ? implode( ' ', $options ) : $options;
		return strpos( $options, 'frm_image_option' ) !== false;
	}

	public static function has_image_option_markup( $value ) {
		return is_string( $value ) && strpos( $value, 'frm_image_option_container' ) !== false;
	}

	private static function get_checkmark_markup( $type = 'square' ) {
		$class = 'frmfont frm_checkmark_' . $type . '_icon';
		return '<div class="frm_selected_checkmark">' . FrmAppHelper::icon_by_class( $class, array( 'echo' => false ) ) . '</div>';
	}

	public static function get_image_icon_markup() {
		return '<div class="frm_image_placeholder_icon">' . FrmAppHelper::icon_by_class( 'frmfont frm_placeholder_image_icon', array( 'echo' => false ) ) . '</div>';
	}

	public static function get_default_size() {
		return 'small';
	}

	/**
	 * Load settings in builder
	 */
	public static function show_image_choices( $args ) {
		$field = $args['field'];
		if ( isset( $field['post_field'] ) && $field['post_field'] === 'post_category' ) {
			return;
		}

		$columns = array(
			'small'  => __( 'Small', 'formidable-pro' ),
			'medium' => __( 'Medium', 'formidable-pro' ),
			'large'  => __( 'Large', 'formidable-pro' ),
			'xlarge' => __( 'Extra Large', 'formidable-pro' ),
		);

		echo '<div class="frm_grid_container frm_priority_field_choices">';
		include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/image-options.php' );
		include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/separate-values.php' );
		echo '</div>';
	}

	/**
	 * Called by hook in lite.
	 */
	public static function admin_options( $atts ) {
		$field = $atts['field'];

		if ( ! self::field_type_support_image_options( $field ) ) {
			return;
		}

		$opt_key = $atts['opt_key'];
		$opt     = isset( $field['options'][ $opt_key ] ) ? $field['options'][ $opt_key ] : '';
		$return  = array( 'filename' );
		$image   = self::single_option_details( compact( 'opt', 'opt_key', 'field', 'return' ) );
		$opt     = FrmFieldsHelper::get_label_from_array( $opt, $opt_key, $field );

		if ( ! isset( $field['image_options'] ) ) {
			$field['image_options'] = 0;
		}

		include self::get_backend_fields_path() . 'image-selector.php';
	}

	/**
	 * @return string
	 */
	private static function get_backend_fields_path() {
		return FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/';
	}

	/**
	 * Checks if field should show images. This is similar to FrmProImages:has_image_options() but does not contain
	 * field type check.
	 *
	 * @since 5.0
	 * @since 6.2 This method is public.
	 *
	 * @param array $field Field data.
	 * @return bool
	 */
	public static function should_show_images( $field ) {
		$image_options = FrmField::get_option( $field, 'image_options' );

		/**
		 * Allows show or hide choice field images using custom code.
		 *
		 * @since 5.0
		 *
		 * @param bool  $show Show images or not.
		 * @param array $args The arguments. Contains `field`.
		 */
		return apply_filters( 'frm_pro_field_should_show_images', ! empty( $image_options ), compact( 'field' ) );
	}

	/**
	 * @param array $atts - includes opt, opt_key, field, and return.
	 */
	public static function single_option_details( $atts ) {
		$id    = self::get_image_from_array( $atts['opt'], $atts['opt_key'], $atts['field'] );
		$image = array(
			'id'  => $id,
			'url' => self::get_url( $id ),
		);

		if ( in_array( 'filename', $atts['return'] ) ) {
			$image['filename'] = self::get_filename( $id );
		}

		if ( in_array( 'label', $atts['return'] ) ) {
			$image['label'] = self::get_label( $atts['field'], $atts['opt'], $image['url'] );
		}

		return $image;
	}

	private static function get_image_from_array( $opt, $opt_key, $field ) {
		$opt = apply_filters( 'frm_field_image_id', $opt, $opt_key, $field );

		return self::check_image( $opt, $opt_key, $field );
	}

	private static function check_image( $opt, $opt_key, $field ) {
		if ( is_array( $opt ) ) {
			if ( FrmField::is_option_true( $field, 'image_options' ) ) {
				$opt = isset( $opt['image'] ) ? $opt['image'] : 0;
			} else {
				$opt = 0;
			}
		}

		return $opt;
	}

	/**
	 * Called by self::single_option_details.
	 *
	 * @param mixed $image_id
	 * @return string
	 */
	private static function get_url( $image_id ) {
		if ( ! self::validate_image_id( $image_id ) ) {
			return '';
		}

		$image_id = (int) $image_id;
		$size     = self::get_default_size();
		$src      = wp_get_attachment_image_src( $image_id, $size );
		$url      = is_array( $src ) ? $src[0] : '';

		if ( ! $url ) {
			$url = wp_get_attachment_image_url( $image_id );
		}

		return $url ? $url : '';
	}

	/**
	 * Check if an image id isn't empty and is a number before trying to get the image.
	 *
	 * @param mixed $image_id
	 * @return bool true if valid.
	 */
	private static function validate_image_id( $image_id ) {
		return $image_id && is_numeric( $image_id );
	}

	/**
	 * Called by self::single_option_details.
	 */
	private static function get_filename( $image_id ) {
		if ( empty( $image_id ) ) {
			return '';
		}

		$filename = get_post_meta( (int) $image_id, '_wp_attached_file', true );

		$matches = array();
		preg_match( '/([A-Za-z0-9.\-_]+)$/', $filename, $matches );

		return isset( $matches[0] ) ? $matches[0] : '';
	}

	/**
	 * Called by self::single_option_details.
	 */
	private static function get_label( $field, $opt, $image_url = '' ) {
		if ( ! self::should_show_images( $field ) ) {
			return $opt;
		}

		$show_label  = self::should_show_label( $field );
		$label_class = $show_label ? ' frm_label_with_image' : '';
		$text_label  = self::get_label_from_opt( $opt );
		$field_type  = FrmField::get_option( $field, 'type' );

		$label  = '<div class="frm_image_option_container' . esc_attr( $label_class ) . '">';
		$label .= self::get_checkmark_markup( $field_type === 'checkbox' ? 'square' : 'circle' );
		if ( empty( $image_url ) ) {
			$label .= '<div class="frm_empty_url">' . self::get_image_icon_markup() . '</div>';
		} else {
			$label .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $text_label ) . '" />';
		}

		if ( $show_label ) {
			$label .= '<span class="frm_text_label_for_image"><span class="frm_text_label_for_image_inner">' . $text_label . '</span></span>';
		}

		$label .= '</div>';

		return $label;
	}

	/**
	 * Checks if should show image label in given field.
	 *
	 * @since 5.0
	 *
	 * @param array $field Field data.
	 * @return bool
	 */
	private static function should_show_label( $field ) {
		/**
		 * Allows showing or hiding image label from custom code.
		 *
		 * @since 5.0
		 *
		 * @param bool  $show Set to `true` to show.
		 * @param array $args The arguments. Contains `field`.
		 */
		return apply_filters( 'frm_pro_field_should_show_label', empty( $field['hide_image_text'] ), compact( 'field' ) );
	}

	private static function get_label_from_opt( $opt ) {
		if ( is_array( $opt ) ) {
			$opt = isset( $opt['label'] ) ? $opt['label'] : '';
		}

		return $opt;
	}

	/**
	 * Called by hooks.
	 */
	public static function get_image_option_classes( $classes, $field ) {
		return $classes . self::get_option_classes_from_field( $field );
	}

	private static function get_option_classes_from_field( $field ) {
		if ( ! self::should_show_images( $field ) ) {
			return '';
		}

		$image_size = FrmField::get_option( $field, 'image_size' );

		$class = ' frm_image_options ';
		if ( ! empty( $image_size ) ) {
			$class .= 'frm_image_size_' . $image_size . ' ';
		}
		return $class;
	}

	public static function showing_images( $field, $atts ) {
		$is_image_field = self::has_image_options( $field );

		// Don't show images in frm-show-entry if show_image_option=0.
		$in_entry_table = ! isset( $atts['show_image_options'] ) || $atts['show_image_options'];

		// Don't show images in field shortcodes if show_image=0.
		$show_image = ! isset( $atts['show_image'] ) || $atts['show_image'];

		// Only show images with the frm-show-entry shortcode if format is set to text or using default.
		$format = empty( $atts['format'] ) || $atts['format'] === 'text';

		return $is_image_field && $in_entry_table && $show_image && $format && empty( $atts['plain_text'] );
	}

	public static function display( $field, $value, $atts ) {
		$multiple_values = is_array( $value );

		$f_values = array();
		$f_labels = array();
		$f_images = array();

		foreach ( $field->options as $opt_key => $opt ) {
			if ( ! is_array( $opt ) ) {
				continue;
			}

			$f_labels[ $opt_key ] = isset( $opt['label'] ) ? $opt['label'] : reset( $opt );
			$f_values[ $opt_key ] = isset( $opt['value'] ) ? $opt['value'] : $f_labels[ $opt_key ];
			$f_images[ $opt_key ] = isset( $opt['image'] ) ? $opt['image'] : 0;
			unset( $opt_key, $opt );
		}

		if ( is_array( $value ) ) {
			$value = FrmAppHelper::array_flatten( $value, 'reset' );
		}

		$has_separate_option = FrmField::is_option_true( $field, 'separate_value' );
		$values_to_check     = $has_separate_option ? $f_values : $f_labels;

		if ( empty( $values_to_check ) ) {
			return $value;
		}

		if ( isset( $atts['show'] ) && $atts['show'] === 'value' ) {
			return $values_to_check;
		}

		foreach ( (array) $value as $v_key => $val ) {
			if ( in_array( $val, $values_to_check ) ) {
				$opt = array_search( $val, $values_to_check );
				$display_value = self::option_array( $f_labels, $f_images, $opt );

				if ( is_array( $value ) ) {
					$value[ $v_key ] = $display_value;
				} else {
					$value = $display_value;
				}
			}
			unset( $v_key, $val );
		}

		$hide_image_label  = ! empty( $field->field_options['hide_image_text'] );
		$image_size_option = FrmField::get_option( $field, 'image_size' );
		$image_values      = array(
			'display_options' => $value,
			'showing_images'  => isset( $atts['show_image'] ) ? $atts['show_image'] : false,
			'show_label'      => isset( $atts['show_label'] ) ? $atts['show_label'] : ! $hide_image_label,
			'multiple_values' => $multiple_values,
			'image_size'      => $image_size_option ? $image_size_option : self::get_default_size(),
		);

		return self::get_image_value( $atts, $field, $image_values );
	}

	private static function option_array( $f_labels, $f_images, $opt ) {
		return array(
			'label' => $f_labels[ $opt ],
			'image' => $f_images[ $opt ],
		);
	}

	private static function get_image_value( $atts, $field, $image_values ) {
		if ( empty( $image_values['display_options'] ) ) {
			return '';
		}

		$image_values['file_object'] = FrmFieldFactory::get_field_type( 'file' );

		if ( empty( $image_values['multiple_values'] ) ) {
			return self::get_value_for_display( $image_values['display_options'], $atts, $image_values );
		}

		$image_markup = array();
		foreach ( $image_values['display_options'] as $key => $single_image_values ) {
			$image_markup[] = self::get_value_for_display( $single_image_values, $atts, $image_values );
		}

		return $image_markup;
	}

	private static function get_value_for_display( $value, $atts, $image_values ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}

		if ( isset( $atts['show'] ) && trim( $atts['show'] ) === 'id' ) {
			return empty( $value['image'] ) ? '' : $value['image'];
		}

		$image_size = $image_values['image_size'] ? $image_values['image_size'] : self::get_default_size();

		$file_field_object                  = $image_values['file_object'];
		$new_atts                           = $file_field_object->set_file_atts( $atts );
		$new_atts['show_image']             = isset( $atts['show_image'] ) ? $atts['show_image'] : 1;
		$new_atts['add_link_for_non_image'] = false;

		// If image_option_size is set for frm-show-entry shortcode, use it.
		if ( ! empty( $atts['image_option_size'] ) ) {
			$atts['size'] = $atts['image_option_size'];
		}

		$new_atts['size'] = $file_field_object->set_size( $atts );

		$has_image = ! empty( $value['image'] ) && $new_atts['show_image'];

		$display_content = '';
		$label           = isset( $value['label'] ) ? $value['label'] : '';

		if ( $has_image ) {
			$image_id = $value['image'];
			$alt_tag  = strip_tags( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) );
			if ( ! $alt_tag && $label !== '' ) {
				// If alt tag not set for image, set the label as the alt tag for the image.
				update_post_meta( $image_id, '_wp_attachment_image_alt', $label, $alt_tag );
			}

			$display_content .= $file_field_object->get_file_display( $value['image'], $new_atts );
		}

		$label_class = '';
		$show_label = ( $label !== '' && ( $image_values['show_label'] || ! $has_image || strpos( $display_content, 'img' ) === false ) );

		if ( $show_label ) {
			// using FrmAppHelper::kses over esc_html to fix Pro issue #3933
			$display_content .= '<span class="frm_text_label_for_image"><span class="frm_text_label_for_image_inner">' . FrmAppHelper::kses( $label, 'all' ) . '</span></span>';
			$label_class = ' frm_label_with_image';
		}

		$display_content = '<span class="frm_show_images frm_image_option_container frm_image_option_size_' . esc_attr( $image_size . $label_class ) . '">' . $display_content . '</span>';

		return $display_content;
	}
}
