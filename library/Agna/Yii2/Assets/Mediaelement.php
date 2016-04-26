<?php

namespace Agna\Yii2\Assets;

use Yii;
use yii\web\AssetBundle;
use Agna\Yii2\Helpers\FileHelper;

class Mediaelement extends AssetBundle
{

    public $sourcePath = '@bower/mediaelement/build';

    public $css = [];

    public $usePlayer = true;

    public $js = [
        'mediaelement.js'
    ];

    public $depends = [

        // Js dependency
        'yii\web\JqueryAsset'
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (!FileHelper::getRealPath('@bower//mediaelement')) {
            throw new \RuntimeException(Yii::t('Agna\Yii2', 'Requires bower-asset/mediaelement, install it with fxp/composer-asset-plugin!'));
        }

        // Considering overrides
        if ($this->sourcePath !== null && $this->usePlayer) {
            $this->js[] = 'mediaelementplayer.js';
            $this->css[] = 'mediaelementplayer.css';
        }
    }
}
