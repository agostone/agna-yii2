<?php

namespace Agna\Yii2\Db\Component;

use yii\di\ServiceLocator;

use Agna\Yii2\Helpers\ArrayHelper;
use Agna\Yii2\Base\ConfigException;

/**
 * Model storage service, aggregating different model storage components under a unified calling mechanism
 *
 * @author stoned
 */
class Storage extends ServiceLocator
{
    public $defaultType = 'activeRecord';

    public function init()
    {
        parent::init();

        if (!is_string($this->defaultType)) {
            throw new ConfigException('defaultType', $this->defaultType, 'string');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function instance($name, $parameters = [], $type = null)
    {
        if ($type === null) {
            $type = $this->defaultType;
        }

        return $this->$type->instance($name, $parameters);
    }

    public function statik($name, $type = null)
    {
        if ($type === null) {
            $type = $this->defaultType;
        }

        return $this->$type->statik($name);
    }
}
