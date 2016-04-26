<?php

namespace Agna\Yii2\Console;

/**
 * Extended console application
 */
class Application extends \yii\console\Application
{
    use \Agna\Yii2\Base\ApplicationTrait,
        \Agna\Yii2\Base\ModuleTrait;

    /**
     * {@see \yii\console\Application::init()}
     */
    public function init()
    {
        // Because module.init() shouldn't run for an application.
        parent::init();
    }

    /**
     * {@see \yii\console\Application::coreCommands()}
     */
    public function coreCommands()
    {
        $coreCommands = parent::coreCommands();
        $coreCommands['scss'] = 'Agna\Yii2\Console\Controllers\ScssController';
        $coreCommands['asset'] = 'Agna\Yii2\Console\Controllers\AssetController';
        return $coreCommands;
    }
}
