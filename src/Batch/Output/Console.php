<?php

namespace Batch\Output;

/**
 * Class Console : php CLI output
 * @package Language\Output
 */
class Console implements OutputInterface
{
    /**
     * CLI colors constants
     */
    const CLI_COLOR_RED = '0;31';
    const CLI_COLOR_GREEN = '1;32';

    /**
     * Messages buffer
     * @var array
     */
    protected $buffer = array();

    /**
     * @inheritdoc
     */
    public function addMessage($message, $severity = self::SEVERITY_INFO)
    {
        array_push(
            $this->buffer,
            $this->getPreparedMessage($message, $severity)
        );
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function flushMessages()
    {
        foreach($this->buffer as $message) {
            echo $message . PHP_EOL;
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function resetMessages()
    {
        $this->buffer = array();
        return $this;
    }

    /**
     * Messages CLI decorator
     * @param $message
     * @param $severity
     * @return string
     */
    protected function getPreparedMessage($message, $severity)
    {
        switch($severity){
            //green for success
            case self::SEVERITY_SUCCESS:
                $preparedMessage = $this->colorizeMessage($message, self::CLI_COLOR_GREEN);
                break;
            //red for error (obvious)
            case self::SEVERITY_ERROR:
                $preparedMessage = $this->colorizeMessage($message, self::CLI_COLOR_RED);
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
        //encapsulates in CLI font color
        return "\033[" . $color . "m" . $message . "\033[0m";
    }
}