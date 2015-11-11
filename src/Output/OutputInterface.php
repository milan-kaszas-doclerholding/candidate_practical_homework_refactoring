<?php

namespace Language\Output;

/**
 * Interface OutputInterface
 * @package Language\Output
 */
interface OutputInterface
{
    const SEVERITY_INFO     = 'info';
    const SEVERITY_SUCCESS  = 'success';
    const SEVERITY_ERROR    = 'error';

    /**
     * @param $message
     * @param string $severity
     * @return OutputInterface
     */
    public function addMessage($message, $severity);

    /**
     * @return OutputInterface
     */
    public function flushMessages();

    /**
     * @return OutputInterface
     */
    public function resetMessages();
}