<?php

namespace Language\Process;

use Batch\Process\AbstractProcess;
use Language\Api\Caller as ApiCaller;
use Language\Config\Reader as ConfigReader;

/**
 * Business logic related to generating language files.
 * Class LanguageBatchBo
 * @package Language
 */
class GenerateAppletLanguageXmlFiles extends AbstractProcess
{

    const JSM2 = 'JSM2_MemberApplet';

    /**
     * @inheritdoc
     */
    public function run($args = [])
    {
        //notify start
        $this->addMessage("Getting applet language XMLs...");
        //fetch applets
        $applets = $this->getAppletsList();
        //iterates
        foreach ($applets as $appletDirectory => $appletLanguageId) {
            //fetch languages
            $this->addMessage(" Getting > $appletLanguageId ($appletDirectory) language xmls...");
            $languages = ApiCaller::getInstance()->getAppletLanguagesFromApi($appletLanguageId);
            if (empty($languages)) {
                self::throwRuntimeError('There is no available languages for the ' . $appletLanguageId . ' applet.');
            } else {
                $this->addMessage(' - Available languages: ' . implode(', ', $languages['data']));
            }
            //generate language files
            foreach ($languages['data'] as $language) {
                $this->buildXmlFile($appletLanguageId, $language);
            }
            //notify
            $this->addMessage(' < ' . $appletLanguageId . '(' . $appletDirectory . ') language xml cached.');
        }
        //notify & return
        $this->addSuccessMessage('Applet language XMLs generated.');
        return $this;
    }

    /**
     * Gets the directory of the cached flash files.
     * @return string
     */
    private function getFlashCachePath()
    {
        return ConfigReader::getInstance()->getSystemPathsRoot() . '/../cache/flash';
    }

    /**
     * List of the applets [directory => applet_id].
     * @return array
     */
    private function getAppletsList()
    {
        return array(
            'memberapplet' => self::JSM2,
        );
    }

    /**
     * @param $applet
     * @param $language
     * @throws \Exception
     */
    private function buildXmlFile($applet, $language)
    {
        $path = $this->getFlashCachePath();
        $result = ApiCaller::getInstance()->getAppletLanguageFilesFromApi($applet, $language);
        $xmlContent = $result['data'];
        $xmlFile = $path . '/lang_' . $language . '.xml';
        if (strlen($xmlContent) == file_put_contents($xmlFile, $xmlContent)) {
            $this->addSuccessMessage(' OK saving ' . $xmlFile . ' was successful.');
        } else {
            self::throwRuntimeError(
                'Unable to save applet: (' .
                $applet .
                ') language: (' .
                $language .
                ') xml (' .
                $xmlFile .
                ')!'
            );
        }
    }
}