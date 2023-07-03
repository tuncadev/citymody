<?php
/*---------------------------------------------------------------------------------------
Theme Logo
---------------------------------------------------------------------------------------*/
function talenthunt_kaya_logo(){
	 $logo = get_theme_mod( 'custom_logo' ) ? get_theme_mod( 'custom_logo' ) : get_template_directory_uri().'/images/logo.png';
     $img_logo = wp_get_attachment_image_src( $logo , 'full' );
     if( get_theme_mod( 'custom_logo' ) ){
        $img_logo = esc_url($img_logo[0]);
     }else{
        $img_logo = esc_url($logo);
     }
    $choose_logo = get_theme_mod( 'choose_logo' ) ? get_theme_mod( 'choose_logo' ) :'img_logo';
    $display_header_text = get_theme_mod( 'display_header_text' ) ? get_theme_mod( 'display_header_text' ) : '';
    if( $choose_logo == 'img_logo' ){
        echo '<a href="'.esc_url(home_url('/')).'" title="'.esc_attr(get_the_title()).'"><img src="'.esc_url($img_logo).'" alt="'.esc_attr(get_the_title()).'" /></a>';
    }else{
        echo '<h1 class="site-title"><a href="'.esc_url( home_url( '/' ) ).'">'.esc_attr(get_bloginfo( 'name' )).'</a></h1>';
        $description = get_bloginfo( 'description', 'display' );
        if (( $description || is_customize_preview()) && ( $display_header_text != '1' ) ) : ?>
            <p class="site-description"><?php echo esc_attr($description); /* WPCS: xss ok. */ echo '</p>';
        endif;
    }
}

function talenthunt_kaya_header(){ ?>
    <header id="kaya-header-content-wrapper" class="site-header"> <!-- Header Section -->
        <div class="container">
            <div class="header-section">
                <div id="logo"> <!-- Header Logo -->
                    <?php talenthunt_kaya_logo(); ?>               
                </div>
                <nav id="header-navigation"> <!-- Header Navigation -->
                    <?php
                    if ( has_nav_menu('primary') ) { 
                        wp_nav_menu(array('container_id' => 'main-nav','menu_id'=> 'main-menu', 'container_class' => 'menu','theme_location' => 'primary', 'menu_class'=> 'top-nav'));
                    }else{
                        wp_nav_menu(array('container_id' => 'main-nav', 'container'=> 'ul', 'menu_id'=> 'main-nav', 'container_class' => 'menu', 'menu_class'=> 'top-nav'));
                    }
                    if( function_exists('kaya_pods_cpt_shortlist_page_link') ){
                        $shortlist_data = kaya_pods_cpt_shortlist_page_link();
                        if( !empty($shortlist_data) ){
                            echo '<div class="shortlist-align">';
                                echo wp_kses($shortlist_data, true);
                            echo '</div>';
                        }     
                    }
                        if ( is_active_sidebar( 'search_filter' ) ){
                            echo '<div class="toggle_search_icon">';
                                echo '<i class="fa fa-search"></i>';
                            echo '</div>';  
                            echo '<div class="toggle_search_wrapper">';
                                echo '<div class="toggle_search_field">';
                                    echo '<span class="search_close">'; 
                                        echo '<i class="fa fa-times">';
                                        echo '</i>';
                                    echo '</span>';
                                        dynamic_sidebar( 'search_filter' );            
                                echo '</div>';
                            echo '</div>';
                        }
                    echo '<div class="mobile_toggle_menu_icons">'; ?>
                        <input id="main-menu-state" type="checkbox" />
                            <label class="main-menu-btn" for="main-menu-state">
                                <span class="main-menu-btn-icon"></span>
                            </label>
                    </div>
                 </nav><!-- End navigation -->
            </div>      
         </div>     
    </header><!-- End Header Section -->
<?php }

