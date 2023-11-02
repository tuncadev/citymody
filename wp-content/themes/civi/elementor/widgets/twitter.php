<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;

defined('ABSPATH') || exit;

class Widget_Twitter extends Base
{

	public function get_name()
	{
		return 'civi-twitter';
	}

	public function get_title()
	{
		return esc_html__('Twitter', 'civi');
	}

	public function get_icon_part()
	{
		return 'eicon-twitter-feed';
	}

	public function get_keywords()
	{
		return ['media', 'twitter'];
	}

    public function get_style_depends()
    {
        return ['civi-el-widget-twitter'];
    }

	protected function register_controls()
	{
		$this->start_controls_section('content_section', [
			'label' => esc_html__('Content', 'civi'),
		]);

		$this->add_control('consumer_key', [
			'label' => esc_html__('Consumer Key', 'civi'),
			'type'  => Controls_Manager::TEXT,
		]);

		$this->add_control('consumer_secret', [
			'label' => esc_html__('Consumer Secret', 'civi'),
			'type'  => Controls_Manager::TEXT,
		]);

		$this->add_control('access_token', [
			'label' => esc_html__('Access Token', 'civi'),
			'type'  => Controls_Manager::TEXT,
		]);

		$this->add_control('access_token_secret', [
			'label' => esc_html__('Access Token Secret', 'civi'),
			'type'  => Controls_Manager::TEXT,
		]);

		$this->add_control('username', [
			'label' => esc_html__('Twitter Username', 'civi'),
			'type'  => Controls_Manager::TEXT,
		]);

		$this->add_control('number_items', [
			'label'   => esc_html__('Number Items', 'civi'),
			'type'    => Controls_Manager::NUMBER,
			'min'     => 1,
			'max'     => 12,
			'step'    => 1,
			'default' => 3,
		]);

		$this->add_control(
			'show_date',
			[
				'label'        => esc_html__('Show date', 'civi'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__('Yes', 'civi'),
				'label_off'    => esc_html__('No', 'civi'),
				'return_value' => '1',
			]
		);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$tweets = $this->get_tweets($settings['username'], $settings['consumer_key'], $settings['consumer_secret'], $settings['access_token'], $settings['access_token_secret'], $settings['number_items']);

		if (!is_array($tweets) || empty($tweets) || isset($twitter['errors'])) {
			return;
		}

		$this->add_render_attribute('wrapper', 'class', 'civi-twitter style-list');
?>
		<div <?php $this->print_attributes_string('wrapper'); ?>>
			<?php foreach ($tweets as $tweet) : ?>
				<?php
				$latest_tweet = $tweet['text'];
				$latest_tweet = preg_replace('/(https:\/\/[a-z0-9\.\/]+)/i', '&nbsp;<a href="$1" target="_blank">$1</a>&nbsp;', $latest_tweet);
				$latest_tweet = preg_replace('/http:\/\/([a-z0-9_\.\-\+\&\!\#\~\/\,]+)/i', '&nbsp;<a href="http://$1" target="_blank">http://$1</a>&nbsp;', $latest_tweet);
				$latest_tweet = preg_replace('/@([a-z0-9_]+)/i', '&nbsp;<a href="http://twitter.com/$1" target="_blank">$1</a>&nbsp;', $latest_tweet);
				?>
				<div class="tweet">
					<?php echo '<div class="tweet-text">' . $latest_tweet . '</div>'; ?>
					<?php if ('1' === $settings['show_date']) : ?>
						<span class="tweet-date"><?php echo date('F d, Y', strtotime($tweet['created_at'])); ?></span>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
<?php
	}

	private function get_tweets($username, $consumer_key, $consumer_secret, $access_token, $access_token_secret, $number_items)
	{
		$tweets = array();

		if ($username !== '' && $consumer_key !== '' && $consumer_secret !== '' && $access_token !== '' && $access_token_secret !== '' && $number_items !== '') {
			$trans_name = "list_tweets_{$username}_c{$number_items}";

			$twitter_data = get_transient($trans_name);
			$json         = json_decode($twitter_data, true);

			if (false === $twitter_data || isset($json['errors'])) {

				$token = get_option('cfTwitterToken_' . $username);

				// Get a new token anyways.
				delete_option('cfTwitterToken_' . $username);

				// Getting new auth bearer only if we don't have one.
				if (!$token) {
					// preparing credentials.
					$credentials = $consumer_key . ':' . $consumer_secret;
					$to_send     = civi_core_base_encode($credentials);

					// http post arguments.
					$args = array(
						'method'      => 'POST',
						'httpversion' => '1.1',
						'blocking'    => true,
						'headers'     => array(
							'Authorization' => 'Basic ' . $to_send,
							'Content-Type'  => 'application/x-www-form-urlencoded;charset=UTF-8',
						),
						'body'        => array('grant_type' => 'client_credentials'),
					);

					add_filter('https_ssl_verify', '__return_false');
					$response = wp_remote_post('https://api.twitter.com/oauth2/token', $args);

					$keys = json_decode(wp_remote_retrieve_body($response));

					if ($keys) {
						// Saving token to wp_options table.
						update_option('cfTwitterToken_' . $username, $keys->access_token);
						$token = $keys->access_token;
					}
				}
				// We have bearer token whether we obtain it from API or from options.
				$args = array(
					'httpversion' => '1.1',
					'blocking'    => true,
					'headers'     => array(
						'Authorization' => "Bearer $token",
					),
				);

				add_filter('https_ssl_verify', '__return_false');
				$api_url  = 'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=' . $username . '&count=' . $number_items;
				$response = wp_remote_get($api_url, $args);

				set_transient($trans_name, wp_remote_retrieve_body($response), HOUR_IN_SECONDS * 2);
			}

			$tweets = json_decode(get_transient($trans_name), true);
		}

		return $tweets;
	}
}
