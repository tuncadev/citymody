<?php
/**
 * kirki Theme Customizer
 *
 * @package kirki
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */


if ( class_exists( 'Kirki' ) ) { 

Kirki::add_config( 'kirki_theme_config', array(
  'capability'    => 'edit_theme_options',
  'option_type'   => 'theme_mod',
) );

/*---------------------------------------------------------------------------------------
Ultimate Member Card Hover Section Starting....
---------------------------------------------------------------------------------------*/
Kirki::add_panel( 'um_color_panel', array(
    'priority'    => 30,
    'title'       => __( 'Ultimate Member Section', 'kirki' ),
    'description' => esc_html__( ' Where you can manage all color and other settings related to Member Directory and Profile pages.', 'kirki' ),
    
) );

/**
 * Add sections
 */
Kirki::add_section( 'member_card_panel_section', array(
    'title'          => esc_html__( 'Member Directory Color Settings ', 'kirki' ),
    'panel'          => 'um_color_panel',    
    'description' => esc_html__( 'Here you can control the Profile card Color settings', 'kirki' ),
    'priority'       => 12,
) );


Kirki::add_field( 'kirki_theme_config', [
 'type'        => 'typography',
  'settings'    => 'member_profile_header_um-font',
  'label'       => __( 'Member Name font-family', 'kirki' ), 
  'section'     => 'member_card_panel_section',
   'default'     => [
    'font-family'    => 'Roboto',
      'font-size'      => '20px',
  ],
  'priority'    => 10,
  'transport'   => 'auto',
  'output'      => [
    [
     
    'element'  => '.um-member-name, .um-member-name a',
     'suffix' => ' !important',

    ],
  ],
  'transport'   => 'auto',
] );



Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'member_text_color',
  'label'       => __( 'Member Name Text  Color', 'kirki' ), 
  'section'     => 'member_card_panel_section',
  'default'     => '#303030',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '
          .um-member-name, .um-member-name a     
          ',
           'property' => 'color',
            'suffix' => ' !important',
        ),
    )
] );


Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'member_card_ho_bg_color',
  'label'       => __( 'Member Card Hover Background Color', 'kirki' ), 
  'section'     => 'member_card_panel_section',
  'default'     => '#ff5722',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '.um-directory .um-members-wrapper .um-members-grid-wrapper .um-members.um-members-list .um-member .um-member-meta-main
                        ',

           'property' => 'background-color',
        ),
    )
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'member_card_text_color',
  'label'       => __( 'Member Card Text  Color', 'kirki' ), 
  'section'     => 'member_card_panel_section',
  'default'     => '#ffffff',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '
          .um-members-wrapper .um-members-grid-wrapper .um-members.um-members-list .um-member-card,
          .um-members-wrapper .um-members-grid-wrapper .um-members.um-members-list .um-member-card a
       
          ',
           'property' => 'color',
        ),
    )
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'list_view_member_card_text_color',
  'label'       => __( 'List View Member Card Text  Color', 'kirki' ), 
  'section'     => 'member_card_panel_section',
  'default'     => '#666666',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '
          .um-members-wrapper .um-members.um-members-list .um-member-card,
          .um-members-wrapper .um-members.um-members-list .um-member-card a, 
          .um-directory .um-members-wrapper .um-members.um-members-list .um-member .um-member-card-container .um-member-card .um-member-card-content .um-member-card-header .um-member-name a
       
          ',
           'property' => 'color',
        ),
    )
] );

Kirki::add_field( 'theme_config_id', [
  'type'        => 'checkbox',
  'settings'    => 'member_card_hover_checkbox_setting',
  'label'       => esc_html__( 'Disable The Hover for Member Card', 'kirki' ),
 // 'description' => esc_html__( 'Description', 'kirki' ),
  'section'     => 'member_card_panel_section',
  'default'     => false,
] );


