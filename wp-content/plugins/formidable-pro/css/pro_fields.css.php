<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
.with_frm_style, .frm_forms {
<?php
if ( ! empty( $vars ) && is_callable( 'FrmStylesHelper::output_vars' ) ) {
	FrmStylesHelper::output_vars( $defaults, array(), $vars );
} else {
	foreach ( $vars as $var ) {
		?>
		--<?php echo esc_html( str_replace( '_', '-', $var ) ); ?>:<?php echo esc_html( $defaults[ $var ] ); ?>;
		<?php
	}
}
	?>
	--progress-border-color-b: <?php echo esc_html( FrmStylesHelper::adjust_brightness( $defaults['progress_border_color'], -10 ) ) ?>;
	--image-size:150px;
}

.js .frm_logic_form:not(.frm_no_hide) {
    display:none;
}

.with_frm_style .frm_conf_field.frm_half label.frm_conf_label {
    overflow: hidden;
    white-space: nowrap;
}

.with_frm_style .frm_time_wrap{
	white-space:nowrap;
}

.with_frm_style select.frm_time_select{
	white-space:pre;
	display:inline;
}

<?php if ( function_exists( 'twenty_twenty_one_setup' ) ) : ?>
	body:not(.wp-admin) .frm_time_select {
		padding-left: var(--form--spacing-unit) !important;
		padding-right: calc(3 * var(--form--spacing-unit)) !important;
	}
<?php endif; ?>

/* Sections */

.with_frm_style .frm-show-form  .frm_section_heading h3{
	padding:<?php echo esc_html( $defaults['section_pad'] . $important ); ?>;
	margin:0<?php echo esc_html( $important ); ?>;
	font-size:<?php echo esc_html( $defaults['section_font_size'] ); ?>;
	font-size:var(--section-font-size)<?php echo esc_html( $important ); ?>;
	<?php if ( ! empty( $defaults['font'] ) ) { ?>
	font-family:<?php echo FrmAppHelper::kses( $defaults['font'] ); // WPCS: XSS ok. ?>;
	font-family:var(--font);
	<?php } ?>
	font-weight:<?php echo esc_html( $defaults['section_weight'] ); ?>;
	font-weight:var(--section-weight)<?php echo esc_html( $important ); ?>;
	color:<?php echo esc_html( $defaults['section_color'] ); ?>;
	color:var(--section-color)<?php echo esc_html( $important ); ?>;
	border:none<?php echo esc_html( $important ); ?>;
	background-color:<?php echo esc_html( $defaults['section_bg_color'] ); ?>;
	background-color:var(--section-bg-color)<?php echo esc_html( $important ); ?>;
}

.frm_trigger .frmsvg {
	width: 20px;
	height: 20px;
}

.frm_trigger > svg.frmsvg:nth-child(1) {
	display:inline-block;
}
.frm_trigger > svg.frmsvg:nth-child(2) {
	display:none;
}

.frm_trigger.active > svg.frmsvg:nth-child(2) {
	display:inline-block;
}
.frm_trigger.active > svg.frmsvg:nth-child(1) {
	display:none;
}

.with_frm_style .frm_repeat_sec{
	margin-bottom:<?php echo esc_html( $defaults['field_margin'] ); ?>;
	margin-bottom:var(--field-margin)<?php echo esc_html( $important ); ?>;
	margin-top:<?php echo esc_html( $defaults['field_margin'] ); ?>;
	margin-top:var(--field-margin)<?php echo esc_html( $important ); ?>;
	padding-bottom:15px;
	border-bottom:<?php echo esc_html( $defaults['section_border_width'] . ' ' . $defaults['section_border_style'] . ' ' . $defaults['section_border_color'] ); ?>;
	border-bottom-width:var(--section-border-width)<?php echo esc_html( $important ); ?>;
	border-bottom-style:var(--section-border-style)<?php echo esc_html( $important ); ?>;
	border-color:var(--section-border-color)<?php echo esc_html( $important ); ?>;
}

.with_frm_style .frm_repeat_sec:last-child{
	border-bottom:none<?php echo esc_html( $important ); ?>;
	padding-bottom:0;
}

.with_frm_style .frm_repeat_inline{
	clear:both;
}

.frm_invisible_section .frm_form_field,
.frm_invisible_section{
	display:none !important;
	visibility:hidden !important;
	height:0;
	margin:0;
}

.frm_form_field .frm_repeat_sec .frm_add_form_row,
.frm_section_heading div.frm_repeat_sec:last-child .frm_add_form_row.frm_hide_add_button,
.frm_form_field div.frm_repeat_grid .frm_add_form_row.frm_hide_add_button,
.frm_form_field div.frm_repeat_inline .frm_add_form_row.frm_hide_add_button {
	-moz-transition: opacity .15s ease-in-out;
	-webkit-transition: opacity .15s ease-in-out;
	transition: opacity .15s ease-in-out;
	pointer-events: none;
}

.frm_form_field .frm_repeat_sec .frm_add_form_row,
.frm_section_heading div.frm_repeat_sec:last-child .frm_add_form_row.frm_hide_add_button {
	display: none;
}

.frm_form_field div.frm_repeat_grid .frm_add_form_row.frm_hide_add_button,
.frm_form_field div.frm_repeat_inline .frm_add_form_row.frm_hide_add_button {
	visibility: hidden;
}

.frm_form_field div.frm_repeat_grid .frm_add_form_row,
.frm_form_field div.frm_repeat_inline .frm_add_form_row,
.frm_section_heading div.frm_repeat_sec:last-child .frm_add_form_row {
	display: inline-block;
	visibility: visible;
	pointer-events: auto;
}

.frm_add_form_row.frm_button.frm_hidden:last-child, .frm_add_form_row.frm_icon_font.frm_hidden:last-child {
	display: inline-block;
}

