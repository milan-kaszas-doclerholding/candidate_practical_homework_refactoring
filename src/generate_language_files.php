<?php

chdir(__DIR__);

include('../vendor/autoload.php');

$languageBatchBo = new \Language\LanguageBatchBo();
$languageBatchBo->generateLanguageFiles();
$languageBatchBo->generateAppletLanguageXmlFiles();