<?php

namespace Language\Api;

use Batch\Traits\Singletonable;
use Language\ApiCall;

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
    const SYSTEM_LANGUAGE_FILE = 'LanguageFiles';
    const ACTION_LANGUAGE_FILE = 'getLanguageFile';
    const ACTION_APPLET_LANGUAGE = 'getAppletLanguages';
    const ACTION_APPLET_LANGUAGE_FILE = 'getAppletLanguageFile';

    /**
     * @param $language
     * @return array|void
     * @throws \Exception
     */
    public function getLanguageFileFromApi($language)
    {
        $result = null;
        try{
            $result = $this->makeApiCall(
                self::SYSTEM_API,
                self::LANGUAGE_API,
                array(
                    'system' => self::SYSTEM_LANGUAGE_FILE,
                    'action' => self::ACTION_LANGUAGE_FILE
                ),
                array('language' => $language)
            );
        } catch (ApiException $e){
            throw new \Exception(
                'Error during getting language file: (' .
                $language .
                ')'
            );
        }
        return $result;
    }

    /**
     * @param $applet
     * @return array|void
     * @throws \Exception
     */
    public function getAppletLanguagesFromApi($applet)
    {
        $result = null;
        try{
            $result = $this->makeApiCall(
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
        } catch (ApiException $e){
            //error
            throw new \Exception(
                'Getting languages for applet (' .
                $applet .
                ') was unsuccessful ' .
                $e->getMessage()
            );
        }
        return $result;
    }

    /**
     * @param $applet
     * @param $language
     * @return array|void
     * @throws \Exception
     */
    public function getAppletLanguageFilesFromApi($applet, $language)
    {
        $result = null;
        try{
            $result = $this->makeApiCall(
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
        } catch (ApiException $e){
            throw new \Exception(
                'Getting language xml for applet: (' .
                $applet .
                ') on language: (' .
                $language .
                ') was unsuccessful: '
                . $e->getMessage()
            );
        }
        return $result;
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
            throw ApiException::apiCallError();
        }
        // Wrong response.
        if ($result['status'] != 'OK') {
            throw ApiException::invalidResponseStatus(
                (!empty($result['error_type']) ? 'Type(' . $result['error_type'] . ') ' : '')
                . (!empty($result['error_code']) ? 'Code(' . $result['error_code'] . ') ' : '')
                . print_r($result['data'],true)
            );
        }
        // Wrong content.
        if ($result['data'] === false) {
            throw ApiException::invalidResponseContent();
        }

        // return result
        return $result;
    }


}