Kirki::add_field( 'theme_config_id', [
  'type'        => 'checkbox',
  'settings'    => 'member_profile_photo_grayscale_checkbox_setting',
  'label'       => esc_html__( 'Enable Profile Photo Grayscale Mode', 'kirki' ),
 // 'description' => esc_html__( 'Description', 'kirki' ),
  'section'     => 'member_card_panel_section',
  'default'     => false,
] );
/*
Kirki::add_field( 'theme_config_id', [
  'type'        => 'checkbox',
  'settings'    => 'checkbox_setting_for_compca_card_button',
  'label'       => esc_html__( 'Enable The Compcard Download Button', 'kirki' ),
  'description' => esc_html__( 'It Display in Member Profile page', 'kirki' ),
  'section'     => 'member_card_panel_section',
  'default'     => false,
] );
*/

Kirki::add_field( 'theme_config_id', [
  'type'        => 'select',
  'settings'    => 'compcard_styles',
  'label'       => esc_html__( 'Select Compcard Layout Style', 'kirki' ),
  'section'     => 'member_card_panel_section',
  'default'     => 'compcard_style-1',
  'placeholder' => esc_html__( 'Select an option...', 'kirki' ),
  'priority'    => 10,
  'multiple'    => 1,
  'choices'     => [
    'compcard_style-1' => esc_html__( 'Compcard Style 1', 'kirki' ),
    'compcard_style-2' => esc_html__( 'Compcard Style 2', 'kirki' ),
  ],
] );


/*
Kirki::add_field( 'theme_config_id', [
  'type'     => 'text',
  'settings' => 'txt_setting_for_compca_card_button_text',
  'label'    => esc_html__( 'Compcard Button Text ', 'kirki' ),
  'description' => esc_html__( 'This works when you enable above Compcard check box', 'kirki' ),
  'section'  => 'member_card_panel_section',
  'default'  => esc_html__( 'Download Compcard', 'kirki' ),
  'priority' => 10,
] );
*/

/**
 * Add sections for search filter
 */
