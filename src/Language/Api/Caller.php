<?php

namespace Language\Api;

use Language\ApiCall;
use Batch\Traits\Singletonable;

/**
 * Class Reader : Singleton access
 * @package Language\Config
 */
class Caller
{
    /**
     * Singleton trait
     */
    use Singletonable;

    /**
     * Apis
     */
    const SYSTEM_API = 'system_api';
    const LANGUAGE_API = 'language_api';

    /**
     * System & actions
     */
    const SYSTEM_LANGUAGE_FILE          = 'LanguageFiles';
    const ACTION_LANGUAGE_FILE          = 'getLanguageFile';
    const ACTION_APPLET_LANGUAGE        = 'getAppletLanguages';
    const ACTION_APPLET_LANGUAGE_FILE   = 'getAppletLanguageFile';

    /**
     * @param $language
     * @return array|void
     * @throws \Exception
     */
    public function getLanguageFileFromApi($language)
    {
        return $this->makeApiCall(
            self::SYSTEM_API,
            self::LANGUAGE_API,
            array(
                'system' => self::SYSTEM_LANGUAGE_FILE,
                'action' => self::ACTION_LANGUAGE_FILE
            ),
            array('language' => $language)
        );
    }

    /**
     * @param $applet
     * @return array|void
     * @throws \Exception
     */
    public function getAppletLanguagesFromApi($applet)
    {
        return $this->makeApiCall(
            self::SYSTEM_API,
            self::LANGUAGE_API,
            array(
                'system' => self::SYSTEM_LANGUAGE_FILE,
                'action' => self::ACTION_APPLET_LANGUAGE
            ),
            array(
                'applet' => $applet,
            )
        );
    }

    /**
     * @param $applet
     * @param $language
     * @return array|void
     * @throws \Exception
     */
    public function getAppletLanguageFilesFromApi($applet, $language)
    {
        return $this->makeApiCall(
            self::SYSTEM_API,
            self::LANGUAGE_API,
            array(
                'system' => self::SYSTEM_LANGUAGE_FILE,
                'action' => self::ACTION_APPLET_LANGUAGE_FILE
            ),
            array(
                'applet' => $applet,
                'language' => $language
            )
        );
    }

    /**
     * @param $target
     * @param $mode
     * @param $getParameters
     * @param $postParameters
     * @return array|void
     * @throws \Exception
     */
    protected function makeApiCall($target, $mode, $getParameters, $postParameters)
    {
        //perform call
        $result = ApiCall::call(
            $target,
            $mode,
            $getParameters,
            $postParameters
        );

        // Keep specific API result analysis ...
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

        // return result
        return $result;
    }


}