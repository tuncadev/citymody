<?php

namespace TidioLiveChat\Admin;

if (!defined('WPINC')) {
    die('File loaded directly. Exiting.');
}

use TidioLiveChat\Admin\Notice\DismissibleNoticeService;
use TidioLiveChat\Admin\Notice\Exception\NoticeNameIsNotAllowedException;
use TidioLiveChat\IntegrationState;
use TidioLiveChat\Translation\ErrorTranslator;
use TidioLiveChat\Translation\I18n;
use TidioLiveChat\Utils\QueryParameters;
use TidioLiveChat\WooCommerceSdk\WooCommerceIntegrationService;

class AdminNotice
{
    /**
     * @var ErrorTranslator
     */
    private $errorTranslator;

    /**
     * @var DismissibleNoticeService
     */
    private $dismissibleNoticeService;

    /**
     * @param ErrorTranslator $errorTranslator
     * @param DismissibleNoticeService $dismissibleNoticeService
     */
    public function __construct($errorTranslator, $dismissibleNoticeService)
    {
        $this->errorTranslator = $errorTranslator;
        $this->dismissibleNoticeService = $dismissibleNoticeService;
    }

    public function load()
    {
        add_action('admin_notices', [$this, 'addAdminErrorNotice']);
        add_action('admin_notices', [$this, 'addLyroAIChatbotNotice']);
    }

    public function addAdminErrorNotice()
    {
        if (!QueryParameters::has('error')) {
            return;
        }

        $errorCode = QueryParameters::get('error');
        $errorMessage = $this->errorTranslator->translate($errorCode);
        echo sprintf('<div class="notice notice-error is-dismissible"><p>%s</p></div>', $errorMessage);
    }

    public function addLyroAIChatbotNotice()
    {
        $this->displayDismissibleNotice(
            __DIR__ . '/Notice/Views/LyroAIChatbotNotice.php',
            DismissibleNoticeService::LYRO_AI_CHATBOT_NOTICE
        );
    }

    /**
     * @param string $templatePath
     * @param string $noticeName
     * @return void
     */
    private function displayDismissibleNotice($templatePath, $noticeName)
    {
        try {
            $this->dismissibleNoticeService->displayNotice($templatePath, $noticeName);
        } catch (NoticeNameIsNotAllowedException $exception) {
            // do not display notice if notice name is invalid
        }
    }
}