.frm_form_fields .frm_section_heading.frm_hidden {
	display: none;
}
.frm_repeat_buttons a.frm_remove_form_row,
.frm_repeat_buttons a.frm_add_form_row {
	line-height: 1.8;
}
.frm_form_field.frm_repeat_buttons .frm_remove_form_row:not(.frm_button) svg.frm-svg-icon,
.frm_form_field.frm_repeat_buttons .frm_add_form_row:not(.frm_button) svg.frm-svg-icon {
	fill: <?php echo esc_html( $defaults['submit_text_color'] . $important ); ?>;
	fill: var(--submit-text-color) <?php echo esc_html( $important ); ?>;
}

.frm_repeat_inline .frm_repeat_buttons a.frm_icon_font{
	vertical-align: sub;
}

.frm_repeat_inline .frm_repeat_buttons a.frm_icon_font:before{
    vertical-align: text-top;
}

.frm_repeat_buttons .frmsvg {
	width: 20px;
	height: 20px;
}

.frm_repeat_grid .frm_button,
.frm_repeat_inline .frm_button,
.frm_repeat_sec .frm_button{
	display: inline-block;
	line-height:1;
}

.with_frm_style .frm_button .frm_icon_font:before{
    font-size:<?php echo esc_html( $defaults['submit_font_size'] ); ?>;
	font-size:var(--submit-font-size)<?php echo esc_html( $important ); ?>;
}

.frm_repeat_sec .frm_button .frm_icon_font:before,
.frm_repeat_grid .frm_button .frm_icon_font:before,
.frm_repeat_inline .frm_button .frm_icon_font:before{
    line-height:1;
}

.frm_form_field .frm_repeat_grid ~ .frm_repeat_grid .frm_form_field .frm_primary_label{
    display:none !important;
}

/* Prefix */

.with_frm_style .frm_input_group {
	position: relative;
	display: -ms-flexbox;
	display: flex;
	-ms-flex-wrap: wrap;
	flex-wrap: wrap;
	-ms-flex-align: stretch;
	align-items: stretch;
	width: 100%;
}

.with_frm_style .frm_input_group.frm_hidden {
	display: none;
}

.with_frm_style .frm_inline_box {
	display: -ms-flexbox;
	display: flex;
	text-align: center;
	align-items: center;
	font-size: 90%;
	padding: 0 10px;
	color:<?php echo esc_html( $defaults['progress_color'] ) ?>;
	color:var(--progress-color);
	border:1px solid <?php echo esc_html( $defaults['border_color'] ) ?>;
	border-color:var(--border-color);
	border-width:var(--field-border-width);
	border-style:var(--field-border-style);
	background-color: <?php echo esc_html( $defaults['progress_bg_color'] ); ?>;
	background-color:var(--progress-bg-color);
	-moz-border-radius:<?php echo esc_html( $defaults['border_radius'] ); ?>;
	-webkit-border-radius:<?php echo esc_html( $defaults['border_radius'] ); ?>;
	border-radius:<?php echo esc_html( $defaults['border_radius'] ); ?>;
	border-radius:var(--border-radius);
	width: auto;
}

.with_frm_style .frm_input_group .frm_inline_box:first-child {
	margin-right: -1px;
	border-top-right-radius: 0 !important;
	border-bottom-right-radius: 0 !important;
}

.with_frm_style .frm_input_group .chosen-container + .frm_inline_box,
.with_frm_style .frm_input_group select + .frm_inline_box,
.with_frm_style .frm_input_group input + .frm_inline_box {
	margin-left: -1px;
	border-top-left-radius: 0 !important;
	border-bottom-left-radius: 0 !important;
}

.with_frm_style .frm_input_group .chosen-container,
.with_frm_style .frm_input_group > select,
.with_frm_style .frm_input_group > input {
	position: relative;
	-ms-flex: 1 1 auto;
	flex: 1 1 auto;
	width: 1% !important;
	min-width: 0;
	margin-bottom: 0;
	display: block;
}

.with_frm_style .frm_input_group.frm_with_pre .chosen-container-multi .chosen-choices,
.with_frm_style .frm_input_group.frm_with_pre .chosen-single,
.with_frm_style .frm_input_group.frm_with_pre > select,
.with_frm_style .frm_input_group.frm_with_pre > input {
	border-top-left-radius: 0 !important;
	border-bottom-left-radius: 0 !important;
}

.with_frm_style .frm_input_group.frm_with_post .chosen-container-multi .chosen-choices,
.with_frm_style .frm_input_group.frm_with_post .chosen-single,
.with_frm_style .frm_input_group.frm_with_post > select,
.with_frm_style .frm_input_group.frm_with_post > input {
	border-top-right-radius: 0 !important;
	border-bottom-right-radius: 0 !important;
}

.with_frm_style .frm_total input,
.with_frm_style .frm_total_big input {
	background-color:transparent;
	border:none;
	width:auto;
	box-shadow: none !important;
}

.with_frm_style .frm_total .frm_inline_box,
.with_frm_style .frm_total_big .frm_inline_box {
	background-color:transparent !important;
	border-width: 0 !important;
	box-shadow:none !important;
	color:var(--text-color);
	padding:0 3px 0 1px !important;
}

.with_frm_style .frm_inline_total {
	padding:0 3px;
}

/* Datepicker */

#ui-datepicker-div {
    display:none;
    z-index:999999 !important;
}

<?php
$use_default_date = ( empty( $defaults['theme_css'] ) || 'ui-lightness' === $defaults['theme_css'] );
$datepicker_class = FrmProFieldsHelper::get_datepicker_class();
$arrow_up         = FrmProStylesController::base64_encode_image( FrmProAppHelper::plugin_path() . '/images/arrowup.svg', 'image/svg+xml' );
?>

.<?php echo esc_html( $datepicker_class ); ?> .ui-datepicker-title select.ui-datepicker-month,
.<?php echo esc_html( $datepicker_class ); ?> .ui-datepicker-title select.ui-datepicker-year {
    width: <?php echo esc_html( $use_default_date ? '33' : '45' ); ?>% <?php echo esc_html( $important ); ?>;
	background-color:#fff;
	padding: 5px 10px;
	background-position-y: center;
	background-position-x: right 5px;
	line-height: 15px;
}

