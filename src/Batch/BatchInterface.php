<?php

namespace Batch;

/**
 * Interface OutputInterface
 * @package Language\Output
 */
interface BatchInterface
{
    /**
     * @return string
     */
    public static function getBatchVersion();

    /**
     * @return string
     */
    public static function getBatchName();


}