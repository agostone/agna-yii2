<?php

namespace Agna\Yii2\Helpers;

use Yii;

/**
 * FileHelper class file.
 *
 * FileHelper provides common methods to manipulate filesystem objects (files and
 * directories) from under Yii Framework (http://www.yiiframework.com)
 *
 * Changes:
 * = Component type been reverted to a simple utility class
 * = A few functions been renamed or removed
 * = Default directory permissions changed to 777
 *
 * @todo Migrated class
 * @todo Review and remove unwanted overlaping code with the original FileHelper
 * @author Agoston Nagy
 */
class FileHelper extends \yii\helpers\FileHelper
{
    /**
     * Returns true if the file system object has no content or doesn't exist.
     *
     * Directory considered empty if it doesn't contain descendants.
     * File considered empty if its size is 0 bytes.
     *
     * @param string $target Path and name of the object to check
     *
     * @return boolean
     */
    public static function isEmpty($target)
    {
        // If target is a file
        if (is_file($target)) {

            // If valid file and has a content size greater than zero
            if ($size = @filesize($target) && $size > 0) {
                return false;
            }
        } elseif (is_dir($target)) { // If target is a directory

            // If valid directory and scandir has at least a third element (first two is '.' and '..')
            if ($size = @scandir($target) && isset($size[2])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Creates an empty file if the current file doesn't exist.
     *
     * @param string $target Path and filename to create
     * @param booelan $createDirectory Determines if the directory tree should be created (default: true)
     *
     * @return boolean
     */
    public static function createFile($target, $createDirectory = true)
    {
        if ($directory = Yii::getAlias($target)) {
            $target = $directory;
        }

        if ($createDirectory) {

            $directory = static::getPathInfo($target, PATHINFO_DIRNAME);

            if (!is_dir($directory) && !static::createDirectory($directory)) {
                return false;
            }
        }

        // Creating file
        if ($handle = fopen($target, 'w')) {
            fclose($handle);
            return true;
        }

        return false;
    }

    /**
     * Returns owner of current filesystem object (UNIX systems).
     *
     * Returned value depends upon $getName parameter value.
     *
     * @param string $target Path and name of the object
     * @param boolean $getName If true returns the owner name instead of the uid. (default: true)
     * @return string|integer|boolean
     */

    public static function getOwner($target, $getName = true)
    {
        if (!$target = static::getRealPath($target)) {
            return false;
        }

        // Getting uid
        $owner = fileowner($target);

        // If possible, replacing uid with unix account name
        if (function_exists('posix_getpwuid') && $getName == true) {
            $owner = posix_getpwuid($owner);
        }

        return $owner;
    }

    /**
     * Returns group of current filesystem object (UNIX systems).
     *
     * Returned value depends upon $getName parameter value.
     *
     * @param string $target
     * @param boolean $getName If true returns the owner group name instead of the uid. (default: true)
     * @return string|integer|boolean
     */

    public static function getGroup($target, $getName = true)
    {
        if (!$target = static::getRealPath($target)) {
            return false;
        }

        // Getting uid
        $group = filegroup($target);

        // If possible, replacing uid with group name
        if (function_exists('posix_getgrgid') && $getName == true) {
            $group = posix_getgrgid($group);
        }

        return $group;
    }

    /**
     * Returns permissions of current filesystem object (UNIX systems).
     *
     * @param string $target Path and name of the object
     * @return string|boolean Filesystem object permissions in octal format (i.e. '0755'), false on failure.
     */

    public static function getPermissions($target)
    {
        if (!$target = static::getRealPath($target)) {
            return false;
        }

        return substr(sprintf('%o', fileperms($target)), -4);
    }

    /**
     * Returns with the real path or false of failure (works with Yii path alises).
     *
     * @param string $target Path to turn to real path
     * @return string|false
     */
    public static function getRealPath($target)
    {
        if (!$realPath = Yii::getAlias($target)) {
            $realPath = realpath($target);
        }

        return $realPath;
    }

    /**
     * Determines if the given filesytem object exists or not.
     *
     * @return boolean
     */
    public static function exists($target)
    {
        if (!is_file($target) && !is_dir($target)) {
            return false;
        }

        return true;
    }

    /**
     * Returns size of current filesystem object.
     *
     * Returned value depends upon $format parameter value.
     * Uses {@link dirSize} method for directory size calculation.
     *
     * @param string $target Path and name of the object
     * @param string|boolean $format Number format (see {@link CNumberFormatter})
     * or 'false' (default: false)
     *
     * @return string|integer|boolean Filesystem object size formatted (eg. '70.4 KB') or in
     * bytes (eg. '72081') if $format set to 'false' or false on failure
     */
    public static function getSize($target, $format = false)
    {
        if (!$target = static::getRealPath($target)) {
            return false;
        }

        // If $target is a file
        if (is_file($target)) {
            $size = sprintf("%u", filesize($target));
        } elseif (is_dir($target)) { // If $target is a directory
            $size = sprintf("%u", static::dirSize());
        }

        // If formatted size is requested
        if ($format !== false) {
            $size = static::formatFileSize($size, $format);
        }

        return $size;
    }

    /**
     * Calculates the current directory size recursively fetching sizes of all descendant files.
     *
     * This method is used internally and only for folders.
     * See {@link getSize} method params for detailed information.
     *
     * @param string $directory Path and name of the directory
     * @return int
     */
    public static function dirSize($directory)
    {
        $size = 0;
        foreach (static::dirContents($directory, true) as $item) {
            if (is_file($item)) {
                $size += sprintf("%u", filesize($item));
            }
        }

        return $size;
    }

    /**
     * Base filesystem object size format method.
     *
     * Converts file size in bytes into human readable format (i.e. '70.4 KB')
     *
     * @param integer $bytes Filesystem object size in bytes
     * @param integer $format Number format (see {@link CNumberFormatter})
     * @return string Filesystem object size in human readable format
     */
    protected static function formatFileSize($bytes, $format)
    {
        $units = array(
            'B', 'KB', 'MB', 'GB', 'TB', 'PB'
        );

        $bytes = max($bytes, 0);
        $expo = floor(($bytes ? log($bytes) : 0) / log(1024));
        $expo = min($expo, count($units) - 1);

        $bytes /= pow(1024, $expo);

        return Yii::app()->numberFormatter->format($format, $bytes) . ' ' . $units[$expo];
    }

    /**
     * Returns the current file last modified time.
     *
     * Returned Unix timestamp could be passed to php date() function.
     *
     * @param string $target Path and name of the file
     * @return integer|boolean Last modified time Unix timestamp (eg. '1213760802')
     */
    public static function getTimeModified($target)
    {
        if (!$target = static::getRealPath($target)) {
            return false;
        }

        return filemtime($target);
    }

    /**
     * Same as pathinfo with a pre php 5.2 patch
     *
     * @see pathinfo
     *
     * @param string $target
     * @param integer $option
     * @return string|array
     */
    public static function getPathInfo($target, $option = null)
    {
        if ($option === null) {
            $pathInfo = pathinfo($target);

            // PHP version < 5.2 workaround
            if (is_string(PATHINFO_FILENAME)) {
                $pathInfo['filename'] = substr($pathInfo['basename'], 0, strrpos($pathInfo['basename'], '.'));
            }
        } else {

            // PHP version < 5.2 workaround
            if ($option === PATHINFO_FILENAME && is_string(PATHINFO_FILENAME)) {
                $pathInfo = pathinfo($target, PATHINFO_BASENAME);
                $pathInfo = substr($pathInfo, 0, strrpos($pathInfo, '.'));
            } else {
                $pathInfo = pathinfo($target, $option);
            }
        }

        return $pathInfo;
    }

    /**
     * Returns the current filesystem object contents.
     *
     * Reads data from filesystem object if it is a regular file.
     * List files and directories inside the specified path if filesystem object
     * is a directory.
     *
     * @param boolean $recursive If 'true' method would return all directory
     * descendants (default: false)
     * @param string $filter Filter to be applied to all directory descendants.
     * Could be a string, or an array of strings (perl regexp supported). (default: null)
     * @return string|boolean The read data or 'false' on fail.
     */

    public static function read($target, $recursive = false, $filter = null)
    {
        if (!$target = static::getRealPath($target)) {
            return false;
        }

        $contents = false;

        if (is_file($target)) {
            $contents = file_get_contents($target);
        } elseif (is_dir($target)) {
            if ($contents = static::dirContents($target, $recursive, $filter)) {
                return $contents;
            }
        }

        return $contents;
    }

    /**
     * Gets directory contents (descendant files and folders).
     *
     * @param string $directory Initial directory to get descendants for
     * @param boolean $recursive If 'true' method would return all descendants
     * recursively, otherwise just immediate descendants
     * @param string $filter Filter to be applied to all directory descendants.
     * Could be a string, or an array of strings (perl regexp supported).
     * See {@link filterPassed} method for further information on filters.
     * @return array Array of descendants filepaths
     */
    public static function dirContents($directory, $recursive = false, $filter = null)
    {
        $descendants = array();

        if ($filter !== null) {

            if (is_string($filter)) {
                $filter = array($filter);
            }

            foreach ($filter as $key => $rule) {
                if ($rule[0] != '/') {
                    $filter[$key] = ltrim($rule, '.');
                }
            }
        }

        if ($contents = @scandir($directory . DIRECTORY_SEPARATOR)) {
            foreach ($contents as $key => $item) {
                $contents[$key] = $directory . DIRECTORY_SEPARATOR . $item;
                if (!in_array($item, array(".", ".."))) {
                    if (static::filterPassed($contents[$key], $filter)) {
                        $descendants[] = $contents[$key];
                    }

                    if (is_dir($contents[$key]) && $recursive) {
                        $descendants =
                            array_merge(
                                $descendants,
                                static::dirContents($contents[$key], $recursive, $filter)
                            );
                    }
                }
            }
        }

        return $descendants;
    }

    /**
     * Applies an array of filter rules to the string representing filepath.
     *
     * Used internally by {@link dirContents} method.
     *
     * @param string $str String representing filepath to be filtered
     * @param array $filter An array of filter rules, where each rule is a
     * string, supposing that the string starting with '/' is a regular
     * expression. Any other string reated as an extension part of the
     * given filepath (eg. file extension)
     * @return boolean Returns 'true' if the supplied string matched one of
     * the filter rules.
     */

    protected static function filterPassed($str, $filter)
    {
        $passed = true;

        if ($filter !== null) {
            foreach ($filter as $rule) {
                if ($rule[0] != '/') {
                    $rule = '.' . $rule;
                    $passed = (bool) substr_count($str, $rule, strlen($str) - strlen($rule));
                } else {
                    $passed = (bool) preg_match($rule, $str);
                }

                if ($passed) {
                    break;
                }
            }
        }

        return $passed;
    }

    /**
     * Writes contents into the targetted file.
     *
     * This method works only for files.
     *
     * @param string $target Path and name of the file
     * @param string $contents Contents to be written
     * @param boolean $createDirectory If 'true' directory will be created automatically
     * @param integer $flags Flags for file_put_contents(). E.g.: FILE_APPEND
     * to append data to file instead of overwriting.
     * @return boolean
     */

    public static function write($target, $contents = null, $createDirectory = true, $flags = 0)
    {
        if ($directory = Yii::getAlias($target)) {
            $target = $directory;
        }

        if ($createDirectory) {
            $directory = static::getPathInfo($target, PATHINFO_DIRNAME);

            if (!is_dir($directory) && !static::createDirectory($directory)) {
                return false;
            }
        }

        $pathInfo = static::getPathInfo($target);

        $temporaryFile =
            $flags & FILE_APPEND ?
            $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['basename'] :
            tempnam($pathInfo['dirname'], $pathInfo['basename']);

        if (false !== @file_put_contents($temporaryFile, $contents, $flags)
            && @rename($temporaryFile, $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['basename'])) {

            return chmod($pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['basename'], 0666);
        }

        return false;
    }

    /**
     * Sets the current filesystem object owner, updates $_owner property on success.
     *
     * For UNIX systems.
     *
     * @param string $target Path and name of the object
     * @param string|integer $owner New owner name or ID
     * @return boolean
     */

    public static function setOwner($target, $owner)
    {
        return $target = static::getRealPath($target) ? chown($target, $owner) : false;
    }

    /**
     * Sets the current filesystem object group, updates $_group property on success.
     *
     * For UNIX systems.
     *
     * @param string $target Path and name of the object
     * @param string|integer $group New group name or ID
     * @return boolean
     */

    public static function setGroup($target, $group)
    {
        return $target = static::getRealPath($target) ? chgrp($target, $group) : false;
    }

    /**
     * Sets the current filesystem object permissions, updates $_permissions property on success.
     *
     * For UNIX systems.
     *
     * @param string $target Path and name of the object
     * @param string $permissions New filesystem object permissions in numeric
     * (octal, i.e. '0755') format
     * @return boolean
     */

    public static function setPermissions($target, $permissions)
    {
        if ($target = static::getRealPath($target) && is_numeric($permissions)) {
            // '755' normalize to octal '0755'
            $permissions = octdec(str_pad($permissions, 4, "0", STR_PAD_LEFT));

            return chmod($target, $permissions);
        }

        return false;
    }

    /**
     * Copies a filesystem object to specified destination.
     *
     * @param string $source Source path and filename
     * @param string $destination Destination path and optional filename
     * @param boolean $createDirectory To create destination directory if it doesn't exists (default: false)
     * @return boolean
     */
    public function copy($source, $destination, $createDirectory = false)
    {
        if (!$source = static::getRealPath($source)) {
            return false;
        }

        $sourceInfo = static::getPathInfo($source);
        $destinationInfo = static::getPathInfo($destination);

        // No destination directory means it's same with source
        if (!$destinationInfo['dirname']) {
            $destinationInfo['dirname'] = $sourceInfo['dirname'];
        }

        if ($alias = Yii::getAlias($destinationInfo['dirname'])) {
            $destinationInfo['dirname'] = $alias;
        }

        if ($createDirectory && !is_dir($destinationInfo['dirname'])) {
            if (!static::createDirectory($destinationInfo['dirname'])) {
                return false;
            }
        }

        if (is_file($sourceInfo['dirname'] . DIRECTORY_SEPARATOR . $sourceInfo['basename'])) {
            return @copy(
                $sourceInfo['dirname'] . DIRECTORY_SEPARATOR . $sourceInfo['basename'],
                $destinationInfo['dirname'] . DIRECTORY_SEPARATOR . $destinationInfo['basename']
            );
        } elseif (is_dir($sourceInfo['dirname'])) {

            $dirContents = static::dirContents($sourceInfo['dirname'], true);

            foreach ($dirContents as $item) {
                $destinationItem = str_replace($sourceInfo['dirname'], $destinationInfo['dirname'], $item);

                if (is_file($item)) {
                    if (!@copy($item, $destinationItem)) {
                        return false;
                    }
                } elseif (is_dir($item)) {
                    if (!static::createDirectory($destinationItem)) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Renames/moves a filesystem object to specified destination.
     *
     * @param string $source Source path and name of the object
     * @param string $destination Destination path and name
     * @param boolean $createDirectory To create destination directory if it doesn't exists (default: false)
     * @return boolean
     */

    public static function rename($source, $destination, $createDirectory = false)
    {
        if (!$source = static::getRealPath($source)) {
            return false;
        }

        $destinationInfo = static::getPathInfo($destination);

        // No destination directory means it's same with source
        if (!$destinationInfo['dirname']) {
            $destinationInfo['dirname'] = static::getPathInfo($source, PATHINFO_DIRNAME);
        }

        if ($alias = Yii::getAlias($destinationInfo['dirname'])) {
            $destinationInfo['dirname'] = $alias;
        }

        if ($createDirectory && !is_dir($destinationInfo['dirname'])) {
            if (!static::createDirectory($destinationInfo['dirname'])) {
                return false;
            }
        }

        if (@rename($source, $destinationInfo['dirname'] . DIRECTORY_SEPARATOR . $destinationInfo['basename'])) {
            return true;
        }

        return false;
    }

    /**
     * Alias for {@link rename}
     */

    public static function move($source, $destination, $createDirectory = false)
    {
        return static::rename($source, $destination, $createDirectory);
    }

    /**
     * Purges (makes empty) a filesystem object.
     *
     * If the filesystem object is a file its contents set to ''.
     * If the filesystem object is a directory all its descendants are
     * deleted.
     *
     * @param string Path and name of the object to be purged
     * @return boolean
     */

    public static function purge($target)
    {
        if (!$target = static::getRealPath($target)) {

            if (is_file($target)) {
                return static::write($target, '', false);
            } elseif (is_dir($target)) {

                $directoryContents = static::dirContents($path, true);
                foreach ($directoryContents as $item) {
                    if (is_file($item)) {
                        if (!@unlink($item)) {
                            return false;
                        }
                    } elseif (is_dir($item)) {
                        if (!static::purge($item) || !@rmdir($item)) {
                            return false;
                        }
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Deletes a filesystem object.
     *
     * For folders $recursive parameter can be supplied.
     *
     * @param string $target Path and name of the object
     * @param boolean $recursive If true deletes children objects as well
     * @return boolean 'True' if sucessfully deleted, 'false' on fail
     */

    public static function delete($target, $recursive = true)
    {
        if ($target = static::getRealPath($target)) {
            if (is_file($target)) {
                return @unlink($target);
            } elseif (is_dir($target)) {
                return static::purge($target) && rmdir($target);
            }
        }

        return false;
    }

    /**
     * Trims the trailing directory separator
     *
     * @param string $file
     * @return string
     */
    public static function trim($file)
    {
        return rtrim($file, '\\/');
    }

    /**
     * Checks if the given path is absolute or not
     *
     * @param string $path
     * @return boolean
     */
    public static function isAbsolutePath($path)
    {
        return preg_match('/^[\\,\/]|^[A-Za-z]:/', $path) === 1;
    }
}
