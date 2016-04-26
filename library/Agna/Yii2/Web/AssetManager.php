<?php

namespace Agna\Yii2\Web;

use Agna\Yii2\Helpers\FileHelper;

/**
 * Extended asset manager.
 *
 * Changes:
 * + Option to turn off asset copying
 *
 * @author Agoston Nagy
 */
class AssetManager extends \yii\web\AssetManager
{
    /**
     * Flag indicating to never copy assets when publishing
     *
     * @var boolean
     */
    public $neverCopyAssets = false;

    protected function publishFile($src)
    {
        $dir = $this->hash(dirname($src) . filemtime($src));
        $fileName = basename($src);
        $dstDir = $this->basePath . DIRECTORY_SEPARATOR . $dir;
        $dstFile = $dstDir . DIRECTORY_SEPARATOR . $fileName;

        // We won't copy assets
        if (!$this->neverCopyAssets) {
            if (!is_dir($dstDir)) {
                FileHelper::createDirectory($dstDir, $this->dirMode, true);
            }

            if ($this->linkAssets) {
                if (!is_file($dstFile)) {
                    symlink($src, $dstFile);
                }
            } elseif (@filemtime($dstFile) < @filemtime($src)) {
                copy($src, $dstFile);
                if ($this->fileMode !== null) {
                    @chmod($dstFile, $this->fileMode);
                }
            }
        }

        return [$dstFile, $this->baseUrl . "/$dir/$fileName"];
    }

    protected function publishDirectory($src, $options)
    {
        $dir = $this->hash($src . filemtime($src));
        $dstDir = $this->basePath . DIRECTORY_SEPARATOR . $dir;

        if (!$this->neverCopyAssets) {
            if ($this->linkAssets) {
                if (!is_dir($dstDir)) {
                    symlink($src, $dstDir);
                }
            } elseif (!empty($options['forceCopy']) ||
                ($this->forceCopy && !isset($options['forceCopy'])) || !is_dir($dstDir)
            ) {
                $opts = [
                    'dirMode' => $this->dirMode,
                    'fileMode' => $this->fileMode,
                ];
                if (isset($options['beforeCopy'])) {
                    $opts['beforeCopy'] = $options['beforeCopy'];
                } elseif ($this->beforeCopy !== null) {
                    $opts['beforeCopy'] = $this->beforeCopy;
                } else {
                    $opts['beforeCopy'] = function ($from, $to) {
                        return strncmp(basename($from), '.', 1) !== 0;
                    };
                }
                if (isset($options['afterCopy'])) {
                    $opts['afterCopy'] = $options['afterCopy'];
                } elseif ($this->afterCopy !== null) {
                    $opts['afterCopy'] = $this->afterCopy;
                }
                FileHelper::copyDirectory($src, $dstDir, $opts);
            }
        }

        return [$dstDir, $this->baseUrl . '/' . $dir];
    }
}
