<?php

namespace Batch;

/**
 * Interface OutputInterface
 * @package Language\Output
 */
interface BatchInterface
{
    /**
     * @return string
     */
    static function getVersion();

    /**
     * @return string
     */
    static function getName();

    /**
     * @param $message
     * @return BatchInterface
     */
    function addMessage($message);

    /**
     * @param $message
     * @return BatchInterface
     */
    function addInfoMessage($message);

    /**
     * @param $message
     * @return BatchInterface
     */
    function addSuccessMessage($message);

    /**
     * @param $message
     * @return BatchInterface
     */
    function addErrorMessage($message);

    /**
     * @return BatchInterface
     */
    function flushMessages();

}