<?php

namespace Agna\Yii2\Db\Component;

use Yii;

use Agna\Yii2\Base\ModelComponentInterface;
use Agna\Yii2\Base\InvalidParamException;
use Agna\Yii2\Base\ConfigException;
use Agna\Yii2\Helpers\FileHelper;
use Agna\Yii2\Base\Component;

/**
 * ActiveRecord component for Yii
 *
 * @todo More than one document path + namespace
 *
 * @author stoned
 */
class ActiveRecord extends Component implements ModelComponentInterface
{
    protected $defaultNamespace = 'Model\\';

    public $namespaceMaps = [];

    public $documentPath = '';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (!is_string($this->documentPath) && is_dir($this->documentPath)) {
            throw new ConfigException('documentPath', $this->documentPath, 'string');
        }

        if (!is_array($this->namespaceMaps)) {
            throw new ConfigException('namespaceMaps', $this->namespaceMaps, 'array');
        }

//         if (!is_array($this->aliases)) {
//             throw new ConfigException('aliases', $this->aliases, 'array');
//         }

        /* @var $autoloader \Agna\Yii2\Autoloader\Composer\Component */
        $autoloader = Yii::$app->get('autoloader');

        $namespaceMaps = $this->namespaceMaps;
        $namespaceMaps[$this->defaultNamespace] = $this->documentPath;

        foreach ($this->namespaceMaps as $namespace => $path) {
            $autoloader->addPsr4($namespace, $path);
        }
    }

    /**
     * Sets the default namespace
     *
     * @param string $defaultNamespace
     * @return \Agna\Yii2\Db\Component\ActiveRecord
     */
    public function setDefaultNamespace($defaultNamespace)
    {
        $this->defaultNamespace = trim($defaultNamespace, '\\ ') . '\\';
        return $this;
    }

    /**
     * Sets active record aliases.
     *
     * Simple proxy function to Yii::$app->get('object')->setMaps().
     *
     * @param array $aliases
     * @return \Agna\Yii2\Db\Component\ActiveRecord
     */
    public function setAliases(array $aliases)
    {
        Yii::$app->get('object')->setMaps($aliases);
        return $this;
    }

    /**
     * Returns with an active record instance
     *
     * @todo Do something with $parameters!
     *
     * @param string $name
     * @param mixed $parameters
     * @return \Agna\Yii2\Db\ActiveRecord
     */
    public function instance($name, $parameters)
    {
        if (is_string($parameters)) {
            $parameters = [
                'scenario' => $parameters
            ];
        }

        if (!is_array($parameters)) {
            throw new InvalidParamException('parameters', $parameters, 'array|string');
        }

        $parameters['class'] = $this->getRealClassName($name);

        return Yii::createObject($parameters);
    }

    /**
     * Returns with the real class name
     *
     * @param string $name
     * @return string
     */
    protected function getRealClassName($name)
    {
        // Checking for an alias
        $name = trim($name, '\\');
//        $name = isset($this->aliases[$name]) ? $this->aliases[$name] : $name;
        $name = stripos($name, '\\') !== false
            // Document with a namespace
            ? $name
            // Document without a namespace
            : $this->defaultNamespace . $name;

        return $name;
    }

    /**
     * Returns with a static callable active record class or name
     *
     * @param string $name
     * @return \Agna\Yii2\Db\ActiveRecord
     */
    public function statik($name)
    {
        return $this->getRealClassName($name);
    }
}
