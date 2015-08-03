<?php

namespace Language;

/**
 * Business logic related to generating language files.
 */
class LanguageBatchBo
{
	/**
	 * Contains the applications which ones require translations.
	 *
	 * @var array
	 */
	protected static $applications = array();

	/**
	 * Starts the language file generation.
	 *
	 * @return void
	 */
	public static function generateLanguageFiles()
	{
		// The applications where we need to translate.
		self::$applications = Config::get('system.translated_applications');

		echo "\nGenerating language files\n";
		foreach (self::$applications as $application => $languages) {
			echo "[APPLICATION: " . $application . "]\n";
			foreach ($languages as $language) {
				echo "\t[LANGUAGE: " . $language . "]";
				if (self::getLanguageFile($application, $language)) {
					echo " OK\n";
				}
				else {
					throw new \Exception('Unable to generate language file!');
				}
			}
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
		var_dump($destination);
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