/*---------------------------------------------------------------------------------------
All pages, posts, archives and category page title bar
---------------------------------------------------------------------------------------*/
function talenthunt_kaya_footer(){ ?>
    <footer id="kaya-footer-content-wrapper">
        <div class="container">
            <div class="fullwidth"><span class="copyright"><?php echo get_theme_mod('footer_copy_rights') ? get_theme_mod('footer_copy_rights') : esc_html__(' All Rights Reserved kayapati', 'talenthunt'); // WPCS: XSS OK ?></span></div>
        </div><!-- .site-info -->
    </footer><!-- #colophon -->
    <?php 
}

/*---------------------------------------------------------------------------------------
All pages, posts, archives and category page title bar
---------------------------------------------------------------------------------------*/
if(!function_exists('talenthunt_kaya_page_title')){
    function talenthunt_kaya_page_title()
    {
        $post_id = get_the_ID();
          $disable_page_title_bar = get_post_meta($post_id, 'disable_page_title_bar', true) ? get_post_meta($post_id, 'disable_page_title_bar', true) : '0';
        $custom_page_title = get_post_meta($post_id, 'custom_page_title', true) ? get_post_meta($post_id, 'custom_page_title', true) : '';
         $subheader_section =  get_post_meta(get_the_ID(), 'select_page_subheader', true) ? get_post_meta(get_the_ID(), 'select_page_subheader', true) : 'page_titlebar';
        if( ($subheader_section == 'page_titlebar') || is_single() || is_home() ){
            $page_titlebar_bg_image = get_theme_mod('page_titlebar_bg_image') ? get_theme_mod('page_titlebar_bg_image') : '';
            $choose_pagetitle_bg = get_theme_mod('choose_pagetitle_bg') ? get_theme_mod('choose_pagetitle_bg') : '';
            $image_id = wp_get_attachment_image_src(get_post_meta($post_id, 'upload_page_title_bar_img', true), 'full');
            if(  $image_id ){
                $bg_image = 'style="'.(!empty($image_id) ? 'background:url('.esc_url($image_id[0]).'); background-size:cover;' : '').'"';
            }else{
                if(!empty($page_titlebar_bg_image) && ( $choose_pagetitle_bg =='bg_img' )){
                     $bg_image = 'style="'.(!empty($page_titlebar_bg_image) ? 'background:url('.esc_url($page_titlebar_bg_image).'); background-size:cover;' : '').'"';
                }else{
                    $bg_image ='';
                }
            }
            $page_title = !empty($custom_page_title) ? $custom_page_title : get_the_title($post_id);
            $page_title_description = get_post_meta(get_the_ID(), 'page_title_description', true) ? get_post_meta(get_the_ID(), 'page_title_description', true) : '';
            // WPCS: XSS OK
            echo '<section class="kaya-page-titlebar-wrapper" '.wp_kses($bg_image, true).' >';
                echo '<div class="container" style="">';            
                    if(is_page()){
                        echo '<h2 class="page-title">'.esc_attr($page_title).'</h2>';
                        if( !empty($page_title_description) ){
                            echo '<p>'.esc_attr($page_title_description).'</p>';
                        }
                    }elseif(is_home()){
                        echo '<h2 class="page-title">'.esc_html__( 'Posts', 'talenthunt' ).'</h2>';

                    }elseif( is_single()){ 
                        echo '<h2 class="page-title">'.esc_attr($page_title).'</h2>';
                    }  elseif(is_tag()){
                        echo '<h2 class="page-title">'.esc_html__( 'Tag Archives - ', 'talenthunt' ). single_cat_title( '', false ).'</h2>';
                    }
                    elseif ( is_author() ) {
                        the_post();
                        echo '<h2 class="page-title">'. esc_html__( 'Author Archives:', 'talenthunt' ). ' <span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( "ID" ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span></h2>';
                        rewind_posts();

                    } elseif (is_category()) { 
                        echo '<h2 class="page-title">'. esc_html__( 'Category Archives: ', 'talenthunt' ) . single_cat_title( '', false ).'</h2>';
                    } elseif( is_tax() ){
                        global $post;
                        $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
                        echo '<h2 class="page-title">' .esc_attr($term->name).'</h2>';
                    }elseif (is_search()) { ?>
                        <?php echo '<h2 class="page-title">'.esc_html__( 'Search Results for:', 'talenthunt' ).' '. get_search_query().'</h2>';
                    }elseif (is_404()) {
                        echo '<h2  class="page-title">'.esc_html__( 'Error 404 - Not Found', 'talenthunt' ).'</h2>';
                    }elseif ( is_day() ){
                        echo '<h2 class="page-title">'.esc_html__( 'Daily Archives:', 'talenthunt' ), '<span>' . get_the_date() . '</span></h2>';
                    }            
                    elseif ( is_month() ) { 
                        echo '<h2 class="page-title">'. esc_html__( 'Monthly Archives:', 'talenthunt' ), '<span>' . get_the_date( 'F Y' ) . '</span></h2>';
                    } elseif ( is_year() ){
                        echo '<h2 class="page-title">'. esc_html__( 'Yearly Archives:', 'talenthunt' ), '<span>' . get_the_date( 'Y' ) . '</span></h2>';
                    }else{ }
                   
                echo '</div>';
            echo'</section>';
        }
    }
}

