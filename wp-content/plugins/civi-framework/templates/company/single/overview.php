<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$content = get_the_content();
if (isset($content) && !empty($content)) : ?>
    <div class="block-archive-inner company-overview-details civi-description-details">
        <h4 class="title-company"><?php esc_html_e('Overview', 'civi-framework') ?></h4>
        <div class="civi-description">
            <?php echo the_content(); ?>
        </div>
        <div class="toggle-description">
            <a href="#" class="show-more-description"><?php esc_html_e('Show more', 'civi-framework'); ?><i class="fas fa-angle-down"></i></a>
            <a href="#" class="hide-all-description"><?php esc_html_e('Hide less', 'civi-framework'); ?><i class="fas fa-angle-up"></i></a>
        </div>
    </div>
<?php endif; ?>