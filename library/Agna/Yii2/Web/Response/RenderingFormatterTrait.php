<?php

namespace Agna\Yii2\Web\Response;

use Yii;
use yii\base\InvalidParamException;

trait RenderingFormatterTrait
{
    /**
     * Renders the view
     *
     * @param string $view
     * @param mixed $parameters
     * @param boolean $throwExceptionOnBadViewFile (default: false)
     * @throws InvalidParamException
     * @return boolean
     */
    public function render($view, $parameters, $throwExceptionOnBadViewFile = false)
    {
        // @todo Doublecheck, if absolute path or full file names with extensions are in no way get passed to this method, if so fix this and create a filename prefix.
        // Prefixing
        // $filename = pathinfo($test, PATHINFO_FILENAME);
        // $view = str_replace($filename, $suffix . $filename, $view);
        $view .= $this->suffix;

        // Auto rendering is at the end of any controller, action call chain,
        // therefore, Yii::$app->contoller - the active controller - should be null.
        // Also, the $action parameter of the Yii::$app->requestedAction->controller, should be null as well.
        // As there is no active action.
        // So, you can chain whatever you want, however you want it, because the auto renderer will set up the
        // enviroment to look like as the requested controller and action is the active one.
        // Period. =D
        $controller = Yii::$app->controller = Yii::$app->requestedAction->controller;
        $controller->action = Yii::$app->requestedAction;

        try {

            $content = $controller->renderPartial($view, $parameters);

            // Using layout
            if ($layoutFile = $controller->getLayoutFile()) {
                $pathinfo = pathinfo($layoutFile);

                $layoutFile =
                    $pathinfo['dirname'] . DIRECTORY_SEPARATOR
                    . $pathinfo['filename']
                    . $this->suffix . '.'
                    . $pathinfo['extension'];

                $content = $controller->renderFile($layoutFile, ['content' => $content]);
            }
        } catch (InvalidParamException $invalidParamException) { // Bad view file or view file doesn't exist

            if ($throwExceptionOnBadViewFile) {
                throw $invalidParamException;
            }

            $content = false;
        }

        // Setting everything back to what it was.
        Yii::$app->controller = $controller->action = null;

        return $content;
    }
}