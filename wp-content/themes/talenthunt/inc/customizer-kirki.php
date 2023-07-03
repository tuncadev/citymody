<?php
/**
 * alexia Theme Customizer
 *
 * @package alexia
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */


if ( class_exists( 'Kirki' ) ) { 

Kirki::add_config( 'talenthunt_theme_config', array(
  'capability'    => 'edit_theme_options',
  'option_type'   => 'theme_mod',
) );

/*---------------------------------------------------------------------------------------
Global Color Section Starting....
---------------------------------------------------------------------------------------*/

Kirki::add_panel( 'talenthunt_color_section_panel', array(
    'priority'    => 10,
    'title'       => __( 'Background  Color Settings', 'talenthunt' ),
   // 'description' => __( '', 'talenthunt' ),
) );
 

Kirki::add_section( 'colors', array(
    'title'          => esc_html__( 'Global Color Settings ', 'talenthunt' ),
   'description'    => esc_html__( ' Where you can manage background color for Body, globle link color, 
      hover color and Heading colors, Note: Please note that few elements does not work if those are created with Elementor page builder.', 'talenthunt' ),
    'panel'          => 'talenthunt_color_section_panel',
    'priority'       => 10,
) );


 Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'talenthunt_color_text',
  'label'       => __( 'Global Text Color', 'talenthunt' ), 
  'section'     => 'colors',
  'default'     => '',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '.site-content',
          'property' => 'color',
        ),
    )
] );

  Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'talenthunt_color_link',
  'label'       => __( 'Global Link Color', 'talenthunt' ), 
  'section'     => 'colors',
  'default'     => '',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '.site-content a:link, .site-content a:visited',
          'property' => 'color',
        ),
    )
] );

    Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'talenthunt_color_link_hover',
  'label'       => __( 'Global Link Hover Color', 'talenthunt' ), 
  'section'     => 'colors',
  'default'     => '',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '.site-content a:hover',
          'property' => 'color',
        ),
    )
] );

Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'talenthunt_color_heading',
  'label'       => __( 'Global  Heading Color', 'talenthunt' ), 
  'section'     => 'colors',
  'default'     => '',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '.site-content h1, .site-content h2, .site-content h3, .site-content h4, .site-content h5, .site-content h6, 
          .site-content h1 a, .site-content h2 a, .site-content h3 a, .site-content h4 a, .site-content h5 a, .site-content h6 a',
          'property' => 'color',
        ),
    )
] );
Kirki::add_section( 'background_image', array(
    'title'          => esc_html__( 'Background Image Settings ', 'talenthunt' ),
   // 'description'    => esc_html__( 'Upload your own logo.', 'talenthunt' ),
    'panel'          => 'talenthunt_color_section_panel',
    'priority'       => 10,
) );


/*---------------------------------------------------------------------------------------
Top Header Section Starts
---------------------------------------------------------------------------------------*/

Kirki::add_panel( 'talenthunt_top_header_panel', array(
    'priority'    => 10,
    'title'       => __( 'Top Header', 'talenthunt' ),
    'description' => __( 'My body Description', 'talenthunt' ),
) );
 
 /**
 * Add sections
 */
Kirki::add_section( 'title_tagline', array(
    'title'          => esc_html__( 'Upload Logo ', 'talenthunt' ),
    'description'    => esc_html__( 'Upload your own logo.', 'talenthunt' ),
    'panel'          => 'talenthunt_top_header_panel',
    'priority'       => 10,
) );

 /**
 * Add sections
 */
Kirki::add_section( 'talenthunt_header_bg_section', array(
    'title'          => esc_html__( 'Header Background Color ', 'talenthunt' ),
   // 'description'    => esc_html__( 'Upload your own logo.', 'talenthunt' ),
    'panel'          => 'talenthunt_top_header_panel',
    'priority'       => 10,
     'transport'   => 'auto',
) );



 Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'talenthunt_header_bg_section',
  'label'       => __( 'Top Header Background  Color', 'talenthunt' ), 
  'section'     => 'talenthunt_header_bg_section',
  'default'     => '#101010',
    'transport'   => 'auto',
    'output'      => array(
        array(
          'element'  => '#kaya-header-content-wrapper, #kaya-header-content-wrapper, .header-section',
          'property' => 'background-color',
            'suffix' => ' !important',
          
        ),
    )
] );

/*---------------------------------------------------------------------------------------
Top Menu Section Startis
---------------------------------------------------------------------------------------*/

Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'dimensions',
  'settings'    => 'nav_margin_top',
 // 'label'       => esc_html__( 'Dimension Control', 'kirki' ),
  'description' => esc_html__( 'Add Menubar margin top if required, ex: 10px', 'kirki' ),
  'section'     => 'talenthunt_top_menu_section',
  'default'     => [
    'margin-top'    => '0px',
  ],
  'transport'   => 'auto',
  'output'      => [
    [
      'property' => '',
      'element'  => '#main-nav, #header-navigation',
    ],
  ],
] );