.<?php echo esc_html( $datepicker_class ); ?> select.ui-datepicker-month{
	margin-right: 3px;
}

.<?php echo esc_html( $datepicker_class ); ?> .ui-datepicker-month, .<?php echo esc_html( $datepicker_class ); ?> .ui-datepicker-year {
	max-width:100%;
	max-height:2em;
	padding:6px 10px;
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;
	display: inline;
	color: <?php echo esc_html( $defaults['text_color'] ); ?>;
}

.<?php echo esc_html( $datepicker_class ); ?> span.ui-datepicker-month, .<?php echo esc_html( $datepicker_class ); ?> span.ui-datepicker-year {
	color: <?php echo esc_html( $defaults['date_head_color'] ); ?>;
	line-height: 25px;
	font-weight: 600;
}

.<?php echo esc_html( $datepicker_class ); ?> .ui-datepicker-calendar {
	margin: 0 !important;
}

<?php if ( $use_default_date ) { ?>
.<?php echo esc_html( $datepicker_class ); ?> .ui-widget-header,
.<?php echo esc_html( $datepicker_class ); ?> .ui-datepicker-header {
    background: <?php echo esc_html( $defaults['date_head_bg_color'] ); ?> !important;
	color: <?php echo esc_html( $defaults['date_head_color'] ); ?> !important;
}

.<?php echo esc_html( $datepicker_class ); ?> td.ui-datepicker-today{
	background: rgba(<?php echo esc_html( FrmStylesHelper::hex2rgb( $defaults['date_band_color'] ) ); ?>,0.15) !important;
}

.<?php echo esc_html( $datepicker_class ); ?> td.ui-datepicker-current-day,
.<?php echo esc_html( $datepicker_class ); ?> td .ui-state-hover,
.<?php echo esc_html( $datepicker_class ); ?> thead {
    background: <?php echo esc_html( $defaults['date_band_color'] ); ?> !important;
	color: <?php echo esc_html( $defaults['date_head_color'] ); ?> !important;
}

.<?php echo esc_html( $datepicker_class ); ?> td.ui-datepicker-current-day .ui-state-default{
	color: <?php echo esc_html( $defaults['date_head_color'] ); ?> !important;
}
<?php } ?>

.<?php echo esc_html( $datepicker_class ); ?> .ui-datepicker-prev:before{
	background-image: url( <?php echo $arrow_up; ?> );
	content: '' !important;
}

.<?php echo esc_html( $datepicker_class ); ?> .ui-datepicker-prev:before, .<?php echo esc_html( $datepicker_class ); ?> .ui-datepicker-next:before{
	position: absolute;
	top: 10px;
	left: 6px;
}

.<?php echo esc_html( $datepicker_class ); ?> .ui-datepicker-next:before{
	background-image: url( <?php echo $arrow_up; ?> );
	content: '' !important;
}

.<?php echo esc_html( $datepicker_class ); ?> .ui-datepicker-prev{
	transform: rotate(-90deg) !important;
}

.<?php echo esc_html( $datepicker_class ); ?> .ui-datepicker-next{
	transform: rotate(90deg) !important;
}

/* Graphs */
.google-visualization-tooltip-item-list,
.google-visualization-tooltip-item-list .google-visualization-tooltip-item:first-child {
	margin: 1em 0 !important;
}

.google-visualization-tooltip-item {
	list-style-type: none !important;
	margin: 0.65em 0 !important;
}

/* Radio Scale */

.with_frm_style .frm_scale{
	margin-right:15px;
	text-align:center;
	float:left;
}

.with_frm_style .frm_scale input[type=radio]{
	display:block !important;
	margin:0;
}

.with_frm_style .frm_scale input[type=radio]:checked:before {
	transform: scale(1);
}

/* Star ratings */

.frm-star-group input {
	opacity: 0;
	position: absolute !important;
	z-index: -1;
}

.frm-star-group .frmsvg,
.frm-star-group input + label.star-rating {
	float:none;
	width:25px;
	height:25px;
	font-size:25px;
	line-height:1;
	cursor:pointer;
	background:transparent;
	overflow:hidden !important;
	clear:none;
	font-style:normal;
	display: inline;
}

.frm-star-group .frmsvg {
	fill: #F0AD4E;
}

.frm-star-group input + label.star-rating:before {
	content: '';
}

.frm-star-group input[type=radio]:checked + label:before,
.frm-star-group:not(.frm-star-hovered) input[type=radio]:checked + label:before{
	color:#F0AD4E;
}

.frm-star-group:not(.frm-star-hovered) input[type=radio]:checked + label,
.frm-star-group input + label:hover,
.frm-star-group:hover input + label:hover ,
.frm-star-group .star-rating-on,
.frm-star-group .star-rating-hover {
	color:#F0AD4E;
}

.frm-star-group .star-rating-readonly{
	cursor:default !important;
}

.frm-star-group > svg + svg {
	margin-left: 5px;
}

.frm-star-group .star-rating .frmsvg:last-of-type,
.frm-star-group .star-rating-on .frmsvg:first-of-type,
.frm-star-group .star-rating-hover .frmsvg:first-of-type {
	display: none;
}

.frm-star-group .star-rating-on .frmsvg:last-of-type,
.frm-star-group .star-rating-hover .frmsvg:last-of-type {
	display: inline;
}

/* Other input */
.with_frm_style .frm_other_input.frm_other_full{
	margin-top:10px;
}

.frm_left_container .frm_other_input{
	grid-column:2;
}

.frm_inline_container.frm_other_container .frm_other_input,
.frm_left_container.frm_other_container .frm_other_input{
	margin-left:5px;
}

.frm_right_container.frm_other_container .frm_other_input{
	margin-right:5px;
}

