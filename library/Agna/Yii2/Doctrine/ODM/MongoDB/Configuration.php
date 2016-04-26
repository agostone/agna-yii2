<?php

namespace Agna\Yii2\Doctrine\ODM\MongoDB;

use Agna\Yii2\Base\InvalidParamException;

/**
 * Extended ODM configuration class
 *
 * Changes:
 * + Added server configuration setting(s)
 * + Added default server setting
 * + Added document path setting
 * + Added document full qualified namespace setting
 *
 * @author Agoston Nagy
 */
class Configuration extends \Doctrine\ODM\MongoDB\Configuration
{
    public function setServers($servers)
    {
        if (is_string($servers)) {
            $servers = [$this->getDefaultServer() => $servers];
        }

        if (!is_array($servers)) {
            throw new InvalidParamException('$servers', $servers, 'string|array');
        }


        $this->attributes['servers'] = $servers;
    }

    public function getServers()
    {
        return isset($this->attributes['servers']) ? $this->attributes['servers'] : null;
    }

    public function getServer($name = null)
    {
        if ($name === null) {
            $name = $this->getDefaultServer();
        }

        return isset($this->attributes['servers'][$name]) ? $this->attributes['servers'][$name] : null;
    }

    public function setDefaultServer($server)
    {
        $this->attributes['defaultServer'] = $server;
    }

    public function getDefaultServer()
    {
        return isset($this->attributes['defaultServer']) ? $this->attributes['defaultServer'] : 'default';
    }

    public function getServerOptions($name = null)
    {
        $server = $this->getServer($name);

        return isset($server['options']) ? $server['options'] : [];
    }

    public function setDocumentPath($path)
    {
        $this->attributes['documentPath'] = $path;
    }

    public function getDocumentPath()
    {
        return isset($this->attributes['documentPath']) ? $this->attributes['documentPath'] : null;
    }

    /**
     * Sets the full qualified class namespace of the document models.
     *
     * Sadly setDocumentNamespace is taken by doctrine for namespace aliases.
     *
     * @param string $namespace
     */
    public function setDocumentFQNS($namespace)
    {
        $this->attributes['documentFQNS'] = trim($namespace, '\\ ') . '\\';
    }

    /**
     * Returns with the full qualified class namespace of the model documents (default: Documents\)
     *
     * Sadly getDocumentNamespace is taken by doctrine for namespace aliases.
     *
     * @return string
     */
    public function getDocumentFQNS()
    {
        return isset($this->attributes['documentFQNS']) ? $this->attributes['documentNamespace'] : 'Documents\\';
    }

    public function getDocumentName($alias)
    {
        return isset($this->attributes['documentAliases'][$alias])
            ? $this->attributes['documentAliases'][$alias]
            : $alias;
    }

    public function getDocumentAliases()
    {
        return isset($this->attributes['documentAliases']) ? $this->attributes['documentAliases'] : [];
    }

    public function setDocumentAliases($aliases)
    {
        $this->attributes['documentAliases'] = $aliases;
    }

}