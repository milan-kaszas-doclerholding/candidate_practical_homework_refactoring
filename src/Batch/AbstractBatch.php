<?php

namespace Batch;

use Batch\Output\OutputFactory;
use Batch\Output\OutputInterface;
use Batch\Config\Reader as ConfigReader;

/**
 * Class AbstractBatch : abstract batch class
 * @package Batch
 */
abstract class AbstractBatch
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
	 * @param string $severity
	 * @return $this
	 */
	protected function addOutputMessage($message, $severity = OutputInterface::SEVERITY_INFO)
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