/**
 * Add sections
 */
Kirki::add_section( 'talenthunt_top_menu_section', array(
    'title'          => esc_html__( 'Top Menu Section ', 'talenthunt' ),
    'panel'          => 'talenthunt_top_header_panel',    
    'description' => esc_html__( 'You can control Top Menu colors from here.', 'talenthunt' ),

    'priority'       => 11,
) );


Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'nav_bg_color',
  'label'       => __( 'Menu bar Background Color', 'talenthunt' ), 
  'section'     => 'talenthunt_top_menu_section',
  'default'     => '#101010',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '#main-nav',
          'property' => 'background-color',
        ),
    )
] );


Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'nav_link_color',
  'label'       => __( 'Menu Links Color', 'talenthunt' ), 
  'section'     => 'talenthunt_top_menu_section',
  'default'     => '#F9F9F9',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => 'nav#header-navigation a, #main-nav a, #header-navigation ul > li a',
          'property' => 'color',
        ),
    )
] );


Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'nav_link_hover_color',
  'label'       => __( 'Menu Links Hover Color', 'talenthunt' ), 
  'section'     => 'talenthunt_top_menu_section',
  'default'     => '#ff5722',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => 'nav#header-navigation a:hover',
          'property' => 'color',
        ),
    )
] );

Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'nav_link_active_bg_color',
  'label'       => __( 'Menu Active Background Color', 'talenthunt' ), 
  'section'     => 'talenthunt_top_menu_section',
  'default'     => '#101010',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => 'nav#header-navigation ul#main-menu li.current-menu-parent, nav#header-navigation ul#main-menu li.current-menu-item,
          nav#header-navigation ul#main-menu li.current-menu-ancestor ',
          'property' => 'background-color',
        ),
    )
] );

Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'nav_link_active_link_color',
  'label'       => __( 'Menu Active Link Color', 'talenthunt' ), 
  'section'     => 'talenthunt_top_menu_section',
  'default'     => '#ff5722',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => 'nav#header-navigation #main-nav > ul li.current_page_item a,
          nav#header-navigation #main-nav > ul li.current-menu-ancestor > a
                      ',
          'property' => 'color',
           'suffix' => ' !important',
        ),
    )
] );


Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'nav_sub_menu_bg_color',
  'label'       => __( 'Child Menu Background Color', 'talenthunt' ), 
  'section'     => 'talenthunt_top_menu_section',
  'default'     => '#F9F9F9',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '#header-navigation ul > li ul li a',
          'property' => 'background-color',
        ),
    )
] );

Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'nav_sub_menu_link_color',
  'label'       => __( 'Child Menu Links Color', 'talenthunt' ), 
  'section'     => 'talenthunt_top_menu_section',
  'default'     => '#101010',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '#header-navigation ul > li ul li a',
          'property' => 'color',
           'suffix' => ' !important',
        ),
    )
] );

Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'nav_sub_menu_hover_bg_color',
  'label'       => __( 'Child Menu Hover Background Color', 'talenthunt' ), 
  'section'     => 'talenthunt_top_menu_section',
  'default'     => '#ff5722',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '#header-navigation ul > li ul a:hover',
          'property' => 'background-color',
        ),
    )
] );

Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'nav_sub_menu_hover_link_color',
  'label'       => __( 'Child Menu Hover Link Color', 'talenthunt' ), 
  'section'     => 'talenthunt_top_menu_section',
  'default'     => '#F9F9F9',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '#header-navigation ul > li ul a:hover',
          'property' => 'color',
           'suffix' => ' !important',
        ),
    )
] );


Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'nav_sub_menu_active_bg_color',
  'label'       => __( 'Child Menu Active BG Color', 'talenthunt' ), 
  'section'     => 'talenthunt_top_menu_section',
  'default'     => '#ff5722',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '#header-navigation ul#main-menu li ul.sub-menu li.current-menu-item a',
          'property' => 'background-color',
        ),
    )
] );

Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'nav_sub_menu_active_link_color',
  'label'       => __( 'Child Menu Active Link Color', 'talenthunt' ), 
  'section'     => 'talenthunt_top_menu_section',
  'default'     => '#F9F9F9',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => 'nav#header-navigation #main-nav > ul ul.sub-menu li.current_page_item a
          ',
          'property' => 'color',
           'suffix' => ' !important',
        ),
    )
] );


