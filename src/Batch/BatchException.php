<?php

namespace Batch;

use Exception;

/**
 * Class BatchException
 * @package Batch
 */
class BatchException extends Exception
{
    /**
     * @param $class
     * @return BatchException
     */
    public static function invalidProcessClass($class)
    {
        return new self('Process class ' . $class . ' does not exists');
    }

    /**
     * @param $class
     * @return BatchException
     */
    public static function invalidProcessObject($class)
    {
        return new self('Process class ' . $class . ' must implements Batch\Process\ProcessInterface');
    }
}
