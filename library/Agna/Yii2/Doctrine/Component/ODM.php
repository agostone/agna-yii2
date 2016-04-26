<?php

namespace Agna\Yii2\Doctrine\Component;

use Yii;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;

use Agna\Yii2\Base\ModelComponentInterface;
use Agna\Yii2\Base\InvalidParamException;
use Agna\Yii2\Base\Component;
use Agna\Yii2\Doctrine\ODM\MongoDB\Configuration;

/**
 * Doctrine ODM (MongoDB) component for Yii
 *
 * @todo More than one document path + namespace
 *
 * @author Agoston Nagy
 */
class ODM extends Component
{
    protected $configuration;
    protected $documentManagers = [];

    public function __construct(array $config = [])
    {
        AnnotationDriver::registerAnnotationClasses();

        $this->configuration = new Configuration();

        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        /* @var $autoloader \Agna\Yii2\Autoloader\Composer\Component */
        $autoloader = \Yii::$app->get('autoloader');
        $autoloader->addPsr4($this->configuration->getDocumentFQNS(), $this->configuration->getDocumentPath());
        $this->configuration->setMetadataDriverImpl(AnnotationDriver::create($this->configuration->getDocumentPath()));
    }

    protected function getDocumentManager($server = null)
    {
        if ($server === null) {
            $server = $this->configuration->getDefaultServer();
        }

        if (!isset($this->documentManagers[$server])) {
            $this->documentManagers[$server] = DocumentManager::create(
                new Connection($this->configuration->getServer($server), $this->configuration->getServerOptions($server)),
                $this->configuration
            );
        }

        return $this->documentManagers[$server];
    }

    /**
     * {@inheritdoc}
     */
    public function __set($name, $value)
    {
        $methodName = "set{$name}";
        if (method_exists($this->configuration, $methodName)) {
            $this->configuration->$methodName($value);
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __get($name)
    {
        $methodName = "get{$name}";
        if (method_exists($this->configuration, $methodName)) {
            $this->configuration->$methodName($value);
        } else {
            parent::__get($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __call($name, $params)
    {
        if ($this->documentManager !== null && method_exists($this->documentManager, $name)) {
            return call_user_func_array([$this->documentManager, $name], $params);
        }

        return parent::__call($name, $params);
    }

    public function setMetadataCache(array $config = [])
    {
        if ($config !== [] && isset($config['class'])) {
            $cache = $config['class'];
            unset($config['class']);

            if ($config !== []) {
                $cache = new \ReflectionClass($cache);
                $cache = $cache->newInstanceArgs($config);
            } else {
                $cache = new $cache();
            }

            $this->configuration->setMetadataCacheImpl($cache);
        }

        return $this;
    }

    /**
     * Returns with an ODM collection instance
     *
     * @param string $name
     * @param mixed $parameters
     * @return Collection
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

        if (!isset($parameters['server'])) {
            $parameters['server'] = null;
        }

        // Checking for an alias
        $name = $this->configuration->getDocumentName(trim($name, '\\'));

        $name = stripos($name, '\\') !== false
            // Document with a namespace
            ? $name
            // Document without a namespace
            : $this->configuration->getDocumentFQNS() . $name;

        return new $name($this->getDocumentManager($parameters['server']));
    }
}