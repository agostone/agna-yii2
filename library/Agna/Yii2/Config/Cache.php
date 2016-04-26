<?php

namespace Agna\Yii2\Config;

/**
 * Basic config class
 *
 * Manages reading and caching (to disk) of config files/trees. If the alwaysFresh property is set to false,
 * a meta file is created as well, which is used to determine whether the cache should be invalided or not.
 *
 * @todo Refactoring the config loading process. Turning this into a config builder.
 * @todo Adding different config formats (like YAML, XML, INI or something), etc.
 * @todo Incorrect function name selections dump* should be write*
 *
 * @author Agoston Nagy
 */
class Cache
{
    /**
     * Name of the cache file. (default: '')
     *
     * @var string
     */
    protected static $cacheFilename = '';

    /**
     * Path of the cache directory. (default: '')
     *
     * @var String
     */
    protected static $cacheDirectory = '';

    /**
     * To check if the cache should be invalidated. (default: false)
     *
     * @var boolean
     */
    protected static $alwaysFresh = false;

    /**
     * Config settings (default: array())
     *
     * @var array
     */
    protected static $config = array();

    /**
     * List of config files used to build the config tree. (default: array())
     *
     * @var array
     */
    protected static $configFiles = array();

    /**
     * Determines if the cache should be written. (default: false)
     *
     * @var boolean
     */
    protected static $shouldDump = false;

    /**
     * Singleton.
     */
    protected function __construct()
    {
    }

    /**
     * Sets the cache filename.
     *
     * @param string $filename Cache filename
     */
    public static function setCacheFilename($filename)
    {
        static::$cacheFilename = $filename;
    }

    /**
     * Returns with the cache filename.
     *
     * @return string
     */
    public static function getCacheFilename()
    {
        return static::$cacheFilename;
    }

    /**
     * Sets the cache directory.
     *
     * @param string $directory Cache directory
     */
    public static function setCacheDirectory($directory)
    {
        static::$cacheDirectory = $directory;

        if (!is_dir(static::$cacheDirectory)) {
            mkdir(static::$cacheDirectory, 0777, true);
        }
    }

    /**
     * Sets the always fresh flag.
     *
     * @param boolean $alwaysFresh
     */
    public static function setAlwaysFresh($alwaysFresh)
    {
        static::$alwaysFresh = $alwaysFresh;
    }

    /**
     * Merges a file/tree with the current config settings.
     *
     * @param string $configFile Configuration file to merge
     * @return boolean
     */
    public static function merge($configFile)
    {
        if ($configFile = realpath($configFile)) {

            if (is_file($configFile)) {
                static::mergeFile($configFile);
            }

            if (is_dir($configFile)) {
                static::mergeDirectory($configFile);
            }

            return true;
        }
        return false;
    }

    /**
     * Merges a file with the current config settings.
     *
     * @param string $configFile Configuration path and filename
     */
    protected static function mergeFile($configFile)
    {
        if (!isset(static::$configFiles[$configFile])) {
            static::$shouldDump = true;
            static::$configFiles[$configFile] = filemtime($configFile);

            /**
             * @todo: Temporary closure, CMap:mergeArray() uses pass by value, not good for recursion.
             * Should be removed on refactoring.
             */
            $mergeFunction = function (&$to, $from) use (&$mergeFunction) {
                foreach ($from as $k => $v) {
                    if (is_integer($k)) {
                        $to[]=$v;
                    } elseif (is_array($v) && isset($to[$k]) && is_array($to[$k])) {
                        $to[$k]= $mergeFunction($to[$k],$v);
                    } else {
                        $to[$k]=$v;
                    }
                }
                return $to;
            };

            static::$config = $mergeFunction(static::$config, require $configFile);
        }
    }

    /**
     * Merges a directory of config files with the current config settings.
     *
     * @param string $configDirectory Configuration directory
     */
    protected static function mergeDirectory($configDirectory)
    {
        // @todo: Perhaps extension should come from a config option
        $files = glob("{$configDirectory}/*.php");

        // Storing directory modification because of new fiel addition
        static::$configFiles[$configDirectory] = filemtime($configDirectory);

        // We have a list of files
        if ($files) {

            // Processing all files
            foreach ($files as $configFile) {
                static::mergeFile($configFile);
            }
        }
    }

    /**
     * Loads the config settings from disk. (either from cache or from config file(s))
     *
     * @return array|false
     */
    public static function load()
    {
        $config = false;

        $cacheFile = static::getCacheFile();
        $metafile = static::getCacheMetaFile();

        if (is_file($cacheFile) && is_file($metafile)) {
            $config = static::$config = unserialize(file_get_contents($cacheFile));

            if (!static::$alwaysFresh) {
                static::$configFiles = unserialize(file_get_contents($metafile));

                foreach (static::$configFiles as $configFile => $modificationDate) {

                    /**
                     * @todo: When reloading a specific config file, need to keep it's position according
                     * to the loading order else gonna replace values that were changed by config files loaded later.
                     * So, as for now, reloading the entire config tree
                     */
                    if ($modificationDate < filemtime($configFile)) {

                        $config = false;
                        static::$config = array();
                        static::$configFiles = array();
                        break;
//                        unset(static::$configFiles[$configFile]);
//                        static::merge($configFile);
                    }
                }
            }

//            $config = static::getConfig();
        }

        return $config;
    }

    /**
     * Returns with the current config settings, writes cache if needed.
     *
     * @return array
     */
    public static function getConfig()
    {
        if (static::$shouldDump) {
            static::$shouldDump = false;
            static::dumpCacheFile();
            static::dumpCacheMetaFile();
        }

        return static::$config;
    }

    /**
     * Returns with the full path and filename of the cache file.
     *
     * @return string
     */
    public static function getCacheFile()
    {
        return static::$cacheDirectory . '/'. static::$cacheFilename;
    }

    /**
     * Returns with the full path and filename of the cache meta file.
     *
     * @return string
     */
    public static function getCacheMetaFile()
    {
        return static::$cacheDirectory . '/' . static::getCacheMetaFilename();
    }

    /**
     * Returns with the filename of the cache meta file.
     *
     * @return string
     */
    public static function getCacheMetaFilename()
    {
        return static::getCacheFilename() . '.meta';
    }

    /**
     * Writes the cache meta file to disk.
     *
     * @throws CException
     */
    protected static function dumpCacheMetaFile()
    {
        $cacheMetaFile = static::getCacheMetaFile();

        $tmpFile = tempnam(static::$cacheDirectory, static::$cacheFilename);
        if (false !== @file_put_contents($tmpFile, serialize(static::$configFiles)) && @rename($tmpFile, $cacheMetaFile)) {
            chmod($cacheMetaFile, 0666);
        } else {
            throw new CException("Failed to write cache meta file \"{$cacheMetaFile}\".");
        }
    }

    /**
     * Writes the cache file to disk.
     *
     * @throws CException
     */
    protected static function dumpCacheFile()
    {
        $cacheFile = static::getCacheFile();
        $tmpFile = tempnam(static::$cacheDirectory, static::$cacheFilename);
        if (false !== @file_put_contents($tmpFile, serialize(static::$config)) && @rename($tmpFile, $cacheFile)) {
            chmod($cacheFile, 0666);
        } else {
            throw new CException("Failed to write cache file \"{$cacheFile}\".");
        }
    }
}
