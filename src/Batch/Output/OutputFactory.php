<?php

namespace Batch\Output;

/**
 * Class OutputFactory : OutputInterface objects factory
 * @package Batch\Output
 */
class OutputFactory
{
    /**
     * Output types
     */
    const TYPE_CLI = 'cli';
    const TYPE_CONSOLE = 'console';
    const TYPE_VOID = 'void';

    /**
     * @param $outputType
     * @return Cli|Console|Void|null
     * @throws OutputException
     */
    public static function create($outputType)
    {
        $output = null;
        //type analysis
        switch ($outputType) {
            //console
            case self::TYPE_CLI:
                $output = new Cli();
                break;
            //console
            case self::TYPE_CONSOLE:
                $output = new Console();
                break;
            //void
            case self::TYPE_VOID:
                $output = new Void();
                break;
            //non handled
            default:
                throw OutputException::invalidOutputType($outputType);
        }
        //check if output implements OutputInterface
        if (!$output instanceof OutputInterface) {
            throw OutputException::invalidOutputObject();
        }
        //return
        return $output;
    }
}