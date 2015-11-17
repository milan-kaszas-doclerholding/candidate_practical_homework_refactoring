<?php

namespace Batch;

use Batch\Output\OutputFactory;
use Batch\Output\OutputInterface;
use Batch\Process\ProcessInterface;

/**
 * Class AbstractBatch : batch class abstraction
 * @package Batch
 */
abstract class AbstractBatch implements BatchInterface
{

	/**
	 * @var OutputInterface
	 */
	protected $output;

	/**
	 * Gets batch name
	 * Uses late static binding
	 * @return mixed
	 */
	public static function getName()
	{
		return static::$batchName;
	}

	/**
	 * Gets batch version
	 * Uses late static binding
	 * @return mixed
	 */
	public static function getVersion()
	{
		return static::$batchVersion;
	}

	/**
	 * @param string $outputType
	 * @throws \Exception
	 */
	public function __construct($outputType = OutputFactory::TYPE_CONSOLE)
	{
		//build output from factory
		$this->output = OutputFactory::create($outputType);
	}

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @inheritdoc
     */
    public function addMessage($message)
    {
        return $this->addOutputMessage($message);
    }

    /**
     * @inheritdoc
     */
    public function addInfoMessage($message)
	{
		return $this->addOutputMessage($message, OutputInterface::SEVERITY_INFO);
	}

    /**
     * @inheritdoc
     */
    public function addSuccessMessage($message)
	{
		return $this->addOutputMessage($message, OutputInterface::SEVERITY_SUCCESS);
	}

    /**
     * @inheritdoc
     */
    public function addErrorMessage($message)
	{
		return $this->addOutputMessage($message, OutputInterface::SEVERITY_ERROR);
	}

	/**
	 * @param $message
	 * @param string $severity
	 * @return BatchInterface
	 */
    protected function addOutputMessage($message, $severity = OutputInterface::SEVERITY_LOG)
	{
		$this->getOutput()->addMessage($message, $severity);
		return $this;
	}

	/**
	 * @return BatchInterface
	 */
    public function flushMessages()
	{
		$this->getOutput()
			->flushMessages()
			->resetMessages();
		return $this;
	}

    /**
     * @param $processClass
     * @param array $args
     * @return BatchInterface
     */
    protected function runProcess($processClass, $args = [])
    {
        try{
            $processObj = $this->getProcessFromClass($processClass);
            //start
            $processObj->start($processClass);
            //run
            $processObj->run($args);
            //stop
            $processObj->stop($processClass);
        }catch (\Exception $e){
            $this->addErrorMessage($e->getMessage());
        }
        //prints messages and return self
        return $this->flushMessages();
    }

    /**
     * @param $processClass
     * @return ProcessInterface
     * @throws \Exception
     */
    protected function getProcessFromClass($processClass)
    {
        //check if class exists
        if(!class_exists($processClass)){
            throw new \Exception('Process class ' . $processClass . ' does not exists');
        }
        $processObj = new $processClass($this);
        //check if ProcessInterface
        if(!$processObj instanceof ProcessInterface){
            throw new \Exception('Process class ' . $processClass . ' must implements Batch\Process\ProcessInterface');
        }
        return $processObj;
    }

}
