<?php

namespace Batch\Output;

/**
 * Class Console : php CLI colorized output
 * @package Batch\Output
 */
class Console extends Cli
{
    /**
     * CLI colors constants
     */
    const CLI_COLOR_RED = '0;31';
    const CLI_COLOR_GREEN = '1;32';
    const CLI_COLOR_CYAN = '1;36';

    /**
     * Messages CLI decorator
     * @param $message
     * @param $severity
     * @return string
     */
    protected function getPreparedMessage($message, $severity)
    {
        switch ($severity) {
            //green for success
            case self::SEVERITY_SUCCESS:
                $preparedMessage = $this->colorizeMessage($message, self::CLI_COLOR_GREEN);
                break;
            //red for error (obvious)
            case self::SEVERITY_ERROR:
                $preparedMessage = $this->colorizeMessage($message, self::CLI_COLOR_RED);
                break;
            //cyan for info
            case self::SEVERITY_INFO:
                $preparedMessage = $this->colorizeMessage($message, self::CLI_COLOR_CYAN);
                break;
            //no color for info and others
            default:
                $preparedMessage = $message;
        }
        //return prepared message
        return $preparedMessage;
    }

    /**
     * @param $message
     * @param $color
     * @return string
     */
    protected function colorizeMessage($message, $color)
    {
        //encapsulates CLI font color
        return "\033[" . $color . "m" . $message . "\033[0m";
    }
}