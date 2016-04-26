<?php

namespace Agna\Yii2\Di\Component;

use Yii;
use Agna\Yii2\Helpers\ArrayHelper;

/**
 * Class instantiator component.
 *
 * Changes:
 * + Possibility to register class maps (simple string maps without definition)
 * + Possibility to register class definitions
 *
 * @author Agoston Nagy
 */
class Object extends \Agna\Yii2\Base\Component
{
    /**
     * Sets class maps
     *
     * If you need to create a simple alias class_alias can make the job done, but what if you wish to
     * substitute an existing class with another? Then creating a map can be handy!
     *
     * @param array $maps
     * @return Agna\Yii2\Di\Component\Object
     */
    public function setMaps(array $maps)
    {
        Yii::$container->maps = $maps;
        return $this;
    }

    /**
     * Sets class definitions
     *
     * @param array $definitions
     * @return Agna\Yii2\Di\Component\Object
     */
    public function setDefinitions(array $definitions)
    {
        // Key should be the class name by default
        foreach ($definitions as $class => $value) {

            // If definition has a class, overrides key
            if (isset($value['class'])) {
                $class = $value['class'];
            }

            Yii::$container->set($class, $value);
        }

        return $this;
    }
}
