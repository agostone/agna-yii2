<?php

namespace Agna\Yii2\Base\Component;

use Agna\Yii2\Base\Component;
use Agna\Yii2\Base\ControllerEvent;
use Agna\Yii2\Helpers\ArrayHelper;

/**
 * Component for injecting behaviors into controllers.
 *
 * Useful if you cannot override the behaviors() method of some controller and you wish to avoid copy/pasting.
 *
 * Changes:
 * + Behavior configuration may be a string pointing to another path
 *
 * @author Agoston Nagy
 */
class ControllerBehaviors extends Component implements \yii\base\BootstrapInterface
{
    /**
     * Array containing the behaviors config
     *
     * @var array
     */
    public $behaviors = [];

    /**
     * {@inheritdoc}
     */
    public function bootstrap($application)
    {
        $application->on(ControllerEvent::EVENT_BEFORE_CONTROLLER, array($this, 'beforeController'));
    }

    /**
     * Before controller event handler
     *
     * @param ControllerEvent $event
     */
    public function beforeController($event)
    {
        $behaviors = [];

        $segments = ['*'];
        $segments = array_merge($segments, explode('/', $event->controller->getUniqueId()));
        $segments[] = $event->actionId;

        $path = '';

        foreach ($segments as $key => $segment) {

            // No '/' should be added if it's the '*' or root segment
            if ($key > 1) {
                $path .= '/';
            } elseif ($key === 1) { // Resetting $path after '*'
                $path = '';
            }

            $path .= $segment;

            if ($segment = $this->getPathBehaviors($path)) {
                $behaviors = ArrayHelper::mergeRecursive($segment, $behaviors);
            }
        }

        if (!empty($behaviors)) {
            $event->controller->attachBehaviors($behaviors);
        }
    }

    /**
     * Returns with the behaviors configuration for the requested path or false on failure
     *
     * @param string $path
     * @return array|false
     */
    protected function getPathBehaviors($path)
    {
        // Behaviors configuration found
        if (isset($this->behaviors[$path])) {

            // If string then it's a reference to another path
            if (is_string($this->behaviors[$path])) {
                return $this->getPathBehaviors($this->behaviors[$path]);
            }

            // If it's an array, then all is green
            if (is_array($this->behaviors[$path])) {
                return $this->behaviors[$path];
            }
        }

        // No or malformed configuration found
        return false;
    }
}
