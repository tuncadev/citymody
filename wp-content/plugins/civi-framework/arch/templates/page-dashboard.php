<?php

/**l
 * The template for displaying Dashboard pages
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php if (!function_exists('aioseo') && !function_exists('yoast_breadcrumb')) { ?>
        <meta name="description" content="You are looking for a topic about jobs that optimize seo as well as current best features, please choose our Civi theme">
    <?php } ?>

    <link rel="profile" href="http://gmpg.org/xfn/11">

    <?php
    wp_enqueue_style(CIVI_PLUGIN_PREFIX . 'dashboard');
    wp_enqueue_style('select2');
    wp_enqueue_style(CIVI_PLUGIN_PREFIX . 'select2');
    wp_enqueue_script('select2');
    wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'select2');
    wp_dequeue_style('elementor-frontend');
    ?>

    <?php wp_head(); ?>
</head>
<?php
$dir = '';
$enable_rtl_mode = civi_get_option('enable_rtl_mode', 0);
if (is_rtl() || $enable_rtl_mode) {
    $dir = 'dir=rtl';
}
?>

<body <?php body_class() ?> <?php echo esc_attr($dir); ?>>
<?php wp_body_open(); ?>
<?php
$layout_content = Civi_Helper::get_setting('layout_content');
$header_classes = array();
?>

<div id="wrapper" class="page-dashboard <?php echo esc_attr($layout_content); ?>">
    <?php global $current_user;
    if (in_array('civi_user_candidate', (array)$current_user->roles)) {
        civi_get_template('dashboard/candidate/nav.php');
    } else {
        civi_get_template('dashboard/employer/nav.php');
    } ?>
    <div id="civi-content-dashboard">
        <header class="site-header <?php echo join(' ', $header_classes); ?>">
            <?php get_template_part('templates/header/header'); ?>
        </header>
        <div id="main" class="site-main" role="main">
            <?php
            // Start the loop.
            while (have_posts()) : the_post();
                the_content();
            endwhile;
            ?>
        </div>
        <footer class="site-footer">
            <?php get_template_part('templates/footer/copyright'); ?>
        </footer>
    </div>
    <?php wp_footer(); ?>
</body>

</html>
