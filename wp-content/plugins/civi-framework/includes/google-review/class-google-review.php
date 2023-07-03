<?php
function civiGetReviews($placeid)
{

	$google_review_api_key  = civi_get_option('google_review_api_key', 'AIzaSyBvgFc-WtkrUrAhTE_7an-OkBNlqReY_Bc');

	$option = array(
		'googlemaps_free_apikey' => $google_review_api_key,       // Google API Key
		'google_maps_review_cid' => $placeid, // Google Placec ID of the Business
		'cache_data_xdays_local' => 2,       // every x day the reviews are loaded from google (save API traffic)
		'your_language_for_tran' => 'en',     // give you language for auto translate reviews
		'show_not_more_than_max' => 5,        // (0-5) only show first x reviews
		'show_only_if_with_text' => false,    // true = show only reviews that have text
		'show_only_if_greater_x' => 0,        // (0-4) only show reviews with more than x stars
		'sort_reviews_by_a_data' => 'rating', // sort by 'time' or by 'rating' (newest/best first)
		'show_cname_as_headline' => true,     // true = show customer name as headline
		'show_stars_in_headline' => true,     // true = show customer stars after name in headline
		'show_author_avatar_img' => true,     // true = show the author avatar image (rounded)
		'show_blank_star_till_5' => true,     // false = don't show always 5 stars e.g. ⭐⭐⭐☆☆
		'show_txt_of_the_review' => true,     // true = show the text of each review
		'show_author_of_reviews' => true,     // true = show the author of each review
		'show_age_of_the_review' => true,     // true = show the age of each review
		'dateformat_for_the_age' => 'Y.m.d',  // see https://www.php.net/manual/en/datetime.format.php
	);

	if (file_exists($option['google_maps_review_cid'] . '.json') and strtotime(filemtime($option['google_maps_review_cid'] . '.json')) < strtotime('-' . $option['cache_data_xdays_local'] . ' days')) {
		$result = file_get_contents($option['google_maps_review_cid'] . '.json');
	} else {

		$url = 'https://maps.googleapis.com/maps/api/place/details/json?jobs_id=' . $option['google_maps_review_cid'] . '&key=' . $option['googlemaps_free_apikey'];
		if (function_exists('curl_version')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			if (isset($option['your_language_for_tran']) and !empty($option['your_language_for_tran'])) {
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: ' . $option['your_language_for_tran']));
			}
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36');
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			curl_close($ch);
		} else {
			$arrContextOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
				),
				'http' => array(
					'method' => 'GET',
					'header' => 'Accept-language: ' . $option['your_language_for_tran'] . "\r\n" .
						"User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36\r\n"
				)
			);
			$result = file_get_contents($url, false, stream_context_create($arrContextOptions));
		}
		$fp = fopen($option['google_maps_review_cid'] . '.json', 'w');
		fwrite($fp, $result);
		fclose($fp);
	}

	$data  = json_decode($result, true);

	if ($data['error_message']) {
		echo '<p class="error-box">' . $data['error_message'] . '</p>';
		echo '<p class="error-box">' . 'CIVI msg: Place review file exists? ' . file_exists($option['google_maps_review_cid'] . '.json') . '</p>';
	} else {
		$reviews = $data['result']['reviews'];
		$html = '';
		if (!empty($reviews)) {

			if (isset($option['sort_reviews_by_a_data']) and $option['sort_reviews_by_a_data'] == 'rating') {
				array_multisort(array_map(function ($element) {
					return $element['rating'];
				}, $reviews), SORT_DESC, $reviews);
			} else if (isset($option['sort_reviews_by_a_data']) and $option['sort_reviews_by_a_data'] == 'time') {
				array_multisort(array_map(function ($element) {
					return $element['time'];
				}, $reviews), SORT_DESC, $reviews);
			}

			$html .= '<div class="place-google-reviews place-area">';
			$html .= '<div class="entry-heading">';
			$html .= '<h3 class="entry-title">' . esc_html__('Google Reviews', 'civi-framework') . '</h3>';
			$html .= '</div>';
			$html .= '<div class="entry-detail">';
			if (isset($option['show_cname_as_headline']) and $option['show_cname_as_headline'] == true) {
				$html .= '<div class="google-reviews-head">';
				$html .= '<div class="google-reviews-avatar">';
				$icon = CIVI_PLUGIN_URL . 'assets/images/google.png';
				$html .= '<div class="avatar-box">';
				$html .= '<img src="' . $icon . '" alt="">';
				$html .= '</div>';
				$html .= '</div>'; // google-reviews-avatar
				$html .= '<div class="google-reviews-info">';
				$html .= '<h3 class="entry-title">' . $data['result']['name'] . '</h3>';
				$html .= '<span class="rate-star">';
				if (isset($option['show_stars_in_headline']) and $option['show_stars_in_headline'] == true) {
					for ($i = 1; $i <= $data['result']['rating']; ++$i) $html .= '<i class="fas fa-star check"></i>';
					if (isset($option['show_blank_star_till_5']) and $option['show_blank_star_till_5'] == true) for ($i = 1; $i <= 5 - floor($data['result']['rating']); ++$i) $html .= '<i class="fas fa-star"></i>';
				}
				$html .= '<span class="number-star">' . $data['result']['rating'] . '</span>';
				$html .= '</span>'; // rate-star
				if ($data['result']['user_ratings_total'] == 1) {
					$html .= '<span class="count">' . $data['result']['user_ratings_total'] . esc_html__(' review', 'civi-framework') . '</span>';
				} else {
					$html .= '<span class="count">' . $data['result']['user_ratings_total'] . esc_html__(' reviews', 'civi-framework') . '</span>';
				}

				$html .= '</div>'; // google-reviews-info
				$html .= '<div class="google-reviews-add">';
				$html .= '<a href="https://search.google.com/local/writereview?placeid=' . $option['google_maps_review_cid'] . '" target="_Blank"><i class="fas fa-comment-alt-dots"></i>' . esc_html__('Add Review', 'civi-framework') . '</a>';
				$html .= '</div>'; // google-reviews-add
				$html .= '</div>'; // google-reviews-head
			}
			$html .= '<div class="google-reviews-content">';

			foreach ($reviews as $key => $review) {
				$html .= '<div class="google-reviews-item">';
				$html .= '<div class="google-reviews-item-avatar">';
				if (isset($option['show_author_avatar_img']) and $option['show_author_avatar_img'] == true) $html .= '<img class="avatar" src="' . $review['profile_photo_url'] . '">';
				$html .= '</div>'; // google-reviews-item-avatar
				$html .= '<div class="google-reviews-item-info">';
				$html .= '<div class="google-reviews-item-head">';
				$html .= '<div class="google-reviews-item-name">';
				if (isset($option['show_author_of_reviews']) and $option['show_author_of_reviews'] == true) $html .= '<h4>' . $review['author_name'] . '</h4>';
				if (isset($option['show_age_of_the_review']) and $option['show_age_of_the_review'] == true) $html .= '<span> ' . $review['relative_time_description'] . ' </span>';
				$html .= '</div>'; // google-reviews-item-name
				$html .= '<div class="google-reviews-item-rate">';
				if (isset($option['show_only_if_greater_x']) and $review['rating'] <= $option['show_only_if_greater_x']) continue;
				for ($i = 1; $i <= $review['rating']; ++$i) $html .= '<i class="fas fa-star check"></i>';
				if (isset($option['show_blank_star_till_5']) and $option['show_blank_star_till_5'] == true) for ($i = 1; $i <= 5 - $review['rating']; ++$i) $html .= '<i class="fas fa-star"></i>';
				$html .= '</div>'; // google-reviews-item-rate
				$html .= '</div>'; // google-reviews-item-head
				$html .= '<div class="google-reviews-item-content">';
				if (isset($option['show_txt_of_the_review']) and $option['show_txt_of_the_review'] == true) $html .= str_replace(array("\r\n", "\r", "\n"), ' ', $review['text']) . '<br>';
				$html .= '</div>'; // google-reviews-item-content
				$html .= '</div>'; // google-reviews-item-info
				$html .= '</div>'; // google-reviews-item
			}
			$html .= '</div>'; // google-reviews-content
			$html .= '</div>'; // entry-detail
			$html .= '</div>'; // place-google-reviews
		}
		return $html;
	}
}
