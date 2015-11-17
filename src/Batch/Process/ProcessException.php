<?php

namespace Batch\Process;

use Exception;

/**
 * Class ProcessException
 * @package Batch\Process
 */
class ProcessException extends Exception
{
    /**
     * @param $message
     * @param null $data
     * @return ProcessException
     */
    public static function runtimeError($message, $data = null)
    {
        return new self(
            $message .
            (!is_null($data) ? print_r($data, true) : '')
        );
    }

}
