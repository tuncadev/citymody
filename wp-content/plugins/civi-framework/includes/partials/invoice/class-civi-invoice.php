<?php
if (!defined('ABSPATH')) {
	exit;
}
if (!class_exists('Civi_Invoice')) {
	/**
	 * Class Civi_Invoice
	 */
	class Civi_Invoice
	{
		/**
		 * Get total my invoice
		 * @return int
		 */
		public function get_total_my_invoice()
		{
			$args = array(
				'post_type' => 'invoice',
				'meta_query' => array(
					array(
						'key' => CIVI_METABOX_PREFIX . 'invoice_user_id',
						'value' => get_current_user_id(),
						'compare' => '='
					)
				)
			);
			$invoices = new WP_Query($args);
			wp_reset_postdata();
			return $invoices->found_posts;
		}

		/**
		 * Insert invoice
		 * @param $payment_type
		 * @param $item_id
		 * @param $user_id
		 * @param $payment_for
		 * @param $payment_method
		 * @param int $paid
		 * @param string $payment_id
		 * @param string $payer_id
		 * @return int|WP_Error
		 */
		public function insert_invoice($payment_type, $item_id, $user_id, $payment_for, $payment_method, $paid = 0, $payment_id = '', $payer_id = '')
		{

			$price_featured_submission = civi_get_option('price_featured_listing', '0');
			$price_featured_submission = floatval($price_featured_submission);
			$total_money = 0;
			if ($payment_type != 'Package') {
				if ($payment_for == 3) {
					$total_money = $price_featured_submission;
				}
			} else {
				$package_free = get_post_meta($item_id, CIVI_METABOX_PREFIX . 'package_free', true);
				if ($package_free == 1) {
					$total_money = 0;
				} else {
					$total_money = get_post_meta($item_id, CIVI_METABOX_PREFIX . 'package_price', true);
				}
			}
			$time = time();
			$invoice_date = date('Y-m-d H:i:s', $time);

			$civi_meta = array();
			$civi_meta['invoice_item_id'] = $item_id;
			$civi_meta['invoice_item_price'] = $total_money;
			$civi_meta['invoice_purchase_date'] = $invoice_date;
			$civi_meta['invoice_user_id'] = $user_id;
			$civi_meta['invoice_payment_type'] = $payment_type;
			$civi_meta['invoice_payment_method'] = $payment_method;
			$civi_meta['trans_payment_id'] = $payment_id;
			$civi_meta['trans_payer_id'] = $payer_id;
			$posttitle = 'Invoice_' . $payment_method . '_' . $total_money . $user_id;
			$args = array(
				'post_title'    => $posttitle,
				'post_status'    => 'publish',
				'post_type'     => 'invoice'
			);

			$rw = get_page_by_title($posttitle, OBJECT, 'invoice');
			$invoice_payment_status = get_post_meta($rw->ID, CIVI_METABOX_PREFIX . 'invoice_payment_status', true);

			if (empty($rw->ID) || ($rw->ID && $invoice_payment_status == '1')) {
				$invoice_id =  wp_insert_post($args);
				update_post_meta($invoice_id, CIVI_METABOX_PREFIX . 'invoice_user_id', $user_id);
				update_post_meta($invoice_id, CIVI_METABOX_PREFIX . 'invoice_item_id', $item_id);
				update_post_meta($invoice_id, CIVI_METABOX_PREFIX . 'invoice_price', $total_money);
				update_post_meta($invoice_id, CIVI_METABOX_PREFIX . 'invoice_date', $invoice_date);
				update_post_meta($invoice_id, CIVI_METABOX_PREFIX . 'invoice_payment_type', $payment_type);
				update_post_meta($invoice_id, CIVI_METABOX_PREFIX . 'invoice_payment_method', $payment_method);
				update_post_meta($invoice_id, CIVI_METABOX_PREFIX . 'invoice_payment_status', $paid);

				update_post_meta($invoice_id, CIVI_METABOX_PREFIX . 'trans_payment_id', $payment_id);
				update_post_meta($invoice_id, CIVI_METABOX_PREFIX . 'trans_payer_id', $payer_id);

				update_post_meta($invoice_id, CIVI_METABOX_PREFIX . 'invoice_meta', $civi_meta);
				$update_post = array(
					'ID'         => $invoice_id,
				);
				wp_update_post($update_post);
			} else {
				$invoice_id = $rw->ID;
			}
			return $invoice_id;
		}

		/**
		 * get_invoice_meta
		 * @param $post_id
		 * @param bool|false $field
		 * @return array|bool|mixed
		 */
		public function get_invoice_meta($post_id, $field = false)
		{
			$defaults = array(
				'invoice_item_id' => '',
				'invoice_item_price' => '',
				'invoice_purchase_date' => '',
				'invoice_user_id' => '',
				'invoice_payment_type' => '',
				'invoice_payment_method' => '',
				'trans_payment_id' => '',
				'trans_payer_id' => '',
			);
			$meta = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'invoice_meta', true);
			$meta = wp_parse_args((array)$meta, $defaults);

			if ($field) {
				if (isset($meta[$field])) {
					return $meta[$field];
				} else {
					return false;
				}
			}
			return $meta;
		}

		/**
		 * @param $payment_type
		 * @return string
		 */
		public static function get_invoice_payment_type($payment_type)
		{
			switch ($payment_type) {
				case 'Package':
					return esc_html__('Package', 'civi-framework');
					break;
				case 'Listing':
					return esc_html__('Listing', 'civi-framework');
					break;
				case 'Upgrade_To_Featured':
					return esc_html__('Upgrade to Featured', 'civi-framework');
					break;
				case 'Listing_With_Featured':
					return esc_html__('Listing with Featured', 'civi-framework');
					break;
				default:
					return '';
			}
		}

		/**
		 * @param $payment_method
		 * @return string
		 */
		public static function get_invoice_payment_method($payment_method)
		{
			switch ($payment_method) {
				case 'Paypal':
					return esc_html__('Paypal', 'civi-framework');
					break;
				case 'Stripe':
					return esc_html__('Stripe', 'civi-framework');
					break;
				case 'Wire_Transfer':
					return esc_html__('Wire Transfer', 'civi-framework');
					break;
				case 'Free_Package':
					return esc_html__('Free Package', 'civi-framework');
					break;
				default:
					return '';
			}
		}
		/**
		 * Print Invoice
		 */
		public function invoice_print_ajax()
		{
			if (!isset($_POST['invoice_id']) || !is_numeric($_POST['invoice_id'])) {
				return;
			}
			$invoice_id = absint(wp_unslash($_POST['invoice_id']));
			$isRTL = 'false';
			if (isset($_POST['isRTL'])) {
				$isRTL = $_POST['isRTL'];
			}
			civi_get_template('invoice/invoice-print.php', array('invoice_id' => intval($invoice_id), 'isRTL' => $isRTL));
			wp_die();
		}
	}
}
