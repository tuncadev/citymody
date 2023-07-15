<?php
defined( 'ABSPATH' ) || exit;

remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'wp_generator' );

// Remove https://api.w.org/
remove_action( 'wp_head', 'rest_output_link_header', 10 );
remove_action( 'template_redirect', 'rest_output_link_header', 11 );

// Remove enqueue style
function remove_block_css() {
    wp_dequeue_style( 'wp-block-library-theme' );
}
add_action( 'wp_enqueue_scripts', 'remove_block_css', 100 );

// Remove unused links on admin bar.
function civi_child_demo_admin_bar_remove_logo() {
	/**
	 * @var WP_Admin_Bar $wp_admin_bar
	 */
	global $wp_admin_bar;
	
	$wp_admin_bar->remove_menu( 'wp-logo' );

	$wp_admin_bar->remove_menu( 'comments' );

	$wp_admin_bar->remove_menu( 'updates' );
}

add_action( 'wp_before_admin_bar_render', 'civi_child_demo_admin_bar_remove_logo', 0 );

// Remove type attribute from script and style tags added by WordPress.
add_action( 'wp_loaded', 'civi_child_demo_output_buffer_start' );
function civi_child_demo_output_buffer_start() {
	ob_start( 'civi_child_demo_output_callback' );
}

add_action( 'shutdown', 'civi_child_demo_output_buffer_end' );
function civi_child_demo_output_buffer_end() {
	if ( ob_get_length() ) {
		ob_end_flush();
	}
}

function civi_child_demo_output_callback( $buffer ) {
	return preg_replace( "%[ ]type=[\'\"]text\/(javascript|css)[\'\"]%", '', $buffer );
}

// Remove Recent Comments wp_head CSS
add_action( 'widgets_init', 'civi_child_demo_remove_recent_comments_style' );
function civi_child_demo_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array(
		$wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
		'recent_comments_style',
	) );
}


/**
 * Theme functions and definitions.
 */
function civi_child_enqueue_styles() {

    wp_enqueue_style( 'civi-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'civi-style' ),
        wp_get_theme()->get('Version')
    );

	
}
add_action(  'wp_enqueue_scripts', 'civi_child_enqueue_styles' );

/**
 * Enqueue child scripts
 */
add_action( 'wp_enqueue_scripts', 'civi_child_enqueue_scripts' );
if ( ! function_exists( 'civi_child_enqueue_scripts' ) ) {

	function civi_child_enqueue_scripts() {
		wp_enqueue_script( 'civi-child-script',	trailingslashit( get_stylesheet_directory_uri() ) . 'script.js', array( 'jquery' ),	null, true );
		wp_enqueue_script( 'civi-json',	trailingslashit( get_stylesheet_directory_uri() ) . '/js/cities.js', array( 'jquery' ),	null, true );

	}
	
}
get_template_part('includes/class-templates');


/* Show membershop */

function candidate_membership_plan() {
	
	global $post;
	global $user;
	$subscriptions_table_string = "[ihc-account-page-subscriptions-table]";
	$clean_subscriptions_table = preg_replace( '/<!--(.|\s)*?-->/', '', do_shortcode($subscriptions_table_string) );
			
	if(preg_replace( '/<!--(.|\s)*?-->/', '', do_shortcode($subscriptions_table_string) ) === "") {
		echo "THIS" . do_shortcode("[ihc-select-level]");
	} else {
		echo "THAT" . do_shortcode("[ihc-account-page-subscriptions-table]");
	}

}
// Remove unwanted HTML comments
function remove_html_comments($content = '') {
    return preg_replace('/<!--(.|\s)*?-->/', '', $content);
}

function pre_me($array) {
	if($array !== "") {
		echo "<pre>", print_r($array), "</pre>";
	}
}


