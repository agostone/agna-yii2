<?php

namespace Agna\Yii2\Assets;

use Yii;
use yii\web\AssetBundle;
use Agna\Yii2\Helpers\FileHelper;

class Agna extends AssetBundle
{
    public $css = [];

    public $additionals = [
        'Agna/Object/Utils.js'
    ];

    public $js = [
        'Agna/Namespace.js'
    ];

    public $depends = [];

    public function __construct($config = [])
    {
        $this->sourcePath = __DIR__ . '/javascript';

        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Considering overrides
        if ($this->sourcePath !== null) {
            // Adding selected libraries to the js list
            foreach ($this->additionals as $additional) {
                $this->js[] = $additional;
            }
        }
    }
}
