<?php

namespace Agna\Yii2\Base;

/**
 * ConfigException could be thrown whenever the type or value of a given variable is not as expected.
 *
 * Unifies configuration error messages.
 *
 * @todo Refactor all classes to use the exception
 *
 * @author Agoston Nagy
 */
class ConfigException extends \yii\base\InvalidConfigException
{
    /**
     * Constructs a new ConfigException on the $name variable.
     *
     * @param string $configName The name of the variable that has wrong value.
     * @param mixed $value The value that the option was tried to be set too.
     * @param string $expectedValue A string explaining the allowed type and value range. (default: null)
     * @param int $code (default: null)
     * @param Exception $previous (default: null)
     */
    public function __construct($configName, $value, $expectedValue = null, $code = null, $previous = null)
    {
        $type = gettype($value);

        if (in_array($type, ['array', 'object', 'resource'])) {
            $value = serialize($value);
        }

        $message = "The value '{$value}' that you were trying to assign to config '{$configName}' is invalid.";

        if ($expectedValue) {
            $message .= " Allowed values are: {$expectedValue}.";
        }

        parent::__construct(\Yii::t('Agna\Yii2', $message), $code, $previous);
    }
}
