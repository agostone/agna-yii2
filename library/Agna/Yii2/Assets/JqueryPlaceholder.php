<?php

namespace Agna\Yii2\Assets;

use Yii;
use yii\web\AssetBundle;
use Agna\Yii2\Helpers\FileHelper;

class JqueryPlaceholder extends AssetBundle
{
    public $sourcePath = '@bower/jquery-placeholder';

    public $css = [];

    public $js = [
        'jquery.placeholder.js',
    ];

    public $depends = [
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (!FileHelper::getRealPath('@bower//jquery-placeholder')) {
            throw new \RuntimeException(Yii::t('Agna\Yii2', 'Requires bower-asset/jquery-placeholder, install it with fxp/composer-asset-plugin!'));
        }
    }
}
