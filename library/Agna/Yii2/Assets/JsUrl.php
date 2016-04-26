<?php

namespace Agna\Yii2\Assets;

use Yii;
use yii\web\AssetBundle;
use Agna\Yii2\Helpers\FileHelper;
use Agna\Yii2\Helpers\ArrayHelper;

class JsUrl extends AssetBundle
{

    public $sourcePath = '@bower/jsurl';

    public $js = [
        'url.js'
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (!FileHelper::getRealPath('@bower//jsurl')) {
            throw new \RuntimeException(Yii::t('Agna\Yii2', 'Requires bower-asset/jsurl, install it with fxp/composer-asset-plugin!'));
        }

//        $converter = Yii::$app->getAssetManager()->getConverter();
    }
}
