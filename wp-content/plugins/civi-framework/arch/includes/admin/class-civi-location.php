<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Civi_Location')) {
    /**
     * Class Civi_Location
     */
    class Civi_Location
    {
        /*
         * Countries settings
         */
        public function countries_create_menu()
        {
            add_submenu_page(
                'edit.php?post_type=place',
                esc_html__('Country', 'civi-framework'),
                esc_html__('Country', 'civi-framework'),
                'manage_civi_framework',
                'countries_settings',
                array($this, 'countries_settings_page')
            );
        }

        public function countries_register_setting()
        {
            register_setting('countries-settings-group', 'country_list');
        }

        public function countries_settings_page()
        {
?>
            <div class="wrap civi-countries-settings">
                <h1><?php esc_html_e('Countries', 'civi-framework'); ?></h1>
                <p><?php esc_html_e('Please Choose Country ( If no country is selected. will automatically take all the country ).', 'civi-framework'); ?></p>
                <div class="heading">
                    <a href="#" class="button button-primary remove-all"><?php esc_html_e('Remove All', 'civi-framework'); ?></a>
                </div>
                <form method="post" action="options.php">
                    <?php settings_fields('countries-settings-group'); ?>
                    <?php do_settings_sections('countries-settings-group'); ?>
                    <?php
                    $countries_selected = get_option('country_list');
                    $countries = civi_get_countries();
                    foreach ($countries as $key => $value) :
                    ?>
                        <div class="form-group">
                            <input type="checkbox" name="country_list[]" <?php if ($countries_selected) echo in_array($key, $countries_selected) ? 'checked' : ''; ?> value="<?php echo esc_attr($key); ?>" id="<?php echo esc_attr($key); ?>" />
                            <label for="<?php echo esc_attr($key); ?>"><?php esc_html_e($value); ?></label>
                        </div>
                    <?php endforeach; ?>
                    <?php submit_button(); ?>
                </form>
            </div>
        <?php
        }

        //place-neighborhood
        public function add_form_fields_place_neighborhood($taxonomy)
        {
            $default_country = civi_get_option('default_country', 'US');
        ?>
            <div id="place-country" class="form-field term-group selectdiv civi-place-select-meta-box-wrap">
                <label for="place_neighborhood_country"><?php esc_html_e('Country', 'civi-framework'); ?></label>
                <select id="place_neighborhood_country" name="place_neighborhood_country" class="postform civi-place-country-ajax">
                    <?php
                    $countries = civi_get_selected_countries();
                    foreach ($countries as $key => $country) :
                        echo '<option ' . selected($default_country, $key, false) . ' value="' . $key . '">' . $country . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <!-- <div id="place-state" class="form-field term-group selectdiv civi-place-select-meta-box-wrap">
                <label for="place_neighborhood_state"><?php esc_html_e('Province / State', 'civi-framework'); ?></label>
                <select id="place_neighborhood_state" name="place_neighborhood_state" data-slug="0" class="postform civi-place-state-ajax">
                    <option value=""><?php esc_html_e('None', 'civi-framework'); ?></option>
                    <?php
                    $terms_state = get_categories(
                        array(
                            'taxonomy' => 'place-state',
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'hide_empty' => false,
                            'parent' => 0
                        )
                    );
                    foreach ($terms_state as $term) : ?>
                        <option
                            value="<?php echo esc_attr($term->term_id); ?>"><?php esc_html_e($term->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div> -->
            <div id="place-city" class="form-field term-group selectdiv civi-place-select-meta-box-wrap">
                <label for="place_neighborhood_city"><?php esc_html_e('City', 'civi-framework'); ?></label>
                <select id="place_neighborhood_city" name="place_neighborhood_city" data-slug="0" class="postform civi-place-city-ajax">
                    <option value=""><?php esc_html_e('None', 'civi-framework'); ?></option>
                    <?php
                    $terms_city = get_categories(
                        array(
                            'taxonomy' => 'place-city',
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'hide_empty' => false,
                            'parent' => 0
                        )
                    );
                    foreach ($terms_city as $term) : ?>
                        <option value="<?php echo esc_attr($term->term_id); ?>"><?php esc_html_e($term->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php
        }

        public function save_place_neighborhood_meta($term_id, $tt_id)
        {
            if (isset($_POST['place_neighborhood_country']) && !empty($_POST['place_neighborhood_country'])) {
                $place_neighborhood_country = sanitize_title(wp_unslash($_POST['place_neighborhood_country']));
                add_term_meta($term_id, 'place_neighborhood_country', strtoupper($place_neighborhood_country), true);
            }
            if (isset($_POST['place_neighborhood_state']) && !empty($_POST['place_neighborhood_state'])) {
                $place_neighborhood_state = sanitize_title(wp_unslash($_POST['place_neighborhood_state']));
                add_term_meta($term_id, 'place_neighborhood_state', $place_neighborhood_state, true);
            }
            if (isset($_POST['place_neighborhood_city']) && !empty($_POST['place_neighborhood_city'])) {
                $place_neighborhood_city = sanitize_title(wp_unslash($_POST['place_neighborhood_city']));
                add_term_meta($term_id, 'place_neighborhood_city', $place_neighborhood_city, true);
            }
        }

        public function edit_form_fields_place_neighborhood($term, $taxonomy)
        {
            $place_neighborhood_country = get_term_meta($term->term_id, 'place_neighborhood_country', true);
            $place_neighborhood_state = get_term_meta($term->term_id, 'place_neighborhood_state', true);
            $place_neighborhood_city =  get_term_meta($term->term_id, 'place_neighborhood_city', true);
        ?>
            <tr id="place-country" class="form-field term-group-wrap civi-place-select-meta-box-wrap">
                <th scope="row"><label for="place_neighborhood_country"><?php esc_html_e('Country', 'civi-framework'); ?></label></th>
                <td>
                    <select class="postform civi-place-country-ajax" id="place_neighborhood_country" name="place_neighborhood_country">
                        <?php
                        $countries = civi_get_selected_countries();
                        foreach ($countries as $key => $country) :
                            echo '<option ' . selected($place_neighborhood_country, $key, false) . ' value="' . $key . '">' . $country . '</option>';
                        endforeach;
                        ?>
                    </select>
                </td>
            </tr>
            <tr id="place-state" class="form-field term-group-wrap civi-place-select-meta-box-wrap">
                <th scope="row"><label for="place_neighborhood_state"><?php esc_html_e('Province / State', 'civi-framework'); ?></label></th>
                <td>
                    <select data-selected="<?php echo esc_attr($place_neighborhood_state); ?>" data-slug="0" class="postform civi-place-state-ajax" id="place_neighborhood_state" name="place_neighborhood_state">
                        <option value=""><?php esc_html_e('None', 'civi-framework'); ?></option>
                        <?php
                        $terms_state = get_categories(
                            array(
                                'taxonomy' => 'place-state',
                                'orderby' => 'name',
                                'order' => 'ASC',
                                'hide_empty' => false,
                                'parent' => 0
                            )
                        );
                        foreach ($terms_state as $term) :
                            echo '<option ' . selected($place_neighborhood_state, $term->term_id, false) . ' value="' . esc_attr($term->term_id) . '">' . esc_html($term->name) . '</option>';
                        endforeach;
                        ?>
                    </select>
                </td>
            </tr>
            <tr id="place-city" class="form-field term-group-wrap civi-place-select-meta-box-wrap">
                <th scope="row"><label for="place_neighborhood_city"><?php esc_html_e('City', 'civi-framework'); ?></label></th>
                <td><select data-selected="<?php echo esc_attr($place_neighborhood_city); ?>" data-slug="0" class="postform civi-place-city-ajax" id="place_neighborhood_city" name="place_neighborhood_city">
                        <option value=""><?php esc_html_e('None', 'civi-framework'); ?></option>
                        <?php
                        $terms_city = get_categories(
                            array(
                                'taxonomy' => 'place-city',
                                'orderby' => 'name',
                                'order' => 'ASC',
                                'hide_empty' => false,
                                'parent' => 0
                            )
                        );
                        foreach ($terms_city as $term) :
                            echo '<option ' . selected($place_neighborhood_city, $term->term_id, false) . ' value="' . esc_attr($term->term_id) . '">' . esc_html($term->name) . '</option>';
                        endforeach; ?>
                    </select></td>
            </tr>
<?php
        }

        public function update_place_neighborhood_meta($term_id, $tt_id)
        {
            if (isset($_POST['place_neighborhood_country']) && !empty($_POST['place_neighborhood_country'])) {
                $place_neighborhood_country = sanitize_title(wp_unslash($_POST['place_neighborhood_country']));
                update_term_meta($term_id, 'place_neighborhood_country', strtoupper($place_neighborhood_country));
            }
            if (isset($_POST['place_neighborhood_state']) && !empty($_POST['place_neighborhood_state'])) {
                $place_neighborhood_state = sanitize_title(wp_unslash($_POST['place_neighborhood_state']));
                update_term_meta($term_id, 'place_neighborhood_state', $place_neighborhood_state);
            }
            if (isset($_POST['place_neighborhood_city']) && !empty($_POST['place_neighborhood_city'])) {
                $place_neighborhood_city = sanitize_title(wp_unslash($_POST['place_neighborhood_city']));
                update_term_meta($term_id, 'place_neighborhood_city', $place_neighborhood_city);
            }
        }

        public function add_columns_place_neighborhood($columns)
        {
            $columns['cb'] = "<input type=\"checkbox\" />";
            $columns['name'] = esc_html__('Name', 'civi-framework');
            $columns['description'] = esc_html__('Description', 'civi-framework');
            $columns['slug'] = esc_html__('Slug', 'civi-framework');
            $columns['place_neighborhood_city'] = esc_html__('City', 'civi-framework');
            $columns['posts'] = esc_html__('Count', 'civi-framework');
            $new_columns = array();
            $custom_order = array('cb', 'name', 'description', 'slug', 'place_neighborhood_city', 'posts');
            foreach ($custom_order as $colname) {
                $new_columns[$colname] = $columns[$colname];
            }
            return $new_columns;
        }

        public function add_columns_place_neighborhood_content($content, $column_name, $term_id)
        {

            if ($column_name !== 'place_neighborhood_city') {
                return $content;
            }
            $term_id = absint($term_id);
            $place_neighborhood_city_tax_id  = get_term_meta($term_id, 'place_neighborhood_city', true);
            if (!empty($place_neighborhood_city_tax_id)) {
                $place_neighborhood_city = get_term($place_neighborhood_city_tax_id);
                if (!empty($place_neighborhood_city) && isset($place_neighborhood_city->name)) {
                    $content .= esc_html($place_neighborhood_city->name);
                }
            }
            return $content;
        }

        public function add_columns_place_neighborhood_sortable($sortable)
        {
            $sortable['place_neighborhood_city'] = 'place_neighborhood_city';
            return $sortable;
        }
    }
}