Kirki::add_section( 'member_profile_page_section', array(
    'title'          => esc_html__( 'Member Profile Page Settings ', 'kirki' ),
    'panel'          => 'um_color_panel',    
   // 'description' => esc_html__( 'You can control Footer Text from here.', 'kirki' ),
    'priority'       => 12,
) );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'member_profile_header_bg_color',
  'label'       => __( 'Um Header Profile Meta Background Color', 'kirki' ), 
  'section'     => 'member_profile_page_section',
  'default'     => '#ffffff',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '.um-profile-meta',
          'property' => 'background-color',
          'suffix' => ' !important',
        ),
    )
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'member_profile_header_border_color',
  'label'       => __( 'Um Header Profile Meta Background Border Color', 'kirki' ), 
  'section'     => 'member_profile_page_section',
  'default'     => '#f9f9f9',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '.um-profile-meta',
           'property' => 'border-color',
          'suffix' => ' !important',
        ),
    )
] );


Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'member_profile_header_um-name',
  'label'       => __( 'Member Name Color', 'kirki' ), 
  'section'     => 'member_profile_page_section',
  'default'     => '#303030',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '.um-name a',
           'property' => 'color',
             'suffix' => ' !important',
        ),
    )
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'member_profile_header_um-profile-meta',
  'label'       => __( 'Profile Header Meta Fields Color', 'kirki' ), 
  'section'     => 'member_profile_page_section',
  'default'     => '#606060',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '.um-meta-text, .um-profile-meta',
           'property' => 'color',
             'suffix' => ' !important',
        ),
    )
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'member_profile_header_um-profile-meta_link',
  'label'       => __( 'Profile Header Meta Fields Link Color', 'kirki' ), 
  'section'     => 'member_profile_page_section',
  'default'     => ' ',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '.um-meta a',
           'property' => 'color',
             'suffix' => ' !important',
        ),
    )
] );


Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'custom',
  'settings'    => 'profile_body_content',
 // 'label'       => esc_html__( 'This is the label', 'kirki' ),
  'section'     => 'member_profile_page_section',
  'default'     => '<div style="padding: 10px;background-color: #333; color: #fff; border-radius: 3px;">' . esc_html__( 'The below color settings for Right Side Profile Body Content Section.', 'kirki' ) . '</div>',
  'priority'    => 10,
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'profile_content_box_bg_color',
  'label'       => __( 'Profile Body Content Box Background Color', 'kirki' ), 
  'section'     => 'member_profile_page_section',
  'default'     => '',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '
          .um-profile .um-profile-body',
          'property' => 'background-color',
          'suffix' => ' !important',
        ),

          array(
      'element'  => '.um-profile .um-profile-body',
      'property' => 'border-color',
         ),
    )
] );


Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'profile_menu_btn_bg_color',
  'label'       => __( 'Profile Menu Button Background Color', 'kirki' ), 
  'section'     => 'member_profile_page_section',
  'default'     => '#333333',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '
          .um-profile-nav a, .um-profile-nav-item a
          ',
          'property' => 'background-color',
          'suffix' => ' !important',
        ),
    )
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'profile_menu_btn_txt_color',
  'label'       => __( 'Profile Menu Button Text Color', 'kirki' ), 
  'section'     => 'member_profile_page_section',
  'default'     => '#ffffff',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '
          .um-profile-nav-item a
          ',
          'property' => 'color',
          'suffix' => ' !important',
        ),
    )
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'profile_menu_btn_hover_bg_color',
  'label'       => __( 'Profile Menu Button Hover Background Color', 'kirki' ), 
  'section'     => 'member_profile_page_section',
  'default'     => '#f9f9f9',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '
          .um-profile-nav a:hover, .um-profile-nav-item a:hover
          ',
          'property' => 'background-color',
          'suffix' => ' !important',
        ),
    )
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'profile_menu_btn_hover_text_color',
  'label'       => __( 'Profile Menu Button Hover Text Color', 'kirki' ), 
  'section'     => 'member_profile_page_section',
  'default'     => '#404040',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '
          .um-profile-nav a:hover, .um-profile-nav-item a:hover
          ',
          'property' => 'color',
          'suffix' => ' !important',
        ),
    )
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'profile_menu_active_btn_hover_bg_color',
  'label'       => __( 'Profile Menu Active Background Color', 'kirki' ), 
  'section'     => 'member_profile_page_section',
  'default'     => '#f9f9f9',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '
          .um .um-profile-nav-item.active a,
          .um .um-profile-nav-item.active a:hover
          ',
          'property' => 'background-color',
          'suffix' => ' !important',
        ),
      array(
      'element'  => '.um-profile-nav',
      'property' => 'border-bottom-color',
       'suffix' => ' !important',
      ),
    )
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'profile_menu_active_btn_text_color',
  'label'       => __( 'Profile Menu Active Text Color', 'kirki' ), 
  'section'     => 'member_profile_page_section',
  'default'     => '#404040',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '
          .um .um-profile-nav-item.active a,
          .um .um-profile-nav-item.active a:hover a
          ',
          'property' => 'color',
          'suffix' => ' !important',
        ),
    )
] );

/**
 * Add sections for search filter
 */
