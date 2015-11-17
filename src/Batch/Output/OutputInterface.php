<?php

namespace Batch\Output;

/**
 * Interface OutputInterface
 * @package Language\Output
 */
interface OutputInterface
{
    /**
     * Output severities
     */
    const SEVERITY_LOG = 'log';
    const SEVERITY_INFO = 'info';
    const SEVERITY_SUCCESS = 'success';
    const SEVERITY_ERROR = 'error';

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