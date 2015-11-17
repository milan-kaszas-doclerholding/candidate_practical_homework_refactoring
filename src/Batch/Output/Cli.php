<?php

namespace Batch\Output;

/**
 * Class Console : php CLI output
 * @package Batch\Output
 */
class Cli implements OutputInterface
{

    /**
     * Messages buffer
     * @var array
     */
    protected $buffer = array();

    /**
     * @inheritdoc
     */
    public function addMessage($message, $severity = self::SEVERITY_LOG)
    {
        array_push(
            $this->buffer,
            $this->getPreparedMessage($message, $severity)
        );
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
        switch ($severity) {
            //green for success
            case self::SEVERITY_SUCCESS:
                $preparedMessage = 'SUCCESS : '.$message;
                break;
            //red for error (obvious)
            case self::SEVERITY_ERROR:
                $preparedMessage = 'ERROR : '.$message;
                break;
            //cyan for info
            case self::SEVERITY_INFO:
                $preparedMessage = 'INFO : '.$message;
                break;
            //no color for info and others
            default:
                $preparedMessage = 'DEBUG : '.$message;
        }
        //return prepared message
        return $preparedMessage;
    }

    /**
     * @inheritdoc
     */
    public function flushMessages()
    {
        foreach ($this->buffer as $message) {
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
}