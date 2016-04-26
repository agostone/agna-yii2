<?php

namespace Agna\Yii2\Console\Controllers;

use Yii;
use yii\base\InvalidConfigException;
use Agna\Yii2\Console\Controller;

class AssetController extends Controller
{
    public $actionConfig = [];

    public function actionPublish($configFile, $bundleClass)
    {
        try {
            $this->loadConfiguration($configFile);
            $this->publishBundle($bundleClass);
        } catch (InvalidConfigException $exception) {
            echo(sprintf(Yii::t('Agna\Yii2', 'Publishing "%s" assets failed!'), $bundleClass) . "\n");
            echo($exception->getMessage() . "\n");
            return 1;
        }

        echo(sprintf(Yii::t('Agna\Yii2', 'Publishing "%s" assets succeed!'), $bundleClass) . "\n");
        return 0;
    }

    protected function publishBundle($bundleClass)
    {
        $assetManager = Yii::$app->getAssetManager();
        $bundle = $assetManager->getBundle($bundleClass, true);

        if (isset($this->actionConfig['publishDependencies']) &&
            $this->actionConfig['publishDependencies']
        ) {
            foreach ($bundle->depends as $depenedency) {
                $this->publishBundle($depenedency);
            }
        }
    }

    public function actionCompress($configFile, $bundleClass)
    {
        $this->loadConfiguration($configFile);
        $oldMap = Yii::$app->controllerMap['asset'];

        $configFile = tempnam(Yii::$app->getRuntimePath(), '');
        file_put_contents($configFile, '<?php return ' . var_export($this->actionConfig, true) . ';');

        Yii::$app->controllerMap['asset'] = 'yii\console\controllers\AssetController';
        $result = Yii::$app->runAction('asset/compress', [$configFile, $bundleClass]);

        unlink($configFile);

        Yii::$app->controllerMap['asset'] = $oldMap;
        return $result;
    }

    public function actionTemplate($configFile)
    {
        $this->loadConfiguration($configFile);
        $oldMap = Yii::$app->controllerMap['asset'];

        $configFile = tempnam(Yii::$app->getRuntimePath(), '');
        file_put_contents($configFile, '<?php return ' . var_export($this->actionConfig, true) . ';');

        Yii::$app->controllerMap['asset'] = 'yii\console\controllers\AssetController';
        $result = Yii::$app->runAction('asset/template', [$configFile]);

        unlink($configFile);

        Yii::$app->controllerMap['asset'] = $oldMap;
        return $result;
    }

    /**
     * Applies configuration from the given file to self instance.
     * @param string $configFile configuration file name.
     * @throws \yii\console\Exception on failure.
     */
    protected function loadConfiguration($configFile)
    {
        $this->stdout("Loading configuration from '{$configFile}'...\n");
        $configs = require($configFile);
        $config =
            isset($configs[$this->action->id]) ?
            (
                is_string($configs[$this->action->id]) && isset($configs[$configs[$this->action->id]]) ?
                $configs[$configs[$this->action->id]] : $configs[$this->action->id]
            ):
            [];
        $this->actionConfig = $config;
    }

    protected function applyConfig(Controller $target)
    {
        foreach ($this->actionConfig as $option => $value) {
            $target->{$option} = $value;
        }

        return $this;
    }
}
