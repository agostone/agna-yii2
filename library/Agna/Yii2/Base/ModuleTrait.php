<?php

namespace Agna\Yii2\Base;

use Yii;
use yii\base\InvalidRouteException;
use yii\i18n\PhpMessageSource;

/**
 * Module extensions
 *
 * Changes:
 * + Extra before/after controller events
 * + Automatic translation detection
 *
 * @todo Perhaps forcing translation labels shouldn't be decided here, rather in module level
 */
trait ModuleTrait
{
    protected $translationId;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Checking for default translations
        $defaultTranslationsDirectory = $this->getBasePath() . DIRECTORY_SEPARATOR . 'messages';

        if ($this->translationId && is_dir($defaultTranslationsDirectory)) {
            // Adding default translation message source to the end of the message queue
            $defaultTranslationMessageSource = new PhpMessageSource();
            $defaultTranslationMessageSource->basePath = $defaultTranslationsDirectory;
//            $defaultTranslationMessageSource->forceTranslation = true; // Because no mater what the application source language is, the module uses translation labels
            if (!isset(Yii::$app->i18n->translations[$this->translationId])) {
                Yii::$app->i18n->translations[$this->translationId] = [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'ufo',
                    'forceTranslation' => true, // Because no mater what the application source language is, the module uses translation labels
                    'basePath' => $defaultTranslationsDirectory
                ];
            }

//             echo('<pre>');
//             var_dump(Yii::$app->i18n->translations);exit();
        }

        // Setting base path alias
        Yii::setAlias($this->id, $this->getBasePath());
    }

    /**
     * This method is invoked right before a controller gets called.
     *
     * The method will trigger the [[EVENT_BEFORE_CONTROLLER]] event.
     *
     * @param yii\base\Controller $controller the controller to be called.
     * @param string $actionId the id of the action to be called.
     */
    public function beforeController($controller, $actionId)
    {
        $event = new ControllerEvent($controller, $actionId);
        $this->trigger(ControllerEvent::EVENT_BEFORE_CONTROLLER, $event);
    }

    /**
     * This method is invoked right after a controller got called.
     *
     * The method will trigger the [[EVENT_AFTER_CONTROLLER]] event.
     *
     * @param yii\base\Controller $controller the called controller.
     * @param string $actionId the id of the called action.
     * @param mixed $result the result of the controller call.
     */
    public function afterController($controller, $actionId, $result)
    {
        $event = new ControllerEvent($controller, $actionId);
        $event->result = $result;
        $this->trigger(ControllerEvent::EVENT_AFTER_CONTROLLER, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function runAction($route, $params = [])
    {
        $parts = $this->createController($route);
        if (is_array($parts)) {
            /** @var Controller $controller */
            list($controller, $actionID) = $parts;
            $oldController = \Yii::$app->controller;
            \Yii::$app->controller = $controller;
            $this->beforeController($controller, $actionID);
            $result = $controller->runAction($actionID, $params);
            $this->afterController($controller, $actionID, $result);
            \Yii::$app->controller = $oldController;

            return $result;
        } else {
            $id = $this->getUniqueId();
            throw new InvalidRouteException('Unable to resolve the request "' . ($id === '' ? $route : $id . '/' . $route) . '".');
        }
    }
}
