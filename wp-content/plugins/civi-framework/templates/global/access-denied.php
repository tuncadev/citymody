<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (empty($type)) {
    return;
}
?>
<div class="access-denied not-login">
    <div class="container">
        <div class="civi-my-page">
            <div class="entry-my-page">
                <?php
                switch ($type) {
                    case 'not_login':
                ?>
                        <div class="account logged-out civi-message alert-success">
                            <div class="icon-message">
                                <i class="fal fa-thumbs-up large"></i>
                            </div>

                            <div class="entry-message">
                                <span><?php esc_html_e('You need login to continue.', 'civi-framework'); ?></span>
                                <a href="#popup-form" class="btn-login"><?php esc_html_e('Login Here', 'civi-framework'); ?></a>
                                <span><?php esc_html_e('or', 'civi-framework'); ?></span>
                                <a href="#popup-form" class="btn-register"><?php esc_html_e('Sign Up Now', 'civi-framework'); ?></a>
                            </div>
                        </div>
                    <?php
                        break;

                    case 'warning':
                    ?>
                        <div class="account logged-out civi-message alert-warning">
                            <div class="icon-message">
                                <i class="fal fa-exclamation-circle large"></i>
                            </div>

                            <div class="entry-message">
                                <p><?php esc_html_e('You are now a Premium Member.', 'civi-framework'); ?></p>
                            </div>
                        </div>
                    <?php
                        break;

                    case 'error':
                    ?>
                        <div class="account logged-out civi-message alert-error">
                            <div class="icon-message">
                                <i class="far fa-times large"></i>
                            </div>

                            <div class="entry-message">
                                <p><?php esc_html_e('An error occurred. Please try again.', 'civi-framework'); ?></p>
                            </div>
                        </div>
                    <?php
                        break;

                    case 'free_submit':
                    ?>
                        <div class="account logged-out civi-message alert-warning">
                            <div class="icon-message">
                                <i class="fal fa-exclamation-circle large"></i>
                            </div>

                            <div class="entry-message">
                                <p>
                                    <?php esc_html_e("You are on free submit active", 'civi-framework'); ?>
                                    <a href="<?php echo civi_get_permalink('jobs_submit'); ?>">
                                        <?php esc_html_e('Add Jobs', 'civi-framework'); ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                <?php
                        break;

                    default:
                        # code...
                        break;
                }
                ?>

            </div>
        </div>
    </div>
</div>