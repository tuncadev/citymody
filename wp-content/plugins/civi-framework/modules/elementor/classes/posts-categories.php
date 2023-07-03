<?php

class Civi_Posts_Categories extends \ElementorPro\Modules\Posts\Skins\Skin_Base
{

    public function get_id()
    {
        return 'civi-posts-categories';
    }

    public function get_title()
    {
        return __('Has Categories', 'civi-framework');
    }

    public function get_container_class()
    {
        if ($this->get_id() == 'civi-posts-categories') {
            if ($this->get_instance_value('masonry')) {
                return 'elementor-posts--skin-classic elementor-posts-masonry elementor-posts--skin-' . $this->get_id();
            }
            return 'elementor-posts--skin-classic elementor-has-item-ratio elementor-posts--skin-' . $this->get_id();
        }
        return 'elementor-posts--skin-' . $this->get_id();
    }

    protected function render_title()
    {
        if (!$this->get_instance_value('show_title')) {
            return;
        }

        $tag = $this->get_instance_value('title_tag');
        $categories = get_the_category();
        $cur_image = CIVI_PLUGIN_URL . 'assets/images/no-image.jpg';
        ?>

        <?php if (!has_post_thumbnail()) : ?>
        <a class="elementor-post__thumbnail__link" href="<?php echo get_the_permalink(); ?>">
            <div class="elementor-post__thumbnail">
                <img src="<?php echo $cur_image; ?>" alt="Thumbnail">
            </div>
        </a>
    <?php endif; ?>

        <?php if ($categories) : ?>
        <ul class="post-categories">
            <?php foreach ($categories as $cat) : ?>
                <li><a href="<?php echo get_category_link($cat); ?>"><?php esc_html_e($cat->name); ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

        <<?php echo $tag; ?> class="elementor-post__title">
        <a href="<?php echo $this->current_permalink; ?>">
            <?php the_title(); ?>
        </a>
        </<?php echo $tag; ?>>
        <?php
    }
}
