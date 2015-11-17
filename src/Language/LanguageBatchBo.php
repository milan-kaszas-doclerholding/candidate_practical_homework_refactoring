<?php

namespace Language;

use Batch\AbstractBatch;

/**
 * Language files batch.
 * Class LanguageBatchBo
 * @package Language
 */
class LanguageBatchBo extends AbstractBatch
{
	/**
	 * Batch object identity
	 */
	protected static $batchName 	= __CLASS__;
	protected static $batchVersion 	= '1.0.0';

	/**
	 * Starts the language file generation.
	 * @throws \Exception
	 */
	public function generateLanguageFiles()
	{
		return $this->runProcess('Language\Process\GenerateLanguageFiles');
	}

	/**
	 * Gets the language files for the applet and puts them into the cache.
	 * @throws \Exception
	 */
	public function generateAppletLanguageXmlFiles()
	{
        return $this->runProcess('Language\Process\GenerateAppletLanguageXmlFiles');
	}
}
