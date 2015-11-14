<?php

namespace Language;

use Batch\AbstractBatch;
use Batch\Output\OutputFactory;

/**
 * Business logic related to generating language files.
 * Class LanguageBatchBo
 * @package Language
 */
class LanguageBatchBo extends AbstractBatch
{

	/**
	 * Batch identity
	 */
	protected static $batchName 	= __CLASS__;
	protected static $batchVersion 	= '1.0.0';

	/**
	 * Contains the applications which require translations.
	 * @var array
	 */
	protected $applications = [];

	/**
	 * @param string $outputType
	 * @throws \Exception
	 */
	public function __construct($outputType = OutputFactory::TYPE_CONSOLE)
	{
		//parent call
		parent::__construct($outputType);
		//fetch apps to translate with config
		$this->applications = $this->configReader->getSystemTranslatedApps();
	}

	/**
	 * Fetch all (or specific) applications
	 * @param array $appNames : if specific apps wanted, apps list
	 * @return array
	 * @throws \Exception
	 */
	protected function getApplications($appNames = [])
	{
		//check if specific apps wanted
		if(!empty($appNames) && is_array($appNames)){
			$appsToReturn = [];
			foreach($appNames as $appName){
				if(!isset($this->applications[$appName])){
					throw new \Exception('Unknown application with name "' . $appName . '"');
				}else{
					$appsToReturn[$appName] = $this->applications[$appName];
				}
			}
			//return specific fetches
			return $appsToReturn;
		}
		//return global fetches
		return $this->applications;
	}

	/**
	 * Starts the language file generation.
	 * @throws \Exception
	 */
	public function generateLanguageFiles()
	{
		try{
			//notify start
			$this->addInfoMessage("Generating language files".$this->getBatchName());
			//iterates on apps
			foreach ($this->getApplications() as $application => $languages) {
				$this->addInfoMessage("[APPLICATION: " . $application . "]");
				//iterates on languages
				foreach ($languages as $language) {
					$this->addInfoMessage("\t[LANGUAGE: " . $language . "]");
					if (self::getLanguageFile($application, $language)) {
						$this->addSuccessMessage("OK");
					} else {
						throw new \Exception('Unable to generate language file!');
					}
				}
			}
		}catch(\Exception $e){
			//add error output
			$this->addErrorMessage($e->getMessage());
		}
		return $this->printOutputMessages();
	}

	/**
	 * Gets the language file for the given language and stores it.
	 * @param $application: 	The name of the application.
	 * @param $language:		The identifier of the language.
	 * @return bool:			The success of the operation.
	 * @throws \Exception:		If there was an error during the download of the language file.
	 */
	protected static function getLanguageFile($application, $language)
	{
		$result = false;
		$languageResponse = ApiCall::call(
			'system_api',
			'language_api',
			array(
				'system' => 'LanguageFiles',
				'action' => 'getLanguageFile'
			),
			array('language' => $language)
		);

		try {
			self::checkForApiErrorResult($languageResponse);
		}
		catch (\Exception $e) {
			throw new \Exception('Error during getting language file: (' . $application . '/' . $language . ')');
		}

		// If we got correct data we store it.
		$destination = self::getLanguageCachePath($application) . $language . '.php';
		// If there is no folder yet, we'll create it.
		if (!is_dir(dirname($destination))) {
			mkdir(dirname($destination), 0755, true);
		}

		$result = file_put_contents($destination, $languageResponse['data']);

		return (bool)$result;
	}

	/**
	 * Gets the directory of the cached language files.
	 *
	 * @param string $application   The application.
	 *
	 * @return string   The directory of the cached language files.
	 */
	protected static function getLanguageCachePath($application)
	{
		return Config::get('system.paths.root') . '/cache/' . $application. '/';
	}

	/**
	 * Gets the language files for the applet and puts them into the cache.
	 *
	 * @throws Exception   If there was an error.
	 *
	 * @return void
	 */
	public static function generateAppletLanguageXmlFiles()
	{
		// List of the applets [directory => applet_id].
		$applets = array(
			'memberapplet' => 'JSM2_MemberApplet',
		);

		echo "\nGetting applet language XMLs..\n";

		foreach ($applets as $appletDirectory => $appletLanguageId) {
			echo " Getting > $appletLanguageId ($appletDirectory) language xmls..\n";
			$languages = self::getAppletLanguages($appletLanguageId);
			if (empty($languages)) {
				throw new \Exception('There is no available languages for the ' . $appletLanguageId . ' applet.');
			}
			else {
				echo ' - Available languages: ' . implode(', ', $languages) . "\n";
			}
			$path = Config::get('system.paths.root') . '/cache/flash';
			foreach ($languages as $language) {
				$xmlContent = self::getAppletLanguageFile($appletLanguageId, $language);
				$xmlFile    = $path . '/lang_' . $language . '.xml';
				if (strlen($xmlContent) == file_put_contents($xmlFile, $xmlContent)) {
					echo " OK saving $xmlFile was successful.\n";
				}
				else {
					throw new \Exception('Unable to save applet: (' . $appletLanguageId . ') language: (' . $language
						. ') xml (' . $xmlFile . ')!');
				}
			}
			echo " < $appletLanguageId ($appletDirectory) language xml cached.\n";
		}

		echo "\nApplet language XMLs generated.\n";
	}

	/**
	 * Gets the available languages for the given applet.
	 *
	 * @param string $applet   The applet identifier.
	 *
	 * @return array   The list of the available applet languages.
	 */
	protected static function getAppletLanguages($applet)
	{
		$result = ApiCall::call(
			'system_api',
			'language_api',
			array(
				'system' => 'LanguageFiles',
				'action' => 'getAppletLanguages'
			),
			array('applet' => $applet)
		);

		try {
			self::checkForApiErrorResult($result);
		}
		catch (\Exception $e) {
			throw new \Exception('Getting languages for applet (' . $applet . ') was unsuccessful ' . $e->getMessage());
		}

		return $result['data'];
	}


	/**
	 * Gets a language xml for an applet.
	 *
	 * @param string $applet      The identifier of the applet.
	 * @param string $language    The language identifier.
	 *
	 * @return string|false   The content of the language file or false if weren't able to get it.
	 */
	protected static function getAppletLanguageFile($applet, $language)
	{
		$result = ApiCall::call(
			'system_api',
			'language_api',
			array(
				'system' => 'LanguageFiles',
				'action' => 'getAppletLanguageFile'
			),
			array(
				'applet' => $applet,
				'language' => $language
			)
		);

		try {
			self::checkForApiErrorResult($result);
		}
		catch (\Exception $e) {
			throw new \Exception('Getting language xml for applet: (' . $applet . ') on language: (' . $language . ') was unsuccessful: '
				. $e->getMessage());
		}

		return $result['data'];
	}

	/**
	 * Checks the api call result.
	 *
	 * @param mixed  $result   The api call result to check.
	 *
	 * @throws Exception   If the api call was not successful.
	 *
	 * @return void
	 */
	protected static function checkForApiErrorResult($result)
	{
		// Error during the api call.
		if ($result === false || !isset($result['status'])) {
			throw new \Exception('Error during the api call');
		}
		// Wrong response.
		if ($result['status'] != 'OK') {
			throw new \Exception('Wrong response: '
				. (!empty($result['error_type']) ? 'Type(' . $result['error_type'] . ') ' : '')
				. (!empty($result['error_code']) ? 'Code(' . $result['error_code'] . ') ' : '')
				. ((string)$result['data']));
		}
		// Wrong content.
		if ($result['data'] === false) {
			throw new \Exception('Wrong content!');
		}
	}
}
