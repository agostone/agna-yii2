<?php

namespace Agna\Yii2\Widgets\Json;

use Yii;
use yii\db\BaseActiveRecord;

use Agna\Yii2\Base\InvalidParamException;
use Agna\Yii2\Widgets\Json;
use yii\web\JsExpression;

class ActiveRecord extends Json
{
    /**
     * Json type flag to indicate encapsulation, ie. json items will be encapsulated by an object with the active record's name
     *
     * @var binary
     */
    const TYPE_ENCAPSULATE = 0b0010;

    /**
     * Active record instance
     *
     * @var BaseActiveRecord
     */
    public $activeRecord;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // Array json is banished, we need an object
        $this->type &= ~static::TYPE_ARRAY;

        parent::init();

        // Encapsulation required
        if ($this->type & static::TYPE_ENCAPSULATE) {
            $this->key($this->activeRecord->formName());
            echo('{');
        }
    }

    public function attribute($attribute)
    {
        $field = [
            'value' => $this->activeRecord->getAttribute($attribute),
            'label' => $this->activeRecord->getAttributeLabel($attribute),
            'errors' => $this->activeRecord->getErrors($attribute)
        ];

        $validators = [];

        foreach ($this->activeRecord->getActiveValidators($attribute) as $validator) {
            /* @var $validator \yii\validators\Validator */
            $js = $validator->clientValidateAttribute($this->activeRecord, $attribute, Yii::$app->view);
            if ($js != '') {
                if ($validator->whenClient !== null) {
                    $js = "if ({$validator->whenClient}(attribute, value)) { $js }";
                }
                $validators[] = $js;
            }
        }

        if (!empty($validators)) {
            $field['validation'] = (string) new JsExpression("function (attribute, value, messages) {" . implode('', $validators) . '}');
        }

        return $this->item($field, $attribute);
    }

    public function errorSummary()
    {
        return $this->item($this->activeRecord->getErrors(), 'errorSummary');
    }

    public function run()
    {
        if ($this->type & static::TYPE_ENCAPSULATE) {
            echo('}');
        }

        parent::run();
    }
}
