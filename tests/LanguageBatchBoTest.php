<?php

chdir(__DIR__);
include('../vendor/autoload.php');

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
}
