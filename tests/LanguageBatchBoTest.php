<?php

chdir(__DIR__);
include('../vendor/autoload.php');

/**
 * Allow test protected methods
 * No need in real, but it will be helpful on existing code,
 * cause we have no tests on init
 * In real code it's antiPattern `Paulik Morozov'
 * Class LanguageBatchBoChild
 */
class LanguageBatchBoChild extends \Language\LanguageBatchBo
{
    protected static $testInstance;

    public static function setTestInstance(PHPUnit_Framework_TestCase $instance)
    {
        self::$testInstance = $instance;
    }

    public static function checkForApiErrorResult($result)
    {
        return parent::checkForApiErrorResult($result);
    }

    public static function getAppletLanguageFile($applet, $language)
    {
        return parent::getAppletLanguageFile($applet, $language);
    }

    public static function apiCall($target, $mode, $getParameters, $postParameters)
    {
        return parent::apiCall($target, $mode, $getParameters, $postParameters);
    }

    public static function getAppletLanguages($applet)
    {
        return parent::getAppletLanguages($applet);
    }
}

/**
 * I know about .getMockClass / .staticExpects but at my env they doesn't work
 * ( or it better use Mockery but it's out of scope )
 * so I decide to avoid wasting time
 * Class ApiCallMock
 */
class ApiCallMock
{
    public static $testInstance;
    public static $target;
    public static $mode;
    public static $getParameters;
    public static $postParameters;
    public static $response;

    public static function setTestInstance(PHPUnit_Framework_TestCase $instance)
    {
        self::$testInstance = $instance;
    }

    public static function setCallMock($target, $mode, $getParameters, $postParameters, $response)
    {
        self::$target = $target;
        self::$mode = $mode;
        self::$getParameters = $getParameters;
        self::$postParameters = $postParameters;
        self::$response = $response;
    }

    public static function call($target, $mode, $getParameters, $postParameters)
    {
        self::$testInstance->assertEquals($target, self::$target, 'Target should be equal');
        self::$testInstance->assertEquals($mode, self::$mode, 'Mode should be equal');
        self::$testInstance->assertEquals($getParameters, self::$getParameters, 'GetParameters should be equal');
        self::$testInstance->assertEquals($postParameters, self::$postParameters, 'PostParameters should be equal');
        return self::$response;
    }
}

class ConfigMock {
    protected static $hash = [];
    public static function set($key, $value) {
        self::$hash[$key] = $value;
    }

    public static function get($key) {
        return self::$hash[$key];
    }
}

class SimpleLogger implements \Language\SimpleLoggerInterface{
    protected $log = '';
    public function log($level, $msg, $context = null) {
        $this->log .= "$msg".(!empty($context) ? "with context: $context" : "");
    }

    public function clear() {
        $this->log = '';
    }

    public function getLog() {
        return $this->log;
    }
}

$defaultLogOutput = '
Generating language files
[APPLICATION: portal]
	[LANGUAGE: en] OK
	[LANGUAGE: hu] OK

Getting applet language XMLs..
 Getting > JSM2_MemberApplet (memberapplet) language xmls..
 - Available languages: en
 OK saving /Users/vvs/repo/candidate_practical_homework_refactoring/cache/flash/lang_en.xml was successful.
 < JSM2_MemberApplet (memberapplet) language xml cached.

Applet language XMLs generated.
';

class LanguageBatchBoTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        LanguageBatchBoChild::setTestInstance($this);
        ApiCallMock::setTestInstance($this);
    }

    /**
     * Check output for existing test
     * @throws Exception
     */
    public function testInitialOutput()
    {
        global $defaultLogOutput;
        ob_start();
        $languageBatchBo = new \Language\LanguageBatchBo();
        $languageBatchBo->generateLanguageFiles();
        $languageBatchBo->generateAppletLanguageXmlFiles();
        $output = ob_get_clean();
        $this->assertEquals($output, $defaultLogOutput, 'The output is the same as at start');
    }

    // protected static function checkForApiErrorResult
    public function testCheckForApiErrorResultWhileErrorDuringApiCall()
    {
        try {
            LanguageBatchBoChild::checkForApiErrorResult(false);
            $this->fail('Should throw exception "Error during the api call"');
        } catch (Exception $ex) {
            $this->assertEquals($ex->getMessage(), "Error during the api call", "Error during the api call (false as result)");
        }

        try {
            LanguageBatchBoChild::checkForApiErrorResult([]);
            $this->fail('Should throw exception "Error during the api call"');
        } catch (Exception $ex) {
            $this->assertEquals($ex->getMessage(), "Error during the api call", "Error during the api call (no status in result");
        }
    }

    public function testCheckForApiErrorWhileWrongResponse()
    {
        try {
            LanguageBatchBoChild::checkForApiErrorResult(['status' => 'wrong', 'data' => true]);
            $this->fail('Should throw exception "Wrong response"');
        } catch (Exception $ex) {
            $this->assertContains("Wrong response", $ex->getMessage(), "Wrong response should throw exception");
        }
    }

    public function testCheckForApiErrorWhileWrongContent()
    {
        try {
            LanguageBatchBoChild::checkForApiErrorResult(['status' => 'OK', 'data' => false]);
            $this->fail('Should throw exception "Wrong content"');
        } catch (Exception $ex) {
            $this->assertContains("Wrong content", $ex->getMessage(), "Wrong response should throw exception");
        }
    }

    // protected static function getAppletLanguageFile
    public function testGetAppletLanguageFile()
    {
        $applet = 'testApplet';
        $language = 'testLanguage';
        $testData = 'Test data';

        LanguageBatchBoChild::setApiClass('ApiCallMock');
        ApiCallMock::setCallMock(
            'system_api',
            'language_api',
            ['system' => 'LanguageFiles', 'action' => 'getAppletLanguageFile'],
            ['applet' => $applet, 'language' => $language],
            /* Response: */
            []
        );

        try {
            $data = LanguageBatchBoChild::getAppletLanguageFile($applet, $language);
            $this->fail('Should throw exception');
        } catch (Exception $ex) {
            $this->assertContains('Getting language xml for applet: (' . $applet . ') on language: (' . $language . ') was unsuccessful: ', $ex->getMessage(), "Wrong response should throw exception");
        }

        ApiCallMock::setCallMock(
            'system_api',
            'language_api',
            ['system' => 'LanguageFiles', 'action' => 'getAppletLanguageFile'],
            ['applet' => $applet, 'language' => $language],
            /* Response: */
            ['data' => $testData, 'status' => 'OK']
        );

        $data = LanguageBatchBoChild::getAppletLanguageFile($applet, $language);
        $this->assertEquals($data, $testData, "Should return data from api");
    }

    // protected static function getAppletLanguages
    public function testGetAppletLanguages()
    {
        $applet = 'testApplet';
        $testData = 'Test data';

        LanguageBatchBoChild::setApiClass('ApiCallMock');
        ApiCallMock::setCallMock(
            'system_api',
            'language_api',
            ['system' => 'LanguageFiles', 'action' => 'getAppletLanguages'],
            ['applet' => $applet],
            /* Response: */
            []
        );

        try {
            $data = LanguageBatchBoChild::getAppletLanguages($applet);
            $this->fail('Should throw exception');
        } catch (Exception $ex) {
            $this->assertContains('Getting languages for applet (' . $applet . ') was unsuccessful ', $ex->getMessage(), "Wrong response should throw exception");
        }

        ApiCallMock::setCallMock(
            'system_api',
            'language_api',
            ['system' => 'LanguageFiles', 'action' => 'getAppletLanguages'],
            ['applet' => $applet],
            /* Response: */
            ['data' => $testData, 'status' => 'OK']
        );

        $data = LanguageBatchBoChild::getAppletLanguages($applet);
        $this->assertEquals($data, $testData, "Should return data from api");
    }

    public function testSetLogger() {
        global $defaultLogOutput;
        $logger = new SimpleLogger();
        LanguageBatchBoChild::setLogger($logger);
        LanguageBatchBoChild::setApiClass(null);
        ob_start();
        $languageBatchBo = new \Language\LanguageBatchBo();
        $languageBatchBo->generateLanguageFiles();
        $languageBatchBo->generateAppletLanguageXmlFiles();
        $output = ob_get_clean();
        $this->assertEquals($output, '', 'Do not log to the output');
        $this->assertEquals($logger->getLog(), $defaultLogOutput, 'Log into logger instance');
    }
}
