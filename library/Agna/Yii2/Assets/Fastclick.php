<?php

namespace Agna\Yii2\Assets;

use Yii;
use yii\web\AssetBundle;
use Agna\Yii2\Helpers\FileHelper;

class Fastclick extends AssetBundle
{
    public $sourcePath = '@bower/fastclick';

    public $css = [];

    public $js = [
        'lib/fastclick.js',
    ];

    public $depends = [
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (!FileHelper::getRealPath('@bower//fastclick')) {
            throw new \RuntimeException(Yii::t('Agna\Yii2', 'Requires bower-asset/fastclick, install it with fxp/composer-asset-plugin!'));
        }
    }
}