/*---------------------------------------------------------------------------------------
Related Artists Code for single page
---------------------------------------------------------------------------------------*/
function talenthunt_kaya_related_post($related_title='', $count='3', $args = array() ) {
       $args = wp_parse_args( (array) $args, array(
        'orderby' => 'rand',
        'return'  => 'query', // Valid values are: 'query' (WP_Query object), 'array' (the arguments array)
    ) );

    $related_args = array(
        'post_type'      => get_post_type( get_the_ID() ),
        'posts_per_page' => $count,
        'post_status'    => 'publish',
        'post__not_in'   => array(get_the_ID() ),
        'orderby'        => $args['orderby'],
        'tax_query'      => array()
    );

    $post       = get_post(get_the_ID() );
    $taxonomies = get_object_taxonomies( $post, 'names' );

    foreach ( $taxonomies as $taxonomy ) {
        $terms = get_the_terms( get_the_ID(), $taxonomy );
        if ( empty( $terms ) ) {
            continue;
        }
        $term_list                   = wp_list_pluck( $terms, 'slug' );
        $related_args['tax_query'][] = array(
            'taxonomy' => $taxonomy,
            'field'    => 'slug',
            'terms'    => $term_list
        );
    }

    if ( count( $related_args['tax_query'] ) > 1 ) {
        $related_args['tax_query']['relation'] = 'OR';
    }

    if ( $args['return'] == 'query' ) {
         $related = new WP_Query( $related_args );
          if( $related->have_posts() ): ?>
        <div class="related_posts">
            <?php echo '<h3>'.(!empty($related_title) ? esc_attr($related_title) : esc_html__('Related Posts', 'talenthunt' )).'</h3>'; ?>
            <ul>
                <?php while( $related->have_posts() ): $related->the_post(); ?>
                <li>
                    <div class="relatedthumb"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
                        <?php echo the_post_thumbnail( array(100, 100) ); ?></a>
                    </div>
                    <div class="related_title">
                        <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                        <?php echo '<span>'.esc_attr(ucwords(str_replace('-', ' ', $term_list[0]))).'</span>'; ?>
                    </div>
                </li>
                <?php endwhile; ?>
            </ul>
        </div>
        <?php
        endif;
        wp_reset_postdata();
    } else {
       
    }
}

/*---------------------------------------------------------------------------------------
 Image resizer Functionality
---------------------------------------------------------------------------------------*/
function talenthunt_kaya_breadcrumb() {
    echo '<a href="'.esc_url(home_url()).'" rel="nofollow">'.esc_html__('Home', 'talenthunt').'</a>';
    if (is_category() || is_single()) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
        the_category(' &bull; ');
            if (is_single()) { }
    } elseif (is_page()) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
        echo the_title();
    } elseif (is_search()) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;Search Results for... ";
        echo '"<em>';
        echo the_search_query();
        echo '</em>"';
    }
}

