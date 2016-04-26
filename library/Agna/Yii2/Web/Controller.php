<?php

namespace Agna\Yii2\Web;

/**
 * Extended base controller
 *
 * @author Agoston Nagy
 */
class Controller extends \yii\web\Controller
{
    /**
     * Returns with the layout path and file name
     *
     * @return string
     */
    public function getLayoutFile()
    {
        return $this->findLayoutFile($this->getView());
    }
}
