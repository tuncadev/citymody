/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {

	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );

	wp.customize( 'text_logo_color', function( value ) {
		value.bind( function( to ) {
			var text_logo_color = '#logo h1.site-title a{ color:'+ to +'; }';
			if( $(document).find('#text_logo_color').length ){
				$(document).find('#text_logo_color').remove();
			}
			$(document).find('head').append($('<style id=text_logo_color>' + text_logo_color + '</style>'));
		});
	} );

	wp.customize( 'text_logo_tagline_color', function( value ) {
		value.bind( function( to ) {
			$('#logo p').css('color', to);
		});
	} );

	// Top Header Section
	wp.customize( 'top_header_bg_color', function( value ) {
		value.bind( function( to ) {
			var top_header_bg_color = '.top-header-section{ background:'+ to +'!important; }';
			if( $(document).find('#top_header_bg_color').length ){
				$(document).find('#top_header_bg_color').remove();
			}
			$(document).find('head').append($('<style id=top_header_bg_color>' + top_header_bg_color + '</style>'));
		});
	} );

	wp.customize( 'top_header_text_color', function( value ) {
		value.bind( function( to ) {
			var top_header_text_color = '.top-header-section{ color:'+ to +'; }';
			if( $(document).find('#top_header_text_color').length ){
				$(document).find('#top_header_text_color').remove();
			}
			$(document).find('head').append($('<style id=top_header_text_color>' + top_header_text_color + '</style>'));
		});
	} );

	wp.customize( 'top_header_link_color', function( value ) {
		value.bind( function( to ) {
			var top_header_link_color = '.top-header-section a{ color:'+ to +'; }';
			if( $(document).find('#top_header_link_color').length ){
				$(document).find('#top_header_link_color').remove();
			}
			$(document).find('head').append($('<style id=top_header_link_color>' + top_header_link_color + '</style>'));
		});
	} );


	wp.customize( 'top_header_link_hover_color', function( value ) {
		value.bind( function( to ) {
			var top_header_link_hover_color = '.top-header-section a:hover{ color:'+ to +'; }';
			if( $(document).find('#top_header_link_hover_color').length ){
				$(document).find('#top_header_link_hover_color').remove();
			}
			$(document).find('head').append($('<style id=top_header_link_hover_color>' + top_header_link_hover_color + '</style>'));
		});
	} );

	// Header background Color
	wp.customize( 'header_bg_color', function( value ) {
		value.bind( function( to ) {
			var header_bg_color = '#kaya-header-content-wrapper{ background:'+ to +'!important; }';
			if( $(document).find('#header_bg_color').length ){
				$(document).find('#header_bg_color').remove();
			}
			$(document).find('head').append($('<style id=header_bg_color>' + header_bg_color + '</style>'));
		});
	} );

	wp.customize( 'header_left_bg_color', function( value ) {
		value.bind( function( to ) {
			var header_left_bg_color = '#kaya-header-content-wrapper .two_third{ background:'+ to +'; }';
			if( $(document).find('#header_left_bg_color').length ){
				$(document).find('#header_left_bg_color').remove();
			}
			$(document).find('head').append($('<style id=header_left_bg_color>' + header_left_bg_color + '</style>'));
		});
	} );

	// Logo Section
	wp.customize( 'logo_image', function( value ) {
		value.bind( function( to ) {
			$('#logo a img').attr('src',to);
		} );
	} );

	//Site Logo Text Font Section
	wp.customize( 'site_logo_title_fontsize', function( value ) {
		value.bind( function( to ) {
			var site_logo_title_fontsize = 'h1.site-title a{ font-size:'+ to +'px !important; }';
			if( $(document).find('#site_logo_title_fontsize').length ){
				$(document).find('#site_logo_title_fontsize').remove();
			}
			$(document).find('head').append($('<style id=site_logo_title_fontsize>' + site_logo_title_fontsize + '</style>'));
		});
	} );
	
	wp.customize( 'site_title_font_weight', function( value ) {
		value.bind( function( to ) {
			var site_title_font_weight = 'h1.site-title a{ font-weight:'+ to +'!important; }';
			if( $(document).find('#site_title_font_weight').length ){
				$(document).find('#site_title_font_weight').remove();
			}
			$(document).find('head').append($('<style id=site_title_font_weight>' + site_title_font_weight + '</style>'));
		});
	} );

	wp.customize( 'site_title_font_family', function( value ) {
		value.bind( function( to ) {
			var site_title_font_family = 'h1.site-title a{ font-family:'+ to +'!important; }';
			if( $(document).find('#site_title_font_family').length ){
				$(document).find('#site_title_font_family').remove();
			}
			$(document).find('head').append($('<style id=site_title_font_family>' + site_title_font_family + '</style>'));
		});
	} );
	
	wp.customize( 'site_title_font_style', function( value ) {
		value.bind( function( to ) {
			var site_title_font_style = 'h1.site-title a{ font-style:'+ to +'!important; }';
			if( $(document).find('#site_title_font_style').length ){
				$(document).find('#site_title_font_style').remove();
			}
			$(document).find('head').append($('<style id=site_title_font_style>' + site_title_font_style + '</style>'));
		});
	} );
	
	wp.customize( 'site_logo_tag_line_fontsize', function( value ) {
		value.bind( function( to ) {
			var site_logo_tag_line_fontsize = 'p.site-description{ font-size:'+ to +'px !important; }';
			if( $(document).find('#site_logo_tag_line_fontsize').length ){
				$(document).find('#site_logo_tag_line_fontsize').remove();
			}
			$(document).find('head').append($('<style id=site_logo_tag_line_fontsize>' + site_logo_tag_line_fontsize + '</style>'));
		});
	} );

	wp.customize( 'site_tag_line_font_weight', function( value ) {
		value.bind( function( to ) {
			var site_tag_line_font_weight = 'p.site-description{ font-weight:'+ to +'!important; }';
			if( $(document).find('#site_tag_line_font_weight').length ){
				$(document).find('#site_tag_line_font_weight').remove();
			}
			$(document).find('head').append($('<style id=site_tag_line_font_weight>' + site_tag_line_font_weight + '</style>'));
		});
	} );

	wp.customize( 'site_tag_line_font_family', function( value ) {
		value.bind( function( to ) {
			var site_tag_line_font_family = 'p.site-description{ font-family:'+ to +'!important; }';
			if( $(document).find('#site_tag_line_font_family').length ){
				$(document).find('#site_tag_line_font_family').remove();
			}
			$(document).find('head').append($('<style id=site_tag_line_font_family>' + site_tag_line_font_family + '</style>'));
		});
	} );

	wp.customize( 'site_tag_line_font_style', function( value ) {
		value.bind( function( to ) {
			var site_tag_line_font_style = 'p.site-description{ font-style:'+ to +'!important; }';
			if( $(document).find('#site_tag_line_font_style').length ){
				$(document).find('#site_tag_line_font_style').remove();
			}
			$(document).find('head').append($('<style id=site_tag_line_font_style>' + site_tag_line_font_style + '</style>'));
		});
	} );

	// Menu Color Section
	wp.customize( 'menu_bg_color', function( value ) {
		value.bind( function( to ) {
			var menu_bg_color = '#header-navigation ul{ background:'+ to +'; }';
			if( $(document).find('#menu_bg_color').length ){
				$(document).find('#menu_bg_color').remove();
			}
			$(document).find('head').append($('<style id=menu_bg_color>' + menu_bg_color + '</style>'));
		} );
	} );
	wp.customize( 'menu_link_color', function( value ) {
		value.bind( function( to ) {
			var menu_link_color = '#header-navigation ul li a{ color:'+ to +'; }';
			if( $(document).find('#menu_link_color').length ){
				$(document).find('#menu_link_color').remove();
			}
			$(document).find('head').append($('<style id=menu_link_color>' + menu_link_color + '</style>'));
		} );
	} );
	wp.customize( 'menu_link_icon_color', function( value ) {
		value.bind( function( to ) {
			var menu_link_icon_color = '.menu > ul > li.fa:before{ color:'+ to +'; }';
			if( $(document).find('#menu_link_icon_color').length ){
				$(document).find('#menu_link_icon_color').remove();
			}
			$(document).find('head').append($('<style id=menu_link_icon_color>' + menu_link_icon_color + '</style>'));
		} );
	} );
	wp.customize( 'menu_link_hover_color', function( value ) {
		value.bind( function( to ) {
			var menu_link_hover_color = '#header-navigation ul li a:hover{ color:'+ to +'; }';
			if( $(document).find('#menu_link_hover_color').length ){
				$(document).find('#menu_link_hover_color').remove();
			}
			$(document).find('head').append($('<style id=menu_link_hover_color>' + menu_link_hover_color + '</style>'));
		});
	} );

	wp.customize( 'menu_link_icon_hover_color', function( value ) {
		value.bind( function( to ) {
			var menu_link_icon_hover_color = '#header-navigation ul li:hover:before{ color:'+ to +'; }';
			if( $(document).find('#menu_link_icon_hover_color').length ){
				$(document).find('#menu_link_icon_hover_color').remove();
			}
			$(document).find('head').append($('<style id=menu_link_icon_hover_color>' + menu_link_icon_hover_color + '</style>'));
		});
	} );

	wp.customize( 'menu_hover_border_color', function( value ) {
		value.bind( function( to ) {
			var menu_hover_border_color = '	#header-navigation ul li a:hover{ color:'+ to +'; }';
			if( $(document).find('#menu_hover_border_color').length ){
				$(document).find('#menu_hover_border_color').remove();
			}
			$(document).find('head').append($('<style id=menu_hover_border_color>' + menu_hover_border_color + '</style>'));
		});
	} );

	wp.customize( 'menu_link_left_border_color', function( value ) {
		value.bind( function( to ) {
			var menu_link_left_border_color = '#main-nav ul#main-menu > li a{ border-left:1px solid '+ to +'; }';
			if( $(document).find('#menu_link_left_border_color').length ){
				$(document).find('#menu_link_left_border_color').remove();
			}
			$(document).find('head').append($('<style id=menu_link_left_border_color>' + menu_link_left_border_color + '</style>'));
		});
	} );

	wp.customize( 'menu_link_right_border_color', function( value ) {
		value.bind( function( to ) {
			var menu_link_left_border_color = '#main-nav ul#main-menu > li a{ border-right:1px solid '+ to +'; }';
			if( $(document).find('#menu_link_right_border_color').length ){
				$(document).find('#menu_link_right_border_color').remove();
			}
			$(document).find('head').append($('<style id=menu_link_right_border_color>' + menu_link_right_border_color + '</style>'));
		});
	} );


	wp.customize( 'menu_active_link_color', function( value ) {
		value.bind( function( to ) {
			var menu_active_link_color = '#header-navigation ul li.current-menu-item.current_page_item a{ color:'+ to +'!important; }';
			if( $(document).find('#menu_active_link_color').length ){
				$(document).find('#menu_active_link_color').remove();
			}
			$(document).find('head').append($('<style id=menu_active_link_color>' + menu_active_link_color + '</style>'));
		} );
	} );

	wp.customize( 'menu_active_link_bg_color', function( value ) {
		value.bind( function( to ) {
			var menu_active_link_bg_color = '#header-navigation ul li.current-menu-item.current_page_item a{ background:'+ to +'; }';
			if( $(document).find('#menu_active_link_bg_color').length ){
				$(document).find('#menu_active_link_bg_color').remove();
			}
			$(document).find('head').append($('<style id=menu_active_link_bg_color>' + menu_active_link_bg_color + '</style>'));
		});
	} );


	wp.customize( 'menu_active_link_icon_color', function( value ) {
		value.bind( function( to ) {
			var menu_active_link_icon_color = '#header-navigation ul li.current-menu-item.current_page_item:before{ color:'+ to +'; }';
			if( $(document).find('#menu_active_link_icon_color').length ){
				$(document).find('#menu_active_link_icon_color').remove();
			}
			$(document).find('head').append($('<style id=menu_active_link_icon_color>' + menu_active_link_icon_color + '</style>'));
		});
	} );
	wp.customize( 'menu_active_border_color', function( value ) {
		value.bind( function( to ) {
			var menu_active_border_color = '#header-navigation ul li.current-menu-item.current_page_item > a{ color:'+ to +'; }';
			if( $(document).find('#menu_active_border_color').length ){
				$(document).find('#menu_active_border_color').remove();
			}
			$(document).find('head').append($('<style id=menu_active_border_color>' + menu_active_border_color + '</style>'));
		});
	} );

	
	wp.customize( 'child_menu_bg_color', function( value ) {
		value.bind( function( to ) {
			var child_menu_bg_color = '.sub-menu li a{ background:'+ to +'!important; }';
			if( $(document).find('#child_menu_bg_color').length ){
				$(document).find('#child_menu_bg_color').remove();
			}
			$(document).find('head').append($('<style id=child_menu_bg_color>' + child_menu_bg_color + '</style>'));
		} );
	} );
	wp.customize( 'child_menu_link_color', function( value ) {
		value.bind( function( to ) {
			var child_menu_link_color = '.sub-menu li a{ color:'+ to +'!important; }';
			if( $(document).find('#child_menu_link_color').length ){
				$(document).find('#child_menu_link_color').remove();
			}
			$(document).find('head').append($('<style id=child_menu_link_color>' + child_menu_link_color + '</style>'));
		} );
	} );
	wp.customize( 'child_menu_hover_bg_color', function( value ) {
		value.bind( function( to ) {
			var child_menu_hover_bg_color = '#header-navigation ul ul li a:hover{ background:'+ to +'!important; }';
			if( $(document).find('#child_menu_hover_bg_color').length ){
				$(document).find('#child_menu_hover_bg_color').remove();
			}
			$(document).find('head').append($('<style id=child_menu_hover_bg_color>' + child_menu_hover_bg_color + '</style>'));
		} );
	} );

	wp.customize( 'child_menu_link_hover_color', function( value ) {
		value.bind( function( to ) {
			var child_menu_link_hover_color = '#header-navigation ul ul li a:hover{ color:'+ to +'!important; }';
			if( $(document).find('#child_menu_link_hover_color').length ){
				$(document).find('#child_menu_link_hover_color').remove();
			}
			$(document).find('head').append($('<style id=child_menu_link_hover_color>' + child_menu_link_hover_color + '</style>'));
		} );
	} );
	wp.customize( 'child_menu_active_bg_color', function( value ) {
		value.bind( function( to ) {
			var child_menu_active_bg_color = '#header-navigation ul ul li.current-menu-item a{ background:'+ to +'!important; }';
			if( $(document).find('#child_menu_active_bg_color').length ){
				$(document).find('#child_menu_active_bg_color').remove();
			}
			$(document).find('head').append($('<style id=child_menu_active_bg_color>' + child_menu_active_bg_color + '</style>'));
		} );
	} );
	wp.customize( 'child_menu_active_link_color', function( value ) {
		value.bind( function( to ) {
			var child_menu_active_link_color = '#header-navigation ul li.current-menu-item.current_page_item a{ color:'+ to +'!important; }';
			if( $(document).find('#child_menu_active_link_color').length ){
				$(document).find('#child_menu_active_link_color').remove();
			}
			$(document).find('head').append($('<style id=child_menu_active_link_color>' + child_menu_active_link_color + '</style>'));
		} );
	} );
	wp.customize( 'child_menu_border_bottom_color', function( value ) {
		value.bind( function( to ) {
			var child_menu_border_bottom_color = '.sub-menu li a{ border-bottom:1px solid'+ to +'!important; }';
			if( $(document).find('#child_menu_border_bottom_color').length ){
				$(document).find('#child_menu_border_bottom_color').remove();
			}
			$(document).find('head').append($('<style id=child_menu_border_bottom_color>' + child_menu_border_bottom_color + '</style>'));
		} );
	} );

	wp.customize( 'shortlist_link_BG_color', function( value ) {
		value.bind( function( to ) {
			var shortlist_link_BG_color = '.shortlist-align a{ background:'+ to +'!important; }';
			if( $(document).find('#shortlist_link_BG_color').length ){
				$(document).find('#shortlist_link_BG_color').remove();
			}
			$(document).find('head').append($('<style id=shortlist_link_BG_color>' + shortlist_link_BG_color + '</style>'));
		} );
	} );

	wp.customize( 'shortlist_link_color', function( value ) {
		value.bind( function( to ) {
			var shortlist_link_color = '.shortlist-align a{ color:'+ to +'!important; }';
			if( $(document).find('#shortlist_link_color').length ){
				$(document).find('#shortlist_link_color').remove();
			}
			$(document).find('head').append($('<style id=shortlist_link_color>' + shortlist_link_color + '</style>'));
		} );
	} );

	wp.customize( 'search_icon_color', function( value ) {
		value.bind( function( to ) {
			var search_icon_color = '.toggle_search_icon{ color:'+ to +'!important; }';
			if( $(document).find('#search_icon_color').length ){
				$(document).find('#search_icon_color').remove();
			}
			$(document).find('head').append($('<style id=search_icon_color>' + search_icon_color + '</style>'));
		} );
	} );

	wp.customize( 'search_icon_BG_color', function( value ) {
		value.bind( function( to ) {
			var search_icon_BG_color = '.toggle_search_icon{ background:'+ to +'!important; }';
			if( $(document).find('#search_icon_BG_color').length ){
				$(document).find('#search_icon_BG_color').remove();
			}
			$(document).find('head').append($('<style id=search_icon_BG_color>' + search_icon_BG_color + '</style>'));
		} );
	} );
	
	// Header Right Section
	wp.customize( 'admin_menu_link_bg', function( value ) {
		value.bind( function( to ) {
			var admin_menu_link_bg = '.login-info #nav-user-dashboard-menu #user-main-menu li a{ background:'+ to +'; }';
			if( $(document).find('#admin_menu_link_bg').length ){
				$(document).find('#admin_menu_link_bg').remove();
			}
			$(document).find('head').append($('<style id=admin_menu_link_bg>' + admin_menu_link_bg + '</style>'));
		} );
	} );

	wp.customize( 'admin_menu_link_border', function( value ) {
		value.bind( function( to ) {
			var admin_menu_link_border = '.login-info #nav-user-dashboard-menu #user-main-menu li a{border:1px solid '+ to +'; }';
			if( $(document).find('#admin_menu_link_border').length ){
				$(document).find('#admin_menu_link_border').remove();
			}
			$(document).find('head').append($('<style id=admin_menu_link_border>' + admin_menu_link_border + '</style>'));
		} );
	} );

	wp.customize( 'admin_menu_link_color', function( value ) {
		value.bind( function( to ) {
			var admin_menu_link_color = '.login-info #nav-user-dashboard-menu #user-main-menu li a{ color:'+ to +'; }';
			if( $(document).find('#admin_menu_link_color').length ){
				$(document).find('#admin_menu_link_color').remove();
			}
			$(document).find('head').append($('<style id=admin_menu_link_color>' + admin_menu_link_color + '</style>'));
		} );
	} );

	// Advance Search Settings	
	wp.customize( 'advance_search_bg_color', function( value ) {
		value.bind( function( to ) {
			var advance_search_bg_color = 'div#flip i{ background:'+ to +'; }';
			if( $(document).find('#advance_search_bg_color').length ){
				$(document).find('#advance_search_bg_color').remove();
			}
			$(document).find('head').append($('<style id=advance_search_bg_color>' + advance_search_bg_color + '</style>'));
		} );
	} );

	wp.customize( 'advance_search_icon_border_color', function( value ) {
		value.bind( function( to ) {
			var advance_search_icon_border_color = 'div#flip i{border:1px solid '+ to +'; }';
			if( $(document).find('#advance_search_icon_border_color').length ){
				$(document).find('#advance_search_icon_border_color').remove();
			}
			$(document).find('head').append($('<style id=advance_search_icon_border_color>' + advance_search_icon_border_color + '</style>'));
		} );
	} );

	wp.customize( 'advance_search_icon_color', function( value ) {
		value.bind( function( to ) {
			var advance_search_icon_color = 'div#flip i{ color:'+ to +'; }';
			if( $(document).find('#advance_search_icon_color').length ){
				$(document).find('#advance_search_icon_color').remove();
			}
			$(document).find('head').append($('<style id=advance_search_icon_color>' + advance_search_icon_color + '</style>'));
		} );
	} );

	wp.customize( 'advance_search_panel_bg_color', function( value ) {
		value.bind( function( to ) {
			var advance_search_panel_bg_color = '#panel{ background:'+ to +'; }';
			if( $(document).find('#advance_search_panel_bg_color').length ){
				$(document).find('#advance_search_panel_bg_color').remove();
			}
			$(document).find('head').append($('<style id=advance_search_panel_bg_color>' + advance_search_panel_bg_color + '</style>'));
		} );
	} );

	wp.customize( 'advance_search_label_color', function( value ) {
		value.bind( function( to ) {
			var advance_search_label_color = '.advanced_search_forms label{ color:'+ to +'; }';
			if( $(document).find('#advance_search_label_color').length ){
				$(document).find('#advance_search_label_color').remove();
			}
			$(document).find('head').append($('<style id=advance_search_label_color>' + advance_search_label_color + '</style>'));
		} );
	} );

	wp.customize( 'search_ui_slider_range_bg_color', function( value ) {
		value.bind( function( to ) {
			var search_ui_slider_range_bg_color = '.ui-slider .ui-slider-range{ background:'+ to +'; }';
			if( $(document).find('#search_ui_slider_range_bg_color').length ){
				$(document).find('#search_ui_slider_range_bg_color').remove();
			}
			$(document).find('head').append($('<style id=search_ui_slider_range_bg_color>' + search_ui_slider_range_bg_color + '</style>'));
		} );
	} );

	wp.customize( 'advance_search_select_bg_color', function( value ) {
		value.bind( function( to ) {
			var advance_search_select_bg_coloradvance_search_select_bg_color = '.search_fields select{ background:'+ to +'; }';
			if( $(document).find('#advance_search_select_bg_color').length ){
				$(document).find('#advance_search_select_bg_color').remove();
			}
			$(document).find('head').append($('<style id=advance_search_select_bg_color>' + advance_search_select_bg_color + '</style>'));
		} );
	} );

	wp.customize( 'advance_search_select_color', function( value ) {
		value.bind( function( to ) {
			var advance_search_select_color = '.search_fields select{ color:'+ to +'; }';
			if( $(document).find('#advance_search_select_color').length ){
				$(document).find('#advance_search_select_color').remove();
			}
			$(document).find('head').append($('<style id=advance_search_select_color>' + advance_search_select_color + '</style>'));
		} );
	} );

	wp.customize( 'advance_search_select_border_color', function( value ) {
		value.bind( function( to ) {
			var advance_search_select_border_color = '.search_fields select{ border:1px solid '+ to +'; }';
			if( $(document).find('#advance_search_select_border_color').length ){
				$(document).find('#advance_search_select_border_color').remove();
			}
			$(document).find('head').append($('<style id=advance_search_select_border_color>' + advance_search_select_border_color + '</style>'));
		} );
	} );

	wp.customize( 'advance_search_button_background_color', function( value ) {
		value.bind( function( to ) {
			var advance_search_button_background_color = '.search_data_submit{ background: '+ to +'; }';
			if( $(document).find('#advance_search_button_background_color').length ){
				$(document).find('#advance_search_button_background_color').remove();
			}
			$(document).find('head').append($('<style id=advance_search_button_background_color>' + advance_search_button_background_color + '</style>'));
		} );
	} );

	wp.customize( 'advance_search_button_color', function( value ) {
		value.bind( function( to ) {
			var advance_search_button_color = '.search_data_submit{ color: '+ to +'; }';
			if( $(document).find('#advance_search_button_color').length ){
				$(document).find('#advance_search_button_color').remove();
			}
			$(document).find('head').append($('<style id=advance_search_button_color>' + advance_search_button_color + '</style>'));
		} );
	} );

	wp.customize( 'page_titlebar_font_size', function( value ) {
		value.bind( function( to ) {
			var page_titlebar_font_size = '.kaya-page-titlebar-wrapper .page-title{ font-size:'+ to +'px !important; }';
			if( $(document).find('#page_titlebar_font_size').length ){
				$(document).find('#page_titlebar_font_size').remove();
			}
			$(document).find('head').append($('<style id=page_titlebar_font_size>' + page_titlebar_font_size + '</style>'));
		});
	} );

	wp.customize( 'page_titlebar_padding_tb', function( value ) {
		value.bind( function( to ) {
			var page_titlebar_padding_tb = '.kaya-page-titlebar-wrapper{ padding:'+ to +'px 0px!important; }';
			if( $(document).find('#page_titlebar_padding_tb').length ){
				$(document).find('#page_titlebar_padding_tb').remove();
			}
			$(document).find('head').append($('<style id=page_titlebar_padding_tb>' + page_titlebar_padding_tb + '</style>'));
		});
	} );

	wp.customize( 'title_position', function( value ) {
		value.bind( function( to ) {
			var title_position = '.kaya-page-titlebar-wrapper{ text-align:'+ to +'!important; }';
			if( $(document).find('#title_position').length ){
				$(document).find('#title_position').remove();
			}
			$(document).find('head').append($('<style id=title_position>' + title_position + '</style>'));
		});
	} );

	// Single Page Section
	wp.customize( 'single_page_tabs_active_BG_color', function( value ) {
		value.bind( function( to ) {
			var single_page_tabs_active_BG_color = '.single_tabs_content_wrapper li.tab-active a{ background: '+ to +'!important; }';
			if( $(document).find('#single_page_tabs_active_BG_color').length ){
				$(document).find('#single_page_tabs_active_BG_color').remove();
			}
			$(document).find('head').append($('<style id=single_page_tabs_active_BG_color>' + single_page_tabs_active_BG_color + '</style>'));
		} );
	} );

	wp.customize( 'single_page_tabs_active_color', function( value ) {
		value.bind( function( to ) {
			var single_page_tabs_active_color = '.single_tabs_content_wrapper li.tab-active a{ color: '+ to +'!important; }';
			if( $(document).find('#single_page_tabs_active_color').length ){
				$(document).find('#single_page_tabs_active_color').remove();
			}
			$(document).find('head').append($('<style id=single_page_tabs_active_color>' + single_page_tabs_active_color + '</style>'));
		} );
	} );

	wp.customize( 'single_page_tabs_active_border_color', function( value ) {
		value.bind( function( to ) {
			var single_page_tabs_active_border_color = '.single_tabs_content_wrapper li.tab-active a{ border: 1px solid '+ to +'!important; }';
			if( $(document).find('#single_page_tabs_active_border_color').length ){
				$(document).find('#single_page_tabs_active_border_color').remove();
			}
			$(document).find('head').append($('<style id=single_page_tabs_active_border_color>' + single_page_tabs_active_border_color + '</style>'));
		} );
	} );

	wp.customize( 'single_page_tabs_BG_color', function( value ) {
		value.bind( function( to ) {
			var single_page_tabs_BG_color = 'ul.tabs_content_wrapper li a{ background: '+ to +'!important; }';
			if( $(document).find('#single_page_tabs_BG_color').length ){
				$(document).find('#single_page_tabs_BG_color').remove();
			}
			$(document).find('head').append($('<style id=single_page_tabs_BG_color>' + single_page_tabs_BG_color + '</style>'));
		} );
	} );

	wp.customize( 'single_page_tabs_border_bottom_color', function( value ) {
		value.bind( function( to ) {
			var single_page_tabs_border_bottom_color = 'ul.tabs_content_wrapper{ border-bottom: 3px solid '+ to +'!important; }';
			if( $(document).find('#single_page_tabs_border_bottom_color').length ){
				$(document).find('#single_page_tabs_border_bottom_color').remove();
			}
			$(document).find('head').append($('<style id=single_page_tabs_border_bottom_color>' + single_page_tabs_border_bottom_color + '</style>'));
		} );
	} );

	wp.customize( 'single_page_tabs_color', function( value ) {
		value.bind( function( to ) {
			var single_page_tabs_color = 'ul.tabs_content_wrapper li a{ color: '+ to +'!important; }';
			if( $(document).find('#single_page_tabs_color').length ){
				$(document).find('#single_page_tabs_color').remove();
			}
			$(document).find('head').append($('<style id=single_page_tabs_color>' + single_page_tabs_color + '</style>'));
		} );
	} );

	wp.customize( 'single_page_tabs_border_color', function( value ) {
		value.bind( function( to ) {
			var single_page_tabs_border_color = 'ul.tabs_content_wrapper li a{ border: 1px solid '+ to +'!important; }';
			if( $(document).find('#single_page_tabs_border_color').length ){
				$(document).find('#single_page_tabs_border_color').remove();
			}
			$(document).find('head').append($('<style id=single_page_tabs_border_color>' + single_page_tabs_border_color + '</style>'));
		} );
	} );

	wp.customize( 'shortlist_tab_BG_color', function( value ) {
		value.bind( function( to ) {
			var shortlist_tab_BG_color = '.cpt_posts_add_remove a{ background: '+ to +'!important; }';
			if( $(document).find('#shortlist_tab_BG_color').length ){
				$(document).find('#shortlist_tab_BG_color').remove();
			}
			$(document).find('head').append($('<style id=shortlist_tab_BG_color>' + shortlist_tab_BG_color + '</style>'));
		} );
	} );

	wp.customize( 'shortlist_tab_color', function( value ) {
		value.bind( function( to ) {
			var shortlist_tab_color = '.cpt_posts_add_remove a{ color: '+ to +'!important; }';
			if( $(document).find('#shortlist_tab_color').length ){
				$(document).find('#shortlist_tab_color').remove();
			}
			$(document).find('head').append($('<style id=shortlist_tab_color>' + shortlist_tab_color + '</style>'));
		} );
	} );

	// Page titlebar color section
	wp.customize( 'page_titlebar_bg_color', function( value ) {
		value.bind( function( to ) {
			var page_titlebar_bg_color = '.kaya-page-titlebar-wrapper{ background:'+ to +'; }';
			if( $(document).find('#page_titlebar_bg_color').length ){
				$(document).find('#page_titlebar_bg_color').remove();
			}
			$(document).find('head').append($('<style id=page_titlebar_bg_color>' + page_titlebar_bg_color + '</style>'));
		} );
	} );

	wp.customize( 'page_titlebar_color', function( value ) {
		value.bind( function( to ) {
			var page_titlebar_color = '.kaya-page-titlebar-wrapper .page-title{ color:'+ to +'; }';
			if( $(document).find('#page_titlebar_color').length ){
				$(document).find('#page_titlebar_color').remove();
			}
			$(document).find('head').append($('<style id=page_titlebar_color>' + page_titlebar_color + '</style>'));
		} );
	} );



	// Page middle color section
	wp.customize( 'page_mid_contant_bg_color', function( value ) {
		value.bind( function( to ) {
			var page_mid_contant_bg_color = '#kaya-mid-content-wrapper{ background:'+ to +'; }';
			if( $(document).find('#page_mid_contant_bg_color').length ){
				$(document).find('#page_mid_contant_bg_color').remove();
			}
			$(document).find('head').append($('<style id=page_mid_contant_bg_color>' + page_mid_contant_bg_color + '</style>'));
		} );
	} );
	wp.customize( 'page_mid_content_title_color', function( value ) {
		value.bind( function( to ) {
			var page_mid_content_title_color = '#kaya-mid-content-wrapper .mid-content h1, #kaya-mid-content-wrapper .mid-content h2, #kaya-mid-content-wrapper .mid-content h3, #kaya-mid-content-wrapper .mid-content h4, #kaya-mid-content-wrapper .mid-content h5, #kaya-mid-content-wrapper .mid-content h6, #kaya-mid-content-wrapper .mid-content h1 a, #kaya-mid-content-wrapper .mid-content h2 a, #kaya-mid-content-wrapper .mid-content h3 a, #kaya-mid-content-wrapper .mid-content h4 a, #kaya-mid-content-wrapper .mid-content h5 a, #kaya-mid-content-wrapper .mid-content h6 a{ color:'+ to +'; }';
			if( $(document).find('#page_mid_content_title_color').length ){
				$(document).find('#page_mid_content_title_color').remove();
			}
			$(document).find('head').append($('<style id=page_mid_content_title_color>' + page_mid_content_title_color + '</style>'));
		} );
	} );
	wp.customize( 'page_mid_content_color', function( value ) {
		value.bind( function( to ) {
			var page_mid_content_color = '#kaya-mid-content-wrapper .mid-content p, #kaya-mid-content-wrapper .mid-content span{ color:'+ to +'; }';
			if( $(document).find('#page_mid_content_color').length ){
				$(document).find('#page_mid_content_color').remove();
			}
			$(document).find('head').append($('<style id=page_mid_content_color>' + page_mid_content_color + '</style>'));
		} );
	} );
	wp.customize( 'page_mid_contant_links_color', function( value ) {
		value.bind( function( to ) {
			var page_mid_contant_links_color = '#kaya-mid-content-wrapper .mid-content a{ color:'+ to +'; }';
			if( $(document).find('#page_mid_contant_links_color').length ){
				$(document).find('#page_mid_contant_links_color').remove();
			}
			$(document).find('head').append($('<style id=page_mid_contant_links_color>' + page_mid_contant_links_color + '</style>'));
		} );
	} );
	wp.customize( 'page_mid_contant_links_hover_color', function( value ) {
		value.bind( function( to ) {
			var page_mid_contant_links_hover_color = '#kaya-mid-content-wrapper .mid-content a:hover{ color:'+ to +'; }';
			if( $(document).find('#page_mid_contant_links_hover_color').length ){
				$(document).find('#page_mid_contant_links_hover_color').remove();
			}
			$(document).find('head').append($('<style id=page_mid_contant_links_hover_color>' + page_mid_contant_links_hover_color + '</style>'));
		} );
	} );

	// Sidebar color section
	wp.customize( 'sidebar_bg_color', function( value ) {
		value.bind( function( to ) {
			var sidebar_bg_color = '#sidebar{ background:'+ to +'; }';
			if( $(document).find('#sidebar_bg_color').length ){
				$(document).find('#sidebar_bg_color').remove();
			}
			$(document).find('head').append($('<style id=sidebar_bg_color>' + sidebar_bg_color + '</style>'));
		} );
	} );
	wp.customize( 'sidebar_title_color', function( value ) {
		value.bind( function( to ) {
			var sidebar_title_color = '#sidebar h1, #sidebar h2, #sidebar h3, #sidebar h4, #sidebar h5, #sidebar h6{ color:'+ to +'; }';
			if( $(document).find('#sidebar_title_color').length ){
				$(document).find('#sidebar_title_color').remove();
			}
			$(document).find('head').append($('<style id=sidebar_title_color>' + sidebar_title_color + '</style>'));
		} );
	} );
	wp.customize( 'sidebar_title_border_color', function( value ) {
		value.bind( function( to ) {
			var sidebar_title_border_color = '#sidebar .widget-title:before{ background:'+ to +'; }';
			if( $(document).find('#sidebar_title_border_color').length ){
				$(document).find('#sidebar_title_border_color').remove();
			}
			$(document).find('head').append($('<style id=sidebar_title_border_color>' + sidebar_title_border_color + '</style>'));
		} );
	} );
	wp.customize( 'sidebar_content_color', function( value ) {
		value.bind( function( to ) {
			var sidebar_content_color = '#sidebar, #sidebar p, #sidebar span{ color:'+ to +'; }';
			if( $(document).find('#sidebar_content_color').length ){
				$(document).find('#sidebar_content_color').remove();
			}
			$(document).find('head').append($('<style id=sidebar_content_color>' + sidebar_content_color + '</style>'));
		} );
	} );
	wp.customize( 'sidebar_links_color', function( value ) {
		value.bind( function( to ) {
			var sidebar_links_color = '#sidebar a{ color:'+ to +'; }';
			if( $(document).find('#sidebar_links_color').length ){
				$(document).find('#sidebar_links_color').remove();
			}
			$(document).find('head').append($('<style id=sidebar_links_color>' + sidebar_links_color + '</style>'));
		} );
	} );
	wp.customize( 'sidebar_links_hover_color', function( value ) {
		value.bind( function( to ) {
			var sidebar_links_hover_color = '#sidebar a:hover{ color:'+ to +'; }';
			if( $(document).find('#sidebar_links_hover_color').length ){
				$(document).find('#sidebar_links_hover_color').remove();
			}
			$(document).find('head').append($('<style id=sidebar_links_hover_color>' + sidebar_links_hover_color + '</style>'));
		} );
	} );

	wp.customize( 'sidebar_links_icon_color', function( value ) {
		value.bind( function( to ) {
			var sidebar_links_icon_color = '#sidebar li a:after{ background:'+ to +'; }';
			if( $(document).find('#sidebar_links_icon_color').length ){
				$(document).find('#sidebar_links_icon_color').remove();
			}
			$(document).find('head').append($('<style id=sidebar_links_icon_color>' + sidebar_links_icon_color + '</style>'));
		} );
	} );

	wp.customize( 'sidebar_links_icon_color', function( value ) {
		value.bind( function( to ) {
			var sidebar_links_icon_color = '#sidebar li a:before{ background:'+ to +'; }';
			if( $(document).find('#sidebar_links_icon_color').length ){
				$(document).find('#sidebar_links_icon_color').remove();
			}
			$(document).find('head').append($('<style id=sidebar_links_icon_color>' + sidebar_links_icon_color + '</style>'));
		} );
	} );

	wp.customize( 'sidebar_links_icon_hover_color', function( value ) {
		value.bind( function( to ) {
			var sidebar_links_icon_hover_color = '#sidebar li a:after{ background:'+ to +'; }';
			if( $(document).find('#sidebar_links_icon_hover_color').length ){
				$(document).find('#sidebar_links_icon_hover_color').remove();
			}
			$(document).find('head').append($('<style id=sidebar_links_icon_hover_color>' + sidebar_links_icon_hover_color + '</style>'));
		} );
	} );

	wp.customize( 'sidebar_links_icon_hover_color', function( value ) {
		value.bind( function( to ) {
			var sidebar_links_icon_hover_color = '#sidebar li a:before{ background:'+ to +'; }';
			if( $(document).find('#sidebar_links_icon_hover_color').length ){
				$(document).find('#sidebar_links_icon_hover_color').remove();
			}
			$(document).find('head').append($('<style id=sidebar_links_icon_hover_color>' + sidebar_links_icon_hover_color + '</style>'));
		} );
	} );

	wp.customize( 'sidebar_list_border_color', function( value ) {
		value.bind( function( to ) {
			var sidebar_list_border_color = '#sidebar li{ border-bottom:1px solid '+ to +'; }';
			if( $(document).find('#sidebar_list_border_color').length ){
				$(document).find('#sidebar_list_border_color').remove();
			}
			$(document).find('head').append($('<style id=sidebar_list_border_color>' + sidebar_list_border_color + '</style>'));
		} );
	} );

	wp.customize( 'sidebar_tagcloud_bg_color', function( value ) {
		value.bind( function( to ) {
			var sidebar_tagcloud_bg_color = '.tagcloud a{ background:'+ to +'; }';
			if( $(document).find('#sidebar_tagcloud_bg_color').length ){
				$(document).find('#sidebar_tagcloud_bg_color').remove();
			}
			$(document).find('head').append($('<style id=sidebar_tagcloud_bg_color>' + sidebar_tagcloud_bg_color + '</style>'));
		} );
	} );

	wp.customize( 'sidebar_tagcloud_font_color', function( value ) {
		value.bind( function( to ) {
			var sidebar_tagcloud_font_color = '.tagcloud a{ color:'+ to +'; }';
			if( $(document).find('#sidebar_tagcloud_font_color').length ){
				$(document).find('#sidebar_tagcloud_font_color').remove();
			}
			$(document).find('head').append($('<style id=sidebar_tagcloud_font_color>' + sidebar_tagcloud_font_color + '</style>'));
		} );
	} );

	wp.customize( 'sidebar_tagcloud_border_color', function( value ) {
		value.bind( function( to ) {
			var sidebar_tagcloud_border_color = '.tagcloud a{ border:1px solid '+ to +'; }';
			if( $(document).find('#sidebar_tagcloud_border_color').length ){
				$(document).find('#sidebar_tagcloud_border_color').remove();
			}
			$(document).find('head').append($('<style id=sidebar_tagcloud_border_color>' + sidebar_tagcloud_border_color + '</style>'));
		} );
	} );

	wp.customize( 'sidebar_tagcloud_hover_bg_color', function( value ) {
		value.bind( function( to ) {
			var sidebar_tagcloud_hover_bg_color = '.tagcloud a:hover{ background:'+ to +'; }';
			if( $(document).find('#sidebar_tagcloud_hover_bg_color').length ){
				$(document).find('#sidebar_tagcloud_hover_bg_color').remove();
			}
			$(document).find('head').append($('<style id=sidebar_tagcloud_hover_bg_color>' + sidebar_tagcloud_hover_bg_color + '</style>'));
		} );
	} );

	wp.customize( 'sidebar_tagcloud_hover_font_color', function( value ) {
		value.bind( function( to ) {
			var sidebar_tagcloud_hover_font_color = '.tagcloud a:hover{ color:'+ to +'; }';
			if( $(document).find('#sidebar_tagcloud_hover_font_color').length ){
				$(document).find('#sidebar_tagcloud_hover_font_color').remove();
			}
			$(document).find('head').append($('<style id=sidebar_tagcloud_hover_font_color>' + sidebar_tagcloud_hover_font_color + '</style>'));
		} );
	} );

	wp.customize( 'sidebar_tagcloud_hover_border_color', function( value ) {
		value.bind( function( to ) {
			var sidebar_tagcloud_hover_border_color = '.tagcloud a:hover{ border:1px solid '+ to +'; }';
			if( $(document).find('#sidebar_tagcloud_hover_border_color').length ){
				$(document).find('#sidebar_tagcloud_hover_border_color').remove();
			}
			$(document).find('head').append($('<style id=sidebar_tagcloud_hover_border_color>' + sidebar_tagcloud_hover_border_color + '</style>'));
		} );
	} );

	// Single Page Section
	wp.customize( 'left_section_bg_color', function( value ) {
		value.bind( function( to ) {
			var left_section_bg_color = '.post_single_page_content_wrapper .one_third{ background: '+ to +'; }';
			if( $(document).find('#left_section_bg_color').length ){
				$(document).find('#left_section_bg_color').remove();
			}
			$(document).find('head').append($('<style id=left_section_bg_color>' + left_section_bg_color + '</style>'));
		} );
	} );
	wp.customize( 'left_section_border_color', function( value ) {
		value.bind( function( to ) {
			var left_section_border_color = '.post_single_page_content_wrapper .one_third{ border:1px solid '+ to +'; }';
			if( $(document).find('#left_section_border_color').length ){
				$(document).find('#left_section_border_color').remove();
			}
			$(document).find('head').append($('<style id=left_section_border_color>' + left_section_border_color + '</style>'));
		} );
	} );

	wp.customize( 'left_section_title_color', function( value ) {
		value.bind( function( to ) {
			var left_section_title_color = '.post_single_page_content_wrapper .one_third h3{ color:'+ to +'; }';
			if( $(document).find('#left_section_title_color').length ){
				$(document).find('#left_section_title_color').remove();
			}
			$(document).find('head').append($('<style id=left_section_title_color>' + left_section_title_color + '</style>'));
		} );
	} );

	wp.customize( 'left_section_title_border_color', function( value ) {
		value.bind( function( to ) {
			var left_section_title_border_color = '.post_single_page_content_wrapper .one_third h3:before{ background:'+ to +'; }';
			if( $(document).find('#left_section_title_border_color').length ){
				$(document).find('#left_section_title_border_color').remove();
			}
			$(document).find('head').append($('<style id=left_section_title_border_color>' + left_section_title_border_color + '</style>'));
		} );
	} );

	wp.customize( 'left_section_input_field_bg_color', function( value ) {
		value.bind( function( to ) {
			var left_section_input_field_bg_color = '.post_single_page_content_wrapper .one_third .caldera-grid input{ background:'+ to +'; }';
			if( $(document).find('#left_section_input_field_bg_color').length ){
				$(document).find('#left_section_input_field_bg_color').remove();
			}
			$(document).find('head').append($('<style id=left_section_input_field_bg_color>' + left_section_input_field_bg_color + '</style>'));
		} );
	} );

	wp.customize( 'left_section_input_field_border_color', function( value ) {
		value.bind( function( to ) {
			var left_section_input_field_border_color = '.post_single_page_content_wrapper .one_third .caldera-grid input{ border:1px solid '+ to +'; }';
			if( $(document).find('#left_section_input_field_border_color').length ){
				$(document).find('#left_section_input_field_border_color').remove();
			}
			$(document).find('head').append($('<style id=left_section_input_field_border_color>' + left_section_input_field_border_color + '</style>'));
		} );
	} );

	wp.customize( 'left_section_input_field_bg_color', function( value ) {
		value.bind( function( to ) {
			var post_single_page_content_wrapper = '.post_single_page_content_wrapper .one_third .caldera-grid input{ background:'+ to +'; }';
			if( $(document).find('#left_section_input_field_bg_color').length ){
				$(document).find('left_section_input_field_bg_color').remove();
			}
			$(document).find('head').append($('<style id=left_section_input_field_bg_color>' + left_section_input_field_bg_color + '</style>'));
		} );
	} );

	wp.customize( 'left_section_input_field_border_color', function( value ) {
		value.bind( function( to ) {
			var left_section_input_field_border_color = '.post_single_page_content_wrapper .one_third .caldera-grid textarea{ border:1px solid '+ to +'; }';
			if( $(document).find('#left_section_input_field_border_color').length ){
				$(document).find('#left_section_input_field_border_color').remove();
			}
			$(document).find('head').append($('<style id=left_section_input_field_border_color>' + left_section_input_field_border_color + '</style>'));
		} );
	} );

	wp.customize( 'left_section_form_button_bg_color', function( value ) {
		value.bind( function( to ) {
			var left_section_form_button_bg_color = '.caldera-grid .btn-default{ background: '+ to +'; }';
			if( $(document).find('#left_section_form_button_bg_color').length ){
				$(document).find('#left_section_form_button_bg_color').remove();
			}
			$(document).find('head').append($('<style id=left_section_form_button_bg_color>' + left_section_form_button_bg_color + '</style>'));
		} );
	} );

	wp.customize( 'left_section_form_button_color', function( value ) {
		value.bind( function( to ) {
			var left_section_form_button_color = '.caldera-grid .btn-default{ color: '+ to +'; }';
			if( $(document).find('#left_section_form_button_color').length ){
				$(document).find('#left_section_form_button_color').remove();
			}
			$(document).find('head').append($('<style id=left_section_form_button_color>' + left_section_form_button_color + '</style>'));
		} );
	} );
	wp.customize( 'left_section_related_post_bg_color', function( value ) {
		value.bind( function( to ) {
			var left_section_related_post_bg_color = '.related_posts ul li{ background: '+ to +'; }';
			if( $(document).find('#left_section_related_post_bg_color').length ){
				$(document).find('#left_section_related_post_bg_color').remove();
			}
			$(document).find('head').append($('<style id=left_section_related_post_bg_color>' + left_section_related_post_bg_color + '</style>'));
		} );
	} );

	wp.customize( 'left_section_related_post_border_color', function( value ) {
		value.bind( function( to ) {
			var left_section_related_post_border_color = '.related_posts ul li{ border:1px solid '+ to +'; }';
			if( $(document).find('#left_section_related_post_border_color').length ){
				$(document).find('#left_section_related_post_border_color').remove();
			}
			$(document).find('head').append($('<style id=left_section_related_post_border_color>' + left_section_related_post_border_color + '</style>'));
		} );
	} );

	wp.customize( 'left_section_related_post_title_color', function( value ) {
		value.bind( function( to ) {
			var left_section_related_post_title_color = '.related_posts .related_title a{ color:'+ to +'; }';
			if( $(document).find('#left_section_related_post_title_color').length ){
				$(document).find('#left_section_related_post_title_color').remove();
			}
			$(document).find('head').append($('<style id=left_section_related_post_title_color>' + left_section_related_post_title_color + '</style>'));
		} );
	} );

	wp.customize( 'left_section_related_post_title_hover_color', function( value ) {
		value.bind( function( to ) {
			var left_section_related_post_title_hover_color = '.related_posts .related_title a:hover{ color:'+ to +'; }';
			if( $(document).find('#left_section_related_post_title_hover_color').length ){
				$(document).find('#left_section_related_post_title_hover_color').remove();
			}
			$(document).find('head').append($('<style id=left_section_related_post_title_hover_color>' + left_section_related_post_title_hover_color + '</style>'));
		} );
	} );

	wp.customize( 'left_section_related_post_category_color', function( value ) {
		value.bind( function( to ) {
			var left_section_related_post_category_color = '.related_posts .related_title span{ color:'+ to +'; }';
			if( $(document).find('#left_section_related_post_category_color').length ){
				$(document).find('#left_section_related_post_category_color').remove();
			}
			$(document).find('head').append($('<style id=left_section_related_post_category_color>' + left_section_related_post_category_color + '</style>'));
		} );
	} );

	// Single Page Right Section
	wp.customize( 'right_section_title_color', function( value ) {
		value.bind( function( to ) {
			var right_section_title_color = '.single_tabs_content_wrapper h3{ color:'+ to +'; }';
			if( $(document).find('#right_section_title_color').length ){
				$(document).find('#right_section_title_color').remove();
			}
			$(document).find('head').append($('<style id=right_section_title_color>' + right_section_title_color + '</style>'));
		} );
	} );

	wp.customize( 'right_section_title_border_color', function( value ) {
		value.bind( function( to ) {
			var right_section_title_border_color = '.post_single_page_content_wrapper h3:before{ background:'+ to +'; }';
			if( $(document).find('#right_section_title_border_color').length ){
				$(document).find('#right_section_title_border_color').remove();
			}
			$(document).find('head').append($('<style id=right_section_title_border_color>' + right_section_title_border_color + '</style>'));
		} );
	} );

	wp.customize( 'right_section_details_font_color', function( value ) {
		value.bind( function( to ) {
			var right_section_details_font_color = '.single .general-meta-fields-info-wrapper ul li{ color:'+ to +'; }';
			if( $(document).find('#right_section_details_font_color').length ){
				$(document).find('#right_section_details_font_color').remove();
			}
			$(document).find('head').append($('<style id=right_section_details_font_color>' + right_section_details_font_color + '</style>'));
		} );
	} );

	wp.customize( 'right_section_details_border_color', function( value ) {
		value.bind( function( to ) {
			var right_section_details_border_color = '.single .general-meta-fields-info-wrapper ul li{ border-bottom:1px solid '+ to +'; }';
			if( $(document).find('#right_section_details_border_color').length ){
				$(document).find('#right_section_details_border_color').remove();
			}
			$(document).find('head').append($('<style id=right_section_details_border_color>' + right_section_details_border_color + '</style>'));
		} );
	} );

	// Footer Section
	wp.customize( 'footer_copy_rights', function( value ) {
		value.bind( function( to ) {
			$( '.copyright' ).html( to );
		} );
	} );

	wp.customize( 'footer_bg_color', function( value ) {
		value.bind( function( to ) {
			var footer_bg_color = '#kaya-footer-content-wrapper{ background:'+ to +'; }';
			if( $(document).find('#footer_bg_color').length ){
				$(document).find('#footer_bg_color').remove();
			}
			$(document).find('head').append($('<style id=footer_bg_color>' + footer_bg_color + '</style>'));
		} );
	} );
	wp.customize( 'footer_content_color', function( value ) {
		value.bind( function( to ) {
			var footer_content_color = '#kaya-footer-content-wrapper p, #kaya-footer-content-wrapper span, #kaya-footer-content-wrapper{ color:'+ to +'; }';
			if( $(document).find('#footer_content_color').length ){
				$(document).find('#footer_content_color').remove();
			}
			$(document).find('head').append($('<style id=footer_content_color>' + footer_content_color + '</style>'));
		} );
	} );
	wp.customize( 'footer_link_color', function( value ) {
		value.bind( function( to ) {
			var footer_link_color = '#kaya-footer-content-wrapper a{ color:'+ to +'; }';
			if( $(document).find('#footer_link_color').length ){
				$(document).find('#footer_link_color').remove();
			}
			$(document).find('head').append($('<style id=footer_link_color>' + footer_link_color + '</style>'));
		} );
	} );
	wp.customize( 'footer_link_hover_color', function( value ) {
		value.bind( function( to ) {
			var footer_link_hover_color = '#kaya-footer-content-wrapper a:hover{ color:'+ to +'; }';
			if( $(document).find('#footer_link_hover_color').length ){
				$(document).find('#footer_link_hover_color').remove();
			}
			$(document).find('head').append($('<style id=footer_link_hover_color>' + footer_link_hover_color + '</style>'));
		} );
	} );

