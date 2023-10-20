<?php
/*
* Plugin Name: Simple Google reCAPTCHA
* Description: Simply protect your WordPress against spam comments and brute-force attacks, thanks to Google reCAPTCHA!
* Version: 4.0
* Author: Michal Novák
* Author URI: https://www.novami.cz
* License: GPLv3
* Text Domain: simple-google-recaptcha
*/

namespace NovaMi\WordPress\SimpleGoogleRecaptcha;

if (!defined('ABSPATH')) {
    die('Direct access not allowed!');
}

/**
 * Class Core
 * @package NovaMi\WordPress\SimpleGoogleRecaptcha
 */
class Core
{
    /** @var string */
    const UPDATE = 'update';

    /** @var string */
    const DISABLE = 'disable';

    /** @var string */
    const SGR_ACTION = Entity::PREFIX . 'action';

    /** @var Core */
    public static $instance;

    /** @var string */
    private $pluginName;

    /** @var Entity[] */
    private $options;

    /**
     * Core constructor.
     */
    private function __construct()
    {
        add_action('init', [$this, 'run']);
        add_action('activated_plugin', [$this, 'activation']);
    }

    /**
     * @return Core
     */
    public static function getInstance(): Core
    {
        require_once dirname(__FILE__) . '/entity.php';

        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param int $type
     * @return int
     */
    private function getOptionFilter(int $type): int
    {
        return $type === Entity::INT ? FILTER_SANITIZE_NUMBER_INT : FILTER_SANITIZE_FULL_SPECIAL_CHARS;
    }

    /**
     * @param string $id
     * @return int|string
     */
    private function getOptionValue(string $id)
    {
        return $this->options[$id]->getValue() ?? '';
    }

    /**
     * @param string $ext
     * @param string $name
     * @return void
     */
    private function enqueue(string $ext = 'js', string $name = 'sgr')
    {
        $fileName = $name . '.' . $ext;
        $dirPath = plugin_dir_path(__FILE__) . $fileName;
        $dirUrl = plugin_dir_url(__FILE__) . $fileName;

        if ($ext === 'js') {
            wp_enqueue_script($name, $dirUrl, [], filemtime($dirPath));
            wp_localize_script($name, $name, [Entity::SITE_KEY => $this->getOptionValue(Entity::SITE_KEY)]);
        } else {
            wp_enqueue_style($name, $dirUrl, [], filemtime($dirPath));
        }
    }

    /**
     * @param array $atts
     * @return void
     */
    public function displayInput(array $atts)
    {
        $key = $atts['key'];
        $type = $atts['type'];
        $val = $this->getOptionValue($key);

        if ($type === Entity::INT) {
            $defaultVal = $key === Entity::VERSION ? 3 : 1;

            echo sprintf('<input type="checkbox" name="%1$s" id="%1$s" value="%2$d" %3$s />', $key, $defaultVal, checked($defaultVal, $val, false));
        } else {
            echo sprintf('<input type="text" name="%1$s" class="regular-text" id="%1$s" value="%2$s" />', $key, $val);
        }
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->pluginName = get_file_data(__FILE__, ['Name' => 'Plugin Name'])['Name'];

        $this->options = [
            Entity::SITE_KEY => new Entity(__('Site Key', 'simple-google-recaptcha'), Entity::STRING),
            Entity::SECRET_KEY => new Entity(__('Secret Key', 'simple-google-recaptcha'), Entity::STRING),
            Entity::LOGIN_DISABLE => new Entity(__('Disable on login form', 'simple-google-recaptcha')),
            Entity::VERSION => new Entity(__('Enable reCAPTCHA v3', 'simple-google-recaptcha')),
            Entity::BADGE_HIDE => new Entity(__('Hide reCAPTCHA v3 badge', 'simple-google-recaptcha'))
        ];

        $this->updateSettings();

        /**
         * @var string $id
         * @var Entity $option
         */
        foreach ($this->options as $id => $option) {
            $type = $option->getType();
            $filter = $this->getOptionFilter($type);
            $filteredValue = filter_var(get_option($id), $filter);
            $option->setValue($type === Entity::INT ? intval($filteredValue) : strval($filteredValue));
        }

        $this->disableProtection();

        $this->enqueue();
        $this->enqueue('css');

        $this->frontend();

        add_filter(sprintf('plugin_action_links_%s', plugin_basename(__FILE__)), [$this, 'actionLinks']);

        add_action('admin_menu', [$this, 'adminMenu']);
    }

    /**
     * @return void
     */
    public function updateSettings()
    {
        $postAction = strval(filter_input(INPUT_POST, self::SGR_ACTION, FILTER_SANITIZE_SPECIAL_CHARS));

        if ($postAction === self::UPDATE && current_user_can('manage_options')) {
            $hash = null;

            foreach ($this->options as $key => $option) {
                $postValue = filter_input(INPUT_POST, $key, $this->getOptionFilter($option->getType()));

                if ($postValue) {
                    update_option($key, $postValue);

                    if (substr($key, -strlen('_key')) === '_key') {
                        $hash .= $postValue;
                    }
                } else {
                    delete_option($key);
                }
            }

            setcookie(Entity::HASH, md5($hash), time() + 60 * 60 * 24 * 10, '/');

            echo sprintf('<div class="notice notice-success"><p><strong>%s</strong></p></div>', __('Settings saved!', 'simple-google-recaptcha'));
        }
    }

    /**
     * @param array $links
     * @return array
     */
    public function actionLinks(array $links): array
    {
        return array_merge(['settings' => sprintf('<a href="options-general.php%s">%s</a>', Entity::PAGE_QUERY, __('Settings', 'simple-google-recaptcha'))], $links);
    }

    /**
     * @param string $plugin
     * @return void
     */
    public function activation(string $plugin)
    {
        if ($plugin === plugin_basename(__FILE__) && (!get_option(Entity::SITE_KEY) || !get_option(Entity::SECRET_KEY))) {
            $adminUrl = admin_url('options-general.php' . Entity::PAGE_QUERY);

            exit(wp_redirect($adminUrl));
        }
    }

    /**
     * @return void
     */
    public function optionsPage()
    {
        echo sprintf('<div class="wrap"><h1>%s - %s</h1><form method="post" action="%s">', $this->pluginName, __('Settings', 'simple-google-recaptcha'), Entity::PAGE_QUERY);

        settings_fields(Entity::PREFIX . 'header_section');
        do_settings_sections(Entity::PREFIX . 'options');

        echo sprintf('<input type="hidden" name="%s" value="%s">', self::SGR_ACTION, self::UPDATE);

        submit_button();

        echo sprintf('%s</form>%s</div>', PHP_EOL, $this->protectionStatus());
    }

    /**
     * @return void
     */
    public function adminMenu()
    {
        add_submenu_page('options-general.php', $this->pluginName, 'Google reCAPTCHA', 'manage_options', Entity::PREFIX . 'options', [$this, 'optionsPage']);
        add_action('admin_init', [$this, 'displayOptions']);
    }

    /**
     * @return void
     */
    public function displayOptions()
    {
        add_settings_section(Entity::PREFIX . 'header_section', __('Google reCAPTCHA keys', 'simple-google-recaptcha'), [], Entity::PREFIX . 'options');

        foreach ($this->options as $key => $option) {
            $args = ['key' => $key, 'type' => $option->getType()];
            add_settings_field($key, $option->getName(), [$this, 'displayInput'], Entity::PREFIX . 'options', Entity::PREFIX . 'header_section', $args);
            register_setting(Entity::PREFIX . 'header_section', $key);
        }
    }

    /**
     * @return void
     */
    public function enqueueScripts()
    {
        $apiUrlBase = sprintf('https://www.recaptcha.net/recaptcha/api.js?hl=%s', get_locale());
        $jsUrl = sprintf('%s&onload=sgr_2&render=explicit', $apiUrlBase);

        if ($this->getOptionValue(Entity::VERSION) === 3) {
            $jsUrl = sprintf('%s&render=%s&onload=sgr_3', $apiUrlBase, $this->getOptionValue(Entity::SITE_KEY));
        }

        wp_enqueue_script(Entity::PREFIX . 'recaptcha', $jsUrl, [], time());
    }

    /**
     * @param array|null $list
     * @return array|string[]
     */
    public function renderList(?array $list = []): array
    {
        $list ?: $list = [
            'bp_after_signup_profile_fields',
            'comment_form_after_fields',
            'lostpassword_form',
            'register_form',
            'woocommerce_lostpassword_form',
            'woocommerce_register_form'
        ];

        if (!$this->getOptionValue(Entity::LOGIN_DISABLE)) {
            array_push($list, 'login_form', 'woocommerce_login_form');
        }

        return $list;
    }

    /**
     * @param array|null $list
     * @return array|string[]
     */
    public function verifyList(?array $list = []): array
    {
        $list ?: $list = [
            'bp_signup_validate',
            'lostpassword_post',
            'preprocess_comment',
            'registration_errors',
            'woocommerce_register_post'
        ];

        if (!$this->getOptionValue(Entity::LOGIN_DISABLE)) {
            $list[] = 'authenticate';
        }

        return $list;
    }

    /**
     * @return void
     */
    public function frontend()
    {
        $rcpActivate = !is_user_logged_in() && !wp_doing_ajax() && !function_exists('wpcf7_contact_form_shortcode');
        $recaptchaSiteKey = $this->getOptionValue(Entity::SITE_KEY);
        $recaptchaSecretKey = $this->getOptionValue(Entity::SECRET_KEY);

        if ($rcpActivate && $recaptchaSiteKey && $recaptchaSecretKey) {
            add_action(Entity::PREFIX . 'display_list', [$this, 'renderList']);
            add_action(Entity::PREFIX . 'verify_list', [$this, 'verifyList']);

            foreach (apply_filters(Entity::PREFIX . 'render_list', self::renderList()) as $display) {
                add_action($display, [$this, 'enqueueScripts']);
                add_action($display, [$this, 'render']);
            }

            foreach (apply_filters(Entity::PREFIX . 'verify_list', self::verifyList()) as $verify) {
                add_action($verify, [$this, 'verify']);
            }
        }
    }

    /**
     * @return bool
     */
    public function render(): bool
    {
        if ($this->adminCookieHash()) {
            $linkText = __('Emergency reCAPTCHA deactivate', 'simple-google-recaptcha');
            echo sprintf('<p class="sgr-infotext"><a href="?%s=%s">%s</a></p>', self::SGR_ACTION, self::DISABLE, $linkText);
        }

        echo $this->getOptionValue(Entity::VERSION) === 3 ? self::v3Render() : '<div class="sgr-main"></div>';

        return true;
    }

    /**
     * @return string
     */
    private function v3Render(): string
    {
        $badgeReplacement = null;

        if ($this->getOptionValue(Entity::BADGE_HIDE)) {
            $this->enqueue('css', Entity::PREFIX . 'hide');

            $msg = __('This site is protected by reCAPTCHA and the Google <a href="https://policies.google.com/privacy">Privacy Policy</a> and <a href="https://policies.google.com/terms">Terms of Service</a> apply.', 'simple-google-recaptcha');
            $badgeReplacement = sprintf('%s<p class="sgr-infotext">%s</p>', PHP_EOL, $msg);
        }

        return sprintf('<input type="hidden" name="g-recaptcha-response" class="sgr-main">%s', $badgeReplacement);
    }

    /**
     * @return void
     */
    private function disableProtection()
    {
        $getAction = strval(filter_input(INPUT_GET, self::SGR_ACTION, FILTER_SANITIZE_SPECIAL_CHARS));

        if ($getAction === self::DISABLE && $this->adminCookieHash()) {
            $keys = [
                Entity::SITE_KEY,
                Entity::SECRET_KEY
            ];

            foreach ($keys as $key) {
                delete_option($key);

                $this->options[$key]->setValue('');
            }
        }
    }

    /**
     * @param string|null $error_code
     * @return string
     */
    private function errorMessage(?string $error_code): string
    {
        switch ($error_code) {
            case 'missing-input-secret':
                return __('The secret parameter is missing.', 'simple-google-recaptcha');
            case 'missing-input-response':
                return __('The response parameter is missing.', 'simple-google-recaptcha');
            case 'invalid-input-secret':
                return __('The secret parameter is invalid or malformed.', 'simple-google-recaptcha');
            case 'invalid-input-response':
                return __('The response parameter is invalid or malformed.', 'simple-google-recaptcha');
            case 'bad-request':
                return __('The request is invalid or malformed.', 'simple-google-recaptcha');
            case 'timeout-or-duplicate':
                return __('The response is no longer valid: either is too old or has been used previously.', 'simple-google-recaptcha');
            default:
                return __('Unknown error.', 'simple-google-recaptcha');
        }
    }

    /**
     * @param string $response
     * @return array
     */
    private function responseParse(string $response): array
    {
        $secretKey = $this->getOptionValue(Entity::SECRET_KEY);
        $rcpUrl = sprintf('https://www.recaptcha.net/recaptcha/api/siteverify?secret=%s&response=%s', $secretKey, $response);
        $response = wp_remote_get($rcpUrl);

        $failedResponse = [
            'success' => false,
            'error-codes' => ['general-fail']
        ];

        if ($response instanceof \WP_Error) {
            $failedResponse['error-msg'] = $response->get_error_message();
            unset($response);
        }

        return isset($response['body']) ? (array)json_decode($response['body'], 1) : $failedResponse;
    }

    /**
     * @param string $msg
     * @return void
     */
    private function wpDie(string $msg)
    {
        $error = __('Error', 'simple-google-recaptcha');
        $verificationFailed = __('verification failed', 'simple-google-recaptcha');
        $errorParams = ['response' => 403, 'back_link' => 1];

        wp_die(sprintf('<p><strong>%s:</strong> Google reCAPTCHA %s. %s</p>', $error, $verificationFailed, $msg), 'Forbidden by reCAPTCHA', $errorParams);
    }

    /**
     * @param $input
     * @return mixed|void
     */
    public function verify($input)
    {
        if (!empty($_POST)) {
            $response = strval(filter_input(INPUT_POST, 'g-recaptcha-response', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $parsedResponse = $this->responseParse($response);
            $errorCode = $parsedResponse['error-codes'][0] ?? null;

            if (($parsedResponse['success'] ?? null) !== true || $errorCode) {
                $this->wpDie($parsedResponse['error-msg'] ?? $this->errorMessage($errorCode));
            } else {
                if ($this->getOptionValue(Entity::VERSION) === 3 && floatval($parsedResponse['score'] ?? 0) < 0.5) {
                    $this->wpDie(__('You are probably not a human!', 'simple-google-recaptcha'));
                }

                return $input;
            }
        }
    }

    /**
     * @return string
     */
    public function protectionStatus(): string
    {
        $class = 'warning';
        $name = __('Notice', 'simple-google-recaptcha');
        $status = __('is enabled', 'simple-google-recaptcha');
        $msg = __('Keep on mind, that in case of emergency, you can disable this plugin via FTP access, just rename the plugin folder.', 'simple-google-recaptcha');

        if (!$this->getOptionValue(Entity::SITE_KEY) || !$this->getOptionValue(Entity::SECRET_KEY)) {
            $class = 'error';
            $name = __('Warning', 'simple-google-recaptcha');
            $status = __('is disabled', 'simple-google-recaptcha');
            $msg = __('You have to <a href="https://www.google.com/recaptcha/admin" rel="external">register your domain</a>, get required Google reCAPTCHA keys %s and save them bellow.', 'simple-google-recaptcha');
        }

        $type = $this->getOptionValue(Entity::VERSION) === 3 ? 'v3' : 'v2 "I\'m not a robot" Checkbox';

        return sprintf('<div class="notice notice-%s"><p><strong>%s:</strong> Google reCAPTCHA %s!</p><p>%s</p></div>', $class, $name, $status, sprintf($msg, $type));
    }

    /**
     * @return bool
     */
    public function adminCookieHash(): bool
    {
        $cookieHash = filter_input(INPUT_COOKIE, Entity::HASH, FILTER_SANITIZE_SPECIAL_CHARS);

        return $cookieHash === md5($this->getOptionValue(Entity::SITE_KEY) . $this->getOptionValue(Entity::SECRET_KEY));
    }
}

Core::getInstance();
