<?php

namespace Agna\Yii2\Di;

use Yii;

/**
 * Extended di container
 *
 * Changes:
 * + Possibility to register class aliases (simple string maps without definition)
 *
 * @author Agoston Nagy
 */
class Container extends \yii\di\Container
{
    /**
     * Class maps
     *
     * @var array
     */
    public $maps = [];

    public function get($class, $params = [], $config = [])
    {
        if (isset($this->maps[$class]) && is_string($this->maps[$class])) {
            $class = $this->maps[$class];
        }

        return parent::get($class, $params, $config);
    }
}
