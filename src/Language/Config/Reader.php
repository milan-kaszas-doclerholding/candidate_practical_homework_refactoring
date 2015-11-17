<?php

namespace Language\Config;

use Batch\Traits\Singletonable;
use Language\Config as BaseConfig;

/**
 * Class Reader : Singleton access
 * @package Language\Config
 */
class Reader
{
    /**
     * Singleton trait
     */
    use Singletonable;

    /**
     * Knows keys from config
     * @see Language\Config
     */
    const SYSTEM_PATHS_ROOT = 'system.paths.root';
    const SYSTEM_TRANSLATED_APPS = 'system.translated_applications';

    /**
     * @param $key
     * @return array|string|void
     */
    public static function getConfigValue($key)
    {
        return BaseConfig::get($key);
    }

    /**
     * @return array|string|void
     */
    public static function getSystemPathsRoot()
    {
        return self::getConfigValue(self::SYSTEM_PATHS_ROOT);
    }

    /**
     * @return array|string|void
     */
    public static function getSystemTranslatedApps()
    {
        return self::getConfigValue(self::SYSTEM_TRANSLATED_APPS);
    }
}