/*---------------------------------------------------------------------------------------
 Image resizer Functionality
---------------------------------------------------------------------------------------*/
if( !function_exists('talenthunt_kaya_image_sizes') ){
	function talenthunt_kaya_image_sizes($url, $width, $height=0, $align='') {
		return talenthunt_kaya_image_resize($url, $width, $height, true, $align, false);
	}
}

/*---------------------------------------------------------------------------------------
 * Limit the wordpress post in Words
 * @param int limit
 ---------------------------------------------------------------------------------------*/

 /*
if( !function_exists('talenthunt_kaya_content_display_words') ){
  function talenthunt_kaya_content_display_words($limit) {
    //$content = explode(' ', get_the_content(), $limit);
    $content = wp_trim_words( get_the_content(), $limit, '' );
    return $content;
  }
} 
*/
/*---------------------------------------------------------------------------------------
 Slider Functionality
---------------------------------------------------------------------------------------*/
if( !function_exists('talenthunt_kaya_slider_shortcode') ){
	function talenthunt_kaya_slider_shortcode() {
		$post_id = get_the_ID();
        $subheader_section =  get_post_meta(get_the_ID(), 'select_page_subheader', true) ? get_post_meta(get_the_ID(), 'select_page_subheader', true) : '';
        if( $subheader_section == 'slider' ){
            $main_slider_shortcode =  get_post_meta($post_id, 'main_slider_shortcode', true) ? get_post_meta($post_id, 'main_slider_shortcode', true) : '0'; 
            if( $main_slider_shortcode ){
                echo '<div class="main-pages-slider-wrapper">';
                        echo do_shortcode($main_slider_shortcode);
                echo '</div>';
            }
        }
    }
}
/*---------------------------------------------------------------------------------------
 * RGBA Color Settings
 * @param (int) color
 * @param (float) opacity
 ---------------------------------------------------------------------------------------*/
function talenthunt_kaya_rgba_color( $color, $opacity='0.5' ) {
    if ( $color[0] == '#' ) {
            $color = substr( $color, 1 );
    }
    if ( strlen( $color ) == 6 ) {
            list( $r, $g, $b ) = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
    } elseif ( strlen( $color ) == 3 ) {
            list( $r, $g, $b ) = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
    } else {
            return false;
    }
    $r = hexdec( $r );
    $g = hexdec( $g );
    $b = hexdec( $b );
    return 'rgba('.$r.','.$g.','.$b.', '.$opacity.')';
}

/*---------------------------------------------------------------------------------------
 * Posts Pagination
 ---------------------------------------------------------------------------------------*/
if ( ! function_exists( 'talenthunt_kaya_pagination' ) ) :
function talenthunt_kaya_pagination() {
    // Don't print empty markup if there's only one page.
    if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
        return;
    }
    $paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
    $pagenum_link = html_entity_decode( get_pagenum_link() );
    $query_args   = array();
    $url_parts    = explode( '?', $pagenum_link );
    if ( isset( $url_parts[1] ) ) {
        wp_parse_str( $url_parts[1], $query_args );
    }
    $pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
    $pagenum_link = trailingslashit( $pagenum_link ) . '%_%';
    $format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
    $format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';
    // Set up paginated links.
    $links = paginate_links( array(
        'base'     => $pagenum_link,
        'format'   => $format,
        'total'    => $GLOBALS['wp_query']->max_num_pages,
        'current'  => $paged,
        'mid_size' => 3,
        //'add_args' => array_map( 'urlencode', $query_args ),
        'prev_text' => '<i class="fa fa-angle-left"></i>',
        'next_text' => '<i class="fa fa-angle-right"></i>',
        'type'      => 'list',
    ) );
    $pagination_allowed_tags = array(
        'a' => array(
            'href' => array(),
            'title' => array(),
            'class' => array()
        ),
        'i' => array(
            'class' => array()
        ),
        'span' => array(
            'class' => array()
        ),
        'ul' => array(
            'class' => array()
        ),
        'li' => array(),
    );
    if ( $links ) :
    ?>  <div class="pagination">
            <?php echo wp_kses($links,$pagination_allowed_tags); ?>
        </div>       
    <?php
    endif;
}
endif;
?>