Kirki::add_section( 'talenthunt_page_titlebar_section', array(
    'title'          => esc_html__( 'Page Titlebar Section ', 'talenthunt' ),
  //  'description'    => esc_html__( 'Change Background color for page title.', 'talenthunt' ),
    'panel'          => 'talenthunt_top_header_panel',
    'priority'       => 12,
) );



Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'page_titlebar_bg_color',
  'label'       => __( 'Page Title Bar Background Color', 'talenthunt' ), 
  'section'     => 'talenthunt_page_titlebar_section',
  'default'     => '#303030',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '.kaya-page-titlebar-wrapper',
          'property' => 'background-color',
        ),
    )
] );

Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'page_titlebar_color',
  'label'       => __( 'Page Title  Color', 'talenthunt' ), 
  'section'     => 'talenthunt_page_titlebar_section',
  'default'     => '#ffffff',
  'transport'   => 'auto',
  'output'      => array( 
        array(
          'element'  => '.kaya-page-titlebar-wrapper .page-title, .main-menu-btn-icon',
          'property' => 'color',
        ),
    )
] );

Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'mobile_menu_icon_color',
  'label'       => __( 'Mobile Menu Icon  Color', 'talenthunt' ), 
  'section'     => 'talenthunt_page_titlebar_section',
  'default'     => '#222222',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '.main-menu-btn-icon, .main-menu-btn-icon:before, .main-menu-btn-icon:after',
          'property' => 'background-color',
        ),
    )
] );



/*---------------------------------------------------------------------------------------
Google font for body, Top menu and headings
---------------------------------------------------------------------------------------*/
Kirki::add_panel( 'talenthunt_typography_panel', array(
    'priority'    => 40,
    'title'       => __( 'Typography', 'talenthunt' ),
    'description' => __( 'Typography Panel', 'talenthunt' ),
) );
 
Kirki::add_section( 'talenthunt_typography_section', array(
    'title'          => esc_html__( 'Typography Section ', 'talenthunt' ),
    'description'    => esc_html__( 'This section mainly used for selecting the google fonts only, so other settings may not work properly as those are created with page builder.', 'talenthunt' ),
    'panel'          => 'talenthunt_typography_panel',
    'priority'       => 10,
) );

Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'typography',
  'settings'    => 'body_font_settings',
  'label'       => esc_html__( 'Body Font Settings', 'kirki' ),
  'section'     => 'talenthunt_typography_section',
  'default'     => [
    'font-family'    => 'Roboto',
     'font-size'      => '14px',
  ],
  'priority'    => 10,
  'transport'   => 'auto',
  'output'      => [
    [
      'element' => 'body',
       'suffix' => ' !important',
    ],
  ],
] );


Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'typography',
  'settings'    => 'topmenu_font_settings',
  'label'       => esc_html__( 'Top Menu Font Settings', 'kirki' ),
  'section'     => 'talenthunt_typography_section',
  'default'     => [
    'font-family'    => 'Roboto',
    'font-size'      => '16px',
  ],
  'priority'    => 10,
  'transport'   => 'auto',
  'output'      => [
    [
       'element'  => '#main-nav ul li a',
       'suffix' => ' !important',
     
    ],
  ],
] );


Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'typography',
  'settings'    => 'heading1_font_settings',
  'label'       => esc_html__( 'Select google Font for headings', 'kirki' ),
  'section'     => 'talenthunt_typography_section',
  'default'     => [
    'font-family'    => 'Roboto',
      'font-size'      => '20px',
  ],
  'priority'    => 10,
  'transport'   => 'auto',
  'output'      => [
    [
     
    'element'  => 'h1, h2, h3, h4, h5 h6',

    ],
  ],
] );

/*---------------------------------------------------------------------------------------
Footer Section Starting....
---------------------------------------------------------------------------------------*/
Kirki::add_panel( 'talenthunt_footer_panel', array(
    'priority'    => 100,
    'title'       => __( 'Footer', 'talenthunt' ),
     'description' => esc_html__( 'If you have not used a footer which is created by "Header Footer Builder" then only this section works.', 'talenthunt' ),
    
) );

/**
 * Add sections
 */
Kirki::add_section( 'talenthunt_footer_panel_section', array(
    'title'          => esc_html__( 'Footer Section ', 'talenthunt' ),
    'panel'          => 'talenthunt_footer_panel',    
    'description' => esc_html__( 'Note: Hey, Mostly all themes come with custom  footer created by "Header Footer Builder,  so if you want to edit the Footer, go to "Appearance > Header Footer Builder
      and open the footer with Elementor Page Builder, modify it as per your requirements and save changes."', 'talenthunt' ),
    'priority'       => 10,
) );


Kirki::add_field( 'theme_config_id', [
  'type'     => 'textarea',
  'settings' => 'footer_copy_rights_text',
  'label'    => esc_html__( 'Footer Copyright Text', 'talenthunt' ),
  'section'  => 'talenthunt_footer_panel_section',
  'priority' => 10,
] );

Kirki::add_field( 'talenthunt_theme_config', [
  'type'        => 'color',
  'settings'    => 'footer_copy_rights_text_color',
  'label'       => __( 'Footer Text Color', 'talenthunt' ), 
  'section'     => 'talenthunt_footer_panel_section',
  'default'     => '#ccc',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '.footer_bottom_position_fix .copyright',
          'property' => 'color',
        ),
    )
] );

}
