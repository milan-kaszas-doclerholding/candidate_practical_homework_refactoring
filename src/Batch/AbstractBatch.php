<?php

namespace Batch;

use Batch\Output\OutputFactory;
use Batch\Output\OutputInterface;
use Batch\Config\Reader as ConfigReader;

/**
 * Class AbstractBatch : batch class abstraction
 * @package Batch
 */
abstract class AbstractBatch implements BatchInterface
{

	/**
	 * @var ConfigReader
	 */
	protected $configReader;

	/**
	 * @var OutputInterface
	 */
	protected $output;


	/**
	 * Gets batch name
	 * Uses late static binding
	 * @return mixed
	 */
	public static function getBatchName()
	{
		return static::$batchName;
	}

	/**
	 * Gets batch version
	 * Uses late static binding
	 * @return mixed
	 */
	public static function getBatchVersion()
	{
		return static::$batchVersion;
	}

	/**
	 * @param string $outputType
	 * @throws \Exception
	 */
	public function __construct($outputType = OutputFactory::TYPE_CONSOLE)
	{
		//init deps
		$this->configReader = new ConfigReader();
		$this->output = OutputFactory::create($outputType);
	}

	/**
	 * @param $message
	 * @return AbstractBatch
	 */
	protected function addInfoMessage($message)
	{
		return $this->addOutputMessage($message);
	}

	/**
	 * @param $message
	 * @return AbstractBatch
	 */
	protected function addSuccessMessage($message)
	{
		return $this->addOutputMessage($message, OutputInterface::SEVERITY_SUCCESS);
	}

	/**
	 * @param $message
	 * @return AbstractBatch
	 */
	protected function addErrorMessage($message)
	{
		return $this->addOutputMessage($message, OutputInterface::SEVERITY_ERROR);
	}

	/**
	 * @param $message
	 * @param string $severity
	 * @return $this
	 */
	private function addOutputMessage($message, $severity = OutputInterface::SEVERITY_INFO)
	{
		$this
			->output
			->addMessage($message, $severity);
		return $this;
	}

	/**
	 * @return $this
	 */
	protected function printOutputMessages()
	{
		$this
			->output
			->flushMessages()
			->resetMessages();
		return $this;
	}

}
