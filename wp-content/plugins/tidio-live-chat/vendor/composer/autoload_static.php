<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit820418631320381391e98ecf2345eecc
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'TidioLiveChat\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'TidioLiveChat\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'TidioLiveChat\\Admin\\AdminActionLink' => __DIR__ . '/../..' . '/src/Admin/AdminActionLink.php',
        'TidioLiveChat\\Admin\\AdminController' => __DIR__ . '/../..' . '/src/Admin/AdminController.php',
        'TidioLiveChat\\Admin\\AdminDashboard' => __DIR__ . '/../..' . '/src/Admin/AdminDashboard.php',
        'TidioLiveChat\\Admin\\AdminNotice' => __DIR__ . '/../..' . '/src/Admin/AdminNotice.php',
        'TidioLiveChat\\Admin\\AdminRouting' => __DIR__ . '/../..' . '/src/Admin/AdminRouting.php',
        'TidioLiveChat\\Admin\\IframeSetup' => __DIR__ . '/../..' . '/src/Admin/IframeSetup.php',
        'TidioLiveChat\\Admin\\NonceValidator' => __DIR__ . '/../..' . '/src/Admin/NonceValidator.php',
        'TidioLiveChat\\Admin\\Notice\\DismissibleNoticeController' => __DIR__ . '/../..' . '/src/Admin/Notice/DismissibleNoticeController.php',
        'TidioLiveChat\\Admin\\Notice\\DismissibleNoticeService' => __DIR__ . '/../..' . '/src/Admin/Notice/DismissibleNoticeService.php',
        'TidioLiveChat\\Admin\\Notice\\Exception\\NoticeNameIsNotAllowedException' => __DIR__ . '/../..' . '/src/Admin/Notice/Exception/NoticeNameIsNotAllowedException.php',
        'TidioLiveChat\\Config' => __DIR__ . '/../..' . '/src/Config.php',
        'TidioLiveChat\\Container' => __DIR__ . '/../..' . '/src/Container.php',
        'TidioLiveChat\\Encryption\\EncryptionService' => __DIR__ . '/../..' . '/src/Encryption/EncryptionService.php',
        'TidioLiveChat\\Encryption\\Exception\\DecryptionFailedException' => __DIR__ . '/../..' . '/src/Encryption/Exception/DecryptionFailedException.php',
        'TidioLiveChat\\Encryption\\Service\\EncryptionServiceFactory' => __DIR__ . '/../..' . '/src/Encryption/Service/EncryptionServiceFactory.php',
        'TidioLiveChat\\Encryption\\Service\\OpenSslEncryptionService' => __DIR__ . '/../..' . '/src/Encryption/Service/OpenSslEncryptionService.php',
        'TidioLiveChat\\Encryption\\Service\\PlainTextEncryptionService' => __DIR__ . '/../..' . '/src/Encryption/Service/PlainTextEncryptionService.php',
        'TidioLiveChat\\Http\\Client\\CurlHttpClient' => __DIR__ . '/../..' . '/src/Http/Client/CurlHttpClient.php',
        'TidioLiveChat\\Http\\Client\\FileGetContentsHttpClient' => __DIR__ . '/../..' . '/src/Http/Client/FileGetContentsHttpClient.php',
        'TidioLiveChat\\Http\\Exception\\ErrorResponseException' => __DIR__ . '/../..' . '/src/Http/Exception/ErrorResponseException.php',
        'TidioLiveChat\\Http\\Exception\\HttpClientException' => __DIR__ . '/../..' . '/src/Http/Exception/HttpClientException.php',
        'TidioLiveChat\\Http\\Exception\\UnauthorizedResponseException' => __DIR__ . '/../..' . '/src/Http/Exception/UnauthorizedResponseException.php',
        'TidioLiveChat\\Http\\HttpClient' => __DIR__ . '/../..' . '/src/Http/HttpClient.php',
        'TidioLiveChat\\IntegrationState' => __DIR__ . '/../..' . '/src/IntegrationState.php',
        'TidioLiveChat\\TidioLiveChat' => __DIR__ . '/../..' . '/src/TidioLiveChat.php',
        'TidioLiveChat\\TidioSdk\\Dto\\ApiCredentialsDto' => __DIR__ . '/../..' . '/src/TidioSdk/Dto/ApiCredentialsDto.php',
        'TidioLiveChat\\TidioSdk\\Exception\\AccessTokensAreNotSetException' => __DIR__ . '/../..' . '/src/TidioSdk/Exception/AccessTokensAreNotSetException.php',
        'TidioLiveChat\\TidioSdk\\Exception\\CannotIntegrateWithProjectException' => __DIR__ . '/../..' . '/src/TidioSdk/Exception/CannotIntegrateWithProjectException.php',
        'TidioLiveChat\\TidioSdk\\Exception\\CannotRetrieveAccessTokensException' => __DIR__ . '/../..' . '/src/TidioSdk/Exception/CannotRetrieveAccessTokensException.php',
        'TidioLiveChat\\TidioSdk\\TidioApiClientFactory' => __DIR__ . '/../..' . '/src/TidioSdk/TidioApiClientFactory.php',
        'TidioLiveChat\\TidioSdk\\TidioIntegrationService' => __DIR__ . '/../..' . '/src/TidioSdk/TidioIntegrationService.php',
        'TidioLiveChat\\Translation\\ErrorTranslator' => __DIR__ . '/../..' . '/src/Translation/ErrorTranslator.php',
        'TidioLiveChat\\Translation\\I18n' => __DIR__ . '/../..' . '/src/Translation/I18n.php',
        'TidioLiveChat\\Translation\\TranslationLoader' => __DIR__ . '/../..' . '/src/Translation/TranslationLoader.php',
        'TidioLiveChat\\Utils\\QueryParameters' => __DIR__ . '/../..' . '/src/Utils/QueryParameters.php',
        'TidioLiveChat\\Utils\\Url' => __DIR__ . '/../..' . '/src/Utils/Url.php',
        'TidioLiveChat\\Widget\\WidgetLoader' => __DIR__ . '/../..' . '/src/Widget/WidgetLoader.php',
        'TidioLiveChat\\WooCommerceSdk\\Dto\\WooCommerceIntegrationDto' => __DIR__ . '/../..' . '/src/WooCommerceSdk/Dto/WooCommerceIntegrationDto.php',
        'TidioLiveChat\\WooCommerceSdk\\WooCommerceApiV3ClientFactory' => __DIR__ . '/../..' . '/src/WooCommerceSdk/WooCommerceApiV3ClientFactory.php',
        'TidioLiveChat\\WooCommerceSdk\\WooCommerceIntegrationService' => __DIR__ . '/../..' . '/src/WooCommerceSdk/WooCommerceIntegrationService.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit820418631320381391e98ecf2345eecc::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit820418631320381391e98ecf2345eecc::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit820418631320381391e98ecf2345eecc::$classMap;

        }, null, ClassLoader::class);
    }
}
