<?php

namespace Agna\Yii2\Console\Controllers;

use Yii;
use Agna\Yii2\Helpers\FileHelper;
use Agna\Yii2\Console\Controller;

/**
 * Scss related console application controller
 *
 * @author Agoston Nagy
 */
class ScssController extends Controller
{
    protected $scss;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (!class_exists('scssc', true)) {
            throw new \RuntimeException(Yii::t('Agna\Yii2', 'Uses leafo/scssphp, install it with composer!'));
        }

        $this->scss = new \scssc();
    }

    /**
     * Compiles an scss file to a css file
     *
     * @param string $from Scss file to convert
     * @param string $to Css file to convert to
     * @param string $importPath,... Import paths
     * @return int
     */
    public function actionCompile($from, $to)
    {
        $importPath = [dirname($from)];

        if (func_num_args() > 2) {
            $importPath = array_merge($importPath, array_slice(func_get_args(), 2));
            foreach ($importPath as $key => $path) {
                $importPath[$key] = Yii::getAlias($path);
            }
        }

        $this->scss->setImportPaths($importPath);

        if (!($css = FileHelper::read($from))) {
            echo(Yii::t('Agna\Yii2', "Cannot read '{$from}' file!"));
            return 1;
        }

        $css = $this->scss->compile($css, $from);

        if (!FileHelper::write($to, $css)) {
            echo(Yii::t('Agna\Yii2', "Cannot write '{$to}' file!"));
            return 1;
        }

        echo(Yii::t('Agna\Yii2', "'{$from}' was compiled and written to '{$to}'.\n"));
        return 0;
    }
}
