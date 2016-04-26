<?php

namespace Agna\Yii2\Helpers;

/**
 * ObjectHelper class file.
 *
 * ObjectHelper provides common methods to manipulate php objects
 * from under Yii Framework (http://www.yiiframework.com)
 *
 * @author Agoston Nagy
 */
class ObjectHelper
{
    /**
     * Returns with the short class name of the desired class
     *
     * @param mixed|string $object
     */
    public static function getShortName($object)
    {
        $object = new \ReflectionClass($object);
        return $object->getShortName();
    }
}