// this is based on WP Core, return the immediate 1 level of children
function get_term_children_in_slug( $term_slug, $taxonomy ) {
    if ( ! taxonomy_exists( $taxonomy ) ) {
        return new WP_Error( 'invalid_taxonomy', __( 'Invalid taxonomy.' ) );
    }

    $term_data = _get_term_hierarchy_in_slug( $taxonomy );
    $terms = $term_data['children'];
    $term_slug_ids = $term_data['term_slug_ids'];

    if ( ! isset( $term_slug_ids[ $term_slug ] ) ) {
        return array();
    }

    if( ! isset( $terms[ $term_slug_ids[ $term_slug ] ] ) ) {
        return array();
    }

    $children = $terms[ $term_slug_ids[ $term_slug ] ];

    // find children of children
    foreach ( (array) $terms[ $term_slug_ids[ $term_slug ] ] as $child_slug => $child_id ) {
        if ( $term_slug === $child_slug ) {
            continue;
        }

        if ( isset( $terms[ $term_slug_ids[ $child_slug ] ]) ) {
            $children = array_merge( $children, get_term_children_in_slug( $child_slug, $taxonomy ) );
        }
    }

    return $children;
}

function list_skill_options($candidate_id, $taxonomyName) {

	$taxonomy_terms = get_categories(
		array(
			'taxonomy' => $taxonomyName,
			'orderby' => 'name',
			'order' => 'ASC',
			'hide_empty' => false,
			'parent' => 0
		)
	);
	$target_by_name = array();
	$target_term_id = array();
	$tax_terms = get_the_terms($candidate_id, $taxonomyName);
	
	if (!empty($tax_terms)) {
		foreach ($tax_terms as $tax_term) {
			$target_term_id[] = $tax_term->term_id;
		}
	}

	if (!empty($taxonomy_terms)) {
		foreach ($taxonomy_terms as $term) {	
		
				echo '<option class="disabled" disabled value="' . $term->term_id . '">' .  $term->name . '</option>';
				
				if($terms = get_terms( $taxonomyName, array( 'parent' => $term->term_id, 'orderby' => 'slug', 'hide_empty' => false ) )) {
					foreach ( $terms as $child_term ) :
						if (in_array($child_term->term_id, $target_term_id)) {
							echo '<option value="' . $child_term->term_id . '" selected>' . $prefix . $child_term->name . '</option>';
						} else {
							echo '<option class="skill_child" value="' . $child_term->term_id . '">' .  $child_term->name . ' ('.$term->name.')</option>';
						}
					endforeach;
				}
		}
	}

//pre_me($terms);
	
}

function list_my_childs($parent_id, $taxonomyName) {
	$taxonomy_terms = get_categories(
		array(
			'taxonomy' => $taxonomyName,
			'orderby' => 'name',
			'order' => 'ASC',
			'hide_empty' => false,
			'parent' => 0
		)
	);
	$html = "";
	if (!empty($taxonomy_terms)) {
		$html = "<select>";
		

			
			$terms = get_terms( $taxonomyName, array( 'parent' => $parent_id, 'orderby' => 'slug', 'hide_empty' => false ) );
			
			foreach ( $terms as $child_term ) :
				$html .= '<option value="' . $child_term->term_id . '">' .  $child_term->name . '</option>';
			endforeach;
	
		$html .= "</select>";
	}
	return $html;
}


/**
 * Maybe change the wp_die_handler.
 */
add_filter( 'wp_die_handler', function( $handler ) {
	return ! is_admin() ? 'themed_wp_die_handler' : $handler;
}, 10 );


/**
 * Use a custom wp_die() handler.
 */
function themed_wp_die_handler( $message, $title = '', $args = array() ) {
	$defaults = array( 'response' => 500 );
	$r = wp_parse_args($args, $defaults);

	if ( function_exists( 'is_wp_error' ) && is_wp_error( $message ) ) {
		$errors = $message->get_error_messages();
		switch ( count( $errors ) ) {
			case 0 :
				$message = '';
				break;
			case 1 :
				$message = $errors[0];
				break;
			default :
				$message = "<ul>\n\t\t<li>" . join( "</li>\n\t\t<li>", $errors ) . "</li>\n\t</ul>";
				break;
		}

	} else {
		$message = strip_tags( $message );
	}
	
		require_once get_stylesheet_directory() . '/wp-die.php';

	

	die();
}











