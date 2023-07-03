<?php

function ocdi_import_files() {
    return array(
        array(
            'import_file_name'             => 'lexian',
            'local_import_file'            => get_parent_theme_file_path('/inc/kaya-xml-files/demo-content.xml'),
            'local_import_customizer_file' => get_parent_theme_file_path('/inc/kaya-xml-files/customizer.dat'),
           // 'local_import_widget_file'     => get_parent_theme_file_path('/inc/kaya-xml-files/widgets.wie'),
            
      
           // 'import_preview_image_url'   => 'http://www.your_domain.com/ocdi/preview_import_image1.jpg',
          //  'import_notice'              => __( 'After you import this demo, you will have to setup the slider separately.', 'your-textdomain' ),
          //  'preview_url'                => 'http://www.your_domain.com/my-demo-1',
        ),
  
    );
}
add_filter( 'pt-ocdi/import_files', 'ocdi_import_files' );

function ocdi_after_import_setup() {
    // Assign menus to their locations.
    $top_menu = get_term_by( 'name', 'primary menu', 'nav_menu' );

    set_theme_mod( 'nav_menu_locations', array(
            'primary' => $top_menu->term_id,
        )
    );

    // Assign front page and posts page (blog page).
    $front_page_id = get_page_by_title( 'Home' );
    $blog_page_id  = get_page_by_title( 'Blog' );

    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $front_page_id->ID );
    update_option( 'page_for_posts', $blog_page_id->ID );


//Import Smart slider Slider
         if (class_exists('SmartSlider3')) {
       for ($i=1; $i < 2 ; $i++) {
           SmartSlider3::import(get_parent_theme_file_path( "/smartslider-demo-content/slider-demo".$i.".ss3"));
       }
    }
}
add_action( 'pt-ocdi/after_import', 'ocdi_after_import_setup' );