.frm_inline_container.frm_other_container select ~ .frm_other_input,
.frm_right_container.frm_other_container select ~ .frm_other_input,
.frm_left_container.frm_other_container select ~ .frm_other_input{
	margin:0;
}

/* File Upload */

.with_frm_style input[type=file]::-webkit-file-upload-button{
	color:<?php echo esc_html( $defaults['text_color'] ); ?>;
	color:var(--text-color)<?php echo esc_html( $important ); ?>;
	background-color:<?php echo esc_html( $defaults['bg_color'] ); ?>;
	background-color:var(--bg_color)<?php echo esc_html( $important ); ?>;
	padding:<?php echo esc_html( $defaults['field_pad'] ); ?>;
	padding:var(--field-pad)<?php echo esc_html( $important ); ?>;
	border-radius:<?php echo esc_html( $defaults['border_radius'] ); ?>;
	border-radius:var(--border-radius)<?php echo esc_html( $important ); ?>;
	border-color: <?php echo esc_html( $defaults['border_color'] ); ?>;
	border-color:var(--border-color)<?php echo esc_html( $important ); ?>;
	border-width:<?php echo esc_html( $defaults['field_border_width'] . $important ); ?>;
	border-width:var(--field-border-width)<?php echo esc_html( $important ); ?>;
	border-style:<?php echo esc_html( $defaults['field_border_style'] ); ?>;
	border-style:var(--field-border-style)<?php echo esc_html( $important ); ?>;
}

/* Pagination */
.frm_pagination_cont ul.frm_pagination{
    display:inline-block;
    list-style:none;
    margin-left:0 !important;
}

.frm_pagination_cont ul.frm_pagination > li{
    display:inline;
    list-style:none;
    margin:2px;
    background-image:none;
}

ul.frm_pagination > li.active a{
	text-decoration:none;
}

.frm_pagination_cont ul.frm_pagination > li:first-child{
    margin-left:0;
}

.archive-pagination.frm_pagination_cont ul.frm_pagination > li{
    margin:0;
}

/* Calendar Styling */
.frmcal{
    padding-top:30px;
}

.frmcal-title{
    font-size:116%;
}

.frmcal table.frmcal-calendar{
    border-collapse:collapse;
    margin-top:20px;
    color:<?php echo esc_html( $defaults['text_color'] ) ?>;
}

.frmcal table.frmcal-calendar,
.frmcal table.frmcal-calendar tbody tr td{
    border:1px solid <?php echo esc_html( $defaults['border_color'] ) ?>;
}

.frmcal table.frmcal-calendar,
.frmcal,
.frmcal-header{
    width:100%;
}

.frmcal-header{
    text-align:center;
}

.frmcal-prev{
    margin-right:10px;
}

.frmcal-prev,
.frmcal-dropdown{
    float:left;
}

.frmcal-dropdown{
    margin-left:5px;
}

.frmcal-next{
    float:right;
}

.frmcal table.frmcal-calendar thead tr th{
    text-align:center;
    padding:2px 4px;
}

.frmcal table.frmcal-calendar tbody tr td{
    height:110px;
    width:14.28%;
    vertical-align:top;
    padding:0 !important;
    color:<?php echo esc_attr( $defaults['text_color'] ) ?>;
    font-size:12px;
}

table.frmcal-calendar .frmcal_date{
    background-color:<?php echo esc_html( empty( $defaults['bg_color'] ) ? 'transparent' : $defaults['bg_color'] ); ?>;
    padding:0 5px;
    text-align:right;
    -moz-box-shadow:0 2px 5px <?php echo esc_html( $defaults['border_color'] ) ?>;
    -webkit-box-shadow:0 2px 5px <?php echo esc_html( $defaults['border_color'] ) ?>;
    box-shadow:0 2px 5px <?php echo esc_html( $defaults['border_color'] ) ?>;
}

table.frmcal-calendar .frmcal-today .frmcal_date{
    background-color:<?php echo esc_html( $defaults['bg_color_active'] ) ?>;
    padding:0 5px;
    text-align:right;
    -moz-box-shadow:0 2px 5px <?php echo esc_html( $defaults['border_color_active'] ) ?>;
    -webkit-box-shadow:0 2px 5px <?php echo esc_html( $defaults['border_color_active'] ) ?>;
    box-shadow:0 2px 5px <?php echo esc_html( $defaults['border_color_active'] ) ?>;
}

.frmcal_day_name,
.frmcal_num{
    display:inline;
}

.frmcal-content{
    padding:2px 4px;
}
/* End Calendar Styling */

/* Start Toggle Styling */
.frm_switch_opt {
	padding:0 8px 0 0;
	white-space:normal;
	display:inline;
	vertical-align: middle;
	font-size:<?php echo esc_html( $defaults['toggle_font_size'] ); ?>;
	font-size:var(--toggle-font-size)<?php echo esc_html( $important ); ?>;
	font-weight:<?php echo esc_html( $defaults['check_weight'] ); ?>;
	font-weight:var(--check-weight)<?php echo esc_html( $important ); ?>;
}

.frm_on_label{
	padding:0 0 0 8px;
}

.frm_on_label,
.frm_off_label{
	color:<?php echo esc_html( $defaults['check_label_color'] ); ?>;
	color:var(--check-label-color)<?php echo esc_html( $important ); ?>;
}

.frm_switch {
	position: relative;
	display: inline-block;
	width: 40px;
	height: 25px;
	vertical-align: middle;
}

.frm_switch_block input {
	display:none !important;
}

.frm_slider {
	position: absolute;
	cursor: pointer;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background-color:<?php echo esc_html( $defaults['toggle_off_color'] ); ?>;
	background-color:var(--toggle-off-color)<?php echo esc_html( $important ); ?>;
	transition: .4s;
	border-radius: 30px;
}

.frm_slider:before {
	border-radius: 50%;
	position: absolute;
	content: "";
	height: 23px;
	width: 23px;
	left: 1px;
	bottom: 1px;
	background-color: white;
	transition: .4s;
	box-shadow:0 2px 6px rgba(41, 58, 82, 0.31);
}

