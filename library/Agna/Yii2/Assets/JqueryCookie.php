<?php

namespace Agna\Yii2\Assets;

use Yii;
use yii\web\AssetBundle;
use Agna\Yii2\Helpers\FileHelper;

class JqueryCookie extends AssetBundle
{
    public $sourcePath = '@bower/jquery.cookie';

    public $css = [];

    public $js = [
        'src/jquery.cookie.js',
    ];

    public $depends = [
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (!FileHelper::getRealPath('@bower//jquery.cookie')) {
            throw new \RuntimeException(Yii::t('Agna\Yii2', 'Requires bower-asset/jquery.cookie, install it with fxp/composer-asset-plugin!'));
        }
    }
}
