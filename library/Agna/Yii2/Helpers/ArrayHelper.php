<?php

namespace Agna\Yii2\Helpers;

use Agna\Yii2\Base\InvalidParamException;
/**
 * Extended array helper class.
 *
 * Changes:
 * + Checking if multiple keys exist in an array
 * + Searching for multiple values in an array
 * + Recursively merging two array, values of the same key will be overwritten
 * + Adding an element to an array at the given position
 *
 * @author Agoston Nagy
 */
class ArrayHelper extends \yii\helpers\ArrayHelper
{
    /**
     * Searches the array for mulitply keys.
     *
     * @param array $keys Keys to search for
     * @param array $search Array to look for keys
     * @param string $booleanReturn If true returns a single boolean value on any match else array of matching keys (default: true)
     * @return boolean|array
     */
    public static function multiplyKeyExist(array $keys, array $search, $booleanReturn = true)
    {
        $keysFound = array();

        foreach ($keys as $key) {

            if (array_key_exists($key, $search)) {
                $keysFound[] = $key;

                if ($booleanReturn) {
                    return true;
                }
            }
        }

        return $booleanReturn ? false : $keysFound;
    }

    /**
     * Searches the array for multiple values.
     *
     * @param array $keys Values to search for
     * @param array $search Array to look for values
     * @param string $booleanReturn If true returns a single boolean value on any match else array of matching values (default: true)
     * @return boolean|array
     */
    public static function multiplySearch(array $values, array $search, $booleanReturn = true)
    {
        $valuesFound = array();

        foreach ($values as $value) {

            if (array_search($value, $search)) {
                $valuesFound[] = $value;

                if ($booleanReturn) {
                    return true;
                }
            }
        }

        return $booleanReturn ? false : $valuesFound;
    }

    /**
     * Merges the elements of two arrays together.
     *
     * The values of $with are appended to the end of $initial.
     *
     * @param array $initial Initial array
     * @param array $with Array to append to initial
     * @return array
     */
    public static function mergeRecursive(array $initial, array $with)
    {
        // Process the array
        foreach ($with as $key => $value) {

            // Key exists in $initial and both elements are arrays, merge recursively.
            if (isset($initial[$key]) && is_array($initial[$key]) && is_array($value)) {
                $initial[$key] = static::mergeRecursive($initial[$key], $value);
            } else {
                $initial[$key] = $value;
            }
        }

        return $initial;
    }

    /**
     * Adds one or more element(s) at the given position or end of an array
     *
     * @param array $target Array to add to
     * @param integer $position Position in the array (null means end of the array)
     * @param mixed $element,...
     * @return int
     */
    public static function add(array &$target, $position)
    {
        if (!is_int($position)) {
            throw new InvalidParamException('$position', $position, 'integer');
        }

        $arguments = func_get_args();

        if (count($arguments) > 2) {
            unset($arguments[0], $arguments[1]);

            if ($position === null) {
                $target = array_merge($target, $arguments);
            } else {
                array_splice($target, $position, 0, $arguments);
            }
        }

        return count($target);
    }

    /**
     * Sets the given value for the given key recursively.
     *
     * @param array $target Array to add to
     * @param string $key Label to set the value for. If $separator present, sub array(s) will be created.
     * @param mixed $value
     * @param string $separator (default: '.');
     * @return int
     */
    public static function set(array &$target, $key, $value, $separator = '.')
    {
        if (!is_string($key)) {
            throw new InvalidParamException('$key', $key, 'string');
        }

        if (!is_string($separator)) {
            throw new InvalidParamException('$separator', $separator, 'string');
        }

        if (stripos($key, $separator) !== false) {

            $subKeys = explode($separator, $key);
            $target = &$target;
            foreach ($subKeys as $index => $subKey) {
                if (count($subKeys) - 1 > $index) {
                    if (!isset($target[$subKey]) || !is_array($target[$subKey])) {
                        $target[$subKey] = [];
                    }
                    $target = &$target[$subKey];
                } else {
                    $target[$subKey] = $value;
                }
            }

        } else {
            $target[$key] = $value;
        }

        return $target;
    }

    public static function get(array $target, $key, $default = null, $separator = '.')
    {
        if (!is_string($key)) {
            throw new InvalidParamException('$key', $key, 'string');
        }

        if (!is_string($separator)) {
            throw new InvalidParamException('$separator', $separator, 'string');
        }

        if (stripos($key, $separator) !== false) {

            $subKeys = explode($separator, $key);
            foreach ($subKeys as $index => $subKey) {
                if (count($subKeys) - 1 > $index) {
                    if (!isset($target[$subKey]) || !is_array($target[$subKey])) {
                        break;
                    }
                    $target = $target[$subKey];
                } elseif (array_key_exists($subKey, $target)) {
                    return $target[$subKey];
                }
            }
        } elseif (array_key_exists($key, $target)) {
            return $target[$key];
        }

        return $default;
    }
}