// Page Footer
wp.customize( 'page_footer_bg_color', function( value ) {
		value.bind( function( to ) {
			var page_footer_bg_color = '.kaya-page-content-footer{ background:'+ to +'; }';
			if( $(document).find('#page_footer_bg_color').length ){
				$(document).find('#page_footer_bg_color').remove();
			}
			$(document).find('head').append($('<style id=page_footer_bg_color>' + page_footer_bg_color + '</style>'));
		} );
	} );
	wp.customize( 'page_footer_content_color', function( value ) {
		value.bind( function( to ) {
			var page_footer_content_color = '.kaya-page-content-footer p, .kaya-page-content-footer span, .kaya-page-content-footer{ color:'+ to +'; }';
			if( $(document).find('#page_footer_content_color').length ){
				$(document).find('#page_footer_content_color').remove();
			}
			$(document).find('head').append($('<style id=page_footer_content_color>' + page_footer_content_color + '</style>'));
		} );
	} );
	wp.customize( 'page_footer_link_color', function( value ) {
		value.bind( function( to ) {
			var page_footer_link_color = '.kaya-page-content-footer a{ color:'+ to +'; }';
			if( $(document).find('#page_footer_link_color').length ){
				$(document).find('#page_footer_link_color').remove();
			}
			$(document).find('head').append($('<style id=page_footer_link_color>' + page_footer_link_color + '</style>'));
		} );
	} );
	wp.customize( 'page_footer_link_hover_color', function( value ) {
		value.bind( function( to ) {
			var page_footer_link_hover_color = '.kaya-page-content-footer a:hover{ color:'+ to +'; }';
			if( $(document).find('#page_footer_link_hover_color').length ){
				$(document).find('#page_footer_link_hover_color').remove();
			}
			$(document).find('head').append($('<style id=page_footer_link_hover_color>' + page_footer_link_hover_color + '</style>'));
		} );
	} );

	// Heading Fonts Sizes
	wp.customize('body_font_size', function(  value ){
		value.bind(function(to){
			var body_font_line_height = Math.round(1.6 * to);
			var body_font_size = 'body, p{ font-size:'+ to +'px!important; line-height:'+ body_font_line_height +'px;}';
			if($(document).find('#body_font_size').length) {
				$(document).find('#body_font_size').remove();
			}
		$(document).find('head').append($('<style id="body_font_size">' + body_font_size + '</style>'));
		});
	});

	wp.customize('menu_font_size', function(  value ){
		value.bind(function(to){
			var menu_font_size = '#header-navigation a{ font-size:'+ to +'px!important;}';
			if($(document).find('#menu_font_size').length) {
				$(document).find('#menu_font_size').remove();
			}
		$(document).find('head').append($('<style id="menu_font_size">' + menu_font_size + '</style>'));
		});
	});
	wp.customize('child_menu_font_size', function(  value ){
		value.bind(function(to){
			var child_menu_font_size = '#header-navigation ul ul li a{ font-size:'+ to +'px!important;}';
			if($(document).find('#child_menu_font_size').length) {
				$(document).find('#child_menu_font_size').remove();
			}
		$(document).find('head').append($('<style id="child_menu_font_size">' + child_menu_font_size + '</style>'));
		});
	});
	wp.customize('h1_title_fontsize', function(  value ){
		value.bind(function(to){
			var line_height_h1 = Math.round(1.1 * to);
			var h1_title_fontsize = 'h1{ font-size:'+ to +'px!important; line-height:'+ line_height_h1 +'px;}';
			if($(document).find('#h1_title_fontsize').length) {
				$(document).find('#h1_title_fontsize').remove();
			}
		$(document).find('head').append($('<style id="h1_title_fontsize">' + h1_title_fontsize + '</style>'));
		});
	});

	wp.customize('h2_title_fontsize', function(  value ){
		value.bind(function(to){
			var line_height_h2 = Math.round(1.1 * to);
			var h2_title_fontsize = 'h2{ font-size:'+ to +'px!important; line-height:'+ line_height_h2 +'px;}';
			if($(document).find('#h2_title_fontsize').length) {
				$(document).find('#h2_title_fontsize').remove();
			}
		$(document).find('head').append($('<style id="h2_title_fontsize">' + h2_title_fontsize + '</style>'));
		});
	});

	wp.customize('h3_title_fontsize', function(  value ){
		value.bind(function(to){
			var line_height_h3 = Math.round(1.1 * to);
			var h3_title_fontsize = 'h3{ font-size:'+ to +'px!important; line-height:'+ line_height_h3 +'px;}';
			if($(document).find('#h3_title_fontsize').length) {
				$(document).find('#h3_title_fontsize').remove();
			}
		$(document).find('head').append($('<style id="h3_title_fontsize">' + h3_title_fontsize + '</style>'));
		});
	});
	wp.customize('h4_title_fontsize', function(  value ){
		value.bind(function(to){
			var line_height_h4 = Math.round(1.1 * to);
			var h4_title_fontsize = 'h4{ font-size:'+ to +'px!important; line-height:'+ line_height_h4 +'px;}';
			if($(document).find('#h4_title_fontsize').length) {
				$(document).find('#h4_title_fontsize').remove();
			}
		$(document).find('head').append($('<style id="h4_title_fontsize">' + h4_title_fontsize + '</style>'));
		});
	});

	wp.customize('h5_title_fontsize', function(  value ){
		value.bind(function(to){
			var line_height_h5 = Math.round(1.1 * to);
			var h5_title_fontsize = 'h5{ font-size:'+ to +'px!important; line-height:'+ line_height_h5 +'px;}';
			if($(document).find('#h5_title_fontsize').length) {
				$(document).find('#h5_title_fontsize').remove();
			}
		$(document).find('head').append($('<style id="h5_title_fontsize">' + h5_title_fontsize + '</style>'));
		});
	});
	wp.customize('h6_title_fontsize', function(  value ){
		value.bind(function(to){
			var line_height_h6 = Math.round(1.1 * to);
			var h6_title_fontsize = 'h6{ font-size:'+ to +'px!important; line-height:'+ line_height_h6 +'px;}';
			if($(document).find('#h6_title_fontsize').length) {
				$(document).find('#h6_title_fontsize').remove();
			}
		$(document).find('head').append($('<style id="h6_title_fontsize">' + h6_title_fontsize + '</style>'));
		});
	});

	// Fonts
	var subset = ['latin,latin-ext,cyrillic,cyrillic-ext,greek,greek-ext,vietnamese'];
	var font_weights = ['100', '100italic', '200', '200italic', '300', '300italic', '400', '400italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'];
	// Frame Border
	wp.customize('frame_border_text_font_family', function(  value ){
		value.bind(function(to){
		if( '0' != to){
			var replacestring = to.split(' ').join('+');
			var google_frame_border_text_font_family ='http://fonts.googleapis.com/css?family='+replacestring;
			var frame_border_text_font_family = '.toggle_menu_wrapper span, .header_contact_info span, .header_contact_info a,  .user_login_info span, .user_login_info a, .bottom_footer_bar_wrapper, .bottom_footer_bar_wrapper a, .bottom_footer_bar_wrapper span{ font-family:'+ to +'!important}';
			if($(document).find('#google_frame_border_text_font_family').length) {
					$(document).find('#google_frame_border_text_font_family').remove();
				}
			if($(document).find('#frame_border_text_font_family').length) {
					$(document).find('#frame_border_text_font_family').remove();
				}	
			$(document).find('head').append($("<link id='google_frame_border_text_font_family' href='"+ google_frame_border_text_font_family +":"+font_weights+"&subset="+subset+"' rel='stylesheet' type='text/css'><style id='frame_border_text_font_family'>" + frame_border_text_font_family + "</style>"));
		}else{
			$(document).find('#frame_border_text_font_family').remove();
			$(document).find('#google_frame_border_text_font_family').remove();
			var frame_border_text_font_family = '.header_logo_wrapper h1.logo a, .header_logo_wrapper h1.sticky_logo a{ font-family:arial!important}';
			$(document).find('head').append($("<style>" + frame_border_text_font_family + "</style>"));
		}
		});
	});
	wp.customize('google_body_font', function(  value ){
	value.bind(function(to){
	if( '0' != to){
		var replacestring = to.split(' ').join('+');
		var   google_body_font ='http://fonts.googleapis.com/css?family='+replacestring;
		var body_font_family = 'body ,p, a{ font-family:'+ to +'!important}';
		if($(document).find('#google_body_font').length) {
				$(document).find('#google_body_font').remove();
			}
		if($(document).find('#body_font_family').length) {
				$(document).find('#body_font_family').remove();
			}	
		$(document).find('head').append($("<link id='google_body_font' href='"+ google_body_font +":"+font_weights+"&subset="+subset+"' rel='stylesheet' type='text/css'><style id='body_font_family'>" + body_font_family + "</style>"));
	}else{
		$(document).find('#body_font_family').remove();
		$(document).find('#google_body_font').remove();
		var body_font_family = 'body ,p, a{ font-family:arial!important}';
		$(document).find('head').append($("<style>" + body_font_family + "</style>"));
	}
	});
	});

	wp.customize('google_heading_font', function(  value ){
		value.bind(function(to){
		if( '0' != to){	
			var replacestring = to.split(' ').join('+');
			var google_heading_font ='http://fonts.googleapis.com/css?family='+replacestring;
			var heading_font_family = 'h1,h2,h3,h4,h5,h6{ font-family:'+ to +'!important}';
			if($(document).find('#google_heading_font').length) {
					$(document).find('#google_heading_font').remove();
				}
			if($(document).find('#heading_font_family').length) {
					$(document).find('#heading_font_family').remove();
				}	
			$(document).find('head').append($("<link id='google_heading_font' href='"+ google_heading_font +":"+font_weights+"&subset="+subset+"' rel='stylesheet' type='text/css'><style id='heading_font_family'>" + heading_font_family + "</style>"));
		}else{
			$(document).find('#google_heading_font').remove();
			$(document).find('#heading_font_family').remove();
			var heading_font_family = 'h1,h2,h3,h4,h5,h6{ font-family:arial!important}';
			$(document).find('head').append($("<style" + heading_font_family + "</style>"));
		}	
		});
	});

	wp.customize('google_menu_font', function(  value ){
		value.bind(function(to){
		if( '0' != to){	
			var replacestring = to.split(' ').join('+');
			var google_menu_font ='http://fonts.googleapis.com/css?family='+replacestring;
			var menu_font_family = '#header-navigation ul li a{ font-family:'+ to +'!important}';
			if($(document).find('#google_menu_font').length) {
					$(document).find('#google_menu_font').remove();
				}
			if($(document).find('#menu_font_family').length) {
					$(document).find('#menu_font_family').remove();
				}	
			$(document).find('head').append($("<link id='google_menu_font' href='"+ google_menu_font +":"+font_weights+"&subset="+subset+"' rel='stylesheet' type='text/css'><style id='menu_font_family'>" + menu_font_family + "</style>"));

		}else{
			$(document).find('#google_menu_font').remove();
			$(document).find('#menu_font_family').remove();
			var menu_font_family = '.menu ul li a{ font-family:arial!important}';
			$(document).find('head').append($("<style>" + menu_font_family + "</style>"));
		}	
	});
	});
	wp.customize('google_all_desc_font', function(  value ){
		value.bind(function(to){
		if( '0' != to){	
			var replacestring = to.split(' ').join('+');
			var google_all_desc_font ='http://fonts.googleapis.com/css?family='+replacestring;
			var titles_desc_font_family = 'span.menu_description, .portfolio_content_wrapper span.pf_title_wrapper, .pf_content_wrapper span, .search_box_style input, .search_box_style select, #mid_container_wrapper .pf_model_info_wrapper ul li span, .social_media_sharing_icons span.share_on_title, span.image_side_title, .custom_title_wrapper p, .testimonial_slider p, .meta_post_info span a, .blog_post_wrapper .readmore_button, span.meta_date_month, .quote_format h3, .widget_container .tagcloud a, .recent_posts_date, .comment_posted_date, div#comments input, div#comments textarea, blockquote p, .related_post_slider span, #slidecaption p{ font-family:'+ to +'!important}';
			if($(document).find('#google_all_desc_font').length) {
					$(document).find('#google_all_desc_font').remove();
				}
			if($(document).find('#titles_desc_font_family').length) {
					$(document).find('#titles_desc_font_family').remove();
				}	
			$(document).find('head').append($("<link id='google_all_desc_font' href='"+ google_all_desc_font +":"+font_weights+"&subset="+subset+"' rel='stylesheet' type='text/css'><style id='titles_desc_font_family'>" + titles_desc_font_family + "</style>"));

		}else{
			$(document).find('#google_all_desc_font').remove();
			$(document).find('#titles_desc_font_family').remove();
			var titles_desc_font_family = 'span.menu_description, .portfolio_content_wrapper span.pf_title_wrapper, .pf_content_wrapper span, .search_box_style input, .search_box_style select, #mid_container_wrapper .pf_model_info_wrapper ul li span, .social_media_sharing_icons span.share_on_title, span.image_side_title, .custom_title_wrapper p, .testimonial_slider p, .meta_post_info span a, .blog_post_wrapper .readmore_button, span.meta_date_month, .quote_format h3, .widget_container .tagcloud a, .recent_posts_date, .comment_posted_date, div#comments input, div#comments textarea, blockquote p, .related_post_slider span, #slidecaption p{ font-family:arial!important}';
			$(document).find('head').append($("<style>" + titles_desc_font_family + "</style>"));
		}	
	});
	});
	// Letter Spacing
	wp.customize('h1_font_letter_space', function(  value ){
		value.bind(function(to){
			var h1_font_letter_space = 'h1{ letter-spacing:'+ to +'px;}';
			if($(document).find('#h1_font_letter_space').length) {
				$(document).find('#h1_font_letter_space').remove();
			}
		$(document).find('head').append($('<style id="h1_font_letter_space">' + h1_font_letter_space + '</style>'));
		});
	});

	wp.customize('h2_font_letter_space', function(  value ){
		value.bind(function(to){
			var h2_font_letter_space = 'h2{ letter-spacing:'+ to +'px;}';
			if($(document).find('#h2_font_letter_space').length) {
				$(document).find('#h2_font_letter_space').remove();
			}
		$(document).find('head').append($('<style id="h2_font_letter_space">' + h2_font_letter_space + '</style>'));
		});
	});

	wp.customize('h3_font_letter_space', function(  value ){
		value.bind(function(to){
			var h3_font_letter_space = 'h3{ letter-spacing:'+ to +'px;}';
			if($(document).find('#h3_font_letter_space').length) {
				$(document).find('#h3_font_letter_space').remove();
			}
		$(document).find('head').append($('<style id="h3_font_letter_space">' + h3_font_letter_space + '</style>'));
		});
	});

	wp.customize('h4_font_letter_space', function(  value ){
		value.bind(function(to){
			var h4_font_letter_space = 'h4{ letter-spacing:'+ to +'px;}';
			if($(document).find('#h4_font_letter_space').length) {
				$(document).find('#h4_font_letter_space').remove();
			}
		$(document).find('head').append($('<style id="h4_font_letter_space">' + h4_font_letter_space + '</style>'));
		});
	});

	wp.customize('h5_font_letter_space', function(  value ){
		value.bind(function(to){
			var h5_font_letter_space = 'h5{ letter-spacing:'+ to +'px;}';
			if($(document).find('#h5_font_letter_space').length) {
				$(document).find('#h5_font_letter_space').remove();
			}
		$(document).find('head').append($('<style id="h5_font_letter_space">' + h5_font_letter_space + '</style>'));
		});
	});
	wp.customize('h6_font_letter_space', function(  value ){
		value.bind(function(to){
			var h6_font_letter_space = 'h6{ letter-spacing:'+ to +'px;}';
			if($(document).find('#h6_font_letter_space').length) {
				$(document).find('#h6_font_letter_space').remove();
			}
		$(document).find('head').append($('<style id="h6_font_letter_space">' + h6_font_letter_space + '</style>'));
		});
	});


	wp.customize('body_font_letter_space', function(  value ){
		value.bind(function(to){
			var body_font_letter_space = 'body,p{ letter-spacing:'+ to +'px;}';
			if($(document).find('#body_font_letter_space').length) {
				$(document).find('#body_font_letter_space').remove();
			}
		$(document).find('head').append($('<style id="body_font_letter_space">' + body_font_letter_space + '</style>'));
		});
	});

	wp.customize('menu_font_letter_space', function(  value ){
		value.bind(function(to){
			var menu_font_letter_space = '#header-navigation ul li a{ letter-spacing:'+ to +'px;}';
			if($(document).find('#menu_font_letter_space').length) {
				$(document).find('#menu_font_letter_space').remove();
			}
		$(document).find('head').append($('<style id="menu_font_letter_space">' + menu_font_letter_space + '</style>'));
		});
	});

	wp.customize('child_menu_font_letter_space', function(  value ){
		value.bind(function(to){
			var child_menu_font_letter_space = '#header-navigation ul ul li a, .wide_menu strong{ letter-spacing:'+ to +'px;}';
			if($(document).find('#child_menu_font_letter_space').length) {
				$(document).find('#child_menu_font_letter_space').remove();
			}
		$(document).find('head').append($('<style id="child_menu_font_letter_space">' + child_menu_font_letter_space + '</style>'));
		});
	});
	wp.customize('child_menu_font_size', function(  value ){
		value.bind(function(to){
			var child_menu_font_size = '#header-navigation ul ul li a, .wide_menu strong{ font-size:'+ to +'px!important;}';
			if($(document).find('#child_menu_font_size').length) {
				$(document).find('#child_menu_font_size').remove();
			}
		$(document).find('head').append($('<style id="child_menu_font_size">' + child_menu_font_size + '</style>'));
		});
	});
	// Desc Font Size
	wp.customize('menu_desc_font_size', function(  value ){
		value.bind(function(to){
			var menu_desc_font_size = '.menu span.menu_description{ font-size:'+ to +'px!important;}';
			if($(document).find('#menu_desc_font_size').length) {
				$(document).find('#menu_desc_font_size').remove();
			}
		$(document).find('head').append($('<style id="menu_desc_font_size">' + menu_desc_font_size + '</style>'));
		});
	});
	wp.customize('menu_desc_letter_space', function(  value ){
		value.bind(function(to){
			var menu_desc_letter_space = '.menu span.menu_description{ letter-spacing:'+ to +'px;}';
			if($(document).find('#menu_desc_letter_space').length) {
				$(document).find('#menu_desc_letter_space').remove();
			}
		$(document).find('head').append($('<style id="menu_desc_letter_space">' + menu_desc_letter_space + '</style>'));
		});
	});
	wp.customize('menu_desc_font_weight', function(  value ){
		value.bind(function(to){
			var menu_desc_font_weight = '.menu span.menu_description{ font-weight:'+ to +'; }';
			if($(document).find('#menu_desc_font_weight').length) {
				$(document).find('#menu_desc_font_weight').remove();
			}
		$(document).find('head').append($('<style id="menu_desc_font_weight">' + menu_desc_font_weight + '</style>'));
		});
	});

	//Uppercase to Lower Case
	wp.customize('child_menu_uppercase', function( value ){
	value.bind( function(to){
		if( to == '1' ){
			$('.menu ul ul li a').css('text-transform', 'uppercase');
		}else{
			$('.menu ul ul li a').css('text-transform', 'inherit');
		}
	});
});
wp.customize('main_menu_uppercase', function( value ){
	value.bind( function(to){
		if( to == '1' ){
			$('.menu > ul > li > a').css('text-transform', 'uppercase');
		}else{
			$('.menu > ul > li > a').css('text-transform', 'inherit');
		}
	});
});
	// Typography
	// Body
	wp.customize('body_font_weight_bold', function(  value ){
		value.bind(function(to){
			var body_font_weight_bold = 'body, p{ font-weight:'+ to +'!important;}';
			if($(document).find('#body_font_weight_bold').length) {
				$(document).find('#body_font_weight_bold').remove();
			}
		$(document).find('head').append($('<style id="body_font_weight_bold">' + body_font_weight_bold + '</style>'));
		});
	});
	// Menu
	wp.customize('menu_font_weight', function(  value ){
		value.bind(function(to){
			var menu_font_weight = '#header-navigation ul li a{ font-weight:'+ to +';}';
			if($(document).find('#menu_font_weight').length) {
				$(document).find('#menu_font_weight').remove();
			}
		$(document).find('head').append($('<style id="menu_font_weight">' + menu_font_weight + '</style>'));
		});
	});
	wp.customize('child_menu_font_weight', function(  value ){
		value.bind(function(to){
			var child_menu_font_weight = '#header-navigation ul ul li a{ font-weight:'+ to +';}';
			if($(document).find('#child_menu_font_weight').length) {
				$(document).find('#child_menu_font_weight').remove();
			}
		$(document).find('head').append($('<style id="child_menu_font_weight">' + child_menu_font_weight + '</style>'));
		});
	});
	//titles
	wp.customize('h1_font_weight_bold', function(  value ){
		value.bind(function(to){
			var h1_font_weight_bold = 'h1{ font-weight:'+ to +'!important;}';
			if($(document).find('#h1_font_weight_bold').length) {
				$(document).find('#h1_font_weight_bold').remove();
			}
		$(document).find('head').append($('<style id="h1_font_weight_bold">' + h1_font_weight_bold + '</style>'));
		});
	});

	wp.customize('h2_font_weight_bold', function(  value ){
		value.bind(function(to){
			var h2_font_weight_bold = 'h2{ font-weight:'+ to +'!important;}';
			if($(document).find('#h2_font_weight_bold').length) {
				$(document).find('#h2_font_weight_bold').remove();
			}
		$(document).find('head').append($('<style id="h2_font_weight_bold">' + h2_font_weight_bold + '</style>'));
		});
	});

	wp.customize('h3_font_weight_bold', function(  value ){
		value.bind(function(to){
			var h3_font_weight_bold = 'h3, .woocommerce ul.products li.product h3, .woocommerce-page ul.products li.product h3{ font-weight:'+ to +'!important;}';
			if($(document).find('#h3_font_weight_bold').length) {
				$(document).find('#h3_font_weight_bold').remove();
			}
		$(document).find('head').append($('<style id="h3_font_weight_bold">' + h3_font_weight_bold + '</style>'));
		});
	});

	wp.customize('h4_font_weight_bold', function(  value ){
		value.bind(function(to){
			var h4_font_weight_bold = 'h4{ font-weight:'+ to +'!important;}';
			if($(document).find('#h4_font_weight_bold').length) {
				$(document).find('#h4_font_weight_bold').remove();
			}
		$(document).find('head').append($('<style id="h4_font_weight_bold">' + h4_font_weight_bold + '</style>'));
		});
	});

	wp.customize('h5_font_weight_bold', function(  value ){
		value.bind(function(to){
			var h5_font_weight_bold = 'h5{ font-weight:'+ to +'!important;}';
			if($(document).find('#h5_font_weight_bold').length) {
				$(document).find('#h5_font_weight_bold').remove();
			}
		$(document).find('head').append($('<style id="h5_font_weight_bold">' + h5_font_weight_bold + '</style>'));
		});
	});

	wp.customize('h6_font_weight_bold', function(  value ){
		value.bind(function(to){
			var h6_font_weight_bold = 'h6{ font-weight:'+ to +'!important;}';
			if($(document).find('#h6_font_weight_bold').length) {
				$(document).find('#h6_font_weight_bold').remove();
			}
		$(document).find('head').append($('<style id="h6_font_weight_bold">' + h6_font_weight_bold + '</style>'));
		});
	});

	/**
	 * Pagination Colors
	 */
	 wp.customize( 'pagination_bg_color', function( value ) {
		value.bind( function( to ) {
			var pagination_bg_color = 'ul.page-numbers li a, .page-links a{ background:'+ to +'!important; }';
			if( $(document).find('#pagination_bg_color').length ){
				$(document).find('#pagination_bg_color').remove();
			}
			$(document).find('head').append($('<style id=pagination_bg_color>' + pagination_bg_color + '</style>'));
		});
	} );

	wp.customize( 'pagination_link_color', function( value ) {
		value.bind( function( to ) {
			var pagination_link_color = 'ul.page-numbers li a, .page-links a{ color:'+ to +'!important; }';
			if( $(document).find('#pagination_link_color').length ){
				$(document).find('#pagination_link_color').remove();
			}
			$(document).find('head').append($('<style id=pagination_link_color>' + pagination_link_color + '</style>'));
		});
	} );


	wp.customize( 'pagination_active_bg_color', function( value ) {
		value.bind( function( to ) {
			var pagination_active_bg_color = '.pagination .current, #kaya-mid-content-wrapper .page-links > span, ul.page-numbers li a:hover, .page-links a:hover{ background:'+ to +'!important; }';
			if( $(document).find('#pagination_active_bg_color').length ){
				$(document).find('#pagination_active_bg_color').remove();
			}
			$(document).find('head').append($('<style id=pagination_active_bg_color>' + pagination_active_bg_color + '</style>'));
		});
	} );

	wp.customize( 'pagination_active_link_color', function( value ) {
		value.bind( function( to ) {
			var pagination_active_link_color = '.pagination .current, #kaya-mid-content-wrapper .page-links > span, ul.page-numbers li a:hover, .page-links a:hover{ color:'+ to +'!important; }';
			if( $(document).find('#pagination_active_link_color').length ){
				$(document).find('#pagination_active_link_color').remove();
			}
			$(document).find('head').append($('<style id=pagination_active_link_color>' + pagination_active_link_color + '</style>'));
		});
	} );



} )( jQuery );
