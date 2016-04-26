<?php

namespace Agna\Yii2\Autoloader\Composer;

/**
 * Composer classloader component
 *
 * Makes it possible to configure additional PSR-0 and PSR-4 class autoloading configurations
 *
 * @example
 * [
 *     'autoloader' => [
 *         'psr4' => [
 *             'RootNameSpace\\' => _DIR_ . '/RootNameSpace/'
 *         ]
 *     ]
 * ]
 *
 * @author Agoston Nagy
 */
class Component extends \yii\base\Component
{
    /**
     * Composer PSR-4 autoloader.
     *
     * @var \Composer\Autoload\ClassLoader
     */
    protected $classLoader;

    /**
     * {@see \yii\base\Component::__construct()}
     */
    public function __construct(array $config = [])
    {
        // Getting the composer classloader instance
        $this->classLoader = require AGNA_VENDOR_PATH . '/autoload.php';

        parent::__construct($config);
    }

    /**
     * Sets PSR-0 class-path maps.
     *
     * @param array $paths
     * @return \Agna\Yii2\Composer\Component\ClassLoader
     */
    public function setPsr0(array $paths = [])
    {
        foreach ($paths as $prefix => $path) {
            $this->classLoader->set($prefix, $paths);
        }

        return $this;
    }

    /**
     * Sets PSR-4 namespace-path maps.
     *
     * @param array $paths
     * @throws \InvalidArgumentException
     * @return \Agna\Yii2\Composer\Component\ClassLoader
     */
    public function setPsr4(array $paths = [])
    {
        foreach ($paths as $prefix => $path) {
            $this->classLoader->setPsr4($prefix, $paths);
        }

        return $this;
    }

    /**
     * Registers a set of PSR-0 directories for a given prefix, either
     * appending or prepending to the ones previously set for this prefix.
     *
     * @param string $prefix The prefix
     * @param array|string $paths The PSR-0 root directories
     * @param bool $prepend Whether to prepend the directories
     * @return \Agna\Yii2\Composer\Component\ClassLoader
     */
    public function addPsr0($prefix, $paths, $prepend = false)
    {
        $this->classLoader->add($prefix, $paths, $prepend);
        return $this;
    }

    /**
     * Registers a set of PSR-4 directories for a given namespace, either
     * appending or prepending to the ones previously set for this namespace.
     *
     * @param string $prefix The prefix/namespace, with trailing '\\'
     * @param array|string $paths The PSR-4 base directories
     * @param bool $prepend Whether to prepend the directories
     * @return \Agna\Yii2\Composer\Component\ClassLoader
     */
    public function addPsr4($prefix, $paths, $prepend = false)
    {
        $this->classLoader->addPsr4($prefix, $paths, $prepend);
        return $this;
    }
}