input:checked + .frm_switch .frm_slider {
	background-color:<?php echo esc_html( $defaults['toggle_on_color'] ); ?>;
	background-color:var(--toggle-on-color)<?php echo esc_html( $important ); ?>;
}

input:focus + .frm_switch .frm_slider {
	box-shadow: 0 0 1px #3177c7;
}

input:checked + .frm_switch .frm_slider:before {
	transform: translateX(15px);
}

.frm_rtl .frm_switch_opt {
	padding: 0 8px;
}

.frm_rtl .frm_slider:before {
	left: 16px;
}

.frm_rtl input:checked + .frm_switch .frm_slider:before {
	transform: none!important;
	left: 1px;
}

/* End Toggle */

/* Range slider */

<?php
$bg_color = '#ccc' . $important;
$thumb_color = '#3177c7' . $important;
$text_color = '#ffffff' . $important;
?>
.with_frm_style .frm_range_unit,
.with_frm_style .frm_range_value{
	display:inline-block;
	padding-left: 2px;
	padding-right: 2px;
}

.with_frm_style .frm_range_value + .frm_range_unit,
.with_frm_style .frm_range_container > .frm_range_unit,
.with_frm_style .frm_range_value{
	font-size:<?php echo esc_html( $defaults['slider_font_size'] ); ?>;
	font-size:var(--slider-font-size);
	color:<?php echo esc_html( $defaults['text_color'] ); ?>;
	color:var(--text-color)<?php echo esc_html( $important ); ?>;
}

.with_frm_style .frm_range_container {
	padding-top: 5px;
}

.with_frm_style input[type=range] {
	-webkit-appearance: none;
	box-shadow:none !important;
	border:none !important;
	cursor: pointer;
	padding:0 <?php echo esc_html( $important ) ?>;
	background:transparent !important;
	display: block;
	width: 100%;
	margin: 15px 0 8px;
	font-size:14px;
	height:auto;
}

.with_frm_style input[type=range]:active,
.with_frm_style input[type=range]:focus {
	outline: none;
	box-shadow:none !important;
	background:transparent !important;
	padding:0 <?php echo esc_html( $important ); ?>;
}

.with_frm_style .frm_range_max {
	float: right;
}

.with_frm_style .frm_range_container input + .frm_range_value {
	text-align: center;
	display: block;
}

.with_frm_style input[type=range]::-webkit-slider-runnable-track {
	-webkit-appearance: none;
	<?php
	echo $border = 'border-radius: ' . esc_html( $defaults['border_radius'] ) . ';
	border-radius:var(--border-radius)' . esc_html( $important ) . ';
	border: none;
	height: 5px;
	background-color: ' . esc_html( $defaults['slider_bar_color'] ) . ';
	background-color:var(--slider-bar-color)' . esc_html( $important ) . ';';
	?>
}
.with_frm_style input[type=range]::-moz-range-track {
	<?php echo $border; ?>
	border-color: transparent;
	border-width: 39px 0;
	color: transparent;
	background-color:var(--toggle-off-color);
}

.with_frm_style input[type=range]::-moz-range-progress {
	<?php echo $border; ?>
	background-color: <?php echo esc_html( $thumb_color ); ?>;
}

.with_frm_style input[type=range]::-ms-fill-lower {
	<?php echo $border; ?>
	background-color:var(--toggle-off-color);
}
.with_frm_style input[type=range]::-ms-fill-upper {
	<?php echo $border; ?>
}

.with_frm_style input[type=range]::-webkit-slider-thumb {
	-webkit-appearance: none;
	-webkit-border-radius: 20px;
	<?php
	echo $thumb_size = 'height: 24px;
	width: 24px;';
	echo $thumb = 'border-radius: 24px;
	border: 1px solid ' . esc_html( $thumb_color ) . ';
	color:' . esc_html( $text_color ) . ';
	background: #fff;
	cursor: pointer;';
	?>
	margin-top: -10px;
	box-shadow:0 2px 6px rgba(41, 58, 82, 0.31);
}

.with_frm_style input[type=range]::-moz-range-thumb {
	<?php echo $thumb_size . $thumb; ?>
	-moz-border-radius: 20px;
}

.with_frm_style input[type=range]::-ms-thumb {
	<?php echo $thumb_size . $thumb; ?>
}

.with_frm_style input[type=range]::-moz-focus-outer {
	border: 0;
}

/* Dropzone */

.with_frm_style .frm_dropzone{
	border-color: <?php echo esc_html( $defaults['border_color'] ); ?>;
	border-color: var(--border-color) <?php echo esc_html( $important ); ?>;
	border-radius:<?php echo esc_html( $defaults['border_radius'] ); ?>;
	border-radius: var(--border-radius) <?php echo esc_html( $important ); ?>;
	color: <?php echo esc_html( $defaults['text_color'] ); ?>;
	color: var(--text-color) <?php echo esc_html( $important ); ?>;
	background-color:<?php echo esc_html( $defaults['bg_color'] ); ?>;
	background-color:var(--bg-color) <?php echo esc_html( $important ); ?>;
}

.with_frm_style .frm_dropzone .frm_upload_icon,
.with_frm_style .frm_dropzone .dz-remove{
	color: <?php echo esc_html( $defaults['text_color'] ); ?>;
	color: var(--text-color) <?php echo esc_html( $important ); ?>;
}

.with_frm_style .frm_compact .frm_dropzone .frm_upload_icon {
	color: <?php echo esc_html( $defaults['submit_text_color'] . $important ); ?>;
	color: var(--submit-text-color) <?php echo esc_html( $important ); ?>;
}

.with_frm_style .frm_dropzone .frmsvg {
	width: 40px;
	height: 40px;
}

.with_frm_style .frm_dropzone .frm_remove_link .frmsvg {
	width: 18px;
	height: 18px;
	cursor: pointer;
}

