<?php

namespace Batch\Output;

/**
 * Class Void : silent output
 * @package Batch\Output
 */
class Void implements OutputInterface
{
    /**
     * @inheritdoc
     */
    public function addMessage($message, $severity)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function flushMessages()
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function resetMessages()
    {
        return $this;
    }
}