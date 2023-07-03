<?php
// Creating Custom Meta boxes
$prefix = '';
$meta_box = array(
    'id' => 'kaya_page_secttings',
    'title' => esc_html__('Page Settings', 'talenthunt'),
    'page' => 'page',
    'context' => 'normal',
    'priority' => 'high',
    'fields' => array(

        array(
            'name' => esc_html__('Choose Page Sub Header Section', 'talenthunt'),
            'id' => 'select_page_subheader',
            'type' => 'select',
            'options' => array(
                array('name' => esc_html__('Page Title bar', 'talenthunt'), 'value' => 'page_titlebar'),
                array('name' => esc_html__('Slider Shortcode', 'talenthunt'), 'value' => 'slider'),
                array('name' => esc_html__('None', 'talenthunt'), 'value' => 'none'),
            )
        ),
        
        array(
            'name' => esc_html__('Custom Page Title', 'talenthunt'),
            'desc' => '',
            'id' => $prefix . 'custom_page_title',
            'type' => 'text',
            'std' => '',
        ),
        array(
            'name' => esc_html__('Page Title Description', 'talenthunt'),
            'desc' => '',
            'id' => $prefix . 'page_title_description',
            'type' => 'textarea',
            'std' => '',
        ),
        array(
            'name' => esc_html__('Upload Page Titlebar Image', 'talenthunt'),
            'desc' => '',
            'id' => $prefix . 'upload_page_title_bar_img',
            'type' => 'upload_image',
            'std' => '',
        ),

          array(
            'name' => esc_html__('Slider Shortcode', 'talenthunt'),
            'desc' => '',
            'id' => $prefix . 'main_slider_shortcode',
            'type' => 'text',
            'std' => '',
        ),
    )
);

add_action('admin_menu', 'talenthunt_kaya_add_metabox');

// Add meta box
function talenthunt_kaya_add_metabox() {
    global $meta_box;
    add_meta_box($meta_box['id'], $meta_box['title'], 'talenthunt_kaya_show_meta_box_data', $meta_box['page'], $meta_box['context'], $meta_box['priority']);
}

// Callback function to show fields in meta box
function talenthunt_kaya_show_meta_box_data() {
    global $meta_box, $post;

    // Use nonce for verification
    echo '<input type="hidden" name="kaya_meta_box_nonce" value="',esc_attr( wp_create_nonce(basename(__FILE__))), '" />';

    echo '<table class="form-table">';

    foreach ($meta_box['fields'] as $field) {
        // get current post meta data
        $meta = get_post_meta($post->ID, $field['id'], true);

        echo '<tr class="'.esc_attr($field['id']).'">',
                '<th style="width:20%"><label for="', esc_attr($field['id']), '">', esc_attr($field['name']), '</label></th>',
                '<td>';
        switch ($field['type']) {
            case 'text':
                echo '<input type="text" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="', (!empty($meta) ? esc_attr($meta) : esc_attr($field['std'])), '" size="30" style="width:97%" />', '<br />', (!empty( $field['desc']) ?  esc_attr($field['desc']) : '');
                break;
            case 'textarea':
                echo '<textarea name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" cols="60" rows="4" style="width:97%">', (!empty($meta) ? esc_attr($meta)  : esc_attr($field['std'])), '</textarea>', '<br />', (!empty( $field['desc']) ? esc_attr( $field['desc']) : '');
                break;
            case 'select':
               echo '<select class="" name="',esc_attr($field['id']), '" id="', esc_attr($field['id']), '">';
                    foreach ($field['options'] as $option)
                    {
                        echo '<option value="', esc_attr($option['value']), '" ', $meta == $option['value'] ? ' selected="selected"' : '', '>', esc_attr($option['name']), '</option>';
                    }
                    echo '</select>', 
                    '<br/>', !empty( $field['desc']) ?  esc_attr($field['desc']) : '';
                break;
            case 'radio':
                foreach ($field['options'] as $option) {
                    echo '<input type="radio" name="', esc_attr($field['id']), '" value="', esc_attr($option['value']), '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', esc_attr($option['name']);
                }
                break;
            case 'checkbox':
                echo '<input type="checkbox" name="', esc_attr($field['id']), '" value="1" id="', esc_attr($field['id']), '"', $meta ? ' checked="checked"' : '', ' />';
                break;
            case 'upload_image':
                echo talenthunt_kaya_image_upload_field( esc_attr($field['id']), get_post_meta($post->ID, esc_attr($field['id']), true) );  // WPCS: XSS OK
                break;
        }
        echo '</td><td>',
            '</td></tr>';
    }

    echo '</table>';
}

function talenthunt_kaya_image_upload_field( $name, $value = '') {
    $image = ' button">Upload image';
    $image_size = 'full';
    $display = 'none';

    if( $image_attributes = wp_get_attachment_image_src( $value, $image_size ) ) {
        $image = '"><img src="' . esc_url($image_attributes[0]) . '" style="width:150px; height:150px; display:block;" />';
        $display = 'inline-block';

    } 
    return '<div>
        <a href="#" class="kaya_upload_image_button' . esc_attr($image) . '</a>
        <input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />
        <a href="#" class="kaya_remove_upload_image_button" style="display:inline-block;display:' . $display . '">Remove image</a>
    </div>';
}


add_action('save_post', 'talenthunt_kaya_save_meatabox_data');
// Save data from meta box
function talenthunt_kaya_save_meatabox_data($post_id) {
    global $meta_box;

    // verify nonce
    $meta_box_nonce = isset($_POST['kaya_meta_box_nonce']) ? sanitize_text_field(wp_unslash($_POST['kaya_meta_box_nonce'])) : '';
    if (!wp_verify_nonce($meta_box_nonce, basename(__FILE__))) {
        return $post_id;
    }

    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // check permissions
    if ('page' == !empty($_POST['post_type'])) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    foreach ($meta_box['fields'] as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $field_id = '';
        //$new = isset($field_id) ? esc_attr($field_id) : '';
        $new = $_POST[$field['id']];
        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    }
}
?>