<?php

namespace Agna\Yii2\Web;

use Yii;

use Agna\Yii2\Helpers\ArrayHelper;

/**
 * Extended base web application
 *
 * @property \Agna\Yii2\Web\Client\Http httpClient
 *
 * @author Agoston Nagy
 */
class Application extends \yii\web\Application
{
    use \Agna\Yii2\Base\ApplicationTrait {
        \Agna\Yii2\Base\ApplicationTrait::coreComponents as _coreComponents;
        \Agna\Yii2\Base\ApplicationTrait::bootstrap as _bootstrap;
    }
    use \Agna\Yii2\Base\ModuleTrait;

    /**
     * @inheritdoc
     */
    public function coreComponents()
    {
        return array_merge($this->_coreComponents(), [
            'request' => ['class' => 'Agna\Yii2\Web\Request'],
            'response' => ['class' => 'Agna\Yii2\Web\Response']
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function bootstrap()
    {
        $this->bootstrap[] = 'request';

        /**
         * Original yii bootstrap is bugged in base application!
         *
         * @todo Keep an eye out for the fix! Then remove these lines and keep _boostrap call only!
         */
        $request = $this->getRequest();
        Yii::setAlias('@webroot', dirname($request->getScriptFile()));
        Yii::setAlias('@web', $request->getBaseUrl());

        $this->_bootstrap();
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // Because module.init() shouldn't run for an application.
        parent::init();
    }
}
