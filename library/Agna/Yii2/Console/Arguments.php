<?php

namespace Agna\Yii2\Console;

/**
 * Basic command line argument parser/extractor for console applications.
 *
 * Uses the format of:
 * --<key>=<value>
 *
 * @todo: Perhaps more flexible parsing and converting logic? Configurable or something?
 *
 * @author Agoston Nagy
 */
class Arguments
{
    /**
     * Parses the passed in arguments array into key => value pairs.
     *
     * @example
     * The following array:
     * array(
     *     '--config=development',
     *     'email',
     *     '--host=localhost'
     * )
     *
     * will result in (if unparsables are ignored):
     * array(
     *     'config' => 'development',
     *     'host' => 'localhost'
     * )
     *
     * will result in (if unparsables are not ignored):
     * array(
     *     'config' => 'development',
     *     0 => 'email',
     *     'host' => 'localhost'
     * )
     *
     * @param array $arguments Arguments to parse
     * @param boolean $ignoreUnparsableArguments Determines if unparsable arguments should be ignored (default: true)
     * @return array
     */
    public static function parse(array $arguments, $ignoreUnparsableArguments = true)
    {
        $parsedArguments = array();

        foreach ($arguments as $argument) {

            // Valid config parameter
            if ($value = static::toKeyValue($argument)) {
                $parsedArguments[$argument] = $value;
            } elseif (!$ignoreUnparsableArguments) { // Storing unparsables as they are, key based
                $parsedArguments[] = $argument;
            }

        }

        return $parsedArguments;
    }

    /**
     * Tries to extract a key => value pair from the pased in arguments array.
     *
     * For exmaple:
     * array(
     *     '--config=development',
     *     'email',
     *     '--host=localhost'
     * )
     *
     * will result in:
     * WwwConsoleArguments::extract('config', $argv) => 'development'
     * WwwConsoleArguments::extract('email', $argv) => null
     * WwwConsoleArguments::extract('host', $argv) => 'localhost'
     *
     * @param string $name Key to extract
     * @param array $arguments Arguments to search in
     * @return string|null
     */
    public static function extract($name, array $arguments)
    {
        $returnValue = null;

        foreach ($arguments as $argument) {

            if ($returnValue = static::toKeyValue($argument)) {
                break;
            }
        }

        return $returnValue;
    }

    /**
     * Converts an array of key => value pairs into an argv array.
     *
     * @example
     * The following array:
     * array(
     *     'config' => 'development',
     *     0 => 'email',
     *     'host' => 'localhost'
     * )
     *
     * will result in (if ignoring numbered keys):
     * array(
     *     '--config=development',
     *     '--host=locahost'
     * )
     *
     * will result in (if not ignoring numbered keys):
     * array(
     *     '--config=development',
     *     'email'
     *     '--host=locahost'
     * )
     *
     * @param array $arguments Arguments to turn into argv
     * @param boolean $ignoreNumberKeys Determines if numbered key should be ignored (default: true)
     * @return array
     */
    public static function toArgv(array $arguments, $ignoreNumberKeys = true)
    {
        $argv = array();

        foreach ($arguments as $key => $value) {

            // Converting string based
            if (is_string($key)) {
                $argv[] = "--{$key}={$value}";
            } elseif (!$ignoreNumberKeys) { // Doing nothing if it's number based
                $argv[] = $value;
            }
        }

        return $argv;
    }

    /**
     * Turns an argument string into key and value pair, returning value and storing key in $argument.
     *
     * @param string $argument Argument to turn into a key and value pair
     * @return mixed|null
     */
    protected static function toKeyValue(&$argument)
    {
        // Valid config parameter
        if (strpos($argument, '--') === 0) {

            // Breaking down to key and value pair
            $configParameter = explode('=', substr($argument, 2), 2);

            // Only valid config parameter if both key and value exists
            if (count($configParameter) == 2) {
                $argument = $configParameter[0];
                return $configParameter[1];
            }
        }

        return null;
    }
}
