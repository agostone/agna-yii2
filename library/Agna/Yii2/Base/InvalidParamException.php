<?php

namespace Agna\Yii2\Base;

use Yii;

/**
 * InvalidParamException could be thrown whenever the type or value of a given parameter is not as expected.
 *
 * @todo Refactor all classes to use the exception
 *
 * @author Agoston Nagy
 */
class InvalidParamException extends \yii\base\InvalidParamException
{
    /**
     * Constructs a new InvalidParamException on the $name variable.
     *
     * @param string $variableName The name of the variable that has wrong value.
     * @param mixed $value The value that the option was tried to be set too.
     * @param string $expectedValue A string explaining the allowed type and value range. (default: null)
     * @param int $code (default: null)
     * @param Exception $previous (default: null)
     */
    public function __construct($variableName, $value, $expectedValue = null, $code = null, $previous = null)
    {
        $type = gettype($value);

        if (in_array($type, ['array', 'object', 'resource'])) {
            $value = serialize($value);
        }

        $message = "The value '{$value}' that you were trying to assign to parameter '{$variableName}' is invalid.";

        if ($expectedValue) {
            $message .= " Allowed values are: {$expectedValue}.";
        }

        parent::__construct(Yii::t('Agna\Yii2', $message), $code, $previous);
    }
}
