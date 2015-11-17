<?php

namespace Batch\Process;

use Batch\BatchInterface;

/**
 * Class AbstractBatch : batch class abstraction
 * @package Batch
 */
abstract class AbstractProcess implements ProcessInterface
{
    /**
     * Process's parent batch object
     * @var BatchInterface
     */
    protected $batch;

	/**
	 * Time markers
	 * @var integer
	 */
	protected $runTimeStart;
	protected $runTimeStop;

	/**
	 * @param BatchInterface $batch
	 */
	public function __construct(BatchInterface $batch)
	{
		//init deps
		$this->batch = $batch;
	}

	/**
	 * @return BatchInterface
	 */
	public function getBatch()
	{
		return $this->batch;
	}

    /**
     * @param null $processName
     * @return ProcessInterface
     */
	public function start($processName = null)
    {
        //record start time
        $this->runTimeStart = microtime(true);
        //notify
        $this->addInfoMessage(
            '[' .
			$this->getBatch()->getName() .
			' version ' .
			$this->getBatch()->getVersion() .
			']'
		);
        $this->addInfoMessage(
            'Starting process' .
            (!is_null($processName) ? ': ' . $processName : '')
        );
        //return self
        return $this;
    }

    /**
     * @param null $processName
     * @return ProcessInterface
     */
	public function stop($processName = null)
    {
		//record stop time
        $this->runTimeStop = microtime(true);
		//notify
        $elapsedTime = $this->runTimeStop - $this->runTimeStart;
        $this->addInfoMessage('Stopping process' . (!is_null($processName)?': '.$processName:''));
        $this->addInfoMessage('Elapsed time :  ' . number_format($elapsedTime,6) . ' secs.' . PHP_EOL);
        //return self
        return $this;
    }

	/**
	 * @inheritdoc
	 */
	public function addMessage($message)
    {
        return $this
			->getBatch()
			->addMessage($message);
    }

	/**
	 * @inheritdoc
	 */
	public function addInfoMessage($message)
	{
		return $this
			->getBatch()
			->addInfoMessage($message);
	}

	/**
	 * @inheritdoc
	 */
	public function addSuccessMessage($message)
	{
		return $this
			->getBatch()
			->addSuccessMessage($message);
	}

	/**
	 * @inheritdoc
	 */
	public function addErrorMessage($message)
	{
		return $this
			->getBatch()
			->addErrorMessage($message);
	}
}
