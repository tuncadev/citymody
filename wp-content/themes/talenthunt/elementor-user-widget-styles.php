<?php
echo '<a href="'. esc_url( get_author_posts_url($user_id) ).'">';
 echo get_avatar($user_id, '500'); // Author Image
echo '</a>';
 echo '<div class="user-title-meta-info">';
    echo '<h3><a href="' . esc_url( get_author_posts_url($user_id) ) . '">' .  $user_name . '</a></h3>'; // Author Name
    $user_settings = !empty($settings) ? $settings : '';
    echo kaya_pods_user_info($user_settings, $user); // Author Information
echo '</div>';
?>