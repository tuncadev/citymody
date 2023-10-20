<?php

namespace NovaMi\WordPress\SimpleGoogleRecaptcha;

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die('Direct access not allowed');
}

/**
 * Class Uninstall
 * @package NovaMi\WordPress\SimpleGoogleRecaptcha
 */
class Uninstall
{
    /**
     * @return void
     */
    public static function run()
    {
        require_once dirname(__FILE__) . '/entity.php';

        $constants = (new \ReflectionClass(Entity::class))->getConstants();

        foreach ($constants as $constant) {
            $constPrefix = substr($constant, 0, strlen(Entity::PREFIX));

            if ($constPrefix === Entity::PREFIX) {
                delete_option($constant);
            }
        }
    }
}

Uninstall::run();
