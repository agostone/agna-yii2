<?php

namespace Agna\Yii2\Widgets;

use yii\helpers\Html;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;

/**
 * Extended ActiveField class
 *
 * Changes:
 * + Configurable content provider via $contentProvider.
 * + Setting placeholders automatically if model has an attributePlaceholders method returning an array.
 * = Id attribute generation tries to find the input id in parts['{input}'] first. Allowing change of input ids while keeping validation intact most times!
 *
 * @author Agoston Nagy
 */
class ActiveField extends \yii\widgets\ActiveField
{
    /**
     * Provides content on rendering
     *
     * @var function
     */
    protected $contentProvider;

    /**
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        $placeholders = [];
        if (method_exists($this->model, 'attributePlaceholders')) {
            $placeholders = $this->model->attributePlaceholders();
            if (isset($placeholders[$this->attribute])) {
                $this->inputOptions['placeholder'] = $placeholders[$this->attribute];
            }
        }
    }

    /**
     * Sets the content provider
     *
     * @param function $callback
     * @return \Agna\Yii2\Widgets\ActiveField
     */
    public function setContentProvider($callback)
    {
        $this->contentProvider = $callback;
        return $this;
    }

    /**
     * Returns with the contet provider
     *
     * @return function
     */
    public function getContentProvider()
    {
        return $this->contentProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function render($content = null)
    {
        if ($content === null && $this->contentProvider !== null && is_callable($this->contentProvider)) {
            $content = $this->contentProvider;
        }

        return parent::render($content);
    }

    /**
     * {@inheritdoc}
     */
    public function begin()
    {
        $inputID = '';

        if ($this->form->enableClientScript) {
            $clientOptions = $this->getClientOptions();
            if (!empty($clientOptions)) {
                $this->form->attributes[] = $clientOptions;
                $inputID = $clientOptions['id'];
            }
        }

        $inputID = $inputID ? $inputID : $this->getInputId();
        $attribute = Html::getAttributeName($this->attribute);
        $options = $this->options;
        $class = isset($options['class']) ? [$options['class']] : [];
        $class[] = "field-$inputID";
        if ($this->model->isAttributeRequired($attribute)) {
            $class[] = $this->form->requiredCssClass;
        }
        if ($this->model->hasErrors($attribute)) {
            $class[] = $this->form->errorCssClass;
        }
        $options['class'] = implode(' ', $class);
        $tag = ArrayHelper::remove($options, 'tag', 'div');

        return Html::beginTag($tag, $options);
    }

    /**
     * Tries to return the real input id by parsing parts['{input}'] first. If fails falls back to Html::getInputId.
     *
     * @return string
     */
    protected function getInputId()
    {
        if (isset($this->parts['{input}'])
           && ($id = stripos($this->parts['{input}'], 'id='))
        ) {
            $id = substr($this->parts['{input}'], $id + 3);
            return trim(
                substr($id, 0, stripos($id, ' ')),
                '" '
            );
        }

        return Html::getInputId($this->model, $this->attribute);
    }

    /**
     * {@inheritdoc}
     */
    protected function getClientOptions()
    {
        $attribute = Html::getAttributeName($this->attribute);
        if (!in_array($attribute, $this->model->activeAttributes(), true)) {
            return [];
        }

        $enableClientValidation = $this->enableClientValidation || $this->enableClientValidation === null && $this->form->enableClientValidation;
        $enableAjaxValidation = $this->enableAjaxValidation || $this->enableAjaxValidation === null && $this->form->enableAjaxValidation;

        if ($enableClientValidation) {
            $validators = [];
            foreach ($this->model->getActiveValidators($attribute) as $validator) {
                /* @var $validator \yii\validators\Validator */
                $js = $validator->clientValidateAttribute($this->model, $attribute, $this->form->getView());
                if ($validator->enableClientValidation && $js != '') {
                    if ($validator->whenClient !== null) {
                        $js = "if (({$validator->whenClient})(attribute, value)) { $js }";
                    }
                    $validators[] = $js;
                }
            }
        }

        if (!$enableAjaxValidation && (!$enableClientValidation || empty($validators))) {
            return [];
        }

        $options = [];

        $inputID = $this->getInputId();
        $options['id'] = $inputID;
        $options['name'] = $this->attribute;

        $options['container'] = isset($this->selectors['container']) ? $this->selectors['container'] : ".field-$inputID";
        $options['input'] = isset($this->selectors['input']) ? $this->selectors['input'] : "#$inputID";
        if (isset($this->selectors['error'])) {
            $options['error'] = $this->selectors['error'];
        } elseif (isset($this->errorOptions['class'])) {
            $options['error'] = '.' . implode('.', preg_split('/\s+/', $this->errorOptions['class'], -1, PREG_SPLIT_NO_EMPTY));
        } else {
            $options['error'] = isset($this->errorOptions['tag']) ? $this->errorOptions['tag'] : 'span';
        }

        $options['encodeError'] = !isset($this->errorOptions['encode']) || $this->errorOptions['encode'];
        if ($enableAjaxValidation) {
            $options['enableAjaxValidation'] = true;
        }
        foreach (['validateOnChange', 'validateOnBlur', 'validateOnType', 'validationDelay'] as $name) {
            $options[$name] = $this->$name === null ? $this->form->$name : $this->$name;
        }

        if (!empty($validators)) {
            $options['validate'] = new JsExpression("function (attribute, value, messages, deferred) {" . implode('', $validators) . '}');
        }

        // only get the options that are different from the default ones (set in yii.activeForm.js)
        return array_diff_assoc($options, [
            'validateOnChange' => true,
            'validateOnBlur' => true,
            'validateOnType' => false,
            'validationDelay' => 500,
            'encodeError' => true,
            'error' => '.help-block',
        ]);
    }
}