.with_frm_style .frm_blank_field .frm_dropzone{
	border-color:<?php echo esc_html( $defaults['border_color_error'] ); ?>;
	border-color:var(--border-color-error) <?php echo esc_html( $important ); ?>;
	color:<?php echo esc_html( $defaults['text_color_error'] ); ?>;
	color:var(--text-color-error) <?php echo esc_html( $important ); ?>;
	background-color:<?php echo esc_html( $defaults['bg_color_error'] . $important ); ?>;
	background-color:var(--bg-color-error) <?php echo esc_html( $important ); ?>;
}


.with_frm_style .frm_dropzone .dz-preview .dz-progress {
	<?php if ( isset( $defaults['progress_bg_color'] ) ) { ?>
	background: <?php echo esc_html( $defaults['progress_bg_color'] ); ?>;
	<?php } ?>
	background: var(--progress-bg-color) <?php echo esc_html( $important ); ?>;
}

.with_frm_style .frm_dropzone .dz-preview .dz-progress .dz-upload,
.with_frm_style .frm_dropzone .dz-preview.dz-complete .dz-progress {
	<?php if ( isset( $defaults['progress_active_bg_color'] ) ) { ?>
	background: <?php echo esc_html( $defaults['progress_active_bg_color'] ); ?>;
	<?php } ?>
	background: var(--progress-active-bg-color) <?php echo esc_html( $important ); ?>;
}

/**
 * Radio Button and Checkbox Images
 */

.frm_image_size_medium {
	--image-size:250px;
}

.frm_image_size_large {
	--image-size:320px;
}

.frm_image_size_xlarge {
	--image-size:400px;
}

.frm_image_options .frm_opt_container {
	display: inline-flex;
	flex-flow: wrap;
	flex-direction:row;
	margin: 0 -10px;
}

.frm_image_options .frm_radio input[type=radio],
.frm_image_options .frm_checkbox input[type=checkbox]{
	opacity: 0;
	position: absolute;
	z-index: -1;
}

.frm_image_options .frm_image_option_container {
	border: 1px solid <?php echo esc_html( $defaults['border_color'] ); ?>;
	border-color: var(--border-color);
	border-width: var(--field-border-width);
	border-radius: 3px;
	border-radius: var(--border-radius);
	display: flex;
	flex-wrap: wrap;
	box-sizing: border-box;
	-moz-box-sizing: border-box;
	position: relative;
	height: 100%;
}

.frm_image_options .frm_image_option_container.frm_label_with_image .frm_empty_url,
.frm_image_options .frm_image_option_container.frm_label_with_image img {
	border-bottom-left-radius:0;
	border-bottom-right-radius:0;
}

.with_frm_style .vertical_radio.frm_image_options .frm_image_option > label {
    text-indent: 0;
}

.frm_show_images.frm_image_option_container {
	display: inline-flex;
	flex-wrap: nowrap;
	flex-direction: column;
	text-align: center;
	align-items: center;
	width: 150px;
	margin-right: 10px;
	margin-bottom: 10px;
}

.frm-summary-page-wrapper .frm_image_option_container img{
	width: 100%;
	height: 150px;
	object-fit: cover;
}

.frm_image_option_container .frm_selected_checkmark{
	position: absolute;
	z-index: 99;
	left: -10px;
	top: -12px;
	display: none;
}

.frm_image_option_container .frm_image_placeholder_icon {
	position: absolute;
}

.frm_image_option_container .frm_selected_checkmark svg{
	fill: <?php echo esc_html( $defaults['border_color_active'] . $important ); ?>;
	fill:var(--border-color-active)<?php echo esc_html( $important ); ?>;
	width: 24px;
	height: 24px;
}

.frm_image_option_container .frm_image_placeholder_icon svg{
	width: 63px;
	opacity: .2;
}

.frm_text_label_for_image {
	text-align: center;
	width: 100%;
	padding: 10px;
	word-break: keep-all;
}

.frm_image_options .frm_image_option_container:hover,
input[type="radio"]:checked + .frm_image_option_container,
input[type="checkbox"]:checked + .frm_image_option_container {
	border-color: <?php echo esc_html( $defaults['border_color_active'] ); ?>;
	border-color: var(--border-color-active);
	box-shadow: 0 0 0 1px <?php echo esc_html( $defaults['border_color_active'] ); ?>;
	box-shadow: 0 0 0 1px var(--border-color-active);
}

input[type="radio"]:disabled + .frm_image_option_container,
input[type="checkbox"]:disabled + .frm_image_option_container{
	opacity: .7;
	border-color:var(--border-color-disabled);
}

input[type="radio"]:checked + .frm_image_option_container .frm_selected_checkmark,
input[type="checkbox"]:checked + .frm_image_option_container .frm_selected_checkmark {
	display: block;
}

.frm_blank_field.frm_image_options .frm_image_option_container {
	border-color: var(--border-color-error);
}

.frm_image_options .frm_image_option_container .frm_empty_url,
.frm_image_options .frm_image_option_container img {
	width: 100%;
	height: 150px;
	height: var(--image-size);
	object-fit: cover;
	border-radius:var(--border-radius);
}

.frm_image_option_container .frm_empty_url {
	background:<?php echo esc_html( FrmStylesHelper::adjust_brightness( $defaults['border_color'], 45 ) ); ?>;
	display:flex;
	justify-content: center;
	align-items: center;
}

.horizontal_radio .frm_checkbox.frm_image_option,
.horizontal_radio .frm_radio.frm_image_option {
	padding-left: 0;
}

.frm_checkbox.frm_image_option,
.frm_radio.frm_image_option {
	width:var(--image-size) !important; /* Overrides grid classes */
}

.frm_form_field .frm_checkbox.frm_image_option,
.frm_form_field .frm_checkbox.frm_image_option + .frm_checkbox,
.frm_form_field .frm_radio.frm_image_option,
.frm_form_field .frm_radio.frm_image_option + .frm_radio {
	margin:10px; /* Override for inline options */
}

