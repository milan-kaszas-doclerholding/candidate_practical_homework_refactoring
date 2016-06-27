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
class LanguageBatchBoChild extends \Language\LanguageBatchBo {
    public static function checkForApiErrorResult($result) {
        return forward_static_call(array('\Language\LanguageBatchBo', 'checkForApiErrorResult'), $result);
    }
};

class LanguageBatchBoTest extends PHPUnit_Framework_TestCase
{

    /**
     * Check output for existing test
     * @throws Exception
     */
    public function testInitialOutput()
    {
        ob_start();
        $languageBatchBo = new \Language\LanguageBatchBo();
        $languageBatchBo->generateLanguageFiles();
        $languageBatchBo->generateAppletLanguageXmlFiles();
        $output = ob_get_clean();
        $this->assertEquals('
Generating language files
[APPLICATION: portal]
	[LANGUAGE: en]string(76) "/Users/vvs/repo/candidate_practical_homework_refactoring/cache/portal/en.php"
 OK
	[LANGUAGE: hu]string(76) "/Users/vvs/repo/candidate_practical_homework_refactoring/cache/portal/hu.php"
 OK

Getting applet language XMLs..
 Getting > JSM2_MemberApplet (memberapplet) language xmls..
 - Available languages: en
 OK saving /Users/vvs/repo/candidate_practical_homework_refactoring/cache/flash/lang_en.xml was successful.
 < JSM2_MemberApplet (memberapplet) language xml cached.

Applet language XMLs generated.
', $output, 'The output is the same as at start');
    }
    
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
            LanguageBatchBoChild::checkForApiErrorResult(['status' => 'wrong', 'data' => true ]);
            $this->fail('Should throw exception "Wrong response"');
        } catch (Exception $ex) {
            $this->assertContains("Wrong response", $ex->getMessage(), "Wrong response should throw exception");
        }
    }


    public function testCheckForApiErrorWhileWrongContent()
    {
        try {
            LanguageBatchBoChild::checkForApiErrorResult(['status' => 'OK', 'data' => false ]);
            $this->fail('Should throw exception "Wrong content"');
        } catch (Exception $ex) {
            $this->assertContains("Wrong content", $ex->getMessage(), "Wrong response should throw exception");
        }
    }
}
