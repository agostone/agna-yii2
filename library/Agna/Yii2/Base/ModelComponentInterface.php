<?php

namespace Agna\Yii2\Base;

/**
 * Provides a unified model component interface
 *
 * @author stoned
 *
 */
interface ModelComponentInterface
{
    /**
     * Creates a model instance
     *
     * @param string $name
     * @param mixed $parameters
     * @return mixed
     */
    public function instance($name, $parameters);

    /**
     * Calls a static method on the given active record class
     *
     * @param string $name
     * @return mixed
     */
    public function statik($name);

    /**
     * Finds the requested model instance
     *
     * @param string $name
     * @param mixed $parameters
     * @return mixed
     */
    // public function find($name, $parameters);
}