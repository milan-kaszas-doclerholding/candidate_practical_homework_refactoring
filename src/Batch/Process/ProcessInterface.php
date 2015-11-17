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
}