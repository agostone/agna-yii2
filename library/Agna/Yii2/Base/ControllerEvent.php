<?php

namespace Agna\Yii2\Base;

use yii\base\Controller;

/**
 * ControllerEvent represents the event parameter used for a controller event.
 */
class ControllerEvent extends \yii\base\Event
{
    /**
     * An event raised before the controller gets called.
     *
     * @event Event
     */
    const EVENT_BEFORE_CONTROLLER = 'beforeController';

    /**
     * An event raised after the controller got called.
     *
     * @event Event
     */
    const EVENT_AFTER_CONTROLLER = 'afterController';

    /**
     * The controller currently being called
     *
     * @var Controller
     */
    public $controller;

    /**
     * Id of the action currently being called
     *
     * @var string
     */
    public $actionId;

    /**
     * The controller result. Event handlers may modify this property to change the action result.
     *
     * @var mixed
     */
    public $result;

    /**
     * Constructor.
     * @param Controller $controller the controller associated with this action event
     * @param array $config name-value pairs that will be used to initialize the object properties
     */
    public function __construct($controller, $actionId, $config = [])
    {
        $this->controller = $controller;
        $this->actionId = $actionId;
        parent::__construct($config);
    }
}
