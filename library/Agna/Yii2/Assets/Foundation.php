<?php

namespace Agna\Yii2\Assets;

use Yii;
use yii\web\AssetBundle;
use Agna\Yii2\Helpers\FileHelper;

class Foundation extends AssetBundle
{
    public $sourcePath = '@bower/foundation';

    // @todo foundation.css includes all styles for all libraries, fix is once
    public $css = [
//        'css/normalize.css',
//        'css/foundation.css'
    ];

    public $libraries = [
        'abide',
        'accordion',
        'alert',
        'clearing',
        'dropdown',
        'equalizer',
        'interchange',
        'joyride',
        'magellan',
        'offcanvas',
        'orbit',
        'reveal',
        'slider',
        'tab',
        'tooltip',
        'topbar'
    ];

    public $js = [
        'js/foundation/foundation.js'
    ];

    public $depends = [

        // Js dependency
        'yii\web\JqueryAsset',
        'Agna\Yii2\Assets\Modernizr',
        'Agna\Yii2\Assets\JqueryCookie',
        'Agna\Yii2\Assets\JqueryPlaceholder'
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (!FileHelper::getRealPath('@bower//foundation')) {
            throw new \RuntimeException(Yii::t('Agna\Yii2', 'Requires bower-asset/foundation, install it with fxp/composer-asset-plugin!'));
        }

        // Considering overrides
        if ($this->sourcePath !== null) {
            // Adding selected libraries to the js list
            foreach ($this->libraries as $library) {
                $this->js[] = "js/foundation/foundation.{$library}.js";
            }
        }
    }
}