.frm_checkbox.frm_image_option label,
.frm_radio.frm_image_option label{
	padding-left: 0;
	margin-left: 0;
	min-height: 0;
	visibility: visible<?php echo esc_html( $important ); ?>; /* Overrides grid classes */
}

/**
 * Background image
 */
.frm_with_bg_image .frm_form_fields > fieldset {
	position: relative;
}

.frm_with_bg_image .frm_form_fields > fieldset:before {
	content: ' ';
	display: block;
	position: absolute;
	top: 0;
	height: 100%;
	background-position: 50% 0;
	left: 0;
	width: 100%;
	background-image: var(--bg-image-url);
	background-repeat: no-repeat;
	background-size: cover;
}

.frm_with_bg_image .frm_form_fields > fieldset > *:not(.frm_screen_reader) {
	z-index: 1;
	position: relative;
}

/**
 * Password strength meter CSS
 */

@media screen and (max-width: 768px) {
    .frm-pass-req, .frm-pass-verified {
        width: 50% !important;
        white-space: nowrap;
    }
}

.frm-pass-req, .frm-pass-verified {
    display: flex;
    align-items: center;
    float: left;
    width: 20%;
    line-height: 20px;
    font-size: 12px;
    padding-top: 4px;
    min-width: 175px;
}

.frm-pass-req .frmsvg, .frm-pass-verified .frmsvg {
	width: 12px;
	height: 12px;
	padding-right: 4px;
}

.passed_svg, .failed_svg {
    display: none !important;
}
.frm-pass-verified .passed_svg,
.frm-pass-req .failed_svg {
    display: inline-block !important;
}

div.frm-password-strength {
    width: 100%;
    float: left;
}

.frm_show_password_wrapper {
	position: relative;
	display: block;
}

.frm_show_password_wrapper input {
	display: block;
}

.frm_show_password_wrapper button {
	position: absolute;
	top: 50%;
	height: 32px;
	height: var(--field-height);
	right: 0;
	border: 0;
	background: transparent !important;
	cursor: pointer;
	transform: translateY(-50%);
	padding: 0 10px;
	color: #BFC3C8;
	color: var(--border-color);
	display: flex;
	align-items: center;
}

.frm_show_password_wrapper button:hover {
	color: #BFC3C8;
	color: var(--border-color);
	background: transparent !important;
}

.frm_show_password_wrapper button svg {
	width: 24px;
	height: 24px;
}

input[type="text"] + .frm_show_password_btn svg:first-child,
input[type="password"] + .frm_show_password_btn svg:last-child,
.frm_form_builder input[type="text"] + .frm_show_password_btn svg:last-child {
	display: none;
}

.frm_form_builder input[type="text"] + .frm_show_password_btn svg:first-child {
	display: block;
}

div.frm_repeat_grid:after, div.frm_repeat_inline:after, div.frm_repeat_sec:after {
    content: '';
    display: table;
    clear: both;
}

.with_frm_style .frm-summary-page-wrapper {
	padding: 50px;
	margin: 25px 0 50px;
	border: 1px solid <?php echo esc_html( $defaults['border_color'] ); ?>;
	border-color: var(--border-color);
	border-radius: <?php echo esc_html( $defaults['border_radius'] ); ?>;
	border-radius: var(--border-radius);
}

.with_frm_style .frm-summary-page-wrapper .frm-edit-page-btn {
	float: right;
	margin: 0;
	padding: 3px 10px;
	font-size: 13px;
}

.frm-summary-page-wrapper .frm-line-table th {
	width: 40%;
}

button .frm-icon {
	display: inline-block;
	color: inherit;
	width: 12px;
	height: 12px;
	fill: currentColor;
}

.frm-line-table {
	width: 100%;
	border-collapse: collapse;
	margin-top: 0.5em;
	font-size: <?php echo esc_html( $defaults['font_size'] ); ?>;
}

.frm-line-table tr {
	background-color: transparent;
	border-bottom: 1px solid rgba(<?php echo esc_html( FrmStylesHelper::hex2rgb( $defaults['border_color'] ) ); ?>,0.6);
}

.frm-line-table td,
.frm-line-table th {
	border: 0;
	padding: 20px 15px;
	background-color: transparent;
	vertical-align: top;
	color: <?php echo esc_html( $defaults['label_color'] ); ?>;
}

.frm-line-table th {
	opacity: .7;
	font-size: 1.1em;
	font-weight: 500;
}

.frm-line-table h3 {
	font-size: 21px;
	font-weight: 500;
	margin: 0;
}

.frm_form_field .frm_total_formatted {
	display: inline-block;
	margin: 5px 0 0;
}

.frm_form_field.frm_total_big .frm_total_formatted {
	margin: 0;
}

.frm_form_field.frm_total_big .frm_total_formatted,
.frm_form_field.frm_total_big input[type=text],
.frm_form_field.frm_total_big input[type=number],
.frm_form_field.frm_total_big input,
.frm_form_field.frm_total_big textarea{
	font-size: 32px;
	font-weight: bold;
	line-height: 44px;
}

/* Views */

.frm_round{
	border-radius:50%;
}

.frm_round.frm_color_block{
	padding:3px;
}

.frm_square {
	border-radius:var(--border-radius);
	object-fit:cover;
	width:150px;
	height:150px;
}

.frmsvg{
	max-width:100%;
	fill:currentColor;
	vertical-align:sub;
	display:inline-block;
}

.frm_smaller{
	font-size:90%;
}

.frm_small{
	font-size:14px;
	font-weight:normal;
}

.frm_bigger{
	font-size:110%;
}

ul.frm_plain_list,
ul.frm_plain_list li{
	list-style:none<?php echo esc_html( $important ); ?>;
	list-style-type:none<?php echo esc_html( $important ); ?>;
	margin-left:0<?php echo esc_html( $important ); ?>;
	margin-right:0<?php echo esc_html( $important ); ?>;
	padding-left:0;
	padding-right:0;
}

