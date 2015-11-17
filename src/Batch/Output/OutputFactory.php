<?php

namespace Batch\Output;

/**
 * Class OutputFactory : OutputInterface objects factory
 * @package Language\Output
 */
class OutputFactory
{
    /**
     * Output types
     */
    const TYPE_CONSOLE = 'console';
    const TYPE_VOID = 'void';

    /**
     * @param $outputType
     * @return Console|Void|null
     * @throws \Exception
     */
    public static function create($outputType)
    {
        $output = null;
        //type analysis
        switch ($outputType) {
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
                throw new \Exception('Unhandled output type "' . $outputType . '"');
        }
        //check if output implements OutputInterface
        if (!$output instanceof OutputInterface) {
            throw new \Exception('Output object must implements Batch\Output\OutputInterface');
        }
        //return
        return $output;
    }
}