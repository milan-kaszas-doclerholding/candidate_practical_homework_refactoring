<?php

namespace Language;

/**
 * Interface SimpleLogger
 * In future replace with
 * @package Language Psr\Log\LoggerInterface
 */
interface SimpleLoggerInterface {
	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed $level
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function log($level, $message, $context);
}

/**
 * Wrapper around json_encode to prevent exceptions
 * @param $value
 * @param int $options
 * @param int $depth
 * @codeCoverageIgnore
 */
function safe_json_encode($value, $options = 0, $depth = 512) {
	$ret = '';
	try {
		$ret = json_encode($value,$options,$depth);
	} catch (\Exception $ex) {
		$ret = 'Error on encoding'.$ex->getMessage();
	}
}

/**
 * Business logic related to generating language files.
 */
class LanguageBatchBo
{
	public static $defaultApiClassName = 'Language\ApiCall';
	public static $defaultConfigClassName = 'Language\Config';

	private static $apiClassName;
	private static $configClassName;

	/* @param SimpleLoggerInterface */
	private static $logger;

	/**
	 * Contains the applications which ones require translations.
	 *
	 * @var array
	 */
	protected static $applications = array();

	/**
	 * Starts the language file generation.json_decode
	 * @return void
	 */
	public static function generateLanguageFiles()
	{
		self::log("generateLanguageFiles: ()");

		// The applications where we need to translate.
		self::$applications = self::getConfig('system.translated_applications');

		self::log("\nGenerating language files\n");
		foreach (self::$applications as $application => $languages) {
			self::getApplicationLangFiles($application, $languages);
		}
	}

	/**
	 * Gets the language file for the given language and stores it.
	 *
	 * @param string $application   The name of the application.
	 * @param string $language      The identifier of the language.
	 *
	 * @throws CurlException   If there was an error during the download of the language file.
	 *
	 * @return bool   The success of the operation.
	 */
	protected static function getLanguageFile($application, $language)
	{
		self::log("getLanguageFile: ($application, $language)");
		$languageResponse = self::apiCall(
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
		return self::putLanguageFileIntoCache($application, $language, $languageResponse);
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
		self::log("getLanguageCachePath: ($application)");
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
		self::log("generateAppletLanguageXmlFiles: ()");
		// List of the applets [directory => applet_id].
		$applets = array(
			'memberapplet' => 'JSM2_MemberApplet',
		);

		self::log("\nGetting applet language XMLs..\n");

		foreach ($applets as $appletDirectory => $appletLanguageId) {
			self::getAppletFiles($appletLanguageId, $appletDirectory);
		}

		self::log("\nApplet language XMLs generated.\n");
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
		self::log("getAppletLanguages: ($applet)");
		$result = self::apiCall(
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
		self::log("getAppletLanguageFile: ($applet, $language)");
		$result = self::apiCall(
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
		self::log("checkForApiErrorResult: (".safe_json_encode($result).")");
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

	protected static function apiCall($target, $mode, $getParameters, $postParameters) {
		$get = safe_json_encode($getParameters);
		$post = safe_json_encode($postParameters);
		self::log("apiCall: ($target, $mode, $get, $post)");
		$className = isset(self::$apiClassName) ? self::$apiClassName : self::$defaultApiClassName;
		return $className::call($target, $mode, $getParameters, $postParameters);
	}

	protected static function getConfig($key) {
		self::log("getConfig: ($key)");
		$className = isset(self::$configClassName) ? self::$configClassName : self::$defaultConfigClassName;
		return $className::get($key);
	}

	/**
	 * @codeCoverageIgnore
	 */
	public static function setApiClass($className) {
		self::log("setApiClass: ($className)");
		self::$apiClassName = $className;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public static function setConfigClassName($className) {
		self::log("setConfigClassName: ($className)");
		self::$configClassName = $className;
	}

	/**
	 * @param $appletLanguageId
	 * @param $language
	 * @param $path
	 * @throws \Exception
	 */
	public static function loadAppletLanguageFile($appletLanguageId, $language, $path)
	{
		self::log("loadAppletLanguageFile: ($appletLanguageId, $language, $path)");
		$xmlContent = self::getAppletLanguageFile($appletLanguageId, $language);
		$relativePath = 'lang_' . $language . '.xml';
		$xmlFile = $path . '/'. $relativePath;
		self::log("xmlFile: $relativePath");
		if (strlen($xmlContent) == file_put_contents($xmlFile, $xmlContent)) {
			self::log(" OK saving $relativePath was successful.\n");
		} else {
			throw new \Exception('Unable to save applet: (' . $appletLanguageId . ') language: (' . $language
				. ') xml (' . $xmlFile . ')!');
		}
	}

	/**
	 * @param $appletLanguageId
	 * @param $appletDirectory
	 * @throws \Exception
	 */
	public static function getAppletFiles($appletLanguageId, $appletDirectory)
	{
		self::log("getAppletFiles: ($appletLanguageId, $appletDirectory)");
		$languages = self::getAppletLanguages($appletLanguageId);
		if (empty($languages)) {
			throw new \Exception('There is no available languages for the ' . $appletLanguageId . ' applet.');
		} else {
			self::log(' - Available languages: ' . implode(', ', $languages) . "\n");
		}
		$path = Config::get('system.paths.root') . '/cache/flash';
		foreach ($languages as $language) {
			self::loadAppletLanguageFile($appletLanguageId, $language, $path);
		}
		self::log(" < $appletLanguageId ($appletDirectory) language xml cached.\n");
	}

	/**
	 * @param $application
	 * @param $languages
	 * @throws \Exception
	 */
	public static function getApplicationLangFiles($application, $languages)
	{
		self::log("getApplicationLangFiles: ($application, ". safe_json_encode($languages) .")");
		foreach ($languages as $language) {
			self::log("\t[LANGUAGE: " . $language . "]");
			if (self::getLanguageFile($application, $language)) {
				self::log(" OK\n");
			} else {
				throw new \Exception('Unable to generate language file!');
			}
		}
	}

	/**
	 * @param $application
	 * @param $language
	 * @param $languageResponse
	 * @return bool
	 * @codeCoverageIgnore
	 */
	protected static function putLanguageFileIntoCache($application, $language, $languageResponse)
	{
		self::log("putLanguageFileIntoCache: ($application, $language, ".safe_json_encode($languageResponse).")");
		$destination = self::getLanguageCachePath($application) . $language . '.php';
		// If there is no folder yet, we'll create it.
		if (!is_dir(dirname($destination))) {
			mkdir(dirname($destination), 0755, true);
		}

		$result = file_put_contents($destination, $languageResponse['data']);

		return (bool)$result;
	}

	/**
	 * @param $msg
	 * @param string $context
	 * @codeCoverageIgnore
	 */
	private static function log($msg, $context = '') {
		if (isset(self::$logger) and self::$logger instanceof SimpleLoggerInterface) {
			self::$logger->log('debug', $msg."\n", $context);
		} else {
			echo $msg."\n";
		}
	}

	/**
	 * @param SimpleLoggerInterface $logger
	 * @codeCoverageIgnore
	 */
	public static function setLogger(SimpleLoggerInterface $logger) {
		self::$logger = $logger;
	}
}
