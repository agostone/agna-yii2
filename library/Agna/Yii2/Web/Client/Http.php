<?php

namespace Agna\Yii2\Web\Client;

/**
 * Simple http client component based on Zend Framework.
 *
 * @author stoned
 */
class Http extends \Agna\Yii2\Base\Component
{
    /**
     * Configuration array.
     *
     * @see \Zend\Http\Client::$config
     *
     * @var array
     */
    public $options;

    /**
     * Constructor
     *
     * @param string $uri
     * @param array|Traversable $options
     * @return \Zend\Http\Client
     */
    public function instance($uri = null, $options = [])
    {
        // Mergin with global options
        if (!empty($this->options) && (is_array($options) || $options instanceof \Traversable)) {
            $options = \Agna\Yii2\Helpers\ArrayHelper::mergeRecursive($this->options, $options);
        }

        return new \Zend\Http\Client($uri, $options);
    }

}