Kirki::add_section( 'search_filters_panel_section', array(
    'title'          => esc_html__( 'Search Filters Color Settings ', 'kirki' ),
    'panel'          => 'um_color_panel',    
   // 'description' => esc_html__( 'You can control Footer Text from here.', 'kirki' ),
    'priority'       => 12,
) );


Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'search_filter_box_bg_color',
  'label'       => __( 'Search Filter Box BG Color', 'kirki' ), 
  'section'     => 'search_filters_panel_section',
  'default'     => '#f9f9f9',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '.search_filter_header_wrap, .search_filter_header_wrap_sidebar',
           'property' => 'background-color',
        ),
    )
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'search_filter_box_txt_color',
  'label'       => __( 'Search Filter Box Text Color', 'kirki' ), 
  'section'     => 'search_filters_panel_section',
  'default'     => '#888',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '.search_filter_header_wrap, .search_filter_header_wrap_sidebar, .um-member-directory-filters-a um-member-directory-filters-visible,
          .um-member-directory-filters-a a, .um-clear-filters-a',
           'property' => 'color',
        ),
    )
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'search_filter_box_link_color',
  'label'       => __( 'Search Filter Box Link Color', 'kirki' ), 
  'section'     => 'search_filters_panel_section',
  'default'     => '#888',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '.search_filter_header_wrap a, .search_filter_header_wrap a,
                          .um-directory .um-member-directory-header .um-member-directory-header-row .um-member-directory-nav-line .um-member-directory-view-type .um-member-directory-view-type-a
            ',
           'property' => 'color',
            'suffix' => ' !important',
        ),
    )
] );


Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'search_filter_btn_bg_color',
  'label'       => __( 'Search Filter Button BG Color', 'kirki' ), 
  'section'     => 'search_filters_panel_section',
  'default'     => '#eee',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '.um-member-directory-search-line button, .um-member-directory-search-line input[type="button"], 
          .um-member-directory-search-line input[type="reset"], .um-member-directory-search-line input[type="submit"]',
           'property' => 'background-color',
        ),
    )
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'search_filter_btn_text_color',
  'label'       => __( 'Search Filter Button Text Color', 'kirki' ), 
  'section'     => 'search_filters_panel_section',
  'default'     => '#444',
  'transport'   => 'auto',
  'output'      => array(
        array(
           'element'  => '.um-member-directory-search-line button, .um-member-directory-search-line input[type="button"], 
          .um-member-directory-search-line input[type="reset"], .um-member-directory-search-line input[type="submit"]',
           'property' => 'color',
        ),
    )
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'search_filter_slider_bg_color',
  'label'       => __( 'Search Filter Slider BG Color', 'kirki' ), 
  'section'     => 'search_filters_panel_section',
  'default'     => '#ccc',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '.um-directory .um-member-directory-header .um-member-directory-header-row .um-search .um-search-filter.um-slider-filter-type .um-slider .ui-slider-range.ui-widget-header,
          .ui-slider-handle,
          .ui-slider-handle:hover, .ui-slider-handle:active,
          .ui-slider .ui-slider-handle
          ',
           'property' => 'background-color',
        ),

        array(
      'element'  => '.um-directory .um-member-directory-header .um-member-directory-header-row .um-search .um-search-filter.um-slider-filter-type .um-slider .ui-slider-range.ui-widget-header',
      'property' => 'border-color',
       'suffix' => ' !important',
      ),
    )
] );



Kirki::add_field( 'theme_config_id', [
  'type'        => 'checkbox',
  'settings'    => 'search_box_left_checkbox_setting',
  'label'       => esc_html__( 'Enable Left Search Box', 'kirki' ),
  'description' => esc_html__( 'It works when you selected "Sidebar Search Filter" from " Ultimate Member > Member Directories" "', 'kirki' ),
  'section'     => 'search_filters_panel_section',
  'default'     => false,
] );



/**
 * Add sections for Default BLUE Color settings
 */
Kirki::add_section( 'default_blue_color_panel_section', array(
    'title'          => esc_html__( 'Default Button and Link Color Settings ', 'kirki' ),
    'panel'          => 'um_color_panel',    
    'description' => esc_html__( ' Where you can manage all Member Directory related buttons and hyper links.', 'kirki' ),
    'priority'       => 12,
) );


/* Link colors */
Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'um_default_link_color',
  'label'       => __( 'Link Color', 'kirki' ), 
  'section'     => 'default_blue_color_panel_section',
  'default'     => '#ff5722',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '
          .um a.um-link,
