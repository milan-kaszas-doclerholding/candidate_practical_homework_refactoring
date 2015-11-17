<?php

namespace Language\Process;

use Batch\Process\AbstractProcess;
use Language\Api\Caller as ApiCaller;
use Language\Config\Reader as ConfigReader;

/**
 * Business logic related to generating language files.
 * Class GenerateLanguageFiles
 * @package Language\Process
 */
class GenerateLanguageFiles extends AbstractProcess
{
    /**
     * @inheritdoc
     */
    public function run($args = [])
    {
        //notify start
        $this->addMessage("Generating language files ...");
        //fetch applications to translate with config
        $applications = ConfigReader::getInstance()->getSystemTranslatedApps();
        //iterates on apps
        foreach ($applications as $application => $languages) {
            $this->addMessage("[APPLICATION: " . $application . "]");
            //iterates on languages
            foreach ($languages as $language) {
                $this->addMessage("[LANGUAGE: " . $language . "]");
                if ($this->buildLanguageFile($application, $language)) {
                    $this->addSuccessMessage("-> OK");
                } else {
                    throw new \Exception('Unable to generate language file!');
                }
            }
        }
        return $this;
    }

    /**
     * Gets the language file for the given language and stores it.
     * @param $application :    The name of the application.
     * @param $language :        The identifier of the language.
     * @return bool:            The success of the operation.
     * @throws \Exception:        If there was an error during the download of the language file.
     */
    private function buildLanguageFile($application, $language)
    {
        //fetch response from Api
        $languageResponse = ApiCaller::getInstance()->getLanguageFileFromApi($language);
        // If we got correct data we store it.
        $destination = $this->getLanguageCachePath($application) . $language . '.php';
        //write & return
        return $this->writeFile($destination, $languageResponse['data']);
    }

    /**
     * Gets the directory of the cached language files.
     * @param $application
     * @return string
     */
    private function getLanguageCachePath($application)
    {
        return ConfigReader::getInstance()->getSystemPathsRoot() . '/../cache/' . $application . '/';
    }

    /**
     * Writes file in filesystem
     * @param $destination
     * @param $content
     * @return bool
     */
    private function writeFile($destination, $content)
    {
        if (!is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }
        return file_put_contents($destination, $content) !== false;
    }

}