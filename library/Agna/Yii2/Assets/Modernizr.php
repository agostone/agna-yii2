<?php

namespace Agna\Yii2\Assets;

use Yii;
use yii\web\AssetBundle;
use Agna\Yii2\Helpers\FileHelper;

class Modernizr extends AssetBundle
{
    public $sourcePath = '@bower/components-modernizr';

    public $css = [];

    public $js = [
        'modernizr.js',
    ];

    public $depends = [
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (!FileHelper::getRealPath('@bower//components-modernizr')) {
            throw new \RuntimeException(Yii::t('Agna\Yii2', 'Requires bower-asset/components-modernizr, install it with fxp/composer-asset-plugin!'));
        }
    }
}
