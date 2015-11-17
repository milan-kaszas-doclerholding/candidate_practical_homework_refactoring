<?php

namespace Batch\Traits;

/**
 * Trait Singletonable : allow singleton facilities
 * @package Batch\Traits
 */
trait Singletonable
{
    /**
     * @var mixed
     */
    private static $instance = null;

    /**
     * Singleton accessor
     * @return mixed
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