ul.frm_inline_list li{
	display:inline;
	padding:2px;
}

.frm_flex,
.frm_full_row{
	display:flex;
	flex-direction:row;
	flex-wrap:wrap;
}

.frm_full_row > li,
.frm_full_row > div{
	flex:1;
	text-align:center;
}

.frm_tiles > li,
.frm_tiles > div{
	border:1px solid <?php echo esc_html( $defaults['border_color'] ); ?>;
	border-radius:<?php echo esc_html( $defaults['border_radius'] ); ?>;
	margin-top:20px;
	padding:25px;
<?php if ( isset( $defaults['box_shadow'] ) && $defaults['box_shadow'] !== 'none' ) { ?>
	box-shadow:0 0 5px 1px rgba(0,0,0,0.075);
<?php } ?>
}
/* Repeater Fields */
.with_frm_style .frm_repeat_sec .frm_form_field.frm_repeat_buttons .frm-svg-icon {
	fill:<?php echo esc_html( $defaults['repeat_icon_color'] . $important ); ?>;
	fill:var(--repeat-icon-color)<?php echo esc_html( $important ); ?>;
}
.frm_style_frm_style_template .frm_form_field.frm_repeat_buttons .frm-svg-icon {
	fill: var(--submit-text-color)!important;
}
.frm_remove_form_row:hover,
.frm_add_form_row:hover {
	background: var(--submit-hover-bg-color);
	border-color: var(--submit-hover-border-color);
	color: var(--submit-hover-color);
}
.frm_repeat_sec .frm_form_field.frm_repeat_buttons .frm_add_form_row:hover svg.frm-svg-icon,
.frm_repeat_sec .frm_form_field.frm_repeat_buttons .frm_remove_form_row:hover svg.frm-svg-icon,
.frm_repeat_sec .frm_form_field.frm_repeat_buttons .frm_add_form_row:hover svg.frmsvg,
.frm_repeat_sec .frm_form_field.frm_repeat_buttons .frm_remove_form_row:hover svg.frmsvg {
	fill: var(--submit-hover-color);
}
/* End Repeater Fields */

.frm_tiles h3{
	margin-top:5px;
}

/* Submit Button Position */

.frm_forms.frm_full_submit .frm_submit button {
	width: 100%;
}

/* Slide in */
.frm_slidein .frm_form_fields  > fieldset{
	animation-name: frmSlideInRight;
	animation-duration: 1s;
}

.frm_slidein.frm_going_back .frm_form_fields  > fieldset {
	animation-name: frmSlideInLeft;
}

.frm_slidein.frm_slideout .frm_form_fields  > fieldset {
	animation-name: frmSlideOutLeft !important;
}

.frm_slidein.frm_slideout.frm_going_back .frm_form_fields  > fieldset {
	animation-name: frmSlideOutRight !important;
}

.frm_slidein .frm-g-recaptcha .grecaptcha-badge{
	animation-name: fadeIn;
	animation-duration: 2s;
	animation-fill-mode: both;
}

@keyframes frmSlideInLeft {
	0% {
		opacity: 0;
		-webkit-transform: translate3d(-3000px, 0, 0);
		transform: translate3d(-3000px, 0, 0);
	}
	100% {
		opacity: 1;
		-webkit-transform: none;
		transform: none;
	}
}

@keyframes frmSlideInRight {
	0% {
		opacity: 0;
		-webkit-transform: translate3d(3000px, 0, 0);
		transform: translate3d(3000px, 0, 0);
	}
	100% {
		opacity: 1;
		-webkit-transform: none;
		transform: none;
	}
}

@keyframes frmSlideOutLeft {
	0% {
		opacity: 1;
		-webkit-transform: none;
		transform: none;
	}
	100% {
		opacity: 0;
		-webkit-transform: translate3d(-2000px, 0, 0);
		transform: translate3d(-2000px, 0, 0);
	}
}

@keyframes frmSlideOutRight {
	0% {
		opacity: 1;
		-webkit-transform: none;
		transform: none;
	}
	100% {
		opacity: 0;
		-webkit-transform: translate3d(2000px, 0, 0);
		transform: translate3d(2000px, 0, 0);
	}
}

/* Slide Up */
.frm_slideup .frm_form_fields  > fieldset {
	animation-name: frmSlideDown;
	-webkit-animation-duration: 1s;
	animation-duration: 1s;
	-webkit-animation-fill-mode: both;
	animation-fill-mode: both;
}

.frm_slideup.frm_going_back .frm_form_fields  > fieldset {
	animation-name: frmSlideUp;
}

.frm_slideup.frm_slideout .frm_form_fields  > fieldset {
	animation-name: frmSlideOutUp !important;
}

.frm_slideup.frm_slideout.frm_going_back .frm_form_fields  > fieldset {
	animation-name: frmSlideOutDown !important;
}

@keyframes frmSlideUp {
	0% {
		opacity: 0;
		-webkit-transform: translate3d(0, -200px, 0);
		transform: translate3d(0, -200px, 0);
	}
	100% {
		opacity: 1;
		-webkit-transform: none;
		transform: none;
	}
}

@keyframes frmSlideDown {
	0% {
		opacity: 0;
		-webkit-transform: translate3d(0, 200px, 0);
		transform: translate3d(0, 200px, 0);
	}
	100% {
		opacity: 1;
		-webkit-transform: none;
		transform: none;
	}
}

@keyframes frmSlideOutUp {
	0% {
		opacity: 1;
		-webkit-transform: none;
		transform: none;
	}
	100% {
		opacity: 0;
		-webkit-transform: translate3d(0, -200px, 0);
		transform: translate3d(0, -200px, 0);
	}
}

@keyframes frmSlideOutDown {
	0% {
		opacity: 1;
		-webkit-transform: none;
		transform: none;
	}
	100% {
		opacity: 0;
		-webkit-transform: translate3d(0, 200px, 0);
		transform: translate3d(0, 200px, 0);
	}
}
