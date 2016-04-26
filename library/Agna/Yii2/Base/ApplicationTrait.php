<?php

namespace Agna\Yii2\Base;

/**
 * Trait extending application classes (web, console).
 *
 * Changes:
 * + Registers an object mapper component
 * + Registers an autoloader component
 *
 * @author Agoston Nagy
 */
trait ApplicationTrait
{
    /**
     * @see \yii\base\Application::coreComponents()
     */
    public function coreComponents()
    {
        return array_merge(parent::coreComponents(), [
            'i18n' => ['class' => 'Agna\Yii2\I18n\I18N'],
            'object' => ['class' => 'Agna\Yii2\Di\Component\Object'],
            'autoloader' => ['class' => 'Agna\Yii2\Autoloader\Composer\Component'],
            //'assetManager' => ['class' => 'Agna\Yii2\web\AssetManager']
        ]);
    }

    /**
     * @see \yii\base\Application::bootstrap()
     */
    protected function bootstrap()
    {
        $this->bootstrap[] = 'autoloader';
        $this->bootstrap[] = 'object';

        /**
         * Original yii bootstrap is bugged in base application!
         *
         * @todo Keep an eye out for the fix!
         */
        //parent::bootstrap();

        foreach ($this->extensions as $extension) {
            if (!empty($extension['alias'])) {
                foreach ($extension['alias'] as $name => $path) {
                    \Yii::setAlias($name, $path);
                }
            }
            if (isset($extension['bootstrap'])) {
                $component = \Yii::createObject($extension['bootstrap']);
                if ($component instanceof BootstrapInterface) {
                    \Yii::trace("Bootstrap with " . get_class($component) . '::bootstrap()', __METHOD__);
                    $component->bootstrap($this);
                } else {
                    \Yii::trace("Bootstrap with " . get_class($component), __METHOD__);
                }
            }
        }

        foreach ($this->bootstrap as $class) {

            // @todo This line is the fix!
            $component = null;
            // @todo This line is the fix!

            if (is_string($class)) {
                if ($this->has($class)) {
                    $component = $this->get($class);
                } elseif ($this->hasModule($class)) {
                    $component = $this->getModule($class);
                } elseif (strpos($class, '\\') === false) {
                    throw new InvalidConfigException("Unknown bootstrap component ID: $class");
                }
            }
            if (!isset($component)) {
                $component = \Yii::createObject($class);
            }

            if ($component instanceof \yii\base\BootstrapInterface) {
                \Yii::trace("Bootstrap with " . get_class($component) . '::bootstrap()', __METHOD__);
                $component->bootstrap($this);
            } else {
                \Yii::trace("Bootstrap with " . get_class($component), __METHOD__);
            }
        }
    }
}
