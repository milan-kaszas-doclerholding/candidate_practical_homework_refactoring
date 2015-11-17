<?php

namespace Batch\Output;

use Exception;

/**
 * Class OutputException
 * @package Batch\Output
 */
class OutputException extends Exception
{
    /**
     * @param $type
     * @return OutputException
     */
    public static function invalidOutputType($type)
    {
        return new self('Invalid output type "' . $type . '"');
    }

    /**
     * @return OutputException
     */
    public static function invalidOutputObject()
    {
        return new self('Output object must implements Batch\Output\OutputInterface');
    }
}
