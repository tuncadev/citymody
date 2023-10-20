<?php

namespace NovaMi\WordPress\SimpleGoogleRecaptcha;

if (!defined('ABSPATH')) {
    die('Direct access not allowed');
}

/**
 * Class Entity
 * @package NovaMi\WordPress\SimpleGoogleRecaptcha
 */
class Entity
{
    /** @var string */
    const PREFIX = 'sgr_';

    /** @var int */
    const INT = 1;

    /** @var int */
    const STRING = 2;

    /** @var string */
    const PAGE_QUERY = '?page=' . self::PREFIX . 'options';

    /** @var string */
    const VERSION = self::PREFIX . 'version';

    /** @var string */
    const LOGIN_DISABLE = self::PREFIX . 'login_disable';

    /** @var string */
    const BADGE_HIDE = self::PREFIX . 'badge_hide';

    /** @var string */
    const SITE_KEY = self::PREFIX . 'site_key';

    /** @var string */
    const SECRET_KEY = self::PREFIX . 'secret_key';

    /** @var string */
    const HASH = self::PREFIX . 'hash';

    /** @var string */
    private $name;

    /** @var int */
    private $type;

    /** @var string|int */
    private $value = '';

    /**
     * @param string $name
     * @param int $type
     */
    public function __construct(string $name, int $type = self::INT)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return int|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $value
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}
