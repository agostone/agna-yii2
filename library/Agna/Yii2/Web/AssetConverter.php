<?php

namespace Agna\Yii2\Web;

/**
 * Extended asset converter.
 *
 * Changes:
 * + Option to turn off asset converting
 *
 * @author Agoston Nagy
 */
class AssetConverter extends \yii\web\AssetConverter
{
    /**
     * Flag indicating to never convert assets
     *
     * @var boolean
     */
    public $neverConvertAssets = false;

    /**
     * Converts a given asset file into a CSS or JS file.
     * @param string $asset the asset file path, relative to $basePath
     * @param string $basePath the directory the $asset is relative to.
     * @return string the converted asset file path, relative to $basePath.
     */
    public function convertz($asset, $basePath)
    {
        $pos = strrpos($asset, '.');
        if ($pos !== false) {
            $ext = substr($asset, $pos + 1);
            if (isset($this->commands[$ext])) {
                list ($ext, $command) = $this->commands[$ext];
                $result = substr($asset, 0, $pos + 1) . $ext;
                if (!$this->neverConvertAssets &&
                    ($this->forceConvert || @filemtime("$basePath/$result") < filemtime("$basePath/$asset"))
                ) {
                    $this->runCommand($command, $basePath, $asset, $result);
                }

                return $result;
            }
        }

        return $asset;
    }
}