.um .um-tip:hover,
.um .um-field-radio.active:not(.um-field-radio-state-disabled) i,
.um .um-field-checkbox.active:not(.um-field-radio-state-disabled) i,
.um .um-member-name a:hover,
.um .um-member-more a:hover,
.um .um-member-less a:hover,
.um .um-members-pagi a:hover,
.um .um-cover-add:hover,
.um .um-profile-subnav a.active,
.um .um-item-meta a,
.um-account-name a:hover,
.um-account-nav a.current,
.um-account-side li a.current span.um-account-icon,
.um-account-side li a.current:hover span.um-account-icon,
.um-dropdown li a:hover,
i.um-active-color,
span.um-active-color
          ',
           'property' => 'color',
           'suffix' => ' !important',
        ),
    )
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'um_default_link_hover_color',
  'label'       => __( 'Link Color Hover Color', 'kirki' ), 
  'section'     => 'default_blue_color_panel_section',
  'default'     => '#ec4a16',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '
        .um a.um-link:hover,
    .um a.um-link-hvr:hover
          ',
           'property' => 'color',
           'suffix' => ' !important',
        ),
    )
] );




/* Button colors */
Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'um_default_btn_bg_color',
  'label'       => __( 'Button Background Color', 'kirki' ), 
  'section'     => 'default_blue_color_panel_section',
  'default'     => '#ff5722',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '
          .um .um-field-group-head,
.picker__box,
.picker__nav--prev:hover,
.picker__nav--next:hover,
.um .um-members-pagi span.current,
.um .um-members-pagi span.current:hover,
.upload,
.um-modal-header,
.um-modal-btn,
.um-modal-btn.disabled,
.um-modal-btn.disabled:hover,
div.uimob800 .um-account-side li a.current,
div.uimob800 .um-account-side li a.current:hover,
.um .um-button,
.um a.um-button,
.um a.um-button.um-disabled:hover,
.um a.um-button.um-disabled:focus,
.um a.um-button.um-disabled:active,
.um input[type=submit].um-button,
.um input[type=submit].um-button:focus,
.um input[type=submit]:disabled:hover, .cc_print_button,
.um-request-button,
span#sndmailusers,
span#sndmailfrnds,
div#send_friends span#sendmail_friends,
div#send_users span#sendmail_members
          ',
          'property' => 'background-color',
          'suffix' => ' !important',
        ), 

 )
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'um_default_btn_txt_color',
  'label'       => __( 'Button Text Color', 'kirki' ), 
  'section'     => 'default_blue_color_panel_section',
  'default'     => '#ffffff',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '
          .um .um-field-group-head,
.picker__box,
.picker__nav--prev:hover,
.picker__nav--next:hover,
.um .um-members-pagi span.current,
.um .um-members-pagi span.current:hover,
.upload,
.um-modal-header,
.um-modal-btn,
.um-modal-btn.disabled,
.um-modal-btn.disabled:hover,
div.uimob800 .um-account-side li a.current,
div.uimob800 .um-account-side li a.current:hover,
.um .um-button,
.um a.um-button,
.um a.um-button.um-disabled:hover,
.um a.um-button.um-disabled:focus,
.um a.um-button.um-disabled:active,
.um input[type=submit].um-button,
.um input[type=submit].um-button:focus,
.um input[type=submit]:disabled:hover,
.cc_print_button,
.um-request-button,
span#sndmailusers,
span#sndmailfrnds,
div#send_friends span#sendmail_friends,
div#send_users span#sendmail_members
          ',
          'property' => 'color',
          'suffix' => ' !important',
        ),
    )
] );



Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'um_default_btn_bg_hover_color',
  'label'       => __( 'Button Background Hover Color', 'kirki' ), 
  'section'     => 'default_blue_color_panel_section',
  'default'     => '#ec4a16',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '
          .um .um-field-group-head:hover,
