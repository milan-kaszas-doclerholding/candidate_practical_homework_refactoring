<?php

namespace Language\Output;

/**
 * Class Void : do nothing :)
 * @package Language\Output
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