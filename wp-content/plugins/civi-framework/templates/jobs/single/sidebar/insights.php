<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$jobs_id = get_the_ID();
$closing_date    = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'closing_date', true);
$jobs_quantity  = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_quantity', true);
$jobs_gender = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_gender', true);

$jobs_location =  get_the_terms($jobs_id, 'jobs-location');
$jobs_career =  get_the_terms($jobs_id, 'jobs-career');
$jobs_experience =  get_the_terms($jobs_id, 'jobs-experience');
$jobs_qualification =  get_the_terms($jobs_id, 'jobs-qualification');

$enable_toggle   = civi_get_option('enable_toggle_show_more_apply', '1');

$jobs_salary_active   = civi_get_option('enable_single_jobs_salary', '1');
$jobs_salary_show = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_salary_show', true);
$jobs_salary_rate = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_salary_rate', true);
$jobs_salary_minimum = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_salary_minimum', true);
$jobs_salary_maximum = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_salary_maximum', true);
$jobs_maximum_price = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_maximum_price', true);
$jobs_minimum_price = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_minimum_price', true);
$jobs_currency_type = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_currency_type', true);

$public_date = get_the_date('Y-m-d');
$jobs_days_closing =  get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_days_closing', true);
$date_posted    = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'date_posted', true);
$closing_date   = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'closing_date', true);
$expiration_date = date('Y-m-d', strtotime($public_date . '+' . $jobs_days_closing . ' days'));