.picker__footer,
.picker__header,
.picker__day--infocus:hover,
.picker__day--outfocus:hover,
.picker__day--highlighted:hover,
.picker--focused .picker__day--highlighted,
.picker__list-item:hover,
.picker__list-item--highlighted:hover,
.picker--focused .picker__list-item--highlighted,
.picker__list-item--selected,
.picker__list-item--selected:hover,
.picker--focused .picker__list-item--selected,
.um .um-button:hover,
.um a.um-button:hover,
.um input[type=submit].um-button:hover,
.um-request-button:hover,
span#sndmailusers:hover,
span#sndmailfrnds:hover,
div#send_friends span#sendmail_friends:hover
          ',
          'property' => 'background-color',
          'suffix' => ' !important',
        ),

      
    )
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'um_default_btn_bg_hover_text_color',
  'label'       => __( 'Button Background Hover Text Color', 'kirki' ), 
  'section'     => 'default_blue_color_panel_section',
  'default'     => '##eeeeee',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '
          .um .um-field-group-head:hover,
.picker__footer,
.picker__header,
.picker__day--infocus:hover,
.picker__day--outfocus:hover,
.picker__day--highlighted:hover,
.picker--focused .picker__day--highlighted,
.picker__list-item:hover,
.picker__list-item--highlighted:hover,
.picker--focused .picker__list-item--highlighted,
.picker__list-item--selected,
.picker__list-item--selected:hover,
.picker--focused .picker__list-item--selected,
.um .um-button:hover,
.um a.um-button:hover,
.um input[type=submit].um-button:hover,
.um-request-button:hover,
span#sndmailusers:hover,
span#sndmailfrnds:hover,
div#send_friends span#sendmail_friends:hover,
div#send_users span#sendmail_members:hover
          ',
          'property' => 'color',
          'suffix' => ' !important',
        ),

      
    )
] );



/* Button alt colors */
Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'um_default_alt_btn_bg_color',
  'label'       => __( 'Alternate Button Background Color', 'kirki' ), 
  'section'     => 'default_blue_color_panel_section',
  'default'     => '#eeeeee',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '
          .um .um-button.um-alt, 
      .um input[type=submit].um-button.um-alt',

          'property' => 'background-color',
          'suffix' => ' !important',
        ),
    )
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'um_default_alt_btn_txt_color',
  'label'       => __( 'Alternate Button Text Color', 'kirki' ), 
  'section'     => 'default_blue_color_panel_section',
  'default'     => '#333333',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '
          .um .um-button.um-alt, 
      .um input[type=submit].um-button.um-alt',

          'property' => 'color',
          'suffix' => ' !important',
        ),
    )
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'color',
  'settings'    => 'um_default_alt_btn_bg_hover_color',
  'label'       => __( 'Alternate Button Background Hover Color', 'kirki' ), 
  'section'     => 'default_blue_color_panel_section',
  'default'     => '#e5e5e5',
  'transport'   => 'auto',
  'output'      => array(
        array(
          'element'  => '
          .um .um-button.um-alt:hover,
      .um input[type=submit].um-button.um-alt:hover
          ',
          'property' => 'background-color',
          'suffix' => ' !important',
        ),
    )
] );


/*---------------------------------------------------------------------------------------
Shortlist Member Section Starting....
---------------------------------------------------------------------------------------*/
/**
 * Add sections
 */
