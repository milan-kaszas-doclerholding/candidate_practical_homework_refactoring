<?php

namespace Batch\Process;

use Batch\BatchInterface;

/**
 * Interface ProcessInterface
 * @package Batch\Process
 */
interface ProcessInterface
{
    /**
     * @param BatchInterface $batch
     */
    function __construct(BatchInterface $batch);

    /**
     * @return BatchInterface
     */
    function getBatch();

    /**
     * @param array $args
     * @return ProcessInterface
     */
    function run($args = []);

    /**
     * @param null $processName
     * @return ProcessInterface
     */
    function start($processName = null);

    /**
     * @param null $processName
     * @return ProcessInterface
     */
    function stop($processName = null);

    /**
     * @param $message
     * @param null $data
     * @throws ProcessException
     * @return void
     */
    static function throwRuntimeError($message, $data = null);
}