$classes = array();
$enable_sticky_sidebar_type = civi_get_option('enable_sticky_sidebar_type', 1);
if ($enable_sticky_sidebar_type) {
    $classes[] = 'has-sticky';
}
?>
<div class="jobs-insights-sidebar block-archive-sidebar <?php echo implode(" ", $classes); ?> <?php if ($enable_toggle == '1') : ?>active<?php endif; ?>">
    <h4 class="title-jobs"><?php esc_html_e('Job role insights', 'civi-framework') ?></h4>
    <ul class="list-jobs-insights-sidebar">
        <?php if ($date_posted == '1') : ?>
            <li class="list-item">
                <div class="icon-jobs">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 2H19C19.2652 2 19.5196 2.10536 19.7071 2.29289C19.8946 2.48043 20 2.73478 20 3V19C20 19.2652 19.8946 19.5196 19.7071 19.7071C19.5196 19.8946 19.2652 20 19 20H1C0.734784 20 0.48043 19.8946 0.292893 19.7071C0.105357 19.5196 0 19.2652 0 19V3C0 2.73478 0.105357 2.48043 0.292893 2.29289C0.48043 2.10536 0.734784 2 1 2H5V0H7V2H13V0H15V2ZM13 4H7V6H5V4H2V8H18V4H15V6H13V4ZM18 10H2V18H18V10Z" fill="#007456" />
                    </svg>
                </div>
                <div class="info">
                    <p class="title-info"><?php esc_html_e('Date posted', 'civi-framework') ?></p>
                    <p class="details-info"><?php echo $public_date ?></p>
                </div>
            </li>
        <?php endif; ?>
        <?php if ($closing_date == '1' && $date_posted == '1' && $expiration_date >= $public_date) : ?>
            <li class="list-item">
                <div class="icon-jobs">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 2H19C19.2652 2 19.5196 2.10536 19.7071 2.29289C19.8946 2.48043 20 2.73478 20 3V19C20 19.2652 19.8946 19.5196 19.7071 19.7071C19.5196 19.8946 19.2652 20 19 20H1C0.734784 20 0.48043 19.8946 0.292893 19.7071C0.105357 19.5196 0 19.2652 0 19V3C0 2.73478 0.105357 2.48043 0.292893 2.29289C0.48043 2.10536 0.734784 2 1 2H5V0H7V2H13V0H15V2ZM13 4H7V6H5V4H2V8H18V4H15V6H13V4ZM18 10H2V18H18V10Z" fill="#007456" />
                    </svg>
                </div>
                <div class="info">
                    <p class="title-info"><?php esc_html_e('Closing date', 'civi-framework') ?></p>
                    <p class="details-info"><?php echo $expiration_date ?></p>
                </div>
            </li>
        <?php endif; ?>
        <?php if (is_array($jobs_location)) : ?>
            <li class="list-item">
                <div class="icon-jobs">
                    <svg width="18" height="22" viewBox="0 0 18 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 18.8999L13.95 13.9499C14.9289 12.9709 15.5955 11.7236 15.8656 10.3658C16.1356 9.00795 15.9969 7.60052 15.4671 6.32148C14.9373 5.04244 14.04 3.94923 12.8889 3.18009C11.7378 2.41095 10.3844 2.00043 9 2.00043C7.61557 2.00043 6.26222 2.41095 5.11109 3.18009C3.95996 3.94923 3.06275 5.04244 2.53292 6.32148C2.00308 7.60052 1.86442 9.00795 2.13445 10.3658C2.40449 11.7236 3.07111 12.9709 4.05 13.9499L9 18.8999ZM9 21.7279L2.636 15.3639C1.37734 14.1052 0.520187 12.5016 0.172928 10.7558C-0.17433 9.00995 0.00390685 7.20035 0.685099 5.55582C1.36629 3.91129 2.51984 2.50569 3.99988 1.51677C5.47992 0.527838 7.21998 0 9 0C10.78 0 12.5201 0.527838 14.0001 1.51677C15.4802 2.50569 16.6337 3.91129 17.3149 5.55582C17.9961 7.20035 18.1743 9.00995 17.8271 10.7558C17.4798 12.5016 16.6227 14.1052 15.364 15.3639L9 21.7279V21.7279ZM9 10.9999C9.53044 10.9999 10.0391 10.7892 10.4142 10.4141C10.7893 10.0391 11 9.53035 11 8.99992C11 8.46949 10.7893 7.96078 10.4142 7.58571C10.0391 7.21064 9.53044 6.99992 9 6.99992C8.46957 6.99992 7.96086 7.21064 7.58579 7.58571C7.21072 7.96078 7 8.46949 7 8.99992C7 9.53035 7.21072 10.0391 7.58579 10.4141C7.96086 10.7892 8.46957 10.9999 9 10.9999ZM9 12.9999C7.93914 12.9999 6.92172 12.5785 6.17158 11.8283C5.42143 11.0782 5 10.0608 5 8.99992C5 7.93906 5.42143 6.92164 6.17158 6.17149C6.92172 5.42135 7.93914 4.99992 9 4.99992C10.0609 4.99992 11.0783 5.42135 11.8284 6.17149C12.5786 6.92164 13 7.93906 13 8.99992C13 10.0608 12.5786 11.0782 11.8284 11.8283C11.0783 12.5785 10.0609 12.9999 9 12.9999Z" fill="#007456" />
                    </svg>
                </div>
                <div class="info">
                    <p class="title-info"><?php esc_html_e('Hiring location', 'civi-framework') ?></p>
                    <p class="details-info">
                        <?php foreach ($jobs_location as $location) { ?>
                            <span><?php echo $location->name; ?></span>
                        <?php } ?>
                    </p>
                </div>
            </li>
        <?php endif; ?>
        <?php if (($jobs_salary_active && $jobs_salary_show == 'range' && $jobs_salary_minimum !== '' && $jobs_salary_maximum !== '')
            || ($jobs_salary_active && $jobs_salary_show == 'starting_amount' && $jobs_minimum_price !== '')
            || ($jobs_salary_active && $jobs_salary_show == 'maximum_amount' && $jobs_maximum_price !== '') || ($jobs_salary_active && $jobs_salary_show == 'agree')
        ) : ?>
            <li class="list-item salary">
                <div class="icon-jobs">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 20C4.477 20 0 15.523 0 10C0 4.477 4.477 0 10 0C15.523 0 20 4.477 20 10C20 15.523 15.523 20 10 20ZM10 18C12.1217 18 14.1566 17.1571 15.6569 15.6569C17.1571 14.1566 18 12.1217 18 10C18 7.87827 17.1571 5.84344 15.6569 4.34315C14.1566 2.84286 12.1217 2 10 2C7.87827 2 5.84344 2.84286 4.34315 4.34315C2.84286 5.84344 2 7.87827 2 10C2 12.1217 2.84286 14.1566 4.34315 15.6569C5.84344 17.1571 7.87827 18 10 18ZM6.5 12H12C12.1326 12 12.2598 11.9473 12.3536 11.8536C12.4473 11.7598 12.5 11.6326 12.5 11.5C12.5 11.3674 12.4473 11.2402 12.3536 11.1464C12.2598 11.0527 12.1326 11 12 11H8C7.33696 11 6.70107 10.7366 6.23223 10.2678C5.76339 9.79893 5.5 9.16304 5.5 8.5C5.5 7.83696 5.76339 7.20108 6.23223 6.73223C6.70107 6.26339 7.33696 6 8 6H9V4H11V6H13.5V8H8C7.86739 8 7.74022 8.05268 7.64645 8.14645C7.55268 8.24022 7.5 8.36739 7.5 8.5C7.5 8.63261 7.55268 8.75979 7.64645 8.85355C7.74022 8.94732 7.86739 9 8 9H12C12.663 9 13.2989 9.26339 13.7678 9.73223C14.2366 10.2011 14.5 10.837 14.5 11.5C14.5 12.163 14.2366 12.7989 13.7678 13.2678C13.2989 13.7366 12.663 14 12 14H11V16H9V14H6.5V12Z" fill="#007456" />
                    </svg>
                </div>
                <div class="info">
                    <p class="title-info"><?php esc_html_e('Offered salary', 'civi-framework') ?></p>
                    <p class="details-info">
                        <?php echo civi_get_salary_jobs($jobs_id); ?>
                    </p>
                </div>
            </li>
        <?php endif; ?>
        <?php if (is_array($jobs_career)) : ?>
            <li class="list-item">
                <div class="icon-jobs">
                    <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18.2279 17.7321L19.9959 15.9641L21.7629 17.7321C22.1126 18.0817 22.3508 18.5271 22.4473 19.0121C22.5439 19.4971 22.4944 19.9998 22.3052 20.4566C22.116 20.9135 21.7956 21.304 21.3844 21.5787C20.9733 21.8535 20.4899 22.0001 19.9954 22.0001C19.5009 22.0001 19.0176 21.8535 18.6064 21.5787C18.1953 21.304 17.8749 20.9135 17.6857 20.4566C17.4965 19.9998 17.447 19.4971 17.5435 19.0121C17.6401 18.5271 17.8782 18.0817 18.2279 17.7321ZM7.87794 0.0800781L19.1919 11.3931C19.2849 11.486 19.3587 11.5962 19.409 11.7176C19.4593 11.839 19.4852 11.9692 19.4852 12.1006C19.4852 12.232 19.4593 12.3621 19.409 12.4835C19.3587 12.6049 19.2849 12.7152 19.1919 12.8081L10.7069 21.2931C10.5194 21.4805 10.2651 21.5859 9.99994 21.5859C9.73477 21.5859 9.48046 21.4805 9.29294 21.2931L0.807935 12.8081C0.714959 12.7152 0.6412 12.6049 0.590875 12.4835C0.540551 12.3621 0.514648 12.232 0.514648 12.1006C0.514648 11.9692 0.540551 11.839 0.590875 11.7176C0.6412 11.5962 0.714959 11.486 0.807935 11.3931L8.58594 3.61508L6.46394 1.49408L7.87994 0.0800781H7.87794ZM9.99994 5.03008L2.92894 12.1001L9.99994 19.1731L17.0709 12.1021L9.99994 5.02908V5.03008Z" fill="#007456" />
                    </svg>
                </div>
                <div class="info">
                    <p class="title-info"><?php esc_html_e('Career level', 'civi-framework') ?></p>
                    <p class="details-info">
                        <?php foreach ($jobs_career as $career) { ?>
                            <span><?php echo $career->name; ?></span>
                        <?php } ?>
                    </p>
                </div>
            </li>
        <?php endif; ?>
        <?php if (is_array($jobs_qualification)) : ?>
            <li class="list-item">
                <div class="icon-jobs">
                    <svg width="16" height="21" viewBox="0 0 16 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.00009 6.5L10.1161 11.588L15.6081 12.028L11.4241 15.612L12.7021 20.972L8.00009 18.1L3.29809 20.972L4.57609 15.612L0.39209 12.028L5.88409 11.588L8.00009 6.5ZM8.00009 11.707L7.26109 13.484L5.34509 13.637L6.80509 14.888L6.35809 16.759L8.00009 15.756L9.64109 16.759L9.19509 14.889L10.6541 13.637L8.73909 13.484L8.00009 11.707ZM4.00009 0V9H2.00009V0H4.00009ZM14.0001 0V9H12.0001V0H14.0001ZM9.00009 0V5H7.00009V0H9.00009Z" fill="#007456" />
                    </svg>
                </div>
                <div class="info">
                    <p class="title-info"><?php esc_html_e('Qualification', 'civi-framework') ?></p>
                    <p class="details-info">
                        <?php foreach ($jobs_qualification as $qualification) { ?>
                            <span><?php echo $qualification->name; ?></span>
                        <?php } ?>
                    </p>
                </div>
            </li>
        <?php endif; ?>
        <?php if (is_array($jobs_experience)) : ?>
            <li class="list-item">
                <div class="icon-jobs">
                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 2V16.721C20.0001 16.818 19.972 16.913 19.9191 16.9943C19.8662 17.0756 19.7908 17.1398 19.702 17.179L11 21.03L2.298 17.18C2.20894 17.1407 2.13329 17.0762 2.08035 16.9945C2.02742 16.9128 1.99949 16.8174 2 16.72V2H0V0H22V2H20ZM4 2V15.745L11 18.845L18 15.745V2H4ZM7 6H15V8H7V6ZM7 10H15V12H7V10Z" fill="#007456" />
                    </svg>
                </div>
                <div class="info">
                    <p class="title-info"><?php esc_html_e('Experience', 'civi-framework') ?></p>
                    <p class="details-info">
                        <?php foreach ($jobs_experience as $experience) { ?>
                            <span><?php echo $experience->name; ?></span>
                        <?php } ?>
                    </p>
                </div>
            </li>
        <?php endif; ?>
        <?php if ($jobs_quantity !== '') : ?>
            <li class="list-item">
                <div class="icon-jobs">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 9C11.3261 9 12.5979 9.52678 13.5355 10.4645C14.4732 11.4021 15 12.6739 15 14V20H13V14C13 13.2348 12.7077 12.4985 12.1827 11.9417C11.6578 11.385 10.9399 11.0499 10.176 11.005L10 11C9.23479 11 8.49849 11.2923 7.94174 11.8173C7.38499 12.3422 7.04989 13.0601 7.005 13.824L7 14V20H5V14C5 12.6739 5.52678 11.4021 6.46447 10.4645C7.40215 9.52678 8.67392 9 10 9ZM3.5 12C3.779 12 4.05 12.033 4.31 12.094C4.13902 12.603 4.03777 13.1328 4.009 13.669L4 14V14.086C3.88505 14.0449 3.76549 14.018 3.644 14.006L3.5 14C3.12712 14 2.76761 14.1389 2.49158 14.3896C2.21555 14.6403 2.0428 14.9848 2.007 15.356L2 15.5V20H0V15.5C0 14.5717 0.368749 13.6815 1.02513 13.0251C1.6815 12.3687 2.57174 12 3.5 12ZM16.5 12C17.4283 12 18.3185 12.3687 18.9749 13.0251C19.6313 13.6815 20 14.5717 20 15.5V20H18V15.5C18 15.1271 17.8611 14.7676 17.6104 14.4916C17.3597 14.2156 17.0152 14.0428 16.644 14.007L16.5 14C16.325 14 16.157 14.03 16 14.085V14C16 13.334 15.892 12.694 15.691 12.096C15.95 12.033 16.221 12 16.5 12ZM3.5 6C4.16304 6 4.79893 6.26339 5.26777 6.73223C5.73661 7.20107 6 7.83696 6 8.5C6 9.16304 5.73661 9.79893 5.26777 10.2678C4.79893 10.7366 4.16304 11 3.5 11C2.83696 11 2.20107 10.7366 1.73223 10.2678C1.26339 9.79893 1 9.16304 1 8.5C1 7.83696 1.26339 7.20107 1.73223 6.73223C2.20107 6.26339 2.83696 6 3.5 6ZM16.5 6C17.163 6 17.7989 6.26339 18.2678 6.73223C18.7366 7.20107 19 7.83696 19 8.5C19 9.16304 18.7366 9.79893 18.2678 10.2678C17.7989 10.7366 17.163 11 16.5 11C15.837 11 15.2011 10.7366 14.7322 10.2678C14.2634 9.79893 14 9.16304 14 8.5C14 7.83696 14.2634 7.20107 14.7322 6.73223C15.2011 6.26339 15.837 6 16.5 6ZM3.5 8C3.36739 8 3.24021 8.05268 3.14645 8.14645C3.05268 8.24021 3 8.36739 3 8.5C3 8.63261 3.05268 8.75979 3.14645 8.85355C3.24021 8.94732 3.36739 9 3.5 9C3.63261 9 3.75979 8.94732 3.85355 8.85355C3.94732 8.75979 4 8.63261 4 8.5C4 8.36739 3.94732 8.24021 3.85355 8.14645C3.75979 8.05268 3.63261 8 3.5 8ZM16.5 8C16.3674 8 16.2402 8.05268 16.1464 8.14645C16.0527 8.24021 16 8.36739 16 8.5C16 8.63261 16.0527 8.75979 16.1464 8.85355C16.2402 8.94732 16.3674 9 16.5 9C16.6326 9 16.7598 8.94732 16.8536 8.85355C16.9473 8.75979 17 8.63261 17 8.5C17 8.36739 16.9473 8.24021 16.8536 8.14645C16.7598 8.05268 16.6326 8 16.5 8ZM10 0C11.0609 0 12.0783 0.421427 12.8284 1.17157C13.5786 1.92172 14 2.93913 14 4C14 5.06087 13.5786 6.07828 12.8284 6.82843C12.0783 7.57857 11.0609 8 10 8C8.93913 8 7.92172 7.57857 7.17157 6.82843C6.42143 6.07828 6 5.06087 6 4C6 2.93913 6.42143 1.92172 7.17157 1.17157C7.92172 0.421427 8.93913 0 10 0ZM10 2C9.46957 2 8.96086 2.21071 8.58579 2.58579C8.21071 2.96086 8 3.46957 8 4C8 4.53043 8.21071 5.03914 8.58579 5.41421C8.96086 5.78929 9.46957 6 10 6C10.5304 6 11.0391 5.78929 11.4142 5.41421C11.7893 5.03914 12 4.53043 12 4C12 3.46957 11.7893 2.96086 11.4142 2.58579C11.0391 2.21071 10.5304 2 10 2Z" fill="#007456" />
                    </svg>
                </div>
                <div class="info">
                    <p class="title-info"><?php esc_html_e('Quantity', 'civi-framework') ?></p>
                    <p class="details-info"><?php echo $jobs_quantity ?> <?php esc_html_e('person', 'civi-framework') ?></p>
                </div>
            </li>
        <?php endif; ?>
        <?php if ($jobs_gender !== '') : ?>
            <li class="list-item">
                <div class="icon-jobs">
                    <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.05 5.537L15.585 2H11V0H19V8H17V3.414L13.463 6.951C14.6168 8.46352 15.1528 10.358 14.9623 12.2508C14.7719 14.1437 13.8692 15.8934 12.4372 17.1457C11.0051 18.398 9.15067 19.0594 7.24934 18.9958C5.34801 18.9322 3.5419 18.1485 2.1967 16.8033C0.851511 15.4581 0.0677747 13.652 0.00419383 11.7506C-0.0593871 9.8493 0.601938 7.99486 1.85426 6.5628C3.10658 5.13075 4.85631 4.2281 6.74914 4.03764C8.64197 3.84719 10.5364 4.38315 12.049 5.537H12.05ZM7.49997 17C8.95866 17 10.3576 16.4205 11.3891 15.3891C12.4205 14.3576 13 12.9587 13 11.5C13 10.0413 12.4205 8.64236 11.3891 7.61091C10.3576 6.57946 8.95866 6 7.49997 6C6.04128 6 4.64233 6.57946 3.61088 7.61091C2.57943 8.64236 1.99997 10.0413 1.99997 11.5C1.99997 12.9587 2.57943 14.3576 3.61088 15.3891C4.64233 16.4205 6.04128 17 7.49997 17Z" fill="#007456" />
                    </svg>
                </div>
                <div class="info">
                    <p class="title-info"><?php esc_html_e('Gender', 'civi-framework') ?></p>
                    <p class="details-info"><?php echo $jobs_gender ?></p>
                </div>
            </li>
        <?php endif; ?>
    </ul>
    <?php if ($enable_toggle == '1') : ?>
        <div class="toggle-insights-sidebar">
            <a href="#" class="show-more-insights-sidebar"><?php esc_html_e('Show more', 'civi-framework'); ?><i class="fas fa-angle-down"></i></a>
            <a href="#" class="hide-all-insights-sidebar"><?php esc_html_e('Hide less', 'civi-framework'); ?><i class="fas fa-angle-up"></i></a>
        </div>
    <?php endif; ?>
</div>