Kirki::add_section( 'talenthunt_shortlist_member_panel_section', array(
    'title'          => esc_html__( 'Shortlist Member Options Settings', 'talenthunt' ),
    'panel'          => 'um_color_panel',    
    'description' => esc_html__( 'Note: This section works only when you installed and activated "UM - Shortlist Member" addon', 'talenthunt' ),
    'priority'       => 15,
) );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'custom',
  'settings'    => 'slm_user_mail_section',
 // 'label'       => esc_html__( 'This is the label', 'kirki' ),
  'section'     => 'talenthunt_shortlist_member_panel_section',
  'default'     => '<div style="padding: 10px;background-color: #333; color: #fff; border-radius: 3px;">' . esc_html__( '"SEND MAIL TO USERS " Option Settings.', 'kirki' ) . '</div>',
  'priority'    => 10,
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'     => 'text',
  'settings' => 'slm_user_mail_button_text',
  'label'    => esc_html__( 'Send Mail To Users Button Text', 'talenthunt' ),
  'section'  => 'talenthunt_shortlist_member_panel_section',
  'priority' => 15,
  'transport'   => 'auto',
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'     => 'text',
  'settings' => 'slm_user_mail_subject_text',
  'label'    => esc_html__( 'Send Mail To Users Subject Text', 'talenthunt' ),
  'section'  => 'talenthunt_shortlist_member_panel_section',
  'priority' => 15,
  'transport'   => 'auto',
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'     => 'text',
  'settings' => 'slm_user_mail_message_text',
  'label'    => esc_html__( 'Send Mail To Users Message Text', 'talenthunt' ),
  'section'  => 'talenthunt_shortlist_member_panel_section',
  'priority' => 15,
  'transport'   => 'auto',
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'     => 'text',
  'settings' => 'slm_user_mail_submit_button_text',
  'label'    => esc_html__( 'Send Mail To Users Submit Button Text', 'talenthunt' ),
  'section'  => 'talenthunt_shortlist_member_panel_section',
  'priority' => 15,
  'transport'   => 'auto',
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'        => 'custom',
  'settings'    => 'slm_send_mail_friends_section',
 // 'label'       => esc_html__( 'This is the label', 'kirki' ),
  'section'     => 'talenthunt_shortlist_member_panel_section',
  'default'     => '<div style="padding: 10px;background-color: #333; color: #fff; border-radius: 3px;">' . esc_html__( '"SEND MAIL TO FRIENDS " Option Settings.', 'kirki' ) . '</div>',
  'priority'    => 16,
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'     => 'text',
  'settings' => 'slm_friends_mail_button_text',
  'label'    => esc_html__( 'Send Mail To Friends Button Text', 'talenthunt' ),
  'section'  => 'talenthunt_shortlist_member_panel_section',
  'priority' => 17,
  'transport'   => 'auto',
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'     => 'text',
  'settings' => 'slm_friends_mail_to_text',
  'label'    => esc_html__( 'Send Mail To Friends "To" Text', 'talenthunt' ),
  'section'  => 'talenthunt_shortlist_member_panel_section',
  'priority' => 17,
  'transport'   => 'auto',
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'     => 'text',
  'settings' => 'slm_friends_mail_multiple_email_note_text',
  'label'    => esc_html__( 'Send Mail To Friends "Multiple Email note" Text', 'talenthunt' ),
  'section'  => 'talenthunt_shortlist_member_panel_section',
  'priority' => 17,
  'transport'   => 'auto',
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'     => 'text',
  'settings' => 'slm_friends_mail_subject_text',
  'label'    => esc_html__( 'Send Mail To Friends "Subject" Text', 'talenthunt' ),
  'section'  => 'talenthunt_shortlist_member_panel_section',
  'priority' => 17,
  'transport'   => 'auto',
] );

Kirki::add_field( 'kirki_theme_config', [
  'type'     => 'text',
  'settings' => 'slm_friends_mail_message_text',
  'label'    => esc_html__( 'Send Mail To Friends "Message" Text', 'talenthunt' ),
  'section'  => 'talenthunt_shortlist_member_panel_section',
  'priority' => 17,
  'transport'   => 'auto',
] );
Kirki::add_field( 'kirki_theme_config', [
  'type'     => 'text',
  'settings' => 'slm_friends_mail_send_button_text',
  'label'    => esc_html__( 'Send Mail To Friends "Send Button" Text', 'talenthunt' ),
  'section'  => 'talenthunt_shortlist_member_panel_section',
  'priority' => 17,
  'transport'   => 'auto',
] );

}
